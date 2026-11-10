<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Common\GetAllData;
use App\Models\Report\RptIvenBatchExpiry;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductFOC;
use App\Models\TblPurcSupProdDtl;
use App\Models\ViewPurcProductBarcode;
use App\Models\ViewPurcProductBarcodeFirst;
use App\Models\ViewPurcProductBarcodeRate;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblInveStock;
use App\Models\TblInveStockDtl;
use App\Models\TblPurcGrnDtl;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblSaleSalesContractDtl;
use App\Models\ViewInveDisplayLocation;
use App\Models\ViewPurcGRN;
use App\Models\ViewStockRequest;
use App\Models\ViewInveStock;
use App\Models\TblPurcProductType;
use App\Models\TblPurcBrand;
use App\Models\ViewPurcGroupItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class BarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getBarcodeVerify($data){
        $barcode_val = $data['barcode_val'];
        if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$barcode_val)->exists()) {
            $data['barcode'] = $barcode_val;
        }else{
            $weight_prod = substr($barcode_val, 0, 7);
            if(TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$weight_prod)->where('product_barcode_weight_apply',1)->exists()){
                $data['barcode'] = $weight_prod;
            }
            if(!isset($data['barcode'])){
                $qry = "select PRODUCT_BARCODE_BARCODE from tbl_PURC_PRODUCT_BARCODE where SUBSTR(PRODUCT_BARCODE_BARCODE, -6, 6) = '".$barcode_val."'";
                $getCode = DB::SelectOne($qry);
                if(!empty($getCode)){
                    $data['barcode'] = $getCode->product_barcode_barcode;
                }
            }
        }
        if(isset($data['barcode']) && !empty($data['barcode'])){
            $barcode_val = $data['barcode'];
            $data['barcode_dtl'] = TblPurcProductBarcode::where('product_barcode_barcode','LIKE',$barcode_val)->first();
            $data['product_id'] = $data['barcode_dtl']->product_id;
            $data['product_barcode_id'] = $data['barcode_dtl']->product_barcode_id;
            if($data['form_type'] == 'purc_order'){
                $data = self::getBarcodeDtlPO($data);
            }
            $data['uom_list'] = Utilities::UOMList($data['product_id']);
        }
        return $data;
    }

    public function getBarcodeDtlPO($data){
        return $data;
    }

    public function getBarcodeDetail(Request $request)
    {
        $data = [];
        $val = $request->val; // this is product_barcode_barcode
        $id = isset($request->id)?$request->id:""; // external selected *form* like in grnForm select poEntry that is po_id
        $form_type = $request->form_type; // current form type
        $product_id = isset($request->product_id)?$request->product_id:""; // this is product id in case pr purchase demand
        $store_id = isset($request->store_id)?$request->store_id:""; // this is product id in case pr purchase demand
        $data['store_id'] = $store_id; // this is product id in case pr purchase demand
        if(isset($request->sales_contract_id)){
            $sales_contract_id = $request->sales_contract_id;
        }
        if(isset($request->customer_id)){
            $customer_id = $request->customer_id;
        }
        if(isset($request->rate_type)){
            $rate_type = $request->rate_type;
        }
        if(isset($request->sup_id)){
            $sup_id = $request->sup_id;
        }
        if(isset($sup_id)){
            $data['sup_product_dtl'] = TblPurcSupProdDtl::where('sup_prod_sup_barcode',$val)
                ->orWhere('sup_prod_dtl_barcode',$val)
                ->where('sup_prod_supplier_id',$sup_id)
                ->where(Utilities::currentBCB())->first();
            if(isset($data['sup_product_dtl']->sup_prod_dtl_barcode)){
                $val = $data['sup_product_dtl']->sup_prod_dtl_barcode;
            }
        }



        if($form_type == 'purc_demand' && !empty($product_id) && TblPurcProductBarcode::where('product_id','LIKE',$product_id)->where('uom_id','LIKE',$val)->exists()) {
            $barcode = TblPurcProductBarcode::where('product_id',$product_id)->where('uom_id',$val)->first('product_barcode_barcode');
            $val = $barcode->product_barcode_barcode;
            //dd('Hi');
        }

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
        if(isset($code) && !empty($code)){            // common
            $data['barcode_type'] = 'common';
            $data['code'] = $code;
            $data['codeVal'] = $val;
            $data['current_user_branch_id'] = auth()->user()->branch_id;
            $data['current_product2'] = TblPurcProductBarcode::where('product_barcode_barcode',$code)->first();
            $data['current_product'] = TblPurcProductBarcode::with('product','barcode_dtl','uom','sale_rate')->where('product_barcode_barcode',$code)->first();
            $data['rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$data['current_product']['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->where('product_category_id',2)->first();
            $data['group_item'] = ViewPurcGroupItem::where('group_item_id',$data['current_product']['product']['group_item_id'])->where('group_parent_item_id',$data['current_product']['product']['group_item_parent_id'])->first();
            $data['pro_central_rate'] = TblPurcProduct::where('product_id',$data['current_product']['product']['product_id'])->first('product_warranty_status');
            $data['central_rate'] = $data['pro_central_rate']->product_warranty_status;
            $data['user_central_rate'] = auth()->user()->central_rate;
            $data['ex_net_tp'] = TblPurcProductBarcodePurchRate::where('product_barcode_id',$data['current_product']->product_barcode_id)
            ->where('branch_id',auth()->user()->branch_id)->first();

            $data['purc_rate'] = TblPurcProductBarcodePurchRate::where('product_barcode_id',$data['current_product']->product_barcode_id)
                ->where('branch_id',auth()->user()->branch_id)->first();
            $data['grn_purc_rate'] = ViewPurcGRN::where('product_barcode_id',$data['current_product']->product_barcode_id)
                ->where('branch_id',auth()->user()->branch_id)
               ->where('grn_type','GRN')
             ->orderBy('grn_date','desc')->orderBy('grn_code','desc')->first();

            // Access The JavaScript Cookie ------ Load Product Detail
            /*$showStockLog = isset($_COOKIE['showStockLog']) ? $_COOKIE['showStockLog'] : null;
            if(!is_null($showStockLog)){
                $data['toast_stock_detail'] = GetAllData::getProductStockDetailByBarcode($code);
            }else{
                $data['toast_stock_detail'] = "";
            }*/

            // change_rate
            if($form_type == 'change_rate'){
                $data['sale_rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$data['current_product']['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->get();
                $data['cost_rate'] = TblPurcProductBarcodePurchRate::where('product_barcode_id',$data['current_product']['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->first('product_barcode_cost_rate');
            }

            // stock transfer
            if($form_type == 'st' && !empty($id) && ViewStockRequest::where('demand_id',$id)->where('product_barcode_barcode',$code)->exists()){
                // if stock request Code selecte then data get from stock request
                $data['barcode_type'] = 'stock_transfer';
                $data['current_product'] = ViewStockRequest::where('demand_id',$id)->where('product_barcode_barcode',$code)->first();
                $vat = TblPurcProductBarcodeDtl::where('product_barcode_id',$data['current_product']['product_barcode_id'])
                    ->where('branch_id',auth()->user()->branch_id)->first();
                if(!empty($vat)){
                    $data['vat'] = $vat;
                }else{
                    $data['vat'] = "";
                }
            }
            if($form_type == 'st'){
                // $pdo = \Illuminate\Support\Facades\DB::getPdo();
                // $business_id = auth()->user()->business_id;
                // $company_id = auth()->user()->company_id;
                // $branch_id = auth()->user()->branch_id;
                // $date =  date('Y-m-d');
                // //dd($date);

                // $v_product_id_from = $data['current_product']['product_id'];
                // $v_product_id_to = $data['current_product']['product_id'];
                // $stmt = $pdo->prepare("begin ".\App\Library\Utilities::getDatabaseUsername().".PRO_PURC_RPT_NEAR_BATCH_EXPIRY(:p1, :p2, :p3, :p4, :p5, :p6); end;");
                // $stmt->bindParam(':p1', $date);
                // $stmt->bindParam(':p2', $business_id);
                // $stmt->bindParam(':p3', $company_id);
                // $stmt->bindParam(':p4', $branch_id);
                // $stmt->bindParam(':p5', $v_product_id_from);
                // $stmt->bindParam(':p6', $v_product_id_to);
                // $stmt->execute();

                // $exp = RptIvenBatchExpiry::where('product_barcode_id',$data['current_product']['product_barcode_id'])->orderby('expiry_days_invoice','desc')->first();
                // $data['batch_expiry_date'] = isset($exp->batch_expiry_date)?$exp->batch_expiry_date:"";
            }
            if($form_type == "barcode_labels"){
                $vat = TblPurcProductBarcodeDtl::where('product_barcode_id',$data['current_product']['product_barcode_id'])
                    ->where('branch_id',auth()->user()->branch_id)->first();
                if(!empty($vat)){
                    $data['vat'] = $vat;
                }else{
                    $data['vat'] = "";
                }
            }
            if($form_type == "dynamic_barcode_labels"){
                $product_id = $data['current_product']['product_id'];
                $current_product = $data['current_product'];
                $data = [];
                $data['barcode_type'] = $form_type;
                $data['current_product'] = $current_product;
                $data['barcode_rate'] = ViewPurcProductBarcodeRate::where('product_id',$product_id)
                    ->where('branch_id',$request->branch_id)->first();
            }
            // stock receiving
            if($form_type == 'str' && !empty($id) && ViewInveStock::where('stock_id',$id)->where(Utilities::currentBC())->where('stock_branch_to_id',$data['current_user_branch_id'])->where('product_barcode_barcode',$code)->exists()){
                // data get from stock request
                $data['barcode_type'] = 'stock_receiving';
                $data['current_product'] = ViewInveStock::where('stock_code_type','st')->where('stock_id',$id)->where(Utilities::currentBC())->where('stock_branch_to_id',$data['current_user_branch_id'])->where('product_barcode_barcode',$code)->first();
                $data['rate'] = [];
            }
            // po = purchase order
            if($form_type == 'purc_order' && isset($request->supplier_id) && !empty($request->supplier_id)){
                $supplier_id = $request->supplier_id;
                if(ViewPurcGRN::where('supplier_id',$supplier_id)->where('product_barcode_barcode',$code)->where(Utilities::currentBCB())->exists()){
                    $data['rate'] = ViewPurcGRN::where('supplier_id',$supplier_id)->where(Utilities::currentBCB())
                        ->where('product_barcode_barcode',$code)->orderBy('grn_date','desc')->first();
                }else{
                    $data['rate'] = [];
                }
            }
            if($form_type == 'purc_order' && empty($request->supplier_id)){
                $data['rate'] = [];
            }
            // grn =  Goods Received Notes
            if($form_type == 'grn' && isset($request->po_id) && !empty($request->po_id)){
                $po_id = $request->po_id;

                // Get Product Barcode ID
                $barcode_id = TblPurcProductBarcode::where('product_barcode_barcode' , $code)->first('product_barcode_id');

                $current_product = TblPurcPurchaseOrderDtl::with('product','barcode','uom')
                    ->where('purchase_order_id',$po_id)
                    ->where('product_barcode_id', $barcode_id->product_barcode_id)
                    ->where(Utilities::currentBCB())
                    ->first();
                //dd($current_product);
                if(!empty($current_product)){
                    $data['barcode_type'] = 'grn';
                    $data['current_product'] = $current_product;
                }
            }

            if($form_type == 'grn' && $data['barcode_type'] == 'common' && $data['current_product']->product->product_perishable == 1){
                $data['product_type'] = 'grn_perishable';
            }

            if($form_type == 'grn' && $data['barcode_type'] == 'common' && $data['current_product']->product->product_perishable == 0 && ($data['pro_central_rate']->product_warranty_status == 0 && $data['user_central_rate'] == 0)){
                $data['barcode_type'] = 'grn_verify';
            }

           /* if($form_type == 'grn' && $data['barcode_type'] == 'common' && ($data['pro_central_rate']->product_warranty_status == 1 && $data['user_central_rate'] == 0)){
                $data['central_rate_type'] = 'grn_central_rate';
            }*/

            if($form_type == 'grn' && !isset($current_product)){
                $po_id = $request->po_id;
                $product_exist = TblPurcPurchaseOrderDtl::where('purchase_order_id',$po_id)
                    ->where('product_id', $data['current_product']->product_id)
                    ->where(Utilities::currentBCB())
                    ->first();
              //  dd($product_exist->toArray());
                if(!empty($product_exist)){
                    $data['pr_grn_rate'] = ((float)$product_exist->purchase_order_dtlpacking * (float)$product_exist->purchase_order_dtlquantity) / (float)$data['current_product']->product_barcode_packing;
                }
            }
            if($form_type ==  'product_discount_setup'){
                $data['grn_dtl'] = TblPurcGrnDtl::where('product_id', $data['current_product']->product_id)
                    ->where(Utilities::currentBCB())
                    ->orderby('created_at','desc')
                    ->first();
            }

            // opening stock
            if($form_type == 'os'){
              //  $data['display_location'] = ViewInveDisplayLocation::orderBy('display_location_name_string')->get();
            }

            // purchase demand
            if($form_type == 'purc_order' || $form_type == 'purc_return' || $form_type == 'grn'
            || $form_type == 'st'){
                $now = new \DateTime("now");
                $today_format = $now->format("d-m-Y");
                $date = date('Y-m-d', strtotime($today_format));
                $arr = [
                    $data['current_product']->product_id,
                    $data['current_product']->product_barcode_id,
                    auth()->user()->business_id,
                    auth()->user()->company_id,
                    auth()->user()->branch_id,
                    '',
                    $date
                ];

                $query = "SELECT
                    nvl (SUM(NVL (QTY_BASE_UNIT_VALUE, 0)), 0) AS STOCK
                FROM
                    VW_PURC_STOCK_DTL GRN
                WHERE GRN.PRODUCT_ID = '".$data['current_product']->product_id."'
                    AND GRN.BUSINESS_ID = '".auth()->user()->business_id."'
                    AND GRN.COMPANY_ID = '".auth()->user()->company_id."'
                    AND GRN.BRANCH_ID = '".auth()->user()->branch_id."'
                    AND GRN.DOCUMENT_DATE <= '".$date."'";

                // $data['s_i_h'] = DB::selectOne($query);
                //$store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;
                $data['store_stock'] = 0;
            }
            if($form_type == 'purc_demand'){
                $now = new \DateTime("now");
                $today_format = $now->format("d-m-Y");
                $date = date('Y-m-d', strtotime($today_format));
                $supplier_id = isset($request->sup_id) ? $request->sup_id : "";
                $arr = [
                    $data['current_product']->product_id,
                    $data['current_product']->product_barcode_id,
                    auth()->user()->business_id,
                    auth()->user()->company_id,
                    auth()->user()->branch_id,
                    '',
                    $date
                ];
                $store_stock =  collect(DB::select('SELECT get_stock_current_qty_uom_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;
                $data['store_stock'] = $store_stock;

                // Suggested Qty
                $SuggestedDetail = TblPurcProductBarcodeDtl::where('product_barcode_id',$data['current_product']['product_barcode_id'])
                ->where('branch_id',auth()->user()->branch_id)->first(['product_barcode_shelf_stock_max_qty','product_barcode_stock_cons_day']);
                // dd($SuggestedDetail,DB::table('tbl_purc_product_barcode_dtl')->where('product_barcode_id', $data['current_product']['product_barcode_id'])->first(['branch_id','product_barcode_shelf_stock_max_qty', 'product_barcode_stock_cons_day']));
                $maxLimit = $SuggestedDetail->product_barcode_shelf_stock_max_qty;

                $consumption_days = $SuggestedDetail->product_barcode_stock_cons_day;

                $data['suggestQty1'] = Utilities::SuggestedQty1($maxLimit , $data['store_stock']);
                $data['suggestQty2'] = Utilities::SuggestedQty2($consumption_days , $data['store_stock'] , $data['current_product']->product_id,auth()->user()->branch_id);

                /*
                * lpo qty
                */
                $lopqty_Qry1 = "select sum(pod.PURCHASE_ORDER_DTLQUANTITY) qty from tbl_purc_purchase_order po
                join tbl_purc_purchase_order_dtl pod on pod.PURCHASE_ORDER_ID = po.PURCHASE_ORDER_ID
                where pod.PRODUCT_BARCODE_ID = ".$data['current_product']->product_barcode_id." and po.branch_id = ".auth()->user()->branch_id;

                $lopqty_1 = DB::selectOne($lopqty_Qry1);

                $lopqty_Qry2 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'GRN' and  grnd.PRODUCT_BARCODE_ID = ".$data['current_product']->product_barcode_id." and grn.branch_id = ".auth()->user()->branch_id." and grn.PURCHASE_ORDER_ID IS NOT NULL";

                $lopqty_2 = DB::selectOne($lopqty_Qry2);

                $lopqty1 = (isset($lopqty_1->qty) && !empty($lopqty_1->qty))?$lopqty_1->qty:0;
                $lopqty2 = (isset($lopqty_2->qty) && !empty($lopqty_2->qty))?$lopqty_2->qty:0;

                $packing    = isset($data['current_product']->product_barcode_packing) && !empty($data['current_product']->product_barcode_packing)?$data['current_product']->product_barcode_packing:1;

                $data['lpo_quantity'] = (int)(((float)$lopqty1 - (float)$lopqty2)/$packing);
                /*
                * Purc. Ret in waiting qty
                */
                if(!empty($supplier_id)){
                //     $supplierReturnableQuery = "SELECT B.grn_date,S.supplier_name,B.grn_code, A.grn_id, A.returnable_qty returnable_qty, A.collected_qty collected_qty, A.pending_qty pending_qty FROM VW_PURC_PENDING_RETURN A,
                //     TBL_PURC_GRN B ,   TBL_PURC_SUPPLIER S
                //     Where  A.Grn_id = B.GRN_ID  AND
                //     B.SUPPLIER_ID = S.SUPPLIER_ID AND B.SUPPLIER_ID = ". $supplier_id ."
                //     AND  B.business_id = ". auth()->user()->business_id ." AND B.branch_id = " . auth()->user()->branch_id;

                //     $returnable = DB::select($supplierReturnableQuery);

                //     $data['supplier_has_returnable'] = FALSE;

                //     if(count($returnable) > 0){
                //         if($returnable[0]->pending_qty > 0){
                //             $data['supplier_has_returnable'] = TRUE;
                //         }
                    // }

                    $pendingQtyQuery = "SELECT B.grn_date,S.supplier_name,B.grn_code, A.grn_id, A.returnable_qty returnable_qty, A.collected_qty collected_qty, A.pending_qty pending_qty FROM VW_PURC_PENDING_RETURN A,
                    TBL_PURC_GRN B ,   TBL_PURC_SUPPLIER S
                    Where  A.Grn_id = B.GRN_ID  AND
                    B.SUPPLIER_ID = S.SUPPLIER_ID AND B.SUPPLIER_ID = ". $supplier_id ."
                    AND A.product_id = ". $data['current_product']->product_id ."
                    AND  B.business_id = ". auth()->user()->business_id ." AND B.branch_id = " . auth()->user()->branch_id;

                    $pendingQty = DB::select($pendingQtyQuery);

                    $data['purc_return_waiting_qty'] = 0;
                    if(count($pendingQty) > 0){
                        $data['purc_return_waiting_qty'] = $pendingQty[0]->pending_qty;
                    }
                }else{
                    $waitingQty_Qry1 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'PRT' and grnd.PRODUCT_BARCODE_ID = ".$data['current_product']->product_barcode_id." and grn.branch_id = ".auth()->user()->branch_id."";

                    $waitingQty_1 = DB::selectOne($waitingQty_Qry1);

                    $waitingQty_Qry2 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                                join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                                where grn.grn_type = 'PR' and grnd.PRODUCT_BARCODE_ID = ".$data['current_product']->product_barcode_id." and grn.branch_id = ".auth()->user()->branch_id." and grn.PURCHASE_ORDER_ID != null";

                    $waitingQty_2 = DB::selectOne($waitingQty_Qry2);
                    $waitingQty1 = (isset($waitingQty_1->qty) && !empty($waitingQty_1->qty))?$waitingQty_1->qty:0;
                    $waitingQty2 = (isset($waitingQty_2->qty) && !empty($waitingQty_2->qty))?$waitingQty_2->qty:0;


                    $data['purc_return_waiting_qty'] = (int)(((float)$waitingQty1 - (float)$waitingQty2)/$packing);
                }
            }

            // pr = purchase return

            if($form_type == 'purc_return'){
                $data['grn_retrn'] = TblPurcGrnDtl::where('product_id',$data['current_product']->product_id)->where(DB::raw('lower(grn_type)'),'grn')->orderby('created_at','desc')->first();
                if(empty($data['grn_retrn'])){
                    $current_product = TblPurcProductBarcode::where('product_id',$data['current_product']->product_id)->get();
                    foreach ($current_product as $c_product){
                        if(empty($data['grn_retrn'])){
                            $grn_retrn = TblPurcGrnDtl::where('product_id',$c_product->product_id)->where(DB::raw('lower(grn_type)'),'grn')->orderby('created_at','desc')->first();
                            if(!empty($grn_retrn)){
                                $data['grn_retrn'] = $grn_retrn;
                            }
                        }
                    }
                }

            }

            // if($form_type == "purc_return"){
                // $supplier_id = isset($request->sup_id) ? $request->sup_id : "";

                // if(!empty($supplier_id)){
                //     $pendingQtyQuery = "SELECT A.product_id, sum(A.retable_qty) returnable_qty, sum(A.qty) qty, sum(A.pending_qty) pending_qty  FROM VW_PURC_PENDING_RETURN A
                //     JOIN tbl_purc_grn B ON
                //     A.Grn_id = B.GRN_ID AND B.supplier_id = ". $supplier_id ." AND B.branch_id = ". auth()->user()->branch_id ." AND A.product_id = ". $data['current_product']->product_id ."
                //     group by A.product_id";

                //     $pendingQty = DB::select($pendingQtyQuery);

                //     $data['purc_return_pending_qty'] = "";
                //     $data['purc_return_returnable_qty'] = "";
                //     $data['purc_return_colleted_qty'] = "";
                //     if(count($pendingQty) > 0){
                //         $data['purc_return_pending_qty'] = $pendingQty[0]->pending_qty;
                //         $data['purc_return_returnable_qty'] = $pendingQty[0]->returnable_qty;
                //         $data['purc_return_colleted_qty'] = $pendingQty[0]->qty;

                //     }
                // }
            // }

            // sales_contract
            if($form_type == 'sales_contract'){
                $data['barcode_type'] = 'sales_contract';
            }
            // cso = customer sales contract
            if($form_type == 'cso'){
                // get last contract rate
                $data['rate'] = DB::table('tbl_sale_sales_contract_dtl scd')
                    ->join('tbl_sale_sales_contract sc','sc.sales_contract_id','=','scd.sales_contract_id')
                    ->where('sc.customer_id',$customer_id)
                    ->where('scd.sales_contract_dtl_barcode',$code)
                    ->where('scd.business_id',auth()->user()->business_id)
                    ->where('scd.company_id',auth()->user()->company_id)
                    ->where('scd.branch_id',auth()->user()->branch_id)
                    ->orderby('sc.sales_contract_code','desc')
                    ->select('scd.*')
                    ->limit(1)->first();
            }

            if($form_type == 'brochure'){
                $data['sale_rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$data['current_product']['product_barcode_id'])->where('branch_id',auth()->user()->branch_id)->get();
            }

            // sale_invoice
            if($form_type == 'sale_invoice'){
                $data['barcode_type'] = 'sale_invoice';
                if(isset($sales_contract_id) && !empty($sales_contract_id) && isset($rate_type) && $rate_type == 'item_contract_rate'){
                    $data['current_product'] = TblSaleSalesContractDtl::with('product','barcode','uom')
                        ->where('sales_contract_id',$sales_contract_id)
                        ->where('sales_contract_dtl_barcode', $code)
                        ->where(Utilities::currentBCB())
                        ->first();
                    $data['rate'] = [];
                    if(empty($data['current_product'])){
                        $data['msg'] = 'Product Not Found in Sale Contract';
                    }
                }
            }
            // sales_quotation
            if($form_type == 'sales_quotation'){
                $data['barcode_type'] = 'sales_quotation';
            }
            // sale_return
            if($form_type == 'sale_return'){
                $data['barcode_type'] = 'sale_return';
            }
            // sale_return
            if($form_type == 'sales_fee'){
                $data['barcode_type'] = 'sale_fee';
            }

            if($form_type == 'sa'){
                $data = $this->getStockAdj($data, $request);
            }
            //Supplier Product Registration
            if($form_type == 'sup_prod_reg'){
                $data['prod_type']    = TblPurcProductType::where('product_type_id',$data['current_product']['product']->product_type_id)->first();
                $data['prod_brand']   = TblPurcBrand::where('brand_id',$data['current_product']['product']->product_brand_id)->first();
            }

            if(!empty($data['current_product'])){
                $data['uom_list'] = Utilities::UOMList($data['current_product']->product_id);
            }
        }

        // $value = ((1.080 / 100) * 5);
        // $value = 1.080 + $value;
        // dd(number_format($value , 3));
        return response()->json($data);
    }

    public function UOMList($product_id)
    {
        $data['uom_list'] =  Utilities::UOMList($product_id);
        return response()->json($data);
    }

    public function getBarcodeDetailByUOM(Request $request){
        $data = [];
        $val = $request->val; // this is product_barcode_barcode
       // $id = $request->po_id; // external selected *form* like in grnForm select poEntry that is po_id
        $form_type = $request->form_type; // current form type
        $product_id = $request->product_id; // this is product id in case pr purchase demand
        $store_id = isset($request->store_id)?$request->store_id:"";

        $current_branch = auth()->user()->branch_id;
        $data['current_user_branch_id'] = $current_branch;
        $data['store_id'] = $store_id;
        $data['barcode_type'] = 'common';

        $data['current_product'] = TblPurcProductBarcode::with('product','barcode_dtl','uom')
                            ->whereHas('product', function ($query) use ($product_id) {
                                $query->where('product_id',$product_id);
                            })->where('uom_id', $val)->first();
        $data['pro_central_rate'] = TblPurcProduct::where('product_id',$data['current_product']['product']['product_id'])->first('product_warranty_status');
        $data['central_rate'] = $data['pro_central_rate']->product_warranty_status;
        $data['user_central_rate'] = auth()->user()->central_rate;

        $data['rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$data['current_product']->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->where('product_category_id',2)->first();
        $barcode = $data['current_product']->product_barcode_barcode;
        $barcode_id = $data['current_product']->product_barcode_id;

        if($form_type == 'purc_order'){
            $data['rate'] = [];
        }
        // change_rate
        if($form_type == 'change_rate'){
            $data['sale_rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$data['current_product']->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->get();
            $data['cost_rate'] = TblPurcProductBarcodePurchRate::where('product_barcode_id',$data['current_product']->product_barcode_id)->where('branch_id',auth()->user()->branch_id)->first('product_barcode_cost_rate');
        }
        // grn =  Goods Received Notes
        if($form_type == 'grn' && isset($request->po_id) && !empty($request->po_id)){
            $po_id = $request->po_id;
            $data['barcode_type'] = 'grn';
            $current_product = TblPurcPurchaseOrderDtl::with('product','barcode','uom')
                ->where('purchase_order_id',$po_id)
                ->where('product_barcode_id', $data['current_product']->product_barcode_id)
                ->where(Utilities::currentBCB())
                ->first();
           // dd($data['current_product']);
            if(!empty($current_product)){
                $data['current_product'] = $current_product;
            }
        }

        if($form_type == 'grn' && $data['current_product']->product->product_perishable == 1){
            $data['barcode_type'] = 'grn_perishable';
            $data['product_type'] = 'grn_perishable';
        }

        if($form_type == 'grn' && $data['current_product']->product->product_perishable == 0 && ($data['pro_central_rate']->product_warranty_status == 0 && $data['user_central_rate'] == 0)){
            $data['barcode_type'] = 'grn_verify';
        }
       /* if($form_type == 'grn' && ($data['pro_central_rate']->product_warranty_status == 1 && $data['user_central_rate'] == 0)){
            $data['central_rate_type'] = 'grn_central_rate';
        }*/

        if($form_type == 'grn' && !isset($current_product)){
            $po_id = $request->po_id;
            $product_exist = TblPurcPurchaseOrderDtl::where('purchase_order_id',$po_id)
                ->where('product_id', $data['current_product']->product_id)
                ->where(Utilities::currentBCB())
                ->first();
            //  dd($product_exist->toArray());
            if(!empty($product_exist)){
                $data['pr_grn_rate'] = ((float)$product_exist->purchase_order_dtlpacking * (float)$product_exist->purchase_order_dtlquantity) / (float)$data['current_product']->product_barcode_packing;
            }
        }

        if(!empty($data['current_product'])){
            $data['uom_list'] = Utilities::UOMList($data['current_product']->product_id);
        }
        if(!empty($barcode) && !empty($barcode_id)){
            $data['purc_rate'] = TblPurcProductBarcodePurchRate::where('product_barcode_barcode', $barcode)
                ->where('product_barcode_id',$barcode_id)
                ->where('branch_id',$current_branch)->first();
        }

        if($form_type == 'sa'){
            $data = $this->getStockAdj($data, $request);
        }

        return response()->json($data);
    }

    public function getBarcodeOSRate(Request $request){
        $validated = $request->validate([
            'barcode_id' => 'required|integer',
            'store_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'stock_id' => 'nullable|integer',
        ]);
        $branchId = (int) $validated['branch_id'];
        abort_unless(Utilities::documentBranchIsUserAccessible($branchId), 403);

        $query = DB::table('tbl_inve_stock_dtl as dtl')
            ->join('tbl_inve_stock as st', 'st.stock_id', '=', 'dtl.stock_id')
            ->where('dtl.product_barcode_id', $validated['barcode_id'])
            ->where('st.stock_code_type', 'os')
            ->where('st.stock_store_from_id', $validated['store_id'])
            ->where('st.business_id', auth()->user()->business_id)
            ->where('st.company_id', auth()->user()->company_id)
            ->where('st.branch_id', $branchId);
        if (!empty($validated['stock_id'])) {
            $query->where('st.stock_id', '<>', $validated['stock_id']);
        }
        $data['os_barcode'] = $query->orderBy('dtl.created_at', 'desc')
            ->select('st.stock_code', 'dtl.stock_dtl_rate')->limit(1)->get();

        if($data['os_barcode']->isNotEmpty()){
            $data['status'] = 'success';
        }else{
            $data['status'] = 'error';
        }
        return response()->json($data);
    }

    public function getBarcodeGRNRate(Request $request){
        // opening stock rate
        $selected_barcode_rate = $request->selected_barcode_rate;
        $barcode_id  = $request->barcode_id;
        $data = [];
        $data['grn_barcode'] = TblPurcGrnDtl::where('grn_type','like','GRN')
            ->where('product_barcode_id',$barcode_id)
            ->where(Utilities::currentBCB())
            ->orderBy('created_at','desc')->first();

        if(!empty($data['grn_barcode'])){
            $data['status'] = 'success';
        }else{
            $data['status'] = 'error';
        }
        return response()->json($data);
    }

    public function changeGridItemRate(Request $request){

        if (in_array($request->input('form_type'), ['os', 'sa', 'st', 'sd', 'di', 'production-consumption'], true)
            && in_array($request->input('rate_type'), ['cost_rate', 'last_purchase_rate', 'average_rate'], true)) {
            return $this->openingStockRate($request);
        }
        if ($request->input('form_type') === 'sa' && $request->input('rate_type') === 'item_average_rate') {
            return $this->openingStockRate($request);
        }

        $data = [];
        $form_type = $request->form_type;
        $product_id = $request->product_id;
        $barcode_id = $request->barcode_id;
        $rate_type = isset($request->rate_type)?$request->rate_type:"";
        $rate_perc = isset($request->rate_perc)?$request->rate_perc:"";
        $sales_contract = isset($request->sales_contract)?$request->sales_contract:"";
        $rateBranchId = auth()->user()->branch_id;
        if (in_array($form_type, ['sa', 'st', 'sd', 'di'], true)) {
            $rateBranchId = (int) $request->input('branch_id');
            abort_unless(Utilities::documentBranchIsUserAccessible($rateBranchId)
                && DB::table('tbl_soft_branch')->where('branch_id', $rateBranchId)
                    ->where(Utilities::currentBC())->exists(), 403);
        }

        $sale_rate_types = ['item_retail_rate','item_sale_rate','item_whole_sale_rate'];
        $purc_rate_types = ['item_purchase_rate','item_cost_rate','item_average_rate','cost_rate','average_rate'];
        $rate = 0;
        if(in_array($rate_type,$sale_rate_types)){
            $qry = TblPurcProductBarcodeSaleRate::where('product_barcode_id',$barcode_id)
                ->where('branch_id',$rateBranchId);
            if($rate_type == 'item_retail_rate'){
                $qry = $qry->where('product_category_id',1);
            }
            if($rate_type == 'item_sale_rate'){ //end user rate
                $qry = $qry->where('product_category_id',2);
            }
            if($rate_type == 'item_whole_sale_rate'){
                $qry = $qry->where('product_category_id',3);
            }
            $rate = $qry->first('product_barcode_sale_rate_rate as rate');
        }
        if(in_array($rate_type,$purc_rate_types)){
            $qry = TblPurcProductBarcodePurchRate::where('product_barcode_id',$barcode_id)
                ->where('branch_id',$rateBranchId);
            if($rate_type == 'item_purchase_rate'){
                $rate = $qry->first('product_barcode_purchase_rate as rate');
            }
            if($rate_type == 'item_cost_rate'){
                $rate = $qry->first('product_barcode_cost_rate as rate');
            }
            if($rate_type == 'item_average_rate'){
                $rate = $qry->first('product_barcode_avg_rate as rate');
            }
        }
        if($rate_type == 'item_contract_rate'){
            $qry = TblSaleSalesContractDtl::where('product_id',$product_id)
                ->where('product_barcode_id',$barcode_id)
                ->where(Utilities::currentBCB());
            if(!empty($sales_contract)){
                $qry->where('sales_contract_id',$sales_contract);
            }
            $rate  =  $qry->orderBy('created_at')->first('sales_contract_dtl_rate as rate');
        }

        $os = ['average_rate','last_stock_rate','cost_rate','last_purchase_rate'];
        if(in_array($rate_type,$os)){
            $rate = TblInveStockDtl::where('product_barcode_id',$barcode_id)->where(Utilities::currentBCB())->first('stock_dtl_rate as rate');
        }
        if($rate_type == 'average_rate'){
           if(empty($rate)){
                $rate = TblPurcProductBarcodePurchRate::where('product_barcode_id',$barcode_id)
                    ->where(Utilities::currentBCB())->first('product_barcode_avg_rate as rate');
            }
        }
        if($rate_type == 'last_stock_rate' || $rate_type == 'cost_rate'){
            if(empty($rate)){
                $rate = TblPurcProductBarcodePurchRate::where('product_barcode_id',$barcode_id)
                    ->where(Utilities::currentBCB())->first('product_barcode_cost_rate as rate');
            }
        }
        if($rate_type == 'last_purchase_rate'){
            if(empty($rate)){
                $rate = TblPurcGrnDtl::where('product_barcode_id',$barcode_id)->where('grn_type','GRN')
                    ->where(Utilities::currentBCB())->first('tbl_purc_grn_dtl_rate as rate');
            }
        }
        if($rate_type == 'item_last_net_tp'){
            $rate = TblPurcProductBarcodePurchRate::where('product_barcode_id',$barcode_id)
                ->where('branch_id',$rateBranchId)
                ->first('net_tp as rate');
        }
        $baseRate = isset($rate->rate) ? (float) $rate->rate : 0;
        if(!empty($rate_perc)){
            $NewRate = $baseRate + ($baseRate * (float) $rate_perc / 100);
        }else{
            $NewRate = $baseRate;
        }
        $data['rate'] = $NewRate;

        return response()->json(['data'=>$data]);
    }

    private function openingStockRate(Request $request)
    {
        $validated = $request->validate([
            'barcode_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'stock_date' => 'required|date_format:d-m-Y',
            'rate_type' => 'required|in:cost_rate,last_purchase_rate,average_rate,item_average_rate',
        ]);

        $branchId = (int) $validated['branch_id'];
        abort_unless(Utilities::documentBranchIsUserAccessible($branchId)
            && DB::table('tbl_soft_branch')->where('branch_id', $branchId)
                ->where(Utilities::currentBC())->exists(), 403);

        $barcode = TblPurcProductBarcode::where('product_barcode_id', $validated['barcode_id'])
            ->where('business_id', auth()->user()->business_id)
            ->firstOrFail();
        $barcodeId = (int) $barcode->product_barcode_id;
        $productId = (int) $barcode->product_id;
        $selectedPacking = (float) $barcode->product_barcode_packing;
        $date = date('Y-m-d', strtotime($validated['stock_date']));
        $rateType = $validated['rate_type'];
        $rate = null;

        if ($rateType === 'average_rate' || $rateType === 'item_average_rate') {
            // Posted GRN, opening stock and stock receiving, weighted by base-item quantity.
            $totals = DB::selectOne("SELECT
                    SUM(RATE_PER_PACK * PACK_QTY) TOTAL_AMOUNT,
                    SUM(PACK_QTY * PRODUCT_PACKING) TOTAL_BASE_QTY
                FROM (
                    SELECT
                        CASE WHEN LEDGER.DOCUMENT_TYPE = 'GRN'
                            THEN NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0)
                            ELSE NVL(NULLIF(LEDGER.STOCK_RATE, 0), LEDGER.DOCUMENT_ACT_RATE)
                        END RATE_PER_PACK,
                        NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0) PACK_QTY,
                        LEDGER.PRODUCT_PACKING
                    FROM VW_PURC_STOCK_DTL LEDGER
                    LEFT JOIN TBL_PURC_GRN_DTL GRN_RATE
                        ON GRN_RATE.GRN_ID = LEDGER.DOCUMENT_ID
                        AND GRN_RATE.PRODUCT_ID = LEDGER.PRODUCT_ID
                        AND GRN_RATE.PRODUCT_BARCODE_ID = LEDGER.PRODUCT_BARCODE_ID
                        AND GRN_RATE.BUSINESS_ID = LEDGER.BUSINESS_ID
                        AND GRN_RATE.COMPANY_ID = LEDGER.COMPANY_ID
                        AND GRN_RATE.BRANCH_ID = LEDGER.BRANCH_ID
                        AND GRN_RATE.GRN_TYPE = 'GRN'
                    WHERE LEDGER.PRODUCT_ID = ?
                        AND LEDGER.BUSINESS_ID = ?
                        AND LEDGER.COMPANY_ID = ?
                        AND LEDGER.BRANCH_ID = ?
                        AND LEDGER.POSTED = 1
                        AND LEDGER.DOCUMENT_TYPE IN ('GRN', 'OS', 'STR')
                        AND LEDGER.DOCUMENT_DATE < TO_DATE(?, 'YYYY-MM-DD') + 1
                        AND NVL(LEDGER.QTY_IN, 0) + NVL(LEDGER.BONUS_QTY_IN, 0) > 0
                        AND NVL(LEDGER.PRODUCT_PACKING, 0) > 0
                        AND ((LEDGER.DOCUMENT_TYPE = 'GRN'
                                AND NVL(GRN_RATE.TBL_PURC_GRN_DTL_RATE, 0) > 0)
                            OR (LEDGER.DOCUMENT_TYPE IN ('OS', 'STR')
                                AND NVL(NULLIF(LEDGER.STOCK_RATE, 0), LEDGER.DOCUMENT_ACT_RATE) > 0))
                )", [$productId, auth()->user()->business_id, auth()->user()->company_id, $branchId, $date]);
            if ($totals && (float) $totals->total_base_qty > 0) {
                $rate = (float) $totals->total_amount / (float) $totals->total_base_qty
                    * $selectedPacking;
            }
        } else {
            // Basic purchase rate follows the latest GRN of any packing;
            // last purchase rate follows the latest GRN of this exact barcode.
            $query = "SELECT RATE_PER_PACK, SOURCE_PACKING FROM (
                SELECT D.TBL_PURC_GRN_DTL_RATE RATE_PER_PACK,
                    D.TBL_PURC_GRN_DTL_PACKING SOURCE_PACKING
                FROM TBL_PURC_GRN H
                JOIN TBL_PURC_GRN_DTL D
                    ON D.GRN_ID = H.GRN_ID
                    AND D.BUSINESS_ID = H.BUSINESS_ID
                    AND D.COMPANY_ID = H.COMPANY_ID
                    AND D.BRANCH_ID = H.BRANCH_ID
                WHERE D.PRODUCT_ID = ?
                    AND H.BUSINESS_ID = ?
                    AND H.COMPANY_ID = ?
                    AND H.BRANCH_ID = ?
                    AND H.POSTED = 1
                    AND D.GRN_TYPE = 'GRN'
                    AND H.GRN_DATE < TO_DATE(?, 'YYYY-MM-DD') + 1
                    AND NVL(D.TBL_PURC_GRN_DTL_PACKING, 0) > 0
                    AND D.TBL_PURC_GRN_DTL_RATE > 0";
            $bindings = [$productId, auth()->user()->business_id, auth()->user()->company_id, $branchId, $date];
            if ($rateType === 'last_purchase_rate') {
                $query .= " AND D.PRODUCT_BARCODE_ID = ?";
                $bindings[] = $barcodeId;
            }
            $query .= " ORDER BY H.GRN_DATE DESC, H.CREATED_AT DESC, D.SR_NO DESC,
                D.PURC_GRN_DTL_ID DESC
            ) WHERE ROWNUM = 1";
            $record = DB::selectOne($query, $bindings);
            $rate = $record && (float) $record->source_packing > 0
                ? (float) $record->rate_per_pack / (float) $record->source_packing * $selectedPacking
                : null;
        }

        if (in_array($request->input('form_type'), ['sa', 'st', 'sd', 'di', 'production-consumption'], true) && $rate !== null) {
            $percentage = $request->validate(['rate_perc' => 'nullable|numeric|between:-100,1000'])['rate_perc'] ?? 0;
            $rate *= 1 + (float) $percentage / 100;
        }

        return response()->json([
            'data' => [
                'found' => $rate !== null,
                'rate' => $rate !== null ? round($rate, 3) : null,
            ],
        ]);
    }

    public function getStockAdj($data, Request $request){
        $data['barcode_type'] = 'sa';
        $data['product_exists'] =   false;
        $branchId = (int) $request->input('branch_id', auth()->user()->branch_id);
        abort_unless(Utilities::documentBranchIsUserAccessible($branchId), 403);

        $data['current_user_branch_id'] = $branchId;
        $data['purc_rate'] = TblPurcProductBarcodePurchRate::where('product_barcode_id', $data['current_product']->product_barcode_id)
            ->where('business_id', auth()->user()->business_id)
            ->where('company_id', auth()->user()->company_id)
            ->where('branch_id', $branchId)->first();
        $data['rate'] = TblPurcProductBarcodeSaleRate::where('product_barcode_id', $data['current_product']->product_barcode_id)
            ->where('branch_id', $branchId)->where('product_category_id', 2)->first();

        $storeId = (int) $request->input('store_id', 0);
        $storeIsValid = $storeId > 0 && DB::table('tbl_defi_store')
            ->where('store_id', $storeId)
            ->where('business_id', auth()->user()->business_id)
            ->where('company_id', auth()->user()->company_id)
            ->where('branch_id', $branchId)
            ->exists();
        abort_unless($storeIsValid, 422, 'Please select a valid store for the selected branch.');

        $requestedDate = trim((string) $request->input('stock_date', ''));
        $dateTimestamp = $requestedDate !== '' ? strtotime(str_replace('/', '-', $requestedDate)) : false;
        $date = $dateTimestamp !== false ? date('Y-m-d', $dateTimestamp) : date('Y-m-d');
        /* start #1
         * if same Product (not barcode) scanned twice in another document no then system should inform
         * that "this product already entered in document no 'SA-?????'"
         * */
        $q = "select s.stock_code from TBL_INVE_STOCK s
                    join TBL_INVE_STOCK_DTL sd on s.stock_id = sd.STOCK_ID
                    where sd.product_id = '".$data['current_product']->product_id."' and lower(s.stock_code_type) = 'sa'
                    and s.stock_date > to_date('01-12-2021', 'dd/mm/yyyy')
                    and s.branch_id = ".$branchId." order by stock_code desc";

        $getStockCode = DB::selectOne($q);
        if(isset($getStockCode->stock_code)){
            $data['product_exists'] =   false;
            $data['product_exists_msg'] =   "This product already exists in document No. $getStockCode->stock_code";
        };
        $data['store_stock'] = 0;
        // if(!isset($getStockCode->stock_code)){
            $store_stock_qty = DB::selectOne("SELECT NVL(SUM(
                    CASE
                        WHEN UPPER(DOCUMENT_TYPE) IN ('OS', 'ST', 'STR', 'SA')
                            THEN (NVL(QTY_IN, 0) - NVL(QTY_OUT, 0)) * NVL(PRODUCT_PACKING, 1)
                        ELSE NVL(QTY_BASE_UNIT_VALUE, 0)
                    END
                ), 0) AS qty
                FROM VW_PURC_STOCK_DTL
                WHERE PRODUCT_ID = ?
                    AND BUSINESS_ID = ?
                    AND COMPANY_ID = ?
                    AND BRANCH_ID = ?
                    AND SALES_STORE_ID = ?
                    AND POSTED = 1
                    AND DOCUMENT_DATE <= TO_DATE(?, 'YYYY-MM-DD')", [
                $data['current_product']->product_id,
                auth()->user()->business_id,
                auth()->user()->company_id,
                $branchId,
                $storeId,
                $date,
            ])->qty;
            $packing    = isset($data['current_product']['product_barcode_packing']) && !empty($data['current_product']['product_barcode_packing'])?$data['current_product']['product_barcode_packing']:1;
            $ss = $store_stock_qty / (float)$packing;
            $data['store_stock'] = number_format($ss,3,'.','');
        // }
        /* End #1
         * */

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getBarcodeRate(Request $request)
    {
       // dd($request->toArray());
        $data = [];
        DB::beginTransaction();
        try {
            $product_id =  $request->product_id;
            $product_barcode_id = $request->product_barcode_id;
            $business_id = Auth::user()->business_id;
            $company_id = Auth::user()->company_id;
            $branch_id = Auth::user()->branch_id;
            $where = "where product_id = $product_id and  product_barcode_id = $product_barcode_id and business_id = $business_id and company_id = $company_id and  branch_id = $branch_id";
            $whereSale = "where product_id = $product_id and  product_barcode_id = $product_barcode_id and branch_id = $branch_id";

            $qry = "select 'Purc Rate' name ,   product_barcode_purchase_rate as rate from tbl_purc_product_barcode_purch_rate $where union all
                    select 'Last basic purchase rate' name ,   product_barcode_cost_rate as rate from tbl_purc_product_barcode_purch_rate $where union all
                    select 'Avg Rate' name ,   product_barcode_purchase_rate as rate from tbl_purc_product_barcode_purch_rate $where ";
            $data['rates'] = DB::select($qry);

            $qrySale = "select category_name as name , product_barcode_sale_rate_rate as rate from vw_purc_product_rate $whereSale";
            $data['sale_rates'] = DB::select($qrySale);

        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, "", 200);
    }
    public function getSupplierFoc(Request $request)
    {
       // dd($request->toArray());
        $data = [];
        DB::beginTransaction();
        try {
            $product_id =  $request->product_id;
            $supplier_id = $request->supplier_id;
            $business_id = Auth::user()->business_id;
            $company_id = Auth::user()->company_id;
            $branch_id = Auth::user()->branch_id;

            $data['supplier_foc'] = TblPurcProductFOC::where('product_id',$product_id)
            ->where('supplier_id',$supplier_id)->first(['product_foc_purc_qty as purc_qty','product_foc_foc_qty as foc_qty']);
            $base_unit = ViewPurcProductBarcodeFirst::where('product_id',$product_id)->first(['product_barcode_packing']);
            if(isset($base_unit->product_barcode_packing)){
                $data['supplier_foc']['base_unit'] = $base_unit->product_barcode_packing;
            }

        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, "", 200);
    }
}
