@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        tr.level_1>td:first-child,
        tr.level_1>td:nth-child(2){
            font-size: 15px;
        }
        tr.level_1>td{
            font-weight: 500 !important;
        }
        tr.level_2>td:first-child,
        tr.level_2>td:nth-child(2){
            font-size: 13px;
        }
        tr.level_2>td.name{
            padding-left: 15px !important;
        }
        tr.level_2>td{
            font-weight: 500 !important;
        }
        tr.level_3>td:first-child,
        tr.level_3>td:nth-child(2){
            font-size: 12px;
        }
        tr.level_3>td:first-child{
            padding-left: 20px !important;
        }
        tr.level_3>td.name{
            padding-left: 30px !important;
        }
        tr.level_3>td{
            font-weight: 500 !important;
        }
        tr.level_4>td:first-child{
            padding-left: 30px !important;
        }
        tr.level_4>td.name{
            padding-left: 45px !important;
        }
        tr.level_4>td {

        }
        td.right_number {
            text-align: right;
        }
        .acc_heading {
            color: #e27d00;
            font-size: 16px;
            font-weight: 400;
            display: block;
            margin-bottom: 7px;
        }
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
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
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
        @php
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $where = '';
            $where .= "(v.voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd'))";
            $where .= " and v.business_id = ".auth()->user()->business_id." and v.branch_id in (".implode(",",$data['branch_ids']).")";
            if (!empty($data['chart_account_id'])) {
                $where .= " and v.chart_account_id in (204,32142,209)";
            }

            $query = "SELECT  ca.CHART_ACCOUNT_ID ,  SUBSTR(vouch.CHART_CODE,0,1)  CHART_CODE_GROUP , vouch.CHART_CODE ,ca.CHART_NAME ,vouch.CHART_LEVEL , BALANCE_LEVEL1  ,  BALANCE_LEVEL2 , BALANCE_LEVEL3  ,   BALANCE_LEVEL4 ,   BALANCE_LEVEL_PER
                        FROM  (
                            SELECT  4  CHART_LEVEL ,  G.CHART_CODE,  0 BALANCE_LEVEL1  , 0 BALANCE_LEVEL2 , 0 BALANCE_LEVEL3  ,G.Balance_level4 AS BALANCE_LEVEL4 , 0 as BALANCE_LEVEL_PER
                            FROM  ( SELECT M.CHART_CODE, M.OPEN_BAL,M.DR_BALANCE,M.CR_BALANCE,M.PERIOD_BAL as Balance_level4  ,(substr (M.CHART_CODE, 0, 1) || '-00-00-0000')     AS GRP_CODE,(substr (M.CHART_CODE, 0, 4) || '-00-0000')   AS SGRP_CODE ,(substr (M.CHART_CODE, 0, 7) || '-0000')  AS SSGRP_CODE
                            FROM  ( SELECT  D.business_id,D.company_id ,D.CHART_CODE  , SUM(D.OPEN_BAL) AS OPEN_BAL,SUM(D.DR_BALANCE) AS DR_BALANCE,SUM(D.CR_BALANCE) AS CR_BALANCE,(SUM(D.DR_BALANCE)-SUM(CR_BALANCE)) AS PERIOD_BAL
                            FROM (  select distinct   v.business_id,v.company_id ,coa.chart_code,  0 OPEN_BAL ,
                                sum (v.voucher_debit) over (partition by v.chart_account_id,v.business_id,v.company_id)  AS DR_BALANCE,
                                sum (v.voucher_credit) over (partition by v.chart_account_id,v.business_id,v.company_id) AS CR_BALANCE
                                from tbl_acco_voucher v, tbl_acco_chart_account coa
                                where v.chart_account_id = coa.chart_account_id  and $where
                            ) D GROUP BY D.chart_code ,   D.business_id,D.company_id
                            ) M
                            ) G
                        UNION ALL
                            SELECT 1  CHART_LEVEL ,  G.GRP_CODE,  SUM(G.PERIOD_BAL)  AS BALANCE_LEVEL1  , 0 BALANCE_LEVEL2 , 0 BALANCE_LEVEL3  ,0  BALANCE_LEVEL4 , 0 as BALANCE_LEVEL_PER
                            FROM  ( SELECT M.CHART_CODE, M.OPEN_BAL,M.DR_BALANCE,M.CR_BALANCE,M.PERIOD_BAL,(substr (M.CHART_CODE, 0, 1) || '-00-00-0000')     AS GRP_CODE,(substr (M.CHART_CODE, 0, 4) || '-00-0000')   AS SGRP_CODE ,(substr (M.CHART_CODE, 0, 7) || '-0000')  AS SSGRP_CODE
                            FROM ( SELECT  D.business_id,D.company_id ,D.CHART_CODE  , SUM(D.OPEN_BAL) AS OPEN_BAL,SUM(D.DR_BALANCE) AS DR_BALANCE,SUM(D.CR_BALANCE) AS CR_BALANCE,(SUM(D.DR_BALANCE)-SUM(CR_BALANCE)) AS PERIOD_BAL
                            FROM ( select distinct   v.business_id,v.company_id ,coa.chart_code,  0 OPEN_BAL ,
                                sum (v.voucher_debit) over (partition by v.chart_account_id,v.business_id,v.company_id)  AS DR_BALANCE,
                                sum (v.voucher_credit) over (partition by v.chart_account_id,v.business_id,v.company_id) AS CR_BALANCE
                                from tbl_acco_voucher v, tbl_acco_chart_account coa
                                where v.chart_account_id = coa.chart_account_id  and $where
                            ) D GROUP BY D.chart_code ,   D.business_id,D.company_id
                            ) M
                            ) G  GROUP BY  G.GRP_CODE
                        UNION ALL
                            SELECT 2  CHART_LEVEL ,  G.SGRP_CODE,  0  AS BALANCE_LEVEL1  , SUM(G.PERIOD_BAL) BALANCE_LEVEL2 , 0 BALANCE_LEVEL3  ,0  BALANCE_LEVEL4 , 0 as BALANCE_LEVEL_PER
                            FROM  ( SELECT M.CHART_CODE, M.OPEN_BAL,M.DR_BALANCE,M.CR_BALANCE,M.PERIOD_BAL,(substr (M.CHART_CODE, 0, 1) || '-00-00-0000')     AS GRP_CODE,(substr (M.CHART_CODE, 0, 4) || '-00-0000')   AS SGRP_CODE ,(substr (M.CHART_CODE, 0, 7) || '-0000')  AS SSGRP_CODE
                            FROM  ( SELECT  D.business_id,D.company_id ,D.CHART_CODE  , SUM(D.OPEN_BAL) AS OPEN_BAL,SUM(D.DR_BALANCE) AS DR_BALANCE,SUM(D.CR_BALANCE) AS CR_BALANCE,(SUM(D.DR_BALANCE)-SUM(CR_BALANCE)) AS PERIOD_BAL
                            FROM ( select distinct   v.business_id,v.company_id ,coa.chart_code,  0 OPEN_BAL ,
                                sum (v.voucher_debit) over (partition by v.chart_account_id,v.business_id,v.company_id)  AS DR_BALANCE,
                                sum (v.voucher_credit) over (partition by v.chart_account_id,v.business_id,v.company_id) AS CR_BALANCE
                                from tbl_acco_voucher v, tbl_acco_chart_account coa
                                where v.chart_account_id = coa.chart_account_id  and $where
                            ) D GROUP BY D.chart_code ,   D.business_id,D.company_id
                            ) M
                            ) G  GROUP BY  G.SGRP_CODE
                        UNION ALL
                            SELECT 3  CHART_LEVEL ,  G.SSGRP_CODE,  0  AS BALANCE_LEVEL1  , 0 BALANCE_LEVEL2 , SUM(G.PERIOD_BAL)  BALANCE_LEVEL3  ,0  BALANCE_LEVEL4 , 0 as BALANCE_LEVEL_PER  FROM
                            ( SELECT M.CHART_CODE, M.OPEN_BAL,M.DR_BALANCE,M.CR_BALANCE,M.PERIOD_BAL,(substr (M.CHART_CODE, 0, 1) || '-00-00-0000')     AS GRP_CODE,(substr (M.CHART_CODE, 0, 4) || '-00-0000')   AS SGRP_CODE ,(substr (M.CHART_CODE, 0, 7) || '-0000')  AS SSGRP_CODE FROM
                            ( SELECT  D.business_id,D.company_id ,D.CHART_CODE  , SUM(D.OPEN_BAL) AS OPEN_BAL,SUM(D.DR_BALANCE) AS DR_BALANCE,SUM(D.CR_BALANCE) AS CR_BALANCE,(SUM(D.DR_BALANCE)-SUM(CR_BALANCE)) AS PERIOD_BAL FROM
                            ( select distinct   v.business_id,v.company_id ,coa.chart_code,  0 OPEN_BAL ,
                                sum (v.voucher_debit) over (partition by v.chart_account_id,v.business_id,v.company_id)  AS DR_BALANCE,
                                sum (v.voucher_credit) over (partition by v.chart_account_id,v.business_id,v.company_id) AS CR_BALANCE
                                from tbl_acco_voucher v, tbl_acco_chart_account coa
                                where v.chart_account_id = coa.chart_account_id  and $where
                            ) D GROUP BY D.chart_code ,   D.business_id,D.company_id
                            ) M
                            ) G  GROUP BY  G.SSGRP_CODE
                        ) vouch, tbl_acco_chart_account ca where  vouch.chart_code = ca.chart_code(+) ORDER BY vouch.CHART_CODE";

                    $acc_data = \Illuminate\Support\Facades\DB::select($query);

                    $query2 = "SELECT  SUM(COST_VALUE) COST_VALUE  FROM
                            (
                            SELECT SUM( (TO_NUMBER( QTY_BASE_UNIT ) *  TO_NUMBER(COST_RATE))) COST_VALUE FROM  VW_SALE_SALES_INVOICE
                            WHERE   (UPPER(SALES_TYPE) = 'POS'  OR UPPER(SALES_TYPE) = 'RPOS' OR  UPPER(SALES_TYPE) = 'SI'   OR  UPPER(SALES_TYPE) = 'SR') AND
                            BRANCH_ID IN (1)
                            AND SALES_DATE BETWEEN to_date('".$from_date."','yyyy/mm/dd') AND to_date('".$to_date."','yyyy/mm/dd')

                            UNION ALL

                            SELECT SUM( (STOCK_DTL_QTY_BASE_UNIT  *  COST_RATE))  COST_VALUE    FROM VW_INVE_STOCK  WHERE
                                (UPPER(STOCK_CODE_TYPE) = 'ST'  OR UPPER(STOCK_CODE_TYPE) = 'DI' OR  UPPER(STOCK_CODE_TYPE) = 'SP' )
                            AND  BRANCH_ID IN (1)
                            AND STOCK_DATE  BETWEEN to_date('".$from_date."','yyyy/mm/dd') AND to_date('".$to_date."','yyyy/mm/dd')
                            ) ABC";
                    $cost_value = \Illuminate\Support\Facades\DB::select($query2);
                    $cost_value = floatval($cost_value[0]->cost_value);
                    $accounts = [];
                    foreach($acc_data as $acc){
                        if('7-01' == substr($acc->chart_code, 0, 4)){
                            $accounts['sales'][] =  $acc;
                        }
                        if('8' == substr($acc->chart_code, 0, 1)){
                            if('8-01-01' != substr($acc->chart_code, 0, 7)){
                                $accounts['cost_of_sales'][] =  $acc;
                            }
                        }
                        if('7' == substr($acc->chart_code, 0, 1) && '7-01' != substr($acc->chart_code, 0, 4)){
                            $accounts['other_income'][] =  $acc;
                        }
                        if('9' == substr($acc->chart_code, 0, 1)){
                            $accounts['exp'][] =  $acc;
                        }
                    }
                    $sub_total_sales = 0;
                    $sub_total_cost_of_sales = 0;
                    $sub_total_other_income = 0;
                    $sub_total_exp = 0;
                    $gross_profit = 0;
                    //dd($accounts['sales']);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <span class="acc_heading">Sales:</span>
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="30%" class="text-left">Account Code</th>
                            <th width="30%" class="text-left">Account Name</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Detail</th>
                        </tr>
                        @if(isset($accounts['sales']))
                            @foreach($accounts['sales'] as $acc_7)
                                @php
                              //  dd($acc_7);
                                    if($acc_7->chart_level == 1){$class = 'level_1';}
                                    if($acc_7->chart_level == 2){$class = 'level_2';}
                                    if($acc_7->chart_level == 3){$class = 'level_3';}
                                    if($acc_7->chart_level == 4){$class = 'level_4';}
                                @endphp
                                @if($acc_7->balance_level1 != 0 || $acc_7->balance_level2 != 0 || $acc_7->balance_level3 != 0 || $acc_7->balance_level4 != 0)
                                    <tr class="{{$class}}">
                                        <td><span class="acc_ledger_report" data-id="{{$acc_7->chart_account_id}}" data-type="{{--{{$list->voucher_type}}--}}">{{$acc_7->chart_code}}</span></td>
                                        <td class="name">{{$acc_7->chart_name}}</td>
                                        <td class="text-right">{{($acc_7->balance_level1 != 0)?number_format(-1*$acc_7->balance_level1,3):""}}</td>
                                        <td class="text-right">{{($acc_7->balance_level2 != 0)?number_format(-1*$acc_7->balance_level2,3):""}}</td>
                                        <td class="text-right">{{($acc_7->balance_level3 != 0)?number_format(-1*$acc_7->balance_level3,3):""}}</td>
                                        <td class="text-right">{{($acc_7->balance_level4 != 0)?number_format(-1*$acc_7->balance_level4,3):""}}</td>
                                    </tr>
                                @endif
                                @php
                                    $sub_total_sales +=  -1*$acc_7->balance_level4;
                                @endphp
                            @endforeach
                        @endif
                        <tr class="sub_total">
                            <td class="rep-font-bold" colspan="2">Total:</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($sub_total_sales,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <span class="acc_heading">Cost of Goods:</span>
                    @php
$opening_qry = "select
  sum(OPEN_QTY * T2.OP_AVG_RATE) opening
FROM
  (
    select
      inner_qry.PRODUCT_ID,
      sum(OPEN_QTY) OPEN_QTY,
      sum(OPEN_AMOUNT) AS OPEN_AMOUNT
    FROM
      (
        select
          PRODUCT_ID,
          SUM(QTY_BASE_UNIT_VALUE) OPEN_QTY,
          SUM(QTY_BASE_UNIT_VALUE * COST_RATE) OPEN_AMOUNT
        FROM
          VW_PURC_STOCK_DTL
        WHERE
          BRANCH_ID IN (".implode(",",$data['branch_ids']).")
          AND trunc(DOCUMENT_DATE) < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
          AND (
            STOCK_CALCULATION_EFFECT = '+'
            OR STOCK_CALCULATION_EFFECT = '-'
          )
        GROUP BY
          PRODUCT_ID
      ) inner_qry
    group by
      inner_qry.PRODUCT_ID
  ) T1
  LEFT JOIN (
    select
      product_id,
      CASE WHEN sum(op_qty) <> 0 THEN (
        sum(op_amount) / sum(op_qty)
      ) ELSE 0 END OP_AVG_RATE,
      CASE WHEN sum(cl_qty) <> 0 THEN (
        sum(cl_amount) / sum(cl_qty)
      ) ELSE 0 END CL_AVG_RATE
    FROM
      (
        select
          product_id,
          (
            sum(TBL_PURC_GRN_DTL_AMOUNT) - sum(TBL_PURC_GRN_DTL_DISC_AMOUNT)
          ) OP_AMOUNT,
          sum(qty_base_unit) OP_QTY,
          0 CL_AMOUNT,
          0 CL_QTY
        from
          TBL_PURC_GRN_DTL A
          inner join TBL_PURC_GRN B ON A.GRN_ID = B.GRN_ID
        WHERE
          B.grn_date < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
          AND B.branch_id in (".implode(",",$data['branch_ids']).")
          AND UPPER(B.GRN_TYPE) = 'GRN'
        group by
          product_id
        UNION ALL
        select
          c.product_id,
          sum(STOCK_DTL_AMOUNT) OP_AMOUNT,
          sum(STOCK_DTL_QTY_BASE_UNIT) OP_QTY,
          0 CL_AMOUNT,
          0 CL_QTY
        from
          TBL_INVE_STOCK_DTL C
          inner join TBL_INVE_STOCK D ON C.STOCK_ID = D.STOCK_ID
        WHERE
          D.STOCK_DATE < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
          AND D.branch_id in (".implode(",",$data['branch_ids']).")
          AND (
            UPPER(D.STOCK_CODE_TYPE) = 'STR'
            OR UPPER(D.STOCK_CODE_TYPE) = 'OS'
          )
        group by
          c.product_id
        UNION ALL
        select
          product_id,
          0 OP_AMOUNT,
          0 OP_QTY,
          (
            sum(TBL_PURC_GRN_DTL_AMOUNT) - sum(TBL_PURC_GRN_DTL_DISC_AMOUNT)
          ) CL_AMOUNT,
          sum(qty_base_unit) CL_QTY
        from
          TBL_PURC_GRN_DTL A
          inner join TBL_PURC_GRN B ON A.GRN_ID = B.GRN_ID
        WHERE
          B.grn_date <= TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
          AND B.branch_id in (".implode(",",$data['branch_ids']).")
          AND UPPER(B.GRN_TYPE) = 'GRN'
        group by
          product_id
        UNION ALL
        select
          c.product_id,
          0 OP_AMOUNT,
          0 OP_QTY,
          sum(STOCK_DTL_AMOUNT) CL_AMOUNT,
          sum(STOCK_DTL_QTY_BASE_UNIT) CL_QTY
        from
          TBL_INVE_STOCK_DTL C
          inner join TBL_INVE_STOCK D ON C.STOCK_ID = D.STOCK_ID
        WHERE
          D.STOCK_DATE <= TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
          AND D.branch_id in (".implode(",",$data['branch_ids']).")
          AND (
            UPPER(D.STOCK_CODE_TYPE) = 'STR'
            OR UPPER(D.STOCK_CODE_TYPE) = 'OS'
          )
        group by
          c.product_id
      ) XX
    Group by
      product_id
  ) T2 ON (T1.PRODUCT_ID = T2.PRODUCT_ID)";

                        $opening_data = \Illuminate\Support\Facades\DB::selectOne($opening_qry);

                        $closing_qry = "select
  sum(OPEN_QTY * T2.OP_AVG_RATE) closing
FROM
  (
    select
      inner_qry.PRODUCT_ID,
      sum(OPEN_QTY) OPEN_QTY,
      sum(OPEN_AMOUNT) AS OPEN_AMOUNT
    FROM
      (
        select
          PRODUCT_ID,
          SUM(QTY_BASE_UNIT_VALUE) OPEN_QTY,
          SUM(QTY_BASE_UNIT_VALUE * COST_RATE) OPEN_AMOUNT
        FROM
          VW_PURC_STOCK_DTL
        WHERE
          BRANCH_ID IN (".implode(",",$data['branch_ids']).")
          AND trunc(DOCUMENT_DATE) < TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
          AND (
            STOCK_CALCULATION_EFFECT = '+'
            OR STOCK_CALCULATION_EFFECT = '-'
          )
        GROUP BY
          PRODUCT_ID
      ) inner_qry
    group by
      inner_qry.PRODUCT_ID
  ) T1
  LEFT JOIN (
    select
      product_id,
      CASE WHEN sum(op_qty) <> 0 THEN (
        sum(op_amount) / sum(op_qty)
      ) ELSE 0 END OP_AVG_RATE,
      CASE WHEN sum(cl_qty) <> 0 THEN (
        sum(cl_amount) / sum(cl_qty)
      ) ELSE 0 END CL_AVG_RATE
    FROM
      (
        select
          product_id,
          (
            sum(TBL_PURC_GRN_DTL_AMOUNT) - sum(TBL_PURC_GRN_DTL_DISC_AMOUNT)
          ) OP_AMOUNT,
          sum(qty_base_unit) OP_QTY,
          0 CL_AMOUNT,
          0 CL_QTY
        from
          TBL_PURC_GRN_DTL A
          inner join TBL_PURC_GRN B ON A.GRN_ID = B.GRN_ID
        WHERE
          B.grn_date < TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
          AND B.branch_id in (".implode(",",$data['branch_ids']).")
          AND UPPER(B.GRN_TYPE) = 'GRN'
        group by
          product_id
        UNION ALL
        select
          c.product_id,
          sum(STOCK_DTL_AMOUNT) OP_AMOUNT,
          sum(STOCK_DTL_QTY_BASE_UNIT) OP_QTY,
          0 CL_AMOUNT,
          0 CL_QTY
        from
          TBL_INVE_STOCK_DTL C
          inner join TBL_INVE_STOCK D ON C.STOCK_ID = D.STOCK_ID
        WHERE
          D.STOCK_DATE < TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
          AND D.branch_id in (".implode(",",$data['branch_ids']).")
          AND (
            UPPER(D.STOCK_CODE_TYPE) = 'STR'
            OR UPPER(D.STOCK_CODE_TYPE) = 'OS'
          )
        group by
          c.product_id
        UNION ALL
        select
          product_id,
          0 OP_AMOUNT,
          0 OP_QTY,
          (
            sum(TBL_PURC_GRN_DTL_AMOUNT) - sum(TBL_PURC_GRN_DTL_DISC_AMOUNT)
          ) CL_AMOUNT,
          sum(qty_base_unit) CL_QTY
        from
          TBL_PURC_GRN_DTL A
          inner join TBL_PURC_GRN B ON A.GRN_ID = B.GRN_ID
        WHERE
          B.grn_date <= TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
          AND B.branch_id in (".implode(",",$data['branch_ids']).")
          AND UPPER(B.GRN_TYPE) = 'GRN'
        group by
          product_id
        UNION ALL
        select
          c.product_id,
          0 OP_AMOUNT,
          0 OP_QTY,
          sum(STOCK_DTL_AMOUNT) CL_AMOUNT,
          sum(STOCK_DTL_QTY_BASE_UNIT) CL_QTY
        from
          TBL_INVE_STOCK_DTL C
          inner join TBL_INVE_STOCK D ON C.STOCK_ID = D.STOCK_ID
        WHERE
          D.STOCK_DATE <= TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
          AND D.branch_id in (".implode(",",$data['branch_ids']).")
          AND (
            UPPER(D.STOCK_CODE_TYPE) = 'STR'
            OR UPPER(D.STOCK_CODE_TYPE) = 'OS'
          )
        group by
          c.product_id
      ) XX
    Group by
      product_id
  ) T2 ON (T1.PRODUCT_ID = T2.PRODUCT_ID)";

                        $closing_data = \Illuminate\Support\Facades\DB::selectOne($closing_qry);

$net_purchases_qry = "select sum(VOUCHER_DEBIT) -  sum(VOUCHER_CREDIT) net_purchase from
      VW_ACCO_VOUCHER where   CHART_CODE like '8-%'
      and VOUCHER_DATE between TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') and TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
      and business_id = ".auth()->user()->business_id." and  company_id = ".auth()->user()->company_id." and branch_id in (".implode(",",$data['branch_ids']).") ";

                        $cogs_net_purchases_data = DB::selectOne($net_purchases_qry);
                        $cogs_net_purchases = isset($cogs_net_purchases_data->net_purchase)?$cogs_net_purchases_data->net_purchase:0;
                        $cogs_opening_stock = isset($opening_data->opening)?$opening_data->opening:0;
                        $cogs_closing_stock = isset($closing_data->closing)?$closing_data->closing:0;
                        $cogs_net_stock = (abs($cogs_opening_stock) + abs($cogs_net_purchases)) - abs($cogs_closing_stock);
                        $sub_total_sales = $sub_total_sales - $cogs_net_stock;

                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <td>Net Purchases</td>
                            <td class="text-right">{{number_format($cogs_net_purchases,3)}}</td>
                        </tr>
                        <tr>
                            <td>Opening Stock</td>
                            <td class="text-right">{{number_format($cogs_opening_stock,3)}}</td>
                        </tr>
                        <tr>
                            <td>Closing Stock</td>
                            <td class="text-right">{{number_format($cogs_closing_stock,3)}}</td>
                        </tr>
                        <tr class="grand_total">
                            <td class="rep-font-bold">Net Stock:</td>
                            <td class="text-right rep-font-bold">{{number_format($cogs_net_stock,3)}}</td>
                        </tr>
                        @php
                            $gross_profit = ($sub_total_sales) - $cost_value;
                        @endphp
                        <tr class="grand_total">
                            <td class="rep-font-bold">Gross Profit / (Loss):</td>
                            <td class="text-right rep-font-bold">{{number_format($gross_profit,3)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <span class="acc_heading">Other Income:</span>
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th width="30%" class="text-left">Account Code</th>
                            <th width="30%" class="text-left">Account Name</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Detail</th>
                        </tr>
                        @if(isset($accounts['other_income']))
                            @php
                                unset($accounts['other_income'][0]);
                            @endphp
                            @foreach($accounts['other_income'] as $other_income)
                                @php
                                    if($other_income->chart_level == 1){$class = 'level_1';}
                                    if($other_income->chart_level == 2){$class = 'level_2';}
                                    if($other_income->chart_level == 3){$class = 'level_3';}
                                    if($other_income->chart_level == 4){$class = 'level_4';}
                                @endphp
                                @if($other_income->balance_level1 != 0 || $other_income->balance_level2 != 0 || $other_income->balance_level3 != 0 || $other_income->balance_level4 != 0)
                                    <tr class="{{$class}}">
                                        <td><span class="acc_ledger_report" data-id="{{$other_income->chart_account_id}}" data-type="{{--{{$list->voucher_type}}--}}">{{$other_income->chart_code}}</span></td>
                                        <td class="name">{{$other_income->chart_name}}</td>
                                        <td class="text-right">{{($other_income->balance_level1 != 0)?number_format(-1*$other_income->balance_level1,3):""}}</td>
                                        <td class="text-right">{{($other_income->balance_level2 != 0)?number_format(-1*$other_income->balance_level2,3):""}}</td>
                                        <td class="text-right">{{($other_income->balance_level3 != 0)?number_format(-1*$other_income->balance_level3,3):""}}</td>
                                        <td class="text-right">{{($other_income->balance_level4 != 0)?number_format(-1*$other_income->balance_level4,3):""}}</td>
                                    </tr>
                                @endif
                                @php
                                    $sub_total_other_income +=  -1*$other_income->balance_level4;
                                @endphp
                            @endforeach
                        @endif
                        <tr class="sub_total">
                            <td class="rep-font-bold" colspan="2">Total:</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($sub_total_other_income,3)}}</td>
                        </tr>
                        @php
                            $gross_profit2 = $gross_profit + $sub_total_other_income;
                        @endphp
                    </table>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <span class="acc_heading">Operating Expenses:</span>
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th width="30%" class="text-left">Account Code</th>
                            <th width="30%" class="text-left">Account Name</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Group Total</th>
                            <th width="10%" class="text-center">Detail</th>
                        </tr>
                        @if(isset($accounts['exp']))
                            @foreach($accounts['exp'] as $exp)
                                @php
                                    if($exp->chart_level == 1){$class = 'level_1';}
                                    if($exp->chart_level == 2){$class = 'level_2';}
                                    if($exp->chart_level == 3){$class = 'level_3';}
                                    if($exp->chart_level == 4){$class = 'level_4';}
                                @endphp
                                @if($exp->balance_level1 != 0 || $exp->balance_level2 != 0 || $exp->balance_level3 != 0 || $exp->balance_level4 != 0)
                                    <tr class="{{$class}}">
                                        <td><span class="acc_ledger_report" data-id="{{$exp->chart_account_id}}" data-type="{{--{{$list->voucher_type}}--}}">{{$exp->chart_code}}</span></td>
                                        <td class="name">{{$exp->chart_name}}</td>
                                        <td class="text-right">{{($exp->balance_level1 != 0)?number_format($exp->balance_level1,3):""}}</td>
                                        <td class="text-right">{{($exp->balance_level2 != 0)?number_format($exp->balance_level2,3):""}}</td>
                                        <td class="text-right">{{($exp->balance_level3 != 0)?number_format($exp->balance_level3,3):""}}</td>
                                        <td class="text-right">{{($exp->balance_level4 != 0)?number_format($exp->balance_level4,3):""}}</td>
                                    </tr>
                                @endif
                                @php
                                    $sub_total_exp +=  $exp->balance_level4;
                                @endphp
                            @endforeach
                        @endif
                        <tr class="sub_total">
                            <td class="rep-font-bold" colspan="2">Total:</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($sub_total_exp,3)}}</td>
                        </tr>
                        @php
                            $gross_profit3 = $gross_profit2 - $sub_total_exp;
                        @endphp
                        <tr class="grand_total">
                            <td class="rep-font-bold" colspan="2">Net Profit / (Loss):</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($gross_profit3,3)}}</td>
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
    <script>
        $(".acc_ledger_report").click(function(e){
            var account_id = $(this).data('id');
            // date differnce between 3 month from today
            var from_date = "{{date('d-m-Y', strtotime($to_date))}}";
            var str = from_date.split('-');
            var to_date = "{{date('d-m-Y', strtotime($from_date))}}";

            var formData = {
                report_branch_ids : [{{auth()->user()->branch_id}}],
                chart_account : account_id,
                date_to : from_date,
                date_from : to_date,
                report_case : 'accounting_ledger',
                report_type :  "static",
                form_file_type : "report"
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : '{{ action('Report\UserReportsController@staticStore', ['static','static','accounting_ledger']) }}',
                type        : 'POST',
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    if(response.status == 'success'){
                        toastr.success(response.message);
                        window.open(response['data']['url'], "_blank");
                    }else{
                        toastr.error(response.message);
                        window.location.reload();
                    }
                }
            });
        });
    </script>
@endsection
@section('exportXls')
    @if(isset($data['form_file_type']) && $data['form_file_type'] == 'xls')
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



