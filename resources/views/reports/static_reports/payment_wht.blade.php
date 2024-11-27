@extends('layouts.report')
@section('title', 'Payment/WHT Report')

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
                @if(!empty($data['chart_account']) && isset($data['chart_account']))
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Chart Code:</span>
                    <span style="color: #5578eb;">{{" ".$data['chart_account']->chart_code." - " .ucfirst(strtolower($data['chart_account']->chart_name))." "}}</span>
                </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        
                        $chart_code="";
                        if(!empty($data['chart_account']) && isset($data['chart_account']))
                        {
                            $chart_code = "and VOUCH.chart_code like '".$data['chart_account']->chart_code."' ";
                        }

                        /*$qry = "SELECT 
                            VOUCH.VOUCHER_ID , 
                            VOUCH.VOUCHER_NO , 
                            VOUCH.VOUCHER_DATE , 
                            VOUCH.BRANCH_ID , 
                            VOUCH.BRANCH_NAME , 
                            SUM(VOUCH.VOUCHER_DEBIT) voucher_debit,  
                            (
                                SELECT MAX(CHART_NAME) CHART_NAME FROM  VW_ACCO_VOUCHER VOUCH_BANK 
                                WHERE VOUCH_BANK.VOUCHER_ID =   VOUCH.VOUCHER_ID 
                                AND VOUCHER_GRID_TYPE = 'actual' FETCH FIRST 1 ROWS ONLY  
                            ) PAYMENT_MODE 
                            ,  
                            (
                                SELECT SUM(VOUCHER_CREDIT) WHT_AMOUNT FROM  VW_ACCO_VOUCHER VOUCH_BANK 
                                WHERE VOUCH_BANK.VOUCHER_ID =   VOUCH.VOUCHER_ID 
                                AND VOUCHER_GRID_TYPE = 'deduction'     FETCH FIRST 1 ROWS ONLY  
                            ) WHT_AMOUNT , 
                            ( 
                                SELECT MAX(CHART_NAME) CHART_NAME FROM  VW_ACCO_VOUCHER VOUCH_BANK 
                                WHERE VOUCH_BANK.VOUCHER_ID =   VOUCH.VOUCHER_ID 
                                AND VOUCHER_GRID_TYPE = 'vendor' FETCH FIRST 1 ROWS ONLY  
                            ) ACC_HEAD_DEBITED,
                            SUP.SUPPLIER_NTN_NO SUPPLIER_NTN_NO 
                        FROM 
                            VW_ACCO_VOUCHER VOUCH ,
                            TBL_PURC_SUPPLIER SUP
                        WHERE VOUCH.CHART_NAME = SUP.SUPPLIER_NAME 
                            AND UPPER(VOUCH.VOUCHER_TYPE) IN ('PV','PVE')  
                            and VOUCH.BRANCH_ID in(".implode(",",$data['branch_ids']).")
                            and VOUCH.VOUCHER_DATE between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')
                            $chart_code
                        GROUP BY VOUCH.VOUCHER_ID , VOUCH.VOUCHER_NO , VOUCH.VOUCHER_DATE , VOUCH.BRANCH_ID , VOUCH.BRANCH_NAME, SUP.SUPPLIER_NTN_NO  
                        ORDER BY VOUCH.VOUCHER_DATE, VOUCH.VOUCHER_NO";*/
        
                    $qry = "SELECT 
  VOUCH.VOUCHER_ID,
  VOUCH.VOUCHER_NO,
  VOUCH.VOUCHER_DATE,
  VOUCH.BRANCH_ID,
  VOUCH.BRANCH_NAME,
  SUM(VOUCH.VOUCHER_DEBIT) voucher_debit,
  (SELECT 
    MAX(CHART_NAME) CHART_NAME 
  FROM
    VW_ACCO_VOUCHER VOUCH_BANK 
  WHERE VOUCH_BANK.VOUCHER_ID = VOUCH.VOUCHER_ID 
    AND VOUCHER_GRID_TYPE = 'actual' FETCH FIRST 1 ROWS ONLY) PAYMENT_MODE,
  (SELECT 
    SUM(VOUCHER_CREDIT) WHT_AMOUNT 
  FROM
    VW_ACCO_VOUCHER VOUCH_BANK 
  WHERE VOUCH_BANK.VOUCHER_ID = VOUCH.VOUCHER_ID 
    AND VOUCHER_GRID_TYPE = 'deduction' FETCH FIRST 1 ROWS ONLY) WHT_AMOUNT,
  (SELECT 
    MAX(CHART_NAME) CHART_NAME 
  FROM
    VW_ACCO_VOUCHER VOUCH_BANK 
  WHERE VOUCH_BANK.VOUCHER_ID = VOUCH.VOUCHER_ID 
    AND VOUCHER_GRID_TYPE = 'vendor' FETCH FIRST 1 ROWS ONLY) ACC_HEAD_DEBITED,
  SUP.SUPPLIER_NTN_NO SUPPLIER_NTN_NO,
  (SELECT 
    MAX(CHART_NAME) CHART_NAME 
  FROM
    VW_ACCO_VOUCHER VOUCH_BANK 
  WHERE VOUCH_BANK.VOUCHER_ID = VOUCH.VOUCHER_ID 
    AND VOUCHER_GRID_TYPE = 'vendor' FETCH FIRST 1 ROWS ONLY) ACC_HEAD_DEBITED,
  SUP.SUPPLIER_TYPE_NAME -- 
FROM
  VW_ACCO_VOUCHER VOUCH,
  VW_PURC_SUPPLIER SUP -- 
WHERE VOUCH.CHART_NAME = SUP.SUPPLIER_NAME 
    AND UPPER(VOUCH.VOUCHER_TYPE) IN ('PV', 'PVE') 
    and VOUCH.BRANCH_ID in(".implode(",",$data['branch_ids']).")
    and VOUCH.VOUCHER_DATE between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')
    $chart_code 
GROUP BY VOUCH.VOUCHER_ID,
  VOUCH.VOUCHER_NO,
  VOUCH.VOUCHER_DATE,
  VOUCH.BRANCH_ID,
  VOUCH.BRANCH_NAME,
  SUP.SUPPLIER_NTN_NO,
  SUP.SUPPLIER_TYPE_NAME --
ORDER BY VOUCH.VOUCHER_DATE,
  VOUCH.VOUCHER_NO ";

                    
           //dd($qry);    
                        
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                       //  dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->branch_name][] = $row;
                        }
                       //dd($list);
                        @endphp
                        @php
                            $si_grand_total_amount = 0;
                        @endphp
                        <table width="100%" id="rep_sales_discount_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-center">V. No.</th>
                                <th class="text-center">Voucher Date</th>
                                <th class="text-left">Branch</th>
                                <th class="text-left">Payment Mode</th>
                                <th class="text-center">Gross Amount</th>
                                <th class="text-center">W.H.T Amount</th>
                                <th class="text-center">Net Amount</th>
                                <th class="text-left">Acc. Head Debited</th>
                                <th class="text-left">Vendor Group</th>
                                <th class="text-center">Payee NTN</th>
                            </tr>
                            @php
                                $gtotdiscamnt = 0;
                                $gtotinvdiscamnt = 0;
                                $gtotnetamnt = 0;
                                $branch_name="";
                            @endphp
                            @foreach($list as $branch_keys=>$branch__row)
                                @php
                                    $branch_name = ucwords(strtolower($branch_keys));
                                @endphp
                                <tr>
                                    <td colspan="15"><b style="color:crimson">Company Branch: {{ucwords(strtolower($branch_keys))}}</b></td>
                                </tr>
                                @php
                                    $ki=1;
                                    $totdiscamnt = 0;
                                    $totinvdiscamnt = 0;
                                    $totnetamnt = 0;
                                @endphp
                                @foreach($branch__row as $inv_k=>$si_detail)
                                    @if($si_detail->wht_amount > 0)
                                        @php
                                            $net_amount = $si_detail->voucher_debit - $si_detail->wht_amount;

                                            $totdiscamnt = $totdiscamnt + $si_detail->voucher_debit;
                                            $totinvdiscamnt = $totinvdiscamnt + $si_detail->wht_amount;
                                            $totnetamnt = $totnetamnt + $net_amount;
                                            
                                            $gtotdiscamnt = $gtotdiscamnt + $si_detail->voucher_debit;
                                            $gtotinvdiscamnt = $gtotinvdiscamnt + $si_detail->wht_amount;
                                            $gtotnetamnt = $gtotnetamnt + $net_amount;
                                            
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{$ki}}</td>
                                                <td class="text-center">{{$si_detail->voucher_no}}</td>
                                                <td class="text-center">{{date('d-m-Y', strtotime($si_detail->voucher_date))}}</td>
                                                <td class="text-left">{{$si_detail->branch_name}}</td>
                                                <td class="text-left">{{$si_detail->payment_mode}}</td>
                                                <td class="text-right">{{number_format($si_detail->voucher_debit,0)}}</td>
                                                <td class="text-right">{{number_format($si_detail->wht_amount,0)}}</td>
                                                <td class="text-right">{{number_format($net_amount,0)}}</td>
                                                <td class="text-left">{{$si_detail->acc_head_debited}}</td>
                                                <td class="text-left">{{$si_detail->supplier_type_name}}</td>
                                                <td class="text-center">{{$si_detail->supplier_ntn_no}}</td>
                                            </tr>
                                        @php
                                            $ki += 1;
                                        @endphp
                                    @endif
                                @endforeach
                                    <tr>
                                        <td colspan="5" class="text-right"><strong style="color:#5578eb">Total: </strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($totdiscamnt,0)}}
                                            </strong>
                                        </td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($totinvdiscamnt,0)}}
                                            </strong>
                                        </td>
                                        <td class="text-right"><strong>{{number_format($totnetamnt,0)}}</strong></td>
                                        <td class="text-right" colspan="5"><strong></strong></td>
                                    </tr>
                            @endforeach
                                <tr>
                                    <td colspan="5" class="text-right"><strong style="color:crimson">{{$branch_name}} Total: </strong></td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($gtotdiscamnt,0)}}
                                        </strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($gtotinvdiscamnt,0)}}
                                        </strong>
                                    </td>
                                    <td class="text-right"><strong>{{number_format($gtotnetamnt,0)}}</strong></td>
                                    <td class="text-right" colspan="5"><strong></strong></td>
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
{{-- @section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_sales_discount_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
