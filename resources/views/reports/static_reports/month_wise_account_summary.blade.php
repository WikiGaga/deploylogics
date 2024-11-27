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
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
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
                @php $chart_lists  = DB::table('tbl_acco_chart_account')->select('chart_code','chart_name')->where('chart_account_id',$data['chart_account_id'])->first(); @endphp
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Account Code:</span>
                    <span style="color: #5578eb;">{{$chart_lists->chart_code}}</span>
                </h6>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Account Name</span>
                    <span style="color: #5578eb;">{{$chart_lists->chart_name}}</span>
                </h6>
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th rowspan="2" class="text-center align-middle">Month</th>
                            <th rowspan="2" class="text-center align-middle">Opening Balance</th>
                            <th colspan="2" class="text-center align-middle">Transactions</th>
                            <th rowspan="2" class="text-center align-middle">Closing Balance</th>
                        </tr>    
                        <tr>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                        @php 
                            $tot_opening_bal = 0;
                            $tot_debit = 0;
                            $tot_credit = 0;
                            $tot_closing_bal = 0;

                            $month_qry = "select  distinct calendar_month_name ,   calendar_year , calendar_month_name || '-'||  calendar_year as calendar_month_year from 
                                            tbl_soft_calendar where calendar_date between  to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') order by  calendar_month_name asc";
                            $month_lists = DB::select($month_qry);
                        @endphp
                        @foreach($month_lists as $month_list)
                            @php 
                                //get start date of the month
                                $fstDay_qry = "select calendar_date from tbl_soft_calendar  where  calendar_month_name = '".$month_list->calendar_month_name."' and calendar_year = ".$month_list->calendar_year."   and rownum = 1 ";
                                $fstday = DB::select($fstDay_qry);
                                $start_date = date('Y-m-d', strtotime($fstday[0]->calendar_date));
                                
                                //get last date of the month
                                $lastDay_qry = "select calendar_date from tbl_soft_calendar  where  calendar_month_name = '".$month_list->calendar_month_name."' and calendar_year = ".$month_list->calendar_year."   order by calendar_date desc FETCH FIRST 1 ROWS ONLY ";
                                $lastday = DB::select($lastDay_qry);
                                $end_date = date('Y-m-d', strtotime($lastday[0]->calendar_date));

                                //get opening balance of the month
                                $paras = [
                                    'chart_account_id' => $data['chart_account_id'],
                                    'voucher_date' => date('d-m-Y', strtotime($start_date)),
                                    'branch_ids' => $data['branch_ids'],
                                ];
                                $opening_balc = \App\Library\CoreFunc::acco_opening_bal($paras);
                                if($opening_balc == null){
                                    $opening_bal =  0;
                                }else{
                                    $opening_bal =  $opening_balc;
                                }
                                $tot_opening_bal += $opening_bal;

                                //where clause
                                $where = "chart_account_id = ".$data['chart_account_id']." and (voucher_date between 
                                        to_date ('".$start_date."', 'yyyy/mm/dd') and to_date ('".$end_date."', 'yyyy/mm/dd')  ) and branch_id in (".implode(",",$data['branch_ids']).") 
                                        and company_id = ".auth()->user()->company_id." and business_id = ".auth()->user()->business_id;
                                
                                // get total debit of the month
                                $debit_qry = "select sum(voucher_debit) as voucher_debit from VW_ACCO_VOUCHER_POSTED where ".$where;
                                $debit_res = DB::select($debit_qry);
                                $debit = $debit_res[0]->voucher_debit;
                                $tot_debit = $debit;
                                
                                //get total credit of the month
                                $credit_qry = "select sum(voucher_credit) as voucher_credit from VW_ACCO_VOUCHER_POSTED where ".$where;
                                $credit_res = DB::select($credit_qry);
                                $credit = $credit_res[0]->voucher_credit;
                                $tot_credit = $credit;

                                //get closing balance
                                $closing_bal = $opening_bal;
                                if($debit != 0)
                                {        
                                    $closing_bal = str_replace(',', '', $closing_bal);
                                    $closing_bal =  $closing_bal + $debit;
                                }

                                if($credit != 0)
                                {   
                                    $closing_bal = str_replace(',', '', $closing_bal);
                                    $closing_bal =  $closing_bal - $credit;
                                }
                                $tot_closing_bal = $closing_bal;
                            @endphp
                            <tr>
                                <td>{{$month_list->calendar_month_year}}</td>
                                <td class="text-right">
                                    @if($opening_bal > 0)
                                        {{number_format($opening_bal,3)}} Dr
                                    @else
                                        {{number_format($opening_bal * (-1),3)}} Cr
                                    @endif
                                </td>
                                <td class="text-right">{{number_format($debit,3)}}</td>
                                <td class="text-right">{{number_format($credit,3)}}</td>
                                <td class="text-right">
                                    @if($closing_bal > 0)
                                        {{number_format($closing_bal,3)}} Dr
                                    @else
                                        {{number_format($closing_bal * (-1),3)}} Cr
                                    @endif
                                </td>
                            </tr>
                       @endforeach
                        <tr class="grand_total">
                            <td class="rep-font-bold">Total</td>
                            <td class="text-right rep-font-bold">
                                @if($tot_opening_bal > 0)
                                    {{number_format($tot_opening_bal,3)}} Dr
                                @else
                                    {{number_format($tot_opening_bal * (-1),3)}} Cr
                                @endif
                            </td>
                            <td class="text-right rep-font-bold">{{number_format($tot_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($tot_credit,3)}}</td>
                            <td class="text-right rep-font-bold">
                                @if($tot_closing_bal > 0)
                                    {{number_format($tot_closing_bal,3)}} Dr
                                @else
                                    {{number_format($tot_closing_bal * (-1),3)}} Cr
                                @endif
                            </td>
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


