<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Settings\TblDefiExpenseAccounts;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiConfigBranches;
use App\Models\TblDefiConfiguration;
use App\Models\TblPurcGrn;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleSales;
use App\Models\TblSoftBranch;
use App\Models\ViewPurcSupplier;
use App\Models\ViewSaleCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class POSVoucherController extends Controller
{
    public static $page_title = 'POS Voucher Posting';
    public static $redirect_url = 'pos-voucher';
    public static $menu_dtl_id = '154';
    //public static $menu_dtl_id = '147';

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
    public function create($id=null)
    {
       // dd(Session::get('dataSession'));
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['permission'] = self::$menu_dtl_id.'-view';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['page_data']['action'] = 'Insert Voucher';
        $data['branches'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();

        return view('accounts.pos_voucher.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id=null)
    {
       // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'pos_branch_ids' => 'required',
            'date_from' => 'required|date|date_format:d-m-Y|before_or_equal:date_to',
            'date_to' => 'required|date|date_format:d-m-Y|after_or_equal:date_from',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if(isset($request->pos_voucher) || isset($request->cash_voucher)){
        }else{
            return $this->jsonErrorResponse($data, 'Please Select Voucher Posting Type', 422);
        }
        DB::beginTransaction();
        try{
            $f_date = $request->date_from;
            $t_date = $request->date_to;
            $from_date = date('Y-m-d', strtotime($f_date));
            $to_date = date('Y-m-d', strtotime($t_date));
            $branches = $request->pos_branch_ids;
            $business_id = isset(Auth::user()->business_id)?Auth::user()->business_id:1;
            $company_id = isset(Auth::user()->company_id)?Auth::user()->company_id:1;

            $BC = "business_id = $business_id AND company_id = $company_id";
            $BC_ARR = [
                ['business_id',$business_id],
                ['company_id',$company_id]
            ];
            $config = TblDefiConfiguration::first();
            //pos voucher posting
            if(isset($request->pos_voucher)){
                /*$f_date = $request->date_from;
                $t_date = $request->date_to;
                $from_date = date('Y-m-d', strtotime($f_date));
                $to_date = date('Y-m-d', strtotime($t_date));
                $branches = $request->pos_branch_ids;
                $business_id = Auth::user()->business_id;
                $company_id = Auth::user()->company_id;
                $BC = "business_id = $business_id AND company_id = $company_id";*/
                $all_dates_qry = "select distinct SALES_DATE, branch_id  from  VW_SALE_SALES_INVOICE
                    where $BC AND branch_id in (".implode(",",$branches).") and SALES_DATE between to_date('".$from_date."','yyyy/mm/dd') and to_date('".$to_date."','yyyy/mm/dd') order by SALES_DATE";
                $all_dates = DB::select($all_dates_qry);

              //  dd($all_dates);
                foreach ($all_dates as $insert_date_wise){
                    $branch_id = $insert_date_wise->branch_id;
                    $date =  date('Y-m-d', strtotime($insert_date_wise->sales_date));

                    $del_data_qry = "Delete from TBL_ACCO_VOUCHER where $BC AND branch_id = $branch_id AND voucher_type = 'POS' and voucher_date = to_date('".$date."','yyyy/mm/dd')";
                    DB::delete($del_data_qry);
                    $del_data_qry = "Delete from TBL_ACCO_VOUCHER where $BC AND branch_id = $branch_id AND voucher_type = 'RPOS' and voucher_date = to_date('".$date."','yyyy/mm/dd')";
                    DB::delete($del_data_qry);
                    $del_data_qry = "Delete from TBL_ACCO_VOUCHER where $BC AND branch_id = $branch_id AND voucher_type = 'CADJ' and voucher_date = to_date('".$date."','yyyy/mm/dd')";
                    DB::delete($del_data_qry);

                    $discount_chart_account_id = $config->sale_discount;
                    $income_chart_account_id = $config->sale_income;
                    $vat_payable_chart_account_id = $config->sale_vat_payable;
                    $walk_in_customer_ac = '13425120060615';
                    $cash_in_hand_ac = $config->sale_cash_ac;
                    $payment_receive_db = $config->payment_receive_dr_ac;
                    $payment_receive_cr = $config->payment_receive_cr_ac;

                    $table_name = 'tbl_acco_voucher';
                    $action = 'add';
                    $where_clause = '';
                    $data = [];
                    $qry = "select distinct sales_id from  VW_SALE_SALES_INVOICE
                            where $BC AND branch_id = $branch_id AND SALES_TYPE = 'POS'
                                and SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')";

                    $sales_ids_data = DB::select($qry);
                   // dd($sales_ids_data);
                    $sum_net_total = 0;
                    foreach ($sales_ids_data as $sales){
                        $sale_invoice = TblSaleSales::where('sales_id',$sales->sales_id)->where($BC_ARR)
                            ->where('branch_id',$branch_id)->first();
                        $ac = '';
                        // 1 = Cash
                        $data['sale_type'] = $sale_invoice->sales_sales_type;
                        $data['current_sale_id'] = $sales->sales_id;

                        if($sale_invoice->sales_sales_type == 1){
                            $ac = (int)$cash_in_hand_ac;
                            $descrip = 'Cash';
                        }
                        // 2 = Credit , 3 = cash and credit
                        if($sale_invoice->sales_sales_type == 2 || $sale_invoice->sales_sales_type == 3){
                            $customer = TblSaleCustomer::where('customer_id',$sale_invoice->customer_id)->where($BC_ARR);
                            
                            if($customer->count() > 0){
                                $customer = $customer->first();
                            }else{
                                $customer = ViewSaleCustomer::where('customer_id',$sale_invoice->customer_id)->where($BC_ARR)->first();  
                            }

                            $data['customer'] = $customer;
                            $data['bcarr'] = $BC_ARR;
                            $data['customer_id'] = $sale_invoice->customer_id;
                            $ac = (int)$customer->customer_account_id;
                            $descrip = 'Credit or (cash and credit)';
                        }
                        // 4 =Visa Card , 5 = Cash and Visa Card
                        if($sale_invoice->sales_sales_type == 4 || $sale_invoice->sales_sales_type == 5){
                            $ac = $sale_invoice->bank_id;
                            $descrip = 'Visa or (Cash and Visa)';
                        }
                        // "CADJ"
                        if(!empty($ac)){
                           // dump($sale_invoice->toArray());
                            $voucher_id = Utilities::uuid();
                            $net_total = number_format((float)$sale_invoice->sales_net_amount,3);
                            if($sale_invoice->sales_sales_type == 1){
                                $sum_net_total += number_format((float)$sale_invoice->sales_net_amount,3);
                            }
                            $data = [
                                'voucher_id'            =>  $voucher_id,
                                'voucher_document_id'   =>  $sale_invoice->sales_id,
                                'voucher_no'            =>  $sale_invoice->sales_code,
                                'voucher_date'          =>  date('Y-m-d', strtotime($date)),
                                'voucher_descrip'       =>  'POS: '.$descrip,
                                'voucher_type'          =>  "POS",
                                'branch_id'             =>  $sale_invoice->branch_id,
                                'business_id'           =>  $sale_invoice->business_id,
                                'company_id'            =>  $sale_invoice->company_id,
                                'voucher_user_id'       =>  $sale_invoice->sales_sales_man,
                            ];
                            $data['chart_account_id'] = $ac;
                            $data['voucher_debit'] = abs($net_total);
                            $data['voucher_credit'] = 0;
                            $data['voucher_sr_no'] = 1;
                            // for debit entry net_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $sales_dtl_qry = "select sum(SALES_DTL_DISC_AMOUNT) disc_amount, sum(SALES_DTL_VAT_AMOUNT) vat_amount, sum(SALES_DTL_TOTAL_AMOUNT) amount from  VW_SALE_SALES_INVOICE
                                        where $BC AND branch_id = $branch_id AND SALES_DATE = to_date('".$date."','yyyy/mm/dd') AND sales_id = ".$sales->sales_id;
                            $sales_dtl = DB::selectOne($sales_dtl_qry);

                            $data['chart_account_id'] = $discount_chart_account_id;
                            $data['voucher_debit'] = abs(number_format((float)$sales_dtl->disc_amount,3));
                            $data['voucher_credit'] = 0;
                            $data['voucher_sr_no'] = 2;
                            // for debit entry disc_amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $data['chart_account_id'] = $income_chart_account_id;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs(number_format((float)$net_total,3));//(abs(number_format((float)$net_total,3)) - abs(number_format((float)$sales_dtl->vat_amount,3)));
                            $data['voucher_sr_no'] = 3;
                            // for credit entry amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $data['chart_account_id'] = $income_chart_account_id;
                            $data['voucher_debit'] = abs(number_format((float)$sales_dtl->vat_amount,3));
                            $data['voucher_credit'] = 0;
                            $data['voucher_sr_no'] = 4;
                            // for credit entry amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $data['chart_account_id'] = $vat_payable_chart_account_id;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs(number_format((float)$sales_dtl->vat_amount,3));
                            $data['voucher_sr_no'] = 5;
                            // for credit entry vat_amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                        }
                    }

                    $sale_return_discount_ac = $config->sale_return_discount;
                    $sale_return_income_ac = $config->sale_return_income;
                    $sale_return_vat_payable_ac = $config->sale_return_vat_payable;
                    $sale_return_cash_ac = $config->sale_return_cash_ac;

                    $qry_return = "select distinct sales_id from  VW_SALE_SALES_INVOICE
                            where $BC AND branch_id = $branch_id AND SALES_TYPE = 'RPOS'
                                and SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')";

                    $sales_return_data = DB::select($qry_return);
                    $sum_rpos_net_total = 0;
                    foreach ($sales_return_data as $sales_return){
                        $sale_invoice = TblSaleSales::where('sales_id',$sales_return->sales_id)->where($BC_ARR)
                            ->where('branch_id',$branch_id)->first();
                        $ac = '';
                        // 1 = Cash
                        if($sale_invoice->sales_sales_type == 1){
                            $ac = (int)$sale_return_cash_ac;
                            $descrip = 'Cash';
                        }
                        // 2 =Credit , 3 = cash and credit
                        if($sale_invoice->sales_sales_type == 2 || $sale_invoice->sales_sales_type == 3){
                            $customer = TblSaleCustomer::where('customer_id',$sale_invoice->customer_id)->where($BC_ARR)->first();
                            $ac = (int)$customer->customer_account_id;
                            $descrip = 'Credit or (cash and credit)';
                        }
                        // 4 =Visa Card , 5 = Cash and Visa Card
                        if($sale_invoice->sales_sales_type == 4 || $sale_invoice->sales_sales_type == 5){
                            $ac = $sale_invoice->bank_id;
                            $descrip = 'Visa or (Cash and Visa)';
                        }
                        if(!empty($ac)){
                            // dump($sale_invoice->toArray());
                            $voucher_id = Utilities::uuid();
                            $net_total = number_format((float)$sale_invoice->sales_net_amount,3);
                            if($sale_invoice->sales_sales_type == 1){
                                $sum_rpos_net_total += number_format((float)$sale_invoice->sales_net_amount,3);
                            }
                            $data = [
                                'voucher_id'            =>  $voucher_id,
                                'voucher_document_id'   =>  $sale_invoice->sales_id,
                                'voucher_no'            =>  $sale_invoice->sales_code,
                                'voucher_date'          =>  date('Y-m-d', strtotime($date)),
                                'voucher_descrip'       =>  'RPOS: '.$descrip,
                                'voucher_type'          =>  "RPOS",
                                'branch_id'             =>  $sale_invoice->branch_id,
                                'business_id'           =>  $sale_invoice->business_id,
                                'company_id'            =>  $sale_invoice->company_id,
                                'voucher_user_id'       =>  $sale_invoice->sales_sales_man,
                            ];
                            $data['chart_account_id'] = $ac;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($net_total);
                            $data['voucher_sr_no'] = 1;
                            // for debit entry net_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $sales_return_dtl_qry = "select sum(SALES_DTL_DISC_AMOUNT) disc_amount, sum(SALES_DTL_VAT_AMOUNT) vat_amount, sum(SALES_DTL_TOTAL_AMOUNT) amount from  VW_SALE_SALES_INVOICE
                                        where $BC AND branch_id = $branch_id AND SALES_DATE = to_date('".$date."','yyyy/mm/dd') AND sales_id = ".$sales_return->sales_id ;
                            $sales_return_dtl = DB::selectOne($sales_return_dtl_qry);

                            $data['chart_account_id'] = $sale_return_discount_ac;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs(number_format((float)$sales_return_dtl->disc_amount,3));
                            $data['voucher_sr_no'] = 2;
                            // for debit entry disc_amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $data['chart_account_id'] = $sale_return_income_ac;
                            $data['voucher_debit'] = abs(number_format((float)$net_total,3));//(abs(number_format((float)$net_total,3)) - abs(number_format((float)$sales_return_dtl->vat_amount,3)));
                            $data['voucher_credit'] = 0;
                            $data['voucher_sr_no'] = 3;
                            // for credit entry amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $data['chart_account_id'] = $sale_return_income_ac;
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs(number_format((float)$sales_return_dtl->vat_amount,3));
                            $data['voucher_sr_no'] = 3;
                            // for credit entry amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                            $data['chart_account_id'] = $sale_return_vat_payable_ac;
                            $data['voucher_debit'] = abs(number_format((float)$sales_return_dtl->vat_amount,3));
                            $data['voucher_credit'] = 0;
                            $data['voucher_sr_no'] = 4;
                            // for credit entry vat_amount_total
                            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                        }
                    }
                    if($sum_net_total != 0){
                        $SIQry = "select sum(sales_net_amount) sales_net_amount from(
                                        Select distinct sales_id, case when     SALES_TYPE = 'SR' THEN sales_net_amount * 1 ELSE sales_net_amount END sales_net_amount
                                        from vw_sale_sales_invoice
                                        where sales_sales_type = 1 AND (SALES_TYPE = 'SI' OR SALES_TYPE = 'SR') AND
                                        SALES_DATE =  to_date('".$date."','yyyy/mm/dd') AND
                                        $BC AND branch_id = $branch_id
                                    ) xyz";
                        $SI_data = DB::selectOne($SIQry);

                        $doc_data = [
                            'biz_type'          => 'branch',
                            'model'             => 'TblAccoVoucher',
                            'code_field'        => 'voucher_no',
                            'code_prefix'       => strtoupper('CADJ'),
                            'branch_id'             =>  $branch_id,
                            'business_id'           =>  $business_id,
                            'company_id'            =>  $company_id,
                        ];
                        $code = Utilities::documentCode($doc_data);
                        $voucher_id = Utilities::uuid();
                        $v = $SI_data->sales_net_amount . " + " . $sum_net_total ." - ".$sum_rpos_net_total;
                        $f = ( (float)$SI_data->sales_net_amount + (float)$sum_net_total ) - (float)$sum_rpos_net_total;
                        $data = [
                            'voucher_id'            =>  $voucher_id,
                            'voucher_document_id'   =>  '',
                            'voucher_no'            =>  $code,
                            'voucher_date'          =>  date('Y-m-d', strtotime($date)),
                            'voucher_descrip'       =>  'CADJ: cash adjustment: ',
                            'voucher_type'          =>  "CADJ",
                            'branch_id'             =>  $branch_id,
                            'business_id'           =>  $business_id,
                            'company_id'            =>  $company_id,
                            'voucher_user_id'       =>  isset(Auth::user()->id)?Auth::user()->id:81,
                        ];

                        $data['chart_account_id'] = $payment_receive_db;
                        $data['voucher_debit'] = abs($f);
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = 1;
                        // for debit entry sum_net_total
                        $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                        $data['chart_account_id'] = $payment_receive_cr;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($f);
                        $data['voucher_sr_no'] = 2;
                        // for Credit entry sum_net_total
                        $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                    }
                }
            }

            //cash voucher posting
            if(isset($request->cash_voucher)){
                /*$f_date = $request->date_from;
                $t_date = $request->date_to;
                $from_date = date('Y-m-d', strtotime($f_date));
                $to_date = date('Y-m-d', strtotime($t_date));
                $branches = $request->pos_branch_ids;
                $business_id = Auth::user()->business_id;
                $company_id = Auth::user()->company_id;
                $BC = "business_id = $business_id AND company_id = $company_id";*/

                $begin = new \DateTime($from_date);
                $end = new \DateTime($to_date);
                $end = $end->modify( '+1 day' );

                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($begin, $interval ,$end);
              //  dd($daterange);
                $del_data_qry = "Delete from TBL_ACCO_VOUCHER where branch_id in(".implode(",",$branches).") AND $BC AND voucher_type = 'CADJ' and ( voucher_date between to_date('".$from_date."','yyyy/mm/dd') and to_date('".$to_date."','yyyy/mm/dd'))";
                DB::delete($del_data_qry);

                foreach($daterange as $now_date){
                    $date = $now_date->format("Y-m-d");
                    $query = "select distinct sales_sales_man,sales_sales_man_name,branch_id from vw_sale_sales_invoice
                                where (SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")";
                    $list = \Illuminate\Support\Facades\DB::select($query);

                  //  dd($list);
                    foreach($list as $saleData){
                        $CaSaleQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                            select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                            where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SI' OR sales_type = 'POS') and sales_sales_type = '1' and
                                                            (SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")
                                                        ) abc";
                        $CaSale = \Illuminate\Support\Facades\DB::select($CaSaleQuery);
                        $CaSale = isset($CaSale[0]->sales_net_amount)?$CaSale[0]->sales_net_amount:0;


                        $CaSaleRQuery = "select sum(sales_net_amount) sales_net_amount from(
                                                        select distinct sales_id , sales_net_amount from vw_sale_sales_invoice
                                                        where sales_sales_man = '".$saleData->sales_sales_man."' and (sales_type = 'SR' OR sales_type = 'RPOS') and sales_sales_type = '1' and
                                                        (SALES_DATE between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")
                                                    ) abc";
                        $CaSaleR = \Illuminate\Support\Facades\DB::select($CaSaleRQuery);
                        $CaSaleR = isset($CaSaleR[0]->sales_net_amount)?$CaSaleR[0]->sales_net_amount:0;

                        $t_Ca_Sale = $CaSale - $CaSaleR;

                        $ColctionQuery = "select sum(day_amount) amount from vw_sale_day where day_payment_handover_received = '".$saleData->sales_sales_man."' and day_case_type = 'payment-received'
                                                        and (day_date between to_date('".$date."','yyyy/mm/dd') and to_date('".$date."','yyyy/mm/dd')) and branch_id in( ".implode(",",$branches).")";
                        $Colction = \Illuminate\Support\Facades\DB::select($ColctionQuery);
                        $ColctionAmt = isset($Colction[0]->amount)?$Colction[0]->amount:0;

                        $diffAmt = $ColctionAmt - $t_Ca_Sale;

                        $general_cash_ac = $config->general_cash_ac;
                        $excess_cash_ac = $config->excess_cash_ac;
                        $payment_receive_dr_ac = $config->payment_receive_dr_ac;
                        $payment_receive_cr_ac = $config->payment_receive_cr_ac;
                        $ChartArr = [
                            $general_cash_ac,
                            $excess_cash_ac,
                            $payment_receive_dr_ac,
                            $payment_receive_cr_ac,
                        ];
                        $response = $this->ValidateCharCode($ChartArr);
                        if($response == false){
                            return $this->returnjsonerror("voucher Account Code not correct",404);
                        }
                        $action = 'add';
                        $table_name = 'tbl_acco_voucher';
                        $voucher_id = Utilities::uuid();
                        $where_clause = '';
                        //voucher start
                        $doc_data = [
                            'biz_type'          => 'branch',
                            'model'             => 'TblAccoVoucher',
                            'code_field'        => 'voucher_no',
                            'code_prefix'       => strtoupper('cadj'),
                            'branch_id'             =>  $saleData->branch_id,
                            'business_id'           =>  $business_id,
                            'company_id'            =>  $company_id,
                        ];
                        $customer_code = Utilities::documentCode($doc_data);

                        $data = [
                            'voucher_id'            =>  $voucher_id,
                            'voucher_document_id'   =>  $voucher_id,
                            'voucher_no'            =>  $customer_code,
                            'voucher_date'          =>  date('Y-m-d', strtotime($date)),
                            'voucher_descrip'       =>  'Excess/Shortage Cash : '.$saleData->sales_sales_man_name,
                            'voucher_type'          =>  "CADJ",
                            'branch_id'             =>  $saleData->branch_id,
                            'business_id'           =>  $business_id,
                            'company_id'            =>  $company_id,
                            'voucher_user_id'       =>  $saleData->sales_sales_man
                        ];
                        $data['chart_account_id'] = $general_cash_ac;
                        if($diffAmt > 0){
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs(number_format((float)$diffAmt,3));
                        }else{
                            $data['voucher_debit'] = abs(number_format((float)$diffAmt,3));
                            $data['voucher_credit'] = 0;
                        }
                        $data['voucher_sr_no'] = 1;
                        // for debit entry total_amount
                        $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                        $action = 'add';
                        $data['chart_account_id'] = $excess_cash_ac;
                        if($diffAmt > 0){
                            $data['voucher_debit'] = abs(number_format((float)$diffAmt,3));
                            $data['voucher_credit'] = 0;
                        }else{
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs(number_format((float)$diffAmt,3));
                        }
                        $data['voucher_sr_no'] = 2;
                        // for credit entry total_amount
                        $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                        //new collection voucher
                        $data = [
                            'voucher_id'            =>  $voucher_id,
                            'voucher_document_id'   =>  '',
                            'voucher_no'            =>  $customer_code,
                            'voucher_date'          =>  date('Y-m-d', strtotime($date)),
                            'voucher_descrip'       =>  'CADJ: cash adjustment: ',
                            'voucher_type'          =>  "CADJ",
                            'branch_id'             =>  $saleData->branch_id,
                            'business_id'           =>  $business_id,
                            'company_id'            =>  $company_id,
                            'voucher_user_id'       =>  $saleData->sales_sales_man,
                        ];

                        $data['chart_account_id'] = $payment_receive_dr_ac;
                        $data['voucher_debit'] = abs($ColctionAmt);
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = 3;
                        // for debit entry sum_net_total
                        $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);

                        $data['chart_account_id'] = $payment_receive_cr_ac;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($ColctionAmt);
                        $data['voucher_sr_no'] = 4;
                        // for Credit entry sum_net_total
                        $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data,$where_clause);
                    }

                }
            }
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
        $data = array_merge($data, Utilities::returnJsonNewForm());
        return $this->jsonSuccessResponse($data, 'Voucher Posted Successfully', 200);

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

    public function storeSIVoucher($date_from,$date_to,$vouNull = null)
    {
        // from 2021-06-01  - to 2021-06-01
        $err_sale_id = "";
        try{
            $vou_null = "";
            if(!isset($vouNull)){
                $vou_null = "and voucher_id is null";
            }
            $col = "sales_id,customer_id,bank_id,sales_sales_type,sales_code,sales_date,sales_remarks,business_id,company_id,branch_id,sales_user_id";
            $qry = "select $col from tbl_sale_sales where lower(sales_type) = 'si' $vou_null
                and (SALES_DATE between to_date('".$date_from."','yyyy/mm/dd') and to_date('".$date_to."','yyyy/mm/dd'))";
            dump($qry);
            $all_sales = DB::select($qry);
          //  dd($all_sales);
            $count = count($all_sales);
            dump($count);
            foreach ($all_sales as $k=>$sales){
                DB::beginTransaction();

                $table_name = 'tbl_acco_voucher';
                $sale_id = $sales->sales_id;
                $err_sale_id = $sale_id;
                $voucher_id = Utilities::uuid();
                TblAccoVoucher::where('voucher_document_id',$sale_id)->delete();
                $customer = ViewSaleCustomer::where('customer_id',$sales->customer_id)->first();
                $bcb = ['business_id'=>$sales->business_id,'company_id',$sales->company_id,'branch_id',$sales->branch_id];
                $new_sale = TblSaleSales::with('expense','simp_dtls')->where('sales_id',$sale_id)->first();

                if($sales->sales_sales_type == 1){
                    $customer_chart_account_id = (int)Session::get('dataSession')->sale_cash_ac;
                }
                if($sales->sales_sales_type == 2){
                    $customer_chart_account_id = (int)$customer->customer_account_id;
                }
                if($sales->sales_sales_type == 3 || $sales->sales_sales_type == 4 || $sales->sales_sales_type == 5){
                    $customer_chart_account_id = $sales->bank_id;
                }

                $net_total = 0;
                $vat_amount_total = 0;
                $disc_amount_total = 0;
                $amount_total = 0;
                $sales_dtl_total_amount = 0;
                foreach ($new_sale['simp_dtls'] as $dtls){
                    $vat_amount_total +=  $dtls['sales_dtl_vat_amount'];
                    $net_total +=  $dtls['sales_dtl_total_amount'];
                    $sales_dtl_total_amount +=  $dtls['sales_dtl_total_amount'];
                    $disc_amount_total +=  $dtls['sales_dtl_disc_amount'];
                    $amount_total +=  $dtls['sales_dtl_amount'];
                }
                foreach($new_sale->expense as $expense){
                    $net_total +=  $expense['sales_expense_amount'];
                }
                $data = [
                    'voucher_id'            =>  $voucher_id,
                    'voucher_document_id'   =>  $sale_id,
                    'voucher_no'            =>  $sales->sales_code,
                    'voucher_date'          =>  date('Y-m-d', strtotime($sales->sales_date)),
                    'voucher_descrip'       =>  $sales->sales_sales_type.': '.$sales->sales_remarks,
                    'voucher_type'          =>  $sales->sales_sales_type,
                    'branch_id'             =>  $sales->branch_id,
                    'business_id'           =>  $sales->business_id,
                    'company_id'            =>  $sales->company_id,
                    'voucher_user_id'       =>  $sales->sales_user_id,
                    'document_ref_account'  =>  (int)$customer->customer_account_id,
                    'vat_amount'            =>  $vat_amount_total,
                ];
                $data['chart_account_id'] = $customer_chart_account_id;
                $data['voucher_debit'] = abs($net_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = 1;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $discount_chart_account_id = Session::get('dataSession')->sale_discount;
                $data['chart_account_id'] = $discount_chart_account_id;
                $data['voucher_debit'] = abs($disc_amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = 2;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $income_chart_account_id = Session::get('dataSession')->sale_income;
                $data['chart_account_id'] = $income_chart_account_id;
                $data['voucher_debit'] = 0;
               // $data['voucher_credit'] = abs($sales_dtl_total_amount) - abs($disc_amount_total);
                $data['voucher_credit'] = (abs($amount_total) + abs($vat_amount_total));
                $data['voucher_sr_no'] = 3;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $data['chart_account_id'] = $income_chart_account_id;
                $data['voucher_debit'] = abs($vat_amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = 4;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $vat_payable_chart_account_id = Session::get('dataSession')->sale_vat_payable;
                $data['chart_account_id'] = $vat_payable_chart_account_id;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($vat_amount_total);
                $data['voucher_sr_no'] = 5;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $sr_no = 6;
                foreach($new_sale->expense as $expense){
                    if(0 < $this->addNo($expense['expense_amount'])){
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($expense['expense_amount']);
                    }else{
                        $data['voucher_debit'] = abs($expense['expense_amount']);
                        $data['voucher_credit'] = 0;
                    }
                    $data['chart_account_id'] = $expense['account_id'];
                    $data['voucher_sr_no'] = $sr_no;
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = Carbon::now();
                    DB::table($table_name)->insert($data);
                    $sr_no++;
                }

                $new_sale->voucher_id = $voucher_id;
                $new_sale->save();
                echo ($k)."<br>";
                DB::commit();
            }

        }catch (Exception $e){
            dump("error: ". $err_sale_id);
            dump("error: ". $e->getMessage());
        }
    }

    public function storePRVoucher($date_from,$date_to,$vouNull = null){
        // from 2022-01-01  - to 2021-01-31
        // firstcare.royalerp.net/grn-vouch/2000-01-01/2022-08-31/1
        $err_grn_id = "";
        $accounts_dr_cr = TblDefiExpenseAccounts::where('expense_accounts_type','grn_acc')->where(Utilities::currentBC())->pluck('expense_accounts_dr_cr','chart_account_id');
        // dump($accounts_dr_cr);
        try{
            $vou_null = "";
            if(!isset($vouNull)){
                $vou_null = "and voucher_id is null";
            }

            $col = "grn_id,supplier_id,grn_date,grn_code,grn_remarks,payment_type_id,grn_bill_no,business_id,company_id,branch_id,grn_user_id";
            $qry = "select $col from tbl_purc_grn where lower(grn_type) = 'pr' $vou_null
                and (grn_date between to_date('".$date_from."','yyyy/mm/dd') and to_date('".$date_to."','yyyy/mm/dd'))";
            // dump($qry);
            $all_grns = DB::select($qry);
            //  dd($all_grns);
            $count = count($all_grns);
            dump($count);
            $table_name = 'tbl_acco_voucher';

            $cash_acc_account_id = (int)Session::get('dataSession')->sale_cash_ac;
            $purchase_stock_account_id = (int)Session::get('dataSession')->purchase_stock;

            foreach ($all_grns as $k=>$grn){
                DB::beginTransaction();

                $grn_id = $grn->grn_id;
                $err_grn_id = $grn_id;
                $voucher_id = Utilities::uuid();
                TblAccoVoucher::where('voucher_document_id',$grn_id)->delete();

                $supplier = ViewPurcSupplier::where('supplier_id',$grn->supplier_id)->first();

                $new_sale = TblPurcGrn::with('grn_expense','simp_dtls')->where('grn_id',$grn_id)->first();

                if($grn->payment_type_id == 1){
                    $supplier_chart_account_id = $cash_acc_account_id;
                }
                if($grn->payment_type_id == 2 || $grn->payment_type_id == 3 || $grn->payment_type_id == 4 || $grn->payment_type_id == 5){
                    $supplier_chart_account_id = (int)$supplier->supplier_account_id;
                }

                $amount_total = 0;
                $disc_amount_total = 0;
                $vat_amount_total = 0;
                $net_total = 0;
                foreach ($new_sale['simp_dtls'] as $dtls){
                    $amount_total +=  $dtls['tbl_purc_grn_dtl_amount'];
                    $disc_amount_total +=  $dtls['tbl_purc_grn_dtl_disc_amount'];
                    $vat_amount_total +=  $dtls['tbl_purc_grn_dtl_vat_amount'];
                    $net_total +=  $dtls['tbl_purc_grn_dtl_total_amount'];
                }
                // dump($new_sale);
                foreach($new_sale->grn_expense as $exp){
                    $net_total +=  $exp['grn_expense_amount'];
                }
                $data = [
                    'voucher_id'            =>  $voucher_id,
                    'voucher_document_id'   =>  $grn_id,
                    'voucher_no'            =>  $grn->grn_code,
                    'voucher_date'          =>  date('Y-m-d', strtotime($grn->grn_date)),
                    'voucher_descrip'       =>  $grn->grn_remarks .' - Ref:'.$grn->grn_bill_no,
                    'voucher_type'          =>  'PR',
                    'branch_id'             =>  $grn->branch_id,
                    'business_id'           =>  $grn->business_id,
                    'company_id'            =>  $grn->company_id,
                    'voucher_user_id'       =>  $grn->grn_user_id,
                    'document_ref_account'  =>  (int)$supplier->supplier_account_id,
                    'vat_amount'            =>  $vat_amount_total,
                ];
                $i_a = 1;
                $data['chart_account_id'] = $supplier_chart_account_id;
                $data['voucher_debit'] = abs($net_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $i_a = $i_a + 1;
                $data['chart_account_id'] = Session::get('dataSession')->purchase_discount;
                $data['voucher_debit'] = abs($disc_amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $i_a = $i_a + 1;
                $data['chart_account_id'] = Session::get('dataSession')->purchase_stock;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($amount_total);
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $i_a = $i_a + 1;
                $data['chart_account_id'] = Session::get('dataSession')->purchase_vat;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($vat_amount_total);
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                // dd($accounts_dr_cr);
                // dd($new_sale);
                if(isset($new_sale->grn_expense)){
                    $i_a = $i_a + 1;
                    foreach($new_sale->grn_expense as $expense){
                        $data['chart_account_id'] = $expense['chart_account_id'];
                        if(0 < $this->addNo($expense['expense_amount'])){
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($expense['expense_amount']);
                        }else{
                            $data['voucher_debit'] = abs($expense['expense_amount']);
                            $data['voucher_credit'] = 0;
                        }
                        $data['voucher_sr_no'] = $i_a;
                        $data['created_at'] = Carbon::now();
                        $data['updated_at'] = Carbon::now();
                        DB::table($table_name)->insert($data);

                        $i_a = $i_a + 1;
                    }
                }

                $new_sale->voucher_id = $voucher_id;
                $new_sale->save();
                echo ($k)."<br>";
                DB::commit();
            }

        }catch (Exception $e){
            dump("error: ". $err_grn_id);
            dump("error: ". $e->getMessage());
        }
    }

    public function storeGRNVoucher($date_from,$date_to,$vouNull = null){
        // from 2022-01-01  - to 2021-01-31
        // firstcare.royalerp.net/grn-vouch/2000-01-01/2022-08-31/1
        $err_grn_id = "";
        $accounts_dr_cr = TblDefiExpenseAccounts::where('expense_accounts_type','grn_acc')->where(Utilities::currentBC())->pluck('expense_accounts_dr_cr','chart_account_id');
        try{
            $vou_null = "";
            if(!isset($vouNull)){
                $vou_null = "and voucher_id is null";
            }

            $col = "grn_id,supplier_id,grn_date,grn_code,grn_remarks,payment_type_id,grn_bill_no,business_id,company_id,branch_id,grn_user_id";
            $qry = "select $col from tbl_purc_grn where lower(grn_type) = 'grn' $vou_null
                and (grn_date between to_date('".$date_from."','yyyy/mm/dd') and to_date('".$date_to."','yyyy/mm/dd')) and branch_id = " . Auth::user()->branch_id . " and company_id = " . Auth::user()->company_id . " and business_id = " . Auth::user()->business_id;
            // dd(Session::all());
            $all_grns = DB::select($qry);
            $count = count($all_grns);
            dump($count);
            $table_name = 'tbl_acco_voucher';

            $cash_acc_account_id = (int)Session::get('dataSession')->sale_cash_ac;
            // dd(Session::get('dataSession'));
            $purchase_stock_account_id = (int)Session::get('dataSession')->purchase_stock;
            foreach ($all_grns as $k=>$grn){
                DB::beginTransaction();

                $grn_id = $grn->grn_id;
                $err_grn_id = $grn_id;
                $voucher_id = Utilities::uuid();
                TblAccoVoucher::where('voucher_document_id',$grn_id)->delete();

                $supplier = ViewPurcSupplier::where('supplier_id',$grn->supplier_id)->first();

                $new_sale = TblPurcGrn::with('grn_expense','simp_dtls')->where('grn_id',$grn_id)->first();

                // if($grn->payment_type_id == 1){
                //     $supplier_chart_account_id = $cash_acc_account_id;
                // }
                // if($grn->payment_type_id == 2 || $grn->payment_type_id == 3 || $grn->payment_type_id == 4 || $grn->payment_type_id == 5){
                //     $supplier_chart_account_id = (int)$supplier->supplier_account_id;
                // }

                // Always Put the GRN into the Supplier Account
                $supplier_chart_account_id = (int)$supplier->supplier_account_id;

                $amount_total = 0;
                $disc_amount_total = 0;
                $vat_amount_total = 0;
                $net_total = 0;
                foreach ($new_sale['simp_dtls'] as $dtls){
                    $amount_total +=  $dtls['tbl_purc_grn_dtl_amount'];
                    $disc_amount_total +=  $dtls['tbl_purc_grn_dtl_disc_amount'];
                    $vat_amount_total +=  $dtls['tbl_purc_grn_dtl_vat_amount'];
                    $net_total +=  $dtls['tbl_purc_grn_dtl_total_amount'];
                }

                foreach($new_sale->grn_expense as $exp){
                    if($accounts_dr_cr[$exp['chart_account_id']] == 'dr'){
                        $net_total +=  $exp['grn_expense_amount'];
                    }else{
                        $net_total -=  $exp['grn_expense_amount'];
                    }
                }

                //voucher start
                $data = [
                    'voucher_id'            =>  $voucher_id,
                    'voucher_document_id'   =>  $grn_id,
                    'voucher_no'            =>  $grn->grn_code,
                    'voucher_date'          =>  date('Y-m-d', strtotime($grn->grn_date)),
                    'voucher_descrip'       =>  $grn->grn_remarks .' - Ref:'.$grn->grn_bill_no,
                    'voucher_type'          =>  'GRN',
                    'branch_id'             =>  $grn->branch_id,
                    'business_id'           =>  $grn->business_id,
                    'company_id'            =>  $grn->company_id,
                    'voucher_user_id'       =>  $grn->grn_user_id,
                    'document_ref_account'  =>  (int)$supplier->supplier_account_id,
                    'vat_amount'            =>  $vat_amount_total,
                ];
                // dump($vat_amount_total);

                $i_a = 1;
                $data['chart_account_id'] = $supplier_chart_account_id;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($net_total);
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $i_a = $i_a + 1;
                $data['chart_account_id'] = Session::get('dataSession')->purchase_discount;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($disc_amount_total);
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $i_a = $i_a + 1;
                $data['chart_account_id'] = Session::get('dataSession')->purchase_stock;
                $data['voucher_debit'] = abs($amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                $i_a = $i_a + 1;
                $data['chart_account_id'] = Session::get('dataSession')->purchase_vat;
                $data['voucher_debit'] = abs($vat_amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $i_a;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                DB::table($table_name)->insert($data);

                if(isset($new_sale->grn_expense)){
                    $i_a = $i_a + 1;
                    foreach($new_sale->grn_expense as $expense){
                        $data['chart_account_id'] = $expense['chart_account_id'];
                        if($accounts_dr_cr[$expense['chart_account_id']] == 'dr'){
                            $data['voucher_debit'] = abs($expense['grn_expense_amount']);
                            $data['voucher_credit'] = 0;
                        }else{
                            $data['voucher_debit'] = 0;
                            $data['voucher_credit'] = abs($expense['grn_expense_amount']);
                        }
                        $data['voucher_sr_no'] = $i_a;
                        $data['created_at'] = Carbon::now();
                        $data['updated_at'] = Carbon::now();
                        DB::table($table_name)->insert($data);

                        $i_a = $i_a + 1;
                    }
                }

                $new_sale->voucher_id = $voucher_id;
                $new_sale->save();
                echo ($k)."<br>";
                DB::commit();
            }

        }catch (Exception $e){
            dump("error: ". $err_grn_id);
            dump("error: ". $e->getMessage());
        }
    }
}
