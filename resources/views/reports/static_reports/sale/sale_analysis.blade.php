@extends('layouts.report')
@section('title', 'Sale Analysis')

@section('pageCSS')
    <style>
        /* Styles go here */
        .sub_category_total{
            font-size: 12px;
            font-weight:bold;
            background-color:#fcd49f;
        }
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
                @if(count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @php $i = 0; @endphp
                        @foreach($data['product_ids'] as $product)
                            @php $i++; @endphp
                            @if($i <= 7) 
                                <span style="color: #5578eb;">{{$product}}</span><span style="color: #fd397a;">, </span>
                            @endif
                        @endforeach
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

$vendorfrom = "";
$vendorjoin = "";
// Vendor Wise whereclause
if(count($data['supplier_ids']) != 0){
    $vendorfrom = ", VW_PURC_PRODUCT_FOC_PROD_WISE SUP_PROD";
    $vendorjoin = " AND VW_SALE_SALES_INVOICE.PRODUCT_ID = SUP_PROD.PRODUCT_ID";
}

$qry = "SELECT 
  CASE
    WHEN SALES_TYPE = 'POS' 
    THEN 'Sale Invoice' 
    WHEN SALES_TYPE = 'RPOS' 
    THEN 'Sale Return' 
    ELSE '' 
  END AS SALES_TYPE,
  GROUP_ITEM_ID,
  GROUP_ITEM_NAME,
  GROUP_ITEM_PARENT_ID,
  GROUP_ITEM_PARENT_NAME,
  PRODUCT_BARCODE_BARCODE,
  PRODUCT_NAME,
  QUANTITY,
  G_AMOUNT,
  DISCOUNT_AMOUNT,
  NET_AMOUNT,
  perc_amount,
  TRAN_NUMBERS,
  GROUP_ITEM_AMOUNT_TOTAL,
  GROUP_ITEM_PARENT_AMOUNT_TOTAL,
  GROUP_ITEM_QTY_TOTAL,
  GROUP_ITEM_PARENT_QTY_TOTAL,
  GROUP_ITEM_DISCOUNT_TOTAL,
  GROUP_ITEM_PARENT_DISCOUNT_TOTAL,
  SUM(NET_AMOUNT) over (
    PARTITION BY GROUP_ITEM_ID 
ORDER BY GROUP_ITEM_ID
) AS GROUP_ITEM_TOTAL,
GROUP_ITEM_PARENT_NET_AMOUNT 
FROM
  (SELECT 
    SALES_TYPE,
    GROUP_ITEM_ID,
    GROUP_ITEM_NAME,
    GROUP_ITEM_PARENT_ID,
    GROUP_ITEM_PARENT_NAME,
    PRODUCT_BARCODE_BARCODE,
    PRODUCT_NAME,
    SUM(SALES_DTL_QUANTITY) QUANTITY,
    SUM(SALES_DTL_AMOUNT) G_AMOUNT,
    SUM(
      NVL (SALES_DTL_DISC_AMOUNT, 0) + NVL (EXT_DISC_AMOUNT, 0)
    ) DISCOUNT_AMOUNT,
    (
      SUM(SALES_DTL_AMOUNT) - SUM(
        NVL (SALES_DTL_DISC_AMOUNT, 0) + NVL (EXT_DISC_AMOUNT, 0)
      )
    ) NET_AMOUNT,
    ROUND(
      100 * (
        SUM(SALES_DTL_AMOUNT) / SUM(SUM(SALES_DTL_AMOUNT)) over ()
      ),
      2
    ) perc_amount,
    COUNT(DISTINCT SALES_ID) TRAN_NUMBERS,
    SUM(SUM(SALES_DTL_AMOUNT)) over (
      PARTITION BY GROUP_ITEM_ID 
  ORDER BY GROUP_ITEM_ID
  ) AS GROUP_ITEM_AMOUNT_TOTAL,
  SUM(SUM(SALES_DTL_AMOUNT)) over (
    PARTITION BY GROUP_ITEM_PARENT_ID 
  ORDER BY GROUP_ITEM_PARENT_ID
  ) AS GROUP_ITEM_PARENT_AMOUNT_TOTAL,
  SUM(SUM(SALES_DTL_QUANTITY)) over (
    PARTITION BY GROUP_ITEM_ID 
  ORDER BY GROUP_ITEM_ID
  ) AS GROUP_ITEM_QTY_TOTAL,
  SUM(SUM(SALES_DTL_QUANTITY)) over (
    PARTITION BY GROUP_ITEM_PARENT_ID 
  ORDER BY GROUP_ITEM_PARENT_ID
  ) AS GROUP_ITEM_PARENT_QTY_TOTAL,
  SUM(
    SUM(
      NVL (SALES_DTL_DISC_AMOUNT, 0) + NVL (EXT_DISC_AMOUNT, 0)
    )
  ) over (
    PARTITION BY GROUP_ITEM_ID 
  ORDER BY GROUP_ITEM_ID
  ) AS GROUP_ITEM_DISCOUNT_TOTAL,
  SUM(
    SUM(
      NVL (SALES_DTL_DISC_AMOUNT, 0) + NVL (EXT_DISC_AMOUNT, 0)
    )
  ) over (
    PARTITION BY GROUP_ITEM_PARENT_ID 
  ORDER BY GROUP_ITEM_PARENT_ID
  ) AS GROUP_ITEM_PARENT_DISCOUNT_TOTAL,
  SUM(
    (
      SUM(SALES_DTL_AMOUNT) - SUM(
        NVL (SALES_DTL_DISC_AMOUNT, 0) + NVL (EXT_DISC_AMOUNT, 0)
      )
    )
  ) over (
    PARTITION BY GROUP_ITEM_PARENT_ID 
  ORDER BY GROUP_ITEM_PARENT_ID
  ) AS GROUP_ITEM_PARENT_NET_AMOUNT 
  FROM
    VW_SALE_SALES_INVOICE
    $vendorfrom
  WHERE branch_id in (".implode(",",$data['branch_ids']).")
    and (created_at between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
    $vendorjoin
    $where
  GROUP BY SALES_TYPE,
    GROUP_ITEM_ID,
    GROUP_ITEM_NAME,
    GROUP_ITEM_PARENT_ID,
    GROUP_ITEM_PARENT_NAME,
    PRODUCT_BARCODE_BARCODE,
    PRODUCT_NAME) abc 
ORDER BY GROUP_ITEM_AMOUNT_TOTAL DESC,
  GROUP_ITEM_ID,
  GROUP_ITEM_PARENT_AMOUNT_TOTAL DESC,
  GROUP_ITEM_PARENT_ID,
  G_AMOUNT DESC,
  GROUP_ITEM_NAME";
//dd($qry);
                        $getdata = \Illuminate\Support\Facades\DB::select($qry);
                        //dd($getdata);

                        $list = [];
                        foreach ($getdata as $list_row){
                            $list[$list_row->sales_type][$list_row->group_item_parent_name][$list_row->group_item_name][] = $list_row;
                            //$list[$list_row->group_item_parent_name]['info'] = $list_row;
                            //$list[$list_row->group_item_parent_name]['items'][$list_row->group_item_name]['info'] = $list_row;
                            //$list[$list_row->group_item_parent_name]['items'][$list_row->group_item_name]['items'][] = $list_row;
                        }
                        //dd($list);
                    @endphp
                    <table width="100%" id="rep_sale_analysis_datatable" class="static_report_table table bt-datatable table-bordered">
                    <tr class="sticky-header">
                            <th width="6%" class="text-center">S.#</th>
                            <th width="15%" class="text-center">Barcode</th>
                            <th width="23%" class="text-left">Product Item</th>
                            <th width="8%" class="text-center">Quantity/WT</th>
                            <th width="8%" class="text-center">Avg. Rate</th>
                            <th width="12%" class="text-center">G.Amount</th>
                            <th width="10%" class="text-center">Discount(%)</th>
                            <th width="12%" class="text-center">Amount</th>
                            <th width="6%" class="text-center">No. of Trans</th>
                        </tr>
                        @php
                            $gtotqty = 0;
                            $gtotamount = 0;
                            $gtotdiscamnt = 0;
                            $gtotnetamnt = 0;
                        @endphp
                        @foreach($list as $sale_type_key=>$sale_type)
                            @php
                                $type_name = ucwords(strtolower($sale_type_key));
                            @endphp
                            <tr class="outer_total">
                                <td colspan="9">{{ucwords(strtolower($sale_type_key))}}</td>
                            </tr>
                            @php
                                $mtotqty = 0;
                                $mtotamount = 0;
                                $mtotdiscamnt = 0;
                                $mtotnetamnt = 0;
                            @endphp
                            @foreach($sale_type as $category_key=>$category_row)
                                @php
                                    $category_name = ucwords(strtolower($category_key));
                                @endphp
                                <tr class="inner_total">
                                    <td colspan="9">&nbsp;&nbsp;{{ucwords(strtolower($category_key))}}</td>
                                </tr>
                                @php
                                    $stotqty = 0;
                                    $stotamount = 0;
                                    $stotdiscamnt = 0;
                                    $stotnetamnt = 0;
                                @endphp
                                @foreach($category_row as $sub_category_key=>$sub_category_row)
                                    @php
                                        $sub_category_name = ucwords(strtolower($sub_category_key));
                                    @endphp
                                    <tr class="sub_category_total">
                                        <td colspan="9">&nbsp;&nbsp;&nbsp;&nbsp;{{ucwords(strtolower($sub_category_key))}}</td>
                                    </tr>
                                    @php
                                        $ki = 1;
                                        $totqty = 0;
                                        $totamount = 0;
                                        $totdiscamnt = 0;
                                        $totnetamnt = 0;
                                    @endphp
                                    @foreach($sub_category_row as $i_key=>$item)
                                        @php
                                            if($item->sales_type == "Sale Invoice"){
                                                $quantity = $item->quantity;
                                                $g_amount = $item->g_amount;
                                                $discount_amount = $item->discount_amount;
                                                $disc_per = @round($item->discount_amount / $item->g_amount * 100,2);
                                                $net_amount = $item->net_amount;
                                                $avg_rate = @round($item->g_amount/$item->quantity,3);
                                            }
                                            if($item->sales_type == "Sale Return"){
                                                $quantity = abs($item->quantity) *-1;
                                                $g_amount = abs($item->g_amount) *-1;
                                                $discount_amount = abs($item->discount_amount) *-1;
                                                $disc_per = @round($item->discount_amount / $item->g_amount * 100,2)*-1;
                                                $net_amount = abs($item->net_amount) *-1;
                                                $avg_rate = @round(abs($item->g_amount/$item->quantity),3)*-1;
                                            }

                                            $totqty = $totqty + $quantity;
                                            $totamount = $totamount + $g_amount;
                                            $totdiscamnt = $totdiscamnt + $discount_amount;
                                            $totnetamnt = $totnetamnt + $net_amount;

                                            $stotqty = $stotqty + $quantity;
                                            $stotamount = $stotamount + $g_amount;
                                            $stotdiscamnt = $stotdiscamnt + $discount_amount;
                                            $stotnetamnt = $stotnetamnt + $net_amount;

                                            $mtotqty = $mtotqty + $quantity;
                                            $mtotamount = $mtotamount + $g_amount;
                                            $mtotdiscamnt = $mtotdiscamnt + $discount_amount;
                                            $mtotnetamnt = $mtotnetamnt + $net_amount;

                                            $gtotqty = $gtotqty + $quantity;
                                            $gtotamount = $gtotamount + $g_amount;
                                            $gtotdiscamnt = $gtotdiscamnt + $discount_amount;
                                            $gtotnetamnt = $gtotnetamnt + $net_amount;

                                            if($item->sales_type == "Sale Invoice"){
                                                $tdisc_per = @round($totdiscamnt / $totamount * 100,2);
                                                $sdisc_per = @round($stotdiscamnt / $stotamount * 100,2);
                                                $mdisc_per = @round($mtotdiscamnt / $mtotamount * 100,2);

                                            }
                                            if($item->sales_type == "Sale Return"){
                                                $tdisc_per = @round($totdiscamnt / $totamount * 100,2) * -1;
                                                $sdisc_per = @round($stotdiscamnt / $stotamount * 100,2) * -1;
                                                $mdisc_per = @round($mtotdiscamnt / $mtotamount * 100,2) * -1;
                                            }


                                        @endphp
                                        <tr>
                                            <td class="text-center">{{$ki}}</td>
                                            <td>{{$item->product_barcode_barcode}}</td>
                                            <td>{{$item->product_name}}</td>
                                            <td class="text-center">{{number_format($item->quantity,2)}}</td>
                                            <td class="text-right">{{@number_format($avg_rate,3)}}</td>
                                            <td class="text-right">{{number_format($g_amount,3)}}</td>
                                            <td class="text-right">{{number_format($discount_amount,3)}}<br>{{$disc_per}}%</td>
                                            <td class="text-right">{{number_format($net_amount,3)}}</td>
                                            <td class="text-right">{{$item->tran_numbers}}</td>
                                        </tr>
                                        @php
                                            $ki += 1;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>{{$sub_category_name}} Total: </strong></td>
                                        <td class="text-center"><strong>{{number_format($totqty,2)}}</strong></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"><strong>{{number_format($totamount,3)}}</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($totdiscamnt,3)}}<br>
                                                {{ $tdisc_per}}%
                                            </strong>
                                        </td>
                                        <td class="text-right"><strong>{{number_format($totnetamnt,3)}}</strong></td>
                                        <td class="text-right"></td>
                                    </tr>
                                @endforeach
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>{{$category_name}} Sub Total: </strong></td>
                                        <td class="text-center"><strong>{{number_format($stotqty,0)}}</strong></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"><strong>{{number_format($stotamount,3)}}</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{number_format($stotdiscamnt,3)}}<br>
                                                {{ $sdisc_per}}%
                                            </strong>
                                        </td>
                                        <td class="text-right"><strong>{{number_format($stotnetamnt,3)}}</strong></td>
                                        <td class="text-right"></td>
                                    </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="text-right"><strong>{{$type_name}} Total: </strong></td>
                                <td class="text-center"><strong>{{number_format($mtotqty,2)}}</strong></td>
                                <td class="text-right"></td>
                                <td class="text-right"><strong>{{number_format($mtotamount,3)}}</strong></td>
                                <td class="text-right">
                                    <strong>
                                        {{number_format($mtotdiscamnt,3)}}<br>
                                        {{ $mdisc_per}}%
                                    </strong>
                                </td>
                                <td class="text-right"><strong>{{number_format($mtotnetamnt,3)}}</strong></td>
                                <td class="text-right"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-right"><strong>Grand Total: </strong></td>
                            <td class="text-center"><strong>{{number_format($gtotqty,2)}}</strong></td>
                            <td class="text-right"></td>
                            <td class="text-right"><strong>{{number_format($gtotamount,3)}}</strong></td>
                            <td class="text-right">
                                <strong>
                                    {{number_format($gtotdiscamnt,3)}}<br>
                                    {{ @round($gtotdiscamnt / $gtotamount * 100,2) }}%
                                </strong>
                            </td>
                            <td class="text-right"><strong>{{number_format($gtotnetamnt,3)}}</strong></td>
                            <td class="text-right"></td>
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
                $("#rep_sale_analysis_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



