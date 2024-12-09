<?php

namespace App\Http\Controllers\Common;

use App\Models\ViewAllColumnData;
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
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListingAdvanceController extends Controller
{

    public function index(Request $request,$caseType,$subType = null){
    //    dd($request->all());
        $data = [];
        $case_name = (isset($subType) && !empty($subType)) ? $subType : $caseType;
        $listing = TblSoftListingStudio::where('listing_studio_case',$case_name)->first();
        if(empty($listing)){
            return abort('404');
        }
        $data['data_url'] = action('Common\ListingAdvanceController@index',$case_name);
        $data['menu_dtl_id'] = $listing->menu_dtl_id;
        $data['title'] =  $listing->listing_studio_title;
        $data['case'] = $case_name;
        $data['caseType'] = $caseType;
        if((isset($subType) && !empty($subType))){
            $data['create-form'] = '/'.$caseType.'/'.$subType.'/form';
            $data['form-action'] = '/'.$caseType.'/'.$subType;
        }else{
            $data['create-form'] = '/'.$case_name.'/form';
            $data['form-action'] = '/'.$case_name;
        }

        $listing_studio_query = unserialize($listing->listing_studio_query);
        $table_name = $listing->listing_studio_table_name;
        $table_name_alias = $listing_studio_query->table_name;
        $modelName = Utilities::getModelFromTable($table_name);
        $data['table_id'] = $modelName::primaryKeyName();

        $data['table_columns'] =  self::getTableColumns($table_name,$listing);

        $customColumnsList =  self::customColumnsList($case_name,$listing);

        if(isset($customColumnsList['table_columns'])){ $data['table_columns'] = $customColumnsList['table_columns'];}

        if($request->ajax()){
            $tbl_1 = 'tbl_1';
            $tbl_1_alias = 'tbl_1.';
            $columns = "$tbl_1_alias".$data['table_id'];
            foreach ($data['table_columns'] as $lk => $table_columns){
                $columns .= ','.$tbl_1_alias.$lk;
            }

            $where = 'where ';

            // fix where
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
                $where .= ' AND '.$listing_studio_query->where.' ';
            }

            // sort by
            $sort_colum_name_1 = "";
            $sort_colum_name_2 = "";
            $sortDirection = "desc";
            $sortField = "created_at";
            if(isset($listing_studio_query->orderBy) && !empty($listing_studio_query->orderBy)){
                $orderBy = " ".$listing_studio_query->orderBy." ";
                // sort by
                if(!empty($listing->listing_studio_sort_colum_name_1)){
                    $sort_colum_name_1 = $listing->listing_studio_sort_colum_name_1;
                }
                if(!empty($listing->listing_studio_sort_colum_name_2)){
                    $sort_colum_name_2 = $listing->listing_studio_sort_colum_name_2;
                }
            }else{
                $sortDirection  = ($request->has('sort.sort') && $request->filled('sort.sort'))? $request->input('sort.sort') : 'desc';
                $sortField  = ($request->has('sort.field') && $request->filled('sort.field'))? $request->input('sort.field') : 'created_at';
                $orderBy = " ORDER BY $tbl_1_alias$sortField $sortDirection ";
                $sort_colum_name_1 = $sortField;
            }

            /*if($case_name != "customer")
            {*/

            // global filter
            $where .= self::getGlobalFilters($request,$data,$tbl_1_alias);

            /*}*/
            // metric (calculations)
            $metric = $listing_studio_query->metric;

            // group by
            $groupBy = self::getGroupBy($listing_studio_query,$columns,$tbl_1_alias,$sort_colum_name_1,$sort_colum_name_2);

            // custom Columns and group by
            $customColumns = self::customColumns($case_name,$tbl_1);

            // total records count
            if(isset($customColumns['columns'])){ $columns = $customColumns['columns']; }
            if(isset($customColumns['groupBy'])){ $groupBy =$customColumns['groupBy']; }

            $qry  = 'select '.$columns.' from '.$table_name_alias.' '.$where.' '.$groupBy;
            $qry = str_replace('$user_id$',Auth::user()->id,$qry);
            dd($qry);

            $totalEntries = DB::select($qry);
            $total  = count($totalEntries);


            $qryForCount = 'SELECT COUNT(*) as total FROM ' . $table_name_alias . ' ' . $where.' '.$groupBy;
            $qryForCount = str_replace('$user_id$',Auth::user()->id,$qryForCount);
            $totalEntries = DB::select($qryForCount);
            $total  = $totalEntries[0]->total;

            $meta    = [];
            $page  = ($request->has('pagination.page') && $request->filled('pagination.page'))? $request->input('pagination.page') : 1;
            $perpage  = ($request->has('pagination.perpage') && $request->filled('pagination.perpage'))? $request->input('pagination.perpage') : -1;

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

            // get all records
            $limit = "OFFSET $offset ROWS FETCH NEXT $perpage ROWS ONLY";
            $qry  = 'select '.$columns.' '.$metric.' from '.$table_name_alias.' '.$where.' '.$groupBy.' '.$orderBy.' '.$limit;
            $qry = str_replace('$user_id$',Auth::user()->id,$qry);
            $entries = DB::select($qry);
//            dump($qry);
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

        return view('common.adv_list.listing',compact('data'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getDateType($table_name,$type){

        $data_type = ViewAllColumnData::where(DB::raw('lower(table_name)'),$this->strLower($table_name))
            ->where(DB::raw('lower(column_name)'),$this->strLower($type))->where(DB::raw('lower(data_type)'),'date')->exists();

        if($data_type){
            $datetime_list = ['created_at','updated_at'];
            if(in_array($type,$datetime_list)){
                return 'datetime';
            }else{
                return 'datetime';
            }
        }

        return 'string';
    }

    public function getTableColumns($table_name,$listing){
        $Dimension = TblSoftListingStudioDimension::where('listing_studio_id',$listing->listing_studio_id)->orderby('sr_no')->get();
        $DimensionJoin = TblSoftListingStudioJoinTable::where('listing_studio_id',$listing->listing_studio_id)->get();
        $data = [];
        for($i=0;$i<count($Dimension);$i++){
            $data[$Dimension[$i]['listing_studio_dimension_column_name']]['title'] = $Dimension[$i]['listing_studio_dimension_column_title'];
            $data[$Dimension[$i]['listing_studio_dimension_column_name']]['type'] = self::getDateType($table_name,$Dimension[$i]['listing_studio_dimension_column_name']);
        }

        for($i=0;$i<count($DimensionJoin);$i++){
            $data[$DimensionJoin[$i]['listing_studio_join_table_column_name']]['title'] = $DimensionJoin[$i]['listing_studio_join_table_column_title'];
            $data[$DimensionJoin[$i]['listing_studio_join_table_column_name']]['type'] = self::getDateType($table_name,$DimensionJoin[$i]['listing_studio_join_table_column_name']);
        }
        if(isset($listing_studio_query->metricTitles) && !empty($listing_studio_query->metricTitles)){
            $metricTitles = explode(',', $listing_studio_query->metricTitles);;
            for($i=0;$i<count($metricTitles);$i++){
                $data[strtolower($metricTitles[$i])]['title'] = ucfirst($metricTitles[$i]);
                $data[strtolower($metricTitles[$i])]['type'] = self::getDateType($table_name,strtolower($metricTitles[$i]));
            }
        }
        return $data;
    }

    public function getGlobalFilters($request,$data,$tbl_1_alias)
    {
        $whereGlobal = "";
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
                            $textSearch .= " lower(".$tbl_1_alias."is_active_status) like '%$generalSearch%' OR ";
                            $textSearch .= " lower(".$tbl_1_alias."expire_status) like '%$generalSearch%' OR ";
                        }else{
                            $textSearch .= " lower($tbl_1_alias".$tkey.") like '%$generalSearch%' OR ";
                        }
                    }
                }
                if(!empty($textSearch)){
                    $textSearch = rtrim($textSearch,' OR');
                    $whereGlobal .= "and ( $textSearch ) ";
                }

                $from = "TO_DATE('01/01/2010 ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                if($data['case'] != "customer"){
                    $whereGlobal .=  ' AND ('.$tbl_1_alias.'created_at between '. $from .' AND '. $to.') ';
                }

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

                    if($data['case'] != "customer"){
                        $whereGlobal .=  ' AND ('.$tbl_1_alias.'created_at between '. $from .' AND '. $to.') ';
                    }

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
                                    $inline_where .= " and (lower(".$tbl_1_alias."is_active_status) like '%".strtolower($inline_filter[$tkey])."%' OR ";
                                    $inline_where .= " lower(".$tbl_1_alias."expire_status) like '%".strtolower($inline_filter[$tkey])."%' ) ";
                                }else{
                                    $inline_where .= " and lower(".$tbl_1_alias.$tkey.") like '%".strtolower($inline_filter[$tkey])."%'";
                                }
                            }
                            if(in_array($table_columns['type'],['date','datetime'])){
                                $created_at = date('d/m/Y',strtotime($inline_filter[$tkey]));
                                $d_from = "TO_DATE('".$created_at." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $d_to = "TO_DATE('".$created_at." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
                                $inline_to_date = "$d_from and $d_to";
                                $inline_where .= " and ( ".$tbl_1_alias.$tkey." between ".$inline_to_date.") ";
                            }
                        }
                    }
                }
                $whereGlobal .= $inline_where;
                $global_filter_bollean = true;
            }

            $whereGlobal .= self::getFormWiseFilters($globalFilters,$data,$tbl_1_alias);
        }
        if(!$global_filter_bollean){
            $from = "TO_DATE('".$today." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
            $to = "TO_DATE('".$today." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";
            if($data['case'] != "customer"){
                $whereGlobal .=  ' AND ('.$tbl_1_alias.'created_at between '. $from .' AND '. $to.') ';
            }
        }

        return $whereGlobal;
    }


    public function getFormWiseFilters($globalFilters,$data,$tbl_1_alias){
        $where = "";
        $time_from = '12:00:00 am';
        $time_to = '11:59:59 pm';
        if($data['case'] == 'product-discount-setup'){
            if(isset($globalFilters['pds_status'])){
                if($globalFilters['pds_status'] == 'in_active_expire'){
                    $where .= " and (".$tbl_1_alias."is_active = 0 and lower(".$tbl_1_alias."expire_status) =  'expired')";
                }
                if($globalFilters['pds_status'] == 'in_active_valid'){
                    $where .= " and (".$tbl_1_alias."is_active = 0 and lower(".$tbl_1_alias."expire_status) =  'valid')";
                }
                if($globalFilters['pds_status'] == 'active_expire'){
                    $where .= " and (".$tbl_1_alias."is_active = 1 and lower(".$tbl_1_alias."expire_status) =  'expired')";
                }
                if($globalFilters['pds_status'] == 'active_valid'){
                    $where .= " and (".$tbl_1_alias."is_active = 1 and lower(".$tbl_1_alias."expire_status) =  'valid')";
                }
            }
        }
        if($data['case'] == 'pv' ||
        $data['case'] == 'cpv' ||
        $data['case'] == 'crv'||
        $data['case'] == 'pve'||
        $data['case'] == 'lv'||
        $data['case'] == 'jv'||
        $data['case'] == 'brpv'||
        $data['case'] == 'brrv'||
        $data['case'] == 'ipv'||
        $data['case'] == 'irv'||
        $data['case'] == 'rv'||
        $data['case'] == 'obv'
        ){
            if(isset($globalFilters['voucher_from']) && isset($globalFilters['voucher_to']))
            {
                $voucher_from = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['voucher_from']))." ".$time_from."', 'dd/mm/yyyy HH:MI:SS pm')";
                $voucher_to = "TO_DATE('".date('d/m/Y',strtotime($globalFilters['voucher_to']))." ".$time_to."', 'dd/mm/yyyy HH:MI:SS pm')";

                $where .= " AND (".$tbl_1_alias."voucher_date between ". $voucher_from ." AND ". $voucher_to.") ";
            }
            if(isset($globalFilters['post_status'])){
                if($globalFilters['post_status'] == "1"){
                    $where .= " and lower(".$tbl_1_alias."voucher_status) = 'posted'";
                }
                if($globalFilters['post_status'] == "0"){
                    $where .= " and lower(".$tbl_1_alias."voucher_status) = 'un-posted'";
                }
            }
        }

        return $where;
    }

    public function getGroupBy($listing_studio_query,$columns,$tbl_1_alias,$sort_colum_name_1,$sort_colum_name_2){
        $groupBy = "";
        if(!empty($listing_studio_query->groupBy)){
            $groupBy = ' GROUP BY '.$columns;
            if(!empty($sort_colum_name_1)){
                $groupBy .= ",$tbl_1_alias".$sort_colum_name_1;
            }
            if(!empty($sort_colum_name_2)){
                $groupBy .= ",$tbl_1_alias".$sort_colum_name_2;
            }
        }
        return $groupBy;
    }

    public function customColumns($case_name,$tbl_1)
    {
        $arr = [];
        if($case_name == 'product-discount-setup'){
            $arr['columns'] = "$tbl_1.discount_setup_id, $tbl_1.discount_code,$tbl_1.discount_title,$tbl_1.is_active_status || '-' || $tbl_1.expire_status     discount_status , $tbl_1.start_date, $tbl_1.end_date, $tbl_1.branch_name ,  $tbl_1.user_name ,  $tbl_1.created_at ";
            $arr['groupBy'] = "GROUP BY $tbl_1.discount_setup_id, $tbl_1.discount_code,$tbl_1.discount_title,$tbl_1.is_active_status,$tbl_1.expire_status , $tbl_1.start_date, $tbl_1.end_date, $tbl_1.branch_name ,  $tbl_1.user_name ,  $tbl_1.created_at ";
        }

        return $arr;
    }

    public function customColumnsList($case_name)
    {
        $arr = [];
        if($case_name == 'product-discount-setup'){
            $arr['table_columns'] = self::customColumnsDiscountSetup();
        }
        return $arr;
    }

    public function customColumnsDiscountSetup()
    {
        return [
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
    }


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
