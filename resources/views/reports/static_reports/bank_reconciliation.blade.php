@extends('layouts.report')
@section('title', 'Reporting')

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
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
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
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th>Date</th>
                            <th>Voucher No#</th>
                            <th>Description</th>
                            <th>Cheque No</th>
                            <th>Cheque Date</th>
                            <th>Cleared Date</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            <th>CR/DR</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>...::: ( OPENING BALANCE ) :::...</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            @php
                                $total_debit = 0;
                                $total_credit =0 ;
                                if($data['opening_balance'] == null){
                                    $opening_balc =  0;
                                }else{
                                    $opening_balc =  $data['opening_balance'];
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
                        @php
                            $where = "( chart_account_id = " . $data['chart_account']->chart_account_id. " )";
                            $where .= ' AND (business_id = ' . auth()->user()->business_id . ' AND branch_id = ' . auth()->user()->branch_id . ')';
                            $query = "Select * from vw_acco_voucher where voucher_date between to_date ('".$data['from_date']."', 'yyyy/mm/dd') and to_date ('".$data['to_date']."', 'yyyy/mm/dd') and ( voucher_debit <> 0 OR  voucher_credit <> 0 ) and bank_rec_posted = 1 and " .$where.'order by voucher_date,voucher_no';
                            $ResultList = \Illuminate\Support\Facades\DB::select($query);
                        @endphp
                        @foreach($ResultList as $key=>$list)
                            @php
                                $path = '';
                                //if($list->voucher_document_id == ''){$path = '/accounts/'.$list->voucher_type.'/print/'.$list->voucher_id;}
                                $path = '/accounts/'.$list->voucher_type.'/print/'.$list->voucher_id;
                            @endphp
                            <tr>
                                <td>{{date('d-m-Y', strtotime(trim(str_replace('/','-',$list->voucher_date))))}}</td>
                                <td><a href="{{$path}}" target="_blank">{{$list->voucher_no}}</a></td>
                                <td>{{$list->voucher_descrip}}</td>
                                <td>{{$list->voucher_mode_no}}</td>
                                @php $chq_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$list->voucher_mode_date)))); @endphp
                                <td>{{($chq_date =='01-01-1970')?'':$chq_date}}</td>
                                @php $chq_clear_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$list->cleared_date)))); @endphp
                                <td>{{($chq_clear_date =='01-01-1970')?'':$chq_clear_date}}</td>
                                <td class="text-right">{{($list->voucher_debit != 0)?number_format($list->voucher_debit,3):""}}</td>
                                <td class="text-right">{{($list->voucher_credit != 0)?number_format($list->voucher_credit,3):""}}</td>
                                <td class="text-right">
                                    @if($list->voucher_debit != 0)
                                        @php
                                            $opening_balc = str_replace(',', '', $opening_balc);
                                            $opening_balc =  $opening_balc + $list->voucher_debit;
                                            $total_debit +=  $list->voucher_debit;
                                        @endphp
                                    @endif

                                    @if($list->voucher_credit != 0)
                                        @php
                                            $opening_balc = str_replace(',', '', $opening_balc);
                                            $opening_balc =  $opening_balc - $list->voucher_credit;
                                            $total_credit += $list->voucher_credit;
                                        @endphp
                                    @endif
                                    @if($opening_balc > 0)
                                        {{number_format($opening_balc,3)}}
                                    @else
                                        {{number_format($opening_balc * (-1),3)}}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($opening_balc > 0)
                                        DR
                                    @else
                                        CR
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="6" class="rep-font-bold">Total ({{$data['currency']->currency_symbol}}):</td>
                            <td class="text-right rep-font-bold">{{number_format($total_debit,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_credit,3)}}</td>
                            <td class="text-right rep-font-bold">
                                @if($opening_balc > 0)
                                    {{number_format($opening_balc,3)}}
                                @else
                                    {{number_format($opening_balc * (-1),3)}}
                                @endif
                            </td>
                            <td class="text-center rep-font-bold">
                                @if($opening_balc > 0)
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



