<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblProductionConsumption;
use App\Library\Utilities;
use App\Models\TblDefiStore;
use App\Services\StagingService;
use App\Traits\HasStaging;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class ProductionConsumptionController extends Controller
{
    use HasStaging;

    public static $page_title = 'Production & Consumption';
    public static $menu_dtl_id = '336';
    public static $redirect_url = 'production-consumption';

    protected function productionConsumptionBranchScope(?int $branchId = null): array
    {
        return [
            ['business_id', auth()->user()->business_id],
            ['company_id', auth()->user()->company_id],
            ['branch_id', $branchId ?? auth()->user()->branch_id],
        ];
    }

    protected function productionConsumptionStagingNotificationOptions(): array
    {
        return [
            'listing_view' => 'tblproductionconsumption',
            'form_path' => '/production-consumption/form',
            'document_code_key' => 'code',
        ];
    }

    protected function findProductionConsumptionMaster(string $code, ?int $branchId = null): ?TblProductionConsumption
    {
        $query = TblProductionConsumption::where('code', $code)
            ->where('business_id', auth()->user()->business_id)
            ->where('company_id', auth()->user()->company_id);
        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        } else {
            $query->whereIn('branch_id', Utilities::userAccessibleBranchIds());
            $preferredBranchMaster = (clone $query)
                ->where('branch_id', auth()->user()->branch_id)
                ->where(function ($q) {
                    $q->where('sr_no', 1)->orWhere('sr_no', '1');
                })
                ->first();
            if ($preferredBranchMaster) {
                return $preferredBranchMaster;
            }
        }

        $master = (clone $query)
            ->where(function ($q) {
                $q->where('sr_no', 1)->orWhere('sr_no', '1');
            })
            ->first();

        if ($master) {
            return $master;
        }

        return $query
            ->orderBy('sr_no')
            ->first();
    }

    protected function resolveProductionConsumptionMaster(string $identifier, ?int $branchId = null): ?TblProductionConsumption
    {
        $baseQuery = TblProductionConsumption::where('business_id', auth()->user()->business_id)
            ->where('company_id', auth()->user()->company_id);

        if (Schema::hasColumn('tblproductionconsumption', 'id')) {
            $lineQuery = (clone $baseQuery)->where('id', $identifier);
            if ($branchId !== null) {
                $lineQuery->where('branch_id', $branchId);
            }
            $line = $lineQuery->first();
            if ($line) {
                return $this->findProductionConsumptionMaster((string) $line->code, (int) $line->branch_id);
            }
        }

        return $this->findProductionConsumptionMaster($identifier, $branchId);
    }

    protected function captureProductionConsumptionStagingSnapshot(string $code, ?int $branchId = null): ?array
    {
        $master = $this->findProductionConsumptionMaster($code, $branchId);
        if (!$master || (int) ($master->staging_apply ?? 0) !== 1) {
            return null;
        }

        return [
            'staging_apply' => (int) $master->staging_apply,
            'current_stg_id' => $master->current_stg_id,
            'posted' => (int) ($master->posted ?? 0),
        ];
    }

    protected function productionConsumptionRateColumns(Request $request): array
    {
        $columns = [];
        if (Schema::hasColumn('tblproductionconsumption', 'rate_type')) {
            $columns['rate_type'] = $request->input('rate_type', '');
        }
        if (Schema::hasColumn('tblproductionconsumption', 'rate_perc')) {
            $columns['rate_perc'] = $request->input('rate_perc', '');
        }

        return $columns;
    }

    protected function syncProductionConsumptionStagingRows($master, string $code): void
    {
        $freshMaster = $master
            ? $this->findProductionConsumptionMaster($code, (int) $master->branch_id)
            : $this->findProductionConsumptionMaster($code);
        if (!$freshMaster) {
            if (!$master) {
                return;
            }
            $freshMaster = $master;
            if (method_exists($freshMaster, 'refresh')) {
                $freshMaster->refresh();
            }
        }

        DB::table('tblproductionconsumption')
            ->where('code', $code)
            ->where($this->productionConsumptionBranchScope((int) $freshMaster->branch_id))
            ->update([
                'current_stg_id' => $freshMaster->current_stg_id,
                'staging_apply' => $freshMaster->staging_apply,
                'posted' => $freshMaster->posted,
                'updated_at' => now(),
            ]);
    }

    public function create(Request $request, $id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'production-consumption';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['menu_id'] = self::$menu_dtl_id;
        $accessibleBranchIds = Utilities::userAccessibleBranchIds();
        $data['user_branches'] = DB::table('tbl_soft_branch')
            ->where(Utilities::currentBC())
            ->whereIn('branch_id', $accessibleBranchIds)
            ->orderBy('branch_name')
            ->get(['branch_id', 'branch_name']);

        if (isset($id)) {
            $allowCrossBranchView = (string) $request->query('view') === '1';
            $requestedBranchId = filter_var($request->query('branch_id'), FILTER_VALIDATE_INT);
            $preferredBranchId = $requestedBranchId !== false
                && in_array((int) $requestedBranchId, array_map('intval', $accessibleBranchIds), true)
                    ? (int) $requestedBranchId
                    : (int) auth()->user()->branch_id;
            $documentQuery = TblProductionConsumption::where('business_id', auth()->user()->business_id)
                ->where('company_id', auth()->user()->company_id);
            $documentLine = null;
            $identifierQuery = function () use ($documentQuery, $id) {
                $query = clone $documentQuery;
                if (Schema::hasColumn('tblproductionconsumption', 'id')) {
                    $query->where('id', $id);
                    if ($query->exists()) {
                        return $query;
                    }
                }
                return (clone $documentQuery)->where('code', $id);
            };
            $preferredLineQuery = $identifierQuery()->where('branch_id', $preferredBranchId);
            $documentLine = $preferredLineQuery->orderBy('sr_no')->first();
            if (!$documentLine && $allowCrossBranchView) {
                $documentLine = $identifierQuery()->orderBy('branch_id')->orderBy('sr_no')->first();
            } elseif (!$documentLine && !empty($accessibleBranchIds)) {
                $documentLine = $identifierQuery()->whereIn('branch_id', $accessibleBranchIds)->orderBy('branch_id')->orderBy('sr_no')->first();
            }

            if ($documentLine) {
                $documentBranchId = (int) $documentLine->branch_id;
                $documentCode = (string) $documentLine->code;
                $data['permission'] = [
                    self::$menu_dtl_id . '-edit',
                    self::$menu_dtl_id . '-view',
                ];
                if ($allowCrossBranchView) {
                    $data['permission'][] = 'flow-dash-view';
                }

                $linesQuery = TblProductionConsumption::with(['barcode.product', 'barcode.uom'])
                    ->where('code', $documentCode)
                    ->where('business_id', auth()->user()->business_id)
                    ->where('company_id', auth()->user()->company_id)
                    ->where('branch_id', $documentBranchId);

                $lines = $linesQuery->orderBy('sr_no')->get();
                if ($lines->isEmpty()) {
                    abort(404);
                }

                $data['current'] = $lines->first();
                $canEdit = auth()->user()->isAbleTo(self::$menu_dtl_id . '-edit')
                    && Utilities::documentBranchIsUserAccessible($data['current']->branch_id);
                $data['page_data'] = array_merge(
                    $data['page_data'],
                    $canEdit ? Utilities::editForm() : Utilities::viewForm()
                );

                $data['id'] = $data['current']->id ?? $data['current']->code;
                $data['lines'] = $lines;
                $data['document_code'] = $data['current']->code;
                $data['page_data']['is_posted'] = isset($data['current']->posted) && (int) $data['current']->posted === 1;
                $data['page_data']['is_canceled'] = isset($data['current']->posted) && (int) $data['current']->posted === 2;
                $this->clearFormUpdateActionIfDocumentNotEditable($data['page_data'], $data['current']);
            } else {
                abort(404);
            }
        } else {
            $data['permission'] = self::$menu_dtl_id . '-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                // PC codes are company-wide so equal codes cannot exist in separate branches.
                'biz_type'    => 'company',
                'model'       => 'TblProductionConsumption',
                'code_field'  => 'code',
                'code_prefix' => strtoupper('pc'),
                'branch_id'   => auth()->user()->branch_id,
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }

        $storeBranchIds = isset($data['current'])
            ? [(int) $data['current']->branch_id]
            : $data['user_branches']->pluck('branch_id')->map(function ($id) { return (int) $id; })->all();
        $data['store'] = TblDefiStore::select('store_id', 'store_name', 'store_default_value', 'branch_id')
            ->where('store_entry_status', 1)
            ->where(Utilities::currentBC())
            ->whereIn('branch_id', $storeBranchIds)
            ->orderByDesc('store_default_value')
            ->orderBy('store_name')
            ->get();
        $data['default_store_id'] = optional($data['store']->firstWhere('branch_id', auth()->user()->branch_id))->store_id;
        $data['rate_types'] = config('constants.rate_by');
        $data['legacy_rate_types'] = config('constants.rate_type');

        return view('inventory.production_consumption.form', compact('data'));
    }

    public function getDocumentCode(int $branchId)
    {
        abort_unless(in_array($branchId, array_map('intval', Utilities::userAccessibleBranchIds()), true), 403);

        $docData = [
            'biz_type' => 'company',
            'model' => 'TblProductionConsumption',
            'code_field' => 'code',
            'code_prefix' => strtoupper('pc'),
            'branch_id' => $branchId,
        ];

        return response()->json(['code' => Utilities::documentCode($docData)]);
    }

    public function store(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'record_date'       => 'required|date_format:d-m-Y',
            'new_branch_id'     => 'required|integer',
            'production_consumption_store' => 'required|numeric|not_in:0',
            'rate_type'         => 'required|in:cost_rate,last_purchase_rate,average_rate,item_last_net_tp,item_cost_rate,item_sale_rate',
            'rate_perc'         => 'nullable|numeric|between:-100,1000',
            'pd'           => 'required|array',
            'pd.*.sr_no'   => 'required|integer',
            'pd.*.pd_barcode' => 'required|string|max:50',
            'pd.*.stock_type' => 'required|in:production,consumption',
            'pd.*.qty'     => 'required|numeric',
            'pd.*.rate'    => 'required|numeric',
            'pd.*.amount'  => 'required|numeric',
            'pd.*.remarks' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, 'Validation Failed', 422);
        }

        $branchId = (int) $request->input('new_branch_id');
        $accessibleBranchIds = array_map('intval', Utilities::userAccessibleBranchIds());
        if (!in_array($branchId, $accessibleBranchIds, true)) {
            return $this->jsonErrorResponse($data, 'Selected branch is not accessible.', 422);
        }
        $storeExists = TblDefiStore::where('store_id', $request->input('production_consumption_store'))
            ->where('branch_id', $branchId)
            ->where('store_entry_status', 1)
            ->where(Utilities::currentBC())
            ->exists();
        if (!$storeExists) {
            return $this->jsonErrorResponse($data, 'Selected store must belong to the selected branch.', 422);
        }

        DB::beginTransaction();

        try {
            $recordDate = date('Y-m-d', strtotime($request->record_date));
            $branchScope = $this->productionConsumptionBranchScope($branchId);
            $isNew = !isset($id);
            $preservedStaging = null;
            $documentId = null;

            if (!$isNew) {
                $master = $this->resolveProductionConsumptionMaster($id, $branchId);
                if (!$master) {
                    throw new RuntimeException('Production & Consumption entry not found.', 404);
                }
                if ((int) $master->branch_id !== $branchId) {
                    throw new RuntimeException('The branch of an existing Production & Consumption entry cannot be changed.', 422);
                }

                $code = (string) $master->code;
                $documentId = (string) ($master->id ?? $id);
                // Staging has historically keyed this document by its code.
                $formId = $code;
                $this->assertCanSaveWithStaging($request, self::$menu_dtl_id, $formId, false, $master);

                if (!$this->stagingShouldPersistFormChanges($request, self::$menu_dtl_id, $formId, $master)) {
                    $wasInStaging = !empty($master->current_stg_id) && (int) ($master->posted ?? 0) === 0;
                    $stagingService = new StagingService();
                    $criteriaApplies = $stagingService->shouldUseStagingForDocument(
                        self::$menu_dtl_id,
                        $formId,
                        $master,
                        $wasInStaging,
                        false
                    );
                    $stagingEnrolled = $stagingService->isDocumentStagingEnrolled($master, self::$menu_dtl_id);

                    if ($criteriaApplies || $stagingEnrolled) {
                        $this->handleStaging(
                            $request,
                            self::$menu_dtl_id,
                            $formId,
                            $master,
                            false,
                            $this->productionConsumptionStagingNotificationOptions()
                        );
                        $this->syncProductionConsumptionStagingRows($master, $formId);
                    }

                    DB::commit();
                    $data = array_merge($data, Utilities::returnJsonEditForm());
                    $data['redirect'] = $this->documentFormStayRedirect('/' . self::$redirect_url, $documentId);
                    return $this->jsonSuccessResponse($data, trans('message.update'), 200);
                }

                $preservedStaging = $this->captureProductionConsumptionStagingSnapshot($code, $branchId);

                DB::table('tblproductionconsumption')
                    ->where('code', $code)
                    ->where($branchScope)
                    ->delete();
            } else {
                $doc_data = [
                    'biz_type'          => 'company',
                    'model'             => 'TblProductionConsumption',
                    'code_field'        => 'code',
                    'code_prefix'       => strtoupper('pc'),
                    'branch_id'         => $branchId,
                ];
                $code = Utilities::documentCode($doc_data);
                $documentId = (string) Str::uuid();
            }

            $rateHeader = $this->productionConsumptionRateColumns($request);

            $srNo = 1;
            foreach ($request->pd as $entry) {
                $lineNo = $srNo;
                $row = [
                    'code'          => $code,
                    'record_date'   => $recordDate,
                    'type'          => 'PC',
                    'sr_no'         => $srNo++,
                    'stock_type'    => $entry['stock_type'],
                    'item_code'     => $entry['pd_barcode'],
                    'qty'           => $entry['qty'],
                    'rate'          => $entry['rate'],
                    'amount'        => $entry['amount'],
                    'remarks'       => $request->remarks ?? null,
                    'user_id'       => auth()->user()->id,
                    'transfer_from' => $entry['stock_type'] === 'production' ? $request->production_consumption_store : null,
                    'transfer_to'   => $entry['stock_type'] === 'consumption' ? $request->production_consumption_store : null,
                    'business_id'   => auth()->user()->business_id,
                    'company_id'    => auth()->user()->company_id,
                    'branch_id'     => $branchId,
                    'status'        => 1,
                    'posted'        => 0,
                    'cancel'        => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];

                if (Schema::hasColumn('tblproductionconsumption', 'id')) {
                    $row['id'] = $lineNo === 1 ? $documentId : (string) Str::uuid();
                }

                $row = array_merge($row, [
                    'staging_apply' => 0,
                    'current_stg_id' => null,
                ]);

                $row = array_merge($row, $rateHeader);

                if ($preservedStaging !== null) {
                    $row = array_merge($row, [
                        'staging_apply' => $preservedStaging['staging_apply'],
                        'current_stg_id' => $preservedStaging['current_stg_id'],
                    ]);
                    $row['posted'] = $preservedStaging['posted'];
                }

                DB::table('tblproductionconsumption')->insert($row);
            }

            $master = $this->findProductionConsumptionMaster($code, $branchId);
            if (!$master) {
                throw new RuntimeException('Unable to load saved Production & Consumption entry.', 500);
            }

            $formId = $code;
            $this->finalizeDocumentStaging($request, self::$menu_dtl_id, $formId, $master, $isNew, [
                'notification' => $this->productionConsumptionStagingNotificationOptions(),
                'posted_when_exempt' => 0,
                'stg_log_posted_when_exempt' => 0,
                'preserved_staging' => $preservedStaging,
                'sync_after_save' => function ($model) use ($code) {
                    $this->syncProductionConsumptionStagingRows($model, $code);
                },
            ]);

            $this->syncProductionConsumptionStagingRows($master, $code);

            DB::commit();

            if (!$isNew) {
                $data = array_merge($data, Utilities::returnJsonEditForm());
                $data['redirect'] = $this->documentFormStayRedirect('/' . self::$redirect_url, $documentId);
                return $this->jsonSuccessResponse($data, trans('message.update'), 200);
            }

            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $documentId;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);

        } catch (QueryException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (RuntimeException $e) {
            DB::rollBack();
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 200;
            return $this->jsonErrorResponse($data, $e->getMessage(), $status);
        } catch (Exception $e) {
            DB::rollBack();
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 200;
            return $this->jsonErrorResponse($data, $e->getMessage(), $status);
        }
    }

    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();

        try {
            $master = $this->resolveProductionConsumptionMaster($id);
            if (!$master) {
                DB::rollBack();
                return $this->jsonErrorResponse($data, 'Production & Consumption entry not found.', 404);
            }

            if (!Utilities::documentBranchIsUserAccessible($master->branch_id)) {
                DB::rollBack();
                return $this->jsonErrorResponse($data, 'You cannot delete a record from a branch you cannot access.', 403);
            }

            $posted = (int) ($master->posted ?? 0);
            if (in_array($posted, [1, 2], true)) {
                DB::rollBack();
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

            $deleted = DB::table('tblproductionconsumption')
                ->where('code', $master->code)
                ->where('business_id', auth()->user()->business_id)
                ->where('company_id', auth()->user()->company_id)
                ->where('branch_id', $master->branch_id)
                ->delete();

            if (!$deleted) {
                DB::rollBack();
                return $this->jsonErrorResponse($data, 'Production & Consumption entry not found.', 404);
            }
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }

        DB::commit();
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
