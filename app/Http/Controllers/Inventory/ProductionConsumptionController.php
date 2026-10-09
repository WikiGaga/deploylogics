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
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class ProductionConsumptionController extends Controller
{
    use HasStaging;

    public static $page_title = 'Production & Consumption';
    public static $menu_dtl_id = '336';
    public static $redirect_url = 'production-consumption';

    protected function productionConsumptionBranchScope(): array
    {
        return [
            ['business_id', auth()->user()->business_id],
            ['company_id', auth()->user()->company_id],
            ['branch_id', auth()->user()->branch_id],
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

    protected function findProductionConsumptionMaster(string $code): ?TblProductionConsumption
    {
        $branchScope = $this->productionConsumptionBranchScope();

        $master = TblProductionConsumption::where('code', $code)
            ->where($branchScope)
            ->where(function ($q) {
                $q->where('sr_no', 1)->orWhere('sr_no', '1');
            })
            ->first();

        if ($master) {
            return $master;
        }

        return TblProductionConsumption::where('code', $code)
            ->where($branchScope)
            ->orderBy('sr_no')
            ->first();
    }

    protected function captureProductionConsumptionStagingSnapshot(string $code): ?array
    {
        $master = $this->findProductionConsumptionMaster($code);
        if (!$master || (int) ($master->staging_apply ?? 0) !== 1) {
            return null;
        }

        return [
            'staging_apply' => (int) $master->staging_apply,
            'current_stg_id' => $master->current_stg_id,
            'posted' => (int) ($master->posted ?? 0),
        ];
    }

    protected function syncProductionConsumptionStagingRows($master, string $code): void
    {
        $freshMaster = $this->findProductionConsumptionMaster($code);
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
            ->where($this->productionConsumptionBranchScope())
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

        if (isset($id)) {
            $branchScope = function ($query) {
                return $query->where('business_id', auth()->user()->business_id)
                    ->where('company_id', auth()->user()->company_id)
                    ->where('branch_id', auth()->user()->branch_id);
            };

            if (TblProductionConsumption::where('code', 'LIKE', $id)->where($branchScope)->exists()) {
                $data['permission'] = self::$menu_dtl_id . '-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $lines = TblProductionConsumption::with(['barcode.product', 'barcode.uom'])
                    ->where('code', $id)
                    ->where($branchScope)
                    ->orderBy('sr_no')
                    ->get();
                $data['lines'] = $lines;
                $data['current'] = $lines->first();
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
                'biz_type'    => 'branch',
                'model'       => 'TblProductionConsumption',
                'code_field'  => 'code',
                'code_prefix' => strtoupper('pc'),
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }

        $data['store'] = TblDefiStore::select('store_id','store_name','store_default_value')->where('store_entry_status',1)->where(Utilities::currentBCB())->get();

        return view('inventory.production_consumption.form', compact('data'));
    }

    public function store(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'record_date'       => 'required|date_format:d-m-Y',
            'transfer_from'     => 'required|numeric|not_in:0',
            'transfer_to'       => 'required|numeric|not_in:0',
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

        DB::beginTransaction();

        try {
            $recordDate = date('Y-m-d', strtotime($request->record_date));
            $branchScope = $this->productionConsumptionBranchScope();
            $isNew = !isset($id);
            $preservedStaging = null;

            if (!$isNew) {
                $master = $this->findProductionConsumptionMaster($id);
                if (!$master) {
                    throw new RuntimeException('Production & Consumption entry not found.', 404);
                }

                $formId = $id;
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
                    $data['redirect'] = $this->documentFormStayRedirect('/' . self::$redirect_url, $formId);
                    return $this->jsonSuccessResponse($data, trans('message.update'), 200);
                }

                $preservedStaging = $this->captureProductionConsumptionStagingSnapshot($id);

                DB::table('tblproductionconsumption')
                    ->where('code', $id)
                    ->where($branchScope)
                    ->delete();
                $code = $id;
            } else {
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblProductionConsumption',
                    'code_field'        => 'code',
                    'code_prefix'       => strtoupper('pc')
                ];
                $code = Utilities::documentCode($doc_data);
            }

            $srNo = 1;
            foreach ($request->pd as $entry) {
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
                    'transfer_from' => $request->transfer_from ?? null,
                    'transfer_to'   => $request->transfer_to ?? null,
                    'business_id'   => auth()->user()->business_id,
                    'company_id'    => auth()->user()->company_id,
                    'branch_id'     => auth()->user()->branch_id,
                    'status'        => 1,
                    'posted'        => 0,
                    'cancel'        => 0,
                    'staging_apply' => 0,
                    'current_stg_id'=> null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];

                if ($preservedStaging !== null) {
                    $row['staging_apply'] = $preservedStaging['staging_apply'];
                    $row['current_stg_id'] = $preservedStaging['current_stg_id'];
                    $row['posted'] = $preservedStaging['posted'];
                }

                DB::table('tblproductionconsumption')->insert($row);
            }

            $master = $this->findProductionConsumptionMaster($code);
            if (!$master) {
                throw new RuntimeException('Unable to load saved Production & Consumption entry.', 500);
            }

            $formId = $code;
            $this->finalizeDocumentStaging($request, self::$menu_dtl_id, $formId, $master, $isNew, [
                'notification' => $this->productionConsumptionStagingNotificationOptions(),
                'posted_when_exempt' => 0,
                'stg_log_posted_when_exempt' => 0,
                'preserved_staging' => $preservedStaging,
                'sync_after_save' => function ($model) use ($formId) {
                    $this->syncProductionConsumptionStagingRows($model, $formId);
                },
            ]);

            $this->syncProductionConsumptionStagingRows($master, $formId);

            DB::commit();

            if (!$isNew) {
                $data = array_merge($data, Utilities::returnJsonEditForm());
                $data['redirect'] = $this->documentFormStayRedirect('/' . self::$redirect_url, $formId);
                return $this->jsonSuccessResponse($data, trans('message.update'), 200);
            }

            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $code;
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
            $master = $this->findProductionConsumptionMaster($id);
            if (!$master) {
                DB::rollBack();
                return $this->jsonErrorResponse($data, 'Production & Consumption entry not found.', 404);
            }

            $posted = (int) ($master->posted ?? 0);
            if (in_array($posted, [1, 2], true)) {
                DB::rollBack();
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

            $deleted = DB::table('tblproductionconsumption')
                ->where('code', $id)
                ->where('business_id', auth()->user()->business_id)
                ->where('company_id', auth()->user()->company_id)
                ->where('branch_id', auth()->user()->branch_id)
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
