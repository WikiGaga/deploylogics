<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblHrEmployeeAttendance;
use Illuminate\Http\Request;
use App\Models\TblSoftBranch;
use App\Models\TblHrDepartment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeShiftController extends Controller
{
        public static $page_title = 'Roster';
    public static $redirect_url = '/roster';
    public static $menu_dtl_id = '117';

    public function roster(){

        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['branch'] = TblSoftBranch::where('business_id',auth()->user()->business_id)->where('company_id',auth()->user()->company_id)->orderBy('branch_name')->get();
        $data['department'] = TblHrDepartment::where('business_id',auth()->user()->business_id)->where('company_id',auth()->user()->company_id)->orderBy('department_name')->get();
        
        $employees = DB::table('tbl_payr_employee')
        //   ->select('employee_id as id','employee_name as name')
        ->get();

        return view('PayrDepartment.roster.index', compact('data','employees'));
    }
    
public function employees(Request $request)
{

    // dd($request->all());
    $branch_id=$request->branch_id;
    $department_id=$request->department_id;
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

public function bulkStore(Request $request)
{
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

    foreach ($validated['employee_ids'] as $empId) {
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            // FIXED: Removed the "!" - now checks if day IS selected
            if (in_array($date->dayOfWeek, $selectedDays)) {
                
                $start = $date->copy()->setTimeFromTimeString($config['start']);
                $end = $date->copy()->setTimeFromTimeString($config['end']);
                
                if ($config['next_day']) {
                    $end->addDay();
                }

                $insertData[] = [
                    'shift_user_id' => $empId,
                    'shift_start_time' => $start,
                    'shift_close_time' => $end,
                    'shift_name' => $type,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
    }

    // Batch insert for performance
    if (!empty($insertData)) {
        DB::table('tbl_payr_shift')->insert($insertData);
    }

    return response()->json([
        'message' => count($insertData) . ' shifts created successfully',
        'count' => count($insertData)
    ]);
}

public function get(Request $request)
{
    $request->validate([
        'start' => 'nullable|date',
        'end' => 'nullable|date'
    ]);

    
    $colors = [
        'morning' => '#2ecc71',
        'night' => '#3498db',
        'leave' => '#e74c3c'
    ];

    $query = DB::table('tbl_payr_shift as shifts')
        ->join('tbl_payr_employee as employees', 'employees.employee_id', '=', 'shifts.shift_user_id')
        ->select(
            'shifts.shift_id as id',
            'shifts.shift_start_time as start',
            'shifts.shift_close_time as end',
            'shifts.shift_user_id as resourceid',
            'shifts.shift_name',
            'shifts.shift_name as shift_type' // Added for eventClick
        );

    if ($request->filled(['start', 'end'])) {
        $query->whereBetween('shifts.shift_start_time', [$request->start, $request->end]);
    }

        $shifts = $query->get()->map(function ($s) use ($colors) {

        $shiftName = strtolower($s->shift_name); // Fix case issue

        // dd($s->shift_name,);

        return [
            'id'         => (string) $s->id,
            'resourceId' => (string) $s->resourceid,
            'title'      => ucfirst($shiftName),
            'start'      => $s->start,
            'end'        => $s->end,
            'shift_type' => $shiftName,
            'color'      => $colors[$shiftName] ?? '#95a5a6',
            'editable'   => $shiftName !== 'leave'
        ];
    });


    return response()->json($shifts);
}
// public function bulkStore(Request $request)
// {
//     $startDate = Carbon::parse($request->start_date);
//     $endDate = Carbon::parse($request->end_date);
//     $selectedDays = $request->selected_days; // e.g. [0, 6] for weekends

//     $period = CarbonPeriod::create($startDate, $endDate);

//     // Set times based on type
//     $times = [
//         'morning' => ['start' => '08:00:00', 'end' => '16:00:00'],
//         'night'   => ['start' => '22:00:00', 'end' => '06:00:00'],
//         'leave'   => ['start' => '00:00:00', 'end' => '23:59:59'],
//     ];

//     $type = $request->shift_type;
//     $sTime = $times[$type]['start'];
//     $eTime = $times[$type]['end'];

//     foreach ($request->employee_ids as $empId) {
//         foreach ($period as $date) {
//             // Only create shift if current date matches a selected checkbox day
//             if (!in_array($date->dayOfWeek, $selectedDays)) {
                
//                 $fullStart = $date->format('Y-m-d') . ' ' . $sTime;
                
//                 // For Night Shift, end date is the next morning
//                 $endLocalDate = ($type == 'night') ? $date->copy()->addDay() : $date;
//                 $fullEnd = $endLocalDate->format('Y-m-d') . ' ' . $eTime;

//                 $p_id= DB::table('tbl_payr_shift')->max('shift_id') +1;

//                 $data=[
//                     'shift_id' => $p_id,
//                     'shift_user_id'=>$empId,
//                     'shift_start_time'=> "$fullStart",
//                     'shift_close_time'=>"$fullEnd",
//                     'shift_name'=>$type,
//                     'created_at' => now(),
//                     'updated_at' => now()
//                 ];

//                 // dd($date->dayOfWeek, $selectedDays,$data);

//                 $id = DB::table('tbl_payr_shift')->insertGetId($data, 'shift_id');
//             }
//         }
//     }

//     return response()->json(['message' => 'Bulk shifts updated!']);
// }

//     public function get(Request $request)
// {
//     $start = $request->start;
//     $end   = $request->end;

//     $query = DB::table('tbl_payr_shift as shifts')
//         ->join('tbl_payr_employee as employees','employees.employee_id','=','shifts.shift_user_id')
//         ->select(
//             'shifts.shift_id as id',
//             'shifts.shift_start_time as start',
//             'shifts.shift_close_time as end',
//             'shifts.shift_user_id as resourceId',
//             'shifts.shift_user_id',
//             'shifts.shift_name'
//         );

//     // Only filter if FullCalendar sends dates
//     if ($start && $end) {
//         $query->whereBetween('shifts.shift_start_time', [$start, $end]);
//     }

//     $shifts = $query->get();

//     return response()->json(
//         $shifts->map(function($s){

//             $colors=[
//                 'morning'=>'#2ecc71',
//                 'night'=>'#3498db',
//                 'leave'=>'#e74c3c'
//             ];

//             return [
//                 'id' => (string)$s->id,
//                 'resourceId' => (string)$s->shift_user_id,
//                 'title' => ucfirst($s->shift_name),
//                 'start' => $s->start,
//                 'end' => $s->end,
//                 'color' => $colors[$s->shift_name] ?? '#95a5a6'
//             ];
//         })
//     );
// }


    public function store(Request $r){

        // dd($r->all());

        $p_id= DB::table('tbl_payr_shift')->max('shift_id') +1;

        $data=[
            'shift_id' => $p_id,
            'shift_user_id'=>$r->employee_id,
            'shift_start_time'=> $r->start,
            'shift_close_time'=>$r->end,
            'shift_name'=>$r->shift_type,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $id = DB::table('tbl_payr_shift')->insertGetId($data, 'shift_id');

        return response()->json(['id' => $id], 201);
    }

    public function update(Request $r)
    {
        // dd($r->all());
        $start = \Carbon\Carbon::parse($r->start);
        $end = \Carbon\Carbon::parse($r->end);

        // If start time is later than end time (e.g., 22:00 to 06:00), 
        // it's a night shift that ends the next day.
        if ($start->gt($end)) {
            $end->addDay();
        }

        DB::table('tbl_payr_shift')
            ->where('shift_id', $r->id)
            ->update([
                'shift_start_time' => $start,
                'shift_close_time' => $end,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }


    public function delete($id)
    {
        $deleted = DB::table('tbl_payr_shift')
            ->where('shift_id', (int)$id)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }


    //////////////////////////////////////////////////////////

    public function m_roster(){

        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        
        $employees = DB::table('tbl_payr_employee')
        ->get();

        return view('PayrDepartment.roster.m_index', compact('data','employees'));
    }


    public function m_get($employee){


        return DB::table('tbl_payr_shift as shifts')
        ->join('tbl_payr_employee as employees','employees.employee_id','=','shifts.shift_user_id')
        ->where('employees.employee_id',$employee)
        ->select(
            'shifts.shift_id as id',
            'shifts.shift_start_time as start',
            'shifts.shift_close_time as end',
            'shifts.shift_name',
            'shifts.shift_user_id as resourceId',
            'employees.employee_name as name'
        )->get()
        ->map(function($s){

            $colors=[
                'morning'=>'#2ecc71',
                'night'=>'#3498db',
                'leave'=>'#e74c3c'
            ];

            return [
                'id'=>(string)$s->id,
                'title'=>$s->name,
                'start'=>$s->start,
                'end'=>$s->end,
                'color'=>$colors[$s->shift_name] ?? '#95a5a6'
            ];
        });

    }



    public function m_store(Request $r){

        // dd($r->all());

        $p_id= DB::table('tbl_payr_shift')->max('shift_id') +1;

        $data=[
            'shift_id' => $p_id,
            'shift_user_id'=>$r->employee_id,
            'shift_start_time'=> $r->start,
            'shift_close_time'=>$r->end,
            'shift_name'=>$r->shift_type,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $id = DB::table('tbl_payr_shift')->insertGetId($data, 'shift_id');

        return response()->json(['id' => $id], 201);
    }

    public function m_update(Request $r)
    {
        DB::table('tbl_payr_shift')
            ->where('shift_id', $r->id)
            ->update([
                'shift_start_time' => $r->start,
                'shift_close_time' => $r->end,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }


    public function m_delete($id)
    {
        $deleted = DB::table('tbl_payr_shift')
            ->where('shift_id', (int)$id)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

}
