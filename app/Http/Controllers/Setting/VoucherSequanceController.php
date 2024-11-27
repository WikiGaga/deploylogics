<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\TblSoftVoucherSquence;
use Illuminate\Http\Request;
use App\Library\Utilities;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;




class VoucherSequanceController extends Controller
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
    public static $page_title = 'Voucher Sequence Setting';
    public static $redirect_url = '';
    public static $menu_dtl_id = '179';
    //public static $menu_dtl_id = '154';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['permission'] = self::$menu_dtl_id.'-create';
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());

        $voucherSquence = TblSoftVoucherSquence::where(Utilities::currentBCB())->orderby('squence_sorting_order')->get('squence_voucher_type as voucher_type');
        if(count($voucherSquence) == 0){
            $data['voucher_types'] = DB::select('select distinct voucher_type from tbl_acco_voucher');
        }else{
            $data['voucher_types'] = $voucherSquence;
        }
        //dd($data['voucher_types']->toArray());
        return view('setting.voucher_squence.form',compact('data'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            $i=1;
            $sequance_id = Utilities::uuid();
            $del_Dtls = TblSoftVoucherSquence::where(Utilities::currentB())->get();
            if($del_Dtls !=null){
                foreach ($del_Dtls as $del_Dtls){
                    TblSoftVoucherSquence::where('squence_id',$del_Dtls->squence_id)->where(Utilities::currentB())->delete();
                }
            }

            if(isset($request->pd)){
                foreach ($request->pd as $dtl){
                    $sequance = new TblSoftVoucherSquence();
                    $sequance->squence_id = $sequance_id;
                    $sequance->squence_voucher_type = $dtl['type'];
                    $sequance->squence_sorting_order = $i++;
                    $sequance->business_id = auth()->user()->business_id;
                    $sequance->company_id = auth()->user()->company_id;
                    $sequance->branch_id = auth()->user()->branch_id;
                    $sequance->user_id = auth()->user()->id;
                    $sequance->save();
                }
            }
        }catch (QueryException $e) {
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
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
