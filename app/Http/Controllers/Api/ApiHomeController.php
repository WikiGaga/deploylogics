<?php

namespace App\Http\Controllers\Api;

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

        // dd($request->all()) ;
        // $validator = Validator::make($request->all(), [
        //      'customer_id'    => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Validation error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        // $bearerToken = $request->bearerToken();
        // if (!$bearerToken) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Authorization token missing'
        //     ], 401);
        // }

        // $user = User::where('authkey',  $bearerToken)->first();
        // if (!$user) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Invalid or expired token'
        //     ], 401);
        // }

    //      $Employee = DB::table('tbl_payr_employee as e')
    //   ->select("e.EMPLOYEE_ID AS ID","e.EMPLOYEE_CODE","e.EMPLOYEE_NAME","e.CREATED_AT")
    //     ->where('e.employee_id', $employee_id)
    //     ->first();

        $employee_id = $request->input('employee_id');

        $Employee = DB::table('tbl_payr_employee as e')
        ->LEFTJOIN('tbl_payr_gender as g','g.gender_id','=','e.GENDER_ID')
        ->LEFTJOIN('tbl_defi_city as c','c.city_id','=','e.EMPLOYEE_LOCAL_CITY_ID')
        ->LEFTJOIN('tbl_defi_country as c','c.country_id','=','e.EMPLOYEE_LOCAL_COUNTRY_ID')
        ->select("e.EMPLOYEE_ID AS ID","e.EMPLOYEE_CODE","e.EMPLOYEE_NAME","e.EMPLOYEE_IMG","e.EMPLOYEE_LOCAL_ADDRESS_1 as ADDRESS","e.EMPLOYEE_LOCAL_PERSONAL_EMAIL as EMAIL","e.EMPLOYEE_DATE_OF_BIRTH as DATE_OF_BIRTH","e.EMPLOYEE_LOCAL_PHONE_NO as PHONE_NO",
        "g.GENDER_NAME AS GENDER","c.CITY_NAME AS CITY","c.COUNTRY_NAME","e.REGISTER_STATUS","ATTENDANCE_IMAGE","e.IMAGE_EMBEDED_CODE","e.CREATED_AT")
        ->where('e.employee_id', $employee_id)
        ->first();

        if (empty($Employee)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404); // Use 404 Not Found HTTP status
        }

        $Employee->id = (int) $Employee->id;
        $Employee->employee_img = "images/employee/$Employee->employee_img";
 
        // 3. Return the user data as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'User data send successfully.',
            'Data' => $Employee,
        ], 200); // Use 200 OK HTTP status
    }

        public function get_all_employees(Request $request)
    {
        // $employee_id = $request->input('employee_id');

        //  $Employee = DB::table('tbl_payr_employee')
        // ->where('employee_id', 12123325261244)
        
        //  ->update(['EMPLOYEE_LOCAL_PERSONAL_EMAIL'=>'o26s26m@gmail.com']);

         $Employees = DB::table('tbl_payr_employee as e')
        ->LEFTJOIN('tbl_payr_gender as g','g.gender_id','=','e.GENDER_ID')
        ->LEFTJOIN('tbl_defi_city as c','c.city_id','=','e.EMPLOYEE_LOCAL_CITY_ID')
        ->LEFTJOIN('tbl_defi_country as c','c.country_id','=','e.EMPLOYEE_LOCAL_COUNTRY_ID')
        ->select("e.EMPLOYEE_ID AS ID","e.EMPLOYEE_CODE","e.EMPLOYEE_NAME","e.EMPLOYEE_IMG","e.EMPLOYEE_LOCAL_ADDRESS_1 as ADDRESS","e.EMPLOYEE_LOCAL_PERSONAL_EMAIL as EMAIL","e.EMPLOYEE_DATE_OF_BIRTH as DATE_OF_BIRTH","e.EMPLOYEE_LOCAL_PHONE_NO as PHONE_NO",
        "g.GENDER_NAME AS GENDER","c.CITY_NAME AS CITY","c.COUNTRY_NAME","e.REGISTER_STATUS","ATTENDANCE_IMAGE","e.IMAGE_EMBEDED_CODE","e.CREATED_AT")
        // ->where('employee_id', $employee_id)
        ->get();

        foreach($Employees as $Employee){

            $Employee->id = (int) $Employee->id;
            $Employee->employee_img = "images/employee/$Employee->employee_img";
        }

    //    dd($Employee);

        // if (empty($Employee)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Employee not found.',
        //     ], 404); // Use 404 Not Found HTTP status
        // }
 
        // 3. Return the user data as a JSON response
        return response()->json( $Employees); // Use 200 OK HTTP status
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
//    public function update_employee_face(Request $request)
//     {

//         $validator = Validator::make($request->all(), [
//              'emp_id'    => 'required',
//              'attendance_image'    => 'required',
//              'image_embeded_code'    => 'required',
             
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Validation error',
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         $employee_id = $request->input('emp_id');

//         $Employee = DB::table('tbl_payr_employee')
//         ->where('employee_id', $employee_id)
//         ->first();


//         if (empty($Employee)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Employee not found.',
//             ], 404); // Use 404 Not Found HTTP status
//         }

//         $attendance_image=NULL;
//         if($request->hasFile('attendance_image'))
//         {
//             $folder = 'images/employee_face/';
//             if (! File::exists($folder)) {
//                 File::makeDirectory($folder, 0775, true,true);
//             }
//             $image = $request->file('attendance_image');
//             $filename = time() . '.' . $image->getClientOriginalExtension();
//             $path = public_path($folder . $filename);
//             Image::make($image->getRealPath())->save($path);
//             $attendance_image = isset($filename)?$filename:'';
//         }

        

//         $data=['attendance_image'=>$attendance_image, 'image_embeded_code'=>$request->image_embeded_code,'REGISTER_STATUS'=>1];

//         // dd($data);

//         $Employee = DB::table('tbl_payr_employee')
//          ->where('employee_id', $employee_id)
//         ->UPDATE(  $data );

//         // 3. Return the user data as a JSON response
//         return response()->json([
//             'success' => true,
//             'message' => 'User data update successfully.',
//             'Data' => $Employee,
//         ], 200); // Use 200 OK HTTP status
//     }


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

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
            }

            //  dd($request->all(), $att_date);
            // 2. Check if the Main Attendance Header exists for this date
            // Fixed the syntax error from ->?id to a safe check
            $attendance_header = DB::table('Tbl_hr_attendence')
                ->where('att_date', $att_date)
                ->first();

                // dd($attendance_header);

            if (!$attendance_header) {
                // Create Header if it doesn't exist
                // Using insertGetId is better than max() + 1
                $att_id= DB::table('Tbl_hr_attendence')->max('id') +1;

                DB::table('Tbl_hr_attendence')->insert([
                    'id' => $att_id,
                    'att_date'    => $att_date,
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
