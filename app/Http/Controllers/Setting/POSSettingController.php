<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftPOSSetting;
use App\Models\TblSoftBranch;
use App\Models\TblAccCoa;
use App\Models\User;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class POSSettingController extends Controller
{
    public static $page_title = 'POS Setting';
    public static $redirect_url = 'pos-setting';
    public static $menu_dtl_id = '162';
    //public static $menu_dtl_id = '141';

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
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSoftPOSSetting::where('pos_setting_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSoftPOSSetting::where('pos_setting_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['users'] = User::where('user_type','pos')->where('user_entry_status',1)->where(Utilities::currentBC())->get();
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        $data['acc_code']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->where('chart_code','like', '6-01-02'."%")->where(Utilities::currentBC())->orderBy('chart_code')->get();
        
        return view('setting.pos_setting.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        //dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|not_in:0',
            'branch_id' => 'required|not_in:0',
            'chart_id' => 'required|not_in:0'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{

            if(isset($id)){
                $setting = TblSoftPOSSetting::where('pos_setting_id',$id)->first();
                $setting->update_id = Utilities::uuid();
            }else{
                $setting = new TblSoftPOSSetting();
                $setting->pos_setting_id = Utilities::uuid();
            }
            $form_id = $setting->pos_setting_id;
            $setting->user_id = $request->user_id;
            $setting->branch_id = $request->branch_id;
            $setting->chart_id = $request->chart_id;
            $setting->hold_apply = ($request->hold_apply == 'on' )?"YES":"NO";
            $setting->delete_apply = ($request->delete_apply == 'on' )?"YES":"NO";
            $setting->cancel_bill = ($request->cancel_bill == 'on' )?"YES":"NO";
            $setting->photo_apply = ($request->photo_apply == 'on' )?"YES":"NO";
            $setting->return_apply = ($request->return_apply == 'on' )?"YES":"NO";
            $setting->return_apply_blank = ($request->return_blanck == 'on' )?"YES":"NO";

            $setting->save_apply = ($request->save_apply == 'on' )?"YES":"NO";
            $setting->less_qty_apply = ($request->less_qty_apply == 'on' )?"YES":"NO";
            $setting->customer_create_apply = ($request->customer_create_apply == 'on' )?"YES":"NO";
            $setting->holdprint_apply = ($request->holdprint_apply == 'on' )?"YES":"NO";
            $setting->inv_discount_apply = ($request->inv_discount_apply == 'on' )?"YES":"NO";
            $setting->forward_apply = ($request->forward_apply == 'on' )?"YES":"NO";
            $setting->loyalty_points_apply = ($request->redeem_loyalty_points == 'on' )?"YES":"NO";
            $setting->last_print_apply  = ($request->last_print_apply == 'on' )?"YES":"NO";
            $setting->list_print_apply  = ($request->list_print_apply == 'on' )?"YES":"NO";

            $setting->business_id = auth()->user()->business_id;
            $setting->company_id = auth()->user()->company_id;
            $setting->save();

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

            $setting = TblSoftPOSSetting::where('pos_setting_id',$id)->first();
            $setting->delete();

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
