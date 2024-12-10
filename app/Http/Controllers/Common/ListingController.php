<?php

namespace App\Http\Controllers\Common;

use Exception;
use Validator;
use App\Library\Utilities;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TblSoftFilterType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TblSoftListingStudio;

// db and Validator
use Illuminate\Support\Facades\Auth;
use App\Models\Defi\TblDefiConstants;
use Illuminate\Database\QueryException;
use App\Models\TblSoftListingUserFilterSave;
use App\Models\TblSoftListingStudioDimension;
use App\Models\TblSoftListingStudioJoinTable;
use App\Models\TblSoftListingStudioUserFilter;
use App\Models\TblListingDownload;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$caseType,$subType=null)
    {
//        dd($request->toArray());
//
        if($subType){
            $case_name = $subType;
        }else{
            $case_name = $caseType;
        }

        if(!TblSoftListingStudio::where('listing_studio_case',$case_name)->exists()){
            return abort('404');
        }
        $listing = TblSoftListingStudio::where('listing_studio_case',$case_name)->first();
        $listing_studio_query = unserialize($listing->listing_studio_query);
//        dd( $listing);
        $data['case'] = $case_name;
        $data['caseType'] = $caseType;
        if($subType){
            $data['path-form'] = $caseType.'/'.$subType.'/form';
            $data['path'] = $caseType.'/'.$subType;
        }else{
            $data['path-form'] = $case_name.'/form';
            $data['path'] = $case_name;
        }
        $table_name = $listing_studio_query->table_name;
        $data['title'] = $listing->listing_studio_title;
        $data['menu_dtl_id'] = $listing->menu_dtl_id;
        $data['table_columns'] = [];
        $Dimension = TblSoftListingStudioDimension::where('listing_studio_id',$listing->listing_studio_id)->orderby('sr_no')->get();
        $DimensionJoin = TblSoftListingStudioJoinTable::where('listing_studio_id',$listing->listing_studio_id)->get();

        for($i=0;$i<count($Dimension);$i++){
            $data['table_columns'][$Dimension[$i]['listing_studio_dimension_column_name']] = $Dimension[$i]['listing_studio_dimension_column_title'];
        }
        for($i=0;$i<count($DimensionJoin);$i++){
            $data['table_columns'][$DimensionJoin[$i]['listing_studio_join_table_column_name']] = $DimensionJoin[$i]['listing_studio_join_table_column_title'];
        }
        if(isset($listing_studio_query->metricTitles) && !empty($listing_studio_query->metricTitles)){
            $metricTitles = explode(',', $listing_studio_query->metricTitles);;
            for($i=0;$i<count($metricTitles);$i++){
                $data['table_columns'][strtolower($metricTitles[$i])] = ucfirst($metricTitles[$i]);
            }
        }
        if($request->ajax()) {
            $modelName = Utilities::getModelFromTable($listing->listing_studio_table_name);
            $data['primaryKeyName'] = $modelName::primaryKeyName();
            // dd($data['primaryKeyName'] );
            $tbl_1 = 'tbl_1';
            $tbl_1_alias = 'tbl_1.';
            if (isset($request['query']['filters']) && !empty($request['query']['filters'])) {
                $userFutureData = [];
                parse_str($request['query']['filters'], $get_filters_array);
               // dd($get_filters_array['outer_filterList']);
                if(!isset($get_filters_array['disable_filters'])){
                    if(isset($get_filters_array['outer_filterList'])){
                        $default_filter_active = false;
                        $default_filter = '(';
                        $i = 1;
                        foreach($get_filters_array['outer_filterList'] as $outer_report_filter){
                            $andClauseChecked = false;
                            $default_filter .= '(';
                            foreach ($outer_report_filter['inner_filterList'] as $inner_filter_list){
                                if(!empty($inner_filter_list['listing_studio_default_filter_name']) && !empty($inner_filter_list['listing_studio_default_filter_condition']) && !empty($inner_filter_list['listing_studio_default_filter_field_type'])) {
                                    // if Yes No
                                    if($inner_filter_list['listing_studio_default_filter_field_type'] == 'boolean'){
                                        if ($inner_filter_list['listing_studio_default_filter_condition'] == 'yes'){
                                            $default_filter .=  'lower('.$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') like \'%yes%\' OR ';
                                            $default_filter .=  'lower('.$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') = 1';
                                        }
                                        if ($inner_filter_list['listing_studio_default_filter_condition'] == 'no'){
                                            $default_filter .=  'lower('.$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') like \'%no%\' OR ';
                                            $default_filter .=  'lower('.$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') = 0 ';
                                        }
                                    }
                                    // if number
                                    if($inner_filter_list['listing_studio_default_filter_field_type'] == 'number'){
                                        if($inner_filter_list['listing_studio_default_filter_condition'] == 'between'){
                                            $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' between '. $inner_filter_list['listing_studio_default_filter_value'] .' AND '. $inner_filter_list['listing_studio_default_filter_value_2'];
                                        }elseif ($inner_filter_list['listing_studio_default_filter_condition'] == 'null'){
                                            $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' is null';
                                        }elseif ($inner_filter_list['listing_studio_default_filter_condition'] == 'not null'){
                                            $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' is not null';
                                        }else{
                                            foreach ($inner_filter_list['listing_studio_default_filter_value'] as $filter_value){
                                                $default_filter .=  'lower('.$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') '. $inner_filter_list['listing_studio_default_filter_condition'] ." '". strtolower($filter_value)."' OR ";
                                            }
                                            $default_filter = rtrim($default_filter, " OR ");
                                        }
                                    }
                                    // if date
                                    if($inner_filter_list['listing_studio_default_filter_field_type'] == 'date'){
                                        // if between
                                        if($inner_filter_list['listing_studio_default_filter_condition'] == 'between'){
                                            if(strtolower($inner_filter_list['listing_studio_default_filter_name']) == 'updated_at' || strtolower($inner_filter_list['listing_studio_default_filter_name']) == 'created_at'){
                                                $from = "TO_DATE('".$inner_filter_list['listing_studio_default_filter_value']." 12:00:00 am', 'dd/mm/yyyy HH:MI:SS pm')";
                                                $to = "TO_DATE('".$inner_filter_list['listing_studio_default_filter_value_2']." 11:59:59 pm', 'dd/mm/yyyy HH:MI:SS pm')";
                                            }else{
                                                $from = "TO_DATE('".$inner_filter_list['listing_studio_default_filter_value']."', 'dd/mm/yyyy')";
                                                $to = "TO_DATE('".$inner_filter_list['listing_studio_default_filter_value_2']."', 'dd/mm/yyyy')";
                                            }
                                            $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' between '. $from .' AND '. $to;
                                        }
                                    }
                                    // if varchar
                                    if($inner_filter_list['listing_studio_default_filter_field_type'] == 'varchar2'){
                                        if ($inner_filter_list['listing_studio_default_filter_condition'] == 'null'){
                                            $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' is null';
                                        }elseif ($inner_filter_list['listing_studio_default_filter_condition'] == 'not null'){
                                            $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' is not null';
                                        }else{
                                            foreach ($inner_filter_list['listing_studio_default_filter_value'] as $filter_value){
                                                if($inner_filter_list['listing_studio_default_filter_condition'] == '=' || $inner_filter_list['listing_studio_default_filter_condition'] == '!=' || gettype($inner_filter_list['listing_studio_default_filter_value']) == 'integer'){
                                                    $default_filter .=  'lower('.$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') '. $inner_filter_list['listing_studio_default_filter_condition'] ." '". strtolower($filter_value)."' OR ";
                                                }else {
                                                    $default_filter .= 'lower(' . $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'] . ') ' . $inner_filter_list['listing_studio_default_filter_condition'] . " '%" . strtolower($filter_value) . "%' OR ";
                                                }
                                            }
                                            $default_filter = rtrim($default_filter, " OR ");
                                        }
                                    }
                                    $default_filter_active = true;
                                    $default_filter .= ' OR ';
                                    /* start save for user future  */
                                    $val_1 = isset($inner_filter_list['listing_studio_default_filter_value'])?$inner_filter_list['listing_studio_default_filter_value']:"";
                                    $val_2 = isset($inner_filter_list['listing_studio_default_filter_value_2'])?$inner_filter_list['listing_studio_default_filter_value_2']:"";
                                    $case_name = isset($inner_filter_list['listing_studio_default_filter_case_name'])?$inner_filter_list['listing_studio_default_filter_case_name']:"";
                                    $userFutureDataObj = (object)[
                                        'name' => $inner_filter_list['listing_studio_default_filter_name'],
                                        'filed_type' => $inner_filter_list['listing_studio_default_filter_field_type'],
                                        'condition' => $inner_filter_list['listing_studio_default_filter_condition'],
                                        'case_name' => $case_name,
                                        'val_1' => $val_1,
                                        'val_2' => $val_2,
                                        'sr_no' => $i
                                    ];
                                    array_push($userFutureData,$userFutureDataObj);
                                    $andClauseChecked = true;
                                    /* end save for user future  */
                                } // check value not empty
                            } // inner loop
                            if($andClauseChecked){
                                $default_filter = rtrim($default_filter, " OR ");
                                $default_filter .= ') AND';
                                $i = $i + 1;
                            }else{
                                $default_filter = rtrim($default_filter, "(");
                            }
                        } // outer loop
                        $default_filter = rtrim($default_filter, " AND ");
                        $default_filter .= ')';
                        if($default_filter_active == false){
                            $default_filter = '';
                        }
                    }
                }

            }
            $userSearch = '';

            // Check If the Search Result is Already Set
            $segment = $request->segment(2);
            if(isset($_COOKIE[$segment . '_listing_search'])){
                $cookie = $_COOKIE[$segment . '_listing_search'];
            }

            if (isset($request['query']['generalSearch']) && !empty($request['query']['generalSearch'])) {
                $generalSearch = str_replace(" " , "%" , $request['query']['generalSearch']);
                $columns_name = explode(',', $listing_studio_query->columns_name);
                $userSearch = '(';
                for($i = 0; $i<count($columns_name); $i++){
                    if(gettype($request['query']['generalSearch']) == 'integer'){
                        $userSearch .=  'lower('.trim($columns_name[$i]).') like '. strtolower($generalSearch);
                    }else{
                        $userSearch .=  'lower('.trim($columns_name[$i]).") like '%". strtolower($generalSearch)."%'";
                    }
                    $userSearch .= ' OR ';
                }
                $userSearch = rtrim($userSearch, " OR ");
                $userSearch .= ')';
            }

            if(isset($cookie)){
                $generalSearch = str_replace(" " , "%" , $cookie);
                $columns_name = explode(',', $listing_studio_query->columns_name);
                $userSearch = '';
                $userSearch = '(';
                for($i = 0; $i<count($columns_name); $i++){
                    if(gettype($cookie) == 'integer'){
                        $userSearch .=  'lower('.trim($columns_name[$i]).') like '. strtolower($generalSearch);
                    }else{
                        $userSearch .=  'lower('.trim($columns_name[$i]).") like '%". strtolower($generalSearch)."%'";
                    }
                    $userSearch .= ' OR ';
                }
                $userSearch = rtrim($userSearch, " OR ");
                $userSearch .= ')';
            }

            $orderBy = '';
            if (isset($request['sort']['sort']) && !empty($request['sort']['sort'])) {
                $orderBy = 'ORDER BY '.$request['sort']['field'].' '.$request['sort']['sort'];
            }else{
                $orderBy = $listing_studio_query->orderBy;
            }

           // $limit = $listing_studio_query->limit;
            $limit = 'FETCH FIRST 500 ROWS ONLY';
        //    dump($listing_studio_query);
                /* start set {~where clause~} */
            $where = 'where ';
            if(isset($listing_studio_query->listing_business_or_branch)){
                if($listing_studio_query->listing_business_or_branch == 'branch'){
                    $where .= '('.$tbl_1_alias.'business_id = '.auth()->user()->business_id.' AND '.$tbl_1_alias.'company_id = '.auth()->user()->company_id.' AND '.$tbl_1_alias.'branch_id = '.auth()->user()->branch_id.')';
                }else{
                    $where .= '('.$tbl_1_alias.'business_id = '.auth()->user()->business_id.' AND '.$tbl_1_alias.'company_id = '.auth()->user()->company_id.')';
                }
            }else{
                $where .= $listing_studio_query->fixedWhere;
            }
            if(!empty($listing_studio_query->where)){
                $where .= ' AND '.$listing_studio_query->where;
            }
            if(!empty($userSearch)){
                $where .= ' AND '.$userSearch;
            }
            if(!empty($default_filter)){
                $where .= ' AND '.$default_filter;
            }

            // if only date filter
            $today = date('d/m/Y');
            $time_from = '12:00:00 am';
            $time_to = '11:59:59 pm';
            $global_filter_bollean = false;
            if (isset($request['query']['globalFilters'])) {
                $globalFilters = $request['query']['globalFilters'];
                if(isset($globalFilters['date'])){
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
                        $where .=  ' AND ('.$tbl_1_alias.'created_at between '. $from .' AND '. $to.') ';
                    }
                    $global_filter_bollean = true;
                }

            }
            if(!$global_filter_bollean){
                $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                $where .=  ' AND ('.$tbl_1_alias.'created_at between '. $from .' AND '. $to.') ';
            }
            /* end set {~where clause~} */
            if(!empty($listing_studio_query->groupBy)){
                $groupBy = 'GROUP BY('.$listing_studio_query->columns_name.')';
            }else{
                $groupBy = '';
            }
            $columns_name = $listing_studio_query->columns_name;
            $metric = $listing_studio_query->metric;
            if($caseType == 'product_old'){
                $metric .= ',tbl_1.product_barcode_id';
                $metric .= ',tbl_1.product_barcode_packing';
                $limit = 'FETCH FIRST 100 ROWS ONLY';
            }
            $query  = 'Select '.$columns_name.' '.$metric.' from '.$table_name.' '.$where.' '.$groupBy.' '.$orderBy.' '.$limit;
//        dd(  $query );
            $query = str_replace('$user_id$',Auth::user()->id,$query);
            $dataSql = DB::select($query);
            if($caseType == 'product_old'){
                $items = [];
                $now = new \DateTime("now");
                $today_format = $now->format("d-m-Y");
                $date = date('Y-m-d', strtotime($today_format));
                foreach ($dataSql as $row){
                    $arr = [
                        $row->product_id,
                        $row->product_barcode_id,
                        auth()->user()->business_id,
                        auth()->user()->company_id,
                        auth()->user()->branch_id,
                        '',
                        $date
                    ];
                    // TEMP FIX FOR ADMINALNAWRAS
                    // Check SubDomain Of the Project
                    if(TblDefiConstants::where('constants_key','subdomain')->where('constants_status',1)->exists()){
                        $subdomain = TblDefiConstants::where('constants_key','subdomain')->first()->constants_value;
                    }
                    if($subdomain == 'adminalnawras'){
                        $row->stock = 0;
                    }else{
                        // $store_stock =  collect(DB::select('SELECT get_stock_current_qty_date(?,?,?,?,?,?,?) AS code from dual', $arr))->first()->code;
                        // $store_stock =  $store_stock / $row->product_barcode_packing;
                        // $row->stock = number_format($store_stock,3);
                        $row->stock = "-";
                    }

                    unset($row->product_barcode_packing);
                    unset($row->product_barcode_id);
                    array_push($items, $row);
                }
                $dataSql = $items;
            }
            $result = [
                'keyid'=> $data['primaryKeyName'],
                'data' => $dataSql,
                'statuses' => 'success'
            ];
            if(isset($userFutureData)){
                $UserFilterSaveExists = TblSoftListingUserFilterSave::where('listing_user_filter_save_user_id',auth()->user()->id)
                                    ->where('listing_studio_id',$listing->listing_studio_id)->exists();
                if($UserFilterSaveExists){
                    $UserFilterSave = TblSoftListingUserFilterSave::where('listing_user_filter_save_user_id',auth()->user()->id)
                        ->where('listing_studio_id',$listing->listing_studio_id)->first();
                }else{
                    $UserFilterSave = new TblSoftListingUserFilterSave();
                    $UserFilterSave->listing_user_filter_save_id = Utilities::uuid();
                }
                $UserFilterSave->listing_studio_id = $listing->listing_studio_id;
                $UserFilterSave->listing_user_filter_save_query = serialize($userFutureData);
                $UserFilterSave->business_id = auth()->user()->business_id;
                $UserFilterSave->company_id = auth()->user()->company_id;
                $UserFilterSave->branch_id = auth()->user()->branch_id;
                $UserFilterSave->listing_user_filter_save_user_id = auth()->user()->id;
                $UserFilterSave->save();
            }
            return response()->json($result);

        }
        if($caseType == 'product_old'){
            $data['table_columns']['stock'] = 'Stock Qty';
        }
        return view('common.listing',compact('data'));
    }

    public function openListingUserFilterModal($case_name){
        $data = [];
        $listing = TblSoftListingStudio::where('listing_studio_case',$case_name)->first();
        $data['UserFilter'] = TblSoftListingStudioUserFilter::where('listing_studio_id',$listing->listing_studio_id)->get();
        $UserFilterSaveExists = TblSoftListingUserFilterSave::where('listing_user_filter_save_user_id',auth()->user()->id)
            ->where('listing_studio_id',$listing->listing_studio_id)->exists();
        if($UserFilterSaveExists){
            $UserFilterSave = TblSoftListingUserFilterSave::where('listing_user_filter_save_user_id',auth()->user()->id)
                ->where('listing_studio_id',$listing->listing_studio_id)->first();
            $data['queryArray'] = unserialize($UserFilterSave->listing_user_filter_save_query);
            // dd($data['queryArray']);
            $max = [];
            foreach ($data['queryArray'] as $queryArrayMax) {
                $max[] = $queryArrayMax->sr_no;
            }
            if(!empty($max)){
                $data['max'] = max($max);
            }
        }

      //  dd($data);
        return view('common.listing-user-filter',compact('data'));
    }

    public function openListingDownloads($case_name){
        $downloads = TblListingDownload::with('user')
        ->where('LISTING_CASE', $case_name)
        ->where('DELETED', 0)
        ->get();

    return view('common.listing-downloads', compact('downloads'));
    }

    public function deleteListingDownload($id)
{
    $download = TblListingDownload::where('id', $id)->first();

    if ($download) {

        $filePath = 'app/reports/' . $download->file_name;

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        DB::table('tbl_listing_downloads')->where('id', $id)->update(['deleted' => 1]);

        return response()->json([
            'status' => 'success',
            'message' => 'File deleted successfully.'
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'File not found.'
    ]);
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
        //
    }
}
