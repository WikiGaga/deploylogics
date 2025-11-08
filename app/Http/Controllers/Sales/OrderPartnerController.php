<?php

namespace App\Http\Controllers\Sales;

use App\Models\Sale\TblSaleCustomerMember;
use App\Models\TblDefiMembershipType;
use Exception;
use Intervention\Image\Image;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use App\Models\TblDefiCity;
use App\Models\TblSaleSales;
use Illuminate\Http\Request;
use App\Models\TblSoftBranch;
use App\Models\TblDefiCountry;
use App\Models\TblSaleCustomer;
use Illuminate\Validation\Rule;
use App\Models\TblSaleSalesOrder;
use App\Models\TblSaleCustomerDtl;
use App\Models\TblSaleSubCustomer;
use App\Models\TblSaleOrderPartner;

// db and Validator
use Illuminate\Support\Facades\DB;
use App\Models\TblSaleCustomerType;
use App\Http\Controllers\Controller;
use App\Models\Defi\TblDefiConstants;
use App\Models\TblDefiArea;
use App\Models\TblSaleCustomerBranch;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Models\Sale\WhatsappLog;

class OrderPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *PARTNER
     * @return \Illuminate\Http\Response
     */

    public static $page_title = 'Order Partners';
    public static $redirect_url = 'order-partners';
    public static $menu_dtl_id = '41';

    public function __construct()
    {
        $getStaticPrefix = Utilities::getStaticPrefix(self::$redirect_url);
        $this->current_path = $getStaticPrefix['path'];
        $this->page_form = '/'.self::$redirect_url.'/form';
        $this->page_view = '/'.self::$redirect_url.'/view';
    }

   

    public function create($id = null)
    {
        // dd($id);
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;

        if(isset($id)){
            if(TblSaleOrderPartner::where('partner_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblSaleOrderPartner::where(Utilities::currentBC())->where('partner_id',$id)->first();

                if(isset($data['current']->city_id)){
                    $data['areas'] = TblDefiArea::where('city_id' , $data['current']->city_id)->where('area_entry_status' , 1)->get();
                }else{
                    $data['areas'] = [];
                }
                $data['partner_code'] = $data['current']->partner_code;
                //$data['customer_branch'] = $name = explode(',',$data['current']->customer_branch_id);
            }else{
                abort('404');
            }
        }else{
            // Check SubDomain Of the Project
            if(TblDefiConstants::where('constants_key','subdomain')->where('constants_status',1)->exists()){
                $subdomain = TblDefiConstants::where('constants_key','subdomain')->first()->constants_value;
            }

            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());

            if(isset($subdomain) && $subdomain == 'adminalnawras'){
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleCustomer',
                    'code_field'        => 'customer_code',
                    'code_prefix'       => strtoupper('a')
                ];

                $data['partner_code'] = Utilities::customCustomerCode($doc_data);
            }else{
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleCustomer',
                    'code_field'        => 'customer_code',
                    'code_prefix'       => strtoupper('OP')
                ];

                $data['partner_code'] = Utilities::documentCode($doc_data);
            }
        }

        $data['city'] = TblDefiCountry::with('country_cities')->where('country_entry_status',1)->where(Utilities::currentBC())->get();

        $data['type'] = TblSaleCustomerType::where('customer_type_entry_status',1)->where(Utilities::currentBC())->get();

        // $data['refrence'] = [];//TblSaleCustomer::where('customer_entry_status',1)->where(Utilities::currentBC())->get();

        // $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();

        // $data['area'] = TblDefiArea::where('area_entry_status',1)->get();
        // $data['membership'] = TblDefiMembershipType::where('membership_type_entry_status',1)->get();

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['partner_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_customer',
            'col_id' => 'partner_id',
            'col_code' => 'partner_code',
        ];
        // $data['switch_entry'] = $this->switchEntry($arr);


        return view('sales.order-partner.form',compact('data','id'));
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
            'partner_name' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        if(!isset($id) && isset($request->partner_phone_1)){
            if(TblSaleOrderPartner::where('partner_phone_1','LIKE',$request->partner_phone_1)->where(Utilities::currentBC())->exists()){
                return $this->jsonErrorResponse($data, 'Partner Already Exist With This Phone/Mobile No.', 422);
            }
        }

        DB::beginTransaction();
        try{
            $cust_type = TblSaleCustomerType::where('customer_type_id',$request->partner_type)->where(Utilities::currentBC())->first();
            $acc_code = TblAccCoa::where('chart_account_id',$cust_type->customer_type_account_id)->where(Utilities::currentBC())->first();
            $level_no = 4;
            $parent_account_code = $acc_code->chart_code;
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;
            $chart_name = $request->partner_name;
            if(isset($id)){
                $OrderPartner =TblSaleOrderPartner::where('partner_id',$id)->where(Utilities::currentBC())->first();
                
                $acc_id = $OrderPartner->customer_account_id;
                if(empty($acc_id)){
                    $partner_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                    $OrderPartner->partner_account_id = $partner_account_id;
                }else{
                    $this->proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$acc_id);
                }

                $OrderPartner->update_id = Utilities::uuid();
            }else{
                $partner_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                $OrderPartner = new TblSaleOrderPartner();
                $OrderPartner->partner_id = Utilities::uuid();

                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'TblSaleOrderPartner',
                    'code_field'        => 'partner_code',
                    'code_prefix'       => strtoupper('op')
                ];

                $OrderPartner->partner_code = Utilities::documentCode($doc_data);

                $OrderPartner->partner_account_id = $partner_account_id;
                $OrderPartner->created_at = Carbon::now();
                $OrderPartner->updated_at = Carbon::now();
            }

            $form_id = $OrderPartner->partner_id;
            $OrderPartner->partner_name = $request->partner_name;
            $OrderPartner->partner_local_name = $request->partner_local_name;
            $OrderPartner->partner_entry_status = isset($request->partner_entry_status)?"1":"0";
           
            if($request->hasFile('customer_image'))
            {
                $image = $request->file('customer_image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path('/images/' . $filename);
                Image::make($image->getRealPath())->resize(200, 200)->save($path);
                $OrderPartner->partner_image = isset($filename)?$filename:'';
            }

            $OrderPartner->partner_address = $request->partner_address;

            if($request->city_id != 0){
                $OrderPartner->city_id = $request->city_id;
                $country_id = TblDefiCity::with('city_country')->where('city_id',$request->city_id)->where('city_entry_status',1)->where(Utilities::currentBC())->first();
                $OrderPartner->country_id = $country_id->city_country['country_id'];
            }else{
                $OrderPartner->city_id = '';
                $OrderPartner->country_id = '';
            }
            
            $OrderPartner->partner_zip_code = $request->partner_zip_code;
            $OrderPartner->partner_contact_person = $request->partner_contact_person_name;
            $OrderPartner->partner_contact_person_mobile = $request->partner_contact_person_mobile_no;
            $OrderPartner->partner_po_box = $request->partner_po_box;
            $OrderPartner->partner_phone_1 = $request->partner_phone_1;
            $OrderPartner->partner_mobile_no = $request->partner_mobile_no;
            $OrderPartner->partner_fax = $request->partner_fax;
            $OrderPartner->partner_whatapp_no = $request->partner_whatapp_no;
            $OrderPartner->partner_email = $request->partner_email;
            $OrderPartner->partner_website = $request->partner_website;
            $OrderPartner->member_status = isset($request->member_status)?1:0;
            $OrderPartner->business_id = auth()->user()->business_id;
            $OrderPartner->company_id = auth()->user()->company_id;
            $OrderPartner->branch_id = auth()->user()->branch_id;
            // $OrderPartner->partner_user_id = auth()->user()->id;
            $OrderPartner->save();

        } catch (QueryException $e) {
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
            // If the Request is Comming From Adminalnawras Modal Form
            if(isset($request->is_modal_entry)){
                $data['OrderPartner'] = $OrderPartner;
                $data['city'] = TblDefiCity::where('city_entry_status' , 1)->get();
                $data['area'] = TblDefiArea::where('area_entry_status' , 1)->get();
                return $this->jsonSuccessResponse($data, trans('message.create'), 200);
            }

            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            $data = array_merge($data, Utilities::returnJsonNewForm());
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param int $phone
     * @return void
     */
    public function getByPhone(Request $request){
        $data = [];

        if(!isset($request->mobile)){
            return $this->jsonErrorResponse($data, 'Please Enter Customer Mobile No.', 422);
        }

        $customer = TblSaleCustomer::where('customer_phone_1' , $request->mobile);
        if($customer->exists()){
            $customer = $customer->first();

            $data['customer_code']      = $customer->customer_code;
            $data['customer_name']      = $customer->customer_name;
            $data['customer_id']        = $customer->customer_id;
            $data['customer_phone_1']   = $customer->customer_phone_1;
            $data['city_id']            = $customer->city_id;
            $data['region_id']          = $customer->region_id;
            $data['found']              = true;
            return $this->jsonSuccessResponse($data, 'Customer Data Is Loaded', 200);
        }else{
            $data['found'] = false;
            return $this->jsonErrorResponse($data, 'No Customer Exist With This Phone/Mobile No.', 422);
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
    // public function destroy($id)
    // {
    //     $data = [];
    //     DB::beginTransaction();
    //     try{
    //         // Don't Delete Any Customer
    //         return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);

    //         $sales = TblSaleSales::where('customer_id',$id)->where(Utilities::currentBC())->first();
    //         $sales_order = TblSaleSalesOrder::where('customer_id',$id)->where(Utilities::currentBC())->first();
    //         if($sales == null && $sales_order == null )
    //         {
    //             $customer = TblSaleCustomer::where('customer_id',$id)->where(Utilities::currentBC())->first();

    //             $business_id = auth()->user()->business_id;
    //             $company_id = auth()->user()->company_id;
    //             $branch_id = auth()->user()->branch_id;
    //             $acc_id = $customer->customer_account_id;
    //             $this->proPurcChartDelete($business_id,$company_id,$branch_id,$acc_id);

    //             $customer->sub_customer()->delete();
    //             $customer->customer_branches()->delete();
    //             $customer->delete();
    //         }else{
    //             return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);
    //         }
    //     }catch (QueryException $e) {
    //         DB::rollback();
    //         return $this->jsonErrorResponse($data, $e->getMessage(), 200);
    //     } catch (ModelNotFoundException $e) {
    //         DB::rollback();
    //         return $this->jsonErrorResponse($data, $e->getMessage(), 200);
    //     } catch (ValidationException $e) {
    //         DB::rollback();
    //         return $this->jsonErrorResponse($data, $e->getMessage(), 200);
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return $this->jsonErrorResponse($data, $e->getMessage(), 200);
    //     }
    //     DB::commit();
    //     return $this->jsonSuccessResponse($data, trans('message.delete'), 200);
    // }

    // public function fetchCustomerInfo(Request $request){

    //     $custCode = $request->query('cust_code');
    //     if (!$custCode) {
    //         return response()->json(['error' => 'Customer code is required'], 400);
    //     }
    //     $customerPhone = TblSaleCustomer::where('CUSTOMER_ID', $custCode)->first()->customer_phone_1;
    //     if (!$customerPhone) {
    //         return response()->json(['error' => 'Customer not found'], 404);
    //     }

    //     return response()->json([
    //         'phone' => $customerPhone
    //     ]);

    // }

    // public function sendWhatsappMsg(Request $request) {

    //     $to = $request->to;
    //     $message = $request->message;
    //     $filePath = $request->filePath;
    //     $invoiceNumber = $request->invoiceNumber;
    //     $title = $request->title;

    //     $curl = curl_init();

    //     if($filePath == '' || $filePath == null) {

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'http://whatsintelligent.com/api/create-message',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => array(
    //         'appkey' => '2fa4c714-9a38-4f81-851b-3470c758c18b',
    //         'authkey' => 'yy3fbHr1GdTaP5D8Tte9w4BlvAmOk0yddf7s8tz0F8L4cZc1iA',
    //         'to' => $to,
    //         'message' => $message,
    //         'sandbox' => 'false'
    //         ),
    //         ));

    //     } else {

    //     curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'http://whatsintelligent.com/api/create-message',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'POST',
    //     CURLOPT_POSTFIELDS => array(
    //     'appkey' => '2fa4c714-9a38-4f81-851b-3470c758c18b',
    //     'authkey' => 'yy3fbHr1GdTaP5D8Tte9w4BlvAmOk0yddf7s8tz0F8L4cZc1iA',
    //     'to' => $to,
    //     'message' => $message,
    //     'sandbox' => 'false',
    //     'file' => $filePath
    //         ),
    //     ));

    //     }

    //     $response = curl_exec($curl);
    //     curl_close($curl);

    //     $responseData = @json_decode($response, true);

    //     if ($responseData) {
    //     if (isset($responseData['message_status']) && $responseData['message_status'] == 'Success') {
    //             echo json_encode(['success' => 'Message sent successfully!']);

    //             WhatsappLog::create([
    //                 'user_id' => session('user_id'),
    //                 'form_name' => $title,
    //                 'entry_code' => $invoiceNumber,
    //                 'created_at' => now()->format('Y-m-d H:i:s'),
    //             ]);

    //     } else {
    //         echo json_encode(['error' => 'Message sending failed. API returned: ' . $responseData['message_status']]);
    //     }
    //     } else {
    //     echo json_encode(['error' => 'Invalid JSON response or empty response.', 'raw_response' => $response]);
    //     }

    // }


}
