<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\ShiftSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Exception;
use App\Library\Utilities;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShiftSessionsController extends Controller
{

    public static $page_title = 'Shift Sessions';
    public static $redirect_url = 'shift_sessions';
    public static $menu_dtl_id = '354';
    public static $type = 'shift_sessions';
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
    public function create($id=null)
    {   
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['permission'] = self::$menu_dtl_id.'-edit';
        $data['page_data']['type'] = 'edit';
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['permission'] = self::$menu_dtl_id.'-create';

        // $id=603521645;

        if(isset($id)){
        $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
        $data['current'] = ShiftSession::with('branch')->where('session_id', $id)->first();
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
             $data['current'] = [];
        }

        return view('sales.shift_sessions.form', compact('data'));

        //  $data['att_id']=$id;
        // $data['form_type'] = 'Att';
        // $data['page_data'] = [];
        // $data['page_data']['title'] = self::$page_title;
        // $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        // $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        // $data['menu_id'] = self::$menu_dtl_id;
        // $data['page_data']['pending_pr'] = TRUE;
        // $data['already_exits'] = false;
        // $data['permission'] = self::$menu_dtl_id.'-create';

        // $data['employee'] =  DB::table('tbl_payr_employee')
        // ->select("EMPLOYEE_ID","EMPLOYEE_NAME")
        // ->get();


        //  $data['att_data'] =[];
        //  $data['att_note'] = '';
        //  $data['att_date'] = '';

        // if(isset($id)){
        //     $Tbl_hr_attendence=DB::table('Tbl_hr_attendence')->where('id',$id)->first();
           
        //     if(!empty($Tbl_hr_attendence)){
        //         $Tbl_hr_attendence_dtl = DB::table('Tbl_hr_attendence_dtl')->where('att_id',$id)->get();
        //         $data['att_data'] = $Tbl_hr_attendence_dtl;
        //         $data['att_no'] = $Tbl_hr_attendence->att_no;
        //         $data['att_note'] = $Tbl_hr_attendence->att_note;
        //         $data['att_date'] = $Tbl_hr_attendence->att_date;
        //         $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
        //     }else{
        //         abort('404');
        //     }
           
        // }else{
        //     // $data['permission'] = $data['stock_menu_id'].'-create';
        //     $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        //     $max_voucher = TblHrEmployeeAttendance::where(Utilities::currentBCB())->max('att_no');
        //     $data['att_no'] = $this->documentCode($max_voucher,'ATT');
        // }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'opening_cash' => 'required|numeric',
            'opening_visa' => 'required|numeric',
            'closing_cash' => 'required|numeric',
            'closing_visa' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->jsonErrorResponse($validator->errors(), 'Validation failed. Please check the input data.', 422);
        }

        DB::beginTransaction();
        try {

            $shiftSession = ShiftSession::find($id);
            $shiftSession->opening_cash = $request->opening_cash ?? 0; // Set opening cash to 0 if it's null
            $shiftSession->closing_cash = $request->closing_cash;
            $shiftSession->opening_visa = $request->opening_visa ?? 0; // Set opening visa to 0 if it's null
            $shiftSession->closing_visa = $request->closing_visa;
            $shiftSession->save();

            DB::commit();
            
            return $this->jsonSuccessResponse([],'Shift session updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonErrorResponse([], 'An error occurred while updating the shift session. Please try again.', 500);
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
        // dd($id);

         $data = [];
        DB::beginTransaction();
        try{
            ShiftSession::where('session_id',$id)->delete();
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
