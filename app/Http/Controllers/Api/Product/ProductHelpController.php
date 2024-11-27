<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\ApiController;
use App\Models\TblPurcProduct;
use Illuminate\Http\Request;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\ViewPurcProductBarcodeHelp;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Library\ApiUtilities;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\ValidationException;

class ProductHelpController extends ApiController
{
    public function barcodeHelp(Request $request){

        $barcode = $request->barcode;
        $form_type = $request->form_type;
        $form_id = isset($request->form_id)?$request->form_id:""; //a external selected *form* like in grnForm select poEntry that is po_id
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

        $exist = TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$barcode)->exists();
        if($exist === false)
        {
            return $this->ApiJsonErrorResponse($data,'barcode not exists');
        }
        $productBarcode = TblPurcProductBarcode::with('product','barcode_dtl','uom','sale_rate')->where('product_barcode_barcode',$barcode)->first();
        $purc_rate = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $barcode)
            ->where('product_barcode_id',$productBarcode->product_barcode_id)
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
        if($form_type == 'demand'){
            $product = TblPurcProductBarcode::with('product','uom')->where('product_barcode_barcode',$barcode)->first();

            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");
            $date = date('Y-m-d', strtotime($today_format));
            $arr = [
                $product->product_id,
                $product->product_barcode_id,
                $business_id,
                $business_id,
                $branch_id,
                '',
                $date
            ];

            $store_stock =  collect(DB::select('SELECT get_stock_current_qty_uom_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;

            if(empty($product)){
                $data = (object)[];
                return $this->ApiJsonErrorResponse($data,'barcode not exists');
            }else{
                $data['barcode_type'] = 'demand';
                $data['barcode'] = [
                    "product_id"=>$product->product_id,
                    "product_barcode_id"=>$product->product_barcode_id,
                    "uom_id"=>$product->uom->uom_id,
                    "product_barcode"=>$product->product_barcode_barcode,
                    "product_name"=> $this->strUcWords($product->product->product_name),
                    "uom_name"=>$product->uom->uom_name,
                    "product_barcode_packing"=>$product->product_barcode_packing,
                    "store_stock"=>$store_stock
                ];
            }
        }

        if($exist){
            $data['uom_list'] = $this->UOMList($productBarcode->product_id);
        }
        return $this->ApiJsonSuccessResponse($data,'barcode data');
    }

    public function getBarcodeDetailByUOM(Request $request){
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
}
