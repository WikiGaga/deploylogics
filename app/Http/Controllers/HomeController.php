<?php

namespace App\Http\Controllers;

use App\Library\Utilities;
use App\Models\TblSoftBranch;
use App\Models\TblSoftDashWidgetBadge;
use App\Models\TblSoftDashWidgetBar;
use App\Models\TblSoftDashWidgetGraph;
use App\Models\User;
use App\Models\ViewSaleBranchMonthWiseSale;
use Carbon\Carbon;
use Illuminate\Http\Request;

// db and Validator
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use PDF;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $data = [];
        Utilities::addSession('save_branch');
        return view('home',compact('data'));
    }
    public function index2()
    {
        /*$data = [
            'name'=>'PDF'
        ];
        $pdf = PDF::loadView('pages.invoice', compact('data'));
        return $pdf->download('invoice.pdf');*/


        Utilities::addSession('save_branch');
        $now = new \DateTime("now");
        $today_format = $now->format("d-m-Y"); //for blade template
        $today = date('Y-m-d', strtotime($today_format)); //for oracle db like 2020-04-16
        $data['today'] = $today_format;
        // previous 6 month date from today

        $previous = $now->modify('-6 months');
        $previous_date = $previous->format("d-m-Y"); //for blade template
        $previous = date('Y-m-d', strtotime($previous_date)); //for oracle db like 2020-04-16
        $data['start_date'] = $previous_date;
        // current user branches
        $data['branch'] = Utilities::getAllBranches();

        // top badges
                $pass_parameter = [$today,auth()->user()->business_id,auth()->user()->company_id,auth()->user()->branch_id];
               $data['badges1_current_month_sale'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','current_month_sale')->first();
               $data['badges1_current_month_sale']['total_count'] = collect(DB::select('SELECT fun_sale_monthly_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
               $data['badges1_current_week_sale'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','current_week_sale')->first();
               $data['badges1_current_week_sale']['total_count'] = collect(DB::select('SELECT fun_sale_weekly_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
               $data['badges1_today_sale'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','today_sale')->first();
               $data['badges1_today_sale']['total_count'] = collect(DB::select('SELECT fun_sale_daily_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
               $data['badges1_current_year_sale'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','current_year_sale')->first();
               $data['badges1_current_year_sale']['total_count'] = collect(DB::select('SELECT fun_sale_yearly_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
        /*
                               // center badges
                               $data['new_products'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','new_products')->first();
                               $data['new_products']['total_count'] = DB::table('tbl_purc_product as p')
                                   ->where('p.business_id', auth()->user()->business_id)
                                   ->where('p.company_id', auth()->user()->company_id)
                                   ->where('p.branch_id', auth()->user()->branch_id)
                                   ->whereBetween('p.created_at', [$previous,$today])
                                   ->count();
                               $data['new_customers'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','new_customers')->first();
                               $data['new_customers']['total_count'] = DB::table('tbl_sale_customer as c')
                                   ->where('c.business_id', auth()->user()->business_id)
                                   ->where('c.company_id', auth()->user()->company_id)
                                   ->where('c.branch_id', auth()->user()->branch_id)
                                   ->whereBetween('c.created_at', [$previous,$today])
                                   ->count();
                               $data['avg_daily_invoices'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','avg_daily_invoices')->first();
                               $data['avg_daily_invoices']['total_count'] = DB::table('vw_sale_sales_invoice as vsi')
                                   ->whereBetween('vsi.sales_date', [$previous,$today])
                                   ->where('vsi.business_id', auth()->user()->business_id)
                                   ->where('vsi.company_id', auth()->user()->company_id)
                                   ->where('vsi.branch_id', auth()->user()->branch_id)
                                   ->select(DB::raw('vsi.sales_date, avg(vsi.sales_dtl_total_amount) as avg')) //,'sum(vsi.sales_dtl_total_amount) total_sales'
                                   ->groupBy('vsi.sales_date')
                                   ->first();
                               $data['avg_monthly_invoices'] = TblSoftDashWidgetBadge::where('dash_widget_case_name','avg_monthly_invoices')->first();
                               $data['avg_monthly_invoices']['total_count'] = DB::table('vw_sale_sales_invoice as vsi')
                                   ->whereBetween('vsi.sales_date', [$previous,$today])
                                   ->where('vsi.business_id', auth()->user()->business_id)
                                   ->where('vsi.company_id', auth()->user()->company_id)
                                   ->where('vsi.branch_id', auth()->user()->branch_id)
                                   ->select(DB::raw('to_char(vsi.sales_date,\'mon-rrrr\') as month, avg(vsi.sales_dtl_total_amount) as avg')) //,'sum(vsi.sales_dtl_total_amount) total_sales'
                                   ->groupBy(DB::raw('to_char(vsi.sales_date,\'mon-rrrr\')'))
                                   ->first();

                               //start Sale Branch Month Wise Sale graph
                               # error sort issue
                               $SaleBranchMonthWiseSale = ViewSaleBranchMonthWiseSale::where('branch_id',auth()->user()->branch_id)->get();
                               $data['branch_month_wise_sale']['Xaxis'] = [];
                               $data['branch_month_wise_sale']['series'] = [];
                               $month_wisale_data = [];
                               $branch_short_name = auth()->user()->branch->branch_short_name;
                               foreach ($SaleBranchMonthWiseSale as $SaleBranchMonthWiseSaleData){
                                   array_push($data['branch_month_wise_sale']['Xaxis'], $SaleBranchMonthWiseSaleData['month']);
                                   array_push($month_wisale_data, $SaleBranchMonthWiseSaleData['total_sales']);
                               }
                               $month_wisale_series = [
                                   'name' => $branch_short_name,
                                   'type' => 'column',
                                   'data' => $month_wisale_data
                               ];
                               array_push($data['branch_month_wise_sale']['series'], $month_wisale_series);
                               //end Sale Branch Month Wise Sale graph

                               // start top_customers
                               $data['top_customers'] = [];
                               $data['top_customers']['Xaxis'] = [];
                               $data['top_customers']['series'] = [];
                               $data['top_customers']['Yaxis'] = 'Number';
                               $top_customers = DB::table('vw_sale_sales_invoice as vsi')
                                   ->whereBetween('vsi.created_at', [$previous,$today])
                                   ->where('vsi.business_id', auth()->user()->business_id)
                                   ->where('vsi.company_id', auth()->user()->company_id)
                                   ->where('vsi.branch_id', auth()->user()->branch_id)
                                   ->select(DB::raw('vsi.customer_id,vsi.customer_name, sum(vsi.sales_dtl_total_amount) as total_sales')) //,'sum(vsi.sales_dtl_total_amount) total_sales'
                                   ->groupBy('vsi.customer_id','vsi.customer_name')
                                   ->orderBy('total_sales', 'desc')
                                   ->limit(5)
                                   ->get();
                               foreach($top_customers as $top_customer){
                                   array_push($data['top_customers']['Xaxis'],$top_customer->customer_name);
                                   array_push($data['top_customers']['series'], $top_customer->total_sales);
                               }
                               // end top_customers
                               // start Top Item Sales
                               $data['top_item_sales'] = [];
                               $data['top_item_sales']['Xaxis'] = [];
                               $data['top_item_sales']['series'] = [];
                               $data['top_item_sales']['Yaxis'] = 'Number';
                               $top_item_sales = DB::table('vw_sale_sales_invoice as vsi')
                                   ->whereBetween('vsi.created_at', [$previous,$today])
                                   ->where('vsi.business_id', auth()->user()->business_id)
                                   ->where('vsi.company_id', auth()->user()->company_id)
                                   ->where('vsi.branch_id', auth()->user()->branch_id)
                                   ->select(DB::raw('vsi.product_id,vsi.product_name,vsi.product_barcode_id,vsi.product_barcode_barcode,sum(vsi.sales_dtl_total_amount) as total_sales'))
                                   ->groupBy('vsi.product_id','vsi.product_name','vsi.product_barcode_id','vsi.product_barcode_barcode')
                                   ->orderBy('total_sales', 'desc')
                                   ->limit(5)
                                   ->get();
                               foreach($top_item_sales as $top_item_sale){
                                   array_push($data['top_item_sales']['Xaxis'],$top_item_sale->product_name);
                                   array_push($data['top_item_sales']['series'], $top_item_sale->total_sales);
                               }
                              // dd($data['top_item_sales']);
                               // end Top Item Sales

                               $data['badges2'] = TblSoftDashWidgetBadge::where('dash_widget_id',2)
                                   ->where('dash_widget_badge_entry_status',1)
                                   ->get();

                               $data['graph'] = $this->WidgetGraph('product');
                               $data['branches_sale'] = $this->WidgetGraph('branches_sale');
                               $data['sale_purchase'] = $this->WidgetGraph('sale_purchase');
                               $data['radial_bar'] = $this->WidgetGraph('radial_bar');
                               $data['donut_chart'] = $this->WidgetGraph('donut_chart');
                              // dd($data);*/
        return view('home',compact('data'));
    }

    public function  WidgetGraph($case_name)
    {
        /**********************
         *   Start Widget
         */
        $DashWidgetGraph = TblSoftDashWidgetGraph::where('dash_widget_case_name',$case_name)->first();
        for($i=0;$i<5;$i++){
            $q =  'query_'.$i;
            if(isset($DashWidgetGraph->$q) && !empty($DashWidgetGraph->$q)){
                ${"query_" . $i} = DB::select($DashWidgetGraph->$q);
            }
        }
        if(empty($DashWidgetGraph)){
            $data = [];
            return $data;
        }
        $Xaxis =   DB::select($DashWidgetGraph->x_axis_titles_qry);
        $x_axis_titles = $DashWidgetGraph->x_axis;
        $titles = explode(', ', $x_axis_titles);
        $data['widget_title'] = $DashWidgetGraph->dash_widget_graph_name;
        $data['Yaxis'] = $DashWidgetGraph->y_axis;
        $data['series'] = [];
        $data['Xaxis'] = [];
        for($k=0;$k<count($titles);$k++){
            $l =$k+1;
            $val ='val_'.$l;
            $Dash = [];
            for($m=0; count($query_1) > $m; $m++){
              // dump($query_1[$m]->val_1);
               array_push($Dash, ${"query_" . $l}[$m]->$val);
            }
            $series_obj = (object)[
                'name' => $titles[$k],
                'data' => $Dash
            ];
            array_push($data['series'], $series_obj);
        }
        foreach ($Xaxis as $Xaxiss){
            array_push($data['Xaxis'], $Xaxiss->name);
        }
        return $data;
        /*
         *  End Widget
         **********************/
    }

    public function saleDashboard(){
        $data = [];
        $data['branch'] = TblSoftBranch::where('business_id',auth()->user()->business_id)->get();
        $data['badges'] = TblSoftDashWidgetBadge::where('dash_widget_id',2)
            ->where('dash_widget_badge_entry_status',1)
            ->get();
        $data['badges2'] = TblSoftDashWidgetBadge::where('dash_widget_id',3)
            ->where('dash_widget_badge_entry_status',1)
            ->get();
        return view('dashboard.sale-dashboard',compact('data'));
    }

    public function branchCreate(Request $request)
    {
        $data = Utilities::getAllBranches();

        if(!session()->has('user_branch')){
            return view('auth.branch',compact('data'));
        }else{
            return redirect()->action('HomeController@index');
        }

    }
    public function branchStore(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'branches' => 'required'
        ]);
        if ($validator->fails()) {
            $data['validator_errors'] = $validator->errors();
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        $getAllBranches = Utilities::getAllBranches();
        $arr = [];
        foreach($getAllBranches as $branch){
            array_push($arr,$branch->branch_id);
        }
        if (!in_array($request->branches,$arr)) {
            return $this->jsonErrorResponse($data, trans('message.required_fields'), 422);
        }
        DB::beginTransaction();
        try {
            $user = User::where('id', auth()->user()->id)->where('user_type','erp')->where('user_entry_status',1)->where(Utilities::currentBC())->first();
            $user->branch_id = $request->branches;
            $user->save();
            session(['user_branch' => $request->branches]);
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

    public function branchChange()
    {
        $data['branch'] = Utilities::getAllBranches();
        return view('setting.change_branch.form',compact('data'));

    }
    public function branchChangePopup($id)
    {
        dd($id);
        

    }
    public function dbTable(Request $request)
    {

        $data = [];
        $qry = "select distinct TABLE_NAME from VW_ALL_COLUMN_DATA order by TABLE_NAME ";
        $data['table'] = DB::select($qry);
        return view('db_view.table', compact('data'));
    }
    public function dbTableDtl($tbl)
    {

        $data = [];
        $qry = "select TABLE_NAME,COLUMN_NAME,DATA_TYPE,DATA_LENGTH from VW_ALL_COLUMN_DATA where lower(TABLE_NAME) = '" . strtolower($tbl) . "'
         order by COLUMN_NAME ";
        $data['table'] = DB::select($qry);
        $data['tbl'] = $tbl;
        return view('db_view.dtl', compact('data'));
    }
    public function dbTableCreateColumn(Request $request, $tbl)
    {
        $data = [];
        $data['tbl'] = $tbl;
        return view('db_view.create', compact('data'));
    }
    public function dbTableStoreColumn(Request $request, $tbl)
    {
        $data = [];
        $data['tbl'] = $tbl;
        if (
            !empty($request->column_name)
            && !empty($request->column_type)
        ) {
            $newColumnType = "";
            if ($request->column_type == 'char2') {
                $newColumnType = 'string';
            }
            if ($request->column_type == 'num') {
                $newColumnType = 'bigInteger';
            }
            if ($request->column_type == 'date') {
                $newColumnType = 'timestamp';
            }

            $newColumnName = str_replace('', '_', trim($request->column_name));

            if (!empty($newColumnType) && !empty($newColumnName)) {
                Schema::table($tbl, function (Blueprint $table) use ($newColumnType, $newColumnName) {
                    $table->$newColumnType($newColumnName)->nullable();
                });
            }
        }
        return redirect()->back();
    }
}
