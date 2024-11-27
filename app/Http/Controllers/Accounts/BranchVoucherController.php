<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\TblAccoVoucher;
use App\Models\TblAccCoa;
use App\Models\TblAccoVoucherBillDtl;
use App\Models\TblDefiCurrency;
use App\Models\User;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use App\Models\Rent\TblRentAgreementDtl;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BranchVoucherController extends Controller
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
    public function brpvstore(Request $request, $type,$id = null){
        $data = [];
        if(!in_array($type,['brpv'])){
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

        $acc_code_list = TblAccCoa::where('chart_level', '=',4)->where('chart_code','like', "3-01-03-%")
                ->where(Utilities::currentBC())->pluck('chart_account_id')->toArray();

        if(!in_array($request->cash_type,$acc_code_list)){
            return $this->jsonErrorResponse($data,"Parent Account Code not correct",200);
        }

        DB::beginTransaction();
        try{
            $voucher_type = 'brpv';
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
            $voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $currency_id = $request->currency_id;
            $exchange_rate = $request->exchange_rate;
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
                    $voucher->voucher_sr_no = ++$i;
                    $voucher->voucher_date = $voucher_date;
                    $voucher->currency_id = $currency_id;
                    $voucher->voucher_exchange_rate = $exchange_rate;
                    $voucher->saleman_id = $saleman_id;
                    $voucher->voucher_notes = $notes;
                    $voucher->chart_account_id = $account_id;
                    $voucher->chart_code = $account_code;

                    $voucher->voucher_descrip = $dtl['voucher_descrip'];
                    $voucher->voucher_payment_mode = $dtl['payment_mode'];
                    $voucher->voucher_mode_no = isset($dtl['mode_no'])?$dtl['mode_no']:'';
                    $voucher->voucher_mode_date = isset($dtl['mode_date'])?date('Y-m-d', strtotime($dtl['mode_date'])):'';
                    $voucher->voucher_debit = 0;
                    $voucher->voucher_fc_debit = 0;
                    $voucher->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucher->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucher->business_id = auth()->user()->business_id;
                    $voucher->company_id = auth()->user()->company_id;
                    $voucher->branch_id = auth()->user()->branch_id;
                    $voucher->voucher_user_id = auth()->user()->id;
                    $voucher->save();

                    //---------grid entry----------

                    $voucherDtl = new TblAccoVoucher();
                    $voucher = new TblAccoVoucher();
                    $voucher->voucher_id = $voucher_id;
                    $voucher->voucher_no  = $voucher_no;
                    $voucher->voucher_type = $voucher_type;
                    $voucher->voucher_sr_no = ++$i;
                    $voucher->voucher_date = $voucher_date;
                    $voucher->currency_id = $currency_id;
                    $voucher->voucher_exchange_rate = $exchange_rate;
                    $voucher->saleman_id = $saleman_id;
                    $voucher->voucher_notes = $notes;
                    $voucher->chart_account_id = $dtl['account_id'];
                    $voucher->chart_code = $dtl['account_code'];

                    $voucher->voucher_descrip = $dtl['voucher_descrip'];
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
                    $voucher->voucher_user_id = auth()->user()->id;
                    $voucher->save();
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
    public function create(Request $request, $type,$id = null)
    {
        $chart_cash_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->cash_group)->where(Utilities::currentBC())->first('chart_code');
        $chart_bank_group = TblAccCoa::where('chart_Account_id',Session::get('dataSession')->bank_group)->where(Utilities::currentBC())->first('chart_code');
        $cash_group = substr($chart_cash_group->chart_code,0,7);
        $bank_group = substr($chart_bank_group->chart_code,0,7);

        $data['page_data'] = [];
        $data['type'] = $type;
        switch ($type){
            case 'jv': {
                $data['page_data']['title'] = 'Journal';
                $formUrl = 'jv';
                $data['stock_menu_id'] = '31';
                break;
            }
            case 'obv': {
                $data['page_data']['title'] = 'Opening Balance';
                $formUrl = 'jv';
                $data['stock_menu_id'] = '62';
                break;
            }
            case 'crv': {
                $data['page_data']['title'] = 'Cash Received';
                $formUrl = 'cash_voucher';
                $data['stock_menu_id'] = '28';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'cpv': {
                $data['page_data']['title'] = 'Cash Payment';
                $formUrl = 'cash_voucher';
                $data['stock_menu_id'] = '37';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $cash_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'brv': {
                $data['page_data']['title'] = 'Bank Received';
                $formUrl = 'bank_voucher';
                $data['stock_menu_id'] = '29';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $bank_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'bpv': {
                $data['page_data']['title'] = 'Bank Payment';
                $formUrl = 'bank_voucher';
                $data['stock_menu_id'] = '36';
                $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', $bank_group."%")->where(Utilities::currentBC())->orderBy('chart_name')->get();
                break;
            }
            case 'ctrv': {
                $data['page_data']['title'] = 'Contra Voucher';
                $formUrl = 'ctrv';
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
                break;
            }
        }
        $data['page_data']['path_index'] = $this->prefixIndexPage.'accounts/'.$type;
        $data['page_data']['create'] = '/accounts/'.$type.$this->prefixCreatePage;
        if(isset($id)){
            if(TblAccoVoucher::where('voucher_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = $data['stock_menu_id'].'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_sr_no','=','1')->where(Utilities::currentBCB())->first();
                if($type == 'crv' || $type =='brv' || $type =='lfv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_credit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else if($type == 'cpv' || $type =='bpv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_debit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else{
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }
                $data['voucher_no'] = $data['current']->voucher_no;
                $data['page_data']['print'] = '/accounts/'.$type.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
            $data['voucher_no'] = $this->documentCode($max_voucher,$type);
        }
        $data['users'] = User::where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
        $data['currency']  = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
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
        return view('accounts.'.$formUrl.'.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

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
                $formUrl = 'jv_obv_print';
                $data['stock_menu_id'] = '31';
                break;
            }
            case 'obv': {
                $data['title'] = 'Opening Balance';
                $formUrl = 'jv_obv_print';
                $data['stock_menu_id'] = '56';
                break;
            }
            case 'crv': {
                $data['title'] = 'Cash Received Voucher';
                $formUrl = 'cash_voucher_print';
                $data['stock_menu_id'] = '28';
                break;
            }
            case 'cpv': {
                $data['title'] = 'Cash Payment Voucher';
                $formUrl = 'cash_voucher_print';
                $data['stock_menu_id'] = '37';
                break;
            }
            case 'brv': {
                $data['title'] = 'Bank Received Voucher';
                $formUrl = 'bank_voucher_print';
                $data['stock_menu_id'] = '29';
                break;
            }
            case 'bpv': {
                $data['title'] = 'Bank Payment Voucher';
                $formUrl = 'bank_voucher_print';
                $data['stock_menu_id'] = '36';
                break;
            }
            case 'ctrv': {
                $data['title'] = 'Contra Voucher';
                $formUrl = 'jv_obv_print';
                $data['stock_menu_id'] = '138';
                break;
            }
            case 'lfv': {
                $data['title'] = 'Listing Fee Voucher';
                $formUrl = 'cash_voucher_print';
                $data['stock_menu_id'] = '171';
                break;
            }
        }
        if(isset($id)){
            if(TblAccoVoucher::where('voucher_id','LIKE',$id)->exists()){
                $data['permission'] = $data['stock_menu_id'].'-print';
                $data['current'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_sr_no','=','1')->where(Utilities::currentBCB())->first();
                if($type == 'crv' || $type =='brv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_credit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else if($type == 'cpv' || $type =='bpv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_debit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else{
                    $data['dtl'] = TblAccoVoucher::with('accounts')->where('voucher_id',$id)->where('voucher_type',$type)->orderBy('voucher_sr_no', 'ASC')->where(Utilities::currentBCB())->get();
                }
            }else{
                abort('404');
            }
        }
        $data['users'] = User::where('id',$data['current']->saleman_id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
        $data['currency']  = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where('currency_entry_status',1)->where(Utilities::currentBC())->first();
        return view('prints.'.$formUrl,compact('data'));
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

            $voucher = TblAccoVoucher::where('voucher_type',$type)->where('voucher_id',$id)->where(Utilities::currentBCB())->get();
            foreach ($voucher as $vch){
                TblAccoVoucher::where('voucher_id',$vch->voucher_id)->where(Utilities::currentBCB())->delete();
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
