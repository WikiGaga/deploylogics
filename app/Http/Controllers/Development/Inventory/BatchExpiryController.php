<?php

namespace App\Http\Controllers\Inventory;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Library\ApiUtilities;
use App\Library\Utilities;
use App\Models\TblDefiStore;
use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcProductBarcodeDtl;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Validator;


class BatchExpiryController extends Controller
{
    public static $page_title = 'Batch Expiry';
    public static $redirect_url = 'batch-expiry';
    public static $menu_dtl_id = '327';

    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'batch_expiry';
        $data['menu_id'] = self::$menu_dtl_id;
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage . self::$redirect_url;
        $data['page_data']['create'] = '/' . self::$redirect_url . $this->prefixCreatePage;
        //$data['current_branch'] = auth()->user()->branch_id;
        $data['stock_code_type'] = 'bt';

        if (isset($id)) {
            if (TblInveStock::where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])
                ->where(Utilities::currentBCB())
                ->exists()) {
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id . '-edit';
                $data['id'] = $id;
                $data['current'] = TblInveStock::where('stock_id','LIKE',$id)->where('stock_code_type',$data['stock_code_type'])
                    ->with('stock_dtls','store','location')
                    ->where(Utilities::currentBCB())
                    ->first();

//                $data['page_data']['print'] = '/' . self::$redirect_url . '/print/' . $id;

            } else {
                abort('404');
            }
        } else {
            $data['permission'] = self::$menu_dtl_id . '-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type' => 'branch',
                'model' => 'TblInveStock',
                'code_field' => 'stock_code',
                'code_prefix' => strtoupper('bt'),
                'code_type_field' => 'stock_code_type',
                'code_type' => 'bt',
            ];

            $data['document_code'] = Utilities::documentCode($doc_data);
        }

        $data['store'] = TblDefiStore::select('store_id','store_name','store_default_value')->where('store_entry_status',1)->where(Utilities::currentBCB())->get();

        return view('inventory.batch_expiry.form', compact('data'));
    }


    public function store(Request $request, $id = null)
    {
//         dd($request->all());
        if (empty($request->pd)) {
            return $this->returnjsonerror("Please Enter Product Detail", 201);
        }
        $data = [];
        $validator = Validator::make($request->all(), [
            'stock_date' => 'required|date_format:d-m-Y',
            'store_id' => 'required',
            'pd.*.product_id' => 'required|numeric',
            'pd.*.product_barcode_id' => 'required|numeric',
            'pd.*.uom_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try {
            if (isset($id)) {
                $stock = TblInveStock::where('stock_id',$id)->first();
            } else {
                $stock = new TblInveStock();
                $stock->stock_id = Utilities::uuid();
                $doc_data = [
                    'biz_type' => 'branch',
                    'model' => 'TblInveStock',
                    'code_field' => 'stock_code',
                    'code_prefix' => strtoupper('bt'),
                    'code_type_field' => 'stock_code_type',
                    'code_type' => 'bt',
                ];
                $stock->stock_code = Utilities::documentCode($doc_data);

            }
            $stock->stock_store_from_id = $request->store_id;
            $stock->stock_location_id = $request->stock_location_id;
            $stock->stock_date = date('Y-m-d', strtotime($request->stock_date));
            $stock->stock_remarks = $request->stock_remarks;
            $stock->stock_menu_id = self::$menu_dtl_id;
            $stock->stock_total_qty =  0;
            $stock->stock_total_amount =  0;
            $stock->stock_entry_status = 1;
            $stock->branch_id = auth()->user()->branch_id;
            $stock->business_id = auth()->user()->business_id;
            $stock->company_id = auth()->user()->company_id;
            $stock->stock_user_id = auth()->user()->id;
            $stock->stock_code_type = 'bt';
            $stock->stock_device_id = 2;
            $stock->save();

            $form_id = $stock->stock_id;

            $del_Dtls = TblInveStockDtl::where('stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveStockDtl::where('stock_dtl_id',$del_Dtl->stock_dtl_id)->delete();
            }

            if(isset($request->pd)){
                $key = 1;
                foreach($request->pd as $pd){
                    $dtl = new TblInveStockDtl();
                    $dtl->stock_id =  $stock->stock_id;
                    $dtl->stock_dtl_id =  Utilities::uuid();
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = $pd['product_barcode_id'];
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->product_barcode_barcode = $pd['pd_barcode'];
                    $dtl->stock_dtl_packing = $pd['pd_packing'];
                    $dtl->stock_dtl_batch_no = $pd['batch_no'];
                    $dtl->stock_dtl_quantity = $pd['quantity'];
                    $dtl->stock_dtl_rate = $pd['rate'];
                    $dtl->stock_dtl_amount = $pd['amount'];
                    $dtl->stock_dtl_production_date =  isset($pd['production_date'])?date('Y-m-d', strtotime($pd['production_date'])):"";
                    $dtl->stock_dtl_expiry_date =  isset($pd['expiry_date'])?date('Y-m-d', strtotime($pd['expiry_date'])):"";
                    $dtl->stock_dtl_qty_base_unit =  Helper::conversionBaseUnitQty(['pd_packing'=>$pd['pd_packing'],'quantity'=>$pd['quantity']]);
                    $dtl->business_id = auth()->user()->branch_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->dtl_user_id = auth()->user()->id;
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->save();

                    $barcodeDtl = TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])
                        ->where('branch_id',auth()->user()->branch_id);
                    if($barcodeDtl->exists()){
                        $barcodeDtl = $barcodeDtl->first();
                        $barcodeDtl->product_barcode_shelf_stock_location = $request->stock_location_id;
                        $barcodeDtl->product_barcode_shelf_stock_sales_man = auth()->user()->id;
                        $barcodeDtl->save();
                    }else{
                        TblPurcProductBarcodeDtl::create([
                            'product_barcode_dtl_id' => Utilities::uuid(),
                            'product_barcode_id' => $pd['product_barcode_id'],
                            'branch_id' => auth()->user()->branch_id,
                            'product_barcode_shelf_stock_location' => $request->stock_location_id,
                            'product_barcode_shelf_stock_sales_man' => auth()->user()->id,
                            'product_barcode_stock_limit_neg_stock' => 0,
                            'product_barcode_stock_limit_limit_apply' => 0,
                            'product_barcode_stock_limit_status' => 0,
                            'product_barcode_tax_apply' => 0,
                        ]);
                    }
                }
            }

        } catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        if (isset($id)) {
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage . self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        } else {
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/' . self::$redirect_url . $this->prefixCreatePage . '/' . $form_id;
            $data['form_id'] = $form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try {
            $dataExist = TblInveStock::where('stock_id',$id)->exists();
            if ($dataExist === true) {
                $stock = TblInveStock::where('stock_id',$id)->first();
                $stock->stock_dtls()->delete();
                $stock->delete();
            } else {
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

        } catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }

}
