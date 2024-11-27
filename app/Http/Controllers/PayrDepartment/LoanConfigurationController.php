<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Library\Utilities;
use App\Models\TblHrGrade;
use App\Library\FileStorage;
use Illuminate\Http\Request;
use App\Models\TblHrReligion;
use App\Models\TblHrDepartment;
use App\Models\TblHrAdvanceType;
use App\Models\TblHrDesignation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblPayrPolicyCriteria;
use App\Models\TblHrLoanConfiguration;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Models\TblHrAllowanceDeductionType;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoanConfigurationController extends Controller
{
    public static $page_title = 'Loan Configuration';
    public static $redirect_url = 'loan-configuration';
    // Local
    // public static $menu_dtl_id = '192';
    // Live
    public static $menu_dtl_id = '193';
    public static $document_type = 'loan-configuration';

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
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblHrLoanConfiguration::where('loan_configuration_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblHrLoanConfiguration::with("leave_policy_dtls")->where('loan_configuration_id',$id)->where(Utilities::currentBCB())->first();
            
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['loan_type'] = TblHrAdvanceType::get();
        $data['loan_code'] = $this->documentCode(0,'LOANC');
        $data['allowance_type'] = TblHrAllowanceDeductionType::get();


        return view('PayrDepartment.loan_configuration.form',compact('data'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'description' => 'required|max:255',
            'loan_type' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $loan_configuration = TblHrLoanConfiguration::where('loan_configuration_id',$id)->first();
            }else{
                $loan_configuration = new TblHrLoanConfiguration();
                $loan_configuration->loan_configuration_id =  Utilities::uuid();
            }
            $form_id = $loan_configuration->loan_configuration_id;
            $loan_configuration->description =  $request->description;
            $loan_configuration->loan_type =  $request->loan_type;

            $loan_configuration->occurence_type =  $request->occurence_type;
            $loan_configuration->minimum_installment =  $request->min_installment;
            $loan_configuration->maximum_installment =  $request->max_installment;

            $loan_configuration->allowance =  $request->allowance_type;
           
            $loan_configuration->rate_type =  $request->rate_type_allowance;
            $loan_configuration->rate_value =  $request->rate_value_allowance;
            $loan_configuration->minimum_value =  $request->min_value;
            $loan_configuration->maximum_value =  $request->max_value;
            $loan_configuration->employee_contribution =  $request->employer_contribution;
         
            $loan_configuration->employee_rate_type =  $request->rate_type_contribution;
            $loan_configuration->employee_rate_value =  $request->rate_value_contribution;
            $loan_configuration->apply_on_loan =  $request->apply_on_loan;

            $loan_configuration->business_id = auth()->user()->business_id;
            $loan_configuration->company_id = auth()->user()->company_id;
            $loan_configuration->branch_id = auth()->user()->branch_id;
            $loan_configuration->loan_configuration_user_id = auth()->user()->id;
            $loan_configuration->save();

            $del_Dtls = TblPayrPolicyCriteria::where('criteria_document_id',$id)
                ->where('criteria_menu_id',self::$menu_dtl_id)
                ->where('criteria_document_type',self::$document_type)->get();

            foreach ($del_Dtls as $del_Dtl){
                TblPayrPolicyCriteria::where('criteria_id',$del_Dtl->criteria_id)->delete();
            }
            if(isset($request->criteria)){
                foreach ($request->criteria as $key=>$criteria){
                    foreach ($criteria as $criterion_id) {
                        $policy_criteria = new TblPayrPolicyCriteria();
                        $policy_criteria->criteria_id = Utilities::uuid();
                        $policy_criteria->criteria_menu_id = self::$menu_dtl_id;
                        $policy_criteria->criteria_document_id = $loan_configuration->loan_configuration_id;
                        $policy_criteria->criteria_document_type = self::$document_type;
                        $policy_criteria->criteria_tag_type = $key;
                        $tag_value_status = false;
                        if($key == 'religion'){
                            if(TblHrReligion::where('religion_id','LIKE',$criterion_id)->exists()){
                                $tag_value = TblHrReligion::where('religion_id',$criterion_id)->first();
                                $policy_criteria->criteria_tag_value_id = $tag_value->religion_id;
                                $policy_criteria->criteria_tag_value_name = $tag_value->religion_name;
                                $tag_value_status = true;
                            }
                        }
                        if($key == 'grade'){
                            if(TblHrGrade::where('grade_id','LIKE',$criterion_id)->exists()){
                                $tag_value = TblHrGrade::where('grade_id',$criterion_id)->first();
                                $policy_criteria->criteria_tag_value_id = $tag_value->grade_id;
                                $policy_criteria->criteria_tag_value_name = $tag_value->grade_name;
                                $tag_value_status = true;
                            }
                        }
                        if($key == 'designation'){
                            if(TblHrDesignation::where('designation_id','LIKE',$criterion_id)->exists()){
                                $tag_value = TblHrDesignation::where('designation_id',$criterion_id)->first();
                                $policy_criteria->criteria_tag_value_id = $tag_value->designation_id;
                                $policy_criteria->criteria_tag_value_name = $tag_value->designation_name;
                                $tag_value_status = true;
                            }
                        }
                        if($key == 'department'){
                            if(TblHrDepartment::where('department_id','LIKE',$criterion_id)->exists()){
                                $tag_value = TblHrDepartment::where('department_id',$criterion_id)->first();
                                $policy_criteria->criteria_tag_value_id = $tag_value->department_id;
                                $policy_criteria->criteria_tag_value_name = $tag_value->department_name;
                                $tag_value_status = true;
                            }
                        }
                        $policy_criteria->business_id = auth()->user()->business_id;
                        $policy_criteria->company_id = auth()->user()->company_id;
                        $policy_criteria->branch_id = auth()->user()->branch_id;
                        $policy_criteria->criteria_user_id = auth()->user()->id;
                        if($tag_value_status){
                            $policy_criteria->save();
                        }
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
            $loan_configuration =TblHrLoanConfiguration::where('loan_configuration_id',$id)->first();
            $loan_configuration->delete();
        }
        catch (QueryException $e) {
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

