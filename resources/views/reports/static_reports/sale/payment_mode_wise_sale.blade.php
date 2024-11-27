@extends('layouts.report')
@section('title', 'Payment Mode Wise Sale')

@section('pageCSS')
    <style>
        /* Styles go here */
        .vertical {
            writing-mode: vertical-rl;
            text-orientation: sideways;
            transform: rotate(180deg);
            font-size:12px;
            font-weight:bold;
        }
        .barnch_color{
            vertical-align: middle;
            text-align: center;
            background: #cffbc7;
        }
        .sale_type_color{
            vertical-align: middle;
            text-align: center;
            background: #fbf9c7;
        }
        .terminal_total{
            background: #deb887;
        }

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
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_time_from']))." to ". date('d-m-Y', strtotime($data['date_time_to']))." "}}</span>
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
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
                    @php
                        $query = "SELECT BRANCH_ID,
                        BRANCH_NAME,
                        SALES_SALES_MAN_NAME,
                        SALES_TYPE,
                        SALES_DATE,
                        SUM(CASH_AMOUNT)  CASH_AMOUNT,
                        SUM(VISA_AMOUNT)   VISA_AMOUNT ,
                        SUM(LOYALTY_AMOUNT) LOYALTY_AMOUNT ,
                        SUM(CASH_AMOUNT) +  SUM(VISA_AMOUNT) + SUM(LOYALTY_AMOUNT) TOTAL_AMOUNT
                        FROM (
                        SELECT DISTINCT
                        BRANCH_ID,
                        BRANCH_NAME,
                        SALES_SALES_MAN_NAME,
                        (CASE
                            WHEN SALES_TYPE = 'POS'
                            THEN 'SALE'
                            WHEN SALES_TYPE = 'RPOS'
                            THEN 'SALE RETURN'
                            END
                        ) as SALES_TYPE,
                        SALES_DATE,
                            CASH_AMOUNT ,
                            VISA_AMOUNT  ,
                            LOYALTY_AMOUNT  ,
                            SALES_ID
                        FROM
                        VW_SALE_SALES_INVOICE
                        where branch_id in (".implode(",",$data['branch_ids']).")
                        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                        ORDER BY branch_name ,  SALES_SALES_MAN_NAME ,  sales_type ,  sales_date
                        )GAGA
                        GROUP BY  BRANCH_ID,BRANCH_NAME,SALES_SALES_MAN_NAME,SALES_TYPE, SALES_DATE
                        ORDER BY SALES_DATE,SALES_SALES_MAN_NAME";
//dd($query);

                        $getdata = \Illuminate\Support\Facades\DB::select($query);
                        //  dd($getdata);
                        $list_branch = [];
                        foreach ($getdata as $row)
                        {
                            $list_branch[$row->branch_name][$row->sales_type][$row->sales_sales_man_name][$row->sales_date] = $row;
                        }

                      //  dd($list_branch);
                      $rowspans = [];
                        $i = 1;
                   @endphp
                    @foreach($list_branch as $branch_key=>$branch_row)
                        @php
                            $rs_branch = 0;
                        @endphp
                        @foreach($branch_row as $sale_type_key=>$sale_type)
                            @php
                                $rs_sale_type = 0;
                                $rs_sale_type += count($sale_type);
                                $rs_branch += count($sale_type);
                                $rs_branch += 1;
                            @endphp
                            @foreach($sale_type as $terminal_key=>$terminal)
                                @php
                                    $rs_terminal = 0;
                                @endphp
                                @foreach($terminal as $item)
                                    @php
                                        $rs_branch += 1;
                                        $rs_sale_type += 1;
                                        $rs_terminal += 1;
                                        $rowspans[$branch_key] = $rs_branch;
                                        $rowspans[$branch_key.'_'.$sale_type_key] = $rs_sale_type;
                                        $rowspans[$branch_key.'_'.$sale_type_key.'_'.$terminal_key] = $rs_terminal;
                                    @endphp
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach

                    @php
                        $branch_key_new = "";
                        $sale_type_key_new = "";
                        $terminal_key_new = "";
                    @endphp
                    <table width="100%" id="rep_payment_mode_wise_sale_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center barnch_color"></th>
                            <th class="text-center sale_type_color"></th>
                            <th class="text-center" colspan="2"></th>
                            <th class="text-center">CASH</th>
                            <th class="text-center">CREDIT CARD</th>
                            <th class="text-center">REWARD POINT</th>
                            <th class="text-center">TOTAL</th>
                        </tr>
                        @foreach($list_branch as $branch_key=>$branch_row)
                            @php
                                $gtot_cash_amount = 0;
                                $gtot_visa_amount = 0;
                                $gtot_loyalty_amount = 0;
                                $gtotal_amount = 0;
                            @endphp
                           @foreach($branch_row as $sale_type_key=>$sale_type)
                                @php
                                    $subtot_cash_amount = 0;
                                    $subtot_visa_amount = 0;
                                    $subtot_loyalty_amount = 0;
                                    $subtotal_amount = 0;
                                @endphp
                            @foreach($sale_type as $terminal_key=>$terminal)
                                @php

                                    $tot_cash_amount = 0;
                                    $tot_visa_amount = 0;
                                    $tot_loyalty_amount = 0;
                                    $total_amount = 0;
                                @endphp
                                @foreach($terminal as $date_key=>$item)
                                    @php

                                        if($branch_key != ""){
                                            $branch_key_new = $branch_key;
                                        }
                                        if($sale_type_key != ""){
                                            $sale_type_key_new = $sale_type_key;
                                        }
                                        if($terminal_key != ""){
                                            $terminal_key_new = $terminal_key;
                                        }

                                        $tot_cash_amount = $tot_cash_amount + $item->cash_amount;
                                        $tot_visa_amount = $tot_visa_amount + $item->visa_amount;
                                        $tot_loyalty_amount = $tot_loyalty_amount + $item->loyalty_amount;
                                        $total_amount = $total_amount + $item->total_amount;

                                        $subtot_cash_amount = $subtot_cash_amount + $item->cash_amount;
                                        $subtot_visa_amount = $subtot_visa_amount + $item->visa_amount;
                                        $subtot_loyalty_amount = $subtot_loyalty_amount + $item->loyalty_amount;
                                        $subtotal_amount = $subtotal_amount + $item->total_amount;

                                        $gtot_cash_amount = $gtot_cash_amount + $item->cash_amount;
                                        $gtot_visa_amount = $gtot_visa_amount + $item->visa_amount;
                                        $gtot_loyalty_amount = $gtot_loyalty_amount + $item->loyalty_amount;
                                        $gtotal_amount = $gtotal_amount + $item->total_amount;
                                    @endphp
                                    <tr>
                                        @if($branch_key != "")
                                        <td class="vertical barnch_color" rowspan="{{$rowspans[$branch_key_new]}}">{{$branch_key}}</td>
                                        @endif
                                        @if($sale_type_key != "")
                                        <td class="vertical sale_type_color" rowspan="{{$rowspans[$branch_key_new.'_'.$sale_type_key_new]}}">{{$sale_type_key}}</td>
                                        @endif
                                        @if($terminal_key != "")
                                        <td class="terminal_total" rowspan="{{$rowspans[$branch_key_new.'_'.$sale_type_key_new.'_'.$terminal_key_new]}}">{{$terminal_key}}</td>
                                        @endif
                                        <td>{{date('d-m-Y',strtotime($date_key))}}</td>
                                        <td class="text-right">{{number_format($item->cash_amount,3)}}</td>
                                        <td class="text-right">{{number_format($item->visa_amount,3)}}</td>
                                        <td class="text-right">{{number_format($item->loyalty_amount,3)}}</td>
                                        <td class="text-right">{{number_format($item->total_amount,3)}}</td>
                                    </tr>
                                    @php
                                        $branch_key = "";
                                        $sale_type_key = "";
                                        $terminal_key = "";
                                    @endphp
                                @endforeach
                                    <tr>
                                        <td class="text-left terminal_total" colspan="2"><b>TOTAL</b> </td>
                                        <td class="text-right terminal_total">{{number_format($tot_cash_amount,3)}}</td>
                                        <td class="text-right terminal_total">{{number_format($tot_visa_amount,3)}}</td>
                                        <td class="text-right terminal_total">{{number_format($tot_loyalty_amount,3)}}</td>
                                        <td class="text-right terminal_total">{{number_format($total_amount,3)}}</td>
                                    </tr>
                            @endforeach
                                <tr>
                                    <td class="text-left sale_type_color" colspan="3"><b>SUB TOTAL</b> </td>
                                    <td class="text-right sale_type_color">{{number_format($subtot_cash_amount,3)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($subtot_visa_amount,3)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($subtot_loyalty_amount,3)}}</td>
                                    <td class="text-right sale_type_color">{{number_format($subtotal_amount,3)}}</td>
                                </tr>
                           @endforeach
                                <tr>
                                    <td class="text-left barnch_color" colspan="4"><b>Branch TOTAL</b></td>
                                    <td class="text-right barnch_color">{{number_format($gtot_cash_amount,3)}}</td>
                                    <td class="text-right barnch_color">{{number_format($gtot_visa_amount,3)}}</td>
                                    <td class="text-right barnch_color">{{number_format($gtot_loyalty_amount,3)}}</td>
                                    <td class="text-right barnch_color">{{number_format($gtotal_amount,3)}}</td>
                                </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div> 
        @include('reports.template.footer')
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
                $("#rep_payment_mode_wise_sale_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



