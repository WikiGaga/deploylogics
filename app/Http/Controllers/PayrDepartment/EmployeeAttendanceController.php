<?php

namespace App\Http\Controllers\PayrDepartment;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Library\AttendancePersistence;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
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
        abort_unless(auth()->user()->isAbleTo(self::$menu_dtl_id . ($id ? '-edit' : '-create')), 403);
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

        $data['employee'] = $this->accessibleEmployees($id)->get();
        $accessibleEmployeeIds = $this->accessibleEmployees()->pluck('employee_id')->map(function ($employeeId) {
            return (string) $employeeId;
        })->all();
        $data['all_employee'] = $this->allCompanyEmployees()->get()->map(function ($employee) use ($accessibleEmployeeIds) {
            $employee->out_side_branch = in_array((string) $employee->employee_id, $accessibleEmployeeIds, true) ? 0 : 1;
            return $employee;
        });
        $branchFlags = $data['all_employee']->pluck('out_side_branch', 'employee_id');
        $data['employee']->each(function ($employee) use ($branchFlags) {
            $employee->out_side_branch = $branchFlags[$employee->employee_id] ?? 1;
        });


         $data['att_data'] =[];
         $data['att_note'] = '';
         $data['att_date'] = '';

        if(isset($id)){
            $data['permission'] = self::$menu_dtl_id.'-edit';
            $Tbl_hr_attendence=DB::table('Tbl_hr_attendence')
                ->where('id',$id)
                ->where(Utilities::currentBCB())
                ->first();

            if(!empty($Tbl_hr_attendence)){
                $Tbl_hr_attendence_dtl = DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)->orderByDesc('created_at')->get()
                    ->map(function ($attendance) {
                        $attendance->attendance_type = $this->normalizeAttendanceType($attendance->attendance_type);
                        return $attendance;
                    });
                $data['att_data'] = $Tbl_hr_attendence_dtl;
                $data['attendance_snapshot'] = Crypt::encryptString(json_encode([
                    'id' => (string) $id,
                    'header' => AttendancePersistence::fingerprint($Tbl_hr_attendence),
                    'details' => $Tbl_hr_attendence_dtl->mapWithKeys(function ($row) {
                        return [(string) $row->id => AttendancePersistence::fingerprint($row)];
                    })->all(),
                ]));
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
        abort_unless(auth()->user()->isAbleTo(self::$menu_dtl_id . ($id ? '-edit' : '-create')), 403);
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:d-m-Y',
            'pd' => 'required|array|min:1',
            'pd.*' => 'required|array',
            'pd.*.employee_select' => 'required|regex:/^[0-9]+$/',
            'pd.*.type_select' => 'required|in:check_in,check_out,Check-In,Check-Out',
            'pd.*.attendance_time' => 'required|string',
            'pd.*.attendance_detail_id' => 'nullable|regex:/^[0-9]+$/|distinct',
            'attendance_snapshot' => $id ? 'required|string' : 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => ['validator_errors' => $validator->errors()],
                'message' => 'Fields are not validate.',
            ], 422);
        }

        $data=[];
        DB::beginTransaction();
        try {
            AttendancePersistence::lockBranch(auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id);
            $attendanceDate = date('Y-m-d', strtotime($request->date));
            $allowedEmployeeIds = $this->allCompanyEmployees()->pluck('employee_id')->map(function ($value) {
                return (string) $value;
            })->all();

            foreach ($request->pd as $index => $row) {
                if (!in_array((string) $row['employee_select'], $allowedEmployeeIds, true)) {
                    throw ValidationException::withMessages([
                        "pd.$index.employee_select" => 'The selected employee is not available in the current company.',
                    ]);
                }
            }

            if(isset($id)){

                $exists_with_same_date = DB::table('Tbl_hr_attendence')
                ->where('att_date', date('Y-m-d', strtotime($request->date)))
                ->where('business_id', auth()->user()->business_id)
                ->where('company_id', auth()->user()->company_id)
                ->where('branch_id', auth()->user()->branch_id)
                ->where('id', '!=', $id)
                ->exists();

                if ($exists_with_same_date) {
                    DB::rollBack();
                    return $this->jsonErrorResponse($data, trans('message.attendance_already_exists'), 200);
                }

                $attendance = TblHrEmployeeAttendance::where('id',$id)
                    ->where(Utilities::currentBCB())
                    ->firstOrFail();

                try {
                    $snapshot = json_decode(Crypt::decryptString($request->attendance_snapshot), true);
                } catch (\Throwable $e) {
                    throw ValidationException::withMessages(['attendance_snapshot' => 'Please reload the attendance form.']);
                }
                if (($snapshot['id'] ?? null) !== (string) $id
                    || ($snapshot['header'] ?? null) !== AttendancePersistence::fingerprint($attendance->getRawOriginal())) {
                    throw ValidationException::withMessages(['attendance_snapshot' => 'Attendance changed. Reload the form before updating.']);
                }
                $loadedDetails = $snapshot['details'] ?? [];
                $currentDetails = DB::table('Tbl_hr_attendence_dtl')->where('att_id', $id)->get()->keyBy('id');
                foreach ($loadedDetails as $detailId => $version) {
                    if (!isset($currentDetails[$detailId]) || AttendancePersistence::fingerprint($currentDetails[$detailId]) !== $version) {
                        throw ValidationException::withMessages(['attendance_snapshot' => 'An attendance row changed. Reload the form before updating.']);
                    }
                }
                if ($attendanceDate !== date('Y-m-d', strtotime($attendance->att_date))
                    && $currentDetails->count() !== count($loadedDetails)) {
                    throw ValidationException::withMessages(['date' => 'New attendance was synced. Reload before changing the date.']);
                }

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
                ->where(Utilities::currentBCB())
                ->update(  $data );

                $keptDetailIds = [];
                foreach($request->pd as $arr){
                    $detailId = $arr['attendance_detail_id'] ?? null;
                    $detailData=[
                        'emp_id'=>$arr['employee_select'],
                        'attendance_date'=>$attendanceDate,
                        'attendance_time'=> $this->combineAttendanceDateAndTime($request->date, $arr['attendance_time']),
                        'attendance_type'=>$this->normalizeAttendanceType($arr['type_select']),
                        'shift_id'=>1,
                        'updated_at' => now()
                    ];

                    $existingDetail = $detailId ? DB::table('Tbl_hr_attendence_dtl')
                        ->where('id', $detailId)
                        ->where('att_id', $id)
                        ->first() : null;

                    if ($detailId && (!$existingDetail || !array_key_exists((string) $detailId, $loadedDetails))) {
                        throw ValidationException::withMessages(['pd' => 'Invalid attendance row. Reload the form.']);
                    }

                    if ($existingDetail) {
                        // Preserve historical app metadata unless the employee itself changes.
                        if ((string) $existingDetail->emp_id !== (string) $arr['employee_select']) {
                            $detailData['out_side_branch'] = $this->employeeIsOutsideCurrentBranch(
                                $arr['employee_select'], $existingDetail->branch_id ?? auth()->user()->branch_id
                            ) ? 1 : 0;
                        }
                        if (date('Y-m-d H:i', strtotime($existingDetail->attendance_time))
                            === substr($detailData['attendance_time'], 0, 16)) {
                            $detailData['attendance_time'] = $existingDetail->attendance_time;
                        }
                        unset($detailData['shift_id']);
                        DB::table('Tbl_hr_attendence_dtl')->where('id', $detailId)->update($detailData);
                        $keptDetailIds[] = $detailId;
                    } else {
                        $detailId = AttendancePersistence::id('Tbl_hr_attendence_dtl');
                        $outsideBranch = $this->employeeIsOutsideCurrentBranch($arr['employee_select']) ? 1 : 0;
                        DB::table('Tbl_hr_attendence_dtl')->insert(array_merge($detailData, [
                            'id'=>$detailId,
                            'type'=>'web',
                            'branch_id'=>auth()->user()->branch_id,
                            'out_side_branch'=>$outsideBranch,
                            'att_id'=>$id,
                            'created_at' => now(),
                        ]));
                        $keptDetailIds[] = $detailId;
                    }
                }

                DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)
                    ->whereIn('id', array_keys($loadedDetails))
                    ->whereNotIn('id', $keptDetailIds)->delete();

            }else{

                $exists_with_same_date = DB::table('Tbl_hr_attendence')
                ->where('att_date', date('Y-m-d', strtotime($request->date)))
                ->where('business_id', auth()->user()->business_id)
                ->where('company_id', auth()->user()->company_id)
                ->where('branch_id', auth()->user()->branch_id)
                ->exists();

                if ($exists_with_same_date) {
                    DB::rollBack();
                    return $this->jsonErrorResponse($data, trans('message.attendance_already_exists'), 200);
                }

                $att_id = AttendancePersistence::id('Tbl_hr_attendence');

                $data=[
                        'id'=>$att_id,
                        'att_date'=>date('Y-m-d',strtotime($request->date)),
                        'att_note'=> $request->att_note,
                        'att_no'=> $this->documentCode(TblHrEmployeeAttendance::where(Utilities::currentBCB())->max('att_no'), 'ATT'),
                        'business_id'=>auth()->user()->business_id,
                        'company_id'=>auth()->user()->company_id,
                        'branch_id'=>auth()->user()->branch_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    // dd(auth()->user()->branch_id);

                DB::table('Tbl_hr_attendence')->insert($data);

                $array=$request->pd;
                foreach($array as $arr){

                    $p_id = AttendancePersistence::id('Tbl_hr_attendence_dtl');

                    $outsideBranch = $this->employeeIsOutsideCurrentBranch($arr['employee_select']) ? 1 : 0;
                    $data=[
                        'id'=>$p_id,
                        'emp_id'=>$arr['employee_select'],
                        'attendance_date'=>date('Y-m-d',strtotime($request->date)),
                        'attendance_time'=> $this->combineAttendanceDateAndTime($request->date, $arr['attendance_time']),
                        'attendance_type'=>$this->normalizeAttendanceType($arr['type_select']),
                        'type'=>'web',
                        'branch_id'=>auth()->user()->branch_id,
                        'out_side_branch'=>$outsideBranch,
                        'shift_id'=>1,
                        'att_id'=>$att_id,
                        'created_at' => now(),
                        'updated_at' => now()

                    ];

                    $Employee = DB::table('Tbl_hr_attendence_dtl')->insert(  $data );
                }
            }

            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'data' => ['validator_errors' => $e->errors()],
                'message' => 'Fields are not validate.',
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Web attendance save failed', ['attendance_id' => $id, 'exception' => $e]);
            return $this->jsonErrorResponse([], 'Attendance could not be saved.', 200);
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
        abort_unless(auth()->user()->isAbleTo(self::$menu_dtl_id.'-delete'), 403);
        $data = [];
        DB::beginTransaction();
        try{
            AttendancePersistence::lockBranch(auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id);
            $employee = TblHrEmployeeAttendance::where('id',$id)
                ->where(Utilities::currentBCB())
                ->firstOrFail();
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

    /**
     * Keep attendance types consistent between mobile-app and web-form values.
     */
    private function normalizeAttendanceType($type)
    {
        $normalized = strtolower(str_replace(['_', ' '], '-', trim((string) $type)));

        if ($normalized === 'check-in') {
            return 'check_in';
        }

        if ($normalized === 'check-out') {
            return 'check_out';
        }

        return trim((string) $type);
    }

    /**
     * The grid collects time only; the attendance date is controlled by the
     * document header so a detail row cannot be saved against another date.
     */
    private function combineAttendanceDateAndTime($attendanceDate, $attendanceTime)
    {
        return AttendancePersistence::time(date('Y-m-d', strtotime($attendanceDate)), $attendanceTime);
    }

    private function accessibleEmployees($attendanceId = null)
    {
        $branchId = auth()->user()->branch_id;
        $existingEmployeeIds = $attendanceId
            ? DB::table('Tbl_hr_attendence_dtl')->where('att_id', $attendanceId)->pluck('emp_id')->all()
            : [];

        return DB::table('tbl_payr_employee as e')
            ->select('e.employee_id', 'e.employee_name')
            ->where('e.business_id', auth()->user()->business_id)
            ->where('e.company_id', auth()->user()->company_id)
            ->where(function ($query) use ($branchId, $existingEmployeeIds) {
                $query->where('e.branch_id', $branchId)
                    ->orWhereExists(function ($subQuery) use ($branchId) {
                        $subQuery->select(DB::raw(1))
                            ->from('tbl_payr_employee_branch as eb')
                            ->whereColumn('eb.employee_id', 'e.employee_id')
                            ->where('eb.branch_id', $branchId);
                    });
                if (!empty($existingEmployeeIds)) {
                    $query->orWhereIn('e.employee_id', $existingEmployeeIds);
                }
            })
            ->orderBy('e.employee_name');
    }

    private function allCompanyEmployees()
    {
        return DB::table('tbl_payr_employee as e')
            ->select('e.employee_id', 'e.employee_name')
            ->where('e.business_id', auth()->user()->business_id)
            ->where('e.company_id', auth()->user()->company_id)
            ->orderBy('e.employee_name');
    }

    private function employeeIsOutsideCurrentBranch($employeeId, $branchId = null)
    {
        $branchId = $branchId ?? auth()->user()->branch_id;

        return !DB::table('tbl_payr_employee as e')
            ->where('e.employee_id', $employeeId)
            ->where('e.business_id', auth()->user()->business_id)
            ->where('e.company_id', auth()->user()->company_id)
            ->where(function ($query) use ($branchId) {
                $query->where('e.branch_id', $branchId)
                    ->orWhereExists(function ($subQuery) use ($branchId) {
                        $subQuery->select(DB::raw(1))
                            ->from('tbl_payr_employee_branch as eb')
                            ->whereColumn('eb.employee_id', 'e.employee_id')
                            ->where('eb.branch_id', $branchId);
                    });
            })->exists();
    }
}
