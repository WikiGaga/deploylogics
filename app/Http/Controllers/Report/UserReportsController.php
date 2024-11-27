<?php

namespace App\Http\Controllers\Report;

set_time_limit(300);

use App\Http\Controllers\Api\WhatsApp\WhatsAppApiController;
use PDF;
use Excel;
use DateTime;
use Exception;
use Validator;
use Dompdf\Dompdf;
use App\Models\User;
use App\Library\CoreFunc;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use App\Models\ViewPurcGRN;
use App\Models\TblDefiStore;
use Illuminate\Http\Request;
use App\Models\TblPurcGrnDtl;
use App\Models\TblSoftBranch;
use App\Models\ViewInveStock;
use App\Models\TblPurcProduct;
use App\Models\TblSoftReports;
use App\Models\TblDefiCurrency;
use App\Models\TblPurcSupplier;
use App\Models\TblSaleCustomer;
use App\Models\TblSaleSalesDtl;
use Illuminate\Validation\Rule;
use App\Models\TblVerifyReports;
use App\Models\TblSoftFilterType;
use App\Models\ViewPurcGroupItem;
use Illuminate\Support\Facades\DB;
use App\Models\TblPurcSupplierType;
// db and Validator
use App\Models\TblSaleCustomerType;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\ViewSaleSalesInvoice;
use Illuminate\Support\Facades\Auth;
use App\Models\TblPurcProductBarcode;
use App\Models\ViewPurcPurchaseOrder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use App\Models\ViewAccoChartAccountHelp;
use App\Models\TblSoftReportStaticCriteria;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Sales\CustomerController;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserReportsController extends Controller
{
    public static $page_title = 'Reporting';
    //public static $redirect_url = 'user-report';

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
    public function create($reportType,$caseType, $id = null)
    {   // TblSoftReportingUserStudio
        $menu = TblSoftReports::select('parent_menu_id')->where('report_case',$caseType)->first();
        if(empty($menu)){
            abort('404');
        }
        if($menu->parent_menu_id == 70){
           $type = 'sale';
        }
        if($menu->parent_menu_id == 75){
            $type = 'inventory';
        }
        if($menu->parent_menu_id == 15){
            $type = 'accounts';
        }
        if($menu->parent_menu_id == 49){
            $type = 'purchase';
        }
        $data['page_data'] = [];
        $data['report_type'] = $reportType;
        $data['case_name'] = $caseType;
        $data['page_data']['title'] = self::$page_title;
        //$data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['path_index'] = '/reports/report-list/'.$type;
        if(isset($id)){

        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['page_data']['action'] = 'Generate';
        }
        $data['report'] = TblSoftReports::with('report_user_criteria')
            ->where('report_case',$caseType)
            ->where('report_entry_status',1)
            ->where(Utilities::currentBC())->first();

        $selected_criteria = explode(",",$data['report']->report_static_criteria);
        $static_criteria = TblSoftReportStaticCriteria::where('report_static_criteria_entry_status',1)->get();
        $collect_static_criteria =  collect($static_criteria);
        $data['selected_criteria'] = [];
        for($i=0; $i<count($selected_criteria); $i++){
            $search = $collect_static_criteria->where('report_static_criteria_id',$selected_criteria[$i])->first();
            if($search){
                array_push($data['selected_criteria'],$search->report_static_criteria_case);
            }
        }
        $now = new \DateTime("now");
        $data['date_from'] =  $now; //$now->modify('-6 months');
        $data['date_to'] = new \DateTime("now");
        $data['branches'] = Utilities::getAllBranches();//TblSoftBranch::where(Utilities::currentBC())->where('branch_active_status',1)->get();
        $data['customers'] = TblSaleCustomer::get();
        if($reportType == 'static'){
            return view('reports.report_static_create', compact('data'));
        } elseif($reportType == 'dynamic'){
            return view('reports.report_static_create', compact('data'));
        }else{
            abort('404');
        }
    }

    public function getStoreByName(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $branch_id = Auth::user()->branch_id;
        $BCB = "business_id = $business_id AND company_id = $company_id AND branch_id = $branch_id";
        $data['items'] = DB::select("select store_name as id, store_name
                         from tbl_defi_store where $BCB
                         AND (lower(store_name) like '%".strtolower($val)."%')
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
                            from tbl_defi_store where $BCB
                            AND (lower(store_name) like '%".strtolower($val)."%') ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    public function getDisplayLocationNameStringByName(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $branch_id = Auth::user()->branch_id;
        $BCB = "branch_id = $branch_id";
        $data['items'] = DB::select("select display_location_name_string as id, display_location_name_string
                         from vw_inve_display_location where $BCB
                         AND (lower(display_location_name_string) like '%".strtolower($val)."%')
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
                            from vw_inve_display_location where $BCB
                            AND (lower(display_location_name_string) like '%".strtolower($val)."%') ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    public function getColumnConditions(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'col_type' => 'required',
        ]);
        session()->forget('data');
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try{
            $col_type = $request->col_type;

            if($col_type == 'float'){
                $col_type = 'number';
            }

            $data['col_type'] = $col_type;

            $data['conditions'] = TblSoftFilterType::where('filter_type_data_type_name',$col_type)->where('filter_type_entry_status',1)->get();

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

        return $this->jsonSuccessResponse($data, '', 200);
    }
    public function getSupplierByName(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $branch_id = Auth::user()->branch_id;
        $BCB = "business_id = $business_id AND company_id = $company_id AND branch_id = $branch_id";
        $data['items'] = DB::select("select supplier_name as id, supplier_name
                         from tbl_purc_supplier where $BCB
                         AND (lower(supplier_name) like '%".strtolower($val)."%')
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
                            from tbl_purc_supplier where $BCB
                            AND (lower(supplier_name) like '%".strtolower($val)."%') ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    public function getproductTypeByName(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $branch_id = Auth::user()->branch_id;
        $BC = "business_id = $business_id AND company_id = $company_id ";
        $data['items'] = DB::select("select product_type_name as id, product_type_name
                         from tbl_purc_product_type where $BC
                         AND (lower(product_type_name) like '%".strtolower($val)."%')
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
                            from tbl_purc_product_type where $BC
                            AND (lower(product_type_name) like '%".strtolower($val)."%') ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    public function getCustomerByName(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $branch_id = Auth::user()->branch_id;
        $BC = "business_id = $business_id AND company_id = $company_id ";
        $data['items'] = DB::select("select customer_name as id, customer_name
                         from tbl_sale_customer where $BC
                         AND (lower(customer_name) like '%".strtolower($val)."%')
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
                            from tbl_sale_customer  where $BC
                            AND (lower(customer_name) like '%".strtolower($val)."%') ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    public function getProductByName(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        if($caseName == 'sale_invoice'){
            $data['items'] = DB::select("select v.product_id,v.product_name from (
                    select distinct product_id,product_name from vw_sale_sales_invoice union all
                    select distinct product_id,product_name from vw_inve_stock ) v
                    where lower(v.product_name) like '%".strtolower($val)."%'");
        }
        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $BC = "p.business_id = $business_id AND p.company_id = $company_id";
        $p_str = strtoupper($val);
        $p_str = str_replace('%2F','/',$p_str);
        $p_str = str_replace('%22','"',$p_str);
        $p_str = str_replace('%2C',',',$p_str);
        $p_str = str_replace("'","''",$p_str);
        $replaced_str = str_replace(' ', '%', trim($p_str));
        $replaced_str = str_replace('%20', '%', trim($replaced_str));
        $data['items'] = DB::select("SELECT 
            p.PRODUCT_NAME AS id,
            p.PRODUCT_NAME,
            MAX(b.PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
            MAX(b.UOM_NAME) UOM_NAME,
            MAX(b.PRODUCT_BARCODE_PACKING) AS packing 
        FROM
            TBL_PURC_PRODUCT p,
            TBL_PURC_PRODUCT_BARCODE b 
        WHERE p.PRODUCT_ID = b.PRODUCT_ID 
            AND (  
                upper(b.product_barcode_barcode) Like '%".$replaced_str."%' 
                OR upper(p.product_name) like '%".$replaced_str."%' 
            )
            AND $BC
        GROUP BY p.PRODUCT_ID,
            p.PRODUCT_NAME,
            p.PRODUCT_ARABIC_NAME,
            p.GROUP_ITEM_ID,
            p.GROUP_ITEM_PARENT_ID  
        ORDER BY 
            Case
                WHEN upper(MAX(b.product_barcode_barcode)) Like '".$replaced_str."' THEN 1
                WHEN upper(MAX(p.product_name)) Like '".$replaced_str."%' THEN 2
                WHEN upper(MAX(p.product_name)) Like '%".$replaced_str."' THEN 4
                Else 3 
            END,
            p.product_name
            OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
            from TBL_PURC_PRODUCT p, TBL_PURC_PRODUCT_BARCODE b where p.PRODUCT_ID = b.PRODUCT_ID AND $BC
            AND (lower(p.product_name) like '%".strtolower($val)."%'  OR lower(b.PRODUCT_BARCODE_BARCODE) like '%".strtolower($val)."%') ");

        /*
        $data['items'] = DB::select("select p.PRODUCT_NAME as id, p.PRODUCT_NAME, b.PRODUCT_BARCODE_BARCODE,b.UOM_NAME,b.PRODUCT_BARCODE_PACKING as packing
                            from TBL_PURC_PRODUCT p, TBL_PURC_PRODUCT_BARCODE b where p.PRODUCT_ID = b.PRODUCT_ID AND $BC
                            AND (lower(p.product_name) like '%".strtolower($val)."%' OR lower(b.PRODUCT_BARCODE_BARCODE) like '%".strtolower($val)."%') OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");
        $count = DB::selectOne("select count(*) as count
                            from TBL_PURC_PRODUCT p, TBL_PURC_PRODUCT_BARCODE b where p.PRODUCT_ID = b.PRODUCT_ID AND $BC
                            AND (lower(p.product_name) like '%".strtolower($val)."%'  OR lower(b.PRODUCT_BARCODE_BARCODE) like '%".strtolower($val)."%') ");

         */




        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    public function getProductById(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }
        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $BC = "p.business_id = $business_id AND p.company_id = $company_id";
        
        $p_str = strtoupper($val);
        $p_str = str_replace('%2F','/',$p_str);
        $p_str = str_replace('%22','"',$p_str);
        $p_str = str_replace('%2C',',',$p_str);
        $p_str = str_replace("'","''",$p_str);
        $replaced_str = str_replace(' ', '%', trim($p_str));
        $replaced_str = str_replace('%20', '%', trim($replaced_str));

        $data['items'] = DB::select("SELECT 
            p.PRODUCT_ID AS id,
            p.PRODUCT_NAME,
            MAX(b.PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE,
            MAX(b.UOM_NAME) UOM_NAME,
            MAX(b.PRODUCT_BARCODE_PACKING) AS packing 
        FROM
            TBL_PURC_PRODUCT p,
            TBL_PURC_PRODUCT_BARCODE b 
        WHERE p.PRODUCT_ID = b.PRODUCT_ID 
            AND (  
                upper(b.product_barcode_barcode) Like '%".$replaced_str."%' 
                OR upper(p.product_name) like '%".$replaced_str."%' 
            )
            AND $BC
        GROUP BY p.PRODUCT_ID,
            p.PRODUCT_NAME,
            p.PRODUCT_ARABIC_NAME,
            p.GROUP_ITEM_ID,
            p.GROUP_ITEM_PARENT_ID  
        ORDER BY 
            Case
                WHEN upper(MAX(b.product_barcode_barcode)) Like '".$replaced_str."' THEN 1
                WHEN upper(MAX(p.product_name)) Like '".$replaced_str."%' THEN 2
                WHEN upper(MAX(p.product_name)) Like '%".$replaced_str."' THEN 4
                Else 3 
            END,
            p.product_name
            OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

            $count = DB::selectOne("select count(*) as count
            from TBL_PURC_PRODUCT p, TBL_PURC_PRODUCT_BARCODE b where p.PRODUCT_ID = b.PRODUCT_ID AND $BC
            AND (lower(p.product_name) like '%".strtolower($val)."%'  OR lower(b.PRODUCT_BARCODE_BARCODE) like '%".strtolower($val)."%') ");

        /*
          $data['items'] = DB::select("select p.product_id as id, p.PRODUCT_NAME, b.PRODUCT_BARCODE_BARCODE,b.UOM_NAME,b.PRODUCT_BARCODE_PACKING as packing
                            from TBL_PURC_PRODUCT p, TBL_PURC_PRODUCT_BARCODE b where p.PRODUCT_ID = b.PRODUCT_ID AND $BC
                            AND (lower(p.product_name) like '%".strtolower($val)."%' OR lower(b.PRODUCT_BARCODE_BARCODE) like '%".strtolower($val)."%') OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");
        $count = DB::selectOne("select count(*) as count
                            from TBL_PURC_PRODUCT p, TBL_PURC_PRODUCT_BARCODE b where p.PRODUCT_ID = b.PRODUCT_ID AND $BC
                            AND (lower(p.product_name) like '%".strtolower($val)."%'  OR lower(b.PRODUCT_BARCODE_BARCODE) like '%".strtolower($val)."%') ");

        */

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    
    public function getMarchantById(Request $request){
        
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        //$branch_id = Auth::user()->branch_id;

        $BC = "business_id = $business_id AND company_id = $company_id ";

        $data['items'] = DB::select("select chart_account_id as id, chart_name
                         from TBL_ACCO_CHART_ACCOUNT where $BC
                         AND (
                            lower(chart_name) like '%".strtolower($val)."%'
                            )
                            and chart_code like '6-01-03%'
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");


        $count = DB::selectOne("select count(*) as count
                            from TBL_ACCO_CHART_ACCOUNT  where $BC
                            AND (
                                lower(chart_name) like '%".strtolower($val)."%'
                                ) 
                        ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;
        
        return response()->json($data);
    }


    public function getCustomerById(Request $request){
        
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $branch_id = Auth::user()->branch_id;


        $val = strtoupper($val);
        $replaced_str = str_replace(' ', '%', trim($val));

        $BC = "business_id = $business_id AND company_id = $company_id ";

        $data['items'] = DB::select("select customer_id as id, CONCAT(CONCAT(CONCAT(CONCAT(customer_name, ' => '),card_number),' => '),customer_mobile_no) as customer_name
                         from tbl_sale_customer where $BC
                         AND (
                            lower(customer_name) like '%".strtolower($replaced_str)."%' OR
                            lower(card_number) like '%".strtolower($replaced_str)."%' OR
                            lower(customer_mobile_no) like '%".strtolower($replaced_str)."%' 
                            )
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
                            from tbl_sale_customer  where $BC
                            AND (
                                lower(customer_name) like '%".strtolower($val)."%'
                                ) 
                        ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;
        
        return response()->json($data);
    }
    public function getSupplierById(Request $request){
        
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }

        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $branch_id = Auth::user()->branch_id;
        $BCB = "business_id = $business_id AND company_id = $company_id ";
        $data['items'] = DB::select("select supplier_id as id, supplier_name
                         from tbl_purc_supplier where $BCB
                         AND (lower(supplier_name) like '%".strtolower($val)."%')
                         OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count
                            from tbl_purc_supplier where $BCB
                            AND (lower(supplier_name) like '%".strtolower($val)."%') ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }

    public function getChartAccountByName(Request $request){
        // dd($request->toArray());
        $data = [];
        $caseName = $request->caseName;
        $val = $request->q;
        if($request->page){
            $offset = ($request->page-1) * 30;
        }else{
            $offset = 0;
        }
        $business_id = Auth::user()->business_id;
        $company_id = Auth::user()->company_id;
        $where = "p.business_id = $business_id AND p.company_id = $company_id";
        if(isset($request->report_case) && $request->report_case == 'activity_trial'){
            $where .= " AND CHART_LEVEL < 4 ";
        }else{
            //$where .= " AND CHART_LEVEL = 4 ";
        }
        $val = strtoupper($val);
        $replaced_str = str_replace(' ', '%', trim($val));

        $where .= " and (upper(p.chart_name) Like '%".$replaced_str."%' OR
                        upper(p.chart_code) like '%".$replaced_str."%' OR
                        upper(p.chart_reference_code) like '%".$replaced_str."%' )
                        order by
                        Case
                            WHEN upper(p.chart_name) Like '".$val."' THEN 1
                            WHEN upper(p.chart_name) Like '".$val."%' THEN 2
                            WHEN upper(p.chart_name) Like '%".$val."' THEN 4
                            Else 3
                        END,p.chart_name ";

        $data['items'] = DB::select("select p.chart_account_id as id, p.chart_name, p.chart_code
                            from tbl_acco_chart_account p where $where OFFSET $offset ROWS FETCH NEXT 30 ROWS ONLY");

        $count = DB::selectOne("select count(*) as count from tbl_acco_chart_account p where $where ");

        if(!empty($data['items'])){
            $data['status'] = 'success';
        }
        $data['total_count'] = $count->count;

        return response()->json($data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function dynamicStore(Request $request, $id = null){
        $data = [];

     //   return $this->jsonErrorResponse($data, "Report in developing process", 200);

        $validator = Validator::make($request->all(), [
            'report_branch_ids' => 'required',
        ]);
        session()->forget('data');
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try{
            
            //dd($request->toArray());
            $data['report_id'] = $request->report_id;
            $data['business_id'] = $request->report_business_id;
            $data['branch_id'] = $request->report_branch_id;
            $data['branch_ids'] = $request->report_branch_ids;
            $data['report_type'] = $request->report_type;
            $data['report_case'] = $request->report_case;
            $data['form_file_type'] = $request->form_file_type;
            $data['report_listing_type'] = $request->report_listing_type;
            $data['date'] = isset($request->date)?date('Y-m-d', strtotime($request->date)):"";
            $data['to_date'] = isset($request->date_to)?date('Y-m-d', strtotime($request->date_to)):"";
            $data['from_date'] = isset($request->date_from)?date('Y-m-d', strtotime($request->date_from)):"";
            $data['product'] = isset($request->product_id)?$request->product_id:"";
            $data['customer'] = isset($request->customer_id)?$request->customer_id:"";
            $data['marchant_id'] = isset($request->marchant_id)?$request->marchant_id:"";
            $data['products'] = isset($request->product_ids)?$request->product_ids:[];
            $data['store_ids'] = isset($request->store)?$request->store:[];
            $data['product_group'] = isset($request->product_group)?$request->product_group:[];
            $data['chart_account_multiple']  = isset($request->chart_account_multiple)?$request->chart_account_multiple:[];
            $data['chart_account'] = isset($request->chart_account)?$request->chart_account:"";
            $data['customer_group'] = isset($request->customer_group)?$request->customer_group:[];
            $data['level_list'] = isset($request->level_list)?$request->level_list:"";
            $data['dead_st'] = isset($request->dead_st)?$request->dead_st:"";
            $data['dead_days'] = isset($request->dead_days)?$request->dead_days:"";
            $data['customers'] = isset($request->customer_ids)?$request->customer_ids:[];
            $data['payment_types'] = isset($request->payment_types)?$request->payment_types:[];
            $data['rate_type']  = isset($request->rate_type)?$request->rate_type:"";
            $data['sales_type']  = isset($request->sales_type)?$request->sales_type:"";
            $data['sales_types']  = isset($request->sale_types_multiple)?$request->sale_types_multiple:[];
            $data['users']  = isset($request->users_ids)?$request->users_ids:[];
            $data['supplier_group']  = isset($request->supplier_group)?$request->supplier_group:[];
            $data['suppliers']  = isset($request->supplier_ids)?$request->supplier_ids:[];
            $data['voucher_types']  = isset($request->voucher_types)?$request->voucher_types:[];
            $data['all_document_type']  = isset($request->all_document_type)?$request->all_document_type:[];
            $data['all_branches']  = isset($request->all_branches)?$request->all_branches:[];


            $f_product_group = isset($request->f_product_group)?$request->f_product_group:[];
            if(!empty($f_product_group)){
                $f_product_group_arr = explode('~',$f_product_group);
                $data['f_product_group_id'] = $f_product_group_arr[0];
                $data['f_product_group_name'] = $f_product_group_arr[1];
            }

            //for multiple
            $f_product_group_multiple = isset($request->f_product_group_multiple)?$request->f_product_group_multiple:[];
            if(!empty($f_product_group_multiple)){
                $f_product_group_id = [];
                $f_product_group_name = [];
                foreach($f_product_group_multiple as $f_product_group){
                    $f_product_group_arr = explode('~',$f_product_group);
                    array_push($f_product_group_id,$f_product_group_arr[0]);
                    array_push($f_product_group_name,$f_product_group_arr[1]);
                }
                $data['f_product_group_id_multiple'] = $f_product_group_id;
                $data['f_product_group_name_multiple'] = $f_product_group_name;
            }

            // change variable in query
            $report_tb_data = \App\Models\TblSoftReports::with('report_styling')->where('report_id',$data['report_id'])->first();
            $qry = str_replace(array("\n","\r\n","\r"), ' ', $report_tb_data['report_query']);
            $qry = strtolower(strtoupper($qry));
            $qry = str_replace('$branch_multiple$'," in (".implode(",",$data['branch_ids']).") ",$qry);

            if($data['date'] != ""){
                $qry = str_replace('$date$',"to_date ('".$data['date']."', 'yyyy/mm/dd')",$qry);
            }
            if($data['to_date'] != ""){
                $qry = str_replace('$to_date$',"to_date ('".$data['to_date']."', 'yyyy/mm/dd')",$qry);
            }
            if($data['from_date'] != ""){
                $qry = str_replace('$from_date$',"to_date ('".$data['from_date']."', 'yyyy/mm/dd')",$qry);
            }
            if($data['product'] != ""){
                $qry = str_replace('$product$',$data['product'],$qry);
            }
            if($data['customer'] != ""){
                $qry = str_replace('$customer$',$data['customer'],$qry);
            }
            if($data['chart_account'] != ""){
                $qry = str_replace('$chart_account$',$data['chart_account'],$qry);
            }
            if($data['level_list'] != ""){
                $qry = str_replace('$level_list$',$data['level_list'],$qry);
            }
            if($data['dead_st'] != ""){
                $qry = str_replace('$dead_st$',$data['dead_st'],$qry);
            }
            if($data['dead_days'] != ""){
                $qry = str_replace('$dead_days$',$data['dead_days'],$qry);
            }
            if($data['rate_type'] != "" && $data['rate_type'] != 0){
                $qry = str_replace('$rate_type$',$data['rate_type'],$qry);
            }
            if($data['sales_type'] != ""){
                $qry = str_replace('$sales_type$',"'".$data['sales_type']."'",$qry);
            }
            if(count($data['products']) != 0 && $data['products'] != "" && $data['products'] != null){
                $qry = str_replace('$product_multiple$'," in (".implode(",",$data['products']).") ",$qry);
            }else{
                $qry = str_replace('$product_multiple$'," not in (-1) ",$qry);
            }
            if(count($data['store_ids']) != 0 && $data['store_ids'] != "" && $data['store_ids'] != null){
                $qry = str_replace('$store_multiple$'," in (".implode(",",$data['store_ids']).") ",$qry);
            }else{
                $qry = str_replace('$store_multiple$'," not in (-1) ",$qry);
            }
            if(count($data['sales_types']) != 0 && $data['sales_types'] != "" && $data['sales_types'] != null){
                $qry = str_replace('$sales_type_multiple$'," in ('".implode("','",$data['sales_types'])."') ",$qry);
            }else{
                $qry = str_replace('$sales_type_multiple$'," not in ('-1') ",$qry);
            }
            if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null){
                $qry = str_replace('$product_group_multiple$'," in (".implode(",",$data['product_group']).") ",$qry);
            }else{
                $qry = str_replace('$product_group_multiple$'," not in (-1) ",$qry);
            }
            if(count($data['chart_account_multiple']) != 0 && $data['chart_account_multiple'] != "" && $data['chart_account_multiple'] != null){
                $qry = str_replace('$chart_account_multiple$'," in (".implode(",",$data['chart_account_multiple']).") ",$qry);
            }else{
                $qry = str_replace('$chart_account_multiple$'," not in (-1) ",$qry);
            }
            if(count($data['customer_group']) != 0 && $data['customer_group'] != "" && $data['customer_group'] != null){
                $qry = str_replace('$customer_group_multiple$'," in (".implode(",",$data['customer_group']).") ",$qry);
            }else{
                $qry = str_replace('$customer_group_multiple$'," not in (-1) ",$qry);
            }
            if(count($data['customers']) != 0 && $data['customers'] != "" && $data['customers'] != null){
                $qry = str_replace('$customer_multiple$'," in (".implode(",",$data['customers']).") ",$qry);
            }else{
                $qry = str_replace('$customer_multiple$'," not in (-1) ",$qry);
            }

            if(count($data['payment_types']) != 0 && $data['payment_types'] != "" && $data['payment_types'] != null){
                $qry = str_replace('$payment_types$'," in (".implode(",",$data['payment_types']).") ",$qry);
            }else{
                $qry = str_replace('$payment_types$'," not in (-1) ",$qry);
            }
            if(count($data['users']) != 0 && $data['users'] != "" && $data['users'] != null){
                $qry = str_replace('$salesman$'," in (".implode(",",$data['users']).") ",$qry);
            }else{
                $qry = str_replace('$salesman$'," not in (-1) ",$qry);
            }
            if(count($data['supplier_group']) != 0 && $data['supplier_group'] != "" && $data['supplier_group'] != null){
                $qry = str_replace('$supplier_group_multiple$'," in (".implode(",",$data['supplier_group']).") ",$qry);
            }else{
                $qry = str_replace('$supplier_group_multiple$'," not in (-1) ",$qry);
            }
            if(count($data['suppliers']) != 0 && $data['suppliers'] != "" && $data['suppliers'] != null){
                $qry = str_replace('$supplier_multiple$'," in (".implode(",",$data['suppliers']).") ",$qry);
            }else{
                $qry = str_replace('$supplier_multiple$'," not in (-1) ",$qry);
            }
            if(count($data['voucher_types']) != 0 && $data['voucher_types'] != "" && $data['voucher_types'] != null){
                $qry = str_replace('$voucher_type_multiple$'," in (".implode(",",$data['voucher_types']).") ",$qry);
            }else{
                $qry = str_replace('$voucher_type_multiple$'," not in (-1) ",$qry);
            }

            // fields Names...
            if(isset($data['f_product_group_id']) && isset($data['f_product_group_name'])){
                $qry = str_replace('$f_product_group_id$',$data['f_product_group_id'],$qry);
                $qry = str_replace('$f_product_group_name$',$data['f_product_group_name'],$qry);
            }else{
                if($report_tb_data['report_case'] =='Product-Group-Wise-Sale'){
                    $data['report_status'] = false;
                }
            }
          //  dd($request->outer_filterList);
            $subQry = "";
            $makeQry = false;
            if(isset($request->outer_filterList)){
                foreach ($request->outer_filterList as $outer_filterList){
                    $outer_clause = !empty($outer_filterList['outer_clause'])?$outer_filterList['outer_clause']:"";
                    $makeInnerQry = false;
                    $subQry .= $outer_clause." (";
                    foreach ($outer_filterList['inner_filterList'] as $inner_filterList){
                        $key = isset($inner_filterList['key'])?$inner_filterList['key']:"";
                        $key_type = isset($inner_filterList['key_type'])?$inner_filterList['key_type']:"";
                        $condition = isset($inner_filterList['conditions'])?$inner_filterList['conditions']:"";
                        $vals = isset($inner_filterList['val'])?$inner_filterList['val']:"";
                        $inner_clause = "";
                        if(!empty($key) && !empty($key_type) && !empty($condition) && !empty($vals)){
                            if(isset($inner_filterList['inner_clause_item']) && count($outer_filterList['inner_filterList']) > 1){
                                $inner_clause = $inner_filterList['inner_clause_item'];
                            }
                            $subQry .= " (";
                            if($key_type == 'varchar2'){
                                foreach ($vals as $val){
                                    if($val != null){
                                        if($condition == '=' || $condition == '!='){
                                            $subQry .= "lower($key) $condition '".strtolower(strtoupper($val))."' OR " ;
                                        }else{
                                            $subQry .= "lower($key) $condition '%".strtolower(strtoupper($val))."%' OR " ;
                                        }
                                    }
                                }
                                $subQry = rtrim($subQry, "OR ");
                            }
                            if($key_type == 'number' || $key_type == 'float'){
                                if($condition == 'between'){
                                    $subQry .= "$key $condition '".$vals."' AND '".$inner_filterList['val_to']."' ";
                                }else{
                                    $subQry .= "$key $condition '".$vals."' ";
                                }
                            }
                            if($key_type == 'date'){
                                $from = "TO_DATE('".$vals."', 'dd-mm-yyyy')";
                                $to = "TO_DATE('".$inner_filterList['val_to']."', 'dd-mm-yyyy')";
                                $subQry .= "$key $condition ".$from." AND ".$to." ";
                            }
                            $subQry .= ") ";
                            $makeQry = true;
                            $makeInnerQry = true;
                            $subQry .= $inner_clause;
                        }

                    }
                    if($makeInnerQry == false){
                        $subQry = rtrim($subQry, $outer_clause." (");
                        $subQry .= " ";
                    }else{
                        $subQry .= ") ";
                    }
                }
            }
            if($makeQry){
                $subQry = "( $subQry ) AND";
            }else{
                $subQry = "";
            }
            $qry = str_replace('$withdynamicqry$',"$subQry",$qry);


            if(!empty($subQry)){
                $subQry = rtrim($subQry, "AND");
                $qry = str_replace('$andwithdynamicqry$',"and $subQry",$qry);
            }
            if(empty($subQry)){
                $qry = str_replace('$andwithdynamicqry$'," ",$qry);
            }

            if(!empty($subQry)){
                $subQry = rtrim($subQry, "AND");
                $qry = str_replace('$wherewithdynamicqry$',"where $subQry",$qry);
            }
            if(empty($subQry)){
                $qry = str_replace('$wherewithdynamicqry$'," ",$qry);
            }

            $data['qry'] = str_replace(array("\n","\r\n","\r"), ' ', $qry);

            session(['data' => $data]);
            $dataJs['url'] = route( 'reports.view_report');

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

        $dataJs['redirect'] =  ''; // $data['report_case'];

        return $this->jsonSuccessResponse($dataJs, trans('message.report_ready'), 200);
    }

    public function staticStore(Request $request, $id = null)
    {
       // dd($request->toArray());
        $data = [];
        session()->forget('data');
        $msg = [];
        $validateField = [];
        $validateField['report_branch_ids'] =  'required';
        $msg['report_branch_ids'] =  'Branch field is required.';
        
        if($request->report_case == 'product_activity'){
            $validateField['product_id'] =  'required';
            $msg['product_id'] =  'Product field is required.';
        }
        /*if($request->report_case == 'sale_report'){
            $validateField['customer_id'] =  'required';
            $msg['customer_id'] =  'Customer field is required.';
        }*/
        // if($request->report_case == 'slow_moving_stock'){
        //     $validateField['product_group'] =  'required';
        //     $msg['product_group'] =  'Select Product Group(s).';
        // }
        $validator = Validator::make($request->all(),$validateField,$msg);
        session()->forget('data');
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try {
            if($request->pd  != "")
            {
                $aa = array();
                foreach ($request->pd as $pd) {
                    $aa[] = $pd['product_name'];
                }
                $new_code = $aa;
            }

            $data['report_case'] = $request->report_case;
            $data['report_type'] = $request->report_type;
            $data['form_file_type'] = $request->form_file_type;
            $clause_business_id = ' business_id = ' . auth()->user()->business_id;
            $clause_company_id = ' AND company_id = ' . auth()->user()->company_id;
            $clause_branch_id = ' AND branch_id = ' . auth()->user()->branch_id;
            $data['clause_business_id'] = $clause_business_id;
            $data['clause_company_id'] = $clause_company_id;
            $data['clause_branch_id'] = $clause_branch_id;
            $from_date = isset($request->date_from)?$request->date_from:"";
            $date_time_from = isset($request->between_date_time_from)?$request->between_date_time_from:"";
            $from_date_merg = $from_date.' '.$date_time_from;
            $date_from = date('Y-m-d H:i:s', strtotime($from_date_merg));
            $date = isset($request->date)?$request->date:"";

            $date_time_wise = isset($request->date_time_wise)?1:"";
            if($date_time_wise == 1){
                $from_date = isset($request->date_from)?$request->date_from:"";
                $time_from = isset($request->time_from)?$request->time_from:"";
                $from_date_merg = $from_date.' '.$time_from;
                $date_from = date('Y-m-d H:i:s', strtotime($from_date_merg));

                //Single Date
                $date = isset($request->date)?$request->date:"";
                $from_date_merg = $date.' '.$time_from;
                $date_time = date('Y-m-d H:i:s', strtotime($from_date_merg));
            }


            $new_code = isset($new_code)?$new_code:[];

            $diff_perc = isset($request->diff_perc)?$request->diff_perc:"";
            $to_date = isset($request->date_to)?$request->date_to:"";
            $date_time_to = isset($request->between_date_time_to)?$request->between_date_time_to:"";
            $time_to = isset($request->time_to)?$request->time_to:"";
            $to_date_merg =  $to_date.' '.$date_time_to;
            $date_to = date('Y-m-d H:i:s', strtotime($to_date_merg));

            if($date_time_wise == 1){
                $to_date = isset($request->date_to)?$request->date_to:"";
                $time_to = isset($request->time_to)?$request->time_to:"";
                $to_date_merg =  $to_date.' '.$time_to;
                $date_to = date('Y-m-d H:i:s', strtotime($to_date_merg));
            }

            $sales_type = isset($request->sales_type)?$request->sales_type:"";
            $marchant_id = isset($request->marchant_id)?$request->marchant_id:"";
            $customer_id = isset($request->customer_id)?$request->customer_id:"";
            $supplier_id = isset($request->supplier_id)?$request->supplier_id:"";
            $product_id = isset($request->product_id)?$request->product_id:"";
            $chart_account_id = isset($request->chart_account)?$request->chart_account:"";
            $level_list = isset($request->level_list)?$request->level_list:"";
            $dead_st = isset($request->dead_st)?$request->dead_st:"";
            $dead_days = isset($request->dead_days)?$request->dead_days:"";
            $cash_flow = isset($request->radiocashflow)?$request->radiocashflow:"";
            $post_wise = isset($request->post_wise)?$request->post_wise:"";
            $OrderBy = isset($request->OrderBy)?$request->OrderBy:"";
            $nagetivestock = isset($request->nagetivestock)?$request->nagetivestock:"";
            $product_status = isset($request->product_status)?$request->product_status:"";
            $product_group = isset($request->product_group)?$request->product_group:[];
            $rate_between = isset($request->rate_between)?$request->rate_between:"";
            $rate_type = isset($request->rate_type)?$request->rate_type:"";
            $sale_types_multiple = isset($request->sale_types_multiple)?$request->sale_types_multiple:[];
            $document_types_multiple = isset($request->document_types_multiple)?$request->document_types_multiple:[];
            $chart_account_multiple = isset($request->chart_account_multiple)?$request->chart_account_multiple:[];
            $product_ids = isset($request->product_ids)?$request->product_ids:[];
            $customer_ids = isset($request->customer_ids)?$request->customer_ids:[];
            $voucher_types = isset($request->voucher_types)?$request->voucher_types:[];
            $payment_types = isset($request->payment_types)?$request->payment_types:[];
            $store = isset($request->store)?$request->store:[];
            $supplier_group = isset($request->supplier_group)?$request->supplier_group:[];
            $customer_group = isset($request->customer_group)?$request->customer_group:[];
            $supplier_ids = isset($request->supplier_ids)?$request->supplier_ids:[];
            $users_ids = isset($request->users_ids)?$request->users_ids:[];
            $hide_total = isset($request->hide_total)?1:"";
            $greater_than_net_tp = isset($request->greater_than_net_tp)?1:"";
            $activity_check = isset($request->activity_check)?1:"";
            $consolidate = isset($request->consolidate)?1:0;
            $with_value_wise = isset($request->with_value_wise)?1:0;
            $uom_list  = isset($request->uom_list)?$request->uom_list:[];
            $specific_purchase_type  = isset($request->specific_purchase_type)?$request->specific_purchase_type:"";
            $data['voucher_mode_date'] = isset($request->voucher_mode_date)?1:"";
            $data['all_document_type']  = isset($request->all_document_type)?$request->all_document_type:[];
            $data['all_branches']  = isset($request->all_branches)?$request->all_branches:[];
            $data['voucher_types_selection']  = isset($request->voucher_types_selection)?$request->voucher_types_selection:"";

            $report_cases = ['slow_moving_stock','stock-with-average-cost','combine_ledger_group_wise','sales-and-cost-summary','consumer-report','supplier_wise_sale','inventory_checklist','total_product_activity_summary','product_and_group_activity','top_sale_qty_barcode_wise','supplier_wise_rebate_calc','inventory_batch_expiry','closing_day','sale_type_wise','summary_of_daily_activity',
                'vouchers_list','sale_invoice','trial_balance','activity_trial','date_wise_summarized','temp_accounting_ledger','accounting_ledger','grn_list',
                'top_sale_products','stock_report','inventory_look_up','stock_activity_summary','item_stock_ledger',
                'chart_account_list','stock_detail_document_wise','supplier_list','customer_rpt',
                'po_list','product_rate','bank_reconciliation','store_wise_stock','stock_valuation',
                'product_activity','product_group_activity','supplier_aging','account_notes',
                'profit_loss_statement','customer_aging','business_reports_factors',
                'month_wise_account_summary','vat_report','daily_purchase','supplier_wise_purchase_summary',
                'item_wise_purchase_summary','category_wise_purchase_analysis','invoice_wise_purchase_summary',
                'invoice_wise_sale_report','sale_register_report','sales_discount','invoice_wise_sales_discount',
                'branch_wise_stock','product_list','product_change_rate','product_pl','stock_audit',
                'branch_wise_stock_summary','group_wise_stock_activity_summary','cash_flow','final_price_update','payment_wht','frb_sales_data','dead_stock','hs_code','product_parent_group_wise_sale','month_wise_product_group_sale','monthly_sale_pur_summary',];
           
            $data['branch_ids'] = $request->report_branch_ids;
            $data['supplier_ids'] = $request->report_supplier_ids;
            $data['product_group'] = $request->report_product_groups;
            $data['product_ids'] = $request->report_product_ids;

            //dump($request->report_branch_ids);
            //dump($request->report_supplier_ids);
            //dd($request->report_product_ids);

            if(in_array($data['report_case'],$report_cases)){
                $list = [];

                if($data['report_case'] == 'supplier_wise_purchase_summary'){
                    $data['key'] = 'supplier_wise_purchase_summary';
                    $data['page_title'] = 'Supplier Wise Purchase Summary';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                    if(empty($specific_purchase_type)){
                        return $this->jsonErrorResponse($data, "Type is required", 200);
                    }
                    $data['specific_purchase_type'] = $specific_purchase_type;
                }
                if($data['report_case'] == 'item_wise_purchase_summary'){
                    $data['key'] = 'item_wise_purchase_summary';
                    $data['page_title'] = 'Item Wise Purchase Summary';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                }
                if($data['report_case'] == 'product_change_rate'){
                    $data['key'] = 'product_change_rate';
                    $data['page_title'] = 'Product Change Rate';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                }
                
                if($data['report_case'] == 'product_pl'){
                    $data['key'] = 'product_pl';
                    $data['page_title'] = 'Product Wise Profit & Loss';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                    
                }
                if($data['report_case'] == 'invoice_wise_purchase_summary'){
                    $data['key'] = 'invoice_wise_purchase_summary';
                    $data['page_title'] = 'Invoice Wise Purchase Summary';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_group'] = $product_group;
                    if(empty($specific_purchase_type)){
                        return $this->jsonErrorResponse($data, "Type is required", 200);
                    }
                    $data['specific_purchase_type'] = $specific_purchase_type;
                }
                if($data['report_case'] == 'branch_wise_stock'){
                    $data['key'] = 'branch_wise_stock';
                    $data['page_title'] = 'Branch Wise Stock';
                    $data['date'] = date('Y-m-d', strtotime($date)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $new_code;//$product_ids;
                }
                if($data['report_case'] == 'branch_wise_stock_summary'){
                    $data['key'] = 'branch_wise_stock_summary';
                    $data['page_title'] = 'Branch Wise Stock Summary';
                    if($date_time_wise == 1){
                        $data['time_from'] = date('Y-m-d H:i', strtotime($date_time)); 
                    }else{
                        $data['date'] = date('Y-m-d', strtotime($date));
                    }
                    $data['date_time_wise'] = $date_time_wise;
                    //$data['product_ids'] = $new_code;
                    $data['supplier_ids'] = $supplier_id;
                    $data['product_group'] = $product_group;
                }
                if($data['report_case'] == 'group_wise_stock_activity_summary'){
                    $data['key'] = 'group_wise_stock_activity_summary';
                    $data['page_title'] = 'Stock Activity Summary';
                    
                    if($date_time_wise == 1){
                        $data['time_from'] = date('Y-m-d H:i', strtotime($date_from)); 
                        $data['time_to'] = date('Y-m-d H:i', strtotime($date_to)); 
                    }else{
                        $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                        $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    }
                    $data['date_time_wise'] = $date_time_wise;
                    
                    $data['product_ids'] = $new_code;
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_group'] = $product_group;
                }


                if($data['report_case'] == 'product_list'){
                    $data['key'] = 'product_list';
                    $data['page_title'] = 'Product List';
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                }
                if($data['report_case'] == 'category_wise_purchase_analysis'){
                    $data['key'] = 'category_wise_purchase_analysis';
                    $data['page_title'] = 'Category Wise Purchase Analysis';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                    $data['supplier_ids'] = $supplier_ids;
                }

                if($data['report_case'] == 'supplier_wise_rebate_calc'){
                    $data['key'] = 'supplier_wise_rebate_calc';
                    $data['page_title'] = 'Supplier Wise Rebate Calculation';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $supplier_ids;
                }


                if($data['report_case'] == 'inventory_batch_expiry'){
                    $data['key'] = 'inventory_batch_expiry';
                    $data['page_title'] = 'Inventory Batch Expiry';
                    $data['date'] = date('Y-m-d', strtotime($date)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['near_expiry_days'] = $request->near_expiry_days;
                    $data['near_expiry_days_filter_types'] = $request->near_expiry_days_filter_types;
                    if(empty($request->near_expiry_days)){
                        return $this->jsonErrorResponse($data, "Near Expiry Days is required", 200);
                    }
                    if(empty($request->near_expiry_days_filter_types)){
                        return $this->jsonErrorResponse($data, "Near Expiry Days Type is required", 200);
                    }
                }
                if($data['report_case'] == 'stock_valuation'){
                    $data['key'] = 'stock_valuation';
                    $data['page_title'] = 'Stock Valuation report';
                    $data['date'] = date('Y-m-d', strtotime($date)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                }
                if($data['report_case'] == 'store_wise_stock'){
                    $data['key'] = 'store_wise_stock';
                    $data['page_title'] = 'Store Wise Stock report';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                    $data['store'] = $store;
                }
                if($data['report_case'] == 'closing_day'){
                    $dates = [];
                    $data['key'] = 'closing_day';
                    $data['page_title'] = 'Daily Closing Report';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['only_date'] = date('Y-m-d', strtotime($from_date));

                    // Report Verification
                    $fromDate   = new DateTime($data['from_date']);
                    $toDate     = new DateTime($data['to_date']);
                    for($i = $fromDate; $i <= $toDate; $i->modify('+1 day')){
                        array_push($dates , $i->format('Y-m-d'));
                    }
                    $verifiedDates = TblVerifyReports::where('report_name' , $data['key'])->orderBy('report_date')->pluck('report_date');
                    $data['verified_dates'] = [];
                    foreach ($verifiedDates as $value) {
                        array_push($data['verified_dates'] , date('Y-m-d' , strtotime($value)));
                    }
                    $diffrence = array_diff($dates,$data['verified_dates']);
                    if(count($diffrence) > 0){
                        $data['is_verified'] = 0;
                    }else{
                        $data['is_verified'] = 1;
                    }
                }

                if($data['report_case'] == 'sale_type_wise'){
                    $data['key'] = 'sale_type_wise';
                    $data['page_title'] = 'Sales Type Wise Report';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['sales_type'] = $sale_types_multiple;
                    $data['payment_types'] = $payment_types;
                    $data['customer_ids'] = $customer_ids;
                    $data['users'] = $users_ids;
                }
                if($data['report_case'] == 'invoice_wise_sale_report'){
                    $data['key'] = 'invoice_wise_sale_report';
                    $data['page_title'] = 'Invoice Wise Sale Report';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['sale_types_multiple'] = $sale_types_multiple;
                    $data['marchant_id'] = $marchant_id;
                    $data['users'] = $users_ids;
                }
                if($data['report_case'] == 'sale_register_report'){
                    $data['key'] = 'sale_register_report';
                    $data['page_title'] = 'Sale Register Report';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                }
                if($data['report_case'] == 'sales_discount'){
                    $data['key'] = 'sales_discount';
                    $data['page_title'] = 'Product Wise Sales Discount Report';
                    $data['sale_types_multiple'] = $sale_types_multiple;
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                }
                if($data['report_case'] == 'invoice_wise_sales_discount'){
                    $data['key'] = 'invoice_wise_sales_discount';
                    $data['page_title'] = 'Invoice Wise Sales Discount Report';
                    $data['sale_types_multiple'] = $sale_types_multiple;
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                }
                
                if($data['report_case'] == 'frb_sales_data'){
                    $data['key'] = 'frb_sales_data';
                    $data['page_title'] = 'FBR Sale Data';
                    $data['sale_types_multiple'] = $sale_types_multiple;
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                }

                if($data['report_case'] == 'hs_code'){
                    $data['key'] = 'hs_code';
                    $data['page_title'] = 'Sales Invoice HS Code Report';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                }

                if($data['report_case'] == 'summary_of_daily_activity'){
                    $data['key'] = 'summary_of_daily_activity';
                    $data['page_title'] = 'Summary of Daily Activity';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //
                    $data['payment_types'] = $payment_types;
                    $data['users'] = $users_ids;
                    // SALES_SALES_TYPE
                    $data['where_payment_types'] = "";
                    $data['where_users'] = "";
                    if(!empty($data['payment_types'])){
                        $data['where_payment_types'] = " and sales_sales_type in (".implode(",",$data['payment_types']).") ";
                    }
                    if(!empty($data['users'])){
                        $data['where_users'] = " and sales_sales_man in (".implode(",",$data['users']).") ";
                    }
                    $Datequery = "select distinct sales_date from vw_sale_sales_invoice where (sales_type ='SI' OR sales_type ='POS') ".$data['where_payment_types']." ".$data['where_users']." and branch_id in (".implode(",",$data['branch_ids']).") and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') order by sales_date";
                    $data['Date'] = DB::select($Datequery);
                    $SIquery = "select distinct sales_sales_man,sales_sales_man_name from vw_sale_sales_invoice where (sales_type ='SI' OR sales_type ='POS') ".$data['where_payment_types']." ".$data['where_users']." and branch_id in (".implode(",",$data['branch_ids']).") and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                    $data['SI'] = DB::select($SIquery);
                    $SRquery = "select distinct sales_sales_man,sales_sales_man_name from vw_sale_sales_invoice where (sales_type ='SR' OR sales_type ='RPOS') ".$data['where_payment_types']." ".$data['where_users']." and branch_id in (".implode(",",$data['branch_ids']).") and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                    $data['SR'] = DB::select($SRquery);
                    $data['SI_Count'] = count($data['SI']);
                    $data['SR_Count'] = count($data['SR']);
                    $list = '';
                }

                if($data['report_case'] == 'vouchers_list'){
                    $data['key'] = 'vouchers_list';
                    $data['page_title'] = 'Vouchers List';
                    $data['chart_account'] = $chart_account_multiple;
                    $data['voucher_type'] = $voucher_types ;
                    $to_date = $request->date_to;
                    $from_date = $request->date_from;
                    $data['where'] = '';
                    $data['where_chart_account'] = '';
                    $data['where_voucher_type'] = '';
                    //--------chart account id--------------
                    if(count($chart_account_multiple) > 0){
                        $chart_account_cond = '';
                        $data['where_chart_account'] = ' and (';
                        foreach($chart_account_multiple as $chart_account)
                        {
                            $chart_account_cond .= "chart_account_id ='".$chart_account."' OR ";
                        }
                        $data['where_chart_account'] .= substr($chart_account_cond,0,-4);
                        $data['where_chart_account'] .= ')';
                    }
                    //-----------end chart--------------
                    //--------voucher type --------------
                    if(count($voucher_types) > 0){
                        $voucher_type_cond = '';
                        $data['where_voucher_type'] = ' and (';
                        foreach($voucher_types as $voucher_type)
                        {
                            $voucher_type_cond .= "lower(voucher_type) ='".strtolower($voucher_type)."' OR ";
                        }
                        $data['where_voucher_type'] .= substr($voucher_type_cond,0,-4);
                        $data['where_voucher_type'] .= ')';
                    }
                    //-----------end type--------------
                    $data['where'] = $data['where_voucher_type'].''.$data['where_chart_account'];
                    $data['hide_total'] = $hide_total;
                    $data['post_wise'] = $post_wise;
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16

                    $query = "Select distinct branch_id,branch_name from vw_acco_voucher where voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') and branch_id in (".implode(",",$data['branch_ids']).") and ( voucher_debit <> 0 OR  voucher_credit <> 0 ) ".$data['where'];
                    $list = DB::select($query);
                }

                if($data['report_case'] == 'sale_invoice'){
                    $list = [];
                    $data['key'] = 'sale_invoice';
                    $data['page_title'] = 'Sale Invoice';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['customer_ids'] = $customer_id;
                    $data['product_ids'] = $new_code;//$product_ids;
                    $data['sale_types_multiple'] = $sale_types_multiple;
                    $data['users'] = $users_ids;
                    $data['payment_types'] = $payment_types;
                }
                
                if($data['report_case'] == 'customer_rpt'){
                    $data['key'] = 'customer_rpt';
                    $data['page_title'] = 'Customer List';
                    /*$data['customer_group'] = $customer_group;
                    $data['customer'] = TblSaleCustomer::where('customer_entry_status',1)->get();
                    if(!empty($customer_group)){
                        $data['customer'] = $data['customer']->whereIn('customer_type',$customer_group);
                    }*/
                    //$data['customer_ids'] = $customer_ids;
                }

                if($data['report_case'] == 'trial_balance'){
                    $data['key'] = 'trial_balance';
                    $data['page_title'] = 'Trial Balance';
                    $data['date'] = date('Y-m-d', strtotime($date));
                    $data['level_list'] = $level_list;
                    $data['OrderBy'] = $OrderBy;
                    
                    if (!empty($chart_account_id)) {
                        $data['chart_account'] = TblAccCoa::where('chart_account_id', $chart_account_id)->first();
                        $data['chart_account_level'] = $data['chart_account']->chart_level;
                    } else {
                        //return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                }

                
                if($data['report_case'] == 'payment_wht'){
                    $data['key'] = 'payment_wht';
                    $data['page_title'] = 'Payment/WHT Report';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    
                    
                    if (!empty($chart_account_id)) {
                        $data['chart_account'] = TblAccCoa::where('chart_account_id', $chart_account_id)->first();
                    } else {
                        //return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                }

                if($data['report_case'] == 'cash_flow'){
                    $data['key'] = 'cash_flow';
                    $data['page_title'] = 'Cash Flow';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['cash_flow'] = $cash_flow;
                    $data['chart_account_id'] = $chart_account_id;

                    //cash in hand id 22282122291903
                    /*
                    $paras = [
                        'chart_account_id' => '22282122291903',//$chart_account_id,
                        'voucher_date' => $date_from,
                        'branch_ids' => $data['branch_ids'],
                    ];
                    if($data['voucher_mode_date'] == 1){
                        //$data['opening_balance'] = CoreFunc::acco_dispatch_opening_bal($paras);
                    }else{
                       $data['opening_balance'] = CoreFunc::acco_opening_bal($paras);
                    }*/
                }
                if($data['report_case'] == 'account_notes'){
                    $data['key'] = 'account_notes';
                    $data['page_title'] = 'Notes Of Accounts';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    $data['chart_account_id'] = $chart_account_id;
                }

                if($data['report_case'] == 'profit_loss_statement'){
                    $data['key'] = 'profit_loss_statement';
                    $data['page_title'] = 'Profit and Loss Statement';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                }

                if($data['report_case'] == 'activity_trial'){
                    $data['key'] = 'activity_trial';
                    $data['page_title'] = 'Activity Trial';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    $data['chart_account_multiple'] = $chart_account_multiple;
                    $data['level_list'] = $level_list;
                    $data['OrderBy'] = $OrderBy;
                }
                if($data['report_case'] == 'date_wise_summarized'){
                    $data['key'] = 'date_wise_summarized';
                    $data['page_title'] = 'Date Wise Summarized Ledger';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    $data['chart_account_id'] = $chart_account_id;
                }

                if($data['report_case'] == 'vat_report'){
                    $data['key'] = 'vat_report';
                    $data['page_title'] = 'VAT Report';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                }

                if($data['report_case'] == 'accounting_ledger') {
                    $data['key'] = 'accounting_ledger';
                    $data['page_title'] = 'Accounting Ledger';
                    $data['voucher_types'] = $voucher_types;
                    $data['opening_bal_toggle'] = isset($request->accounting_ledger_ob_toggle)?1:0;
                    $data['al_ref_acc_toggle'] = isset($request->al_ref_acc_toggle)?1:0;
                    $data['al_vat_amount_toggle'] = isset($request->al_vat_amount_toggle)?1:0;
                    $data['currency'] = TblDefiCurrency::select('currency_symbol')->where('currency_default',1)->where('business_id',auth()->user()->business_id)->first();
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    if($date_time_wise == 1){
                        $data['time_from'] = date('Y-m-d H:i', strtotime($date_from)); 
                        $data['time_to'] = date('Y-m-d H:i', strtotime($date_to)); 
                    }else{
                        $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                        $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    }
                    $data['date_time_wise'] = $date_time_wise;
                    // dd($chart_account_multiple);


                    if (isset($chart_account_multiple) && count($chart_account_multiple) > 0) {
                        $data['chart_account'] = TblAccCoa::whereIn('chart_account_id', $chart_account_multiple)->get();
                        $chart_account_ids = [];
                        foreach ($data['chart_account'] as $value) {
                            array_push($chart_account_ids , $value->chart_account_id);
                        }
                        $data['chart_account_ids'] = $chart_account_ids;
                        //$data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id]))->first()->code;
                      //  $data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id,auth()->user()->branch_id]))->first()->code;
                        $paras = [
                            'chart_account_id' => $chart_account_ids,
                            'voucher_date' => $from_date,
                            'branch_ids' => $data['branch_ids'],
                        ];
                        if($data['voucher_mode_date'] == 1){
                            $data['opening_balance'] = CoreFunc::acco_dispatch_opening_bal($paras);
                        }else{
                            $data['opening_balance'] = CoreFunc::acco_opening_bal($paras);
                        }
                    } else {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }

                }

                if($data['report_case'] == 'temp_accounting_ledger') {
                    $data['key'] = 'temp_accounting_ledger';
                    $data['page_title'] = 'Accounting Ledger';
                    $data['voucher_types'] = $voucher_types;
                    $data['opening_bal_toggle'] = isset($request->accounting_ledger_ob_toggle)?1:0;
                    $data['al_ref_acc_toggle'] = isset($request->al_ref_acc_toggle)?1:0;
                    $data['al_vat_amount_toggle'] = isset($request->al_vat_amount_toggle)?1:0;
                    $data['currency'] = TblDefiCurrency::select('currency_symbol')->where('currency_default',1)->where('business_id',auth()->user()->business_id)->first();
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    $data['not_contain_customer'] = $request->customer_not_contain ?? [];
                    $data['customer_contain_selection'] = $request->customer_contain_selection;
                    if(isset($chart_account_multiple) && count($chart_account_multiple) > 0) {
                        $data['chart_account'] = TblAccCoa::whereIn('chart_account_id', $chart_account_multiple)->get();
                        $chart_account_ids = [];
                        foreach ($data['chart_account'] as $value) {
                            array_push($chart_account_ids , $value->chart_account_id);
                        }
                        $data['chart_account_ids'] = $chart_account_ids;
                        //$data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id]))->first()->code;
                      //  $data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id,auth()->user()->branch_id]))->first()->code;
                        $paras = [
                            'chart_account_id' => $chart_account_ids,
                            'voucher_date' => $from_date,
                            'branch_ids' => $data['branch_ids'],
                        ];
                        if($data['voucher_mode_date'] == 1){
                            $data['opening_balance'] = CoreFunc::acco_dispatch_opening_bal($paras);
                        }else{
                            $data['opening_balance'] = CoreFunc::acco_opening_bal($paras);
                        }
                    } else {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }

                }

                if($data['report_case'] == 'combine_ledger_group_wise'){
                    $list = '';
                    $data['key'] = 'combine_ledger_group_wise';
                    $data['page_title'] = 'Combine Ledger Group Wise';
                    $data['to_date'] = date('Y/m/d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y/m/d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['opening_bal_toggle'] = isset($request->accounting_ledger_ob_toggle)?1:0;
                    $data['currency'] = TblDefiCurrency::select('currency_symbol')->where('currency_default',1)->where('business_id',auth()->user()->business_id)->first();
                    $data['chart_account_id'] = $chart_account_id;
                    if (!empty($chart_account_id)) {
                        $data['chart_account'] = TblAccCoa::where('chart_account_id', $chart_account_id)->first();
                        $data['chart_account_level'] = $data['chart_account']->chart_level;
                        $paras = [
                            'chart_account_id' => $chart_account_id,
                            'voucher_date' => $from_date,
                            'branch_ids' => $data['branch_ids'],
                        ];
                        if($data['voucher_mode_date'] == 1){
                            $data['opening_balance'] = CoreFunc::acco_dispatch_opening_bal($paras);
                        }else{
                            $data['opening_balance'] = CoreFunc::acco_opening_bal($paras);
                        }
                    } else {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                }

                if($request->report_case == 'bank_reconciliation') {
                    $data['key'] = 'bank_reconciliation';
                    $data['page_title'] = 'Bank Book';
                    $data['currency'] = TblDefiCurrency::select('currency_symbol')->where('currency_default',1)->where('business_id',auth()->user()->business_id)->first();
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    if (!empty($chart_account_id)) {
                        $data['chart_account'] = TblAccCoa::where('chart_account_id', $chart_account_id)->first();
                       //$data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id, auth()->user()->branch_id]))->first()->code;
                      //  $data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$data['date'], $chart_account_id, auth()->user()->business_id, auth()->user()->company_id,auth()->user()->branch_id]))->first()->code;
                      $paras = [
                        'chart_account_id' => $chart_account_id,
                        'voucher_date' => $from_date,
                        'branch_ids' => $data['branch_ids'],
                    ];
                    $data['opening_balance'] = CoreFunc::acco_opening_bal($paras);

                    } else {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                    
                }



                if($data['report_case'] == 'grn_list'){
                    $data['key'] = 'grn_list';
                    $data['page_title'] = 'GRN List';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;

                }
                if($data['report_case'] == 'daily_purchase'){
                    $data['key'] = 'daily_purchase';
                    $data['page_title'] = 'Daily Purchase';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_group'] = $product_group;
                    $data['product_ids'] = $new_code;
                    //$data['product_ids'] = $product_ids;
                    if(empty($specific_purchase_type)){
                        return $this->jsonErrorResponse($data, "Type is required", 200);
                    }
                    $data['specific_purchase_type'] = $specific_purchase_type;

                }

                if($data['report_case'] == 'supplier_list'){
                    $data['key'] = 'supplier_list';
                    $data['page_title'] = 'Supplier List';
                    $data['supplier_group'] = $supplier_group;
                    $data['supplier'] = TblPurcSupplier::where('supplier_entry_status',1)->get();
                    if(!empty($supplier_group)){
                        $data['supplier'] = $data['supplier']->whereIn('supplier_type',$supplier_group);
                    }
                }

                if($data['report_case'] == 'product_rate'){
                    $data['key'] = 'product_rate';
                    $data['page_title'] = 'Product Rate';
                    $data['product_group'] = $product_group;
                    if(!empty($rate_type)){
                        $data['rate_type'] = $rate_type;
                    }
                    if(!empty($rate_between) && !empty($rate_type)){
                        $data['rate_between'] = $rate_between;
                    }
                }

                if($data['report_case'] == 'po_list'){
                    $data['key'] = 'po_list';
                    $data['page_title'] = 'Purchase Order List';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                }

                
                if($data['report_case'] == 'top_sale_qty_barcode_wise'){
                    $list = '';
                    $data['key'] = 'top_sale_qty_barcode_wise';
                    $data['page_title'] = 'Top Sales Quantity Barcode Wise';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['orderby'] = !empty($request->top_sale_qty_barcode_wise_orderby)?$request->top_sale_qty_barcode_wise_orderby:"";
                    $data['filter_qty'] = !empty($request->top_sale_qty_barcode_wise_product_qty)?$request->top_sale_qty_barcode_wise_product_qty:"";
                    $data['filter_qty_val'] = !empty($request->top_sale_qty_barcode_wise_product_qty_val)?$request->top_sale_qty_barcode_wise_product_qty_val:"";
                    $data['filter_amount'] = !empty($request->top_sale_qty_barcode_wise_product_amount)?$request->top_sale_qty_barcode_wise_product_amount:"";
                    $data['filter_amount_val'] = !empty($request->top_sale_qty_barcode_wise_product_amount_val)?$request->top_sale_qty_barcode_wise_product_amount_val:"";
                    $data['product_group'] = $product_group;
                }

                if($data['report_case'] == 'product_and_group_activity'){
                    $list = '';
                    $data['key'] = 'product_and_group_activity';
                    $data['page_title'] = 'Product And Group Activity';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['product'] = $product_id;
                    $data['product_group'] = $product_group;
                }

                if($data['report_case'] == 'product_parent_group_wise_sale'){
                    $list = '';
                    $data['key'] = 'product_parent_group_wise_sale';
                    $data['page_title'] = 'Parent Group Wise Sale';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['product'] = $product_id;
                    $data['product_group'] = $product_group;
                }
                
                if($data['report_case'] == 'month_wise_product_group_sale'){
                    $list = '';
                    $data['key'] = 'month_wise_product_group_sale';
                    $data['page_title'] = 'Month Wise Product Group Sale';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    //$data['product'] = $product_id;
                    $data['product_group'] = $product_group;
                    $data['product_sub_group'] = isset($request->product_sub_group)?1:0;
                    $data['supplier_ids'] = $supplier_ids;
                }
                if($data['report_case'] == 'monthly_sale_pur_summary'){
                    $list = '';
                    $data['key'] = 'monthly_sale_pur_summary';
                    $data['page_title'] = 'Monthly Sales & Purchase Summary';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                }

                if($data['report_case'] == 'slow_moving_stock'){
                    $list = '';
                    $data['key'] = 'slow_moving_stock';
                    $data['page_title'] = 'Slow Moving Items';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['product_group'] = $product_group;
                    $data['movement'] = $request->reorder_order;

                    $data['subQuery'] = $this->getDynamicQueryForSlowStock($request->toArray());
                }

                if($data['report_case'] == 'stock-with-average-cost'){
                    $list = '';
                    $data['key'] = 'stock-with-average-cost';
                    $data['page_title'] = 'Stock & Average Cost Rate';
                    $data['date'] = date('Y-m-d', strtotime($date));
                    $data['product'] = $product_id;
                }

                if($data['report_case'] == 'supplier_wise_sale'){
                    $data['key'] = 'supplier_wise_sale';
                    $data['page_title'] = 'Supplier Wise Sale';
                    $data['to_date'] = date('Y/m/d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y/m/d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $supplier_ids;
                }

                if($data['report_case'] == 'consumer-report'){
                    $list = '';
                    $data['key'] = 'consumer-report';
                    $data['page_title'] = 'Consumer Report';
                    $data['product_group'] = $product_group;
                }

                if($data['report_case'] == 'sales-and-cost-summary'){
                    $list = '';
                    $data['key'] = 'sales-and-cost-summary';
                    $data['page_title'] = 'Sales & Cost Report';
                    $data['to_date'] = date('Y/m/d', strtotime($to_date)); //for oracle db like 2020-04-16
                    $data['from_date'] = date('Y/m/d', strtotime($from_date)); //for oracle db like 2020-04-16
                }


                if($data['report_case'] == 'inventory_checklist'){
                    $list = '';
                    $data['key'] = 'inventory_checklist';
                    $data['page_title'] = 'Inventory Checklist';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['product'] = $product_id;
                    $data['product_group'] = $product_group;
                    $data['uom_list'] = $uom_list;
                    $data['store'] = $store;

                    $data['stock_quantity_filter_types'] = (isset($request->stock_quantity_filter_types) && $request->stock_quantity_filter_types != null && $request->stock_quantity_filter_types != "")?$request->stock_quantity_filter_types:"";
                    $data['stock_quantity_filter_types_val'] = (isset($request->stock_quantity_filter_types_val) && $request->stock_quantity_filter_types_val != null && $request->stock_quantity_filter_types_val != "")?$request->stock_quantity_filter_types_val:"";
                    $data['stock_value_filter_types'] = (isset($request->stock_value_filter_types) && $request->stock_value_filter_types != null && $request->stock_value_filter_types != "")?$request->stock_value_filter_types:"";
                    $data['stock_value_filter_types_val'] = (isset($request->stock_value_filter_types_val) && $request->stock_value_filter_types_val != null && $request->stock_value_filter_types_val != "")?$request->stock_value_filter_types_val:"";
                }

                if($data['report_case'] == 'total_product_activity_summary'){
                    $list = '';
                    $data['key'] = 'total_product_activity_summary';
                    $data['page_title'] = 'Total Product Activity Summary';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['product'] = $product_id;
                    $data['product_group'] = $product_group;
                }

                if($data['report_case'] == 'top_sale_products'){
                    $data['key'] = 'top_sale_products';
                    $data['page_title'] = 'Top Sale Products';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                }

                if($data['report_case'] == 'inventory_look_up'){
                    $data['key'] = 'inventory_look_up';
                    $data['page_title'] = 'Inventory Look Up';
                    $data['date'] = date('Y-m-d', strtotime($date));
                  
                    $data['product_ids'] = $new_code;//$product_ids;
                    $data['product_group'] = $product_group;
                    $data['supplier_ids'] = $supplier_ids;
                    $data['consolidate'] = $consolidate;
                }
                
                if($data['report_case'] == 'stock_report'){
                    $data['key'] = 'stock_report';
                    $data['page_title'] = 'Stock Report';
                    //$data['date'] = date('Y-m-d', strtotime($date));
                    
                    if($date_time_wise == 1){
                        $data['time_from'] = date('Y-m-d H:i', strtotime($date_time)); 
                    }else{
                        $data['date'] = date('Y-m-d', strtotime($date));
                    }
                    $data['date_time_wise'] = $date_time_wise;
                    $data['nagetivestock'] = $nagetivestock;
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $new_code;//$product_ids;
                    $data['product_group'] = $product_group;
                    $data['with_value_wise'] = $with_value_wise;
                }
                if($data['report_case'] == 'stock_audit'){
                    $data['key'] = 'stock_audit';
                    $data['page_title'] = 'Audit Stock Report';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                }
                if($data['report_case'] == 'dead_stock'){
                    $data['key'] = 'dead_stock';
                    $data['page_title'] = 'Dead Stock Report';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                    $data['dead_st'] = $dead_st;
                    $data['dead_days'] = $dead_days;

                    if(empty($data['dead_days']) || empty($dead_days) || $dead_days == 0){
                        return $this->jsonErrorResponse($data, 'Invalid Days', 422);
                    }

                    //$data['product_group'] = $product_group;
                }

                if($data['report_case'] == 'product_activity'){
                    $data['key'] = 'product_activity';
                    $data['page_title'] = 'Product Activity Report';
                    $data['product'] = $product_id;
                    $data['document_types'] = $document_types_multiple;

                    if($date_time_wise == 1){
                        $data['time_from'] = date('Y-m-d H:i', strtotime($date_from)); 
                        $data['time_to'] = date('Y-m-d H:i', strtotime($date_to)); 
                    }else{
                        $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                        $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    }
                    $data['date_time_wise'] = $date_time_wise;

                    $data['store']  = $store;
                    $data['month_wise']  = isset($request->month_wise)?true:false;
                }

                if($data['report_case'] == 'stock_activity_summary'){
                    $data['key'] = 'stock_activity_summary';
                    $data['page_title'] = 'Stock Activity Summary';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['date_opening_bal'] = date("Y-m-d", strtotime($data['from_date'] ." -1 day"));
                    /*$query = "select distinct s.business_id, s.company_id, s.branch_id,br.branch_name, s.sales_store_id, st.store_name, s.product_id, vp.product_name, s.product_barcode_id,vp.product_barcode_barcode,opening_stock,qty_in,qty_out,opening_stock + qty_in - qty_out balance
                            from (select distinct s.branch_id, s.sales_store_id, s.product_id, s.product_barcode_id, s.business_id, s.company_id, get_stock_current_qty_date ( s.product_id, s.product_barcode_id, s.business_id, s.company_id, s.branch_id, '', to_date('".$date_opening_bal."', 'yyyy/mm/dd')) opening_stock,
                            sum (s.qty_in) over (partition by s.branch_id, s.sales_store_id, s.product_id, s.product_barcode_id) qty_in,
                            sum (s.qty_out) over (partition by s.branch_id, s.sales_store_id, s.product_id, s.product_barcode_id)  qty_out
                            from vw_purc_stock_dtl s where (document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd')) and (".$clause_business_id . $clause_company_id . $clause_branch_id.")) s,
                             tbl_soft_branch br,tbl_defi_store st,vw_purc_product_barcode vp
                             where vp.base_barcode = 1 and s.branch_id = br.branch_id and s.sales_store_id = st.store_id(+) and s.product_barcode_id = vp.product_barcode_id and s.product_id = vp.product_id";
                    $list = DB::select($query);*/
                }

                if($data['report_case'] == 'item_stock_ledger'){
                    $data['key'] = 'item_stock_ledger';
                    $data['page_title'] = 'Item Stock Ledger';
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    if (!empty($product_id)) {
                        $data['product'] = TblPurcProduct::select('product_id','product_name')->where(DB::raw('lower(product_name)'),$this->strLower($product_id))->first();
                    }
                    if(empty($data['product']) || empty($product_id)){
                        return $this->jsonErrorResponse($data, 'Product Not Found', 422);
                    }
                    /*$query = "SELECT DISTINCT 1 data_priority, -- opening Bal
                        NULL DOCUMENT_DATE,NULL DOCUMENT_CODE,NULL DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,0 QTY_IN,0 RATE_IN,0 AMOUNT_IN,0 QTY_OUT,0 RATE_OUT,0 AMOUNT_OUT,
                        GET_STOCK_CURRENT_QTY_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,SALES_STORE_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) BALANCE_QTY,
                        GET_STOCK_AVG_RATE_ON_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) avg_rate,
                        GET_STOCK_AVG_RATE_ON_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,to_date('".$data['date']."', 'yyyy/mm/dd'))* GET_STOCK_CURRENT_QTY_DATE (PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,SALES_STORE_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) BALANCE_AMOUNT,0 RATE_EFFECT
                        FROM VW_PURC_STOCK_DTL WHERE  product_id = '".$product_id."'
                        UNION ALL SELECT DISTINCT 2 data_priority,DOCUMENT_DATE,DOCUMENT_CODE,DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,QTY_IN,STOCK_RATE rate_in,QTY_IN * STOCK_RATE AMOUNT_IN,0 QTY_OUT,0 RATE_OUT,0 amount_out,0 BALANCE_QTY,0 AVG_RATE,0 BALANCE_AMOUNT,RATE_EFFECT
                        FROM VW_PURC_STOCK_DTL WHERE STOCK_CALCULATION_EFFECT = '+' AND document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') AND product_id = '".$product_id."'
                        UNION ALL SELECT DISTINCT 2 data_priority,DOCUMENT_DATE,DOCUMENT_CODE,DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,0 QTY_IN,0 RATE_IN,
                        0 amount_in,QTY_OUT,0 RATE_out,0 amount_out,0 BALANCE_QTY,0 average_rate,0 BALANCE_AMOUNT,RATE_EFFECT
                        FROM VW_PURC_STOCK_DTL WHERE STOCK_CALCULATION_EFFECT = '-' AND document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') AND product_id = '".$product_id."' order by  data_priority,document_date ";

                    $list = DB::select($query);*/
                }
                if($data['report_case'] == 'final_price_update'){
                    $data['key'] = 'final_price_update';
                    $data['page_title'] = 'Final Price Update';
                    $data['date'] = date('Y-m-d',(strtotime ( '-1 day' , strtotime ($from_date) ) ));
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['product_group'] = $product_group;
                    /*if (empty($product_group)) {
                        return $this->jsonErrorResponse($data, 'Product group must be selected', 422);
                    }*/
                    $data['product_ids'] = $product_ids;
                    /*
                    if (!empty($product_id)) {
                        $data['product'] = TblPurcProduct::select('product_id','product_name')->where(DB::raw('lower(product_name)'),$this->strLower($product_id))->first();
                    }
                    if(empty($data['product']) || empty($product_id)){
                        return $this->jsonErrorResponse($data, 'Product Not Found', 422);
                    }
                    */
                }

                if($data['report_case'] == 'chart_account_list'){
                    $data['key'] = 'chart_account_list';
                    $data['page_title'] = 'Chart of Account List';
                }

                if($data['report_case'] == 'stock_detail_document_wise'){
                    $list = '';
                    $data['key'] = 'stock_detail_document_wise';
                    $data['page_title'] = 'Stock Detail Document Wise';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['date_opening_bal'] = date("Y-m-d", strtotime($data['from_date'] ." -1 day"));

                    $document_type_query = "select tbl_soft_company.business_id,'OS' field_value,'Opening Stock' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'GRN' field_value,'GRN' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'PR' field_value,'GRN Ret' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'POS' field_value,'POS' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'POSR' field_value,'POS Ret' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'SR' field_value,'Sale Return' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'SI' field_value,'Sale Invoice' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'ST' field_value,'Stock Trans' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'STR' field_value,'Stock Rec' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'SA' field_value,'Stock Adj' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'EI' field_value,'Exp' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'SP' field_value,'Sampl' field_heading from tbl_soft_company where rownum = 1 union all
                    select tbl_soft_company.business_id,'DI' field_value,'Damag' field_heading from tbl_soft_company where rownum = 1";
                    $data['document_type'] = DB::select($document_type_query);
                    $types = '';
                    foreach ($data['document_type'] as $type){
                        $types .=   "'".$type->field_value."' ".$type->field_value.', ';
                    }
                    $data['types'] = rtrim($types, ", ");

                    /* $query = "select * from (select product_id,business_id,company_id,branch_id,document_type,
                                 get_stock_current_qty_date (product_id,'',business_id,company_id,branch_id,'',to_date('".$date_opening_bal."', 'yyyy/mm/dd')) opening_stock,qty_base_unit_value,
                                 0 closing_bal
                                 from vw_purc_stock_dtl s where document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') and (".$clause_business_id . $clause_company_id . $clause_branch_id."))
                                 pivot (sum (qty_base_unit_value) for document_type in (".$types.")) order by product_id";
                     $data['list_data'] = DB::select($query);*/
                }


                if($data['report_case'] == 'product_group_activity'){
                    $data['key'] = 'product_group_activity';
                    $data['page_title'] = 'Product Group Activity Report';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['product_group'] = $product_group;
                    if (empty($product_group)) {
                        return $this->jsonErrorResponse($data, 'Product group must be selected', 422);
                    }
                }
                if($data['report_case'] == 'business_reports_factors'){
                    $data['key'] = 'business_reports_factors';
                    $data['page_title'] = 'Business Reports Factors';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                }

                if($data['report_case'] == 'supplier_aging') {
                    $data['key'] = 'supplier_aging';
                    $data['page_title'] = 'Supplier Aging';
                    $data['date'] = date('Y-m-d', strtotime($date));
                    $data['account_ids'] = $chart_account_multiple;
                    if (empty($chart_account_multiple)) {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                }

                if($data['report_case'] == 'customer_aging') {
                    $data['key'] = 'customer_aging';
                    $data['page_title'] = 'Customer Aging';
                    $data['date'] = date('Y-m-d', strtotime($date));
                    $data['account_ids'] = $chart_account_multiple;
                    if (empty($chart_account_multiple)) {
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                }

                if($data['report_case'] == 'month_wise_account_summary'){
                    $data['key'] = 'month_wise_account_summary';
                    $data['page_title'] = 'Month Wise Account Summary';
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));//for oracle db like 2020-04-16
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));//for oracle db like 2020-04-16
                    $data['chart_account_id'] = $chart_account_id;
                    if(empty($chart_account_id)){
                        return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
                    }
                }
            }

            /* POS Sale Reports */
            $pos_reports = ['date_wise_sales_summary','date_wise_summarized_sale','gross_profit','gross_profit_first_level','month_wise_group_first_level',
            'gross_profit_last_level','gross_profit_item_detail'];
            if(in_array($data['report_case'],$pos_reports)){
                $list = [];
                if($data['report_case'] == 'date_wise_sales_summary'){
                    $data['key'] = 'date_wise_sales_summary';
                    $data['page_title'] = 'Date Wise Sales Summary';
                    $data['to_date_time'] = date('Y-m-d H:i:s', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['from_date_time'] = date('Y-m-d H:i:s', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['product_group'] = $product_group;
                    $data['product_ids'] = $new_code;
                    $data['supplier_ids'] = $supplier_id;
                }
                if($data['report_case'] == 'date_wise_summarized_sale'){
                    $data['key'] = 'date_wise_summarized_sale';
                    $data['page_title'] = 'Date Wise Summarized Sale';
                    $data['to_date_time'] = date('Y-m-d H:i:s', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['from_date_time'] = date('Y-m-d H:i:s', strtotime($date_from)); //for oracle db like 2020-04-16
                }
                if($data['report_case'] == 'gross_profit'){
                    $data['key'] = 'gross_profit';
                    $data['page_title'] = 'Gross Profit';
                    //$data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    //$data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $new_code;//$product_ids;
                    $data['product_group'] = $product_group;
                   
                }
                if($data['report_case'] == 'month_wise_group_first_level'){
                    $data['key'] = 'month_wise_group_first_level';
                    $data['page_title'] = 'Month Wise Product Group Sale';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    //$data['product'] = $product_id;
                    $data['product_group'] = $product_group;
                    $data['supplier_ids'] = $supplier_ids;
                }
                
                if($data['report_case'] == 'gross_profit_first_level'){
                    $data['key'] = 'gross_profit_first_level';
                    $data['page_title'] = 'Gross Profit';
                    //$data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    //$data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['supplier_ids'] = $request->report_supplier_ids;
                    $data['product_ids'] = $request->report_product_ids;
                    $data['product_group'] = $request->report_product_groups;
                }
                if($data['report_case'] == 'gross_profit_last_level'){
                    $data['key'] = 'gross_profit_last_level';
                    $data['page_title'] = 'Gross Profit';
                    //$data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    //$data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                   
                    if(empty($request->group_item_first_level)){
                        return $this->jsonErrorResponse($data, "Category not Found", 200);
                    }
                    $data['first_level'] = $request->group_item_first_level;
                    
                    $data['supplier_ids'] = $request->report_supplier_ids;
                    $data['product_ids'] = $request->report_product_ids;
                    $data['product_group'] = $request->report_product_groups;
                }
                if($data['report_case'] == 'gross_profit_item_detail'){
                    $data['key'] = 'gross_profit_item_detail';
                    $data['page_title'] = 'Gross Profit';
                    //$data['to_date'] = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
                    //$data['from_date'] = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                   if(empty($request->group_item_first_level)){
                        return $this->jsonErrorResponse($data, "Category not Found", 200);
                    }
                    $data['first_level'] = $request->group_item_first_level;
                    if(empty($request->group_item_last_level)){
                            return $this->jsonErrorResponse($data, "Category not Found", 200);
                    }
                    $data['last_level'] = $request->group_item_last_level;
                    $data['supplier_ids'] = $request->report_supplier_ids;
                    $data['product_ids'] = $request->report_product_ids;
                    $data['product_group'] = $request->report_product_groups;
                }
            }

            /* Purchase Reports */
            $purchase_reports = ['purchase_register','purchase_order_detail'];
            if(in_array($data['report_case'],$purchase_reports)){
                $list = [];
                if($data['report_case'] == 'purchase_register'){
                    $data['key'] = 'purchase_register';
                    $data['page_title'] = 'Purchase Register';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                    if(empty($specific_purchase_type)){
                        return $this->jsonErrorResponse($data, "Type is required", 200);
                    }
                    $data['specific_purchase_type'] = $specific_purchase_type;

                }
                if($data['report_case'] == 'purchase_order_detail'){
                    $data['key'] = 'purchase_order_detail';
                    $data['page_title'] = 'Purchase Order Detail';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                    $data['product_ids'] = $product_ids;
                }
            }

            // Sale Reports
            $sale_reports = ['payment_mode_wise_sale','product_price_comparison','sale_analysis',
            'monthly_sale_pur_summ','pos_session_short_and_excess','sale_report','category_wise_profit',
            'sub_category_wise_profit','product_wise_profit','reward_point_ledger','reward_point_summary','product_rate_list','central_rate_items',];

            if(in_array($data['report_case'],$sale_reports)){
                $list = [];
                if($data['report_case'] == 'payment_mode_wise_sale'){
                    $data['key'] = 'payment_mode_wise_sale';
                    $data['page_title'] = 'Payment Mode Wise Sale';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                }
                if($data['report_case'] == 'reward_point_ledger'){
                    $data['key'] = 'reward_point_ledger';
                    $data['page_title'] = 'Reward Point Ledger Detail';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['customer_ids'] = $customer_id;
                }
                if($data['report_case'] == 'reward_point_summary'){
                    $data['key'] = 'reward_point_summary';
                    $data['page_title'] = 'Reward Point Summary';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['customer_ids'] = $customer_id;
                }
                
                if($data['report_case'] == 'product_rate_list'){
                    $data['key'] = 'product_rate_list';
                    $data['page_title'] = 'Product Rate List';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                    //$data['greater_than_net_tp'] = $greater_than_net_tp;
                    $data['diff_perc'] = $diff_perc;
                    $data['activity_check'] = $activity_check;
                    $data['product_status'] = $product_status;
                }
                if($data['report_case'] == 'monthly_sale_pur_summ'){
                    $data['key'] = 'monthly_sale_pur_summ';
                    $data['page_title'] = 'GROSS PROFIT STORE WISE';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    $data['supplier_ids'] = $supplier_ids;
                }
                if($data['report_case'] == 'product_price_comparison'){
                    $data['key'] = 'product_price_comparison';
                    $data['page_title'] = 'Product Price Comparison';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                }
                if($data['report_case'] == 'sale_analysis'){
                    $data['key'] = 'sale_analysis';
                    $data['page_title'] = 'Sale Analysis';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['product_ids'] = $new_code;
                    $data['product_group'] = $product_group;
                    $data['supplier_ids'] = $supplier_ids;
                }
                if($data['report_case'] == 'pos_session_short_and_excess'){
                    $data['key'] = 'pos_session_short_and_excess';
                    $data['page_title'] = 'POS Short/Excess Activity';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['users'] = $users_ids;
                    //$data['product_group'] = $product_group;
                    //$data['supplier_ids'] = $supplier_ids;
                }

                if($data['report_case'] == 'sale_report'){
                    $data['key'] = 'sale_report';
                    $data['page_title'] = 'Sale Report';
                    $data['date_time_to'] = date('Y-m-d H:i', strtotime($date_to)); //for oracle db like 2020-04-16
                    $data['date_time_from'] = date('Y-m-d H:i', strtotime($date_from)); //for oracle db like 2020-04-16
                    $data['product_group'] = $product_group;
                    $data['product_ids'] = $new_code;
                    $data['customer_ids'] = $customer_id;
                    $data['supplier_ids'] = $supplier_id;
                }

                if($data['report_case'] == 'category_wise_profit'){
                    $data['key'] = 'category_wise_profit';
                    $data['page_title'] = 'Category Wise Profit';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                }

                if($data['report_case'] == 'sub_category_wise_profit'){
                    $data['key'] = 'sub_category_wise_profit';
                    $data['page_title'] = 'Sub Category Wise Profit';
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    if(empty($request->group_item_first_level)){
                        return $this->jsonErrorResponse($data, "Category not Found", 200);
                    }
                    $data['first_level'] = $request->group_item_first_level;
                }

                if($data['report_case'] == 'product_wise_profit'){
                    $data['key'] = 'product_wise_profit';
                    $data['page_title'] = 'Product Wise Profit';
                    
                    $data['to_date'] = date('Y-m-d', strtotime($to_date));
                    $data['from_date'] = date('Y-m-d', strtotime($from_date));
                    if(empty($request->group_item_first_level)){
                        return $this->jsonErrorResponse($data, "Category not Found", 200);
                    }
                    $data['first_level'] = $request->group_item_first_level;
                    if(empty($request->group_item_last_level)){
                            return $this->jsonErrorResponse($data, "Category not Found", 200);
                    }
                    $data['last_level'] = $request->group_item_last_level;
                }
                
                if($data['report_case'] == 'central_rate_items'){
                    $data['key'] = 'central_rate_items';
                    $data['page_title'] = 'Central Rate Items';

                    //$data['product_ids'] = $product_ids;
                    $data['product_group'] = $product_group;
                }


            }

            $inventoryCase = ['opening_stock','stock_transfer','stock_adjustment',
                'stock_receiving','expired_items','sample_items','damaged_items',];
            if(in_array($data['report_case'],$inventoryCase)){

                $data['to_date'] = date('Y-m-d', strtotime($to_date));
                $data['from_date'] = date('Y-m-d', strtotime($from_date));
                
                $getdata = DB::table('vw_inve_stock')->whereIn('branch_id', $data['branch_ids'])
                    ->whereBetween('stock_date',[$data['from_date'],$data['to_date']]);
                
                if($data['report_case'] == 'opening_stock'){
                    $data['key'] = 'opening_stock';
                    $data['page_title'] = 'Opening Stock Report';
                    $getdata = $getdata->where('stock_code_type','like','os');
                    if(!empty($store)){
                        $getdata = $getdata->whereIn('stock_store_from_id',$store);
                    }

                }
                if($data['report_case'] == 'stock_transfer'){
                    $data['key'] = 'stock_transfer';
                    $data['page_title'] = 'Stock Transfer Report';
                    $getdata = $getdata->where('stock_code_type','like','st');
                    if(!empty($data['all_branches'])){
                        $getdata = $getdata->whereIn('stock_branch_to_id',$data['all_branches']);
                    }
                    if(!empty($product_group)){
                        $getdata = $getdata->whereIn('group_item_parent_id',$product_group);
                    }
                    
                    $data['product_group'] = $product_group;
                }
                if($data['report_case'] == 'stock_adjustment'){
                    $data['key'] = 'stock_adjustment';
                    $data['page_title'] = 'Stock Adjustment Report';
                    $getdata = $getdata->where('stock_code_type','like','sa');
                }
                if($data['report_case'] == 'expired_items'){
                    $data['key'] = 'expired_items';
                    $data['page_title'] = 'Expired Items Report';
                    $getdata = $getdata->where('stock_code_type','like','ei');
                }
                if($data['report_case'] == 'sample_items'){
                    $data['key'] = 'sample_items';
                    $data['page_title'] = 'Sample Items Report';
                    $getdata = $getdata->where('stock_code_type','like','si');
                }
                if($data['report_case'] == 'damaged_items'){
                    $data['key'] = 'damaged_items';
                    $data['page_title'] = 'Damaged Items Report';
                    $getdata = $getdata->where('stock_code_type','like','di');
                }
                if($data['report_case'] == 'stock_receiving'){
                    $data['key'] = 'stock_receiving';
                    $data['page_title'] = 'Stock Receiving';
                    $getdata = $getdata->where('stock_code_type','like','str');
                    if(!empty($data['all_branches'])){
                        $getdata = $getdata->whereIn('stock_branch_from_id',$data['all_branches']);
                    }
                    if(!empty($product_group)){
                        $getdata = $getdata->whereIn('group_item_parent_id',$product_group);
                    }
                    if(!empty($supplier_id)){
                        $getdata = $getdata->where('supplier_id','like',$supplier_id);
                    }
                    if(!empty($product_id)){
                        $getdata = $getdata->where('product_id','like',$product_id);
                    }
                    $data['product_group'] = $product_group;
                    $data['supplier_ids'] = $supplier_id;
                    if (!empty($product_id)) {
                        $data['product'] = TblPurcProduct::select('product_id','product_name')->where(DB::raw('lower(product_id)'),$this->strLower($product_id))->first();
                    }
                }
                
                $getdata = $getdata->orderby('stock_date')->orderby('stock_code')->get();
                //dd($getdata);
                $list = [];
                foreach ($getdata as $row)
                {
                    $today = date('Y-m-d', strtotime($row->stock_date));
                    $list[$today][$row->stock_code][] = $row;
                }
            }

            $data['list'] = $list;

            session(['data' => $data]);
            $dataJs['url'] = route( 'reports.view_report');
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

        $dataJs['redirect'] =  ''; // $data['report_case'];

        return $this->jsonSuccessResponse($dataJs, trans('message.report_ready'), 200);
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

    public function getDynamicQueryForSlowStock($request){
        $subQry = "";
        if(isset($request['outer_filterList'])){
            foreach ($request['outer_filterList'] as $outer_filterList){
                $outer_clause = !empty($outer_filterList['outer_clause'])?$outer_filterList['outer_clause']:"";
                $makeInnerQry = false;
                $subQry .= $outer_clause." (";
                foreach ($outer_filterList['inner_filterList'] as $inner_filterList){
                    $key = isset($inner_filterList['key'])?$inner_filterList['key']:"";
                    $key_type = isset($inner_filterList['key_type'])?$inner_filterList['key_type']:"";
                    $condition = isset($inner_filterList['conditions'])?$inner_filterList['conditions']:"";
                    $vals = isset($inner_filterList['val'])?$inner_filterList['val']:"";
                    $inner_clause = "";
                    if(!empty($key) && !empty($key_type) && !empty($condition) && $vals != ""){
                        if(isset($inner_filterList['inner_clause_item']) && count($outer_filterList['inner_filterList']) > 1){
                            $inner_clause = $inner_filterList['inner_clause_item'];
                        }
                        if($key_type == 'number' || $key_type == 'float'){
                            if($condition == 'between'){
                                $subQry .= "$key $condition '".$vals."' AND '".$inner_filterList['val_to']."' ";
                            }else{
                                $subQry .= "$key $condition '".$vals."' ";
                            }
                        }
                        $makeInnerQry = true;
                        $subQry .= $inner_clause;
                    }

                }
                if($makeInnerQry == false){
                    $subQry = rtrim($subQry, $outer_clause." (");
                    $subQry .= " ";
                }else{
                    $subQry .= ") ";
                }
            }
        }

        if( $subQry == "" ) return "";
        return $subQry;
    }

    public function reportList($case_name)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title . ' List';
        $data['page_data']['path_index'] = '';
        if(isset($id)){
            /*if(UserReportController::where('reporting_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
            }else{
                abort('404');
            }*/
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['page_data']['type'] = '';
            $data['page_data']['action'] = '';
        }
        if($case_name == 'purchase'){
            $menu_dtl_id = 49;
        }
        if($case_name == 'sale'){
            $menu_dtl_id = 70;
        }
        if($case_name == 'accounts'){
            $menu_dtl_id = 15;
        }
        if($case_name == 'inventory'){
            $menu_dtl_id = 75;
        }
        $data['case_name'] = $case_name;
        $data['list'] = TblSoftReports::where('report_entry_status',1)
            ->where('parent_menu_id',$menu_dtl_id)->orderBy('report_sr_no')->get();

        return view('reports.list', compact('data'));
    }

    public function ViewReport(){
        $data = Session::get('data');

        /***
         *  Dynamic Report
         ********/

        if($data['report_type'] == 'dynamic'){
            if($data['report_listing_type'] == 'listing'){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.dynamic_reports.listing_report', compact('data'))->render();
                }else{
                    return view('reports.dynamic_reports.listing_report');
                }
            }
            if($data['report_listing_type'] == 'listing_group'){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.dynamic_reports.listing_report_group', compact('data'))->render();
                }else{
                    return view('reports.dynamic_reports.listing_report_group');
                }
            }
            if($data['report_listing_type'] == 'tabular_report'){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.dynamic_reports.tabular_report', compact('data'))->render();
                }else{
                    return view('reports.dynamic_reports.tabular_report');
                }
            }
        }
        /***
         *  Staic Report
         ********/
        if($data['report_type'] == 'static'){
            /***
             *  General Report
             ********/
            $report_cases = ['slow_moving_stock','stock-with-average-cost','combine_ledger_group_wise','sales-and-cost-summary','consumer-report','supplier_wise_sale','inventory_checklist','total_product_activity_summary','product_and_group_activity','top_sale_qty_barcode_wise','supplier_wise_rebate_calc','inventory_batch_expiry','closing_day','sale_type_wise','summary_of_daily_activity',
                'vouchers_list','sale_invoice','trial_balance','activity_trial','date_wise_summarized','temp_accounting_ledger','accounting_ledger','grn_list',
                'top_sale_products','stock_report','inventory_look_up','stock_activity_summary','item_stock_ledger',
                'chart_account_list','stock_detail_document_wise','supplier_list','customer_rpt',
                'po_list','product_rate','bank_reconciliation','store_wise_stock','stock_valuation',
                'product_group_activity', 'product_activity','supplier_aging','account_notes',
                'profit_loss_statement','customer_aging','business_reports_factors',
                'month_wise_account_summary','vat_report','daily_purchase','supplier_wise_purchase_summary',
                'item_wise_purchase_summary','category_wise_purchase_analysis','invoice_wise_purchase_summary',
                'branch_wise_stock','product_list','product_change_rate','invoice_wise_sale_report','sale_register_report','sales_discount','invoice_wise_sales_discount','stock_audit',
                'branch_wise_stock_summary','group_wise_stock_activity_summary','cash_flow','final_price_update','payment_wht','frb_sales_data','dead_stock','hs_code','product_parent_group_wise_sale','month_wise_product_group_sale','product_pl','monthly_sale_pur_summary',];

            if(in_array($data['key'],$report_cases)){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.static_reports.'.$data['key'], compact('data'))->render();
                }else{
                    return view('reports.static_reports.'.$data['key']);
                }
            }

            /***
             *  Inventory Stock Report
             ********/

            $inventory_stock_keys = ['opening_stock','stock_transfer','stock_adjustment',
                'stock_receiving','expired_items','sample_items','damaged_items',];

            if(in_array($data['key'],$inventory_stock_keys)){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.static_reports.inventory_stock', compact('data'))->render();
                }else{
                    return view('reports.static_reports.inventory_stock');
                }
            }
            /***
             *  Sale POS Report
             ********/

            $inventory_stock_keys = ['date_wise_sales_summary','date_wise_summarized_sale','gross_profit','gross_profit_first_level','month_wise_group_first_level',
                'gross_profit_last_level','gross_profit_item_detail',];

            if(in_array($data['key'],$inventory_stock_keys)){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.static_reports.pos.'.$data['key'], compact('data'))->render();
                }else{
                    return view('reports.static_reports.pos.'.$data['key']);
                }
            }
            /***
             *  Purchase Report
             ********/

            $purchase_keys = ['purchase_register','purchase_order_detail'];

            if(in_array($data['key'],$purchase_keys)){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.static_reports.purchase.'.$data['key'], compact('data'))->render();
                }else{
                    return view('reports.static_reports.purchase.'.$data['key']);
                }
            }
            /***
             *  Sale Report
             ********/

            $sale_keys = ['payment_mode_wise_sale','product_price_comparison','sale_analysis','monthly_sale_pur_summ'
            ,'pos_session_short_and_excess','sale_report','category_wise_profit',
            'sub_category_wise_profit','product_wise_profit','reward_point_ledger','reward_point_summary','product_rate_list','central_rate_items',];

            if(in_array($data['key'],$sale_keys)){
                if($data['form_file_type'] == 'pdf'){
                    $view = view('reports.static_reports.sale.'.$data['key'], compact('data'))->render();
                }else{
                    return view('reports.static_reports.sale.'.$data['key']);
                }
            }
        }

        /***
         *  Generate Report
         ********/
        if($data['form_file_type'] == 'pdf'){
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
        }
    }

    public static function notifyClosingDayReportStatus(){
        $date = date('Y-m-d');
        $exists = TblVerifyReports::where('report_name' , 'closing_day')->where('report_date' , $date)->exists();
        if(!$exists){
            WhatsAppApiController::sendWhatsAppText('Closing Day Report Of ' . $date . ' Is Not Verified.' , '96899542253');
        }
    }

    public function verifyClosingDayReport(Request $request){
        $data = $dates = [];
        $sessionData = Session::get('data');

        $validator = Validator::make($request->all() , [
            'from_date'     => 'required|date',
            'to_date'       => 'required|date',
            'branch_ids'    => 'required',
            'reportName'    => 'required'
        ]);

        if($validator->fails()){
            return $this->jsonErrorResponse([] , 'Something went wrong!' , 403);
        }
        if($sessionData['report_case'] != $request->reportName){
            return $this->jsonErrorResponse([] , 'Something went wrong!' , 403);
        }

        $fromDate   = new DateTime($request->from_date);
        $toDate     = new DateTime($request->to_date);
        $branches   = json_decode(htmlspecialchars_decode($request->branch_ids));

        for($i = $fromDate; $i <= $toDate; $i->modify('+1 day')){
            array_push($dates , $i->format('Y-m-d'));
        }

        DB::beginTransaction();
        try{
            foreach ($branches as $branch) {
                foreach ($dates as $dt) {
                    $exist = TblVerifyReports::where('report_name' , $request->reportName)
                        ->where('branch_id' , $branch)
                        ->where('report_date' , $dt);
                    if($exist->exists()){
                        $report = $exist->first();
                    }else{
                        $report = new TblVerifyReports();
                        $report->verify_reports_id = Utilities::uuid();
                        $report->report_name = $request->reportName;
                    }
                    $report->report_date = $dt;
                    $report->user_id = auth()->user()->id;
                    $report->report_status = 'VERIFIED';
                    $report->branch_id = $branch;
                    $report->save();
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
        return $this->jsonSuccessResponse($data, 'Report Verified!', 200);

    }

    public function exportStaticReportListing($data,$fieldsKeys)
    {
        /**************
        $fieldsKeys = [
            'Branch Name',
            'Store Name',
            'Location Name',
        ];
        $list = \Illuminate\Support\Facades\DB::select($data['qry']);
        return $this->exportStaticReportListing($list,$fieldsKeys);
        ********/

        return Excel::download(new \App\Exports\BladeExport($data,$fieldsKeys), 'report.xlsx');
    }
    public function exportInventoryGrouping($key,$list)
    {
        $fieldsKeys = [];
        $fieldsKeys[] = 'Store';
        if($key == 'stock_transfer'){
            $fieldsKeys[] = 'Branch From';
            $fieldsKeys[] = 'Branch To';
        }
        if($key == 'stock_receiving'){
            $fieldsKeys[] = 'Receiving From';
            $fieldsKeys[] = 'Receiving To';
        }
        $fieldsKeys[] = 'Barcode';
        $fieldsKeys[] = 'Product Name';
        $fieldsKeys[] = 'UOM';
        $fieldsKeys[] = 'Packing';
        if($key == 'opening_stock'){
            $fieldsKeys[] = 'Production Date';
            $fieldsKeys[] = 'Expiry Date';
        }
        if($key == 'stock_adjustment'){
            $fieldsKeys[] = 'Stock Qty';
            $fieldsKeys[] = 'Physical Stock Qty';
        }
        $fieldsKeys[] = 'Batch No';
        $fieldsKeys[] = 'Quantity';
        if($key != 'stock_adjustment'){
            $fieldsKeys[] = 'Rate';
            $fieldsKeys[] = 'Amount';
        }
        $data = [];
        foreach($list as $kgs=>$groups){
            $r = [];
            if($key == 'opening_stock' || $key == 'stock_transfer' || $key == 'stock_receiving'){
                $r[] = $kgs;
                for ($i=0;$i<11;$i++){
                    $r[] = '';
                }
                array_push($data,$r);
            }
            if($key == 'stock_adjustment' || $key == 'expired_items' || $key == 'sample_items' || $key == 'damaged_items'){
                $r[] = $kgs;
                for ($i=0;$i<9;$i++){
                    $r[] = '';
                }
                array_push($data,$r);
            }
            foreach($groups as $kg=>$group){
                $rr = [];
                if($key == 'opening_stock' || $key == 'stock_transfer' || $key == 'stock_receiving'){
                    $rr[] = $kg;
                    for ($i=0;$i<11;$i++){
                        $rr[] = '';
                    }
                }
                if($key == 'stock_adjustment' || $key == 'expired_items' || $key == 'sample_items' || $key == 'damaged_items'){
                    $rr[] = $kg;
                    for ($i=0;$i<9;$i++){
                        $rr[] = '';
                    }
                }
                array_push($data,$rr);
                foreach($group as $item){
                    $dr = [];
                    $dr[] = $item->stock_store_from_name;
                    if($key == 'stock_transfer'){
                        $dr[] = $item->stock_branch_from_name;
                        $dr[] = $item->stock_branch_to_name;
                    }
                    if($key == 'stock_receiving'){
                        $dr[] = $item->stock_branch_from_name;
                        $dr[] = $item->stock_branch_to_name;
                    }
                    $dr[] = $item->product_barcode_barcode;
                    $dr[] = $item->product_name;
                    $dr[] = $item->uom_name;
                    $dr[] = $item->stock_dtl_packing;
                    if($key == 'opening_stock'){
                        if(!empty($item->stock_dtl_production_date)){
                            $dr[] = date('d-m-Y', strtotime($item->stock_dtl_production_date));
                        }else{
                            $dr[] = "";
                        }
                        if(!empty($item->stock_dtl_production_date)){
                            $dr[] = date('d-m-Y', strtotime($item->stock_dtl_expiry_date));
                        }else{
                            $dr[] = "";
                        }
                    }
                    if($key == 'stock_adjustment'){
                        $dr[] = $item->stock_dtl_stock_quantity;
                        $dr[] = $item->stock_dtl_physical_quantity;
                    }
                    $dr[] = $item->stock_dtl_batch_no;
                    $dr[] = number_format($item->stock_dtl_quantity);
                    if($key != 'stock_adjustment'){
                        $dr[] = number_format($item->stock_dtl_rate,3);
                        $dr[] = number_format($item->stock_dtl_amount,3);
                    }
                    array_push($data,$dr);
                }
            }
        }
        return Excel::download(new BladeExport($data,$fieldsKeys), 'report.xlsx');
    }
}
