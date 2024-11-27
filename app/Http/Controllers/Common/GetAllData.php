<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblInveDisplayLocation;
use App\Models\TblPurcProduct;
use App\Models\TblPurcProductBarcode;
use App\Models\TblPurcSupplier;
use App\Models\TblSoftBranch;
use App\Models\TblSoftFormCases;
use App\Models\TblPurcGroupItem;
use App\Models\TblAccoChequeBook;
use App\Models\TblSoftMenuDtl;
use App\Models\TblSoftUserPageSetting;
use App\Models\ViewAccoChartAccountHelp;
use App\Models\TblPurcQuotation;
use App\Models\TblDefiCurrency;
use App\Models\ViewPurcProductBarcode;
use App\Models\ViewPurcProductBarcodeHelp;
use App\Models\TblPurcProductBarcodePurchRate;
use App\Models\ViewPurcSupplier;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class GetAllData extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($caseType)
    {
        $formCase = TblSoftFormCases::where('form_cases_casename',$caseType)->firstOrFail();
        try {
            $data['caseType'] = $caseType;
            $data['title'] = $formCase['form_cases_title'];
            $data['path-form'] = $caseType.'/form';
            $data['path'] = $caseType;
            $headings = explode(',', $formCase['form_cases_heading']);
            $data['headings'] = $headings;
            $columnName = explode(',', $formCase['form_cases_column_name']);
            $data['columnName'] = $columnName;

            $modelName = 'App\Models\\' .$formCase['form_cases_modelname'];
            array_push($columnName,$modelName::primaryKeyName());
            $data['primaryKeyName'] = $modelName::primaryKeyName();
            $data['table'] = $modelName::get($columnName);

        }
        catch(ModelNotFoundException $exception)
        {
            return abort(404);
        }

        return view('common.datatable', compact('data'));
    }
    public function getAjaxData($caseTyle, $searchStr)
    {
       // dd($caseTyle.' -  - '.$searchStr);
        $formCase = TblSoftFormCases::where('form_cases_casename',$caseTyle)->firstOrFail();

        $columnName = explode(',', $formCase['form_cases_column_name']);
        $modelName = 'App\Models\\' .$formCase['form_cases_modelname'];
        $strs = '%' . strtoupper($searchStr) . '%';
        //dd($strs);
        $data = $modelName::where('upper(group_item_name)','LIKE', $strs)->get();
   //    $data = User::where('name', 'LIKE', '%umar%');
        dd($data);
//        dd($data->toSql());
        foreach($columnName as $column) {
            $data = $data->orWhere($column, 'LIKE', '%car%');
        }
        $data = $data->get();
        dd($data);
      //  dd($data->toSql());
        return response()->json($data);
    }

    public function supplier()
    {
        $data = TblPurcSupplier::get();
        return view('common.supplier',compact('data'));
    }

    public function suppliergrid()
    {
        $data = TblPurcSupplier::get();
        return view('common.suppliergrid',compact('data'));
    }

    public function treeView(){
        $Categorys = TblPurcGroupItem::where('parent_group_id')->get();
        $tree='<ul id="browser" class="filetree"><li class="tree-view"></li>';
        foreach ($Categorys as $Category) {
             $tree .='<li class="tree-view closed"<a class="tree-name">'.$Category->group_item_name.'</a>';
             if(count($Category->childs)) {
                $tree .=$this->childView($Category);
            }
        }
        $tree .='<ul>';
        // return $tree;
        return view('common.parentitemgroup',compact('tree'));
    }

    public function childView($Category){
        $html ='<ul>';
        foreach ($Category->childs as $arr) {
            if(count($arr->childs)){
            $html .='<li class="tree-view closed"><a class="tree-name">'.$arr->group_item_name.'</a>';
                    $html.= $this->childView($arr);
                }else{
                    $html .='<li class="tree-view"><a class="tree-name">'.$arr->group_item_name.'</a>';
                    $html .="</li>";
                }
        }
        $html .="</ul>";
        return $html;
    }
    public function openTree(Request $request)
    {
        $data = '';
        $data2 = '';
        $DisplayLocations = TblInveDisplayLocation::where('parent_display_location_id')->get();
        $data.='<ul>';
        foreach ($DisplayLocations as $DisplayLocation) {
            $data .='<li class="tree-view closed"<a class="tree-name">'.$DisplayLocation->display_location_name.'</a>';
            if(count($DisplayLocation->childs)) {
                $name = 'display_location_name';
                $data .= $this->childTree($DisplayLocation,$name);
            }
        }
        $data .='<ul>';
        $view = view('common.tree', compact(['data','data2']))->render();
        return response()->json(['body'=>$view]);
    }
    public function childTree($childArr,$name){
        $html ='<ul>';
        foreach ($childArr->childs as $key=>$arr) {
            if(count($arr->childs) && in_array($arr->display_location_level,[1,2])){
                $html .='<li class="tree-view closed"><a class="tree-name">'.$key.' - '.$arr->$name.'</a>';
                $html.= $this->childTree($arr,$name);
            }else{
                $html .='<li data-jstree=\'{ "type" : "file" }\' class="2 tree-view"><a class="tree-name">'.$key.' -E- '.$arr->$name.'</a>';
                $html .="</li>";
            }
        }
        $html .="</ul>";
        return $html;
    }

    public function productList()
    {
        $data = DB::table('tbl_purc_product as p')
        ->join('tbl_purc_product_barcode as b', 'p.product_id', '=', 'b.product_id')
        ->join('tbl_defi_uom as uom', 'uom.uom_id', '=', 'b.uom_id')
        ->join('tbl_purc_packing as pack', 'pack.packing_id', '=', 'b.packing_id')
        ->select('p.product_id', 'p.product_name', 'p.product_arabic_name', 'p.product_code',
            'b.product_barcode_id', 'b.product_barcode_barcode', 'b.uom_id', 'b.packing_id',
            'uom.uom_name', 'pack.packing_name')->get();

        return view('common.product',compact('data'));
    }

    public function Quotation()
    {
        $data = TblPurcQuotation::get();
        return view('common.comp_quotation',compact('data'));
    }

    public function accountList()
    {
        $data = TblAccCoa::get();
        return view('common.account',compact('data'));
    }

    public function cheqbookList(){
        $data = TblAccoChequeBook::get();
        return view('common.cheqbook',compact('data'));
    }

    public function maskingFormat($code){
        $data = ViewAccoChartAccountHelp::where('chart_code',$code)->first(['chart_account_id','chart_code','chart_name']);
        return response()->json($data);
    }

    public function help($helpType)
    {
        $formCase = TblSoftFormCases::where('form_cases_casename',$helpType)->firstOrFail();
        //dd($formCase['form_cases_orderby']);
        try {
            $data['caseType'] = $helpType;
            $data['title'] = $formCase['form_cases_title'];
            $orderby = $formCase['form_cases_orderby'];
            $data['path'] = $helpType;

            $headings = explode(',', $formCase['form_cases_heading']);
            $data['headings'] = $headings;
            $columnName = explode(',', $formCase['form_cases_column_name']);
            $data['columnName'] = $columnName;
            $hiddenFields = explode(',', $formCase['form_cases_hidden_field']);
            $data['hiddenFields'] = $hiddenFields;
            $allcolumnsfields = array_merge($columnName,$hiddenFields);
            if($helpType == 'productHelp') {
                $data['table'] = DB::table('tbl_purc_product as p')
                                ->join('tbl_purc_product_barcode as b', 'p.product_id', '=', 'b.product_id')
                                ->join('tbl_defi_uom as uom', 'uom.uom_id', '=', 'b.uom_id')
                                ->join('tbl_purc_packing as pack', 'pack.packing_id', '=', 'b.packing_id')
                                ->select('p.product_id', 'p.product_name', 'p.product_arabic_name', 'p.product_code',
                                    'b.product_barcode_id', 'b.product_barcode_barcode', 'b.uom_id', 'b.packing_id',
                                    'uom.uom_name', 'pack.packing_name')
                                ->get();
            }else{
                $modelName = 'App\Models\\' .$formCase['form_cases_modelname'];
                array_push($columnName,$modelName::primaryKeyName());
                $data['primaryKeyName'] = $modelName::primaryKeyName();
                if($helpType == 'lpoPoHelp'){
                    $data['table'] = $modelName::where('lpo_dtl_generate_lpo',1)->orderBy($orderby)->get($allcolumnsfields);
                }else if($helpType == 'lpoPoQuotationHelp'){
                    $data['table'] = $modelName::where('lpo_dtl_generate_quotation',1)->orderBy($orderby)->get($allcolumnsfields);
                }else if($helpType == 'demandApprovalHelp'){
                    $data['table'] = $modelName::select($columnName)->groupBy($columnName)->orderBy('demand_approval_dtl_code')->get($columnName);
                }else if($orderby != ''){
                    $data['table'] = $modelName::orderBy($orderby)->get($allcolumnsfields);
                }else{
                    $data['table'] = $modelName::get($allcolumnsfields);
                }
               // dd($data['table']->toArray());
            }
        }
        catch(ModelNotFoundException $exception)
        {
            return abort(404);
        }


        return view('common.help',compact('data'));
    }
    public function openNotification()
    {
        try {
            $data['title'] = 'Notification';
        }
        catch(ModelNotFoundException $exception)
        {
            return abort(404);
        }
        return view('common.notification',compact('data'));
    }
    public function ReceiptAccountData($moduleName,$caseType){
        //dd($moduleName ."---". $caseType);
        $formCase = TblSoftFormCases::where('form_cases_casename',$caseType)->firstOrFail();
        try {
            $data['caseType'] = $caseType;
            $data['title'] = $formCase['form_cases_title'];
            $data['path-form'] = $caseType.'/form';
            $data['path'] = $moduleName ."/". $caseType;
            $headings = explode(',', $formCase['form_cases_heading']);
            $data['headings'] = $headings;
            $columnName = explode(',', $formCase['form_cases_column_name']);
            $data['columnName'] = $columnName;
            $modelName = 'App\Models\\' .$formCase['form_cases_modelname'];
            array_push($columnName,$modelName::primaryKeyName());
            $data['primaryKeyName'] = $modelName::primaryKeyName();
            $activeCaseTypeAcc = ['crv','brv','jv','cpv','bpv','obv'];
            $activeCaseTypeStock = ['sp','dp','ep'];
            $activeCaseTypeSale = ['day-opening','day-closing','payment-handover','payment-received'];
            if(in_array($caseType,$activeCaseTypeAcc)){
                $columnSelect = $columnName;
                array_push($columnSelect,DB::raw('SUM(voucher_debit)total_voucher_debit'));
                $data['path-form'] = 'accounts/'.$caseType.'/form';
                $data['table'] = $modelName::select($columnSelect)->where('voucher_type',$caseType)->groupBy($columnName)->get();
            }else if(in_array($caseType,$activeCaseTypeStock)){
                $data['path-form'] = 'stock-item/'.$caseType.'/form';
                $columnSelect = $columnName;
                $data['table'] = $modelName::select($columnSelect)->where('item_type',$caseType)->groupBy($columnName)->get();
            }else if(in_array($caseType,$activeCaseTypeSale)){
                $data['path-form'] = 'day/'.$caseType.'/form';
                $columnSelect = $columnName;
                $data['table'] = $modelName::select($columnSelect)->where('day_case_type',$caseType)->groupBy($columnName)->get();
            }
        }
        catch(ModelNotFoundException $exception)
        {
            return abort(404);
        }
        return view('common.datatable', compact('data'));
    }
    public function selectItems(Request $request, $str1 = null ,$str2 = null)
    {
        //  $param = explode( '-', $parameter);
        $data = [];
        $sorted = [];
        if($str1 == 'productHelp' || $str2 == 'productHelp'){
            $products = TblPurcProduct::get(['product_id','product_name','product_code']);
            $products->map(function($d){
                $d['name'] = $d['product_name'];
                $d['code'] = $d['product_code'];
                $d['type'] = 'product';
                return $d;
            });
            foreach ($products as $product){
                array_push($sorted,$product);
            }
        }
        if($str1 == 'groupItemsHelp' || $str2 == 'groupItemsHelp'){
            $GroupItems = TblPurcGroupItem::get(['group_item_id','group_item_name','group_item_code']);
            $GroupItems->map(function($d){
                $d['name'] = $d['group_item_name'];
                $d['code'] = $d['group_item_code'];
                $d['type'] = 'Group Item';
                return $d;
            });
            foreach ($GroupItems as $GroupItem){
                array_push($sorted,$GroupItem);
            }
        }

        $collection = collect($sorted);
        $data = $collection->sortBy('name');
        return view('common.select-items',compact('data'));
    }


    function ExRate($id){
        $rate = TblDefiCurrency::select('currency_rate')->where('currency_id',$id)->first();
        return response()->json($rate);
    }
    function getMenuDtl(){
        $data = [];
        DB::beginTransaction();
        try{
            $data = TblSoftMenuDtl::get();

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
        return $this->jsonSuccessResponse($data, 'Data Updated', 200);
    }
    public function getProductDetail($type,$product_id=null){
        $barcode = $product_id;
        try {
            $data = [];
            if($type == 'ajax_prod_search'){
                $data['pur_rate'] = TblPurcProductBarcodePurchRate::where('product_barcode_barcode',$product_id)->where('branch_id',auth()->user()->branch_id)->first();
                $prod = TblPurcProductBarcode::where('product_barcode_barcode',$product_id)->first('product_id');
                $product_id = $prod->product_id;

            }
            if(isset($product_id) && !empty($product_id)){
                $data['product'] = ViewPurcProductBarcode::where('product_id',$product_id)->where('product_barcode_barcode',$barcode)->get();
                $data['all_branches'] = DB::table('tbl_soft_user_branch')
                    ->join('tbl_soft_branch', 'tbl_soft_user_branch.branch_id', '=', 'tbl_soft_branch.branch_id')
                    ->where('tbl_soft_user_branch.user_id', Auth()->user()->id)
                    ->where('tbl_soft_user_branch.branch_id','!=', Auth()->user()->branch_id)
                    ->select('tbl_soft_user_branch.*','tbl_soft_branch.branch_name','tbl_soft_branch.branch_short_name')
                    ->get();
            }
        }catch (Exception $e) {
            $data['error'] = "Product not Found..";
            DB::rollback();
        }
        if($type == 'ajax_prod_search'){
            return view('common.single-product-detail-block',compact('data'));
        }else{
            return view('common.single-product-detail',compact('data'));
        }

    }

    public static function getProductStockDetailByBarcode($barcode=null){
        try {
            $data = [];
            if(isset($barcode) && !empty($barcode)){
                $data['product'] = ViewPurcProductBarcode::where('product_barcode_barcode',$barcode)->orderby('product_barcode_id')->get();
                $data['all_branches'] = DB::table('tbl_soft_user_branch')
                    ->join('tbl_soft_branch', 'tbl_soft_user_branch.branch_id', '=', 'tbl_soft_branch.branch_id')
                    ->where('tbl_soft_user_branch.user_id', Auth()->user()->id)
                    ->where('tbl_soft_user_branch.branch_id','!=', Auth()->user()->branch_id)
                    ->select('tbl_soft_user_branch.*','tbl_soft_branch.branch_name','tbl_soft_branch.branch_short_name')
                    ->get();
            }
        }catch (Exception $e) {
            $data['error'] = "Product not Found..";
            DB::rollback();
        }

        return view('common.single-product-detail-toast',compact('data'))->render();
    }

    public function userPageSetting(Request $request){
        $data = [];
        DB::beginTransaction();

        try {

            TblSoftUserPageSetting::where('user_page_setting_document_type',$request->document_type)
                ->where('user_page_setting_user_id',auth()->user()->id)->delete();

            $colWidth = explode(",",$request->colWidth);
            $colHide = explode(",",$request->colHide);

            $data = (object)[
                'colWidth' =>  $colWidth,
                'colHide' =>  $colHide,
            ];

            $pagSet = new TblSoftUserPageSetting();
            $pagSet->user_page_setting_id = Utilities::uuid();
            $pagSet->user_page_setting_document_type = $request->document_type;
            $pagSet->user_page_setting_data = serialize($data);
            $pagSet->business_id = auth()->user()->business_id;
            $pagSet->company_id = auth()->user()->company_id;
            $pagSet->branch_id = auth()->user()->branch_id;
            $pagSet->user_page_setting_user_id = auth()->user()->id;
            $pagSet->user_page_setting_type = 'form';
            $pagSet->save();

        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, "Page setting updated.", 200);
    }

    public function userReportSetting(Request $request){
        $data = [];
        $validator = Validator::make($request->all(), [
            'report_branch_ids' => 'required',
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 200);
        }
        DB::beginTransaction();
        try {
            TblSoftUserPageSetting::where('user_page_setting_document_type',$request->report_case)
                ->where('user_page_setting_user_id',auth()->user()->id)->delete();

            $saveSaticCriteria['branch_id'] = $request->report_branch_ids;

            if(isset($request->date_from) && isset($request->date_to)){
                $saveSaticCriteria['from_date'] = $request->from_date;
                $saveSaticCriteria['to_date'] = $request->to_date;
            }
            if(isset($request->date)){
                $saveSaticCriteria['date'] = $request->date;
            }
            if(isset($request->sales_type)){
                $saveSaticCriteria['sales_type'] = $request->sales_type;
            }
            if(isset($request->sale_types_multiple)){
                $saveSaticCriteria['sale_types_multiple'] = $request->sale_types_multiple;
            }
            if(isset($request->product_id)){
                $saveSaticCriteria['product_id'] = $request->product_id;
            }
            if(isset($request->product_ids)){
                $saveSaticCriteria['multi_products'] = $request->product_ids;
            }
            if(isset($request->chart_account)){
                $saveSaticCriteria['chart_account'] = $request->chart_account;
            }
            if(isset($request->chart_account_multiple)){
                $saveSaticCriteria['chart_account_multiple'] = $request->chart_account_multiple;
            }
            if(isset($request->customer_ids)){
                $saveSaticCriteria['customer_ids'] = $request->customer_ids;
            }
            if(isset($request->supplier_ids)){
                $saveSaticCriteria['supplier_ids'] = $request->supplier_ids;
            }
            if(isset($request->voucher_types)){
                $saveSaticCriteria['voucher_types'] = $request->voucher_types;
            }
            if(isset($request->payment_types)){
                $saveSaticCriteria['payment_types'] = $request->payment_types;
            }
            if(isset($request->supplier_group)){
                $saveSaticCriteria['supplier_group'] = $request->supplier_group;
            }
            if(isset($request->customer_group)){
                $saveSaticCriteria['customer_group'] = $request->customer_group;
            }
            if(isset($request->product_group)){
                $saveSaticCriteria['product_group'] = $request->product_group;
            }
            if(isset($request->store)){
                $saveSaticCriteria['store'] = $request->store;
            }
            if(isset($request->users_ids)){
                $saveSaticCriteria['users_ids'] = $request->users_ids;
            }
            //dd($saveSaticCriteria);
            $outer_filterList = [];
            $createList = 0;
            foreach ($request->outer_filterList as $key=>$outer){
                $outer_filterList[$key]['outer_clause'] = $outer['outer_clause'];
                foreach($outer['inner_filterList'] as $k=>$inner){
                    $value = 0;
                    if(isset($inner['val']) && isset($inner['key_type'])){
                        if($inner['key_type'] == 'number' && $inner['val'] != ""){
                            $value = 1;
                        }
                        if($inner['key_type'] == 'varchar2' && !empty($inner['val'])){
                            $value = 1;
                        }
                        if($inner['key_type'] == 'date' && isset($inner['val_to'])){
                            $value = 1;
                        }
                    }
                    if(isset($inner['key']) && isset($inner['conditions']) && $value == 1){
                        $outer_filterList[$key]['inner_filterList'][$k]['key'] = $inner['key'];
                        $outer_filterList[$key]['inner_filterList'][$k]['key_type'] = $inner['key_type'];
                        $outer_filterList[$key]['inner_filterList'][$k]['conditions'] = $inner['conditions'];
                        $outer_filterList[$key]['inner_filterList'][$k]['val'] = $inner['val'];
                        if(isset($inner['val_to'])){
                            $outer_filterList[$key]['inner_filterList'][$k]['val_to'] = $inner['val_to'];
                        }
                        $outer_filterList[$key]['inner_filterList'][$k]['inner_clause_item'] = isset($inner['inner_clause_item'])?$inner['inner_clause_item']:"";
                        $createList = 1;
                    }
                }
            }
            if($createList == 0){
                $outer_filterList = [];
            }
            $data = (object)[
                'saveSaticCriteria' =>  $saveSaticCriteria,
                'saveDynamicCriteria' =>  $outer_filterList,
            ];

            $pagSet = new TblSoftUserPageSetting();
            $pagSet->user_page_setting_id = Utilities::uuid();
            $pagSet->user_page_setting_document_type = $request->report_case;
            $pagSet->user_page_setting_data = serialize($data);
            $pagSet->business_id = auth()->user()->business_id;
            $pagSet->company_id = auth()->user()->company_id;
            $pagSet->branch_id = auth()->user()->branch_id;
            $pagSet->user_page_setting_user_id = auth()->user()->id;
            $pagSet->user_page_setting_type = 'report';
            $pagSet->save();

        }catch (Exception $e) {
            DB::rollback();
            return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        DB::commit();
        return $this->jsonSuccessResponse($data, "Page setting updated.", 200);
    }

    public function selectMultipleProducts(Request $request){
        $data = [];
        $data['supplier_id'] = $request['supplier_id'];
        $data['form_type']  = $request->form_type ?? '';
        $dataSql = ViewPurcProductBarcodeHelp::where('product_barcode_id', '<>', '0');
        /*if(!empty($data['supplier_id'])){
            $dataSql = $dataSql->where('supplier_id',$data['supplier_id']);
        }*/
        $data['list'] = $dataSql->where('business_id',auth()->user()->business_id)->limit(50)->get()->toArray();
        //dd($data);
        return view('common.select_multi_products',compact('data'));
    }
    public function selectMultipleProductsData(Request $request,$type){
       // dd($request->toArray());
        $data = [];
        $page = isset($request['pagination']['page'])?$request['pagination']['page']:1;
        $perpage = isset($request['pagination']['perpage'])?$request['pagination']['perpage']:30;
        $offset = $perpage * ($page-1);
        $pagination = " OFFSET $offset ROWS FETCH NEXT $perpage ROWS ONLY";

        $filters = isset($request['query']['filters'])?$request['query']['filters']:"";
        if($type =='product'){
            $where = "";
            $supplierField = "";
            $supplierTable = "";
            $supplierWhere = "";
            $branch_id = auth()->user()->branch_id;

            if(isset($filters['supplierSearch']) && !empty($filters['supplierSearch'])){
               // dd($filters['supplierSearch']);
                $supplierField = "PROD_FOC.SUPPLIER_ID,";
                $supplierTable = "TBL_PURC_PRODUCT_FOC  PROD_FOC,";
                $supplierWhere = "and  PROD.PRODUCT_ID =   PROD_FOC.PRODUCT_ID  and  PROD_FOC.SUPPLIER_ID in('".implode("','",$filters['supplierSearch'])."')";
            }
            if(isset($filters['productGroupSearch']) && !empty($filters['productGroupSearch'])){
                $pgi = \App\Models\ViewPurcGroupItem::where('group_item_id',$filters['productGroupSearch'])->first(['group_item_id','group_item_name_code_string']);
                $where .= "and grp_item.group_item_name_code_string like '".$pgi->group_item_name_code_string."%'";
            }
            if(isset($filters['generalSearch']) && !empty($filters['generalSearch'])){
                $str = strtoupper($filters['generalSearch']);
                $replaced_str = str_replace(' ', '%', trim($str));
                $where .= " and (upper(product_barcode_barcode) Like '%".$replaced_str."%'
                            OR upper(PROD.product_name) like '%".$replaced_str."%')
                        order by
                        Case
                            WHEN upper(PROD.product_name) Like '".$str."' THEN 1
                            WHEN upper(PROD.product_name) Like '".$str."%' THEN 2
                            WHEN upper(PROD.product_name) Like '%".$str."' THEN 4
                            Else 3
                        END,PROD.product_name";
            }

$dataQry = "SELECT
                PROD.*,
                PROD_PUR_RATE.PRODUCT_BARCODE_PURCHASE_RATE,
                PROD_PUR_RATE.PRODUCT_BARCODE_COST_RATE
            FROM (
                SELECT
                    $supplierField
                    PROD.PRODUCT_ID ,
                    PRODUCT_CODE   ,
                    PRODUCT_NAME ,
                    PROD.PRODUCT_BARCODE_ID ,
                    PRODUCT_BARCODE_BARCODE,
                    PROD.GROUP_ITEM_ID ,
                    PROD.GROUP_ITEM_NAME ,
                    UOM_ID ,
                    UOM_NAME ,
                    PRODUCT_BARCODE_PACKING,
                    PRODUCT_BARCODE_STOCK_LIMIT_REORDER_QTY ,
                    PRODUCT_BARCODE_SHELF_STOCK_MAX_QTY,
                    PRODUCT_BARCODE_SHELF_STOCK_MIN_QTY ,
                    PRODUCT_BARCODE_STOCK_CONS_DAY,
                    GROUP_ITEM_NAME_CODE_STRING,
                    GROUP_ITEM_NAME_STRING
                FROM
                    $supplierTable
                    vw_purc_group_item GRP_ITEM   ,  VW_PURC_PRODUCT_BARCODE_DTL PROD
                    LEFT JOIN  TBL_PURC_PRODUCT_BARCODE_DTL PRO_DTL  ON
                    (
                        PROD.PRODUCT_BARCODE_ID =  PRO_DTL.PRODUCT_BARCODE_ID  AND  PRO_DTL.BRANCH_ID = $branch_id
                    )
                WHERE  PROD.group_item_id = GRP_ITEM.group_item_id
                       $supplierWhere $where
            ) PROD
                LEFT JOIN TBL_PURC_PRODUCT_BARCODE_PURCH_RATE  PROD_PUR_RATE ON
                ( PROD.PRODUCT_BARCODE_ID = PROD_PUR_RATE.PRODUCT_BARCODE_ID AND PROD_PUR_RATE.BRANCH_ID = $branch_id )
                $pagination ";

           // dd($dataQry);
            $getData = DB::select($dataQry);
           // dd($getData);
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
                $packing = !empty($list->product_barcode_packing)?$list->product_barcode_packing:1;
                $store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;
                $list->stock = $store_stock;
                $SuggestedQty1 = Utilities::SuggestedQty1($list->product_barcode_shelf_stock_max_qty,$store_stock);
                $list->suggest_qty_1 = number_format($SuggestedQty1 / $packing,3);
                $last_define_date_consumption_qty = !empty($list->product_barcode_stock_cons_day)?$list->product_barcode_stock_cons_day:0;
                $SuggestedQty2 = Utilities::SuggestedQty2($last_define_date_consumption_qty,$store_stock,$list->product_id,auth()->user()->branch_id);

                $list->suggest_qty_2 =  number_format($SuggestedQty2 / $packing,3);

                $list->qty_last_consumption_days =  number_format($SuggestedQty2 / $packing,3);
                $list->last_consumption_days =  $last_define_date_consumption_qty;

                if(isset($filters['supplierSearch']) && !empty($filters['supplierSearch'])){
                    $supplier = ViewPurcSupplier::where('supplier_id',$list->supplier_id)->first(['supplier_name']);
                    $list->supplier_name = $supplier->supplier_name;
                }else{
                    $list->supplier_name = "";
                }
                array_push($data,(array)$list);
            }
        }
        $dataList = [
            "meta"=> [
                "page"=> $page,
                "pages"=> '',
                "perpage"=> $perpage,
                "total"=> '',
                "sort"=> "asc",
                "field"=> "ShipDate"
            ],
            'data' => $data
        ];
        return response()->json($dataList);
    }
}
