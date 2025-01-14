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
            if (TblProductionConsumption::where('code', 'LIKE', $id)->exists()) {
                $data['permission'] = self::$menu_dtl_id . '-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblProductionConsumption::where('code', $id)->first();
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
        dd($request->all());
        $data = [];
        $validator = Validator::make($request->all(), [
            'record_date'       => 'required|date_format:Y-m-d',
            'type'              => 'required|string|max:50',
            'pd'           => 'required|array',
            'pd.*.sr_no'   => 'required|integer',
            'pd.*.item_code' => 'required|string|max:50',
            'pd.*.stock_type' => 'required|string|max:50',
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
            $recordDate = $request->record_date;
            $type = $request->type;

            // For update, delete existing records for the given ID and type
            if ($id) {
                DB::table('your_table_name')
                    ->where('code', $id)
                    ->where('type', $type)
                    ->delete();
            }

            // Insert new records
            foreach ($request->entries as $entry) {
                DB::table('your_table_name')->insert([
                    'code'          => $id ?? Utilities::generateCode('your_table_prefix'),
                    'record_date'   => $recordDate,
                    'type'          => $type,
                    'sr_no'         => $entry['sr_no'],
                    'stock_type'    => $entry['stock_type'],
                    'item_code'     => $entry['item_code'],
                    'qty'           => $entry['qty'],
                    'rate'          => $entry['rate'],
                    'amount'        => $entry['amount'],
                    'remarks'       => $entry['remarks'] ?? null,
                    'user_id'       => auth()->user()->id,
                    'transfer_from' => $request->transfer_from ?? null,
                    'transfer_to'   => $request->transfer_to ?? null,
                    'business_id'   => auth()->user()->business_id,
                    'company_id'    => auth()->user()->company_id,
                    'branch_id'     => auth()->user()->branch_id,
                    'status'        => $request->status ?? 1,
                    'posted'        => $request->posted ?? 0,
                    'cancel'        => $request->cancel ?? 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::commit();

            $data['redirect'] = route('your_route_name.index'); // Adjust the redirect route
            return $this->jsonSuccessResponse($data, $id ? 'Record Updated Successfully' : 'Record Created Successfully', 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 500);
        }
    }



}
