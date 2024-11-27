<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcGrn;
use App\Models\TblPurcGrnDtl;
use App\Models\TblDefiStore;
use App\Models\TblDefiPaymentType;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccCoa;
use App\Models\TblPurcGrnExpense;
use App\Models\TblPurcProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use App\Models\ViewPurcGRN;
use Illuminate\Validation\Rule;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PurchaseReturnController extends Controller
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
    public static $page_title = 'Purchase Return';
    public static $redirect_url = 'purchase-return';
    public static $menu_dtl_id = '50';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'purc_return';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['page_data']['pending_pr'] = TRUE;
        if(isset($id)){
            if(TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;

                $data['current'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense','refPurcReturn')->where('grn_id',$id)->where('grn_type','PR')->where(Utilities::currentBCB())->first();
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
                'code_prefix'       => strtoupper('pr'),
                'code_type_field'   => 'grn_type',
                'code_type'         => strtoupper('pr'),
            ];
            $data['grn_code'] = Utilities::documentCode($doc_data);
        }
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->get();
        $data['accounts'] = TblAccCoa::where('chart_purch_expense_account',1)->where(Utilities::currentBC())->get();
        $data['store'] = TblDefiStore::where('store_entry_status',1)->where(Utilities::currentBCB())->get();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where(Utilities::currentBC())->get();
        $data['tax_on'] = TblDefiConstants::where('constants_type','tax_on')->where('constants_status','1')->get();
        $data['disc_on'] = TblDefiConstants::where('constants_type','disc_on')->where('constants_status','1')->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['grn_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_grn',
            'col_id' => 'grn_id',
            'col_code' => 'grn_code',
            'code_type_field'   => 'grn_type',
            'code_type'         => strtoupper('pr'),
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.purchase_return.form', compact('data'));
    }

    // public function getGRN($id){
    //     $data['all'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense','refPurcReturn')->where('grn_id',$id)->where('grn_type','GRN')->where(Utilities::currentBCB())->first();
    //     dd($data);
    //     return response()->json($data);
    // }


    public function getGRN($code){

        $data['status'] = "success";
        if(ViewPurcGRN::where('grn_code',$code)->exists()){
            $grn = ViewPurcGRN::where('grn_type','GRN');
            $grn = $grn->where('grn_code',$code);
            $data['all'] = $grn->get();
        }else{
            $data['status'] = "error";
        }
        $data['tax_on'] = TblDefiConstants::where('constants_type','tax_on')->where('constants_status','1')->get();
        $data['disc_on'] = TblDefiConstants::where('constants_type','disc_on')->where('constants_status','1')->get();
        return response()->json($data);
    }

    public function getPO($id){
        $data['all'] = TblPurcPurchaseOrder::with('po_details','supplier','lpo')->where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
        return response()->json($data);
    }

    public function getPR(Request $request , $id){

        $supplier_id = $request->supplier_id;
        $grn_id = $request->grn_id;
        $data['all'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')
        ->where('grn_id',$id)
        ->where('grn_type','PR')
        ->where(Utilities::currentBCB());

        if($data['all']->count() > 0){
            $data['all'] = $data['all']->first();
            foreach ($data['all']->grn_dtl as $value) {
                if(!empty($supplier_id)){
                    $pendingQtyQuery = "SELECT A.product_id, sum(A.collected_qty) collected_qty , sum(A.RETURNABLE_QTY) returnable_qty, sum(A.pending_qty) pending_qty FROM VW_PURC_PENDING_RETURN A
                    JOIN tbl_purc_grn B ON
                    A.Grn_id = B.GRN_ID AND A.GRN_ID = ". $grn_id ." AND B.supplier_id = ". $supplier_id ." AND B.branch_id = ". auth()->user()->branch_id ." AND A.product_id = ". $value->product_id ."
                    group by A.product_id";

                    $pendingQty = DB::select($pendingQtyQuery);

                    $value->purc_return_waiting_qty = "";
                    $value->purc_return_returnable_qty = "";
                    $value->purc_return_collected_qty = "";
                    if(count($pendingQty) > 0){
                        $value->purc_return_waiting_qty = $pendingQty[0]->pending_qty;
                        $value->purc_return_returnable_qty = $pendingQty[0]->returnable_qty;
                        $value->purc_return_collected_qty = $pendingQty[0]->collected_qty;
                    }
                }
            }
            // Filter Array If the Pending is 0 Delete that Index
            foreach ($data['all']->grn_dtl as $key => $value){
                if($value->purc_return_waiting_qty == 0){
                    unset($data['all']->grn_dtl[$key]);
                }
            }
            return $this->jsonSuccessResponse($data , "Data Loaded Successfully");
        }else{
            return $this->jsonErrorResponse([] , "Nothing Found");
        }
    }

    public function getPurcReturnWaitingQty($value,$id){

        $waitingQty_Qry1 = "select sum(grnd.TBL_PURC_GRN_DTL_RETABLE_QTY) qty from tbl_purc_grn grn
        join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
        where grn.grn_type = 'PR' and grnd.PRODUCT_BARCODE_ID = ".$value->product_barcode_id." and grn.grn_id = ".$id." and grn.branch_id = ".auth()->user()->branch_id."";

        $waitingQty_1 = DB::selectOne($waitingQty_Qry1);

        $waitingQty_Qry2 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
        join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
        where grn.grn_type = 'PR' and
        grnd.PRODUCT_BARCODE_ID = ".$value->product_barcode_id."
        and (grn.PURC_RETURN_REF = ".$id." OR grn.grn_id = ".$id.") and grn.branch_id = ".auth()->user()->branch_id." ";

        $waitingQty_2 = DB::selectOne($waitingQty_Qry2);

        $waitingQty1 = (isset($waitingQty_1->qty) && !empty($waitingQty_1->qty))?$waitingQty_1->qty:0;
        $waitingQty2 = (isset($waitingQty_2->qty) && !empty($waitingQty_2->qty))?$waitingQty_2->qty:0;


        return (int)(((float)$waitingQty1 - (float)$waitingQty2));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        /*
            NOTE: In this Form We are calculating voucher price of the base of Returnable Qty
        */
        $data = [];
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required',
            'supplier_id' => 'required|numeric',
            'grn_store' => 'required|numeric',
            'grn_notes' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        // Check Condition If the Voucher Exist OR Not
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $grn = TblPurcGrn::where('grn_type','PR')->where('grn_id',$id)->where(Utilities::currentBCB())->first();
                $grn->update_by_user_id = Auth::id();
            }else{
                $grn = new TblPurcGrn();
                $grn->grn_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcGrn',
                    'code_field'        => 'grn_code',
                    'code_prefix'       => strtoupper('pr'),
                    'code_type_field'   => 'grn_type',
                    'code_type'         => strtoupper('pr'),
                ];
                $grn->grn_code = Utilities::documentCode($doc_data);
                $grn->create_by_user_id = Auth::id();
            }
            $form_id = $grn->grn_id;
            $grn->grn_type = 'PR';
            $grn->grn_exchange_rate = $request->exchange_rate;
            $grn->payment_type_id = $request->payment_type_id;
            $grn->grn_date = date('Y-m-d', strtotime($request->grn_date));
            $grn->supplier_id = $request->supplier_id;
            $grn->purchase_order_id = $request->purchase_order_id;
            //$grn->grn_receiving_date = date('Y-m-d', strtotime($request->grn_receiving_date));
            $grn->store_id = $request->grn_store;
            $grn->grn_ageing_term_id = $request->grn_ageing_term_id;
            $grn->grn_ageing_term_value = $request->grn_ageing_term_value;
            $grn->grn_freight = $request->grn_freight;
            $grn->currency_id = $request->grn_currency;
            $grn->grn_bill_no = $request->grn_bill_no;
            $grn->grn_other_expense = $request->grn_other_expenses;
            $grn->purc_return_ref = isset($request->retqty_id) ? $request->retqty_id : "";
            $grn->grn_remarks = $request->po_notes;

            $grn->grn_total_items = $request->summary_total_item;
            $grn->grn_total_qty = $request->summary_qty_wt;
            $grn->grn_total_amount = $request->summary_amount;
            $grn->grn_total_disc_amount = $request->summary_disc_amount;
            $grn->grn_total_gst_amount = $request->summary_gst_amount;
            $grn->grn_total_fed_amount = $request->summary_fed_amount;
            $grn->grn_total_spec_disc_amount = $request->summary_spec_disc_amount;
            $grn->grn_total_gross_net_amount = $request->summary_net_amount;
            $grn->grn_overall_discount = $request->overall_discount_perc;
            $grn->grn_overall_disc_amount = $request->overall_disc_amount;
            $grn->grn_advance_tax_perc = $request->overall_vat_perc;
            $grn->grn_advance_tax_amount = $request->overall_vat_amount;
            $grn->grn_total_net_amount = $request->overall_net_amount;

            $grn->business_id = auth()->user()->business_id;
            $grn->company_id = auth()->user()->company_id;
            $grn->branch_id = auth()->user()->branch_id;
            $grn->grn_user_id = auth()->user()->id;
            $grn->grn_device_id = 1;
            $grn->save();

            $pr_dtls = TblPurcGrnDtl::where('grn_id',$grn->grn_id)->where('grn_type','PR')->where(Utilities::currentBCB())->get();
            foreach($pr_dtls as $pr_dtl){
                TblPurcGrnDtl::where('purc_grn_dtl_id',$pr_dtl->purc_grn_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            $net_total = 0;
            $amount_total = 0;
            $vat_amount_total = 0;
            $disc_amount_total = 0;
            $total_gross_amount = 0;
            $net_amount = 0;
            $spec_disc_amount = 0;
            $fed_total_amount = 0;
            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $dtl){
                    $prDtl = new TblPurcGrnDtl();
                    if(isset($id) && isset($account['purc_grn_dtl_id'])){
                        $prDtl->grn_id = $id;
                        $prDtl->purc_grn_dtl_id = $account['purc_grn_dtl_id'];
                    }else{
                        $prDtl->purc_grn_dtl_id = Utilities::uuid();
                        $prDtl->grn_id  = $grn->grn_id;
                    }
                    $prDtl->grn_type = 'PR';
                    $prDtl->sr_no = $sr_no;
                    $sr_no = $sr_no+1;
                    $prDtl->purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                    // $prDtl->supplier_id = $dtl['grn_supplier_id'];
                    $prDtl->product_id = $dtl['product_id'];
                    $prDtl->product_barcode_id = $dtl['product_barcode_id'];
                    // $prDtl->uom_id  = $dtl['uom_id'];
                    // $prDtl->tbl_purc_grn_dtl_packing = $dtl['pd_packing'];
                    // $prDtl->qty_base_unit = (isset($dtl['pd_packing'])?$dtl['pd_packing']:'0') * ((isset($dtl['quantity'])?$dtl['quantity']:'0')+(isset($dtl['foc_qty'])?$dtl['foc_qty']:'0'));
                    // $prDtl->tbl_purc_grn_dtl_supplier_barcode = $dtl['grn_supplier_barcode'];
                    $prDtl->product_barcode_barcode = $dtl['pd_barcode'];
                    $prDtl->qty_base_unit = $dtl['quantity'];
                    $prDtl->tbl_purc_grn_dtl_quantity = $dtl['quantity'];
                    $prDtl->tbl_purc_grn_dtl_rate = $this->addNo($dtl['rate']);
                    $prDtl->tbl_purc_grn_dtl_sale_rate = $this->addNo($dtl['sale_rate']);
                    $prDtl->tbl_purc_grn_dtl_sys_quantity = $this->addNo($dtl['sys_qty']);
                    $prDtl->tbl_purc_grn_dtl_mrp = $this->addNo($dtl['mrp']);
                    $prDtl->tbl_purc_grn_dtl_amount = $this->addNo($dtl['cost_amount']);
                    $prDtl->tbl_purc_grn_dtl_disc_percent = $this->addNo($dtl['dis_perc']);
                    $prDtl->tbl_purc_grn_dtl_disc_amount = $this->addNo($dtl['dis_amount']);
                    $prDtl->tbl_purc_grn_dtl_after_dis_amount = $this->addNo($dtl['after_dis_amount']);
                    $prDtl->tbl_purc_grn_dtl_tax_on = isset($dtl['pd_tax_on'])?$dtl['pd_tax_on']:'';
                    $prDtl->tbl_purc_grn_dtl_vat_percent = $this->addNo($dtl['gst_perc']);
                    $prDtl->tbl_purc_grn_dtl_vat_amount = $this->addNo($dtl['gst_amount']);
                    $prDtl->tbl_purc_grn_dtl_fed_percent = $this->addNo($dtl['fed_perc']);
                    $prDtl->tbl_purc_grn_dtl_fed_amount = $this->addNo($dtl['fed_amount']);
                    $prDtl->tbl_purc_grn_dtl_disc_on = isset($dtl['pd_disc'])?$dtl['pd_disc']:'';
                    $prDtl->tbl_purc_grn_dtl_spec_disc_perc = $this->addNo($dtl['spec_disc_perc']);
                    $prDtl->tbl_purc_grn_dtl_spec_disc_amount = $this->addNo($dtl['spec_disc_amount']);
                    $prDtl->tbl_purc_grn_dtl_gross_amount = $this->addNo($dtl['gross_amount']);
                    $prDtl->tbl_purc_grn_dtl_total_amount = $this->addNo($dtl['net_amount']);
                    $prDtl->tbl_purc_grn_dtl_net_tp = $this->addNo($dtl['net_tp']);
                    $prDtl->tbl_purc_grn_dtl_last_tp = $this->addNo($dtl['last_tp']);
                    $prDtl->tbl_purc_grn_dtl_vend_last_tp = $this->addNo($dtl['vend_last_tp']);
                    $prDtl->tbl_purc_grn_dtl_tp_diff = $this->addNo($dtl['tp_diff']);
                    $prDtl->tbl_purc_grn_dtl_gp_perc = $this->addNo($dtl['gp_perc']);
                    $prDtl->tbl_purc_grn_dtl_gp_amount = $this->addNo($dtl['gp_amount']);
                    $prDtl->tbl_purc_grn_dtl_remarks = $dtl['remarks'];
                    $prDtl->tbl_purc_grn_dtl_fc_rate = $this->addNo($dtl['fc_rate']);
                    $prDtl->business_id = auth()->user()->business_id;
                    $prDtl->company_id = auth()->user()->company_id;
                    $prDtl->branch_id = auth()->user()->branch_id;
                    $prDtl->tbl_purc_grn_dtl_user_id = auth()->user()->id;

                    $prDtl->save();
                    $net_total += Utilities::NumFormat($dtl['gross_amount']);
                    $total_gross_amount += Utilities::NumFormat($dtl['gross_amount']);
                    $amount_total += Utilities::NumFormat($dtl['cost_amount']);
                    $vat_amount_total += Utilities::NumFormat($dtl['gst_amount']);
                    $disc_amount_total += Utilities::NumFormat($dtl['dis_amount']);
                    $net_amount += Utilities::NumFormat($dtl['net_amount']);
                    $spec_disc_amount += Utilities::NumFormat($dtl['spec_disc_amount']);
                    $fed_total_amount += Utilities::NumFormat($dtl['fed_amount']);
                }
            }

            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $grn_id = $id;
                $grn = TblPurcGrn::where('grn_id',$grn_id)->where(Utilities::currentBCB())->first();
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
            $supplier = TblPurcSupplier::where('supplier_id',$request->supplier_id)->where(Utilities::currentBC())->first();

            $supplier_ca_id = (int)$supplier->supplier_account_id;


            $gst_ca_id = ''; // '3-01-05-0009';
            $amount_ca_id = ''; // '6-01-01-0001';
            $fed_ca_id = ''; //  '6-01-01-0001';
            $adv_tax_ca_id = ''; // '6-01-10-0001';
            $round_of_amt_ca_id = ''; // '7-01-01-0003';
            $discount_ca_id = ''; // '7-01-02-0002';
            $spec_disc_ca_id = ''; // '7-01-02-0002';
            $order_disc_ca_id = ''; // '7-01-02-0002';

            $allChartAcc = TblAccCoa::whereIn('chart_code',['3-01-05-0009','6-01-01-0001','6-01-10-0001','7-01-01-0003','7-01-02-0002'])->select('chart_account_id','chart_code','chart_name')->get();
            foreach ($allChartAcc as $oneChartAcc){
                if($oneChartAcc->chart_code == '3-01-05-0009'){
                    $gst_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '6-01-01-0001'){
                    $amount_ca_id = $oneChartAcc->chart_account_id;
                    $fed_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '6-01-10-0001'){
                    $adv_tax_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '7-01-01-0003'){
                    $round_of_amt_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '7-01-02-0002'){
                    $discount_ca_id = $oneChartAcc->chart_account_id;
                    $spec_disc_ca_id = $oneChartAcc->chart_account_id;
                    $order_disc_ca_id = $oneChartAcc->chart_account_id;
                }
            }

            $ChartArr = [
                $supplier_ca_id,
                $amount_ca_id,
                $discount_ca_id,
                $spec_disc_ca_id,
                $order_disc_ca_id,
                $gst_ca_id,
                $adv_tax_ca_id,
                $fed_ca_id,
                $round_of_amt_ca_id,
            ];
            $response = $this->ValidateCharAccCodeIds($ChartArr);
            if(isset($response['error']) && empty($response['error'])){
                return $this->jsonErrorResponse($data,"Account Code not correct",200);
            }

            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $grn_id,
                'voucher_no'            =>  $grn->grn_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->grn_date)),
                'voucher_descrip'       =>  'Purchase: '.$grn->grn_remarks .' - Ref:'.$request->grn_bill_no,
                'voucher_type'          =>  'PR',
                'voucher_posted'         =>  1,
                'branch_id'             =>  auth()->user()->branch_id,
                'business_id'           =>  auth()->user()->business_id,
                'company_id'            =>  auth()->user()->company_id,
                'voucher_user_id'       =>  auth()->user()->id,
            ];
            $voucher_sr_no = 1;
            $overall_net_amount = $request->overall_net_amount;
            $data['chart_account_id'] = $supplier_ca_id;
            $data['voucher_posted'] = 1;
            $data['voucher_credit'] = 0;
            $data['voucher_debit'] = abs($overall_net_amount);
            $data['voucher_sr_no'] = $voucher_sr_no++;
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);

            $action = 'add';
            if(!empty($disc_amount_total)){
                $data['chart_account_id'] = $discount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_credit'] = 0;
                $data['voucher_debit'] = abs($disc_amount_total);
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Discount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($spec_disc_amount)){
                $data['chart_account_id'] = $discount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_credit'] = 0;
                $data['voucher_debit'] = abs($spec_disc_amount);
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Spec Discount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($request->overall_disc_amount)){
                $data['chart_account_id'] = $discount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_credit'] = 0;
                $data['voucher_debit'] = abs($request->overall_disc_amount);
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Order Discount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($amount_total)){
                $data['chart_account_id'] = $amount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_credit'] = abs($amount_total);
                $data['voucher_debit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($vat_amount_total)){
                $data['chart_account_id'] = $gst_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_credit'] = abs($vat_amount_total);
                $data['voucher_debit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'GST Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($request->overall_vat_amount)){
                $data['chart_account_id'] = $adv_tax_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_credit'] = abs($request->overall_vat_amount);
                $data['voucher_debit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Adv Tax Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($fed_total_amount)){
                $data['chart_account_id'] = $fed_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_credit'] = abs($fed_total_amount);
                $data['voucher_debit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'FED Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            $debit_amount = abs($amount_total) + abs($vat_amount_total) + abs($request->overall_vat_amount) + abs($fed_total_amount);
            $credit_amount = abs($overall_net_amount) + abs($disc_amount_total) + abs($spec_disc_amount) + abs($request->overall_disc_amount);
          
            $round_of_amt = number_format($debit_amount,3,'.','') - number_format($credit_amount,3,'.','');
            $round_of_amt = number_format($round_of_amt,3,'.','');

            if(!empty($round_of_amt)) {

                $data['chart_account_id'] = $round_of_amt_ca_id;
                $data['voucher_posted'] = 1;
                if ($round_of_amt > 0) {
                    $data['voucher_debit'] = abs($round_of_amt);
                    $data['voucher_credit'] = 0;
                } else {
                    $data['voucher_debit'] = 0;
                    $data['voucher_credit'] = abs($round_of_amt);
                }
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Round of Amount';
                $this->proAccoVoucherInsert($voucher_id, $action, $table_name, $data);
            }

            $grnVou = TblPurcGrn::where('grn_id',$grn_id)->first();
            $grnVou->voucher_id = $voucher_id;
            $grnVou->save();

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
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
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

    public function print(Request $request, $id)
    {
        // dd($request->toArray());
        $data['title'] = 'Purchase Return';
        $data['type'] = $request->type;
        $data['id'] = $id;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $url = '/purchase-return/print/'.$id;
        $data['print_link'] = $url;
        // dd($url);
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
            if ($data['type'] == '1') {
                return view('prints.purchase.purchase_return.purchase_invoice', compact('data'));
            }elseif($data['type'] == '2'){
                return view('prints.purchase.purchase_return.ex_purchase_invoice', compact('data'));
            }elseif($data['type'] == '3'){
                return view('prints.purchase.purchase_return.purchase_invoice_uk', compact('data'));
            }elseif($data['type'] == '4'){
                return view('prints.purchase.purchase_return.purchase_invoice_landscape', compact('data'));
            }elseif($data['type'] == '5'){
                return view('prints.purchase.purchase_return.stock_direct_delivery', compact('data'));
            }elseif($data['type'] == '6'){
                return view('prints.purchase.purchase_return.dispatch_purchase_return_print', compact('data'));
            }else{
            return view('prints.purchase.purchase_return.purchase_return_print', compact('data'));
            }
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

            $grn = TblPurcGrn::where('grn_id',$id)->where('grn_type','PR')->where(Utilities::currentBCB())->first();
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
}
