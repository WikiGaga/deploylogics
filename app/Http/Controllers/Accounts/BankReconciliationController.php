<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Library\CoreFunc;
use App\Library\Utilities;
use App\Models\Defi\TblDefiChequeStatus;
use App\Models\TblAccCoa;
use App\Models\TblAccoBankRec;
use App\Models\TblAccoBankRecDtl;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccoVoucher;
use App\Models\TblSoftBranch;
use App\Models\ViewAccoVoucher;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class BankReconciliationController extends Controller
{
    public static $page_title = 'Bank Reconciliation';
    public static $redirect_url = 'bank-reconciliation';
    public static $menu_dtl_id = '152';
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
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblAccoBankRec::where('bank_rec_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccoBankRec::with('dtl','bank_acco')->where('bank_rec_id',$id)->where(Utilities::currentBCB())->first();

               // dd($data['current']->toArray());
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());

            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblAccoBankRec',
                'code_field'        => 'bank_rec_code',
                'code_prefix'       => strtoupper('brs')
            ];
            $data['document_no'] = Utilities::documentCode($doc_data);
        }
        $data['branches'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
        $data['cheque_status'] = TblDefiChequeStatus::where('cheque_status_entry_status',1)->where(Utilities::currentBC())->get();
        return view('accounts.bank_reconciliation.form',compact('data'));
    }
    public function getAccData(Request $request){
        $data = [];
        $chart_account_id = $request->account_id;
        $exitsAcc = TblAccCoa::where('chart_account_id',$chart_account_id)->where(Utilities::currentBC())->exists();
        if (empty($exitsAcc)) {
            return $this->jsonErrorResponse($data,"Account Code not correct",200);
        }
        $opening_date = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($request->from_date) ) ));
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));
        if(!isset($request->branches) || count($request->branches) == 0){
            return $this->jsonErrorResponse($data, 'Branch is required', 200);
        }
        if(!isset($request->branches) || count($request->branches) == 0){
            return $this->jsonErrorResponse($data, 'Branch is required', 200);
        }

        if($request->sort_by == ""){
            $sort_by = "order by v.voucher_date , voucher_no";
        }
        if($request->sort_by == "voucher_date"){
            $sort_by = "order by v.voucher_date DESC";
        }
        if($request->sort_by == "voucher_no"){
            $sort_by = "order by voucher_no DESC";
        }
        if($request->sort_by == "voucher_chqno"){
            $sort_by = "order by voucher_chqno DESC";
        }
        if($request->sort_by == "voucher_mode_date"){
            $sort_by = "order by voucher_mode_date DESC";
        }




        $branch_ids = $request->branches;
        $where = "";
        if(isset($request->unreconciled)){
            $where .= " and v.bank_rec_posted = 0 ";
        }
        if(isset($request->transactions)){
            $where .= " and (v.bank_rec_posted = 0 or v.bank_rec_posted = 1) ";
        }
        $where .= " and (v.voucher_date between to_date('".$from_date."','yyyy/mm/dd') AND to_date('".$to_date."','yyyy/mm/dd') ) ";
        $where .= " and v.business_id = ".auth()->user()->business_id." ";
        $where .= " and v.company_id = ".auth()->user()->company_id." ";
        $where .= " and v.branch_id in (".implode(",",$branch_ids).") ";

        $qry = "select v.*,acc.chart_name contra_chart_name from VW_ACCO_VOUCHER v
        left join TBL_ACCO_CHART_ACCOUNT acc on acc.chart_account_id = v.voucher_cont_acc_code
        where v.chart_account_id = $chart_account_id
        $where $sort_by ";
         //dd($qry);
        $items = DB::select($qry);
        // dd($items);
        $data['items'] = $items;
        $paras = [
            'chart_account_id' => $chart_account_id,
            'voucher_date' => $from_date,
            'branch_ids' => $branch_ids,
        ];
        $data['opening_balance'] = CoreFunc::acco_opening_bal($paras);
        $data['payment_mode'] = TblAccoPaymentTerm::where('payment_term_id','<>',0)->where(Utilities::currentBC())->pluck('payment_term_name','payment_term_id')->toArray();
        // dd($data);
        return response()->json(['data'=>$data,'status'=>'success']);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        //dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|max:255',
            // 'pd.*.voucher_id' => 'required',
            // 'pd.*.cleared_date' => 'required|date'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $marked_checked = false;
        if(isset($request->pd)) {
            foreach ($request->pd as $pd) {
                if (isset($pd['marked'])) {
                    $marked_checked = true;
                }
            }
        }
        if(!$marked_checked){
            return $this->jsonErrorResponse($data, 'Any entry  must be reconciled', 200);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                return $this->jsonErrorResponse($data, 'Entry not updated', 200);
                $bank_rec = TblAccoBankRec::where('bank_rec_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $bank_rec = new TblAccoBankRec();
                $bank_rec->bank_rec_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblAccoBankRec',
                    'code_field'        => 'bank_rec_code',
                    'code_prefix'       => strtoupper('brs')
                ];
                $bank_rec->bank_rec_code = Utilities::documentCode($doc_data);
            }
            $form_id = $bank_rec->bank_rec_id;
            $bank_rec->bank_rec_date = date('Y-m-d', strtotime($request->document_date));
            $bank_rec->bank_rec_bank_id = $request->account_id;
            $bank_rec->bank_rec_bank_balance = $request->bank_balance;
            $bank_rec->bank_rec_opening_balance = $this->addNo($request->opening_balance);
            $bank_rec->bank_rec_closing_balance = $request->closing_balance;
            $bank_rec->bank_rec_uncleared_balance = $request->uncleared;
            $bank_rec->bank_rec_satement_date = date('Y-m-d', strtotime($request->statement_date));
            $bank_rec->bank_rec_start_date = date('Y-m-d', strtotime($request->from_date));
            $bank_rec->bank_rec_end_date = date('Y-m-d', strtotime($request->to_date));
            $bank_rec->bank_rec_notes = $request->bank_rec_notes;
            $bank_rec->bank_rec_reconciled = $request->transactions;
            $bank_rec->bank_rec_entry_status = 1;
            $bank_rec->business_id = auth()->user()->business_id;
            $bank_rec->company_id = auth()->user()->company_id;
            $bank_rec->branch_id = auth()->user()->branch_id;
            $bank_rec->bank_rec_user_id = auth()->user()->id;
            if(!empty($request->branch_ids)){
                $branch_ids = implode(",",$request->branch_ids);
            }else{
                $branch_ids = auth()->user()->branch_id;
            }
            $bank_rec->branch_ids = $branch_ids;
            $bank_rec->save();
            if(isset($id)){
                TblAccoBankRecDtl::where('bank_rec_id',$bank_rec->bank_rec_id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->pd)){
                $k = 1;
                foreach ($request->pd as $pd){
                    if(isset($pd['marked'])){
                        if(!TblAccoVoucher::where('voucher_id',$pd['voucher_id'])->exists()){
                            return $this->jsonErrorResponse($data, 'Voucher Code not Correct', 200);
                        }
                        $dtl = new TblAccoBankRecDtl();
                        $dtl->bank_rec_dtl_id = Utilities::uuid();
                        $dtl->bank_rec_id = $bank_rec->bank_rec_id;
                        $dtl->bank_rec_sr = $k++;
                        $dtl->bank_rec_voucher_id = $pd['voucher_id'];
                        if(empty($pd['cleared_date'])){
                            return $this->jsonErrorResponse($data, 'Cleared date is required', 200);
                        }
                        $dtl->bank_rec_voucher_date = date('Y-m-d', strtotime($pd['voucher_date']));
                        $dtl->bank_rec_voucher_no = $pd['voucher_no'];
                        $dtl->bank_rec_voucher_descrip = !empty($pd['narration'])?$pd['narration']:"";
                        $dtl->bank_rec_voucher_debit = $this->addNo($pd['debit']);
                        $dtl->bank_rec_voucher_credit = $this->addNo($pd['credit']);
                        $dtl->bank_rec_voucher_chqno = $pd['cheque_no'];
                        $dtl->bank_rec_voucher_chqdate = date('Y-m-d', strtotime($pd['cheque_date']));
                        $dtl->bank_rec_voucher_posted = 1;
                        $dtl->bank_rec_cheque_status = (!empty($pd['cheque_status']) && $pd['cheque_status'] != 0)?$pd['cheque_status']:"";
                        $dtl->bank_rec_voucher_mode_no = isset($pd['marked'])?1:0;
                        $dtl->bank_rec_voucher_notes = (isset($pd['voucher_notes']) && !empty($pd['voucher_notes']))?$pd['voucher_notes']:"";
                        $dtl->bank_rec_voucher_branch = $pd['branch_name'];
                        $dtl->business_id = auth()->user()->business_id;
                        $dtl->company_id = auth()->user()->company_id;
                        $dtl->branch_id = auth()->user()->branch_id;
                        $dtl->bank_rec_voucher_cleared_date = (!empty($pd['cleared_date']))?date('Y-m-d', strtotime($pd['cleared_date'])):"";
                        $dtl->save();


                        TblAccoVoucher::where('voucher_id',$pd['voucher_id'])
                            ->where('chart_account_id',$pd['bank_chart_account_id'])
                            ->where('voucher_mode_no',$pd['cheque_no'])->update([
                                'bank_rec_posted'=> isset($pd['marked'])?1:0,
                                'bank_rec_cheque_status_id'=> (!empty($pd['cheque_status']) && $pd['cheque_status'] != 0)?$pd['cheque_status']:"",
                                'bank_rec_voucher_notes'=> (!empty($pd['voucher_notes']))?$pd['voucher_notes']:"",
                                'bank_rec_cleared_date'=> (!empty($pd['cleared_date']))?date('Y-m-d', strtotime($pd['cleared_date'])):"",
                            ]);
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            // $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage;
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($type,$id)
    {
        //
    }

    public function print($id)
    {
        $data['title'] = self::$page_title;
        $data['permission'] = self::$menu_dtl_id.'-print';
        if(isset($id)){
            if(TblAccoBankRec::where('bank_rec_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblAccoBankRec::with('dtl','bank_acco')->where('bank_rec_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['cheque_status'] = TblDefiChequeStatus::where('cheque_status_entry_status',1)->where(Utilities::currentBC())->get();
        return view('prints.acc.bank_reconciliation_print',compact('data'));
    }
}
