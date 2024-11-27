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
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type,$id = null)
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
            case 'lv': {
                $data['page_data']['title'] = 'Liability Voucher';
                $formUrl = 'lv';
                $data['stock_menu_id'] = '138';
                break;
            }
        }
        $data['page_data']['path_index'] = $this->prefixIndexPage.'accounts/'.$type;
        if(isset($id)){
            if(TblAccoVoucher::where('voucher_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = $data['stock_menu_id'].'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_sr_no','=','1')->where(Utilities::currentBCB())->first();
                if($type == 'crv' || $type =='brv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_credit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else if($type == 'cpv' || $type =='bpv'){
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where('voucher_debit','!=','0')->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }else{
                    $data['dtl'] = TblAccoVoucher::with('accounts','voucher_bill')->where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->orderBy('voucher_sr_no', 'ASC')->get();
                }
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
        if($type == 'crv' || $type == 'brv'){
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
            $i = 1;
            $voucher = new TblAccoVoucher();
            if(isset($id)){
                $voucher->voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $del_rvs = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->get();
                foreach ($del_rvs as $del_rv){
                    TblAccoVoucher::where('voucher_id',$del_rv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher->voucher_id = Utilities::uuid();
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
            $voucher_notes = $request->voucher_notes;
            $cashcode= TblAccCoa::select('chart_Account_id')->where('chart_level', '=',4)->where('chart_code', $request->cash_type)->where(Utilities::currentBC())->first();
            $voucher->chart_account_id = $cashcode->chart_account_id;
            $voucher->voucher_no  = $voucher_no;
            $voucher->voucher_type = $voucher_type;
            $voucher->voucher_sr_no = $i;
            $voucher->voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $voucher->voucher_payment_mode = isset($request->payment_mode)?$request->payment_mode:'';
            $voucher->voucher_mode_no = isset($request->mode_no)?$request->mode_no:'';
            $voucher->saleman_id = $request->saleman_id;
            $voucher->currency_id = $request->currency_id;
            $voucher->voucher_exchange_rate = $request->exchange_rate;
            $voucher->voucher_descrip = $request->narration;
            $voucher->chart_code = $request->cash_type;
            $voucher->voucher_notes = $voucher_notes;
            $voucher->voucher_credit = 0;
            $voucher->voucher_fc_credit = 0;
            if($type == 'brv'){
                $voucher->voucher_payment_mode = isset($request->pd[1]['payment_mode'])?$request->pd[1]['payment_mode']:'';
                $voucher->voucher_mode_no = isset($request->pd[1]['mode_no'])?$request->pd[1]['mode_no']:'';
                $voucher->voucher_mode_date = isset($request->pd[1]['mode_date'])?date('Y-m-d', strtotime($request->pd[1]['mode_date'])):'';
                $voucher->voucher_debit = isset($request->pd[1]['voucher_credit'])?$this->addNo($request->pd[1]['voucher_credit']):0;
                $voucher->voucher_fc_debit = isset($request->pd[1]['voucher_fc_credit'])?$this->addNo($request->pd[1]['voucher_fc_credit']):0;
            }else{
                $voucher->voucher_debit = isset($request->tot_voucher_credit)?$request->tot_voucher_credit:0;
                $voucher->voucher_fc_debit = isset($request->tot_voucher_fccredit)?$request->tot_voucher_fccredit:0;
            }
            $voucher->business_id = auth()->user()->business_id;
            $voucher->company_id = auth()->user()->company_id;
            $voucher->branch_id = auth()->user()->branch_id;
            $voucher->voucher_user_id = auth()->user()->id;
            $voucher->save();
            $tot_voucher_amt = 0;
            if($request->pd){
                foreach($request->pd as $dtl){
                    $i++;
                    $voucherDtl = new TblAccoVoucher();
                    if(isset($id)){
                        $voucherDtl->voucher_id = $id;
                    }else{
                        $voucherDtl->voucher_id = $voucher->voucher_id;
                    }
                    $voucherDtl->voucher_date = date('Y-m-d', strtotime($request->voucher_date));
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i;
                    $voucherDtl->chart_account_id = $dtl['account_id'];
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
                    $voucherDtl->voucher_credit = isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    $voucherDtl->voucher_fc_credit = isset($dtl['voucher_fc_credit'])?$this->addNo($dtl['voucher_fc_credit']):0;
                    $voucherDtl->voucher_debit = 0;
                    $voucherDtl->voucher_fc_debit = 0;
                    $voucherDtl->voucher_notes = $voucher_notes;
                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    $voucherDtl->voucher_user_id = auth()->user()->id;
                    $voucherDtl->save();
                    $tot_voucher_amt += isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    //if($type == 'brv'){
                    //    break;
                    //}
                }
            }
            $voucher_id = $voucher->voucher_id;
            if(isset($id)){
                TblAccoVoucherBillDtl::where('voucher_id',$voucher_id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->bl)){
                $total_curr_pay = 0;
                foreach ($request->bl as $k=>$bl){
                    $billDtl =  new TblAccoVoucherBillDtl();
                    $billDtl->voucher_bill_sr_no = $k;
                    $billDtl->voucher_bill_id   = Utilities::uuid();
                    $billDtl->voucher_id   = $voucher_id;
                    $billDtl->voucher_document_id   = $bl['grn_id'];
                    $billDtl->voucher_document_date   = date('Y-m-d', strtotime($bl['grn_date']));
                    $billDtl->voucher_document_code   = $bl['grn_code'];
                    $billDtl->voucher_document_ref   = $bl['grn_bill_no'];
                    $billDtl->voucher_bill_amount   = $this->addNo($bl['grn_amount']);
                    $billDtl->voucher_bill_bal_amount   = $this->addNo($bl['balance_amount']);
                    $billDtl->voucher_bill_rec_amount   = $this->addNo($bl['curr_pay']);
                    $billDtl->voucher_bill_net_bal_amount   = $this->addNo($bl['net_balance']);
                    $billDtl->voucher_bill_marke   = isset($bl['marked'])?1:0;
                    $billDtl->business_id = auth()->user()->business_id;
                    $billDtl->company_id = auth()->user()->company_id;
                    $billDtl->branch_id = auth()->user()->branch_id;
                    $billDtl->save();
                    $total_curr_pay += isset($bl['curr_pay'])?$this->addNo($bl['curr_pay']):0;
                }

                /*if($total_curr_pay != $tot_voucher_amt){
                    return $this->jsonErrorResponse([], 'Bill list not tally with voucher', 200);
                }*/
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
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }

    }
    public function pvstore(Request $request,$type, $id = null)
    {
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
            $i = 1;
            $voucher = new TblAccoVoucher();
            if(isset($id)){
                $voucher->voucher_id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $del_rvs = TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->get();
                foreach ($del_rvs as $del_rv){
                    TblAccoVoucher::where('voucher_id',$del_rv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $voucher->voucher_id = Utilities::uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $cashcode= TblAccCoa::select('chart_Account_id')->where('chart_level', '=',4)->where('chart_code', $request->cash_type)->where(Utilities::currentBC())->first();
            $voucher->chart_account_id = $cashcode->chart_account_id;
            $voucher->voucher_no  = $voucher_no;
            $voucher->voucher_type = $voucher_type;
            $voucher->voucher_sr_no = $i;
            $voucher->voucher_date = date('Y-m-d', strtotime($request->voucher_date));
            $voucher->voucher_payment_mode = isset($request->payment_mode)?$request->payment_mode:'';
            $voucher->voucher_mode_no = isset($request->mode_no)?$request->mode_no:'';
            $voucher->saleman_id = $request->saleman_id;
            $voucher->currency_id = $request->currency_id;
            $voucher->voucher_exchange_rate = $request->exchange_rate;
            $voucher->voucher_descrip = $request->narration;
            $voucher->chart_code = $request->cash_type;
            $voucher->voucher_notes = $request->voucher_notes;
            $voucher->voucher_debit = 0;
            $voucher->voucher_fc_debit = 0;
            if($type == 'bpv'){
                $voucher->voucher_payment_mode = isset($request->pd[1]['payment_mode'])?$request->pd[1]['payment_mode']:'';
                $voucher->voucher_mode_no = isset($request->pd[1]['mode_no'])?$request->pd[1]['mode_no']:'';
                $voucher->voucher_mode_date = isset($request->pd[1]['mode_date'])?date('Y-m-d', strtotime($request->pd[1]['mode_date'])):'';
                $voucher->voucher_credit = isset($request->pd[1]['voucher_credit'])?$this->addNo($request->pd[1]['voucher_credit']):0;
                $voucher->voucher_fc_credit = isset($request->pd[1]['voucher_fc_credit'])?$this->addNo($request->pd[1]['voucher_fc_credit']):0;
            }else{
                $voucher->voucher_fc_credit = isset($request->tot_voucher_fccredit)?$this->addNo($request->tot_voucher_fccredit):0;
                $voucher->voucher_credit = isset($request->tot_voucher_credit)?$this->addNo($request->tot_voucher_credit):0;
            }
            $voucher->business_id = auth()->user()->business_id;
            $voucher->company_id = auth()->user()->company_id;
            $voucher->branch_id = auth()->user()->branch_id;
            $voucher->voucher_user_id = auth()->user()->id;
            $voucher->save();
            $tot_voucher_amt = 0;
            if($request->pd){
                foreach($request->pd as $dtl){
                    $i++;
                    $voucherDtl = new TblAccoVoucher();
                    if(isset($id)){
                        $voucherDtl->voucher_id = $id;
                    }else{
                        $voucherDtl->voucher_id = $voucher->voucher_id;
                    }
                    $voucherDtl->voucher_date = date('Y-m-d', strtotime($request->voucher_date));
                    $voucherDtl->voucher_no  = $voucher_no;
                    $voucherDtl->voucher_type = $voucher_type;
                    $voucherDtl->voucher_sr_no = $i;
                    $voucherDtl->chart_account_id = $dtl['account_id'];
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

                    $voucherDtl->business_id = auth()->user()->business_id;
                    $voucherDtl->company_id = auth()->user()->company_id;
                    $voucherDtl->branch_id = auth()->user()->branch_id;
                    $voucherDtl->voucher_user_id = auth()->user()->id;
                    $voucherDtl->save();
                    $tot_voucher_amt += isset($dtl['voucher_credit'])?$this->addNo($dtl['voucher_credit']):0;
                    //if($type == 'bpv'){
                     //   break;
                    //}
                }
            }
            $voucher_id = $voucher->voucher_id;

            if(isset($id)){
                TblAccoVoucherBillDtl::where('voucher_id',$voucher_id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->bl)){
                $total_curr_pay = 0;
                foreach ($request->bl as $k=>$bl){
                    $billDtl =  new TblAccoVoucherBillDtl();
                    $billDtl->voucher_bill_sr_no = $k;
                    $billDtl->voucher_bill_id   = Utilities::uuid();
                    $billDtl->voucher_id   = $voucher_id;
                    $billDtl->voucher_document_id   = $bl['grn_id'];
                    $billDtl->voucher_document_date   = date('Y-m-d', strtotime($bl['grn_date']));
                    $billDtl->voucher_document_code   = $bl['grn_code'];
                    $billDtl->voucher_document_ref   = $bl['grn_bill_no'];
                    $billDtl->voucher_bill_amount   = $this->addNo($bl['grn_amount']);
                    $billDtl->voucher_bill_bal_amount   = $this->addNo($bl['balance_amount']);
                    $billDtl->voucher_bill_rec_amount   = $this->addNo($bl['curr_pay']);
                    $billDtl->voucher_bill_net_bal_amount   = $this->addNo($bl['net_balance']);
                    $billDtl->voucher_bill_marke   = isset($bl['marked'])?1:0;
                    $billDtl->business_id = auth()->user()->business_id;
                    $billDtl->company_id = auth()->user()->company_id;
                    $billDtl->branch_id = auth()->user()->branch_id;
                    $billDtl->save();
                    $total_curr_pay += isset($bl['curr_pay'])?$this->addNo($bl['curr_pay']):0;
                }

                /*if($total_curr_pay != $tot_voucher_amt){
                    return $this->jsonErrorResponse([], 'Bill list not tally with voucher', 200);
                }*/
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
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }

    }
    public function jvStore(Request $request,$type, $id = null)
    {
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
                $id = $id;
                $code= TblAccoVoucher::where('voucher_id',$id)->where('voucher_type',$type)->where(Utilities::currentBCB())->first('voucher_no');
                $voucher_no = $code->voucher_no;
                $del_jvs = TblAccoVoucher::where('voucher_id',$id)->where(Utilities::currentBCB())->get();
                foreach ($del_jvs as $del_jv){
                    TblAccoVoucher::where('voucher_id',$del_jv->voucher_id)->where(Utilities::currentBCB())->delete();
                }
            }else{
                $id = $this->uuid();
                $max_voucher = TblAccoVoucher::where('voucher_type',$type)->where(Utilities::currentBCB())->max('voucher_no');
                $voucher_no = $this->documentCode($max_voucher,$type);
            }
            $voucher_date =  $request->voucher_date;
            $currency_id = $request->currency_id;
            $voucher_exchange_rate = $request->exchange_rate;
            $voucher_notes = $request->voucher_notes;
            if($request->pd) {
                foreach ($request->pd as $dtl) {
                    $i++;
                    $voucher = new TblAccoVoucher();
                    $voucher->voucher_id = $id;
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
                    $voucher->voucher_user_id = auth()->user()->id;
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
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
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
            case 'lv': {
                $data['title'] = 'Liability Voucher';
                $formUrl = 'jv_obv_print';
                $data['stock_menu_id'] = '138';
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
