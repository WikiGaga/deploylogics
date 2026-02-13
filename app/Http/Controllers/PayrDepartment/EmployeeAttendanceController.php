<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrEmployeeAttendance;
use Illuminate\Http\Request;


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
       public function create($id = null)
    {
        $data['att_id']=$id;
        $data['form_type'] = 'Att';
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


         $data['att_data'] =[];
         $data['att_note'] = '';
         $data['att_date'] = '';

        if(isset($id)){
            $Tbl_hr_attendence=DB::table('Tbl_hr_attendence')->where('id',$id)->first();
           
            if(!empty($Tbl_hr_attendence)){
                $Tbl_hr_attendence_dtl = DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)->get();
                $data['att_data'] = $Tbl_hr_attendence_dtl;
                $data['att_no'] = $Tbl_hr_attendence->att_no;
                $data['att_note'] = $Tbl_hr_attendence->att_note;
                $data['att_date'] = $Tbl_hr_attendence->att_date;
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
            }else{
                abort('404');
            }
           
        }else{
            // $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $max_voucher = TblHrEmployeeAttendance::where(Utilities::currentBCB())->max('att_no');
            $data['att_no'] = $this->documentCode($max_voucher,'ATT');
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
        $data=[];
            if(isset($id)){
                $employee = TblHrEmployeeAttendance::where('id',$id)->first();

                  $data=[
                        'att_date'=>date('Y-m-d',strtotime($request->date)),
                        'att_note'=> $request->att_note, 
                        // 'att_no'=> $request->att_no, 
                        'business_id'=>auth()->user()->business_id,
                        'company_id'=>auth()->user()->company_id,
                        'branch_id'=>auth()->user()->branch_id,
                        'updated_at' => now(),
                    ];

                       
                $Employee = DB::table('Tbl_hr_attendence')
                ->where('id',$id)
                ->update(  $data );

                $Employee = DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)->delete();

                 $array=$request->pd;
                foreach($array as $arr){

                    $p_id= DB::table('Tbl_hr_attendence_dtl')->max('id') +1;

                    $data=[
                        'id'=>$p_id, 
                        'emp_id'=>$arr['employee_select'], 
                        'attendance_date'=>date('Y-m-d',strtotime($request->date)),
                        'attendance_time'=> date('Y-m-d H:i',strtotime($arr['attendance_time'])), 
                        'attendance_type'=>$arr['type_select'], 
                        'shift_id'=>1,
                        'att_id'=>$id,
                        'created_at' => now(),
                        'updated_at' => now()

                    ];

                    $Employee = DB::table('Tbl_hr_attendence_dtl')->insert([  $data ]);
                }

            }else{
                $p_id= DB::table('Tbl_hr_attendence')->max('id') +1;

                $data=[
                        'id'=>$p_id, 
                        'att_date'=>date('Y-m-d',strtotime($request->date)),
                        'att_note'=> $request->att_note, 
                        'att_no'=> $request->att_no, 
                        'business_id'=>auth()->user()->business_id,
                        'company_id'=>auth()->user()->company_id,
                        'branch_id'=>auth()->user()->branch_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                $att_id = DB::table('Tbl_hr_attendence')
                ->insertGetId( $data );

                $array=$request->pd;
                foreach($array as $arr){

                    $p_id= DB::table('Tbl_hr_attendence_dtl')->max('id') +1;

                    $data=[
                        'id'=>$p_id, 
                        'emp_id'=>$arr['employee_select'], 
                        'attendance_date'=>date('Y-m-d',strtotime($request->date)),
                        'attendance_time'=> date('Y-m-d H:i',strtotime($arr['attendance_time'])), 
                        'attendance_type'=>$arr['type_select'], 
                        'shift_id'=>1,
                        'att_id'=>$att_id,
                        'created_at' => now(),
                        'updated_at' => now()

                    ];

                    $Employee = DB::table('Tbl_hr_attendence_dtl')->insert([  $data ]);
                }
            }
       
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$att_id;
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
            $employee =TblHrEmployeeAttendance::where('id',$id)->first();
            $employee->delete();
            DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)->delete();
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
