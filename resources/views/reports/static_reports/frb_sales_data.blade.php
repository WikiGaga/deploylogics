@extends('layouts.report')
@section('title', 'FBR Sale Data')

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
                @php
                    $where = "";

                    if(count($data['sale_types_multiple']) != 0){
                        $where .= " and sales_type in ('".implode("','",$data['sale_types_multiple'])."') ";
                    }

                    
                    /* HS CODE COUNTER*/
                    $hs_qry = "select  
                        count(distinct PRODUCT_HS_CODE)  hs_code
                    from 
                        VW_SALE_SALES_INVOICE 
                    where branch_id in (".implode(",",$data['branch_ids']).")
                        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        AND (PRODUCT_HS_CODE <> '' OR upper(PRODUCT_HS_CODE) IS NOT NULL)
                        $where ";
                    
                    $hs_getdata = \Illuminate\Support\Facades\DB::select($hs_qry);

                    $ehs_qry = "select  
                        count(distinct PRODUCT_HS_CODE)  hs_code
                    from 
                        VW_SALE_SALES_INVOICE 
                    where branch_id in (".implode(",",$data['branch_ids']).")
                        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        AND (PRODUCT_HS_CODE  = '' OR PRODUCT_HS_CODE IS NULL)
                        $where ";

                    $ehs_getdata = \Illuminate\Support\Facades\DB::select($ehs_qry);

                    /* HS CODE COUNTER*/


                    /* POSTED AND UNPOSTED FBR*/
                    $unpfbr_qry = "select  
                        count(distinct sales_id)  sales_id
                    from 
                        VW_SALE_SALES_INVOICE 
                    where branch_id in (".implode(",",$data['branch_ids']).")
                        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        AND (upper(FBR_INVOICE_NO)  = 'NOT AVAILABLE' 
                        OR upper(FBR_INVOICE_NO)  IS NULL 
                        OR upper(FBR_INVOICE_NO)  = '')
                        $where ";
                    $unpfbr_getdata = \Illuminate\Support\Facades\DB::select($unpfbr_qry);
                    
                    $unpafbr_qry = "select  
                        SUM(SALES_DTL_AMOUNT) AS SALES_DTL_AMOUNT
                    from 
                        VW_SALE_SALES_INVOICE 
                    where branch_id in (".implode(",",$data['branch_ids']).")
                        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        AND (upper(FBR_INVOICE_NO)  = 'NOT AVAILABLE' 
                        OR upper(FBR_INVOICE_NO)  IS NULL 
                        OR upper(FBR_INVOICE_NO)  = '')
                        $where ";

                    $unpfbra_getdata = \Illuminate\Support\Facades\DB::select($unpafbr_qry);


                    $pfbra_qry = "select  
                        SUM(SALES_DTL_AMOUNT) AS SALES_DTL_AMOUNT
                    from 
                        VW_SALE_SALES_INVOICE 
                    where branch_id in (".implode(",",$data['branch_ids']).")
                        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        AND (upper(FBR_INVOICE_NO)  <> 'NOT AVAILABLE')
                        $where ";
                    $pfbra_getdata = \Illuminate\Support\Facades\DB::select($pfbra_qry);

                    $pfbr_qry = "select  
                        count(distinct sales_id)  sales_id
                    from 
                        VW_SALE_SALES_INVOICE 
                    where branch_id in (".implode(",",$data['branch_ids']).")
                        and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                        AND (upper(FBR_INVOICE_NO)  <> 'NOT AVAILABLE')
                        $where ";
                    
                    $pfbr_getdata = \Illuminate\Support\Facades\DB::select($pfbr_qry);
                    
                    /* POSTED AND UNPOSTED FBR*/

                @endphp
                <!-- POSTED AND UNPOSTED FBR -->
                @foreach($unpfbr_getdata as $val)
                    @php 
                        $unpfbr =  $val->sales_id; 
                    @endphp
                @endforeach
                @foreach($unpfbra_getdata as $val)
                    @php 
                        $unpfbramount = $val->sales_dtl_amount; 
                    @endphp
                @endforeach
                @foreach($pfbr_getdata as $val1)
                    @php 
                        $pfbr =  $val1->sales_id; 
                    @endphp
                @endforeach
                @foreach($pfbra_getdata as $val1)
                    @php 
                        $pfbramount = $val1->sales_dtl_amount; 
                    @endphp
                @endforeach

                <!-- HS CODE COUNTER -->
                @foreach($hs_getdata as $val2)
                    @php $hscode =  $val2->hs_code; @endphp
                @endforeach
                @foreach($ehs_getdata as $val3)
                    @php $ehs =  $val3->hs_code; @endphp
                @endforeach

                <div class="col-lg-4">
                    <table width="100%" class="static_report_table table bt-datatable table-bordered">
                        <tr style="background-color:teal">
                            <th colspan='3' class="text-center"><b>Hs Code</b></th>
                        </tr>
                        <tr style="background-color:bisque">
                            <td class="text-center">ALL</td>
                            <td class="text-center">HS</td>
                            <td class="text-center">E.HS</td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>{{ $hscode+$ehs }}</b></td>
                            <td class="text-center"><b>{{ $hscode }}</b></td>
                            <td class="text-center"><b>{{ $ehs }}</b></td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-4">
                    <table width="100%" class="static_report_table table bt-datatable table-bordered">
                        <tr style="background-color:teal">
                            <th colspan='5' class="text-center"><b>Posted Fbr</b></th>
                        </tr>
                        <tr style="background-color:bisque">
                            <td class="text-center">ALL</td>
                            <td class="text-center">P FBR</td>
                            <td class="text-center">UNP FBR</td>
                            <td class="text-center">P FBR Amount</td>
                            <td class="text-center">UNP FBR Amount</td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>{{ $unpfbr+$pfbr }}</b></td>
                            <td class="text-center"><b>{{ $pfbr }}</b></td>
                            <td class="text-center"><b>{{ $unpfbr }}</b></td>
                            <td class="text-center"><b>{{number_format($pfbramount,2)}}</b></td>
                            <td class="text-center"><b>{{number_format($unpfbramount,2)}}</b></td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-4"></div>
                <!-- end -->
                <div class="col-lg-12">
                    @php
                        $qry = "SELECT 
                            BRANCH_ID,
                            BRANCH_NAME,
                            SALES_DATE,
                            SALES_CODE,
                            CASE
                                WHEN SALES_TYPE = 'POS' 
                                THEN 'Sale Invoice' 
                                WHEN SALES_TYPE = 'RPOS' 
                                THEN 'Sale Return' 
                                ELSE ''
                            END AS SALES_TYPE,
                            SALES_ID,
                            FBR_INVOICE_NO,
                            SALES_DATE FBR_DATE,
                            CREATED_AT,
                            PRODUCT_HS_CODE HS_CODE,
                            TERMINAL_ID,
                            TERMINAL_NAME,
                            SALES_SALES_MAN_NAME,
                            CONCAT(
                                CONCAT(
                                    GROUP_ITEM_PARENT_NAME,
                                    '-'
                                ),
                                GROUP_ITEM_NAME
                            ) GROUP_NAME,
                            PRODUCT_BARCODE_BARCODE,
                            PRODUCT_NAME,
                            SALES_DTL_RATE - SALES_DTL_VAT_AMOUNT SALE_RATE_EX_TAX,
                            SALES_DTL_QUANTITY,
                            SALES_DTL_AMOUNT,
                            SALES_DTL_DISC_PER,
                            SALES_DTL_DISC_AMOUNT,
                            SALES_DTL_VAT_PER,
                            SALES_DTL_VAT_AMOUNT,
                            SALES_DTL_TOTAL_AMOUNT 
                        FROM
                            VW_SALE_SALES_INVOICE 
                        WHERE branch_id in (".implode(",",$data['branch_ids']).")
                            and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                            $where
                        ORDER BY SALES_DATE,SALES_TYPE,SALES_CODE";
                    
           //dd($qry);    
                        
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        //dd($getdata);
                        $list = [];
                        foreach ($getdata as $row){
                            $list[$row->branch_name][$row->sales_type][] = $row;
                        }
                        //dd($list);
                        @endphp
                        @php
                            $si_grand_total_amount = 0;
                        @endphp
                        <table width="100%" id="rep_fbr_sales_data_datatable" class="static_report_table table bt-datatable table-bordered">
                            <tr class="sticky-header">
                                <th class="text-center">S.#</th>
                                <th class="text-left">Branch</th>
                                <th class="text-center">Inv. Date</th>
                                <th class="text-center">Inv. #</th>
                                <th class="text-center">FBR Inv. #</th>
                                <th class="text-center">FBR Inv. Date</th>
                                <th class="text-center">HS Code</th>
                                <th class="text-center">User</th>
                                <th class="text-left">Counter</th>
                                <th class="text-left">First and Last Level Category</th>
                                <th class="text-left">Barcode</th>
                                <th class="text-left">Product Name</th>
                                <th class="text-center">Sale Rate Exc. Tax</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Net Amount</th>
                                <th class="text-center">Discount</th>
                                <th class="text-center">Tax (%)</th>
                                <th class="text-center">Tax Amount</th>
                            </tr>
                            @php
                                $gtotsaletax = 0;
                                $gtotqty = 0;
                                $gtotnetamnt = 0;
                                $gtotdisc = 0;
                                $gtottax = 0;
                            @endphp
                            @foreach($list as $branch_keys=>$branch__row)
                                @php
                                    $branch_name = ucwords(strtolower($branch_keys));
                                @endphp
                                <tr>
                                    <td colspan="18"><b style="color:crimson">Company Branch: {{ucwords(strtolower($branch_keys))}}</b></td>
                                </tr>
                                @php
                                    $stotsaletax = 0;
                                    $stotqty = 0;
                                    $stotnetamnt = 0;
                                    $stotdisc = 0;
                                    $stottax = 0;
                                @endphp
                                @foreach($branch__row as $sale_type_key=>$sale_type_row)
                                    @php
                                        $sale_type_name = ucwords(strtolower($sale_type_key));
                                    @endphp
                                    <tr>
                                        <td colspan="18"><b style="color:#5578eb">{{ucwords(strtolower($sale_type_key))}}</b></td>
                                    </tr>
                                    @php
                                        $ki=1;
                                        $totsaletax = 0;
                                        $totqty = 0;
                                        $totnetamnt = 0;
                                        $totdisc = 0;
                                        $tottax = 0;
                                    @endphp
                                    @foreach($sale_type_row as $inv_k=>$si_detail)
                                        @php

                                            $totsaletax = $totsaletax + $si_detail->sale_rate_ex_tax;
                                            $totqty = $totqty + $si_detail->sales_dtl_quantity;
                                            $totnetamnt = $totnetamnt + $si_detail->sales_dtl_amount;
                                            $totdisc = $totdisc + $si_detail->sales_dtl_disc_amount;
                                            $tottax = $tottax + $si_detail->sales_dtl_vat_amount;
                                            
                                            
                                            $stotsaletax = $stotsaletax + $si_detail->sale_rate_ex_tax;
                                            $stotqty = $stotqty + $si_detail->sales_dtl_quantity;
                                            $stotnetamnt = $stotnetamnt + $si_detail->sales_dtl_amount;
                                            $stotdisc = $stotdisc + $si_detail->sales_dtl_disc_amount;
                                            $stottax = $stottax + $si_detail->sales_dtl_vat_amount;
                                            
                                            $gtotsaletax = $gtotsaletax + $si_detail->sale_rate_ex_tax;
                                            $gtotqty = $gtotqty + $si_detail->sales_dtl_quantity;
                                            $gtotnetamnt = $gtotnetamnt + $si_detail->sales_dtl_amount;
                                            $gtotdisc = $gtotdisc + $si_detail->sales_dtl_disc_amount;
                                            $gtottax = $gtottax + $si_detail->sales_dtl_vat_amount;
                                        @endphp
                                            <tr>
                                                <td class="text-center">{{$ki}}</td>
                                                <td class="text-left">{{$si_detail->branch_name}}</td>
                                                <td class="text-center">{{date('d-m-Y', strtotime($si_detail->sales_date))}}</td>
                                                <td class="text-center">{{$si_detail->sales_code}}</td>
                                                <td class="text-center">{{$si_detail->fbr_invoice_no}}</td>
                                                <td class="text-center">{{date('d-m-Y', strtotime($si_detail->fbr_date))}}</td>
                                                <td class="text-center">{{$si_detail->hs_code}}</td>
                                                <td class="text-center">{{$si_detail->sales_sales_man_name}}</td>
                                                <td class="text-left">{{$si_detail->terminal_name}}</td>
                                                <td class="text-left">{{$si_detail->group_name}}</td>
                                                <td class="text-left">{{$si_detail->product_barcode_barcode}}</td>
                                                <td class="text-left">{{$si_detail->product_name}}</td>
                                                <td class="text-right">{{number_format($si_detail->sale_rate_ex_tax,2)}}</td>
                                                <td class="text-center">{{number_format($si_detail->sales_dtl_quantity,0)}}</td>
                                                <td class="text-right">{{number_format($si_detail->sales_dtl_amount,0)}}</td>
                                                <td class="text-right">{{number_format($si_detail->sales_dtl_disc_amount,0)}}</td>
                                                <td class="text-center">{{number_format($si_detail->sales_dtl_vat_per,0)}}</td>
                                                <td class="text-right">{{number_format($si_detail->sales_dtl_vat_amount,2)}}</td>
                                            </tr>
                                        @php
                                            $ki += 1;
                                        @endphp
                                    @endforeach
                                        <tr>
                                            <td colspan="12" class="text-right"><strong style="color:#5578eb">{{$sale_type_name}} Total: </strong></td>
                                            <td class="text-right">
                                                <strong>
                                                    {{number_format($totsaletax,0)}}
                                                </strong>
                                            </td>
                                            <td class="text-right">
                                                <strong>
                                                    {{number_format($totqty,0)}}
                                                </strong>
                                            </td>
                                            <td class="text-right"><strong>{{number_format($totnetamnt,0)}}</strong></td>
                                            <td class="text-right"><strong>{{number_format($totdisc,0)}}</strong></td>
                                            <td class="text-right"><strong>&nbsp;</strong></td>
                                            <td class="text-right"><strong>{{number_format($tottax,0)}}</strong></td>
                                        </tr>
                                @endforeach
                                    <tr>
                                        <td colspan="12" class="text-right"><strong style="color:crimson">{{$branch_name}} Total: </strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($stotsaletax,0)}}
                                            </strong>
                                        </td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($stotqty,0)}}
                                            </strong>
                                        </td>
                                        <td class="text-right"><strong>{{number_format($stotnetamnt,0)}}</strong></td>
                                        <td class="text-right"><strong>{{number_format($stotdisc,0)}}</strong></td>
                                        <td class="text-right"><strong>&nbsp;</strong></td>
                                        <td class="text-right"><strong>{{number_format($stottax,0)}}</strong></td>
                                    </tr>
                            @endforeach
                                <tr>
                                    <td colspan="12" class="text-right"><strong style="color:teal">Grand Total: </strong></td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($gtotsaletax,0)}}
                                        </strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>
                                            {{number_format($gtotqty,0)}}
                                        </strong>
                                    </td>
                                    <td class="text-right"><strong>{{number_format($gtotnetamnt,0)}}</strong></td>
                                    <td class="text-right"><strong>{{number_format($gtotdisc,0)}}</strong></td>
                                    <td class="text-right"><strong>&nbsp;</strong></td>
                                    <td class="text-right"><strong>{{number_format($gtottax,0)}}</strong></td>
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
                $("#rep_fbr_sales_data_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
