<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrEmployeeAttendance;
use Illuminate\Http\Request;

use App\Models\Defi\TblDefiConstants;
use App\Models\Defi\TblDefiTaxGroup;
use App\Models\Draft\DraftPurcPurchaseOrder;
use App\Models\Draft\DraftPurcPurchaseOrderDtl;
use App\Models\TblAccoPaymentMode;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccoPaymentType;
use App\Models\TblAutoDemandDtl;
use App\Models\TblDefiCurrency;
use App\Models\TblDefiPaymentType;
use App\Models\TblPurcComparativeQuotation;
use App\Models\TblPurcComparativeQuotationDtl;
use App\Models\TblPurcDemand;
use App\Models\TblPurcLpo;
use App\Models\TblPurcLpoDtl;
use App\Models\TblPurcGrn;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblSoftUserPageSetting;
use App\Models\User;
use App\Models\ViewPurcGRN;
use App\Models\ViewPurcLpoDetail;

use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeAttendanceController extends Controller
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
    public static $page_title = 'Employee Attendance';
    public static $redirect_url = 'Employee-Attendance';
    public static $menu_dtl_id='85';
    //  public function create($id = null)
    // {
    //     $data['page_data'] = [];
    //     $data['page_data']['title'] = self::$page_title;
    //     $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
    //     $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
    //     if(isset($id)){
    //         if(TblHrEmployeeAttendance::where('employee_type_id','LIKE',$id)->exists()){
    //             $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
    //             $data['permission'] = self::$menu_dtl_id.'-edit';
    //             $data['id'] = $id;
    //             $data['current'] = TblHrEmployeeAttendance::where('employee_type_id',$id)->first();
    //         }else{
    //             abort('404');
    //         }
    //     }else{
    //         $data['permission'] = self::$menu_dtl_id.'-create';
    //         $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
    //     }
        
    //     return view('PayrDepartment.employee_attendance.form',compact('data'));
    // }

       public function create($id = null)
    {
        $data['form_type'] = 'purc_order';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['menu_id'] = self::$menu_dtl_id;
        $data['page_data']['pending_pr'] = TRUE;
        $data['already_exits'] = false;
        $data['permission'] = self::$menu_dtl_id.'-create';
        $data['employee'] =  DB::table('tbl_payr_employee')
        ->select("EMPLOYEE_ID","EMPLOYEE_NAME")
        ->get();
        $data['type']='brv';

        // dd( $data['page_data'],$data['employee']);

         $data['att_data'] =[];

          if(isset($id)){
             $data['att_data'] = DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)->get();
           
        }else{
            // $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }

      
        return view('PayrDepartment.employee_attendance.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id=null)
    {

        dd($request->all(), $id);

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|max:100'
        // ]);
        // if ($validator->fails()) {
        //     $data['validator_errors'] = $validator->errors();
        //     return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        // }
  
        $data=[];
            // if(isset($id)){
            //     $employee = TblHrEmployeeAttendance::where('employee_type_id',$id)->first();
            // }else{
                 $p_id= DB::table('Tbl_hr_attendence_dtl')->max('id') +1;

                $data=[
                        'id'=>$p_id, 
                        'emp_id'=>$request->employee_select, 
                        'attendance_date'=>date('Y-m-d',strtotime($request->date)),
                        'attendance_time'=> date('Y-m-d H:i',strtotime($request->att_date)), 
                        'attendance_type'=>$request->type_select, 
                        'shift_id'=>1
                    ];

                $Employee = DB::table('Tbl_hr_attendence_dtl')
                ->insert([  $data ]);

                $array=$request->pd;
                foreach($array as $arr){

                    // dd($arr);

                    $p_id= DB::table('Tbl_hr_attendence_dtl')->max('id') +1;

                    $data=[
                        'id'=>$p_id, 
                        'emp_id'=>$arr['employee_select'], 
                        'attendance_date'=>date('Y-m-d',strtotime($request->date)),
                        'attendance_time'=> date('Y-m-d H:i',strtotime($arr['att_date'])), 
                        'attendance_type'=>$arr['type_select'], 
                        'shift_id'=>1
                    ];

                    $Employee = DB::table('Tbl_hr_attendence_dtl')
                    ->insert([  $data ]);



                }
            // }
       
        // if(isset($id)){
        //     $data = array_merge($data, Utilities::returnJsonEditForm());
        //     $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
        //     return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        // }else{
        //     $data = array_merge($data, Utilities::returnJsonNewForm());
        //     $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
        //     return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        // }  
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
            $employee =TblHrEmployeeAttendance::where('employee_type_id',$id)->first();
            $employee->delete();
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
