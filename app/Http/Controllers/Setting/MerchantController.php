<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblAccCoa;
use App\Models\TblDefiMerchant;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'Merchant';
    public static $redirect_url = 'merchant';
    public static $menu_dtl_id = '268';

    public function index()
    {

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
            if(TblDefiMerchant::where('merchant_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblDefiMerchant::where('merchant_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        return view('setting.merchant.form',compact('data'));
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
            if(isset($id)){
                $merchant = TblDefiMerchant::where('merchant_id',$id)->first();
            }else{
                $level_no = 4;
                $parent_account_code = "6-01-03-0000";
                $customer_group = TblAccCoa::where('chart_code',$parent_account_code)->where(Utilities::currentBC())->first('chart_code');
                $parent_account_code = $customer_group->chart_code;
                $business_id = auth()->user()->business_id;
                $company_id = auth()->user()->company_id;
                $branch_id = auth()->user()->branch_id;
                $user_id = auth()->user()->id;
                $chart_name = $request->name;
                $merchant_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);

                $merchant = new TblDefiMerchant();
                $merchant->merchant_id = Utilities::uuid();
                $merchant->merchant_account_id = $merchant_account_id;
                $merchant->update_id = Utilities::uuid();
            }
            $form_id = $merchant->merchant_id;
            $merchant->merchant_name = $request->name;
            $merchant->merchant_short_name = $request->short_name;
            $merchant->merchant_gst = $request->gst;
            $merchant->merchant_excise_duty = $request->excise_duty;
            $merchant->merchant_max_consume_amount = $request->consume_amount;
            $merchant->merchant_commission = $request->merchant_commission;
            $merchant->merchant_entry_status = isset($request->merchant_entry_status)?"1":"0";
            $merchant->merchant_user_id = auth()->user()->id;
            $merchant->business_id = auth()->user()->business_id;
            $merchant->company_id = auth()->user()->company_id;
            $merchant->branch_id = auth()->user()->branch_id;
            // dd($merchant);
            $merchant->save();
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
        //
    }
}
