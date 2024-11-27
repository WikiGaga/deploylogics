@extends('layouts.report')
@section('title', 'Sales & Cost Report')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        .link{
            text-decoration: underline;
            color: #fd397a;
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
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get(['branch_name','branch_short_name']); @endphp
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
                        $from_date = $data['from_date'];
                        $to_date = $data['to_date'];

                        $query = "select 
                                    sales_date, 
                                    CALENDAR_YEAR, 
                                    month_date, 
                                    sum(SALE_AMOUNT) SALE_AMOUNT, 
                                    sum(sale_return_amount) sale_return_amount, 
                                    sum(STOCK_AMOUNT) STOCK_AMOUNT,
                                    sum(STOCK_RCV_AMOUNT) STOCK_RCV_AMOUNT,
                                    (
                                        sum(SALE_AMOUNT) + sum(sale_return_amount)
                                    ) net_sale 
                                    from 
                                    (
                                        select 
                                        SALES_DATE, 
                                        CALENDAR_YEAR, 
                                        to_char(SALE.SALES_DATE, 'MM/YYYY') MONTH_DATE, 
                                        SUM(SALE.SALES_DTL_AMOUNT) SALE_AMOUNT, 
                                        0 SALE_RETURN_AMOUNT, 
                                        0 STOCK_AMOUNT, 
                                        0 STOCK_RCV_AMOUNT 
                                        from 
                                        VW_SALE_SALES_INVOICE SALE, 
                                        TBL_SOFT_CALENDAR 
                                        WHERE 
                                        TBL_SOFT_CALENDAR.CALENDAR_DATE = SALE.SALES_DATE 
                                        and (
                                            SALE.SALES_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
                                            AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
                                        ) 
                                        AND SALE.BRANCH_ID IN ( ".implode(",",$data['branch_ids']).")
                                        AND lower(SALES_TYPE) IN ('si', 'pos') 
                                        group by 
                                        to_char(SALE.SALES_DATE, 'MM/YYYY'), 
                                        CALENDAR_YEAR, 
                                        SALES_DATE 
                                        UNION ALL 
                                        select 
                                        SALES_DATE, 
                                        CALENDAR_YEAR, 
                                        to_char(SALE.SALES_DATE, 'MM/YYYY') MONTH_DATE, 
                                        0 SALE_AMOUNT, 
                                        SUM(SALE.SALES_DTL_AMOUNT) SALE_RETURN_AMOUNT, 
                                        0 STOCK_AMOUNT, 
                                        0 STOCK_RCV_AMOUNT 
                                        from 
                                        VW_SALE_SALES_INVOICE SALE, 
                                        TBL_SOFT_CALENDAR 
                                        WHERE 
                                        TBL_SOFT_CALENDAR.CALENDAR_DATE = SALE.SALES_DATE 
                                        and (
                                            SALE.SALES_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') 
                                            AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
                                        ) 
                                        AND SALE.BRANCH_ID IN ( ".implode(",",$data['branch_ids']).")
                                        AND lower(SALES_TYPE) IN ('rpos', 'sr') 
                                        group by 
                                        to_char(SALE.SALES_DATE, 'MM/YYYY'), 
                                        CALENDAR_YEAR, 
                                        SALES_DATE
                                    ) 
                                    group by 
                                    sales_date, 
                                    CALENDAR_YEAR, 
                                    month_date
                                    order by 
                                    CALENDAR_YEAR, 
                                    sales_date
                                ";  
                            $Result_List = DB::select($query);
                    @endphp

                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-left">Date</th>
                            <th class="text-center">Sales</th>
                            <th class="text-center">Sales Return</th>
                            <th class="text-center">Net Sale</th>
                            <th class="text-center">Cost</th>
                            <th class="text-center">Cost Return</th>
                            <th class="text-center">Net Cost</th>
                            <th class="text-center">Gross Margin</th>
                            <th class="text-center">Gross Margin %</th>
                        </tr>
                        @php
                            $total_Sale = 0;
                            $total_SaleReturn = 0;
                            $total_NetSale = 0;
                            $total_Cost = 0;
                            $total_CostReturn = 0;
                            $total_NetCost = 0;
                            $total_GrossMargin = 0;
                        @endphp
                        @foreach($Result_List as $result)
                            @php
                                $net_sale_amount = $result->sale_amount - abs($result->sale_return_amount);
                                $cost_from_date = date('d/m/Y' , strtotime($result->sales_date));
                                $todate = date('Y-m-d' , strtotime($result->sales_date));
                                
                                // Cost
                                // $costQuery = "SELECT SALES_DATE, SUM(SALE_QTY * OP_AVG_RATE) COST_AMOUNT FROM( select SUM(SALE.SALES_DTL_AMOUNT) SALE_AMOUNT, SUM(SALE.QTY_BASE_UNIT) SALE_QTY , PRODUCT_ID , SALES_DATE from VW_SALE_SALES_INVOICE SALE WHERE(SALE.SALES_DATE BETWEEN TO_DATE('$cost_from_date', 'DD/MM/YYYY') AND TO_DATE('$cost_from_date', 'DD/MM/YYYY')) AND lower(SALES_TYPE) IN ('si' , 'pos') and branch_id IN ( ".implode(",",$data['branch_ids']).") group by PRODUCT_ID , SALES_DATE) SALE LEFT JOIN ( select product_id, CASE WHEN sum(op_qty) > 0 THEN sum(op_amount) / sum(op_qty) ELSE 0 END OP_AVG_RATE FROM ( select product_id,(sum(TBL_PURC_GRN_DTL_AMOUNT) - sum(TBL_PURC_GRN_DTL_DISC_AMOUNT)) OP_AMOUNT, sum(qty_base_unit) OP_QTY, 0 CL_AMOUNT, 0 CL_QTY from TBL_PURC_GRN_DTL A inner join TBL_PURC_GRN B ON A.GRN_ID = B.GRN_ID WHERE B.grn_date <= to_date('$todate', 'yyyy/mm/dd') AND B.branch_id IN ( ".implode(",",$data['branch_ids']).") AND UPPER(B.GRN_TYPE) = 'GRN' group by product_id) XX Group by product_id ) XYZ ON SALE.PRODUCT_ID = XYZ.PRODUCT_ID GROUP BY SALES_DATE";
                                $costQuery = "SELECT SALES_DATE , SUM(SALE_AMOUNT) SALE_AMOUNT , SUM(COST_AMOUNT) COST_AMOUNT FROM( SELECT SALES_DATE, SALE.PRODUCT_ID , SALE.PRODUCT_BARCODE_ID , sum(SALE_AMOUNT) SALE_AMOUNT , SUM(SALE_QTY) * OP_AVG_RATE COST_AMOUNT FROM ( select PRODUCT_BARCODE_ID , SUM(SALE.SALES_DTL_AMOUNT) SALE_AMOUNT, SUM(SALE.SALES_DTL_QUANTITY) SALE_QTY , PRODUCT_ID , SALES_DATE from VW_SALE_SALES_INVOICE SALE WHERE(SALE.SALES_DATE BETWEEN TO_DATE('$cost_from_date', 'DD/MM/YYYY') AND TO_DATE('$cost_from_date', 'DD/MM/YYYY')) AND lower(SALES_TYPE) IN ('si' , 'pos') and branch_id IN ( ".implode(",",$data['branch_ids']).") group by PRODUCT_ID , SALES_DATE, PRODUCT_BARCODE_ID) SALE LEFT JOIN ( select PRODUCT_ID, PRODUCT_BARCODE_ID, BRANCH_ID, PRODUCT_BARCODE_COST_RATE OP_AVG_RATE from TBL_PURC_PRODUCT_BARCODE_PURCH_RATE where branch_id IN ( ".implode(",",$data['branch_ids']).")) XYZ ON SALE.PRODUCT_ID = XYZ.PRODUCT_ID and SALE.PRODUCT_BARCODE_ID = XYZ.PRODUCT_BARCODE_ID GROUP BY SALE.SALES_DATE, SALE.PRODUCT_ID , SALE.PRODUCT_BARCODE_ID , OP_AVG_RATE ) CCC GROUP BY SALES_DATE";
                                $costOfGoods = DB::select($costQuery);
                                $cost_amount = $costOfGoods[0]->cost_amount ?? 0;
                                
                                // Cost Return
                                $costReturnQuery = "SELECT SALES_DATE , SUM(SALE_AMOUNT) SALE_AMOUNT , SUM(COST_AMOUNT) COST_AMOUNT FROM( SELECT SALES_DATE, SALE.PRODUCT_ID , SALE.PRODUCT_BARCODE_ID , sum(SALE_AMOUNT) SALE_AMOUNT , SUM(SALE_QTY) * OP_AVG_RATE COST_AMOUNT FROM ( select PRODUCT_BARCODE_ID , SUM(SALE.SALES_DTL_AMOUNT) SALE_AMOUNT, SUM(SALE.SALES_DTL_QUANTITY) SALE_QTY , PRODUCT_ID , SALES_DATE from VW_SALE_SALES_INVOICE SALE WHERE(SALE.SALES_DATE BETWEEN TO_DATE('$cost_from_date', 'DD/MM/YYYY') AND TO_DATE('$cost_from_date', 'DD/MM/YYYY')) AND lower(SALES_TYPE) IN ('sr' , 'rpos') and branch_id IN ( ".implode(",",$data['branch_ids']).") group by PRODUCT_ID , SALES_DATE, PRODUCT_BARCODE_ID) SALE LEFT JOIN ( select PRODUCT_ID, PRODUCT_BARCODE_ID, BRANCH_ID, PRODUCT_BARCODE_COST_RATE OP_AVG_RATE from TBL_PURC_PRODUCT_BARCODE_PURCH_RATE where branch_id IN ( ".implode(",",$data['branch_ids']).")) XYZ ON SALE.PRODUCT_ID = XYZ.PRODUCT_ID and SALE.PRODUCT_BARCODE_ID = XYZ.PRODUCT_BARCODE_ID GROUP BY SALE.SALES_DATE, SALE.PRODUCT_ID , SALE.PRODUCT_BARCODE_ID , OP_AVG_RATE ) CCC GROUP BY SALES_DATE";
                                $costOfReturnGoods = DB::select($costReturnQuery);
                                $cost_return_amount = $costOfReturnGoods[0]->cost_amount ?? 0;
                                
                                // Net Cost Amount & Gross Margin
                                $net_cost_amount = $cost_amount - abs($cost_return_amount);
                                $gross_margin = $net_sale_amount - $net_cost_amount;

                                // Gross Margin Percentage
                                $percentage = (($net_sale_amount / $net_cost_amount) - 1)*100;

                                // Calculation For Grand Total
                                $total_Sale         = $total_Sale + $result->sale_amount;
                                $total_SaleReturn   = $total_SaleReturn + $result->sale_return_amount;
                                $total_NetSale      = $total_NetSale + $net_sale_amount;
                                $total_Cost = $total_Cost + $cost_amount;
                                $total_CostReturn = $total_CostReturn + $cost_return_amount;
                                $total_NetCost = $total_NetCost + $net_cost_amount;
                                $total_GrossMargin = $total_GrossMargin +  $gross_margin;
                            @endphp
                            <tr>
                                <td class="text-left">{{ date('Y-m-d' , strtotime($result->sales_date)) }}</td>
                                <td class="text-right">{{ number_format($result->sale_amount,3) }}</td>
                                <td class="text-right">{{ number_format($result->sale_return_amount,3) }}</td>
                                <td class="text-right">{{ number_format($net_sale_amount,3) }}</td>
                                <td class="text-right">{{ number_format($cost_amount , 3) }}</td>
                                <td class="text-right">{{ number_format($cost_return_amount , 3) }}</td>
                                <td class="text-right">{{ number_format($net_cost_amount , 3) }}</td>
                                <td class="text-right">{{ number_format($gross_margin , 3) }}</td>
                                <td class="text-right">{{ number_format($percentage , 3) }} %</td>
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($total_Sale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_SaleReturn,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_NetSale,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_Cost,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CostReturn,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_NetCost,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_GrossMargin,3)}}</td>
                            <td class="text-right rep-font-bold">{{ number_format((($total_NetSale / $total_NetCost )-1)*100 , 3) }} % </td>
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