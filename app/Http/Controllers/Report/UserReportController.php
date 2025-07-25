<?php

namespace App\Http\Controllers\Report;

use DateTime;
use Exception;
use Validator;
use DatePeriod;
use DateInterval;
use App\Models\TblAccCoa;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblAccoVoucher;
use App\Models\ViewAccoVoucher;
use Illuminate\Validation\Rule;
use App\Models\TblSoftReporting;
use App\Models\TblSoftFilterType;
use App\Models\ViewAllColumnData;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

// db and Validator
use App\Models\ViewSaleSalesInvoice;
use App\Models\TblSoftReportingFilter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\TblSoftReportingDimension;
use App\Models\TblSoftReportingFilterCase;
use App\Models\TblSoftReportingUserFilter;
use App\Models\TblSoftReportingUserStudio;
use App\Models\TblSoftReportingUserStudioDtl;
use App\Models\TblVerifyReports;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class UserReportController extends Controller
{
    public static $page_title = 'Reporting';
    public static $redirect_url = '/report/user-report';

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
    public function create($case, $id = null)
    {   // TblSoftReportingUserStudio
        $data['page_data'] = [];
        $data['case_name'] = $case;
        $data['page_data']['title'] = self::$page_title;
        $data['page_data']['path_index'] = $this->prefixIndexPage.self::$redirect_url;;
        if(isset($id)){
            if(TblSoftReportingUserStudio::where('reporting_user_studio_id','LIKE',$id)->exists()){
                $data['page_data'] = array_merge($data['page_data'], Utilities::editForm());
                $data['page_data']['action'] = 'Generate Update';
                $data['user_studio'] = TblSoftReportingUserStudio::with('user_studio_dtl')->where('reporting_user_studio_id','LIKE',$id)->first();
                $max = [];
                foreach ($data['user_studio']->user_studio_dtl as $item) {
                    $max[] = $item['reporting_user_studio_dtl_sr'];
                }
                if(!empty($max)){
                    $data['max'] = max($max);
                }
                $data['id'] = $id;
            }else{
                abort('404');
            }
        }else{
            $data['page_data'] = array_merge($data['page_data'], Utilities::newForm());
            $data['page_data']['action'] = 'Generate';
        }
        $data['report'] = TblSoftReporting::with('user_filter')
            ->where('reporting_case',$case)
            ->where('reporting_entry_status',1)->first();
        return view('report.user_report', compact('data'));
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
        $data['case_name'] = $case_name;
        $data['list'] = TblSoftReporting::where('reporting_entry_status',1)
            ->where('menu_dtl_id',$menu_dtl_id)->get();
        return view('report.list', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFiledTypes(Request $request,$id,$field)
    {
        $data = [];
        DB::beginTransaction();
        try{
            $filtercase = TblSoftReportingFilterCase::where('reporting_filter_case_name', $field)->first();
            if(!empty($filtercase)){
                if(!empty($filtercase->reporting_filter_case_query)){
                    $data['case_data'] = DB::select($filtercase->reporting_filter_case_query);
                }
            }
            $datatype = TblSoftReportingUserFilter::where('reporting_id', $id)
                ->where('reporting_user_filter_type', $field)
                ->first();
            $data['column_type_name'] = $datatype->reporting_user_filter_field_type;
            $data['type'] = TblSoftFilterType::where('filter_type_data_type_name',$datatype->reporting_user_filter_field_type)->where('filter_type_entry_status',1)->get();
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

    public function store(Request $request, $id = null)
    {
        //dd($request->toArray());
        $getTable = TblSoftReporting::where('reporting_id',$request->reporting_id)->first();
        $table_name = $getTable->reporting_table_name;
        $getCols = TblSoftReportingDimension::where('reporting_id',$request->reporting_id)->get();
        $columns = '';
        $data['headings'] = [];
        $data['cols'] = [];
        foreach ($getCols as $getCol){
            $columns .= $getCol->reporting_dimension_column_name.',';
            array_push($data['headings'], $getCol->reporting_dimension_column_title);
            array_push($data['cols'], $getCol->reporting_dimension_column_name);
        }
        //dd($data);
        $columns = rtrim($columns, ",");
        if($columns == ''){
            $columns = '*';
        }
        /****************************************/
        $where = '';
       // dd($request->report_filter);
        if(isset($request->outer_report_filter)){
            //dd($request->outer_report_filter);
            foreach ($request->outer_report_filter as $outerFilter){
               // dd($outerFilter['report_filter']);
                $where .= "( ";
                foreach($outerFilter['report_filter'] as $filter){
                    if(!empty($filter['report_filter_name'])){
                        $colName = TblSoftReportingUserFilter::where('reporting_id', $request->reporting_id)
                            ->where('reporting_user_filter_type', $filter['report_filter_name'])->first();
                        if($filter['report_filter_type'] == 'yes'){
                            $where .= $colName->reporting_user_filter_field_name .' like \'%Yes%\' OR ';
                            $where .= $colName->reporting_user_filter_field_name .' = 1 OR ';
                        }
                        if($filter['report_filter_type'] == 'no'){
                            $where .= $colName->reporting_user_filter_field_name .' like \'%No%\' OR ';
                            $where .= $colName->reporting_user_filter_field_name .' = 0 OR ';
                        }
                        if($filter['report_filter_type'] == 'not null'){
                            $where .= $colName->reporting_user_filter_field_name .' is not null OR ';
                        }
                        if($filter['report_filter_type'] == 'null'){
                            $where .= $colName->reporting_user_filter_field_name .' is null OR ';
                        }
                        if(isset($filter['report_value_column_type_name'])){
                            if($filter['report_value_column_type_name'] == 'number' && $filter['report_filter_type'] == 'between'){
                                $where .= $colName->reporting_user_filter_field_name .' between '.$filter['report_value_from'].' AND '.$filter['report_value_to'].' OR ';
                            }
                            if($filter['report_value_column_type_name'] == 'date' && $filter['report_filter_type'] == 'between'){
                                $from = "TO_DATE ('".$filter['report_value_from']."', 'dd-mm-yyyy')";
                                $to = "TO_DATE ('".$filter['report_value_to']."', 'dd-mm-yyyy')";
                                $where .= $colName->reporting_user_filter_field_name .' between '.$from.' AND '.$to.' OR ';
                            }
                        }
                        if(isset($filter['report_value'])){
                            if(gettype($filter['report_value'])  == 'array'){
                                foreach ($filter['report_value'] as $val){
                                    if($filter['report_value_column_type_name'] == 'number' && $filter['report_filter_type'] == '=' || $filter['report_filter_type'] == '!=' || $filter['report_filter_type'] == '>' || $filter['report_filter_type'] == '<' || $filter['report_filter_type'] == '<=' || $filter['report_filter_type'] == '>=' ){
                                        $val = $val;
                                    }else{
                                        $val = "'%".$val."%'";
                                    }
                                    $where .= $colName->reporting_user_filter_field_name .' '. $filter['report_filter_type'] .' '. $val.' OR ';
                                }
                            }else{
                                $where .= $colName->reporting_user_filter_field_name .' '. $filter['report_filter_type'] .' '. $filter['report_value'].' OR ';
                            }
                        }
                    }
                }
                $where = rtrim($where, "OR "). ' ) AND ';
            }
            $where = rtrim($where, "AND ");
        }
        $where = str_replace('( )', '', $where);
        $where = rtrim($where, "AND ");

        /****************************************/
        $ReportingFilter = TblSoftReportingFilter::where('reporting_id',$request->reporting_id)->first();
        $main_filter_and = '';
        if($ReportingFilter != null){
            $ReportingFilter = TblSoftReportingFilter::with('filter_dtl')->where('reporting_id',$request->reporting_id)->first();
            $n = 1;
            $main_filter_and = '(';
            foreach ($ReportingFilter->filter_dtl as $sub){
                if($sub->reporting_filter_sr_no == $n){
                    $w =  'OR';
                    $x =  $sub->reporting_filter_column_name;
                    $y =  $sub->reporting_filter_condition;
                    if($sub->reporting_filter_condition == 'like'){
                        $z =  "'%".$sub->reporting_filter_value."%'";
                    }else{
                        $z =  $sub->reporting_filter_value;
                    }
                    $main_filter_and .= $x.' '.$y.' '.$z.' '.$w.' ' ;
                }else{
                    $main_filter_and = rtrim($main_filter_and, "OR ");
                    $main_filter_and .= ') AND (';
                    $n++;
                    $w =  'OR';
                    $x =  $sub->reporting_filter_column_name;
                    $y =  $sub->reporting_filter_condition;
                    if($sub->reporting_filter_condition == 'like'){
                        $z =  "'%".$sub->reporting_filter_value."%'";
                    }else{
                        $z =  $sub->reporting_filter_value;
                    }

                    $main_filter_and .= $x.' '.$y.' '.$z.' '.$w.' ' ;
                }
            }
            $main_filter_and = rtrim($main_filter_and, "OR ");
            $main_filter_and .= ')';
        }
        /**************************************/
       // dd($main_filter_and);
       // dd($where);
        if($main_filter_and == '' && $where == ''){
            $total_where = '';
            $sql_w = ''; //remove part line
        }
        if($main_filter_and != '' && $where != ''){
            $total_where = 'where (' .$where .') AND ( '.$main_filter_and.')';
            $sql_w = 'where (' .$where .')<br> AND( '.$main_filter_and.')'; //remove part line
        }
        if($main_filter_and == '' && $where != ''){
            $total_where = 'where '.$where;
            $sql_w = 'where '.$where; //remove part line
        }
        if($main_filter_and != '' && $where == ''){
            $total_where = 'where '.$main_filter_and;
            $sql_w = 'where '.$main_filter_and; //remove part line
        }
        if($total_where == ''){
            $total_where = 'where (business_id = '.$request->report_business_name.' AND branch_id = '.$request->report_branch_name.')';
        }else{
            $total_where = $total_where . ' AND (business_id = '.$request->report_business_name.' AND branch_id = '.$request->report_branch_name.')';
        }

        /* start remove part */
            if($sql_w == ''){
                $sql_w = 'where (business_id = '.$request->report_business_name.' AND branch_id = '.$request->report_branch_name.')';
            }else{
                $sql_w = $sql_w . '<br> AND (business_id = '.$request->report_business_name.' AND branch_id = '.$request->report_branch_name.')';
            }

            $data['sql'] = 'Select '.$columns.' from '.$table_name . ' <br>'. $sql_w;
        /* end remove part */

        $query = 'Select '.$columns.' from '.$table_name . ' '. $total_where;
       // dd($query);
        try {
            $data['all'] = DB::select($query);
            if(isset($id)){
                $user_studio = TblSoftReportingUserStudio::where('reporting_user_studio_id',$id)->first();
            }else{
                $user_studio = new TblSoftReportingUserStudio();
                $user_studio->reporting_user_studio_id = Utilities::uuid();
            }
            $user_studio->reporting_id = $getTable->reporting_id;
            $user_studio->reporting_user_studio_name = $getTable->reporting_title;
            $user_studio->reporting_user_studio_case_name = $getTable->reporting_case;
            $user_studio->reporting_user_studio_entry_status = 1;
            $user_studio->business_id = auth()->user()->business_id;
            $user_studio->company_id = auth()->user()->company_id;
            $user_studio->branch_id = auth()->user()->branch_id;
            $user_studio->reporting_user_studio_user_id = auth()->user()->id;
            $user_studio->save();

            $user_studio_dtls = TblSoftReportingUserStudioDtl::where('reporting_user_studio_id',$id)->get();
            foreach($user_studio_dtls as $user_studio_dtl_del){
                $del = TblSoftReportingUserStudioDtl::where('reporting_user_studio_dtl_id',$user_studio_dtl_del->reporting_user_studio_dtl_id)->first();
                $del->delete();
            }
            $from_date = '';
            $to_date = '';
            $criteria = [];
            if(isset($request->outer_report_filter)){
               $i = 1;
               foreach($request->outer_report_filter as $outer_report_filter){
                   foreach ($outer_report_filter['report_filter'] as $inner_report_filter){
                       $inner_criteria = [];
                       if(!empty($inner_report_filter['report_filter_name'])){
                           $inner_criteria['name'] = $inner_report_filter['report_filter_name'];
                           $inner_criteria['type'] = $inner_report_filter['report_filter_type'];
                           $user_studio_dtl = new TblSoftReportingUserStudioDtl();
                           $user_studio_dtl->reporting_user_studio_id = $user_studio->reporting_user_studio_id;
                           $user_studio_dtl->reporting_user_studio_dtl_id = Utilities::uuid();
                           $user_studio_dtl->reporting_user_studio_dtl_name = $inner_report_filter['report_filter_name'];
                           $user_studio_dtl->reporting_user_studio_dtl_type = $inner_report_filter['report_filter_type'];
                           $user_studio_dtl->reporting_user_studio_dtl_column_type_name = $inner_report_filter['report_value_column_type_name'];
                           $report_value = isset($inner_report_filter['report_value'])?$inner_report_filter['report_value']:[];
                           if(count($report_value) != 0){
                               $inner_criteria['value_1'] = $report_value;
                               $user_studio_dtl->reporting_user_studio_dtl_value = implode(',', $report_value);
                           }else{
                               $inner_criteria['value_2'] = $from_date = $inner_report_filter['report_value_from'];
                               $inner_criteria['value_3'] = $to_date = $inner_report_filter['report_value_to'];
                               $user_studio_dtl->reporting_user_studio_dtl_value = $inner_report_filter['report_value_from'];
                               $user_studio_dtl->reporting_user_studio_dtl_and = $inner_report_filter['report_value_to'];
                           }
                           $user_studio_dtl->reporting_user_studio_dtl_sr = $i;
                           array_push($criteria,$inner_criteria);
                           $user_studio_dtl->save();
                       }
                   }
                   $i++;
               }
            }
        }catch (Exception $e) {
            return $this->jsonErrorResponse($data, trans('message.query_error'), 200);
          //  return $this->jsonErrorResponse($data, $e->getMessage(), 200);
        }
        //dd($data);


        /// generating reports
        if($getTable->reporting_case == 'sale_invoice'){
            $data = [];
            $data['key'] = 'sale_invoice';
            $page_title = $getTable->reporting_title;
            $to_date = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
            $from_date = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
            $getdata = ViewSaleSalesInvoice::where('branch_id', $request->report_branch_name)
                ->whereBetween('sales_date',[$from_date,$to_date])
                ->orderby('sales_date')->orderby('sales_code')
                ->get();
            $list = [];
            foreach ($getdata as $row)
            {
                $today = date('Y-m-d', strtotime($row['sales_date']));
                $list[$today][$row['sales_code']][] = $row;
            }
            $data['list'] = $list;
            $data['page_title'] = $page_title;
            $data['criteria'] = $criteria;
            session(['data' => $data]);
            $dataJs['url'] = route( 'report.report_sale_invoice');
        }elseif($getTable->reporting_case == 'accounting_ledger'){
            $data = [];
            $data['key'] = 'accounting_ledger';
            $query = 'Select * from '.$table_name . ' '. $total_where;
            $data['list'] = DB::select($query);
            $from_date = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
            $chart_account = 0;
            if(isset($request->outer_report_filter)) {
                foreach ($request->outer_report_filter as $outer_report_filter) {
                    foreach ($outer_report_filter['report_filter'] as $inner_report_filter) {
                        if (!empty($inner_report_filter['report_filter_name'])) {
                            if($inner_report_filter['report_filter_name'] == 'chart_code') {
                                $chart_account = TblAccCoa::where('chart_code',$inner_report_filter['report_value'][0])->first();
                            }
                        }
                    }
                }
            }
            //dd($chart_account_id->chart_account_id);
            $chart_account_id = (int)$chart_account->chart_account_id;
            if(!empty($chart_account_id)){
                $data['opening_balance'] = collect(DB::select('SELECT fun_acco_opening_bal(?,?,?,?,?) AS code from dual', [$from_date,$chart_account_id,auth()->user()->business_id,auth()->user()->company_id,auth()->user()->branch_id]))->first()->code;
            }else{
                return $this->jsonErrorResponse($data, 'Must select Chart Code', 422);
            }
            $data['page_title'] = $getTable->reporting_title;
            $data['criteria'] = $criteria;
            session(['data' => $data]);
            $dataJs['url'] = route( 'report.report_accounting_ledger');
        }elseif($getTable->reporting_case == 'sales_type_wise'){
            $data = [];
            $data['key'] = 'sales_type_wise';
            $page_title = $getTable->reporting_title;
            $to_date = date('Y-m-d', strtotime($to_date)); //for oracle db like 2020-04-16
            $from_date = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
            $query = 'Select distinct sales_code,sales_date,customer_name,sales_sales_type,sales_net_amount  from '.$table_name . ' '. $total_where.' and sales_sales_type <> 3 order by  sales_code asc';
            //dd($query);
            $data['list'] = DB::select($query);
            $data['page_title'] = $page_title;
            $data['criteria'] = $criteria;
            session(['data' => $data]);
            $dataJs['url'] = route( 'report.report_sale_type_wise');
        }elseif($getTable->reporting_case == 'closing_day'){
            $data['key'] = 'closing_day';
            $data['page_title'] = 'Daily closing Report';
            $to_date = $request->outer_report_filter[0]['report_filter'][0]['report_value_from'];
            $from_date = $request->outer_report_filter[0]['report_filter'][0]['report_value_to'];
            $data['to_date'] = date('Y-m-d', strtotime($to_date));
            $data['from_date'] = date('Y-m-d', strtotime($from_date));
            $query = "select distinct sales_sales_man ,sales_sales_man_name from vw_sale_sales_invoice where sales_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd') and to_date ('".$data['from_date']."', 'yyyy/mm/dd')";
            $data['SI'] = DB::select($query);
            $data['criteria'] = $criteria;
            session(['data' => $data]);
            $dataJs['url'] = route( 'report.daily_closing_report');
        }elseif($getTable->reporting_case == 'dailyActivity'){
            $data['key'] = 'dailyActivity';
            $data['page_title'] = 'Summary of Daily Activity';
            $from_date = $request->outer_report_filter[0]['report_filter'][0]['report_value_from'];
            $to_date = $request->outer_report_filter[0]['report_filter'][0]['report_value_to'];
            $data['from_date'] = date('Y-m-d', strtotime($from_date));
            $data['to_date'] = date('Y-m-d', strtotime($to_date));
            $Datequery = "select distinct sales_date from vw_sale_sales_invoice where sales_type ='SI' and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') order by sales_date";
            $data['Date'] = DB::select($Datequery);
            $SIquery = "select distinct sales_sales_man ,sales_sales_man_name from vw_sale_sales_invoice where sales_type ='SI' and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
            $data['SI'] = DB::select($SIquery);
            $SRquery = "select distinct sales_sales_man ,sales_sales_man_name from vw_sale_sales_invoice where sales_type ='SR' and sales_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
            $data['SR'] = DB::select($SRquery);
            $data['SI_Count'] = count($data['SI']);
            $data['SR_Count'] = count($data['SR']);
            $data['criteria'] = $criteria;
            session(['data' => $data]);
            $dataJs['url'] = route( 'report.daily_activity_report');
        }else{
            $data['title'] = $getTable->reporting_title;
            $data['key'] = 'view';
            session(['data' => $data]);
            $dataJs['url'] = route( 'report.report_view' );
        }

        $dataJs['redirect'] =  $user_studio->reporting_user_studio_id;

        return $this->jsonSuccessResponse($dataJs, trans('message.report_ready'), 200);
    }
    public function reportSaleInvoice(){
        $data = Session::get('data');
        if($data['key'] == 'sale_invoice'){
            return view('report.report_sale_invoice');
        }else{
            abort('404');
        }
    }
    public function reportAccountingLedger(){
        $data = Session::get('data');
        if($data['key'] == 'accounting_ledger'){
            return view('report.report_accounting_ledger');
        }else{
            abort('404');
        }
    }
    public function reportSaleTypeWise(){
        $data = Session::get('data');
        if($data['key'] == 'sales_type_wise'){
            return view('report.report_sale_type_wise');
        }else{
            abort('404');
        }
    }
    public function reportDailyActivity(){
        $data = Session::get('data');
        if($data['key'] == 'dailyActivity'){
            return view('report.daily_activity_report');
        }else{
            abort('404');
        }
    }

    public function closeOutReport(){
        return view('prints.sale.close-out-report');
    }
    public function DayClosing(){
        $data = Session::get('data');
        if($data['key'] == 'closing_day'){
            return view('report.daily_closing_report');
        }else{
            abort('404');
        }
        /*
        $to_date = '09-10-2020';
        $from_date = '09-12-2020';
        $data['to_date'] = date('Y-m-d', strtotime($to_date));
        $data['from_date'] = date('Y-m-d', strtotime($from_date));
        $query = "select distinct sales_sales_man ,sales_sales_man_name from vw_sale_sales_invoice where sales_date between to_date ('".$data['to_date']."', 'yyyy/mm/dd') and to_date ('".$data['from_date']."', 'yyyy/mm/dd')";
        $data['SI'] = DB::select($query);
        return view('report.daily_closing_report',compact('data'));
        */
    }
    public function reportView()
    {
        $data = Session::get('data');
        if($data['key'] == 'view'){
            return view('report.report_view');
        }else{
            abort('404');
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
}
