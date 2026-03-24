<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrRoasterShift;
use Illuminate\Http\Request;


use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\TblSoftBranch;
use App\Models\TblHrDepartment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeRoasterController extends Controller
{
    /**EmployeeRoasterController
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
    public static $page_title = 'Employee Roaster';
    public static $redirect_url = 'Employee-Roaster';
    public static $menu_dtl_id='85';
       public function create($id = null)
    {
        // dd('ds');
        $data['roaster_id']=$id;
        $data['form_type'] = 'RST';
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

        $data['branch'] = TblSoftBranch::where('business_id',auth()->user()->business_id)->where('company_id',auth()->user()->company_id)->orderBy('branch_name')->get();
        $data['department'] = TblHrDepartment::where('business_id',auth()->user()->business_id)->where('company_id',auth()->user()->company_id)->orderBy('department_name')->get();
        
        $employees = DB::table('tbl_payr_employee')
        //   ->select('employee_id as id','employee_name as name')
        ->get();


         $data['roaster_data'] =[];
         $data['roaster_note'] = '';
         $data['roaster_date'] = '';

        if(isset($id)){
            $Tbl_hr_roaster_shift=DB::table('Tbl_hr_roaster_shift')->where('roaster_id',$id)->first();
           
            if(!empty($Tbl_hr_roaster_shift)){
                $Tbl_hr_roaster_shift_dtl = DB::table('Tbl_hr_roaster_shift_dtl')->where('roaster_id',$id)->get();
                $data['roaster_data'] = $Tbl_hr_roaster_shift_dtl;
                $data['roaster_no'] = $Tbl_hr_roaster_shift->roaster_no;
           
                $data['roaster_date'] = $Tbl_hr_roaster_shift->roaster_date;
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
            }else{
                abort('404');
            }
           
        }else{
            // $data['permission'] = $data['stock_menu_id'].'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $max_voucher = TblHrRoasterShift::where(Utilities::currentBCB())->max('roaster_no');
            $data['roaster_no'] = $this->documentCode($max_voucher,'RST');
        }

      
        return view('PayrDepartment.employee_roaster.form', compact('data'));
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
                $employee = TblHrRoasterShift::where('id',$id)->first();

                  $data=[
                        'att_date'=>date('Y-m-d',strtotime($request->date)),
                        'att_note'=> $request->att_note, 
                        // 'att_no'=> $request->att_no, 
                        'business_id'=>auth()->user()->business_id,
                        'company_id'=>auth()->user()->company_id,
                        'branch_id'=>auth()->user()->branch_id,
                        'updated_at' => now(),
                    ];

                       
                $Employee = DB::table('Tbl_hr_roaster_shift')
                ->where('id',$id)
                ->update(  $data );

                $Employee = DB::table('Tbl_hr_roaster_shift_dtl')->where('att_id',$id)->delete();

                 $array=$request->pd;
                foreach($array as $arr){

                    $p_id= DB::table('Tbl_hr_roaster_shift_dtl')->max('id') +1;

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

                    $Employee = DB::table('Tbl_hr_roaster_shift_dtl')->insert( $data );
                }

            }else{
                $att_id= DB::table('Tbl_hr_roaster_shift')->max('id') +1;

                $data=[
                        'id'=>$att_id, 
                        'att_date'=>date('Y-m-d',strtotime($request->date)),
                        'att_note'=> $request->att_note, 
                        'att_no'=> $request->att_no, 
                        'business_id'=>auth()->user()->business_id,
                        'company_id'=>auth()->user()->company_id,
                        'branch_id'=>auth()->user()->branch_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                $att_id = DB::table('Tbl_hr_roaster_shift')
                ->insert( $data );

                $array=$request->pd;
                foreach($array as $arr){

                    $p_id= DB::table('Tbl_hr_roaster_shift_dtl')->max('id') +1;

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

                    $Employee = DB::table('Tbl_hr_roaster_shift_dtl')->insert(  $data );
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
            $employee =TblHrRoasterShift::where('roaster_id',$id)->first();
            $employee->delete();
            DB::table('Tbl_hr_roaster_shift_dtl')->where('roaster_id',$id)->delete();
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

    public function employees(Request $request)
{

    // dd($request->all());
    $branch_id=(array) $request->branch_id;
    $department_id=(array) $request->department_id;
    $employees = DB::table('tbl_payr_employee')
        ->select(
            "employee_id as id",
            "employee_name as title"
        )
    ->when(!empty($branch_id ), function ($query) use ($branch_id) {
        return $query->whereIn('branch_id', $branch_id);
    })
    ->when(!empty($department_id ), function ($query) use ($department_id) {
        return $query->whereIn('department_id', $department_id);
    })
    ->get();

    return response()->json($employees);
}


    public function get(Request $request)
    {


        // dd($request->all());
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date'
        ]);

        
        $colors = [
            'morning' => '#2ecc71',
            'night' => '#3498db',
            'leave' => '#e74c3c'
        ];

        $query = DB::table('Tbl_hr_roaster_shift_dtl as shifts')
            ->join('tbl_payr_employee as employees', 'employees.employee_id', '=', 'shifts.employee_id')
            ->select(
                'shifts.roaster_id_dtl as id',
                'shifts.shift_start_time as start',
                'shifts.shift_close_time as end',
                'shifts.employee_id as resourceid',
                'shifts.shift_type'
            )->where('shifts.roaster_id', $request->roaster_id);

        if ($request->filled(['start', 'end'])) {
            // $query->whereBetween('shifts.shift_start_time', [$request->start, $request->end]);
            $query->where('shift_start_time', '>=', $request->start)
            ->where('shift_start_time', '<', $request->end);
        }


            $shifts = $query->get()->map(function ($s) use ($colors) {

            $shift_type = strtolower($s->shift_type); // Fix case issue

            // dd($s->shift_type,);

            return [
                'id'         => (string) $s->id,
                'resourceId' => (string) $s->resourceid,
                'title'      => ucfirst($shift_type),
                'start'      => $s->start,
                'end'        => $s->end,
                'shift_type' => $shift_type,
                'color'      => $colors[$shift_type] ?? '#95a5a6',
                'editable'   => $shift_type !== 'leave'
            ];
        });


        return response()->json($shifts);
    }

    public function bulkStore(Request $request)
    {

        // dd($request->all());
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'selected_days' => 'required|array',
            'shift_type' => 'required|in:morning,night,leave'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $selectedDays = array_map('intval', $validated['selected_days']);

        $times = [
            'morning' => ['start' => '08:00:00', 'end' => '16:00:00', 'next_day' => false],
            'night'   => ['start' => '22:00:00', 'end' => '06:00:00', 'next_day' => true],
            'leave'   => ['start' => '00:00:00', 'end' => '23:59:59', 'next_day' => false],
        ];

        $type = $validated['shift_type'];
        $config = $times[$type];
        $insertData = [];

        

        if(empty($request->roaster_id)){

            $roaster_id =  Utilities::uuid();

            $data=[
                'roaster_id'=>$roaster_id,
                'roaster_date'=>date('Y-m-d',strtotime($startDate)),
                'start_date'=> $startDate, 
                'end_date'=> $endDate, 
                'shift_type' => $type,
                'roaster_no'=> $request->roaster_no, 
                'business_id'=>auth()->user()->business_id,
                'company_id'=>auth()->user()->company_id,
                'branch_id'=>auth()->user()->branch_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('Tbl_hr_roaster_shift')->insert($data);

        }else{
            $roaster_id = $request->roaster_id;
        }
    

        foreach ($validated['employee_ids'] as $empId) {
            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                // FIXED: Removed the "!" - now checks if day IS selected
                if (!in_array($date->dayOfWeek, $selectedDays)) {
                    
                    $start = $date->copy()->setTimeFromTimeString($config['start'])->toDateTimeString();
                    $end = $date->copy()->setTimeFromTimeString($config['end'])->toDateTimeString();
                    
                    if ($config['next_day']) {
                        $end->addDay();
                    }

                    $roaster_id_dtl = Utilities::uuid();
                    $shift_date=date('Y-m-d',strtotime($start));

                    $insertData[] = [
                        'roaster_id_dtl'=>$roaster_id_dtl,
                        'roaster_id'=>$roaster_id,
                        'employee_id' => $empId,
                        'shift_date'=> $shift_date,
                        'shift_start_time' => $start,
                        'shift_close_time' => $end,
                        'shift_type' => $type,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $exists = DB::table('Tbl_hr_roaster_shift_dtl')
                        ->where('employee_id', $empId)
                        ->where('shift_date', $shift_date)
                        ->where('shift_type', $type,)
                        ->exists();

                    if($exists){
                        return response()->json([
                            'message' => "duplicate shift at $shift_date",
                            'count' => count($insertData),
                            'status' => 'duplicate',
                            'url' => ''
                        ]);
                    }
                }
            }
        }

        // Batch insert for performance
        if (!empty($insertData)) {
            DB::table('Tbl_hr_roaster_shift_dtl')->insert($insertData);
        }

        if(!empty($request->roaster_id)){
           return response()->json([
                'message' => count($insertData) . ' shifts created successfully',
                'count' => count($insertData),
                'status' => '',
                'url' => ''
            ]);

        }else{
                $url = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$roaster_id;  
                
                return response()->json([
                'message' => count($insertData) . ' shifts created successfully',
                'count' => count($insertData),
                'status' => 'redirect',
                'url' => $url
            ]);
        } 
    }



    public function r_store(Request $r){

        
        if(empty($r->roaster_id)){

            $roaster_id =  Utilities::uuid();

            $data=[
                'roaster_id'=>$roaster_id,
                'roaster_date'=>date('Y-m-d',strtotime($r->start)),
                'start_date'=> date('Y-m-d',strtotime($r->start)), 
                'end_date'=> date('Y-m-d',strtotime($r->end)),  
                'shift_type' => $r->shift_type,
                'roaster_no'=> $r->roaster_no, 
                'business_id'=>auth()->user()->business_id,
                'company_id'=>auth()->user()->company_id,
                'branch_id'=>auth()->user()->branch_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('Tbl_hr_roaster_shift')->insert($data);

        }else{
            $roaster_id = $r->roaster_id;
        }

        $shift_date=date('Y-m-d',strtotime($r->start));


        $data=[
            'roaster_id_dtl' => Utilities::uuid(),
            'roaster_id' => $roaster_id,
            'employee_id'=>$r->employee_id,
            'shift_date'=> $shift_date,
            'shift_start_time'=> $r->start,
            'shift_close_time'=>$r->end,
            'shift_type'=>$r->shift_type,
            'created_at' => now(),
            'updated_at' => now()
        ];



        $exists = DB::table('Tbl_hr_roaster_shift_dtl')
        ->where('employee_id', $r->employee_id)
        ->where('shift_date', $shift_date)
        ->where('shift_type', $r->shift_type,)
        ->exists();

        if($exists){
            return response()->json(['message' => "duplicate shift at $shift_date",'status' => 'duplicate' ,'url' => ''], 201);
        }

        $id = DB::table('Tbl_hr_roaster_shift_dtl')->insertGetId($data, 'roaster_id_dtl');

        if(!empty($r->roaster_id)){
            return response()->json(['id' => $id ,'status' => '' ,'url' => ''], 201);
        }else{
            $url = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$roaster_id;
            return response()->json(['id' => $id ,'status' => 'redirect' ,'url' => $url], 201);
        } 
    }

    public function r_update(Request $r)
    {
        // dd($r->all());
        $start = \Carbon\Carbon::parse($r->start);
        $end = \Carbon\Carbon::parse($r->end);

        // If start time is later than end time (e.g., 22:00 to 06:00), 
        // it's a night shift that ends the next day.
        if ($start->gt($end)) {
            $end->addDay();
        }

        DB::table('Tbl_hr_roaster_shift_dtl')
            ->where('roaster_id_dtl', $r->id)
            ->update([
                'employee_id'    => $r->employee_id,
                'shift_start_time' => $start,
                'shift_close_time' => $end,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }


    public function delete($id)
    {
        $deleted = DB::table('Tbl_hr_roaster_shift_dtl')
            ->where('roaster_id_dtl', (int)$id)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
