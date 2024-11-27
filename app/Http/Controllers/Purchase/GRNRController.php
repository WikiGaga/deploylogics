<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerNA;
use App\Models\Settings\TblDefiExpenseAccounts;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiCurrency;
use App\Models\TblDefiPaymentType;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcGrn;
use App\Models\TblPurcGrnDtl;
use App\Models\TblPurcGrnExpense;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccCoa;
use App\Models\TblDefiStore;
use App\Models\ViewPurcProductBarcodeHelp;
use App\Models\TblPurcSupProdDtl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use App\Models\TblPurcProductBarcodeSaleRate;
use Illuminate\Validation\Rule;
use Dompdf\Dompdf;
use Validator;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GRNRController extends ControllerNA
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public static $page_title = 'GRN';
    public static $redirect_url = 'grn';
    public static $menu_dtl_id = '23';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request , $id = null)
    {

        $data['page_data'] = [];
        $data['form_type'] = 'grn';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;

        // Check Host and Make Values
        $host = $request->getHost();
        $hostName = explode('.' , $host)[0];
        $branch_branch_id = 2; // Mulada Branch


        $currentBCB = [
            ['business_id',1],
            ['company_id',1],
            ['branch_id',$branch_branch_id]
        ];
        $currentBC = [
            ['business_id',1],
            ['company_id',1]
        ];
        $data['branch_branch_id'] = $branch_branch_id;
        if(isset($id)){

            if(TblPurcGrn::where('grn_id',$id)->where($currentBCB)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;

                $data['current'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')->where('grn_id',$id)->where($currentBCB)->first();
                if(empty($data['current'])){
                    abort('404');
                }

                $data['grn_code'] = $data['current']->grn_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcGrn',
                'code_field'        => 'grn_code',
                'code_prefix'       => strtoupper('grn'),
                'code_type_field'   => 'grn_type',
                'code_type'         => strtoupper('grn'),
            ];
            $data['grn_code'] = Utilities::documentCode($doc_data);
        }
        $data['currency'] = TblDefiCurrency::where($currentBC)->get();
        $data['accounts'] = TblDefiExpenseAccounts::with('account')->where('expense_accounts_type','grn_acc')->where($currentBCB)->get();
        $data['store'] = TblDefiStore::where('store_entry_status',1)->where($currentBCB)->get();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)
            ->where($currentBC)->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where($currentBC)->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['grn_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_grn',
            'col_id' => 'grn_id',
            'col_code' => 'grn_code',
            'code_type_field'   => 'grn_type',
            'code_type'         => strtoupper('grn'),
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.grn.formNA', compact('data'));
    }

    public function getPO($id){
        $data['all'] = TblPurcPurchaseOrder::with('po_details','supplier','lpo')->where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
        return response()->json($data);
    }

    public function getPOProduct($code,$po_id=null){

        if(isset($po_id) && !empty($po_id)){
            $data['product'] = TblPurcPurchaseOrderDtl::with('product','barcode','uom')
                ->where('purchase_order_id',$po_id)
                ->where('product_barcode_barcode', $code)
                ->where(Utilities::currentBCB())
                ->first();
            if(!empty($data['product'])){
                $data['uom_list'] = Utilities::UOMList($data['product']->product_id);
            }
            $data['selected_po_code'] = true;
        }else{
            $data['product'] = ViewPurcProductBarcodeHelp::where('product_barcode_barcode', $code)
                        ->where('product_perishable', 1)
                        ->where(Utilities::currentBC())
                        ->first();
            if(!empty($data['product'])){
                $data['uom_list'] = Utilities::UOMList($data['product']->product_id);
            }
            $data['selected_po_code'] = false;
        }
        if(!empty($data['product'])){
            return $this->jsonSuccessResponse($data, trans('PO Product'), 200);
        }else{
            return $this->jsonErrorResponse($data, trans('Product Not Found'), 201);
        }

    }

    public function getSupProd($code,$sup_id){
        $data['current'] = TblPurcSupProdDtl::where('sup_prod_sup_barcode',$code)->where('sup_prod_supplier_id',$sup_id)->where(Utilities::currentBCB())->first('sup_prod_dtl_barcode');
        return response()->json($data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $data = [];
        // Check Host and Make Values
        $host = $request->getHost();
        $hostName = explode('.' , $host)[0];

        $branch_purchase_discount = 197;
        $branch_purchase_stock = 765434;
        $branch_purchase_vat = 24114620301008;
        $branch_branch_id = 2; // Mulada Branch

        $data['branch_branch_id'] = $branch_branch_id;

        $currentBCB = [
            ['business_id',1],
            ['company_id',1],
            ['branch_id',$branch_branch_id]
        ];
        $currentBC = [
            ['business_id',1],
            ['company_id',1]
        ];

        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required',
            'supplier_id' => 'required|numeric',
            'purchase_order_id' => 'nullable|numeric',
            'grn_currency' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'grn_store' => 'required|numeric',
            'grn_ageing_term_id' => 'nullable|numeric',
            'grn_ageing_term_value' => 'nullable|numeric',
            'payment_type_id' => 'required|numeric',
            'grn_notes' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->where($currentBC)->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }
        if(isset($request->pd)){
            foreach($request->pd as $dtl){
                $purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                $product = $dtl['product_id'];
                $product_barcode = $dtl['product_barcode_id'];
                $uom_id = $dtl['uom_id'];
                if($purchase_order_id != ""){
                    $exist_barcode = false;
                    $purchase_order_barcodes = TblPurcPurchaseOrderDtl::where('purchase_order_id',$purchase_order_id)->where($currentBCB)->get();
                    foreach ($purchase_order_barcodes as $barcode){
                        if($barcode['product_id'] == $product && $barcode['uom_id'] == $uom_id && $barcode['product_barcode_id'] == $product_barcode){
                            $exist_barcode = true;
                        }
                    }
                    if($exist_barcode == false){
                        return $this->jsonErrorResponse($data, trans('message.not_barcode'), 422);
                    }
                    $purchase_order_id = "";
                }else{
                    if(!ViewPurcProductBarcodeHelp::where('product_barcode_id','LIKE',$product_barcode)->where($currentBC)->exists()){
                        return $this->jsonErrorResponse($data, trans('message.not_product'), 422);
                    }
                }
            }
        }else{
            return $this->jsonErrorResponse($data, 'Fill The Grid', 200);
        }
        DB::beginTransaction();
        try{
            $sumOfProdTotalQty = 0;
            if(isset($request->pd)){
                foreach($request->pd as $dtl){
                    $prod_total_qty = (float)$dtl['quantity']+(float)$dtl['foc_qty'];
                    $sumOfProdTotalQty += $prod_total_qty;
                }
            }
            if(isset($id)){
                $grn = TblPurcGrn::where('grn_id',$id)->where($currentBCB)->first();

            }else{
                $grn = new TblPurcGrn();
                $grn->grn_id = Utilities::uuid();
                $grn->grn_type = 'GRN';
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcGrn',
                    'code_field'        => 'grn_code',
                    'code_prefix'       => strtoupper('grn'),
                    'code_type_field'   => 'grn_type',
                    'code_type'         => strtoupper('grn'),
                ];
                $grn->grn_code = Utilities::documentCode($doc_data);
            }

            $form_id = $grn->grn_id;
            $grn->grn_exchange_rate = $request->exchange_rate;
            $grn->payment_type_id = $request->payment_type_id;
            $grn->grn_date = date('Y-m-d', strtotime($request->grn_date));
            $grn->supplier_id = $request->supplier_id;
            $grn->purchase_order_id = $request->purchase_order_id;
            // $grn->grn_receiving_date = date('Y-m-d', strtotime($request->grn_receiving_date));
            $grn->store_id = $request->grn_store;
            $grn->grn_ageing_term_id = $request->grn_ageing_term_id;
            $grn->grn_ageing_term_value = $request->grn_ageing_term_value;
            $grn->grn_freight = $request->grn_freight;
            $grn->currency_id = $request->grn_currency;
            $grn->grn_bill_no = $request->grn_bill_no;
            $grn->grn_other_expense = $request->grn_other_expenses;
            $grn->grn_remarks = $request->grn_notes;
            $grn->business_id = 1;
            $grn->company_id = 1;
            $grn->branch_id = $branch_branch_id;
            $grn->grn_user_id = 91;
            $grn->grn_device_id = 1;
            $grn->save();

            $net_total = 0;
            $amount_total = 0;
            $vat_amount_total = 0;
            $disc_amount_total = 0;
            $TotalExpAmount = 0;
            $total_gross_amount = 0;
            if(isset($id)){
                $del_Dtls = TblPurcGrnExpense::where('grn_id',$id)->where($currentBCB)->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblPurcGrnExpense::where('grn_expense_id',$del_Dtls->grn_expense_id)->where($currentBCB)->delete();
                }
            }

            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    if(isset($expense['expense_amount'])){
                        if($expense['expense_plus_minus'] != '+' && $expense['expense_plus_minus'] != '-'){
                            return $this->jsonErrorResponse(['error'=>'expense'], trans('message.required_fields'), 200);
                        }
                        if($expense['expense_dr_cr'] != 'dr' && $expense['expense_dr_cr'] != 'cr'){
                            return $this->jsonErrorResponse(['error'=>'expense'], trans('message.required_fields'), 200);
                        }
                        $expenseDtl = new TblPurcGrnExpense();
                        $expenseDtl->grn_expense_id = Utilities::uuid();
                        if(isset($id)){
                            $expenseDtl->grn_id = $id;
                        }else{
                            $expenseDtl->grn_id = $grn->grn_id;
                        }
                        $expenseDtl->chart_account_id = $expense['account_id'];
                        $expenseDtl->grn_expense_account_code = $expense['account_code'];
                        $expenseDtl->grn_expense_account_name = $expense['account_name'];
                        $expenseDtl->grn_expense_amount = $this->addNo($expense['expense_amount']);
                        $expenseDtl->grn_expense_perc = $this->addNo($expense['expense_perc']);
                        $expenseDtl->business_id = 1;
                        $expenseDtl->company_id = 1;
                        $expenseDtl->branch_id = $branch_branch_id;
                        $expenseDtl->grn_expense_user_id = 91;
                        $expenseDtl->save();

                        if($expense['expense_plus_minus'] == '+'){
                            $net_total += $this->addNo($expense['expense_amount']);
                            $TotalExpAmount += $this->addNo($expense['expense_amount']);
                        }else{
                            $net_total -= $this->addNo($expense['expense_amount']);
                            $TotalExpAmount -= $this->addNo($expense['expense_amount']);
                        }

                    }
                }
            }

            $grn_dtls = TblPurcGrnDtl::where('grn_id',$grn->grn_id)->where($currentBCB)->get();
            foreach($grn_dtls as $grn_dtl){
                TblPurcGrnDtl::where('purc_grn_dtl_id',$grn_dtl->purc_grn_dtl_id)->where($currentBCB)->delete();
            }

            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $dtl){


                    if($dtl['vat_perc'] > 0){
                        $updateVat = TblPurcProductBarcodeDtl::checkBarcodeVatPercStatusR($dtl['product_barcode_id'],$dtl['vat_perc'],$branch_branch_id);
                        if($updateVat == false){
                            return $this->jsonErrorResponse($data, $dtl['pd_barcode']. ": vat not updated", 200);
                        }
                    }

                    $grnDtl = new TblPurcGrnDtl();

                    if(isset($id) && isset($account['purc_grn_dtl_id'])){
                        $grnDtl->grn_id = $id;
                        $grnDtl->purc_grn_dtl_id = $account['purc_grn_dtl_id'];
                    }else{
                        $grnDtl->purc_grn_dtl_id = Utilities::uuid();
                        $grnDtl->grn_id  = $grn->grn_id;
                    }
                    $grnDtl->grn_type = 'GRN';
                    $grnDtl->sr_no = $sr_no;
                    $sr_no = $sr_no+1;
                    $grnDtl->purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                    $grnDtl->supplier_id = $dtl['grn_supplier_id'];
                    $grnDtl->product_id = $dtl['product_id'];
                    $grnDtl->product_barcode_id = $dtl['product_barcode_id'];
                    $grnDtl->uom_id  = $dtl['uom_id'];
                    $grnDtl->grn_dtl_po_rate = $this->addNo($dtl['grn_dtl_po_rate']);
                    $grnDtl->tbl_purc_grn_dtl_packing = $dtl['pd_packing'];
                    $grnDtl->qty_base_unit = (isset($dtl['pd_packing'])?$dtl['pd_packing']:'0') * ((isset($dtl['quantity'])?$dtl['quantity']:'0')+(isset($dtl['foc_qty'])?$dtl['foc_qty']:'0'));
                    $grnDtl->tbl_purc_grn_dtl_supplier_barcode = $dtl['grn_supplier_barcode'];
                    $grnDtl->product_barcode_barcode = $dtl['pd_barcode'];
                    $grnDtl->tbl_purc_grn_dtl_quantity = $dtl['quantity'];
                    $grnDtl->tbl_purc_grn_dtl_foc_quantity = $dtl['foc_qty'];
                    $grnDtl->tbl_purc_grn_dtl_sale_rate = $this->addNo($dtl['sale_rate']);
                    $grnDtl->tbl_purc_grn_dtl_fc_rate = $this->addNo($dtl['fc_rate']);
                    $grnDtl->tbl_purc_grn_dtl_rate = $this->addNo($dtl['rate']);
                    $grnDtl->tbl_purc_grn_dtl_amount = $this->addNo($dtl['amount']);
                    $grnDtl->tbl_purc_grn_dtl_disc_percent = $this->addNo($dtl['dis_perc']);
                    $grnDtl->tbl_purc_grn_dtl_disc_amount = $this->addNo($dtl['dis_amount']);
                    $grnDtl->tbl_purc_grn_dtl_gst_percent = ""; // $this->addNo($dtl['grn_gst']);
                    $grnDtl->tbl_purc_grn_dtl_vat_percent = $this->addNo($dtl['vat_perc']);
                    $grnDtl->tbl_purc_grn_dtl_vat_amount = $this->addNo($dtl['vat_amount']);
                    $grnDtl->tbl_purc_grn_dtl_batch_no = $dtl['batch_no'];
                    $grnDtl->tbl_purc_grn_dtl_production_date = date('Y-m-d', strtotime($dtl['production_date']));
                    $grnDtl->tbl_purc_grn_dtl_expiry_date = date('Y-m-d', strtotime($dtl['expiry_date']));
                    $grnDtl->tbl_purc_grn_dtl_total_amount = $this->addNo($dtl['gross_amount']);
                    $grnDtl->business_id = 1;
                    $grnDtl->company_id = 1;
                    $grnDtl->branch_id = $branch_branch_id;
                    $grnDtl->tbl_purc_grn_dtl_user_id = 91;
                    // calculations
                    $prod_total_qty = (float)$dtl['quantity']+(float)$dtl['foc_qty'];
                    $prod_gross_amount = $this->addNo($dtl['amount']) - $this->addNo($dtl['dis_amount']);
                    $prod_gross_rate = $prod_gross_amount/$prod_total_qty;
                    $prod_rate_expense = $TotalExpAmount/$sumOfProdTotalQty;
                    $prod_net_rate = ($prod_rate_expense+$prod_gross_rate);
                    $grnDtl->dtl_prod_total_qty = $prod_total_qty;
                    $grnDtl->dtl_prod_gross_amount = $prod_gross_amount;
                    $grnDtl->dtl_prod_gross_rate = $prod_gross_rate;
                    $grnDtl->dtl_prod_rate_expense = $prod_rate_expense;
                    $grnDtl->dtl_prod_net_rate = $prod_net_rate;
                    // dd($prod_net_rate);

                    if(!empty($request->supplier_id) && !empty($dtl['product_id'])){
                        $pdo = DB::getPdo();
                        $supplier_id = $request->supplier_id ;
                        $business_id = 1;
                        $company_id = 1;
                        $branch_id = $branch_branch_id;
                        $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_SUP_BATCH_INSERT(:p1, :p2, :p3, :p4, :p5, :p6); end;");
                        $stmt->bindParam(':p1', $dtl['product_id']);
                        $stmt->bindParam(':p2', $supplier_id);
                        $stmt->bindParam(':p3', $dtl['grn_supplier_barcode']);
                        $stmt->bindParam(':p4', $business_id);
                        $stmt->bindParam(':p5', $company_id);
                        $stmt->bindParam(':p6', $branch_id);
                        $stmt->execute();
                    }

                    $barcode = TblPurcProductBarcode::where('product_barcode_id',$dtl['product_barcode_id'])
                        ->where('product_id',$dtl['product_id'])->first();

                    if($dtl['foc_qty'] > 0){
                        $amount = $this->addNo($dtl['amount']);
                        $quantity = $this->addNo($dtl['quantity']);
                        $foc_qty = $this->addNo($dtl['foc_qty']);
                        $barcode_packing = $barcode->product_barcode_packing;
                        $rate_inc_foc = ((float)$amount / ((float) $quantity + (float) $foc_qty )) / (float)$barcode_packing;
                    }else{
                        $rate = $this->addNo($dtl['rate']);
                        $rate_inc_foc = (float)$rate/(float)$barcode->product_barcode_packing;
                    }
                    $grnDtl->tbl_purc_grn_dtl_rate_inc_foc = $rate_inc_foc;

                    $grnDtl->save();
                    $net_total += $this->addNo($dtl['gross_amount']);
                    $total_gross_amount += $this->addNo($dtl['gross_amount']);
                    $amount_total += $this->addNo($dtl['amount']);
                    $vat_amount_total += $this->addNo($dtl['vat_amount']);
                    $disc_amount_total += $this->addNo($dtl['dis_amount']);

                    $firstBarcodeRate = (float)$prod_net_rate/(float)$barcode->product_barcode_packing;

                    $purc_rate = $this->addNo($dtl['rate']);
                    $purc_rate = (float)$purc_rate/(float)$barcode->product_barcode_packing;
                    $barcodeList = TblPurcProductBarcode::where('product_id',$dtl['product_id'])->get();
                    foreach ($barcodeList as $item){
                        $barcodeRate = (float)$firstBarcodeRate * (float)$item->product_barcode_packing;
                        $barcodePurcRate = (float)$purc_rate * (float)$item->product_barcode_packing;
                        TblPurcProductBarcodePurchRate::where('product_barcode_id',$item->product_barcode_id)
                            ->where('product_id',$item->product_id)
                            ->where('branch_id',$branch_branch_id)->update([
                                'product_barcode_cost_rate'=> $barcodeRate,
                                'product_barcode_purchase_rate'=> $barcodePurcRate,
                            ]);
                    }
                }

            }

            $grnTotal = TblPurcGrn::where('grn_id',$grn->grn_id)->where($currentBCB)->first();
            $grnTotal->grn_total_qty = $sumOfProdTotalQty;
            $grnTotal->grn_total_amount = $total_gross_amount;
            $grnTotal->grn_total_expense_amount = $TotalExpAmount;
            $grnTotal->grn_total_net_amount = $total_gross_amount + $TotalExpAmount;
            $grnTotal->save();
            // insert update grn voucher
            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $grn_id = $id;
                $grn = TblPurcGrn::where('grn_id',$grn_id)->where($currentBCB)->first();
                if(!empty($grn->voucher_id)){
                    $voucher_id = $grn->voucher_id;
                }else{
                    $voucher_id = Utilities::uuid();
                }
            }else{
                $action = 'add';
                $grn_id = $grn->grn_id;
                $voucher_id = Utilities::uuid();
            }
            $where_clause = '';
            $supplier = TblPurcSupplier::where('supplier_id',$request->supplier_id)->where($currentBC)->first();
            $supplier_chart_account_id = (int)$supplier->supplier_account_id;

            // Check Host and Assign Static Session Values
            //check account code
            $ChartArr = [
                $supplier_chart_account_id,
                $branch_purchase_discount,
                $branch_purchase_stock,
                $branch_purchase_vat
            ];
            $response = $this->ValidateCharCode($ChartArr);
            if($response == false){
                return $this->returnjsonerror("voucher Account Code not correct",404);
            }

            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $grn_id,
                'voucher_no'            =>  $grn->grn_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->grn_date)),
                'voucher_descrip'       =>  'Purchase: '.$grn->grn_remarks .' - Ref:'.$request->grn_bill_no,
                'voucher_type'          =>  'GRN',
                'branch_id'             =>  $branch_branch_id,
                'business_id'           =>  1,
                'company_id'            =>  1,
                'voucher_user_id'       =>  91,
                'document_ref_account'  =>  (int)$supplier->supplier_account_id,
                'vat_amount'            =>  $vat_amount_total,
            ];

            if($net_total <> 0)
            {
                $data['chart_account_id'] = $supplier_chart_account_id;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($net_total);
                $data['voucher_sr_no'] = 1;
                // for debit entry net_total
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
            }

            if($disc_amount_total <> 0)
            {
                $action = 'add';
                $discount_chart_account_id = $branch_purchase_discount;
                $data['chart_account_id'] = $discount_chart_account_id;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] =  abs($disc_amount_total);
                $data['voucher_sr_no'] = 2;
                // for debit entry disc_amount_total
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
            }
            if($amount_total <> 0)
            {
                $action = 'add';
                $stock_chart_account_id = $branch_purchase_stock;
                $data['chart_account_id'] = $stock_chart_account_id;
                $data['voucher_debit'] = abs($amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = 3;
                // for credit entry amount_total
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
            }
            if($vat_amount_total <> 0)
            {
                $action = 'add';
                $vat_payable_chart_account_id = $branch_purchase_vat;
                $data['chart_account_id'] = $vat_payable_chart_account_id;
                $data['voucher_debit'] = abs($vat_amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = 4;
                // for credit entry vat_amount_total
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
            }
            if(isset($request->pdsm)){
                $sr_no = 5;
                foreach($request->pdsm as $expense){
                     if($expense['expense_dr_cr'] == 'dr'){
                   // if(0 < $this->addNo($expense['expense_amount'])){
                        $data['voucher_debit'] = abs($expense['expense_amount']);
                        $data['voucher_credit'] = 0;
                    }else{
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($expense['expense_amount']);
                    }
                    $action = 'add';
                    $data['chart_account_id'] = $expense['account_id'];
                    $data['voucher_sr_no'] = $sr_no;
                    $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                    $sr_no++;
                }
            }
            $grn = TblPurcGrn::where('grn_id',$grn_id)->first();
            $grn->voucher_id = $voucher_id;
            $grn->save();

            // end insert update grn voucher

        }catch (QueryException $e) {
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
            return $this->jsonErrorResponse($data, $e->getLine().' : '.$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
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


    public function print($id,$type = null)
    {
        $data['title'] = 'Goods Received Note';
        $data['type'] = $type;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/grn/print/'.$id.'/pdf';
        if(isset($id)){
            if(TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')->where('grn_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['store'] = TblDefiStore::where('store_id',$data['current']->store_id)->where(Utilities::currentBCB())->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->grn_ageing_term_id)->where('payment_term_entry_status',1)->where(Utilities::currentBCB())->first();

        if(isset($type) && $type=='pdf'){
            $view = view('prints.grn_print', compact('data'))->render();
            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            return view('prints.grn_print', compact('data'));
        }
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
            $grn = TblPurcGrn::where('grn_id',$id)->where('grn_type','GRN')->where(Utilities::currentBCB())->first();
            $voucher_id = $grn->voucher_id;
            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }
            $grn->grn_dtl()->delete();
            $grn->delete();

        }catch (QueryException $e) {
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

    public function barcodePriceTag(Request $request){
        $barcode_ids = $request->data;
        $data = [];
        foreach ($barcode_ids as $barcode){
            $ba = TblPurcProductBarcode::with('product','cb_sale_rate')->where('product_barcode_id',$barcode['barcode_id'])->first()->toArray();
            $data[] = [
                'barcode' => $ba['product_barcode_barcode'],
                'name' => $ba['product']['product_name'],
                'rate' => $ba['cb_sale_rate']['product_barcode_sale_rate_rate'],
                'qty' => $barcode['qty'],
                'packing_date' => isset($barcode['packing_date'])?$barcode['packing_date']:"",
                'expiry_date' => isset($barcode['expiry_date'])?$barcode['expiry_date']:"",
            ];
        }
        session(['dataBarcodeTags'=>$data]);
        return response()->json(['status'=>'success']);
    }

    public function barcodePriceTagView(){
        $data['barcodes'] = session('dataBarcodeTags');
        if(empty($data['barcodes'])){
            abort('404');
        }
        return view('purchase.grn.price_tags',compact('data'));
    }

    public function barcodeSalePrice(Request $request)
    {
        $dataBarcode = $request->data;
        $data = [];
        foreach ($dataBarcode as $key => $databar) {

            array_push($data, TblPurcProductBarcodeSaleRate::where('product_barcode_id', $databar)->where('branch_id', 1)->where('product_category_id', 2)->first());
        }

        return response()->json(['product_barcode' => $data, 'status' => 'success']);

    }
}
