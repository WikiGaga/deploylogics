<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Settings\TblDefiExpenseAccounts;
use App\Models\TblSoftBranch;
use App\Models\TblAccoVoucher;
use App\Models\TblDefiCurrency;
use App\Models\TblDefiPaymentType;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcProductBarcodeDtl;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\TblPurcProductFOC;
use App\Models\TblPurcPurchaseOrder;
use App\Models\TblPurcPurchaseOrderDtl;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcGrn;
use App\Models\TblPurcGrnDtl;
use App\Models\TblPurcGrnExpense;
use App\Models\TblAccoPaymentTerm;
use App\Models\TblAccCoa;
use App\Models\TblDefiStore;
use App\Models\ViewPurcPoGrnStock;
use App\Models\ViewPurcProductBarcodeHelp;
use App\Models\TblPurcSupProdDtl;
use App\Models\ViewPurcProductBarcodeRate;
use App\Models\ViewPurcPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Library\Utilities;
use App\Models\Defi\TblDefiConstants;
use App\Models\TblAccoPaymentMode;
use App\Models\TblPurcProductBarcodeSaleRate;
use App\Models\ViewPurcGRN;
use Illuminate\Validation\Rule;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Importer;

use function PHPSTORM_META\type;

class GRNController extends Controller
{
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
        $data['path-form'] = self::$redirect_url.$this->prefixCreatePage;
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        $data['table_columns'] = [
            "grn_code" => "GRN NO",
            "grn_date" => "GRN Date",
            "supplier_type_name" => "Vend Group",
            "supplier_name" => "Vendor Name",
            "grn_advance_tax_amount" => "ADV Tax",
            "grn_total_net_amount" => "Net Amount",
            "po_date" => "PO Date",
            "po_code" => "PO No",
            "created_by" => "Entry User",
            "created_at" => "Entry Date",
            "updated_by" => "Edit User",
            "updated_at" => "Edit Date"
        ];
        $data['table_columns_date'] = [
            "grn_date",
            "po_date",
            "created_at",
            "updated_at"
        ];
        if($request->ajax()){
            $tbl_1 = " tbl_1";
            $table = " vw_purc_grn_listing $tbl_1 ";
            $columns = "$tbl_1.grn_id, $tbl_1.grn_code, $tbl_1.grn_date, $tbl_1.supplier_type_name, $tbl_1.supplier_name, $tbl_1.grn_advance_tax_amount, $tbl_1.grn_total_net_amount, $tbl_1.po_date, $tbl_1.po_code, $tbl_1.created_by, $tbl_1.created_at, $tbl_1.updated_by, $tbl_1.updated_at";

            $where = " where $tbl_1.business_id = ".auth()->user()->business_id;
            $where .= " and $tbl_1.branch_id = ".auth()->user()->branch_id;
            $where .= " and (LOWER($tbl_1.grn_type) = 'grn')";

            $today = date('d/m/Y');
            $time_from = '12:00:00 am';
            $time_to = '11:59:59 pm';
            $global_filter_bollean = false;
            if (isset($request['query']['globalFilters'])) {
                $globalFilters = $request['query']['globalFilters'];
                $global_search = false;
                if(isset($globalFilters['global_search']) && !empty($globalFilters['global_search'])){
                    $generalSearch = str_replace(" " , "%" , $globalFilters['global_search']);
                    $where .= " and ( ";
                    $where .= " $tbl_1.grn_code like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.supplier_type_name like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.supplier_name like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.grn_advance_tax_amount like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.grn_total_net_amount like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.po_code like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.created_by like '%$generalSearch%' OR ";
                    $where .= " $tbl_1.updated_by like '%$generalSearch%' ";
                    $where .= " ) ";

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
                        if(isset($inline_filter['grn_code']) && !empty($inline_filter['grn_code'])){
                            $inline_where .= " and lower($tbl_1.grn_code) like '%".strtolower($inline_filter['grn_code'])."%'";
                        }
                        if(isset($inline_filter['grn_date']) && !empty($inline_filter['grn_date'])){
                            $created_at = date('d/m/Y',strtotime($inline_filter['grn_date']));
                            $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $inline_to_date = "$d_from and $d_to";
                            $inline_where .= " and ( $tbl_1.grn_date between ".$inline_to_date.") ";
                        }
                        if(isset($inline_filter['supplier_type_name']) && !empty($inline_filter['supplier_type_name'])){
                            $inline_where .= " and lower($tbl_1.supplier_type_name) like '%".strtolower($inline_filter['supplier_type_name'])."%'";
                        }
                        if(isset($inline_filter['supplier_name']) && !empty($inline_filter['supplier_name'])){
                            $inline_where .= " and lower($tbl_1.supplier_name) like '%".strtolower($inline_filter['supplier_name'])."%'";
                        }
                        if(isset($inline_filter['grn_advance_tax_amount']) && !empty($inline_filter['grn_advance_tax_amount'])){
                            $inline_where .= " and $tbl_1.grn_advance_tax_amount = '".$inline_filter['grn_advance_tax_amount']."'";
                        }
                        if(isset($inline_filter['grn_total_net_amount']) && !empty($inline_filter['grn_total_net_amount'])){
                            $inline_where .= " and $tbl_1.grn_total_net_amount like '%".$inline_filter['grn_total_net_amount']."%'";
                        }
                        if(isset($inline_filter['po_date']) && !empty($inline_filter['po_date'])){
                            $created_at = date('d/m/Y',strtotime($inline_filter['po_date']));
                            $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $inline_to_date = "$d_from and $d_to";
                            $inline_where .= " and ( $tbl_1.po_date between ".$inline_to_date.") ";
                        }
                        if(isset($inline_filter['po_code']) && !empty($inline_filter['po_code'])){
                            $inline_where .= " and lower($tbl_1.po_code) like '%".strtolower($inline_filter['branch_name'])."%'";
                        }
                        if(isset($inline_filter['created_by']) && !empty($inline_filter['created_by'])){
                            $inline_where .= " and lower($tbl_1.created_by) like '%".strtolower($inline_filter['created_by'])."%'";
                        }
                        if(isset($inline_filter['created_at']) && !empty($inline_filter['created_at'])){
                            $created_at = date('d/m/Y',strtotime($inline_filter['created_at']));
                            $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $inline_to_date = "$d_from and $d_to";
                            $inline_where .= " and ( $tbl_1.created_at between ".$inline_to_date.") ";
                        }
                        if(isset($inline_filter['updated_by']) && !empty($inline_filter['updated_by'])){
                            $inline_where .= " and lower($tbl_1.updated_by) like '%".strtolower($inline_filter['updated_by'])."%'";
                        }
                        if(isset($inline_filter['updated_at']) && !empty($inline_filter['updated_at'])){
                            $created_at = date('d/m/Y',strtotime($inline_filter['updated_at']));
                            $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                            $inline_to_date = "$d_from and $d_to";
                            $inline_where .= " and ( $tbl_1.updated_at between ".$inline_to_date.") ";
                        }
                    }
                    $where .= $inline_where;
                }
            }
            if(!$global_filter_bollean){
                $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                $where .=  ' AND ('.$tbl_1.'.created_at between '. $from .' AND '. $to.') ';
            }


            $sortDirection  = ($request->has('sort.sort') && $request->filled('sort.sort'))? $request->input('sort.sort') : 'desc';
            $sortField  = ($request->has('sort.field') && $request->filled('sort.field'))? $request->input('sort.field') : 'updated_at';
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
            // dd($qry);
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
        return view('purchase.grn.list',compact('data'));
    }
    public static $page_title = 'GRN';
    public static $redirect_url = 'grn';
    public static $menu_dtl_id = '23';
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['form_type'] = 'grn';
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = '/grn/list';//$this->prefixIndexPage.self::$redirect_url;;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        $data['page_data']['pending_pr'] = TRUE;
        $data['menu_dtl_id'] = self::$menu_dtl_id;
        if(isset($id)){
            if(TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;

                $data['current'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')->where('grn_id',$id)->where(Utilities::currentBCB())->first();
              
                if(empty($data['current'])){
                    abort('404');
                }
                $data['grn_code'] = $data['current']->grn_code;
                $data['page_data']['print'] = '/'.self::$redirect_url.'/print/'.$id;
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $doc_data = [
                'biz_type'          => 'branch',
                'model'             => 'TblPurcGrn',
                'code_field'        => 'grn_code',
                'code_prefix'       => strtoupper('grn'),
                'code_type_field'   => 'grn_type',
                'code_type'         => strtoupper('grn'),
            ];
            $data['grn_code'] = Utilities::documentCode($doc_data);
        }
        $data['currency'] = TblDefiCurrency::where(Utilities::currentBC())->get();
        $data['accounts'] = TblDefiExpenseAccounts::with('account')->where('expense_accounts_type','grn_acc')->where(Utilities::currentBCB())->get();
        $data['store'] = TblDefiStore::where('store_entry_status',1)->where(Utilities::currentBCB())->get();
        $data['payment_type'] = TblDefiPaymentType::where('payment_type_entry_status',1)
            ->where(Utilities::currentBC())->get();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_entry_status',1)->where(Utilities::currentBC())->get();
        $data['payment_mode'] = TblAccoPaymentMode::where('payment_mode_entry_status',1)->where(Utilities::currentBC())->get();
        $data['tax_on'] = TblDefiConstants::where('constants_type','tax_on')->where('constants_status','1')->get();
        $data['disc_on'] = TblDefiConstants::where('constants_type','disc_on')->where('constants_status','1')->get();
        $arr = [
            'biz_type' => 'branch',
            'code' => $data['grn_code'],
            'link' => $data['page_data']['create'],
            'table_name' => 'tbl_purc_grn',
            'col_id' => 'grn_id',
            'col_code' => 'grn_code',
            'code_type_field'   => 'grn_type',
            'code_type'         => strtoupper('grn'),
        ];
        $data['switch_entry'] = $this->switchEntry($arr);
        return view('purchase.grn.form', compact('data'));
    }

    public function getPO($code){ 
        $data['status'] = "success";
        if(TblPurcPurchaseOrder::where('purchase_order_code',$code)->exists()){
            //$po = ViewPurcPoGrnStock::where(Utilities::currentBCB());
            $po = ViewPurcPurchaseOrder::where(Utilities::currentBCB());
            $po = $po->where('purchase_order_code',$code);
            $data['all'] = $po->orderby('purchase_order_dtlsr_no')->get();
            $data['tax_on'] = TblDefiConstants::where('constants_type','tax_on')->where('constants_status','1')->get();
            $data['disc_on'] = TblDefiConstants::where('constants_type','disc_on')->where('constants_status','1')->get();

        }else{
            $data['status'] = "error";
        }
        
        return response()->json($data);
    }

    public function getPOProduct($code,$po_id=null){

        if(isset($po_id) && !empty($po_id)){
            $data['product'] = TblPurcPurchaseOrderDtl::with('product','barcode','uom')
                ->where('purchase_order_id',$po_id)
                ->where('product_barcode_barcode', $code)
                ->where(Utilities::currentBCB())
                ->first();
            if(!empty($data['product'])){
                $data['uom_list'] = Utilities::UOMList($data['product']->product_id);
            }
            $data['selected_po_code'] = true;
        }else{
            $data['product'] = ViewPurcProductBarcodeHelp::where('product_barcode_barcode', $code)
                        ->where('product_perishable', 1)
                        ->where(Utilities::currentBC())
                        ->first();
            if(!empty($data['product'])){
                $data['uom_list'] = Utilities::UOMList($data['product']->product_id);
            }
            $data['selected_po_code'] = false;
        }
        if(!empty($data['product'])){
            return $this->jsonSuccessResponse($data, trans('PO Product'), 200);
        }else{
            return $this->jsonErrorResponse($data, trans('Product Not Found'), 201);
        }
    }

    public function getSupProd($code,$sup_id){
        $data['current'] = TblPurcSupProdDtl::where('sup_prod_sup_barcode',$code)->where('sup_prod_supplier_id',$sup_id)->where(Utilities::currentBCB())->first('sup_prod_dtl_barcode');
        return response()->json($data);
    }


    public function checkPO($po_id)
    {
        $data= [];
        if(isset($po_id) && !empty($po_id))
        {
            $data['grn_po'] = TblPurcGrn::where('purchase_order_id',$po_id)
                ->where(Utilities::currentBCB())
                ->first();
        }
        if(isset($data['grn_po']->purchase_order_id) && !empty($data['grn_po']->purchase_order_id))
        {
            //return $this->returnjsonerror("This PO is Already Enter In GRN",200);
            return $data['grn_po']->purchase_order_id;
        }
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
            'supplier_name' => 'required',
            'supplier_id' => 'required|numeric',
            'purchase_order_id' => 'nullable|numeric',
            'grn_currency' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'grn_store' => 'required|numeric',
            'grn_bill_no' => 'required',
            // 'grn_ageing_term_id' => 'nullable|numeric',
            // 'grn_ageing_term_value' => 'nullable|numeric',
            // 'payment_type_id' => 'required|numeric',
            'grn_notes' => 'nullable|max:255',
            'pd.*.product_id' => 'nullable|numeric',
            'pd.*.product_barcode_id' => 'nullable|numeric',
            'pd.*.rate' => 'nullable|numeric',
            'pd.*.quantity' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        if(isset($request->pdsm)){
            foreach($request->pdsm as $expense){
                if(!empty($expense['account_code'])){
                    $exits = TblAccCoa::where('chart_account_id',$expense['account_id'])->where('chart_code',$expense['account_code'])->where(Utilities::currentBC())->exists();
                    if (!$exits) {
                        return $this->returnjsonerror(" Account Code not correct",201);
                    }
                }else{
                    return $this->returnjsonerror(" Enter Account Code",201);
                }
            }
        }

        /*if(!isset($id))
        {
            $grn_date = date('Y-m-d', strtotime($request->grn_date));

            if(TblPurcGrn::where('supplier_id','=',$request->supplier_id)
            ->where('grn_date','=',$grn_date)
            ->where('store_id','=',$request->grn_store)
            ->where('purchase_order_id','=',$request->purchase_order_id)
            ->where('grn_total_net_amount','=',$request->overall_net_amount)->exists())
            {
                return $this->jsonErrorResponse($data, 'Already Exist This GRN.', 422);
            }
        }*/

        if(isset($request->pd)){
            foreach($request->pd as $dtl){
                $purchase_order_id = isset($dtl['purchase_order_id'])?$dtl['purchase_order_id']:"";
                $product = $dtl['product_id'];
                $product_barcode = $dtl['product_barcode_id'];
                $uom_id = $dtl['uom_id'];
                if($purchase_order_id != ""){
                    $exist_barcode = false;
                    $purchase_order_barcodes = TblPurcPurchaseOrderDtl::where('purchase_order_id',$purchase_order_id)->where(Utilities::currentBCB())->get();
                    foreach ($purchase_order_barcodes as $barcode){
                        if($barcode['product_id'] == $product && $barcode['uom_id'] == $uom_id && $barcode['product_barcode_id'] == $product_barcode){
                            $exist_barcode = true;
                        }
                    }
                    if($exist_barcode == false){
                        return $this->jsonErrorResponse($data, trans('message.not_barcode'), 422);
                    }
                    $purchase_order_id = "";
                }else{
                    if(!ViewPurcProductBarcodeHelp::where('product_barcode_id','LIKE',$product_barcode)->where(Utilities::currentBC())->exists()){
                       // return $this->jsonErrorResponse($data, trans('message.not_product'), 422);
                    }
                }
            }
        }else{
            return $this->jsonErrorResponse($data, 'Fill The Grid', 200);
        }

        foreach($request->pd as $pd)
        {
            if($pd['sale_rate']  < $pd['net_tp'])
            {
                return $this->returnjsonerror("Sale Rate is less than Net TP",200);
            }
        }




        DB::beginTransaction();
        try{
            $sumOfProdTotalQty = 0;
            if(isset($request->pd)){
                foreach($request->pd as $dtl){
                    $prod_total_qty = (float)$dtl['quantity'];
                    // $prod_total_qty = (float)$dtl['quantity']+(float)$dtl['foc_qty'];
                    $sumOfProdTotalQty += $prod_total_qty;
                }
            }
            if(isset($id)){
                $grn = TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->first();
                $grn->update_by_user_id = Auth::id();
            }else{
                $grn = new TblPurcGrn();
                $grn->grn_id = Utilities::uuid();
                $grn->grn_type = 'GRN';
                $doc_data = [
                    'biz_type'          => 'branch',
                    'model'             => 'TblPurcGrn',
                    'code_field'        => 'grn_code',
                    'code_prefix'       => strtoupper('grn'),
                    'code_type_field'   => 'grn_type',
                    'code_type'         => strtoupper('grn'),
                ];
                $grn->grn_code = Utilities::documentCode($doc_data);
                $grn->create_by_user_id = Auth::id();
                $new = true;
            }



            $form_id = $grn->grn_id;
            $grn->supplier_id = $request->supplier_id;
            $grn->grn_date = date('Y-m-d', strtotime($request->grn_date));
            $grn->purchase_order_id = $request->purchase_order_id;
            $grn->grn_ageing_term_id = $request->grn_ageing_term_id;
            $grn->grn_ageing_term_value = $request->grn_ageing_term_value;
            $grn->currency_id = $request->grn_currency;
            $grn->grn_exchange_rate = $request->exchange_rate;
            $grn->payment_mode_id = $request->payment_mode_id;
            $grn->store_id = $request->grn_store;
            $grn->grn_bill_no = $request->grn_bill_no;
            // $grn->grn_receiving_date = date('Y-m-d', strtotime($request->grn_receiving_date));
            // $grn->grn_freight = $request->grn_freight;
            // $grn->grn_other_expense = $request->grn_other_expenses;

            $grn->grn_total_items = $request->summary_total_item;
            $grn->grn_total_qty = $request->summary_qty_wt;
            $grn->grn_total_amount = $request->summary_amount;
            $grn->grn_total_disc_amount = $request->summary_disc_amount;
            $grn->grn_total_gst_amount = $request->summary_gst_amount;
            $grn->grn_total_fed_amount = $request->summary_fed_amount;
            $grn->grn_total_spec_disc_amount = $request->summary_spec_disc_amount;
            $grn->grn_total_gross_net_amount = $request->summary_net_amount;
            $grn->grn_overall_discount = $request->overall_discount_perc;
            $grn->grn_overall_disc_amount = $request->overall_disc_amount;
            $grn->grn_advance_tax_perc = $request->overall_vat_perc;
            $grn->grn_advance_tax_amount = $request->overall_vat_amount;
            $grn->grn_total_net_amount = $request->overall_net_amount;

            $grn->grn_remarks = $request->grn_notes;
            $grn->business_id = auth()->user()->business_id;
            $grn->company_id = auth()->user()->company_id;
            $grn->branch_id = auth()->user()->branch_id;
            $grn->grn_user_id = auth()->user()->id;
            $grn->grn_device_id = 1;
            $grn->save();

            $net_total = 0;
            $amount_total = 0;
            $gst_amount_total = 0;
            $disc_amount_total = 0;
            $TotalExpAmount = 0;
            $total_gross_amount = 0;
            $net_amount = 0;
            $spec_disc_amount = 0;
            $fed_total_amount = 0;
            if(isset($id)){
                $del_Dtls = TblPurcGrnExpense::where('grn_id',$id)->where(Utilities::currentBCB())->get();
                foreach ($del_Dtls as $del_Dtls){
                    TblPurcGrnExpense::where('grn_expense_id',$del_Dtls->grn_expense_id)->where(Utilities::currentBCB())->delete();
                }
            }
            if(isset($request->pdsm)){
                foreach($request->pdsm as $expense){
                    if(isset($expense['expense_amount'])){
                        if($expense['expense_plus_minus'] != '+' && $expense['expense_plus_minus'] != '-'){
                            return $this->jsonErrorResponse(['error'=>'expense'], trans('message.required_fields'), 200);
                        }
                        if($expense['expense_dr_cr'] != 'dr' && $expense['expense_dr_cr'] != 'cr'){
                            return $this->jsonErrorResponse(['error'=>'expense'], trans('message.required_fields'), 200);
                        }
                        $expenseDtl = new TblPurcGrnExpense();
                        $expenseDtl->grn_expense_id = Utilities::uuid();
                        if(isset($id)){
                            $expenseDtl->grn_id = $id;
                        }else{
                            $expenseDtl->grn_id = $grn->grn_id;
                        }
                        $expenseDtl->chart_account_id = $expense['account_id'];
                        $expenseDtl->grn_expense_account_code = $expense['account_code'];
                        $expenseDtl->grn_expense_account_name = $expense['account_name'];
                        $expenseDtl->grn_expense_amount = $this->addNo($expense['expense_amount']);
                        $expenseDtl->grn_expense_perc = $this->addNo($expense['expense_perc']);
                        $expenseDtl->business_id = auth()->user()->business_id;
                        $expenseDtl->company_id = auth()->user()->company_id;
                        $expenseDtl->branch_id = auth()->user()->branch_id;
                        $expenseDtl->grn_expense_user_id = auth()->user()->id;
                        $expenseDtl->save();

                        if($expense['expense_plus_minus'] == '+'){
                            $net_total += $this->addNo($expense['expense_amount']);
                            $TotalExpAmount += $this->addNo($expense['expense_amount']);
                        }else{
                            $net_total -= $this->addNo($expense['expense_amount']);
                            $TotalExpAmount -= $this->addNo($expense['expense_amount']);
                        }

                    }
                }
            }

            $grn_dtls = TblPurcGrnDtl::where('grn_id',$grn->grn_id)->where(Utilities::currentBCB())->get();
            foreach($grn_dtls as $grn_dtl){
                TblPurcGrnDtl::where('purc_grn_dtl_id',$grn_dtl->purc_grn_dtl_id)->where(Utilities::currentBCB())->delete();
            }
            if(isset($request->pd)){
                $sr_no = 1;
                foreach($request->pd as $dtl){

                    if($dtl['gst_perc'] > 0){
                        $updateGst = TblPurcProductBarcodeDtl::checkBarcodeVatPercStatus($dtl['product_barcode_id'],$dtl['gst_perc']);
                        if($updateGst == false){
                            return $this->jsonErrorResponse($data, $dtl['pd_barcode']. ": gst not updated", 200);
                        }
                    }
                    $grnDtl = new TblPurcGrnDtl();
                    if(isset($id) && isset($account['purc_grn_dtl_id'])){
                        $grnDtl->grn_id = $id;
                        $grnDtl->purc_grn_dtl_id = $account['purc_grn_dtl_id'];
                    }else{
                        $grnDtl->purc_grn_dtl_id = Utilities::uuid();
                        $grnDtl->grn_id  = $grn->grn_id;
                    }
                    $grnDtl->grn_type = 'GRN';
                    $grnDtl->sr_no = $sr_no;
                    $sr_no = $sr_no+1;
                    $grnDtl->purchase_order_id = (isset($dtl['purchase_order_id']) && !empty($dtl['purchase_order_id']))?$dtl['purchase_order_id']:"";
                    // $grnDtl->supplier_id = $dtl['grn_supplier_id'];
                    $grnDtl->product_id = $dtl['product_id'];
                    $grnDtl->product_barcode_id = $dtl['product_barcode_id'];
                    $grnDtl->product_barcode_barcode = $dtl['pd_barcode'];
                    // $grnDtl->grn_dtl_po_rate = $this->addNo($dtl['grn_dtl_po_rate']);
                    $grnDtl->tbl_purc_grn_dtl_rate = Utilities::NumFormat($dtl['rate']);
                    $grnDtl->tbl_purc_grn_dtl_sale_rate = Utilities::NumFormat($dtl['sale_rate']);
                    $grnDtl->tbl_purc_grn_dtl_quantity = Utilities::NumFormat($dtl['quantity']);
                    $grnDtl->tbl_purc_grn_dtl_sys_quantity = Utilities::NumFormat($dtl['sys_qty']);
                    $grnDtl->tbl_purc_grn_dtl_mrp = Utilities::NumFormat($dtl['mrp']);
                    $grnDtl->tbl_purc_grn_dtl_amount = Utilities::NumFormat($dtl['cost_amount']);
                    $grnDtl->tbl_purc_grn_dtl_disc_percent = Utilities::NumFormat($dtl['dis_perc']);
                    $grnDtl->tbl_purc_grn_dtl_disc_amount = Utilities::NumFormat($dtl['dis_amount']);
                    $grnDtl->tbl_purc_grn_dtl_after_dis_amount = Utilities::NumFormat($dtl['after_dis_amount']);
                    $grnDtl->tbl_purc_grn_dtl_tax_on  = $dtl['pd_tax_on'];
                    $grnDtl->tbl_purc_grn_dtl_vat_percent = Utilities::NumFormat($dtl['gst_perc']);
                    $grnDtl->tbl_purc_grn_dtl_vat_amount = Utilities::NumFormat($dtl['gst_amount']);
                    $grnDtl->tbl_purc_grn_dtl_fed_percent = Utilities::NumFormat($dtl['fed_perc']);
                    $grnDtl->tbl_purc_grn_dtl_fed_amount = Utilities::NumFormat($dtl['fed_amount']);
                    $grnDtl->tbl_purc_grn_dtl_disc_on  = $dtl['pd_disc'];
                    $grnDtl->tbl_purc_grn_dtl_spec_disc_perc = Utilities::NumFormat($dtl['spec_disc_perc']);
                    $grnDtl->tbl_purc_grn_dtl_spec_disc_amount = Utilities::NumFormat($dtl['spec_disc_amount']);

                    $grnDtl->tbl_purc_grn_dtl_gross_amount = Utilities::NumFormat($dtl['gross_amount']);
                    $grnDtl->tbl_purc_grn_dtl_total_amount = Utilities::NumFormat($dtl['net_amount']);

                    $grnDtl->tbl_purc_grn_dtl_net_tp = Utilities::NumFormat($dtl['net_tp']);
                    $grnDtl->tbl_purc_grn_dtl_last_tp = Utilities::NumFormat($dtl['last_tp']);
                    $grnDtl->tbl_purc_grn_dtl_vend_last_tp = Utilities::NumFormat($dtl['vend_last_tp']);
                    $grnDtl->tbl_purc_grn_dtl_tp_diff = Utilities::NumFormat($dtl['tp_diff']);
                    $grnDtl->tbl_purc_grn_dtl_gp_perc = Utilities::NumFormat($dtl['gp_perc']);
                    $grnDtl->tbl_purc_grn_dtl_gp_amount = Utilities::NumFormat($dtl['gp_amount']);
                    $grnDtl->tbl_purc_grn_dtl_fc_rate = Utilities::NumFormat($dtl['fc_rate']);
                    $grnDtl->tbl_purc_grn_dtl_remarks = $dtl['remarks'];
                  //  $grnDtl->uom_id  = $dtl['uom_id'];
                  //  $grnDtl->tbl_purc_grn_dtl_packing = $dtl['pd_packing'];
                    $grnDtl->purchase_order_id = $dtl['po_id'];
                    if(!empty($dtl['po_id'])){
                        TblPurcPurchaseOrder::where('purchase_order_id',$dtl['po_id'])->update(['po_grn_status'=>'completed']);
                    }
                    $grnDtl->po_net_tp = Utilities::NumFormat($dtl['po_net_tp']);
                    $grnDtl->business_id = auth()->user()->business_id;
                    $grnDtl->company_id = auth()->user()->company_id;
                    $grnDtl->branch_id = auth()->user()->branch_id;
                    $grnDtl->tbl_purc_grn_dtl_user_id = auth()->user()->id;

                    //$grnDtl->qty_base_unit = (isset($dtl['pd_packing'])?$dtl['pd_packing']:'0') * ((isset($dtl['quantity'])?$dtl['quantity']:'0'));
                    $grnDtl->qty_base_unit = Utilities::NumFormat($dtl['quantity']);
                    // $grnDtl->tbl_purc_grn_dtl_supplier_barcode = $dtl['grn_supplier_barcode'];
                    // $grnDtl->tbl_purc_grn_dtl_foc_quantity = $dtl['foc_qty'];
                    // $grnDtl->tbl_purc_grn_dtl_batch_no = $dtl['batch_no'];
                    // $grnDtl->tbl_purc_grn_dtl_production_date = date('Y-m-d', strtotime($dtl['production_date']));
                    // $grnDtl->tbl_purc_grn_dtl_expiry_date = date('Y-m-d', strtotime($dtl['expiry_date']));

                    // calculations
                    // $prod_total_qty = (float)$dtl['quantity']+(float)$dtl['foc_qty'];
                    $prod_total_qty = Utilities::NumFormat($dtl['quantity']);
                    $prod_gross_amount = Utilities::NumFormat($dtl['cost_amount']) - Utilities::NumFormat($dtl['dis_amount']);
                    $prod_gross_rate = ($prod_total_qty == 0) ? 0 : $prod_gross_amount/$prod_total_qty;
                    $prod_rate_expense = $TotalExpAmount/$sumOfProdTotalQty;
                    $prod_net_rate = ($prod_rate_expense+$prod_gross_rate);
                    $grnDtl->dtl_prod_total_qty = $prod_total_qty;
                    $grnDtl->dtl_prod_gross_amount = $prod_gross_amount;
                    $grnDtl->dtl_prod_gross_rate = $prod_gross_rate;
                    $grnDtl->dtl_prod_rate_expense = $prod_rate_expense;
                    $grnDtl->dtl_prod_net_rate = $prod_net_rate;
                    
                    if(!empty($request->supplier_id) && !empty($dtl['product_id'])){
                        $TblPurcProductFOCExists = TblPurcProductFOC::where('supplier_id',$request->supplier_id)->where('product_id',$dtl['product_id'])->exists();
                        if(!$TblPurcProductFOCExists){
                            $pdo = DB::getPdo();
                            $supplier_id = $request->supplier_id;
                            $business_id = auth()->user()->business_id;
                            $company_id = auth()->user()->company_id;
                            $branch_id = auth()->user()->branch_id;
                                                                //dd($prod_net_rate);
                            $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_SUP_BATCH_INSERT(:p1, :p2, :p3, :p4, :p5, :p6); end;");
                            $stmt->bindParam(':p1', $dtl['product_id']);
                            $stmt->bindParam(':p2', $supplier_id);
                            $stmt->bindParam(':p3', $dtl['grn_supplier_barcode']);
                            $stmt->bindParam(':p4', $business_id);
                            $stmt->bindParam(':p5', $company_id);
                            $stmt->bindParam(':p6', $branch_id);
                            $stmt->execute();
                        }
                    }

                    
                    if(!empty($request->supplier_id) && !empty($dtl['product_id']))
                    {
                        $pdo = DB::getPdo();
                        $supplier_id = $request->supplier_id;
                        $branch_id = auth()->user()->branch_id;

                        $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_PURC_PRODUCT_SUP_INSERT(:p1, :p2, :p3); end;");
                        $stmt->bindParam(':p1', $dtl['product_id']);
                        $stmt->bindParam(':p2', $supplier_id);
                        $stmt->bindParam(':p3', $branch_id);
                        $stmt->execute();
                    }

                    // if($dtl['foc_qty'] > 0){
                    //     $amount = Utilities::NumFormat($dtl['amount']);
                    //     $quantity = Utilities::NumFormat($dtl['quantity']);
                    //     $foc_qty = Utilities::NumFormat($dtl['foc_qty']);
                    //     $barcode_packing = $barcode->product_barcode_packing;
                    //     $rate_inc_foc = ((float)$amount / ((float) $quantity + (float) $foc_qty )) / (float)$barcode_packing;
                    // }else{
                    //     $rate = Utilities::NumFormat($dtl['rate']);
                    //     $rate_inc_foc = (float)$rate/(float)$barcode->product_barcode_packing;
                    // }
                    // $grnDtl->tbl_purc_grn_dtl_rate_inc_foc = $rate_inc_foc;
                    $barcode = TblPurcProductBarcode::where('product_barcode_id',$dtl['product_barcode_id'])
                        ->where('product_id',$dtl['product_id'])->first();
                    if(empty($barcode)){
                        return $this->jsonErrorResponse($data,$dtl['pd_barcode']." barcode not fount",200);
                    }
                    $grnDtl->uom_id = $barcode->uom_id;
                    $grnDtl->tbl_purc_grn_dtl_packing = $barcode->product_barcode_packing;

                    $grnDtl->save();
                    $net_total += Utilities::NumFormat($dtl['gross_amount']);
                    $total_gross_amount += Utilities::NumFormat($dtl['gross_amount']);
                    $amount_total += Utilities::NumFormat($dtl['cost_amount']);
                    $gst_amount_total += Utilities::NumFormat($dtl['gst_amount']);
                    $disc_amount_total += Utilities::NumFormat($dtl['dis_amount']);
                    $net_amount += Utilities::NumFormat($dtl['net_amount']);
                    $spec_disc_amount += Utilities::NumFormat($dtl['spec_disc_amount']);
                    $fed_total_amount += Utilities::NumFormat($dtl['fed_amount']);

                    $product = TblPurcProduct::where('product_id',$dtl['product_id'])->first();
                    $product->supplier_id = $request->supplier_id;
                    $product->update_id = Utilities::uuid();
                    $product->save();

                    /* create supplier attach with product -- 23-feb-2023
                    $foc = TblPurcProductFOC::where('product_id',$dtl['product_id'])->where('branch_id',auth()->user()->branch_id)->first();
                    if(empty($foc)){
                        TblPurcProductFOC::create([
                            'product_foc_id' => Utilities::uuid(),
                            'product_id' => $dtl['product_id'],
                            'supplier_id' => $request->supplier_id,
                            'branch_id' => auth()->user()->branch_id,
                        ]);
                    }else{
                        $foc->supplier_id = $request->supplier_id;
                        $foc->save();
                    } */
                    
                    $get_branch_central_rate = TblSoftBranch::where('branch_id',auth()->user()->branch_id)->first();
                    $get_warranty = TblPurcProduct::where('product_id',$dtl['product_id'])->first();
                    $all_branches = false;
                    $central_rate = true;
                    if(auth()->user()->central_rate == 0 && $get_warranty->product_warranty_status == 1)
                    {
                        $all_branches = false;
                        $central_rate = true;
                    }
                    if(auth()->user()->central_rate == 1 && $get_warranty->product_warranty_status == 1)
                    {
                        $all_branches = true;
                        $central_rate = true;
                    }
                    if((auth()->user()->central_rate == 0 || auth()->user()->central_rate == "") && 
                        ($get_warranty->product_warranty_status == 0 || $get_warranty->product_warranty_status == "")
                    ){
                        $all_branches = false;
                        $central_rate = true;
                    }
                    /*if($get_branch_central_rate->branch_size == 1 && auth()->user()->central_rate == 0)
                    {
                        $all_branches = false;
                        $central_rate = false;
                    }*/

                    if($all_branches){
                        $branch_ids = TblSoftBranch::where('branch_size', 1)->pluck('branch_id')->toArray();
                    }else{
                        $branch_ids[] = auth()->user()->branch_id;
                    }
                    
                    $rateUpdate = false;
                    $now = new \DateTime("now");
                    $todayFormat = $now->format("d-m-Y"); //for blade template
                    $previous = $now->modify('-6 days');
                    $previousDate = $previous->format("d-m-Y"); //for blade template
                    $grnDate = date('d-m-Y', strtotime($request->grn_date));
                    if(strtotime($todayFormat) >= strtotime($grnDate) && strtotime($previousDate) <= strtotime($grnDate)){
                        $rateUpdate = true;
                    }
                    
                    if($rateUpdate)
                    {
                        $barcodeList = TblPurcProductBarcode::where('product_id',$dtl['product_id'])->get();
                        foreach ($barcodeList as $item)
                        {
                            foreach($branch_ids as $branch_id)
                            {
                                $bpr = TblPurcProductBarcodePurchRate::where('product_barcode_id',$item->product_barcode_id)
                                    ->where('product_id',$item->product_id)
                                    ->where('branch_id',$branch_id)->first();
  
                                if(!empty($bpr)){
                                    $old_sale_rate = $bpr->sale_rate;
                                    $old_net_tp = $bpr->net_tp;
                                    $old_updated_at = $bpr->updated_at;

                                    $last_tp = $bpr->net_tp;

                                    $bpr->product_barcode_cost_rate = Utilities::NumFormat($dtl['rate']);
                                    if($central_rate)
                                    {
                                        $bpr->sale_rate = Utilities::NumFormat($dtl['sale_rate']);
                                    }
                                    $bpr->net_tp = Utilities::NumFormat($dtl['net_tp']);
                                    $bpr->mrp = Utilities::NumFormat($dtl['mrp']);
                                    $bpr->last_tp = Utilities::NumFormat($last_tp);
                                    $bpr->supplier_last_tp = Utilities::NumFormat($dtl['vend_last_tp']);
                                    $bpr->last_gst_perc = Utilities::NumFormat($dtl['gst_perc']);
                                    $bpr->last_disc_perc = Utilities::NumFormat($dtl['dis_perc']);
                                    $bpr->pd_tax_on = $dtl['pd_tax_on'];
                                    $bpr->pd_disc = $dtl['pd_disc'];

                                    $bpr->save();

                                }else{
                                    $bpr = TblPurcProductBarcodePurchRate::create([
                                        'product_barcode_purch_id' => Utilities::uuid(),
                                        'product_id' => $item->product_id,
                                        'product_barcode_id' => $item->product_barcode_id,
                                        'product_barcode_barcode' => $item->product_barcode_barcode,
                                        'product_barcode_cost_rate' => Utilities::NumFormat($dtl['rate']),
                                        'sale_rate' => Utilities::NumFormat($dtl['sale_rate']),
                                        'net_tp' => Utilities::NumFormat($dtl['net_tp']),
                                        'mrp' => Utilities::NumFormat($dtl['mrp']),
                                        'last_tp' => Utilities::NumFormat($dtl['net_tp']),
                                        'supplier_last_tp' => Utilities::NumFormat($dtl['vend_last_tp']),
                                        'last_gst_perc' => Utilities::NumFormat($dtl['gst_perc']),
                                        'last_disc_perc' => Utilities::NumFormat($dtl['dis_perc']),
                                        'pd_tax_on' => $dtl['pd_tax_on'],
                                        'pd_disc' => $dtl['pd_disc'],
                                        'business_id' => auth()->user()->business_id,
                                        'company_id' => auth()->user()->company_id,
                                        'branch_id' => $branch_id,
                                    ]);
                                    $old_sale_rate = "";
                                    $old_net_tp = "";
                                    $old_updated_at = "";
                                }


                                /* start=> add product log */
                                $req = [
                                    "document_id" => $grn->grn_id,
                                    "product_barcode_purch_id" => $bpr->product_barcode_purch_id,
                                    "product_id" => $item->product_id,
                                    "product_barcode_id" => $item->product_barcode_id,
                                    "product_barcode_barcode" => $item->product_barcode_barcode,
                                    "product_barcode_cost_rate" => Utilities::NumFormat($dtl['rate']),
                                    "sale_rate" => Utilities::NumFormat($dtl['sale_rate']),
                                    "mrp" => Utilities::NumFormat($dtl['mrp']),
                                    "supplier_last_tp" => Utilities::NumFormat($dtl['vend_last_tp']),
                                    "last_gst_perc" => Utilities::NumFormat($dtl['gst_perc']),
                                    "last_disc_perc" => Utilities::NumFormat($dtl['dis_perc']),
                                    "business_id" => auth()->user()->business_id,
                                    "company_id" => auth()->user()->company_id,
                                    "branch_id" => $branch_id,
                                    "user_id" => auth()->user()->id,
                                    "old_sale_rate" => $old_sale_rate,
                                    "old_net_tp" => $old_net_tp,
                                    "old_created_date" => date('Y-m-d H:i:s', strtotime($old_updated_at)),
                                    "activity_form_type" => "grn",
                                    "activity_form_action" => isset($new)?"create":"update",
                                ];

                            // dd($req);
                                $logRate = new ProductCardController();
                                $return = $logRate->storeRateLog($req);
                                if(!isset($return->original['status']) && $return->original['status'] != 'success'){
                                    return $this->jsonErrorResponse($data, "Rate log not update...", 200);
                                }
                                /* end=> add product log */
                            }
                        }
                    }
                }
            }

            $table_name = 'tbl_acco_voucher';
            if(isset($id)){
                $action = 'update';
                $grn_id = $id;
                $grn = TblPurcGrn::where('grn_id',$grn_id)->where(Utilities::currentBCB())->first();
                if(!empty($grn->voucher_id)){
                    $voucher_id = $grn->voucher_id;
                }else{
                    $voucher_id = Utilities::uuid();
                }
            }else{
                $action = 'add';
                $grn_id = $grn->grn_id;
                $voucher_id = Utilities::uuid();
            }
            $supplier = TblPurcSupplier::where('supplier_id',$request->supplier_id)->where(Utilities::currentBC())->first();

            $supplier_ca_id = (int)$supplier->supplier_account_id;

            $gst_ca_id = ''; // '3-01-05-0009';
            $amount_ca_id = ''; // '6-01-01-0001';
            $fed_ca_id = ''; //  '6-01-01-0001';
            $adv_tax_ca_id = ''; // '6-01-10-0001';
            $round_of_amt_ca_id = ''; // '7-01-01-0003';
            $discount_ca_id = ''; // '7-01-02-0002';
            $spec_disc_ca_id = ''; // '7-01-02-0002';
            $order_disc_ca_id = ''; // '7-01-02-0002';

            $allChartAcc = TblAccCoa::whereIn('chart_code',['3-01-05-0009','6-01-01-0001','6-01-10-0001','7-01-01-0003','7-01-02-0002'])->select('chart_account_id','chart_code','chart_name')->get();
            foreach ($allChartAcc as $oneChartAcc){
                if($oneChartAcc->chart_code == '3-01-05-0009'){
                    $gst_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '6-01-01-0001'){
                    $amount_ca_id = $oneChartAcc->chart_account_id;
                    $fed_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '6-01-10-0001'){
                    $adv_tax_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '7-01-01-0003'){
                    $round_of_amt_ca_id = $oneChartAcc->chart_account_id;
                }
                if($oneChartAcc->chart_code == '7-01-02-0002'){
                    $discount_ca_id = $oneChartAcc->chart_account_id;
                    $spec_disc_ca_id = $oneChartAcc->chart_account_id;
                    $order_disc_ca_id = $oneChartAcc->chart_account_id;
                }
            }
            $ChartArr = [
                $supplier_ca_id,
                $amount_ca_id,
                $discount_ca_id,
                $spec_disc_ca_id,
                $order_disc_ca_id,
                $gst_ca_id,
                $adv_tax_ca_id,
                $fed_ca_id,
                $round_of_amt_ca_id,
            ];
            $response = $this->ValidateCharAccCodeIds($ChartArr);
            if(isset($response['error']) && empty($response['error'])){
                return $this->jsonErrorResponse($data,"Account Code not correct",200);
            }

            //voucher start
            $data = [
                'voucher_id'            =>  $voucher_id,
                'voucher_document_id'   =>  $grn_id,
                'voucher_no'            =>  $grn->grn_code,
                'voucher_date'          =>  date('Y-m-d', strtotime($request->grn_date)),
                'voucher_descrip'       =>  'Purchase: '.$grn->grn_remarks .' - Ref:'.$request->grn_bill_no,
                'voucher_type'          =>  'GRN',
                'voucher_posted'         =>  1,
                'branch_id'             =>  auth()->user()->branch_id,
                'business_id'           =>  auth()->user()->business_id,
                'company_id'            =>  auth()->user()->company_id,
                'voucher_user_id'       =>  auth()->user()->id,
            ];
            $overall_net_amount = $request->overall_net_amount;
            $voucher_sr_no = 1;
            $data['chart_account_id'] = $supplier_ca_id;
            $data['voucher_posted'] = 1;
            $data['voucher_debit'] = 0;
            $data['voucher_credit'] = abs($overall_net_amount);
            $data['voucher_sr_no'] = $voucher_sr_no++;
            $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);

            $action = 'add';
            if(!empty($disc_amount_total)){
                $data['chart_account_id'] = $discount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($disc_amount_total);
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Discount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($spec_disc_amount)){
                $data['chart_account_id'] = $discount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($spec_disc_amount);
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Spec Discount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($request->overall_disc_amount)){
                $data['chart_account_id'] = $discount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_debit'] = 0;
                $data['voucher_credit'] = abs($request->overall_disc_amount);
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Order Discount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($amount_total)){
                $data['chart_account_id'] = $amount_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_debit'] = abs($amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($gst_amount_total)){
                $data['chart_account_id'] = $gst_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_debit'] = abs($gst_amount_total);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'GST Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($request->overall_vat_amount)){
                $data['chart_account_id'] = $adv_tax_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_debit'] = abs($request->overall_vat_amount);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Adv Tax Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            if(!empty($fed_total_amount)){
                $data['chart_account_id'] = $fed_ca_id;
                $data['voucher_posted'] = 1;
                $data['voucher_debit'] = abs($fed_total_amount);
                $data['voucher_credit'] = 0;
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'FED Amount';
                $this->proAccoVoucherInsert($voucher_id,$action,$table_name,$data);
            }
            $debit_amount = abs($amount_total) + abs($gst_amount_total) + abs($request->overall_vat_amount) + abs($fed_total_amount);
            $credit_amount = abs($overall_net_amount) + abs($disc_amount_total) + abs($spec_disc_amount) + abs($request->overall_disc_amount);

            $round_of_amt = number_format($debit_amount,3,'.','') - number_format($credit_amount,3,'.','');
            $round_of_amt = number_format($round_of_amt,3,'.','');

            if(!empty($round_of_amt)) {

                $data['chart_account_id'] = $round_of_amt_ca_id;
                $data['voucher_posted'] = 1;
                if ($round_of_amt < 0) {
                    $data['voucher_debit'] = abs($round_of_amt);
                    $data['voucher_credit'] = 0;
                } else {
                    $data['voucher_debit'] = 0;
                    $data['voucher_credit'] = abs($round_of_amt);
                }
                $data['voucher_sr_no'] = $voucher_sr_no++;
                $data['voucher_descrip'] = 'Round of Amount';
                $this->proAccoVoucherInsert($voucher_id, $action, $table_name, $data);
            }

            $grnVou = TblPurcGrn::where('grn_id',$grn_id)->first();
            $grnVou->voucher_id = $voucher_id;
            $grnVou->save();

            // end insert update grn voucher

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
            return $this->jsonErrorResponse($data, $e->getLine().' : '.$e->getMessage(), 200);
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


    public function print(Request $request, $id)
    {
        // dd($request->toArray());
        $data['title'] = 'Goods Received Note';
        $data['type'] = $request->type;
        $data['id'] = $id;
        $data['permission'] = self::$menu_dtl_id.'-print';
        $url = '/grn/print/'.$id;
        $data['print_link'] = $url;
        // dd($url);
        if(isset($id)){
            if(TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcGrn::with('grn_dtl','supplier','PO','grn_expense')->where('grn_id',$id)->where(Utilities::currentBCB())->first();
            }else{
                abort('404');
            }
        }
        $data['currency'] = TblDefiCurrency::where('currency_id',$data['current']->currency_id)->where(Utilities::currentBC())->first();
        $data['store'] = TblDefiStore::where('store_id',$data['current']->store_id)->where(Utilities::currentBCB())->first();
        $data['payment_terms'] = TblAccoPaymentTerm::where('payment_term_id',$data['current']->grn_ageing_term_id)->where('payment_term_entry_status',1)->where(Utilities::currentBCB())->first();

        if(isset($type) && $type=='pdf'){
            $view = view('prints.grn_print', compact('data'))->render();
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
                return view('prints.purchase.grn.purchase_invoice', compact('data'));
            }elseif($data['type'] == '2'){
                return view('prints.purchase.grn.ex_purchase_invoice', compact('data'));
            }elseif($data['type'] == '3'){
                return view('prints.purchase.grn.purchase_invoice_uk', compact('data'));
            }elseif($data['type'] == '4'){
                return view('prints.purchase.grn.purchase_invoice_landscape', compact('data'));
            }elseif($data['type'] == '5'){
                return view('prints.purchase.grn.stock_direct_delivery', compact('data'));
            }else{
            return view('prints.purchase.grn.grn_print', compact('data'));
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
            $grn = TblPurcGrn::where('grn_id',$id)->where('grn_type','GRN')->where(Utilities::currentBCB())->first();
            $voucher_id = $grn->voucher_id;
            $po_id = $grn->purchase_order_id;

            if(!empty($voucher_id)){
                $this->proAccoVoucherDelete($voucher_id);
            }
            if(!empty($po_id)){
                TblPurcPurchaseOrder::where('purchase_order_id',$po_id)->update(['po_grn_status'=>'pending']);
            }

            $grn->grn_dtl()->delete();
            $grn->delete();

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


    public function shelfBarcodePriceTag(Request $request){
        $barcode_ids = $request->data;
        $data = [];
        foreach ($barcode_ids as $barcode){
            $ba = TblPurcProductBarcode::with('product','cb_sale_rate','barcode_dtl_branch')->where('product_barcode_id',$barcode['barcode_id'])->first()->toArray();
            // Check If the gst is Apply on this Product
            if(isset($ba['barcode_dtl_branch'][0]['product_barcode_tax_apply']) && $ba['barcode_dtl_branch'][0]['product_barcode_tax_apply'] == 1){
               $gst =  ($ba['cb_sale_rate']['product_barcode_sale_rate_rate'] / 100) * $ba['barcode_dtl_branch'][0]['product_barcode_tax_value'];
               $gst = number_format($gst , 3);
               $rate = 1 *  $ba['cb_sale_rate']['product_barcode_sale_rate_rate'] + $gst;
            }else{
                $rate = $ba['cb_sale_rate']['product_barcode_sale_rate_rate'];
            }

            $data[] = [
                'barcode' => $ba['product_barcode_barcode'],
                'rate' => $rate,
                'name' => $ba['product']['product_name'],
                'qty' => 1,
                'arabic_name' => $ba['product']['product_arabic_name'],
            ];
        }
        session(['dataShelfBarcodeTags'=>$data]);
        return response()->json(['status'=>'success']);
    }
    public function barcodePriceTag2(Request $request,$id){
        if(isset($id)){
            if(TblPurcGrn::where('grn_id',$id)->where(Utilities::currentBCB())->exists()){
                $data['current'] = TblPurcGrn::with('grn_dtl_smpl_data')->where('grn_id',$id)
                    ->select('grn_id')->first();
            }else{
                abort('404');
            }
        }

        return view('purchase.grn.barcode_with_price',compact('data'));
    }

    public function barcodePriceTag(Request $request){
        $barcode_ids = $request->data;

        session(['dataBarcodeTags'=>$barcode_ids]);
        return response()->json(['status'=>'success']);
    }

    public function barcodeGRNTag(Request $request){
        $grn_barcode_id = $request->data;
        // dd($grn_barcode_id);

        session(['dataGrnTags'=>$grn_barcode_id]);
        return response()->json(['status'=>'success']);
    }

    public function UpdateProductPrice(Request $request){
        $grn_update_peice = $request->data;
        // dd($grn_update_peice);

        session(['UpdatePrice'=>$grn_update_peice]);
        return response()->json(['status'=>'success']);
    }

    public function barcodePriceTagView(){
        $data['barcodes'] = session('dataBarcodeTags');
        if(empty($data['barcodes'])){
            abort('404');
        }
        return view('purchase.grn.price_tags',compact('data'));
    }

    public function shelfBarcodePriceTagView(){
        $data['barcodes'] = session('dataShelfBarcodeTags');
        if(empty($data['barcodes'])){
            abort('404');
        }
        return view('purchase.grn.shelf_price_tags',compact('data'));
    }

    public function barcodeSalePrice(Request $request)
    {
        $dataBarcode = $request->data;
        $data = [];
        foreach ($dataBarcode as $key => $databar) {

            array_push($data, TblPurcProductBarcodeSaleRate::where('product_barcode_id', $databar)->where('branch_id', auth()->user()->branch_id)->where('product_category_id', 2)->first());
        }

        return response()->json(['product_barcode' => $data, 'status' => 'success']);

    }
}
