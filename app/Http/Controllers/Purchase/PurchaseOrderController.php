<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Traits\HasStaging;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use App\Models\Defi\TblDefiTaxGroup;
use App\Models\Draft\DraftPurcPurchaseOrder;
use App\Models\Draft\DraftPurcPurchaseOrderDtl;
use App\Models\TblAccoPaymentMode;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccoPaymentType;
use App\Models\TblAutoDemandDtl;
use App\Models\TblDefiCurrency;
use App\Models\TblDefiPaymentType;
use App\Models\TblPurcComparativeQuotation;
use App\Models\TblPurcComparativeQuotationDtl;
use App\Models\TblPurcDemand;
use App\Models\TblPurcLpo;
use App\Models\TblPurcLpoDtl;
use App\Models\TblPurcGrn;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblSoftUserPageSetting;
use App\Models\User;
use App\Models\ViewPurcGRN;
use App\Models\ViewPurcLpoDetail;
use App\Models\ViewPurcProductBarcodeRate;
use App\Models\ViewPurcPurchaseOrderListing;
use App\Models\ViewSaleSalesInvoice;
use App\Models\Sale\WhatsappLog;
use App\Services\StagingService;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    use HasStaging;
    public static $page_title = 'Purchase Order';
    public static $redirect_url = 'purchase-order';
    public static $menu_dtl_id = '38';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['title'] = self::$page_title;
        $data['case'] = self::$redirect_url;
        $data['create-form'] = '/purchase-order/form';
        $data['form-action'] = '/purchase-order';
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['table_id'] = 'purchase_order_id';
        $data['data_url'] = action('Purchase\PurchaseOrderController@index');

        $data['page_data'] = [];
        $data['page_data']['post'] = action('Purchase\PurchaseOrderController@post');
        $data['table_columns'] = [
            "purchase_order_code" => [
                'title' => "PO NO",
                'type' => 'string',
            ],
            "purchase_order_entry_date" => [
                'title' => "PO Date",
                'type' => 'date',
            ],
            "supplier_name" => [
                'title' => "Vendor Name",
                'type' => 'string',
            ],
            "purchase_order_total_net_amount" => [
                'title' => "Net Amount",
                'type' => 'string',
            ],
            "po_grn_status" => [
                'title' => "Status",
                'type' => 'string',
            ],
            "posted" => [
                'title' => "Posted",
                'type' => 'string',
            ],
            "purchase_order_delivery_date" => [
                'title' => "Delivery Date",
                'type' => 'date',
            ],
            "branch_name" => [
                'title' => "Branch",
                'type' => 'string',
            ],
            "remarks" => [
                'title' => "Remarks",
                'type' => 'string',
            ],
            "created_by" => [
                'title' => "Entry User",
                'type' => 'string',
            ],
            "created_at" => [
                'title' => "Entry Date",
                'type' => 'datetime',
            ],
            "updated_by" => [
                'title' => "Edit User",
                'type' => 'string',
            ],
            "updated_at" => [
                'title' => "Edit Date",
                'type' => 'datetime',
            ],
        ];

        if($request->ajax()){
            $tbl_1 = " tbl_1";
            $table = " vw_purc_purchase_order_listing $tbl_1 ";
            $columns = "$tbl_1.purchase_order_id, $tbl_1.purchase_order_code, $tbl_1.purchase_order_entry_date, $tbl_1.supplier_name, $tbl_1.purchase_order_total_net_amount, $tbl_1.po_grn_status, $tbl_1.posted, $tbl_1.purchase_order_delivery_date, $tbl_1.branch_name, $tbl_1.remarks, $tbl_1.created_by, $tbl_1.created_at, $tbl_1.updated_by, $tbl_1.updated_at";

            // dd($columns);

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

            $total  = DB::selectOne("select count(*) count from $table $where");
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
            $qry = "select $columns from $table $where $orderby $limit";
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id = null)
    {
        $data['form_type'] = 'purc_order';
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['menu_id'] = self::$menu_dtl_id;
        $data['page_data']['pending_pr'] = TRUE;
        $data['already_exits'] = false;
        if(isset($id)){
            $allowCrossBranchView = (string) $request->query('view') === '1';
            $baseQuery = TblPurcPurchaseOrder::where('purchase_order_id','LIKE',$id)->where(Utilities::currentBC());
            $exists = $allowCrossBranchView
                ? $baseQuery->exists()
                : $baseQuery->where('branch_id', auth()->user()->branch_id)->exists();

            if($exists){
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $currentQuery = TblPurcPurchaseOrder::with('po_details','supplier','lpo','comparative_quotation')
                    ->where('purchase_order_id',$id)
                    ->where(Utilities::currentBC());
                if(!$allowCrossBranchView){
                    $currentQuery = $currentQuery->where('branch_id', auth()->user()->branch_id);
                }
                $data['current'] = $currentQuery->first();
                if(empty($data['current'])){
                    abort('404');
                }
                if((string) $data['current']->branch_id === (string) auth()->user()->branch_id){
                    $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                }else{
                    $data['page_data'] = array_merge($data['page_data'], Utilities::viewForm());
                }
                    /*
                    * Add All Possible UOM Of The Product -- Required For Purchase Auto Demand
                    * We want to change the UOM of the Product Comming from Purchase Auto Demand
                    * We can't do this In Purchase Auto Demand Because there we are making Groups Of Items & It will become Complex.
                    */
                    foreach ($data['current']->po_details as $key => $product) {
                        $product['uom_list'] = Utilities::UOMList($product->product_id);
                    }
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
                $data['document_code'] = $data['current']->purchase_order_code;


                if(TblPurcGrn::where('purchase_order_id',$id)->exists()){
                    $data['already_exits'] = true;
                    $data['page_data']['action'] = '';
                }

                $data['page_data']['post'] = action('Purchase\PurchaseOrderController@post');
                $data['page_data']['cancel'] = action('Purchase\PurchaseOrderController@cancel');
                $data['page_data']['document_id_field'] = 'purchase_order_id';
                $data['page_data']['is_posted'] = isset($data['current']->posted) && (int) $data['current']->posted === 1;
                $data['page_data']['is_canceled'] = isset($data['current']->posted) && (int) $data['current']->posted === 2;

                $this->clearFormUpdateActionIfDocumentNotEditable($data['page_data'], $data['current']);
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcPurchaseOrder',
                'code_field'        => 'purchase_order_code',
                'code_prefix'       => strtoupper('po')
            ];
            $data['document_code'] = Utilities::documentCode($doc_data);
        }
        $data['currency'] = TblDefiCurrency::where('currency_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_mode'] = TblAccoPaymentMode::where('payment_mode_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where(Utilities::currentBC())->get();
        $data['tax_group'] = TblDefiTaxGroup::where('tax_group_entry_status',1)->where(Utilities::currentBC())->get();
        $data['tax_on'] = TblDefiConstants::where('constants_type','tax_on')->where('constants_status','1')->get();
        $data['disc_on'] = TblDefiConstants::where('constants_type','disc_on')->where('constants_status','1')->get();
        $data['po_priority'] = TblDefiConstants::where('constants_type','po_priority')->where('constants_status','1')->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['document_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_purchase_order',
            'col_id' => 'purchase_order_id',
            'col_code' => 'purchase_order_code',
        ];
        $data['switch_entry'] = $this->switchEntry($arr);

        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['perPrefix'] = self::$menu_dtl_id;

        return view('purchase.purchase_order.form', compact('data'));
    }


    public function post(Request $request)
    {
        try {
            $purchase_order_id = $request->purchase_order_id;
            if (empty($purchase_order_id)) {
                return response()->json(['status' => 'error', 'message' => 'Purchase Order id not found.'], 422);
            }
            $row = TblPurcPurchaseOrder::where('purchase_order_id', $purchase_order_id)->where(Utilities::currentBCB())->first();
            $this->guardUmDocumentAction(self::$menu_dtl_id, $row, 'post', 'post');
            $row->posted = 1;
            $row->update();
            return response()->json(['status' => 'success']);
        } catch (\RuntimeException $e) {
            return $this->umJsonErrorFromException($e);
        }
    }

    public function Posted(Request $request)
    {
        $data = [];
        $ids = $request->data;
        if(is_array($ids) && count($ids) > 0){
            foreach($ids as $id){
                try {
                    $row = TblPurcPurchaseOrder::where('purchase_order_id', $id)->where(Utilities::currentBCB())->first();
                    $this->guardUmDocumentAction(self::$menu_dtl_id, $row, 'post', 'post');
                } catch (\RuntimeException $e) {
                    return $this->jsonErrorResponse([], $e->getMessage(), $e->getCode() >= 400 ? $e->getCode() : 422);
                }
            }
            foreach($ids as $id){
                if(TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBCB())->exists()){
                    $row = TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
                    $row->posted = 1;
                    $row->update();
                }
            }
            return $this->jsonSuccessResponse($data, trans('Successfully Posted'), 200);
        }else{
            abort(404);
        }
    }


    public function UnPosted(Request $request)
    {
        $data = [];
        $ids = $request->data;
        if(is_array($ids) && count($ids) > 0){
            foreach($ids as $id){
                try {
                    $row = TblPurcPurchaseOrder::where('purchase_order_id', $id)->where(Utilities::currentBCB())->first();
                    $this->guardUmDocumentAction(self::$menu_dtl_id, $row, 'un_post_module', 'unpost');
                } catch (\RuntimeException $e) {
                    return $this->jsonErrorResponse([], $e->getMessage(), $e->getCode() >= 400 ? $e->getCode() : 422);
                }
            }
            foreach($ids as $id){
                if(TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBCB())->exists()){
                    $row = TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
                    $row->posted = 0;
                    $row->update();
                }
            }
            return $this->jsonSuccessResponse($data, trans('Successfully Un-Posted'), 200);
        }else{
            abort(404);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $purchase_order_id = $request->purchase_order_id;
            if (empty($purchase_order_id)) {
                return response()->json(['status' => 'error', 'message' => 'Purchase Order id not found.'], 422);
            }
            $row = TblPurcPurchaseOrder::where('purchase_order_id', $purchase_order_id)->where(Utilities::currentBCB())->first();
            $this->guardUmDocumentAction(self::$menu_dtl_id, $row, 'cancel', 'cancel');
            $row->posted = 2;
            $row->update();
            return response()->json(['status' => 'success', 'message' => trans('message.cancel')]);
        } catch (\RuntimeException $e) {
            return $this->umJsonErrorFromException($e);
        }
    }

    public function lpo()
    {
        $data = DB::table('tbl_purc_lpo')
            ->join('tbl_purc_lpo_dtl','tbl_purc_lpo.lpo_id','=','tbl_purc_lpo_dtl.lpo_id')
            ->join('tbl_purc_product','tbl_purc_product.product_id','=','tbl_purc_lpo_dtl.prod_id')
            ->join('tbl_defi_uom', 'tbl_defi_uom.uom_id', '=', 'tbl_purc_lpo_dtl.uom_id')
            ->join('tbl_purc_packing', 'tbl_purc_packing.packing_id', '=', 'tbl_purc_lpo_dtl.lpo_dtl_packing')
            ->select('tbl_purc_lpo.*','tbl_purc_lpo_dtl.*','tbl_defi_uom.uom_name','tbl_purc_packing.packing_name','tbl_purc_product.product_name')
            ->get();
      //  dd($data->toArray());
        return view('purchase.quotation.lpo',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id = null)
    {
        // dd($request->toArray());
        $data = [];
        if(isset($request->pd)){
            foreach($request->pd as $pd){
                if(!empty($pd['barcode'])){
                    $exits = TblPurcProductBarcode::where('product_barcode_barcode',$pd['barcode'])->exists();
                    if (!$exits) {
                        return $this->jsonErrorResponse($data, trans('message.not_barcode'), 200);
                    }
                }
            }
        }else{
            return $this->jsonErrorResponse($data, 'Fill The Grid', 200);
        }
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required',
            'supplier_id' => 'required|numeric',
            'lpo_generation_no_id' => 'nullable|numeric',
            'payment_terms' => 'nullable|numeric',
            'payment_mode' => 'nullable|numeric',
            'exchange_rate' => 'required',
            'po_currency' => 'required|numeric',
            'po_notes' => 'nullable|max:255'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try {
            if(isset($id)){
                $po = TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                $po = new TblPurcPurchaseOrder();
                $po->purchase_order_id = Utilities::uuid();
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcPurchaseOrder',
                    'code_field'        => 'purchase_order_code',
                    'code_prefix'       => strtoupper('po')
                ];
                $po->purchase_order_code = Utilities::documentCode($doc_data);
            }
            $form_id = $po->purchase_order_id;

            if(isset($id)){
                $this->assertCanSaveWithStaging($request, self::$menu_dtl_id, $form_id, false, $po);
            }

            if (isset($id) && !$this->stagingShouldPersistFormChanges($request, self::$menu_dtl_id, $form_id, $po)) {
                $wasInStaging = !empty($po->current_stg_id) && (int)($po->posted ?? 0) === 0;
                $stagingService = new StagingService();
                $criteriaApplies = $stagingService->shouldUseStagingForDocument(self::$menu_dtl_id, $form_id, $po, $wasInStaging, false);

                if ($criteriaApplies) {
                    $this->handleStaging($request, self::$menu_dtl_id, $form_id, $po, false, [
                        'listing_view' => 'vw_purc_purchase_order_listing',
                        'form_path' => '/purchase-order/form',
                        'document_code_key' => 'purchase_order_code',
                    ]);
                    $po->save();
                }
                DB::commit();

                $data = array_merge($data, Utilities::returnJsonEditForm());
                $data['redirect'] = $this->documentFormStayRedirect('/'.self::$redirect_url, $form_id);
                return $this->jsonSuccessResponse($data, trans('message.update'), 200);
            }

            $po->purchase_order_entry_date = date('Y-m-d', strtotime($request->po_date));;
            $po->payment_mode_id = $request->payment_terms;
            $po->purchase_order_credit_days = $request->payment_mode;
            $po->lpo_id = $request->lpo_generation_no_id;
            $po->supplier_id = $request->supplier_id;
            $po->currency_id = $request->po_currency;
            $po->purchase_order_exchange_rate = $request->exchange_rate;
            $po->purchase_order_remarks = $request->po_notes;
            $po->comparative_quotation_id = $request->comparative_quotation_id;
            $po->purchase_order_entry_status = "1";
            $po->business_id = auth()->user()->business_id;
            $po->company_id = auth()->user()->company_id;
            $po->branch_id = auth()->user()->branch_id;
            $po->purchase_order_user_id = auth()->user()->id;

            if(isset($id)){
                TblPurcPurchaseOrderDtl::where('purchase_order_id',$id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->pd)){
                $sr_no = 1;
                $grandTotal = 0;
                foreach($request->pd as $pd){
                    $dtl = new TblPurcPurchaseOrderDtl();
                    if(isset($pd['purchase_order_dtl_id'])){
                        $dtl->purchase_order_dtl_id = $pd['purchase_order_dtl_id'];
                        $dtl->purchase_order_id = $id;
                    }else{
                        $dtl->purchase_order_dtl_id = Utilities::uuid();
                        $dtl->purchase_order_id = $po->purchase_order_id;
                    }
                    $dtl->purchase_order_dtlsr_no = $sr_no++;
                    $dtl->lpo_id = isset($pd['lpo_id'])?$pd['lpo_id']:'';
                    $dtl->comparative_quotation_id = isset($pd['comparative_quotation_id'])?$pd['comparative_quotation_id']:'';
                    $dtl->product_barcode_barcode = isset($pd['pd_barcode'])?$pd['pd_barcode']:"";
                    $dtl->product_id = $pd['product_id'];
                    $dtl->product_barcode_id = isset($pd['product_barcode_id'])?$pd['product_barcode_id']:"";
                    $dtl->uom_id = $pd['uom_id'];
                    $dtl->purchase_order_dtlpacking = isset($pd['pd_packing'])?$pd['pd_packing']:"";
                    $dtl->purchase_order_dtl_remarks = isset($pd['remarks'])?$pd['remarks']:"";
                    $dtl->purchase_order_dtlquantity = $this->addNo($pd['quantity']);
                    $dtl->purchase_order_dtlfoc_quantity = $this->addNo($pd['foc_qty']);
                    $dtl->purchase_order_dtlfc_rate = $this->addNo($pd['fc_rate']);
                    $dtl->purchase_order_dtlrate = $this->addNo($pd['rate']);
                    $dtl->purchase_order_dtlamount = $this->addNo($pd['amount']);
                    $dtl->purchase_order_dtldisc_percent = $this->addNo($pd['dis_perc']);
                    $dtl->purchase_order_dtldisc_amount = $this->addNo($pd['dis_amount']);
                    $dtl->purchase_order_dtlvat_percent = $this->addNo($pd['vat_perc']);
                    $dtl->purchase_order_dtlvat_amount = $this->addNo($pd['vat_amount']);
                    $dtl->purchase_order_dtltotal_amount = $this->addNo($pd['gross_amount']);
                    $grandTotal += (float) $dtl->purchase_order_dtltotal_amount;
                    $dtl->business_id = auth()->user()->business_id;
                    $dtl->company_id = auth()->user()->company_id;
                    $dtl->branch_id = auth()->user()->branch_id;
                    $dtl->purchase_order_dtluser_id = auth()->user()->id;
                    $dtl->save();

                }
                $po->purchase_order_total_net_amount = $grandTotal;
            }else{
                $po->purchase_order_total_net_amount = 0;
            }
            $po->save();

            $this->finalizeDocumentStaging($request, self::$menu_dtl_id, $form_id, $po, !isset($id), [
                'notification' => [
                    'listing_view' => 'vw_purc_purchase_order_listing',
                    'form_path' => '/purchase-order/form',
                    'document_code_key' => 'purchase_order_code',
                ],
                'posted_when_exempt' => 0,
                'stg_log_posted_when_exempt' => 0,
            ]);

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
            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 200;
            return $this->jsonErrorResponse($data, $e->getMessage(), $status);
        }
        DB::commit();
        if(isset($id)){
            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->documentFormStayRedirect('/'.self::$redirect_url, $form_id);
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
            $data['redirect'] = '/'.self::$redirect_url.$this->prefixCreatePage.'/'.$form_id;
            return $this->jsonSuccessResponse($data, trans('message.create'), 200);
        }
    }
    public function getLpo($lpo_id, $suppler_id){
        $data = [];
        DB::beginTransaction();
        try {
            $data['all'] = ViewPurcLpoDetail::where('lpo_id',$lpo_id)
                ->where('supplier_id',$suppler_id)
                ->where('lpo_dtl_generate_lpo',1)
                ->orderBy('sr_no' , 'asc')
                ->get();
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
        if(isset($lpo_id) && isset($suppler_id)){
            $data['lpo_id'] = $lpo_id;
            $data['suppler_id'] = $suppler_id;
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }else{
            return $this->jsonErrorResponse($data, 'Something will be wrong', 200);
        }
    }
    public function getAutoDemand($ad_id, $suppler_id = null){
        $data = [];
        DB::beginTransaction();
        try {
            $where = '';
            if(isset($ad_id) && isset($suppler_id)){
                $where .= "D.AD_ID =" . $ad_id . " AND D.SUPPLIER_ID = " . $suppler_id;
            }else{
                $where .= "D.AD_ID =" . $ad_id;
            }

            $count = DB::table('TBL_PURC_AUTO_DEMAND_DTL')->where('ad_id' , $ad_id )->count();

            //SUM(D.APPROVE_QTY) QTY,SUM(D.FOC_QTY) FOC_QTY,DECODE(SUM(D.APPROVE_QTY),0,0,((SUM(D.AMOUNT)/SUM(D.APPROVE_QTY)))) AS RATE,

            if($count > 0){
                $query = "SELECT BR.UOM_NAME,BR.PRODUCT_BARCODE_BARCODE,BR.PRODUCT_NAME,D.PRODUCT_ID,
                    D.PRODUCT_BARCODE_ID,D.PRODUCT_BARCODE_PACKING PACKING,D.PRODUCT_UNIT_ID UOM_ID,
                    SUM(D.APPROVE_QTY) QTY,SUM(D.FOC_QTY) FOC_QTY,DECODE(SUM(D.APPROVE_QTY),0,0,((SUM(D.AMOUNT)/SUM(D.APPROVE_QTY)))) AS RATE,
                    SUM(D.AMOUNT) AMOUNT,AVG(D.VAT) VAT_AMOUNT,SUM(D.TOT_AMOUNT) GROSS_AMOUNT,
                    AVG(D.VAT_PERC) VAT_PERC, SUM(D.DISC_PERC) DIS_PERC, SUM(D.DISC) DISC FROM
                    TBL_PURC_AUTO_DEMAND_DTL D
                    JOIN VW_PURC_PRODUCT_BARCODE_FIRST BR ON BR.PRODUCT_ID = D.PRODUCT_ID
                    WHERE ". $where ." AND D.IS_APPROVE = 'approved' GROUP BY
                    BR.UOM_NAME,BR.PRODUCT_BARCODE_BARCODE,BR.PRODUCT_NAME,D.PRODUCT_ID,D.PRODUCT_BARCODE_ID,D.PRODUCT_BARCODE_PACKING,D.PRODUCT_UNIT_ID";
                $data['all'] = DB::select($query);
            }else{
                $data['all'] = [];
                return $this->jsonErrorResponse($data, 'Nothing Found Try Again Later!', 200);
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
        if(isset($ad_id) && isset($suppler_id)){
            $data['ad_id'] = $ad_id;
            $data['suppler_id'] = $suppler_id;
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }elseif(isset($ad_id) && !isset($suppler_id)){
            $data['ad_id'] = $ad_id;
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }else{
            return $this->jsonErrorResponse($data, 'Something will be wrong', 200);
        }
    }
    public function getQuotation($id){
        $data = [];
        DB::beginTransaction();
        try {
            $data['all'] = TblPurcComparativeQuotationDtl::with('product','uom','packing','supplier')->where('comparative_quotation_id',$id)
                ->get();
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
            $data['comparative_quotation_id'] = $id;
            return $this->jsonSuccessResponse($data, 'Data loaded', 200);
        }else{
            return $this->jsonErrorResponse($data, 'Something will be wrong', 200);
        }
    }
    public function store_old(Request $request)
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

    public function print(Request $request, $id)
    {
        // dd($request->toArray());
        $data['title'] = 'Purchase Order';
        $data['type'] = $request->type;
        $data['id'] = $id;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $url = '/purchase-order/print/'.$id;
        $data['print_link'] = $url;
        // dd($url);
        if(isset($id)){
            if(TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBC())->exists()){
                $data['current'] = TblPurcPurchaseOrder::with('po_details','supplier','lpo','comparative_quotation')->where('purchase_order_id',$id)->where(Utilities::currentBC())->first();
            }else{
                abort('404');
            }
        }
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_mode_id)->where('payment_term_entry_status',1)->where(Utilities::currentBC())->first();
        if(isset($type) && $type=='pdf'){
            $view = view('prints.purchase_order_print', compact('data'))->render();
            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            if ($data['type'] == '1') {
                return view('prints.purchase.purchase_order.purchase_order_print', compact('data'));
            }elseif($data['type'] == '2'){
                return view('prints.purchase.purchase_order.purchase_order_super_store', compact('data'));
            }elseif($data['type'] == '3'){
                return view('prints.purchase.purchase_order.purchase_order_with_mrp', compact('data'));
            }elseif($data['type'] == '4'){
                return view('prints.purchase.purchase_order.ex_purchase_order_with_inventory', compact('data'));
            }elseif($data['type'] == '5'){
                return view('prints.purchase.purchase_order.simple_purchase_order_print', compact('data'));
            }else{
                return view('prints.purchase.purchase_order.purchase_order_default_print', compact('data'));
            }
        }
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


            $dataExist = TblPurcGrn::where('purchase_order_id','Like',$id)->where(Utilities::currentBCB())->exists();
            if($dataExist === false)
            {
                $po = TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
                $po->po_details()->delete();
                $po->delete();
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

    public function InventoryPrint($id,$type = null)
    {
        $data['title'] = 'Purchase Order';
        $data['type'] = $type;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/purchase-order/inventory/print/'.$id.'/pdf';
        if(isset($id)){
            if(TblPurcPurchaseOrder::where('purchase_order_id','LIKE',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcPurchaseOrder::with('po_details','supplier')->where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
                // dd($data['current']);
            }else{
                abort('404');
            }
        }
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_mode_id)->where('payment_term_entry_status',1)->where(Utilities::currentBC())->first();
        if(isset($type) && $type=='pdf'){
            $view = view('prints.purchase_order_inventory_print', compact('data'))->render();
            //dd($view);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            return $dompdf->stream();
        }else{
            return view('prints.purchase_order_inventory_print', compact('data'));
        }
    }


    public function listDraft(Request $request)
    {
        //dd($request->toArray());
        $data = [];
        $data['list'] = DraftPurcPurchaseOrder::with('supplier')
            ->where('purchase_order_user_id',auth()->user()->id)
            ->where(Utilities::currentBCB())->select(['created_at','purchase_order_id','supplier_id','purchase_order_remarks','purchase_order_total_amount'])->orderby('created_at','desc')->get();

        return view('purchase.purchase_order.list_draft', compact('data'));
    }

    public function createDraft(Request $request,$id)
    {
        $draft = DraftPurcPurchaseOrder::where('purchase_order_id',$id)->first();

        if(!empty($draft)){
            session(['po_draft_id' => $id]);
        }
        return $this->jsonSuccessResponse([], '', 200);
    }

    public function storeDraft(Request $request,$id = null)
    {
        //dd($request->toArray());
        $data = [];

        $validator = Validator::make($request->all(), [
            /*'supplier_name' => 'required',
            'supplier_id' => 'required|numeric',
            'payment_terms' => 'nullable|numeric',
            'payment_mode' => 'nullable|numeric',
            'exchange_rate' => 'required',
            'po_currency' => 'required|numeric',
            'po_notes' => 'nullable|max:255',
            'pd.*.product_id' => 'required|numeric',
            'pd.*.product_barcode_id' => 'required|numeric',*/
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try {
            $po_data = [
                'purchase_order_code' => '',
                'purchase_order_entry_date' => date('Y-m-d', strtotime($request->po_date)),
                'purchase_order_delivery_date' => date('Y-m-d', strtotime($request->po_date)),
                'payment_mode_id' => $request->payment_terms,
                'purchase_order_credit_days' => $request->payment_mode,
                'supplier_id' => $request->supplier_id,
                'currency_id' => $request->po_currency,
                'purchase_order_exchange_rate' => $request->exchange_rate,
                'priority_value' => $request->priority_id,
                'shipment_provided_id' => $request->shipment_provided_id,
                'purchase_order_overall_discount' => $request->overall_discount_perc,
                'purchase_order_overall_disc_amount' => $request->overall_disc_amount,
                'purchase_order_total_items' => $request->summary_total_item,
                'purchase_order_total_qty' => $request->summary_qty_wt,
                'purchase_order_total_amount' => $request->summary_amount,
                'purchase_order_total_disc_amount' => $request->summary_disc_amount,
                'purchase_order_total_gst_amount' => $request->summary_gst_amount,
                'purchase_order_total_fed_amount' => $request->summary_fed_amount,
                'purchase_order_total_spec_disc_amount' => $request->summary_spec_disc_amount,
                'purchase_order_total_gross_net_amount' => $request->summary_net_amount,
                'purchase_order_total_net_amount' => $request->overall_net_amount,
                'purchase_order_remarks' => $request->po_notes,
                'purchase_order_entry_status' => 1,
                'business_id' => auth()->user()->business_id,
                'company_id' => auth()->user()->company_id,
                'branch_id' => auth()->user()->branch_id,
                'purchase_order_user_id' => auth()->user()->id,
            ];

            if(isset($id)){
                DraftPurcPurchaseOrder::where('purchase_order_id',$id)->update($po_data);
                $data['purchase_order_id'] = $id;
            }else{
                $po_data['purchase_order_id'] = Utilities::uuid();
                DraftPurcPurchaseOrder::create($po_data);
                $data['purchase_order_id'] = $po_data['purchase_order_id'];
            }

            if(isset($request->pd) && count($request->pd) != 0){
                $i = 1;
                DraftPurcPurchaseOrderDtl::where('purchase_order_id',$id)->where(Utilities::currentBCB())->delete();
                foreach ($request->pd as $pd){
                    $exits = TblPurcProductBarcode::where('product_barcode_barcode',$pd['pd_barcode'])->exists();
                    if(empty($exits)){
                        $msg = $pd['barcode']." Barcode is not correct";
                        return $this->jsonErrorResponse($data, $msg, 200);
                    }
                    $po_dtl = [
                        'purchase_order_dtl_id' => Utilities::uuid(),
                        'purchase_order_id' => $data['purchase_order_id'],
                        'purchase_order_dtlsr_no' => $i,
                        'product_id' => $pd['product_id'],
                        'uom_id' => $pd['uom_id'],
                        'product_barcode_id' => $pd['product_barcode_id'],
                        'product_barcode_barcode' => $pd['pd_barcode'],
                        'purchase_order_dtlpacking' =>  $pd['pd_packing'],
                        'purchase_order_dtlquantity' => Utilities::NumFormat($pd['quantity']),
                        'purchase_order_dtlfc_rate' => Utilities::NumFormat($pd['fc_rate']),
                        'purchase_order_dtlrate' => Utilities::NumFormat($pd['rate']),
                        'purchase_order_dtlamount' => Utilities::NumFormat($pd['cost_amount']),
                        'purchase_order_dtldisc_percent' => Utilities::NumFormat($pd['dis_perc']),
                        'purchase_order_dtldisc_amount' => Utilities::NumFormat($pd['dis_amount']),
                        'purchase_order_dtlafter_dis_amount' => Utilities::NumFormat($pd['after_dis_amount']),
                        'purchase_order_dtlvat_percent' => Utilities::NumFormat($pd['gst_perc']),
                        'purchase_order_dtlvat_amount' => Utilities::NumFormat($pd['gst_amount']),
                        'purchase_order_dtltotal_amount' => Utilities::NumFormat($pd['net_amount']),
                        'business_id' => auth()->user()->business_id,
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                        'purchase_order_dtluser_id' => auth()->user()->id,
                        'purchase_order_dtl_remarks',
                        'purchase_order_dtlsale_rate' => Utilities::NumFormat($pd['sale_rate']),
                        'purchase_order_dtlmrp' => Utilities::NumFormat($pd['mrp']),
                        'purchase_order_dtlspec_disc_perc' => Utilities::NumFormat($pd['spec_disc_perc']),
                        'purchase_order_dtlspec_disc_amount' => Utilities::NumFormat($pd['spec_disc_amount']),
                        'purchase_order_dtlnet_tp' => Utilities::NumFormat($pd['net_tp']),
                        'purchase_order_dtllast_tp' => Utilities::NumFormat($pd['last_tp']),
                        'purchase_order_dtlvend_last_tp' => Utilities::NumFormat($pd['vend_last_tp']),
                        'purchase_order_dtlfed_perc' => Utilities::NumFormat($pd['fed_perc']),
                        'purchase_order_dtlfed_amount' => Utilities::NumFormat($pd['fed_amount']),
                        'purchase_order_dtlsys_quantity' => Utilities::NumFormat($pd['sys_qty']),
                        'purchase_order_dtltax_on' => $pd['pd_tax_on'],
                        'purchase_order_dtldisc_on' => $pd['pd_disc'],
                        'purchase_order_dtltp_diff' => Utilities::NumFormat($pd['tp_diff']),
                        'purchase_order_dtlgp_perc' => Utilities::NumFormat($pd['gp_perc']),
                        'purchase_order_dtlgp_amount' => Utilities::NumFormat($pd['gp_amount']),
                        'purchase_order_dtlgross_amount' => Utilities::NumFormat($pd['gross_amount']),
                    ];

                    DraftPurcPurchaseOrderDtl::create($po_dtl);

                    $i += 1;
                }
            }

        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();

        return $this->jsonSuccessResponse($data, '', 200);
    }

    public function destroyDraft($id)
    {
        $data = [];
        DB::beginTransaction();
        try{
           // dd($id);
            $po = DraftPurcPurchaseOrder::where('purchase_order_id',$id)->first();
            $po->po_details()->delete();
            $po->delete();

        } catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, "PO Draft Entry Successfully deleted", 200);
    }

    public function fetchSupplierInfo(Request $request)
    {
        $supplierCode = $request->query('supplier_code');
        if (!$supplierCode) {
            return response()->json(['error' => 'Supplier code is required'], 400);
        }
        $supplier = TblPurcSupplier::where('supplier_id', $supplierCode)->first();
        if (!$supplier) {
            return response()->json(['error' => 'Supplier not found'], 404);
        }
        $supplierPhone = $supplier->supplier_phone_1;
        if (!$supplierPhone) {
            return response()->json(['error' => 'Supplier phone number not found'], 404);
        }

        return response()->json([
            'phone' => $supplierPhone
        ]);
    }

    public function generatePdfForWhatsApp(Request $request, $id)
    {
        $data['title'] = 'Purchase Order';
        $data['type'] = 'pdf';
        $data['id'] = $id;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $data['print_link'] = '/purchase-order/print/'.$id;

        if(isset($id)){
            if(TblPurcPurchaseOrder::where('purchase_order_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcPurchaseOrder::with(['po_details.product.group_item','po_details.barcode','po_details.uom','supplier','lpo','comparative_quotation'])->where('purchase_order_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                return response()->json(['error' => 'Purchase order not found'], 404);
            }
        }

        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        if(!$data['currency']){
            $data['currency'] = (object)['currency_name' => ''];
        }
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->payment_mode_id)->where('payment_term_entry_status',1)->where(Utilities::currentBC())->first();

        try {
            // Generate PDF using type 1 template
            $view = view('prints.purchase.purchase_order.purchase_order_print', compact('data'))->render();

            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('dpi', 100);
            $options->set('isPhpEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $options->setDefaultFont('roboto');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($view,'UTF-8');
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Save PDF to storage
            $filename = 'po_' . $data['current']->purchase_order_code . '_' . time() . '.pdf';
            $directory = storage_path('app/public/purchase_orders');

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $filePath = $directory . '/' . $filename;
            file_put_contents($filePath, $dompdf->output());

            // Return public URL (requires: php artisan storage:link)
            $publicUrl = url('storage/purchase_orders/' . $filename);

            return response()->json([
                'success' => true,
                'url' => $publicUrl,
                'filePath' => $publicUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function sendWhatsappMsg(Request $request)
    {
        $to = $request->to;
        $message = $request->message;
        $filePath = $request->filePath;
        $invoiceNumber = $request->invoiceNumber;
        $title = $request->title;

        $curl = curl_init();

        $apiUrl = config('whatsapp.intelligent.api_url');
        $appkey = config('whatsapp.intelligent.appkey');
        $authkey = config('whatsapp.intelligent.authkey');
        $sandbox = config('whatsapp.intelligent.sandbox');

        if($filePath == '' || $filePath == null) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                'appkey' => $appkey,
                'authkey' => $authkey,
                'to' => $to,
                'message' => $message,
                'sandbox' => $sandbox
                ),
            ));
        } else {
            curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
            'appkey' => $appkey,
            'authkey' => $authkey,
            'to' => $to,
            'message' => $message,
            'sandbox' => $sandbox,
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
                    'user_id' => auth()->id(),
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
