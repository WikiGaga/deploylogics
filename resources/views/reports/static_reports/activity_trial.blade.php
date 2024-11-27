@extends('layouts.report')
@section('title', 'Trial Balance Report')

@section('pageCSS')
    <style>
        body{
            width: 1600px;
        }
        tr.level_1>td:first-child,
        tr.level_1>td:nth-child(2){
            font-size: 15px;
        }
        tr.level_1>td{
            font-weight: 500 !important;
        }
        #level_1_color{
          color:#800000 !important;
        }
        #level_2_color{
          color:#32CD32 !important;
        }
        #level_3_color{
          color:#0000D1 !important;
        }
        tr.level_2>td:first-child,
        tr.level_2>td:nth-child(2){
            font-size: 13px;
        }
        tr.level_2>td{
            font-weight: 500 !important;
        }
        tr.level_3>td:first-child,
        tr.level_3>td:nth-child(2){
            font-size: 12px;
        }
        tr.level_3>td{
            font-weight: 500 !important;
        }

        tr.level_4>td {

        }
        td.right_number {
            text-align: right;
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
        $chart_qry = "";
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
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['chart_account_multiple']) &&  count($data['chart_account_multiple']) != 0 && $data['chart_account_multiple'] != "" && $data['chart_account_multiple'] != null)
                    @php
                        $chart_accounts = \Illuminate\Support\Facades\DB::table('tbl_acco_chart_account')->whereIn('chart_account_id',$data['chart_account_multiple'])->get();
                        $chart_qry = " and ( ";
                    @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Chart Account:</span>
                        @foreach($chart_accounts as $chart_account)
                            @php
                                $chart_qry .= "coa.chart_code like '";
                                if($chart_account->chart_level == 1){
                                    $str = substr($chart_account->chart_code, 0, 1);
                                }
                                if($chart_account->chart_level == 2){
                                    $str = substr($chart_account->chart_code, 0, 4);
                                }
                                if($chart_account->chart_level == 3){
                                    $str = substr($chart_account->chart_code, 0, 7);
                                }
                                $chart_qry .= $str."%' OR ";
                            @endphp
                            <span style="color: #5578eb;">{{$chart_account->chart_code}} - {{$chart_account->chart_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                    @php
                       $chart_qry = rtrim($chart_qry, "OR ");
                       $chart_qry .= " )";
                    @endphp
                @endif
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Chart Level:</span>
                    <span style="color: #5578eb;">
                        @if($data['level_list'] != "")
                            {{ $data['level_list'] }}
                        @else
                            ALL
                        @endif
                    </span>
                </h6>
            </div>
            @include('reports.template.branding')
        </div>
        @php

            $level_list="";

            if(!empty($data['level_list']) && isset($data['level_list'])){
                $level_list = "and vouch.chart_level = '".$data['level_list']."' ";
            }

            if($data['OrderBy'] == "code")
            {
                $OrderBy = 'vouch.CHART_CODE';
            }
            if($data['OrderBy'] == "name")
            {
                $OrderBy = 'vouch.chart_name_sorting';
            }

/*
$qry = "SELECT 
  ca.CHART_ACCOUNT_ID,
  vouch.CHART_CODE,
  ca.CHART_NAME,
  vouch.CHART_LEVEL,
  OPEN_BAL_DR,
  OPEN_BAL_CR,
  DR_BALANCE,
  CR_BALANCE,
  CLOSING_BAL_DR,
  CLOSING_BAL_CR 
FROM
  (
  SELECT 
    4 CHART_LEVEL,
    G.CHART_CODE,
    OPEN_BAL_DR,
    OPEN_BAL_CR,
    G.OPEN_BAL AS OPEN_BAL,
    G.DR_BALANCE AS DR_BALANCE,
    G.CR_BALANCE AS CR_BALANCE,
    G.PERIOD_BAL AS PERIOD_BAL,
    G.CLOSING_BAL_DR,
    G.CLOSING_BAL_CR 
  FROM
    (SELECT 
      M.CHART_CODE,
      (
        CASE
          WHEN M.OPEN_BAL > 0 
          THEN M.OPEN_BAL 
          ELSE 0 
        END
      ) OPEN_BAL_DR,
      (
        CASE
          WHEN M.OPEN_BAL < 0 
          THEN M.OPEN_BAL * - 1 
          ELSE 0 
        END
      ) OPEN_BAL_CR,
      M.OPEN_BAL,
      M.DR_BALANCE,
      M.CR_BALANCE,
      M.PERIOD_BAL,
      (
        CASE
          WHEN M.CLOSING_BAL > 0 
          THEN M.CLOSING_BAL 
          ELSE 0 
        END
      ) CLOSING_BAL_DR,
      (
        CASE
          WHEN M.CLOSING_BAL < 0 
          THEN M.CLOSING_BAL * - 1 
          ELSE 0 
        END
      ) CLOSING_BAL_CR,
      (
        SUBSTR(M.CHART_CODE, 0, 1) || '-00-00-0000'
      ) AS GRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 4) || '-00-0000'
      ) AS SGRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 7) || '-0000'
      ) AS SSGRP_CODE 
    FROM
      (SELECT 
        D.business_id,
        D.company_id,
        D.CHART_CODE,
        SUM(D.OPEN_BAL) AS OPEN_BAL,
        SUM(D.DR_BALANCE) AS DR_BALANCE,
        SUM(D.CR_BALANCE) AS CR_BALANCE,
        (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) AS PERIOD_BAL,
        SUM(D.OPEN_BAL) + (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) CLOSING_BAL 
      FROM
        (SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) - SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) OPEN_BAL,
          0 DR_BALANCE,
          0 CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).")
          AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
          $chart_qry
        UNION
        ALL 
        SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          0 OPEN_BAL,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS DR_BALANCE,
          SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
          AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
          AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
          $chart_qry) D 
      GROUP BY D.chart_code,
        D.business_id,
        D.company_id) M) G 
  UNION
  ALL
  SELECT 
    1 CHART_LEVEL,
    G.GRP_CODE,
    SUM(G.OPEN_BAL_DR) AS OPEN_BAL_DR,
    SUM(G.OPEN_BAL_CR) AS OPEN_BAL_CR,
    SUM(G.OPEN_BAL) AS OPEN_BAL,
    SUM(G.DR_BALANCE) AS DR_BALANCE,
    SUM(G.CR_BALANCE) AS CR_BALANCE,
    SUM(G.PERIOD_BAL) AS PERIOD_BAL,
    SUM(G.CLOSING_BAL_DR) AS CLOSING_BAL_DR,
    SUM(G.CLOSING_BAL_CR) AS CLOSING_BAL_CR 
  FROM
    (SELECT 
      M.CHART_CODE,
      (
        CASE
          WHEN M.OPEN_BAL > 0 
          THEN M.OPEN_BAL 
          ELSE 0 
        END
      ) OPEN_BAL_DR,
      (
        CASE
          WHEN M.OPEN_BAL < 0 
          THEN M.OPEN_BAL * - 1 
          ELSE 0 
        END
      ) OPEN_BAL_CR,
      M.OPEN_BAL,
      M.DR_BALANCE,
      M.CR_BALANCE,
      M.PERIOD_BAL,
      (
        CASE
          WHEN M.CLOSING_BAL > 0 
          THEN M.CLOSING_BAL 
          ELSE 0 
        END
      ) CLOSING_BAL_DR,
      (
        CASE
          WHEN M.CLOSING_BAL < 0 
          THEN M.CLOSING_BAL * - 1 
          ELSE 0 
        END
      ) CLOSING_BAL_CR,
      (
        SUBSTR(M.CHART_CODE, 0, 1) || '-00-00-0000'
      ) AS GRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 4) || '-00-0000'
      ) AS SGRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 7) || '-0000'
      ) AS SSGRP_CODE 
    FROM
      (SELECT 
        D.business_id,
        D.company_id,
        D.CHART_CODE,
        SUM(D.OPEN_BAL) AS OPEN_BAL,
        SUM(D.DR_BALANCE) AS DR_BALANCE,
        SUM(D.CR_BALANCE) AS CR_BALANCE,
        (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) AS PERIOD_BAL,
        SUM(D.OPEN_BAL) + (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) CLOSING_BAL 
      FROM
        (SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) - SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) OPEN_BAL,
          0 AS DR_BALANCE,
          0 AS CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).")
          AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
          $chart_qry
        UNION
        ALL 
        SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          0 OPEN_BAL,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS DR_BALANCE,
          SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
          AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
          AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
          $chart_qry) D 
      GROUP BY D.chart_code,
        D.business_id,
        D.company_id) M) G 
  GROUP BY G.GRP_CODE 
  UNION
  ALL
  SELECT 
    2 CHART_LEVEL,
    G.SGRP_CODE,
    SUM(G.OPEN_BAL_DR) AS OPEN_BAL_DR,
    SUM(G.OPEN_BAL_CR) AS OPEN_BAL_CR,
    SUM(G.OPEN_BAL) AS OPEN_BAL,
    SUM(G.DR_BALANCE) AS DR_BALANCE,
    SUM(G.CR_BALANCE) AS CR_BALANCE,
    SUM(G.PERIOD_BAL) AS PERIOD_BAL,
    SUM(G.CLOSING_BAL_DR) AS CLOSING_BAL_DR,
    SUM(G.CLOSING_BAL_CR) AS CLOSING_BAL_CR 
  FROM
    (SELECT 
      M.CHART_CODE,
      (
        CASE
          WHEN M.OPEN_BAL > 0 
          THEN M.OPEN_BAL 
          ELSE 0 
        END
      ) OPEN_BAL_DR,
      (
        CASE
          WHEN M.OPEN_BAL < 0 
          THEN M.OPEN_BAL * - 1 
          ELSE 0 
        END
      ) OPEN_BAL_CR,
      M.OPEN_BAL,
      M.DR_BALANCE,
      M.CR_BALANCE,
      M.PERIOD_BAL,
      (
        CASE
          WHEN M.CLOSING_BAL > 0 
          THEN M.CLOSING_BAL 
          ELSE 0 
        END
      ) CLOSING_BAL_DR,
      (
        CASE
          WHEN M.CLOSING_BAL < 0 
          THEN M.CLOSING_BAL * - 1 
          ELSE 0 
        END
      ) CLOSING_BAL_CR,
      (
        SUBSTR(M.CHART_CODE, 0, 1) || '-00-00-0000'
      ) AS GRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 4) || '-00-0000'
      ) AS SGRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 7) || '-0000'
      ) AS SSGRP_CODE 
    FROM
      (SELECT 
        D.business_id,
        D.company_id,
        D.CHART_CODE,
        SUM(D.OPEN_BAL) AS OPEN_BAL,
        SUM(D.DR_BALANCE) AS DR_BALANCE,
        SUM(D.CR_BALANCE) AS CR_BALANCE,
        (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) AS PERIOD_BAL,
        SUM(D.OPEN_BAL) + (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) CLOSING_BAL 
      FROM
        (SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) - SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) OPEN_BAL,
          0 AS DR_BALANCE,
          0 AS CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).")
          AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
          $chart_qry
        UNION
        ALL 
        SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          0 OPEN_BAL,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS DR_BALANCE,
          SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
          AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
          AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
          $chart_qry) D 
      GROUP BY D.chart_code,
        D.business_id,
        D.company_id) M) G 
  GROUP BY G.SGRP_CODE 
  UNION
  ALL  
  SELECT 
    3 CHART_LEVEL,
    G.SSGRP_CODE,
    SUM(G.OPEN_BAL_DR) AS OPEN_BAL_DR,
    SUM(G.OPEN_BAL_CR) AS OPEN_BAL_CR,
    SUM(G.OPEN_BAL) AS OPEN_BAL,
    SUM(G.DR_BALANCE) AS DR_BALANCE,
    SUM(G.CR_BALANCE) AS CR_BALANCE,
    SUM(G.PERIOD_BAL) AS PERIOD_BAL,
    SUM(G.CLOSING_BAL_DR) AS CLOSING_BAL_DR,
    SUM(G.CLOSING_BAL_CR) AS CLOSING_BAL_CR 
  FROM
    (SELECT 
      M.CHART_CODE,
      (
        CASE
          WHEN M.OPEN_BAL > 0 
          THEN M.OPEN_BAL 
          ELSE 0 
        END
      ) OPEN_BAL_DR,
      (
        CASE
          WHEN M.OPEN_BAL < 0 
          THEN M.OPEN_BAL * - 1 
          ELSE 0 
        END
      ) OPEN_BAL_CR,
      M.OPEN_BAL,
      M.DR_BALANCE,
      M.CR_BALANCE,
      M.PERIOD_BAL,
      (
        CASE
          WHEN M.CLOSING_BAL > 0 
          THEN M.CLOSING_BAL 
          ELSE 0 
        END
      ) CLOSING_BAL_DR,
      (
        CASE
          WHEN M.CLOSING_BAL < 0 
          THEN M.CLOSING_BAL * - 1 
          ELSE 0 
        END
      ) CLOSING_BAL_CR,
      (
        SUBSTR(M.CHART_CODE, 0, 1) || '-00-00-0000'
      ) AS GRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 4) || '-00-0000'
      ) AS SGRP_CODE,
      (
        SUBSTR(M.CHART_CODE, 0, 7) || '-0000'
      ) AS SSGRP_CODE 
    FROM
      (SELECT 
        D.business_id,
        D.company_id,
        D.CHART_CODE,
        SUM(D.OPEN_BAL) AS OPEN_BAL,
        SUM(D.DR_BALANCE) AS DR_BALANCE,
        SUM(D.CR_BALANCE) AS CR_BALANCE,
        (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) AS PERIOD_BAL,
        SUM(D.OPEN_BAL) + (SUM(D.DR_BALANCE) - SUM(CR_BALANCE)) CLOSING_BAL 
      FROM
        (SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) - SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) OPEN_BAL,
          0 AS DR_BALANCE,
          0 AS CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
          AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
          $chart_qry
        UNION
        ALL 
        SELECT DISTINCT 
          v.business_id,
          v.company_id,
          coa.chart_code,
          0 OPEN_BAL,
          SUM(v.voucher_debit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS DR_BALANCE,
          SUM(v.voucher_credit) OVER (
            PARTITION BY v.chart_account_id,
            v.business_id,
            v.company_id
          ) AS CR_BALANCE 
        FROM
          VW_ACCO_VOUCHER_POSTED v,
          tbl_acco_chart_account coa 
        WHERE v.chart_account_id = coa.chart_account_id 
          AND v.branch_id IN (".implode(",",$data['branch_ids']).")
          AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
          AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
          $chart_qry) D 
      GROUP BY D.chart_code,
        D.business_id,
        D.company_id) M) G 
  GROUP BY G.SSGRP_CODE) vouch,
  tbl_acco_chart_account ca 
WHERE vouch.chart_code = ca.chart_code (+) 
$level_list
ORDER BY $OrderBy ";
*/

$qry ="SELECT ca.CHART_ACCOUNT_ID,
  vouch.CHART_CODE,
  ca.CHART_NAME,
  chart_name_sorting,
  vouch.CHART_LEVEL,
  OPEN_BAL_DR,
  OPEN_BAL_CR,
  DR_BALANCE,
  CR_BALANCE,
  CLOSING_BAL_DR,
  CLOSING_BAL_CR
FROM (
      SELECT 
        4 CHART_LEVEL,
        G.CHART_CODE,
        chart_name_sorting,
        OPEN_BAL_DR,
        OPEN_BAL_CR,
        G.OPEN_BAL       AS OPEN_BAL,
        G.DR_BALANCE     AS DR_BALANCE,
        G.CR_BALANCE     AS CR_BALANCE,
        G.PERIOD_BAL     AS PERIOD_BAL,
        G.CLOSING_BAL_DR,
        G.CLOSING_BAL_CR
      FROM (
        SELECT 
          M.CHART_CODE,
          CONCAT (CONCAT (SUBSTR (M.chart_code, 0, 7), '-'),
          ca.chart_name)
          AS chart_name_sorting,
          (CASE WHEN M.OPEN_BAL > 0 THEN M.OPEN_BAL ELSE 0 END)
          OPEN_BAL_DR,
          (CASE
          WHEN M.OPEN_BAL < 0 THEN M.OPEN_BAL * -1
          ELSE 0
          END)
          OPEN_BAL_CR,
          M.OPEN_BAL,
          M.DR_BALANCE,
          M.CR_BALANCE,
          M.PERIOD_BAL,
          (CASE
          WHEN M.CLOSING_BAL > 0 THEN M.CLOSING_BAL
          ELSE 0
          END)
          CLOSING_BAL_DR,
          (CASE
          WHEN M.CLOSING_BAL < 0 THEN M.CLOSING_BAL * -1
          ELSE 0
          END)
          CLOSING_BAL_CR,
          (SUBSTR (M.CHART_CODE, 0, 1) || '-00-00-0000')
          AS GRP_CODE,
          (SUBSTR (M.CHART_CODE, 0, 4) || '-00-0000')
          AS SGRP_CODE,
          (SUBSTR (M.CHART_CODE, 0, 7) || '-0000')
          AS SSGRP_CODE
      FROM (  
          SELECT
            D.business_id,
            D.company_id,
            D.CHART_CODE,
            SUM (D.OPEN_BAL)
            AS OPEN_BAL,
            SUM (D.DR_BALANCE)
            AS DR_BALANCE,
            SUM (D.CR_BALANCE)
            AS CR_BALANCE,
            (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
            AS PERIOD_BAL,
            SUM (D.OPEN_BAL)
            + (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
            CLOSING_BAL
          FROM (
              SELECT DISTINCT
                v.business_id,
                v.company_id,
                coa.chart_code,
                SUM (v.voucher_debit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                - SUM (v.voucher_credit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                OPEN_BAL,
                0
                DR_BALANCE,
                0
                CR_BALANCE
              FROM 
                VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
              WHERE v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
                $chart_qry
              UNION 
              ALL
              SELECT DISTINCT
                v.business_id,
                v.company_id,
                coa.chart_code,
                0
                OPEN_BAL,
                SUM (v.voucher_debit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                AS DR_BALANCE,
                SUM (v.voucher_credit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                AS CR_BALANCE
              FROM 
                VW_ACCO_VOUCHER_POSTED    v,tbl_acco_chart_account coa
              WHERE v.chart_account_id = coa.chart_account_id 
                AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
                AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
                AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
                $chart_qry
            ) D
            GROUP BY D.chart_code, D.business_id, D.company_id
      ) M,
      tbl_acco_chart_account ca
      WHERE M.chart_code = ca.chart_code(+)
    ) G
    UNION 
    ALL
    SELECT 
      1                          CHART_LEVEL,
      G.GRP_CODE,
      G.GRP_CODE                 chart_name_sorting,
      SUM (G.OPEN_BAL_DR)        AS OPEN_BAL_DR,
      SUM (G.OPEN_BAL_CR)        AS OPEN_BAL_CR,
      SUM (G.OPEN_BAL)           AS OPEN_BAL,
      SUM (G.DR_BALANCE)         AS DR_BALANCE,
      SUM (G.CR_BALANCE)         AS CR_BALANCE,
      SUM (G.PERIOD_BAL)         AS PERIOD_BAL,
      SUM (G.CLOSING_BAL_DR)     AS CLOSING_BAL_DR,
      SUM (G.CLOSING_BAL_CR)     AS CLOSING_BAL_CR
    FROM (
        SELECT 
          M.CHART_CODE,
          (CASE WHEN M.OPEN_BAL > 0 THEN M.OPEN_BAL ELSE 0 END)
          OPEN_BAL_DR,
          (CASE
          WHEN M.OPEN_BAL < 0 THEN M.OPEN_BAL * -1
          ELSE 0
          END)
          OPEN_BAL_CR,
          M.OPEN_BAL,
          M.DR_BALANCE,
          M.CR_BALANCE,
          M.PERIOD_BAL,
          (CASE
          WHEN M.CLOSING_BAL > 0 THEN M.CLOSING_BAL
          ELSE 0
          END)
          CLOSING_BAL_DR,
          (CASE
          WHEN M.CLOSING_BAL < 0 THEN M.CLOSING_BAL * -1
          ELSE 0
          END)
          CLOSING_BAL_CR,
          (SUBSTR (M.CHART_CODE, 0, 1) || '-00-00-0000')
          AS GRP_CODE,
          (SUBSTR (M.CHART_CODE, 0, 4) || '-00-0000')
          AS SGRP_CODE,
          (SUBSTR (M.CHART_CODE, 0, 7) || '-0000')
          AS SSGRP_CODE
        FROM (
            SELECT 
              D.business_id,
              D.company_id,
              D.CHART_CODE,
              SUM (D.OPEN_BAL)
              AS OPEN_BAL,
              SUM (D.DR_BALANCE)
              AS DR_BALANCE,
              SUM (D.CR_BALANCE)
              AS CR_BALANCE,
              (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
              AS PERIOD_BAL,
              SUM (D.OPEN_BAL)
              + (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
              CLOSING_BAL
            FROM (
                SELECT DISTINCT
                  v.business_id,
                  v.company_id,
                  coa.chart_code,
                  SUM (v.voucher_debit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id)
                  - SUM (v.voucher_credit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id)
                  OPEN_BAL,
                  0
                  AS DR_BALANCE,
                  0
                  AS CR_BALANCE
                FROM 
                  VW_ACCO_VOUCHER_POSTED  v, tbl_acco_chart_account coa
              WHERE v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
                $chart_qry

                UNION 
                ALL
                SELECT DISTINCT
                  v.business_id,
                  v.company_id,
                  coa.chart_code,
                  0
                  OPEN_BAL,
                  SUM (v.voucher_debit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id)
                  AS DR_BALANCE,
                  SUM (v.voucher_credit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id)
                  AS CR_BALANCE
                FROM 
                  VW_ACCO_VOUCHER_POSTED  v, tbl_acco_chart_account coa
                WHERE v.chart_account_id = coa.chart_account_id
                  AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
                  AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
                  AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
                  $chart_qry
              ) D
              GROUP BY D.chart_code, D.business_id, D.company_id
            ) M
          )G 
          GROUP BY G.GRP_CODE

          UNION
          ALL
          SELECT 2                          CHART_LEVEL,
            G.SGRP_CODE,
            G.SGRP_CODE                chart_name_sorting,
            SUM (G.OPEN_BAL_DR)        AS OPEN_BAL_DR,
            SUM (G.OPEN_BAL_CR)        AS OPEN_BAL_CR,
            SUM (G.OPEN_BAL)           AS OPEN_BAL,
            SUM (G.DR_BALANCE)         AS DR_BALANCE,
            SUM (G.CR_BALANCE)         AS CR_BALANCE,
            SUM (G.PERIOD_BAL)         AS PERIOD_BAL,
            SUM (G.CLOSING_BAL_DR)     AS CLOSING_BAL_DR,
            SUM (G.CLOSING_BAL_CR)     AS CLOSING_BAL_CR
          FROM (
            SELECT 
              M.CHART_CODE,
              (CASE WHEN M.OPEN_BAL > 0 THEN M.OPEN_BAL ELSE 0 END)
              OPEN_BAL_DR,
              (CASE
              WHEN M.OPEN_BAL < 0 THEN M.OPEN_BAL * -1
              ELSE 0
              END)
              OPEN_BAL_CR,
              M.OPEN_BAL,
              M.DR_BALANCE,
              M.CR_BALANCE,
              M.PERIOD_BAL,
              (CASE
              WHEN M.CLOSING_BAL > 0 THEN M.CLOSING_BAL
              ELSE 0
              END)
              CLOSING_BAL_DR,
              (CASE
              WHEN M.CLOSING_BAL < 0 THEN M.CLOSING_BAL * -1
              ELSE 0
              END)
              CLOSING_BAL_CR,
              (SUBSTR (M.CHART_CODE, 0, 1) || '-00-00-0000')
              AS GRP_CODE,
              (SUBSTR (M.CHART_CODE, 0, 4) || '-00-0000')
              AS SGRP_CODE,
              (SUBSTR (M.CHART_CODE, 0, 7) || '-0000')
              AS SSGRP_CODE
            FROM (  
              SELECT D.business_id,
              D.company_id,
              D.CHART_CODE,
              SUM (D.OPEN_BAL)
              AS OPEN_BAL,
              SUM (D.DR_BALANCE)
              AS DR_BALANCE,
              SUM (D.CR_BALANCE)
              AS CR_BALANCE,
              (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
              AS PERIOD_BAL,
              SUM (D.OPEN_BAL)
              + (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
              CLOSING_BAL
            FROM (
              SELECT DISTINCT
                v.business_id,
                v.company_id,
                coa.chart_code,
                SUM (v.voucher_debit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                - SUM (v.voucher_credit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                OPEN_BAL,
                0
                AS DR_BALANCE,
                0
                AS CR_BALANCE
              FROM 
                VW_ACCO_VOUCHER_POSTED  v, tbl_acco_chart_account coa
              WHERE v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
                $chart_qry
              UNION ALL
              SELECT DISTINCT
                v.business_id,
                v.company_id,
                coa.chart_code,
                0
                OPEN_BAL,
                SUM (v.voucher_debit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                AS DR_BALANCE,
                SUM (v.voucher_credit)
                OVER (
                PARTITION BY v.chart_account_id,
                v.business_id,
                v.company_id)
                AS CR_BALANCE
              FROM 
                VW_ACCO_VOUCHER_POSTED  v, tbl_acco_chart_account coa
              WHERE v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
                AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
                AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
                $chart_qry
              ) D
              GROUP BY D.chart_code, D.business_id, D.company_id
            ) M
          )G
          GROUP BY G.SGRP_CODE
          UNION 
          ALL
          SELECT 
            3                          CHART_LEVEL,
            G.SSGRP_CODE,
            G.SSGRP_CODE               chart_name_sorting,
            SUM (G.OPEN_BAL_DR)        AS OPEN_BAL_DR,
            SUM (G.OPEN_BAL_CR)        AS OPEN_BAL_CR,
            SUM (G.OPEN_BAL)           AS OPEN_BAL,
            SUM (G.DR_BALANCE)         AS DR_BALANCE,
            SUM (G.CR_BALANCE)         AS CR_BALANCE,
            SUM (G.PERIOD_BAL)         AS PERIOD_BAL,
            SUM (G.CLOSING_BAL_DR)     AS CLOSING_BAL_DR,
            SUM (G.CLOSING_BAL_CR)     AS CLOSING_BAL_CR
          FROM (
            SELECT 
              M.CHART_CODE,
              (CASE WHEN M.OPEN_BAL > 0 THEN M.OPEN_BAL ELSE 0 END)
              OPEN_BAL_DR,
              (CASE
              WHEN M.OPEN_BAL < 0 THEN M.OPEN_BAL * -1
              ELSE 0
              END)
              OPEN_BAL_CR,
              M.OPEN_BAL,
              M.DR_BALANCE,
              M.CR_BALANCE,
              M.PERIOD_BAL,
              (CASE
              WHEN M.CLOSING_BAL > 0 THEN M.CLOSING_BAL
              ELSE 0
              END)
              CLOSING_BAL_DR,
              (CASE
              WHEN M.CLOSING_BAL < 0 THEN M.CLOSING_BAL * -1
              ELSE 0
              END)
              CLOSING_BAL_CR,
              (SUBSTR (M.CHART_CODE, 0, 1) || '-00-00-0000')
              AS GRP_CODE,
              (SUBSTR (M.CHART_CODE, 0, 4) || '-00-0000')
              AS SGRP_CODE,
              (SUBSTR (M.CHART_CODE, 0, 7) || '-0000')
              AS SSGRP_CODE
            FROM (
              SELECT 
                D.business_id,
                D.company_id,
                D.CHART_CODE,
                SUM (D.OPEN_BAL)
                AS OPEN_BAL,
                SUM (D.DR_BALANCE)
                AS DR_BALANCE,
                SUM (D.CR_BALANCE)
                AS CR_BALANCE,
                (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
                AS PERIOD_BAL,
                SUM (D.OPEN_BAL)
                + (SUM (D.DR_BALANCE) - SUM (CR_BALANCE))
                CLOSING_BAL
              FROM (
                SELECT DISTINCT
                  v.business_id,
                  v.company_id,
                  coa.chart_code,
                  SUM (v.voucher_debit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id)
                  - SUM (v.voucher_credit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id)
                  OPEN_BAL,
                  0
                  AS DR_BALANCE,
                  0
                  AS CR_BALANCE
                FROM 
                  VW_ACCO_VOUCHER_POSTED  v, tbl_acco_chart_account coa
                WHERE v.chart_account_id = coa.chart_account_id
                  AND v.branch_id IN (".implode(",",$data['branch_ids']).") 
                  AND v.VOUCHER_DATE < to_date('".$data['from_date']."','yyyy/mm/dd')
                  $chart_qry

                UNION ALL
                SELECT DISTINCT
                  v.business_id,
                  v.company_id,
                  coa.chart_code,
                  0
                  OPEN_BAL,
                  SUM (v.voucher_debit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id)
                  AS DR_BALANCE,
                  SUM (v.voucher_credit)
                  OVER (
                  PARTITION BY v.chart_account_id,
                  v.business_id,
                  v.company_id) AS CR_BALANCE
                FROM 
                  VW_ACCO_VOUCHER_POSTED  v, tbl_acco_chart_account coa
                WHERE v.chart_account_id = coa.chart_account_id
                  AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                  AND v.VOUCHER_DATE >= to_date('".$data['from_date']."','yyyy/mm/dd')
                  AND v.VOUCHER_DATE <= to_date('".$data['to_date']."','yyyy/mm/dd')
                  $chart_qry
                ) D
                GROUP BY D.chart_code, D.business_id, D.company_id
              ) M
            )G
            GROUP BY G.SSGRP_CODE) vouch, tbl_acco_chart_account ca
            WHERE vouch.chart_code = ca.chart_code(+)
            $level_list
            ORDER BY $OrderBy";

//dd($qry);
            $list = DB::select($qry);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="150px">Account Code</th>
                            <th width="400px">Account Title</th>
                            <th class="text-center" colspan="2">Opening</th>
                            <th class="text-center" colspan="2">Period</th>
                            <th class="text-center" colspan="2">Closing</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th width="125px" class="text-center">Debit</th>
                            <th width="125px" class="text-center">Credit</th>

                            <th width="125px" class="text-center">Debit</th>
                            <th width="125px" class="text-center">Credit</th>

                            <th width="125px" class="text-center">Debit</th>
                            <th width="125px" class="text-center">Credit</th>
                        </tr>
                        @php
                            $level_4_opening_debit = 0;
                            $level_4_opening_credit = 0;

                            $level_4_period_debit = 0;
                            $level_4_period_credit = 0;

                            $level_4_closing_debit = 0;
                            $level_4_closing_credit = 0;
                        @endphp
                        @foreach($list as $accounts)
                            @if($accounts->chart_level == 1)
                                <tr class="level_1">
                                    <td ><span id="level_1_color" class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td id="level_1_color">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    {{-- opening --}}
                                    @if($accounts->open_bal_dr != 0)
                                        <td id="level_1_color" class="right_number">{{number_format($accounts->open_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->open_bal_cr != 0)
                                        <td id="level_1_color" class="right_number">{{number_format($accounts->open_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->dr_balance != 0)
                                        <td id="level_1_color" class="right_number">{{number_format($accounts->dr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->cr_balance != 0)
                                        <td id="level_1_color" class="right_number">{{number_format($accounts->cr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->closing_bal_dr != 0)
                                        <td id="level_1_color" class="right_number">{{number_format($accounts->closing_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->closing_bal_cr != 0)
                                        <td id="level_1_color" class="right_number">{{number_format($accounts->closing_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                </tr>
                            @endif
                            @if($accounts->chart_level == 2)
                                <tr class="level_2" id="level_2_color">
                                    <td><span id="level_2_color" class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td id="level_2_color" style="padding-left: 15px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    {{-- opening --}}
                                    @if($accounts->open_bal_dr != 0)
                                        <td id="level_2_color" class="right_number">{{number_format($accounts->open_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->open_bal_cr != 0)
                                        <td id="level_2_color" class="right_number">{{number_format($accounts->open_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->dr_balance != 0)
                                        <td id="level_2_color" class="right_number">{{number_format($accounts->dr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->cr_balance != 0)
                                        <td id="level_2_color" class="right_number">{{number_format($accounts->cr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->closing_bal_dr != 0)
                                        <td id="level_2_color" class="right_number">{{number_format($accounts->closing_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->closing_bal_cr != 0)
                                        <td id="level_2_color" class="right_number">{{number_format($accounts->closing_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                </tr>
                            @endif

                            @if($accounts->chart_level == 3)
                                <tr class="level_3">
                                    <td><span id="level_3_color" class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td id="level_3_color" style="padding-left: 30px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    {{-- opening --}}
                                    @if($accounts->open_bal_dr != 0)
                                        <td id="level_3_color" class="right_number">{{number_format($accounts->open_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->open_bal_cr != 0)
                                        <td id="level_3_color" class="right_number">{{number_format($accounts->open_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->dr_balance != 0)
                                        <td id="level_3_color" class="right_number">{{number_format($accounts->dr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->cr_balance != 0)
                                        <td id="level_3_color" class="right_number">{{number_format($accounts->cr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->closing_bal_dr != 0)
                                        <td id="level_3_color" class="right_number">{{number_format($accounts->closing_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->closing_bal_cr != 0)
                                        <td id="level_3_color" class="right_number">{{number_format($accounts->closing_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                </tr>
                            @endif

                            @if($accounts->chart_level == 4)
                                <tr class="level_4">
                                    <td style="padding-left: 25px !important;"><span class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td style="padding-left: 45px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    {{-- opening --}}
                                    @if($accounts->open_bal_dr != 0)
                                        <td class="right_number">{{number_format($accounts->open_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->open_bal_cr != 0)
                                        <td class="right_number">{{number_format($accounts->open_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->dr_balance != 0)
                                        <td class="right_number">{{number_format($accounts->dr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->cr_balance != 0)
                                        <td class="right_number">{{number_format($accounts->cr_balance,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    {{-- closing --}}
                                    @if($accounts->closing_bal_dr != 0)
                                        <td class="right_number">{{number_format($accounts->closing_bal_dr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->closing_bal_cr != 0)
                                        <td class="right_number">{{number_format($accounts->closing_bal_cr,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif

                                    @php
                                        $level_4_opening_debit += $accounts->open_bal_dr;
                                        $level_4_opening_credit += $accounts->open_bal_cr;
                                        
                                        $level_4_period_debit += $accounts->dr_balance;
                                        $level_4_period_credit += $accounts->cr_balance;

                                        $level_4_closing_debit += $accounts->closing_bal_dr;
                                        $level_4_closing_credit += $accounts->closing_bal_cr;
                                    @endphp
                                </tr>
                            @endif
                            
                            @if(!empty($data['level_list']) && isset($data['level_list']))
                                @if($data['level_list'] < 4)
                                    @php
                                        $level_4_opening_debit += $accounts->open_bal_dr;
                                        $level_4_opening_credit += $accounts->open_bal_cr;
                                        
                                        $level_4_period_debit += $accounts->dr_balance;
                                        $level_4_period_credit += $accounts->cr_balance;

                                        $level_4_closing_debit += $accounts->closing_bal_dr;
                                        $level_4_closing_credit += $accounts->closing_bal_cr;
                                    @endphp
                                @endif
                            @endif

                        @endforeach
                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_opening_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_opening_credit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_period_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_period_credit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_closing_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_closing_credit,3)}}</td>
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



