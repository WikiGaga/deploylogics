@extends('layouts.report')
@section('title', 'Monthly Sales & Purchase Summary')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        .border_right{
            border-right: 1px solid #000 !important;
        }
        .sec-sticky-header{
            position: sticky;
            top: 28px;
            background-color: #f7f8fa;
        }
        .sticky-header-to{
            position: sticky;
            top: 19px;
            background-color: #f7f8fa;
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
                @if(count($data['all_document_type']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Document Type:</span>
                        @foreach($data['all_document_type'] as $ad_type)
                            <span style="color: #5578eb;">{{" ".$ad_type.", "}}</span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get(['branch_name','branch_short_name']); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['supplier_ids']) != 0)
                    @php $suppliers = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->whereIn('supplier_id',$data['supplier_ids'])->get(); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        @foreach($suppliers as $supplier)
                            <span style="color: #5578eb;">{{$supplier->supplier_name}}</span><span style="color: #fd397a;">, </span>
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
                        if(count($data['supplier_ids']) != 0){
                          $where .= " and SUP_PROD.supplier_id in (".implode(",",$data['supplier_ids']).")";
                        }

                        // Vendor Wise whereclause
                        $vendorfrom = "";
                        $vendorjoin = "";
                        $vendorjoin1 = "";
                        $vendorjoin2 = "";
                        if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0){
                            
                            $vendorfrom = " ,VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
                            $vendorjoin = " AND sale.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
                            $vendorjoin1 = " AND grn.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
                            $vendorjoin2 = " AND st.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
                        }
                        //End Vendor Wise whereclause
                        
                        $query = "SELECT 
                            calendar_year,
                            calendar_month,
                            to_char (
                            to_date (month_date, 'mm/yyyy'),
                                'MON/yyyy'
                            ) month_date,
                            SUM(sale_amount) sale_amount,
                            SUM(grn_amount) grn_amount,
                            SUM(stock_amount) stock_amount,
                            SUM(stock_rcv_amount) stock_rcv_amount,
                            (SUM(sale_amount) + SUM(stock_amount)) tot_sale,
                            (
                                SUM(stock_rcv_amount) + SUM(grn_amount)
                            ) tot_purchase,
                            SUM(SALE_cost_amount) SALE_cost_amount,
                            SUM(STOCK_cost_amount) STOCK_cost_amount,
                            (
                                SUM(SALE_cost_amount) + SUM(STOCK_cost_amount)
                            ) tot_cost,
                            (
                                SUM(sale_amount) - SUM(sale_cost_amount)
                            ) net_gp,
                            CASE
                                WHEN (SUM(sale_amount) + SUM(stock_amount)) > 0 
                                THEN ROUND(
                                    (
                                        (SUM(sale_amount) + SUM(stock_amount)) - (SUM(sale_cost_amount) + SUM(stock_cost_amount))
                                    ) / (SUM(sale_amount) + SUM(stock_amount)) * 100,
                                2
                                ) 
                            END gp_per 
                        FROM (
                            SELECT 
                                calendar_year,
                                calendar_month,
                                to_char (sale.sales_date, 'mm/yyyy') month_date,
                                SUM(sale.SALES_DTL_NET_AMOUNT) sale_amount,
                                0 grn_amount,
                                0 stock_amount,
                                0 stock_rcv_amount,
                                SUM(sale.cost_amount) SALE_cost_amount,
                                0 STOCK_cost_amount,
                                branch_id 
                            FROM
                                vw_sale_sales_invoice sale, 
                                tbl_soft_calendar 
                                $vendorfrom
                            WHERE tbl_soft_calendar.calendar_date = sale.sales_date
                                $vendorjoin
                                AND (SALE.SALES_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')) 
                                AND SALE.BRANCH_ID IN (".implode(",",$data['branch_ids']).") 
                                AND LOWER(sales_type) IN ('si', 'pos', 'rpos', 'sr')
                                $where
                            GROUP BY branch_id,
                                to_char (sale.sales_date, 'mm/yyyy'),
                                calendar_year,
                                calendar_month,
                                calendar_month 
                            
                            UNION
                            ALL 
                            SELECT 
                                calendar_year,
                                calendar_month,
                                to_char (grn.grn_date, 'mm/yyyy') month_date,
                                0 sale_amount,
                                SUM(
                                    (
                                        CASE grn.grn_type 
                                            WHEN 'pr' 
                                            THEN - tbl_purc_grn_dtl_total_amount 
                                            ELSE tbl_purc_grn_dtl_total_amount 
                                        END
                                    )
                                ) grn_amount,
                                0 stock_amount,
                                0 stock_rcv_amount,
                                0 SALE_cost_amount,
                                0 STOCK_cost_amount,
                                branch_id 
                            FROM
                                vw_purc_grn grn, 
                                tbl_soft_calendar
                                $vendorfrom
                            WHERE tbl_soft_calendar.calendar_date = grn.grn_date
                                $vendorjoin1
                                AND (grn.GRN_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')) 
                                AND BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                AND LOWER(grn.grn_type) IN ('grn', 'pi', 'grn', 'pi') 
                                $where
                            GROUP BY grn.branch_id,
                                to_char (grn.grn_date, 'mm/yyyy'),
                                calendar_year,
                                calendar_month 
                            
                            UNION
                            ALL 
                            SELECT 
                                calendar_year,
                                calendar_month,
                                to_char (st.stock_date, 'mm/yyyy') month_date,
                                0 sale_amount,
                                0 grn_amount,
                                SUM(st.stock_dtl_amount) stock_amount,
                                0 stock_rcv_amount,
                                0 SALE_cost_amount,
                                SUM(st.cost_amount) STOCK_cost_amount,
                                st.branch_id 
                            FROM
                                vw_inve_stock st, 
                                tbl_soft_calendar
                                $vendorfrom 
                            WHERE tbl_soft_calendar.calendar_date = st.stock_date 
                                $vendorjoin2
                                AND (st.STOCK_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')) 
                                AND st.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                AND LOWER(st.stock_code_type) IN ('st', 'st')
                                $where
                            GROUP BY st.branch_id,
                                to_char (st.stock_date, 'mm/yyyy'),
                                calendar_year,
                                calendar_month 
                        
                            UNION
                            ALL 
                            SELECT 
                                calendar_year,
                                calendar_month,
                                to_char (st.stock_date, 'mm/yyyy') month_date,
                                0 sale_amount,
                                0 grn_amount,
                                0 stock_amount,
                                SUM(st.stock_dtl_amount) stock_rcv_amount,
                                0 SALE_cost_amount,
                                0 STOCK_cost_amount,
                                st.branch_id
                            FROM
                                vw_inve_stock st, 
                                tbl_soft_calendar
                                $vendorfrom
                            WHERE tbl_soft_calendar.calendar_date = st.stock_date
                                $vendorjoin2
                                AND (st.STOCK_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')) 
                                AND st.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                AND LOWER(st.stock_code_type) IN ('str', 'str')
                                $where
                            GROUP BY st.branch_id,
                                to_char (st.stock_date, 'mm/yyyy'),
                                calendar_year,
                                calendar_month
                        ) kaka
                        GROUP BY month_date,
                            calendar_year,
                            calendar_month 
                            ORDER BY calendar_year,
                            calendar_month";

                       // dd($query);
                        $get_data = DB::select($query);

                    @endphp
                    <table width="100%" id="monthly_sale_pur_summary_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                       <tr class="sticky-header">
                            <th width="6%" class="text-center">Sr#</th>
                            <th width="14%" class="text-center">Month</th>
                            <th width="10%" class="text-center">Purchase</th>
                            <th width="10%" class="text-center">Stock Receive</th>
                            <th width="10%" class="text-center">Total Purchase</th>
                            <th width="10%" class="text-center">Sale</th>
                            <th width="10%" class="text-center">Cost Amount</th>
                            <th width="10%" class="text-center">Net Profit</th>
                            <th width="10%" class="text-center">Stock Transfer</th>
                            <th width="10%" class="text-center">Total Sale</th>
                        </tr>
                        <tbody>
                        @php
                            $tot_grn_amount = 0;
                            $tot_stock_rcv_amount = 0;
                            $tot_tot_purchase = 0;
                            $tot_sale_amount = 0;
                            $tot_sale_cost_amount = 0;
                            $tot_net_gp = 0;
                            $tot_stock_amount = 0;
                            $tot_tot_sale = 0;
                        @endphp
                        @foreach ($get_data as $item)
                            @php
                                $tot_grn_amount = $tot_grn_amount + $item->grn_amount;
                                $tot_stock_rcv_amount = $tot_stock_rcv_amount + $item->stock_rcv_amount;
                                $tot_tot_purchase = $tot_tot_purchase + $item->tot_purchase;
                                $tot_sale_amount = $tot_sale_amount + $item->sale_amount;
                                $tot_sale_cost_amount = $tot_sale_cost_amount + $item->sale_cost_amount;
                                $tot_net_gp = $tot_net_gp + $item->net_gp;
                                $tot_stock_amount = $tot_stock_amount + $item->stock_amount;
                                $tot_tot_sale = $tot_tot_sale + $item->tot_sale;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }} </td>
                                <td class="text-center">{{ $item->month_date }}</td>
                                <td class="text-right">{{ @number_format($item->grn_amount,3)  }} </td>
                                <td class="text-right">{{ @number_format($item->stock_rcv_amount,3)  }} </td>
                                <td class="text-right">{{ @number_format($item->tot_purchase,3)  }} </td>
                                <td class="text-right">{{ @number_format($item->sale_amount,3)  }} </td>
                                <td class="text-right">{{ @number_format($item->sale_cost_amount,3)  }} </td>
                                <td class="text-right">{{ @number_format($item->net_gp,3)  }} </td>
                                <td class="text-right">{{ @number_format($item->stock_amount,3)  }} </td>
                                <td class="text-right">{{ @number_format($item->tot_sale,3)  }} </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td colspan="2" class="text-right rep-font-bold">Total</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_grn_amount,3)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_stock_rcv_amount,3)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_tot_purchase,3)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_sale_amount,3)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_sale_cost_amount,3)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_net_gp,3)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_stock_amount,3)  }}</td>
                            <td class="text-right rep-font-bold">{{ @number_format($tot_tot_sale,3)  }}</td>
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
    <script>
        var $contextMenu = $("#contextMenu");
            $("body").on("contextmenu", 'td[data-id="pd_product_name"]', function(e) {
                var thix = $(this);
                var val = thix.val();
                var product_id = thix.parents('tr').find('td:first-child>.product_id').val();
                var pd_barcode = thix.parents('tr').find('.pd_barcode').val();
                $("#contextMenu li a").attr('data-id',product_id);
                $("#contextMenu li a").attr('data-val',val);
                $("#contextMenu li a").attr('data-barcode',pd_barcode);
                $("#contextMenu li.product_card a").attr('href','/product/edit/'+product_id);

                $contextMenu.css({display: "block",left: e.pageX,top: e.pageY});
                return false;
            });
    </script>
@endsection

@section('customJS')

@endsection
@section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#monthly_sale_pur_summary_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



