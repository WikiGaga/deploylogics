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


use App\Services\PayrollService;

class PayrollController extends Controller
{

    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function generate($employeeId)
    {

        $month = now()->month;
        $year = now()->year;

        $payrollId = $this->payrollService
            ->generatePayroll($employeeId,$month,$year);

        return response()->json([
            'message'=>'Payroll generated',
            'payroll_id'=>$payrollId
        ]);

    }



public function runPayroll($month,$year)
{

    DB::beginTransaction();

    $runId = DB::table('payroll_runs')->insertGetId([
        'month'=>$month,
        'year'=>$year,
        'status'=>'processing',
        'created_at'=>now()
    ]);

    $employees = DB::table('employees')
        ->where('status','active')
        ->pluck('id');

    foreach($employees as $employeeId){

        $this->processEmployee(
            $employeeId,
            $runId,
            $month,
            $year
        );

    }

    DB::table('payroll_runs')
        ->where('id',$runId)
        ->update(['status'=>'completed']);

    DB::commit();

}

// public function runPayroll()
// {

//     $month = now()->month;
//     $year = now()->year;

//     app(\App\Services\PayrollEngine::class)
//         ->runPayroll($month,$year);

//     return "Payroll generated successfully";

// }




}