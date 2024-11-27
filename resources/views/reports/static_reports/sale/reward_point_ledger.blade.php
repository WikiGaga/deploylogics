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

                       /*$query = "SELECT 
                            BRANCH_ID,
                            BRANCH_NAME,
                            CUSTOMER_ID,
                            CUSTOMER_NAME,
                            SALES_ID,
                            SALES_CODE,
                            SALES_TYPE,
                            SALES_DATE,
                            CARD_NUMBER,
                            EXPIRY_DATE,
                            ISSUE_DATE,
                            MEMBERSHIP_TYPE_ID,
                            SUM(LOYALTY_AMOUNT) LOYALTY_AMOUNT,
                            SUM(LOYALTY_EARNED) LOYALTY_EARNED,
                            SUM(SALES_LOYALTY_POINTS) AS SALES_LOYALTY_POINTS,
                            SUM(SALES_DTL_AMOUNT) AS SALES_DTL_AMOUNT
                        FROM (
                            SELECT DISTINCT 
                                BRANCH_ID,
                                BRANCH_NAME,
                                CUSTOMER_ID,
                                CUSTOMER_NAME,
                                (
                                CASE
                                WHEN SALES_TYPE = 'POS' 
                                THEN 'SALE' 
                                WHEN SALES_TYPE = 'RPOS' 
                                THEN 'SALE RETURN' 
                                END
                                ) AS SALES_TYPE,
                                SALES_DATE,
                                CARD_NUMBER,
                                EXPIRY_DATE,
                                ISSUE_DATE,
                                MEMBERSHIP_TYPE_ID,
                                LOYALTY_AMOUNT,
                                LOYALTY_EARNED,
                                SALES_LOYALTY_POINTS,
                                SALES_ID ,
                                SALES_CODE,
                                SALES_DTL_AMOUNT
                            FROM
                                VW_SALE_SALES_INVOICE
                            WHERE branch_id in (".implode(",",$data['branch_ids']).") 
                                and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                                $where
                            ORDER BY branch_name,
                                CUSTOMER_NAME,
                                sales_type,
                                sales_date,
                                CARD_NUMBER
                        ) GAGA 
                        GROUP BY BRANCH_ID,
                            BRANCH_NAME,
                            CUSTOMER_ID,
                            CUSTOMER_NAME,
                            SALES_TYPE,
                            SALES_DATE,
                            CARD_NUMBER,
                            EXPIRY_DATE,
                            ISSUE_DATE,
                            MEMBERSHIP_TYPE_ID,
                            SALES_ID,
                            SALES_CODE
                        ORDER BY SALES_DATE,
                        CARD_NUMBER,
                        CUSTOMER_NAME";
                        */

$query="SELECT 
  BRANCH_ID,
  BRANCH_NAME,
  CUSTOMER_ID,
  CUSTOMER_NAME,
  SALES_ID,
  SALES_CODE,
  SALES_TYPE,
  SALES_DATE,
  CARD_NUMBER,
  EXPIRY_DATE,
  ISSUE_DATE,
  MEMBERSHIP_TYPE_ID,
  SUM(LOYALTY_AMOUNT) LOYALTY_AMOUNT,
  SUM(LOYALTY_EARNED) LOYALTY_EARNED,
  SUM(SALES_LOYALTY_POINTS) AS SALES_LOYALTY_POINTS,
  SUM(SALES_NET_AMOUNT) AS SALES_DTL_AMOUNT 
FROM
  (
  SELECT DISTINCT 
    BRANCH_ID,
    BRANCH_NAME,
    CUSTOMER_ID,
    CUSTOMER_NAME,
    (
      CASE
        WHEN SALES_TYPE = 'POS' 
        THEN 'SALE' 
        WHEN SALES_TYPE = 'RPOS' 
        THEN 'SALE RETURN' 
      END
    ) AS SALES_TYPE,
    SALES_DATE,
    CARD_NUMBER,
    EXPIRY_DATE,
    ISSUE_DATE,
    MEMBERSHIP_TYPE_ID,
    LOYALTY_AMOUNT,
    LOYALTY_EARNED,
    SALES_LOYALTY_POINTS,
    SALES_ID,
    SALES_CODE,
    SALES_NET_AMOUNT 
  FROM
    VW_SALE_SALES_INVOICE 
  WHERE branch_id in (".implode(",",$data['branch_ids']).") 
        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
        and customer_id <> 26720820182987
        $where
  ORDER BY branch_name,
    CUSTOMER_NAME,
    sales_type,
    sales_date,
    CARD_NUMBER) GAGA 
GROUP BY BRANCH_ID,
  BRANCH_NAME,
  CUSTOMER_ID,
  CUSTOMER_NAME,
  SALES_TYPE,
  SALES_DATE,
  CARD_NUMBER,
  EXPIRY_DATE,
  ISSUE_DATE,
  MEMBERSHIP_TYPE_ID,
  SALES_ID,
  SALES_CODE 
ORDER BY SALES_DATE,
  CARD_NUMBER,
  CUSTOMER_NAME";

//dd($query);
                        $getdata = \Illuminate\Support\Facades\DB::select($query);
                        //dd($getdata);
                        $list_branch = [];
                        foreach ($getdata as $row)
                        {
                            $list_branch[$row->branch_name][$row->customer_name][] = $row;
                        }
                      //dd($list_branch);
                   @endphp
                    <table width="100%" id="rep_reward_point_ledger_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="6%" class="text-center">S #</th>
                            <th width="40%" class="text-center">Company Branch</th>
                            <th width="8%" class="text-center">Trans. #</th>
                            <th width="12%" class="text-center">Inv. Date</th>
                            <th width="10%" class="text-center">Inv. Amount</th>
                            <th width="8%" class="text-center">Point Earn</th>
                            <th width="8%" class="text-center">Points Redeem</th>
                            <th width="8%" class="text-center">Balance</th>
                        </tr>
                        @foreach($list_branch as $branch_key=>$branch_row)
                            @php
                                $gtot_amount =0;
                                $gtot_loyalty_earned =0;
                                $gtot_loyalty_amount =0;
                                $gtot_bal=0;
                            @endphp
                            <tr>
                                <td class="text-left" colspan="9">{{$branch_key}}</td>
                            </tr>
                           @foreach($branch_row as $sale_type_key=>$sale_type)
                                @php
                                    $Point_Eearn = \Illuminate\Support\Facades\DB::table('vw_sale_sales_invoice')
                                    ->whereIn('branch_id',$data['branch_ids'])
                                    ->where('created_at','<',date('Y-m-d' ,strtotime($data['date_time_from'])))
                                    ->where('customer_id',$sale_type[0]->customer_id)
                                    ->first('loyalty_earned');

                                    $Point_Eearn = isset($Point_Eearn->loyalty_earned)?$Point_Eearn->loyalty_earned:0;

                                    $Redeem_Point = \Illuminate\Support\Facades\DB::table('vw_sale_sales_invoice')
                                    ->whereIn('branch_id',$data['branch_ids'])
                                    ->where('created_at','<',date('Y-m-d' ,strtotime($data['date_time_from'])))
                                    ->where('customer_id',$sale_type[0]->customer_id)
                                    ->first('loyalty_amount');

                                    $Redeem_Point = isset($Redeem_Point->loyalty_amount)?$Redeem_Point->loyalty_amount:0;

                                    if(isset($sale_type[0]->membership_type_id) && $sale_type[0]->membership_type_id != ''){
                                        $member_type = \Illuminate\Support\Facades\DB::table('tbl_defi_membership_type')->where('membership_type_id',$sale_type[0]->membership_type_id)->first();
                                        $member_type = $member_type->membership_type_name;
                                    }else{
                                        $member_type = '';
                                    }
                                    $balance = $Point_Eearn - $Redeem_Point;
                                @endphp
                                <tr>
                                    <td class="text-left" colspan="3">
                                        <b>Name:</b> {{$sale_type[0]->customer_name}}
                                    </td>
                                    <td class="text-left" colspan="3">
                                        <b>Membership Type:</b> {{ $member_type }}
                                    </td>
                                    <td class="text-left" colspan="2">
                                        <b>Card Number:</b> {{$sale_type[0]->card_number}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-left" colspan="3">
                                        <b>Issue Date:</b> {{date('d-m-Y',strtotime($sale_type[0]->issue_date))}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <b>Expiry Date:</b> {{date('d-m-Y',strtotime($sale_type[0]->expiry_date))}}
                                    </td>
                                    <td class="text-left" colspan="3">
                                        <b>No Of Visits:</b> 
                                    </td>
                                    <td class="text-left" colspan="2">
                                        <b>Opening Quantity: {{ number_format($balance,3) }}</b>
                                    </td>
                                </tr>
                                @php 
                                $i=1;
                                $tot_amount =0;
                                $tot_loyalty_earned =0;
                                $tot_loyalty_amount =0;
                                $tot_bal =0;
                                @endphp
                                @foreach($sale_type as $date_key=>$item)
                                    @php
                                        $balance = $balance + $item->loyalty_earned - $item->loyalty_amount;

                                        $tot_amount = $tot_amount + $item->sales_dtl_amount;
                                        $tot_loyalty_earned = $tot_loyalty_earned + $item->loyalty_earned;
                                        $tot_loyalty_amount = $tot_loyalty_amount + $item->loyalty_amount;

                                        $gtot_amount = $gtot_amount + $item->sales_dtl_amount;
                                        $gtot_loyalty_earned = $gtot_loyalty_earned + $item->loyalty_earned;
                                        $gtot_loyalty_amount = $gtot_loyalty_amount + $item->loyalty_amount;

                                        
                                        $tot_bal = $tot_loyalty_earned - $tot_loyalty_amount;
                                        $gtot_bal = $gtot_loyalty_earned - $gtot_loyalty_amount;
                                    
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{$i}}</td>
                                        <td>{{$item->branch_name}}</td>
                                        <td class="text-center">{{$item->sales_code}}</td>
                                        <td class="text-center">{{date('d-m-Y',strtotime($item->sales_date))}}</td>
                                        <td class="text-right">{{number_format($item->sales_dtl_amount,3)}}</td>
                                        <td class="text-right">{{number_format($item->loyalty_earned,3)}}</td>
                                        <td class="text-right">{{number_format($item->loyalty_amount,3)}}</td>
                                        <td class="text-right">{{number_format($balance,3)}}</td>
                                   </tr>
                                    @php
                                    $i++;
                                    $branch_key = "";
                                    $sale_type_key = "";
                                    $terminal_key = "";
                                    @endphp
                                @endforeach
                                    <tr>
                                        <td class="text-left customer_total" colspan="4"><b>TOTAL</b> </td>
                                        <td class="text-right customer_total">{{number_format($tot_amount,3)}}</td>
                                        <td class="text-right customer_total">{{number_format($tot_loyalty_earned,3)}}</td>
                                        <td class="text-right customer_total">{{number_format($tot_loyalty_amount,3)}}</td>
                                        <td class="text-right customer_total">{{number_format($tot_bal,3)}}</td>
                                    </tr>
                            @endforeach
                                <tr>
                                    <td class="text-left barnch_color" colspan="4"><b>BRANCH WISE TOTAL</b> </td>
                                    <td class="text-right barnch_color">{{number_format($gtot_amount,3)}}</td>
                                    <td class="text-right barnch_color">{{number_format($gtot_loyalty_earned,3)}}</td>
                                    <td class="text-right barnch_color">{{number_format($gtot_loyalty_amount,3)}}</td>
                                    <td class="text-right barnch_color">{{number_format($gtot_bal,3)}}</td>
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
                $("#rep_reward_point_ledger_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



