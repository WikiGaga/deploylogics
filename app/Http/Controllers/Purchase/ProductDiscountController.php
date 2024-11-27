<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use App\Models\Sale\TblSaleDiscount;
use App\Models\Sale\TblSaleDiscountSetup;
use App\Models\Sale\TblSaleDiscountSetupMembership;
use App\Models\TblDefiMembershipType;
use App\Models\TblPurcGroupItem;
use App\Models\TblSchemeBranches;
use App\Models\TblSoftBranch;
use App\Models\ViewPurcGroupItem;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductDiscountController extends Controller
{
    public static $page_title = 'Discount Setup';
    public static $redirect_url = 'product-discount-setup';
    public static $menu_dtl_id = '282';

    public function __construct()
    {
        $getStaticPrefix = Utilities::getStaticPrefix(self::$redirect_url);
        $this->current_path = $getStaticPrefix['path'];
        $this->page_form = '/'.self::$redirect_url.'/form';
        $this->page_view = '/'.self::$redirect_url.'/view';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['title'] = self::$page_title;
        $data['case'] = 'product-discount-setup';
        $data['create-form'] = '/smart-product/product-discount-setup/form';
        $data['form-action'] = '/smart-product/product-discount-setup';
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['table_id'] = 'discount_setup_id';
        $data['data_url'] = action('Purchase\ProductDiscountController@index');
        $data['table_columns'] = [
            "discount_code" => [
                'title' => 'Code',
                'type' => 'string',
            ],
            "discount_title" => [
                'title' => 'Title',
                'type' => 'string',
            ],
            "discount_status" => [
                'title' => 'Status',
                'type' => 'string',
            ],
            "start_date" => [
                'title' => "Start Date",
                'type' => 'datetime',
            ],
            "end_date" => [
                'title' => "End Date",
                'type' => 'datetime',
            ],
            "branch_name" => [
                'title' => 'Branch',
                'type' => 'string',
            ],
            "user_name" => [
                'title' => 'Entry User',
                'type' => 'string',
            ],
            "created_at" => [
                'title' => "Entry Date",
                'type' => 'datetime',
            ],
        ];

        if($request->ajax()){
            $tbl_1 = " tbl_1";
            $table = " vw_sale_discount_setup $tbl_1 ";
            $columns = "$tbl_1.discount_setup_id, $tbl_1.discount_code,$tbl_1.discount_title,$tbl_1.is_active_status || '-' || $tbl_1.expire_status     discount_status , $tbl_1.start_date, $tbl_1.end_date, $tbl_1.branch_name ,  $tbl_1.user_name ,  $tbl_1.created_at ";

            $where = " where $tbl_1.business_id = ".auth()->user()->business_id;
            $where .= " and $tbl_1.branch_id = ".auth()->user()->branch_id;

            $today = date('d/m/Y');
            $time_from = '12:00:00 am';
            $time_to = '11:59:59 pm';
            $global_filter_bollean = false;
            if (isset($request['query']['globalFilters'])) {
                $globalFilters = $request['query']['globalFilters'];
                $global_search = false;
                if(isset($globalFilters['global_search']) && !empty($globalFilters['global_search'])){
                    $generalSearch = str_replace(" " , "%" , $globalFilters['global_search']);
                    $generalSearch = strtolower($generalSearch);
                    $textSearch = "";
                    foreach ($data['table_columns'] as $tkey=>$table_columns){
                        if($table_columns['type'] == 'string'){
                            if($tkey == 'discount_status'){
                                $textSearch .= " lower($tbl_1.is_active_status) like '%$generalSearch%' OR ";
                                $textSearch .= " lower($tbl_1.expire_status) like '%$generalSearch%' OR ";
                            }else{
                                $textSearch .= " lower($tbl_1.".$tkey.") like '%$generalSearch%' OR ";
                            }
                        }
                    }
                    if(!empty($textSearch)){
                        $textSearch = rtrim($textSearch,' OR');
                        $where .= "and ( $textSearch ) ";
                    }

                    $from = "TO_DATE('01/01/2010 ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                    $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';

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
                        $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';
                    }
                    $global_filter_bollean = true;
                }

                if(isset($globalFilters['inline'])){
                    $inline_filter = $globalFilters['inline'];
                    $inline_where = "";
                    if(!empty($inline_filter)){
                        foreach ($data['table_columns'] as $tkey=>$table_columns){
                            if(isset($inline_filter[$tkey]) && !empty($inline_filter[$tkey])){
                                if($table_columns['type'] == 'string'){
                                    if($tkey == 'discount_status'){
                                        $inline_where .= " and (lower($tbl_1.is_active_status) like '%".strtolower($inline_filter[$tkey])."%' OR ";
                                        $inline_where .= " lower($tbl_1.expire_status) like '%".strtolower($inline_filter[$tkey])."%' ) ";
                                    }else{
                                        $inline_where .= " and lower($tbl_1.$tkey) like '%".strtolower($inline_filter[$tkey])."%'";
                                    }
                                }
                                if(in_array($table_columns['type'],['date','datetime'])){
                                    $created_at = date('d/m/Y',strtotime($inline_filter[$tkey]));
                                    $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                    $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                                    $inline_to_date = "$d_from and $d_to";
                                    $inline_where .= " and ( $tbl_1.$tkey between ".$inline_to_date.") ";
                                }
                            }
                        }
                    }
                    $where .= $inline_where;
                }

                if(isset($globalFilters['pds_status'])){
                    if($globalFilters['pds_status'] == 'in_active_expire'){
                        $where .= " and (is_active = 0 and lower(expire_status) =  'expired')";
                    }
                    if($globalFilters['pds_status'] == 'in_active_valid'){
                        $where .= " and (is_active = 0 and lower(expire_status) =  'valid')";
                    }
                    if($globalFilters['pds_status'] == 'active_expire'){
                        $where .= " and (is_active = 1 and lower(expire_status) =  'expired')";
                    }
                    if($globalFilters['pds_status'] == 'active_valid'){
                        $where .= " and (is_active = 1 and lower(expire_status) =  'valid')";
                    }
                }
            }
            if(!$global_filter_bollean){
                $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';
            }

            $sortDirection  = ($request->has('sort.sort') && $request->filled('sort.sort'))? $request->input('sort.sort') : 'desc';
            $sortField  = ($request->has('sort.field') && $request->filled('sort.field'))? $request->input('sort.field') : 'created_at';
            $meta    = [];
            $page  = ($request->has('pagination.page') && $request->filled('pagination.page'))? $request->input('pagination.page') : 1;
            $perpage  = ($request->has('pagination.perpage') && $request->filled('pagination.perpage'))? $request->input('pagination.perpage') : -1;

            $total  = DB::selectOne("select count(DISTINCT ".$data['table_id'].") count from $table $where");
            $total  = isset($total->count)?$total->count:0;
            // $perpage 0; get all data
            if ($perpage > 0) {
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
            $qry = "select DISTINCT $columns from $table $where $orderby $limit";
            //   dd($qry);
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

        return view('common.adv_list.list',compact('data'));
    }

    public function viewProductDiscountSetup($id=null){
        $data = [];
        $data['page_data']['title'] = 'Product Discount Setup';
        $data['page_data']['path_index'] = $this->prefixIndexPage.'smart-product/'.self::$redirect_url;
        $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
        $data['branch'] = TblSoftBranch::where(Utilities::currentBC())->get();
        $data['group_item'] = TblPurcGroupItem::with('last_level')->where('group_item_level',2)->where(Utilities::currentBC())->orderByRaw(DB::raw('lower(group_item_name)'))->get();
        //dd($data['group_item']->toArray());
        $constants = TblDefiConstants::whereIn('constants_type',['sale_type','discount_type','promotion_type'])->get();
        $data['sale_type'] = [];
        $data['discount_type'] = [];
        $data['promotion_type'] = [];
        foreach ($constants as $constant){
            if($constant->constants_type == 'sale_type'){
                $data['sale_type'][] = $constant;
            }
            if($constant->constants_type == 'discount_type'){
                $data['discount_type'][] = $constant;
            }
            if($constant->constants_type == 'promotion_type'){
                $data['promotion_type'][] = $constant;
            }
        }
        if(isset($id)){

            if(TblSaleDiscountSetup::where('discount_setup_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSaleDiscountSetup::with('discount_setup_membership','scheme_branches')->where('discount_setup_id',$id)->where(Utilities::currentBCB())->first();
                $data['dtl'] = TblSaleDiscountSetup::where('discount_setup_id',$id)->where(Utilities::currentBCB())->get();
                // dd($data['current']->toArray());
                if(empty($data['current'])){
                    abort('404');
                }
                $data['discount_setup_code'] = $data['current']->discount_setup_code;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'business',
                'model'             => 'Sale\TblSaleDiscountSetup',
                'code_field'        => 'discount_setup_code',
                'code_prefix'       => strtoupper('DISC')
            ];
            $data['discount_setup_code'] = Utilities::documentCode($doc_data);
        }
        $doc_data = [
            'biz_type'          => 'business',
            'model'             => 'Sale\TblSaleDiscountSetup',
            'code_field'        => 'discount_setup_code',
            'code_prefix'       => strtoupper('DISC')
        ];
        $data['document_code'] = Utilities::documentCode($doc_data);
        $data['membership_type'] = TblDefiMembershipType::where('membership_type_entry_status',1)->get();
        //dd($data['sale_type']);
        return view('purchase.product_smart.discount_setup.form', compact('data'));
    }

    public function storeProductDiscountSetup(Request $request, $id=null)
    {
       // dd($request->toArray());
        $data = [];
        $discount_setup_title_valid = "required|max:100|unique:tbl_sale_discount_setup";
        if(isset($id)){
            $discount_setup_title_valid = 'required|max:100';
        }
        $validator = Validator::make($request->all(), [
            'discount_setup_title' => $discount_setup_title_valid,
            'start_date' => 'required|date|date_format:d-m-Y H:i|before:end_date',
            'end_date' => 'required|date|date_format:d-m-Y H:i|after:start_date',
            'branch_id' => 'required|not_in:0',
            'sale_type' => 'required|not_in:0',
            'discount_type' => 'required|not_in:0',
            'promotion_type' => 'required|not_in:0',
        ],[
            'discount_setup_title.required' => 'Title is required',
            'discount_setup_title.max' => 'Title max length is 100',
            'kt_dtpicker_start_date.required' => 'Start Date is required',
            'kt_dtpicker_start_date.date' => 'Start Date must be date time format',
            'kt_dtpicker_start_date.date_format' => 'Start Date must be date time format',
            'kt_dtpicker_start_date.before' => 'Start Date must be less then End Date',

            'kt_dtpicker_end_date.required' => 'End Date is required',
            'kt_dtpicker_end_date.date' => 'End Date must be date time format',
            'kt_dtpicker_end_date.date_format' => 'End Date must be date time format',
            'kt_dtpicker_end_date.after' => 'End Date must be greater then Start Date',

            'branch_id.required' => 'Branch is required',
            'branch_id.not_in' => 'Branch is required',

            'sale_type.required' => 'Sale type is required',
            'sale_type.not_in' => 'Sale type is required',

            'discount_type.required' => 'Discount type is required',
            'discount_type.not_in' => 'Discount type is required',

            'promotion_type.required' => 'Promotion type is required',
            'promotion_type.not_in' => 'Promotion type is required',

        ]);

        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            $msg = "Some fields are required";
            foreach ($data['validator_errors']->messages() as $validator_errors){
                $msg = $validator_errors[0];
                return $this->jsonErrorResponse($data, $msg);
                break;
            }
        }
        if(isset($id)){
            if(TblSaleDiscountSetup::where('discount_setup_id','!=',$id)->where('discount_setup_title',trim($request->discount_setup_title))->where(Utilities::currentBCB())->exists()){
                return $this->jsonErrorResponse($data, "Title Already exists");
            }
        }
        $discount_qty = 0;
        $discount_perc = '';
        $flat_discount_qty = 0;
        $flat_discount_amount = 0;
        $slab_base = 0;
        if($request->promotion_type == 'cash_discount'){
            $discount_qty = $request->discount_qty;
            $discount_perc = $request->discount_perc;
            $flat_discount_qty = $request->flat_discount_qty;
            $flat_discount_amount = $request->flat_discount_amount;
            $slab_base = isset($request->slab_base)?1:0;
            if(empty($discount_qty)){
                return $this->jsonErrorResponse($data, 'Discount Qty is required');
            }
            if(empty($discount_perc) && empty($flat_discount_qty) && empty($flat_discount_amount)){
                return $this->jsonErrorResponse($data, 'Discount Perc is required');
            }
        }


        $amount_for_point = '';
        $point_quantity = '';
        if($request->promotion_type == 'reward_point'){

            $amount_for_point = $request->amount_for_point;
            $point_quantity = $request->point_quantity;

        }
        $is_all_member = isset($request->is_all_member)?1:0;
        $is_with_member = isset($request->is_with_member)?1:0;
        $is_without_member = isset($request->is_without_member)?1:0;
        if(empty($is_all_member) && empty($is_with_member) && empty($is_without_member)){
            return $this->jsonErrorResponse($data, 'Category Scheme Type must be checked');
        }
        if(!empty($is_with_member) &&
            (!isset($request->membership_type) || count($request->membership_type) == 0)){
            return $this->jsonErrorResponse($data, 'Any membership type is required');
        }
        if($is_all_member){
            $is_with_member = 1;
            $is_without_member = 1;
            $membership_types = TblDefiMembershipType::where('membership_type_entry_status',1)->pluck('membership_type_id')->toArray();
        }

        DB::beginTransaction();
        try{
            if(isset($id)){
                $disc = TblSaleDiscount::where('discount_setup_id',$id)->where(Utilities::currentBCB())->first();
                $created_at = $disc->created_at;
                $discount_setup_id = $id;
                $document_code = $disc->discount_code;

                TblSaleDiscount::where('discount_setup_id',$id)->where(Utilities::currentBCB())
                    ->update([
                    'discount_title' => trim($request->discount_setup_title),
                    'discount_type' => $request->discount_type,
                    'start_date' => date('Y-m-d H:i',strtotime($request->start_date)),
                    'end_date' => date('Y-m-d H:i',strtotime($request->end_date)),
                    'is_active' => isset($request->is_active)?1:0,
                    'remarks' => $request->discount_setup_remarks,
                    'update_id' => Utilities::uuid(),
                    'user_id' => auth()->user()->id,
                    'business_id' => auth()->user()->business_id,
                    'company_id' => auth()->user()->company_id,
                    'branch_id' => auth()->user()->branch_id

                ]);
            }else{
                $discount_setup_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'business',
                    'model'             => 'Sale\TblSaleDiscountSetup',
                    'code_field'        => 'discount_setup_code',
                    'code_prefix'       => strtoupper('DISC')
                ];
                $document_code = Utilities::documentCode($doc_data);

                TblSaleDiscount::create([
                    'discount_setup_id' => $discount_setup_id,
                    'discount_title' => trim($request->discount_setup_title),
                    'discount_code' => $document_code,
                    'discount_type' => $request->discount_type,
                    'start_date' => date('Y-m-d H:i',strtotime($request->start_date)),
                    'end_date' => date('Y-m-d H:i',strtotime($request->end_date)),
                    'is_active' => isset($request->is_active)?1:0,
                    'remarks' => $request->discount_setup_remarks,
                    'user_id' => auth()->user()->id,
                    'business_id' => auth()->user()->business_id,
                    'company_id' => auth()->user()->company_id,
                    'branch_id' => auth()->user()->branch_id,
                ]);
            }
            $all_branches = false;
            if(in_array('all',$request->branch_id)){
                $all_branches = true;
            }
            if($all_branches){
                $branch_ids = TblSoftBranch::pluck('branch_id')->toArray();
            }else{
                $branch_ids = $request->branch_id;
            }
            $discount_setup_entry_saved = false;
            if(!empty($branch_ids) && count($branch_ids) != 0) {
                $group_item = false;
                // Group Items
                if(isset($request->group_item_id) && !empty($request->group_item_id)){
                    $discount_setup_entry_saved = true;
                    $group_item = true;
                    if (isset($id)) {
                        TblSchemeBranches::where('discount_setup_id',$discount_setup_id)->delete();
                    }
                    foreach ($branch_ids as $branch_id){
                        $scheme_branch_uuid = Utilities::uuid();
                        $branches = new TblSchemeBranches();
                        $branches->scheme_id = $scheme_branch_uuid;
                        $branches->scheme_branch_uuid = $scheme_branch_uuid;
                        $branches->discount_setup_id = $discount_setup_id;
                        $branches->discount_setup_title = trim($request->discount_setup_title);
                        $branches->branch_id = $branch_id;
                        $branches->is_active = isset($request->is_active)?1:0;
                        $branches->start_date = date('Y-m-d H:i',strtotime($request->start_date));
                        $branches->end_date = date('Y-m-d H:i',strtotime($request->end_date));
                        $branches->min_sale_amount = $amount_for_point;
                        $branches->loyalty_rate = $point_quantity;
                        $branches->slab_base = $slab_base;
                        $branches->is_with_member = $is_with_member;
                        $branches->is_without_member = $is_without_member;
                        $branches->save();
                    }
                    if (isset($id)) {
                        TblSaleDiscountSetup::where('discount_setup_id',$id)->delete();
                    }
                    foreach ($request->group_item_id as $group_item_id){

                        $discount = new TblSaleDiscountSetup();
                        $discount->discount_setup_id = $discount_setup_id;
                        $discount->discount_setup_code = $document_code;
                        $discount->discount_setup_row_id = Utilities::uuid();

                        $discount->discount_setup_title = trim($request->discount_setup_title);
                        $discount->discount_setup_type = 'group_item';
                        $discount->start_date = date('Y-m-d H:i',strtotime($request->start_date));
                        $discount->end_date = date('Y-m-d H:i',strtotime($request->end_date));
                        $discount->sale_type = $request->sale_type;
                        $discount->discount_type = $request->discount_type;
                        $discount->promotion_type = $request->promotion_type;
                        $discount->discount_qty = $discount_qty;
                        $discount->discount_perc = $discount_perc;
                        $discount->flat_discount_qty = $flat_discount_qty;
                        $discount->flat_discount_amount = $flat_discount_amount;
                        $discount->sr_no = 1;
                        $discount->product_id = '';
                        $discount->product_barcode_id = '';
                        $discount->product_barcode_barcode = '';
                        $discount->uom_id = '';
                        $discount->packing = '';
                        $discount->group_item_id = $group_item_id;
                        $discount->cost_rate = '';
                        $discount->mrp = '';
                        $discount->sale_rate = '';
                        $discount->gp_amount = '';
                        $discount->gp_perc = '';
                        $discount->disc_amount = '';
                        $discount->disc_perc = '';
                        $discount->after_disc_gp_amount = '';
                        $discount->after_disc_gp_perc = '';
                        $discount->is_active = isset($request->is_active)?1:0;
                        $discount->remarks = $request->discount_setup_remarks;
                        $discount->user_id = auth()->user()->id;
                        $discount->business_id = auth()->user()->business_id;
                        $discount->company_id = auth()->user()->company_id;
                        $discount->branch_id = auth()->user()->branch_id;
                        $discount->amount_for_point = $amount_for_point;
                        $discount->point_quantity = $point_quantity;
                        $discount->slab_base = $slab_base;
                        $discount->is_with_member = $is_with_member;
                        $discount->is_without_member = $is_without_member;
                        $discount->save();
                    }


                    if (isset($id)) {
                        TblSaleDiscountSetupMembership::where('discount_setup_id',$id)->delete();
                    }
                    if(!empty($is_all_member)){
                        foreach ($membership_types as $membership_type) {
                            $membership = new TblSaleDiscountSetupMembership;
                            $membership->discount_setup_membership_id = Utilities::uuid();
                            $membership->discount_setup_id = $discount_setup_id;
                            $membership->membership_type_id = $membership_type;
                            $membership->save();
                        }
                    }else{
                        if (!empty($is_with_member) && isset($request->membership_type) && count($request->membership_type) != 0) {
                            foreach ($request->membership_type as $membership_type) {
                                $membership = new TblSaleDiscountSetupMembership;
                                $membership->discount_setup_membership_id = Utilities::uuid();
                                $membership->discount_setup_id = $discount_setup_id;
                                $membership->membership_type_id = $membership_type;
                                $membership->save();
                            }
                        }
                    }
                }
                // Products
                if (isset($request->pd) && count($request->pd) != 0 && !$group_item) {
                    $discount_setup_entry_saved = true;
                    if (isset($id)) {
                        TblSchemeBranches::where('discount_setup_id',$discount_setup_id)->delete();
                    }
                    foreach ($branch_ids as $branch_id){
                        $scheme_branch_uuid = Utilities::uuid();
                        $branches = new TblSchemeBranches();
                        $branches->scheme_id = $scheme_branch_uuid;
                        $branches->scheme_branch_uuid = $scheme_branch_uuid;
                        $branches->discount_setup_id = $discount_setup_id;
                        $branches->discount_setup_title = trim($request->discount_setup_title);
                        $branches->branch_id = $branch_id;
                        $branches->is_active = isset($request->is_active)?1:0;
                        $branches->start_date = date('Y-m-d H:i',strtotime($request->start_date));
                        $branches->end_date = date('Y-m-d H:i',strtotime($request->end_date));
                        $branches->min_sale_amount = $amount_for_point;
                        $branches->loyalty_rate = $point_quantity;
                        $branches->slab_base = $slab_base;
                        $branches->is_with_member = $is_with_member;
                        $branches->is_without_member = $is_without_member;
                        $branches->save();
                    }
                    $sr_no = 1;
                    if (isset($id)) {
                        TblSaleDiscountSetup::where('discount_setup_id',$id)->delete();
                    }

                    foreach ($request->pd as $pd) {
                        $discount = new TblSaleDiscountSetup();
                        $discount->discount_setup_id = $discount_setup_id;
                        $discount->discount_setup_code = $document_code;
                        $discount->discount_setup_row_id = Utilities::uuid();
                        $discount->discount_setup_title = trim($request->discount_setup_title);
                        $discount->discount_setup_type = 'product';
                        $discount->start_date = date('Y-m-d H:i', strtotime($request->start_date));
                        $discount->end_date = date('Y-m-d H:i', strtotime($request->end_date));
                        $discount->sale_type = $request->sale_type;
                        $discount->discount_type = $request->discount_type;
                        $discount->promotion_type = $request->promotion_type;

                        $discount->discount_qty = $discount_qty;
                        $discount->discount_perc = $discount_perc;
                        $discount->flat_discount_qty = $flat_discount_qty;
                        $discount->flat_discount_amount = $flat_discount_amount;
                        $discount->sr_no = $sr_no;
                        $discount->product_id = $pd['product_id'];
                        $discount->product_barcode_id = $pd['product_barcode_id'];
                        $discount->product_barcode_barcode = $pd['pd_barcode'];

                        $discount->uom_id = $pd['uom_id'];
                        $discount->packing = $pd['pd_packing'];
                        $discount->group_item_id = isset($pd['cate_last_level_id'])?$pd['cate_last_level_id']:"";
                        $discount->cost_rate = is_null($pd['current_tp'])?0:$pd['current_tp'];

                        $discount->mrp = is_null($pd['mrp'])?0:$pd['mrp'];
                        $discount->sale_rate = is_null($pd['sale_rate'])?0:$pd['sale_rate'];
                        $discount->gp_amount = is_null($pd['gp_rate'])?0:$pd['gp_rate'];
                        $discount->gp_perc = is_null($pd['gp_perc'])?0:$pd['gp_perc'];
                        $discount->disc_amount = is_null($pd['disc_amt'])?0:$pd['disc_amt'];
                        $discount->after_disc_gp_amount = is_null($pd['after_disc_gp_amt'])?0:$pd['after_disc_gp_amt'];
                        $discount->after_disc_gp_perc = is_null($pd['after_disc_gp_perc'])?0:$pd['after_disc_gp_perc'];
                        $discount->is_active = isset($request->is_active)?1:0;
                        $discount->remarks = $request->discount_setup_remarks;
                        $discount->user_id = auth()->user()->id;
                        $discount->business_id = auth()->user()->business_id;
                        $discount->company_id = auth()->user()->company_id;
                        $discount->branch_id = auth()->user()->branch_id;
                        $discount->amount_for_point = $amount_for_point;
                        $discount->point_quantity = $point_quantity;
                        $discount->slab_base = $slab_base;
                        $discount->is_with_member = $is_with_member;
                        $discount->is_without_member = $is_without_member;
                        if(isset($created_at)){
                            $discount->created_at = date('Y-m-d h:i:s A', strtotime($created_at));;
                        }
                        $sr_no = $sr_no + 1;
                        $discount->save();
                    }
                    if (isset($id)) {
                        TblSaleDiscountSetupMembership::where('discount_setup_id',$id)->delete();
                    }
                    if(!empty($is_all_member)){
                        foreach ($membership_types as $membership_type) {
                            $membership = new TblSaleDiscountSetupMembership;
                            $membership->discount_setup_membership_id = Utilities::uuid();
                            $membership->discount_setup_id = $discount_setup_id;
                            $membership->membership_type_id = $membership_type;
                            $membership->save();
                        }
                    }else{
                        if (!empty($is_with_member) && isset($request->membership_type) && count($request->membership_type) != 0) {
                            foreach ($request->membership_type as $membership_type) {
                                $membership = new TblSaleDiscountSetupMembership;
                                $membership->discount_setup_membership_id = Utilities::uuid();
                                $membership->discount_setup_id = $discount_setup_id;
                                $membership->membership_type_id = $membership_type;
                                $membership->save();
                            }
                        }
                    }
                }
            }
            if(empty($discount_setup_entry_saved)){
                return $this->jsonErrorResponse($data,'Please add Item or select Product Group', 200);
            }
        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getLine()." : ".$e->getMessage(), 200);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = '/smart-product/product-discount-setup/form';
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/smart-product/product-discount-setup/form';
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

            $discount = TblSaleDiscountSetup::where('discount_setup_id',$id)->first();
            if(!empty($discount)){
                $discount->discount_setup_membership()->delete();
                $discount->scheme_branches()->delete();
                $discount->delete();
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


    public function allGroupItemAdded($id){


        if(TblSaleDiscountSetup::where('discount_setup_type','group_item')->where('discount_setup_id',$id)->exists()){
            $discount = TblSaleDiscountSetup::with('discount_setup_membership','scheme_branches')->where('discount_setup_id',$id)->first();
            $discount_setup_id = $discount->discount_setup_id;
            $document_code = $discount->discount_setup_code;
            $discount_setup_title = $discount->discount_setup_title;
            $start_date = $discount->start_date;
            $end_date = $discount->end_date;
            $sale_type = $discount->sale_type;
            $discount_type = $discount->discount_type;
            $promotion_type = $discount->promotion_type;
            $discount_qty = $discount->discount_qty;
            $discount_perc = $discount->discount_perc;
            $flat_discount_qty = $discount->flat_discount_qty;
            $flat_discount_amount = $discount->flat_discount_amount;
            $discount_setup_remarks = $discount->remarks;
            $amount_for_point = $discount->amount_for_point;
            $point_quantity = $discount->point_quantity;
            $slab_base = $discount->slab_base;
            $is_with_member = $discount->is_with_member;
            $is_without_member = $discount->is_without_member;
            $created_at = $discount->created_at;
            $discount = null;
            TblSaleDiscountSetup::where('discount_setup_type','group_item')->where('discount_setup_id',$id)->where(Utilities::currentBCB())->delete();
            dump("delete all old items");
            $group_items = TblPurcGroupItem::where('group_item_level',3)->pluck('group_item_id')->toArray();
            dump("level 3 group items: " . count($group_items));
            foreach ($group_items as $k=>$group_item_id){
                $discount = new TblSaleDiscountSetup();
                $discount->discount_setup_id = $discount_setup_id;
                $discount->discount_setup_code = $document_code;
                $discount->discount_setup_row_id = Utilities::uuid();
                $discount->discount_setup_title = $discount_setup_title;
                $discount->discount_setup_type = 'group_item';
                $discount->start_date = date('Y-m-d H:i',strtotime($start_date));
                $discount->end_date = date('Y-m-d H:i',strtotime($end_date));
                $discount->sale_type = $sale_type;
                $discount->discount_type = $discount_type;
                $discount->promotion_type = $promotion_type;
                $discount->discount_qty = $discount_qty;
                $discount->discount_perc = $discount_perc;
                $discount->flat_discount_qty = $flat_discount_qty;
                $discount->flat_discount_amount = $flat_discount_amount;
                $discount->sr_no = 1;
                $discount->product_id = '';
                $discount->product_barcode_id = '';
                $discount->product_barcode_barcode = '';
                $discount->uom_id = '';
                $discount->packing = '';
                $discount->group_item_id = $group_item_id;
                $discount->cost_rate = '';
                $discount->mrp = '';
                $discount->sale_rate = '';
                $discount->gp_amount = '';
                $discount->gp_perc = '';
                $discount->disc_amount = '';
                $discount->disc_perc = '';
                $discount->after_disc_gp_amount = '';
                $discount->after_disc_gp_perc = '';
                $discount->is_active = 1;
                $discount->remarks = $discount_setup_remarks;
                $discount->user_id = 17580923022021;
                $discount->business_id = auth()->user()->business_id;
                $discount->company_id = auth()->user()->company_id;
                $discount->branch_id = 8;
                $discount->amount_for_point = $amount_for_point;
                $discount->point_quantity = $point_quantity;
                $discount->slab_base = $slab_base;
                $discount->is_with_member = $is_with_member;
                $discount->is_without_member = $is_without_member;
                $discount->created_at = $created_at;
                $discount->save();
                dump($k);
            }
            dd('done');
        }
        dd('error');
    }
}
