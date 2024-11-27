<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\CustomerController;
use App\Library\Utilities;
use App\Models\TblAccCoa;
use App\Models\TblPurcProduct;
use App\Models\TblSaleCustomer;
use App\Models\TblSoftReports;
use App\Models\TblSoftReportStaticCriteria;
use App\Models\ViewInveStock;
use App\Models\ViewSaleSalesInvoice;
use App\Models\ViewPurcGRN;
use App\Models\ViewAccoChartAccountHelp;
use App\Models\TblDefiCurrency;
use Illuminate\Http\Request;
use App\Models\TblDefiStore;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcSupplierType;
use App\Models\TblSaleCustomerType;
use App\Models\ViewPurcGroupItem;
use App\Models\ViewPurcPurchaseOrder;
use App\Models\User;
// db and Validator
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserReportsController2 extends Controller
{
    public static $page_title = 'Reporting';
    //public static $redirect_url = 'user-report';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($reportType,$caseType, $id = null)
    {   // TblSoftReportingUserStudio
        $menu = TblSoftReports::select('parent_menu_id')->where('report_case',$caseType)->first();
        if($menu->parent_menu_id == 70){
           $type = 'sale';
        }
        if($menu->parent_menu_id == 75){
            $type = 'inventory';
        }
        if($menu->parent_menu_id == 15){
            $type = 'accounts';
        }
        if($menu->parent_menu_id == 49){
            $type = 'purchase';
        }
        $data['page_data'] = [];
        $data['report_type'] = $reportType;
        $data['case_name'] = $caseType;
        $data['page_data']['title'] = self::$page_title;
        //$data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['path_index'] = '/reports/report-list/'.$type;
        if(isset($id)){

        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['page_data']['action'] = 'Generate';
        }
        $data['report'] = TblSoftReports::with('report_user_criteria')
            ->where('report_case',$caseType)
            ->where('report_entry_status',1)->first();
        $selected_criteria = explode(",",$data['report']->report_static_criteria);
        $static_criteria = TblSoftReportStaticCriteria::where('report_static_criteria_entry_status',1)->get();
        $collect_static_criteria =  collect($static_criteria);
        $data['selected_criteria'] = [];
        for($i=0; $i<count($selected_criteria); $i++){
            $search = $collect_static_criteria->where('report_static_criteria_id',$selected_criteria[$i])->first();
            if($search){
                array_push($data['selected_criteria'],$search->report_static_criteria_case);
            }
        }
        $now = new \DateTime("now");
        $data['date_from'] =  $now; //$now->modify('-6 months');
        $data['date_to'] = new \DateTime("now");
        $data['chart_accounts'] = TblAccCoa::where('chart_level',4)->orderby('chart_code')->get();
        $data['customer_list'] = TblSaleCustomer::where('customer_entry_status',1)->get();
        $data['supplier_list'] = TblPurcSupplier::where('supplier_entry_status',1)->get();
        $data['product_list'] = DB::select('select distinct product_id,product_name from vw_sale_sales_invoice union all
                                            select distinct product_id,product_name from vw_inve_stock ');
        $data['voucher_type_list'] = DB::select('select distinct voucher_type from tbl_acco_voucher');
        $data['sales_type_list'] = DB::select('select distinct sales_type from vw_sale_sales_invoice');
        $data['payment_types'] = DB::select('select * from tbl_defi_payment_type');
        $data['store'] = TblDefiStore::pluck('store_name','store_id');
        $data['supplier_group'] = TblPurcSupplierType::where('supplier_type_entry_status',1)->get();
        $data['customer_group'] = TblSaleCustomerType::where('customer_type_entry_status',1)->get();
        $data['group_item'] = ViewPurcGroupItem::orderBy('group_item_name_string')->get();
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
        if($reportType == 'static'){
            return view('reports.report_static_create', compact('data'));
        }else{
            abort('404');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
       // dd($request->toArray());
        $data = [];
        session()->forget('data');

        DB::beginTransaction();
        try {

            $clause_business_id = ' business_id = ' . auth()->user()->business_id;
            $clause_company_id = ' AND company_id = ' . auth()->user()->company_id;
            $clause_branch_id = ' AND branch_id = ' . auth()->user()->branch_id;
            $data['clause_business_id'] = $clause_business_id;
            $data['clause_company_id'] = $clause_company_id;
            $data['clause_branch_id'] = $clause_branch_id;
            $to_date = isset($request->date_to)?$request->date_to:"";
            $from_date = isset($request->date_from)?$request->date_from:"";
            $date_time_from = isset($request->between_date_time_from)?$request->between_date_time_from:"";
            $date_time_to = isset($request->between_date_time_to)?$request->between_date_time_to:"";
            $date = isset($request->date)?$request->date:"";
            $sales_type = isset($request->sales_type)?$request->sales_type:"";
            $product_id = isset($request->product_id)?$request->product_id:"";
            $chart_account_id = isset($request->chart_account)?$request->chart_account:"";
            $product_group = isset($request->product_group)?$request->product_group:"";
            $rate_between = isset($request->rate_between)?$request->rate_between:"";
            $rate_type = isset($request->rate_type)?$request->rate_type:"";
            $sale_types_multiple = isset($request->sale_types_multiple)?$request->sale_types_multiple:[];
            $chart_account_multiple = isset($request->chart_account_multiple)?$request->chart_account_multiple:[];
            $product_ids = isset($request->product_ids)?$request->product_ids:[];
            $customer_ids = isset($request->customer_ids)?$request->customer_ids:[];
            $voucher_types = isset($request->voucher_types)?$request->voucher_types:[];
            $payment_types = isset($request->payment_types)?$request->payment_types:[];
            $store = isset($request->store)?$request->store:[];
            $supplier_group = isset($request->supplier_group)?$request->supplier_group:[];
            $customer_group = isset($request->customer_group)?$request->customer_group:[];
            $supplier_ids = isset($request->supplier_ids)?$request->supplier_ids:[];
            $users_ids = isset($request->users_ids)?$request->users_ids:[];
            $hide_total = isset($request->hide_total)?1:"";

            $report_cases = ['closing_day','sale_type_wise','summary_of_daily_activity',
                'vouchers_list','sale_invoice','trial_balance','accounting_ledger','grn_list',
                'top_sale_products','stock_report','stock_activity_summary','item_stock_ledger',
                'chart_account_list','stock_detail_document_wise','supplier_list','customer_list',
                'po_list','product_rate','bank_reconciliation'];

            if(in_array($request->report_case,$report_cases)){
                $data['branch_id'] = $request->report_branch_name;
                $list = [];
                if($request->report_case == 'closing_day'){
                    $data['key'] = 'closing_day';
                    $data['page_title'] = 'Daily Closing Report';
                    $data['date_time_from'] = date('Y-m-d h:m:s A', strtotime($date_time_from));
                    $data['date_time_to'] = date('Y-m-d h:m:s A', strtotime($date_time_to));
                    $data['only_date'] = date('Y-m-d', strtotime($date_time_from));
                }

                if($request->report_case == 'sale_type_wise'){
                    $data['key'] = 'sale_type_wise';
                    $data['page_title'] = 'Sales Type Wise Report';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['sales_type'] = $sale_types_multiple;
                    $data['payment_types'] = $payment_types;
                    $data['users'] = $users_ids;
                }

                if($request->report_case == 'summary_of_daily_activity'){
                    $data['key'] = 'summary_of_daily_activity';
                    $data['page_title'] = 'Summary of Daily Activity';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $Datequery = "select distinct sales_date from vw_sale_sales_invoice where sales_type ='SI' and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') order by sales_date";
                    $data['Date'] = DB::select($Datequery);
                    $SIquery = "select distinct sales_sales_man,sales_sales_man_name from vw_sale_sales_invoice where sales_type ='SI' and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                    $data['SI'] = DB::select($SIquery);
                    $SRquery = "select distinct sales_sales_man,sales_sales_man_name from vw_sale_sales_invoice where sales_type ='SR' and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                    $data['SR'] = DB::select($SRquery);
                    $data['SI_Count'] = count($data['SI']);
                    $data['SR_Count'] = count($data['SR']);
                    $list = '';
                }

                if($request->report_case == 'vouchers_list'){
                    $data['key'] = 'vouchers_list';
                    $data['page_title'] = 'Vouchers List';
                    $data['chart_account'] = $chart_account_multiple;
                    $data['voucher_type'] = $voucher_types ;
                    $to_date = $request->date_to;
                    $from_date = $request->date_from;
                    $data['where'] = '';
                    $data['where_chart_account'] = '';
                    $data['where_voucher_type'] = '';
                    //--------chart account id--------------
                    if(count($chart_account_multiple) > 0){
                        $chart_account_cond = '';
                        $data['where_chart_account'] = ' and (';
                        foreach($chart_account_multiple as $chart_account)
                        {
                            $chart_account_cond .= "chart_account_id ='".$chart_account."' OR ";
                        }
                        $data['where_chart_account'] .= substr($chart_account_cond,0,-4);
                        $data['where_chart_account'] .= ')';
                    }
                    //-----------end chart--------------
                    //--------voucher type --------------
                    if(count($voucher_types) > 0){
                        $voucher_type_cond = '';
                        $data['where_voucher_type'] = ' and (';
                        foreach($voucher_types as $voucher_type)
                        {
                            $voucher_type_cond .= "voucher_type ='".$voucher_type."' OR ";
                        }
                        $data['where_voucher_type'] .= substr($voucher_type_cond,0,-4);
                        $data['where_voucher_type'] .= ')';
                    }
                    //-----------end type--------------
                    $data['where'] = $data['where_voucher_type'].''.$data['where_chart_account'];
                    $data['hide_total'] = $hide_total;
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $query = "Select distinct branch_id,branch_name from vw_acco_voucher where voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') and branch_id = '".$request->report_branch_name."' and ( voucher_debit <> 0 OR  voucher_credit <> 0 ) ".$data['where'];
                    $list = DB::select($query);
                }

                if($request->report_case == 'sale_invoice'){
                    $list = [];
                    $data['key'] = 'sale_invoice';
                    $data['page_title'] = 'Sale Invoice';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['customer_ids'] = $customer_ids;
                    $data['product_ids'] = $product_ids;
                    $data['sale_types_multiple'] = $sale_types_multiple;
                    $data['users'] = $users_ids;
                }

                if($request->report_case == 'customer_list'){
                    $data['key'] = 'customer_list';
                    $data['page_title'] = 'Customer List';
                    $data['customer_group'] = $customer_group;
                    $data['customer'] = TblSaleCustomer::where('customer_entry_status',1)->get();
                    if(!empty($customer_group)){
                        $data['customer'] = $data['customer']->whereIn('customer_type',$customer_group);
                    }
                }

                if($request->report_case == 'trial_balance'){
                    $data['key'] = 'trial_balance';
                    $data['page_title'] = 'Trial Balance';
                    $data['date'] = date('Y-m-d', strtotime($date));
                }

                if($request->report_case == 'accounting_ledger') {
                    $data['key'] = 'accounting_ledger';
                    $data['page_title'] = 'Accounting Ledger';
                    $data['currency'] = TblDefiCurrency::select('currency_symbol')->where('currency_default',1)->where('business_id',auth()->user()->business_id)->first();
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    if (!empty($chart_account_id)) {
                        $data['chart_account'] = TblAccCoa::where('chart_account_id', $chart_account_id)->first();
                        //$data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id]))->first()->code;
                        $data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id,'']))->first()->code;
                    } else {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                    $where = "( chart_account_id = " . $chart_account_id . " )";
                    $where .= ' AND (business_id = ' . auth()->user()->business_id . ' AND branch_id = ' . auth()->user()->branch_id . ')';
                    $query = "Select * from vw_acco_voucher where voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') and ( voucher_debit <> 0 OR  voucher_credit <> 0 ) and " .$where.'order by voucher_date,voucher_no';
                    $list = DB::select($query);
                }

                if($request->report_case == 'bank_reconciliation') {
                    dd('jko');
                    $data['key'] = 'bank_reconciliation';
                    $data['page_title'] = 'Bank Reconciliation';
                    $data['currency'] = TblDefiCurrency::select('currency_symbol')->where('currency_default',1)->where('business_id',auth()->user()->business_id)->first();
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    if (!empty($chart_account_id)) {
                        $data['chart_account'] = TblAccCoa::where('chart_account_id', $chart_account_id)->first();
                        //$data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id]))->first()->code;
                        $data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id,'']))->first()->code;
                    } else {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                    $where = "( chart_account_id = " . $chart_account_id . " )";
                    $where .= ' AND (business_id = ' . auth()->user()->business_id . ' AND branch_id = ' . auth()->user()->branch_id . ')';
                    $query = "Select * from vw_acco_voucher where voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') and ( voucher_debit <> 0 OR  voucher_credit <> 0 ) and " .$where.'order by voucher_date,voucher_no';
                    $list = DB::select($query);
                }

                if($request->report_case == 'grn_list'){
                    $data['key'] = 'grn_list';
                    $data['page_title'] = 'GRN List';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $getdata = ViewPurcGRN::where('branch_id', $request->report_branch_name)
                        ->whereBetween('grn_date',[$data['from_date'],$data['to_date']])
                        ->orderby('grn_date')->orderby('grn_code')
                        ->get();
                    $list = [];
                    foreach ($getdata as $row)
                    {
                        $today = date('Y-m-d', strtotime($row['grn_date']));
                        $list[$today][$row['grn_code']][] = $row;
                    }

                }

                if($request->report_case == 'supplier_list'){
                    $data['key'] = 'supplier_list';
                    $data['page_title'] = 'Supplier List';
                    $data['supplier_group'] = $supplier_group;
                    $data['supplier'] = TblPurcSupplier::where('supplier_entry_status',1)->get();
                    if(!empty($supplier_group)){
                        $data['supplier'] = $data['supplier']->whereIn('supplier_type',$supplier_group);
                    }
                }
                if($request->report_case == 'product_rate'){
                    $data['key'] = 'product_rate';
                    $data['page_title'] = 'Product Rate';
                    $data['product_group'] = $product_group;
                    if(!empty($rate_type)){
                        $data['rate_type'] = $rate_type;
                    }
                    if(!empty($rate_between) && !empty($rate_type)){
                        $data['rate_between'] = $rate_between;
                    }
                }

                if($request->report_case == 'po_list'){
                    $data['key'] = 'po_list';
                    $data['page_title'] = 'Purchase Order List';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                }

                if($request->report_case == 'top_sale_products'){
                    $data['key'] = 'top_sale_products';
                    $data['page_title'] = 'Top Sale Products';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                }

                if($request->report_case == 'stock_report'){
                    $data['key'] = 'stock_report';
                    $data['page_title'] = 'Stock Report';
                    $data['date'] = date('Y-m-d', strtotime($date));
                }

                if($request->report_case == 'stock_activity_summary'){
                    $data['key'] = 'stock_activity_summary';
                    $data['page_title'] = 'Stock Activity Summary';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $date_opening_bal = date("Y-m-d", strtotime($data['from_date'] ." -1 day"));

                    $query = "select distinct s.business_id, s.company_id, s.branch_id,br.branch_name, s.sales_store_id, st.store_name, s.product_id, vp.product_name, s.product_barcode_id,vp.product_barcode_barcode,opening_stock,qty_in,qty_out,opening_stock + qty_in - qty_out balance
                            from (select distinct s.branch_id, s.sales_store_id, s.product_id, s.product_barcode_id, s.business_id, s.company_id, get_stock_current_qty_date ( s.product_id, s.product_barcode_id, s.business_id, s.company_id, s.branch_id, '', to_date('".$date_opening_bal."', 'yyyy/mm/dd')) opening_stock,
                            sum (s.qty_in) over (partition by s.branch_id, s.sales_store_id, s.product_id, s.product_barcode_id) qty_in,
                            sum (s.qty_out) over (partition by s.branch_id, s.sales_store_id, s.product_id, s.product_barcode_id)  qty_out
                            from vw_purc_stock_dtl s where (document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd')) and (".$clause_business_id . $clause_company_id . $clause_branch_id.")) s,
                             tbl_soft_branch br,tbl_defi_store st,vw_purc_product_barcode vp
                             where vp.base_barcode = 1 and s.branch_id = br.branch_id and s.sales_store_id = st.store_id(+) and s.product_barcode_id = vp.product_barcode_id and s.product_id = vp.product_id";
                    $list = DB::select($query);
                }

                if($request->report_case == 'item_stock_ledger'){
                    $data['key'] = 'item_stock_ledger';
                    $data['page_title'] = 'Item Stock Ledger';
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    if (!empty($product_id)) {
                        $data['product'] = ViewSaleSalesInvoice::select('product_id','product_name')->where('product_id',$product_id)->first();
                    } else {
                        return $this->jsonErrorResponse($data, 'Must select Product', 422);
                    }
                    $query = "SELECT DISTINCT 1 data_priority, -- opening Bal
                        NULL DOCUMENT_DATE,NULL DOCUMENT_CODE,NULL DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,0 QTY_IN,0 RATE_IN,0 AMOUNT_IN,0 QTY_OUT,0 RATE_OUT,0 AMOUNT_OUT,
                        GET_STOCK_CURRENT_QTY_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,SALES_STORE_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) BALANCE_QTY,
                        GET_STOCK_AVG_RATE_ON_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) avg_rate,
                        GET_STOCK_AVG_RATE_ON_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,to_date('".$data['date']."', 'yyyy/mm/dd'))* GET_STOCK_CURRENT_QTY_DATE (PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,SALES_STORE_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) BALANCE_AMOUNT,0 RATE_EFFECT
                        FROM VW_PURC_STOCK_DTL WHERE  product_id = '".$product_id."'
                        UNION ALL SELECT DISTINCT 2 data_priority,DOCUMENT_DATE,DOCUMENT_CODE,DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,QTY_IN,STOCK_RATE rate_in,QTY_IN * STOCK_RATE AMOUNT_IN,0 QTY_OUT,0 RATE_OUT,0 amount_out,0 BALANCE_QTY,0 AVG_RATE,0 BALANCE_AMOUNT,RATE_EFFECT
                        FROM VW_PURC_STOCK_DTL WHERE STOCK_CALCULATION_EFFECT = '+' AND document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') AND product_id = '".$product_id."'
                        UNION ALL SELECT DISTINCT 2 data_priority,DOCUMENT_DATE,DOCUMENT_CODE,DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,0 QTY_IN,0 RATE_IN,
                        0 amount_in,QTY_OUT,0 RATE_out,0 amount_out,0 BALANCE_QTY,0 average_rate,0 BALANCE_AMOUNT,RATE_EFFECT
                        FROM VW_PURC_STOCK_DTL WHERE STOCK_CALCULATION_EFFECT = '-' AND document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') AND product_id = '".$product_id."' order by  data_priority,document_date ";

                    $list = DB::select($query);
                }

                if($request->report_case == 'chart_account_list'){
                    $data['key'] = 'chart_account_list';
                    $data['page_title'] = 'Chart of Account List';
                    $list = TblAccCoa::where('branch_id', $request->report_branch_name)
                            ->orderby('chart_code')
                            ->get();
                }

                if($request->report_case == 'stock_detail_document_wise'){
                    $list = '';
                    $data['key'] = 'stock_detail_document_wise';
                    $data['page_title'] = 'Stock Detail Document Wise';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['date_opening_bal'] = date("Y-m-d", strtotime($data['from_date'] ." -1 day"));

                    $document_type_query = "select tbl_soft_company.business_id,'OS' field_value,'Opening Stock' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'GRN' field_value,'GRN' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'PR' field_value,'GRN Ret' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'POS' field_value,'POS' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'POSR' field_value,'POS Ret' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'SR' field_value,'Sale Return' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'SI' field_value,'Sale Invoice' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'ST' field_value,'Stock Trans' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'STR' field_value,'Stock Rec' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'SA' field_value,'Stock Adj' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'EI' field_value,'Exp' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'SP' field_value,'Sampl' field_heading from tbl_soft_company where rownum = 1 union all
                                        select tbl_soft_company.business_id,'DI' field_value,'Damag' field_heading from tbl_soft_company where rownum = 1";
                    $data['document_type'] = DB::select($document_type_query);
                    $types = '';
                    foreach ($data['document_type'] as $type){
                        $types .=   "'".$type->field_value."' ".$type->field_value.', ';
                    }
                    $data['types'] = rtrim($types, ", ");

                   /* $query = "select * from (select product_id,business_id,company_id,branch_id,document_type,
                                get_stock_current_qty_date (product_id,'',business_id,company_id,branch_id,'',to_date('".$date_opening_bal."', 'yyyy/mm/dd')) opening_stock,qty_base_unit_value,
                                0 closing_bal
                                from vw_purc_stock_dtl s where document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') and (".$clause_business_id . $clause_company_id . $clause_branch_id."))
                                pivot (sum (qty_base_unit_value) for document_type in (".$types.")) order by product_id";
                    $data['list_data'] = DB::select($query);*/
                }
            }

            $inventoryCase = ['opening_stock','stock_transfer','stock_adjustment',
                'stock_receiving','expired_items','sample_items','damaged_items'];
            if(in_array($request->report_case,$inventoryCase)){

                $data['to_date'] = date('Y-m-d', strtotime($to_date));
                $data['from_date'] = date('Y-m-d', strtotime($from_date));

                $getdata = ViewInveStock::where('branch_id', $request->report_branch_name)
                    ->whereBetween('stock_date',[$data['from_date'],$data['to_date']]);

                if($request->report_case == 'opening_stock'){
                    $data['key'] = 'opening_stock';
                    $data['page_title'] = 'Opening Stock Report';
                    $getdata = $getdata->where('stock_code_type','like','os');
                    if(!empty($store)){
                        $getdata = $getdata->whereIn('stock_store_from_id',$store);
                    }

                }
                if($request->report_case == 'stock_transfer'){
                    $data['key'] = 'stock_transfer';
                    $data['page_title'] = 'Stock Transfer Report';
                    $getdata = $getdata->where('stock_code_type','like','st');
                }
                if($request->report_case == 'stock_adjustment'){
                    $data['key'] = 'stock_adjustment';
                    $data['page_title'] = 'Stock Adjustment Report';
                    $getdata = $getdata->where('stock_code_type','like','sa');
                }
                if($request->report_case == 'expired_items'){
                    $data['key'] = 'expired_items';
                    $data['page_title'] = 'Expired Items Report';
                    $getdata = $getdata->where('stock_code_type','like','ei');
                }
                if($request->report_case == 'sample_items'){
                    $data['key'] = 'sample_items';
                    $data['page_title'] = 'Sample Items Report';
                    $getdata = $getdata->where('stock_code_type','like','si');
                }
                if($request->report_case == 'damaged_items'){
                    $data['key'] = 'damaged_items';
                    $data['page_title'] = 'Damaged Items Report';
                    $getdata = $getdata->where('stock_code_type','like','di');
                }
                if($request->report_case == 'stock_receiving'){
                    $data['key'] = 'stock_receiving';
                    $data['page_title'] = 'Stock Receiving';
                    $getdata = $getdata->where('stock_code_type','like','str');
                }
                $getdata = $getdata->orderby('stock_date')->orderby('stock_code')->get();

                $list = [];
                foreach ($getdata as $row)
                {
                    $today = date('Y-m-d', strtotime($row['stock_date']));
                    $list[$today][$row['stock_code']][] = $row;
                }
            }

            $data['list'] = $list;
            session(['data' => $data]);
            $dataJs['url'] = route( 'reports.view_report');

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

        $dataJs['redirect'] =  ''; // $request->report_case;

        return $this->jsonSuccessResponse($dataJs, trans('message.report_ready'), 200);
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

    public function reportList($case_name)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title . ' List';
        $data['page_data']['path_index'] = '';
        if(isset($id)){
            /*if(UserReportController::where('reporting_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
            }else{
                abort('404');
            }*/
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['page_data']['type'] = '';
            $data['page_data']['action'] = '';
        }
        if($case_name == 'purchase'){
            $menu_dtl_id = 49;
        }
        if($case_name == 'sale'){
            $menu_dtl_id = 70;
        }
        if($case_name == 'accounts'){
            $menu_dtl_id = 15;
        }
        if($case_name == 'inventory'){
            $menu_dtl_id = 75;
        }
        $data['case_name'] = $case_name;
        $data['list'] = TblSoftReports::where('report_entry_status',1)
            ->where('parent_menu_id',$menu_dtl_id)->get();

        return view('reports.list', compact('data'));
    }

    public function ViewReport(){
        $data = Session::get('data');
        /***
         *  General Report
         ********/

        $report_cases = ['closing_day','sale_type_wise','summary_of_daily_activity',
            'vouchers_list','sale_invoice','trial_balance','accounting_ledger','grn_list',
            'top_sale_products','stock_report','stock_activity_summary','item_stock_ledger',
            'chart_account_list','stock_detail_document_wise','supplier_list','customer_list',
            'po_list','product_rate','bank_reconciliation'];


        if(in_array($data['key'],$report_cases)){
            return view('reports.static_reports.'.$data['key']);
        }

        /***
        *  Inventory Stock Report
        ********/

        $inventory_stock_keys = ['opening_stock','stock_transfer','stock_adjustment',
            'stock_receiving','expired_items','sample_items','damaged_items'];

        if(in_array($data['key'],$inventory_stock_keys)){
            return view('reports.static_reports.inventory_stock');
        }else{
            abort('404');
        }
    }
}
