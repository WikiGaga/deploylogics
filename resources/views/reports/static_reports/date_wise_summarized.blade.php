@extends('layouts.report')
@section('title', 'Date Wise Summarized Ledger')

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
       // dd($data);
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
                @if(!empty($data['chart_account_id']))
                    @php 
                    $chart_detail  = DB::table('tbl_acco_chart_account')->select('chart_code','chart_name')->where('chart_account_id',$data['chart_account_id'])->first();
                    $chart_code = $chart_detail->chart_code;
                    @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Chart Name:</span>
                        <span style="color: #5578eb;">{{$chart_detail->chart_name}}</span>
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        @php
            $where = '';
            $where .= " and voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd')";
            $where .= " and business_id = ".auth()->user()->business_id."";
           
            if(!empty($data['chart_account_id'])){
                $chart_lists  = DB::table('tbl_acco_chart_account')->select('chart_code','chart_name')->where('chart_account_id',$data['chart_account_id'])->first();
                $where .= " and chart_code Like '".$chart_lists->chart_code."'";
            }

            $query = "select distinct 
                VOUCHER_DATE, 
                BRANCH_ID,
                SUM(VOUCHER_DEBIT) AS VOUCHER_DEBIT, 
                SUM(VOUCHER_CREDIT) AS VOUCHER_CREDIT
            from 
                VW_ACCO_VOUCHER 
            where BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                $where
            group by VOUCHER_DATE,BRANCH_ID
            ORDER by VOUCHER_DATE";
            
            //dd($query);

            $Result_List = DB::select($query);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th width="10%" class="text-center">Date</th>
                            <th width="10%" class="text-center">Opening Balance</th>
                            <th width="10%" class="text-center">Debit</th>
                            <th width="10%" class="text-center">Credit</th>
                            <th width="10%" class="text-center">Net</th>
                            <th width="10%" class="text-center">Close Balance</th>
                        </tr>
                        @php
                            $voucher_debit = 0;
                            $voucher_credit = 0;
                            $opening_balance = 0;
                            $net_bal = 0;
                        @endphp
                        @foreach($Result_List as $list)
                            @php
                            $paras = [
                                'chart_account_id' => $data['chart_account_id'],
                                'voucher_date' => date('d-m-Y', strtotime($list->voucher_date)),
                                'branch_ids' => $data['branch_ids'],
                            ];
                            $openBal = App\Library\CoreFunc::acco_opening_bal($paras);
                            
                            // Opening Balance
                            if($openBal >= 0){
                                $opening_balance = number_format($openBal,0).' Dr';
                            }
                            if($openBal < 0){
                                $opening_balance = number_format(abs($openBal),0).' Cr';
                            }

                            // Debit Balance
                            if($list->voucher_debit >= 0){
                                $voucher_debit = number_format($list->voucher_debit,0).' Dr';
                            }
                            if($list->voucher_debit < 0){
                                $voucher_debit = number_format(abs($list->voucher_debit),0).' Cr';
                            }

                            // Credit Balance
                            if($list->voucher_credit >= 0){
                                $voucher_credit = number_format($list->voucher_credit,0).' Dr';
                            }
                            if($list->voucher_credit < 0){
                                $voucher_credit = number_format(abs($list->voucher_credit),0).' Cr';
                            }

                            // Net Balance
                            $net_balance = $list->voucher_debit - $list->voucher_credit;
                            if($net_bal >= 0){
                                $net_bal = number_format($net_balance,0).' Dr';
                            }
                            if($net_bal < 0){
                                $net_bal = number_format(abs($net_balance),0).' Cr';
                            }

                            // Close Balance
                            $close_bal = (abs($openBal) - $net_balance);
                            if($close_bal >= 0){
                                $close_bal = number_format($close_bal,0).' Dr';
                            }
                            if($close_bal < 0){
                                $close_bal = number_format(abs($close_bal),0).' Cr';
                            }
                            @endphp
                            <tr>
                                <td class="text-center">{{date('d-m-Y', strtotime($list->voucher_date))}}</td>
                                <td class="text-right">{{ $opening_balance }}</td>
                                <td class="text-right">{{ $voucher_debit }}</td>
                                <td class="text-right">{{ $voucher_credit }}</td>
                                <td class="text-right">{{ $net_bal }}</td>
                                <td class="text-right">{{ $close_bal }}</td>
                            </tr>
                        @endforeach
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
