<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function saleDashboard(Request $request)
    {
        $data = [];
        $now = new \DateTime("now");
        $today_format = $now->format("d-m-Y"); //for blade template
        $today = date('Y-m-d', strtotime($today_format)); //for oracle db like 2020-04-16
        $data['today'] = $today_format;
        // previous 1 month date from today
        $from = $now->modify('-1 months');
        $from_date = $from->format("d-m-Y"); //for blade template
        $from = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
        $data['from'] = $from_date;

        $pass_parameter = [$today,auth()->user()->business_id,auth()->user()->company_id,auth()->user()->branch_id];
        $data['monthly_sale'] = collect(DB::select('SELECT fun_sale_monthly_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
        $data['weekly_sale'] = collect(DB::select('SELECT fun_sale_weekly_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
        $data['today_sale'] = collect(DB::select('SELECT fun_sale_daily_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
        $data['year_sale'] = collect(DB::select('SELECT fun_sale_yearly_sale(?,?,?,?) AS code from dual', $pass_parameter))->first()->code;
        $data['new_products'] = DB::table('tbl_purc_product as p')
            ->where('p.business_id', auth()->user()->business_id)
            ->where('p.company_id', auth()->user()->company_id)
            ->where('p.branch_id', auth()->user()->branch_id)
            ->whereBetween('p.created_at', [$from,$today])
            ->count();

        $data['new_customers'] = DB::table('tbl_sale_customer as c')
            ->where('c.business_id', auth()->user()->business_id)
            ->where('c.company_id', auth()->user()->company_id)
            ->where('c.branch_id', auth()->user()->branch_id)
            ->whereBetween('c.created_at', [$from,$today])
            ->count();

        $q = "SELECT  (SUM(avg_INV) / COUNT(sales_date)) as avg_daily_invoices FROM (
                select vsi.sales_date, COUNT( DISTINCT vsi.sales_dtl_total_amount) as avg_INV
                from  VW_SALE_SALES_INVOICE   vsi
                WHERE vsi.SALES_DATE BETWEEN TO_DATE('".$today."', 'yyyy/mm/dd') AND TO_DATE('".$today."', 'yyyy/mm/dd')
                and business_id = ".auth()->user()->business_id." and company_id = ".auth()->user()->company_id." and branch_id = ".auth()->user()->branch_id."
                group by VSI.SALES_DATE
                ) daily_invoices";
        $data['avg_daily_invoices'] = DB::selectOne($q)->avg_daily_invoices;

        $q = "SELECT  (SUM(avg_inv) / COUNT( month_sales)) as avg_monthly_invoices  FROM (
                select to_char(vsi.SALES_DATE,'mon-rrrr') as month_sales, COUNT( DISTINCT vsi.sales_dtl_total_amount) as avg_inv
                from  VW_SALE_SALES_INVOICE  vsi
                WHERE vsi.SALES_DATE BETWEEN TO_DATE('".$from."', 'yyyy/mm/dd') AND TO_DATE('".$today."', 'yyyy/mm/dd')
                and business_id = ".auth()->user()->business_id." and company_id = ".auth()->user()->company_id." and branch_id = ".auth()->user()->branch_id."
                group by to_char(vsi.SALES_DATE,'mon-rrrr')
                ) monthly_invoices";
        $data['avg_monthly_invoices'] = DB::selectOne($q)->avg_monthly_invoices;

        $view['view'] = view('dashboard.sale',compact('data'))->render();

        return $this->jsonSuccessResponse($view, '', 200);
    }



    public function accountDashboard(Request $request)
    {
        $data = [];
        
        $q = "select 
                SYN.branch_id , 
                BRA.BRANCH_NAME , 
                SYN.description , 
                max(SYN.ENTRY_DATE_TIME)  ENTRY_DATE_TIME  ,
                CURRENT_TIMESTAMP as time,
                max(SYN.ENTRY_DATE_TIME) - CURRENT_TIMESTAMP AS difference
            from 
                TBL_SOFT_SYNCING_UPDATES SYN , TBL_SOFT_BRANCH BRA   
            where BRA.BRANCH_ID  = SYN.branch_id 
                AND SYN.DESCRIPTION is not null   
                -- AND SYN.branch_id = ".auth()->user()->branch_id."
            group by  SYN.branch_id , 
                SYN.description  ,  
                BRA.BRANCH_NAME 
            ORDER BY BRANCH_NAME ,  DESCRIPTION";

        $data['sync_data'] = DB::select($q);
        
        //dd($data['sync_data']);
        
        $view['view'] = view('dashboard.account',compact('data'))->render();

        return $this->jsonSuccessResponse($view, '', 200);
    }



    public function getChartData(Request $request)
    {
        $data = [];
        $now = new \DateTime("now");
        $today_format = $now->format("d-m-Y"); //for blade template
        $today = date('Y-m-d', strtotime($today_format)); //for oracle db like 2020-04-16
        $data['today'] = $today_format;
        // previous 1 month date from today
        $from = $now->modify('-1 months');
        $from_date = $from->format("d-m-Y"); //for blade template
        $from = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
        $data['branch_name'] = auth()->user()->branch->branch_name;
        if(isset($request->chart_name) && $request->chart_name == 'top_item_sales'){
            $query = "select * from (
                        select product_name,sum(sales_dtl_quantity) sales_dtl_quantity
                        from vw_sale_sales_invoice where sales_date between to_date('".$from."', 'yyyy/mm/dd') and to_date('".$today."', 'yyyy/mm/dd')
                        and business_id = ".auth()->user()->business_id." and company_id = ".auth()->user()->company_id." and branch_id = ".auth()->user()->branch_id."
                        group by product_name order by sales_dtl_quantity desc
                    )  abc where sales_dtl_quantity is not null and rownum <= 5 ";
            $data['top_sale_product'] = DB::select($query);
        }

        return $this->jsonSuccessResponse($data, '', 200);
    }
    public function getChartData2(Request $request){
        $data = [];
        $now = new \DateTime("now");
        $today_format = $now->format("d-m-Y"); //for blade template
        $today = date('Y-m-d', strtotime($today_format)); //for oracle db like 2020-04-16
        $data['today'] = $today_format;
        // previous 1 month date from today
        $from = $now->modify('-1 months');
        $from_date = $from->format("d-m-Y"); //for blade template
        $from = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
        $data['from'] = $from_date;

        if(isset($request->chart_name) && $request->chart_name == 'hours_branch_wise'){
            $query = "SELECT *  FROM   (
                            SELECT SALES_NET_AMOUNT , extract(hour from cast(CREATED_AT as timestamp)) HOUR
                            FROM   tbl_sale_Sales
                            WHERE   SALES_DATE BETWEEN  to_date('".$today."', 'yyyy/mm/dd') and to_date('".$today."', 'yyyy/mm/dd')
                            and business_id = ".auth()->user()->business_id." and company_id = ".auth()->user()->company_id." and branch_id = ".auth()->user()->branch_id."
                        )  PIVOT  ( SUM(SALES_NET_AMOUNT) FOR HOUR IN (
                            1 AS H1,2 As H2,3 As H3,4 As H4,5 As H5,6 As H6,
                            7 As H7,8  As H8,9 As H9,10 As H10,11 As H11,12 As H12,
                            13 AS H13,14 AS H14,15 AS H15,16 AS H16,17 AS H17,18 AS H18,
                            19 AS H19,20 AS H20,21 AS H21,22 AS H22,23 AS H23,00 AS H24 )
                        )";

            $data['hours_branch_wise'] = DB::selectOne($query);
            if(!empty($data['hours_branch_wise'])){
                $data['hours_branch_wise_count'] = 24;
            }else{
                $data['hours_branch_wise_count'] = 0;
            }
        }
        return $this->jsonSuccessResponse($data, '', 200);
    }

    public function getChartData3(Request $request){
        $data = [];
        $now = new \DateTime("now");
        $today_format = $now->format("d-m-Y"); //for blade template
        $today = date('Y-m-d', strtotime($today_format)); //for oracle db like 2020-04-16
        $data['today'] = $today_format;
        // previous 1 month date from today
        $from = $now->modify('-1 months');
        $from_date = $from->format("d-m-Y"); //for blade template
        $from = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
        $data['from'] = $from_date;

        if(isset($request->chart_name) && $request->chart_name == 'ratio_and_product_group_wise_ajax'){
            $query = "select SUM(SALES_DTL_TOTAL_AMOUNT) TOTAL_AMOUNT , SUM(NET_PROFT_AMOUNT)    NET_PROFIT  ,   round(100*(SUM(NET_PROFT_AMOUNT) / sum(SUM(NET_PROFT_AMOUNT)) over ()),6) perc
                         FROM( select SALES_DATE ,  SALES_CODE  , PRODUCT_BARCODE_BARCODE ,
                                PRODUCT_NAME  , SALES_DTL_QUANTITY  , SALES_DTL_RATE ,  SALES_DTL_TOTAL_AMOUNT ,
                                COST_RATE , SALES_DTL_RATE-COST_RATE as Net_Profit, round( ((SALES_DTL_RATE-COST_RATE)/COST_RATE)  * 100,3) profit_Percent  ,
                                SALES_DTL_QUANTITY * (SALES_DTL_RATE-COST_RATE) NET_PROFT_AMOUNT
                                from VW_SALE_SALES_INVOICE
                                where COST_RATE <> 0  and SALES_DATE  BETWEEN to_date('".$today."', 'yyyy/mm/dd') and to_date('".$today."', 'yyyy/mm/dd')
                                and business_id = ".auth()->user()->business_id." and company_id = ".auth()->user()->company_id." and branch_id = ".auth()->user()->branch_id."

                         ) ratio ";
            $data['ratio_and_product_group_wise_ajax'] = DB::selectOne($query);

            $query = "SELECT PRODUCT.GROUP_ITEM_NAME, SUM(SALE.SALES_DTL_QUANTITY) QUANTITY, SUM(SALE.SALES_DTL_AMOUNT) AMOUNT,
                        ROUND(100*(SUM(SALE.SALES_DTL_AMOUNT) / SUM(SUM(SALE.SALES_DTL_AMOUNT)) OVER ()),2) PERC
                        FROM VW_SALE_SALES_INVOICE SALE , VW_PURC_PRODUCT_BARCODE_REPORTING PRODUCT
                        WHERE PRODUCT.PRODUCT_BARCODE_ID = SALE.PRODUCT_BARCODE_ID
                        and  (SALE.SALES_DATE BETWEEN   to_date('".$from."', 'yyyy/mm/dd') and to_date('".$today."', 'yyyy/mm/dd') )
                        and PRODUCT.business_id = ".auth()->user()->business_id." and PRODUCT.company_id = ".auth()->user()->company_id." and PRODUCT.branch_id = ".auth()->user()->branch_id."
                        GROUP BY  PRODUCT.GROUP_ITEM_ID,PRODUCT.GROUP_ITEM_NAME ORDER BY SUM(SALES_DTL_AMOUNT) DESC FETCH FIRST 5 ROWS ONLY";

            $data['product_group'] = DB::select($query);

        }
        return $this->jsonSuccessResponse($data, '', 200);
    }

    public function getChartData4(Request $request)
    {
        $data = [];
        $now = new \DateTime("now");
        $today_format = $now->format("d-m-Y"); //for blade template
        $today = date('Y-m-d', strtotime($today_format)); //for oracle db like 2020-04-16
        $data['today'] = $today_format;
        // previous 1 month date from today
        $from = $now->modify('-11 months');
        $from_date = $from->format("d-m-Y"); //for blade template
        $from = date('Y-m-d', strtotime($from_date)); //for oracle db like 2020-04-16
        // first day of this year
        $fromYer = date('Y').'01-01'; //for oracle db like 2020-04-16
        $data['branch_name'] = auth()->user()->branch->branch_name;
        if(isset($request->chart_name) && $request->chart_name == 'month_sale_branch'){
            $query = "select TBL_SOFT_CALENDAR.CALENDAR_MONTH_NAME as month ,
                        SUM(SALE.SALES_DTL_AMOUNT) AMOUNT
                        from VW_SALE_SALES_INVOICE SALE , VW_PURC_PRODUCT_BARCODE  PRODUCT ,  TBL_SOFT_CALENDAR
                        WHERE TBL_SOFT_CALENDAR.CALENDAR_DATE =  SALE.SALES_DATE
                        and PRODUCT.PRODUCT_BARCODE_ID = SALE.PRODUCT_BARCODE_ID
                        and (SALE.SALES_DATE BETWEEN to_date('".$from."', 'yyyy/mm/dd') and to_date('".$today."', 'yyyy/mm/dd') )
                        and SALE.business_id = ".auth()->user()->business_id." and SALE.company_id = ".auth()->user()->company_id." and SALE.branch_id = ".auth()->user()->branch_id."
                        group by  TBL_SOFT_CALENDAR.CALENDAR_MONTH_NAME, TBL_SOFT_CALENDAR.CALENDAR_MONTH ORDER BY TBL_SOFT_CALENDAR.CALENDAR_MONTH";

            $data['month_sale_branch'] = DB::select($query);
        }
        return $this->jsonSuccessResponse($data, '', 200);
    }
    public function dummy($str)
    {
        return view("dashboard.dummy.$str")->render();
    }




}
