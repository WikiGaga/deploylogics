<?php

namespace App\Http\Controllers\Api\Product;

use App\Helpers\Helper;
use App\Library\Utilities;
use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Models\TblPurcProduct;
use Illuminate\Support\Facades\DB;
use App\Models\TblPurcProductBarcode;
use App\Http\Controllers\ApiController;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use Illuminate\Validation\ValidationException;

class ProductBarcodeController extends ApiController
{
    public function getBarcodeDtl(Request $request)
    {
        $data = [];
        DB::beginTransaction();
        try {
            $barcode = $request->barcode;
            $form_type = $request->form_type;
            $form_id = isset($request->form_id)?$request->form_id:""; //a external selected *form* like in grnForm select poEntry that is po_id
            $store_id = isset($request->store_id)?$request->store_id:""; //a external selected *form* like in grnForm select poEntry that is po_id
            $business_id = $request->business_id;
            $branch_id = $request->branch_id;
            $currentBC = [
                ['business_id', $business_id],
                ['company_id',$business_id]
            ];
            $currentBCB = [
                ['business_id', $business_id],
                ['company_id',$business_id],
                ['branch_id',$branch_id]
            ];
            $arr = [
                'currentBC' => $currentBC,
                'currentBCB' => $currentBCB,
                'barcode' => $barcode,
                'form_type' => $form_type,
                'form_id' => $form_id,
                'branch_id' => $branch_id,
                'business_id' => $business_id,
                'company_id' => $business_id,
                'product_exists' =>   false,
                'product_exists_msg' =>   "",
                'store_id' =>   $store_id
            ];
            $d =  $this->getBarcodeVerify($arr);
        //    dd($d);
            if(!isset($d->original)){
                $msg = 'barcode data';
                if($d['barcode_type'] == 'product_verify'){
                    $msg = 'Barcode is not perishable. Are you sure add this?';
                }
                return $this->ApiJsonSuccessResponse($d,$msg);
            }else{
                return $this->ApiJsonErrorResponse([], $d->original['message']);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }

    public function getBarcodeVerify($arr)
    {
        /*
         [currentBC, currentBCB, barcode, form_type, form_id]
         * */
        $data = [];
        DB::beginTransaction();
        try {

            $val = $arr['barcode'];
            if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$val)->exists()) {
                $code = $val;
            }else{
                $weight_prod = substr($val, 0, 7);
                if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$weight_prod)->where('product_barcode_weight_apply',1)->exists()){
                    $code = $weight_prod;
                }
            }
            if(!isset($code) && empty($code)){
                if(TblPurcProduct::where('product_code','LIKE',$val)->exists()) {
                    $product = TblPurcProduct::where('product_code','LIKE',$val)->first();
                    $barcode = TblPurcProductBarcode::where('product_id','LIKE',$product->product_id)->first();
                    $code = $barcode->product_barcode_barcode;
                }
            }
            if(!isset($code) && empty($code)){
                $qry = "select PRODUCT_BARCODE_BARCODE from tbl_PURC_PRODUCT_BARCODE where SUBSTR(PRODUCT_BARCODE_BARCODE, -6, 6) = '$val'";
                $getCode = DB::SelectOne($qry);
                if(!empty($getCode)){
                    $code = $getCode->product_barcode_barcode;
                }
            }
            if(isset($code) && !empty($code)){
                $arr['code'] = $code;
                $form_types = ['grn','sa','d','os'];
                if(in_array($arr['form_type'],$form_types)){
                    if($arr['form_type'] == 'grn'){
                        if(!empty($arr['form_id'])){
                            return $this->issetBarcodeInPO($arr); // Goods Receive Notes
                        }else{
                            return $this->getBarcodeDtlGRN($arr); // Goods Receive Notes
                        }
                    }elseif($arr['form_type'] == 'sa'){
                        return $this->getBarcodeDtlSA($arr); // Stock Adjustment / Stock Taking
                    }elseif($arr['form_type'] == 'd'){
                        return $this->getBarcodeDtlPD($arr); // Purchase Demand
                    }elseif($arr['form_type'] == 'os'){
                        return $this->getBarcodeDtlOS($arr); // Purchase Demand
                    }
                }else{
                    return $this->ApiJsonErrorResponse($data, 'Form type not exists');
                }
            }else{
                return $this->ApiJsonErrorResponse($data, 'Barcode not exists');
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }

    public function getBarcodeBasicDetail($arr,$with=""){
        $data = [];
        DB::beginTransaction();
        try {
             $barcode_dtl = TblPurcProductBarcode::with($with.'product','uom','barcode_dtl')
                 ->where('product_barcode_barcode',$arr['barcode'])->first();

             $max_limit = 0;
             foreach ($barcode_dtl->barcode_dtl as $dtl){
                if($dtl['branch_id'] == $arr['branch_id']){
                    $max_limit = !empty($dtl['product_barcode_shelf_stock_max_qty'])?$dtl['product_barcode_shelf_stock_max_qty']:0;
                    break;
                }
             }
            $arr = [
                "product_id"=> $barcode_dtl->product->product_id,
                "product_name"=> $this->strUcWords($barcode_dtl->product->product_name),
                "product_barcode_id"=> $barcode_dtl->product_barcode_id,
                "product_barcode"=> $barcode_dtl->product_barcode_barcode,
                "uom_id"=> $barcode_dtl->uom_id,
                "uom_name"=> $barcode_dtl->uom->uom_name,
                "product_barcode_packing"=> $barcode_dtl->product_barcode_packing,
                "product_perishable"=> $barcode_dtl->product->product_perishable,
                'max_limit' => $max_limit,
                'product_barcode_stock_cons_day' => $barcode_dtl->product_barcode_stock_cons_day
            ];
            //dd($arr);
            return $arr;
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }

    public function getBarcodePurcRate($product_barcode_id,$currentBCB){
        $purc_rate = TblPurcProductBarcodePurchRate::where('product_barcode_id',$product_barcode_id)
            ->where($currentBCB)->first(['product_barcode_purchase_rate','product_barcode_cost_rate','product_barcode_avg_rate']);
        return $purc_rate;
    }

    public function finalizaedData($arr){
        $data = [];
        DB::beginTransaction();
        try {
            $product_id					= isset($arr['data']['product_id'])?$arr['data']['product_id']:"";
            $product_name				= isset($arr['data']['product_name'])?$arr['data']['product_name']:"";
            $product_barcode_id			= isset($arr['data']['product_barcode_id'])?$arr['data']['product_barcode_id']:"";
            $product_barcode			= isset($arr['data']['product_barcode'])?$arr['data']['product_barcode']:"";
            $uom_id						= isset($arr['data']['uom_id'])?$arr['data']['uom_id']:"";
            $uom_name					= isset($arr['data']['uom_name'])?$arr['data']['uom_name']:"";
            $product_barcode_packing	= isset($arr['data']['product_barcode_packing'])?$arr['data']['product_barcode_packing']:"";
            $qty						= isset($arr['data']['product_barcode_qty'])?$arr['data']['product_barcode_qty']:"";
            $foc_qty					= isset($arr['data']['product_barcode_foc_qty'])?$arr['data']['product_barcode_foc_qty']:"";
            $fc_rate					= isset($arr['data']['product_barcode_fc_rate'])?$arr['data']['product_barcode_fc_rate']:"";
            $rate						= isset($arr['data']['product_barcode_rate'])?$arr['data']['product_barcode_rate']:"";
            $amount						= isset($arr['data']['product_barcode_amount'])?$arr['data']['product_barcode_amount']:"";
            $disc_perc					= isset($arr['data']['product_barcode_disc_perc'])?$arr['data']['product_barcode_disc_perc']:"";
            $disc_amount				= isset($arr['data']['product_barcode_disc_amount'])?$arr['data']['product_barcode_disc_amount']:"";
            $tax_value					= isset($arr['data']['product_barcode_vat_perc'])?$arr['data']['product_barcode_vat_perc']:"";
            $tax_amount					= isset($arr['data']['product_barcode_vat_amount'])?$arr['data']['product_barcode_vat_amount']:"";
            $total_amount				= isset($arr['data']['product_barcode_total_amount'])?$arr['data']['product_barcode_total_amount']:"";
            $stock_quantity             = isset($arr['data']['stock_quantity']) ? $arr['data']['stock_quantity']:"";
            $product_exists             = isset($arr['data']['product_exists']) ? $arr['data']['product_exists']:false;
            $product_exists_msg         = isset($arr['data']['product_exists_msg']) ? $arr['data']['product_exists_msg']:"";
            $max_limit                  = isset($arr['data']['max_limit']) ? $arr['data']['max_limit']:0;
            $lop_qty                    = isset($arr['data']['lop_qty']) ? $arr['data']['lop_qty']:0;
            $purc_return_waiting_qty    = isset($arr['data']['purc_return_waiting_qty']) ? $arr['data']['purc_return_waiting_qty']:0;
            $consumption_qty            = isset($arr['data']['consumption_qty']) ? $arr['data']['consumption_qty']:0;
            $suggest_qty_1            = isset($arr['data']['suggest_qty_1']) ? $arr['data']['suggest_qty_1']:0;
            $suggest_qty_2            = isset($arr['data']['suggest_qty_2']) ? $arr['data']['suggest_qty_2']:0;

            $data['product_type'] =  isset($arr['product_type'])?$arr['product_type']:"";
            $data['barcode_type'] =  $arr['barcode_type'];
            $data['form_type'] =  $arr['form_type'];
            $data['product_exists'] =  $product_exists;
            $data['product_exists_msg'] =  $product_exists_msg;

            $data['barcode'] = [
                "product_id"=> $product_id,
                "product_name"=> $this->strUcWords($product_name),
                "product_barcode_id"=> $product_barcode_id,
                "product_barcode"=> $product_barcode,
                "uom_id"=> $uom_id,
                "uom_name"=> $uom_name,
                "stock_quantity" => $stock_quantity,
                "product_barcode_packing"=> $product_barcode_packing,
                "product_barcode_qty"=>$qty,
                "product_barcode_foc_qty"=>$foc_qty,
                "product_barcode_fc_rate"=>$fc_rate,
                "product_barcode_rate"=> $rate,
                "product_barcode_amount"=> $amount,
                "product_barcode_disc_perc"=> $disc_perc,
                "product_barcode_disc_amount"=> $disc_amount,
                "product_barcode_vat_perc"=> $tax_value,
                "product_barcode_vat_amount"=> $tax_amount,
                "product_barcode_total_amount"=> $total_amount,
                'max_limit' => $max_limit,
                'lop_qty' => $lop_qty,
                'purc_return_waiting_qty' => $purc_return_waiting_qty,
                'consumption_qty' => $consumption_qty,
                'suggest_qty_1' => $suggest_qty_1,
                'suggest_qty_2' => $suggest_qty_2,
            ];
            $data['uom_list'] = $this->UOMList($product_id);
            return $data;
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, 'Barcode data not exists');
        }
        DB::commit();
    }

    public function getBarcodeDetailByUOM(Request $request){
        $data = [];
        DB::beginTransaction();
        try {
            $uom_id = $request->uom_id;
            $product_id = $request->product_id;
            $form_type = $request->form_type;
            $form_id = isset($request->form_id)?$request->form_id:""; //a external selected *form* like in grnForm select poEntry that is po_id
            $business_id = $request->business_id;
            $branch_id = $request->branch_id;

            $productBarcode = TblPurcProductBarcode::where('product_id', $product_id)->where('uom_id', $uom_id)->first('product_barcode_barcode');

            if(isset($productBarcode->product_barcode_barcode)){
                $barcode = $productBarcode->product_barcode_barcode;
            }else{
                return $this->ApiJsonErrorResponse([], 'Barcode not exists');
            }

            $currentBC = [
                ['business_id', $business_id],
                ['company_id',$business_id]
            ];
            $currentBCB = [
                ['business_id', $business_id],
                ['company_id',$business_id],
                ['branch_id',$branch_id]
            ];
            $arr = [
                'currentBC' => $currentBC,
                'currentBCB' => $currentBCB,
                'barcode' => $barcode,
                'form_type' => $form_type,
                'form_id' => $form_id,
                'branch_id' => $branch_id,
                'business_id' => $business_id,
                'company_id' => $business_id
            ];
            $d =  $this->getBarcodeVerify($arr);
            //  dd($d);
            if(!isset($d->original)){
                $msg = 'barcode data';
                if($d['barcode_type'] == 'product_verify'){
                    $msg = 'Barcode is not perishable. Are you sure add this?';
                }
                return $this->ApiJsonSuccessResponse($d,$msg);
            }else{
                return $this->ApiJsonErrorResponse([], $d->original['message']);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }

    public function getBarcodeDetailByUOMOld(Request $request){
        $data = [];
        $val = $request->uom_id; // this is product_barcode_barcode
        $form_id = isset($request->form_id)?$request->form_id:""; // external selected *form* like in grnForm select poEntry that is po_id
        $form_type = $request->form_type; // current form type
        $product_id = $request->product_id; // this is product id in case pr purchase demand
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;

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

        $exist = TblPurcProduct::where('product_id','LIKE',$product_id)->exists();
        if($exist === false)
        {
            return $this->ApiJsonErrorResponse($data,'Product not exists');
        }
        $productBarcode = TblPurcProductBarcode::with('product','barcode_dtl','uom')
            ->whereHas('product', function ($query) use ($product_id) {
                $query->where('product_id',$product_id);
            })->where('uom_id', $val)->first();

        $barcode = $productBarcode->product_barcode_barcode;
        $barcode_id = $productBarcode->product_barcode_id;

        $purc_rate = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $barcode)
            ->where('product_barcode_id',$barcode_id)
            ->where('branch_id',$branch_id)->first();

        if($form_type == 'grn' && empty($form_id)){
            $product = TblPurcProductBarcode::with('product','uom')
                ->whereHas('product', function ($query){
                    $query->where('product_perishable',1);
                })->where('product_barcode_barcode', $barcode)
                ->where('business_id', $business_id)
                ->first();

            if(empty($product)){
                $data = (object)[];
                return $this->ApiJsonErrorResponse($data,'Product is not Perishable');
            }else{
                $dtl = TblPurcProductBarcodeDtl::where('product_barcode_id',$product->product_barcode_id)
                    ->where('branch_id',$branch_id)->first();
                $data['barcode_type'] = 'grn';
                $data['barcode'] = [
                    "product_id"=>$product->product_id,
                    "product_barcode_id"=>$product->product_barcode_id,
                    "uom_id"=>$product->uom->uom_id,
                    "product_barcode"=>$product->product_barcode_barcode,
                    "product_name"=> $this->strUcWords($product->product->product_name),
                    "uom_name"=>$product->uom->uom_name,
                    "product_barcode_packing"=>$product->product_barcode_packing,
                    "product_barcode_qty"=>1,
                    "product_barcode_foc_qty"=>"",
                    "product_barcode_fc_rate"=>"",
                    "product_barcode_rate"=> $purc_rate->product_barcode_purchase_rate,
                    "product_barcode_amount"=> "",
                    "product_barcode_disc_perc"=> "",
                    "product_barcode_disc_amount"=> "",
                    "product_barcode_vat_perc"=>isset($dtl->product_barcode_tax_value)?$dtl->product_barcode_tax_value:0,
                    "product_barcode_vat_amount"=> "",
                    "product_barcode_total_amount"=> ""
                ];
            }
        }
        if($form_type == 'grn' && !empty($form_id)){
            $po_id = $form_id;
            $PODtls = TblPurcPurchaseOrderDtl::with('product','barcode','uom')
                ->where('purchase_order_id',$po_id)
                ->where('product_barcode_barcode', $barcode)
                ->where($currentBCB)
                ->first();
            if(empty($PODtls)){
                $data = (object)[];
                return $this->ApiJsonErrorResponse($data,'Product Not Found in Selected PO');
            }else{
                $data['barcode_type'] = 'po';
                $data['barcode'] = [
                    "product_id"=>$PODtls->product_id,
                    "product_barcode_id"=>$PODtls->product_barcode_id,
                    "uom_id"=>$PODtls->uom->uom_id,
                    "product_barcode"=>$PODtls->barcode->product_barcode_barcode,
                    "product_name"=> $this->strUcWords($PODtls->product->product_name),
                    "uom_name"=>$PODtls->uom->uom_name,
                    "product_barcode_packing"=>$PODtls->barcode->product_barcode_packing,
                    "product_barcode_qty"=>$PODtls->purchase_order_dtlquantity,
                    "product_barcode_foc_qty"=>isset($PODtls->purchase_order_dtlfoc_quantity)?$PODtls->purchase_order_dtlfoc_quantity:'',
                    "product_barcode_fc_rate"=>isset($PODtls->purchase_order_dtlfc_rate)?number_format($PODtls->purchase_order_dtlfc_rate,3):'',
                    "product_barcode_rate"=>isset($PODtls->purchase_order_dtlrate)?number_format($PODtls->purchase_order_dtlrate,3):'',
                    "product_barcode_amount"=>isset($PODtls->purchase_order_dtlamount)?number_format($PODtls->purchase_order_dtlamount,3):'',
                    "product_barcode_disc_perc"=>isset($PODtls->purchase_order_dtldisc_percent)?number_format($PODtls->purchase_order_dtldisc_percent,2):'',
                    "product_barcode_disc_amount"=>isset($PODtls->purchase_order_dtldisc_amount)?number_format($PODtls->purchase_order_dtldisc_amount,3):'',
                    "product_barcode_vat_perc"=>isset($PODtls->purchase_order_dtlvat_percent)?number_format($PODtls->purchase_order_dtlvat_percent,2):'',
                    "product_barcode_vat_amount"=>isset($PODtls->purchase_order_dtlvat_amount)?number_format($PODtls->purchase_order_dtlvat_amount,3):'',
                    "product_barcode_total_amount"=>isset($PODtls->purchase_order_dtltotal_amount)?number_format($PODtls->purchase_order_dtltotal_amount,3):'',
                ];
            }
        }

        if($exist){
            $data['uom_list'] = $this->UOMList($productBarcode->product_id);
        }
        return $this->ApiJsonSuccessResponse($data,'barcode data');
    }

    public function UOMList($product_id){
        $barcodes = TblPurcProductBarcode::with('uom')->where('product_id',$product_id)->get();
        $uom_list = [];
        foreach ($barcodes as $barcode){
            if($barcode['uom']['uom_entry_status'] == 1){
                $uom = [
                    'uom_id' => $barcode['uom']['uom_id'],
                    'uom_name' => $barcode['uom']['uom_name'],
                    'uom_entry_status' => $barcode['uom']['uom_entry_status'],
                ];
                array_push($uom_list,$uom);
            }
        }
        return $uom_list;
    }

    /*
    *
    * Conditional Call Back Based on Form Type
    *
    */

    public function getBarcodeDtlGRN($arr){
        $data = [];
        DB::beginTransaction();
        try {
            $arr['barcode_type'] = 'grn';
            $arr['product_type'] = 'common';
            $arr['data'] = $this->getBarcodeBasicDetail($arr);

            if($arr['data']['product_perishable'] == 1){
                $arr['product_type'] = 'product_perishable';
            }
            if($arr['data']['product_perishable'] == 0){
                $arr['barcode_type'] = 'product_verify';
            }
            $dtl = TblPurcProductBarcodeDtl::where('product_barcode_id',$arr['data']['product_barcode_id'])
                ->where($arr['currentBCB'])->first();
            $product_barcode_tax_value = 0;
            if(isset($dtl->product_barcode_tax_value) && $dtl->product_barcode_tax_apply == 1){
                $product_barcode_tax_value = $dtl->product_barcode_tax_value;
            }
            $getBarcodePurcRate = $this->getBarcodePurcRate($arr['data']['product_barcode_id'],$arr['currentBCB']);
            $rate = !empty($getBarcodePurcRate->product_barcode_purchase_rate)?$getBarcodePurcRate->product_barcode_purchase_rate:"";
            $data_values = [
                'product_barcode_foc_qty' => "",
                'product_barcode_fc_rate' => "",
                'product_barcode_qty' => "1",
                'product_barcode_rate' => $rate,
                'product_barcode_amount' => "",
                'product_barcode_disc_perc' => "",
                'product_barcode_disc_amount' => "",
                'product_barcode_vat_perc' => $product_barcode_tax_value,
                'product_barcode_vat_amount' => "",
                'product_barcode_total_amount' => "",
            ];

            $arr['data'] = array_merge($arr['data'],$data_values);

            return $this->finalizaedData($arr);
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }

    public function issetBarcodeInPO($arr){
        $arr['barcode_type'] = 'grn';
        $arr['product_type'] = 'common';
        $arr['data'] = $this->getBarcodeBasicDetail($arr);
        if($arr['data']['product_perishable'] == 1){
            $arr['product_type'] = 'product_perishable';
        }
        if($arr['data']['product_perishable'] == 0){
            $arr['barcode_type'] = 'product_verify';
        }
        $dtl = TblPurcPurchaseOrderDtl::where('product_barcode_id',$arr['data']['product_barcode_id'])
            ->where('purchase_order_id',$arr['form_id'])
            ->where($arr['currentBCB'])->first();

        if(empty($dtl) && $arr['data']['product_perishable'] == 1){
            return $this->getBarcodeDtlGRN($arr);
        }
        if(empty($dtl) && $arr['data']['product_perishable'] == 0){
            return $this->ApiJsonErrorResponse($arr, 'Barcode is not perishable and not exit in selected PO');
        }
        if(!empty($dtl)){
            $data_values = [
                'product_barcode_foc_qty' => $dtl['purchase_order_dtlfoc_quantity'],
                'product_barcode_fc_rate' => $dtl['purchase_order_dtlfc_rate'],
                'product_barcode_qty' => $dtl['purchase_order_dtlquantity'],
                'product_barcode_rate' => $dtl['purchase_order_dtlrate'],
                'product_barcode_amount' => $dtl['purchase_order_dtlamount'],
                'product_barcode_disc_perc' => $dtl['purchase_order_dtldisc_percent'],
                'product_barcode_disc_amount' => $dtl['purchase_order_dtldisc_amount'],
                'product_barcode_vat_perc' => $dtl['purchase_order_dtlvat_percent'],
                'product_barcode_vat_amount' => $dtl['purchase_order_dtlvat_amount'],
                'product_barcode_total_amount' => $dtl['purchase_order_dtltotal_amount'],
            ];
            $arr['data'] = array_merge($arr['data'],$data_values);
            return $this->finalizaedData($arr);
        }
    }

    public function getBarcodeDtlST($arr){ // Stock Taking
        $data = [];
        DB::beginTransaction();
        try {
            $arr['barcode_type'] = 'st'; // Stock Taking
            $arr['product_type'] = 'common';
            $arr['data'] = $this->getBarcodeBasicDetail($arr);

            if($arr['data']['product_perishable'] == 1){
                $arr['product_type'] = 'product_perishable';
            }
            if($arr['data']['product_perishable'] == 0){
                $arr['barcode_type'] = 'product_verify';
            }

            $arr['pd_packing'] = $arr['data']['product_barcode_packing'];

            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");
            $date = date('Y-m-d', strtotime($today_format));
            $itr = [
                $arr['data']['product_id'],
                $arr['data']['product_barcode_id'],
                $arr['branch_id'],
                $arr['business_id'],
                $arr['company_id'],
                '',
                $date
            ];

            $store_stock = '';
            $store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $itr))->first()->code;

            $data_values = [
                'stock_quantity' => $store_stock / (float)$arr['data']['product_barcode_packing'],
            ];

            $arr['data'] = array_merge($arr['data'],$data_values);

            return $this->finalizaedData($arr);

        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }

    public function getBarcodeDtlPD($arr)
    {

        $data = [];
        DB::beginTransaction();
        try {

            $arr['barcode_type'] = 'd';
            $arr['product_type'] = 'common';
            $arr['data'] = $this->getBarcodeBasicDetail($arr);

            if($arr['data']['product_perishable'] == 1){
                $arr['product_type'] = 'product_perishable';
            }
            if($arr['data']['product_perishable'] == 0){
                $arr['barcode_type'] = 'product_verify';
            }

            $pd_packing = !empty($arr['data']['product_barcode_packing'])?$arr['data']['product_barcode_packing']:1;

            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");
            $date = date('Y-m-d', strtotime($today_format));
            $itr = [
                $arr['data']['product_id'],
                $arr['data']['product_barcode_id'],
                $arr['branch_id'],
                $arr['business_id'],
                $arr['company_id'],
                '',
                $date
            ];
            $store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $itr))->first()->code;

            /* lpo qty
             * */

            $req = [
                'product_barcode_id' => $arr['data']['product_barcode_id'],
                'branch_id' => $arr['branch_id'],
            ];
            $lopQty = Utilities::lopQty($req);
            $lopqty = $lopQty / $pd_packing;
            /* Purc. Ret in waiting qty
             * */

            $PurcRetWaitingQty = Utilities::purcRetWaitingQty($req);

            $purc_return_waiting_qty = $PurcRetWaitingQty / $pd_packing;

            $consumption_days = $arr['data']['product_barcode_stock_cons_day'];

            $suggestQty1 = Utilities::SuggestedQty1($arr['data']['max_limit'], $store_stock);

            $suggestQty2 = Utilities::SuggestedQty2($consumption_days , $store_stock , $arr['data']['product_id'],$arr['branch_id']);


            $data_values = [
                'stock_quantity' => $store_stock / (float)$arr['data']['product_barcode_packing'],
                'lop_qty' => $lopqty,
                'purc_return_waiting_qty' => $purc_return_waiting_qty,
                'consumption_qty' => $consumption_days, // Consumption Days
                'suggest_qty_1' => $suggestQty1,
                'suggest_qty_2' => $suggestQty2,
            ];

            $arr['data'] = array_merge($arr['data'],$data_values);

            return $this->finalizaedData($arr);

        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getLine());
        }
        DB::commit();
    }

    public function getBarcodeDtlOS($arr)
    {
        $data = [];
        DB::beginTransaction();
        try {

            $arr['barcode_type'] = 'os';
            $arr['product_type'] = 'common';
            $arr['data'] = $this->getBarcodeBasicDetail($arr);

            if($arr['data']['product_perishable'] == 1){
                $arr['product_type'] = 'product_perishable';
            }
            if($arr['data']['product_perishable'] == 0){
                $arr['barcode_type'] = 'product_verify';
            }

            $arr['pd_packing'] = $arr['data']['product_barcode_packing'];
            $data_values = [
                'product_barcode_foc_qty' => "",
                'product_barcode_fc_rate' => "",
                'product_barcode_qty' => "1",
                'product_barcode_rate' => "0",
                'product_barcode_amount' => "",
                'product_barcode_disc_perc' => "",
                'product_barcode_disc_amount' => "",
                'product_barcode_vat_perc' => "",
                'product_barcode_vat_amount' => "",
                'product_barcode_total_amount' => "",
            ];

            /* start #1
             * if same Product (not barcode) scanned twice in another document no then system should inform
             * that "this product already entered in document no 'OS-?????'"
             * */
            $q = "select s.stock_code from TBL_INVE_STOCK s
                    join TBL_INVE_STOCK_DTL sd on s.stock_id = sd.STOCK_ID
                    where sd.product_id = '".$arr['data']['product_id']."' and upper(s.stock_code_type) = 'OS'
                    and s.stock_date > to_date('01-12-2021', 'dd/mm/yyyy')
                    and s.branch_id = ".$arr['branch_id']." order by stock_code desc";
            $getStockCode = DB::selectOne($q);
            if(isset($getStockCode->stock_code)){
                $data_values['product_exists'] =   true;
                $data_values['product_exists_msg'] =   "This product already exists in document No. $getStockCode->stock_code";
            };
            /* End #1
             * */

            /* start #2
             * in opening stock, when add barcode (when login other than main branch)
             * system should check that barcode already added in opening stock (in main branch) or no.
             * if not exist then should show only message
             * "this barcode not found in opening stock of main branch"
             * */
            $q = "select s.stock_code from TBL_INVE_STOCK s
                    join TBL_INVE_STOCK_DTL sd on s.stock_id = sd.STOCK_ID
                    where sd.product_id = '".$arr['data']['product_id']."' and upper(s.stock_code_type) = 'OS'
                    and s.branch_id = ".Helper::$DefaultBranch." order by stock_code desc";
            $getStock = DB::selectOne($q);

            if(empty($getStock) && !isset($getStockCode->stock_code)){
                if(Helper::$DefaultBranch == $arr['branch_id']){
                    $data_values['product_exists'] =   false;
                }else{
                    $data_values['product_exists'] =   true;
                }
                $data_values['product_exists_msg'] =   "This product not found in main branch";
            };
            /* End #2
             * */
            $arr['data'] = array_merge($arr['data'],$data_values);
            return $this->finalizaedData($arr);

        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }

    public function getBarcodeDtlSA($arr)
    {
        $data = [];
        DB::beginTransaction();
        try {

            $arr['barcode_type'] = 'sa';
            $arr['product_type'] = 'common';
            $arr['data'] = $this->getBarcodeBasicDetail($arr);

            if($arr['data']['product_perishable'] == 1){
                $arr['product_type'] = 'product_perishable';
            }
            if($arr['data']['product_perishable'] == 0){
                $arr['barcode_type'] = 'product_verify';
            }

            $arr['pd_packing'] = $arr['data']['product_barcode_packing'];
            $data_values = [
                'product_barcode_foc_qty' => "",
                'product_barcode_fc_rate' => "",
                'product_barcode_qty' => "1",
                'product_barcode_rate' => "0",
                'product_barcode_amount' => "",
                'product_barcode_disc_perc' => "",
                'product_barcode_disc_amount' => "",
                'product_barcode_vat_perc' => "",
                'product_barcode_vat_amount' => "",
                'product_barcode_total_amount' => "",
            ];

            /* start #1
             * if same Product (not barcode) scanned twice in another document no then system should inform
             * that "this product already entered in document no 'SA-?????'"
             * */
            $q = "select s.stock_code from TBL_INVE_STOCK s
                    join TBL_INVE_STOCK_DTL sd on s.stock_id = sd.STOCK_ID
                    where sd.product_id = '".$arr['data']['product_id']."' and lower(s.stock_code_type) = '".$arr['barcode_type']."'
                    and s.stock_date > to_date('01-12-2021', 'dd/mm/yyyy')
                    and s.branch_id = ".$arr['branch_id']." order by stock_code desc";

            $getStockCode = DB::selectOne($q);
            if(isset($getStockCode->stock_code)){
                $data_values['product_exists'] =   true;
                $data_values['product_exists_msg'] =   "This product already exists in document No. $getStockCode->stock_code";
            };
            /* End #1
             * */
            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");
            $date = date('Y-m-d', strtotime($today_format));
            $itr = [
                $arr['data']['product_id'],
                $arr['data']['product_barcode_id'],
                $arr['branch_id'],$arr['business_id'],$arr['company_id'],
                $arr['store_id'],$date
            ];
            $data_values['stock_quantity'] = 0;
            if(!isset($getStockCode->stock_code)){
                $store_stock =  collect(DB::select('SELECT GET_STOCK_CURRENT_QTY_DATE_OPENING(?,?,?,?,?,?,?) AS qty from dual', $itr))->first()->qty;

                $data_values['stock_quantity'] = $store_stock / (float)$arr['data']['product_barcode_packing'];
            }

            /* start #2
             * in opening stock, when add barcode (when login other than main branch)
             * system should check that barcode already added in opening stock (in main branch) or no.
             * if not exist then should show only message
             * "this barcode not found in opening stock of main branch"
             * */
            $q = "select s.stock_code from TBL_INVE_STOCK s
                    join TBL_INVE_STOCK_DTL sd on s.stock_id = sd.STOCK_ID
                    where sd.product_id = '".$arr['data']['product_id']."' and lower(s.stock_code_type) = '".$arr['barcode_type']."'
                    and s.branch_id = ".Helper::$DefaultBranch." order by stock_code desc";
            $getStock = DB::selectOne($q);

            if(empty($getStock) && !isset($getStockCode->stock_code)){
                if(Helper::$DefaultBranch == $arr['branch_id']){
                    $data_values['product_exists'] =   false;
                }else{
                    $data_values['product_exists'] =   true;
                }
                $data_values['product_exists_msg'] =   "This product not found in main branch";
            };
            /* End #2
             * */
            $arr['data'] = array_merge($arr['data'],$data_values);
            return $this->finalizaedData($arr);

        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiJsonErrorResponse($data, $e->getMessage());
        }
        DB::commit();
    }
}
