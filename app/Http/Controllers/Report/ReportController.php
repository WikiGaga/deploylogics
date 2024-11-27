<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Library\Utilities;
use App\Models\TblSaleSales;
use App\Models\TblSoftFilterType;
use App\Models\TblSoftMenuDtl;
use App\Models\TblSoftReporting;
use App\Models\TblSoftReportingDimension;
use App\Models\TblSoftReportingFilter;
use App\Models\TblSoftReportingFilterDtl;
use App\Models\TblSoftReportingMetric;
use App\Models\TblSoftReportingMetricDtl;
use App\Models\TblSoftReportingUserFilter;
use App\Models\TblSoftReportingUserStudio;
use App\Models\TblSoftReportingUserStudioDtl;
use App\Models\ViewAllColumnData;
use App\Models\ViewSaleSalesInvoice;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ReportController extends Controller
{
    public static $page_title = 'Reporting';
    public static $redirect_url = 'report';
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
    public function create($id = null)
    {
        $data['page_data'] = [];
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        if(isset($id)){
            if(TblSoftReporting::where('reporting_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['id'] = $id;
                $data['current'] = TblSoftReporting::with('reporting_dimension','user_filter','reporting_filter')->where('reporting_id',$id)->first();
                $max = [];
                foreach ($data['current']->reporting_filter as $reporting_filter) {
                    foreach ($reporting_filter->filter_dtl as $reporting_filter_dtl){
                        $max[] = $reporting_filter_dtl['reporting_filter_sr_no'];
                    }
                }
                if(!empty($max)){
                    $data['max'] = max($max);
                }
            }else{
                abort('404');
            }
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['reporting_code'] = $this->documentCode(TblSoftReporting::max('reporting_code'),'R');
        }
        $data['reporting_menu'] = TblSoftMenuDtl::where('menu_id',6)->where('menu_dtl_id','!=',46)->get();

        $sorted =  ViewAllColumnData::select('table_name')->groupby('table_name')->get();
        $collection = collect($sorted);
        $data['table_list'] = $collection->sortBy('table_name');
        return view('report.form', compact('data'));
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
        if(!isset($id)){
            $validator = Validator::make($request->all(), [
                'reporting_case' => 'required|unique:tbl_soft_reporting',
            ]);
            if ($validator->fails()) {
                $data['validator_errors'] = $validator->errors();
                return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
            }
        }
        DB::beginTransaction();
        try{
            if(isset($id)){
                $reporting = TblSoftReporting::where('reporting_id',$id)->first();
            }else{
                $reporting = new TblSoftReporting();
                $reporting->reporting_id = Utilities::uuid();
                $reporting->reporting_code = $this->documentCode(TblSoftReporting::max('reporting_code'),'R');;
                $reporting->reporting_case = $request->reporting_case;
            }
            $reporting->reporting_title = $request->reporting_title;
            $reporting->reporting_table_name = $request->reporting_table_name;
            $reporting->reporting_date = $request->reporting_date;
            $reporting->reporting_rows_per_page = $request->reporting_rows_per_page;
            $reporting->reporting_sort_colum_name_1 = $request->reporting_sort_colum_name_1;
            $reporting->reporting_sort_colum_name_value_1 = $request->reporting_sort_colum_name_value_1;
            $reporting->reporting_sort_colum_name_2 = $request->reporting_sort_colum_name_2;
            $reporting->reporting_sort_colum_name_value_2 = $request->reporting_sort_colum_name_value_2;
            $reporting->menu_dtl_id = $request->reporting_select_menu;
            // style
            $style = '';
            $table_header_bg_color = $request->table_header_bg_color == 'on'?'inherit':$request->table_header_bg_color;
            $style .= 'table>thead>tr{background-color:'.$table_header_bg_color.' !important;}';

            $table_header_color = $request->table_header_color == 'on'?'inherit':$request->table_header_color;
            $table_header_font_size = $request->table_header_font_size == 0 ?'inherit':$request->table_header_font_size.'px';
            $style .= 'table>thead>tr>th>span{color:'.$table_header_color.' !important;font-size:'.$table_header_font_size.' !important;}';

            $table_body_color = $request->table_body_color == 'on'?'inherit':$request->table_body_color;
            $table_body_font_size = $request->table_body_font_size == 0 ?'inherit':$request->table_body_font_size.'px';
            $style .= 'table>tbody>tr>td>span{color:'.$table_body_color.' !important;font-size:'.$table_body_font_size.' !important;}';

            $table_row_odd_bg_color = $request->table_row_odd_bg_color == 'on'?'inherit':$request->table_row_odd_bg_color;
            $style .= 'table>tbody>tr:nth-child(odd){background-color:'.$table_row_odd_bg_color.' !important;}';

            $table_row_even_bg_color = $request->table_row_even_bg_color == 'on'?'inherit':$request->table_row_even_bg_color;
            $style .= 'table>tbody>tr:nth-child(even){background-color:'.$table_row_even_bg_color.' !important;}';

            if(isset($request->column)){
                foreach($request->column as $key=>$column){
                    $k = $key+1;
                    $style .= 'table>thead>tr>th:nth-child('.$k.'),table>tbody>tr>td:nth-child('.$k.'){text-align:'.$column['align'].' !important}';
                }
            }
            $reporting->reporting_css_style = $style;
            // end style
            // start query
            $table_name = $request->reporting_table_name;
            $where = '';
            $orderby = '';
            $limit = '';
            $metric_q = '';
            $group_by = '';
            if(isset($request->reporting_dimension_column_name) && isset($request->reporting_table_name)){
                $select_colum = implode(', ', $request->reporting_dimension_column_name);
                if(!empty($request->reporting_sort_colum_name_1) || !empty($request->reporting_sort_colum_name_2)){
                    $orderby = ' order by ';
                }
                if(!empty($request->reporting_sort_colum_name_1)){
                    $orderby .= $request->reporting_sort_colum_name_1.' '.$request->reporting_sort_colum_name_value_1. ',' ;
                }
                if(!empty($request->reporting_sort_colum_name_2)){
                    $orderby .= $request->reporting_sort_colum_name_2.' '.$request->reporting_sort_colum_name_value_2;
                }
                if(isset($request->reporting_rows_per_page)){
                    $limit = ' FETCH FIRST '. $request->reporting_rows_per_page.' ROWS ONLY';
                }
                if($request->reporting_filter_name){
                    $where =  ' where ';
                    foreach ($request->outer_filterList as $outer_key=>$outer_column_name){
                        $where .= '(';
                        foreach ($outer_column_name['inner_filterList'] as $inner_key=>$inner_column_name) {
                            $where .= $inner_column_name['report_fields_name'].' ';
                            if($inner_column_name['report_condition'] == 'like'){
                                $where .= $inner_column_name['report_condition'].' "';
                                $where .= $inner_column_name['report_value'].'" ';
                            }else{
                                $where .= $inner_column_name['report_condition'].' ';
                                $where .= $inner_column_name['report_value'].' ';
                            }
                            $where .= 'OR ';
                        }
                        $where = rtrim($where, " OR ");
                        $where .= ') AND ';

                    }
                    if(!empty($where)){
                        $where = rtrim($where, " OR  AND");
                        if($where == ' where (0 0)'){
                            $where = '';
                        }
                    }
                }
                foreach($request->metric as $outer_metric){
                    //dd($outer_metric['reporting_select_metric']);
                    if(!empty($outer_metric['reporting_select_metric'])){
                        // $inner_metric['reporting_select_aggregation'];
                        // $inner_metric['reporting_select_types'];
                        // $inner_metric['reporting_select_calculation'];
                        $metric_q .= $outer_metric['reporting_select_aggregation'].'('.$outer_metric['reporting_select_metric'].'),';
                    }
                }
                if(!empty($metric_q)){
                    $group_by = ' GROUP BY('. $select_colum.')';
                }
            }
            $sql = 'Select '. $metric_q.' '.$select_colum .' from '.$table_name.' '.$where.$group_by.$orderby.$limit;
          //  dd($sql);

            // end query
            $reporting->reporting_query = $sql;
            $reporting->reporting_entry_status = 1;
            $reporting->business_id = auth()->user()->business_id;
            $reporting->company_id = auth()->user()->company_id;
            $reporting->branch_id = auth()->user()->branch_id;
            $reporting->reporting_user_id = auth()->user()->id;
            $reporting->save();
            if(isset($request->reporting_dimension_column_name)){
                $Dimensions = TblSoftReportingDimension::where('reporting_id',$id)->get();
                foreach($Dimensions as $Dimension){
                    $del = TblSoftReportingDimension::where('reporting_dimension_id',$Dimension->reporting_dimension_id)->first();
                    $del->delete();
                }
                $dimension_column_arr = [];
                $dimension_column =[];
                for($i=0;$i< count($request->reporting_dimension_column_name);$i++){
                    array_push($dimension_column_arr,$request->reporting_dimension_column_name[$i]);
                    array_push($dimension_column_arr,$request->reporting_dimension_column_title[$i]);
                    array_push($dimension_column,$dimension_column_arr);
                    $dimension_column_arr =[];
                }
                foreach ($dimension_column as $column_name){
                    $dimension = new TblSoftReportingDimension();
                    $dimension->reporting_id = $reporting->reporting_id;
                    $dimension->reporting_dimension_id = Utilities::uuid();
                    $dimension->reporting_dimension_column_name = isset($column_name[0])?$column_name[0]:"";
                    $dimension->reporting_dimension_column_title = isset($column_name[1])?$column_name[1]:"";
                    $dimension->reporting_dimension_entry_status = 1;
                    $dimension->business_id = auth()->user()->business_id;
                    $dimension->company_id = auth()->user()->company_id;
                    $dimension->branch_id = auth()->user()->branch_id;
                    $dimension->reporting_dimension_user_id = auth()->user()->id;
                    $dimension->save();
                }
            }
            if(isset($request->user_filter)){
                $UserFilters = TblSoftReportingUserFilter::where('reporting_id',$id)->get();
                foreach($UserFilters as $UserFilter){
                    $del = TblSoftReportingUserFilter::where('reporting_user_filter_id',$UserFilter->reporting_user_filter_id)->first();
                    $del->delete();
                }
                foreach ($request->user_filter as $filter){
                    if(!empty($filter['name']) && !empty($filter['type']) && !empty($filter['title'])){
                        $dimension = new TblSoftReportingUserFilter();
                        $dimension->reporting_id = $reporting->reporting_id;
                        $dimension->reporting_user_filter_id = Utilities::uuid();
                        $dimension->reporting_user_filter_field_name = isset($filter['name'])?$filter['name']:"";
                        $dimension->reporting_user_filter_type = isset($filter['name'])?$filter['name']:"";
                        $dimension->reporting_user_filter_title = isset($filter['title'])?$filter['title']:"";
                        $dimension->reporting_user_filter_field_type = isset($filter['type'])?$filter['type']:"";
                        $dimension->reporting_user_filter_entry_status = 1;
                        $dimension->business_id = auth()->user()->business_id;
                        $dimension->company_id = auth()->user()->company_id;
                        $dimension->branch_id = auth()->user()->branch_id;
                        $dimension->reporting_user_filter_user_id = auth()->user()->id;
                        $dimension->save();
                    }
                }
            }
            if(!empty($request->reporting_filter_name)){
                if(isset($id)){
                    $filter = TblSoftReportingFilter::where('reporting_id',$id)->first();
                }else{
                    $filter = new TblSoftReportingFilter();
                    $filter->reporting_id = $reporting->reporting_id;
                    $filter->reporting_filter_id = Utilities::uuid();
                }
                $filter->reporting_filter_name = $request->reporting_filter_name;
                $filter->reporting_filter_entry_status = 1;
                $filter->business_id = auth()->user()->business_id;
                $filter->company_id = auth()->user()->company_id;
                $filter->branch_id = auth()->user()->branch_id;
                $filter->reporting_filter_user_id = auth()->user()->id;
                $filter->save();
                $Filters = TblSoftReportingFilterDtl::where('reporting_filter_id',$filter->reporting_filter_id)->get();
                foreach($Filters as $Filter){
                    $del = TblSoftReportingFilterDtl::where('reporting_filter_dtl_id',$Filter->reporting_filter_dtl_id)->first();
                    $del->delete();
                }
                $i = 1;
                foreach ($request->outer_filterList as $outer_key=>$outer_column_name){
                    foreach ($outer_column_name['inner_filterList'] as $inner_key=>$inner_column_name){
                        $filterDtl = new TblSoftReportingFilterDtl();
                        $filterDtl->reporting_filter_dtl_id = Utilities::uuid();
                        $filterDtl->reporting_filter_id = $filter->reporting_filter_id;
                        $filterDtl->reporting_filter_sr_no = $i;
                        $filterDtl->reporting_filter_column_name = $inner_column_name['report_fields_name'];
                        $filterDtl->reporting_filter_condition = $inner_column_name['report_condition'];
                        $filterDtl->reporting_filter_value = $inner_column_name['report_value'];
                        $filterDtl->reporting_filter_value_2 = isset($inner_column_name['report_value_to'])?$inner_column_name['report_value_to']:"";
                        $filterDtl->reporting_filter_dtl_entry_status = 1;
                        $filterDtl->reporting_filter_field_type = $inner_column_name['report_value_column_type_name'];
                        $filterDtl->business_id = auth()->user()->business_id;
                        $filterDtl->company_id = auth()->user()->company_id;
                        $filterDtl->branch_id = auth()->user()->branch_id;
                        $filterDtl->reporting_filter_dtl_user_id = auth()->user()->id;
                        $filterDtl->save();
                    }
                    $i++;
                }
            }
            if(!empty($request->metric)){
                /*
                foreach($request->metric as $outer_metric){
                    if($outer_metric['reporting_select_metric'] != 0){
                        $metric = new TblSoftReportingMetric();
                        $metric->reporting_id = $reporting->reporting_id;
                        $metric->reporting_metric_id = Utilities::uuid();
                        $metric->reporting_metric_column_name = $outer_metric['reporting_select_metric'];
                        $metric->reporting_metric_entry_status = 1;
                        $metric->business_id = auth()->user()->business_id;
                        $metric->company_id = auth()->user()->company_id;
                        $metric->branch_id = auth()->user()->branch_id;
                        $metric->reporting_metric_user_id = auth()->user()->id;
                        $metric->save();
                        if(isset($outer_metric['inner_metric'])){
                            foreach($outer_metric['inner_metric'] as $key=>$inner_metric){
                                $metricDtl = new TblSoftReportingMetricDtl();
                                $metricDtl->reporting_metric_id = $metric->reporting_metric_id;
                                $metricDtl->reporting_metric_dtl_id = Utilities::uuid();
                                $metricDtl->reporting_metric_dtl_aggregation = $inner_metric['reporting_select_aggregation'];
                                $metricDtl->reporting_metric_dtl_types = $inner_metric['reporting_select_types'];
                                $metricDtl->reporting_metric_dtl_calculation = $inner_metric['reporting_select_calculation'];
                                $metricDtl->reporting_metric_dtl_entry_status = 1;
                                $metricDtl->business_id = auth()->user()->business_id;
                                $metricDtl->company_id = auth()->user()->company_id;
                                $metricDtl->branch_id = auth()->user()->branch_id;
                                $metricDtl->reporting_metric_dtl_user_id = auth()->user()->id;
                                $metricDtl->save();
                            }
                        }
                    }
                 }
                */
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
            // delete all users_studios data...
            // generate all reports by user will be delete
            $user_studios = TblSoftReportingUserStudio::where('reporting_id',$id)->get();
            foreach($user_studios as $user_studio_del){
                $user_studio_dtls = TblSoftReportingUserStudioDtl::where('reporting_user_studio_id',$user_studio_del->reporting_user_studio_id)->get();
                foreach($user_studio_dtls as $user_studio_dtl_del){
                    $del = TblSoftReportingUserStudioDtl::where('reporting_user_studio_dtl_id',$user_studio_dtl_del->reporting_user_studio_dtl_id)->first();
                    $del->delete();
                }
                $user_studio_del->delete();
            }

            $data = array_merge($data, Utilities::returnJsonEditForm());
            $data['redirect'] = $this->prefixIndexPage.self::$redirect_url;;
            return $this->jsonSuccessResponse($data, trans('message.update'), 200);
        }else{
            $data = array_merge($data, Utilities::returnJsonNewForm());
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
        $data = [];
        DB::beginTransaction();
        try{
            $reporting = TblSoftReporting::where('reporting_id',$id)->first();
            $reporting->reporting_dimension()->delete();
            $reporting->user_filter()->delete();

            $user_studios = TblSoftReportingUserStudio::where('reporting_id',$id)->get();
            foreach($user_studios as $user_studio_del){
                $user_studio_dtls = TblSoftReportingUserStudioDtl::where('reporting_user_studio_id',$user_studio_del->reporting_user_studio_id)->get();
                foreach($user_studio_dtls as $user_studio_dtl_del){
                    $del = TblSoftReportingUserStudioDtl::where('reporting_user_studio_dtl_id',$user_studio_dtl_del->reporting_user_studio_dtl_id)->first();
                    $del->delete();
                }
                $user_studio_del->delete();
            }
            $filter = TblSoftReportingFilter::where('reporting_id',$id)->first();
            if(!empty($filter)){
                $Filters = TblSoftReportingFilterDtl::where('reporting_filter_id',$filter->reporting_filter_id)->get();
                foreach($Filters as $Filter){
                    $del = TblSoftReportingFilterDtl::where('reporting_filter_dtl_id',$Filter->reporting_filter_dtl_id)->first();
                    $del->delete();
                }
                $filter->delete();
            }
            $reporting->delete();

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
    public function getColumns($table)
    {
        $data = '';
        DB::beginTransaction();
        try{

            $data = ViewAllColumnData::where('table_name', strtoupper($table))->get();

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
    public function getFiledConditions($table, $field)
    {
        $data = [];
        DB::beginTransaction();
        try{

            $datatype = ViewAllColumnData::where('table_name', strtoupper($table))
                ->where('column_name', strtoupper($field))->first();
            $data = TblSoftFilterType::where('filter_type_data_type_name',strtolower($datatype->data_type))->where('filter_type_entry_status',1)->get();

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

    public function getFiledMetric($table, $field)
    {
        $data = [];
        DB::beginTransaction();
        try{

            $data = ViewAllColumnData::where('table_name', strtoupper($table))
                ->where('column_name', strtoupper($field))->first();


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

    public function ReportSaleInvoice($from,$to){
        $getdata = ViewSaleSalesInvoice::orderby('sales_date')->orderby('sales_code')->get();
        $data = [];
        foreach ($getdata as $row)
        {
            $today = date('Y-m-d', strtotime($row['sales_date']));
            $data[$today][$row['sales_code']][] = $row;
        }
     //   dd($data);
        return view('report.report_sale_invoice',compact('data'));
    }
}
// vw_sale_sales_invoice
