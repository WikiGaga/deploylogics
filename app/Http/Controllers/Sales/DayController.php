<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Defi\TblDefiShift;
use App\Models\TblAccCoa;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiMerchant;
use App\Models\TblSaleDay;
use App\Models\TblDefiDenomination;
use App\Models\TblSaleDayDtl;
use App\Models\TblSaleSales;
use App\Models\TblSoftPOSTerminal;
use App\Models\User;
use App\Models\TblAccoPaymentType;
use App\Models\ViewSaleDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Library\Utilities;
use Image;
// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Session;


class DayController extends Controller
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($casetype,$id = null)
    {
        $data['page_data'] = [];
        $data['casetype'] = $casetype;
        if($casetype == 'day-opening'){
            $data['page_data']['title'] = 'Day Opening';
            $data['stock_menu_id'] = '63';
            $data['document_type'] = '';
            $data['document_code'] = '';
        }
        if($casetype == 'day-closing'){
            $data['page_data']['title'] = 'Day Closing';
            $data['stock_menu_id'] = '64';
            $data['document_type'] = '';
            $data['document_code'] = '';
        }
        if($casetype == 'payment-handover'){
            $data['page_data']['title'] = 'Payment Handover';
            $data['document_type'] = 'PHO';
            $data['stock_menu_id'] = '72';
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblSaleDay',
                'code_field'        => 'day_code',
                'code_prefix'       => strtoupper('PHO'),
                'code_type_field'   => 'day_case_type',
                'code_type'         => 'payment-handover'

            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
            // $data['document_code'] = $this->documentCode(TblSaleDay::where('day_case_type','payment-handover')->where(Utilities::currentBCB())->max('day_code'),'PHO');
            $data['payment_type'] = TblAccoPaymentType::where(Utilities::currentBC())->get();
        }
        if($casetype == 'payment-received'){
            $data['page_data']['title'] = 'Payment Received';
            $data['document_type'] = 'PREC';
            $data['stock_menu_id'] = '73';
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblSaleDay',
                'code_field'        => 'day_code',
                'code_prefix'       => strtoupper('PREC'),
                'code_type_field'   => 'day_case_type',
                'code_type'         => 'payment-received'
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
            //$data['document_code'] = $this->documentCode(TblSaleDay::where('day_case_type','payment-received')->where(Utilities::currentBCB())->max('day_code'),'PREC');
            $data['payment_type'] = TblAccoPaymentType::where('payment_type_entry_status',1)->where(Utilities::currentBC())->get();
        }
        $data['page_data']['path_index'] = $this->prefixIndexPage.'day/'.$casetype;
        $data['page_data']['create'] = '/day/'.$casetype.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleDay::where('day_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['permission'] = $data['stock_menu_id'].'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleDay::with('terminal','dtl')->where('day_id',$id)->where('day_case_type',$casetype)->where(Utilities::currentBCB())->first();
                $data['document_code'] = $data['current']->day_code;
                $data['denomins'] = TblSaleDay::where('day_id',$id)->where('day_case_type',$casetype)->where(Utilities::currentBCB())->get();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        
        $data['users'] = User::where('user_entry_status',1)->where('user_type','pos')->where('branch_id',auth()->user()->branch_id)->orderby(DB::raw('lower(name)'))->get();

        //$data['users'] = User::where('user_entry_status',1)->where(Utilities::currentBCB())->orderby(DB::Raw('lower(name)'))->get();
        $data['payment_person'] = User::where('user_entry_status',1)->where(Utilities::currentBCB())->orderby(DB::Raw('lower(name)'))->get();
        $data['denomination'] = TblDefiDenomination::where('denomination_entry_status',1)->where(Utilities::currentBC())->orderBy('sr_no')->get();
        $data['shift'] = TblDefiShift::where(Utilities::currentBC())->orderBy('shift_sr_no')->get();
        if($casetype == 'payment-handover' || $casetype == 'payment-received'){
            $arr = [
                'biz_type' => 'business',
                'code' => $data['document_code'],
                'link' => $data['page_data']['create'],
                'table_name' => 'tbl_sale_day',
                'col_id' => 'day_id',
                'col_code' => 'day_code',
                'code_type_field'   => 'day_case_type',
                'code_type'         => $casetype,
            ];
            $data['switch_entry'] = $this->switchEntry($arr);
        }
        return view('sales.day.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$casetype, $id = null)
    {
        //    dd($request->toArray());
        $data = [];
        if($casetype == 'day-closing'){
            $validator = Validator::make($request->all(), [
                'day_shift' => 'required',
                'terminal_id' => 'required'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'day_shift' => 'required'
            ]);
        }
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        if($casetype == 'day-closing'){
            if(!isset($id))
            {
                $from_date_merg = $request->day_date.' '.$request->from_time;
                $day_date = date('Y-m-d H:i:s', strtotime($from_date_merg));

                $to_date_merg =  $request->to_date.' '.$request->to_time;
                $to_date = date('Y-m-d H:i:s', strtotime($to_date_merg));

                if(TblSaleDay::where('saleman_id','=',$request->saleman_id)
                ->where('day_date','=',$day_date)
                ->where('shift_id','=',$request->day_shift)->exists())
                {
                    return $this->jsonErrorResponse($data, 'Shift already closed.', 422);
                }
            }
        }
        

        DB::beginTransaction();
        try{

            if(isset($id)){
                $day_id = $id;
                $saleday = TblSaleDay::where('day_id',$id)->where('day_case_type',$casetype)->where(Utilities::currentBCB())->first();
                $code = $saleday->day_code;
                $voucher_id = $saleday->voucher_id;
                $created_at = $saleday->created_at;
                TblSaleDayDtl::where('day_id',$id)->delete();
                TblSaleDay::where('day_id',$id)->where('day_case_type',$casetype)->where(Utilities::currentBCB())->delete();
            }else{
                $day_id = Utilities::uuid();
            }
            if(!isset($id) || (isset($id) && empty($code))){
                if($casetype == 'payment-handover'){
                    $doc_data = [
                        'biz_type'          => 'branch',
                        'model'             => 'TblSaleDay',
                        'code_field'        => 'day_code',
                        'code_prefix'       => strtoupper('PHO'),
                        'code_type_field'   => 'day_case_type',
                        'code_type'         => 'payment-handover'

                    ];
                    $code = Utilities::documentCode($doc_data);
                    //   $code = $this->documentCode(TblSaleDay::where('day_case_type','payment-handover')->where(Utilities::currentBCB())->max('day_code'),'PHO');
                }
                if($casetype == 'payment-received'){
                    $doc_data = [
                        'biz_type'          => 'branch',
                        'model'             => 'TblSaleDay',
                        'code_field'        => 'day_code',
                        'code_prefix'       => strtoupper('PREC'),
                        'code_type_field'   => 'day_case_type',
                        'code_type'         => 'payment-received'
                    ];
                    $code = Utilities::documentCode($doc_data);
                    //  $code = $this->documentCode(TblSaleDay::where('day_case_type','payment-received')->where(Utilities::currentBCB())->max('day_code'),'PREC');
                }
                if($casetype == 'day-closing'){
                    $doc_data = [
                        'biz_type'          => 'branch',
                        'model'             => 'TblSaleDay',
                        'code_field'        => 'day_code',
                        'code_prefix'       => strtoupper('DAYC'),
                        'code_type_field'   => 'day_case_type',
                        'code_type'         => 'day-closing'
                    ];
                    $code = Utilities::documentCode($doc_data);
                }
            }
            if(!isset($code) && ($casetype == 'payment-received' || $casetype == 'payment-handover' || $casetype == 'day-closing')){
                return $this->jsonErrorResponse($data, "Code not generate please try again", 422);
            }
            $form_id = $day_id;
            $day_code =  isset($code)?$code:'';
            $day_date_merg = date('Y-m-d', strtotime($request->day_date)); 
            $from_date_merg = $request->day_date.' '.$request->from_time;
            $day_date = date('Y-m-d H:i:s', strtotime($from_date_merg));
            $to_date_merg =  $request->to_date.' '.$request->to_time;
            $to_date = date('Y-m-d H:i:s', strtotime($to_date_merg));

            $shift_id = $request->day_shift;
            $saleman_id = $request->saleman_id;
            $payment_handover_received = $request->payment_handover_received;
            $payment_way_type = $request->payment_way_type;
            $reference_no = $request->reference_no;
            $document_type = isset($request->document_type)?$request->document_type:'';
            $notes = $request->notes;
            $terminal_id = $request->terminal_id;

            if($casetype == 'day-closing'){
                // check shift
                $exists = TblSaleDay::where('saleman_id',$saleman_id)->where('shift_id',$shift_id)->where('day_date',$day_date_merg);
                if(isset($id)){
                    $exists = $exists->where('day_id','!=',$form_id);
                }
                $exists = $exists->first();
                if($exists){
                    return $this->jsonErrorResponse($data,"Shift already closed",200);
                }
            }

            $lopCount = TblDefiDenomination::where('denomination_entry_status',1)->where(Utilities::currentBC())->count();
            $total_amount = 0;
            if(isset($request->dayDtl)){
                foreach ($request->dayDtl as $dtl){
                    $day = new TblSaleDay();
                    $day->day_id = $day_id;
                    $day->day_code = isset($day_code)?$day_code:'';
                    $day->day_case_type = $casetype;
                    $day->day_code_type = isset($document_type)?$document_type:'';
                    $day->day_date = $day_date;
                    $day->to_date = $to_date;
                    $day->shift_id = $shift_id;
                    $day->saleman_id = $saleman_id;
                    $day->day_payment_handover_received = isset($payment_handover_received)?$payment_handover_received:'';
                    $day->day_payment_way_type = isset($payment_way_type)?$payment_way_type:'';
                    $day->day_reference_no = $reference_no;
                    $day->day_notes = $notes;
                    $day->denomination_id = $dtl['denomination_id'];
                    $day->day_qty = $dtl['day_qty'];
                    $day->day_amount = $dtl['day_value'];
                    $day->business_id = auth()->user()->business_id;
                    $day->company_id = auth()->user()->company_id;
                    $day->branch_id = auth()->user()->branch_id;
                    $day->day_user_id = auth()->user()->id;
                    if(isset($created_at)){
                        $day->created_at = $created_at;
                    }
                    $day->terminal_id = $terminal_id;
                    $day->save();
                    $total_amount += $dtl['day_value'];
                }
            }

           // dd($request->pos);
            $sales_invoice_amount = 0;
            $discount_amount = 0;
            $return_discount_amount = 0;
            $return_sales_invoice_amount = 0;
            if(isset($request->pos)){
                $sr = 1;
                foreach ($request->pos as $pos){
                    if($pos['document_name'] == 'Sales Invoice'){
                        $sales_invoice_amount += $pos['amount'];
                        $discount_amount += $pos['discount'];
                    }
                    if($pos['document_name'] == 'Sales Return'){
                        $return_sales_invoice_amount += $pos['amount'];
                        $return_discount_amount += $pos['discount'];
                    }
                    $dayDtlCreate = [
                        'day_id' => $form_id,
                        'shift_id' => $shift_id,
                        'day_dtl_id' => Utilities::uuid(),
                        'day_date' => $day_date,
                        'to_date' => $to_date,
                        'day_case_type' => 'day_pos',
                        'saleman_id' => $saleman_id,
                        'sr_no' => $sr,
                        'document_name' => $pos['document_name'],
                        'no_of_documents' =>  $pos['total_doc'],
                        'total_amount' =>  $pos['amount'],
                        'total_discount' =>  $pos['discount'],
                        'notes' => $notes,
                    ];
                    if(isset($created_at)){
                        $dayDtlCreate['created_at'] = $created_at;
                    }
                    TblSaleDayDtl::create($dayDtlCreate);
                    $sr = $sr + 1;
                }
            }

            if(isset($request->pad)){
                $sr = 1;
                foreach ($request->pad as $pad){
                    $dayDtlCreate = [
                        'day_id' => $form_id,
                        'shift_id' => $shift_id,
                        'day_dtl_id' => Utilities::uuid(),
                        'day_date' => $day_date,
                        'to_date' => $to_date,
                        'day_case_type' => 'day_payment',
                        'saleman_id' => $saleman_id,
                        'sr_no' => $sr,
                        'payment_mode' => $pad['payment_mode'],
                        'opening_amount' =>  $pad['opening_amount'],
                        'in_flow' =>  $pad['in_flow'],
                        'out_flow' =>  $pad['out_flow'],
                        'payment_mode_balance' =>  $pad['balance_amount'],
                        'notes' => $notes,
                    ];
                    if(isset($created_at)){
                        $dayDtlCreate['created_at'] = $created_at;
                    }
                    TblSaleDayDtl::create($dayDtlCreate);
                    $sr = $sr + 1;
                }
            }
            $dayDtlCreate = [
                'day_id' => $form_id,
                'shift_id' => $shift_id,
                'day_dtl_id' => Utilities::uuid(),
                'day_date' => $day_date,
                'to_date' => $to_date,
                'day_case_type' => 'day_calc',
                'saleman_id' => $saleman_id,
                'sr_no' => 1,
                'cash_in_hand_per_system' => $request->cih_per_sys,
                'closing_cash' => $request->closing_cash,
                'cash_difference' => $request->diff_amount,
                'transfer_amount' => $request->trans_amount,
                'pos_opening_amount' => $request->opening_amount,
                'cash_transfer_status' => isset($request->cash_transfer_status)?1:0,
                'notes' => $notes,
            ];
            if(isset($created_at)){
                $dayDtlCreate['created_at'] = $created_at;
            }
            TblSaleDayDtl::create($dayDtlCreate);
            $valid = true;
            if($valid && $casetype == 'day-closing'){
                $sales_ids = TblSaleSales::where('branch_id',auth()->user()->branch_id)
                    ->where('sales_user_id',$saleman_id)
                    ->whereIn(DB::raw('lower(sales_type)'),['pos','rpos'])
                    ->whereBetween('created_at', [$day_date,$to_date])->update(['posted'=>1]);

                $cash_amount = 0;
                $dtls = TblSaleDayDtl::where('day_id',$form_id)->get();
                foreach ($dtls as $dtl){
                    if($dtl->payment_mode == 'Cash' && $dtl->day_case_type == 'day_payment'){
                        $cash_amount = $dtl->in_flow;
                    }
                }

                $date_filter = " and ( created_at between to_date('".$day_date."','yyyy/mm/dd HH24:MI:SS')";
                $date_filter .= " AND to_date('".$to_date."','yyyy/mm/dd HH24:MI:SS') )";

                $where = $date_filter;

                $where .= " and sales_sales_man = ".$saleman_id;
                $where .= " and business_id = ".auth()->user()->business_id;
                $where .= " and company_id = ".auth()->user()->company_id;
                $where .= " and branch_id = ".auth()->user()->branch_id;
                $fbr_qry = "select sum(abc.fbr_charges) fbr from(
                                select DISTINCT sales_id,fbr_charges
                                FROM  VW_SALE_SALES_INVOICE
                                WHERE SALES_TYPE = 'POS'
                                $where
                            ) abc";
                $fbr_data = \Illuminate\Support\Facades\DB::selectOne($fbr_qry);

                $fbr_charges = isset($fbr_data->fbr)?$fbr_data->fbr:0;



                $loyalty_point_qry = "select sum(abc.LOYALTY_AMOUNT) loyalty_amount from(
                    select DISTINCT sales_id,LOYALTY_AMOUNT
                    FROM  VW_SALE_SALES_INVOICE
                    WHERE SALES_TYPE = 'POS'
                    $where
                ) abc";
                $loyalty_point_data = \Illuminate\Support\Facades\DB::selectOne($loyalty_point_qry);

                $loyalty_amount = isset($loyalty_point_data->loyalty_amount)?$loyalty_point_data->loyalty_amount:0;


                $qry = "select abc.terminal_id,abc.terminal_name,abc.merchant_id,m.MERCHANT_NAME,count(SALES_ID)as no_of_documents,sum(amount)as amount ,sum(fbr_charges) as fbr_charges from (
                select DISTINCT
                BRANCH_ID,
                BRANCH_NAME,
                SALES_TYPE DOCUMENT_TYPE,
                SALES_ID,
                MERCHANT_ID,
                terminal_id,
                terminal_name,
                VISA_AMOUNT  amount,
                fbr_charges
                FROM  VW_SALE_SALES_INVOICE
                WHERE SALES_TYPE = 'POS' AND  NVL(VISA_AMOUNT,0) <> 0
                $where
                ) abc join tbl_defi_merchant m on m.MERCHANT_ID = abc.MERCHANT_ID group  by abc.MERCHANT_ID,m.MERCHANT_NAME,abc.terminal_id,abc.terminal_name";
                
                $merchants_data = \Illuminate\Support\Facades\DB::select($qry);
                $terminal = count($merchants_data) != 0 ? current($merchants_data):"";

                $terminal_chart_id = "";
                if(isset($terminal->terminal_id)){
                    $fetchTerminal = TblSoftPOSTerminal::where('terminal_id',$terminal->terminal_id)->first();
                    if(isset($fetchTerminal->chart_id)){
                        $terminal_chart_id = $fetchTerminal->chart_id;
                    }
                }

                $qry = "select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE,   DOCUMENT_NAME, DOCUMENT_TYPE, sum(SALES_DTL_AMOUNT)  AMOUNT, count(distinct SALES_ID) no_of_documents, sum(gst_amount) gst_amount
                    from (
                     select DISTINCT    BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Invoice' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, SALES_ID, sum(SALES_DTL_AMOUNT ) SALES_DTL_AMOUNT,   sum(nvl(SALES_DTL_VAT_AMOUNT,0)) gst_amount
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'POS'
                    $where
                     group by  BRANCH_ID, BRANCH_NAME, SALES_DATE, SALES_TYPE , SALES_ID
                    UNION ALL
                    select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Return' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, SALES_ID,  sum(ABS(SALES_DTL_AMOUNT)) * -1     SALES_DTL_AMOUNT,  sum(nvl(SALES_DTL_VAT_AMOUNT,0)) * -1 gst_amount
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'RPOS'
                     $where
                       group by  BRANCH_ID, BRANCH_NAME, SALES_DATE, SALES_TYPE , SALES_ID
                    ) gaga group by BRANCH_ID, BRANCH_NAME, SALES_DATE, DOCUMENT_NAME, DOCUMENT_TYPE order by  SALES_DATE , DOCUMENT_TYPE";

                $gst_tax_sum = \Illuminate\Support\Facades\DB::select($qry);

                $qry2 = "select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE,   DOCUMENT_NAME, DOCUMENT_TYPE, SUM(SALES_DTL_AMOUNT) -  SUM(SALES_DTL_VAT_AMOUNT) SALE_AMOUNT
                    from (
                     select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Invoice' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, sum(SALES_DTL_AMOUNT ) SALES_DTL_AMOUNT,   sum(nvl(SALES_DTL_VAT_AMOUNT,0)) SALES_DTL_VAT_AMOUNT
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'POS'
                    $where
                     group by  BRANCH_ID, BRANCH_NAME, SALES_DATE,SALES_TYPE
                    UNION ALL
                    select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Return' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, sum(ABS(SALES_DTL_AMOUNT)) * -1     SALES_DTL_AMOUNT,  sum(nvl(SALES_DTL_VAT_AMOUNT,0)) * -1 SALES_DTL_VAT_AMOUNT
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'RPOS'
                     $where
                       group by  BRANCH_ID, BRANCH_NAME, SALES_DATE,SALES_TYPE
                    ) gaga group by BRANCH_ID, BRANCH_NAME, SALES_DATE, DOCUMENT_NAME, DOCUMENT_TYPE order by  SALES_DATE , DOCUMENT_TYPE";

                $sale_amount_data = \Illuminate\Support\Facades\DB::select($qry2);
                

                $sale_amount = 0;
                $return_sale_amount = 0;
                foreach ($sale_amount_data as $row) {
                    if ($row->document_name == 'Sales Invoice') {
                        $sale_amount += $row->sale_amount;
                    }
                    if ($row->document_name == 'Sales Return') {
                        $return_sale_amount += $row->sale_amount;
                    }
                }
                
                $table_name = 'tbl_acco_voucher';
                if(isset($id)){
                    $action = 'update';
                    $day_id = $id;
                    $entry_type = 'edit';
                }else{
                    $entry_type = 'new';
                    $action = 'add';
                }
                //voucher start
                $data = [
                    'voucher_document_id'   =>  $day_id,
                    'voucher_no'            =>  $day_code,
                    'voucher_date'          =>  date('Y-m-d', strtotime($day_date)),
                    'branch_id'             =>  auth()->user()->branch_id,
                    'business_id'           =>  auth()->user()->business_id,
                    'company_id'            =>  auth()->user()->company_id,
                    'voucher_user_id'       =>  auth()->user()->id,
                    'saleman_id'            =>  $saleman_id,
                ];
                /* CCD - Closing Cash Deposit */
                $vouch_ccd = true;
                if($vouch_ccd){
                    $type = 'ccd';
                    $voucher_id1 = Utilities::uuid();
                    $created_at = Carbon::now();
                    $update_at = $created_at;
                    $max_voucher = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where(Utilities::currentBCB())->max('voucher_no');
                    $voucher_no = $this->documentCode($max_voucher,$type);
                    if($entry_type == 'edit'){
                        $get_ccd_vouch = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->first();
                        TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->delete();
                        if(isset($get_ccd_vouch->voucher_id) && !empty($get_ccd_vouch)){
                            $voucher_id1 = $get_ccd_vouch->voucher_id;
                            $created_at = $get_ccd_vouch->created_at;
                            $voucher_no = $get_ccd_vouch->voucher_no;
                            $update_at = Carbon::now();
                        }
                    }

                    $data['voucher_id'] = $voucher_id1;
                    $data['voucher_no'] = $voucher_no;
                    $data['voucher_type'] = $type;
                    $data['created_at'] = $created_at;
                    $data['updated_at'] = $update_at;
                    $voucher_sr_no = 1;
                    $cih_acc_code = "6-01-05-0001"; // Cash In Hand
                    $cih_acc = TblAccCoa::where('chart_code',$cih_acc_code)->first();
                    if(isset($cih_acc->chart_account_id)){
                        $data['chart_account_id'] = $cih_acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($request->closing_cash));
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Cash in Hand';
                        DB::table($table_name)->insert($data);
                    }
                    $action = 'add';
                    if(isset($terminal_chart_id)){
                        $data['chart_account_id'] = $terminal_chart_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($request->closing_cash));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Point of Sale Cash';
                        DB::table($table_name)->insert($data);
                    }
                }
                /* JV - Difference amount voucher*/
                $vouch_jv = true;
                if($vouch_jv){
                    $type = 'jv';
                    $voucher_id2 = Utilities::uuid();
                    $created_at = Carbon::now();
                    $update_at = $created_at;
                    $max_voucher = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where(Utilities::currentBCB())->max('voucher_no');
                    $voucher_no = $this->documentCode($max_voucher,$type);
                    if($entry_type == 'edit'){
                        $get_ccd_vouch = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->first();
                        TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->delete();
                        if(isset($get_ccd_vouch->voucher_id) && !empty($get_ccd_vouch)){
                            $voucher_id2 = $get_ccd_vouch->voucher_id;
                            $created_at = $get_ccd_vouch->created_at;
                            $voucher_no = $get_ccd_vouch->voucher_no;
                            $update_at = Carbon::now();
                        }
                    }

                    $data['voucher_id'] = $voucher_id2;
                    $data['voucher_no'] = $voucher_no;
                    $data['voucher_type'] = $type;
                    $data['created_at'] = $created_at;
                    $data['updated_at'] = $update_at;
                    $voucher_sr_no = 1;
                    $diff = $request->cih_per_sys - $request->closing_cash;
                    if($diff > 0){
                        $increase_amount = $diff;
                        $decrease_amount = 0;
                    }else{
                        $increase_amount = 0;
                        $decrease_amount = $diff;
                    }
                    $pos_acc_code = "6-01-02-0001"; // 	RISEN-FG-POS-1
                    $pos_acc = TblAccCoa::where('chart_code',$pos_acc_code)->first();
                    if(isset($pos_acc->chart_account_id)){
                        $data['chart_account_id'] = $terminal_chart_id;//$pos_acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($decrease_amount));
                        $data['voucher_credit'] = abs($this->addNo($increase_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = $pos_acc->chart_name;
                        DB::table($table_name)->insert($data);
                    }
                    $pos_acc_code = "9-02-03-0001"; // 	SHORT & EXCESS AMOUNT ON SALE
                    $pos_acc = TblAccCoa::where('chart_code',$pos_acc_code)->first();
                    if(isset($pos_acc->chart_account_id)){
                        $data['chart_account_id'] = $pos_acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($increase_amount));
                        $data['voucher_credit'] = abs($this->addNo($decrease_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = $pos_acc->chart_name;
                        DB::table($table_name)->insert($data);
                    }
                }

                /* SRV - Sale Invoice Voucher */
                $vouch_siv = true;
                if($vouch_siv) {
                    $type = 'siv';
                    $voucher_id3 = Utilities::uuid();
                    $created_at = Carbon::now();
                    $update_at = $created_at;
                    $max_voucher = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where(Utilities::currentBCB())->max('voucher_no');
                    $voucher_no = $this->documentCode($max_voucher,$type);
                    if($entry_type == 'edit'){
                        $get_ccd_vouch = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->first();
                        TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->delete();
                        if(isset($get_ccd_vouch->voucher_id) && !empty($get_ccd_vouch)){
                            $voucher_id3 = $get_ccd_vouch->voucher_id;
                            $created_at = $get_ccd_vouch->created_at;
                            $voucher_no = $get_ccd_vouch->voucher_no;
                            $update_at = Carbon::now();
                        }
                    }

                    $data['voucher_id'] = $voucher_id3;
                    $data['voucher_no'] = $voucher_no;
                    $data['voucher_type'] = $type;
                    $data['created_at'] = $created_at;
                    $data['updated_at'] = $update_at;
                    /*debit Side*/
                    $voucher_sr_no = 1;
                    if(!empty($terminal_chart_id)){
                        $data['chart_account_id'] = $terminal_chart_id;
                        $data['voucher_debit'] = abs($this->addNo($cash_amount));
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Closing Cash';
                        DB::table($table_name)->insert($data);
                    }
                    $merchant_total_amount = 0;
                    foreach($merchants_data as $merchant_row){
                        $merchant = TblDefiMerchant::where('merchant_id',$merchant_row->merchant_id)->first();
                        if(isset($merchant->merchant_account_id)){
                            $merchant_total_amount += $merchant_row->amount;
                            $data['chart_account_id'] = $merchant->merchant_account_id;
                            $data['voucher_debit'] = abs($this->addNo($merchant_row->amount));
                            $data['voucher_credit'] = 0;
                            $data['voucher_sr_no'] = $voucher_sr_no++;
                            $data['voucher_descrip'] = 'for Credit Card Merchant';
                            DB::table($table_name)->insert($data);
                        }

                    }

                    $disc_acc_code = "9-02-02-0001";
                    $acc = TblAccCoa::where('chart_code',$disc_acc_code)->first();
                    if(isset($acc->chart_account_id)){
                        $data['chart_account_id'] = $acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($discount_amount));
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Sale Discount';
                        DB::table($table_name)->insert($data);
                    }

                    $reward_acc_code = "9-02-02-0002";
                    $reward_acc = TblAccCoa::where('chart_code',$reward_acc_code)->first();
                    $reward_amount = $loyalty_amount;
                    if(isset($reward_acc->chart_account_id)){
                        $data['chart_account_id'] = $reward_acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($reward_amount));
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Reward Point';
                        DB::table($table_name)->insert($data);
                    }

                    /*credit Side*/
                    $fbr_acc_code = "3-01-05-0008";
                    $fbr_acc = TblAccCoa::where('chart_code', $fbr_acc_code)->first();
                    if (isset($fbr_acc->chart_account_id)) {
                        $data['chart_account_id'] = $fbr_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($fbr_charges));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'FBR Service Charges Rs1';
                        DB::table($table_name)->insert($data);
                    }

                    $reward_lib_acc_code = "3-01-02-0002";
                    $reward_lib_acc = TblAccCoa::where('chart_code', $reward_lib_acc_code)->first();
                    $reward_lib_amount = 0;
                    if (isset($reward_lib_acc->chart_account_id)) {
                        $data['chart_account_id'] = $reward_lib_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($reward_lib_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Reward Point Lib';
                        DB::table($table_name)->insert($data);
                    }

                    $gst_acc_code = "3-01-05-0003";
                    $gst_acc = TblAccCoa::where('chart_code', $gst_acc_code)->first();
                    $gst_amount = 0;
                    $return_gst_amount = 0;
                    $return_amount = 0;
                    foreach ($gst_tax_sum as $gst_tax) {
                        if ($gst_tax->document_name == 'Sales Invoice') {
                            $gst_amount += $gst_tax->gst_amount;
                        }
                        if ($gst_tax->document_name == 'Sales Return') {
                            $return_gst_amount += $gst_tax->gst_amount;
                            $return_amount += $gst_tax->amount;
                        }
                    }
                    if (isset($gst_acc->chart_account_id)) {
                        $data['chart_account_id'] = $gst_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($gst_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'GST on Sale';
                        DB::table($table_name)->insert($data);
                    }

                    $roa_acc_code = "7-01-01-0003";
                    $roa_acc = TblAccCoa::where('chart_code', $roa_acc_code)->first();
                    $retail_amount = $sale_amount; //$sales_invoice_amount - $gst_amount
                    $debit_side = $cash_amount + $merchant_total_amount + $discount_amount + $reward_amount;
                    $credit_side = $fbr_charges + $reward_lib_amount + $gst_amount + $retail_amount;
                    $roa_amount = $debit_side - $credit_side;
                    if (isset($roa_acc->chart_account_id)) {
                        $data['chart_account_id'] = $roa_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($roa_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'round of Amount';
                        DB::table($table_name)->insert($data);
                    }

                    $retail_acc_code = "7-01-01-0001";
                    $retail_acc = TblAccCoa::where('chart_code', $retail_acc_code)->first();
                    if (isset($retail_acc->chart_account_id)) {
                        $data['chart_account_id'] = $retail_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($sale_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Retail Sale';
                        DB::table($table_name)->insert($data);
                    }
                }

                /* SRV - Sale Return Voucher */
                $vouch_srv = true;
                if($vouch_srv){
                    $type = 'srv';
                    $voucher_id4 = Utilities::uuid();
                    $created_at = Carbon::now();
                    $update_at = $created_at;
                    $max_voucher = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where(Utilities::currentBCB())->max('voucher_no');
                    $voucher_no = $this->documentCode($max_voucher,$type);
                    if($entry_type == 'edit'){
                        $get_ccd_vouch = TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->first();
                        TblAccoVoucher::where(DB::raw('lower(voucher_type)'),$type)->where('voucher_document_id',$id)->delete();
                        if(isset($get_ccd_vouch->voucher_id) && !empty($get_ccd_vouch)){
                            $voucher_id4 = $get_ccd_vouch->voucher_id;
                            $created_at = $get_ccd_vouch->created_at;
                            $voucher_no = $get_ccd_vouch->voucher_no;
                            $update_at = Carbon::now();
                        }
                    }

                    $data['voucher_id'] = $voucher_id4;
                    $data['voucher_no'] = $voucher_no;
                    $data['voucher_type'] = $type;
                    $data['created_at'] = $created_at;
                    $data['updated_at'] = $update_at;
                    $voucher_sr_no = 1;
                    $cih_acc_code = "3-01-02-0002"; // Customer Reward Point Lib
                    $cih_acc = TblAccCoa::where('chart_code',$cih_acc_code)->first();
                    if(isset($cih_acc->chart_account_id)){
                        $data['chart_account_id'] = $cih_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Reward Point Lib';
                        DB::table($table_name)->insert($data);
                    }
                    $action = 'add';
                    $pos_acc_code = "3-01-05-0003"; // 	GST on Sale
                    $pos_acc = TblAccCoa::where('chart_code',$pos_acc_code)->first();
                    if(isset($pos_acc->chart_account_id)){
                        $data['chart_account_id'] = $pos_acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($return_gst_amount));
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = $pos_acc->chart_name;
                        DB::table($table_name)->insert($data);
                    }
                    $pos_acc_code = "7-01-01-0001"; // 	RETAIL SALE
                    $pos_acc = TblAccCoa::where('chart_code',$pos_acc_code)->first();
                    $srv_retail_sale = abs($return_amount) - abs($return_gst_amount);
                    if(isset($pos_acc->chart_account_id)){
                        $data['chart_account_id'] = $pos_acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($srv_retail_sale));
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = $pos_acc->chart_name;
                        DB::table($table_name)->insert($data);
                    }
                    $pos_acc_code = "9-02-02-0002"; // 	Reward Point Discount
                    $pos_acc = TblAccCoa::where('chart_code',$pos_acc_code)->first();
                    if(isset($pos_acc->chart_account_id)){
                        $data['chart_account_id'] = $pos_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = 0;
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = $pos_acc->chart_name;
                        DB::table($table_name)->insert($data);
                    }
                    $pos_acc_code = "9-02-02-0001"; // 	SALE DISCOUNT
                    $pos_acc = TblAccCoa::where('chart_code',$pos_acc_code)->first();
                    if(isset($pos_acc->chart_account_id)){
                        $data['chart_account_id'] = $pos_acc->chart_account_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($return_discount_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = $pos_acc->chart_name;
                        DB::table($table_name)->insert($data);
                    }
                    $return_retail_amount = abs($return_sales_invoice_amount) ;//- $return_gst_amount;
                    if(!empty($terminal_chart_id)){
                        $data['chart_account_id'] = $terminal_chart_id;
                        $data['voucher_debit'] = 0;
                        $data['voucher_credit'] = abs($this->addNo($return_retail_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'Point of Sale Cash';
                        DB::table($table_name)->insert($data);
                    }
                    $srv_debit =  0 + abs($return_gst_amount) + abs($srv_retail_sale);
                    $srv_credit = 0 + abs($return_discount_amount) + abs($return_retail_amount);
                    $diff = $srv_debit - $srv_credit;
                    if($diff > 0){
                        $increase_amount = $diff;
                        $decrease_amount = 0;
                    }else{
                        $increase_amount = 0;
                        $decrease_amount = $diff;
                    }
                    $pos_acc_code = "9-02-03-0001"; // 	SHORT & EXCESS AMOUNT ON SALE
                    $pos_acc = TblAccCoa::where('chart_code',$pos_acc_code)->first();
                    if(isset($pos_acc->chart_account_id)){
                        $data['chart_account_id'] = $pos_acc->chart_account_id;
                        $data['voucher_debit'] = abs($this->addNo($decrease_amount));
                        $data['voucher_credit'] = abs($this->addNo($increase_amount));
                        $data['voucher_sr_no'] = $voucher_sr_no++;
                        $data['voucher_descrip'] = 'ROUND OFF';
                        DB::table($table_name)->insert($data);
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
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'day/'.$casetype;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/day/'.$casetype.$this->prefixCreatePage.'/'.$form_id;
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

    public function print($type,$id)
    {
        $data['type'] = $type;
        if($type == 'payment-handover'){
            $data['title'] = 'Payment Handover';
            $data['stock_menu_id'] = '72';
        }
        if($type == 'payment-received'){
            $data['title'] = 'Payment Received';
            $data['stock_menu_id'] = '73';
        }
        if($type == 'day-opening'){
            $data['title'] = 'Opening Day';
            $data['stock_menu_id'] = '63';
        }
        if($type == 'day-closing'){
            $data['title'] = 'Closing Day';
            $data['stock_menu_id'] = '64';
        }
        if(isset($id)){
            if(TblSaleDay::where('day_id','LIKE',$id)->exists()){
                $data['permission'] = $data['stock_menu_id'].'-print';
                $data['current'] = TblSaleDay::with('dtl')->where('day_id',$id)->where('day_case_type',$type)->where(Utilities::currentBCB())->first();
                // dd($data['current']->toArray());
                $data['dtl'] = ViewSaleDay::select('denomination_name','day_qty','day_amount')->where('day_id',$id)->where('day_case_type',$type)->where(Utilities::currentBCB())->orderby('denomination_id')->get();
            }else{
                abort('404');
            }
        }
        if(empty($data['current'])){
            abort('404');
        }
        $data['users'] = User::where('user_entry_status',1)->where(Utilities::currentBC())->where('id',$data['current']->saleman_id)->first();
        $data['payment_person'] = User::where('user_entry_status',1)->where(Utilities::currentBC())->where('id',$data['current']->day_payment_handover_received)->first();
        $data['payment_type'] = TblAccoPaymentType::where('payment_type_entry_status',1)->where('payment_type_id',$data['current']->day_payment_way_type)->where(Utilities::currentBC())->first();
        $data['shift'] = TblDefiShift::where(Utilities::currentBC())->where('shift_id',$data['current']->shift_id)->first();
        if ($type == 'day-closing') {
            return view('prints.sale.day_closing_print',compact('data'));
        }else{
            return view('prints.sale_day_payment',compact('data'));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($casetype,$id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $del_day_dtl = TblSaleDayDtl::where('day_id',$id)->where('day_case_type',$casetype)->get();
            foreach ($del_day_dtl as $del_day_dtl){
                TblSaleDayDtl::where('day_id',$del_day_dtl->day_id)->where(Utilities::currentBCB())->delete();
            }
            $del_dtls = TblSaleDay::where('day_id',$id)->where('day_case_type',$casetype)->where(Utilities::currentBCB())->get();
            foreach ($del_dtls as $del_dtl){
                TblSaleDay::where('day_id',$del_dtl->day_id)->where(Utilities::currentBCB())->delete();
                TblAccoVoucher::where('voucher_document_id',$del_dtl->day_id)->where(Utilities::currentBCB())->delete();
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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }

    public function getPosShiftData(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'day_date' => 'required',
            'to_date' => 'required',
            'salesman_id' => 'required',
            'day_shift' => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try{
            $date_filter = " and ( created_at between ";
            if(isset($request->from_time)){
                $date_filter .= " to_date('".date('Y-m-d',strtotime($request->day_date)).' '.$request->from_time."','yyyy/mm/dd HH:MI AM')";
            }else{
                $date_filter .= " to_date('".date('Y-m-d',strtotime($request->day_date))."','yyyy/mm/dd')";
            }

            if(isset($request->to_time)){
                $date_filter .= " AND to_date('".date('Y-m-d',strtotime($request->to_date)).' '.$request->to_time."','yyyy/mm/dd HH:MI AM') )";
            }else{
                $date_filter .= " AND to_date('".date('Y-m-d',strtotime($request->to_date))."','yyyy/mm/dd') ) ";
            }

            $where = $date_filter;
            //  $where .= " and shift_id = ".$request->day_shift;
            $where .= " and sales_sales_man = ".$request->salesman_id;
            $where .= " and business_id = ".auth()->user()->business_id;
            $where .= " and company_id = ".auth()->user()->company_id;
            $where .= " and branch_id = ".auth()->user()->branch_id;


            $ipv_date_filter = " and ( VOUCHER_DATE between to_date('".date('Y-m-d',strtotime($request->day_date))."','yyyy/mm/dd')";
            $ipv_date_filter .= " AND to_date('".date('Y-m-d',strtotime($request->day_date))."','yyyy/mm/dd') ) ";
            $ipv_where = $ipv_date_filter;
            $ipv_where .= " and saleman_id = ".$request->salesman_id;
            $ipv_where .= " and business_id = ".auth()->user()->business_id;
            $ipv_where .= " and company_id = ".auth()->user()->company_id;
            $ipv_where .= " and branch_id = ".auth()->user()->branch_id;

            $qry = "select DISTINCT BRANCH_ID, BRANCH_NAME,   DOCUMENT_NAME,TERMINAL_ID,TERMINAL_NAME, DOCUMENT_TYPE, sum(SALES_NET_AMOUNT)+  SUM(FBR_CHARGES)   AMOUNT,sum(DISCOUNT) DISCOUNT, count(distinct SALES_ID) total_doc
                    from (
                     select DISTINCT    BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Invoice' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, SALES_ID,TERMINAL_ID,TERMINAL_NAME, sum(SALES_DTL_TOTAL_AMOUNT ) SALES_NET_AMOUNT,sum(nvl(SALES_DTL_DISC_AMOUNT,0))    DISCOUNT , MAX(FBR_CHARGES) FBR_CHARGES
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'POS'
                     $where
                     group by  BRANCH_ID, BRANCH_NAME, SALES_DATE, SALES_TYPE , SALES_ID,TERMINAL_ID,TERMINAL_NAME
                    UNION ALL
                    select DISTINCT BRANCH_ID, BRANCH_NAME, SALES_DATE, 'Sales Return' DOCUMENT_NAME, SALES_TYPE DOCUMENT_TYPE, SALES_ID,TERMINAL_ID,TERMINAL_NAME,  sum(ABS(SALES_DTL_TOTAL_AMOUNT)) * -1     SALES_NET_AMOUNT, sum(nvl(ABS(SALES_DTL_DISC_AMOUNT),0)) * -1  DISCOUNT , MAX(ABS(FBR_CHARGES)) * -1 FBR_CHARGES
                    FROM  VW_SALE_SALES_INVOICE WHERE SALES_TYPE = 'RPOS'
                      $where
                       group by  BRANCH_ID, BRANCH_NAME, SALES_DATE, SALES_TYPE , SALES_ID,TERMINAL_ID,TERMINAL_NAME
                    ) gaga group by BRANCH_ID, BRANCH_NAME, DOCUMENT_NAME,TERMINAL_ID, TERMINAL_NAME,DOCUMENT_TYPE order by DOCUMENT_TYPE";
              //dd($qry);
            $data['pos_activity'] = DB::select($qry);

            $pqry = "select DOCUMENT_NAME, ROUND(SUM(OPENING_AMOUNT),0) OPENING_AMOUNT,
                        ROUND(SUM(IN_FLOW),0) IN_FLOW, ROUND(SUM(OUT_FLOW),0) OUT_FLOW,
                        ROUND(SUM(OPENING_AMOUNT),0) + ROUND(SUM(IN_FLOW),0) - ROUND(SUM(OUT_FLOW),0) BALANCE_AMOUNT
                        from (
                            select DISTINCT
                            BRANCH_ID,
                            BRANCH_NAME,
                            1 SORTING_ID ,
                            'Cash' DOCUMENT_NAME,
                            SALES_TYPE DOCUMENT_TYPE,
                            SALES_ID,
                            0 OPENING_AMOUNT ,
                            CASH_AMOUNT   IN_FLOW ,
                            0    OUT_FLOW
                            FROM  VW_SALE_SALES_INVOICE
                            WHERE SALES_TYPE = 'POS'  AND  NVL(CASH_AMOUNT,0) <> 0
                            $where
                            UNION ALL
                            select DISTINCT
                            BRANCH_ID,
                            BRANCH_NAME,
                            1 SORTING_ID ,
                            'Cash' DOCUMENT_NAME,
                            SALES_TYPE DOCUMENT_TYPE,
                            SALES_ID,
                            0              OPENING_AMOUNT ,
                            0              IN_FLOW ,
                            CASH_AMOUNT * -1   OUT_FLOW
                            FROM  VW_SALE_SALES_INVOICE
                            WHERE SALES_TYPE = 'RPOS' AND  NVL(CASH_AMOUNT,0) <> 0
                            $where
                            UNION ALL
                            select DISTINCT
                            BRANCH_ID,
                            BRANCH_NAME,
                            2 SORTING_ID ,
                            'Credit Card' DOCUMENT_NAME,
                            SALES_TYPE DOCUMENT_TYPE,
                            SALES_ID,
                            0              OPENING_AMOUNT ,
                            VISA_AMOUNT              IN_FLOW ,
                            0   OUT_FLOW
                            FROM  VW_SALE_SALES_INVOICE
                            WHERE SALES_TYPE = 'POS' AND  NVL(VISA_AMOUNT,0) <> 0
                            $where
                            UNION ALL
                            select DISTINCT
                            BRANCH_ID,
                            BRANCH_NAME,
                            3 SORTING_ID ,
                            'Internal Voucher' DOCUMENT_NAME,
                            VOUCHER_TYPE DOCUMENT_TYPE,
                            VOUCHER_ID SALES_ID,
                            0 OPENING_AMOUNT ,
                            0 IN_FLOW ,
                            VOUCHER_CREDIT OUT_FLOW
                            FROM  VW_ACCO_VOUCHER
                            WHERE lower(VOUCHER_TYPE) = 'ipv' AND NVL(VOUCHER_CREDIT,0) <> 0
                            $ipv_where
                         ) gaga group by
                            DOCUMENT_NAME , SORTING_ID
                            order by SORTING_ID";

            $data['payment_dtl'] = DB::select($pqry);
           // dd($pqry);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, 'Data loaded..', 200);
    }
}
