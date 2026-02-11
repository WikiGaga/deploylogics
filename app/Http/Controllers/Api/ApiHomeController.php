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

        $employee_id = $request->input('employee_id');

        $Employee = DB::table('tbl_payr_employee as e')
        ->LEFTJOIN('tbl_payr_gender as g','g.gender_id','=','e.GENDER_ID')
        ->LEFTJOIN('tbl_defi_city as c','c.city_id','=','e.EMPLOYEE_LOCAL_CITY_ID')
        ->LEFTJOIN('tbl_defi_country as c','c.country_id','=','e.EMPLOYEE_LOCAL_COUNTRY_ID')
        ->select("e.EMPLOYEE_ID AS ID","e.EMPLOYEE_CODE","e.EMPLOYEE_NAME","e.EMPLOYEE_IMG","e.EMPLOYEE_LOCAL_ADDRESS_1 as ADDRESS","e.EMPLOYEE_DATE_OF_BIRTH as DATE_OF_BIRTH","e.EMPLOYEE_LOCAL_PHONE_NO as PHONE_NO",
        "g.GENDER_NAME AS GENDER","c.CITY_NAME AS CITY","c.COUNTRY_NAME","e.REGISTER_STATUS","ATTENDANCE_IMAGE","e.IMAGE_EMBEDED_CODE","e.CREATED_AT")
        ->where('e.employee_id', $employee_id)
        ->first();

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

        public function get_all_employees(Request $request)
    {
        // $employee_id = $request->input('employee_id');

         $Employee = DB::table('tbl_payr_employee as e')
        ->LEFTJOIN('tbl_payr_gender as g','g.gender_id','=','e.GENDER_ID')
        ->LEFTJOIN('tbl_defi_city as c','c.city_id','=','e.EMPLOYEE_LOCAL_CITY_ID')
        ->LEFTJOIN('tbl_defi_country as c','c.country_id','=','e.EMPLOYEE_LOCAL_COUNTRY_ID')
        ->select("e.EMPLOYEE_ID AS ID","e.EMPLOYEE_CODE","e.EMPLOYEE_NAME","e.EMPLOYEE_IMG","e.EMPLOYEE_LOCAL_ADDRESS_1 as ADDRESS","e.EMPLOYEE_DATE_OF_BIRTH as DATE_OF_BIRTH","e.EMPLOYEE_LOCAL_PHONE_NO as PHONE_NO",
        "g.GENDER_NAME AS GENDER","c.CITY_NAME AS CITY","c.COUNTRY_NAME","e.REGISTER_STATUS","ATTENDANCE_IMAGE","e.IMAGE_EMBEDED_CODE","e.CREATED_AT")
        // ->where('employee_id', $employee_id)
        ->get();

        //   $Employee = DB::table('tbl_payr_employee as e')
        // ->LEFTJOIN('tbl_payr_gender as g','g.gender_id','=','e.GENDER_ID')
        // ->LEFTJOIN('tbl_defi_city as c','c.city_id','=','e.EMPLOYEE_LOCAL_CITY_ID')
        // ->LEFTJOIN('tbl_defi_country as c','c.country_id','=','e.EMPLOYEE_LOCAL_COUNTRY_ID')
        // ->select("e.EMPLOYEE_ID AS ID","e.EMPLOYEE_CODE","e.EMPLOYEE_NAME","e.EMPLOYEE_IMG","e.EMPLOYEE_LOCAL_ADDRESS_1 as ADDRESS","e.EMPLOYEE_DATE_OF_BIRTH as DATE_OF_BIRTH","e.EMPLOYEE_LOCAL_PHONE_NO as PHONE_NO",
        // "g.GENDER_NAME AS GENDER","c.CITY_NAME AS CITY","c.COUNTRY_NAME","e.REGISTER_STATUS","e.EMBEDED_CODE","e.CREATED_AT")
        // // ->where('employee_id', $employee_id)
        // ->get();

        // if (empty($Employee)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Employee not found.',
        //     ], 404); // Use 404 Not Found HTTP status
        // }
 
        // 3. Return the user data as a JSON response
        return response()->json( $Employee); // Use 200 OK HTTP status
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
   public function update_employee(Request $request)
    {

        $validator = Validator::make($request->all(), [
             'emp_id'    => 'required',
             'employee_img'    => 'required',
             'embeded_code'    => 'required',
             
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee_id = $request->input('emp_id');

        $Employee = DB::table('tbl_payr_employee')
        ->where('employee_id', $employee_id)
        ->first();

        // dd($Employee,DB::table('tbl_payr_employee')->get());

        if (empty($Employee)) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
            ], 404); // Use 404 Not Found HTTP status
        }

        $employee_img=NULL;
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
            $employee_img = isset($filename)?$filename:'';
        }

        

        $data=['employee_img'=>$employee_img, 'embeded_code'=>$request->embeded_code,'REGISTER_STATUS'=>'inactive'];

        // dd($data);

        $Employee = DB::table('tbl_payr_employee')
         ->where('employee_id', $employee_id)
        ->UPDATE(  $data );

        // 3. Return the user data as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'User data update successfully.',
            'Data' => $Employee,
        ], 200); // Use 200 OK HTTP status
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
