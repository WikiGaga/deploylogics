@extends('layouts.report')
@section('title', 'Trial Balance Report')

@section('pageCSS')
    <style>
        tr.level_1>td:first-child,
        tr.level_1>td:nth-child(2){
            font-size: 15px;
        }
        tr.level_1>td{
            font-weight: bold !important;
        }
        tr.level_2>td:first-child,
        tr.level_2>td:nth-child(2){
            font-size: 13px;
        }
        tr.level_2>td{
            font-weight: bold !important;
        }
        tr.level_3>td:first-child,
        tr.level_3>td:nth-child(2){
            font-size: 12px;
        }
        tr.level_3>td{
            font-weight: bold !important;
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
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date']))." "}}</span>
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
                @if(!empty($data['chart_account']) && isset($data['chart_account']))
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Chart Code:</span>
                    <span style="color: #5578eb;">{{" ".$data['chart_account']->chart_code." - " .ucfirst(strtolower($data['chart_account']->chart_name))." "}}</span>
                </h6>
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
            $colspan = 4;
            $chart_code="";
            if(!empty($data['chart_account']) && isset($data['chart_account']))
            {
                $chart_data = explode ("-",  $data['chart_account']->chart_code);
                $chartPrefix = $chart_data[0] . "-%";
                
                if($data['chart_account_level'] == 1){
                    $chartPrefix = $chartPrefix;
                }
                if($data['chart_account_level'] == 2){
                    $chartPrefix = $chart_data[0]."-".$chart_data[1] . "-%";
                }
                if($data['chart_account_level'] == 3){
                    $chartPrefix = $chart_data[0]."-".$chart_data[1]."-".$chart_data[2] . "-%";
                }
                if($data['chart_account_level'] == 4){
                    $chartPrefix = $data['chart_account']->chart_code;
                }

                $chart_code = "and coa.chart_code like '".$chartPrefix."' ";
            }
            


            $where = '';
            $level_list="";
            if(!empty($data['date']) && isset($data['date'])){
                $where .= "and v.voucher_date <= to_date('".$data['date']."', 'yyyy/mm/dd')";
            }
            if(!empty($data['level_list']) && isset($data['level_list'])){
                $where .= "and coa.chart_level = '".$data['level_list']."' ";
                $level_list = "and trial.chart_level = '".$data['level_list']."' ";
            }

            if($data['OrderBy'] == "code")
            {
                $OrderBy = 'abc.chart_code, abc.chart_level';
            }
            if($data['OrderBy'] == "name")
            {
                $OrderBy = 'abc.chart_name_sorting, abc.chart_level';
            }

/*
            $query = "select trial.business_id,trial.company_id,trial.branch_id,trial.chart_level,ca.chart_account_id,trial.chart_code,ca.chart_name, trial.bal,
                (case  when	trial.bal > 0 then trial.bal else 0 end ) debit ,
                (case when     trial.bal < 0   then   trial.bal  * -1   else 0 end) credit
                from ( select distinct 4 chart_level,v.business_id,v.company_id,v.branch_id,coa.chart_code, sum (v.voucher_debit)
                over (partition by v.chart_account_id,v.business_id,v.company_id,v.branch_id) -
                sum (v.voucher_credit)
                over (partition by v.chart_account_id,v.business_id,v.company_id,v.branch_id) bal
                from VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
                where v.chart_account_id = coa.chart_account_id ".$where." 
                union all select distinct 3 chart_level,v.business_id,v.company_id,v.branch_id,
                (substr (coa.parent_account_code, 0, 7) || '-0000') level2,
                sum (v.voucher_debit) over (partition by substr (coa.parent_account_code, 0, 7),
                v.business_id,v.company_id,v.branch_id) - sum (v.voucher_credit)
                over (partition by substr (coa.parent_account_code, 0, 7),v.business_id,v.company_id,v.branch_id) level2_bal
                from VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
                where v.chart_account_id = coa.chart_account_id ".$where." 
                union all select distinct 2 chart_level, v.business_id, v.company_id, v.branch_id,
                (substr (coa.parent_account_code, 0, 4) || '-00-0000') level2,
                sum (v.voucher_debit) over (partition by substr (coa.parent_account_code, 0, 4),
                v.business_id, v.company_id, v.branch_id)  - sum (v.voucher_credit)
                over (partition by substr (coa.parent_account_code, 0, 4),  v.business_id, v.company_id, v.branch_id) level2_bal
                from VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
                where v.chart_account_id = coa.chart_account_id ".$where." 
                union all select distinct 1 chart_level, v.business_id, v.company_id, v.branch_id,
                (substr (coa.parent_account_code, 0, 1) || '-00-00-0000')    level1,
                sum (v.voucher_debit) over (partition by substr (coa.parent_account_code, 0, 1),  v.business_id, v.company_id, v.branch_id) - sum (v.voucher_credit)
                over (partition by substr (coa.parent_account_code, 0, 1), v.business_id, v.company_id, v.branch_id) level1_bal
                from VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
                where v.chart_account_id = coa.chart_account_id ".$where." )  trial,
                tbl_acco_chart_account ca
                where  trial.chart_code = ca.chart_code(+) order by trial.chart_code , trial.chart_level";

                */

  /* $qry = "SELECT trial.business_id, trial.company_id, trial.chart_level, ca.chart_account_id,
  trial.chart_code,ca.chart_name,trial.bal,
  (
    CASE
      WHEN trial.bal > 0
      THEN trial.bal
      ELSE 0
    END
  ) debit,
  (
    CASE
      WHEN trial.bal < 0
      THEN trial.bal * - 1
      ELSE 0
    END
  ) credit
FROM
  (SELECT DISTINCT
    4 chart_level,
    v.business_id,
    v.company_id,
    coa.chart_code,
    SUM(v.voucher_debit) over (
      PARTITION BY v.chart_account_id,
      v.business_id,
      v.company_id
    ) - SUM(v.voucher_credit) over (
      PARTITION BY v.chart_account_id,
      v.business_id,
      v.company_id
    ) bal
  FROM
    VW_ACCO_VOUCHER_POSTED v,
    tbl_acco_chart_account coa
  WHERE v.chart_account_id = coa.chart_account_id
    AND v.branch_id IN (".implode(",",$data['branch_ids']).")
    $chart_code
    AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
  UNION
  ALL
  SELECT DISTINCT
    3 chart_level,
    v.business_id,
    v.company_id,
    (
      SUBSTR(coa.parent_account_code, 0, 7) || '-0000'
    ) level2,
    SUM(v.voucher_debit) over (
      PARTITION BY SUBSTR(coa.parent_account_code, 0, 7),
      v.business_id,
      v.company_id
    ) - SUM(v.voucher_credit) over (
      PARTITION BY SUBSTR(coa.parent_account_code, 0, 7),
      v.business_id,
      v.company_id
    ) level2_bal
  FROM
    VW_ACCO_VOUCHER_POSTED v,
    tbl_acco_chart_account coa
  WHERE v.chart_account_id = coa.chart_account_id
    AND v.branch_id IN (".implode(",",$data['branch_ids']).")
    $chart_code
    AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
  UNION
  ALL
  SELECT DISTINCT
    2 chart_level,
    v.business_id,
    v.company_id,
    (
      SUBSTR(coa.parent_account_code, 0, 4) || '-00-0000'
    ) level2,
    SUM(v.voucher_debit) over (
      PARTITION BY SUBSTR(coa.parent_account_code, 0, 4),
      v.business_id,
      v.company_id
    ) - SUM(v.voucher_credit) over (
      PARTITION BY SUBSTR(coa.parent_account_code, 0, 4),
      v.business_id,
      v.company_id
    ) level2_bal
  FROM
    VW_ACCO_VOUCHER_POSTED v,
    tbl_acco_chart_account coa
  WHERE v.chart_account_id = coa.chart_account_id
    AND v.branch_id IN (".implode(",",$data['branch_ids']).")
    $chart_code
    AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
  UNION
  ALL
  SELECT DISTINCT
    1 chart_level,
    v.business_id,
    v.company_id,
    (
      SUBSTR(coa.parent_account_code, 0, 1) || '-00-00-0000'
    ) level1,
    SUM(v.voucher_debit) over (
      PARTITION BY SUBSTR(coa.parent_account_code, 0, 1),
      v.business_id,
      v.company_id
    ) - SUM(v.voucher_credit) over ( PARTITION BY SUBSTR(coa.parent_account_code, 0, 1), v.business_id, v.company_id ) level1_bal
  FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
  WHERE v.chart_account_id = coa.chart_account_id
    AND v.branch_id IN (".implode(",",$data['branch_ids']).")
    $chart_code
    AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')) trial,
  tbl_acco_chart_account ca 
  WHERE trial.chart_code = ca.chart_code (+)
  $level_list
   ORDER BY $OrderBy";

   */


   
$qry = "select 
    company_id,
    chart_level,
    chart_account_id,
    chart_code,
    chart_name,
    chart_name_sorting,
    bal,
    debit,
    credit
from 
(
SELECT trial.business_id,
         trial.company_id,
         trial.chart_level,
         ca.chart_account_id,
         trial.chart_code,
         ca.chart_name,
         trial.chart_code as chart_name_sorting,
         trial.bal,
         (CASE WHEN trial.bal > 0 THEN trial.bal ELSE 0 END)          debit,
         (CASE WHEN trial.bal < 0 THEN trial.bal * -1 ELSE 0 END)     credit
    FROM (SELECT DISTINCT
                 4                                       chart_level,
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
                                        v.company_id)    bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
          UNION ALL
          SELECT DISTINCT
                 3                                                      chart_level,
                 v.business_id,
                 v.company_id,
                 (SUBSTR (coa.parent_account_code, 0, 7) || '-0000')    level2,
                   SUM (v.voucher_debit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 7),
                                        v.business_id,
                                        v.company_id)
                 - SUM (v.voucher_credit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 7),
                                        v.business_id,
                                        v.company_id)                   level2_bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
          UNION ALL
          SELECT DISTINCT
                 2
                     chart_level,
                 v.business_id,
                 v.company_id,
                 (SUBSTR (coa.parent_account_code, 0, 4) || '-00-0000')
                     level2,
                   SUM (v.voucher_debit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 4),
                                        v.business_id,
                                        v.company_id)
                 - SUM (v.voucher_credit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 4),
                                        v.business_id,
                                        v.company_id)
                     level2_bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
          UNION ALL
          SELECT DISTINCT
                 1
                     chart_level,
                 v.business_id,
                 v.company_id,
                 (SUBSTR (coa.parent_account_code, 0, 1) || '-00-00-0000')
                     level1,
                   SUM (v.voucher_debit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 1),
                                        v.business_id,
                                        v.company_id)
                 - SUM (v.voucher_credit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 1),
                                        v.business_id,
                                        v.company_id)
                     level1_bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
                ) trial,
         tbl_acco_chart_account ca
   WHERE trial.chart_code = ca.chart_code(+) and trial.chart_level < 4
    UNION ALL
  SELECT trial.business_id,
         trial.company_id,
         trial.chart_level,
         ca.chart_account_id,
         trial.chart_code,
         ca.chart_name,
         concat(concat(substr(trial.chart_code,0,7),'-'),ca.chart_name) as chart_name_sorting,
         trial.bal,
         (CASE WHEN trial.bal > 0 THEN trial.bal ELSE 0 END)          debit,
         (CASE WHEN trial.bal < 0 THEN trial.bal * -1 ELSE 0 END)     credit
    FROM (SELECT DISTINCT
                 4                                       chart_level,
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
                                        v.company_id)    bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
          UNION ALL
          SELECT DISTINCT
                 3                                                      chart_level,
                 v.business_id,
                 v.company_id,
                 (SUBSTR (coa.parent_account_code, 0, 7) || '-0000')    level2,
                   SUM (v.voucher_debit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 7),
                                        v.business_id,
                                        v.company_id)
                 - SUM (v.voucher_credit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 7),
                                        v.business_id,
                                        v.company_id)                   level2_bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
          UNION ALL
          SELECT DISTINCT
                 2
                     chart_level,
                 v.business_id,
                 v.company_id,
                 (SUBSTR (coa.parent_account_code, 0, 4) || '-00-0000')
                     level2,
                   SUM (v.voucher_debit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 4),
                                        v.business_id,
                                        v.company_id)
                 - SUM (v.voucher_credit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 4),
                                        v.business_id,
                                        v.company_id)
                     level2_bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
          UNION ALL
          SELECT DISTINCT
                 1
                     chart_level,
                 v.business_id,
                 v.company_id,
                 (SUBSTR (coa.parent_account_code, 0, 1) || '-00-00-0000')
                     level1,
                   SUM (v.voucher_debit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 1),
                                        v.business_id,
                                        v.company_id)
                 - SUM (v.voucher_credit)
                       OVER (
                           PARTITION BY SUBSTR (coa.parent_account_code, 0, 1),
                                        v.business_id,
                                        v.company_id)
                     level1_bal
            FROM VW_ACCO_VOUCHER_POSTED v, tbl_acco_chart_account coa
           WHERE     v.chart_account_id = coa.chart_account_id
                AND v.branch_id IN (".implode(",",$data['branch_ids']).")
                $chart_code
                AND v.VOUCHER_DATE <= to_date('".$data['date']."', 'yyyy/mm/dd')
                )trial,
         tbl_acco_chart_account ca
   WHERE trial.chart_code = ca.chart_code(+) and trial.chart_level = 4
)abc
ORDER BY $OrderBy";


  //dd($qry);
            $list = DB::select($qry);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th>Account Code</th>
                            <th>Account Title</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Credit</th>
                        </tr>
                        @php
                            $level_4_debit = 0;
                            $level_4_credit = 0;
                        @endphp
                        @foreach($list as $accounts)
                            @if($accounts->chart_level == 1)
                                <tr class="level_1">
                                    <td><span style="font-size: 15px; font-weight: bold !important" class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td>{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    @if($accounts->debit != 0)
                                        <td class="right_number" style="font-size: 15px; font-weight: bold !important">{{number_format($accounts->debit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->credit != 0)
                                        <td class="right_number" style="font-size: 15px; font-weight: bold !important">{{number_format($accounts->credit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                </tr>
                            @endif
                            @if($accounts->chart_level == 2)
                                <tr class="level_2">
                                    <td><span style="font-size: 13px; font-weight: bold !important" class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td  style="padding-left: 15px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    @if($accounts->debit != 0)
                                        <td class="right_number" style="font-size: 13px; font-weight: bold !important">{{number_format($accounts->debit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->credit != 0)
                                        <td class="right_number" style="font-size: 13px; font-weight: bold !important">{{number_format($accounts->credit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                </tr>       
                            @endif

                            @if($accounts->chart_level == 3)
                                <tr class="level_3">
                                    <td><span style="font-size: 12px; font-weight: bold !important" class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td style="padding-left: 30px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    @if($accounts->debit != 0)
                                        <td class="right_number" style="font-size: 12px; font-weight: bold !important">{{number_format($accounts->debit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->credit != 0)
                                        <td class="right_number" style="font-size: 12px; font-weight: bold !important">{{number_format($accounts->credit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                </tr>
                            @endif

                            @if($accounts->chart_level == 4)
                                <tr class="level_4">
                                    <td style="padding-left: 25px !important;"><span class="generate_report" data-id="{{$accounts->chart_account_id}}">{{$accounts->chart_code}}</span></td>
                                    <td style="padding-left: 45px !important;">{{ucwords(strtolower($accounts->chart_name))}}</td>
                                    @if($accounts->debit != 0)
                                        <td class="right_number">{{number_format($accounts->debit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                    @if($accounts->credit != 0)
                                        <td class="right_number">{{number_format($accounts->credit,3)}}</td>
                                    @else
                                        <td class="right_number"></td>
                                    @endif
                                </tr>
                                @php
                                    $level_4_debit += $accounts->debit;
                                    $level_4_credit += $accounts->credit;
                                @endphp
                            @endif
                            @if(!empty($data['level_list']) && isset($data['level_list']))
                                @if($data['level_list'] < 4)
                                    @php
                                        $level_4_debit += $accounts->debit;
                                        $level_4_credit += $accounts->credit;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($level_4_credit,3)}}</td>
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
        $(".generate_report").click(function(e){
            var account_id = $(this).data('id');
            // date differnce between 3 month from today
            var from_date = "{{date('d-m-Y', strtotime($data['date']))}}";
            var str = from_date.split('-');
            var to_date = '01'+'-'+ (str[1]-3) +'-'+str[2];

            var formData = {
                report_branch_ids : [{{auth()->user()->branch_id}}],
                chart_account_multiple : [account_id],
                date_to : from_date,
                date_from : "01-01-2000",//to_date,
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



