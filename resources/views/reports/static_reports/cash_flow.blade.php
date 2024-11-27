@extends('layouts.report')
@section('title', 'Cash Flow')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }

        .font{
            font-size:12px ;
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
                <h3 class="kt-invoice__title">
                    @php
                        $page_title = "";
                        if($data['cash_flow'] == "bank")
                        {
                            $page_title = 'Bank Flow';
                        }
                        if($data['cash_flow'] == "cash")
                        {
                            $page_title = $data['page_title'];
                        }
                        if($data['cash_flow'] == "both")
                        {
                            $page_title = "Cash / Bank Flow";
                        }
                    @endphp
                    {{strtoupper($page_title)}}
                </h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;font-size:14px;">Date:</span>
                    <span style="color: #5578eb;font-size:14px;">{{" ".date('d-m-Y', strtotime($data['date_time_from']))." to ". date('d-m-Y', strtotime($data['date_time_to']))." "}}</span>
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;font-size:14px;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;font-size:14px;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if($data['cash_flow'] != "")
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;font-size:14px;">Cash Parameter:</span>
                        <span style="color: #5578eb;font-size:14px;">{{strtoupper($data['cash_flow'])}}</span>
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        @php
     
                        $wherecash = "";
                        $wherebank = "";
                        
                        $whereoutflowcash = "";
                        $whereoutflowbank = "";

                        if($data['cash_flow'] == "cash"){
                            $wherecash = "SELECT 
                                CHART_ACCOUNT_ID,
                                CHART_CODE,
                                CHART_NAME,
                                0 BANK,
                                SUM(VOUCHER_CREDIT) CASH ,
                                BRANCH_ID,
                                BRANCH_NAME
                            FROM
                                VW_ACCO_VOUCHER 
                            WHERE VOUCHER_ID IN 
                                (SELECT 
                                VOUCHER_ID 
                                FROM
                                VW_ACCO_VOUCHER 
                                WHERE (CHART_CODE LIKE '6-01-05-0001%') 
                                AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
                                AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                                AND VOUCHER_DEBIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV')) 
                                AND VOUCHER_CREDIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV') 
                            GROUP BY CHART_CODE,
                            BRANCH_NAME,
                            BRANCH_ID,
                                CHART_NAME,
                                CHART_ACCOUNT_ID ";
                        }
                        if($data['cash_flow'] == "bank"){
                            $wherebank = "SELECT 
                            CHART_ACCOUNT_ID,
                            CHART_CODE,
                            CHART_NAME,
                            SUM(BANK) BANK,
                            SUM(CASH) CASH ,
                            BRANCH_ID,
                            BRANCH_NAME
                        FROM
                            (
                              
                              
                                SELECT 
                                CHART_ACCOUNT_ID,
                                CHART_CODE,
                                CHART_NAME,
                                SUM(VOUCHER_CREDIT) BANK,
                                0 CASH ,
                                BRANCH_ID,
                                BRANCH_NAME
                            FROM
                                VW_ACCO_VOUCHER 
                            WHERE VOUCHER_ID IN 
                                (SELECT 
                                VOUCHER_ID 
                                FROM
                                VW_ACCO_VOUCHER 
                                WHERE (CHART_CODE LIKE '6-01-04-%') 
                                AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
                                AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
                                AND VOUCHER_DEBIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV' , 'RV' )) -- CHANGES 
                                AND VOUCHER_CREDIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV' , 'RV')  -- CHANGES 
                            GROUP BY CHART_CODE,
                                CHART_NAME,
                                CHART_ACCOUNT_ID,
                                BRANCH_ID,
                                BRANCH_NAME 
                                
     UNION ALL 
    SELECT 
    CHART.CHART_ACCOUNT_ID,
    CHART.CHART_CODE,
    CHART.CHART_NAME,
    SUM(VOUCHER_CREDIT) -  SUM(VOUCHER_DEBIT)  ,
    0  CASH,
    VOUCH.BRANCH_ID,
    VOUCH.BRANCH_NAME
     
  FROM
    VW_ACCO_VOUCHER VOUCH , TBL_ACCO_CHART_ACCOUNT  CHART 
  WHERE
     VOUCH.VOUCHER_CONT_ACC_CODE =  CHART.CHART_ACCOUNT_ID(+)
     AND 
 VOUCHER_ID IN 
    (
   
    SELECT DISTINCT 
      VOUCHER_ID 
    FROM
      VW_ACCO_VOUCHER 
WHERE (CHART_CODE LIKE '6-01-04-%') 
AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
AND VOUCHER_DEBIT > 0 
AND  UPPER(VOUCHER_TYPE)   IN ('RV')
                                  
      
      ) 
  
     AND VOUCH.CHART_CODE NOT LIKE '6-01-04-%'
 
  GROUP BY CHART.CHART_CODE,
    CHART.CHART_NAME,
    VOUCH.BRANCH_NAME,
    CHART.CHART_ACCOUNT_ID ,
    VOUCH.BRANCH_ID
     ) GAGA 
                            GROUP BY CHART_ACCOUNT_ID,
                            CHART_CODE,
                            CHART_NAME ,
                            BRANCH_ID,
                            BRANCH_NAME
                            ORDER BY CHART_CODE";
                        }

                        if($data['cash_flow'] == "both"){
                            $wherecash = "SELECT 
                                CHART_ACCOUNT_ID,
                                CHART_CODE,
                                CHART_NAME,
                                0 BANK,
                                SUM(VOUCHER_CREDIT) CASH ,
                                BRANCH_ID,
                                BRANCH_NAME
                            FROM
                                VW_ACCO_VOUCHER 
                            WHERE VOUCHER_ID IN 
                                (SELECT 
                                VOUCHER_ID 
                                FROM
                                VW_ACCO_VOUCHER 
                                WHERE (CHART_CODE LIKE '6-01-05-0001%') 
                                AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
                                AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                                AND VOUCHER_DEBIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV')) 
                                AND VOUCHER_CREDIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV') 
                            GROUP BY CHART_CODE,
                            BRANCH_NAME,
                            BRANCH_ID,
                                CHART_NAME,
                                CHART_ACCOUNT_ID ";

                            $wherebank = "UNION
                            ALL
                            SELECT 
                            CHART_ACCOUNT_ID,
                            CHART_CODE,
                            CHART_NAME,
                            SUM(BANK) BANK,
                            SUM(CASH) CASH ,
                            BRANCH_ID,
                            BRANCH_NAME
                        FROM
                            (
                              
                              
                                SELECT 
                                CHART_ACCOUNT_ID,
                                CHART_CODE,
                                CHART_NAME,
                                SUM(VOUCHER_CREDIT) BANK,
                                0 CASH ,
                                BRANCH_ID,
                                BRANCH_NAME
                            FROM
                                VW_ACCO_VOUCHER 
                            WHERE VOUCHER_ID IN 
                                (SELECT 
                                VOUCHER_ID 
                                FROM
                                VW_ACCO_VOUCHER 
                                WHERE (CHART_CODE LIKE '6-01-04-%') 
                                AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
                                AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
                                AND VOUCHER_DEBIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV' , 'RV' )) -- CHANGES 
                                AND VOUCHER_CREDIT > 0 
                                AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV' , 'RV')  -- CHANGES 
                            GROUP BY CHART_CODE,
                                CHART_NAME,
                                CHART_ACCOUNT_ID,
                                BRANCH_ID,
                                BRANCH_NAME 
                                
     UNION ALL 
    SELECT 
    CHART.CHART_ACCOUNT_ID,
    CHART.CHART_CODE,
    CHART.CHART_NAME,
    SUM(VOUCHER_CREDIT) -  SUM(VOUCHER_DEBIT)  ,
    0  CASH,
    VOUCH.BRANCH_ID,
    VOUCH.BRANCH_NAME
     
  FROM
    VW_ACCO_VOUCHER VOUCH , TBL_ACCO_CHART_ACCOUNT  CHART 
  WHERE
     VOUCH.VOUCHER_CONT_ACC_CODE =  CHART.CHART_ACCOUNT_ID(+)
     AND 
 VOUCHER_ID IN 
    (
   
    SELECT DISTINCT 
      VOUCHER_ID 
    FROM
      VW_ACCO_VOUCHER 
WHERE (CHART_CODE LIKE '6-01-04-%') 
AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
AND VOUCHER_DEBIT > 0 
AND  UPPER(VOUCHER_TYPE)   IN ('RV')
                                  
      
      ) 
  
     AND VOUCH.CHART_CODE NOT LIKE '6-01-04-%'
 
  GROUP BY CHART.CHART_CODE,
    CHART.CHART_NAME,
    VOUCH.BRANCH_NAME,
    CHART.CHART_ACCOUNT_ID ,
    VOUCH.BRANCH_ID
     ) GAGA 
                            GROUP BY CHART_ACCOUNT_ID,
                            CHART_CODE,
                            CHART_NAME ,
                            BRANCH_ID,
                            BRANCH_NAME
                            ORDER BY CHART_CODE";

                        }

                        if($data['cash_flow'] == "bank" || $data['cash_flow'] == 'cash')
                        {
                            $qry = "SELECT 
                                CHART_ACCOUNT_ID,
                                CHART_CODE,
                                CHART_NAME,
                                SUM(BANK) BANK,
                                SUM(CASH) CASH ,
                                BRANCH_ID,
                                BRANCH_NAME
                            FROM
                                (
                                    $wherecash
                                    $wherebank
                                ) GAGA 
                                GROUP BY CHART_ACCOUNT_ID,
                                CHART_CODE,
                                CHART_NAME ,
                                BRANCH_ID,
                                BRANCH_NAME
                                ORDER BY CHART_CODE";
                        }
                        /*
                        if($data['cash_flow'] == "both")
                        {

                        $qry = "SELECT 
  CHART_ACCOUNT_ID,
  CHART_CODE,
  CHART_NAME,
  SUM(BANK) BANK,
  SUM(CASH) CASH,
  BRANCH_ID,
  BRANCH_NAME 
FROM
  (SELECT 
    CHART_ACCOUNT_ID,
    CHART_CODE,
    CHART_NAME,
    0 BANK,
    SUM(VOUCHER_CREDIT) CASH,
    BRANCH_ID,
    BRANCH_NAME 
  FROM
    VW_ACCO_VOUCHER 
  WHERE VOUCHER_ID IN 
    (SELECT 
      VOUCHER_ID 
    FROM
      VW_ACCO_VOUCHER 
    WHERE (CHART_CODE LIKE '6-01-05-0001%') 
    AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 

      AND VOUCHER_DEBIT > 0 
      AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV')) 
    AND VOUCHER_CREDIT > 0 
    AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV') 
  GROUP BY CHART_CODE,
    BRANCH_NAME,
    BRANCH_ID,
    CHART_NAME,
    CHART_ACCOUNT_ID 
  UNION
  ALL 
  SELECT 
    CHART_ACCOUNT_ID,
    CHART_CODE,
    CHART_NAME,
    SUM(BANK) BANK,
    SUM(CASH) CASH,
    BRANCH_ID,
    BRANCH_NAME 
  FROM
    (SELECT 
      CHART_ACCOUNT_ID,
      CHART_CODE,
      CHART_NAME,
      SUM(VOUCHER_CREDIT) BANK,
      0 CASH,
      BRANCH_ID,
      BRANCH_NAME 
    FROM
      VW_ACCO_VOUCHER 
    WHERE VOUCHER_ID IN 
      (SELECT 
        VOUCHER_ID 
      FROM
        VW_ACCO_VOUCHER 
      WHERE (CHART_CODE LIKE '6-01-04-%') 
      AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
 
        AND VOUCHER_DEBIT > 0 
        AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV', 'RV')) -- CHANGES 
      AND VOUCHER_CREDIT > 0 
      AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV', 'RV') -- CHANGES 
    GROUP BY CHART_CODE,
      CHART_NAME,
      CHART_ACCOUNT_ID,
      BRANCH_ID,
      BRANCH_NAME 
    UNION
    ALL 
    SELECT 
      CHART.CHART_ACCOUNT_ID,
      CHART.CHART_CODE,
      CHART.CHART_NAME,
      SUM(VOUCHER_CREDIT) - SUM(VOUCHER_DEBIT),
      0 CASH,
      VOUCH.BRANCH_ID,
      VOUCH.BRANCH_NAME 
    FROM
      VW_ACCO_VOUCHER VOUCH,
      TBL_ACCO_CHART_ACCOUNT CHART 
    WHERE VOUCH.VOUCHER_CONT_ACC_CODE = CHART.CHART_ACCOUNT_ID (+) 
      AND VOUCHER_ID IN 
      (SELECT DISTINCT 
        VOUCHER_ID 
      FROM
        VW_ACCO_VOUCHER 
      WHERE (CHART_CODE LIKE '6-01-04-%') 
      AND BRANCH_ID in (".implode(",",$data['branch_ids']).")
AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 

        AND VOUCHER_DEBIT > 0 
        AND UPPER(VOUCHER_TYPE) IN ('RV')) 
      AND VOUCH.CHART_CODE NOT LIKE '6-01-04-%' 
    GROUP BY CHART.CHART_CODE,
      CHART.CHART_NAME,
      VOUCH.BRANCH_NAME,
      CHART.CHART_ACCOUNT_ID,
      VOUCH.BRANCH_ID) GAGA 
  GROUP BY CHART_ACCOUNT_ID,
    CHART_CODE,
    CHART_NAME,
    BRANCH_ID,
    BRANCH_NAME 
  ORDER BY CHART_CODE) GAGA 
GROUP BY CHART_ACCOUNT_ID,
  CHART_CODE,
  CHART_NAME,
  BRANCH_ID,
  BRANCH_NAME 
ORDER BY CHART_CODE";
                        }
                        */

//dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);

                        $list = [];
                        foreach ($getdata as $list_row){
                            $list[$list_row->branch_id][] = $list_row;
                        }

                        if($data['cash_flow'] == "bank")
                        {
                            $where_acc_code = " AND CHART_CODE LIKE '6-01-04-%'";
                        }

                        if($data['cash_flow'] == 'cash')
                        {
                            $where_acc_code = " AND CHART_CODE LIKE '6-01-05-0001%'";
                        }

                        if($data['cash_flow'] == 'both')
                        {
                            $where_acc_code = "AND CHART_CODE LIKE '6-01-04-%' AND CHART_CODE LIKE '6-01-05-0001%'";
                        }

                    @endphp
                    <div class="kt-portlet__body">
                        <div class="row row-block">
                            <div class="col-lg-12">
                                <table width="100%" id="rep_cash_flow_datatable" class="static_report_table table bt-datatable table-bordered">
                                <tr class="sticky-header">
                                        <th width="12%" class="text-center">COA Code</th>
                                        <th width="50%" class="text-left">COA</th>
                                        <th width="10%" class="text-center">Bank</th>
                                        <th width="10%" class="text-center">Cash</th>
                                        <th width="6%" class="text-center">Share%</th>
                                        <th width="12%" class="text-center">Amount</th>
                                    </tr>
                                    @foreach($list as $branch_key=>$branch_name)

                                        @php
                                            $bran_name= \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->where('branch_id',$branch_key)->get('branch_name')->first();
                                            $query = "Select distinct
                                                (sum(voucher_debit) - sum(voucher_credit)) as BAL 
                                            from 
                                                vw_acco_voucher 
                                            where  branch_id = $branch_key  
                                                AND (voucher_date < to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI')) 
                                                $where_acc_code
                                                
                                            order by voucher_date,voucher_no";

                                            $opening_balance = DB::selectOne($query);
                                            /*
                                            $paras = [
                                                'chart_account_id' => '22282122291903',
                                                'voucher_date' => date('Y-m-d', strtotime($data['date_time_from'])),
                                                'branch_ids' => $branch_key,
                                            ];
                                            $data['opening_balance'] = App\Library\CoreFunc::cash_flow_acco_opening_bal($paras);
                                            */
                                            if($opening_balance->bal == null){
                                                $opening_balc =  0;
                                            }else{
                                                $opening_balc =  $opening_balance->bal;
                                            }
                                        @endphp

                                        <tr class="outer_total">
                                            <td colspan="9"><b>{{ucwords(strtolower($bran_name->branch_name))}}</b></td>
                                        </tr>
                                        <tr class="outer_total">
                                            <td colspan="4">&nbsp;</td>
                                            <td colspan="5" class="text-right">
                                                <b>
                                                    Opening Amount &nbsp;&nbsp;&nbsp;
                                                    @if($opening_balc > 0)
                                                        {{number_format($opening_balc,3).' DR'}}
                                                    @else
                                                        {{number_format($opening_balc * (-1),3).' CR'}}
                                                    @endif
                                                </b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9">
                                                <span style="color: blue !important;font-size:14px;"><b>In Flow</b></span>
                                            </td>
                                        </tr>
                                        @php
                                            $ki = 1;
                                            $totcash = 0;
                                            $totbank = 0;
                                            $totamount = 0;
                                            $close_bal=0;
                                        @endphp
                                        @foreach($branch_name as $i_key=>$item)
                                            @php
                                                $amount = $item->bank + $item->cash;

                                                $totbank = $totbank + $item->bank;
                                                $totcash = $totcash + $item->cash;
                                                $totamount = $totamount + $amount;
                                            @endphp
                                                    <tr>
                                                        <td class="font text-center">{{$item->chart_code}}</td>
                                                        <td class="font text-left">{{$item->chart_name}}</td>
                                                        <td class="font text-right">{{number_format($item->bank)}}</td>
                                                        <td class="font text-right">{{number_format($item->cash)}}</td>
                                                        <td class="font text-center"></td>
                                                        <td class="font text-right">{{number_format($amount)}}</td>
                                                    </tr>
                                                @php
                                                    $ki += 1;
                                                @endphp
                                        @endforeach
                                            <tr>
                                                <td colspan="2" class="font text-right"><strong> Total In Flow: </strong></td>
                                                <td class="font text-right"><strong>{{number_format($totbank,0)}}</strong></td>
                                                <td class="font text-right"><strong>{{number_format($totcash,0)}}</strong></td>
                                                <td class="font text-center"></td>
                                                <td class="font text-right"><strong>{{number_format($totamount,3)}}</strong></td>
                                            </tr>
                                            
                                            <!--======================-->
                                            <!--====== END IN-FLOW ===-->
                                            <!--======================-->

                                            <!--==================== Start OUT Flow =====-->


                                            @php
                                                if($data['cash_flow'] == "cash")
                                                {
                                                    //====================
                                                    //======= CASH OUT FLOW
                                                    //====================

                                                    $whereoutflowcash ="SELECT 
                                                        CHART_ACCOUNT_ID,
                                                        CHART_CODE,
                                                        CHART_NAME,
                                                        SUM(BANK) BANK,
                                                        SUM(CASH) CASH,
                                                        BRANCH_NAME 
                                                        FROM
                                                        (SELECT 
                                                            CHART_ACCOUNT_ID,
                                                            CHART_CODE,
                                                            CHART_NAME,
                                                            0 BANK,
                                                            SUM(VOUCHER_DEBIT) CASH,
                                                            BRANCH_NAME 
                                                        FROM
                                                            VW_ACCO_VOUCHER 
                                                        WHERE VOUCHER_ID IN 
                                                            (SELECT DISTINCT 
                                                            VOUCHER_ID 
                                                            FROM
                                                            VW_ACCO_VOUCHER 
                                                            WHERE (CHART_CODE LIKE '6-01-05-0001%') 
                                                            AND BRANCH_ID = '".$branch_key."' 
                                                            AND (voucher_date BETWEEN to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
                                                            AND VOUCHER_CREDIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV', 'PV')) 
                                                            AND VOUCHER_DEBIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV', 'PV') 
                                                        GROUP BY CHART_CODE,
                                                            CHART_NAME,
                                                            BRANCH_NAME,
                                                            CHART_ACCOUNT_ID 
                                                        UNION
                                                        ALL 
                                                        SELECT 
                                                            CHART.CHART_ACCOUNT_ID,
                                                            CHART.CHART_CODE,
                                                            CHART.CHART_NAME,
                                                            0 BANK,
                                                            SUM(VOUCHER_CREDIT) CASH,
                                                            VOUCH.BRANCH_NAME 
                                                        FROM
                                                            VW_ACCO_VOUCHER VOUCH,
                                                            TBL_ACCO_CHART_ACCOUNT CHART 
                                                        WHERE VOUCH.VOUCHER_CONT_ACC_CODE = CHART.CHART_ACCOUNT_ID (+) 
                                                            AND VOUCHER_ID IN 
                                                            (SELECT DISTINCT 
                                                            VOUCHER_ID 
                                                            FROM
                                                            VW_ACCO_VOUCHER 
                                                            WHERE (CHART_CODE LIKE '6-01-05-0001%') 
                                                            AND BRANCH_ID = '".$branch_key."' 
                                                            AND (voucher_date BETWEEN to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
                                                            AND VOUCHER_CREDIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) IN ('PV') 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV')) 
                                                            AND VOUCHER_CREDIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV') 
                                                            AND UPPER(VOUCHER_TYPE) IN ('PV') 
                                                            AND VOUCH.CHART_CODE LIKE '6-01-05-%' 
                                                        GROUP BY CHART.CHART_CODE,
                                                            CHART.CHART_NAME,
                                                            VOUCH.BRANCH_NAME,
                                                            CHART.CHART_ACCOUNT_ID 
                                                        ) GAGA 
                                                        GROUP BY CHART_ACCOUNT_ID,
                                                        CHART_CODE,
                                                        CHART_NAME,
                                                        BRANCH_NAME 
                                                        ORDER BY CHART_CODE ";
                                                }


                                                if($data['cash_flow'] == "bank")
                                                {
                                                    //====================
                                                    //======= BANK OUT FLOW
                                                    //====================

                                                    $whereoutflowbank ="SELECT 
                    CHART_ACCOUNT_ID,
                    CHART_CODE,
                    CHART_NAME,
                    SUM(BANK) BANK,
                    SUM(CASH) CASH ,
                    BRANCH_NAME
                FROM
                    (
                        
                        SELECT 
                            CHART_ACCOUNT_ID,
                            CHART_CODE,
                            CHART_NAME,
                            SUM(VOUCHER_DEBIT) BANK,
                            0 CASH ,
                            BRANCH_NAME
                        FROM
                            VW_ACCO_VOUCHER 
                        WHERE VOUCHER_ID IN 
                            (SELECT DISTINCT
                            VOUCHER_ID 
                            FROM
                            VW_ACCO_VOUCHER 
                            WHERE (CHART_CODE LIKE '6-01-04-%') AND BRANCH_ID = '".$branch_key."' 
                            AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
                            AND VOUCHER_CREDIT > 0 
                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV','PV')) 
                            AND VOUCHER_DEBIT > 0 
                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV','PV') 
                        GROUP BY CHART_CODE,
                            CHART_NAME,
                            BRANCH_NAME,
                            CHART_ACCOUNT_ID
                            
                            
                              union all 
     -- ================ for purchase voucher
     
       -- ==== 
     
     SELECT 
     CHART.CHART_ACCOUNT_ID,
     CHART.CHART_CODE,
     CHART.CHART_NAME,
     SUM(VOUCHER_CREDIT)  BANK,
     0  CASH,
     VOUCH.BRANCH_NAME 
     
  FROM
    VW_ACCO_VOUCHER VOUCH , TBL_ACCO_CHART_ACCOUNT  CHART 
  WHERE
     VOUCH.VOUCHER_CONT_ACC_CODE =  CHART.CHART_ACCOUNT_ID(+)
     AND 
 VOUCHER_ID IN 
    (
    
    SELECT DISTINCT 
      VOUCHER_ID 
    FROM
      VW_ACCO_VOUCHER 
    WHERE (CHART_CODE LIKE '6-01-04-%') 
      AND BRANCH_ID = '".$branch_key."' 
      AND (voucher_date BETWEEN to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
      AND VOUCHER_CREDIT > 0 
      AND UPPER(VOUCHER_TYPE) IN ('PV')
      AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV'  )
      
      ) 
    AND VOUCHER_CREDIT > 0 
    AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV' ) 
    AND UPPER(VOUCHER_TYPE)   IN ('PV') 
      AND VOUCH.CHART_CODE LIKE '6-01-04-%'
 
  GROUP BY CHART.CHART_CODE,
    CHART.CHART_NAME,
    VOUCH.BRANCH_NAME,
    CHART.CHART_ACCOUNT_ID 
    ) GAGA 
                    GROUP BY CHART_ACCOUNT_ID,
                    CHART_CODE,
                    CHART_NAME ,
                    BRANCH_NAME
                    ORDER BY CHART_CODE";
                                                }
/*
                                                if($data['cash_flow'] == "both")
                                                {

                                                    //====================
                                                    //======= BOTH OUT FLOW
                                                    //====================

                                                    $whereoutflowcash ="SELECT 
                                                        CHART_ACCOUNT_ID,
                                                        CHART_CODE,
                                                        CHART_NAME,
                                                        SUM(BANK) BANK,
                                                        SUM(CASH) CASH,
                                                        BRANCH_NAME 
                                                        FROM
                                                        (SELECT 
                                                            CHART_ACCOUNT_ID,
                                                            CHART_CODE,
                                                            CHART_NAME,
                                                            0 BANK,
                                                            SUM(VOUCHER_DEBIT) CASH,
                                                            BRANCH_NAME 
                                                        FROM
                                                            VW_ACCO_VOUCHER 
                                                        WHERE VOUCHER_ID IN 
                                                            (SELECT DISTINCT 
                                                            VOUCHER_ID 
                                                            FROM
                                                            VW_ACCO_VOUCHER 
                                                            WHERE (CHART_CODE LIKE '6-01-05-0001%') 
                                                            AND BRANCH_ID = '".$branch_key."' 
                                                            AND (voucher_date BETWEEN to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
                                                            AND VOUCHER_CREDIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV', 'PV')) 
                                                            AND VOUCHER_DEBIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV', 'PV') 
                                                        GROUP BY CHART_CODE,
                                                            CHART_NAME,
                                                            BRANCH_NAME,
                                                            CHART_ACCOUNT_ID 
                                                        UNION
                                                        ALL 
                                                        SELECT 
                                                            CHART.CHART_ACCOUNT_ID,
                                                            CHART.CHART_CODE,
                                                            CHART.CHART_NAME,
                                                            0 BANK,
                                                            SUM(VOUCHER_CREDIT) CASH,
                                                            VOUCH.BRANCH_NAME 
                                                        FROM
                                                            VW_ACCO_VOUCHER VOUCH,
                                                            TBL_ACCO_CHART_ACCOUNT CHART 
                                                        WHERE VOUCH.VOUCHER_CONT_ACC_CODE = CHART.CHART_ACCOUNT_ID (+) 
                                                            AND VOUCHER_ID IN 
                                                            (SELECT DISTINCT 
                                                            VOUCHER_ID 
                                                            FROM
                                                            VW_ACCO_VOUCHER 
                                                            WHERE (CHART_CODE LIKE '6-01-05-0001%') 
                                                            AND BRANCH_ID = '".$branch_key."' 
                                                            AND (voucher_date BETWEEN to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI')) 
                                                            AND VOUCHER_CREDIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) IN ('PV') 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV')) 
                                                            AND VOUCHER_CREDIT > 0 
                                                            AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV') 
                                                            AND UPPER(VOUCHER_TYPE) IN ('PV') 
                                                            AND VOUCH.CHART_CODE LIKE '6-01-05-%' 
                                                        GROUP BY CHART.CHART_CODE,
                                                            CHART.CHART_NAME,
                                                            VOUCH.BRANCH_NAME,
                                                            CHART.CHART_ACCOUNT_ID 
                                                        ) GAGA 
                                                        GROUP BY CHART_ACCOUNT_ID,
                                                        CHART_CODE,
                                                        CHART_NAME,
                                                        BRANCH_NAME 
                                                        ORDER BY CHART_CODE ";
                                                        
                                                    $whereoutflowbank ="UNION
                                                        ALL 
                                                            SELECT 
                                                        CHART_ACCOUNT_ID,
                                                        CHART_CODE,
                                                        CHART_NAME,
                                                        SUM(VOUCHER_DEBIT) BANK,
                                                        0 CASH ,
                                                        BRANCH_NAME
                                                    FROM
                                                        VW_ACCO_VOUCHER 
                                                    WHERE VOUCHER_ID IN 
                                                        (SELECT DISTINCT
                                                        VOUCHER_ID 
                                                        FROM
                                                        VW_ACCO_VOUCHER 
                                                        WHERE (CHART_CODE LIKE '6-01-04-%') 
                                                        AND BRANCH_ID = '".$branch_key."'
                                                        AND (voucher_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                                                        AND VOUCHER_CREDIT > 0 
                                                        AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV')) 
                                                        AND VOUCHER_DEBIT > 0 
                                                        AND UPPER(VOUCHER_TYPE) NOT IN ('SIV', 'SRV') 
                                                    GROUP BY CHART_CODE,
                                                        CHART_NAME,
                                                        BRANCH_NAME,
                                                        CHART_ACCOUNT_ID";
                                                }
                                                */
                                            @endphp

                                            <tr>
                                                <td colspan="9">
                                                    <span style="color: red !important;font-size:14px;"><b>Out Flow</b></span>
                                                </td>
                                            </tr>
                                            @php
                                            
                                            $outflowquery = "SELECT 
                                                CHART_ACCOUNT_ID,
                                                CHART_CODE,
                                                CHART_NAME,
                                                SUM(BANK) BANK,
                                                SUM(CASH) CASH ,
                                                BRANCH_NAME
                                            FROM
                                                (
                                                    $whereoutflowcash
                                                    $whereoutflowbank
                                                 
                                                ) KAKA 
                                                GROUP BY CHART_ACCOUNT_ID,
                                                CHART_CODE,
                                                CHART_NAME ,
                                                BRANCH_NAME
                                                ORDER BY CHART_CODE ";
                   // dd($outflowquery);
                                            
                                            $getoutflowdata = \Illuminate\Support\Facades\DB::select($outflowquery);
                                            //dd($getdata);

                                            $outlist = [];
                                            foreach ($getoutflowdata as $row){
                                                $outlist[$row->chart_account_id]= $row;
                                            }

                                                $kki = 1;
                                                $totoutflowcash = 0;
                                                $totoutflowbank = 0;
                                                $totoutflowamount = 0;
                                            @endphp
                                            @foreach($outlist as $outitem)
                                                @php
                                                    $outamount = $outitem->bank + $outitem->cash;

                                                    $totoutflowbank = $totoutflowbank + $outitem->bank;
                                                    $totoutflowcash = $totoutflowcash + $outitem->cash;
                                                    $totoutflowamount = $totoutflowamount + $outamount;
                                                @endphp
                                                        <tr>
                                                            <td class="font text-center">{{$outitem->chart_code}}</td>
                                                            <td class="font text-left">{{$outitem->chart_name}}</td>
                                                            <td class="font text-right">{{number_format($outitem->bank)}}</td>
                                                            <td class="font text-right">{{number_format($outitem->cash)}}</td>
                                                            <td class="font text-center"></td>
                                                            <td class="font text-right">{{number_format($outamount)}}</td>
                                                        </tr>
                                                    @php
                                                        $kki += 1;
                                                    @endphp
                                            @endforeach
                                                <tr>
                                                    <td colspan="2" class="font text-right"><strong> Total Out Flow: </strong></td>
                                                    <td class="font text-right"><strong>{{number_format($totoutflowbank,0)}}</strong></td>
                                                    <td class="font text-right"><strong>{{number_format($totoutflowcash,0)}}</strong></td>
                                                    <td class="font text-center"></td>
                                                    <td class="font text-right"><strong>{{number_format($totoutflowamount,3)}}</strong></td>
                                                </tr>
                                                <tr class="outer_total">
                                                    <td colspan="4"></td>
                                                    <td colspan="5" class="text-right">
                                                        <b>
                                                            @php
                                                            $close_bal = $opening_balc + $totamount - $totoutflowamount;
                                                            @endphp
                                                            Closing Amount &nbsp;&nbsp;&nbsp;
                                                            @if($close_bal > 0)
                                                                {{number_format($close_bal,3).' DR'}}
                                                            @else
                                                                {{number_format($close_bal * (-1),3).' CR'}}
                                                            @endif
                                                        </b>
                                                    </td>
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
                $("#rep_cash_flow_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



