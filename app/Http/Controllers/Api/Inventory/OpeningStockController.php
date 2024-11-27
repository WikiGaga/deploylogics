<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Helpers\Helper;
use App\Library\Utilities;
use App\Models\TblPurcProductBarcodeDtl;
use Exception;
use Validator;
use App\Models\TblDefiStore;
use App\Models\TblInveStock;
use Illuminate\Http\Request;
use App\Library\ApiUtilities;
use App\Models\TblInveStockDtl;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use App\Models\TblInveItemStockAdjustment;
use App\Models\TblInveItemStockAdjustmentDtl;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OpeningStockController extends ApiController
{
    public static $page_title = 'Batch Expiry';
    public static $prefix = 'bt';
    public static $menu_id = 54;

    public function create(Request $request, $id = null){

        $business_id = $request['business_id'];
        $branch_id = $request['branch_id'];
        $id = isset($request->id)?$request->id:"";

        $currentBC = [
            ['business_id', $business_id],
            ['company_id',$business_id]
        ];
        $currentBCB = [
            ['business_id', $business_id],
            ['company_id',$business_id],
            ['branch_id',$branch_id]
        ];

        $data = [];
        $data['stock_code_type'] = self::$prefix;
        $data['title'] = self::$page_title;

        if(!empty($id)){
            if(TblInveStock::where('stock_id',$id)->where('stock_code_type',$data['stock_code_type'])
            ->where($currentBCB)
        ->exists()){
                $data['action'] = 'edit';
                $data['id'] = $id;
                $current = TblInveStock::where('stock_id','LIKE',$id)->where('stock_code_type',$data['stock_code_type'])
                ->with('stock_dtls','store','location')
                ->where($currentBCB)
                ->first();
                $data['current'] = $this->FilterData($current);
            }else{
                return $this->ApiJsonErrorResponse($data,'Not Found');
            }
        }else{
            $data['action'] = 'save';
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblInveStock',
                'code_field'        => 'stock_code',
                'code_prefix'       => strtoupper($data['stock_code_type']),
                'code_type_field'   => 'stock_code_type',
                'code_type'         => $data['stock_code_type'],
                'business_id'      => $business_id,
                'branch_id'      => $branch_id,

            ];
            $data['document_code'] = ApiUtilities::documentCode($doc_data);
        }
        $data['store'] = TblDefiStore::select('store_id','store_name','store_default_value')->where('store_entry_status',1)->where($currentBCB)->get();

        if(count($data['store']) == 0){
            $data['store'] = (object)[];
        }

        return $this->ApiJsonSuccessResponse($data,'form data');
    }

    public function store(Request $request , $id = null){
        $data = [];
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;
        $user_id = $request->user_id;
        $currentBC = [
            ['business_id', $business_id],
            ['company_id',$business_id]
        ];
        $currentBCB = [
            ['business_id', $business_id],
            ['company_id',$business_id],
            ['branch_id',$branch_id]
        ];
        //$id = isset($request->id)?$request->id:"";

        $validator = Validator::make($request->all(), [
            'stock_date' => 'required|date_format:d-m-Y',
            'store_id' => 'required',
            // 'stock_location_id' => 'required',
            'pd.*.product_id' => 'required|numeric',
            'pd.*.product_barcode_id' => 'required|numeric',
            'pd.*.uom_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->ApiJsonErrorResponse($data, trans('message.required_fields'));
        }
        if(empty($request->pd)){
            return $this->ApiJsonErrorResponse($data,"Please Enter Product Detail");
        }
        DB::beginTransaction();
        try{

            if(isset($id)){
                $stock = TblInveStock::where('stock_id',$id)->first();
            }else{

                $stock = new TblInveStock();
                $stock->stock_id =  ApiUtilities::uuid();

                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblInveStock',
                    'code_field'        => 'stock_code',
                    'code_prefix'       => strtoupper(self::$prefix),
                    'code_type_field'   => 'stock_code_type',
                    'code_type'         => self::$prefix,
                    'business_id'      => $business_id,
                    'branch_id'      => $branch_id

                ];

                $stock->stock_code = ApiUtilities::documentCode($doc_data);
            }

            $stock->stock_store_from_id = $request->store_id;
            $stock->stock_location_id = $request->stock_location_id;
            $stock->stock_date = date('Y-m-d', strtotime($request->stock_date));
            $stock->stock_remarks = $request->notes;
            $stock->stock_menu_id = self::$menu_id;
            $stock->stock_total_qty =  0;
            $stock->stock_total_amount =  0;
            $stock->stock_entry_status = 1;
            $stock->business_id = $business_id;
            $stock->company_id = $business_id;
            $stock->branch_id = $branch_id;
            $stock->stock_user_id = $user_id;
            $stock->stock_code_type = self::$prefix;
            $stock->stock_device_id = 2;
            $stock->save();

            $del_Dtls = TblInveStockDtl::where('stock_id',$id)->get();
            foreach ($del_Dtls as $del_Dtl){
                TblInveStockDtl::where('stock_dtl_id',$del_Dtl->stock_dtl_id)->delete();
            }
            if(isset($request->pd)){
                $key = 1;
                foreach($request->pd as $pd){
                    $dtl = new TblInveStockDtl();
                    $dtl->stock_id =  $stock->stock_id;
                    $dtl->stock_dtl_id =  ApiUtilities::uuid();
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
                    $dtl->business_id = $business_id;
                    $dtl->company_id = $business_id;
                    $dtl->branch_id = $branch_id;
                    $dtl->dtl_user_id = $user_id;
                    $dtl->stock_dtl_sr_no =  $key++;
                    $dtl->save();

                    $barcodeDtl = TblPurcProductBarcodeDtl::where('product_barcode_id',$pd['product_barcode_id'])
                        ->where('branch_id',$branch_id);
                    if($barcodeDtl->exists()){
                        $barcodeDtl = $barcodeDtl->first();
                        $barcodeDtl->product_barcode_shelf_stock_location = $request->stock_location_id;
                        $barcodeDtl->product_barcode_shelf_stock_sales_man = $user_id;
                        $barcodeDtl->save();
                    }else{
                        TblPurcProductBarcodeDtl::create([
                            'product_barcode_dtl_id' => Utilities::uuid(),
                            'product_barcode_id' => $pd['product_barcode_id'],
                            'branch_id' => $branch_id,
                            'product_barcode_shelf_stock_location' => $request->stock_location_id,
                            'product_barcode_shelf_stock_sales_man' => $user_id,
                            'product_barcode_stock_limit_neg_stock' => 0,
                            'product_barcode_stock_limit_limit_apply' => 0,
                            'product_barcode_stock_limit_status' => 0,
                            'product_barcode_tax_apply' => 0,
                        ]);
                    }
                }
            }

        }catch (QueryException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage(), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage(), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            return $this->ApiJsonSuccessResponse($data,trans('message.update'));
        }else{
            return $this->ApiJsonSuccessResponse($data,trans('message.create'));
        }

    }

    public function FilterData($current){
        $stock_dtl = [];
        $i = 0; $k = 1;
        if(isset($current->stock_dtls) && !empty($current->stock_dtls)){
            foreach($current->stock_dtls as $dtl){
                $stock_dtl[$i]["sr_no"] = $k;
                $stock_dtl[$i]["product_id"] = $dtl->product->product_id;
                $stock_dtl[$i]["product_name"] = $dtl->product->product_name;
                $stock_dtl[$i]["product_barcode_id"] = $dtl->barcode->product_barcode_id;
                $stock_dtl[$i]["pd_barcode"] = $dtl->barcode->product_barcode_barcode;
                $stock_dtl[$i]["uom_id"] = $dtl->uom->uom_id;
                $stock_dtl[$i]["uom_name"] = $dtl->uom->uom_name;
                $stock_dtl[$i]["pd_packing"] = $dtl->barcode->product_barcode_packing;
                $stock_dtl[$i]["quantity"] = ($dtl->stock_dtl_quantity != null) ? $dtl->stock_dtl_quantity : "";
                $stock_dtl[$i]["rate"] = ($dtl->stock_dtl_rate != null) ? $dtl->stock_dtl_rate: "";
                $stock_dtl[$i]["amount"] = ($dtl->stock_dtl_amount != null) ? $dtl->stock_dtl_amount : "";
                $stock_dtl[$i]["batch_no"] = !empty($dtl->stock_dtl_batch_no)?$dtl->stock_dtl_batch_no:"";
                $prod_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_production_date))));
                $stock_dtl[$i]["production_date"] =  ($prod_date == '01-01-1970') ? '' : $prod_date;
                $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->stock_dtl_expiry_date))));
                $stock_dtl[$i]["expiry_date"] = ($expiry_date == '01-01-1970') ? '' : $expiry_date;
                $i = $i+1;
                $k = $k+1;
            }
        }
        $stock = (object) [
            "business_id" 		=> 	$current->business_id,
            "branch_id" 		=> 	$current->branch_id,
            "user_id" 			=> 	$current->stock_user_id,
            "stock_code"        =>  $current->stock_code,
            "store_id"			=>	$current->stock_store_from_id,
            "store_name"			=>	$current->store->store_name,
            "stock_location_id"	=>	$current->stock_location_id??'',
            "stock_location_name"	=>	$current->location->display_location_name_string??'',
            "stock_date"        =>  date('d-m-Y', strtotime(trim(str_replace('/','-',$current->stock_date)))),
            "notes"             =>  !empty($current->stock_remarks) ? $current->stock_remarks : "",
            "pd"                =>  $stock_dtl,
        ];
        return $stock;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $stock= TblInveStock::where('stock_id',$id)->first();
            $stock->stock_dtls()->delete();
            $stock->delete();

        }catch (QueryException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
        return $this->ApiJsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
