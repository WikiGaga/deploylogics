@extends('layouts.report')
@section('title', 'Gross Profit Store Wise')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        tbody.item_row tr:hover {
            background: antiquewhite;
        }
    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data');
        // dd($data);
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
                @if(count($data['supplier_ids']) != 0)
                    @php $suppliers = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->whereIn('supplier_id',$data['supplier_ids'])->get(); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        @foreach($suppliers as $supplier)
                            <span style="color: #5578eb;">{{$supplier->supplier_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @else
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier: </span>
                        <span style="color: #5578eb;"> All</span><span style="color: #fd397a;">, </span>
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="supplier_wise_sale_datatable" class="table bt-datatable table-bordered">
                        <tr class="header">
                            <th>Sr.</th>
                            <th>Month</th>
                            <th>Purchase</th>
                            <th>Stock Recieve</th>
                            <th>Total Purchase</th>
                            <th>Sale</th>
                            <th>Stock Transfer</th>
                            <th>Total Sale</th>
                            <th>Sale Cost</th>
                            <th>Transfer Cost</th>
                            <th>Total Cost</th>
                            <th>GP%</th>
                            <th>Total GP</th>
                        </tr>
                        @if(count($data['branch_ids']) != 0)
                            @php $branch_ids = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_id'); @endphp
                            @foreach($branch_ids as $branch_id_list)
                                @php
                                    $pdo = \Illuminate\Support\Facades\DB::getPdo();
                                    $start_val =0;
                                    $end_val =99999999999999999;

                                    $stmt = $pdo->prepare("begin ".\App\Library\Utilities::getDatabaseUsername().".PRO_PURC_RATE_FIFO(:p1, :p2, :p3); end;");
                                    $stmt->bindParam(':p1', $branch_id_list->branch_id);
                                    $stmt->bindParam(':p2', $start_val);
                                    $stmt->bindParam(':p3', $end_val);
                                    $stmt->execute();
                                @endphp
                            @endforeach
                        @endif

                        @php
                            $supplier_ids = '';
                            $from_date = isset($data['from_date'])?$data['from_date']:'';
                            $to_date = isset($data['to_date'])?$data['to_date']:'';
                            $branch_multiple = isset($data['branch_ids'])?$data['branch_ids']:'';
                            $supplier_multiple = isset($data['supplier_ids'])?$data['supplier_ids']:'';
                            // dd($supplier_multiple);
                            if ($supplier_multiple != null) {
                                $supplier_ids = "AND supplier_id IN (".implode(",",$supplier_multiple).")";
                            }

                            $query = "SELECT 
                                calendar_year, 
                                calendar_month, 
                                month_date, 
                                SUM(sale_amount) sale_amount, 
                                SUM(grn_amount) grn_amount, 
                                SUM(stock_amount) stock_amount, 
                                SUM(stock_rcv_amount) stock_rcv_amount,
                                (SUM(sale_amount) + SUM(stock_amount)) tot_sale, 
                                (SUM(stock_rcv_amount) + SUM(grn_amount)) tot_purchase , 
                                SUM(SALE_cost_amount) SALE_cost_amount ,
                                SUM(STOCK_cost_amount)  STOCK_cost_amount , 
                                branch_id,
                                (SUM(SALE_cost_amount)  + SUM(STOCK_cost_amount))  tot_cost, 
                                ((SUM(sale_amount) + SUM(stock_amount)) - (SUM(sale_cost_amount) + SUM(stock_cost_amount))) net_gp,
                                CASE 
                                    WHEN (SUM(sale_amount) + SUM(stock_amount)) > 0 
                                    THEN ROUND(((SUM(sale_amount) + SUM(stock_amount)) - (SUM(sale_cost_amount) + SUM(stock_cost_amount)))/ (SUM(sale_amount) + SUM(stock_amount)) * 100, 2) 
                                    END gp_per 
                            FROM (
                                SELECT 
                                    calendar_year, 
                                    calendar_month, 
                                    to_char (sale.sales_date, 'mm/yyyy') month_date, 
                                    SUM(sale.SALES_DTL_NET_AMOUNT) sale_amount, 
                                    0 grn_amount, 
                                    0 stock_amount, 
                                    0 stock_rcv_amount ,
                                    SUM(sale.cost_amount) SALE_cost_amount , 
                                    0 STOCK_cost_amount ,
                                    branch_id
                                FROM 
                                    vw_sale_sales_invoice sale, tbl_soft_calendar 
                                WHERE tbl_soft_calendar.calendar_date = sale.sales_date 
                                    AND sale.sales_date BETWEEN to_date ('".$from_date."', 'yyyy/mm/dd') and to_date ('".$to_date."', 'yyyy/mm/dd')
                                    AND sale.branch_id IN (".implode(",",$branch_multiple).") $supplier_ids
                                    AND LOWER(sales_type) IN ('si', 'pos', 'rpos', 'sr') 
                                GROUP BY branch_id, to_char (sale.sales_date, 'mm/yyyy'), calendar_year, calendar_month, calendar_month 
                                
                                UNION ALL 
                                SELECT 
                                    calendar_year, 
                                    calendar_month, 
                                    to_char (grn_date, 'mm/yyyy') month_date, 0 sale_amount,
                                    SUM(
                                    ( 
                                        CASE grn_type 
                                        WHEN 'pr' 
                                        THEN - tbl_purc_grn_dtl_total_amount 
                                        ELSE tbl_purc_grn_dtl_total_amount 
                                        END
                                    )) grn_amount, 
                                    0 stock_amount, 
                                    0 stock_rcv_amount , 
                                    0 SALE_cost_amount ,
                                    0 STOCK_cost_amount ,
                                    branch_id
                                FROM 
                                    vw_purc_grn, tbl_soft_calendar 
                                WHERE tbl_soft_calendar.calendar_date = grn_date 
                                    AND grn_date BETWEEN to_date ('".$from_date."', 'yyyy/mm/dd') and to_date ('".$to_date."', 'yyyy/mm/dd')
                                    AND branch_id IN (".implode(",",$branch_multiple).") $supplier_ids 
                                    AND LOWER(grn_type) IN ('grn', 'pi', 'grn', 'pi')
                                GROUP BY branch_id, to_char (grn_date, 'mm/yyyy'), calendar_year, calendar_month 
                                
                                UNION ALL 
                                SELECT 
                                    calendar_year, 
                                    calendar_month,
                                    to_char (stock_date, 'mm/yyyy') month_date, 
                                    0 sale_amount, 
                                    0 grn_amount, 
                                    SUM(stock_dtl_amount) stock_amount, 
                                    0 stock_rcv_amount ,
                                    0 SALE_cost_amount , 
                                    SUM(cost_amount)  STOCK_cost_amount ,
                                    branch_id
                                FROM
                                    vw_inve_stock, tbl_soft_calendar 
                                WHERE tbl_soft_calendar.calendar_date = stock_date 
                                    AND stock_date BETWEEN to_date ('".$from_date."', 'yyyy/mm/dd') and to_date ('".$to_date."', 'yyyy/mm/dd')
                                    AND branch_id IN (".implode(",",$branch_multiple).") $supplier_ids 
                                    AND LOWER(stock_code_type) IN ('st', 'st') 
                                GROUP BY branch_id, to_char (stock_date, 'mm/yyyy'), calendar_year, calendar_month 
                                
                                UNION ALL 
                                SELECT 
                                    calendar_year, 
                                    calendar_month, 
                                    to_char (stock_date, 'mm/yyyy') month_date,
                                    0 sale_amount, 
                                    0 grn_amount, 
                                    0 stock_amount, 
                                    SUM(stock_dtl_amount) stock_rcv_amount , 
                                    0 SALE_cost_amount , 
                                    0 STOCK_cost_amount ,
                                    branch_id
                                FROM 
                                    vw_inve_stock, tbl_soft_calendar 
                                WHERE tbl_soft_calendar.calendar_date = stock_date 
                                    AND stock_date BETWEEN to_date ('".$from_date."', 'yyyy/mm/dd') and to_date ('".$to_date."', 'yyyy/mm/dd')
                                    AND branch_id IN (".implode(",",$branch_multiple).") $supplier_ids
                                    AND LOWER(stock_code_type) IN ('str', 'str') 
                                GROUP BY branch_id, to_char (stock_date, 'mm/yyyy'), calendar_year, calendar_month
                            ) kaka 
                            GROUP BY branch_id, month_date, calendar_year, calendar_month 
                            ORDER BY calendar_year, calendar_month";

//dd($query);


                            $ResultList = DB::select($query);
                            // dd($ResultList);
                        @endphp
                        @php
                            $grn_amt = 0;
                            $stock_rcv = 0;
                            $tot_purc = 0;
                            $sale_amt = 0;
                            $stock_amt = 0;
                            $tot_sale = 0;
                            $sale_cost_amt = 0;
                            $stock_cost = 0;
                            $tot_cost = 0;
                            $gp_per = 0;
                            $net_gp = 0;
                        @endphp
                        @foreach($ResultList as $key => $list)
                            @php
                                $grn_amt += $list->grn_amount;
                                $stock_rcv += $list->stock_rcv_amount;
                                $tot_purc += $list->tot_purchase;
                                $sale_amt += $list->sale_amount;
                                $stock_amt += $list->stock_amount;
                                $tot_sale += $list->tot_sale;
                                $sale_cost_amt += $list->sale_cost_amount;
                                $stock_cost += $list->stock_cost_amount;
                                $tot_cost += $list->tot_cost;
                                $gp_per += $list->gp_per;
                                $net_gp += $list->net_gp;
                            @endphp
                            
                        <tbody class="item_row">
                            <tr data-id="{{ $list->branch_id }}">
                                <td>{{ $key+1 }}</td>
                                <td>{{ $list->month_date }}</td>
                                <td class="text-right">{{ number_format($list->grn_amount, 3) }}</td>
                                <td class="text-right">{{ number_format($list->stock_rcv_amount, 3) }}</td>
                                <td class="text-right">{{ number_format($list->tot_purchase, 3) }}</td>
                                <td class="text-right">{{ number_format($list->sale_amount, 3) }}</td>
                                <td class="text-right">{{ number_format($list->stock_amount, 3) }}</td>
                                <td class="text-right">{{ number_format($list->tot_sale, 3) }}</td>
                                <td class="text-right">{{ number_format($list->sale_cost_amount, 3) }}</td>
                                <td class="text-right">{{ number_format($list->stock_cost_amount, 3) }}</td>
                                <td class="text-right">{{ number_format($list->tot_cost, 3) }}</td>
                                <td class="text-right">{{ number_format($list->gp_per, 3) }}</td>
                                <td class="text-right">{{ number_format($list->net_gp, 3) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        @php
                            $grand_avg_gp_perc = 0;
                            if($sale_amt+$stock_amt > 0){
                                $grand_avg_gp_perc = (((($sale_amt+$stock_amt)-($sale_cost_amt+$stock_cost)) / ($sale_amt+$stock_amt))*100);
                            }
                        @endphp
                        <tr class="grand_total">
                            <td class="rep-font-bold">Grand Total</td>
                            <td class="rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{ number_format($grn_amt, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($stock_rcv, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($tot_purc, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($sale_amt, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($stock_amt, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($tot_sale, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($sale_cost_amt, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($stock_cost, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($tot_cost, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($grand_avg_gp_perc, 3)  }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($net_gp, 3)  }}</td>
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
    var xhrGetData = true;
    $(document).on('click','td',function(){
        var thix = $(this);
        var tbody = thix.parents('tbody.item_row');
        var tr = thix.parents('tr');
        var branch_id = tr.attr('data-id');
        var validate = true;
        if(tbody.length == 1){
            if(valueEmpty(branch_id)){
                toastr.error("Branch not Found");
                validate = false;
                return true;
            }
            if(validate && xhrGetData){
                $('body').addClass('pointerEventsNone');
                xhrGetData = false;
                var formData = {
                    report_case : 'category_wise_profit',
                    report_type: 'static',
                    date_from: '{{$data['from_date']}}',
                    date_to: '{{$data['to_date']}}',
                    form_file_type: 'report',
                    report_business_id : '{{auth()->user()->business_id}}',
                    'report_branch_ids[0]' : branch_id,
                };
                var url = "{{ action('Report\UserReportsController@staticStore', ['static','category_wise_profit','']) }}";
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        console.log(response);
                        if(response.status == 'success'){
                            toastr.success(response.message);
                            window.open(response['data']['url'], parseInt(Math.random()*10000000000));
                        }else{
                            toastr.error(response.message);
                        }
                        xhrGetData = true;
                        $('body').removeClass('pointerEventsNone');
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGetData = true;
                        $('body').removeClass('pointerEventsNone');
                    }
                });
            }
        }

    })
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