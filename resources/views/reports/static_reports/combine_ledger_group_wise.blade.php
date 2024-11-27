@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>
        .level1-title{padding-left: 0em!important; font-size: 18px;color: green!important;}
        .level2-title{padding-left: 0.5em!important; font-size: 16px;color: orange!important;}
        .level3-title{padding-left: 1.5em!important; font-size: 17px; color: #5578eb!important;}
        .level4-title{padding-left: 2em!important; font-size: 15px; color: red!important;}
        .level5-title{padding-left: 2.5em!important; font-size: 15px;}
        .title{
            font-weight: bold;
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
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Chart Code:</span>
                    <span style="color: #5578eb;">{{" ".$data['chart_account']->chart_code." - " .ucfirst(strtolower($data['chart_account']->chart_name))." "}}</span>
                </h6>
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $colspan = 4;
                        $grand_debit = $grand_credit = $grand_balance = 0;
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
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <thead>
                            <tr class="sticky-header">
                                <th style="width: 10%;">Date</th>
                                <th style="width: 10%;">Voucher</th>
                                <th style="width: 60%;">Account</th>
                                <th style="width: 5%;">Debit</th>                               
                                <th style="width: 5%;">Credit</th>
                                <th style="width: 5%;">Balance</th>
                                <th style="width: 5%;">CR/DR</th>
                            </tr>
                        </thead>
                        @php
                            $total_debit = 0;
                            $total_credit =0 ;
                            $sub_opening_balc = 0;
                            $opening_balc = 0;
                        @endphp
                        {{-- Opening Balance --}}
                        @if(isset($data['chart_account']) && $data['chart_account']->chart_level == 1)
                            @php
                                $lv1chartPrefix = $chart_data[0];
                                $level1Criteria = " AND (VW_ACCO_VOUCHER.business_id = ".auth()->user()->business_id." AND VW_ACCO_VOUCHER.branch_id in (".implode(",",$data['branch_ids']).") )";
                                $level1Query = "select sum(VOUCHER_DEBIT) DEBIT_TOT, sum(VOUCHER_CREDIT)  CREDIT_TOT , sum(VOUCHER_DEBIT)- sum(VOUCHER_CREDIT) BALANCE_TOT  from VW_ACCO_VOUCHER where 
                                CHART_CODE LIKE '".$lv1chartPrefix."-%' $level1Criteria AND VOUCHER_DATE BETWEEN to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                                $level1Calc = \Illuminate\Support\Facades\DB::select($level1Query)[0];
                                $opening_balc = 0;
                                $paras = [
                                    'chart_account_id' => $data['chart_account']->chart_account_id,
                                    'voucher_date' => $data['from_date'],
                                    'branch_ids' => $data['branch_ids'],
                                ];
                                $lvl1OB = \App\Library\CoreFunc::acco_opening_bal($paras);
                            @endphp
                            <tr>
                                <th></th>
                                <th></th>
                                <th class="title level1-title">{{ $data['chart_account']->chart_code }} {{ $data['chart_account']->chart_name }}</th>    
                                <th class="text-right">{{ $level1Calc->debit_tot }}</th>
                                <th class="text-right">{{ $level1Calc->credit_tot }}</th>
                                <th class="text-right">{{ $level1Calc->balance_tot }}</th>
                                <th class="text-center">
                                    @if($lvl1OB > 0)
                                        DR
                                    @else
                                        CR
                                    @endif
                                </th>
                            </tr>
                            @if($data['opening_bal_toggle'] == 1)
                            <tr>
                                <th></th>
                                <th class="title level1-title">OPENING BALANCE</th>
                                <th></th>
                                <th></th>
                                @php
                                    if($lvl1OB == null){
                                        $opening_balc =  0;
                                    }else{
                                        $opening_balc =  $lvl1OB;
                                    }
                                @endphp
                                <th class="text-right">
                                    @if($opening_balc > 0)
                                        {{number_format($opening_balc,3)}}
                                    @else
                                        {{number_format($opening_balc * (-1),3)}}
                                    @endif
                                </th>
                                <th></th>
                                <td class="text-center">
                                    @if($opening_balc > 0)
                                        DR
                                    @else
                                        CR
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endif
                        @php
                            $query = "SELECT DISTINCT COA.CHART_ACCOUNT_ID,
                                COA.CHART_NAME,
                                COA.CHART_CODE,
                                COA.CHART_ACCOUNT_ENTRY_STATUS,
                                COA.CHART_GROUP,
                                COA.CHART_LEVEL,
                                COA.chart_reference_code,
                                COA.BUSINESS_ID,
                                B.BUSINESS_NAME,
                                COA.COMPANY_ID,
                                C.COMPANY_NAME,
                                COA.BRANCH_ID,
                                BR.BRANCH_NAME,
                                COA.CHART_CAN_PURCHASE,
                                COA.CHART_CAN_SALE ,
                                COA.CHART_ACCOUNT_USER_ID,
                                COA.UPDATED_AT,
                                COA.CREATED_AT
                                FROM TBL_ACCO_CHART_ACCOUNT  COA,
                                    TBL_SOFT_BUSINESS       B,
                                    TBL_SOFT_COMPANY        C,
                                    TBL_SOFT_BRANCH         BR
                                WHERE     COA.BUSINESS_ID = B.BUSINESS_ID
                                    AND COA.COMPANY_ID = C.COMPANY_ID
                                    AND COA.BRANCH_ID = BR.BRANCH_ID
                                    AND COA.CHART_ACCOUNT_ENTRY_STATUS = 1
                                    AND COA.CHART_LEVEL > 1 
                                    and COA.CHART_CODE like '".$chartPrefix."'
                                    order by COA.CHART_CODE";
                            $ResultList = \Illuminate\Support\Facades\DB::select($query);
                        @endphp
                        @foreach($ResultList as $result)
                            @if($result->chart_level == 2)
                                @php
                                    $lv2chartPrefix = $chart_data[0]."-".$chart_data[1];
                                    $level2Criteria = " AND (VW_ACCO_VOUCHER.business_id = ".auth()->user()->business_id." AND VW_ACCO_VOUCHER.branch_id in (".implode(",",$data['branch_ids']).") )";
                                    $level2Query = "select sum(VOUCHER_DEBIT) DEBIT_TOT, sum(VOUCHER_CREDIT)  CREDIT_TOT , sum(VOUCHER_DEBIT)- sum(VOUCHER_CREDIT) BALANCE_TOT  from VW_ACCO_VOUCHER where 
                                    CHART_CODE LIKE '".$lv2chartPrefix."-%' $level2Criteria AND VOUCHER_DATE BETWEEN to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                                    $level2Calc = \Illuminate\Support\Facades\DB::select($level2Query)[0];
                                    $opening_balc = 0;
                                    $paras = [
                                        'chart_account_id' => $result->chart_account_id,
                                        'voucher_date' => $data['from_date'],
                                        'branch_ids' => $data['branch_ids'],
                                    ];
                                    $lvl2OB = \App\Library\CoreFunc::acco_opening_bal($paras);
                                @endphp
                                <tr>
                                    <th></th>
                                    <td></td>
                                    <th class="title level2-title">{{ $result->chart_code }} {{ $result->chart_name }}</th>    
                                    <th class="text-right">{{ $level2Calc->debit_tot }}</th>
                                    <th class="text-right">{{ $level2Calc->credit_tot }}</th>
                                    <th class="text-right">{{ $level2Calc->balance_tot }}</th>
                                    <td class="text-center">
                                        @if($lvl2OB > 0)
                                            DR
                                        @else
                                            CR
                                        @endif
                                    </td>
                                </tr>
                                @if($data['opening_bal_toggle'] == 1)
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="title level2-title">OPENING BALANCE</th>
                                        <th></th>
                                        <th></th>
                                        @php
                                            if($lvl2OB == null){
                                                $opening_balc =  0;
                                            }else{
                                                $opening_balc =  $lvl2OB;
                                            }
                                        @endphp
                                        <th class="text-right">
                                            @if($opening_balc > 0)
                                                {{number_format($opening_balc,3)}}
                                            @else
                                                {{number_format($opening_balc * (-1),3)}}
                                            @endif
                                        </th>
                                        <th class="text-center">
                                            @if($opening_balc > 0)
                                                DR
                                            @else
                                                CR
                                            @endif
                                        </th>
                                    </tr>
                                @endif
                            @endif
                            @if($result->chart_level == 3)
                                <tr>
                                    @php
                                        $lv3chartPrefix = $chart_data[0]."-".$chart_data[1];
                                        $level3Criteria = " AND (VW_ACCO_VOUCHER.business_id = ".auth()->user()->business_id." AND VW_ACCO_VOUCHER.branch_id in (".implode(",",$data['branch_ids']).") )";
                                        $level3Query = "select sum(VOUCHER_DEBIT) DEBIT_TOT, sum(VOUCHER_CREDIT)  CREDIT_TOT , sum(VOUCHER_DEBIT)- sum(VOUCHER_CREDIT) BALANCE_TOT  from VW_ACCO_VOUCHER where 
                                        CHART_CODE LIKE '".$lv3chartPrefix."-%' $level3Criteria AND VOUCHER_DATE BETWEEN to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
                                        $level3Calc = \Illuminate\Support\Facades\DB::select($level3Query)[0];
                                        $opening_balc = 0;
                                        $paras = [
                                            'chart_account_id' => $result->chart_account_id,
                                            'voucher_date' => $data['from_date'],
                                            'branch_ids' => $data['branch_ids'],
                                        ];
                                        $lvl3OB = \App\Library\CoreFunc::acco_opening_bal($paras);
                                    @endphp
                                    <th></th>
                                    <th></th>
                                    <th class="title level3-title">{{ $result->chart_code }} {{ $result->chart_name }}</th>    
                                    <th class="text-right">{{ $level3Calc->debit_tot }}</th>
                                    <th class="text-right">{{ $level3Calc->credit_tot }}</th>
                                    <th class="text-right">{{ $level3Calc->balance_tot }}</th>
                                    <th class="text-center">
                                        @if($lvl3OB > 0)
                                            DR
                                        @else
                                            CR
                                        @endif
                                    </th>
                                </tr>
                                @if($data['opening_bal_toggle'] == 1)
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="title level3-title">OPENING BALANCE</th>
                                        <th></th>
                                        <th></th>
                                        @php
                                            if($lvl3OB == null){
                                                $opening_balc =  0;
                                            }else{
                                                $opening_balc =  $lvl3OB;
                                            }
                                        @endphp
                                        <th class="text-right">
                                            @if($opening_balc > 0)
                                                {{number_format($opening_balc,3)}}
                                            @else
                                                {{number_format($opening_balc * (-1),3)}}
                                            @endif
                                        </th>
                                        <th class="text-center">
                                            @if($opening_balc > 0)
                                                DR
                                            @else
                                                CR
                                            @endif
                                        </th>
                                    </tr>
                                @endif  
                            @endif
                            @if($result->chart_level == 4)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="level4-title">{{ $result->chart_code }} {{ $result->chart_name }}</td>    
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @php
                                    $level5OpeningBlnc = $total_debit = $total_credit = 0;
                                    $where = "( VOUCH.chart_account_id = " . $result->chart_account_id . " )";
                                    $where .= " AND (VOUCH.business_id = ".auth()->user()->business_id." AND VOUCH.branch_id in (".implode(",",$data['branch_ids']).") )";
                                    $q1 = "Select VOUCH.* from vw_acco_voucher VOUCH,TBL_SOFT_VOUCHER_SQUENCE SEQ
                                    where VOUCH.voucher_TYPE = SEQ.SQUENCE_VOUCHER_TYPE(+) AND
                                    (VOUCH.voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd'))
                                    and ( VOUCH.voucher_debit <> 0 OR  VOUCH.voucher_credit <> 0 ) and " .$where." order by VOUCH.voucher_date,SEQ.SQUENCE_SORTING_ORDER,VOUCH.voucher_sr_no,VOUCH.VOUCHER_NO";
                                    $DetailList = \Illuminate\Support\Facades\DB::select($q1);
                                @endphp
                                @if($data['opening_bal_toggle'] == 1)
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="title level4-title">OPENING BALANCE</th>
                                        <th></th>
                                        <th></th>
                                        @php
                                            $paras = [
                                                'chart_account_id' => $result->chart_account_id,
                                                'voucher_date' => $data['from_date'],
                                                'branch_ids' => $data['branch_ids'],
                                            ];
                                            $level5OpeningBlnc = \App\Library\CoreFunc::acco_opening_bal($paras);
                                            if($level5OpeningBlnc == null) { $level5OpeningBlnc = 0; }
                                            $sub_opening_balc = $level5OpeningBlnc;
                                        @endphp
                                        <th class="text-right">
                                            @if($level5OpeningBlnc > 0)
                                                {{number_format($level5OpeningBlnc,3)}}
                                            @else
                                                {{number_format($level5OpeningBlnc * (-1),3)}}
                                            @endif
                                        </th>
                                        <th class="text-center">
                                            @if($level5OpeningBlnc > 0)
                                                DR
                                            @else
                                                CR
                                            @endif
                                        </th>
                                    </tr>
                                @endif
                                @foreach($DetailList as $key=>$list)
                                    @php
                                        $color = 'black!important';
                                        $print_id = $list->voucher_document_id;
                                        if($list->voucher_document_id == ''){$print_id = $list->voucher_id;}
                                        if($list->voucher_credit > 0) { $color = 'blue!important'; }
                                        if($list->voucher_type == 'cpv' || $list->voucher_type == 'bpv'){ $color = 'red!important'; }
                                        if($list->voucher_type == 'GRN'){ $list->voucher_descrip = str_replace("Purchase:" , "" , $list->voucher_descrip); }
                                        if($list->voucher_type == 'PR'){ $list->voucher_descrip = str_replace("Purchase Return:" , "" , $list->voucher_descrip); }
                                        // Replace Some Words
                                        if($list->voucher_type == 'PR' || $list->voucher_type == 'PO' || $list->voucher_type == 'GRN'){
                                            $list->voucher_descrip = str_replace("-" , "" , $list->voucher_descrip);
                                        }
                                        $list->voucher_descrip = str_replace("Inv.:" , "" , $list->voucher_descrip);
                                    @endphp
                                    <tr>
                                        <td style="color:{{$color}}">{{date('d-m-Y', strtotime(trim(str_replace('/','-',$list->voucher_date))))}}</td>
                                        <td><span style="color:{{$color}}" class="generate_report" data-id="{{$print_id}}" data-type="{{$list->voucher_type}}">{{$list->voucher_no}}</span></td>
                                        <td style="color:{{$color}}" class="level5-title">{{trim($list->voucher_descrip)}}</td>
                                        <td class="text-right" style="color:{{$color}}">{{($list->voucher_debit != 0)?number_format($list->voucher_debit,3):""}}</td>
                                        <td class="text-right" style="color:{{$color}}">{{($list->voucher_credit != 0)?number_format($list->voucher_credit,3):""}}</td>
                                        <td class="text-right" style="color:{{$color}}">
                                            @if($list->voucher_debit != 0)
                                                @php
                                                    $level5OpeningBlnc = str_replace(',', '', $level5OpeningBlnc);
                                                    $level5OpeningBlnc =  $level5OpeningBlnc + $list->voucher_debit;
                                                    $total_debit +=  $list->voucher_debit;
                                                @endphp
                                            @endif

                                            @if($list->voucher_credit != 0)
                                                @php
                                                    $level5OpeningBlnc = str_replace(',', '', $level5OpeningBlnc);
                                                    $level5OpeningBlnc =  $level5OpeningBlnc - $list->voucher_credit;
                                                    $total_credit += $list->voucher_credit;
                                                @endphp
                                            @endif
                                            @if($level5OpeningBlnc > 0)
                                                {{number_format($level5OpeningBlnc,3)}}
                                            @else
                                                {{number_format($level5OpeningBlnc * (-1),3)}}
                                            @endif
                                        </td>
                                        <td class="text-center" style="color:{{$color}}">
                                            @if($level5OpeningBlnc > 0)
                                                DR
                                            @else
                                                CR
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="sub_total">
                                    <td>Sub Total</td>
                                    <td class="rep-font-bold" colspan="2" style="padding-left: 2em;">{{ $result->chart_code }} {{ $result->chart_name }} TOTAL ({{$data['currency']->currency_symbol}}):</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_debit,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_credit,3)}}</td>
                                    <td class="text-right rep-font-bold">
                                        @php 
                                            $sub_opening_balc = $sub_opening_balc + $total_debit - $total_credit;
                                            $grand_debit += $total_debit;
                                            $grand_credit += $total_credit;
                                            $grand_balance += $sub_opening_balc;
                                        @endphp
                                        @if($sub_opening_balc > 0)
                                            {{number_format($sub_opening_balc,3)}}
                                        @else
                                            {{number_format($sub_opening_balc * (-1),3)}}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($sub_opening_balc > 0)
                                            DR
                                        @else
                                            CR
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="grand_total">
                            <td class="text-center rep-font-bold"></td>
                            <td colspan="2" class="rep-font-bold">Closing Total ({{$data['currency']->currency_symbol}}):</td>
                            <td class="text-right rep-font-bold">{{ number_format($grand_debit , 3) }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($grand_credit) }}</td>
                            <td class="text-right rep-font-bold">
                                {{ number_format($grand_balance , 3) }}
                            </td>
                            <td class="text-center rep-font-bold">
                                @if($grand_balance > 0)
                                    DR
                                @else
                                    CR
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
    <script>

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