<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblProductionConsumption;
use App\Library\Utilities;
use App\Models\TblDefiStore;
use App\Models\Defi\TblDefiConstants;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ProductionConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Production & Consumption';
    public static $menu_dtl_id = '336';
    public static $redirect_url = 'production-consumption';


    public function create(Request $request, $id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'production-consumption';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;

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
                $lines = TblProductionConsumption::with(['product', 'barcode', 'uom'])
                    ->where('code', $id)
                    ->where($branchScope)
                    ->orderBy('sr_no')
                    ->get();
                $data['lines'] = $lines;
                $data['current'] = $lines->first();
                $data['document_code'] = $data['current']->code;
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

        $arr = [
            'biz_type'   => 'branch',
            'code'       => $data['document_code'],
            'link'       => $data['page_data']['create'],
            'table_name' => 'tblproductionconsumption',
            'col_id'     => 'code',
            'col_code'   => 'code',
        ];

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
            'status'            => 'required|in:1,2',
            'pd'           => 'required|array',
            'pd.*.sr_no'   => 'required|integer',
            'pd.*.pd_barcode' => 'required|string|max:50',
            // 'pd.*.stock_type' => 'required|string|max:50',
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

            $branchScope = [
                ['business_id', auth()->user()->business_id],
                ['company_id', auth()->user()->company_id],
                ['branch_id', auth()->user()->branch_id],
            ];

            if ($id) {
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

            foreach ($request->pd as $entry) {
                DB::table('tblproductionconsumption')->insert([
                    'code'          => $code,
                    'record_date'   => $recordDate,
                    'type'          => 'PC',
                    'sr_no'         => $entry['sr_no'],
                    // 'stock_type'    => $entry['stock_type'],
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
                    'status'        => $request->status ?? 1,
                    'posted'        => $request->posted ?? 0,
                    'cancel'        => $request->has('cancel') ? 1 : 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::commit();

            if (isset($id)) {
                $data = array_merge($data, Utilities::returnJsonEditForm());
                $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
                return $this->jsonSuccessResponse($data, trans('message.update'), 200);
            } else {
                $data = array_merge($data, Utilities::returnJsonNewForm());
                $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $code;
                return $this->jsonSuccessResponse($data, trans('message.create'), 200);
            }

        } catch (QueryException $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse($data, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();

        try {
            $deleted = DB::table('tblproductionconsumption')
                ->where('code', $id)
                ->where('business_id', auth()->user()->business_id)
                ->where('company_id', auth()->user()->company_id)
                ->where('branch_id', auth()->user()->branch_id)
                ->delete();

            if (!$deleted) {
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
