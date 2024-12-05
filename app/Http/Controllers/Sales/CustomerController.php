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

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static $page_title = 'Customer';
    public static $redirect_url = 'customer';
    public static $menu_dtl_id = '41';

    public function __construct()
    {
        $getStaticPrefix = Utilities::getStaticPrefix(self::$redirect_url);
        $this->current_path = $getStaticPrefix['path'];
        $this->page_form = '/'.self::$redirect_url.'/form';
        $this->page_view = '/'.self::$redirect_url.'/view';
    }

    /*public function index(Request $request)
    {
        dd('hhh');
        $data = [];
        $data['title'] = self::$page_title;
        $data['case'] = self::$redirect_url;
        $data['path-form'] = self::$redirect_url.$this->prefixCreatePage;
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['table_columns'] = [
            "customer_name" => "Customer Name",
            "customer_entry_status" => "Active / De-Active",
            "customer_code" => "Code",
            "customer_address" => "Address",
            "card_number" => "Card Nooo",
            "issue_date" => "Issue Date",
            "expiry_date " => "Expiry Date"
        ];
        $data['table_columns_date'] = [
            "issue_date",
            "expiry_date"
        ];

        if($request->ajax()){
                $tbl_1 = " tbl_1";
                $table = " vw_sale_customer $tbl_1 ";
                $columns = " $tbl_1.customer_name, $tbl_1.customer_entry_status, $tbl_1.customer_code, $tbl_1.customer_address, $tbl_1.card_number, $tbl_1.issue_date, $tbl_1.expiry_date, $tbl_1.entry_type";

                $where = " where $tbl_1.business_id = ".auth()->user()->business_id;
                $where .= " and $tbl_1.branch_id = ".auth()->user()->branch_id;
                $where .= " and (LOWER($tbl_1.entry_type) = 'cus')";

                $today = date('d/m/Y');
                $time_from = '12:00:00 am';
                $time_to = '11:59:59 pm';
                $global_filter_bollean = false;

                if (isset($request['query']['globalFilters']))
                {
                    $globalFilters = $request['query']['globalFilters'];
                    $global_search = false;

                    if(isset($globalFilters['global_search']) && !empty($globalFilters['global_search']))
                    {
                        $generalSearch = str_replace(" " , "%" , $globalFilters['global_search']);
                        $where .= " and ( ";
                        $where .= " $tbl_1.customer_code like '%$generalSearch%' OR ";
                        $where .= " $tbl_1.customer_name like '%$generalSearch%' OR ";
                        $where .= " $tbl_1.customer_address like '%$generalSearch%' OR ";
                        $where .= " $tbl_1.card_number like '%$generalSearch%' OR ";
                        $where .= " $tbl_1.issue_date like '%$generalSearch%' OR ";
                        $where .= " $tbl_1.expiry_date like '%$generalSearch%' ";
                        $where .= " ) ";

                        $global_search = true;
                        $global_filter_bollean = true;
                    }

                    if(isset($globalFilters['date']) && $global_search == false){
                        $date = $globalFilters['date'];
                        if(!empty($date)){
                            if(isset($globalFilters['time_from'])){
                                $time_from = date('h:i:s a',strtotime($globalFilters['time_from']));
                            }
                            if(isset($globalFilters['time_to'])){
                                $time_to = date('h:i:s a',strtotime($globalFilters['time_to']));
                            }
                            if($date == 'today'){
                                $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                            $yesterday = date('d/m/Y',strtotime(date('d-m-Y').' -1 day'));
                            if($date == 'yesterday'){
                                $from = "TO_DATE('".$yesterday." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                            $l7days = date('d/m/Y',strtotime(date('d-m-Y').' -7 day'));
                            if($date == 'last_7_days'){
                                $from = "TO_DATE('".$l7days." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                            $l30days = date('d/m/Y',strtotime(date('d-m-Y').' -30 day'));
                            if($date == 'last_30_days'){
                                $from = "TO_DATE('".$l30days." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                            if($date == 'yesterday'){
                                $to = "TO_DATE('".$yesterday." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }else{
                                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                            if($date == 'custom_date'){
                                if(isset($globalFilters['from']) && isset($globalFilters['to'])){
                                    $from = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['from']))." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                    $to = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['to']))." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                                }else{
                                    $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                    $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                                }
                            }
                            if($date == 'all'){
                                $from = "TO_DATE('01/01/2010 ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            }
                            $where .=  ' AND ('.$tbl_1.'.issue_date between '. $from .' AND '. $to.') ';
                        }
                        $global_filter_bollean = true;
                    }


                    if(isset($globalFilters['inline']))
                    {
                        $inline_filter = $globalFilters['inline'];
                        $inline_where = "";
                        if(!empty($inline_filter))
                        {
                            if(isset($inline_filter['customer_code']) && !empty($inline_filter['customer_code'])){
                                $inline_where .= " and lower($tbl_1.customer_code) like '%".strtolower($inline_filter['customer_code'])."%'";
                            }
                            if(isset($inline_filter['customer_name']) && !empty($inline_filter['customer_name'])){
                                $inline_where .= " and lower($tbl_1.customer_name) like '%".strtolower($inline_filter['customer_name'])."%'";
                            }
                            if(isset($inline_filter['customer_address']) && !empty($inline_filter['customer_address'])){
                                $inline_where .= " and lower($tbl_1.customer_address) like '%".strtolower($inline_filter['customer_address'])."%'";
                            }
                            if(isset($inline_filter['card_number']) && !empty($inline_filter['card_number'])){
                                $inline_where .= " and lower($tbl_1.card_number) like '%".strtolower($inline_filter['card_number'])."%'";
                            }
                            if(isset($inline_filter['issue_date']) && !empty($inline_filter['issue_date'])){
                                $issue_date = date('d/m/Y',strtotime($inline_filter['issue_date']));
                                $d_from = "TO_DATE('".$issue_date." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $d_to = "TO_DATE('".$issue_date." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $inline_to_date = "$d_from and $d_to";
                                $inline_where .= " and ( $tbl_1.issue_date between ".$inline_to_date.") ";
                            }
                            if(isset($inline_filter['expiry_date']) && !empty($inline_filter['expiry_date'])){
                                $expiry_date = date('d/m/Y',strtotime($inline_filter['expiry_date']));
                                $d_from = "TO_DATE('".$expiry_date." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $d_to = "TO_DATE('".$expiry_date." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $inline_to_date = "$d_from and $d_to";
                                $inline_where .= " and ( $tbl_1.expiry_date between ".$inline_to_date.") ";
                            }
                        }
                        $where .= $inline_where;
                    }
                }
                if(!$global_filter_bollean){
                    $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $where .=  ' AND ('.$tbl_1.'.issue_date between '. $from .' AND '. $to.') ';
                }

                $sortDirection  = ($request->has('sort.sort') && $request->filled('sort.sort'))? $request->input('sort.sort') : 'asc';
                $sortField  = ($request->has('sort.field') && $request->filled('sort.field'))? $request->input('sort.field') : 'customer_code';
                $meta    = [];
                $page  = ($request->has('pagination.page') && $request->filled('pagination.page'))? $request->input('pagination.page') : 1;
                $perpage  = ($request->has('pagination.perpage') && $request->filled('pagination.perpage'))? $request->input('pagination.perpage') : -1;

                $total  = DB::selectOne("select count(*) count from $table $where");
                $total  = isset($total->count)?$total->count:0;

                // $perpage 0; get all data
                if($perpage > 0)
                {
                    $pages  = ceil($total / $perpage); // calculate total pages
                    $page   = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                    $page   = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                    $offset = ($page - 1) * $perpage;
                    if ($offset < 0) {
                        $offset = 0;
                    }
                    //$data = array_slice($data, $offset, $perpage, true);
                }

                $orderby = " ORDER BY $tbl_1.$sortField $sortDirection ";
                $limit = "OFFSET $offset ROWS FETCH NEXT $perpage ROWS ONLY";
                $qry = "select $columns from $table $where $orderby $limit";
                dump($qry);
                $entries = DB::select($qry);

                $meta = [
                    'page'    => $page,
                    'pages'   => $pages,
                    'perpage' => $perpage,
                    'total'   => $total
                ];

                $result = [
                    'meta' => $meta + [
                        'sort'  => $sortDirection,
                        'field' => $sortField,
                    ],
                    'data' => $entries,
                ];
            return response()->json($result);
        }

        return view('sales.customer.list',compact('data'));
    }
    */

    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSaleCustomer::where('customer_id','LIKE',$id)->where(Utilities::currentBC())->exists()){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] =  TblSaleCustomer::with("sub_customer","contact_person","customer_branches")->where(Utilities::currentBC())->where('customer_id',$id)->first();

                if(isset($data['current']->city_id)){
                    $data['areas'] = TblDefiArea::where('city_id' , $data['current']->city_id)->where('area_entry_status' , 1)->get();
                }else{
                    $data['areas'] = [];
                }
                $data['customer_code'] = $data['current']->customer_code;
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

                $data['customer_code'] = Utilities::customCustomerCode($doc_data);
            }else{
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblSaleCustomer',
                    'code_field'        => 'customer_code',
                    'code_prefix'       => strtoupper('cu')
                ];

                $data['customer_code'] = Utilities::documentCode($doc_data);
            }
        }

        $data['city'] = TblDefiCountry::with('country_cities')->where('country_entry_status',1)->where(Utilities::currentBC())->get();

        $data['type'] = TblSaleCustomerType::where('customer_type_entry_status',1)->where(Utilities::currentBC())->get();

        $data['refrence'] = [];//TblSaleCustomer::where('customer_entry_status',1)->where(Utilities::currentBC())->get();

        $data['branch'] = TblSoftBranch::where('branch_active_status',1)->where(Utilities::currentBC())->get();

        $data['area'] = TblDefiArea::where('area_entry_status',1)->get();
        $data['membership'] = TblDefiMembershipType::where('membership_type_entry_status',1)->get();

        $arr = [
            'biz_type' => 'branch',
            'code' => $data['customer_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_sale_customer',
            'col_id' => 'customer_id',
            'col_code' => 'customer_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);

        return view('sales.customer.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        // dd($request->toArray());
        $data = [];
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:100',
            'customer_type' => 'required|not_in:0',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }

        if(!isset($id) && isset($request->customer_phone_1)){
            if(TblSaleCustomer::where('customer_phone_1','LIKE',$request->customer_phone_1)->where(Utilities::currentBC())->exists()){
                return $this->jsonErrorResponse($data, 'User Already Exist With This Phone/Mobile No.', 422);
            }
        }

        if(!isset($id) && isset($request->card_number)){
            if(TblSaleCustomer::where('card_number','LIKE',$request->card_number)->where(Utilities::currentBC())->exists()){
                return $this->jsonErrorResponse($data, 'This Card Number Already Exist..', 422);
            }
        }

        DB::beginTransaction();
        try{
            $cust_type = TblSaleCustomerType::where('customer_type_id',$request->customer_type)->where(Utilities::currentBC())->first();
            $acc_code = TblAccCoa::where('chart_account_id',$cust_type->customer_type_account_id)->where(Utilities::currentBC())->first();
            $level_no = 4;
            $parent_account_code = $acc_code->chart_code;
            $business_id = auth()->user()->business_id;
            $company_id = auth()->user()->company_id;
            $branch_id = auth()->user()->branch_id;
            $user_id = auth()->user()->id;
            $chart_name = $request->customer_name;
            if(isset($id)){
                $customer =TblSaleCustomer::where('customer_id',$id)->where(Utilities::currentBC())->first();
                /*if(isset($request->member_status)){

                }*/
                $acc_id = $customer->customer_account_id;
                if(empty($acc_id)){
                    $customer_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                    $customer->customer_account_id = $customer_account_id;
                }else{
                    $this->proPurcChartUpdate($business_id,$company_id,$branch_id,$chart_name,$acc_id);
                }

                $customer->update_id = Utilities::uuid();
            }else{
                /*if(isset($request->member_status)){
                    $customer_account_id = $this->proPurcChartInsert($level_no,$parent_account_code,$business_id,$company_id,$branch_id,$user_id,$chart_name);
                }*/
                $customer = new TblSaleCustomer();
                $customer->customer_id = Utilities::uuid();

                // Check SubDomain Of the Project
                if(TblDefiConstants::where('constants_key','subdomain')->where('constants_status',1)->exists()){
                    $subdomain = TblDefiConstants::where('constants_key','subdomain')->first()->constants_value;
                }

                if(isset($subdomain) && $subdomain == 'adminalnawras'){
                    $doc_data = [
                        'biz_type'          => 'branch',
                        'model'             => 'TblSaleCustomer',
                        'code_field'        => 'customer_code',
                        'code_prefix'       => strtoupper('a')
                    ];

                    $customer->customer_code = Utilities::customCustomerCode($doc_data);
                }else{
                    $doc_data = [
                        'biz_type'          => 'branch',
                        'model'             => 'TblSaleCustomer',
                        'code_field'        => 'customer_code',
                        'code_prefix'       => strtoupper('cu')
                    ];

                    $customer->customer_code = Utilities::documentCode($doc_data);
                }

                /*if(isset($request->member_status)) {
                    $customer->customer_account_id = $customer_account_id;
                }*/
                $customer->created_at = Carbon::now();
                $customer->updated_at = Carbon::now();
            }
            $form_id = $customer->customer_id;
            $customer->customer_name = $request->customer_name;
            $customer->customer_local_name = $request->customer_local_name;
            $customer->customer_type = $request->customer_type;
            $customer->customer_entry_status = isset($request->customer_entry_status)?"1":"0";
            $customer->customer_default_customer = isset($request->customer_default_customer)?"1":"0";
            if($request->hasFile('customer_image'))
            {
                $image = $request->file('customer_image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path('/images/' . $filename);
                Image::make($image->getRealPath())->resize(200, 200)->save($path);
                $customer->customer_image = isset($filename)?$filename:'';
            }
            $customer->customer_address = $request->customer_address;
            $customer->customer_delivery_address = $request->customer_delivery_address;
            $customer->customer_billing_address = $request->customer_billing_address;
            if($request->city_id != 0){
                $customer->city_id = $request->city_id;
                $country_id = TblDefiCity::with('city_country')->where('city_id',$request->city_id)->where('city_entry_status',1)->where(Utilities::currentBC())->first();
                $customer->country_id = $country_id->city_country['country_id'];
            }else{
                $customer->city_id = '';
                $customer->country_id = '';
            }
            $customer->region_id = $request->customer_area_id ?? 0;
            $customer->customer_zip_code = $request->customer_zip_code;
            $customer->customer_contact_person = $request->customer_contact_person_name;
            $customer->customer_contact_person_mobile = $request->customer_contact_person_mobile_no;
            //$customer->customer_branch_id = isset($request->customer_branch_id)?implode(', ', $request->customer_branch_id):'';
            $customer->customer_po_box = $request->customer_po_box;
            $customer->customer_phone_1 = $request->customer_phone_1;
            $customer->loyalty_opnening = $request->loyalty_opnening;
            $customer->customer_mobile_no = $request->customer_mobile_no;
            $customer->customer_fax = $request->customer_fax;
            $customer->customer_whatapp_no = $request->customer_whatapp_no;
            $customer->customer_email = $request->customer_email;
            $customer->customer_website = $request->customer_website;
            $customer->customer_reference_code = $request->customer_reference_code;
            $customer->customer_no_of_days = $request->customer_no_of_days;
            $customer->customer_credit_period = $request->customer_credit_period;
            $customer->customer_tax_no = $request->customer_tax_no;
            $customer->customer_credit_limit = $request->customer_credit_limit;
            $customer->customer_debit_limit = $request->customer_debit_limit;
            $customer->customer_tax_rate = $request->customer_tax_rate;
            $customer->customer_tax_status = $request->customer_tax_status;
            $customer->customer_cheque_beneficry_name = $request->customer_cheque_beneficry_name;
            $customer->customer_mode_of_payment = $request->customer_mode_of_payment;
            $customer->customer_can_scale = isset($request->customer_can_scale)?"1":"0";
            $customer->customer_bank_name = $request->customer_bank_name;
            $customer->customer_bank_account_no = $request->customer_bank_account_no;
            $customer->customer_bank_account_title = $request->customer_bank_account_title;
            $customer->membership_type_id = $request->membership_type_id;
            $customer->card_number = $request->card_number;
            $customer->issue_date = date('Y-m-d', strtotime($request->issue_date));
            $customer->expiry_date = date('Y-m-d', strtotime($request->expiry_date));
            $customer->member_status = isset($request->member_status)?1:0;
            $customer->business_id = auth()->user()->business_id;
            $customer->company_id = auth()->user()->company_id;
            $customer->branch_id = auth()->user()->branch_id;
            $customer->customer_user_id = auth()->user()->id;
            $customer->save();

            /*TblSaleCustomerMember::creat([
                'customer_member_id' => Utilities::uuid(),
                'customer_member_card_no' => $request->card_number,
                'membership_type_id' => $request->card_number,
                'issue_date' => date('Y-m-d', strtotime($request->issue_date)),
                'expiry_date' => date('Y-m-d', strtotime($request->expiry_date)),
                'customer_member_status' => isset($request->member_status)?1:0,
                'business_id' => auth()->user()->business_id,
                'company_id' => auth()->user()->company_id,
                'branch_id' => auth()->user()->branch_id,
                'customer_member_user_id' => auth()->user()->id,
            ]);
            */

             // customer branch
             if(isset($id)){
                $del_brchs = TblSaleCustomerBranch::where('customer_id',$id)->get();
                foreach ($del_brchs as $del_brch){
                    TblSaleCustomerBranch::where('customer_id',$del_brch->customer_id)->delete();
                }
            }
            if(isset($request->customer_branch_id)){
                foreach($request->customer_branch_id as $branch){
                    $customer_branch = new TblSaleCustomerBranch();
                    $customer_branch->customer_branch_id = Utilities::uuid();
                    $customer_branch->customer_id = $customer->customer_id;
                    $customer_branch->branch_id = $branch;
                    $customer_branch->customer_branch_entry_status = 1;
                    $customer_branch->save();
                }
            }

            if(isset($id)){
                $del_Dtls = TblSaleCustomerDtl::where('customer_id',$id)->where(Utilities::currentBC())->get();
                foreach ($del_Dtls as $del_Dtl){
                    TblSaleCustomerDtl::where('customer_dtl_id',$del_Dtl->customer_dtl_id)->where(Utilities::currentBC())->delete();
                }
            }
            if(isset($request->pd)){
                foreach ($request->pd as $subCustomer){
                    $sub_customer = new TblSaleCustomerDtl();
                    if(isset($id) && isset($subCustomer['contactp_dtl_id'])){
                        $sub_customer->customer_dtl_id = $subCustomer['contactp_dtl_id'];
                        $sub_customer->customer_id = $id;
                    }else{
                        $sub_customer->customer_dtl_id = Utilities::uuid();
                        $sub_customer->customer_id = $customer->customer_id;
                    }
                    $sub_customer->customer_dtl_name = $subCustomer['contactp_dtl_name'];
                    $sub_customer->customer_dtl_cont_no = $subCustomer['contactp_dtl_cont_no'];
                    $sub_customer->customer_dtl_address = $subCustomer['contactp_dtl_address'];
                    $sub_customer->customer_dtl_entry_status = 1;
                    $sub_customer->business_id = auth()->user()->business_id;
                    $sub_customer->company_id = auth()->user()->company_id;
                    $sub_customer->branch_id = auth()->user()->branch_id;
                    $sub_customer->customer_dtl_user_id = auth()->user()->id;
                    $sub_customer->save();
                }
            }

            // Store Sub Customer(s) Data
            if(isset($id)){
                $del_SubCustomer = TblSaleSubCustomer::where('customer_id',$id)->get();
                foreach ($del_SubCustomer as $del_Dtl){
                    TblSaleSubCustomer::where('customer_id',$id)->delete();
                }
            }
            if(isset($request->subcustomer)){
                foreach ($request->subcustomer as $subCustomer){
                    $sub_customer = new TblSaleSubCustomer();
                    $sub_customer->sub_customer_id = Utilities::uuid();
                    $sub_customer->parent_customer_id = $customer->customer_id;
                    $sub_customer->customer_id = $subCustomer['customer_id'];
                    $sub_customer->type = 'customer';
                    $sub_customer->save();
                }
            }

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
                $data['customer'] = $customer;
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
    public function destroy($id)
    {
        $data = [];
        DB::beginTransaction();
        try{
            // Don't Delete Any Customer
            return $this->jsonErrorResponse($data, trans('message.not_delete'), 200);

            $sales = TblSaleSales::where('customer_id',$id)->where(Utilities::currentBC())->first();
            $sales_order = TblSaleSalesOrder::where('customer_id',$id)->where(Utilities::currentBC())->first();
            if($sales == null && $sales_order == null )
            {
                $customer = TblSaleCustomer::where('customer_id',$id)->where(Utilities::currentBC())->first();

                $business_id = auth()->user()->business_id;
                $company_id = auth()->user()->company_id;
                $branch_id = auth()->user()->branch_id;
                $acc_id = $customer->customer_account_id;
                $this->proPurcChartDelete($business_id,$company_id,$branch_id,$acc_id);

                $customer->sub_customer()->delete();
                $customer->customer_branches()->delete();
                $customer->delete();
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

    public function fetchCustomerInfo(Request $request){

        $custCode = $request->query('cust_code');
        if (!$custCode) {
            return response()->json(['error' => 'Customer code is required'], 400);
        }
        $customerPhone = TblSaleCustomer::where('CUSTOMER_ID', $custCode)->first()->customer_phone_1;
        if (!$customerPhone) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json([
            'phone' => $customerPhone
        ]);

    }

    public function sendWhatsappMsg(Request $request) {

        $to = $request->to;
        $message = $request->message;
        $filePath = $request->filePath;
        $invoiceNumber = $request->invoiceNumber;
        $title = $request->title;

        $curl = curl_init();

        if($filePath == '' || $filePath == null) {

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://whatsintelligent.com/api/create-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
            'appkey' => '2fa4c714-9a38-4f81-851b-3470c758c18b',
            'authkey' => 'yy3fbHr1GdTaP5D8Tte9w4BlvAmOk0yddf7s8tz0F8L4cZc1iA',
            'to' => $to,
            'message' => $message,
            'sandbox' => 'false'
            ),
            ));

        } else {

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://whatsintelligent.com/api/create-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
        'appkey' => '2fa4c714-9a38-4f81-851b-3470c758c18b',
        'authkey' => 'yy3fbHr1GdTaP5D8Tte9w4BlvAmOk0yddf7s8tz0F8L4cZc1iA',
        'to' => $to,
        'message' => $message,
        'sandbox' => 'false',
        'file' => $filePath
            ),
        ));

        }

        $response = curl_exec($curl);
        curl_close($curl);

        $responseData = @json_decode($response, true);

        if ($responseData) {
        if (isset($responseData['message_status']) && $responseData['message_status'] == 'Success') {
                echo json_encode(['success' => 'Message sent successfully!']);

                WhatsappLog::create([
                    'user_id' => session('user_id'),
                    'form_name' => $title,
                    'entry_code' => $invoiceNumber,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                ]);

        } else {
            echo json_encode(['error' => 'Message sending failed. API returned: ' . $responseData['message_status']]);
        }
        } else {
        echo json_encode(['error' => 'Invalid JSON response or empty response.', 'raw_response' => $response]);
        }

    }


}
