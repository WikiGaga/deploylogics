@extends('layouts.report')
@section('title', 'Reward Point Ledger Detail')

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
        .customer_total{
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
                @if(isset($data['customer_ids']) && !empty($data['customer_ids']))
                    @php 
                        $customerDtl = \Illuminate\Support\Facades\DB::table('tbl_sale_customer')->where('customer_id',$data['customer_ids'])->first();
                    @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Customer:</span>
                        <span style="color: #5578eb;">{{" ".$customerDtl->customer_name." "}}</span>
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $where = "";
                        /*if(count($data['customer_ids']) != 0){
                            $where .= " and customer_id in( ".implode(",",$data['customer_ids']).")";
                        }*/

                        if(isset($data['customer_ids']) && !empty($data['customer_ids']) != 0){
                            $where .= " and customer_id = ".$customerDtl->customer_id."";
                        }

                        $query="SELECT 
                            CUSTOMER_ID,
                            CUSTOMER_NAME,
                            customer_mobile_no,
                            CARD_NUMBER,
                            MEMBERSHIP_TYPE_ID,
                            EXPIRY_DATE,
                            ISSUE_DATE,
                            SUM(LOYALTY_AMOUNT) LOYALTY_AMOUNT,
                            SUM(LOYALTY_EARNED) LOYALTY_EARNED,
                            SUM(SALES_LOYALTY_POINTS) AS SALES_LOYALTY_POINTS,
                            SUM(SALES_NET_AMOUNT) AS SALES_DTL_AMOUNT 
                        FROM 
                        (
                            SELECT DISTINCT 
                                CUSTOMER_ID,
                                CUSTOMER_NAME,
                                customer_mobile_no,
                                CARD_NUMBER,
                                MEMBERSHIP_TYPE_ID,
                                EXPIRY_DATE,
                                ISSUE_DATE,
                                LOYALTY_AMOUNT LOYALTY_AMOUNT,
                                LOYALTY_EARNED LOYALTY_EARNED,
                                SALES_LOYALTY_POINTS SALES_LOYALTY_POINTS,
                                SALES_NET_AMOUNT SALES_NET_AMOUNT 
                            FROM
                                VW_SALE_SALES_INVOICE 
                            WHERE branch_id in (".implode(",",$data['branch_ids']).") 
                                AND (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                                AND customer_id <> 26720820182987
                                $where
                            ORDER BY CUSTOMER_NAME,
                                customer_mobile_no,
                                SALES_CODE
                        ) GAGA 
                        GROUP BY CUSTOMER_ID,
                            CUSTOMER_NAME,
                            customer_mobile_no,
                            CARD_NUMBER,
                            MEMBERSHIP_TYPE_ID,
                            EXPIRY_DATE,
                            ISSUE_DATE
                        ORDER BY CARD_NUMBER,
                            CUSTOMER_NAME";

//dd($query);
                        $getdata = \Illuminate\Support\Facades\DB::select($query);
                        //dd($getdata);
                        $list = [];
                        foreach ($getdata as $row)
                        {
                            $list[] = $row;
                        }
                      //dd($list_branch);
                   @endphp
                    <table width="100%" id="rep_reward_point_summary_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">S #</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Mobile No.</th>
                            <th class="text-center">Card Number</th>
                            <th class="text-center">Inv. Amount</th>
                            <th class="text-center">Opening Point</th>
                            <th class="text-center">Point Earn</th>
                            <th class="text-center">Points Redeem</th>
                            <th class="text-center">Balance</th>
                        </tr>
                        @php 
                        $i=1;
                        $tot_amount =0;
                        $tot_open =0;
                        $tot_loyalty_earned =0;
                        $tot_loyalty_amount =0;
                        $tot_bal =0;
                        @endphp
                        @foreach($list as $k=>$item)
                            @php
                                $Point_Eearn = \Illuminate\Support\Facades\DB::table('vw_sale_sales_invoice')
                                    ->whereIn('branch_id',$data['branch_ids'])
                                    ->where('created_at','<',date('Y-m-d' ,strtotime($data['date_time_from'])))
                                    ->where('customer_id',$item->customer_id)
                                    ->first('loyalty_earned');

                                    $Point_Eearn = isset($Point_Eearn->loyalty_earned)?$Point_Eearn->loyalty_earned:0;

                                    $Redeem_Point = \Illuminate\Support\Facades\DB::table('vw_sale_sales_invoice')
                                    ->whereIn('branch_id',$data['branch_ids'])
                                    ->where('created_at','<',date('Y-m-d' ,strtotime($data['date_time_from'])))
                                    ->where('customer_id',$item->customer_id)
                                    ->first('loyalty_amount');

                                    $Redeem_Point = isset($Redeem_Point->loyalty_amount)?$Redeem_Point->loyalty_amount:0;

                                    $Openbalance = $Point_Eearn - $Redeem_Point;
                                    
                                    $balance = $Openbalance + $item->loyalty_earned - $item->loyalty_amount;
                                
                                    $tot_amount = $tot_amount + $item->sales_dtl_amount;
                                    $tot_loyalty_earned = $tot_loyalty_earned + $item->loyalty_earned;
                                    $tot_open = $tot_open + $Openbalance;
                                    $tot_loyalty_amount = $tot_loyalty_amount + $item->loyalty_amount;
                                    $tot_bal = $tot_bal + $balance;
                                @endphp
                                <tr>
                                    <td class="text-center">{{$i}}</td>
                                    <td class="text-left">{{$item->customer_name}}</td>
                                    <td class="text-center">{{$item->customer_mobile_no}}</td>
                                    <td class="text-center">{{$item->card_number}}</td>
                                    <td class="text-right">{{number_format($item->sales_dtl_amount,3)}}</td>
                                    <td class="text-right">{{number_format($Openbalance,3)}}</td>
                                    <td class="text-right">{{number_format($item->loyalty_earned,3)}}</td>
                                    <td class="text-right">{{number_format($item->loyalty_amount,3)}}</td>
                                    <td class="text-right">{{number_format($balance,3)}}</td>
                                </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                            <tr>
                                <td class="text-left customer_total" colspan="4"><b>TOTAL</b> </td>
                                <td class="text-right customer_total">{{number_format($tot_amount,3)}}</td>
                                <td class="text-right customer_total">{{number_format($tot_open,3)}}</td>
                                <td class="text-right customer_total">{{number_format($tot_loyalty_earned,3)}}</td>
                                <td class="text-right customer_total">{{number_format($tot_loyalty_amount,3)}}</td>
                                <td class="text-right customer_total">{{number_format($tot_bal,3)}}</td>
                            </tr>
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
                $("#rep_reward_point_summary_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



