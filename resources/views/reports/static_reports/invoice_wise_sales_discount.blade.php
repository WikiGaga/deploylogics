@extends('layouts.report')
@section('title', 'Invoice Wise Sales Discount Report')

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
                @if(isset($data['sale_types_multiple']) && count($data['sale_types_multiple']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Sales Type:</span>
                        @foreach($data['sale_types_multiple'] as $sales_type)
                            <span style="color: #5578eb;">{{" ".$sales_type." "}}</span>
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

                        if(count($data['sale_types_multiple']) != 0){
                            $where .= " and GAGA.sales_type in ('".implode("','",$data['sale_types_multiple'])."') ";
                        }

                        $qry = "SELECT GAGA.BRANCH_ID,
                            GAGA.BRANCH_NAME,
                            GAGA.SALES_DATE,
                            GAGA.CREATED_AT,
                            GAGA.SALES_SALES_MAN,
                            GAGA.SALES_SALES_MAN_NAME,
                            GAGA.CUSTOMER_NAME,
                            CASE
                                WHEN GAGA.SALES_TYPE = 'POS' THEN 'Sale Invoice'
                                WHEN GAGA.SALES_TYPE = 'RPOS' THEN 'Sale Return'
                                ELSE ''
                            END AS SALES_TYPE,
                            GAGA.SALES_ID,
                            GAGA.SALES_CODE,
                            SUM (SALES_DTL_DISC_AMOUNT) + SUM (EXT_DISC_AMOUNT) AS ITEM_DISCOUNT,
                            DISC_AMOUNT AS INVOICE_DISCOUNT,
                            SALES_DTL_NET_AMOUNT,
                            USERS.NAME
                        FROM 
                            VW_SALE_SALES_INVOICE GAGA, USERS
                        WHERE GAGA.SALES_USER_ID = USERS.ID
                            AND GAGA.branch_id in (".implode(",",$data['branch_ids']).")
                            AND (GAGA.created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                            $where
                        GROUP BY GAGA.BRANCH_ID,
                            GAGA.BRANCH_NAME,
                            GAGA.SALES_DATE,
                            GAGA.CREATED_AT,
                            GAGA.SALES_SALES_MAN,
                            GAGA.SALES_SALES_MAN_NAME,
                            GAGA.CUSTOMER_NAME,
                            GAGA.SALES_TYPE,
                            GAGA.SALES_ID,
                            GAGA.SALES_CODE,
                            DISC_AMOUNT,
                            SALES_DTL_NET_AMOUNT,
                            USERS.NAME
                        -- HAVING SUM (SALES_DTL_DISC_AMOUNT) + SUM (EXT_DISC_AMOUNT) > 0 OR DISC_AMOUNT > 0
                        ORDER BY SALES_DATE, SALES_TYPE, SALES_CODE";
                    
           // dd($qry);    
                        
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                       //  dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->branch_name][$row->sales_type][] = $row;
                        }
                       // dd($list);
                        @endphp
                        @php
                            $si_grand_total_amount = 0;
                        @endphp
                        <table width="100%" id="rep_invoice_sales_discount_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th width="12%" class="text-center">Invoice #</th>
                                <th width="13%" class="text-center">Invoice Date Time</th>
                                <th width="35%" class="text-center">User Name</th>
                                <th width="10%" class="text-center">Item Discount</th>
                                <th width="10%" class="text-center">Invoice Discount</th>
                                <th width="10%" class="text-center">Total Discount</th>
                                <th width="10%" class="text-center">Net Amount</th>
                            </tr>
                            @php
                                $gtotdiscamnt = 0;
                                $gtotinvdiscamnt = 0;
                                $gtotnetdiscamnt=0;
                                $gtotnetamnt = 0;
                            @endphp
                            @foreach($list as $branch_keys=>$branch__row)
                                @php
                                    $branch_name = ucwords(strtolower($branch_keys));
                                @endphp
                                <tr>
                                    <td colspan="9"><b style="color:chocolate">Company Branch: {{ucwords(strtolower($branch_keys))}}</b></td>
                                </tr>
                                @php
                                    $stotdiscamnt = 0;
                                    $stotinvdiscamnt = 0;
                                    $stotnetdiscamnt=0;
                                    $stotnetamnt = 0;
                                @endphp
                                @foreach($branch__row as $sale_type_key=>$sale_type_row)
                                    @php
                                        $sale_type_name = ucwords(strtolower($sale_type_key));
                                    @endphp
                                    <tr>
                                        <td colspan="9"><b style="color:purple">{{ucwords(strtolower($sale_type_key))}}</b></td>
                                    </tr>
                                    @php
                                        $ki=1;
                                        $totdiscamnt = 0;
                                        $totinvdiscamnt = 0;
                                        $totnetdiscamnt = 0;
                                        $totnetamnt = 0;
                                    @endphp
                                    @foreach($sale_type_row as $inv_k=>$si_detail)
                                        @if($si_detail->item_discount <> 0)
                                            @php
                                                $nettotdisc = $si_detail->item_discount + $si_detail->invoice_discount;


                                                $totdiscamnt = $totdiscamnt + $si_detail->item_discount;
                                                $totinvdiscamnt = $totinvdiscamnt + $si_detail->invoice_discount;
                                                $totnetdiscamnt = $totnetdiscamnt + $nettotdisc;
                                                $totnetamnt = $totnetamnt + $si_detail->sales_dtl_net_amount;
                                                
                                                $stotdiscamnt = $stotdiscamnt + $si_detail->item_discount;
                                                $stotinvdiscamnt = $stotinvdiscamnt + $si_detail->invoice_discount;
                                                $stotnetdiscamnt = $stotnetdiscamnt + $nettotdisc;
                                                $stotnetamnt = $stotnetamnt + $si_detail->sales_dtl_net_amount;

                                                $gtotdiscamnt = $gtotdiscamnt + $si_detail->item_discount;
                                                $gtotinvdiscamnt = $gtotinvdiscamnt + $si_detail->invoice_discount;
                                                $gtotnetdiscamnt = $gtotnetdiscamnt + $nettotdisc;
                                                $gtotnetamnt = $gtotnetamnt + $si_detail->sales_dtl_net_amount;
                                                
                                            @endphp
                                                <tr>
                                                    <td class="text-center">{{$si_detail->sales_code}}</td>
                                                    <td class="text-left">{{ date('d-M-Y h:i A', strtotime($si_detail->created_at)) }}</td>
                                                    <td class="text-left">{{$si_detail->name}}</td>
                                                    <td class="text-right">{{number_format($si_detail->item_discount,0)}}</td>
                                                    <td class="text-right">{{number_format($si_detail->invoice_discount,0)}}</td>
                                                    <td class="text-right">{{number_format($nettotdisc,0)}}</td>
                                                    <td class="text-right">{{number_format($si_detail->sales_dtl_net_amount,0)}}</td>
                                                </tr>
                                            @php
                                                $ki += 1;
                                            @endphp
                                        @endif
                                    @endforeach
                                        <tr>
                                            <td colspan="3" class="text-right"><strong style="color:purple">{{$sale_type_name}} Total: </strong></td>
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
                                            <td class="text-right"><strong>{{number_format($totnetdiscamnt,0)}}</strong></td>
                                            <td class="text-right"><strong>{{number_format($totnetamnt,0)}}</strong></td>
                                        </tr>
                                @endforeach
                                    <tr>
                                        <td colspan="3" class="text-right"><strong style="color:chocolate">{{$branch_name}} Total: </strong></td>
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
                                        <td class="text-right"><strong>{{number_format($stotnetdiscamnt,0)}}</strong></td>
                                        <td class="text-right"><strong>{{number_format($stotnetamnt,0)}}</strong></td>
                                    </tr>
                            @endforeach
                                <tr>
                                    <td colspan="3" class="text-right"><strong style="color:teal">Grand Total: </strong></td>
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
                                    <td class="text-right"><strong>{{number_format($gtotnetdiscamnt,0)}}</strong></td>
                                    <td class="text-right"><strong>{{number_format($gtotnetamnt,0)}}</strong></td>
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
                $("#rep_invoice_sales_discount_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
