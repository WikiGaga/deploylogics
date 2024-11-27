<?php

namespace App\Http\Controllers\Accounts;

use DateTime;
use Exception;
use Validator;
use App\Models\User;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblDefiCurrency;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblAccoChequeManagment;
use Illuminate\Database\QueryException;
use App\Models\TblAccoChequeManagmentDtl;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChequeManagmentController extends Controller
{
    public static $page_title = 'Cheque Management';
    public static $redirect_url = 'cheque-management';
    public static $menu_dtl_id = '243';

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
    public function create(Request $request , $id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] =  $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblAccoChequeManagment::where('cheque_managment_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblAccoChequeManagment::with('dtls')->where('cheque_managment_id',$id)->where(Utilities::currentBC())->first();
                $data['code'] = $data['current']->cheque_managment_code;

                // dd($data['current']);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['code'] = $this->documentCode(TblAccoChequeManagment::max('cheque_managment_code'),'CHEQ');
        }

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_acco_cheque_managment',
            'col_id' => 'cheque_managment_id',
            'col_code' => 'cheque_managment_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        $data['users'] = User::where('user_type' , 'erp')->where('user_entry_status' , 1)->where('branch_id' , auth()->user()->branch_id)->get();
        return view('accounts.cheque_managment.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request , $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all() , [
            'cheque_entry_date' => 'required|date',
            'notify_to' => 'required',
            'notify_on' => 'required'
        ]);

        if($validator->fails()){
            return $this->jsonErrorResponse([] ,  trans('message.required_fields') , 200);
        }

        if(isset($request->notify_to)){
            $user = User::where('id' , $request->notify_to)->first();
            if($user->mobile_no == ''){
                return $this->jsonErrorResponse($data , 'This User do not have mobile number. Please add and try again.');
            }
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $cb = TblAccoChequeManagment::where('cheque_managment_id',$id)->where(Utilities::currentBC())->first();
            }else{
                $cb = new TblAccoChequeManagment();
                $cb->cheque_managment_id = Utilities::uuid();
                $cb->cheque_managment_code = $this->documentCode(TblAccoChequeManagment::max('cheque_managment_code'),'CHEQ'); 
            }
            
            $form_id = $cb->cheque_managment_id;
            $cb->cheque_managment_date = date('Y-m-d' , strtotime($request->cheque_entry_date));
            $cb->cheque_managment_remarks = $request->voucher_notes;
            $cb->notify_before_days = date('d' , strtotime($request->notify_on));
            $cb->notify_to = $request->notify_to;
            $cb->notify_on = date('Y-m-d' , strtotime($request->notify_on));
            $cb->business_id = auth()->user()->business_id;
            $cb->company_id = auth()->user()->company_id;
            $cb->branch_id = auth()->user()->branch_id;
            $cb->user_id = auth()->user()->id;
            $cb->save();

            if(isset($id)){
                TblAccoChequeManagmentDtl::where('cheque_managment_id' , $id)->delete();
            }

            if(isset($request->pd) && count($request->pd) > 0){
                $sr = 0;
                foreach ($request->pd as $cheque) {
                    $dtl = new TblAccoChequeManagmentDtl();
                    $dtl->cheque_managment_dtl_id = Utilities::uuid();
                    $dtl->cheque_managment_id =  $cb->cheque_managment_id;
                    $dtl->cheque_managment_dtl_type = $cheque['cheque_type'];
                    $dtl->receive_date = date('Y-m-d' , strtotime($cheque['cheque_receive_date']));
                    $dtl->cheque_date = date('Y-m-d' , strtotime($cheque['cheque_date']));
                    $dtl->cheque_no = $cheque['cheque_no'];
                    $dtl->chart_account_id = $cheque['account_id'];
                    $dtl->amount = $cheque['cheque_amount'];
                    $dtl->cheque_status = $cheque['cheque_status'];
                    $dtl->cheque_description = $cheque['voucher_descrip'];
                    $dtl->sr_no = ++$sr;
                    $dtl->save();
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
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
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

            $cb = TblAccoChequeManagment::where('cheque_managment_id',$id)->where(Utilities::currentBC())->first();
            $cb->dtls()->delete();
            $cb->delete();

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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
