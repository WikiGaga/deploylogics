<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\TblDefiCountry;
use App\Models\TblDefiCity;
use App\Models\TblDefiCurrency;
use App\Models\TblSoftBusiness;
use App\Models\TblSoftBusinessNature;
use App\Models\TblSoftBusinessType;
use App\Models\TblSoftBranch;
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

class BusinessController extends Controller
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
    public static $page_title = 'Company';
    public static $redirect_url = 'business';
    public static $menu_dtl_id = '1';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSoftBusiness::where('business_id','LIKE',$id)->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data']['type'] = 'edit';
                $data['id'] = $id;
                $data['current'] = TblSoftBusiness::where('business_id',$id)->first();
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
        return view('purchase.business.form', compact('data'));
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
            'business_name' => 'required|max:50',
            'business_short_name' => 'required|max:20',
            'business_email' => 'required|max:50'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $business = TblSoftBusiness::where('business_id',$id)->first();
            }else{
                $uuid = Uuid::generate()->string;
                $business = new TblSoftBusiness();
                $business_id = TblSoftBusiness::max('business_id');
                $business->business_id  = $business_id+1;
                $business->business_uuid = $uuid;
            }
            $form_id = $business->business_id;
            $business->business_name = $request->business_name;
            $business->business_short_name = $request->business_short_name;
            $business->business_start_date = Carbon::now();
            if($request->business_city != 0){
                $business->business_city = $request->business_city;
                $country_id = TblDefiCity::with('city_country')->where('city_id',$request->business_city)->where('city_entry_status',1)->first();
                $business->business_country = $country_id->city_country['country_id'];
            }else{
                $business->business_city = '';
                $business->business_country = '';
            }
            $business->business_email = $request->business_email;
            $business->business_website = $request->business_website;
            $business->business_mobile_no = $request->business_mobile_no;
            $business->business_whatsapp_no = $request->business_whatsapp_no;
            $business->business_land_line_no = $request->business_land_line_no;
            $business->business_fax = $request->business_fax;
            $business->business_address = $request->business_address;
            if($request->hasFile('business_profile'))
            {
                $image = $request->file('business_profile');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path('/images/' . $filename);
                Image::make($image->getRealPath())->resize(200, 200)->save($path);
                $business->business_profile = isset($filename)?$filename:'';
            }

            $business->business_google_address = $request->business_google_address;
            $business->business_latitude = $request->business_latitude;
            $business->business_longitude = $request->business_longitude;
            $business->type_id = $request->business_type;
            $business->currency_id = $request->business_currency;
            $business->nature_id = $request->business_nature;
            $business->business_tax_certificate_no = $request->business_tax_certificate_no;
            $business->business_company_size = $request->business_company_size;
            $business->business_maximum_employment_size = $request->business_maximum_employment_size;
            $business->business_local_employment_size = $request->business_local_employment_size;
            $business->business_foreign_employment_size = $request->business_foreign_employment_size;
            $business->business_omanization_rate = $request->business_omanization_rate;
            $business->business_cr_no = $request->business_cr_no;
            $business->business_confirmation_email = $request->business_confirmation_email;
            $business->save();
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
            $branch = TblSoftBranch::where('business_id',$id)->first();
            if($branch == null)
            {
                $business = TblSoftBusiness::where('business_id',$id)->first();
                $business->delete();
            }else{
                return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
            }

        }catch (QueryException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    }
}
