<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Common\ListingController;
use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSoftFilterType;
use App\Models\TblSoftListingStudio;
use App\Models\TblSoftListingStudioDefaultFilter;
use App\Models\TblSoftListingStudioDimension;
use App\Models\TblSoftListingStudioJoinTable;
use App\Models\TblSoftListingStudioMetric;
use App\Models\TblSoftListingStudioUserFilter;
use App\Models\TblSoftMenuDtl;
use App\Models\TblSoftReportingFilterCase;
use App\Models\ViewAllColumnData;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ListingStudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static $page_title = 'Listing Studio';
    public static $redirect_url = 'listing-studio';
    public static $menu_dtl_id = '74';

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;
        $data['page_data']['create'] = '/'.self::$redirect_url.$this->prefixCreatePage;
        if(isset($id)){
            if(TblSoftListingStudio::where('listing_studio_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['permission'] = self::$menu_dtl_id.'-edit';
                $data['id'] = $id;
                $data['current'] = TblSoftListingStudio::with('listing_studio_user_filter','listing_studio_dimension','listing_studio_default_filter','listing_studio_metric','join_table')->where('listing_studio_id',$id)->first();
                $data['MenuDtl']  = TblSoftMenuDtl::get();
                $max = [];
                foreach ($data['current']->listing_studio_default_filter as $listing_studio_default_filter) {
                    $max[] = $listing_studio_default_filter['listing_studio_default_filter_sr'];
                }
                if(!empty($max)){
                    $data['max'] = max($max);
                }
                //dd($data['current']->listing_studio_query);
                if(!empty($data['current']->listing_studio_query)){
                    $query = unserialize($data['current']->listing_studio_query);
                    $where = 'where ';
                    if(empty($query->where)){
                        $where .= $query->fixedWhere;
                    }
                    if(!empty($query->where)){
                        $where .= $query->where.' AND '.$query->fixedWhere;
                    }
                    $data['query']  = 'Select '.$query->columns_name.' '.$query->metric.' from '.$query->table_name.' '.$where.' '.$query->groupBy.' '.$query->orderBy.' '.$query->limit;
                }else{
                    $data['query'] = '';
                }
            }else{
                abort('404');
            }
        }else{
            $data['permission'] = self::$menu_dtl_id.'-create';
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['listing_studio_code'] = $this->documentCode(TblSoftListingStudio::max('listing_studio_code'),'LS');
        }

        $sorted =  ViewAllColumnData::select('table_name')->groupby('table_name')->get();
        $collection = collect($sorted);
        $data['table_list'] = $collection->sortBy('table_name');
        $data['filter_case_list'] = TblSoftReportingFilterCase::where('reporting_filter_case_entry_status',1)->orderBy('reporting_filter_case_name')->get();
        return view('development.listing_studio.form',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id=null)
    {
      //  dd($request->toArray());
        $data = [];
        if(!isset($id)){
            $validator = Validator::make($request->all(), [
                'listing_studio_case' => 'required|unique:tbl_soft_listing_studio',
            ]);
            if ($validator->fails()) {
                $data['validator_errors'] = $validator->errors();
                return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
            }
        }
        DB::beginTransaction();
        try{
            // TblSoftListingStudio
            if(isset($id)){
                $listing = TblSoftListingStudio::where('listing_studio_id',$id)->first();
            }else{
                $listing = new TblSoftListingStudio();
                $listing->listing_studio_id = Utilities::uuid();
                $listing->listing_studio_code = $this->documentCode(TblSoftListingStudio::max('listing_studio_code'),'LS');;
                $listing->listing_studio_case = $request->listing_studio_case;
            }
            $form_id = $listing->listing_studio_id;
            $listing->listing_studio_title = $request->listing_studio_title;
            $listing->listing_studio_table_name = $request->listing_studio_table_name;
            $listing->listing_studio_date = $request->listing_studio_date;
            $listing->listing_studio_rows_per_page = $request->listing_studio_rows_per_page;
            $listing->listing_studio_sort_colum_name_1 = !empty($request->listing_studio_sort_colum_name_1) ? $request->listing_studio_sort_colum_name_1 : "";
            $listing->listing_studio_sort_colum_name_value_1 = $request->listing_studio_sort_colum_name_value_1;
            $listing->listing_studio_sort_colum_name_2 = !empty($request->listing_studio_sort_colum_name_2) ? $request->listing_studio_sort_colum_name_2 : "";
            $listing->listing_studio_sort_colum_name_value_2 = $request->listing_studio_sort_colum_name_value_2;
            $listing->listing_studio_view_type = $request->listing_studio_view_type;
            $listing->listing_studio_type = $request->listing_studio_select_menu;
            if($request->listing_studio_select_menu == 'main_listing'){
                $listing->menu_dtl_id = $request->listing_studio_select_menu_dtl_id;;
                $parent_menu = (!empty($request->listing_studio_parent_menu) && isset($request->listing_studio_parent_menu))?$request->listing_studio_parent_menu:'';
                $listing->listing_studio_parent_menu = $parent_menu;
            }
            $listing->listing_studio_group_by = isset($request->listing_studio_group_by)?1:0;
            $listing->listing_studio_query = ''; // not value from form // add in bottom
            $listing->listing_studio_entry_status = 1;
            $listing->business_id = auth()->user()->business_id;
            $listing->company_id = auth()->user()->company_id;
            $listing->branch_id = auth()->user()->branch_id;
            $listing->listing_studio_user_id = auth()->user()->id;
            $listing->save();
            // TblSoftListingStudioDimension
            if(count($request->listing_studio_dimension_column_name) == count($request->listing_studio_dimension_column_title)){
                $Dimensions = TblSoftListingStudioDimension::where('listing_studio_id',$id)->get();
                foreach($Dimensions as $Dimension){
                    $del = TblSoftListingStudioDimension::where('listing_studio_dimension_id',$Dimension->listing_studio_dimension_id)->first();
                    $del->delete();
                }
                $dik = 1;
                for($di=0; $di < count($request->listing_studio_dimension_column_name); $di++){
                    $listingDimension = new TblSoftListingStudioDimension();
                    $listingDimension->listing_studio_dimension_id = Utilities::uuid();
                    $listingDimension->listing_studio_id = $listing->listing_studio_id;
                    $listingDimension->listing_studio_dimension_column_name = $request->listing_studio_dimension_column_name[$di];
                    $listingDimension->listing_studio_dimension_column_title = $request->listing_studio_dimension_column_title[$di];
                    $listingDimension->listing_studio_dimension_entry_status = 1;
                    $listingDimension->business_id = auth()->user()->business_id;
                    $listingDimension->company_id = auth()->user()->company_id;
                    $listingDimension->branch_id = auth()->user()->branch_id;
                    $listingDimension->listing_studio_dimension_user_id = auth()->user()->id;
                    $listingDimension->sr_no = $dik;
                    $listingDimension->save();
                    $dik = $dik + 1;
                }
            }
            // TblSoftListingStudioUserFilter
            if(isset($request->user_filter)){
                $UserFilters = TblSoftListingStudioUserFilter::where('listing_studio_id',$id)->get();
                foreach($UserFilters as $UserFilter){
                    $del = TblSoftListingStudioUserFilter::where('listing_studio_user_filter_id',$UserFilter->listing_studio_user_filter_id)->first();
                    $del->delete();
                }
                foreach($request->user_filter as $user_filter){
                    if(!empty($user_filter['listing_studio_user_filter_name']) && !empty($user_filter['listing_studio_user_filter_title']) && !empty($user_filter['listing_studio_user_filter_type'])){
                        $listingUserFilter = new TblSoftListingStudioUserFilter();
                        $listingUserFilter->listing_studio_user_filter_id = Utilities::uuid();
                        $listingUserFilter->listing_studio_id = $listing->listing_studio_id;
                        $listingUserFilter->listing_studio_user_filter_name = $user_filter['listing_studio_user_filter_name'];
                        $listingUserFilter->listing_studio_user_filter_title = $user_filter['listing_studio_user_filter_title'];
                        $listingUserFilter->listing_studio_user_filter_type = $user_filter['listing_studio_user_filter_type'];
                        $listingUserFilter->listing_studio_user_case_name = (isset($user_filter['listing_studio_user_case_name']) && !empty($user_filter['listing_studio_user_case_name']))?$user_filter['listing_studio_user_case_name']:"";
                        $listingUserFilter->listing_studio_user_filter_entry_status = 1;
                        $listingUserFilter->business_id = auth()->user()->business_id;
                        $listingUserFilter->company_id = auth()->user()->company_id;
                        $listingUserFilter->branch_id = auth()->user()->branch_id;
                        $listingUserFilter->listing_studio_user_filter_user_id = auth()->user()->id;
                        $listingUserFilter->save();
                    }
                }
            }
            // TblSoftListingStudioDefaultFilter

            if(isset($request->outer_filterList)){
                $DefaultFilters = TblSoftListingStudioDefaultFilter::where('listing_studio_id',$id)->get();
                foreach($DefaultFilters as $DefaultFilter){
                    $del = TblSoftListingStudioDefaultFilter::where('listing_studio_default_filter_id',$DefaultFilter->listing_studio_default_filter_id)->first();
                    $del->delete();
                }
                $i = 1;
                foreach($request->outer_filterList as $outer_report_filter){
                    foreach ($outer_report_filter['inner_filterList'] as $inner_filter_list){
                        if(!empty($inner_filter_list['listing_studio_default_filter_name']) && !empty($inner_filter_list['listing_studio_default_filter_condition']) && !empty($inner_filter_list['listing_studio_default_filter_field_type'])){
                            $ListingDefaultFilter = new TblSoftListingStudioDefaultFilter();
                            $ListingDefaultFilter->listing_studio_default_filter_id = Utilities::uuid();
                            $ListingDefaultFilter->listing_studio_id = $listing->listing_studio_id;
                            $ListingDefaultFilter->listing_studio_default_filter_name = $inner_filter_list['listing_studio_default_filter_name'];
                            $ListingDefaultFilter->listing_studio_default_filter_field_type = $inner_filter_list['listing_studio_default_filter_field_type'];
                            $ListingDefaultFilter->listing_studio_default_filter_condition = $inner_filter_list['listing_studio_default_filter_condition'];
                            $ListingDefaultFilter->listing_studio_default_filter_value = isset($inner_filter_list['listing_studio_default_filter_value'])?$inner_filter_list['listing_studio_default_filter_value']:"";
                            $ListingDefaultFilter->listing_studio_default_filter_value_2 = isset($inner_filter_list['listing_studio_default_filter_value_2'])?$inner_filter_list['listing_studio_default_filter_value_2']:"";
                            $ListingDefaultFilter->listing_studio_default_filter_sr = $i;
                            $ListingDefaultFilter->save();
                        }
                    }
                    $i++;
                }
            }
            // metric aggregation functions
            if(isset($request->metric)){
                $MetricAggresAll = TblSoftListingStudioMetric::where('listing_studio_id',$id)->get();
                foreach($MetricAggresAll as $MetricAggres){
                    $del = TblSoftListingStudioMetric::where('listing_studio_metric_id',$MetricAggres->listing_studio_metric_id)->first();
                    $del->delete();
                }
                foreach ($request->metric as $metric_field){
                    if(!empty($metric_field['listing_studio_metric_column_name']) && !empty($metric_field['listing_studio_metric_column_title']) && !empty($metric_field['listing_studio_metric_aggregation'])){
                        $MetricAggre = new TblSoftListingStudioMetric();
                        $MetricAggre->listing_studio_metric_id = Utilities::uuid();
                        $MetricAggre->listing_studio_id = $listing->listing_studio_id;
                        $MetricAggre->listing_studio_metric_column_name = $metric_field['listing_studio_metric_column_name'];
                        $MetricAggre->listing_studio_metric_column_title = $metric_field['listing_studio_metric_column_title'];
                        $MetricAggre->listing_studio_metric_aggregation = $metric_field['listing_studio_metric_aggregation'];
                        $MetricAggre->listing_studio_metric_entry_status = 1;
                        $MetricAggre->business_id = auth()->user()->business_id;
                        $MetricAggre->company_id = auth()->user()->company_id;
                        $MetricAggre->branch_id = auth()->user()->branch_id;
                        $MetricAggre->listing_studio_metric_user_id = auth()->user()->id;
                        $MetricAggre->save();
                    }
                }
            }

            // join
            $join_col_name = isset($request->listing_studio_join_table_column_name)?count($request->listing_studio_join_table_column_name):"";
            $join_col_title = isset($request->listing_studio_join_table_column_title)?count($request->listing_studio_join_table_column_title):"";
            if(isset($request->listing_studio_join_name)){
                $joinRows = TblSoftListingStudioJoinTable::where('listing_studio_id',$id)->get();
                if(!empty($joinRows)){
                    TblSoftListingStudioJoinTable::where('listing_studio_id',$id)->delete();
                }
                if(!empty($join_col_name) && !empty($join_col_title) && ($join_col_name == $join_col_title)){
                    $join_sr = 1;
                    for($ji = 0; $ji < $join_col_name; $ji++){
                        TblSoftListingStudioJoinTable::create([
                            'listing_studio_join_table_id'=> Utilities::uuid(),
                            'listing_studio_id'=> $listing->listing_studio_id,
                            'listing_studio_join_table_name'=>  $request->listing_studio_join_name,
                            'listing_studio_join_table_sr_no'=> $join_sr,
                            'listing_studio_join_table_column_name'=> $request->listing_studio_join_table_column_name[$ji],
                            'listing_studio_join_table_column_title'=> $request->listing_studio_join_table_column_title[$ji],
                            'business_id'=> auth()->user()->business_id,
                            'company_id'=> auth()->user()->company_id,
                            'branch_id'=> auth()->user()->branch_id,
                            'listing_join_table_user_id'=> auth()->user()->id,
                            'listing_join_table_entry_status'=> 1,
                        ]);
                    }
                }
            }
            // Query
            $tbl_1 = 'tbl_1';
            $tbl_1_alias = 'tbl_1.';
            $tbl_2 = 'tbl_2';
            $tbl_2_alias = 'tbl_2.';
            $columns_name = '';
            $table_name = '';
            $fixedWhere = '';
            $where = '';
            $orderBy = 'ORDER BY ';
            $limit = '';
            $metric = '';
            $groupBy = '';

            // $table_name
            if(isset($request->listing_studio_table_name)){
                $table_name .= $request->listing_studio_table_name.' '.$tbl_1;
            dd($request->listing_studio_table_name);

                $modelName = Utilities::getModelFromTable($request->listing_studio_table_name);
            }
            // join tbl column
            if(isset($request->listing_studio_join_name) && !empty($request->listing_studio_join_name)) {
                $modelName2 = Utilities::getModelFromTable($request->listing_studio_table_name);
                $table_name .= ' join '.$request->listing_studio_join_name.' '.$tbl_2.' on ';
                $table_name .= '( '. $tbl_1_alias.$modelName::primaryKeyName()  .' = '.$tbl_2_alias.$modelName2::primaryKeyName() .') ';
            }
            // $columns_name
            if(count($request->listing_studio_dimension_column_name) == count($request->listing_studio_dimension_column_title)){
                $columns_name .= $tbl_1_alias.$modelName::primaryKeyName().', ';
                for($di=0; $di < count($request->listing_studio_dimension_column_name); $di++){
                    $columns_name .= $tbl_1_alias.$request->listing_studio_dimension_column_name[$di] . ', ';
                }
                $columns_name = rtrim($columns_name, ", ");
            }
            // join tbl column
            if(!empty($join_col_name) && !empty($join_col_title) && ($join_col_name == $join_col_title)){
                for($ji=0; $ji < $join_col_name; $ji++){
                    $columns_name .= ' ,'.$tbl_2_alias.$request->listing_studio_join_table_column_name[$ji];
                }
            }
            // limit
            if(isset($request->listing_studio_rows_per_page)){
                $limit = ' FETCH FIRST '. $request->listing_studio_rows_per_page.' ROWS ONLY';
            }
            // orderby
            $sort_active = false;
            if(isset($request->listing_studio_sort_colum_name_1) && !empty($request->listing_studio_sort_colum_name_1)){
                $sort_active = true;
                $orderBy .= $tbl_1_alias.$request->listing_studio_sort_colum_name_1.' '.$request->listing_studio_sort_colum_name_value_1.',';
            }
            if(isset($request->listing_studio_sort_colum_name_2) && !empty($request->listing_studio_sort_colum_name_2)){
                $sort_active = true;
                $orderBy .= $tbl_1_alias.$request->listing_studio_sort_colum_name_2.' '.$request->listing_studio_sort_colum_name_value_2.',';
            }
            $orderBy = rtrim($orderBy, ",");
            if($sort_active == false){
                $orderBy = '';
            }
           // default filter where
            if(isset($request->outer_filterList)){
                $default_filter_active = false;
                $default_filter = '(';
                foreach($request->outer_filterList as $outer_report_filter){
                    $default_filter .= '(';
                    foreach ($outer_report_filter['inner_filterList'] as $inner_filter_list){
                        if(!empty($inner_filter_list['listing_studio_default_filter_name']) && !empty($inner_filter_list['listing_studio_default_filter_condition']) && !empty($inner_filter_list['listing_studio_default_filter_field_type'])) {
                            // if number
                            if($inner_filter_list['listing_studio_default_filter_field_type'] == 'number'){
                                if($inner_filter_list['listing_studio_default_filter_condition'] == 'between'){
                                    $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' between '. $inner_filter_list['listing_studio_default_filter_value'] .' AND '. $inner_filter_list['listing_studio_default_filter_value_2'];
                                }elseif ($inner_filter_list['listing_studio_default_filter_condition'] == 'null'){
                                    $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' is null';
                                }elseif ($inner_filter_list['listing_studio_default_filter_condition'] == 'not null'){
                                    $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' is not null';
                                }else{
                                    $default_filter .=  $tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].' '. $inner_filter_list['listing_studio_default_filter_condition'] .' '. $inner_filter_list['listing_studio_default_filter_value'];
                                }
                            }
                            // if date
                            if($inner_filter_list['listing_studio_default_filter_field_type'] == 'date'){
                                // if between
                                if($inner_filter_list['listing_studio_default_filter_condition'] == 'between'){
                                    if(strtolower($inner_filter_list['listing_studio_default_filter_name']) == 'updated_at' || strtolower($inner_filter_list['listing_studio_default_filter_name']) == 'created_at'){
                                        $from = "TO_DATE('".$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_value']." 12:00:00 am', 'dd/mm/yyyy HH:MI:SS pm')";
                                        $to = "TO_DATE('".$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_value_2']." 11:59:59 pm', 'dd/mm/yyyy HH:MI:SS pm')";
                                    }else{
                                        $from = "TO_DATE('".$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_value']."', 'dd-mm-yyyy')";
                                        $to = "TO_DATE('".$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_value_2']."', 'dd-mm-yyyy')";
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
                                    if($inner_filter_list['listing_studio_default_filter_condition'] == '=' || $inner_filter_list['listing_studio_default_filter_condition'] == '!=' || gettype($inner_filter_list['listing_studio_default_filter_value']) == 'integer'){
                                        $default_filter .=  'lower(' .$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') '. $inner_filter_list['listing_studio_default_filter_condition'] ." '". strtolower($inner_filter_list['listing_studio_default_filter_value'])."'";
                                    }else{
                                        $default_filter .=  'lower(' .$tbl_1_alias.$inner_filter_list['listing_studio_default_filter_name'].') '. $inner_filter_list['listing_studio_default_filter_condition'] ."'%". strtolower($inner_filter_list['listing_studio_default_filter_value'])."%'";
                                    }
                                }
                            }
                            $default_filter_active = true;
                            $default_filter .= ' OR ';
                        } // check value not empty
                    } // inner loop
                    $default_filter = rtrim($default_filter, " OR ");
                    $default_filter .= ') AND';
                } // outer loop
                $default_filter = rtrim($default_filter, " AND ");
                $default_filter .= ')';
                if($default_filter_active == false){
                    $default_filter = '';
                }
            }
            // metric aggregation functions
            $metricTitle = '';
            if(isset($request->listing_studio_group_by)){
                $groupBy = 'GROUP BY('.$columns_name.')';
            }
            if(isset($request->metric)){
                foreach ($request->metric as $metric_field){
                    if(!empty($metric_field['listing_studio_metric_column_name']) && !empty($metric_field['listing_studio_metric_column_title']) && !empty($metric_field['listing_studio_metric_aggregation'])){
                        $metric .= $metric_field['listing_studio_metric_aggregation'].'('.$tbl_1_alias.$metric_field['listing_studio_metric_column_name'].') AS "'.$metric_field['listing_studio_metric_column_title'].'",';
                        $metricTitle .= $metric_field['listing_studio_metric_column_title'].',';
                    }
                }
                $metric = rtrim($metric, ",");
                $metricTitle = rtrim($metricTitle, ",");
            }
            if(!empty($metric)){
                $groupBy = 'GROUP BY('.$columns_name.')';
                $metric = ','.$metric;
            }
            if(!empty($default_filter)){
                $where .= $default_filter;
            }
            $listing_business_or_branch = $request->listing_studio_view_type;
            // $query .= $columns_name.' '.$metric.' from '.$table_name.' '.$where.' '.$groupBy.' '.$orderBy.' '.$limit;
            $query = (object)[
                    'columns_name' => $columns_name,
                    'metric' => $metric,
                    'metricTitles' => $metricTitle,
                    'table_name' => $table_name,
                    'listing_business_or_branch' => $listing_business_or_branch,
                    'where' => $where,
                    'fixedWhere' => $fixedWhere,
                    'groupBy' => $groupBy,
                    'orderBy' => $orderBy,
                    'limit' => $limit,
                ];
           // dd($query);
            $listing = TblSoftListingStudio::where('listing_studio_id',$listing->listing_studio_id)->first();
            $listing->listing_studio_query = serialize($query);
            $listing->save();
            if($request->listing_studio_select_menu == 'main_listing'){
                $menuDtl = TblSoftMenuDtl::where('menu_dtl_id',$listing->menu_dtl_id)->first();
                if(isset($parent_menu) && !empty($parent_menu)){
                    $menuDtl->menu_dtl_link = $this->prefixIndexPage.$parent_menu.'/'.$request->listing_studio_case;
                }else{
                    $menuDtl->menu_dtl_link = $this->prefixIndexPage.$request->listing_studio_case;
                }

                $menuDtl->menu_dtl_table_name = $listing->listing_studio_table_name;
                $menuDtl->save();
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
        //
    }

    public function getFiledConditions($casetype,$field)
    {
        $data = [];
        DB::beginTransaction();
        try{

            $listing = TblSoftListingStudio::where('listing_studio_case',$casetype)->first();
            $userFilters = TblSoftListingStudioUserFilter::where('listing_studio_id',$listing->listing_studio_id)->get();
            $data_type = '';
            foreach ($userFilters as $userFilter){
                if($userFilter->listing_studio_user_filter_name == $field){
                    $data_type = $userFilter->listing_studio_user_filter_type;
                    $data['case_name'] = $userFilter->listing_studio_user_case_name;
                    break;
                }
            }
            $data['condition_arr'] = TblSoftFilterType::where('filter_type_data_type_name',$data_type)->where('filter_type_entry_status',1)->get();

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
        return $this->jsonSuccessResponse($data, 'Data successfully loaded.', 200);

    }


    public function getFiledData($caseName)
    {
        $data = [];
        DB::beginTransaction();
        try{

            $FilterCase = TblSoftReportingFilterCase::where('reporting_filter_case_id',$caseName)->first();
            $data['list'] = DB::select($FilterCase->reporting_filter_case_query);
            $data['search_type'] = $FilterCase->reporting_filter_case_search_type;

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
        return $this->jsonSuccessResponse($data, 'Data successfully loaded.', 200);

    }
}
