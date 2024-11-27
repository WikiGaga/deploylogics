@extends('layouts.report')
@section('title', 'Sale Register Report')

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
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $where = "";
                        $qry = "SELECT 
                            BRANCH_ID,
                            BRANCH_NAME,
                            SALES_DATE,
                            SALES_SALES_MAN,
                            SALES_SALES_MAN_NAME,
                            CUSTOMER_NAME,
                            CASE
                                WHEN SALES_TYPE = 'POS' 
                                THEN 'Sale Invoice' 
                                WHEN SALES_TYPE = 'RPOS' 
                                THEN 'Sale Return' 
                                ELSE ''
                            END AS SALES_TYPE,
                            SALES_ID,
                            SALES_CODE,
                            PRODUCT_NAME,
                            PRODUCT_BARCODE_BARCODE,
                            SALES_DTL_QUANTITY,
                            SALES_DTL_RATE,SALES_DTL_AMOUNT,
                            CASE 
                                WHEN  NVL(EXT_DISC_AMOUNT,0) > 0
                                THEN  NVL(EXT_DISC_AMOUNT,0) /  SALES_DTL_AMOUNT
                                ELSE 0
                                END DISC_PER,
                            CASE 
                                WHEN  NVL(EXT_DISC_AMOUNT,0) > 0
                                THEN  NVL(EXT_DISC_AMOUNT,0) /  SALES_DTL_QUANTITY
                                ELSE 0
                                END PER_ITEM_DISC,
                            SALES_DTL_DISC_AMOUNT TOTAL_ITEM_DISC,
                            EXT_DISC_AMOUNT INV_DISCOUNT,
                            SALES_DTL_NET_AMOUNT-  EXT_DISC_AMOUNT  SALES_DTL_NET_AMOUNT
                        FROM 
                            VW_SALE_SALES_INVOICE
                        where branch_id in (".implode(",",$data['branch_ids']).")
                                and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        ORDER BY  SALES_DATE,SALES_TYPE,SALES_CODE";
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                       //  dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->branch_name][$row->sales_type][$row->customer_name][$row->sales_code][] = $row;
                        }
                       // dd($list);
                        @endphp
                        @php
                            $si_grand_total_amount = 0;
                        @endphp
                        <table width="100%" id="rep_sale_invoice_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-center">Barcode</th>
                                <th class="text-center">Item</th>
                                <th class="text-center">Qty/Wt(Pkt)</th>
                                <th class="text-center">Rate</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Item Discount</th>
                                <th class="text-center">Invoice Discount</th>
                                <th class="text-center">Total</th>
                            </tr>
                            @php
                                $ggtotqty = 0;
                                $ggtotamount = 0;
                                $ggtotdiscamnt = 0;
                                $ggtotinvdiscamnt = 0;
                                $ggtotnetamnt = 0;
                            @endphp
                            @foreach($list as $branch_keys=>$branch__row)
                                @php
                                    $branch_name = ucwords(strtolower($branch_keys));
                                @endphp
                                <tr>
                                    <td colspan="10"><b>Company Branch: {{ucwords(strtolower($branch_keys))}}</b></td>
                                </tr>
                                @php
                                    $gtotqty = 0;
                                    $gtotamount = 0;
                                    $gtotdiscamnt = 0;
                                    $gtotinvdiscamnt = 0;
                                    $gtotnetamnt = 0;
                                @endphp
                                @foreach($branch__row as $sale_type_key=>$sale_type_row)
                                    @php
                                        $sale_type_name = ucwords(strtolower($sale_type_key));
                                    @endphp
                                    <tr>
                                        <td colspan="10"><b>{{ucwords(strtolower($sale_type_key))}}</b></td>
                                    </tr>
                                    @php
                                        $mtotqty = 0;
                                        $mtotamount = 0;
                                        $mtotdiscamnt = 0;
                                        $mtotinvdiscamnt = 0;
                                        $mtotnetamnt = 0;
                                    @endphp
                                    @foreach($sale_type_row as $cust_key=>$cust_detail)
                                        @php
                                            $cust_name = ucwords(strtolower($cust_key));
                                        @endphp
                                        <tr>
                                            <td colspan="10"><b>Customer Name : {{ucwords(strtolower($cust_key))}}</b></td>
                                        </tr>
                                        @php
                                            $stotqty = 0;
                                            $stotamount = 0;
                                            $stotdiscamnt = 0;
                                            $stotinvdiscamnt = 0;
                                            $stotnetamnt = 0;
                                        @endphp
                                        @foreach($cust_detail as $inv_k=>$inv_detail)
                                            <tr>
                                                <td colspan="10">
                                                    <b>Invoice #: </b>{{($inv_k)}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                    <b>Ref #:</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                    <b>User: </b>{{auth()->user()->name}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                 </td>
                                            </tr>
                                            @php
                                                $ki=1;
                                                $totqty = 0;
                                                $totamount = 0;
                                                $totdiscamnt = 0;
                                                $totinvdiscamnt = 0;
                                                $totnetamnt = 0;
                                            @endphp
                                            @foreach($inv_detail as $inv_k=>$si_detail)
                                                @php
                                                    $totqty = $totqty + $si_detail->sales_dtl_quantity;
                                                    $totamount = $totamount + $si_detail->sales_dtl_amount;
                                                    $totdiscamnt = $totdiscamnt + $si_detail->per_item_disc;
                                                    $totinvdiscamnt = $totinvdiscamnt + $si_detail->inv_discount;
                                                    $totnetamnt = $totnetamnt + $si_detail->sales_dtl_net_amount;
                                                    
                                                    $stotqty = $stotqty + $si_detail->sales_dtl_quantity;
                                                    $stotamount = $stotamount + $si_detail->sales_dtl_amount;
                                                    $stotdiscamnt = $stotdiscamnt + $si_detail->per_item_disc;
                                                    $stotinvdiscamnt = $stotinvdiscamnt + $si_detail->inv_discount;
                                                    $stotnetamnt = $stotnetamnt + $si_detail->sales_dtl_net_amount;
                                                    
                                                    $mtotqty = $mtotqty + $si_detail->sales_dtl_quantity;
                                                    $mtotamount = $mtotamount + $si_detail->sales_dtl_amount;
                                                    $mtotdiscamnt = $mtotdiscamnt + $si_detail->per_item_disc;
                                                    $mtotinvdiscamnt = $mtotinvdiscamnt + $si_detail->inv_discount;
                                                    $mtotnetamnt = $mtotnetamnt + $si_detail->sales_dtl_net_amount;
                                                    
                                                    $gtotqty = $gtotqty + $si_detail->sales_dtl_quantity;
                                                    $gtotamount = $gtotamount + $si_detail->sales_dtl_amount;
                                                    $gtotdiscamnt = $gtotdiscamnt + $si_detail->per_item_disc;
                                                    $gtotinvdiscamnt = $gtotinvdiscamnt + $si_detail->inv_discount;
                                                    $gtotnetamnt = $gtotnetamnt + $si_detail->sales_dtl_net_amount;
                                                  
                                                    $ggtotqty = $ggtotqty + $si_detail->sales_dtl_quantity;
                                                    $ggtotamount = $ggtotamount + $si_detail->sales_dtl_amount;
                                                    $ggtotdiscamnt = $ggtotdiscamnt + $si_detail->per_item_disc;
                                                    $ggtotinvdiscamnt = $ggtotinvdiscamnt + $si_detail->inv_discount;
                                                    $ggtotnetamnt = $ggtotnetamnt + $si_detail->sales_dtl_net_amount;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{$ki}}</td>
                                                    <td class="text-left">{{$si_detail->product_barcode_barcode}}</td>
                                                    <td>{{$si_detail->product_name}}</td>
                                                    <td class="text-right">{{$si_detail->sales_dtl_quantity}}</td>
                                                    <td class="text-right">{{$si_detail->sales_dtl_rate}}</td>
                                                    <td class="text-right">{{$si_detail->sales_dtl_amount}}</td>
                                                    <td class="text-right">{{$si_detail->per_item_disc}}</td>
                                                    <td class="text-right">{{$si_detail->inv_discount}}</td>
                                                    <td class="text-right">{{$si_detail->sales_dtl_net_amount}}</td>
                                                </tr>
                                                @php
                                                    $ki += 1;
                                                @endphp
                                            @endforeach
                                                <tr>
                                                    <td colspan="3" class="text-right"><strong>Invoice Wise Total: </strong></td>
                                                    <td class="text-right"><strong>{{number_format($totqty,0)}}</strong></td>
                                                    <td class="text-right"></td>
                                                    <td class="text-right"><strong>{{number_format($totamount,0)}}</strong></td>
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
                                                </tr>
                                        @endforeach
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>{{$cust_name}} Total: </strong></td>
                                                <td class="text-right"><strong>{{number_format($stotqty,0)}}</strong></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"><strong>{{number_format($stotamount,0)}}</strong></td>
                                                <td class="text-right">
                                                    <strong>
                                                        {{number_format($stotdiscamnt,0)}}
                                                    </strong>
                                                </td>
                                                <td class="text-right">
                                                    <strong>
                                                        {{number_format($stotinvdiscamnt,0)}}
                                                    </strong>
                                                </td>
                                                <td class="text-right"><strong>{{number_format($stotnetamnt,0)}}</strong></td>
                                            </tr>
                                    @endforeach
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>{{$sale_type_name}} Total: </strong></td>
                                            <td class="text-right"><strong>{{number_format($mtotqty,0)}}</strong></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><strong>{{number_format($mtotamount,0)}}</strong></td>
                                            <td class="text-right">
                                                <strong>
                                                    {{number_format($mtotdiscamnt,0)}}
                                                </strong>
                                            </td>
                                            <td class="text-right">
                                                <strong>
                                                    {{number_format($mtotinvdiscamnt,0)}}
                                                </strong>
                                            </td>
                                            <td class="text-right"><strong>{{number_format($stotnetamnt,0)}}</strong></td>
                                        </tr>
                                @endforeach
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>{{$branch_name}} Total: </strong></td>
                                        <td class="text-right"><strong>{{number_format($gtotqty,0)}}</strong></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"><strong>{{number_format($gtotamount,0)}}</strong></td>
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
                                    </tr>
                            @endforeach
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Grand Total: </strong></td>
                                    <td class="text-right"><strong>{{number_format($ggtotqty,0)}}</strong></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"><strong>{{number_format($ggtotamount,0)}}</strong></td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($ggtotdiscamnt,0)}}
                                        </strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($ggtotinvdiscamnt,0)}}
                                        </strong>
                                    </td>
                                    <td class="text-right"><strong>{{number_format($ggtotnetamnt,0)}}</strong></td>
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
                $("#rep_sale_invoice_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
