@extends('layouts.report')
@section('title', 'Inventory Checklist')

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
/*
        $data['stock_quantity_filter_types'];
        $data['stock_quantity_filter_types_val'];
        $data['stock_value_filter_types'];
        $data['stock_value_filter_types_val'];
        $data['uom_list'];
*/
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
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->select('group_item_name_string','group_item_id','group_item_name_code_string')->get(); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Group:</span>
                        @foreach($product_groups as $product_group)
                            <span style="color: #5578eb;">{{$product_group->group_item_name_string}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(count($data['store']) != 0 && $data['store'] != "" && $data['store'] != null)
                    @php $stores = \Illuminate\Support\Facades\DB::table('tbl_defi_store')->whereIn('store_id',$data['store'])->get('store_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Stores:</span>
                        @foreach($stores as $store)
                            <span style="color: #5578eb;">{{$store->store_name}}</span><span style="color: #fd397a;">, </span>
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
                        $document_type = "";
                        if(isset($data['all_document_type']) && count($data['all_document_type']) > 0){
                            $document_type= " AND DOCUMENT_TYPE IN ('".implode("','",$data['all_document_type'])."') ";
                        }
                        $filter_product = "";
                        if($data['product'] != "" && $data['product'] != ""){
                            $filter_product= " AND inner_qry.PRODUCT_ID  = ". $data['product']." ";
                        }
                        $filter_group_item = "";
                        if(isset($product_groups) && count($product_groups) != 0){
                            // AND (group_item_name_code_string like '1-10-85%' OR group_item_name_code_string like '1-21-98%')
                            $filter_group_item = " AND ( ";
                            $arr_count = count($product_groups) - 1;
                            foreach ($product_groups as $k=>$product_group){
                                $filter_group_item .= "group_item_name_code_string like '".$product_group->group_item_name_code_string."%'";
                                if($arr_count != $k){
                                    $filter_group_item .= " OR ";
                                }
                            }
                            $filter_group_item .= " ) ";
                            // $filter_group_item = " AND PRODUCT.group_item_id IN (".implode(",",$data['product_group']).") ";
                        }
                        $store_ids = [];$filter_store_ids='';
                        if(count($data['store']) != 0){
                            $store_ids = $data['store'];
                            $filter_store_ids = " AND inner_qry.SALES_STORE_ID IN (".implode(",",$store_ids).")";
                        }
                        $open_qty = "";
                        if($data['stock_quantity_filter_types'] != "" && $data['stock_quantity_filter_types_val'] != ""){
                            $open_qty = "AND OPEN_QTY ".$data['stock_quantity_filter_types']." ".(float)$data['stock_quantity_filter_types_val'];
                        }
                        $query = "select   inner_qry.PRODUCT_ID ,inner_qry.SALES_STORE_ID, PRODUCT.PRODUCT_NAME  , GROUP_ITEM_NAME_CODE_STRING , GROUP_ITEM_NAME_STRING ,
    GROUP_ITEM_LEVEL , inner_qry.SALES_STORE_NAME,  sum(OPEN_QTY) OPEN_QTY ,  sum(OPEN_AMOUNT) OPEN_AMOUNT  ,   sum(IN_QTY) IN_QTY ,
       sum(IN_AMOUNT) IN_AMOUNT  ,    sum(OUT_QTY) OUT_QTY  ,   sum(OUT_AMOUNT) OUT_AMOUNT
     FROM
    (

    select PRODUCT_ID , SALES_STORE_ID, SALES_STORE_NAME,  SUM(QTY_BASE_UNIT_VALUE) OPEN_QTY ,  SUM(QTY_BASE_UNIT_VALUE * COST_RATE ) OPEN_AMOUNT , 0 IN_QTY , 0 IN_AMOUNT  ,  0 OUT_QTY  , 0 OUT_AMOUNT    FROM VW_PURC_STOCK_DTL WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).")
    $document_type AND trunc(DOCUMENT_DATE)  < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND  (STOCK_CALCULATION_EFFECT = '+'  OR STOCK_CALCULATION_EFFECT = '-' )  GROUP BY  PRODUCT_ID, SALES_STORE_NAME, SALES_STORE_ID

    UNION ALL

    select PRODUCT_ID ,SALES_STORE_ID, SALES_STORE_NAME, 0  OPEN_QTY , 0 OPEN_AMOUNT , SUM(QTY_BASE_UNIT_VALUE) IN_QTY   , SUM(COST_RATE *  QTY_BASE_UNIT_VALUE ) IN_AMOUNT   , 0 OUT_QTY  , 0 OUT_AMOUNT   FROM VW_PURC_STOCK_DTL WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).")
    $document_type AND trunc(DOCUMENT_DATE)  BETWEEN  TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
    AND STOCK_CALCULATION_EFFECT = '+' GROUP BY  PRODUCT_ID,SALES_STORE_NAME, SALES_STORE_ID

    UNION ALL

    select PRODUCT_ID ,SALES_STORE_ID, SALES_STORE_NAME, 0  OPEN_QTY  , 0 OPEN_AMOUNT , 0 IN_QTY , 0 IN_AMOUNT , SUM(QTY_BASE_UNIT_VALUE * -1 )   OUT_QTY ,  SUM(COST_RATE *  (QTY_BASE_UNIT_VALUE * -1))  OUT_AMOUNT
    FROM VW_PURC_STOCK_DTL WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).")
    $document_type AND trunc(DOCUMENT_DATE)  BETWEEN  TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
    AND STOCK_CALCULATION_EFFECT = '-'
    GROUP BY  PRODUCT_ID , SALES_STORE_NAME, SALES_STORE_ID

    ) inner_qry ,

        VW_PURC_PRODUCT  PRODUCT ,
        VW_PURC_GROUP_ITEM  GRP_PRODUCT
        WHERE  PRODUCT.PRODUCT_ID = inner_qry.PRODUCT_ID
        AND  PRODUCT.GROUP_ITEM_ID = GRP_PRODUCT.GROUP_ITEM_ID
        $filter_product
        $filter_group_item
        $filter_store_ids
        $open_qty
        group by inner_qry.PRODUCT_ID ,inner_qry.SALES_STORE_NAME, SALES_STORE_ID,
        PRODUCT.PRODUCT_NAME  , GROUP_ITEM_NAME_CODE_STRING , GROUP_ITEM_NAME_STRING ,
    GROUP_ITEM_LEVEL";

                        $get_data = DB::select($query);
                    @endphp

                    @php
                        $total_products_price = 0;$total_products_qty = 0;
                        $total_nev_products_price = 0;$total_nev_products_qty = 0;
                        $total_grand_products_price = 0;$total_grand_products_qty = 0;
                    @endphp

                    <table width="100%" id="product_and_group_activity_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                        <tr class="sticky-header">
                            <th class="text-center">SR #</th>
                            <th class="text-center">Product Code</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Product Name Arabic</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Total Price</th>
                            <th class="text-center">Store</th>
                        </tr>
                        <tbody>
                            @foreach ($get_data as $item)
                                @php
                                    $stock_value = true;
                                    $tp = (float)$item->open_qty * (float)$item->open_amount;
                                    if($data['stock_value_filter_types'] != "" && $data['stock_value_filter_types_val'] != ""){
                                        if($data['stock_value_filter_types'] == "="){
                                            if(number_format($tp,3,'.','') == number_format((float)$data['stock_value_filter_types_val'],3)){
                                                $stock_value = true;
                                            }else{
                                                $stock_value = false;
                                            }
                                        }elseif ($data['stock_value_filter_types'] == "!="){
                                            if(number_format($tp,3,'.','') != number_format((float)$data['stock_value_filter_types_val'],3)){
                                                $stock_value = true;
                                            }else{
                                                $stock_value = false;
                                            }
                                        }elseif ($data['stock_value_filter_types'] == ">"){
                                            if(number_format($tp,3,'.','') > number_format((float)$data['stock_value_filter_types_val'],3)){
                                                $stock_value = true;
                                            }else{
                                                $stock_value = false;
                                            }
                                        }elseif ($data['stock_value_filter_types'] == "<"){
                                            if(number_format($tp,3,'.','') < number_format((float)$data['stock_value_filter_types_val'],3)){
                                                $stock_value = true;
                                            }else{
                                                $stock_value = false;
                                            }
                                        }elseif ($data['stock_value_filter_types'] == ">="){
                                            if(number_format($tp,3,'.','') >= number_format((float)$data['stock_value_filter_types_val'],3)){
                                                $stock_value = true;
                                            }else{
                                                $stock_value = false;
                                            }
                                        }elseif ($data['stock_value_filter_types'] == "<="){
                                            if(number_format($tp,3,'.','') <= number_format((float)$data['stock_value_filter_types_val'],3)){
                                                $stock_value = true;
                                            }else{
                                                $stock_value = false;
                                            }
                                        }else{
                                            $stock_value = false;
                                        }
                                    }
                                @endphp
                                @if($stock_value == true)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product_id }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td class="text-right">{{ number_format($item->open_qty,3) }}</td>
                                        <td class="text-right"></td>
                                        <td class="text-right">{{ number_format($item->open_amount,3) }}</td>

                                        @php $tp = (float)$item->open_qty * (float)$item->open_amount;  @endphp
                                        <td class="text-right">{{ number_format($tp,3) }}</td>

                                        <td class="text-right">{{$item->sales_store_name }}</td>

                                        @php
                                            $total_products_price += $item->open_amount;
                                            $total_products_qty += $item->open_qty;
                                            if($item->open_qty < 0){
                                                $total_nev_products_qty += $item->open_qty;
                                            }else{
                                                $total_grand_products_qty += $item->open_qty;
                                            }
                                            if($item->open_amount < 0){
                                                $total_nev_products_price += $item->open_amount;
                                            }else{
                                                $total_grand_products_price += $item->open_amount;
                                            }
                                        @endphp
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="4">Prices</td>
                            <td colspan="3">Qtys.</td>
                        </tr>
                        <tr class="grand_total">
                            <td colspan="3" class="rep-font-bold">Total Products</td>
                            <td class="text-right rep-font-bold" colspan="4">{{number_format($total_products_price,3)}}</td>
                            <td class="text-right rep-font-bold" colspan="3">{{number_format($total_products_qty,3)}}</td>
                        </tr>
                        <tr class="grand_total">
                            <td colspan="3">Total Negative Products</td>
                            <td class="text-right" colspan="4">{{number_format($total_nev_products_price,3)}}</td>
                            <td class="text-right" colspan="3">{{number_format($total_nev_products_qty,3)}}</td>
                        </tr>
                        <tr class="grand_total">
                            <td colspan="3">Grand Total</td>
                            <td class="text-right" colspan="4">{{number_format($total_grand_products_price,3)}}</td>
                            <td class="text-right" colspan="3">{{number_format($total_grand_products_qty,3)}}</td>
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
                $("#product_and_group_activity_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



