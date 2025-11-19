<?php

namespace App\Http\Controllers\Setting;

use Exception;
use Validator;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use App\Models\TblDefiBank;
use Illuminate\Http\Request;


// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblPurcSupplierAccount;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Setting\UserActivityLogController;


class BankController extends Controller
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
    public static $page_title = 'Bank';
    public static $redirect_url = 'bank';
    public static $menu_dtl_id = '48';

    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblDefiBank::where('bank_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblDefiBank::where('bank_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        return view('setting.bank.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            
            $acc_code = TblAccCoa::where('chart_account_id','20319825131541')->where(Utilities::currentBC())->first();
            $level_no = 4;
            $parent_account_code = $acc_code->chart_code;
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;
            $chart_name = $request->name;

            if(isset($id)){
                $bank = TblDefiBank::where('bank_id',$id)->first();
                $acc_id = $bank->bank_account_id;
                if(empty($acc_id)){
                    $bank_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                    $bank->bank_account_id = $bank_account_id;
                }else{
                    $this->proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$acc_id);
                }
            }else{
                $bank = new TblDefiBank();
                $bank->bank_id = Utilities::uuid();
                // $bank->bank_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
            }
            $form_id = $bank->bank_id;
            $bank->bank_name = $request->name;
            $bank->bank_entry_status = isset($request->bank_entry_status)?"1":"0";
            $bank->business_id = auth()->user()->business_id;
            $bank->company_id = auth()->user()->company_id;
            $bank->branch_id = auth()->user()->branch_id;
            $bank->bank_user_id = auth()->user()->id;
            $bank->save();

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
            UserActivityLogController::store(self::$menu_dtl_id,'update',$bank->bank_id,self::$page_title);
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            UserActivityLogController::store(self::$menu_dtl_id,'add',$bank->bank_id,self::$page_title);
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
            $supBank = TblPurcSupplierAccount::where('supplier_bank_name',$id)->first();
            if($supBank == null)
            {
                $bank = TblDefiBank::where('bank_id',$id)->first();
                $bank->delete();
            }else{
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
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
        UserActivityLogController::store(self::$menu_dtl_id,'delete',$bank->bank_id,self::$page_title);
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
