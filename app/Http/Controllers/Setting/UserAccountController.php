<?php

namespace App\Http\Controllers\Setting;

use App\Models\Defi\TblDefiConstants;
use App\Http\Controllers\Controller;
use App\Models\Soft\TblSoftIpLocation;
use App\Models\Soft\TblSoftUserIp;
use App\Models\TblSoftBranch;
use App\Models\User;
use App\Library\Utilities;
use App\Models\TblSoftCompany;
use App\Models\UsersTypeAcco;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Image;
use App\Models\TblSaleSalesOrder;
use App\Models\TblSaleSales;
use App\Models\TblSaleDay;
use App\Models\TblAccoVoucher;
use App\Models\TblPurcDemand;
use App\Models\TblPurcProductBarcodeDtl;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static $page_title = 'User';
    public static $redirect_url = 'user_account';
    public static $menu_dtl_id = '35';


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
        $data['user_ip'] = [];
        if(isset($id)){
            if(User::where('id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = User::with('users_type_acco')->where('id',$id)->where(Utilities::currentBC())->first();
                //dd($data['current']->toArray());
                $data['pivot_default_branch']  = Utilities::getDefaultBranches($id);
                $data['pivot_optional_branch']  = Utilities::getOptionalBranches($id);
                $data['user_ip'] = TblSoftUserIp::where('user_id',$id)->pluck('ip_location_id')->toArray();
            }else{
                abort('404');
            }
        }else{
            $data['pivot_optional_branch'] = [];
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        }
        $data['branches'] = TblSoftBranch::all();
        $data['ip_location'] = TblSoftIpLocation::orderby('ip_location_name')->get();
        $data['user_types'] = TblDefiConstants::where('constants_type','user_logged_type')->where('constants_status',1)->get();


        return view('setting.UserAccount.form',compact('data'));
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
        if(isset($id)){
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'email' => 'required|max:50',
                'user_branch' => 'required',
                'user_type' => 'required',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'password' => 'required|max:20',
                'password_pos' => 'required|min:6|max:20',
                'email' => 'required|min:4|max:50',
                'user_branch' => 'required',
                'user_type' => 'required',
            ]);
        }
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $user = User::where('id',$id)->where('user_entry_status',1)->where(Utilities::currentBC())->first();

                $user->update_id = Utilities::uuid();
            }else{
                $user = new User();
                $user->id = Utilities::uuid();
                $user->password = Hash::make($request->password);
                $user->password_pos = $request->password_pos;
            }
            $form_id = $user->id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile_no = $request->mobile_no;
            $user->user_company = $request->user_company;
            $user->expiry_date = date('Y-m-d', strtotime($request->expiry_date));
            $user->expiry_account = isset($request->expiry_account)?"1":"0";
            $user->apply_warehouse = isset($request->apply_warehouse)?"1":"0";
            $user->apply = isset($request->apply)?"1":"0";
            $user->start_date = date('Y-m-d', strtotime($request->start_date));
            $user->end_date = date('Y-m-d', strtotime($request->end_date));
            $user->apply_time = isset($request->apply_time)?"1":"0";
            $user->ip_address = $request->ip_address;
            $user->ip_address_apply = isset($request->ip_address_apply)?"1":"0";
            $user->administrator =  isset($request->administrator)?"1":"0";
            $user->central_rate =  isset($request->central_rate)?"1":"0";
            $user->two_step_verification = isset($request->two_step_verification)?"1":"0";
            $user->two_step_verification_type = isset($request->two_step_verification_type)?$request->two_step_verification_type:"";

            if($request->hasFile('user_image'))
            {
                $image = $request->file('user_image');
                $filename = time().$image->getClientOriginalName();
                $path = public_path('/images/' . $filename);
                Image::make($image->getRealPath())->resize(200, 200)->save($path);
                $user->image_url = isset($filename)?$filename:'';
            }
            if($request->hasFile('user_signature'))
            {
                $image = $request->file('user_signature');
                $filename = time().$image->getClientOriginalName();
                $path = public_path('/images/' . $filename);
                Image::make($image->getRealPath())->resize(200, 200)->save($path);
                $user->degital_signature_url = isset($filename)?$filename:'';
            }
            $user->user_entry_status = isset($request->user_entry_status)?"1":"0";
            $user->branch_id = $request->user_branch;
            $user->user_type = $request->user_type;
            $branch = TblSoftBranch::where('branch_id', $request->user_branch)->first();
            $user->business_id = $branch->business_id;
            $user->company_id = $branch->company_id;
            $user->save();
            UsersTypeAcco::where('user_id',$user->id)->delete();
            if($request->user_type == 'customer'){
                UsersTypeAcco::create([
                    'id' => Utilities::uuid(),
                    'user_id' => $user->id,
                    'user_type' => 'customer',
                    'document_id' => $request->customer_id
                ]);
            }
            $filter_optional_branches = [];
            if(isset($request->optional_branches)){
                if(count($request->optional_branches) != 0){
                    foreach ($request->optional_branches as $opt_branches){
                        if($opt_branches != $request->user_branch){
                            array_push($filter_optional_branches, $opt_branches);
                        }
                    }
                }
            }
            if(isset($id)) {
                $all_branch = DB::table('tbl_soft_user_branch')->where('user_id', $id)->get();
                $del_branch = [];
                foreach ($all_branch as $optional_branch){
                    array_push($del_branch, $optional_branch->branch_id);
                }
                $user->userbranch()->detach($del_branch);

                $user->userbranch()->attach($filter_optional_branches,['default_branch' => 0]);
                $user->defaultbranch()->attach($request->user_branch,['default_branch' => 1]);
            }else{
                $user->userbranch()->attach($filter_optional_branches,['default_branch' => 0]);
                $user->defaultbranch()->attach($request->user_branch,['default_branch' => 1]);
            }

            TblSoftUserIp::where('user_id',$user->id)->delete();
            if(isset($request->ip) && count($request->ip) > 0){
                foreach ($request->ip as $ip){
                    $ipLocation = TblSoftIpLocation::where('ip_location_id',$ip)->first();
                    if(!empty($ipLocation)){
                        TblSoftUserIp::create([
                            'user_ip_uuid' => Utilities::uuid(),
                            'ip_location_id' => $ip,
                            'ip_location_address' => $ipLocation->ip_location_address,
                            'user_id' => $user->id,
                        ]);
                    }
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
            $sales_order = TblSaleSalesOrder::where('sales_order_sales_man',$id)->first();
            $sales = TblSaleSales::where('sales_sales_man',$id)->first();
            $dayOpen = TblSaleDay::where('saleman_id',$id)->first();
            /*$payment_handover = TblSaleDay::where('day_payment_handover',$id)->first();
            $payment_received = TblSaleDay::where('day_payment_received',$id)->first();
            */$voucher= TblAccoVoucher::where('saleman_id',$id)->first();
            $purchaseDemand = TblPurcDemand::where('salesman_id',$id)->first();
            $product = TblPurcProductBarcodeDtl::where('product_barcode_shelf_stock_sales_man',$id)->first();
            if($sales_order == null && $sales == null && $dayOpen == null && $voucher == null && $purchaseDemand == null && $product == null) {
                $user = User::where('id',$id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
                $user->delete();
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
