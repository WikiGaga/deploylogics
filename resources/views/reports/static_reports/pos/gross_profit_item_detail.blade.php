@extends('layouts.report')
@section('title', 'Gross Profit')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        tbody.gross_profit_list tr:hover {
            background: antiquewhite;
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
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get(['branch_id','branch_name']); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(!empty($data['supplier_ids']))
                    @php $suppliers = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->whereIn('supplier_id',$data['supplier_ids'])->get(); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        @foreach($suppliers as $supplier)
                            <span style="color: #5578eb;">{{$supplier->supplier_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(!empty($data['product_ids']))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['product_ids'] as $product)
                            <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if($data['first_level'] != "" && $data['first_level'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('tbl_purc_group_item')->where('group_item_id',$data['first_level'])->first('group_item_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">First Level Group:</span>
                        <span style="color: #5578eb;">{{$product_groups->group_item_name}}</span>
                    </h6>
                @endif
                @if($data['last_level'] != "" && $data['last_level'] != null)
                    @php $last_level = \Illuminate\Support\Facades\DB::table('tbl_purc_group_item')->where('group_item_id',$data['last_level'])->first('group_item_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Last Level Group:</span>
                        <span style="color: #5578eb;">{{$last_level->group_item_name}}</span>
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

                        if(!empty($data['supplier_ids'])){
                            $where .= " AND SUPPLIER_ID IN (".implode(",",$data['supplier_ids']).") ";
                        }
                        if(!empty($data['product_ids']))
                        {
                            $where .= " AND product_name IN ('".implode("','",$data['product_ids'])."') ";
                        }

                        $qry = "SELECT
                            PRODUCT_ID ,
                            PRODUCT_NAME ,

                            ROUND(SUM(SALE_QTY),0) SALE_QTY ,
                            ROUND(SUM(SALE_RET_QTY * -1 ),0) SALE_RET_QTY ,
                            ROUND(SUM(SALE_QTY) + SUM(SALE_RET_QTY),0) NET_QTY ,

                             CASE WHEN ROUND(SUM(SALE_QTY) + SUM(SALE_RET_QTY),0) > 0
                             THEN ROUND(SUM(SALE_AMOUNT) / ROUND(SUM(SALE_QTY) + SUM(SALE_RET_QTY),0) ,0) END  AVG_SALE_RATE ,

                            ROUND(SUM(SALE_AMOUNT),0) SALE_AMOUNT,
                            ROUND(SUM(COST_AMOUNT),0) COST_AMOUNT,

                            CASE WHEN ROUND(SUM(SALE_QTY) + SUM(SALE_RET_QTY),0) > 0
                            THEN  ROUND(SUM(NET_GP) / ROUND(SUM(SALE_QTY) + SUM(SALE_RET_QTY),0) ,2)  END PER_ITEM_GP ,

                            ROUND(SUM(NET_GP),0)  NET_GP ,

                            CASE WHEN SUM(SALE_AMOUNT) > 0 THEN  ROUND( SUM(NET_GP)  /  SUM(SALE_AMOUNT) * 100 ,2) END  GP_PER


                            FROM
                            (

                            select
                            GROUP_ITEM_PARENT_ID,
                            GROUP_ITEM_PARENT_NAME,
                            GROUP_ITEM_ID,
                            GROUP_ITEM_NAME,
                            PRODUCT_ID,
                            PRODUCT_NAME,
                            BRANCH_ID , BRANCH_NAME ,
                            CASE
                              WHEN SALES_TYPE = 'POS' THEN SUM(NVL(SALES_DTL_QUANTITY,0))  ELSE 0
                            END SALE_QTY,
                            CASE
                              WHEN SALES_TYPE = 'RPOS' THEN SUM(NVL(SALES_DTL_QUANTITY,0)) ELSE 0
                            END   SALE_RET_QTY,
                             SUM(SALES_DTL_NET_AMOUNT)   SALE_AMOUNT ,
                             SUM(COST_AMOUNT)   COST_AMOUNT,
                             SUM(NVL(SALES_DTL_NET_AMOUNT,0))  -   SUM(NVL(COST_AMOUNT,0))  NET_GP
                             FROM  VW_SALE_SALES_INVOICE
                             where branch_id in (".implode(",",$data['branch_ids']).")
                             and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI') )
                             and group_item_id = '".$data['last_level']."'
                             $where
                            GROUP BY
                            GROUP_ITEM_PARENT_ID,
                            GROUP_ITEM_PARENT_NAME,
                            GROUP_ITEM_ID ,
                            GROUP_ITEM_NAME ,
                            PRODUCT_ID ,
                            PRODUCT_NAME ,
                            BRANCH_ID , BRANCH_NAME , SALES_TYPE

                            )
                            gaga
                            GROUP BY

                            PRODUCT_ID ,
                            PRODUCT_NAME
                            order by  PRODUCT_NAME";
//dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        $ki = 1;
                        $sale_qty = 0;
                        $return_qty = 0;
                        $net_sale_qty = 0;
                        $sale_amount = 0;
                        $total_cost= 0;
                        $avg_gp_perc = 0;
                        $total_gp = 0;
                    @endphp
                    <table width="100%" id="gross_profit" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Sr No#</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Sale Qty</th>
                            <th class="text-center">Return Qty</th>
                            <th class="text-center">Net Sale Qty</th>
                            <th class="text-center">Sale Amount</th>
                            <th class="text-center">Total Cost</th>
                            <th class="text-center">Per Item GP</th>
                            <th class="text-center">GP %</th>
                            <th class="text-center">Total GP</th>
                        </tr>

                        <tbody class="gross_profit_list">
                        @foreach($getdata as $row)
                            <tr>
                                <td>{{$ki}}</td>
                                <td>{{$row->product_name}}</td>
                                <td class="text-right">{{number_format($row->sale_qty,3)}}</td>
                                <td class="text-right">{{number_format($row->sale_ret_qty,3)}}</td>
                                <td class="text-right">{{number_format($row->net_qty,3)}}</td>
                                <td class="text-right">{{number_format($row->sale_amount,3)}}</td>
                                @if($row->cost_amount != null)
                                <td class="text-right">{{number_format($row->cost_amount,3)}}</td>
                                @else
                                <td class="text-right">{{number_format(0,3)}}</td>
                                @endif
                                <td class="text-right">{{number_format($row->gp_per,3)}}</td>
                                <td class="text-right">{{number_format($row->per_item_gp,3)}}</td>
                                <td class="text-right">{{number_format($row->net_gp,3)}}</td>
                            </tr>
                            @php
                                $ki += 1;
                                $sale_qty += $row->sale_qty;
                                $return_qty += $row->sale_ret_qty;
                                $net_sale_qty += $row->net_qty;
                                $sale_amount += $row->sale_amount;
                                $total_cost += $row->cost_amount;
                                $avg_gp_perc += $row->gp_per;
                                $total_gp += $row->net_gp;
                            @endphp
                        @endforeach
                        </tbody>
                        @php
                            $grand_avg_gp_perc = 0;
                            /*if($total_cost != 0){
                                $grand_avg_gp_perc = $total_gp / $total_cost * 100;
                            }*/
                        @endphp
                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($sale_qty,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($return_qty,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($net_sale_qty,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($sale_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_cost,3)}}</td>
                            <td class="text-right rep-font-bold"> - </td>
                            <td class="text-right rep-font-bold"> - </td>
                            <td class="text-right rep-font-bold">{{number_format($total_gp,3)}}</td>
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



