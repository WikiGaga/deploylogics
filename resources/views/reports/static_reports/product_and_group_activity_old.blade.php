@extends('layouts.report')
@section('title', 'Product & Group Activity')

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
                        $query = "select   inner_qry.PRODUCT_ID , PRODUCT.PRODUCT_NAME , PRODUCT.PRODUCT_CODE  , GROUP_ITEM_NAME_CODE_STRING , GROUP_ITEM_NAME_STRING ,
    GROUP_ITEM_LEVEL ,     sum(OPEN_QTY) OPEN_QTY ,  sum(OPEN_AMOUNT) OPEN_AMOUNT  ,   sum(IN_QTY) IN_QTY ,
       sum(IN_AMOUNT) IN_AMOUNT  ,    sum(OUT_QTY) OUT_QTY  ,   sum(OUT_AMOUNT) OUT_AMOUNT
     FROM
    (

    select PRODUCT_ID ,  SUM(QTY_BASE_UNIT_VALUE) OPEN_QTY ,  SUM(QTY_BASE_UNIT_VALUE * COST_RATE ) OPEN_AMOUNT , 0 IN_QTY , 0 IN_AMOUNT  ,  0 OUT_QTY  , 0 OUT_AMOUNT    FROM VW_PURC_STOCK_DTL WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).")
    $document_type AND trunc(DOCUMENT_DATE)  < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND  (STOCK_CALCULATION_EFFECT = '+'  OR STOCK_CALCULATION_EFFECT = '-' )  GROUP BY  PRODUCT_ID

    UNION ALL

    select PRODUCT_ID , 0  OPEN_QTY , 0 OPEN_AMOUNT , SUM(QTY_BASE_UNIT_VALUE) IN_QTY   , SUM(COST_RATE *  QTY_BASE_UNIT_VALUE ) IN_AMOUNT   , 0 OUT_QTY  , 0 OUT_AMOUNT   FROM VW_PURC_STOCK_DTL WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).")
    $document_type AND trunc(DOCUMENT_DATE)  BETWEEN  TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
    AND STOCK_CALCULATION_EFFECT = '+' GROUP BY  PRODUCT_ID

    UNION ALL


    select PRODUCT_ID , 0  OPEN_QTY  , 0 OPEN_AMOUNT , 0 IN_QTY , 0 IN_AMOUNT , SUM(QTY_BASE_UNIT_VALUE * -1 )   OUT_QTY ,  SUM(COST_RATE *  (QTY_BASE_UNIT_VALUE * -1))  OUT_AMOUNT
    FROM VW_PURC_STOCK_DTL WHERE BRANCH_ID IN (".implode(",",$data['branch_ids']).")
    $document_type AND trunc(DOCUMENT_DATE)  BETWEEN  TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
    AND STOCK_CALCULATION_EFFECT = '-'
    GROUP BY  PRODUCT_ID

    ) inner_qry ,

      VW_PURC_PRODUCT  PRODUCT ,
        VW_PURC_GROUP_ITEM  GRP_PRODUCT
        WHERE  PRODUCT.PRODUCT_ID = inner_qry.PRODUCT_ID
        AND  PRODUCT.GROUP_ITEM_ID = GRP_PRODUCT.GROUP_ITEM_ID
        $filter_product
        $filter_group_item
        group by inner_qry.PRODUCT_ID ,
        PRODUCT.PRODUCT_NAME , PRODUCT.PRODUCT_CODE  , GROUP_ITEM_NAME_CODE_STRING , GROUP_ITEM_NAME_STRING ,
    GROUP_ITEM_LEVEL
    order by PRODUCT.PRODUCT_NAME";
                        $get_data = DB::select($query);

                    @endphp
                    <table width="100%" id="product_and_group_activity_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                        <tr class="sticky-header">
                            <th class="border_right" colspan="3"></th>
                            <th class="text-center border_right" colspan="3">Balance {{ $data['from_date'] }}</th>
                            <th class="text-center border_right" colspan="3">Inputs</th>
                            <th class="text-center border_right" colspan="3">Outputs</th>
                            <th class="text-center" colspan="3">Balance {{ $data['to_date'] }}</th>
                        </tr>
                        <tr class="sec-sticky-header">
                            <th class="text-center">SR</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center border_right">Product Code</th>

                            {{-- Opening Balance --}}
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center border_right">Total Price</th>

                            {{-- Inputs --}}
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center border_right">Total Price</th>

                            {{-- Outputs --}}
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center border_right">Total Price</th>

                            {{-- Closing Balance --}}
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Total Price</th>
                        </tr>
                        <tbody>
                            {{-- Add Record --}}
                            @php
                                $total_open_qty_price = 0;$total_open_unit_price = 0;$total_open_price = 0;$total_in_qty_price = 0;$total_in_unit_price = 0;$total_in_price = 0;$total_out_qty_price = 0;$total_out_unit_price = 0;$total_out_price = 0;$total_close_qty_price = 0;$total_close_unit_price = 0;$total_close_price = 0;
                                $total_nev_open_qty_price = 0;$total_nev_open_unit_price = 0;$total_nev_open_price = 0;$total_nev_in_qty_price = 0;$total_nev_in_unit_price = 0;$total_nev_in_price = 0;$total_nev_out_qty_price = 0;$total_nev_out_unit_price = 0;$total_nev_out_price = 0;$total_nev_close_qty_price = 0;$total_nev_close_unit_price = 0;$total_nev_close_price = 0;
                                $total_pos_open_qty_price = 0;$total_pos_open_unit_price = 0;$total_pos_open_price = 0;$total_pos_in_qty_price = 0;$total_pos_in_unit_price = 0;$total_pos_in_price = 0;$total_pos_out_qty_price = 0;$total_pos_out_unit_price = 0;$total_pos_out_price = 0;$total_pos_close_qty_price = 0;$total_pos_close_unit_price = 0;$total_pos_close_price = 0;
                            @endphp
                            @foreach ($get_data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td class="border_right">{{ $item->product_code }}</td>
                                    {{-- opening Balance --}}
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->open_qty)){
                                            $oq = number_format($item->open_qty,4,'.','');
                                        }else{
                                            $oq = number_format(0,4);
                                        }
                                        if(!\App\Helpers\Helper::NumberEmpty($item->open_amount) && !\App\Helpers\Helper::NumberEmpty($item->open_qty)){
                                            $oa = number_format($item->open_amount,4,'.','');
                                        }else{
                                            $oa = number_format(0,4);
                                        }
                                    @endphp
                                    <td class="text-right">{{ $oq }}</td>
                                    @php
                                        $total_open_qty_price += $oq;
                                        if($oq < 0){
                                            $total_nev_open_qty_price += $oq;
                                        }else{
                                            $total_pos_open_qty_price += $oq;
                                        }
                                        $open_unit_price_val =($oa != 0 && $oq != 0)? $oa / $oq :0;
                                    @endphp
                                    <td class="text-right">{{ number_format($open_unit_price_val,4)  }} </td>
                                    <td class="text-right border_right">{{ $oa }}</td>
                                    @php
                                        $total_open_unit_price += $open_unit_price_val;
                                        if($open_unit_price_val < 0){
                                            $total_nev_open_unit_price += $open_unit_price_val;
                                        }else{
                                            $total_pos_open_unit_price += $open_unit_price_val;
                                        }

                                        $total_open_price += $oa;
                                        if($oa < 0){
                                            $total_nev_open_price += $oa;
                                        }else{
                                            $total_pos_open_price += $oa;
                                        }
                                    @endphp
                                    {{-- Inputs --}}
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->in_qty)){
                                            $iq = number_format($item->in_qty,4,'.','');
                                        }else{
                                            $iq = number_format(0,4);
                                        }
                                        if(!\App\Helpers\Helper::NumberEmpty($item->in_amount) && !\App\Helpers\Helper::NumberEmpty($item->in_qty)){
                                            $ia = number_format($item->in_amount,4,'.','');
                                        }else{
                                            $ia = number_format(0,4);
                                        }
                                    @endphp
                                    <td class="text-right">{{ $iq }}</td>
                                    @php
                                        $total_in_qty_price += $iq;
                                        if($iq < 0){
                                            $total_nev_in_qty_price += $iq;
                                        }else{
                                            $total_pos_in_qty_price += $iq;
                                        }
                                        $in_unit_price_val = ($ia != 0 && $iq != 0)? $ia / $iq :0;
                                    @endphp
                                    <td class="text-right">{{ number_format($in_unit_price_val,4)  }}</td>
                                    <td class="text-right border_right">{{ $ia }}</td>
                                    @php
                                        $total_in_unit_price += $in_unit_price_val;
                                        if($in_unit_price_val < 0){
                                            $total_nev_in_unit_price += $in_unit_price_val;
                                        }else{
                                            $total_pos_in_unit_price += $in_unit_price_val;
                                        }

                                        $total_in_price += $ia;
                                        if($ia < 0){
                                            $total_nev_in_price += $ia;
                                        }else{
                                            $total_pos_in_price += $ia;
                                        }
                                    @endphp

                                    {{-- Outputs --}}
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->out_qty)){
                                            $ouq = number_format($item->out_qty,4,'.','');
                                        }else{
                                            $ouq = number_format(0,4);
                                        }
                                        if(!\App\Helpers\Helper::NumberEmpty($item->out_amount) && !\App\Helpers\Helper::NumberEmpty($item->out_qty)){
                                            $oua = number_format($item->out_amount,4,'.','');
                                        }else{
                                            $oua = number_format(0,4);
                                        }
                                    @endphp
                                    <td class="text-right">{{ $ouq }}</td>
                                    @php
                                        $total_out_qty_price += $ouq;
                                        if($ouq < 0){
                                            $total_nev_out_qty_price += $ouq;
                                        }else{
                                            $total_pos_out_qty_price += $ouq;
                                        }
                                        $out_unit_price_val = ($oua != 0 && $ouq != 0)? $oua / $ouq :0;
                                    @endphp
                                    <td class="text-right">{{ number_format($out_unit_price_val,4) }}</td>
                                    <td class="text-right border_right">{{ $oua }}</td>
                                    @php
                                        $total_out_unit_price += $out_unit_price_val;
                                        if($out_unit_price_val < 0){
                                            $total_nev_out_unit_price += $out_unit_price_val;
                                        }else{
                                            $total_pos_out_unit_price += $out_unit_price_val;
                                        }

                                        $total_out_price += $oua;
                                        if($oua < 0){
                                            $total_nev_out_price += $oua;
                                        }else{
                                            $total_pos_out_price += $oua;
                                        }
                                    @endphp

                                    {{-- Closing Balance --}}
                                    @php
                                        $quantity = ($oq + $iq - $ouq);
                                        $total = ($oa + $ia - $oua);
                                        $unit = ($total != 0 && $quantity != 0) ? $total / $quantity : '0';
                                    @endphp
                                    @php
                                        if($quantity == 0){
                                             $unit = 0;
                                             $total = 0;
                                        }
                                        $total_close_qty_price += $quantity;
                                        if($quantity < 0){
                                            $total_nev_close_qty_price +=$quantity;
                                        }else{
                                            $total_pos_close_qty_price += $quantity;
                                        }
                                        $total_close_unit_price += $unit;
                                        if($unit < 0){
                                            $total_nev_close_unit_price += $unit;
                                        }else{
                                            $total_pos_close_unit_price += $unit;
                                        }
                                         $total_close_price += $total;
                                        if($total < 0){
                                            $total_nev_close_price += $total;
                                        }else{
                                            $total_pos_close_price += $total;
                                        }
                                    @endphp
                                    <td class="text-right">{{ number_format($quantity,4) }}</td>
                                    <td class="text-right">{{ number_format($unit , 4) }}</td>
                                    <td class="text-right">{{ number_format($total,4) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td colspan="3" class="rep-font-bold">Total Price</td>
                            <td class="text-right rep-font-bold">{{number_format($total_open_qty_price,4)}}</td>
                            <td class="text-right rep-font-bold">{{--{{number_format($total_open_unit_price,3)}}--}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_open_price,4)}}</td>

                            <td class="text-right rep-font-bold">{{number_format($total_in_qty_price,4)}}</td>
                            <td class="text-right rep-font-bold">{{--{{number_format($total_in_unit_price,3)}}--}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_in_price,4)}}</td>

                            <td class="text-right rep-font-bold">{{number_format($total_out_qty_price,4)}}</td>
                            <td class="text-right rep-font-bold">{{--{{number_format($total_out_unit_price,3)}}--}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_out_price,4)}}</td>

                            <td class="text-right rep-font-bold">{{number_format($total_close_qty_price,4)}}</td>
                            <td class="text-right rep-font-bold">{{--{{number_format($total_close_unit_price,3)}}--}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_close_price,4)}}</td>

                        </tr>
                        <tr class="grand_total">
                            <td colspan="3">Total Negative Price</td>
                            <td class="text-right">{{number_format($total_nev_open_qty_price,4)}}</td>
                            <td class="text-right">{{--{{number_format($total_nev_open_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_nev_open_price,4)}}</td>

                            <td class="text-right">{{number_format($total_nev_in_qty_price,4)}}</td>
                            <td class="text-right">{{--{{number_format($total_nev_in_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_nev_in_price,4)}}</td>

                            <td class="text-right">{{number_format($total_nev_out_qty_price,4)}}</td>
                            <td class="text-right">{{--{{number_format($total_nev_out_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_nev_out_price,4)}}</td>

                            <td class="text-right">{{number_format($total_nev_close_qty_price,4)}}</td>
                            <td class="text-right">{{--{{number_format($total_nev_close_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_nev_close_price,4)}}</td>

                        </tr>
                        <tr class="grand_total">
                            <td colspan="3">Total Positive Price</td>

                            <td class="text-right">{{number_format($total_pos_open_qty_price,4)}}</td>
                            <td class="text-right">{{--{{number_format($total_pos_open_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_pos_open_price,4)}}</td>

                            <td class="text-right">{{number_format($total_pos_in_qty_price,4)}}</td>
                            <td class="text-right">{{--{{number_format($total_pos_in_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_pos_in_price,4)}}</td>

                            <td class="text-right">{{number_format($total_pos_out_qty_price,4)}}</td>
                            <td class="text-right">{{--{{number_format($total_pos_out_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_pos_out_price,4)}}</td>

                            <td class="text-right">{{number_format($total_pos_close_qty_price,3)}}</td>
                            <td class="text-right">{{--{{number_format($total_pos_close_unit_price,3)}}--}}</td>
                            <td class="text-right">{{number_format($total_pos_close_price,4)}}</td>

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



