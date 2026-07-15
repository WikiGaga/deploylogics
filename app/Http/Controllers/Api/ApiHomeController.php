<?php

namespace App\Http\Controllers\Api;
use App\Models\TblHrEmployeeAttendance;
use App\Http\Controllers\ApiController;
use App\Library\Utilities;
use App\Models\TempPro;
use App\Models\TempProDtl;
use App\Models\TempTestProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Session;
use Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Image;


class ApiHomeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $business_id = $request->business_id;
        $branch_id = $request->branch_id;

        $offset = 0;
        $limit = 10;
        $pageNo = 1;
        if(isset($current_page)){
            $offset = $limit * ($current_page-1);
            $pageNo = $current_page;
        }

        $grnData = DB::table('vw_purc_grn')
            ->select('grn_id','grn_code','grn_date','supplier_name')
            ->where('grn_type','GRN')
            ->where('vw_purc_grn.business_id',$business_id)
            ->where('vw_purc_grn.company_id',$business_id)
            ->where('vw_purc_grn.branch_id',$branch_id);

        $total = $grnData->groupBy('grn_id','grn_code','grn_date','supplier_name')->orderby('grn_code', 'ASC')->get();

        $total = count($total);

        $data['title'] = 'Goods Received Note';
        $data['pageNo'] = $pageNo;
        $data['total_pages'] = ceil($total / $limit); // calculate total pages
        $allData = $grnData->skip($offset)->take($limit)->groupBy('grn_id','grn_code','grn_date','supplier_name')->orderby('grn_code', 'ASC')->get();

        $doc_data = [];
        foreach ($allData as $doc){
            $ite = [
                'id'  => $doc->grn_id,
                'row1' => $doc->grn_code,
                'row2' => "Date:".date('Y-m-d', strtotime($doc->grn_date))." ~ Supplier: ".$this->strUcWords($doc->supplier_name),
                'row3' => "",
                'action' => [
                    'edit' =>true,
                    'del' =>true,
                    'pdf' =>true,
                ],
            ];
            array_push($doc_data,$ite);
        }
        $data['list_data'] = $doc_data;

        return $this->ApiJsonSuccessResponse($data,'Test Api data');
    }

        public function get_employee(Request $request)
    {


        $employee_id = $request->input('employee_id');
        $branch_id = $request->input('branch_id');

        $Employee = DB::table('tbl_payr_employee as e')
        ->LEFTJOIN('tbl_payr_gender as g','g.gender_id','=','e.GENDER_ID')
        ->LEFTJOIN('tbl_defi_city as cty','cty.city_id','=','e.EMPLOYEE_LOCAL_CITY_ID')
        ->LEFTJOIN('tbl_defi_country as c','c.country_id','=','e.EMPLOYEE_LOCAL_COUNTRY_ID')
        ->select("e.EMPLOYEE_ID AS ID","e.EMPLOYEE_CODE","e.EMPLOYEE_NAME","e.EMPLOYEE_IMG","e.EMPLOYEE_LOCAL_ADDRESS_1 as ADDRESS","e.EMPLOYEE_LOCAL_PERSONAL_EMAIL as EMAIL","e.EMPLOYEE_DATE_OF_BIRTH as DATE_OF_BIRTH","e.EMPLOYEE_LOCAL_PHONE_NO as PHONE_NO",
        "e.branch_id","g.GENDER_NAME AS GENDER","cty.CITY_NAME AS CITY","c.COUNTRY_NAME","e.REGISTER_STATUS","ATTENDANCE_IMAGE","e.IMAGE_EMBEDED_CODE","e.CREATED_AT")
        ->where('e.employee_id', $employee_id)
        // ->where('e.branch_id', $branch_id)
        ->first();

         $intFields = [
            'id',
            'branch_id',
            'register_status',
            // 'phone_no',
        ];

        if (empty($Employee)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404); // Use 404 Not Found HTTP status
        }

         $Employee->employee_branches = DB::table('tbl_soft_branch')
            ->join('tbl_payr_employee_branch', 'tbl_soft_branch.branch_id', '=', 'tbl_payr_employee_branch.branch_id' )
            ->select( 'tbl_soft_branch.branch_id', 'tbl_soft_branch.branch_short_name','tbl_soft_branch.branch_longitude','tbl_soft_branch.branch_latitude' )
            ->where('employee_id', $employee_id)
            ->get()->transform(function ($branch) {
                $branch->branch_id = (int) $branch->branch_id;
                return $branch;
            });

        $Employee->id = (int) $Employee->id;
        $Employee->employee_img = "images/employee/$Employee->employee_img";

        foreach ($intFields as $field) {
            if (isset($Employee->$field)) {
                $Employee->$field = (int) $Employee->$field;
            }
        }


        // 3. Return the user data as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'User data send successfully.',
            'Data' => $Employee,
        ], 200); // Use 200 OK HTTP status
    }

        public function get_all_employees(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $search = trim($request->input('search'));

        $Employees = DB::table('tbl_payr_employee as e')
            ->leftJoin('tbl_payr_gender as g', 'g.gender_id', '=', 'e.GENDER_ID')
            ->leftJoin('tbl_defi_city as cty', 'cty.city_id', '=', 'e.EMPLOYEE_LOCAL_CITY_ID')
            ->leftJoin('tbl_defi_country as c', 'c.country_id', '=', 'e.EMPLOYEE_LOCAL_COUNTRY_ID')
            ->where(function ($query) use ($branch_id) {
                $query->where('e.branch_id', $branch_id)
                    ->orWhereExists(function ($sub) use ($branch_id) {
                        $sub->select(DB::raw(1))
                            ->from('tbl_payr_employee_branch epb')
                            ->whereColumn('epb.employee_id', 'e.employee_id')
                            ->where('epb.branch_id', $branch_id);
                    });
            })
            ->select(
                "e.EMPLOYEE_ID AS ID",
                "e.EMPLOYEE_CODE",
                "e.EMPLOYEE_NAME",
                "e.EMPLOYEE_IMG",
                "e.EMPLOYEE_LOCAL_ADDRESS_1 as ADDRESS",
                "e.EMPLOYEE_LOCAL_PERSONAL_EMAIL as EMAIL",
                "e.EMPLOYEE_DATE_OF_BIRTH as DATE_OF_BIRTH",
                "e.EMPLOYEE_LOCAL_PHONE_NO as PHONE_NO",
                "e.branch_id",
                "g.GENDER_NAME AS GENDER",
                "cty.CITY_NAME AS CITY",
                "c.COUNTRY_NAME",
                "e.REGISTER_STATUS",
                "ATTENDANCE_IMAGE",
                "e.IMAGE_EMBEDED_CODE",
                "e.CREATED_AT"
            )
            // ->where('e.branch_id', $branch_id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('e.EMPLOYEE_NAME', 'like', "%{$search}%")
                    ->orWhere('e.EMPLOYEE_LOCAL_PERSONAL_EMAIL', 'like', "%{$search}%")
                    ->orWhere('e.EMPLOYEE_CODE', 'like', "%{$search}%");
                });
            })
            ->get();

        $intFields = [
            'id',
            'branch_id',
            'register_status',
        ];

        foreach ($Employees as $Employee) {

            $Employee->employee_img = "images/employee/$Employee->employee_img";

            $Employee->employee_branches = DB::table('tbl_soft_branch')
                ->join(
                    'tbl_payr_employee_branch',
                    'tbl_soft_branch.branch_id',
                    '=',
                    'tbl_payr_employee_branch.branch_id'
                )
                ->select(
                    'tbl_soft_branch.branch_id',
                    'tbl_soft_branch.branch_short_name','tbl_soft_branch.branch_longitude','tbl_soft_branch.branch_latitude'
                )
                ->where('employee_id', $Employee->id)
                ->get();

            foreach ($Employee->employee_branches as $branch) {
                $branch->branch_id = (int) $branch->branch_id;
            }

            foreach ($intFields as $field) {
                if (isset($Employee->$field)) {
                    $Employee->$field = (int) $Employee->$field;
                }
            }
        }

        return response()->json($Employees);
    }

    public function store_attendance(Request $request)
    {

        // dd($request->all()) ;
        $validator = Validator::make($request->all(), [
             'emp_id'    => 'required',
             'attendance_date'    => 'required',
             'attendance_time'    => 'required',
             'attendance_type'    => 'required',
             'shift_id'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }


        $p_id= DB::table('Tbl_hr_attendence_dtl')->max('id') +1;

        $data=[
            'id'=>$p_id, 'emp_id'=>$request->emp_id, 'attendance_date'=>date('Y-m-d',strtotime($request->attendance_date)),
             'attendance_time'=> date('Y-m-d H:i:s',strtotime($request->attendance_time)), 'attendance_type'=>$request->attendance_type, 'shift_id'=>$request->shift_id
        ];

        $Employee = DB::table('Tbl_hr_attendence_dtl')
        ->insert([  $data ]);

        if (empty($Employee)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404); // Use 404 Not Found HTTP status
        }

        // 3. Return the user data as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'User data send successfully.',
            'Data' => $Employee,
        ], 200); // Use 200 OK HTTP status
    }

    public function bulk_store_attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attendances'              => 'required|array|min:1',
            'attendances.*.emp_id'         => 'required',
            'attendances.*.attendance_date' => 'required',
            'attendances.*.attendance_time' => 'required',
            'attendances.*.attendance_type' => 'required',
            'attendances.*.shift_id'        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $insertData = [];
        $nextId = DB::table('Tbl_hr_attendence_dtl')->max('id') + 1;

        foreach ($request->attendances as $attendance) {
            $insertData[] = [
                'id'              => $nextId++,
                'emp_id'          => $attendance['emp_id'],
                'attendance_date' => date('Y-m-d', strtotime($attendance['attendance_date'])),
                'attendance_time' => date('Y-m-d H:i:s', strtotime($attendance['attendance_time'])),
                'attendance_type' => $attendance['attendance_type'],
                'shift_id'        => $attendance['shift_id'],
            ];
        }

        $inserted = DB::table('Tbl_hr_attendence_dtl')->insert($insertData);

        if (!$inserted) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store attendance records.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => count($insertData) . ' attendance record(s) stored successfully.',
            'total'   => count($insertData),
        ], 200);
    }

    public function update_employee_face(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'emp_id'             => 'required',
            'attendance_image'   => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'image_embeded_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $employee_id = $request->input('emp_id');

        // 2. Check if Employee exists
        $employee = DB::table('tbl_payr_employee')
            ->where('employee_id', $employee_id)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404);
        }

        $attendance_image = null;

        // 3. Handle File Upload
        if ($request->hasFile('attendance_image')) {
            try {
                $image = $request->file('attendance_image');
                $folder = 'images/employee_face';
                $destinationPath = public_path($folder);

                // Create directory if it doesn't exist
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }

                $filename = time() . '_' . $employee_id . '.' . $image->getClientOriginalExtension();

                // Use native move() instead of Intervention Image to bypass permission locks
                $image->move($destinationPath, $filename);

                $attendance_image = $filename;

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed: ' . $e->getMessage(),
                ], 500);
            }
        }

        // 4. Update Database
        $data = [
            'attendance_image'   => $attendance_image,
            'image_embeded_code' => $request->image_embeded_code,
            'REGISTER_STATUS'    => 1,
            'updated_at'         => now()
        ];

        try {
            DB::table('tbl_payr_employee')
                ->where('employee_id', $employee_id)
                ->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Employee face updated successfully.',
                'data'    => [
                    'image_name' => $attendance_image,
                    'emp_id'     => $employee_id
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database update failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function add_attendance(Request $request)
    {

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'emp_id'          => 'required',
            'attendance_time' => 'required',
            'attendance_type' => 'required', // e.g., 'Check-In' or 'Check-Out'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Wrap in a transaction to ensure data integrity
        return DB::transaction(function () use ($request) {

            $employee_id = $request->input('emp_id');
            $att_date = date('Y-m-d', strtotime($request->attendance_time));
            $att_full_time = date('Y-m-d H:i:s', strtotime($request->attendance_time));

            // 1. Verify Employee exists
            $employee = DB::table('tbl_payr_employee')
                ->where('employee_id', $employee_id)
                ->first();

            if (empty($employee)) {
                return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
            }

            // 2. Check if the Main Attendance Header exists for this date
            // Fixed the syntax error from ->?id to a safe check
            $attendance_header = DB::table('Tbl_hr_attendence')
                ->where('att_date', $att_date)
                ->first();

            if (!$attendance_header) {
                // Create Header if it doesn't exist
                // Using insertGetId is better than max() + 1
                $att_id= DB::table('Tbl_hr_attendence')->max('id') +1;
                //  $max_voucher = TblHrEmployeeAttendance::where(Utilities::currentBCB())->max('att_no');
                $max_voucher = TblHrEmployeeAttendance::max('att_no');
                $att_no = $this->documentCode($max_voucher,'ATT');

                DB::table('Tbl_hr_attendence')->insert([
                    'id' => $att_id,
                    'att_date'    => $att_date,
                    'att_no'    => $att_no,
                    'business_id' => $employee->business_id,
                    'company_id'  => $employee->company_id,
                    'branch_id'   => $employee->branch_id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } else {
                $att_id = $attendance_header->id;
            }

            $p_id= DB::table('Tbl_hr_attendence_dtl')->max('id') +1;
            // 3. Insert into Attendance Detail
            $detail_data = [
                'id'=>$p_id,
                'emp_id'          => $employee_id,
                'attendance_date' => $att_date,
                'attendance_time' => $att_full_time,
                'attendance_type' => $request->attendance_type,
                'shift_id'        => 1,
                'att_id'          => $att_id,
                'created_at'      => now(),
                'updated_at'      => now()
            ];

            DB::table('Tbl_hr_attendence_dtl')->insert($detail_data);

            return response()->json([
                'success' => true,
                'message' => 'Attendance logged successfully.',
                'type'    => $request->attendance_type,
                'time'    => $att_full_time
            ], 200);
        });
    }

   public function add_bulk_attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.attendance_time' => 'required|date',
            '*.attendance_type' => 'required|in:Check-In,Check-Out',
            '*.employees'       => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request) {

            $success = [];
            $failed = [];

            foreach ($request->all() as $attendance) {

                $employeeId = $attendance['employees'];
                $attendanceTime = date('Y-m-d H:i:s', strtotime($attendance['attendance_time']));
                $attendanceDate = date('Y-m-d', strtotime($attendance['attendance_time']));

                $employee = DB::table('tbl_payr_employee')
                    ->where('employee_id', $employeeId)
                    ->first();

                if (!$employee) {
                    $failed[] = [
                        'employee_id' => $employeeId,
                        'message' => 'Employee not found.'
                    ];
                    continue;
                }

                $header = DB::table('Tbl_hr_attendence')
                    ->where('att_date', $attendanceDate)
                    ->first();

                if (!$header) {

                    $attId = DB::table('Tbl_hr_attendence')->max('id') + 1;

                    $maxVoucher = TblHrEmployeeAttendance::max('att_no');
                    $attNo = $this->documentCode($maxVoucher, 'ATT');

                    DB::table('Tbl_hr_attendence')->insert([
                        'id' => $attId,
                        'att_no' => $attNo,
                        'att_date' => $attendanceDate,
                        'business_id' => $employee->business_id,
                        'company_id' => $employee->company_id,
                        'branch_id' => $employee->branch_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $attId = $header->id;
                }

                $detailId = DB::table('Tbl_hr_attendence_dtl')->max('id') + 1;

                DB::table('Tbl_hr_attendence_dtl')->insert([
                    'id' => $detailId,
                    'emp_id' => $employeeId,
                    'attendance_date' => $attendanceDate,
                    'attendance_time' => $attendanceTime,
                    'attendance_type' => $attendance['attendance_type'],
                    'shift_id' => 1,
                    'att_id' => $attId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $success[] = [
                    'employee_id' => $employeeId,
                    'attendance_type' => $attendance['attendance_type'],
                    'attendance_time' => $attendanceTime,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk attendance processed successfully.',
                'total_received' => count($request->all()),
                'successful' => count($success),
                'failed' => count($failed),
                'success_records' => $success,
                'failed_records' => $failed,
            ]);
        });
    }

    private function haversineDistance($point1, $point2) {
        $R = 6371000; // Earth radius in meters
        $lat1 = deg2rad($point1['latitude']);
        $lat2 = deg2rad($point2['latitude']);
        $dLat = deg2rad($point2['latitude'] - $point1['latitude']);
        $dLon = deg2rad($point2['longitude'] - $point1['longitude']);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos($lat1) * cos($lat2) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $R * $c; // Distance in meters
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $data = (object)[];
        return $this->ApiJsonSuccessResponse($data,'empty dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {

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
