<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\TblAccCoa;
use App\Models\TblAccBudget;
use App\Models\TblSoftBranch;
use App\Models\TblSoftCompany;
use Illuminate\Http\Request;
// db and Validator
use App\Library\Utilities;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Budget';
    public static $redirect_url = 'budget';
    public static $menu_dtl_id = '52';


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
        $data['page_data']['path_index'] =  $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblAccBudget::where('budget_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccBudget::where('budget_id',$id)->where(Utilities::currentBC())->first();
                $data['dtl'] = TblAccBudget::with('accounts')->where('budget_id',$id)->where(Utilities::currentBC())->get();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'TblAccBudget',
                'code_field'        => 'budget_code',
                'code_prefix'       => strtoupper('bgt')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        //$data['company'] = TblSoftCompany::where('company_active_status',1)->get();
        $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();
        return view('accounts.budget.form',compact('data'));
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
        $validator = Validator::make($request->all(), [

        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $budget_id = $id;
                $code= TblAccBudget::where('budget_id',$id)->where(Utilities::currentBC())->first('budget_code');
                $document_code = $code->budget_code;
                $del_dtl = TblAccBudget::where('budget_id',$id)->where(Utilities::currentBC())->get();
                foreach ($del_dtl as $dtl){
                    TblAccBudget::where('budget_id',$dtl->budget_id)->where(Utilities::currentBC())->delete();
                }
                
                $existed = $this->checkAlreadyExistedEntries($request->pd);
                if(count($existed) > 0){
                    $data['existed'] = $existed;
                    $budgetCodes = '';
                    foreach ($data['existed'] as $value) {
                        $budgetCodes .= $value[1] . ',';
                    }
                    return $this->jsonErrorResponse($data , 'Some Budget Entries with simillar dates already existed. Check In ('. $budgetCodes .')' , 200);
                }

            }else{
                $budget_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'TblAccBudget',
                    'code_field'        => 'budget_code',
                    'code_prefix'       => strtoupper('bgt')
                ];
                $document_code = Utilities::documentCode($doc_data);
                
                $existed = $this->checkAlreadyExistedEntries($request->pd);
                if(count($existed) > 0){
                    $data['existed'] = $existed;
                    $budgetCodes = '';
                    foreach ($data['existed'] as $value) {
                        $budgetCodes .= $value[1] . ',';
                    }
                    return $this->jsonErrorResponse($data , 'Some Budget Entries with simillar dates already existed. Check In ('. $budgetCodes .')' , 200);
                }
            }
            $form_id = $budget_id;
            $budget_entry_date =  $request->budget_entry_date;
            $budget_notes = $request->budget_notes;
            if(isset($request->pd)){
                foreach($request->pd as $dtl){
                    $budget = new TblAccBudget();
                    $budget->budget_id = $budget_id;
                    $budget->budget_code = $document_code;
                    $budget->budget_entry_date = date('Y-m-d', strtotime($budget_entry_date));
                    $budget->budget_notes = $budget_notes;
                    $budget->budget_branch_id = $dtl["budget_branch_id"];
                    //$budget->budget_from_period = date('Y-m-d', strtotime($request->budget_from_period));
                   // $budget->budget_to_period = date('Y-m-d', strtotime($request->budget_to_period));
                    $budget->budget_budgetart_position = $dtl['budget_budgetart_position'];
                    $budget->chart_account_id = $dtl['account_id'];
                    $budget->budget_start_date = date('Y-m-d', strtotime($dtl['budget_start_date']));
                    $budget->budget_end_date = date('Y-m-d', strtotime($dtl['budget_end_date']));
                    $budget->budget_alert_type = isset($dtl['budget_alert_type'])?$dtl['budget_alert_type']:"0";
                    $budget->budget_exceeded_limit = $this->addNo($dtl['budget_exceeded_limit']);
                    $budget->budget_credit_amount = $this->addNo($dtl['budget_credit_amount']);
                    $budget->budget_debit_amount = $this->addNo($dtl['budget_debit_amount']);
                    $budget->budget_practical_amount = $this->addNo($dtl['budget_practical_amount']);
                    //$budget->budget_theoretical_amount = $dtl['budget_theoretical_amount'];
                    $budget->budget_achievement = $dtl['budget_achievement'] ?? 0;
                    $budget->budget_entry_status = "1";
                    $budget->business_id = auth()->user()->business_id;
                    $budget->company_id = auth()->user()->company_id;
                    $budget->branch_id = auth()->user()->branch_id;
                    $budget->budget_user_id = auth()->user()->id;
                    $budget->save();

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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    function checkAlreadyExistedEntries($entries = array() , $documentCode = NULL){
        $existedIds = array();
        if(isset($entries) && count($entries) > 0){
            $data['alreadyExisted'] = array();
            foreach ($entries as $entry) {
                $check = TblAccBudget::where('budget_branch_id' , $entry['budget_branch_id'])
                ->where('chart_account_id' , $entry['account_id'])
                ->where('branch_id' , auth()->user()->branch_id);

                if(isset($documentCode)){
                    $check = $check->whereNot('budget_code' , $documentCode);
                }

                if($check->exists()){
                    $alreadyEntries = $check->select('budget_start_date' , 'budget_end_date','budget_code');
                    $alreadyEntries = $alreadyEntries->get();
                    foreach ($alreadyEntries as $existedEntry) {
                        $entryStartDate =  date('Y-m-d' , strtotime($entry['budget_start_date'])); $entryEndDate = date('Y-m-d' , strtotime($entry['budget_end_date']));
                        $duplicates = $check->where(function($query) use ($entryStartDate, $entryEndDate){
                            $query->whereBetween('budget_start_date' , [$entryStartDate , $entryEndDate]);
                            $query->orWhereBetween('budget_end_date' , [$entryStartDate , $entryEndDate]);
                        })->get();
                        
                        if(count($duplicates) > 0){
                            $index = 0;
                            foreach ($duplicates as $duplicate) {
                                $index++; $existedIds[$index] = array();
                                array_push($existedIds[$index] , $entry['sr_no']);   
                                array_push($existedIds[$index] , $duplicate->budget_code);
                            }
                        }
                    }
                }
            }
        }
        return array_unique($existedIds);
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
        $data = [];
        DB::beginTransaction();
        try{

            $del_dtl = TblAccBudget::where('budget_id',$id)->where(Utilities::currentBC())->get();
            foreach ($del_dtl as $dtl){
                TblAccBudget::where('budget_id',$dtl->budget_id)->where(Utilities::currentBC())->delete();
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
