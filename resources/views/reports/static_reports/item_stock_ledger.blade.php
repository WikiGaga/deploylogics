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
        //dd($data);
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
                    <span style="color: #e27d00;">Code:</span>
                    <span style="color: #5578eb;">{{$data['product']->product_id}}</span>
                </h6>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Name:</span>
                    <span style="color: #5578eb;">{{$data['product']->product_name}}</span>
                </h6>
            </div>
            @include('reports.template.branding')
        </div>
        @php
            $query = "SELECT DISTINCT 1 data_priority, -- opening Bal
                NULL DOCUMENT_DATE,NULL DOCUMENT_CODE,NULL DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,0 QTY_IN,0 RATE_IN,0 AMOUNT_IN,0 QTY_OUT,0 RATE_OUT,0 AMOUNT_OUT,
                GET_STOCK_CURRENT_QTY_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,SALES_STORE_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) BALANCE_QTY,
                GET_STOCK_AVG_RATE_ON_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) avg_rate,
                GET_STOCK_AVG_RATE_ON_DATE(PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,to_date('".$data['date']."', 'yyyy/mm/dd'))* GET_STOCK_CURRENT_QTY_DATE (PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,SALES_STORE_ID,to_date('".$data['date']."', 'yyyy/mm/dd')) BALANCE_AMOUNT,0 RATE_EFFECT
                FROM VW_PURC_STOCK_DTL WHERE  product_id = '".$data['product']->product_id."'
                UNION ALL SELECT DISTINCT 2 data_priority,DOCUMENT_DATE,DOCUMENT_CODE,DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,QTY_IN,STOCK_RATE rate_in,QTY_IN * STOCK_RATE AMOUNT_IN,0 QTY_OUT,0 RATE_OUT,0 amount_out,0 BALANCE_QTY,0 AVG_RATE,0 BALANCE_AMOUNT,RATE_EFFECT
                FROM VW_PURC_STOCK_DTL WHERE STOCK_CALCULATION_EFFECT = '+' AND document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') AND product_id = '".$data['product']->product_id."'
                UNION ALL SELECT DISTINCT 2 data_priority,DOCUMENT_DATE,DOCUMENT_CODE,DOCUMENT_TYPE,PRODUCT_ID,'',BUSINESS_ID,COMPANY_ID,BRANCH_ID,0 QTY_IN,0 RATE_IN,
                0 amount_in,QTY_OUT,0 RATE_out,0 amount_out,0 BALANCE_QTY,0 average_rate,0 BALANCE_AMOUNT,RATE_EFFECT
                FROM VW_PURC_STOCK_DTL WHERE STOCK_CALCULATION_EFFECT = '-' AND document_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd') AND product_id = '".$data['product']->product_id."' order by  data_priority,document_date ";

            $ResultList = DB::select($query);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th colspan="3"></th>
                            <th colspan="3" class="text-center">IN</th>
                            <th colspan="3" class="text-center">OUT</th>
                            <th colspan="3" class="text-center">Balance</th>
                        </tr>
                        <tr>
                            <th class="text-center">Doc Date</th>
                            <th class="text-center">Doc Code</th>
                            <th class="text-center">Doc Type</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Amount</th>
                        </tr>
                        @php
                            $grand_total_QtyIn =0;
                            $grand_total_AmtIn =0;
                            $grand_total_QtyOut =0;
                            $grand_total_AmtOut =0;
                            $tot_qty = 0;
                            $rate = 0;
                            $tot_amount = 0;
                        @endphp
                        @foreach($ResultList as $key=>$list)
                            @if($list->data_priority == 1)
                                <tr>
                                    <td colspan="9" class="rep-font-bold">Opening Quantity :</td>
                                    <td class="text-right rep-font-bold">{{$list->balance_qty}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($list->avg_rate,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($list->balance_amount,3)}}</td>
                                </tr>
                                @php
                                    $rate = $list->avg_rate;
                                    $tot_qty = $list->balance_qty;
                                    $tot_amount = $list->balance_amount;
                                @endphp
                            @else
                                @php
                                    if($list->rate_effect == 1){
                                        $rate_in = $list->rate_in;
                                        $amount_in = $list->amount_in;

                                        $rate_out = $list->rate_out;
                                        $amount_out = $list->amount_out;
                                    }else{
                                        if($list->qty_in > 0){
                                            $rate_in = $rate;
                                            $amount_in = $list->qty_in * $rate_in;
                                        }else{
                                            $rate_in = $list->rate_in;
                                            $amount_in = $list->amount_in;
                                        }

                                        if($list->qty_out > 0){
                                            $rate_out = $rate;
                                            $amount_out = $list->qty_out * $rate_out;
                                        }else{
                                            $rate_out = $list->rate_out;
                                            $amount_out = $list->amount_out;
                                        }
                                    }

                                @endphp
                                <tr>
                                    <td>{{date('d-m-Y', strtotime(trim(str_replace('/','-',$list->document_date))))}}</td>
                                    <td>{{$list->document_code}}</td>
                                    <td class="text-center">{{$list->document_type}}</td>
                                    <td class="text-right">{{number_format($list->qty_in,0)}}</td>
                                    <td class="text-right">{{number_format($rate_in,3)}}</td>
                                    <td class="text-right">{{number_format($amount_in,3)}}</td>
                                    <td class="text-right">{{number_format($list->qty_out,0)}}</td>
                                    <td class="text-right">{{number_format($rate_out,3)}}</td>
                                    <td class="text-right">{{number_format($amount_out,3)}}</td>
                                    @php
                                        $tot_qty = $tot_qty + $list->qty_in - $list->qty_out;
                                        $tot_amount = $tot_amount + $amount_in - $amount_out;
                                        if($tot_qty > 0){
                                        $rate = $tot_amount / $tot_qty;
                                        }else{
                                            $rate = 0;
                                        }

                                        $grand_total_QtyIn += $list->qty_in;
                                        $grand_total_AmtIn += $amount_in;
                                        $grand_total_QtyOut += $list->qty_out;
                                        $grand_total_AmtOut += $amount_out;
                                    @endphp
                                    <td class="text-right">{{number_format($tot_qty,0)}}</td>
                                    <td class="text-right">{{number_format($rate,3)}}</td>
                                    <td class="text-right">{{number_format($tot_amount,3)}}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="3" class="rep-font-bold">Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_QtyIn,0)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_AmtIn,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_QtyOut,0)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_AmtOut,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($tot_qty,0)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($rate,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($tot_amount,3)}}</td>
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



