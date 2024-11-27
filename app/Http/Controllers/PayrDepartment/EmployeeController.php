<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrDesignation;
use App\Models\TblHrDocuments;
use App\Models\TblHrEmployee;
use App\Models\TblHrEmployeeEducational;
use App\Models\TblHrGender;
use App\Models\TblHrGrade;
use App\Models\TblHrInsuranceType;
use App\Models\TblHrNationality;
use App\Models\TblHrReligion;
use App\Models\TblPayrLeavePolicy;
use App\Models\TblDefiCountry;
use App\Models\TblDefiCity;
use App\Models\TblHrLanguage;
use App\Models\TblHrDepartment;
use App\Models\TblHrEmployeeType;
use App\Models\TblHrSponsorShip;
use App\Models\TblDefiBank;
use App\Models\TblHrEmployeeBank;
use App\Models\TblHrEmployeeEmployment;
use App\Models\TblHrEmployeeExperience;
use App\Models\TblHrEmployeeInsurance;
use App\Models\TblSoftBranch;
use App\Models\TblHrInsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Image;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeController extends Controller
{
    public static $page_title = 'Employee';
    public static $redirect_url = 'employee';
    public static $menu_dtl_id = '124';

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
            if(TblHrEmployee::where('employee_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblHrEmployee::with('language','educational','employment','insurance','bank','experience')->where('employee_id',$id)->first();
                $data['local_cities'] = $this->CityCurrent($data['current']->employee_local_country_id,true);
                $data['permanent_cities'] = $this->CityCurrent($data['current']->employee_permanent_country_id,true);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['employee_code'] = $this->documentCode(TblHrEmployee::max('employee_code'),'EMP');
        }
        $data['gender'] = TblHrGender::where($this->currentBusinessCompanyBranch)->where('gender_entry_status',1)->orderBy('gender_name')->get();
        $data['religion'] = TblHrReligion::where($this->currentBusinessCompanyBranch)->where('religion_entry_status',1)->orderBy('religion_name')->get();
        $data['nationality'] = TblHrNationality::where($this->currentBusinessCompanyBranch)->where('nationality_entry_status',1)->orderBy('nationality_name')->get();
        $data['country'] = TblDefiCountry::where($this->currentBusinessCompanyBranch)->where('country_entry_status',1)->orderBy('country_name')->get();
        $data['designation'] = TblHrDesignation::where($this->currentBusinessCompanyBranch)->where('designation_entry_status',1)->orderBy('designation_name')->get();
        $data['grade'] = TblHrGrade::where($this->currentBusinessCompanyBranch)->where('grade_entry_status',1)->orderBy('grade_name')->get();
        $data['document_types'] = TblHrDocuments::where($this->currentBusinessCompanyBranch)->where('document_entry_status',1)->orderBy('document_name')->get();
        $data['language'] = TblHrLanguage::where($this->currentBusinessCompanyBranch)->where('language_entry_status',1)->orderBy('language_name')->get();
        $data['department'] = TblHrDepartment::where($this->currentBusinessCompanyBranch)->where('department_entry_status',1)->orderBy('department_name')->get();
        $data['employee_type'] = TblHrEmployeeType::where($this->currentBusinessCompanyBranch)->where('employee_type_entry_status',1)->orderBy('employee_type_name')->get();
        $data['sponsorship'] = TblHrSponsorShip::where($this->currentBusinessCompanyBranch)->where('sponsorship_entry_status',1)->orderBy('sponsorship_name')->get();
        $data['bank'] = TblDefiBank::where($this->currentBusinessCompanyBranch)->where('bank_entry_status',1)->orderBy('bank_name')->get();
        $data['branch'] = TblSoftBranch::where('business_id',auth()->user()->business_id)->where('company_id',auth()->user()->company_id)->orderBy('branch_name')->get();
        $data['insurance'] = TblHrInsuranceCompany::where($this->currentBusinessCompanyBranch)->where('insurance_company_entry_status',1)->orderBy('insurance_company_name')->get();
        $data['insurance_type'] = TblHrInsuranceType::where($this->currentBusinessCompanyBranch)->where('insurance_type_entry_status',1)->orderBy('insurance_type_name')->get();
        $data['martial_status'] = config('constants.marital_status');
        $data['termination_type'] = config('constants.termination.type');
        $data['termination_status'] = config('constants.termination.status');
        $data['blood_group'] = config('constants.blood_group');

        $data['form_type'] = 'employee';
        $data['menu_id'] = self::$menu_dtl_id;

        return view('PayrDepartment.employee.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
       // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $employee =  TblHrEmployee::where('employee_id',$id)->first();
            }else{
                $employee =  new TblHrEmployee();
                $employee->employee_id = Utilities::uuid();
                $employee->employee_code = $this->documentCode(TblHrEmployee::max('employee_code'),'EMP');
            }
            $form_id = $employee->employee_id;
            $employee->employee_name = $request->employee_name;
            $employee->employee_arabic_name = $request->employee_arabic_name;
            $employee->employee_fh_name = $request->employee_fh_name;
            $employee->employee_date = isset($request->employee_date) ? date('Y-m-d', strtotime($request->employee_date)) : date('Y-m-d' , time());
            if($request->hasFile('employee_img'))
            {
                $folder = 'images/employee/';
                if (! File::exists($folder)) {
                    File::makeDirectory($folder, 0775, true,true);
                }
                $image = $request->file('employee_img');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path($folder . $filename);
                Image::make($image->getRealPath())->save($path);
                $employee->employee_img = isset($filename)?$filename:'';
            }
            // General Info tab
            $employee->employee_man_power_no = $request->employee_man_power_no;
            $employee->employee_id_no = $request->employee_id_no;
            $employee->employee_cpr_no = $request->employee_cpr_no;
            $employee->employee_eobi_no = $request->employee_eobi_no;
            $employee->employee_date_of_birth = date('Y-m-d', strtotime($request->employee_date_of_birth));
            $employee->gender_id = $request->gender_id;
            $employee->blood_group_id = $request->blood_group_id;
            $employee->nationality_id = $request->nationality_id;
            $employee->marital_status_id = $request->marital_status_id;
            $employee->religion_id = $request->religion_id;
            // address tab - local address
            $employee->employee_local_address_1 = $request->employee_local_address_1;
            $employee->employee_local_address_2 = $request->employee_local_address_2;
            $employee->employee_local_country_id = $request->employee_local_country_id;
            $employee->employee_local_city_id = $request->employee_local_city_id;
            $employee->employee_local_postal_code = $request->employee_local_postal_code;
            $employee->employee_local_mobile_no = $request->employee_local_mobile_no;
            $employee->employee_local_phone_no = $request->employee_local_phone_no;
            $employee->employee_local_personal_email = $request->employee_local_personal_email;
            $employee->employee_local_official_email = $request->employee_local_official_email;
            $employee->employee_local_emergency_contact_name = $request->employee_local_emergency_contact_name;
            $employee->employee_local_emergency_contact_phone = $request->employee_local_emergency_contact_phone;
            // address tab - permanent address
            $employee->employee_permanent_address_1 = $request->employee_permanent_address_1;
            $employee->employee_permanent_address_2 = $request->employee_permanent_address_2;
            $employee->employee_permanent_country_id = $request->employee_permanent_country_id;
            $employee->employee_permanent_city_id = $request->employee_permanent_city_id;
            $employee->employee_permanent_postal_code = $request->employee_permanent_postal_code;
            $employee->employee_permanent_mobile_no = $request->employee_permanent_mobile_no;
            $employee->employee_permanent_phone_no = $request->employee_permanent_phone_no;
            $employee->employee_permanent_personal_email = $request->employee_permanent_personal_email;
            $employee->employee_permanent_official_email = $request->employee_permanent_official_email;
            $employee->employee_permanent_emergency_contact_name = $request->employee_permanent_emergency_contact_name;
            $employee->employee_permanent_emergency_contact_phone = $request->employee_permanent_emergency_contact_phone;
            // Employment Tab
                // $employee->employee_joining_date = date('Y-m-d', strtotime($request->employee_joining_date));
                // $employee->employee_confirmation_date = date('Y-m-d', strtotime($request->employee_confirmation_date));
                // $employee->employee_card_validity_date = date('Y-m-d', strtotime($request->employee_card_validity_date));
                // $employee->employee_report_to_id = $request->employee_report_to_id;
                // $employee->employee_card_no = $request->employee_card_no;
                // $employee->employee_experience = $request->employee_experience;
                // $employee->employee_manual_attendance = isset($request->employee_manual_attendance)?1:0;
                // $employee->grade_id = $request->grade_id;
                // $employee->employee_type_id = $request->employee_type_id;
                // $employee->designation_id = $request->designation_id;
                // $employee->department_id = $request->department_id;
            $employee->branch_contract_id = $request->branch_contract_id;
            $employee->branch_working_id = $request->branch_working_id;
            $employee->sponsorship_type_id = $request->sponsorship_type_id;
            $employee->employee_sponsorship_name = $request->employee_sponsorship_name;
            $employee->employee_sponsorship_no = $request->employee_sponsorship_no;
                // $employee->employee_approval_authority_id = $request->employee_approval_authority_id;
                // $employee->employee_probation_upto = date('Y-m-d', strtotime($request->employee_probation_upto));
            $employee->employee_contract_renewal_date = date('Y-m-d', strtotime($request->employee_contract_renewal_date));
            $employee->employee_contract_renewal_upto = date('Y-m-d', strtotime($request->employee_contract_renewal_upto));
        
            $employee->employee_entry_status = isset($request->employee_entry_status)?1:0;
            $employee->employee_user_id = auth()->user()->id;
            $employee->business_id = auth()->user()->business_id;
            $employee->company_id = auth()->user()->company_id;
            $employee->branch_id = auth()->user()->branch_id;
            // Termination Tab
            $employee->employee_termination_date = date('Y-m-d', strtotime($request->employee_termination_date));
            $employee->termination_type_id = $request->termination_type_id;
            $employee->employee_leaving_reason = $request->employee_leaving_reason;
            $employee->termination_status_id = $request->termination_status_id;
            
            $employee->save();
            $employee->language()->sync($request->language_known);

            // Employment Grid
            if(isset($id)){
                $dtls = TblHrEmployeeEmployment::where('employee_id',$id)->get();
                foreach ($dtls as $del){
                    TblHrEmployeeEmployment::where('employee_employment_id',$del->employee_employment_id)->delete();
                }
            }
            if(isset($request->emp)){
                foreach ($request->emp as $emp){
                    TblHrEmployeeEmployment::create([
                        'employee_employment_id' => Utilities::uuid(),
                        'employee_id' => $employee->employee_id,
                        'employee_employment_sr_no' => $emp['sr_no'],
                        'employee_date' => date('Y-m-d' , strtotime($emp['employee_joining_date'])),
                        'grade_id' => $emp['grade_id'],
                        'employee_type_id' => $emp['employee_type_id'],
                        'designation_id' => $emp['designation_id'],
                        'department_id' => $emp['department_id'],
                        'business_id' => auth()->user()->business_id,
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                    ]);
                }
            }

            // Experience Grid
            if(isset($id)){
                $dtls = TblHrEmployeeExperience::where('employee_id',$id)->get();
                foreach ($dtls as $del){
                    TblHrEmployeeExperience::where('employee_experience_id',$del->employee_experience_id)->delete();
                }
            }
            if(isset($request->exp)){
                foreach ($request->exp as $exp){
                    TblHrEmployeeExperience::create([
                        'employee_experience_id' => utilities::uuid(),
                        'employee_id' => $employee->employee_id,
                        'employee_experience_sr_no' => $exp['sr_no'],
                        'company_name' => $exp['employee_exp_company_name'],
                        'field_name' => $exp['employee_exp_field_name'],
                        'experience_in_year' => $exp['employee_exp_experience_years'],
                        'business_id' => auth()->user()->business_id,
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                    ]);
                }
            }
            
            // Educational Grid
            if(isset($id)){
                $dtls = TblHrEmployeeEducational::where('employee_id',$id)->get();
                foreach ($dtls as $del){
                    TblHrEmployeeEducational::where('employee_educational_id',$del->employee_educational_id)->delete();
                }
            }
            if(isset($request->edu)){
                foreach ($request->edu as $edu){
                    TblHrEmployeeEducational::create([
                        'employee_educational_id' => Utilities::uuid(),
                        'employee_id' => $employee->employee_id,
                        'employee_educational_sr_no' => $edu['sr_no'],
                        'employee_educational_degree_name' => $edu['employee_educational_degree_name'],
                        'employee_educational_marks' => $edu['employee_educational_marks'],
                        'employee_educational_grade' => $edu['employee_educational_grade'],
                        'employee_educational_subject_detail' => $edu['employee_educational_subject_detail'],
                        'employee_educational_board_name' => $edu['employee_educational_board_name'],
                        'employee_educational_passing_year' => $edu['employee_educational_passing_year'],
                    ]);
                }
            }

            // Bank Details Grid
            if(isset($id)){
                $dtls = TblHrEmployeeBank::where('employee_id',$id)->get();
                foreach ($dtls as $del){
                    TblHrEmployeeBank::where('employee_bank_id',$del->employee_bank_id)->delete();
                }
            }
            if(isset($request->bank)){
                foreach ($request->bank as $bank){
                    TblHrEmployeeBank::create([
                        'employee_bank_id' => Utilities::uuid(),
                        'employee_id' => $employee->employee_id,
                        'employee_bank_sr_no' => $bank['sr_no'],
                        'chart_bank_id' => $bank['employee_bank_bank_id'],
                        'account_title' => $bank['employee_bank_account_title'],
                        'account_no' => $bank['employee_bank_account_no'],
                        'business_id' => auth()->user()->business_id,
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                    ]);
                }
            }

            // Insurance Grid
            if(isset($id)){
                $dtls = TblHrEmployeeInsurance::where('employee_id',$id)->get();
                foreach ($dtls as $del){
                    TblHrEmployeeInsurance::where('employee_insurance_id',$del->employee_insurance_id)->delete();
                }
            }
            if(isset($request->ins)){
                foreach ($request->ins as $ins){
                    TblHrEmployeeInsurance::create([
                        'employee_insurance_id' => Utilities::uuid(),
                        'employee_id' => $employee->employee_id,
                        'employee_insurance_sr_no' => $ins['sr_no'],
                        'insurance_company_id' => $ins['insurance_company_id'],
                        'employee_insurance_health_name' => $ins['employee_insurance_health_name'],
                        'employee_insurance_rate_for_foreign' => $ins['employee_insurance_rate_for_foreign'],
                        'employee_insurance_rate_settlement' => $ins['employee_insurance_rate_settlement'],
                        'insurance_type_id' => $ins['insurance_type_id'],
                        'employee_insurance_start_date' => date('Y-m-d' , strtotime($ins['employee_insurance_start_date'])),
                        'employee_insurance_end_date' => date('Y-m-d' , strtotime($ins['employee_insurance_end_date'])),
                        'business_id' => auth()->user()->business_id,
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                    ]);
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
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    public function getLeavePolicy(Request $request)
    {
        //dd($request->toArray());
        $religion  = $request->religion;
        $designation  = $request->designation;
        $grade  = $request->grade;
        $totalParameter = 2;

        $leave_policys = TblPayrLeavePolicy::whereHas('leave_policy_dtls', function ($query) {
            $query->where('criteria_document_type', '=', 'leave-policy');
        })->get();

        $data = [];
        foreach ($leave_policys as $leave_policy){
            $get = false;
            $arr = [];
            if(count($leave_policy->leave_policy_dtls) <= $totalParameter && count($leave_policy->leave_policy_dtls) !== 0){
                $getPolicy = 0;
                foreach ($leave_policy->leave_policy_dtls as $leave_policy_dtls){
                    if($leave_policy_dtls['criteria_tag_type'] == 'grade' && $leave_policy_dtls['criteria_tag_value_id'] == $grade){
                        $getPolicy++;
                    }
                    if($leave_policy_dtls['criteria_tag_type'] == 'religion' && $leave_policy_dtls['criteria_tag_value_id'] == $religion){
                        $getPolicy++;
                    }
                    if($leave_policy_dtls['criteria_tag_type'] == 'designation' && $leave_policy_dtls['criteria_tag_value_id'] == $designation){
                        $getPolicy++;
                    }
                }
            }
            if(count($leave_policy->leave_policy_dtls) == $getPolicy){
                $get = true;
            }
            if($get){
                $arr['id'] = $leave_policy->leave_policy_id;
                $arr['name'] = $leave_policy->leave_policy_name;
                array_push($data,$arr);
            }
        }

       // dd($data);

        return response()->json($data);

    }

    public function CityCurrent($countryCurrent,$editcase = false)
    {
        $data = TblDefiCity::where($this->currentBusinessCompanyBranch)
        ->where('country_id', '=', $countryCurrent)
        ->where('city_entry_status',1)
        ->orderBy('city_name')
        ->get();
        if($editcase){
            return $data;
        }else{
            return response()->json($data);
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
