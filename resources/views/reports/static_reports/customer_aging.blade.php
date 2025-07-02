@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date']))}}</span>
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th class="text-center">Current Balance</th>
                            <th class="text-center"><= 15 Days</th>
                            <th class="text-center"><= 30 Days</th>
                            <th class="text-center"><= 45 Days</th>
                            <th class="text-center"><= 60 Days</th>
                            <th class="text-center"><= 90 Days</th>
                            <th class="text-center"> > 90 Days</th>
                        </tr>
                        @php
                            $totopenBal = 0;
                            $totDays15Amount = 0;
                            $totDays30Amount = 0;
                            $totDays45Amount = 0;
                            $totDays60Amount = 0;
                            $totDays90Amount = 0;
                            $totDays1000Amount = 0;
                        @endphp
                        @php
                            $where = "chart_account_id in (".implode(",",$data['account_ids']).")";
                            $where_common = " and branch_id in (".implode(",",$data['branch_ids']).")";
                            $where_common .= " AND business_id = ".auth()->user()->business_id." AND company_id =".auth()->user()->company_id;
                            $where .= $where_common;

                            $chart_query = "select chart_account_id, chart_code, chart_name  from tbl_acco_chart_account  where ".$where." and chart_level = 4 order by chart_code";
                            $chart_lists = DB::select($chart_query);

                            foreach($chart_lists as $chart_list){
                                $paras = [
                                    'chart_account_id' => $chart_list->chart_account_id,
                                    'voucher_date' => date('d-m-Y', strtotime($data['date'])),
                                    'branch_ids' => $data['branch_ids'],
                                ];
                                $opening_bal = \App\Library\CoreFunc::acco_opening_bal($paras);

                                if($opening_bal < 0){

                                    $netAgingAmount =  $opening_bal ;

                                    $totopenBal+= $opening_bal ;


                                    // ====== 15 days =====

                                    $Days15 = date('Y-m-d', strtotime($data['date']. ' - 15 days'));
                                    $qry15 = "select sum(sales_net_amount) net_amount from
                                                (
                                                select distinct  sales_id  , sales_code, sales_net_amount from vw_sale_sales_invoice where (sales_type = 'pos' or sales_type = 'si')
                                                and sales_date  between  to_date ('".$Days15."', 'yyyy/mm/dd') and to_date ('".$data['date']."', 'yyyy/mm/dd')
                                                ".$where_common." and  customer_account_id = ".$chart_list->chart_account_id."
                                                ) xyz";
                                    $res15 = DB::select($qry15);
                                    $Days15Amount =  $res15[0]->net_amount ;

                                    // ====== case if balance => invoiceAmount ==

                                    if( $netAgingAmount < $Days15Amount )
                                    {
                                        $Days15Amount= $netAgingAmount;
                                    }
                                    else
                                    {
                                        $Days15Amount= $Days15Amount;
                                    }

                                    $totDays15Amount+= $Days15Amount ;
                                    $netAgingAmount =  $netAgingAmount -  $Days15Amount ;
                                    // ==========================================

                                    // ====== 30 days =====

                                    $Days30 = date('Y-m-d', strtotime($data['date']. ' - 30 days'));
                                    $qry30 = "select sum(sales_net_amount) net_amount from
                                                (
                                                select distinct  sales_id  , sales_code, sales_net_amount from vw_sale_sales_invoice where (sales_type = 'pos' or sales_type = 'si')
                                                and sales_date  between  to_date ('".$Days30."', 'yyyy/mm/dd') and to_date ('".$data['date']."', 'yyyy/mm/dd')
                                                ".$where_common." and  customer_account_id = ".$chart_list->chart_account_id."
                                                ) xyz";
                                    $res30 = DB::select($qry30);
                                    $Days30Amount =  $res30[0]->net_amount ;


                                    // ====== case if balance => invoiceAmount ==

                                    if( $netAgingAmount < $Days30Amount )
                                    {
                                        $Days30Amount = $netAgingAmount;
                                    }
                                    else
                                    {
                                        $Days30Amount= $Days30Amount;
                                    }

                                    $totDays30Amount+= $Days30Amount ;
                                    $netAgingAmount =  $netAgingAmount -  $Days30Amount ;
                                    // ==========================================

                                    // ====== 45 days =====

                                    $Days45 = date('Y-m-d', strtotime($data['date']. ' - 45 days'));
                                    $qry45 = "select sum(sales_net_amount) net_amount from
                                                (
                                                select distinct  sales_id  , sales_code, sales_net_amount from vw_sale_sales_invoice where (sales_type = 'pos' or sales_type = 'si')
                                                and sales_date  between  to_date ('".$Days45."', 'yyyy/mm/dd') and to_date ('".$data['date']."', 'yyyy/mm/dd')
                                                ".$where_common." and  customer_account_id = ".$chart_list->chart_account_id."
                                                ) xyz";
                                    $res45 = DB::select($qry45);
                                    $Days45Amount =  $res45[0]->net_amount ;


                                    // ====== case if balance => invoiceAmount ==

                                    if( $netAgingAmount < $Days45Amount )
                                    {
                                        $Days45Amount = $netAgingAmount;
                                    }
                                    else
                                    {
                                        $Days45Amount= $Days45Amount;
                                    }

                                    $totDays45Amount+= $Days45Amount ;
                                    $netAgingAmount =  $netAgingAmount -  $Days45Amount ;
                                    // ==========================================

                                    // ====== 60 days =====

                                    $Days60 = date('Y-m-d', strtotime($data['date']. ' - 60 days'));
                                    $qry60 = "select sum(sales_net_amount) net_amount from
                                                (
                                                select distinct  sales_id  , sales_code, sales_net_amount from vw_sale_sales_invoice where (sales_type = 'pos' or sales_type = 'si')
                                                and sales_date  between  to_date ('".$Days60."', 'yyyy/mm/dd') and to_date ('".$data['date']."', 'yyyy/mm/dd')
                                                ".$where_common." and  customer_account_id = ".$chart_list->chart_account_id."
                                                ) xyz";
                                    $res60 = DB::select($qry60);
                                    $Days60Amount =  $res60[0]->net_amount ;


                                    // ====== case if balance => invoiceAmount ==

                                    if( $netAgingAmount < $Days60Amount )
                                    {
                                        $Days60Amount = $netAgingAmount;
                                    }
                                    else
                                    {
                                        $Days60Amount= $Days60Amount;
                                    }

                                    $totDays60Amount+= $Days60Amount ;
                                    $netAgingAmount =  $netAgingAmount -  $Days60Amount ;
                                    // ==========================================

                                    // ====== 90 days =====

                                    $Days90 = date('Y-m-d', strtotime($data['date']. ' - 90 days'));
                                    $qry90 = "select sum(sales_net_amount) net_amount from
                                                (
                                                select distinct  sales_id  , sales_code, sales_net_amount from vw_sale_sales_invoice where (sales_type = 'pos' or sales_type = 'si')
                                                and sales_date  between  to_date ('".$Days90."', 'yyyy/mm/dd') and to_date ('".$data['date']."', 'yyyy/mm/dd')
                                                ".$where_common." and  customer_account_id = ".$chart_list->chart_account_id."
                                                ) xyz";
                                    $res90 = DB::select($qry90);
                                    $Days90Amount =  $res90[0]->net_amount ;


                                    // ====== case if balance => invoiceAmount ==

                                    if( $netAgingAmount < $Days90Amount )
                                    {
                                        $Days90Amount = $netAgingAmount;
                                    }
                                    else
                                    {
                                        $Days90Amount= $Days90Amount;
                                    }

                                    $totDays90Amount+= $Days90Amount ;
                                    $netAgingAmount =  $netAgingAmount -  $Days90Amount ;
                                    // ==========================================

                                    // ====== greater 90 days =====

                                    $Days1000 = date('Y-m-d', strtotime($data['date']. ' - 1000 days'));
                                    $qry1000 = "select sum(sales_net_amount) net_amount from
                                                (
                                                select distinct  sales_id  , sales_code, sales_net_amount from vw_sale_sales_invoice where (sales_type = 'pos' or sales_type = 'si')
                                                and sales_date  between  to_date ('".$Days1000."', 'yyyy/mm/dd') and to_date ('".$data['date']."', 'yyyy/mm/dd')
                                                ".$where_common." and  customer_account_id = ".$chart_list->chart_account_id."
                                                ) xyz";
                                    $res1000 = DB::select($qry1000);
                                    $Days1000Amount =  $res1000[0]->net_amount ;


                                    // ====== case if balance => invoiceAmount ==

                                    if( $netAgingAmount < $Days1000Amount )
                                    {
                                        $Days1000Amount = $netAgingAmount;
                                    }
                                    else
                                    {
                                        $Days1000Amount= $Days1000Amount;
                                    }

                                    $totDays1000Amount+= $Days1000Amount ;
                                    $netAgingAmount =  $netAgingAmount -  $Days1000Amount ;
                                    // ==========================================
                        @endphp
                            <tr>
                                <td>{{$chart_list->chart_code}}</td>
                                <td>{{$chart_list->chart_name}}</td>
                                <td class="text-right">{{number_format($opening_bal,3)}}</td>
                                <td class="text-right">{{number_format($Days15Amount,3)}}</td>
                                <td class="text-right">{{number_format($Days30Amount,3)}}</td>
                                <td class="text-right">{{number_format($Days45Amount,3)}}</td>
                                <td class="text-right">{{number_format($Days60Amount,3)}}</td>
                                <td class="text-right">{{number_format($Days90Amount,3)}}</td>
                                <td class="text-right">{{number_format($Days1000Amount,3)}}</td>
                            </tr>
                        @php }} @endphp
                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Total</td>
                            <td class="text-right rep-font-bold">{{number_format($totopenBal,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($totDays15Amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($totDays30Amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($totDays45Amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($totDays60Amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($totDays90Amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($totDays1000Amount,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot sale_invoice_footer" style="background: #f7f8fa">
            <div class="row">
                <div class="col-lg-12 kt-align-right">
                    <div class="date"><span>Date: </span>{{ date('d-m-Y') }} - <span>User: </span>{{auth()->user()->name}}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection
@section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_sale_invoice_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection


