<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleCustomerType;
use App\Models\TblAccCoa;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerTypeController extends Controller
{
    public static $page_title = 'Customer Type';
    public static $redirect_url = 'customer-type';
    public static $menu_dtl_id = '43';
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
            if(TblSaleCustomerType::where('customer_type_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSaleCustomerType::where('customer_type_id',$id)->where(Utilities::currentBC())->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        return view('sales.customer_type.form',compact('data'));
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
            $level_no = 3;
            $parent_account_code = "6-02-00-0000";
            $customer_group = TblAccCoa::where('chart_code',$parent_account_code)->where(Utilities::currentBC())->first('chart_code');
            $parent_account_code = $customer_group->chart_code;
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;
            $chart_name = $request->name;

            if(isset($id)){
                $cust_type = TblSaleCustomerType::where('customer_type_id',$id)->where(Utilities::currentBC())->first();
                $acc_id = $cust_type->customer_type_account_id;
                if(empty($acc_id)){
                    $customer_type_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                    $cust_type->customer_type_account_id = $customer_type_account_id;
                }else{
                    $this->proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$acc_id);
                }
            }else{
                $customer_type_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                $cust_type = new TblSaleCustomerType();
                $cust_type->customer_type_id = Utilities::uuid();
                $cust_type->customer_type_account_id = $customer_type_account_id;
            }
            $form_id = $cust_type->customer_type_id;
            $cust_type->customer_type_name = $request->name;
            $cust_type->customer_type_entry_status = isset($request->customer_type_entry_status)?"1":"0";
            $cust_type->business_id = auth()->user()->business_id;
            $cust_type->company_id = auth()->user()->company_id;
            $cust_type->branch_id = auth()->user()->branch_id;
            $cust_type->customer_type_user_id = auth()->user()->id;
            $cust_type->save();

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
            return $this->jsonSuccessResponse($data, 'Customer  Type successfully updated.', 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, 'Customer  Type successfully created.', 200);
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
            $exist = TblSaleCustomer::where('customer_type',$id)->where(Utilities::currentBC())->exists();
            if(!$exist){
                $cust_type = TblSaleCustomerType::where('customer_type_id',$id)->where(Utilities::currentBC())->first();
                $business_id = auth()->user()->business_id;
                $company_id = auth()->user()->company_id;
                $branch_id = auth()->user()->branch_id;
                $acc_id = $cust_type->customer_type_account_id;
                $this->proPurcChartDelete($business_id,$company_id,$branch_id,$acc_id);
                $cust_type->delete();
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
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
