@php set_time_limit(0) @endphp
@extends('layouts.report')
@section('title', 'Slow Moving Stock')

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
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get(['branch_name','branch_short_name']); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->select('group_item_name_string','group_item_id','group_item_name_code_string')->get(); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Group:</span>
                        @foreach($product_groups as $product_group)
                            <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center" colspan="9"></th>
                            <th class="text-center" colspan="3">Last Sale</th>
                        </tr>
                        <tr class="sticky-header">
                            <th class="text-center">Sr</th>
                            <th class="text-left">Branch Name</th>
                            <th class="text-left">Product Name</th>
                            <th class="text-left">Sales Qty</th>
                            <th class="text-left">Purchase Qty</th>
                            <th class="text-left">Sale Rate</th>
                            <th class="text-left">Purchase Rate</th>
                            <th class="text-left">Stock</th>
                            <th class="text-left">Reorder Count</th>
                            <th class="text-left">Date</th>
                            <th class="text-left">UOM</th>
                            <th class="text-left">Qty</th>
                        </tr>
                        
                        <tbody>
                            @php
                                $filter_group_item = "";
                                if(isset($product_groups) && count($product_groups) != 0){
                                    $filter_group_item = " AND ( ";
                                    $arr_count = count($product_groups) - 1;
                                    foreach ($product_groups as $k=>$product_group){
                                        $filter_group_item .= "group_item_name_code_string like '".$product_group->group_item_name_code_string."%'";
                                        if($arr_count != $k){
                                            $filter_group_item .= " OR ";
                                        }
                                    }
                                    $filter_group_item .= " ) ";
                                }
                                $query = "SELECT 
                                        SLOW.PRODUCT_ID,
                                        SLOW.PRODUCT_BARCODE_ID,
                                        SLOW.BRANCH_ID,
                                        SUM(REORDER_COUNT) REORDER_COUNT,
                                        PROD.PRODUCT_NAME,
                                        PROD.GROUP_ITEM_ID,
                                        GRO.GROUP_ITEM_NAME,
                                        PRATE.PRODUCT_BARCODE_PURCHASE_RATE PURCHASE_RATE,
                                        PRATE.PRODUCT_BARCODE_COST_RATE COST_RATE,
                                        SRATE.PRODUCT_BARCODE_SALE_RATE_RATE SALE_RATE,
                                        BAR.PRODUCT_BARCODE_BARCODE,
                                        BRAN.BRANCH_SHORT_NAME
                                    FROM 
                                        TBL_SLOW_MOVING_ITEMS SLOW
                                        JOIN TBL_PURC_PRODUCT PROD ON SLOW.PRODUCT_ID = PROD.PRODUCT_ID
                                        JOIN TBL_PURC_PRODUCT_BARCODE BAR ON SLOW.PRODUCT_BARCODE_ID = BAR.PRODUCT_BARCODE_ID
                                        JOIN VW_PURC_GROUP_ITEM GRO ON GRO.GROUP_ITEM_ID = PROD.GROUP_ITEM_ID
                                        JOIN TBL_PURC_PRODUCT_BARCODE_PURCH_RATE PRATE ON PRATE.PRODUCT_BARCODE_BARCODE = BAR.PRODUCT_BARCODE_BARCODE
                                        JOIN TBL_PURC_PRODUCT_BARCODE_SALE_RATE SRATE ON SRATE.PRODUCT_BARCODE_BARCODE = BAR.PRODUCT_BARCODE_BARCODE
                                        JOIN TBL_SOFT_BRANCH BRAN ON SLOW.BRANCH_ID = BRAN.BRANCH_ID
                                    WHERE
                                        SLOW.BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                        $filter_group_item
                                        AND PRATE.BRANCH_ID = SLOW.BRANCH_ID
                                        AND SRATE.BRANCH_ID = SLOW.BRANCH_ID
                                        AND SLOW.REORDER_DATE BETWEEN '". $data['from_date'] ."' AND '". $data['to_date'] ."'
                                        AND SRATE.PRODUCT_CATEGORY_ID = 2
                                    GROUP BY 
                                        SLOW.PRODUCT_ID,
                                        SLOW.PRODUCT_BARCODE_ID,
                                        SLOW.BRANCH_ID,
                                        PROD.PRODUCT_NAME,
                                        PROD.GROUP_ITEM_ID,
                                        GRO.GROUP_ITEM_NAME,
                                        PRATE.PRODUCT_BARCODE_PURCHASE_RATE,
                                        PRATE.PRODUCT_BARCODE_COST_RATE,
                                        SRATE.PRODUCT_BARCODE_SALE_RATE_RATE,
                                        BAR.PRODUCT_BARCODE_BARCODE,
                                        BRAN.BRANCH_SHORT_NAME ";
                                    if(!empty($data['subQuery'])){
                                        $query .= " HAVING " .$data['subQuery'];
                                    }
                                    $query .= " ORDER BY
                                    PRODUCT_NAME,REORDER_COUNT " . $data['movement'];
                            // die($query);
                            $Result_List = DB::select($query);
                            @endphp    

                            @if(count($Result_List) > 0)
                                @foreach($Result_List as $product)
                                    @php
                                        $currentStock = "SELECT open_qty FROM( SELECT inner_qry.product_id, PRODUCT.product_name, PRODUCT.product_code, group_item_name_code_string, group_item_name_string, group_item_level, Sum(open_qty) OPEN_QTY, Sum(open_amount) AS OPEN_AMOUNT, Sum(in_qty) IN_QTY, Sum(in_amount) IN_AMOUNT, Sum(out_qty) OUT_QTY, Sum(out_amount) OUT_AMOUNT FROM ( SELECT product_id, Sum(qty_base_unit_value) OPEN_QTY, Sum(qty_base_unit_value * cost_rate) OPEN_AMOUNT, 0 IN_QTY, 0 IN_AMOUNT, 0 OUT_QTY, 0 OUT_AMOUNT FROM vw_purc_stock_dtl WHERE branch_id IN (". $product->branch_id .") AND Trunc(document_date) <= to_date('". $data['to_date'] ."', 'yyyy/mm/dd') AND ( stock_calculation_effect = '+' OR stock_calculation_effect = '-') GROUP BY product_id ) inner_qry, vw_purc_product PRODUCT, vw_purc_group_item GRP_PRODUCT WHERE PRODUCT.product_id = inner_qry.product_id AND PRODUCT.group_item_id = GRP_PRODUCT.group_item_id AND inner_qry.PRODUCT_ID = ". $product->product_id ." GROUP BY inner_qry.product_id, PRODUCT.product_name, PRODUCT.product_code, group_item_name_code_string, group_item_name_string, group_item_level ) T1 ORDER BY open_qty, product_name";
                                        $currentStock = DB::select($currentStock)[0];
                                        $lastSale = "SELECT * FROM VW_SALE_SALES_INVOICE WHERE BRANCH_ID = ". $product->branch_id ." AND PRODUCT_ID = ". $product->product_id ." ORDER BY SALES_DATE desc FETCH FIRST 1 ROWS ONLY";
                                        $lastSale = DB::select($lastSale);
                                        if(count($lastSale) > 0){
                                            $lastSale = $lastSale[0];
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->branch_short_name }}</td>
                                        <td>{{ $product->product_name }}</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">{{ number_format($product->sale_rate,3) }}</td>
                                        <td class="text-right">{{ number_format($product->purchase_rate,3) }}</td>
                                        <td class="text-right">{{ number_format($currentStock->open_qty , 3) }}</td>
                                        <td class="text-right">{{ $product->reorder_count }}</td>
                                        <td>{{ isset($lastSale->sales_date) ? date('Y-m-d' , strtotime($lastSale->sales_date)) : '-' }}</td>
                                        <td>{{ $lastSale->uom_name ?? '-' }}</td>
                                        <td class="text-right">{{ $lastSale->sales_dtl_quantity ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="13">No Data Found...</td>
                                </tr>
                            @endif
                        </tbody>
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