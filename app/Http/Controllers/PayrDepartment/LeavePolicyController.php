<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Library\FileStorage;
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblFileUpload;
use App\Models\TblHrDepartment;
use App\Models\TblHrDesignation;
use App\Models\TblHrGrade;
use App\Models\TblHrLeaveType;
use App\Models\TblHrReligion;
use App\Models\TblPayrLeavePolicy;
use App\Models\TblPayrPolicyCriteria;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class LeavePolicyController extends Controller
{
    public static $page_title = 'Leave Policy';
    public static $redirect_url = 'leave-policy';
    public static $menu_dtl_id = '122';
    public static $document_type = 'leave-policy';

    public function __construct(){
        $this->currentBusinessCompanyBranch = [
            ['business_id',auth()->user()->business_id],
            ['company_id',auth()->user()->company_id],
            ['branch_id',auth()->user()->branch_id]
        ]; // ->where($currentBusinessCompanyBranch)
    }
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
            if(TblPayrLeavePolicy::where('leave_policy_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblPayrLeavePolicy::with("leave_policy_dtls")->where('leave_policy_id',$id)
                    ->where('menu_dtl_id',self::$menu_dtl_id)->first();

                $data['files'] = TblFileUpload::where('document_form_id',$id)->get();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }

        $data['leave_type'] = TblHrLeaveType::where($this->currentBusinessCompanyBranch)->where('leave_type_entry_status',1)->orderBy('leave_type_name')->get();
        $data['religion'] = TblHrReligion::where($this->currentBusinessCompanyBranch)->where('religion_entry_status',1)->orderBy('religion_name')->get();
        $data['grade'] = TblHrGrade::where($this->currentBusinessCompanyBranch)->where('grade_entry_status',1)->orderBy('grade_name')->get();
        $data['designation'] = TblHrDesignation::where($this->currentBusinessCompanyBranch)->where('designation_entry_status',1)->orderBy('designation_name')->get();
        $data['department'] = TblHrDepartment::where($this->currentBusinessCompanyBranch)->where('department_entry_status',1)->orderBy('department_name')->get();
       // dd($data['current']->toArray());
        return view('PayrDepartment.leave_policy.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'leave_policy_date' => 'required|date_format:d-m-Y',
            'leave_policy_name' => 'required|max:100',
            'leave_policy_year' => 'required|numeric',
            'leave_type_id' => 'required|numeric',
            'leaves_allowed' => 'required|numeric|max:99',
            'leave_policy_notes' => 'max:255',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $leave_policy = TblPayrLeavePolicy::where('leave_policy_id',$id)->first();
            }else{
                $leave_policy = new TblPayrLeavePolicy();
                $leave_policy->leave_policy_id =  Utilities::uuid();
            }
            $form_id = $leave_policy->leave_policy_id;
            $leave_policy->leave_policy_date =  date('Y-m-d', strtotime($request->leave_policy_date));
            $leave_policy->leave_policy_name =  $request->leave_policy_name;
            $leave_policy->leave_policy_year =  $request->leave_policy_year;
            $leave_policy->leave_type_id =  $request->leave_type_id;
            $leave_policy->leaves_allowed =  $request->leaves_allowed;
            $leave_policy->leave_policy_entry_status =  1;
            $leave_policy->menu_dtl_id =  self::$menu_dtl_id;
            $leave_policy->leave_policy_notes =  $request->leave_policy_notes;
            $leave_policy->business_id = auth()->user()->business_id;
            $leave_policy->company_id = auth()->user()->company_id;
            $leave_policy->branch_id = auth()->user()->branch_id;
            $leave_policy->leave_policy_user_id = auth()->user()->id;
            $leave_policy->save();

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
                        $policy_criteria->criteria_document_id = $leave_policy->leave_policy_id;
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
            if(isset($request->myFiles)){
                $fileData = [
                    'document_type' =>  self::$menu_dtl_id,
                    'form_id' =>  $leave_policy->leave_policy_id,
                    'menu_dtl_id' =>  self::$menu_dtl_id,
                    'document_type_id' =>  $request->documents_name,
                ];
                FileStorage::fileUpload($fileData,$request);
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
        //
    }
}
