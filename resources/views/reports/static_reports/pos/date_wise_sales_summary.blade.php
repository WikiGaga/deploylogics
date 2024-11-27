@extends('layouts.report')
@section('title', 'Date Wise Sales Summary')

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
        $supplierDtl = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->where('supplier_id',$data['supplier_ids'])->first();
   @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y H:s:i', strtotime($data['from_date_time']))." to ". date('d-m-Y H:s:i', strtotime($data['to_date_time']))." "}}</span>
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
                @if(isset($data['supplier_ids']) && !empty($data['supplier_ids']))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        <span style="color: #5578eb;">{{" ".$supplierDtl->supplier_name." "}}</span>
                    </h6>
                @endif
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->get('group_item_name_string'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Group:</span>
                        @foreach($product_groups as $product_group)
                            <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['product_ids'] as $product)
                            <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
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
                        if(isset($data['supplier_ids']) && !empty($data['supplier_ids']) != 0){
                            $where .= " and supplier_id = ".$supplierDtl->supplier_id."";
                        }
                        if(count($data['product_ids']) != 0){
                            $where .= " and product_name in ('".implode("','",$data['product_ids'])."') ";
                        }
                        if(count($data['product_group']) != 0){
                            $inner_where = "";
                            foreach($data['product_group'] as $product_group){
                                $group_item_item = \App\Models\TblPurcGroupItem::where('group_item_id',$product_group)->first();
                                if($group_item_item->group_item_level == 1){

                                }
                                if($group_item_item->group_item_level == 2){
                                    $group_items = \App\Models\TblPurcGroupItem::where('parent_group_id',$product_group)->pluck('group_item_id')->toArray();

                                    $inner_where .= " group_item_id in (".implode(",",$group_items).") OR";
                                }
                                if($group_item_item->group_item_level == 3){
                                    $inner_where .= " group_item_id = $product_group OR ";
                                }
                            }
                            if(!empty($inner_where)){
                                $inner_where = rtrim($inner_where, " OR ");
                                $where .= "and ( ".$inner_where." ) ";
                            }
                        }




                        $qry = "SELECT
                            BRANCH_ID ,
                            BRANCH_NAME ,
                            SALES_DATE ,

                            SUM(SALE_QTY)  SALE_QTY  ,
                            ABS(SUM(SALE_RET_QTY)) SALE_RET_QTY ,

                            SUM(SALE_QTY) - ABS(SUM(SALE_RET_QTY)) SALE_NET_QTY ,

                            SUM(SALE_AMOUNT) SALE_AMOUNT ,
                            SUM(SALE_RET_AMOUNT) SALE_RET_AMOUNT ,
                            SUM(SALE_AMOUNT) - ABS(SUM(SALE_RET_AMOUNT)) SALE_NET_AMOUNT ,

                            SUM(COST_SALES_AMOUNT) COST_SALES_AMOUNT ,
                            SUM(COST_SALES_RET_AMOUNT) COST_SALES_RET_AMOUNT ,
                            SUM(COST_SALES_AMOUNT) - SUM(COST_SALES_RET_AMOUNT) COST_SALES_NET_AMOUNT,

                            (SUM(SALE_AMOUNT) - SUM(SALE_RET_AMOUNT) )-  (SUM(COST_SALES_AMOUNT) - SUM(COST_SALES_RET_AMOUNT)) NET_PROFIT_SALE


                            FROM
                            (

                            select
                            BRANCH_ID ,
                            BRANCH_NAME ,
                            SALES_DATE ,
                            CASE
                              WHEN SALES_TYPE = 'POS' THEN SUM(NVL(SALES_DTL_QUANTITY,0))  ELSE 0
                            END SALE_QTY  ,
                            CASE
                              WHEN SALES_TYPE = 'RPOS' THEN SUM(NVL(SALES_DTL_QUANTITY,0)) ELSE 0
                            END   SALE_RET_QTY ,
                            CASE
                              WHEN SALES_TYPE = 'POS' THEN SUM(NVL(SALES_DTL_NET_AMOUNT,0)) ELSE 0
                            END   SALE_AMOUNT ,
                            CASE
                              WHEN SALES_TYPE = 'RPOS' THEN SUM(NVL(SALES_DTL_NET_AMOUNT,0)) ELSE 0
                            END   SALE_RET_AMOUNT ,

                            CASE
                              WHEN SALES_TYPE = 'POS'  THEN(NVL(SUM(NVL(COST_AMOUNT,0)) ,0)) ELSE 0
                            END   COST_SALES_AMOUNT ,
                            CASE
                              WHEN SALES_TYPE = 'RPOS' THEN  (NVL(SUM(NVL(COST_AMOUNT * -1 ,0)) ,0)) ELSE 0
                            END   COST_SALES_RET_AMOUNT

                             FROM  
                                VW_SALE_SALES_INVOICE 
                             WHERE branch_id in (".implode(",",$data['branch_ids']).") 
                                and (created_at between to_date('".$data['from_date_time']."','yyyy/mm/dd HH24:MI:SS') and to_date('".$data['to_date_time']."','yyyy/mm/dd HH24:MI:SS') )
                                $where
                            GROUP BY BRANCH_ID , BRANCH_NAME , SALES_DATE , SALES_TYPE

                            )
                              gaga
                            GROUP BY
                            BRANCH_ID ,
                            BRANCH_NAME ,
                            SALES_DATE
                            order by BRANCH_NAME, SALES_DATE";

                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        // dd($getdata);
                        $list = [];
                        foreach ($getdata as $list_row){
                            $list[$list_row->branch_name][$list_row->sales_date][] = $list_row;
                        }
                        //dd($list);
                    $grand_sale_qty = 0;
                    $grand_sale_ret_qty = 0;
                    $grand_sale_net_qty = 0;
                    $grand_sale_amount = 0;
                    $grand_sale_ret_amount = 0;
                    $grand_sale_net_amount = 0;
                    $grand_cost_sales_amount = 0;
                    $grand_cost_sales_ret_amount = 0;
                    $grand_cost_sales_net_amount = 0;
                    $grand_net_profit_sale = 0;
                    @endphp
                    <table width="100%" id="date_wise_summary_sales" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Date</th>
                            <th class="text-center">Sale Qty</th>
                            <th class="text-center">Return Qty</th>
                            <th class="text-center">Net Qty</th>
                            <th class="text-center">POS Sales</th>
                            <th class="text-center">POS Sales Return</th>
                            <th class="text-center">Net POS Sales</th>
                            <th class="text-center">Cost Sales</th>
                            <th class="text-center">Cost Sales Return</th>
                            <th class="text-center">Net Cost Sales</th>
                            <th class="text-center">Net Profit on Sales</th>
                            <th width="60px" class="text-center"></th>
                        </tr>
                        @foreach($list as $bn=>$br_row)
                            @php
                                $sale_qty = 0;
                                $sale_ret_qty = 0;
                                $sale_net_qty = 0;
                                $sale_amount = 0;
                                $sale_ret_amount = 0;
                                $sale_net_amount = 0;
                                $cost_sales_amount = 0;
                                $cost_sales_ret_amount = 0;
                                $cost_sales_net_amount = 0;
                                $net_profit_sale = 0;
                            @endphp
                            <tr>
                                <td colspan="12"><h6>{{$bn}}</h6></td>
                            </tr>
                            @foreach($br_row as $rows)
                                @foreach($rows as $row)
                                    @php
                                        $sna = $row->sale_net_amount;
                                        $csna = $row->cost_sales_net_amount;
                                        $nps = $row->net_profit_sale;
                                        $perc1 = 0;
                                        $perc2 = 0;
                                        if($nps != 0 && $sna != 0){
                                           $perc1 = ($nps / $sna) * 100;
                                        }
                                        if($nps != 0 && $csna != 0){
                                           $perc2 = ($nps / $csna) * 100;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{date('d-m-Y',strtotime($row->sales_date))}}</td>
                                        <td class="text-right">{{number_format($row->sale_qty,3)}}</td>
                                        <td class="text-right">{{'-'.number_format($row->sale_ret_qty,3)}}</td>
                                        <td class="text-right">{{number_format($row->sale_net_qty,3)}}</td>
                                        <td class="text-right">{{number_format($row->sale_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->sale_ret_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->sale_net_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->cost_sales_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->cost_sales_ret_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->cost_sales_net_amount,3)}}</td>
                                        <td class="text-right">{{number_format($row->net_profit_sale,3)}}</td>
                                        <td class="text-right">
                                            <div>{{ROUND($perc1,2)}}</div>
                                            <div style="background: #b8b8b8;">{{ROUND($perc2,2)}}</div>
                                        </td>
                                    </tr>
                                @endforeach
                                @php
                                    $sale_qty += $row->sale_qty;
                                    $sale_ret_qty += $row->sale_ret_qty;
                                    $sale_net_qty += $row->sale_net_qty;
                                    $sale_amount += $row->sale_amount;
                                    $sale_ret_amount += $row->sale_ret_amount;
                                    $sale_net_amount += $row->sale_net_amount;
                                    $cost_sales_amount += $row->cost_sales_amount;
                                    $cost_sales_ret_amount += $row->cost_sales_ret_amount;
                                    $cost_sales_net_amount += $row->cost_sales_net_amount;
                                    $net_profit_sale += $row->net_profit_sale;
                                @endphp
                            @endforeach
                            @php
                                $grand_sale_qty += $sale_qty;
                                $grand_sale_ret_qty += $sale_ret_qty;
                                $grand_sale_net_qty += $sale_net_qty;
                                $grand_sale_amount += $sale_amount;
                                $grand_sale_ret_amount += $sale_ret_amount;
                                $grand_sale_net_amount += $sale_net_amount;
                                $grand_cost_sales_amount += $cost_sales_amount;
                                $grand_cost_sales_ret_amount += $cost_sales_ret_amount;
                                $grand_cost_sales_net_amount += $cost_sales_net_amount;
                                $grand_net_profit_sale += $net_profit_sale;
                            @endphp
                            <tr class="sub_total">
                                <td class="rep-font-bold">Branch Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_qty,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_ret_qty,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_net_qty,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_ret_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sale_net_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($cost_sales_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($cost_sales_ret_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($cost_sales_net_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($net_profit_sale,3)}}</td>
                                <td class="text-right rep-font-bold"></td>
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_qty,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_ret_qty,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_net_qty,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_ret_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_sale_net_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_cost_sales_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_cost_sales_ret_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_cost_sales_net_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_net_profit_sale,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                        </tr>
                    </table>
                    @php
                        $gsna = $grand_sale_net_amount;
                        $gcsna = $grand_cost_sales_net_amount;
                        $gnps = $grand_net_profit_sale;
                        $gperc1 = 0;
                        $gperc2 = 0;
                        if($gnps != 0 && $gsna != 0){
                           $gperc1 = ($gnps / $gsna) * 100;
                        }
                        if($gnps != 0 && $gcsna != 0){
                           $gperc2 = ($gnps / $gcsna) * 100;
                        }
                    @endphp
                    <table width="200px" style="float: right;">
                        <tbody>
                        <tr>
                            <th><b>P\L RATIO SALE:</b></th>
                            <td class="text-right" style="background: #b8b8b8;">{{@ROUND($gperc1,2)}} %</td>
                        </tr>
                        <tr>
                            <th><b>P\L RATIO COST:</b></th>
                            <td class="text-right">{{@ROUND($gperc2,2)}} %</td>
                        </tr>
                        </tbody>
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



