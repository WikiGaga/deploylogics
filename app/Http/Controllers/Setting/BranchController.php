<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\TblSoftBranch;
use App\Models\TblDefiCountry;
use App\Models\TblDefiCity;
use App\Models\TblDefiCurrency;
use App\Models\TblSoftBusiness;
use App\Models\TblSoftBusinessNature;
use App\Models\TblSoftBusinessType;
use App\Models\TblInveItemStockTransfer;
use App\Models\TblInveStock;
use App\Models\TblDefiStore;
use App\Models\TblAccCoa;
use App\Models\User;
use App\Models\TblSaleCustomer;
use Carbon\Carbon;
use Image;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use App\Library\Utilities;
// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BranchController extends Controller
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
    public static $page_title = 'Branch';
    public static $redirect_url = 'branch-profile';
    public static $menu_dtl_id = '67';
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
            if(TblSoftBranch::where('branch_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data']['type'] = 'edit';
                $data['id'] = $id;
                $data['current'] = TblSoftBranch::where('branch_id',$id)->first();
            }else{
                abort('404');
            }
        }else{
            $data['page_data']['type'] = 'new';
            $data['permission'] = self::$menu_dtl_id.'-create';
        }
        $data['city'] = TblDefiCountry::with('country_cities')->where('country_entry_status',1)->get();
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->get();
        $data['type'] = TblSoftBusinessType::where('business_type_entry_status',1)->get();
        $data['nature'] = TblSoftBusinessNature::where('business_nature_entry_status',1)->get();
        $data['business'] = TblSoftBusiness::get();
        $data['Chart_L4']= TblAccCoa::select('chart_code','chart_name','chart_Account_id')->where('chart_level', '=',4)->get();
        return view('setting.branch.form', compact('data'));
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
            'branch_name' => 'required|max:50',
            'branch_short_name' => 'required|max:20',
            'branch_email' => 'required|max:50'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $branch = TblSoftBranch::where('branch_id',$id)->first();
                $branch->update_id = Utilities::uuid();
            }else{
                $branch = new TblSoftBranch();
                $branch_id = TblSoftBranch::max('branch_id');
                $branch->branch_id = $branch_id+1;
            }
            $form_id = $branch->branch_id;
            $branch->branch_name = $request->branch_name;
            $branch->branch_short_name = $request->branch_short_name;
            $branch->branch_name_arabic = $request->branch_name_arabic;
            $branch->branch_short_name_arabic = $request->branch_short_name_arabic;
            $branch->branch_entry_date_time = Carbon::now();
            if($request->branch_city != 0){
                $branch->city_id = $request->branch_city;
                $country_id = TblDefiCity::with('city_country')->where('city_id',$request->branch_city)->where('city_entry_status',1)->first();
                $branch->country_id = $country_id->city_country['country_id'];
            }else{
                $branch->city_id = '';
                $branch->country_id = '';
            }
            $branch->business_id = $request->business_id;
            $branch->company_id = $request->business_id;
            $branch->branch_email = $request->branch_email;
            $branch->branch_website = $request->branch_website;
            $branch->branch_mobile_no = $request->branch_mobile_no;
            $branch->branch_whatsapp = $request->branch_whatsapp_no;
            $branch->branch_land_line_no = $request->branch_land_line_no;
            $branch->branch_fax = $request->branch_fax;

            if($request->hasFile('branch_profile'))
            {
                $image = $request->file('branch_profile');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path('/images/' . $filename);
                Image::make($image->getRealPath())->resize(200, 200)->save($path);
                $branch->branch_logo = isset($filename)?$filename:'';
            }

            $branch->branch_address = $request->branch_address;
            $branch->branch_google_address = $request->branch_google_address;
            $branch->branch_latitude = $request->branch_latitude;
            $branch->branch_longitude = $request->branch_longitude;
            $branch->branch_type = $request->business_type;
            $branch->branch_currency_id = $request->branch_currency;
            $branch->branch_nature = $request->business_nature;
            $branch->branch_tax_certificate_no = $request->branch_tax_certificate_no;
            $branch->branch_size = isset($request->branch_size)?'1':'0';
            $branch->branch_maximum_employment_size = $request->branch_maximum_employment_size;
            $branch->branch_local_employment_size = $request->branch_local_employment_size;
            $branch->branch_foreign_employment_size = $request->branch_foreign_employment_size;
            $branch->branch_omanization_rate = $request->branch_omanization_rate;
            $branch->branch_cr_no = $request->branch_cr_no;
            $branch->branch_account_code = $request->branch_account_code;
            $branch->branch_confirmation_email = $request->branch_confirmation_email;
            $branch->branch_active_status = isset($request->branch_active_status)?"1":"0";
            $branch->save();

            //default store creation
            if(empty($id)){
                $store = new TblDefiStore();
                $store->store_id = Utilities::uuid();
                $store->store_name = $request->branch_short_name;
                $store->store_branch = $branch->business_id;
                $store->store_entry_status = 1;
                $store->store_default_value = 0;
                $store->business_id = $branch->business_id;
                $store->company_id = $branch->company_id;
                $store->branch_id = $branch->branch_id;
                $store->store_user_id = auth()->user()->id;
                $store->save();
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

            $StockTransferFrom = TblInveItemStockTransfer::where('item_stock_transfer_from_store',$id)->first();
            $StockTransferTo = TblInveItemStockTransfer::where('item_stock_transfer_to_store',$id)->first();
            $StockRecevFrom = TblInveStock::where('stock_branch_from_id',$id)->first();
            $StockRecevTo = TblInveStock::where('stock_branch_to_id',$id)->first();
            $user = User::where('branch_id',$id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            $user_branch = DB::table('tbl_soft_user_branch')->where('branch_id', $id)->get();
            //$customer =TblSaleCustomer::where('customer_branch_id',$id)->first();

            if($StockTransferFrom == null && $StockTransferTo == null && $StockRecevFrom == null && $StockRecevTo == null && $user == null && count($user_branch) === 0)
            {
                $branch = TblSoftBranch::where('branch_id',$id)->first();
                $branch->delete();
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
