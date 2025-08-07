<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccoVoucher;
use App\Models\TblAccCoa;
use App\Models\TblAccoVoucherBillDtl;
use App\Models\TblDefiBank;
use App\Models\TblDefiCurrency;
use App\Models\Permission;
use App\Models\TblDefiWHT;
use App\Models\User;
use App\Models\ViewPurcGrnPayments;
use App\Models\TblSoftPOSTerminal;
use App\Models\ViewPOSSoftTerminal;
use App\Models\ViewSaleSalesInvoice;
use App\Models\Rent\TblRentAgreementDtl;
use App\Models\TblSoftBranch;

use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpParser\Node\Stmt\Else_;
use TheUmar98\BarcodeBundle\Utils\QrCode;








class VoucherController extends Controller
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
    public function setSessionVoucherNo(Request $request)
    {
        $voucher = TblAccoVoucher::where('voucher_no','LIKE',$request->voucher_no)
            ->where('voucher_type','LIKE',$request->voucher_type)
            ->where(Utilities::currentBCB())->first();
        if(!empty($voucher)){
            $voucher_id = $voucher->voucher_id;
            $voucher_no = $voucher->voucher_no;
            $key_id = $request->voucher_type;
            session([$key_id => ['id'=>$voucher_id,'no'=>$voucher_no]]);
            return $this->jsonSuccessResponse([], '', 200);
        }else{
            return $this->jsonErrorResponse([], 'Voucher No not Correct', 200);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $type,$id = null)
    {
        $chart_cash_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->cash_group)->where(Utilities::currentBC())->first('chart_code');
        $chart_bank_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->bank_group)->where(Utilities::currentBC())->first('chart_code');
        $cash_group = substr($chart_cash_group->chart_code,0,7);
        $bank_group = substr($chart_bank_group->chart_code,0,7);

        $copy_entry = false;
        if(!isset($id)){
            if(!empty(session($type))){
                $id = session($type)['id'];
                $v_no = session($type)['no'];
                $copy_entry = true;
                $data['last_voucher_no'] = $v_no;
            }
        }

        $data['page_data'] = [];
        $data['page_data']['path_index'] = $this->prefixIndexPage.'accounts/'.$type;
        $data['page_data']['create'] = '/accounts/'.$type.$this->prefixCreatePage;
        $data['type'] = $type;
        switch ($type){
            case 'jv': {
                $data['page_data']['title'] = 'Journal Voucher';
                $formUrl = 'jv';
                $data['stock_menu_id'] = '31';
                break;
            }
            case 'obv': {
                $data['page_data']['title'] = 'Opening Balance Voucher';
                $formUrl = 'jv';
                $data['stock_menu_id'] = '62';
                break;
            }
            case 'crv': {
                $data['page_data']['title'] = 'Cash Withdrawal Voucher';
                $formUrl = 'cash_voucher';
                $data['stock_menu_id'] = '28';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                break;
            }
            case 'cpv': {
                $data['page_data']['title'] = 'Cash Payment';
                $formUrl = 'cash_voucher';
                $data['stock_menu_id'] = '37';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $bank_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                break;
            }
            case 'brv': {
                $data['page_data']['title'] = 'Bank Received Voucher';
                $formUrl = 'bank_voucher';
                $data['stock_menu_id'] = '29';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $bank_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'bpv': {
                $data['page_data']['title'] = 'Bank Payment Voucher';
                $formUrl = 'bank_voucher';
                $data['stock_menu_id'] = '36';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $bank_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'lv': {
                $data['page_data']['title'] = 'Liability Voucher';
                $formUrl = 'lv';
                $data['stock_menu_id'] = '138';
                break;
            }
            case 'lfv': {
                $data['page_data']['title'] = 'Listing Fee Voucher';
                $formUrl = 'cash_voucher';
                $data['stock_menu_id'] = '171';
                //$data['stock_menu_id'] = '148';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'brpv': {
                $data['page_data']['title'] = 'Branch Payment Voucher';
                $formUrl = 'branch_voucher';
                $data['stock_menu_id'] = '269';
                $data['acc_code'] = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', "3-01-03-%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                break;
            }
            case 'brrv': {
                $data['page_data']['title'] = 'Branch Receive Voucher';
                $formUrl = 'branch_receive';
                $data['stock_menu_id'] = '274';
                $data['wht'] = TblDefiWHT::where('wht_type_id','<>',0)->get();
                $data['bank'] = TblDefiBank::where('bank_id','<>',0)->orderby('bank_name')->get();
                $receiving_branch = auth()->user()->branch_id;
                $existing_value = DB::table('tbl_acco_voucher_bill_dtl')->where('voucher_bill_type','brrv')->pluck('voucher_document_id');
                $data['branch_payment_list'] = DB::table('VW_ACCO_VOUCHER_BRANCH_PAYMENT_BALANCE')->where('PAYMENT_BRANCH_ID', $receiving_branch)->where('BALANCE_AMOUNT', '>' ,0)
                ->whereNotIn('VOUCHER_ID', $existing_value)->orderBy('BRANCH_NAME' , 'asc')->orderBy('VOUCHER_NO','asc')->get();

                // $sql = "SELECT * FROM VW_ACCO_VOUCHER_BRANCH_PAYMENT_BALANCE
                // where PAYMENT_BRANCH_ID = $receiving_branch and BALANCE_AMOUNT > 0
                // order by BRANCH_NAME , VOUCHER_NO ";
                //$data['branch_payment_list'] = DB::select($sql);
               //dd($existing_value);

                $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                break;
            }
            case 'pv': {
                $data['page_data']['title'] = 'Vendor Payment Voucher';
                $formUrl = 'vendor_payment_voucher';
                $data['stock_menu_id'] = '270';
                $data['acc_code'] = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', "3-01-03-%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('sr_no')->get();
                $data['bank'] = TblDefiBank::where('bank_id','<>',0)->orderby('bank_name')->get();
                break;
            }
            case 'ipv': {
                $data['page_data']['title'] = 'Internal Payment Voucher';
                $formUrl = 'ipv';
                $data['stock_menu_id'] = '271';
                $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                //$data['acc_code'] = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $bank_group."%")->orWhere('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', '6-01-02'."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'irv': {
                $data['page_data']['title'] = 'Internal Receive Voucher';
                $formUrl = 'irv';
                $data['stock_menu_id'] = '272';
                $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                $data['acc_code'] = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $bank_group."%")->orWhere('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'pve': {
                $data['page_data']['title'] = 'Expense Voucher';
                $formUrl = 'pve';
                $data['stock_menu_id'] = '273';
                $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                $data['deduction'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_grid_type','deduction')->where(Utilities::currentBCB())->get();
                break;
            }
            case 'rv': {
                $data['page_data']['title'] = 'Receiving Voucher';
                $formUrl = 'receiving_voucher';
                $data['stock_menu_id'] = '286';
                $data['acc_code'] = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', "3-01-03-%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('sr_no')->get();
                $data['bank'] = TblDefiBank::where('bank_id','<>',0)->orderby('bank_name')->get();
                break;
            }
        }
        if(isset($id)){
            $data['rec_type'] =  TblAccoVoucher::where('voucher_type',$type)->where('voucher_id',$id)->where(Utilities::currentBCB())->first();
            if($data['rec_type']->bank_rec_posted == 1)
            {
                Session::flash('msg', 'BRS Posted');
            }

            if(TblAccoVoucher::where('voucher_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = $data['stock_menu_id'].'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_sr_no','=','1')->where(Utilities::currentBCB())->first();
                if($type == 'crv' || $type =='brv' || $type =='lfv' || $type == 'brpv' || $type == 'irv'){
                    //Voucher Credit
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_credit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                    $data['deduction'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','deduction')->where(Utilities::currentBCB())->get();
                }else if($type == 'cpv' || $type =='bpv' || $type == 'ipv'){
                    //Voucher Debit
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_sr_no','!=','1')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else if($type == 'pv'){
                    //Voucher Debit
                    $data['current'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','vendor')->where(Utilities::currentBCB())->first();
                    $data['current_purchase'] = TblAccoVoucherBillDtl::where('voucher_id',$id)->where('voucher_type','=','GRN')->where(Utilities::currentBCB())->get();
                    $data['current_purchase_return'] = TblAccoVoucherBillDtl::where('voucher_id',$id)->where('voucher_type','=','PR')->where(Utilities::currentBCB())->get();
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->get();
                    $data['deduction'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','deduction')->where(Utilities::currentBCB())->get();
                    //dd($data['deduction']->toArray());
                }else if($type == 'rv'){
                    //Voucher Debit
                    $data['current'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','vendor')->where(Utilities::currentBCB())->first();
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->get();
                    $data['deduction'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','deduction')->where(Utilities::currentBCB())->get();
                }else if($type == 'brrv'){
                    //Voucher credit
                    $data['current'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->first();
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->get();
                }else if($type == 'pve'){
                    $data['debit'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_grid_type','debit')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                    $data['credit'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_grid_type','credit')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                    $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                    $data['deduction'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','deduction')->where(Utilities::currentBCB())->get();
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }
                else{
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                    $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->orderby('payment_term_name')->get();
                }
                $data['voucher_no'] = $data['current']->voucher_no;
                $data['page_data']['print'] = '/accounts/'.$type.'/print/'.$id;

            }else{
                abort('404');
            }
        }

        if(!isset($id)){
            $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
            $data['voucher_no'] = $this->documentCode($max_voucher,$type);
        }
        if($type == 'ipv')
        {
            $data['terminals'] = TblSoftPOSTerminal::where('branch_id',auth()->user()->branch_id)->orderby('terminal_name')->get();
            $data['users'] = User::where('user_entry_status',1)->where('user_type','pos')->where('branch_id',auth()->user()->branch_id)->orderby(DB::raw('lower(name)'))->get();
        }else{
            $data['users'] = User::where('user_entry_status',1)->where(Utilities::currentBC())->orderby(DB::raw('lower(name)'))->get();
        }

        $data['cash_name'] = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', "6-01-05-0001")->where(Utilities::currentBC())->orderBy('chart_name')->first();
        $data['currency']  = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();

        $user = User::where('id',auth()->user()->id)->where('user_entry_status',1)->where(Utilities::currentBC())->first();
        $data['voucher_post'] = Permission::where('menu_dtl_id',$data['stock_menu_id'])->where('display_name','post')->first();
        if(isset($data['voucher_post']))
        {
            if($data['voucher_post']->display_name == "post")
            {
                $data['page_data']['post'] = $data['stock_menu_id'].'-post';
            }else{
                $data['page_data']['post'] ='';
            }
        }else{
            $data['page_data']['post'] ='';
        }


        $arr = [
            'biz_type' => 'branch',
            'code' => $data['voucher_no'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_acco_voucher',
            'col_id' => 'voucher_id',
            'col_code' => 'voucher_no',
            'code_type_field'   => 'voucher_type',
            'code_type'         => $type,
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        // dd('accounts.'.$formUrl.'.form');

        $data['addInstallmentRow'] = 0;
        if ($request->session()->exists('installmentDetail')) {
            if($formUrl == 'cash_voucher'){
                $data['installmentChartCode'] = '6-06-01-0001';
            }
            $installmentDetails = $request->session()->get('installmentDetail');
            $data['addInstallmentRow'] = 1;
            $data['userdetail']['account_id'] = $installmentDetails['chart_account']['chart_account_id'];
            $data['userdetail']['budget_id'] = '';
            $data['userdetail']['budget_branch_id'] = '';
            $data['userdetail']['cheque_book_id'] = '';
            $data['userdetail']['invoice_id'] = '';
            $data['userdetail']['account_code'] = $installmentDetails['chart_account']['chart_code'];
            $data['userdetail']['account_name'] = $installmentDetails['chart_account']['chart_name']; // chart_account_title
            $data['userdetail']['voucher_credit'] = '';
            $data['userdetail']['installment_id'] = $installmentDetails['installmentId'];
            $data['userdetail']['installment_amount'] = $installmentDetails['installmentAmount'];
            $data['userdetail']['installment_discount'] = $installmentDetails['installmentDiscount'];
            $data['userdetail']['installment_balance'] = $installmentDetails['installmentBalance'];
            $data['userdetail']['installment_desc'] = $installmentDetails['installmentDesc'];
            $request->session()->forget('installmentDetail');
        }
        $data['copy_entry'] = $copy_entry;
        return view('accounts.'.$formUrl.'.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rvstore(Request $request,$type, $id = null)
    {
        // Bank Receive Voucher & Cash Receive Voucher
        $data = [];
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'up_account_id' => 'required',
            'pd.*.account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if($type == 'crv' || $type == 'brv' || $type == 'lfv'){
            $voucher_type = $type;
        }else{
            return $this->returnjsonerror("Voucher type not correct",201);
        }
        if($request->tot_voucher_credit <= 0 || $request->tot_voucher_credit == ''){
            return $this->returnjsonerror(trans('message.fill_the_grid'),201);
        }
        if(isset($request->pd)){
            foreach($request->pd as $pd){
                if(!empty($pd['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$pd['account_id'])->where('chart_code',$pd['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code  not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }
        DB::beginTransaction();
        try{
            $i = 0;
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;

                $del_rvs = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->get();
                foreach ($del_rvs as $del_rv){
                    TblAccoVoucher::where('voucher_id',$del_rv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblAccoVoucher',
                    'code_field'        => 'voucher_no',
                    'code_prefix'       => strtoupper($voucher_type),
                    'code_type_field'   => 'voucher_type',
                    'code_type'         => $voucher_type

                ];
                $voucher_no = Utilities::documentCode($doc_data);
            }
            $form_id = $voucher_id;
            $notes = $request->voucher_notes;
            $cashcode= TblAccCoa::select('chart_Account_id')->where('chart_level', '=',4)->where('chart_code', $request->up_account_code)->where(Utilities::currentBC())->first();
            $account_id = $cashcode->chart_account_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $payment_mode = isset($request->payment_mode)?$request->payment_mode:'';
            $mode_no = isset($request->mode_no)?$request->mode_no:'';
            $saleman_id = $request->saleman_id;
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            //$voucher_descrip = $request->narration;
            $up_chart_code = $request->up_chart_code;

            if($request->pd){
                foreach($request->pd as $dtl){
                    //top entry
                    $voucher = new TblAccoVoucher();
                    $voucher->voucher_sr_no = ++$i;
                    $voucher->voucher_id = $voucher_id;
                    $voucher->voucher_no  = $voucher_no;
                    $voucher->voucher_type = $voucher_type;
                    $voucher->voucher_date = $voucher_date;
                    $voucher->chart_account_id = $account_id;
                    $voucher->voucher_cont_acc_code = $dtl['account_id'];
                    $voucher->chart_code = $up_chart_code;
                    $voucher->voucher_payment_mode = isset($dtl['payment_mode'])?$dtl['payment_mode']:$payment_mode;
                    $voucher->voucher_chqno = isset($dtl['voucher_chqno'])?$dtl['voucher_chqno']:'';
                    $voucher->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:$mode_no;
                    $voucher->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucher->voucher_payee_title = isset($dtl['voucher_payee_title'])?$dtl['voucher_payee_title']:"";;
                    $voucher->saleman_id = $saleman_id;
                    $voucher->currency_id = $currency_id;
                    $voucher->voucher_exchange_rate = $exchange_rate;
                    $voucher->voucher_descrip = $dtl['voucher_descrip'];
                    $voucher->voucher_notes = $notes;
                    $voucher->voucher_debit = 0;
                    $voucher->voucher_fc_debit = 0;
                    $voucher->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucher->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucher->business_id = auth()->user()->business_id;
                    $voucher->company_id = auth()->user()->company_id;
                    $voucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucher->update_user_id = auth()->user()->id;
                        $voucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucher->voucher_user_id = auth()->user()->id;
                    }

                    $voucher->save();

                    //-----------grid entry-----------
                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = ++$i;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->chart_account_id = $dtl['account_id'];
                    $voucherDtl->voucher_cont_acc_code = $account_id;
                    $voucherDtl->budget_id = isset($dtl['budget_id'])?$dtl['budget_id']:"";
                    $voucherDtl->voucher_invoice_id = isset($dtl['invoice_id'])?$dtl['invoice_id']:'';
                    $voucherDtl->budget_branch_id = isset($dtl['budget_branch_id'])?$dtl['budget_branch_id']:"";
                    $voucherDtl->chart_code = $dtl['account_code'];
                    $voucherDtl->voucher_acc_name = $dtl['account_name'];
                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_chqno = isset($dtl['voucher_chqno'])?$dtl['voucher_chqno']:'';
                    $voucherDtl->voucher_payment_mode = isset($dtl['payment_mode'])?$dtl['payment_mode']:'';
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_payee_title = isset($dtl['voucher_payee_title'])?$dtl['voucher_payee_title']:"";;
                    $voucherDtl->voucher_invoice_code = isset($dtl['invoice_code'])?$dtl['invoice_code']:'';
                    $voucherDtl->voucher_debit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_debit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->voucher_credit = 0;
                    $voucherDtl->voucher_fc_credit = 0;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();

                    // If the Entry is Rent Installment
                    if(isset($dtl['installment_id'])){
                        $installment = TblRentAgreementDtl::where('rent_agreement_dtl_id' , $dtl['installment_id'])->first();
                        $installment->rent_agreement_dtl_status = 1;
                        $installment->rent_agreement_dtl_amount = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                        $installment->rent_agreement_dtl_balance = isset($dtl['installment_balance'])?$this->addNo($dtl['installment_balance']):0;
                        $installment->rent_agreement_dtl_discount = isset($dtl['installment_discount'])?$this->addNo($dtl['installment_discount']):0;
                        $installment->voucher_id = $voucher_id;
                        $installment->voucher_no = $voucher_no;
                        $installment->voucher_type = $voucher_type;
                        $installment->voucher_date = $voucher_date;
                        $installment->rent_agreement_dtl_desc = $dtl['voucher_descrip'];
                        $installment->user_id = auth()->user()->id;
                        $installment->save();
                        $form_id = $installment->rent_agreement_id;
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
            return $this->jsonErrorResponse($data, $e->getLine(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            if(isset($dtl['installment_id'])){
                $data['redirect'] = '/rent-agreement/form/'.$form_id;
            }else{
                $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            }
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }

    }
    public function pvstore(Request $request,$type, $id = null)
    {
        // Bank Payment Voucher & Cash Payment Voucher
       // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'cash_type' => 'required',
            'pd.*.account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if($type == 'cpv' || $type == 'bpv'){
            $voucher_type = $type;
        }else{
            return $this->returnjsonerror("Voucher type not correct",201);
        }
        if($request->tot_voucher_credit <= 0 || $request->tot_voucher_credit == ''){
            return $this->returnjsonerror("Fill The Grid",201);
        }
        if(isset($request->pd)){
            foreach($request->pd as $pd){
                if(!empty($pd['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$pd['account_id'])->where('chart_code',$pd['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code  not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Acount Code",201);
                }
            }
        }
        DB::beginTransaction();
        try{
            $i = 0;
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $del_rvs = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->get();
                foreach ($del_rvs as $del_rv){
                    TblAccoVoucher::where('voucher_id',$del_rv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $form_id = $voucher_id;
            $cashcode= TblAccCoa::select('chart_Account_id')->where('chart_level', '=',4)->where('chart_code', $request->cash_type)->where(Utilities::currentBC())->first();
            $account_id = $cashcode->chart_account_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $payment_mode = isset($request->payment_mode)?$request->payment_mode:'';
            $mode_no = isset($request->mode_no)?$request->mode_no:'';
            $saleman_id = $request->saleman_id;
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            //$voucher_descrip = $request->narration;
            $cash_type = $request->cash_type;
            $notes = $request->voucher_notes;

            if($request->pd){
                foreach($request->pd as $dtl){
                    //top entry
                    $voucher = new TblAccoVoucher();
                    $voucher->voucher_id = $voucher_id;
                    $voucher->voucher_no  = $voucher_no;
                    $voucher->voucher_type = $voucher_type;
                    $voucher->voucher_sr_no = ++$i;
                    $voucher->voucher_date = $voucher_date;
                    $voucher->chart_account_id = $account_id;
                    $voucher->voucher_tax_status = '0';
                    $voucher->voucher_chqno = isset($dtl['voucher_chqno'])?$dtl['voucher_chqno']:'';
                    $voucher->voucher_payment_mode = isset($dtl['payment_mode'])?$dtl['payment_mode']:$payment_mode;
                    $voucher->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:$mode_no;
                    $voucher->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucher->saleman_id = $saleman_id;
                    $voucher->currency_id = $currency_id;
                    $voucher->voucher_exchange_rate = $exchange_rate;
                    $voucher->voucher_descrip = $dtl['voucher_descrip'];
                    $voucher->chart_code = $cash_type;
                    $voucher->voucher_notes = $notes;
                    $voucher->voucher_debit = 0;
                    $voucher->voucher_fc_debit = 0;
                    $voucher->voucher_credit = (isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0) + ($dtl['vat_amt']);
                    $voucher->voucher_fc_credit = (isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0) + ($dtl['vat_amt']);

                    $voucher->vat_perc = 0;
                    $voucher->vat_amt = 0;
                    $voucher->net_amt = 0;

                    $voucher->business_id = auth()->user()->business_id;
                    $voucher->company_id = auth()->user()->company_id;
                    $voucher->branch_id = auth()->user()->branch_id;
                    $voucher->voucher_user_id = auth()->user()->id;
                    $voucher->save();

                    //---------grid entry----------

                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = ++$i;
                    $voucherDtl->voucher_date = date('Y-m-d', strtotime($request->voucher_date));
                    $voucherDtl->chart_account_id = $dtl['account_id'];
                    $voucherDtl->voucher_tax_status = '0';
                    $voucherDtl->budget_id = $dtl['budget_id'];
                    $voucherDtl->voucher_invoice_id = isset($dtl['invoice_id'])?$dtl['invoice_id']:'';
                    $voucherDtl->budget_branch_id = $dtl['budget_branch_id'];
                    $voucherDtl->chart_code = $dtl['account_code'];
                    $voucherDtl->voucher_acc_name = $dtl['account_name'];
                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_chqno = isset($dtl['voucher_chqno'])?$dtl['voucher_chqno']:'';
                    $voucherDtl->voucher_payment_mode = isset($dtl['payment_mode'])?$dtl['payment_mode']:'';
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_invoice_code = isset($dtl['invoice_code'])?$dtl['invoice_code']:'';
                    $voucherDtl->voucher_credit = 0;
                    $voucherDtl->voucher_fc_credit = 0;
                    $voucherDtl->voucher_debit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_debit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;

                    $voucherDtl->vat_perc = $dtl['vat_perc'];
                    $voucherDtl->vat_amt = $dtl['vat_amt'];
                    $voucherDtl->net_amt = $dtl['net_amt'];

                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    $voucherDtl->voucher_user_id = auth()->user()->id;
                    $voucherDtl->save();


                    // For VAT saving

                    if($dtl['vat_amt'] != 0) {

                        $voucherDtl = new TblAccoVoucher();
                        $voucherDtl->voucher_id = $voucher_id;
                        $voucherDtl->voucher_no  = $voucher_no;
                        $voucherDtl->voucher_type = $voucher_type;
                        $voucherDtl->voucher_sr_no = ++$i;
                        $voucherDtl->voucher_date = date('Y-m-d', strtotime($request->voucher_date));
                        // $voucherDtl->chart_account_id = $dtl['account_id'];
                        $voucherDtl->chart_account_id = DB::table('TBLG_TAX_SETTING')->where('account_id',$dtl['account_id'])->value('tax_account_id') ?? '67192221301240';
                        $voucherDtl->voucher_tax_status = '1';
                        $voucherDtl->budget_id = $dtl['budget_id'];
                        $voucherDtl->voucher_invoice_id = isset($dtl['invoice_id'])?$dtl['invoice_id']:'';
                        $voucherDtl->budget_branch_id = $dtl['budget_branch_id'];
                        // $voucherDtl->chart_code = $dtl['account_code'];
                        $voucherDtl->chart_code = DB::table('TBL_ACCO_CHART_ACCOUNT')->where('chart_account_id',$voucherDtl->chart_account_id)->value('chart_code');
                        $voucherDtl->voucher_acc_name = DB::table('TBL_ACCO_CHART_ACCOUNT')->where('chart_account_id',$voucherDtl->chart_account_id)->value('chart_name');
                        $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                        $voucherDtl->voucher_chqno = isset($dtl['voucher_chqno'])?$dtl['voucher_chqno']:'';
                        $voucherDtl->voucher_payment_mode = isset($dtl['payment_mode'])?$dtl['payment_mode']:'';
                        $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                        $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                        $voucherDtl->voucher_invoice_code = isset($dtl['invoice_code'])?$dtl['invoice_code']:'';
                        $voucherDtl->voucher_credit = 0;
                        $voucherDtl->voucher_fc_credit = 0;
                        $voucherDtl->voucher_debit = isset($dtl['vat_amt'])?$this->addNo($dtl['vat_amt']):0;
                        $voucherDtl->voucher_fc_debit = isset($dtl['vat_amt'])?$this->addNo($dtl['vat_amt']):0;

                        $voucherDtl->vat_perc = 0;
                        $voucherDtl->vat_amt = 0;
                        $voucherDtl->net_amt = 0;

                        $voucherDtl->voucher_notes = $notes;
                        $voucherDtl->business_id = auth()->user()->business_id;
                        $voucherDtl->company_id = auth()->user()->company_id;
                        $voucherDtl->branch_id = auth()->user()->branch_id;
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                        $voucherDtl->save();

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
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }

    }
    public function jvStore(Request $request,$type, $id = null)
    {
        // journal Voucher
        $data = [];
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'pd.*.account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        if($type == 'jv' || $type == 'obv' || $type == 'lv'){
            $voucher_type = $type;
        }else{
            return $this->returnjsonerror("Voucher type not correct",201);
        }
        if($type == 'jv' || $type == 'lv'){
            if($request->tot_jv_difference != 0){
                return $this->returnjsonerror(" Voucher not correct",201);
            }
        }
        if(!isset($request->pd) || count($request->pd) == 0){
            return $this->jsonErrorResponse($data, trans('message.fill_the_grid'), 200);
        }
        if(isset($request->pd)){
            foreach($request->pd as $pd){
                if(!empty($pd['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$pd['account_id'])->where('chart_code',$pd['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code  not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }
        DB::beginTransaction();
        try {
            $i = 0;
            if(isset($id)){
                $uuid = $id;
                $code= TblAccoVoucher::where('voucher_id',$uuid)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                $del_jvs = TblAccoVoucher::where('voucher_id',$uuid)->where(Utilities::currentBCB())->get();
                foreach ($del_jvs as $del_jv){
                    TblAccoVoucher::where('voucher_id',$del_jv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $uuid = $this->uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $form_id = $uuid;
            $voucher_date =  $request->voucher_date;
            $currency_id = $request->currency_id;
            $voucher_exchange_rate = $request->exchange_rate;
            $voucher_notes = $request->voucher_notes;
            if($request->pd) {
                foreach ($request->pd as $dtl) {
                    $i++;
                    $voucher = new TblAccoVoucher();
                    $voucher->voucher_id = $uuid;
                    $voucher->voucher_type = $voucher_type;
                    $voucher->voucher_date = date('Y-m-d', strtotime($voucher_date));
                    $voucher->voucher_sr_no = $i;
                    $voucher->voucher_no = $voucher_no;
                    $voucher->currency_id = $currency_id;
                    $voucher->voucher_exchange_rate = $voucher_exchange_rate;
                    $voucher->voucher_notes = $voucher_notes;
                    $voucher->chart_account_id = $dtl['account_id'];
                    $voucher->budget_id = $dtl['budget_id'];
                    $voucher->budget_branch_id = $dtl['budget_branch_id'];
                    $voucher->chart_code = $dtl['account_code'];
                    $voucher->voucher_acc_name = $dtl['account_name'];
                    $voucher->voucher_descrip = $dtl['voucher_descrip'];
                    $voucher->voucher_debit =  isset($dtl['voucher_debit'])?$this->addNo($dtl['voucher_debit']):0;
                    $voucher->voucher_fc_debit =  isset($dtl['voucher_fc_debit'])?$this->addNo($dtl['voucher_fc_debit']):0;
                    $voucher->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucher->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucher->business_id = auth()->user()->business_id;
                    $voucher->company_id = auth()->user()->company_id;
                    $voucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucher->update_user_id = auth()->user()->id;
                        $voucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucher->voucher_user_id = auth()->user()->id;
                    }
                    $voucher->save();
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
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function brpvstore(Request $request, $type,$id = null)
    {

        $data = [];
        $voucher_type = 'brpv';
        if(!in_array($type,[$voucher_type])){
            return $this->jsonErrorResponse($data,"Type not correct",200);
        }
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'cash_type' => 'required',
            'pd.*.c_account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }

        $acc_code_list = TblAccCoa::where('chart_level', '=',4)->where('chart_code','like', "3-01-03-%")
            ->where(Utilities::currentBC())->pluck('chart_account_id')->toArray();

        if(!in_array($request->cash_type,$acc_code_list)){
            return $this->jsonErrorResponse($data,"Parent Account Code not correct",200);
        }
        $total_amount = 0;
        if(isset($request->duc) && isset($request->is_deduction)){
            foreach($request->duc as $duc_c){
                $total_amount += $this->addNo($duc_c['amount']);
            }
        }

        if(($request->bill_amount+$total_amount) != $request->tot_voucher_credit){
            return $this->jsonErrorResponse($data,"Invalid Voucher Amount",200);
        }


        DB::beginTransaction();
        try{
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                $del_rvs = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->get();
                foreach ($del_rvs as $del_rv){
                    TblAccoVoucher::where('voucher_id',$del_rv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }

            $form_id = $voucher_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            $narration = $request->narration;
            $bill_amount = $request->bill_amount;
            $account_id = $request->cash_type;
            $acc_code_fetch = TblAccCoa::where('chart_Account_id',$account_id)->first();
            $account_code = $acc_code_fetch->chart_code;
            $payment_branch_id = $acc_code_fetch->chart_branch_id;
            $saleman_id = $request->saleman_id;
            $notes = $request->voucher_notes;
            $is_deduction = isset($request->is_deduction)?1:0;

            //top entry
            $voucher = new TblAccoVoucher();
            $voucher->voucher_id = $voucher_id;
            $voucher->voucher_no  = $voucher_no;
            $voucher->voucher_type = $voucher_type;
            $voucher->voucher_sr_no = 1;
            $voucher->voucher_date = $voucher_date;
            $voucher->currency_id = $currency_id;
            $voucher->voucher_exchange_rate = $exchange_rate;
            $voucher->saleman_id = $saleman_id;
            $voucher->voucher_notes = $notes;
            $voucher->chart_account_id =  $account_id;
            $voucher->chart_code = $account_code;
            $voucher->payment_branch_id = $payment_branch_id;
            $voucher->voucher_descrip = $narration;
            $voucher->is_deduction = $is_deduction;

            // $voucher->voucher_payment_mode = $dtl['payment_mode'];
            // $voucher->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
            // $voucher->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
            $voucher->voucher_debit = isset($bill_amount)?$this->addNo($bill_amount):0;
            $voucher->voucher_fc_debit = isset($bill_amount)?$this->addNo($bill_amount):0;
            $voucher->voucher_credit = 0;
            $voucher->voucher_fc_credit = 0;
            $voucher->business_id = auth()->user()->business_id;
            $voucher->company_id = auth()->user()->company_id;
            $voucher->branch_id = auth()->user()->branch_id;
            if(isset($id))
            {
                $voucher->update_user_id = auth()->user()->id;
                $voucher->voucher_user_id = $voucher_user_id;
            }else{
                $voucher->voucher_user_id = auth()->user()->id;
            }
            $voucher->save();

            if($request->pd){
                $i = 2;
                foreach($request->pd as $dtl){
                    $exits = TblAccCoa::where('chart_account_id',$dtl['c_account_id'])->where('chart_code',$dtl['c_account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->jsonErrorResponse($data,"Account Code not correct",200);
                    }

                    //---------grid entry----------

                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $exchange_rate;
                    $voucherDtl->saleman_id = $saleman_id;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->chart_account_id = $dtl['c_account_id'];
                    $voucherDtl->chart_code = $dtl['c_account_code'];
                    $voucherDtl->payment_branch_id = $payment_branch_id;
                    $voucherDtl->is_deduction = $is_deduction;
                    $voucherDtl->voucher_grid_type = 'actual';

                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_payment_mode = $dtl['payment_mode'];
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_debit = 0;
                    $voucherDtl->voucher_fc_debit = 0;
                    $voucherDtl->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
                }
            }
            if(isset($request->is_deduction) && isset($request->duc) && count($request->duc) != 0){
                foreach ($request->duc as $duc){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$duc['account_id'])->where('chart_code',$duc['account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct",200);
                    }
                    $ducVoucher = new TblAccoVoucher();
                    $ducVoucher->voucher_id = $voucher_id;
                    $ducVoucher->voucher_no  = $voucher_no;
                    $ducVoucher->voucher_type = $voucher_type;
                    $ducVoucher->voucher_sr_no = $i++;
                    $ducVoucher->voucher_date = $voucher_date;
                    $ducVoucher->currency_id = $currency_id;
                    $ducVoucher->voucher_exchange_rate = $exchange_rate;
                    $ducVoucher->voucher_notes = $notes;
                    $ducVoucher->chart_account_id = $duc['account_id'];
                    $ducVoucher->voucher_cont_acc_code = $account_id;
                    $ducVoucher->chart_code = $exitsAcc->chart_code;
                    $ducVoucher->is_deduction = $is_deduction;
                    $ducVoucher->voucher_grid_type = 'deduction';

                    $ducVoucher->voucher_descrip = $duc['voucher_narration'];
                    $ducVoucher->voucher_debit = $this->addNo($duc['amount']);
                    $ducVoucher->voucher_fc_debit = $this->addNo($duc['amount']);
                    $ducVoucher->voucher_credit = 0;
                    $ducVoucher->voucher_fc_credit = 0;
                    $ducVoucher->business_id = auth()->user()->business_id;
                    $ducVoucher->company_id = auth()->user()->company_id;
                    $ducVoucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $ducVoucher->update_user_id = auth()->user()->id;
                        $ducVoucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $ducVoucher->voucher_user_id = auth()->user()->id;
                    }
                    $ducVoucher->save();
                }
            }

        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function brrvstore(Request $request, $type,$id = null){
        // dd(current($request->pd));
        $data = [];
        $voucher_type = 'brrv';
        if(!in_array($type,[$voucher_type])){
            return $this->jsonErrorResponse($data,"Type not correct",200);
        }
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'pd.*.account_id' => 'required|numeric',
            'inv.*.chart_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(!isset($request->pd)){
            return $this->jsonErrorResponse($data,"Fill The Grid",200);
        }
        $pd_total_amount = 0;
        foreach($request->pd as $dtl_c){
            $pd_total_amount += $dtl_c['voucher_credit'];
        }
        $inv_total_amount = 0;
        if(isset($request->inv)){
            foreach($request->inv as $inv_c){
                $inv_total_amount += $inv_c['current_amount'];
            }
        }
        if($inv_total_amount != $inv_total_amount){
            return $this->jsonErrorResponse($data,"Debit Credit Amount not equal",200);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->delete();
                TblAccoVoucherBillDtl::where('voucher_id',$id)->delete();
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $form_id = $voucher_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            $narration = $request->narration;
            $notes = $request->voucher_notes;
            $ki = 1;
            if($request->pd){
                $i = 1;
                foreach($request->pd as $dtl){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$dtl['account_id'])->where('chart_code',$dtl['account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct",200);
                    }
                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $exchange_rate;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->chart_account_id = $dtl['account_id'];
                    $voucherDtl->chart_code = $exitsAcc->chart_code;
                    $voucherDtl->voucher_grid_type = 'actual';

                    $voucherDtl->bank_id = $dtl['bank'];
                    $voucherDtl->bank_branch_code = $dtl['bank_branch_code'];
                    $voucherDtl->payee_ac_no = $dtl['payee_ac_no'];
                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_payment_mode = $dtl['payment_mode'];
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_debit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_debit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->voucher_credit = 0;
                    $voucherDtl->voucher_fc_credit = 0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
                    $ki = $i;
                }
            }
            if($request->inv){
                $bi = 1;
                foreach ($request->inv as $inv){
                    $bill = new TblAccoVoucherBillDtl();
                    $bill->voucher_bill_id = Utilities::uuid();
                    $bill->voucher_id = $voucher_id;
                    $bill->voucher_bill_sr_no = $bi;
                    $bill->voucher_document_id = $inv['document_id'];
                    $bill->voucher_document_code = $inv['document_code'];
                    $bill->chart_account_id = $inv['chart_id'];
                    $bill->voucher_document_date = date('Y-m-d', strtotime($inv['document_date']));
                    $bill->voucher_bill_amount = $this->addNo($inv['current_amount']);
                    $bill->voucher_bill_bal_amount = $this->addNo($inv['current_amount']);
                    $bill->voucher_bill_rec_amount = $this->addNo($inv['current_balance']);
                    $bill->voucher_bill_net_bal_amount = $this->addNo($inv['current_balance']);
                    $bill->voucher_bill_narration = $narration;
                    $bill->paymnet_branch_id = $inv['branch_id'];
                    $bill->paymnet_branch_name = $inv['branch_name'];
                    $bill->voucher_bill_grn_paid_status = !empty($this->addNo($inv['current_balance']))?1:0;
                    $bill->voucher_bill_type = !empty($this->addNo($inv['current_balance']))?'brrv':'';
                    $bill->business_id = auth()->user()->business_id;
                    $bill->company_id = auth()->user()->company_id;
                    $bill->branch_id = auth()->user()->branch_id;
                    $bill->save();
                    $bi += 1;
                }
                foreach($request->inv as $invo){
                    $exitsAcc = TblSoftBranch::with('branch_coa')->where('branch_id',$invo['branch_id'])->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct",200);
                    }
                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $ki++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $exchange_rate;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->chart_account_id = $exitsAcc->branch_coa->chart_account_id;
                    $voucherDtl->chart_code = $exitsAcc->branch_coa->chart_code;
                    $voucherDtl->voucher_grid_type = 'branch';

                    $voucherDtl->voucher_debit = 0;
                    $voucherDtl->voucher_fc_debit = 0;
                    $voucherDtl->voucher_credit = isset($invo['current_balance'])?$this->addNo($invo['current_balance']):0;
                    $voucherDtl->voucher_fc_credit = isset($invo['current_balance'])?$this->addNo($invo['current_balance']):0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
                }
            }


        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function vpvstore(Request $request, $type,$id = null){
        $data = [];
        $voucher_type = 'pv';
        if(!in_array($type,[$voucher_type])){
            return $this->jsonErrorResponse($data,"Type not correct",200);
        }
        $validator = Validator::make($request->all(), [
            'up_chart_account_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'pd.*.c_account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(!isset($request->pd)){
            return $this->jsonErrorResponse($data,"Fill The Grid",200);
        }
        $total_amount = 0;
        foreach($request->pd as $pdk=>$dtl_c){
           // dump($dtl_c);
            $total_amount += $this->addNo($dtl_c['voucher_credit']);
            if ($dtl_c['payment_mode'] == '18714922081948' && $dtl_c['mode_no'] != null) {
                foreach($request->pd as $pdkd=>$dtl_cc){
                    // dd($dtl_cc);
                    if($pdk != $pdkd && $dtl_cc['mode_no'] == $dtl_c['mode_no']){
                        return $this->jsonErrorResponse($data,$dtl_c['mode_no']." : Mode No is duplicate",200);
                    }
                }
                $exists = TblAccoVoucher::where('chart_account_id',$dtl_c['c_account_id'])->where('voucher_mode_no',$dtl_c['mode_no'])->where(Utilities::currentBCB());
                if(isset($id)){
                    $exists = $exists->where('voucher_id','!=',$id);
                }
                $exists = $exists->first();
                if(!empty($exists)){
                    return $this->jsonErrorResponse($data,$dtl_c['mode_no']." : Mode No already used in ".$exists->voucher_no,200);
                }
            }
        }
        if(isset($request->duc) && isset($request->is_deduction)){
            foreach($request->duc as $duc_c){
                $total_amount += $this->addNo($duc_c['amount']);
            }
        }
        $grn_total_amount = 0;
        if(isset($request->inv) && !isset($request->on_account_voucher)){
            foreach($request->inv as $inv_c){
                $grn_total_amount += $this->addNo($inv_c['current_amount']);
            }
        }
        $bill_total_amount = $request->bill_total_amount;
        if(isset($request->on_account_voucher)){
            if($this->addNo($total_amount) != $this->addNo($bill_total_amount)){
                return $this->jsonErrorResponse($data,"Amount not equal",200);
            }
        }else{
            if($this->addNo($total_amount) != $this->addNo($grn_total_amount)){
                return $this->jsonErrorResponse($data,"Amount not equal",200);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->delete();
                TblAccoVoucherBillDtl::where('voucher_id',$id)->delete();
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }


            $form_id = $voucher_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            $narration = $request->narration;
            $account_id = $request->up_chart_account_id;
            $acc_code_fetch = TblAccCoa::where('chart_Account_id',$account_id)->first();
            $account_code = $acc_code_fetch->chart_code;
            $notes = $request->voucher_notes;
            $on_account_voucher = isset($request->on_account_voucher)?1:0;
            $is_deduction = isset($request->is_deduction)?1:0;

            if(isset($request->inv)){
                if($request->inv && !isset($request->on_account_voucher)){
                    $bi = 1;
                    foreach ($request->inv as $inv){
                        $bill = new TblAccoVoucherBillDtl();
                        $bill->voucher_bill_id = Utilities::uuid();
                        $bill->voucher_id = $voucher_id;
                        $bill->voucher_bill_sr_no = $bi;
                        $bill->voucher_document_id = $inv['grn_id'];
                        $bill->voucher_document_code = $inv['grn_code'];
                        $bill->voucher_document_date = date('Y-m-d', strtotime($inv['grn_date']));
                        $bill->voucher_document_ref = $inv['grn_bill_no'];
                        $bill->voucher_bill_amount = $this->addNo($inv['grn_total_net_amount']);
                        $bill->voucher_bill_bal_amount = $this->addNo($inv['balance_amount']);
                        $bill->voucher_bill_rec_amount = $this->addNo($inv['current_amount']);
                        $bill->voucher_bill_net_bal_amount = $this->addNo($inv['current_balance']);
                        $bill->voucher_bill_paid_amount = $this->addNo($inv['paid_amount']);
                        $bill->voucher_bill_narration = $narration;
                        $bill->voucher_bill_grn_paid_status = !empty($this->addNo($inv['current_amount']))?1:0;
                        $bill->business_id = auth()->user()->business_id;
                        $bill->company_id = auth()->user()->company_id;
                        $bill->branch_id = $inv['branch_id'];
                        $bill->voucher_type = $inv['grn_type'];
                        $bill->save();
                        $bi += 1;
                    }
                }
            }
            if(isset($request->invr) &&  !isset($request->on_account_voucher)){
                $bi = 1;
                foreach ($request->invr as $invr){
                    $ref_id = "";
                    if(!empty($invr['ref_code'])){
                        $ref_code_boolean = false;
                        foreach ($request->inv as $inv){
                            if($invr['ref_code'] == $inv['grn_code']){
                                $ref_code_boolean = true;
                                $ref_id = $inv['grn_id'];
                            }
                        }
                        if(!$ref_code_boolean){
                            return $this->jsonErrorResponse($data,"GRNR Code is not correct",200);
                        }
                    }
                    $bill = new TblAccoVoucherBillDtl();
                    $bill->voucher_bill_id = Utilities::uuid();
                    $bill->voucher_id = $voucher_id;
                    $bill->voucher_bill_sr_no = $bi;
                    $bill->voucher_document_id = $invr['grn_id'];
                    $bill->voucher_document_code = $invr['grn_code'];
                    $bill->voucher_document_date = date('Y-m-d', strtotime($invr['grn_date']));
                    $bill->voucher_bill_amount = $this->addNo($invr['grn_total_net_amount']);
                    $bill->voucher_bill_bal_amount = $this->addNo($invr['grn_total_net_amount']);
                    if(!empty($ref_id)){
                        $bill->voucher_bill_rec_amount = $this->addNo($invr['grn_total_net_amount']);
                        $bill->voucher_bill_net_bal_amount = 0;
                    }else{
                        $bill->voucher_bill_rec_amount = 0;
                        $bill->voucher_bill_net_bal_amount = $this->addNo($invr['grn_total_net_amount']);
                    }
                    $bill->voucher_bill_paid_amount = 0;
                    $bill->voucher_bill_grn_paid_status = !empty($ref_id)?1:0;
                    $bill->business_id = auth()->user()->business_id;
                    $bill->company_id = auth()->user()->company_id;
                    $bill->branch_id = $invr['branch_id'];
                    $bill->voucher_type = $invr['grn_type'];
                    $bill->document_reference_id = $ref_id;
                    $bill->save();
                    $bi += 1;
                }
            }

            if(isset($request->pd) && count($request->pd) != 0){
                $voucher = new TblAccoVoucher();
                $voucher->voucher_id = $voucher_id;
                $voucher->voucher_no  = $voucher_no;
                $voucher->voucher_type = $voucher_type;
                $voucher->voucher_date = $voucher_date;
                $voucher->currency_id = $currency_id;
                $voucher->voucher_exchange_rate = $exchange_rate;
                $voucher->voucher_notes = $notes;
                $voucher->chart_account_id = $account_id;
                $voucher->voucher_cont_acc_code = $account_id;
                $voucher->chart_code = $account_code;
                $voucher->voucher_descrip = $narration;
                $voucher->on_account_voucher = $on_account_voucher;
                $voucher->is_deduction = $is_deduction;
                $voucher->voucher_grid_type = 'vendor';

                $voucher->voucher_debit = $this->addNo($bill_total_amount);
                $voucher->voucher_fc_debit =$this->addNo($bill_total_amount);
                $voucher->voucher_credit = 0;
                $voucher->voucher_fc_credit = 0;
                $voucher->business_id = auth()->user()->business_id;
                $voucher->company_id = auth()->user()->company_id;
                $voucher->branch_id = auth()->user()->branch_id;

                if(isset($id))
                {
                    $voucher->update_user_id = auth()->user()->id;
                    $voucher->voucher_user_id = $voucher_user_id;
                }else{
                    $voucher->voucher_user_id = auth()->user()->id;
                }

                $voucher->save();

                $i = 1;
                foreach($request->pd as $dtl){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$dtl['c_account_id'])->where('chart_code',$dtl['c_account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct v",200);
                    }
                    //Credit
                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $exchange_rate;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->chart_account_id = $dtl['c_account_id'];
                    $voucherDtl->voucher_cont_acc_code = $account_id;
                    $voucherDtl->chart_code = $exitsAcc->chart_code;
                    $voucherDtl->on_account_voucher = $on_account_voucher;
                    $voucherDtl->is_deduction = $is_deduction;
                    $voucherDtl->voucher_grid_type = 'actual';

                    $voucherDtl->payee_bank = isset($dtl['supplier_bank'])?$dtl['supplier_bank']:'';
                    $voucherDtl->payee_ac_no = isset($dtl['supplier_bank_ac_no'])?$dtl['supplier_bank_ac_no']:'';
                    $voucherDtl->bank_branch_code = isset($dtl['bank_branch_code'])?$dtl['bank_branch_code']:'';
                    // $voucherDtl->payee_ac_no = $dtl['payee_ac_no'];
                    $voucherDtl->tbl_supplier_account_id = $dtl['supplier_account_id'];
                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_payment_mode = $dtl['payment_mode'];
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_debit = 0;
                    $voucherDtl->voucher_fc_debit = 0;
                    $voucherDtl->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
                }
            }
            if(isset($request->is_deduction) && isset($request->duc) && count($request->duc) != 0){
                foreach ($request->duc as $duc){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$duc['account_id'])->where('chart_code',$duc['account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct v",200);
                    }
                    $ducVoucher = new TblAccoVoucher();
                    $ducVoucher->voucher_id = $voucher_id;
                    $ducVoucher->voucher_no  = $voucher_no;
                    $ducVoucher->voucher_type = $voucher_type;
                    $ducVoucher->voucher_sr_no = $i++;
                    $ducVoucher->voucher_date = $voucher_date;
                    $ducVoucher->currency_id = $currency_id;
                    $ducVoucher->voucher_exchange_rate = $exchange_rate;
                    $ducVoucher->voucher_notes = $notes;
                    $ducVoucher->chart_account_id = $duc['account_id'];
                    $ducVoucher->voucher_cont_acc_code = $account_id;
                    $ducVoucher->chart_code = $exitsAcc->chart_code;
                    $ducVoucher->on_account_voucher = $on_account_voucher;
                    $ducVoucher->is_deduction = $is_deduction;
                    $ducVoucher->voucher_grid_type = 'deduction';

                    $ducVoucher->voucher_descrip = $duc['voucher_narration'];
                    $ducVoucher->voucher_debit = 0;
                    $ducVoucher->voucher_fc_debit = 0;
                    $ducVoucher->voucher_credit = $this->addNo($duc['amount']);
                    $ducVoucher->voucher_fc_credit = $this->addNo($duc['amount']);
                    $ducVoucher->business_id = auth()->user()->business_id;
                    $ducVoucher->company_id = auth()->user()->company_id;
                    $ducVoucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $ducVoucher->update_user_id = auth()->user()->id;
                        $ducVoucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $ducVoucher->voucher_user_id = auth()->user()->id;
                    }
                    $ducVoucher->save();
                }
            }
        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function receivedVoucherStore(Request $request, $type,$id = null){
        // Receiving Voucher
        //dd($request->toArray());
        $data = [];
        $voucher_type = 'rv';
        if(!in_array($type,[$voucher_type])){
            return $this->jsonErrorResponse($data,"Type not correct",200);
        }
        $validator = Validator::make($request->all(), [
            'up_chart_account_id' => 'required|numeric',
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'pd.*.c_account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(!isset($request->pd)){
            return $this->jsonErrorResponse($data,"Fill The Grid",200);
        }
        $total_amount = 0;
        foreach($request->pd as $dtl_c){
            $total_amount += $this->addNo($dtl_c['voucher_credit']);
        }
        if(isset($request->duc) && isset($request->is_deduction)){
            foreach($request->duc as $duc_c){
                $total_amount += $this->addNo($duc_c['amount']);
            }
        }
        $grn_total_amount = 0;
        if(isset($request->inv) && !isset($request->on_account_voucher)){
            foreach($request->inv as $inv_c){
                $grn_total_amount += $this->addNo($inv_c['current_amount']);
            }
        }
        $bill_total_amount = $request->bill_total_amount;
        if(isset($request->on_account_voucher)){
            if($this->addNo($total_amount) != $this->addNo($bill_total_amount)){
                return $this->jsonErrorResponse($data,"Amount not equal",200);
            }
        }else{
            if($this->addNo($total_amount) != $this->addNo($grn_total_amount)){
                return $this->jsonErrorResponse($data,"Amount not equal",200);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->delete();
                TblAccoVoucherBillDtl::where('voucher_id',$id)->delete();
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $form_id = $voucher_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            $narration = $request->narration;
            $account_id = $request->up_chart_account_id;
            $acc_code_fetch = TblAccCoa::where('chart_Account_id',$account_id)->first();
            $account_code = $acc_code_fetch->chart_code;
            $notes = $request->voucher_notes;
            $on_account_voucher = isset($request->on_account_voucher)?1:0;
            $is_deduction = isset($request->is_deduction)?1:0;


            if(isset($request->inv)){
                if($request->inv && !isset($request->on_account_voucher)){
                    $bi = 1;
                    foreach ($request->inv as $inv){
                        $v_type = explode("-",$inv['grn_code']);
                       // dump($v_type);
                        if($v_type[0] == "PR"){
                            $account_id_vtype = "";
                        }else{
                            $account_id_vtype = $account_id;
                        }
                            $bill = new TblAccoVoucherBillDtl();
                            $bill->voucher_bill_id = Utilities::uuid();
                            $bill->voucher_id = $voucher_id;
                            $bill->voucher_bill_sr_no = $bi;
                            $bill->chart_account_id = $account_id_vtype;
                            $bill->voucher_document_id = $inv['grn_id'];
                            $bill->voucher_document_code = $inv['grn_code'];
                            $bill->voucher_document_date = date('Y-m-d', strtotime($inv['grn_date']));
                            $bill->voucher_document_ref = $inv['grn_bill_no'];
                            $bill->voucher_bill_amount = $this->addNo($inv['grn_total_net_amount']);
                            $bill->voucher_bill_bal_amount = $this->addNo($inv['balance_amount']);
                            $bill->voucher_bill_rec_amount = $this->addNo($inv['current_amount']);
                            $bill->voucher_bill_net_bal_amount = $this->addNo($inv['current_balance']);
                            $bill->voucher_bill_paid_amount = $this->addNo($inv['paid_amount']);
                            $bill->voucher_bill_narration = $narration;
                            $bill->voucher_bill_grn_paid_status = !empty($this->addNo($inv['current_amount']))?1:0;
                            $bill->business_id = auth()->user()->business_id;
                            $bill->company_id = auth()->user()->company_id;
                            $bill->branch_id = $inv['branch_id'];
                            $bill->save();
                            $bi += 1;

                    }
                }
            }

            if(isset($request->pd) && count($request->pd) != 0){
                $voucher = new TblAccoVoucher();
                $voucher->voucher_id = $voucher_id;
                $voucher->voucher_no  = $voucher_no;
                $voucher->voucher_type = $voucher_type;
                $voucher->voucher_date = $voucher_date;
                $voucher->currency_id = $currency_id;
                $voucher->voucher_exchange_rate = $exchange_rate;
                $voucher->voucher_notes = $notes;
                $voucher->chart_account_id = $account_id;
                $voucher->voucher_cont_acc_code = $account_id;
                $voucher->chart_code = $account_code;
                $voucher->voucher_descrip = $narration;
                $voucher->on_account_voucher = $on_account_voucher;
                $voucher->is_deduction = $is_deduction;
                $voucher->voucher_grid_type = 'vendor';

                $voucher->voucher_debit = 0;
                $voucher->voucher_fc_debit = 0;
                $voucher->voucher_credit = $this->addNo($bill_total_amount);
                $voucher->voucher_fc_credit = $this->addNo($bill_total_amount);
                $voucher->business_id = auth()->user()->business_id;
                $voucher->company_id = auth()->user()->company_id;
                $voucher->branch_id = auth()->user()->branch_id;
                if(isset($id))
                {
                    $voucher->update_user_id = auth()->user()->id;
                    $voucher->voucher_user_id = $voucher_user_id;
                }else{
                    $voucher->voucher_user_id = auth()->user()->id;
                }
                $voucher->save();

                $i = 1;
                foreach($request->pd as $dtl){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$dtl['c_account_id'])->where('chart_code',$dtl['c_account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct v",200);
                    }
                    //Credit
                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $exchange_rate;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->chart_account_id = $dtl['c_account_id'];
                    $voucherDtl->voucher_cont_acc_code = $account_id;
                    $voucherDtl->chart_code = $exitsAcc->chart_code;
                    $voucherDtl->on_account_voucher = $on_account_voucher;
                    $voucherDtl->is_deduction = $is_deduction;
                    $voucherDtl->voucher_grid_type = 'actual';

                    $voucherDtl->bank_id = $dtl['supplier_bank_id'];
                    $voucherDtl->tbl_supplier_account_id = $dtl['supplier_account_id'];
                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_payment_mode = $dtl['payment_mode'];
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_debit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_debit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->voucher_credit = 0;
                    $voucherDtl->voucher_fc_credit = 0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
                }
            }
            if(isset($request->is_deduction) && isset($request->duc) && count($request->duc) != 0){
                foreach ($request->duc as $duc){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$duc['account_id'])->where('chart_code',$duc['account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct v",200);
                    }
                    $ducVoucher = new TblAccoVoucher();
                    $ducVoucher->voucher_id = $voucher_id;
                    $ducVoucher->voucher_no  = $voucher_no;
                    $ducVoucher->voucher_type = $voucher_type;
                    $ducVoucher->voucher_sr_no = $i++;
                    $ducVoucher->voucher_date = $voucher_date;
                    $ducVoucher->currency_id = $currency_id;
                    $ducVoucher->voucher_exchange_rate = $exchange_rate;
                    $ducVoucher->voucher_notes = $notes;
                    $ducVoucher->chart_account_id = $duc['account_id'];
                    $ducVoucher->voucher_cont_acc_code = $account_id;
                    $ducVoucher->chart_code = $exitsAcc->chart_code;
                    $ducVoucher->on_account_voucher = $on_account_voucher;
                    $ducVoucher->is_deduction = $is_deduction;
                    $ducVoucher->voucher_grid_type = 'deduction';

                    $ducVoucher->voucher_descrip = $duc['voucher_narration'];
                    $ducVoucher->voucher_debit = $this->addNo($duc['amount']);
                    $ducVoucher->voucher_fc_debit = $this->addNo($duc['amount']);
                    $ducVoucher->voucher_credit = 0;
                    $ducVoucher->voucher_fc_credit = 0;
                    $ducVoucher->business_id = auth()->user()->business_id;
                    $ducVoucher->company_id = auth()->user()->company_id;
                    $ducVoucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $ducVoucher->update_user_id = auth()->user()->id;
                        $ducVoucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $ducVoucher->voucher_user_id = auth()->user()->id;
                    }
                    $ducVoucher->save();
                }
            }
        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            if(auth()->user()->isAbleTo('286-edit')){
                $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            }else{
                $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage;
            }
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function ipvstore(Request $request, $type,$id = null){
        $data = [];
        $chart_cash_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->cash_group)->where(Utilities::currentBC())->first('chart_code');
        $chart_bank_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->bank_group)->where(Utilities::currentBC())->first('chart_code');
        $cash_group = substr($chart_cash_group->chart_code,0,7);
        $bank_group = substr($chart_bank_group->chart_code,0,7);

        $voucher_type = 'ipv';
        if(!in_array($type,[$voucher_type])){
            return $this->jsonErrorResponse($data,"Type not correct",200);
        }
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'cash_type' => 'required',
            'pd.*.account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if($request->tot_voucher_credit <= 0 || $request->tot_voucher_credit == ''){
            return $this->jsonErrorResponse($data,"Fill The Grid",200);
        }

        /*$acc_code_list = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)
            ->where('chart_code','like', $bank_group."%")->orWhere('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->pluck('chart_account_id')->toArray();
        */

        $acc_code_list = TblAccCoa::select('chart_code','chart_name','chart_Account_id')
            ->where('chart_level', '=',4)
            ->where('chart_code','like', '6-01-02'."%")
            ->where(Utilities::currentBC())
            ->pluck('chart_account_id')
            ->toArray();

        if(!in_array($request->cash_type,$acc_code_list)){
            return $this->jsonErrorResponse($data,"Parent Account Code not correct",200);
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                $del_rvs = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->get();
                foreach ($del_rvs as $del_rv){
                    TblAccoVoucher::where('voucher_id',$del_rv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $form_id = $voucher_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            $narration = $request->narration;
            $account_id = $request->cash_type;
            $acc_code_fetch = TblAccCoa::where('chart_Account_id',$account_id)->first();
            $account_code = $acc_code_fetch->chart_code;
            $saleman_id = $request->saleman_id;
            $terminal_id = $request->terminal_id;
            $notes = $request->voucher_notes;

            if($request->pd){
                $i = 1;
                foreach($request->pd as $dtl){
                    $exits = TblAccCoa::where('chart_account_id',$dtl['account_id'])->where('chart_code',$dtl['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->jsonErrorResponse($data,"Account Code not correct",200);
                    }

                    //top entry
                    $voucher = new TblAccoVoucher();
                    $voucher->voucher_id = $voucher_id;
                    $voucher->voucher_no  = $voucher_no;
                    $voucher->voucher_type = $voucher_type;
                    $voucher->voucher_sr_no = $i++;
                    $voucher->voucher_date = $voucher_date;
                    $voucher->currency_id = $currency_id;
                    $voucher->voucher_exchange_rate = $exchange_rate;
                    $voucher->saleman_id = $saleman_id;
                    $voucher->terminal_id = $terminal_id;
                    $voucher->voucher_notes = $notes;
                    $voucher->chart_account_id = $account_id;
                    $voucher->chart_code = $account_code;
                    $voucher->voucher_descrip = $narration;

                    $voucher->voucher_payment_mode = $dtl['payment_mode'];
                    $voucher->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucher->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucher->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucher->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucher->voucher_debit = 0;
                    $voucher->voucher_fc_debit = 0;
                    $voucher->business_id = auth()->user()->business_id;
                    $voucher->company_id = auth()->user()->company_id;
                    $voucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucher->update_user_id = auth()->user()->id;
                        $voucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucher->voucher_user_id = auth()->user()->id;
                    }
                    $voucher->save();

                    //---------grid entry----------

                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $exchange_rate;
                    $voucherDtl->saleman_id = $saleman_id;
                    $voucherDtl->terminal_id = $terminal_id;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->chart_account_id = $dtl['account_id'];
                    $voucherDtl->chart_code = $dtl['account_code'];

                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_payment_mode = $dtl['payment_mode'];
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_credit = 0;
                    $voucherDtl->voucher_fc_credit = 0;
                    $voucherDtl->voucher_debit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_debit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
                }
            }


        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function irvstore(Request $request, $type,$id = null){
        $data = [];
        $chart_cash_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->cash_group)->where(Utilities::currentBC())->first('chart_code');
        $chart_bank_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->bank_group)->where(Utilities::currentBC())->first('chart_code');
        $cash_group = substr($chart_cash_group->chart_code,0,7);
        $bank_group = substr($chart_bank_group->chart_code,0,7);

        $voucher_type = 'irv';
        if(!in_array($type,[$voucher_type])){
            return $this->jsonErrorResponse($data,"Type not correct",200);
        }
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'cash_type' => 'required',
            'pd.*.account_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if($request->tot_voucher_credit <= 0 || $request->tot_voucher_credit == ''){
            return $this->jsonErrorResponse($data,"Fill The Grid",200);
        }


        $acc_code_list = TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)
        ->where('chart_code','like', $bank_group."%")->orWhere('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->pluck('chart_account_id')->toArray();


        if(!in_array($request->cash_type,$acc_code_list)){
            return $this->jsonErrorResponse($data,"Parent Account Code not correct",200);
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                $del_rvs = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->get();
                foreach ($del_rvs as $del_rv){
                    TblAccoVoucher::where('voucher_id',$del_rv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $form_id = $voucher_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
            $narration = $request->narration;
            $account_id = $request->cash_type;
            $acc_code_fetch = TblAccCoa::where('chart_Account_id',$account_id)->first();
            $account_code = $acc_code_fetch->chart_code;
            $saleman_id = $request->saleman_id;
            $notes = $request->voucher_notes;

            if($request->pd){
                $i = 1;
                foreach($request->pd as $dtl){
                    $exits = TblAccCoa::where('chart_account_id',$dtl['account_id'])->where('chart_code',$dtl['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->jsonErrorResponse($data,"Account Code not correct",200);
                    }

                    //top entry
                    $voucher = new TblAccoVoucher();
                    $voucher->voucher_id = $voucher_id;
                    $voucher->voucher_no  = $voucher_no;
                    $voucher->voucher_type = $voucher_type;
                    $voucher->voucher_sr_no = $i++;
                    $voucher->voucher_date = $voucher_date;
                    $voucher->currency_id = $currency_id;
                    $voucher->voucher_exchange_rate = $exchange_rate;
                    $voucher->saleman_id = $saleman_id;
                    $voucher->voucher_notes = $notes;
                    $voucher->chart_account_id = $account_id;
                    $voucher->chart_code = $account_code;
                    $voucher->voucher_descrip = $narration;

                    $voucher->voucher_payment_mode = $dtl['payment_mode'];
                    $voucher->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucher->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucher->voucher_debit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucher->voucher_fc_debit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucher->voucher_credit = 0;
                    $voucher->voucher_fc_credit = 0;
                    $voucher->business_id = auth()->user()->business_id;
                    $voucher->company_id = auth()->user()->company_id;
                    $voucher->branch_id = auth()->user()->branch_id;

                    if(isset($id))
                    {
                        $voucher->update_user_id = auth()->user()->id;
                        $voucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucher->voucher_user_id = auth()->user()->id;
                    }
                    $voucher->save();

                    //---------grid entry----------

                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $exchange_rate;
                    $voucherDtl->saleman_id = $saleman_id;
                    $voucherDtl->voucher_notes = $notes;
                    $voucherDtl->chart_account_id = $dtl['account_id'];
                    $voucherDtl->chart_code = $dtl['account_code'];

                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_payment_mode = $dtl['payment_mode'];
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_debit = 0;
                    $voucherDtl->voucher_fc_debit = 0;
                    $voucherDtl->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
                }
            }


        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function pveStore(Request $request,$type, $id = null)
    {
        // Expense Voucher
        $data = [];
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if($type == 'pve'){
            $voucher_type = $type;
        }else{
            return $this->jsonErrorResponse($data,"Voucher type not correct",200);
        }
        if($type == 'pve'){
            if($request->tot_pve_difference != 0){
                return $this->jsonErrorResponse($data," Voucher not correct",200);
            }
        }
        if(!isset($request->pd) || count($request->pd) == 0){
            return $this->jsonErrorResponse($data, trans('message.fill_the_grid'), 200);
        }
        if(isset($request->pd)){
            foreach($request->pd as $pd){
                if(!empty($pd['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$pd['account_id'])->where('chart_code',$pd['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->jsonErrorResponse($data," Account Code  not correct",200);
                    }
                }else{
                    return $this->jsonErrorResponse($data," Enter Account Code",200);
                }
            }
        }
        $total_amount = 0;
        foreach($request->pd as $dtl_c){
            $total_amount += $this->addNo($dtl_c['voucher_credit']);
        }
        if(isset($request->duc) && isset($request->is_deduction)){
            foreach($request->duc as $duc_c){
                $total_amount += $this->addNo($duc_c['voucher_credit']);
            }
        }
        $debit_total_amount = 0;
        if(isset($request->debits)){
            foreach($request->debits as $dts){
                $debit_total_amount += $this->addNo($dts['voucher_debit']);
                // dd($debit_total_amount);
            }
        }
        if($total_amount != $debit_total_amount){
            return $this->jsonErrorResponse($data,"Debit Credit amount must be equal",200);
        }
        DB::beginTransaction();
        try {
            $i = 0;
            // if(isset($id)){
            //     $uuid = $id;
            //     $code= TblAccoVoucher::where('voucher_id',$uuid)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
            //     $voucher_no = $code->voucher_no;
            //     $del_jvs = TblAccoVoucher::where('voucher_id',$uuid)->where(Utilities::currentBCB())->get();
            //     foreach ($del_jvs as $del_jv){
            //         TblAccoVoucher::where('voucher_id',$del_jv->voucher_id)->where(Utilities::currentBCB())->delete();
            //     }
            // }else{
            //     $uuid = $this->uuid();
            //     $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
            //     $voucher_no = $this->documentCode($max_voucher,$type);
            // }
            if(isset($id)){
                $voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $user= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_user_id');
                $voucher_user_id = $user->voucher_user_id;
                $del_jvs = TblAccoVoucher::where('voucher_id',$voucher_id)->where(Utilities::currentBCB())->get();
                foreach ($del_jvs as $del_jv){
                    TblAccoVoucher::where('voucher_id',$del_jv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $form_id = $voucher_id;
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $voucher_exchange_rate = $request->exchange_rate;
            $voucher_notes = $request->voucher_notes;
            $payment_mode = isset($request->payment_mode)?$request->payment_mode:'';
            $mode_no = isset($request->mode_no)?$request->mode_no:'';
            $is_deduction = isset($request->is_deduction)?1:0;
            $sr = 1;
            if(isset($request->debits) && count($request->debits) != 0){
                foreach ($request->debits as $inv){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$inv['account_id'])->where('chart_code',$inv['account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct v",200);
                    }
                    $ducVoucher = new TblAccoVoucher();
                    $ducVoucher->voucher_id = $voucher_id;
                    $ducVoucher->voucher_no  = $voucher_no;
                    $ducVoucher->voucher_type = $voucher_type;
                    $ducVoucher->voucher_sr_no = $sr++;
                    $ducVoucher->voucher_date = $voucher_date;
                    $ducVoucher->currency_id = $currency_id;
                    $ducVoucher->voucher_exchange_rate = $voucher_exchange_rate;
                    $ducVoucher->voucher_notes = $voucher_notes;
                    $ducVoucher->chart_account_id = $inv['account_id'];
                    $ducVoucher->chart_code = $inv['account_code'];
                    $ducVoucher->voucher_acc_name = $inv['account_name'];
                    $ducVoucher->is_deduction = $is_deduction;
                    $ducVoucher->voucher_grid_type = 'debit';

                    $ducVoucher->voucher_descrip = $inv['voucher_desc'];
                    $ducVoucher->voucher_debit = isset($inv['voucher_debit'])?$this->addNo($inv['voucher_debit']):0;
                    $ducVoucher->voucher_fc_debit = isset($inv['voucher_debit'])?$this->addNo($inv['voucher_debit']):0;
                    $ducVoucher->voucher_credit = 0;
                    $ducVoucher->voucher_fc_credit = 0;
                    $ducVoucher->business_id = auth()->user()->business_id;
                    $ducVoucher->company_id = auth()->user()->company_id;
                    $ducVoucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $ducVoucher->update_user_id = auth()->user()->id;
                        $ducVoucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $ducVoucher->voucher_user_id = auth()->user()->id;
                    }
                    $ducVoucher->save();
                }
            }

            if(isset($request->is_deduction) && isset($request->duc) && count($request->duc) != 0){
                foreach ($request->duc as $duc){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$duc['account_id'])->where('chart_code',$duc['account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct v",200);
                    }
                    $ducVoucher = new TblAccoVoucher();
                    $ducVoucher->voucher_id = $voucher_id;
                    $ducVoucher->voucher_no  = $voucher_no;
                    $ducVoucher->voucher_type = $voucher_type;
                    $ducVoucher->voucher_sr_no = $sr++;
                    $ducVoucher->voucher_date = $voucher_date;
                    $ducVoucher->currency_id = $currency_id;
                    $ducVoucher->voucher_exchange_rate = $voucher_exchange_rate;
                    $ducVoucher->voucher_notes = $voucher_notes;
                    $ducVoucher->chart_account_id = $duc['account_id'];
                    $ducVoucher->chart_code = $duc['account_code'];
                    $ducVoucher->voucher_acc_name = $duc['account_name'];
                    $ducVoucher->chart_code = $exitsAcc->chart_code;
                    $ducVoucher->is_deduction = $is_deduction;
                    $ducVoucher->voucher_grid_type = 'deduction';

                    $ducVoucher->voucher_descrip = $duc['voucher_narration'];
                    $ducVoucher->voucher_debit = 0;
                    $ducVoucher->voucher_fc_debit = 0;
                    $ducVoucher->voucher_credit = $this->addNo($duc['voucher_credit']);
                    $ducVoucher->voucher_fc_credit = $this->addNo($duc['voucher_credit']);
                    $ducVoucher->business_id = auth()->user()->business_id;
                    $ducVoucher->company_id = auth()->user()->company_id;
                    $ducVoucher->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $ducVoucher->update_user_id = auth()->user()->id;
                        $ducVoucher->voucher_user_id = $voucher_user_id;
                    }else{
                        $ducVoucher->voucher_user_id = auth()->user()->id;
                    }
                    $ducVoucher->save();
                }
            }

            if(isset($request->pd) && count($request->pd) != 0){
                foreach($request->pd as $dtl){
                    $exitsAcc = TblAccCoa::where('chart_account_id',$dtl['account_id'])->where('chart_code',$dtl['account_code'])->where(Utilities::currentBC())->first();
                    if (empty($exitsAcc)) {
                        return $this->jsonErrorResponse($data,"Account Code not correct v",200);
                    }

                    $voucherDtl = new TblAccoVoucher();
                    $voucherDtl->voucher_id = $voucher_id;
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $sr++;
                    $voucherDtl->voucher_date = $voucher_date;
                    $voucherDtl->currency_id = $currency_id;
                    $voucherDtl->voucher_exchange_rate = $voucher_exchange_rate;
                    $voucherDtl->voucher_notes = $voucher_notes;
                    $voucherDtl->chart_account_id = $dtl['account_id'];
                    $voucherDtl->chart_code = $dtl['account_code'];
                    $voucherDtl->voucher_acc_name = $dtl['account_name'];
                    $voucherDtl->chart_code = $exitsAcc->chart_code;
                    $voucherDtl->is_deduction = $is_deduction;
                    $voucherDtl->voucher_grid_type = 'credit';

                    $voucherDtl->bank_id = $dtl['supplier_bank_id'];
                    $voucherDtl->bank_branch_code = $dtl['bank_branch_code'];
                    $voucherDtl->tbl_supplier_account_id = $dtl['supplier_account_id'];
                    $voucherDtl->voucher_descrip = $dtl['voucher_descrip'];
                    $voucherDtl->voucher_payment_mode = $dtl['payment_mode'];
                    $voucherDtl->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucherDtl->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucherDtl->voucher_payee_title = isset($dtl['voucher_payee_title'])?$dtl['voucher_payee_title']:"";
                    $voucherDtl->voucher_debit = 0;
                    $voucherDtl->voucher_fc_debit = 0;
                    $voucherDtl->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    if(isset($id))
                    {
                        $voucherDtl->update_user_id = auth()->user()->id;
                        $voucherDtl->voucher_user_id = $voucher_user_id;
                    }else{
                        $voucherDtl->voucher_user_id = auth()->user()->id;
                    }
                    $voucherDtl->save();
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
            return $this->jsonErrorResponse($data, $e->getLine(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.'accounts/'.$type;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/accounts/'.$type.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function getInvDetails(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'supplier_account_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $supplier_account_id = $request->supplier_account_id;
            $exits = TblAccCoa::where('chart_account_id',$supplier_account_id)->where(Utilities::currentBC())->exists();
            if (!$exits) {
                return $this->jsonErrorResponse($data,"Account Code not correct",200);
            }
            $allData = ViewPurcGrnPayments::where('supplier_account_id',$supplier_account_id)
            ->whereColumn('grn_total_net_amount','!=','paid_amount')
            ->where(Utilities::currentB())->orderBy('grn_code', 'ASC')->orderBy('grn_date', 'desc')->get();

            $data['list'] = [];
            $data['list_pr'] = [];
            foreach ($allData as $row){
               if(strtolower($row->grn_type) == 'grn'){
                   $data['list'][] = $row;
               }
               if(strtolower($row->grn_type) == 'pr' || strtolower($row->grn_type) == 'jv'){
                   $data['list_pr'][] = $row;
               }
            }

        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, 'Data load..', 200);
    }

    public function getReceiveInvDetails(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'supplier_account_id' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $supplier_account_id = $request->supplier_account_id;
            $exits = TblAccCoa::where('chart_account_id',$supplier_account_id)->where(Utilities::currentBC())->exists();
            if (!$exits) {
                return $this->jsonErrorResponse($data,"Account Code not correct",200);
            }
            $data['list'] = ViewPurcGrnPayments::where('supplier_account_id',$supplier_account_id)
            ->whereColumn('grn_total_net_amount','!=','paid_amount')
            ->where(Utilities::currentB())->orderBy('grn_code', 'ASC')->orderBy('grn_date', 'desc')->get();
            // dd($data['list']->toArray());

        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, 'Data load..', 200);
    }

    public function getBillListdata(Request $request){

        $chart_account_id = $request->account_id;
        $data = [];
        $items = DB::table('vw_purc_grn_vouch_balance')
            ->where('chart_account_id',$chart_account_id)
            // ->where(Utilities::currentBC())
            ->orderBy('grn_date')->get();

        $data['items'] = $items;
        return response()->json(['data'=>$data,'status'=>'success']);
    }


    public function getTerminalBySaleman(Request $request)
    {
        $salesman_id = $request->salesman_id;
        $voucher_date = date('Y-m-d', strtotime($request->voucher_date));

        $terminal = ViewSaleSalesInvoice::where('sales_entry_status' , 1)
            ->whereDate('sales_date' , $voucher_date)
            ->where('sales_sales_man' , $salesman_id)
            ->where('terminal_name' ,"<>", NULL)
            ->groupBy(['terminal_id','terminal_name','sales_date'])
            ->orderBy('sales_date','desc')
            ->select('terminal_id','terminal_name')
            ->get();

        // $terminal = DB::select("SELECT TERMINAL_ID, TERMINAL_NAME
        // FROM VW_SALE_SALES_INVOICE
        // WHERE SALES_ENTRY_STATUS = 1
        // AND SALES_DATE = to_date('2024-05-11', 'yyyy/mm/dd')
        // AND SALES_SALES_MAN = 10712723301437
        // AND TERMINAL_NAME IS NOT NULL
        // GROUP BY TERMINAL_ID, TERMINAL_NAME,SALES_DATE
        // ORDER BY SALES_DATE DESC");


        if(count($terminal) > 0){
            return $this->jsonSuccessResponse($terminal , 'Success');
        }else{
            return $this->jsonErrorResponse([],'No Area Found');
        }
    }

    public function getAccByTerminal(Request $request)
    {
        $terminal_id = $request->terminal_id;

        $acc_code = ViewPOSSoftTerminal::where('terminal_id' , $terminal_id)
            ->groupBy(['chart_id','chart_name'])
            ->orderBy('chart_name')
            ->get(['chart_id','chart_name']);


        if(count($acc_code) > 0){
            return $this->jsonSuccessResponse($acc_code , 'Success');
        }else{
            return $this->jsonErrorResponse([],'No Account Code Found');
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
    public function edit($type,$id)
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
    public function update(Request $request, $type,$id )
    {
        //
    }

    public function print($type,$id)
    {
        $data['type'] = $type;
        switch ($type){
            case 'jv': {
                $data['title'] = 'Journal Voucher';
                $formUrl = 'acc.jv_obv_print';
                $data['stock_menu_id'] = '31';
                break;
            }
            case 'obv': {
                $data['title'] = 'Opening Balance Voucher';
                $formUrl = 'acc.jv_obv_print';
                $data['stock_menu_id'] = '62';
                break;
            }
            case 'crv': {
                $data['title'] = 'Cash Withdrawal Voucher';
                $formUrl = 'acc.cash_withdrawl_print';
                $data['stock_menu_id'] = '28';
                break;
            }
            case 'cpv': {
                $data['title'] = 'Cash Deposit Voucher';
                $formUrl = 'acc.cash_deposit_print';
                $data['stock_menu_id'] = '37';
                break;
            }
            case 'brv': {
                $data['title'] = 'Bank Received Voucher';
                $formUrl = 'acc.bank_voucher_print';
                $data['stock_menu_id'] = '29';
                break;
            }
            case 'bpv': {
                $data['title'] = 'Bank Payment Voucher';
                $formUrl = 'acc.bank_voucher_print';
                $data['stock_menu_id'] = '36';
                break;
            }
            case 'lv': {
                $data['title'] = 'Liability Voucher';
                $formUrl = 'acc.lv_print';
                $data['stock_menu_id'] = '138';
                break;
            }
            case 'lfv': {
                $data['title'] = 'Listing Fee Voucher';
                $formUrl = 'acc.cash_voucher_print';
                $data['stock_menu_id'] = '171';
                break;
            }
            case 'brpv': {
                $data['title'] = 'Branch Payment Voucher';
                $formUrl = 'acc.branch_payment_voucher_print';
                $data['stock_menu_id'] = '269';
                break;
            }
            case 'brrv': {
                $data['title'] = 'Branch Receive Voucher';
                $formUrl = 'acc.branch_recieve_voucher_print';
                $data['stock_menu_id'] = '274';
                break;
            }
            case 'pv': {
                $data['title'] = 'Vendor Payment Voucher';
                $formUrl = 'acc.vendor_payment_voucher_print';
                $data['stock_menu_id'] = '270';
                break;
            }
            case 'rv': {
                $data['title'] = 'Receiving Voucher';
                $formUrl = 'acc.receiving_voucher_print';
                $data['stock_menu_id'] = '286';
                break;
            }
            case 'ipv': {
                $data['title'] = 'Internal Payment Voucher';
                $formUrl = 'acc.internal_payment_print';
                $data['stock_menu_id'] = '271';
                break;
            }
            case 'irv': {
                $data['title'] = 'Internal Receive Voucher';
                $formUrl = 'acc.internal_receive_print';
                $data['stock_menu_id'] = '272';
                break;
            }
            case 'pve': {
                $data['title'] = 'Expense Voucher';
                $formUrl = 'acc.expense_voucher';
                $data['stock_menu_id'] = '273';
                break;
            }
            case 'siv': {
                $data['title'] = 'Sale Invoice';
                $formUrl = 'acc.day_closing_voucher';
                $data['stock_menu_id'] = '64';
                break;
            }
            case 'ccd': {
                $data['title'] = 'Cash Closing Deposit';
                $formUrl = 'acc.day_closing_voucher';
                $data['stock_menu_id'] = '64';
                break;
            }
        }
        if(isset($id)){
            if(TblAccoVoucher::where('voucher_id','LIKE',$id)->exists()){
                $data['permission'] = $data['stock_menu_id'].'-print';
                $data['current'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_sr_no','=','1')->where(Utilities::currentBCB())->first();
                if($type =='brv'){
                    //Voucher Credit
                    $data['dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_credit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else if($type == 'cpv' || $type =='bpv'){
                    $data['current'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first();
                    $data['dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                    // $data['dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_credit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                    // dd($data['dtl']->toArray());
                }else if($type == 'pve'){
                    $data['current'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first();
                    $data['dtl'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','debit')->where('voucher_id',$id)->where('voucher_type',$type)->orderBy('voucher_sr_no', 'asc')->where(Utilities::currentBCB())->get();
                    $data['deduction'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','deduction')->where('voucher_id',$id)->where('voucher_type',$type)->orderBy('voucher_sr_no', 'asc')->where(Utilities::currentBCB())->get();
                    $data['credit'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','credit')->where('voucher_id',$id)->where('voucher_type',$type)->orderBy('voucher_sr_no', 'asc')->where(Utilities::currentBCB())->get();
                    $data['chq_dtl'] = TblAccoVoucher::with('accounts','payment_mode')->where('voucher_id',$id)->where('voucher_grid_type','credit')->where(Utilities::currentBCB())->get();

                }else if($type == 'pv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts','bank','payment_mode')->where('voucher_id',$id)->where('voucher_type',$type)->orderBy('voucher_sr_no', 'desc')->where(Utilities::currentBCB())->get();
                    $data['vendor'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','vendor')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->select(DB::raw("SUM(voucher_debit) AS debit"), DB::raw("SUM(voucher_credit) AS credit"),'chart_account_id')->groupBy('chart_account_id')->get();
                    $data['deduction'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','deduction')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->select(DB::raw("SUM(voucher_debit) AS debit"), DB::raw("SUM(voucher_credit) AS credit"),'chart_account_id')->groupBy('chart_account_id')->get();
                    $data['actual'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','actual')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->select(DB::raw("SUM(voucher_debit) AS debit"), DB::raw("SUM(voucher_credit) AS credit"),'chart_account_id')->groupBy('chart_account_id')->get();
                    $data['chq_dtl'] = TblAccoVoucher::with('accounts','payment_mode','bank')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->get();
                    $data['voucher_bill'] = TblAccoVoucherBillDtl::where('voucher_id',$id)->where('voucher_bill_grn_paid_status',1)->where('voucher_type','!=','PR')->where(Utilities::currentBCB())->get();
                    $data['vendor_name'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_grid_type','vendor')->where(Utilities::currentBCB())->first();
                }else if($type == 'rv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts','bank','payment_mode')->where('voucher_id',$id)->where('voucher_type',$type)->orderBy('voucher_sr_no', 'desc')->where(Utilities::currentBCB())->get();
                    $data['vendor'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','vendor')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->select(DB::raw("SUM(voucher_debit) AS debit"), DB::raw("SUM(voucher_credit) AS credit"),'chart_account_id')->groupBy('chart_account_id')->get();
                    $data['deduction'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','deduction')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->select(DB::raw("SUM(voucher_debit) AS debit"), DB::raw("SUM(voucher_credit) AS credit"),'chart_account_id')->groupBy('chart_account_id')->get();
                    $data['actual'] = TblAccoVoucher::with('accounts')->where('voucher_grid_type','actual')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->select(DB::raw("SUM(voucher_debit) AS debit"), DB::raw("SUM(voucher_credit) AS credit"),'chart_account_id')->groupBy('chart_account_id')->get();
                    $data['chq_dtl'] = TblAccoVoucher::with('accounts','payment_mode')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->get();
                    $data['voucher_bill'] = TblAccoVoucherBillDtl::where('voucher_id',$id)->where('voucher_bill_grn_paid_status',1)->where(Utilities::currentBCB())->get();
                    $data['vendor_name'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_grid_type','vendor')->where(Utilities::currentBCB())->first();
                }else if($type == 'brrv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts','bank','payment_mode','voucher_bill')->where('voucher_id',$id)->where('voucher_grid_type','branch')->where('voucher_credit','!=','0')->where('voucher_type',$type)->orderBy('voucher_sr_no', 'ASC')->where(Utilities::currentBCB())->get();
                    $data['chq_dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->get();
                }else{
                    $data['dtl'] = TblAccoVoucher::with('accounts','bank','payment_mode')->where('voucher_id',$id)->where('voucher_type',$type)->orderBy('voucher_debit', 'desc')->where(Utilities::currentBCB())->get();
                    $data['chq_dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_grid_type','actual')->where(Utilities::currentBCB())->get();
                }
            }else{
                abort('404');
            }
        }
        if(isset($data['current']->saleman_id)){
            $data['users'] = User::where('id',$data['current']->saleman_id)->where('user_entry_status',1)->where(Utilities::currentBC())->first();
        }
        if(isset($data['current']->currency_id)){
            $data['currency']  = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where('currency_entry_status',1)->where(Utilities::currentBC())->first();
        }
        return view('prints.'.$formUrl,compact('data'));
    }


    public function voucherpost(Request $request)
    {
        $voucher_id  = $request->voucher_id;
        $data = [];

        //$data['locations'] = ViewInveDisplayLocation::where('branch_id',auth()->user()->branch_id)->where('store_id',$store_id)->orderBy('display_location_name_string')->get();

        if(!empty($voucher_id)){
            $row = TblAccoVoucher::where('voucher_id',$voucher_id)->where('branch_id',auth()->user()->branch_id)->first();
            $row->voucher_posted = 1;
            $row->update();

            $data['status'] = 'success';
        }else{
            $data['status'] = 'error';
        }
        return response()->json($data);
    }

    public function VoucherUnPosted(Request $request)
    {
        $data = [];
        $voucher_id  = $request->data[0];

        if(TblAccoVoucher::where('voucher_id','LIKE',$voucher_id)->exists())
        {
            $row = TblAccoVoucher::where('voucher_id',$voucher_id)->where('branch_id',auth()->user()->branch_id)->first();
            $row->voucher_posted = 0;
            $row->update();

            return $this->jsonSuccessResponse($data, trans('Successfully Un-Posted'), 200);
        }else{
            abort('404');
        }

        return response()->json(['status'=>'success']);
    }

    public function VoucherPosted(Request $request)
    {
        $data = [];
        $voucher_id  = $request->data[0];

        if(TblAccoVoucher::where('voucher_id','LIKE',$voucher_id)->exists())
        {
            $row = TblAccoVoucher::where('voucher_id',$voucher_id)->where('branch_id',auth()->user()->branch_id)->first();
            $row->voucher_posted = 1;
            $row->update();

            return $this->jsonSuccessResponse($data, trans('Successfully Posted'), 200);
        }else{
            abort('404');
        }

        return response()->json(['status'=>'success']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($type,$id)
    {
        $data = [];
        DB::beginTransaction();
        try{

            $data['rec_type'] =  TblAccoVoucher::where('voucher_type',$type)->where('voucher_id',$id)->where(Utilities::currentBCB())->first();
            if($data['rec_type']->bank_rec_posted == 1)
            {
                return $this->jsonErrorResponse($data, 'This Voucher enter in BRS', 200);
            }

            if(TblAccoVoucher::where('voucher_type',$type)->where('voucher_id',$id)->where(Utilities::currentBCB())->exists()){
                TblAccoVoucherBillDtl::where('voucher_id',$id)->where(Utilities::currentBCB())->delete();
                TblAccoVoucher::where('voucher_id',$id)->where(Utilities::currentBCB())->delete();
            }else{
                return $this->jsonErrorResponse($data, "Invalid Voucher No.", 200);
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

}
