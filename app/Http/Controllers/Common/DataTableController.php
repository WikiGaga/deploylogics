<?php

namespace App\Http\Controllers\Common;

use App\Models\TblAccCoa;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblPurcProduct;
use App\Models\TblPurcSupplier;
use App\Models\TblPurcGroupItem;
use App\Models\TblSoftFormCases;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\TblPurcProductBarcode;
use App\Models\TblInveItemFormulation;
use App\Models\TblPurcProductBarcodeDtl;
use Illuminate\Support\Facades\Response;
use App\Models\ViewPurcProductBarcodeHelp;
use Illuminate\Support\Facades\Cookie;

class DataTableController extends Controller
{
    public function index(Request $request,$caseType){
        $formCase = TblSoftFormCases::where('form_cases_casename',$caseType)->firstOrFail();

        $data['case'] = $caseType;
        $data['menu_dtl_id'] = $formCase['menu_dtl_id'];
        $data['title'] = $formCase['form_cases_title'];
        $listing_view_type = $formCase['form_cases_listing_view_type'];
        $data['path-form'] = $caseType.'/form';
        $data['path'] = $caseType;

        $headings = explode(',', $formCase['form_cases_heading']);
        $data['headings'] = $headings;

        $columnName = explode(',', $formCase['form_cases_column_name']);
        $data['columnName'] = $columnName;
        $data['table_colums'] = [];
        for($i=0;$i<count($data['headings']);$i++){
            $data['table_colums'][$data['columnName'][$i]] = $data['headings'][$i];
        }
        if($request->ajax()) {
            $modelName = 'App\Models\\' .$formCase['form_cases_modelname'];
            $data['primaryKeyName'] = $modelName::primaryKeyName();
            $dataSql = $modelName::where($data['primaryKeyName'], '<>', '0');

            if($caseType == 'demand-approve'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('demand_approval_dtl_branch_id',auth()->user()->branch_id)
                    ->groupBy($columnName);
            }else if($caseType == 'demand'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->groupBy($columnName);
                //dd($dataSql->toSql());
            }else if($caseType == 'purchase-order'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->groupBy($columnName);
                //dd($dataSql->toSql());
            }else if($caseType == 'grn'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->where('grn_type','GRN')
                    ->groupBy($columnName);
            }else if($caseType == 'pr'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->where('grn_type','PR')
                    ->groupBy($columnName);
            }else if($caseType == 'sales-invoice'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->where('sales_type','SI')
                    ->groupBy($columnName);
            }else if($caseType == 'sale-return'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->where('sales_type','SR')
                    ->groupBy($columnName);
            }else if($caseType == 'budget'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->groupBy($columnName);
            }else if($caseType == 'lpo'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id)
                    ->groupBy($columnName);
            }else if($listing_view_type == 'branch'){
                $dataSql = $modelName::where($data['primaryKeyName'], '<>', '0')
                    ->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id);
            }else{
                $dataSql = $modelName::where($data['primaryKeyName'], '<>', '0')
                    ->where('business_id',auth()->user()->business_id);
            }
            if($caseType == 'business'){
                $dataSql = $modelName::where($data['primaryKeyName'], '<>', '0');
            }
            if (isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch'])) {
                $dataSql = $modelName::where(DB::raw('lower('.$data['columnName'][0].')'), 'like', '%' . strtolower($request['query']['generalSearch']) . '%');
                for($i=1;$i<count($data['columnName']);$i++){
                    $dataSql->OrWhere(DB::raw('lower('.$data['columnName'][$i].')'), 'like', '%' . strtolower($request['query']['generalSearch']) . '%');
                }

            }
           // dd($dataSql->toSql());
            $sortDirection = ($request->has('sort.sort') && $request->filled('sort.sort')) ? $request->input('sort.sort') : 'asc';
            $sortField = ($request->has('sort.field') && $request->filled('sort.field')) ? $request->input('sort.field') : '';

            $meta = [];
            $page = ($request->has('pagination.page') && $request->filled('pagination.page')) ? $request->input('pagination.page') : 1;
            $perpage = ($request->has('pagination.perpage') && $request->filled('pagination.perpage')) ? $request->input('pagination.perpage') : -1;

            $total = $dataSql->count();
            // $perpage 0; get all data
            if ($perpage > 0) {
                $pages = ceil($total / $perpage); // calculate total pages
                $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $offset = ($page - 1) * $perpage;
                if ($offset < 0) {
                    $offset = 0;
                }

                //$data = array_slice($data, $offset, $perpage, true);
            }

            $limit = 50;
            /*if (isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch']))
            {
                $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take(100)->get();
                //dd($data['columnName']);
                //dd($entries->toArray());
                $generalSearch = $request['query']['generalSearch'];
                $entries = collect($entries)->filter(function ($item) use ($data,$generalSearch) {
                   // return false !== stristr($item->group_item_name, $generalSearch);
                    for($i=0;$i<count($data['columnName']);$i++){
                        $col = $data['columnName'][$i];
                        $fil = false !== stristr($item->$col, $generalSearch);
                        if($fil){
                            return true;
                        }
                        if(count($data['columnName'])-1 == $i){
                            if($fil){
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }
                });
                $entries->take($limit)->all();
            }else{
                $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take($limit)->get();
            }*/

            $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take($limit)->get();
           // $entries = $dataSql->skip($offset)->take($limit)->get();
            $meta = [
                'page' => $page,
               // 'pages' => $pages,
                'perpage' => $perpage,
                //'total' => $total,
            ];
            $result = [
                'meta' => $meta + [
                        'sort' => $sortDirection,
                        'field' => $sortField,
                    ],
                'keyid'=>$data['primaryKeyName'],
                'data' => $entries,
                'statuses' => 'success'
            ];
            return response()->json($result);
        }
        return view('common.new',compact('data'));
    }

    public function help_old(Request $request,$helpType){

        $formCase = TblSoftFormCases::where('form_cases_casename',$helpType)->firstOrFail();
        $data['caseType'] = $helpType;
        $listing_view_type = $formCase['form_cases_listing_view_type'];
        $data['title'] = $formCase['form_cases_title'];
        $orderby = $formCase['form_cases_orderby'];
        $data['path'] = $helpType;
        $headings = explode(',', $formCase['form_cases_heading']);
        $data['headings'] = $headings;

        $columnName = explode(',', $formCase['form_cases_column_name']);
        $data['columnName'] = $columnName;
        $data['table_colums'] = [];
        for($i=0;$i<count($data['headings']);$i++){
            $data['table_colums'][$data['columnName'][$i]] = $data['headings'][$i];
        }
        $hiddenFields = explode(',', $formCase['form_cases_hidden_field']);
        $data['hiddenFields'] = $hiddenFields;
        if($request->ajax()) {
            $modelName = 'App\Models\\' .$formCase['form_cases_modelname'];
            $data['primaryKeyName'] = $modelName::primaryKeyName();
            $dataSql = '';

            $dataSql = DB::table('vw_purc_product');

            if (isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch'])) {
                for($i=1;$i<count($data['columnName']);$i++){
                    $dataSql->OrWhere(''.$data['columnName'][$i].'', 'like', '%' . $request['query']['generalSearch'] . '%');
                }
            }

           // dd($request->toArray());
            $sortDirection = ($request->has('sort.sort') && $request->filled('sort.sort')) ? $request->input('sort.sort') : 'asc';
            $sortField = ($request->has('sort.field') && $request->filled('sort.field')) ? $request->input('sort.field') : '';
            $meta = [];
            $page = ($request->has('pagination.page') && $request->filled('pagination.page')) ? $request->input('pagination.page') : 1;
            $perpage = ($request->has('pagination.perpage') && $request->filled('pagination.perpage')) ? $request->input('pagination.perpage') : -1;
            //dd($request->toArray());
            $total = $dataSql->count();
            // $perpage 0; get all data
            if ($perpage > 0) {
                $pages = ceil($total / $perpage); // calculate total pages
                $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $offset = ($page - 1) * $perpage;
                if ($offset < 0) {
                    $offset = 0;
                }

                //$data = array_slice($data, $offset, $perpage, true);
            }
            $limit = 200;

            $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take($limit)->get();

            $meta = [
                'page' => $page,
                // 'pages' => $pages,
                'perpage' => $perpage,
                //'total' => $total,
            ];
            $result = [
                'meta' => $meta + [
                        'sort' => $sortDirection,
                        'field' => $sortField,
                    ],
                'keyid'=>$data['primaryKeyName'],
                'data' => $entries,
                'statuses' => 'success'
            ];
            return response()->json($result);
        }

    }

    public function help(Request $request,$helpType){

        $formCase = TblSoftFormCases::where('form_cases_casename',$helpType)->firstOrFail();

        $data['caseType'] = $helpType;
        $listing_view_type = $formCase['form_cases_listing_view_type'];
        $data['title'] = $formCase['form_cases_title'];
        $orderby = $formCase['form_cases_orderby'];
        $data['path'] = $helpType;

        $headings = explode(',', $formCase['form_cases_heading']);
        $data['headings'] = $headings;

        $columnName = explode(',', $formCase['form_cases_column_name']);
        $data['columnName'] = $columnName;
        $data['table_colums'] = [];
        for($i=0;$i<count($data['headings']);$i++){
            $data['table_colums'][$data['columnName'][$i]] = $data['headings'][$i];
        }
        $hiddenFields = explode(',', $formCase['form_cases_hidden_field']);
        $data['hiddenFields'] = $hiddenFields;
        if($request->ajax()) {

            $modelName = 'App\Models\\' .$formCase['form_cases_modelname'];
            $data['primaryKeyName'] = $modelName::primaryKeyName();
            $dataSql = $modelName::where($data['primaryKeyName'], '<>', '0');

            if($helpType == 'lpoPoQuotationHelp'){
                $dataSql = $dataSql->where('lpo_dtl_generate_quotation',1)->orderBy($orderby);
            }
            if($helpType == 'demandApprovalHelp'){
                array_push($columnName,$modelName::primaryKeyName());
                $dataSql = $modelName::select($columnName)->groupBy($columnName)->orderBy('demand_approval_dtl_code');
            }
            if($helpType == 'demandApprovalHelp'){
                $dataSql = $dataSql->where('demand_approval_dtl_branch_id',auth()->user()->branch_id);
            }
            if($helpType == 'lpoPoHelp'){
                $dataSql = $dataSql->where('lpo_dtl_generate_lpo',1)->orderBy($orderby);
                /*
                 * add karny hai
                 * ->where('business_id',auth()->user()->business_id)
                ->where('branch_id',auth()->user()->branch_id);*/
            }
            if($listing_view_type == 'branch'){
                $dataSql = $dataSql->where('business_id',auth()->user()->business_id)
                    ->where('branch_id',auth()->user()->branch_id);
            }else{
                $dataSql = $dataSql->where('business_id',auth()->user()->business_id);
            }
            $sortDirection = ($request->has('sort.sort') && $request->filled('sort.sort')) ? $request->input('sort.sort') : 'asc';
            $sortField = ($request->has('sort.field') && $request->filled('sort.field')) ? $request->input('sort.field') : '';

            $meta = [];
            $page = ($request->has('pagination.page') && $request->filled('pagination.page')) ? $request->input('pagination.page') : 1;
            $perpage = ($request->has('pagination.perpage') && $request->filled('pagination.perpage')) ? $request->input('pagination.perpage') : -1;
            //dd($request->toArray());
            $total = $dataSql->count();
            // $perpage 0; get all data
            if ($perpage > 0) {
                $pages = ceil($total / $perpage); // calculate total pages
                $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $offset = ($page - 1) * $perpage;
                if ($offset < 0) {
                    $offset = 0;
                }

                //$data = array_slice($data, $offset, $perpage, true);
            }
            $limit = 50;
            /*if (isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch']))
            {
                $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take($total)->get();
                $generalSearch = $request['query']['generalSearch'];
                $entries = collect($entries)->filter(function ($item) use ($data,$generalSearch) {
                    // return false !== stristr($item->group_item_name, $generalSearch);
                    for($i=0;$i<count($data['columnName']);$i++){
                        $col = $data['columnName'][$i];
                        $fil = false !== stristr($item->$col, $generalSearch);
                        if($fil){
                            return true;
                        }
                        if(count($data['columnName'])-1 == $i){
                            if($fil){
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }
                });
                $entries->take($limit)->all();
            }else{
                $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take($limit)->get();
            }*/
            if (isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch'])) {
                $dataSql = $modelName::where(DB::raw('lower('.$data['columnName'][0].')'), 'like', '%' . strtolower($request['query']['generalSearch']) . '%');
                for($i=1;$i<count($data['columnName']);$i++){
                    $dataSql->OrWhere(DB::raw('lower('.$data['columnName'][$i].')'), 'like', '%' . strtolower($request['query']['generalSearch']) . '%');
                }
            }

            $entries = $dataSql->orderBy($sortField, $sortDirection)->skip($offset)->take($limit)->get();
           // $entries = $dataSql->skip($offset)->take($limit)->get();

            $meta = [
                'page' => $page,
                // 'pages' => $pages,
                'perpage' => $perpage,
                //'total' => $total,
            ];
            $result = [
                'meta' => $meta + [
                        'sort' => $sortDirection,
                        'field' => $sortField,
                    ],
                'keyid'=>$data['primaryKeyName'],
                'data' => $entries,
                'statuses' => 'success'
            ];
            return response()->json($result);
        }
       // return view('common.help_new',compact('data'));
    }

    public function helpOpen(Request $request,$helpType){
        $data['case'] = $helpType;
        $data['caseType'] = $helpType;
        $data['path'] = $helpType;

        if($helpType == 'productHelp'){
            $data['title'] = 'Product';
            $data['hiddenFields'] = ['product_id','product_barcode_id','uom_id'];
            $data['table_colums'] = [
                "product_code" => "Code",
                "product_name" => "Name",
                "product_arabic_name" => "Arabic Name",
                "product_barcode_barcode" => "Barcode",
                "uom_name" => "UOM",
                "product_barcode_packing" => "Packing",
                'stock' => 'Stock',
                'sale_rate' => 'Sale Rate',
                'cost_rate' => 'Cost Rate',
                'purchase_rate' => 'Purc. Rate',
                'last_tp' => 'Purc. LastTP',
                'vend_last_tp' => 'Purc. Vend LastTp',
            ];
        }
        if($helpType == 'productHelp'){
            $data['table_colums']['action'] = 'Action';
        }
        if($helpType == 'supplierHelp'){
            $data['title'] = 'Supplier';
            $data['hiddenFields'] = ['supplier_id'];
            $data['table_colums'] = [
                "supplier_code" => "Code",
                "supplier_name" => "Name",
                "supplier_address" => "Address",
                "supplier_phone_1" => "Phone",
                "supplier_reference_code" => "Reference Code"
            ];
        }
        if($helpType == 'poHelp'){
            $data['title'] = 'Purchase Order';
            $data['hiddenFields'] = ['purchase_order_id'];
            $data['table_colums'] = [
                "purchase_order_entry_date" => "Date",
                "purchase_order_code" => "Code",
                "supplier_name" => "Supplier Name",
            ];
        }
        if($helpType == 'demandApprovalHelp'){
            $data['title'] = 'Demand Approval';
            $data['hiddenFields'] = ['demand_approval_dtl_id'];
            $data['table_colums'] = [
                "demand_approval_dtl_date" => "Date",
                "demand_approval_dtl_code" => "Code",
                "demand_approval_dtl_approve_status" => "Status",
                "demand_codes" => "Demand Codes",
            ];
        }
        if($helpType == "pendingPR"){
            $data['title'] = 'Pending Purchase Return';
            $data['hiddenFields'] = ['grn_id'];
            $data['table_colums'] = [
                "supplier_name" => "Supplier Name",
                "grn_code" => "Code",
                "grn_date" => "Date",
                "pending_qty" => "Pending Qty",
            ];
        }
        return view('common.help_new',compact('data'));
    }
    public function modalHelpOpen(Request $request,$helpType){
        $data['case'] = $helpType;
        $str = "";
        if(isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch'])){
            $str = $request['query']['generalSearch'];
        }
        $data['list'] = [];
        if($helpType == 'productHelp'){
            $field = 'PRODUCT_NAME';
            $keyid = 'PRODUCT_BARCODE_ID';
            $listQry = "select P.PRODUCT_CODE,P.PRODUCT_ID, P.PRODUCT_NAME, P.PRODUCT_ARABIC_NAME, B.PRODUCT_BARCODE_ID, B.PRODUCT_BARCODE_BARCODE,B.PRODUCT_BARCODE_PACKING, B.UOM_ID, UOM.UOM_NAME  , END_USER_RATE SALE_RATE   ,
                            P_PUR_RATE.PRODUCT_BARCODE_COST_RATE  COST_RATE  , P_PUR_RATE.PRODUCT_BARCODE_PURCHASE_RATE  PURCHASE_RATE , P_PUR_RATE.LAST_TP  LAST_TP , P_PUR_RATE.SUPPLIER_LAST_TP  VEND_LAST_TP
                            from TBL_PURC_PRODUCT p
                            inner join TBL_PURC_PRODUCT_BARCODE b on B.PRODUCT_ID = P.PRODUCT_ID
                            inner join TBL_DEFI_UOM uom on UOM.UOM_ID = B.UOM_ID
                            LEFT OUTER JOIN VW_PURC_PRODUCT_RATE_COLUMN P_SALE_RATE
                            on b.PRODUCT_BARCODE_ID = P_SALE_RATE.PRODUCT_BARCODE_ID  and  P_SALE_RATE.BRANCH_ID = ".Auth::user()->branch_id."
                            LEFT OUTER JOIN TBL_PURC_PRODUCT_BARCODE_PURCH_RATE P_PUR_RATE
                            on b.PRODUCT_BARCODE_ID = P_PUR_RATE.PRODUCT_BARCODE_ID and  P_PUR_RATE.BRANCH_ID = ".Auth::user()->branch_id;
            if(!empty($str)){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $listQry .= " WHERE upper(B.product_barcode_barcode) Like '%".$replaced_str."%' OR
                        upper(P.product_name) like '%".$replaced_str."%'
                        order by
                        Case
                            WHEN upper(P.product_name) Like '".$str."' THEN 1
                            WHEN upper(P.product_name) Like '".$str."%' THEN 2
                            WHEN upper(P.product_name) Like '%".$str."' THEN 4
                            Else 3
                        END,P.product_name ";
            }
            $listQry .= " fetch first 50 rows only";

            //dd($listQry);

            $getData = DB::select($listQry);
            $data['list'] = [];
            //dd($getData);
            $now = new \DateTime("now");
            $today_format = $now->format("d-m-Y");
            $date = date('Y-m-d', strtotime($today_format));
            foreach ($getData as $list){
                $arr = [
                    $list->product_id,
                    $list->product_barcode_id,
                    auth()->user()->business_id,
                    auth()->user()->company_id,
                    auth()->user()->branch_id,
                    '',
                    $date
                ];
                $store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;
                $list->stock = $store_stock;

                // Suggested Qty
                $SuggestedDetail = TblPurcProductBarcodeDtl::where('product_barcode_id',$list->product_barcode_id)
                ->where('branch_id',auth()->user()->branch_id)->first(['product_barcode_shelf_stock_max_qty','product_barcode_stock_cons_day']);

                $maxLimit = isset($SuggestedDetail->product_barcode_shelf_stock_max_qty) ? $SuggestedDetail->product_barcode_shelf_stock_max_qty : 0;
                $consumption_days = isset($SuggestedDetail->product_barcode_stock_cons_day) ? $SuggestedDetail->product_barcode_stock_cons_day : 0;
                $list->suggestQty1 = Utilities::SuggestedQty1($maxLimit , $store_stock);
                $list->suggestQty2 = Utilities::SuggestedQty2($consumption_days , $store_stock , $list->product_id,auth()->user()->branch_id);

                /*
                * lpo qty
                */
                $lopqty_Qry1 = "select sum(pod.PURCHASE_ORDER_DTLQUANTITY) qty from tbl_purc_purchase_order po
                join tbl_purc_purchase_order_dtl pod on pod.PURCHASE_ORDER_ID = po.PURCHASE_ORDER_ID
                where pod.PRODUCT_BARCODE_ID = ".$list->product_barcode_id." and po.branch_id = ".auth()->user()->branch_id;

                $lopqty_1 = DB::selectOne($lopqty_Qry1);

                $lopqty_Qry2 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'GRN' and  grnd.PRODUCT_BARCODE_ID = ".$list->product_barcode_id." and grn.branch_id = ".auth()->user()->branch_id." and grn.PURCHASE_ORDER_ID IS NOT NULL";

                $lopqty_2 = DB::selectOne($lopqty_Qry2);

                $lopqty1 = (isset($lopqty_1->qty) && !empty($lopqty_1->qty))?$lopqty_1->qty:0;
                $lopqty2 = (isset($lopqty_2->qty) && !empty($lopqty_2->qty))?$lopqty_2->qty:0;

                /*
                * Purc. Ret in waiting qty
                */

                $waitingQty_Qry1 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'PRT' and grnd.PRODUCT_BARCODE_ID = ".$list->product_barcode_id." and grn.branch_id = ".auth()->user()->branch_id."";

                $waitingQty_1 = DB::selectOne($waitingQty_Qry1);

                $waitingQty_Qry2 = "select sum(grnd.TBL_PURC_GRN_DTL_QUANTITY) qty from tbl_purc_grn grn
                            join tbl_purc_grn_dtl grnd on grnd.GRN_ID = grn.GRN_ID
                            where grn.grn_type = 'PR' and grnd.PRODUCT_BARCODE_ID = ".$list->product_barcode_id." and grn.branch_id = ".auth()->user()->branch_id." and grn.PURCHASE_ORDER_ID != null";

                $waitingQty_2 = DB::selectOne($waitingQty_Qry2);

                $waitingQty1 = (isset($waitingQty_1->qty) && !empty($waitingQty_1->qty))?$waitingQty_1->qty:0;
                $waitingQty2 = (isset($waitingQty_2->qty) && !empty($waitingQty_2->qty))?$waitingQty_2->qty:0;

                $packing    = isset($list->product_barcode_packing) && !empty($list->product_barcode_packing)?$list->product_barcode_packing:1;

                $list->lpo_quantity = (int)(((float)$lopqty1 - (float)$lopqty2)/$packing);
                $list->purc_return_waiting_qty = (int)(((float)$waitingQty1 - (float)$waitingQty2)/$packing);

                $data['list'][] = (array)$list;
            }
        }
        if($helpType == 'supplierHelp'){
            $field = 'supplier_name';
            $keyid = 'supplier_id';
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            if(!empty($str)){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $where .= "and (upper(supplier_name) Like '%".$replaced_str."%' OR
                        upper(supplier_code) like '%".$replaced_str."%' OR
                        upper(supplier_reference_code) like '%".$replaced_str."%' )
                        order by
                        Case
                            WHEN upper(supplier_name) Like '".$str."' THEN 1
                            WHEN upper(supplier_name) Like '".$str."%' THEN 2
                            WHEN upper(supplier_name) Like '%".$str."' THEN 4
                            Else 3
                        END,supplier_name ";
            }

            $data['list'] = DB::select('select supplier_id,supplier_code,supplier_name,supplier_address,supplier_phone_1,supplier_reference_code from tbl_purc_supplier '.$where.' FETCH FIRST 50 ROWS ONLY');
        }
        if($helpType == 'poHelp'){
            $field = 'purchase_order_code';
            $keyid = 'purchase_order_id';
            $where = 'where ';
            if($str){
                $where .= "( lower(purchase_order_code) like '%".strtolower($str)."%' ";
                $where .= "OR lower(supplier_name) like '%".strtolower($str)."%') ";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $where .= " AND po_grn_status = 'pending'";
            $data['list'] = DB::select('select purchase_order_id,purchase_order_entry_date,purchase_order_code,supplier_name from vw_purc_po_help '.$where.' ORDER BY purchase_order_code desc FETCH FIRST 50 ROWS ONLY');
        }
        if($helpType == 'pendingPR'){
            $field = 'grn_code';
            $keyid = 'grn_id';
            $where = 'where ';
            $supplier_id = isset($request->supplier_id) ? $request->supplier_id : "";
            if($str){
                $where .= " lower(B.grn_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " B.business_id = ".auth()->user()->business_id;
            $where .= " AND B.branch_id = ".auth()->user()->branch_id;
            if(isset($supplier_id) && !empty($supplier_id)){
                $query = "SELECT B.grn_date,S.supplier_name,B.grn_code, A.grn_id, A.returnable_qty returnable_qty, A.collected_qty collected_qty, A.pending_qty pending_qty FROM VW_PURC_PENDING_RETURN A,
                TBL_PURC_GRN B ,   TBL_PURC_SUPPLIER S
                Where  A.Grn_id = B.GRN_ID  AND
                B.SUPPLIER_ID = S.SUPPLIER_ID
                AND  B.business_id = ". auth()->user()->business_id ." AND B.branch_id = ". auth()->user()->branch_id ."    AND B.supplier_id = ". $supplier_id;
            }else{
                $query = "SELECT B.grn_date,S.supplier_name,B.grn_code, A.grn_id, A.returnable_qty returnable_qty, A.collected_qty collected_qty, A.pending_qty pending_qty FROM VW_PURC_PENDING_RETURN A,
                TBL_PURC_GRN B ,   TBL_PURC_SUPPLIER S
                Where  A.Grn_id = B.GRN_ID  AND
                B.SUPPLIER_ID = S.SUPPLIER_ID
                AND  B.business_id = ". auth()->user()->business_id ." AND B.branch_id = " . auth()->user()->branch_id;
            }

            $query = $query . ' ORDER BY grn_code desc FETCH FIRST 50 ROWS ONLY';

            $data['list'] = DB::select($query);
        }
        if($helpType == 'demandApprovalHelp'){
            $field = 'demand_approval_dtl_code';
            $keyid = 'demand_approval_dtl_id';
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " and branch_id = ".auth()->user()->branch_id;
            $columns = "demand_approval_dtl_id,demand_approval_dtl_code,demand_approval_dtl_approve_status,demand_approval_dtl_date";
            if(!empty($str)){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $where .= " and (upper(demand_approval_dtl_code) Like '%".$replaced_str."%')
                        GROUP BY($columns)
                        order by
                        Case
                            WHEN upper(demand_approval_dtl_code) Like '".$str."' THEN 1
                            WHEN upper(demand_approval_dtl_code) Like '".$str."%' THEN 2
                            WHEN upper(demand_approval_dtl_code) Like '%".$str."' THEN 4
                            Else 3
                        END,demand_approval_dtl_code ";
            }else{
                $where .= " GROUP BY($columns) order by demand_approval_dtl_date desc,demand_approval_dtl_code desc";
            }
           // dd("select $columns from tbl_purc_demand_approval_dtl $where FETCH FIRST 50 ROWS ONLY");
            $lists = DB::select("select $columns from tbl_purc_demand_approval_dtl $where FETCH FIRST 50 ROWS ONLY");

          //  dump($lists);
            $data['list'] = [];

            foreach ($lists as $list){
                $demand_approval_dtl_id = $list->demand_approval_dtl_id;
                $qry = "select dap.demand_approval_dtl_date,d.DEMAND_NO
                        from tbl_purc_demand_approval_dtl dap
                        join tbl_purc_demand d on d.DEMAND_ID = dap.DEMAND_ID
                        where dap.demand_approval_dtl_id = $demand_approval_dtl_id
                        GROUP BY(dap.demand_approval_dtl_date,d.DEMAND_NO)
                        order by dap.demand_approval_dtl_date desc,d.DEMAND_NO asc FETCH FIRST 50 ROWS ONLY";
                $notes = DB::select($qry);
                $demand_codes = "";
                $last = count($notes) - 1;
                foreach ($notes as $k=>$note){
                    $demand_codes .= $note->demand_no;
                    if($last != $k){
                        $demand_codes .= ", ";
                    }
                }
                $list->demand_codes = $demand_codes;
                $data['list'][] = $list;
            }
        }

        if($helpType == 'productFormulationHelp'){
            $data['show_name'] = 'product_barcode_barcode';
            $data['hideKeys'] = ['product_id','product_barcode_id','uom_id'];
            $data['keys'] = ['product_barcode_barcode','product_name','product_arabic_name','uom_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            if(!$str){

                $data['list'] = DB::table('tbl_purc_product p')
                    ->join('tbl_purc_product_barcode b','b.product_id','=','p.product_id')
                    ->join('tbl_defi_uom uom','uom.uom_id','=','b.uom_id')
                    ->select('p.product_id','p.product_name','p.product_arabic_name','b.product_barcode_id','b.product_barcode_barcode','b.uom_id','uom.uom_name')
                    ->limit(50)->get();
            }
            if($str){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $qry = "Select * from VW_PURC_PRODUCT_BARCODE_HELP
                        WHERE upper(product_barcode_barcode) Like '%".$replaced_str."%' OR
                        upper(product_name) like '%".$replaced_str."%'
                        order by
                        Case
                            WHEN upper(product_name) Like '".$str."' THEN 1
                            WHEN upper(product_name) Like '".$str."%' THEN 2
                            WHEN upper(product_name) Like '%".$str."' THEN 4
                            Else 3
                        END,product_name
                            fetch first 50 rows only";

                $data['list'] = DB::select($qry);
            }

            $data['head'] = ['Barcode','Name','Arabic Name','UOM'];
        }

        $result = [
            'meta' => [
                'page' => 1,
                'perpage' => 50,
                'sort' => "asc",
                'field' => $field,
            ],
            'keyid'=> $keyid,
            'data' => $data['list'],
            'statuses' => 'success'
        ];
        return response()->json($result);
    }
    public function formulationHelpWithId(Request $request, $helpType, $id=null, $str = null){
        $data['case'] = $helpType;
        if(isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch'])){
            $str = $request['query']['generalSearch'];
        }
        $form_type = $request->form_type; // current form type
        if(isset($request->account_id)){
            $account_id = $request->account_id; // this is account id in case of vouchers
        }
        if(isset($request->customer_id)){
            $customer_id = $request->customer_id;
        }
        if(isset($request->supplier_id)){
            $supplier_id = $request->supplier_id;
        }
        if($helpType == 'productFormulationHelp'){

            $data['list'] = TblInveItemFormulation::with('dtls','product')->where('product_id',$id)->first();
            $data['show_name'] = 'product_barcode_barcode';
            $data['hideKeys'] = ['product_id','product_barcode_id','uom_id'];
            $data['keys'] = ['product_barcode_barcode','product_name','product_arabic_name','uom_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            // $where .= " AND branch_id = ".auth()->user()->branch_id;
                    if(!$str){
        /*                  $dataSql = ViewPurcProductBarcodeHelp::where('product_barcode_id', '<>', '0');
                            if($str){
                                $dataSql->where(DB::raw('lower(product_barcode_barcode)'),'like','%'.strtolower($str).'%')
                                        ->orWhere(DB::raw('lower(product_name)'),'like','%'.strtolower($str).'%');
                            }
                            $data['list'] = $dataSql->where('business_id',auth()->user()->business_id)->limit(50)->get();
        */
                        $data['list'] = DB::table('tbl_purc_product p')
                            ->join('tbl_purc_product_barcode b','b.product_id','=','p.product_id')
                            ->join('tbl_defi_uom uom','uom.uom_id','=','b.uom_id')
                            ->select('p.product_id','p.product_name','p.product_arabic_name','b.product_barcode_id','b.product_barcode_barcode','b.uom_id','uom.uom_name')
                            ->limit(50)->get();
                    }
                    if($str){
                        $str = strtoupper($str);
                        $replaced_str = str_replace(' ', '%', trim($str));
                        $qry = "Select * from VW_PURC_PRODUCT_BARCODE_HELP
                                WHERE upper(product_barcode_barcode) Like '%".$replaced_str."%' OR
                                upper(product_name) like '%".$replaced_str."%'
                                order by
                                Case
                                    WHEN upper(product_name) Like '".$str."' THEN 1
                                    WHEN upper(product_name) Like '".$str."%' THEN 2
                                    WHEN upper(product_name) Like '%".$str."' THEN 4
                                    Else 3
                                END,product_name
                                    fetch first 50 rows only";

                        $data['list'] = DB::select($qry);
                    }

                    $data['head'] = ['Barcode','Name','Arabic Name','UOM'];
        }
        return Response::json(['body' => View::make('common.inline-help',compact('data'))->render()]);
    }

    public function inlineHelpWithIdOpen(Request $request, $helpType, $id = null, $str = null){
        $data['case'] = $helpType;
        if(isset($request->supplier_id)){
            $supplier_id = $request->supplier_id;
        }
        if($helpType == 'supplierHelp'){
            $data['show_name'] = 'supplier_name';
            $data['hideKeys'] = ['supplier_id'];
            $data['keys'] = ['supplier_code','supplier_name','supplier_address','supplier_phone_1','supplier_reference_code'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            if($str){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $where .= " and (upper(supplier_name) Like '%".$replaced_str."%' OR
                        upper(supplier_code) like '%".$replaced_str."%' OR
                        upper(supplier_reference_code) like '%".$replaced_str."%' )
                        order by
                        Case
                            WHEN upper(supplier_name) Like '".$str."' THEN 1
                            WHEN upper(supplier_name) Like '".$str."%' THEN 2
                            WHEN upper(supplier_name) Like '%".$str."' THEN 4
                            Else 3
                        END,supplier_name ";
            }
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_purc_supplier '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Name','Address','Mobile Number','Reference Code'];
        }
        return Response::json(['body' => View::make('common.inline-help',compact('data'))->render()]);
    }

    public function inlineHelpOpen(Request $request, $helpType, $str = null){
        $data['case'] = $helpType;
        if(isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch'])){
            $str = $request['query']['generalSearch'];
        }
        $form_type = $request->form_type; // current form type
        if(isset($request->account_id)){
            $account_id = $request->account_id; // this is account id in case of vouchers
        }
        if(isset($request->customer_id)){
            $customer_id = $request->customer_id;
        }
        if(isset($request->supplier_id)){
            $supplier_id = $request->supplier_id;
        }
        if(isset($request->unique_id)){
            $unique_id = $request->unique_id;
        }
        if($helpType == 'supplierHelp'){
            $data['show_name'] = 'supplier_name';
            $data['hideKeys'] = ['supplier_id'];
            $data['keys'] = ['supplier_code','supplier_name','supplier_address','supplier_phone_1','supplier_reference_code'];
            if($form_type == 'lpo'){
                $data['row_identifier'] = $unique_id;
            }

            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            if($str){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $where .= " and (upper(supplier_name) Like '%".$replaced_str."%' OR
                        upper(supplier_code) like '%".$replaced_str."%' OR
                        upper(supplier_reference_code) like '%".$replaced_str."%' )
                        order by
                        Case
                            WHEN upper(supplier_name) Like '".$str."' THEN 1
                            WHEN upper(supplier_name) Like '".$str."%' THEN 2
                            WHEN upper(supplier_name) Like '%".$str."' THEN 4
                            Else 3
                        END,supplier_name ";
            }
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_purc_supplier '.$where.' FETCH FIRST 50 ROWS ONLY');

            // Check If the Supplier Have Some Purchase Returns
            if($form_type == "purc_demand" || $form_type == "grn" || $form_type == "purc_order" || $form_type == "purc_return")
            foreach ($data['list'] as $value) {
                $supplierReturnableQuery = "SELECT B.grn_date,S.supplier_name,B.grn_code, A.grn_id, A.returnable_qty returnable_qty, A.collected_qty collected_qty, A.pending_qty pending_qty FROM VW_PURC_PENDING_RETURN A,
                TBL_PURC_GRN B ,   TBL_PURC_SUPPLIER S
                Where  A.Grn_id = B.GRN_ID  AND
                B.SUPPLIER_ID = S.SUPPLIER_ID AND B.SUPPLIER_ID = ". $value->supplier_id ."
                AND  B.business_id = ". auth()->user()->business_id ." AND B.branch_id = " . auth()->user()->branch_id;
                $returnable = DB::select($supplierReturnableQuery);

                if(count($returnable) > 0){
                    if($returnable[0]->pending_qty > 0){
                        $value->supplier_has_returnable = 1;
                    }
                }
            }
            $data['head'] = ['Code','Name','Address','Mobile Number','Reference Code'];
        }
        if($helpType == 'employeeHelp'){
            $data['show_name'] = 'employee_name';
            $data['hideKeys'] = ['employee_id'];
            $data['keys'] = ['employee_code','employee_name','employee_arabic_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            // $where .= " AND branch_id = ".auth()->user()->branch_id;
            if($str){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $where .= " and (upper(employee_name) Like '%".$replaced_str."%' OR
                        upper(employee_code) like '%".$replaced_str."%' )
                        order by
                        Case
                            WHEN upper(employee_name) Like '".$str."' THEN 1
                            WHEN upper(employee_name) Like '".$str."%' THEN 2
                            Else 3
                        END,employee_name ";
            }
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_payr_employee '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Name','Arabic Name'];
        }
        // Product Formulation
        if($helpType == 'productFormulationHelp'){
            $data['show_name'] = 'product_barcode_barcode';
            $data['hideKeys'] = ['product_id','product_barcode_id','uom_id','product_barcode_packing'];
            $data['keys'] = ['product_barcode_barcode','product_name','product_arabic_name','uom_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            // $where .= " AND branch_id = ".auth()->user()->branch_id;
                    if(!$str){
        /*                  $dataSql = ViewPurcProductBarcodeHelp::where('product_barcode_id', '<>', '0');
                            if($str){
                                $dataSql->where(DB::raw('lower(product_barcode_barcode)'),'like','%'.strtolower($str).'%')
                                        ->orWhere(DB::raw('lower(product_name)'),'like','%'.strtolower($str).'%');
                            }
                            $data['list'] = $dataSql->where('business_id',auth()->user()->business_id)->limit(50)->get();
        */
                        $data['list'] = DB::table('tbl_purc_product p')
                            ->join('tbl_purc_product_barcode b','b.product_id','=','p.product_id')
                            ->join('tbl_defi_uom uom','uom.uom_id','=','b.uom_id')
                            ->select('p.product_id','p.product_name','p.product_arabic_name','b.product_barcode_id','b.product_barcode_barcode','b.uom_id','uom.uom_name','b.product_barcode_packing')
                            ->limit(50)->get();
                    }
                    if($str){
                        $str = strtoupper($str);
                        $replaced_str = str_replace(' ', '%', trim($str));
                        $qry = "Select * from VW_PURC_PRODUCT_BARCODE_HELP
                                WHERE upper(product_barcode_barcode) Like '%".$replaced_str."%' OR
                                upper(product_name) like '%".$replaced_str."%'
                                order by
                                Case
                                    WHEN upper(product_name) Like '".$str."' THEN 1
                                    WHEN upper(product_name) Like '".$str."%' THEN 2
                                    WHEN upper(product_name) Like '%".$str."' THEN 4
                                    Else 3
                                END,product_name
                                    fetch first 50 rows only";

                        $data['list'] = DB::select($qry);
                    }

                    $data['head'] = ['Barcode','Name','Arabic Name','UOM'];
        }

         //Formula Entry Help
         if($helpType == 'formulationEntryHelp'){
            $data['show_name'] = 'item_formulation_code';
            $data['hideKeys'] = ['item_formulation_id'];
            $data['keys'] = ['item_formulation_code','product_name','product_barcode_barcode','item_formulation_qty'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            // $where .= " AND branch_id = ".auth()->user()->branch_id;
         if(!$str){
        /*                  $dataSql = ViewPurcProductBarcodeHelp::where('product_barcode_id', '<>', '0');
                            if($str){
                                $dataSql->where(DB::raw('lower(product_barcode_barcode)'),'like','%'.strtolower($str).'%')
                                        ->orWhere(DB::raw('lower(product_name)'),'like','%'.strtolower($str).'%');
                            }
                            $data['list'] = $dataSql->where('business_id',auth()->user()->business_id)->limit(50)->get();
        */
                $data['list'] = DB::table('TBL_INVE_ITEM_FORMULATION iif')
                    ->join('tbl_purc_product pro','pro.product_id','=','iif.product_id')
                    ->join('tbl_purc_product_barcode b','b.product_id','=','iif.product_id')
                    ->select('iif.item_formulation_id','iif.item_formulation_code','pro.product_name','b.PRODUCT_BARCODE_barcode', 'iif.item_formulation_qty')
                    ->limit(50)->get();

            }
            if($str){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $qry = "Select * from tbl_inve_item_formulation
                        WHERE upper(item_formulation_code) Like '%".$replaced_str."%' OR
                        upper(item_formulation_code) like '%".$replaced_str."%'
                        order by
                        Case
                            WHEN upper(item_formulation_code) Like '".$str."' THEN 1
                            WHEN upper(item_formulation_code) Like '".$str."%' THEN 2
                            WHEN upper(item_formulation_code) Like '%".$str."' THEN 4
                            Else 3
                        END,item_formulation_code
                            fetch first 50 rows only";

                $data['list'] = DB::select($qry);
            }

                    $data['head'] = ['Code','Product Name','Barcode','Qty'];
        }
        // Loan COnfiguration Help
        if($helpType == 'loanConfiHelp'){
            $data['show_name'] = 'loan_configuration_id';
            $data['hideKeys'] = ['loan_configuration_id','loan_type'];
            $data['keys'] = ['description','advance_type_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            // $where = ' ';
            $where .= " confi.business_id = ".auth()->user()->business_id;
            if($str){
                $str = strtoupper($str);
                $replaced_str = str_replace(' ', '%', trim($str));
                $where .= " and (confi.descripiton Like '%".$replaced_str."%' OR
                        upper(confi.description) like '%".$replaced_str."%' )
                        order by
                        Case
                            WHEN confi.descripiton Like '".$str."' THEN 1
                            WHEN upper(confi.description) Like '".$str."%' THEN 2
                            Else 3
                        END ";
            }

            $qry = 'select confi.loan_configuration_id,confi.loan_type,confi.description,adv.advance_type_id,adv.advance_type_name from tbl_payr_loan_configuration confi
                    join tbl_payr_advance_type adv on confi.loan_type = adv.advance_type_id
                    '.$where.'
                    FETCH FIRST 50 ROWS ONLY';
            $data['list'] = DB::select($qry);
            $data['head'] = ['Description','Loan Type'];
        }
        // Product Help
        $listHelpProducts = ['productHelp','productTPHelp','productMergedFromHelp','productMergedToHelp'];
        if(in_array($helpType,$listHelpProducts)){
            $data['show_name'] = 'product_barcode_barcode';
            $data['hideKeys'] = ['product_id','product_barcode_id','uom_id'];
            $data['keys'] = ['product_barcode_barcode','product_name','uom_name','product_barcode_packing'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);

            if(!$str){
/*                  $dataSql = ViewPurcProductBarcodeHelp::where('product_barcode_id', '<>', '0');
                    if($str){
                        $dataSql->where(DB::raw('lower(product_barcode_barcode)'),'like','%'.strtolower($str).'%')
                                ->orWhere(DB::raw('lower(product_name)'),'like','%'.strtolower($str).'%');
                    }
                    $data['list'] = $dataSql->where('business_id',auth()->user()->business_id)->limit(50)->get();
*/
                $data['list'] = DB::table('tbl_purc_product p')
                    ->join('tbl_purc_product_barcode b','b.product_id','=','p.product_id')
                    ->join('tbl_defi_uom uom','uom.uom_id','=','b.uom_id')
                    ->join('vw_purc_group_item item','item.group_item_id','=','p.group_item_id' )
                    ->join('vw_purc_group_item item','item.group_parent_item_id','=','p.group_item_parent_id' )
                    ->select('p.product_id','p.product_name','p.product_arabic_name','b.product_barcode_id','b.product_barcode_barcode','b.product_barcode_packing','b.uom_id','uom.uom_name','item.group_item_name','item.parent_group_item_name')
                    ->groupby('p.product_id','p.product_name','p.product_arabic_name','b.product_barcode_id','b.product_barcode_barcode','b.product_barcode_packing','b.uom_id','uom.uom_name','item.group_item_name','item.parent_group_item_name')
                    ->limit(50)->get();
            }
            if(isset($request->val)){
                $p_str = strtoupper($request->val);
                $p_str = str_replace('%2F','/',$p_str);
                $p_str = str_replace('%22','"',$p_str);
                $p_str = str_replace('%2C',',',$p_str);
                $p_str = str_replace("'","''",$p_str);
                $replaced_str = str_replace(' ', '%', trim($p_str));
                $replaced_str = str_replace('%20', '%', trim($replaced_str));
                $qry = "Select PRODUCT_ID, PRODUCT_NAME, PRODUCT_ARABIC_NAME, MAX(PRODUCT_BARCODE_ID) PRODUCT_BARCODE_ID ,
                            MAX(PRODUCT_BARCODE_BARCODE) PRODUCT_BARCODE_BARCODE, MAX(UOM_ID) UOM_ID , MAX(UOM_NAME) UOM_NAME , GROUP_ITEM_NAME, GROUP_ITEM_PARENT_NAME,MAX(product_barcode_packing) product_barcode_packing
                        from VW_PURC_PRODUCT_BARCODE_RATE
                        WHERE upper(product_barcode_barcode) Like '%".$replaced_str."%' OR
                        upper(product_name) like '%".$replaced_str."%'
                        group by PRODUCT_ID, PRODUCT_NAME, PRODUCT_ARABIC_NAME, GROUP_ITEM_NAME, GROUP_ITEM_PARENT_NAME
                        order by
                        Case
                            WHEN upper(product_barcode_barcode) Like '".$replaced_str."' THEN 1
                            WHEN upper(product_name) Like '".$replaced_str."%' THEN 2
                            WHEN upper(product_name) Like '%".$replaced_str."' THEN 4
                            Else 3
                        END,product_name,product_barcode_barcode
                            fetch first 50 rows only";

                $data['list'] = DB::select($qry);
            }
            $data['head'] = ['Barcode','Name','UOM','Packing'];
        }
        if($helpType == 'productHelpSI'){
            $data['show_name'] = 'product_barcode_barcode';
            $data['hideKeys'] = ['product_id','product_barcode_id','uom_id'];
            $data['keys'] = ['product_barcode_barcode','product_name','product_arabic_name','uom_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            /*$dataSql = DB::table('tbl_purc_product as p')
                ->join('tbl_purc_product_barcode as b', 'p.product_id', '=', 'b.product_id')
                ->join('tbl_defi_uom as uom', 'uom.uom_id', '=', 'b.uom_id')
                ->select('p.product_id', 'p.product_name', 'p.product_arabic_name', 'p.product_code',
                    'b.product_barcode_id', 'b.product_barcode_barcode', 'b.uom_id', 'b.product_barcode_packing',
                    'uom.uom_name','p.business_id');*/
            $dataSql = ViewPurcProductBarcodeHelp::where('product_barcode_id', '<>', '0')->where('product_can_sale',1);
            if($str){
                $dataSql->where(DB::raw('lower(product_barcode_barcode)'),'like','%'.strtolower($str).'%')
                        ->orWhere(DB::raw('lower(product_name)'),'like','%'.strtolower($str).'%');
            }
            $data['list'] = $dataSql->where('business_id',auth()->user()->business_id)->limit(50)->get();
            $data['head'] = ['Barcode','Name','Arabic Name','UOM'];
        }
        if($helpType == 'accountsHelp' || $helpType == 'upAccountsHelp' || $helpType == 'cAccountsHelp'){
            $data['show_name'] = 'chart_code';
            $data['hideKeys'] = ['chart_account_id'];
            $data['keys'] = ['chart_code','chart_name','chart_reference_code'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(chart_name) like '%".strtolower($str)."%' OR";
                $where .= " lower(chart_code) like '%".strtolower($str)."%' OR";
                $where .= " lower(chart_reference_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $data['list'] = DB::select('select '.$selectColumns.' from vw_acco_chart_account_help '.$where.' FETCH FIRST 400 ROWS ONLY');
            $data['head'] = ['Account Code','Account Name','Reference Code'];
        }
        // Sales Quotation Help
        if($helpType == 'salesQuotationHelp'){
            $data['show_name'] = 'sales_order_code';
            $data['hideKeys'] = ['sales_order_id'];
            $data['keys'] = ['sales_order_code','sales_order_date'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(sales_order_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " sales_order_code_type = 'sq' and business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_sale_sales_order '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date'];
        }
        if($helpType == 'salesRequestQuotationHelp'){
            $data['show_name'] = 'sales_order_code';
            $data['hideKeys'] = ['sales_order_id'];
            $data['keys'] = ['sales_order_code','sales_order_date','customer_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(ord.sales_order_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " ord.sales_order_code_type = 'rq' and ord.business_id = ".auth()->user()->business_id;
            $where .= " AND ord.branch_id = ".auth()->user()->branch_id;
            $join  = " JOIN tbl_sale_customer customer ON customer.customer_id = ord.customer_id ";

            $query = 'select ord.sales_order_id,ord.sales_order_code,ord.sales_order_date,customer.customer_name from tbl_sale_sales_order ord '.$join.' '.$where.' ORDER BY ord.sales_order_code DESC FETCH FIRST 50 ROWS ONLY';

            $data['list'] = DB::select('select '.$selectColumns.' from tbl_sale_sales_order ord '.$join.' '.$where.' ORDER BY ord.sales_order_code DESC FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date','Customer'];
        }
        if($helpType == 'accSupplierBankHelp'){
            $data['show_name'] = 'bank_name';
            $data['hideKeys'] = ['supplier_account_id','supplier_bank_name'];
            $data['keys'] = ['bank_name','supplier_iban_no','supplier_account_title','supplier_account_no'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(sa.supplier_account_title) like '%".strtolower($str)."%' OR";
                $where .= " lower(sa.supplier_account_no) like '%".strtolower($str)."%' OR";
                $where .= " lower(sa.supplier_iban_no) like '%".strtolower($str)."%' OR";
                $where .= " lower(b.bank_name) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " s.business_id = ".auth()->user()->business_id;
            if(isset($request->supplier_chart_id)){
                $where .= " and s.supplier_account_id = ".$request->supplier_chart_id;
            }
            $col = "sa.supplier_account_id,sa.supplier_account_no,sa.supplier_account_title,sa.supplier_bank_name,b.bank_name,sa.supplier_iban_no";
            $qry = "select ".$col." from TBL_PURC_SUPPLIER s join TBL_PURC_SUPPLIER_ACCOUNT sa on s.supplier_id = sa.supplier_id join TBL_DEFI_BANK b on b.bank_id = sa.SUPPLIER_BANK_NAME";
            $qry .= " ".$where.' FETCH FIRST 50 ROWS ONLY';
            $data['list'] = DB::select($qry);
            $data['head'] = ['Bank Name','Branch No','Title','Account No'];
        }
        if($helpType == 'oExpVoucherHelp'){
            $data['show_name'] = 'voucher_no';
            $data['hideKeys'] = ['voucher_id'];
            $data['keys'] = ['voucher_date','voucher_no','amount'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(voucher_no) like '%".strtolower($str)."%' OR";
                $where .= " amount like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " and lower(voucher_type) = 'pve'";
            $qry = "select ".$selectColumns." from VW_ACCO_VOUCHER_LISTING";
            $qry .= " ".$where.' order by voucher_date FETCH FIRST 50 ROWS ONLY';
            $data['list'] = DB::select($qry);
            $data['head'] = ['Date','Voucher No','Amount'];
        }
        if($helpType == 'oJVVoucherHelp'){
            $data['show_name'] = 'voucher_no';
            $data['hideKeys'] = ['voucher_id'];
            $data['keys'] = ['voucher_date','voucher_no','amount'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(voucher_no) like '%".strtolower($str)."%' OR";
                $where .= " amount like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " and lower(voucher_type) = 'jv'";
            $qry = "select voucher_id,voucher_date,voucher_no,sum(VOUCHER_DEBIT) amount from TBL_ACCO_VOUCHER";
            $qry .= " ".$where.' group by voucher_id,voucher_date,voucher_no order by voucher_date FETCH FIRST 50 ROWS ONLY';
            $data['list'] = DB::select($qry);
            $data['head'] = ['Date','Voucher No','Amount'];
        }
        if($helpType == 'oLVVoucherHelp'){
            $data['show_name'] = 'voucher_no';
            $data['hideKeys'] = ['voucher_id'];
            $data['keys'] = ['voucher_date','voucher_no','amount'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(voucher_no) like '%".strtolower($str)."%' OR";
                $where .= " amount like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " and lower(voucher_type) = 'lv'";
            $qry = "select voucher_id,voucher_date,voucher_no,sum(VOUCHER_DEBIT) amount from TBL_ACCO_VOUCHER";
            $qry .= " ".$where.' group by voucher_id,voucher_date,voucher_no order by voucher_date FETCH FIRST 50 ROWS ONLY';
            $data['list'] = DB::select($qry);
            $data['head'] = ['Date','Voucher No','Amount'];
        }

        // E-Services Help
        if($helpType == 'servicesOrderHelp'){
            $data['show_name'] = 'sales_order_code';
            $data['hideKeys'] = ['sales_order_id'];
            $data['keys'] = ['sales_order_code','sales_order_date','customer_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(sales_order_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " ord.sales_order_code_type = 'or' and ord.business_id = ".auth()->user()->business_id;
            $where .= " AND ord.branch_id = ".auth()->user()->branch_id;
            $join  = " JOIN tbl_sale_customer customer ON customer.customer_id = ord.customer_id ";

            $data['list'] = DB::select('select '.$selectColumns.' from tbl_sale_sales_order ord '.$join.' '.$where.' ORDER BY ord.sales_order_code DESC FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date','Customer'];
        }

        if($helpType == 'budgetHelp'){
            $data['show_name'] = 'budget_budgetart_position';
            $data['hideKeys'] = ['budget_id','branch_id'];
            $data['keys'] = ['budget_budgetart_position','budget_credit_amount','budget_debit_amount'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(budget_budgetart_position) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from vw_acco_budget_help '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Description','Credit Amount','Debit Amount'];
        }
        if($helpType == 'chequebookHelp'){
            $data['show_name'] = 'cheque_book_name';
            $data['hideKeys'] = ['cheque_book_id'];
            $data['keys'] = ['cheque_book_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(cheque_book_name) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_acco_cheque_book '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Name'];
        }
        if($helpType == 'poHelp'){
            $data['show_name'] = 'purchase_order_code';
            $data['hideKeys'] = ['purchase_order_id','supplier_id'];
            $data['keys'] = ['purchase_order_code','created_at','supplier_name','purchase_order_total_net_amount'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(purchase_order_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $where .= " AND po_grn_status = 'pending'";
            $data['list'] = DB::select('select '.$selectColumns.' from vw_purc_purchase_order_listing '.$where.' ORDER BY purchase_order_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date','Supplier Name','Total Amount'];
        }

        if($helpType == 'grnHelp'){
            $data['show_name'] = 'grn_code';
            $data['hideKeys'] = ['grn_id'];
            $data['keys'] = ['grn_code','grn_date','supplier_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(grn_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $where .= " AND grn_type = 'GRN' ";
            $data['list'] = DB::select('select '.$selectColumns.' from vw_purc_grn '.$where.' GROUP BY supplier_name,grn_code,grn_date,grn_id ORDER BY grn_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date','Supplier Name'];
        }

        if($helpType == 'grnHelpNew'){
            $data['show_name'] = 'grn_code';
            $data['hideKeys'] = ['grn_id'];
            $data['keys'] = ['grn_code','grn_date','supplier_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(grn_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= "business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $where .= " AND grn_id not in(select grn_id from tbl_purc_grn where grn_id is not null)";
            $where .= " AND grn_type = 'GRN' ";
            $data['list'] = DB::select('select '.$selectColumns.' from vw_purc_grn '.$where.' GROUP BY supplier_name,grn_code,grn_date,grn_id ORDER BY grn_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date','Supplier Name'];
        }

        if($helpType == 'grnGlobalHelp'){
            $data['show_name'] = 'grn_id';
            $data['hideKeys'] = ['grn_id'];
            $data['keys'] = ['grn_code','grn_date','supplier_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(grn_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            // $where .= " business_id = ".auth()->user()->business_id;
            // $where .= " AND branch_id = ".auth()->user()->branch_id;
            $where .= " AND grn_type = 'GRN' ";
            $data['list'] = DB::select('select '.$selectColumns.' from vw_purc_grn '.$where.' GROUP BY supplier_name,grn_code,grn_date,grn_id ORDER BY grn_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date','Supplier Name'];
        }

        if($helpType == 'autoDemandHelp'){
            $data['show_name'] = 'auto_demand_code';
            $data['hideKeys'] = ['ad_id','supplier_id'];
            $data['keys'] = ['ad_date','ad_code','supplier_name','ad_status'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);

            $where = 'where ';
            $join = '';
            if($str){
                $where .= " lower(ad_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            if(isset($supplier_id)){
                $where .= " AD.supplier_id = ".$supplier_id . " AND ";
            }
            $where .= " AD.business_id = ".auth()->user()->business_id;
            $where .= " AND AD.branch_id = ".auth()->user()->branch_id;

            // $query = "SELECT B.grn_date,B.supplier_name,B.supplier_id,B.grn_code, A.grn_id, sum(A.returnable_qty) returnable_qty, sum(A.collected_qty) collected_qty, sum(A.pending_qty) pending_qty FROM VW_PURC_PENDING_RETURN A
            // JOIN vw_purc_grn B ON
            // A.Grn_id = B.GRN_ID ". $where ."
            // group by A.grn_id, B.grn_date ,B.grn_code, B.supplier_name, B.supplier_id";
            $query = "SELECT S.SUPPLIER_NAME as supplier_name,AD.ad_id,AD.supplier_id,AD.ad_date,AD.ad_code,AD.ad_status FROM tbl_purc_auto_demand AD
            JOIN tbl_purc_supplier S ON S.SUPPLIER_ID = AD.SUPPLIER_ID " . $where . " ORDER BY ad_code desc FETCH FIRST 50 ROWS ONLY";

            $data['list'] = DB::select($query);
            $data['head'] = ['Date','Code','Supplier','Status'];
        }
        if($helpType == 'prHelp'){
            $data['show_name'] = 'purchase_return_code';
            $data['hideKeys'] = ['grn_id','supplier_id'];
            $data['keys'] = ['grn_date','supplier_name','grn_code'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);

            $where = 'where ';
            if($str){
                $where .= " lower(grn_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " B.grn_type = 'PR'";
            $where .= " AND B.supplier_id = ".$supplier_id;
            $where .= " AND B.business_id = ".auth()->user()->business_id;
            $where .= " AND B.branch_id = ".auth()->user()->branch_id;

            $query = "SELECT B.grn_date,B.supplier_name,B.supplier_id,B.grn_code, A.grn_id, sum(A.returnable_qty) returnable_qty, sum(A.collected_qty) collected_qty, sum(A.pending_qty) pending_qty FROM VW_PURC_PENDING_RETURN A
            JOIN vw_purc_grn B ON
            A.Grn_id = B.GRN_ID ". $where ."
            group by A.grn_id, B.grn_date ,B.grn_code, B.supplier_name, B.supplier_id";
            $data['list'] = DB::select($query . ' FETCH FIRST 50 ROWS ONLY');

            $data['head'] = ['Date','Supplier','Code'];
        }
        if($helpType == 'customerHelp'){
            $data['show_name'] = 'customer_name';
            $data['hideKeys'] = ['customer_id','city_id','region_id'];
            $data['keys'] = ['customer_code','customer_name','customer_address','customer_phone_1','customer_reference_code'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(customer_phone_1) like '%".strtolower($str)."%' OR";
                $where .= " lower(customer_name) like '%".strtolower($str)."%' OR";
                $where .= " lower(customer_code) like '%".strtolower($str)."%' OR";
                $where .= " lower(customer_reference_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            //dd($where);
            $data['list'] = DB::select('select '.$selectColumns.' from vw_sale_customer '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Name','Address','Mobile Number','Reference Code'];
        }
        if($helpType == 'saleorderHelp'){
            $data['show_name'] = 'sales_order_code';
            $data['hideKeys'] = ['sales_order_id'];
            $data['keys'] = ['sales_order_code','sales_order_date'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(sales_order_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " sales_order_code_type = 'so' and business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_sale_sales_order '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Date'];
        }
        if($helpType == 'lpoPoHelp'){
            $data['show_name'] = 'lpo_code';
            $data['hideKeys'] = ['lpo_id','supplier_id','currency_id','exchange_rate','payment_term_id','supplier_ageing_terms_value'];
            $data['keys'] = ['lpo_code','supplier_code','supplier_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(lpo_code) like '%".strtolower($str)."%' OR";
                $where .= " lower(supplier_code) like '%".strtolower($str)."%' OR";
                $where .= " lower(supplier_name) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from vw_purc_Lpo_help '.$where.' order by lpo_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['LPO Code','Supplier Code','Supplier'];
        }
        if($helpType == 'lpoPoQuotationHelp'){
            $data['show_name'] = 'lpo_code';
            $data['hideKeys'] = ['lpo_id','supplier_id','currency_id','exchange_rate','payment_term_id','supplier_ageing_terms_value'];
            $data['keys'] = ['lpo_code','supplier_code','supplier_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(lpo_code) like '%".strtolower($str)."%' OR";
                $where .= " lower(supplier_code) like '%".strtolower($str)."%' OR";
                $where .= " lower(supplier_name) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from vw_purc_Lpo_help '.$where.' order by lpo_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['LPO Code','Supplier Code','Supplier'];
        }
        if($helpType == 'stockRequestHelp'){
            $data['show_name'] = 'demand_no';
            $data['hideKeys'] = ['demand_id'];
            $data['keys'] = ['demand_date','demand_no','branch_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(demand_no) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND demand_branch_to_id = ".auth()->user()->branch_id;
            $where .= " AND demand_dtl_approve_status = 'pending'";
            $data['list'] = DB::select('select '.$selectColumns.' from vw_stock_request '.$where.' GROUP BY('.$selectColumns.') ORDER BY demand_no asc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Date','Code','Branch From'];
        }
        if($helpType == 'stockTransferHelp'){
            $data['show_name'] = 'stock_code';
            $data['hideKeys'] = ['stock_id','stock_store_from_id','stock_branch_from_id'];
            $data['keys'] = ['stock_date','stock_code','stock_branch_to_name','stock_store_to_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(stock_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " stock_code_type = 'st'";
            $where .= " AND business_id = ".auth()->user()->business_id;
            $where .= " AND stock_receive_status = 0";
            $where .= " AND stock_branch_to_id = ".auth()->user()->branch_id;
            //dd('select '.$selectColumns.' from vw_inve_stock '.$where.' GROUP BY('.$selectColumns.') ORDER BY stock_code desc FETCH FIRST 50 ROWS ONLY');
            $data['list'] = DB::select('select '.$selectColumns.' from vw_inve_stock '.$where.' GROUP BY('.$selectColumns.') ORDER BY stock_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Date','Code','To Transfer Branch','To Store'];
        }
        if($helpType == 'stockReceivingHelp'){
            $data['show_name'] = 'stock_code';
            $data['hideKeys'] = ['stock_id'];
            $data['keys'] = ['stock_date','stock_code','stock_store_to_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(stock_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " stock_code_type = 'str'";
            $where .= " AND business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from vw_inve_stock '.$where.' GROUP BY('.$selectColumns.') ORDER BY stock_code desc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Date','Code','Store'];
        }
        if($helpType == 'InternalStockTransferHelp'){
            $data['show_name'] = 'stock_code';
            $data['hideKeys'] = ['stock_id'];
            $data['keys'] = ['stock_code','stock_store_from_name','stock_store_to_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(stock_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $supplier_id = isset($supplier_id)?$supplier_id:0;
            $where .= " stock_code_type = 'ist'";
            $where .= " AND business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $where .= " AND supplier_id = ".$supplier_id;
            $data['list'] = DB::select('select '.$selectColumns.' from vw_inve_stock '.$where.' GROUP BY('.$selectColumns.') ORDER BY stock_code asc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Code','Store From','Store To'];
        }
        if($helpType == 'invoiceHelp'){
            if($form_type == 'cpv' || $form_type == 'bpv'){
                $data['show_name'] = 'grn_code';
                $data['hideKeys'] = ['grn_id'];
                $data['keys'] = ['grn_code','grn_date','tbl_purc_grn_dtl_total_amount','received_amount','tbl_purc_grn_dtl_total_amount'];
                //$merge = array_merge( $data['keys'], $data['hideKeys']);
                //$selectColumns = implode(', ', $merge);
                $where = 'where ';
                if($str){
                    $where .= " lower(sales_order_code) like '%".strtolower($str)."%'";
                    $where .= " AND ";
                }
                $where .= " business_id = ".auth()->user()->business_id;
                $where .= " AND branch_id = ".auth()->user()->branch_id;
                $where .= " AND grn_type = 'GRN' ";
                if($account_id){
                    $account_id = "'".$account_id."'";
                }else{
                    $account_id = "''";
                }
                $data['list'] = DB::select('select distinct grn_id,grn_code,grn_date,tbl_purc_grn_dtl_total_amount,0 AS received_amount,tbl_purc_grn_dtl_total_amount from vw_purc_grn '.$where.' AND chart_account_id = '.$account_id.' FETCH FIRST 50 ROWS ONLY');
                $data['head'] = ['Document Code','Document Date','Amount','Received Amount','Balance Amount'];
            }
        }
        if($helpType == 'salesContractHelp'){
            $data['show_name'] = 'sales_contract_code';
            $data['hideKeys'] = ['sales_contract_id','sales_contract_rate_type','sales_contract_rate_perc'];
            $data['keys'] = ['sales_contract_code','customer_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(sco.sales_contract_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " sco.customer_id = ".$customer_id;
            $where .= " AND sco.business_id = ".auth()->user()->business_id;
            $where .= " AND sco.branch_id = ".auth()->user()->branch_id;
            $qry = 'select sco.sales_contract_id,sco.sales_contract_code,sco.sales_contract_rate_type,sco.sales_contract_rate_perc,scu.customer_name from tbl_sale_sales_contract sco
                    join tbl_sale_customer scu on scu.customer_id = sco.customer_id
                    '.$where.'
                    ORDER BY sco.sales_contract_code desc FETCH FIRST 50 ROWS ONLY';
            $data['list'] = DB::select($qry);
            $data['head'] = ['Code','Customer Name'];
        }
        if($helpType == 'salesInvoiceHelp'){
            $data['show_name'] = 'sales_code';
            $data['hideKeys'] = ['sales_id','customer_id','customer_name'];
            $data['keys'] = ['sales_code','customer_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
                if($str){
                    $where .= " lower(sales_code) like '%".strtolower($str)."%' OR ";
                    $where .= " lower(customer_name) like '%".strtolower($str)."%'";
                    $where .= " AND ";
                }
                $where .= " business_id = ".auth()->user()->business_id;
                $where .= " AND branch_id = ".auth()->user()->branch_id;
                $data['list'] = DB::select('select '.$selectColumns.' from vw_sale_sales_invoice '.$where.' GROUP BY('.$selectColumns.') ORDER BY sales_code asc FETCH FIRST 50 ROWS ONLY');
                $data['head'] = ['Code','Customer Name'];
        }
        if($helpType == 'brandHelp'){
            $data['show_name'] = 'brand_name';
            $data['hideKeys'] = ['brand_id'];
            $data['keys'] = ['brand_name'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(brand_name) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_purc_brand '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Brand Name'];
        }
        if($helpType == 'groupHelp'){
            $data['show_name'] = 'group_item_name_string';
            $data['hideKeys'] = ['group_item_id'];
            $data['keys'] = ['group_item_name_string'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(group_item_name_string) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from vw_purc_group_item '.$where.' FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Group Item Name'];
        }
        if($helpType == 'stockPurchasingHelp'){
            $data['show_name'] = 'purchasing_code';
            $data['hideKeys'] = ['purchasing_id'];
            $data['keys'] = ['purchasing_entry_date','purchasing_code'];
            $merge = array_merge( $data['keys'], $data['hideKeys']);
            $selectColumns = implode(', ', $merge);
            $where = 'where ';
            if($str){
                $where .= " lower(purchasing_code) like '%".strtolower($str)."%'";
                $where .= " AND ";
            }
            $where .= " purchasing_type = 'purchasing'";
            $where .= " AND business_id = ".auth()->user()->business_id;
            $where .= " AND branch_id = ".auth()->user()->branch_id;
            $data['list'] = DB::select('select '.$selectColumns.' from tbl_purc_purchasing '.$where.' GROUP BY('.$selectColumns.') ORDER BY purchasing_code asc FETCH FIRST 50 ROWS ONLY');
            $data['head'] = ['Date','Code'];
        }
        if($request->help_view == 'popup'){
            $result = [
                'meta' => [
                    'page' => 1,
                    'perpage' => 50,
                    'sort' => "asc",
                    'field' => "product_code",
                ],
                'keyid'=> "product_barcode_id",
                'data' => $data['list'],
                'statuses' => 'success'
            ];
            return response()->json($result);
        }else{
           // return $this->jsonSuccessResponse($data, "", 200);
            return Response::json(['body' => View::make('common.inline-help',compact('data'))->render()]);
        }

    }
}
