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
                        $query = "select
                                      T1.PRODUCT_ID,
                                      PRODUCT_NAME,
                                      PRODUCT_CODE,
                                      GROUP_ITEM_NAME_CODE_STRING,
                                      GROUP_ITEM_NAME_STRING,
                                      GROUP_ITEM_LEVEL,
                                      OPEN_QTY,
                                      T2.OP_AVG_RATE,
                                      IN_QTY,
                                      OUT_QTY,
                                      OPEN_QTY + IN_QTY - OUT_QTY BAL_QTY,
                                      T2.CL_AVG_RATE
                                    FROM (
                                        select
                                          inner_qry.PRODUCT_ID,
                                          PRODUCT.PRODUCT_NAME,
                                          PRODUCT.PRODUCT_CODE,
                                          GROUP_ITEM_NAME_CODE_STRING,
                                          GROUP_ITEM_NAME_STRING,
                                          GROUP_ITEM_LEVEL,
                                          sum(OPEN_QTY) OPEN_QTY,
                                          sum(OPEN_AMOUNT) AS OPEN_AMOUNT,
                                          sum(IN_QTY) IN_QTY,
                                          sum(IN_AMOUNT) IN_AMOUNT,
                                          sum(OUT_QTY) OUT_QTY,
                                          sum(OUT_AMOUNT) OUT_AMOUNT
                                        FROM (
                                            select
                                              PRODUCT_ID,
                                              SUM(QTY_BASE_UNIT_VALUE) OPEN_QTY,
                                              SUM(QTY_BASE_UNIT_VALUE * COST_RATE) OPEN_AMOUNT,
                                              0 IN_QTY,
                                              0 IN_AMOUNT,
                                              0 OUT_QTY,
                                              0 OUT_AMOUNT
                                            FROM VW_PURC_STOCK_DTL
                                            WHERE
                                              BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                              AND trunc(DOCUMENT_DATE) < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
                                              AND ( STOCK_CALCULATION_EFFECT = '+'  OR STOCK_CALCULATION_EFFECT = '-')
                                            GROUP BY PRODUCT_ID
                                            UNION ALL
                                            select
                                              PRODUCT_ID,
                                              0 OPEN_QTY,
                                              0 OPEN_AMOUNT,
                                              SUM(QTY_BASE_UNIT_VALUE) IN_QTY,
                                              SUM(COST_RATE * QTY_BASE_UNIT_VALUE) IN_AMOUNT,
                                              0 OUT_QTY,
                                              0 OUT_AMOUNT
                                            FROM VW_PURC_STOCK_DTL
                                            WHERE
                                              BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                              AND trunc(DOCUMENT_DATE) BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
                                              AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd') AND STOCK_CALCULATION_EFFECT = '+'
                                            GROUP BY PRODUCT_ID
                                            UNION ALL
                                            select
                                              PRODUCT_ID,
                                              0 OPEN_QTY,
                                              0 OPEN_AMOUNT,
                                              0 IN_QTY,
                                              0 IN_AMOUNT,
                                              SUM(QTY_BASE_UNIT_VALUE * -1) OUT_QTY,
                                              SUM( COST_RATE * (QTY_BASE_UNIT_VALUE * -1) ) OUT_AMOUNT
                                            FROM VW_PURC_STOCK_DTL
                                            WHERE
                                              BRANCH_ID IN (".implode(",",$data['branch_ids']).")
                                              AND trunc(DOCUMENT_DATE) BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
                                              AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
                                              AND STOCK_CALCULATION_EFFECT = '-'
                                            GROUP BY PRODUCT_ID
                                          ) inner_qry,
                                          VW_PURC_PRODUCT PRODUCT, VW_PURC_GROUP_ITEM GRP_PRODUCT
                                        WHERE
                                          PRODUCT.PRODUCT_ID = inner_qry.PRODUCT_ID
                                          AND PRODUCT.GROUP_ITEM_ID = GRP_PRODUCT.GROUP_ITEM_ID
                                          $filter_product
                                        group by
                                          inner_qry.PRODUCT_ID,
                                          PRODUCT.PRODUCT_NAME,
                                          PRODUCT.PRODUCT_CODE,
                                          GROUP_ITEM_NAME_CODE_STRING,
                                          GROUP_ITEM_NAME_STRING,
                                          GROUP_ITEM_LEVEL
                                      ) T1
                                      LEFT JOIN (
                                        select product_id,
                                        CASE WHEN sum(op_qty) <> 0 THEN ( sum(op_amount) / sum(op_qty) ) ELSE 0 END OP_AVG_RATE,
                                        CASE WHEN sum(cl_qty) <> 0 THEN ( sum(cl_amount) / sum(cl_qty) ) ELSE 0 END CL_AVG_RATE
                                        FROM (
                                            select product_id,
                                              ( sum(TBL_PURC_GRN_DTL_AMOUNT) - sum(TBL_PURC_GRN_DTL_DISC_AMOUNT) ) OP_AMOUNT,
                                              sum(qty_base_unit) OP_QTY,
                                              0 CL_AMOUNT,
                                              0 CL_QTY
                                            from
                                              TBL_PURC_GRN_DTL A
                                              inner join TBL_PURC_GRN B ON A.GRN_ID = B.GRN_ID
                                            WHERE
                                              B.grn_date < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
                                              AND B.branch_id in (".implode(",",$data['branch_ids']).")
                                              AND UPPER(B.GRN_TYPE) = 'GRN'
                                            group by product_id
                                            UNION ALL
                                            select
                                              c.product_id,
                                              sum(STOCK_DTL_AMOUNT) OP_AMOUNT,
                                              sum(STOCK_DTL_QTY_BASE_UNIT) OP_QTY,
                                              0 CL_AMOUNT,
                                              0 CL_QTY
                                            from
                                              TBL_INVE_STOCK_DTL C
                                              inner join TBL_INVE_STOCK D ON C.STOCK_ID = D.STOCK_ID
                                            WHERE
                                              D.STOCK_DATE < TO_DATE('".$data['from_date']."', 'yyyy/mm/dd')
                                              AND D.branch_id in (".implode(",",$data['branch_ids']).")
                                              AND ( UPPER(D.STOCK_CODE_TYPE) = 'STR' OR UPPER(D.STOCK_CODE_TYPE) = 'OS' )
                                            group by c.product_id
                                            UNION ALL
                                            select
                                              product_id,
                                              0 OP_AMOUNT,
                                              0 OP_QTY,
                                              (sum(TBL_PURC_GRN_DTL_AMOUNT) - sum(TBL_PURC_GRN_DTL_DISC_AMOUNT) ) CL_AMOUNT,
                                              sum(qty_base_unit) CL_QTY
                                            from
                                              TBL_PURC_GRN_DTL A
                                              inner join TBL_PURC_GRN B ON A.GRN_ID = B.GRN_ID
                                            WHERE
                                              B.grn_date <= TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
                                              AND B.branch_id in (".implode(",",$data['branch_ids']).")
                                              AND UPPER(B.GRN_TYPE) = 'GRN'
                                            group by product_id
                                            UNION ALL
                                            select
                                              c.product_id,
                                              0 OP_AMOUNT,
                                              0 OP_QTY,
                                              sum(STOCK_DTL_AMOUNT) CL_AMOUNT,
                                              sum(STOCK_DTL_QTY_BASE_UNIT) CL_QTY
                                            from
                                              TBL_INVE_STOCK_DTL C
                                              inner join TBL_INVE_STOCK D ON C.STOCK_ID = D.STOCK_ID
                                            WHERE
                                              D.STOCK_DATE <= TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')
                                              AND D.branch_id in (".implode(",",$data['branch_ids']).")
                                              AND (
                                                UPPER(D.STOCK_CODE_TYPE) = 'STR'
                                                OR UPPER(D.STOCK_CODE_TYPE) = 'OS'
                                              )
                                            group by c.product_id
                                          ) XX
                                        Group by product_id
                                      ) T2 ON (T1.PRODUCT_ID = T2.PRODUCT_ID) order by BAL_QTY,PRODUCT_NAME";
                        // echo $query;
                        // die();
                        $get_data = DB::select($query);

                    @endphp
                    <table width="100%" id="product_and_group_activity_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                        <tr class="sticky-header">
                            <th class="border_right" colspan="3"></th>
                            <th class="text-center border_right" colspan="3">Balance {{ $data['from_date'] }}</th>
                            <th class="text-center border_right">Inputs</th>
                            <th class="text-center border_right">Outputs</th>
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
                            <th class="text-center border_right">Quantity</th>

                            {{-- Outputs --}}
                            <th class="text-center border_right">Quantity</th>

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
                                    <td>
                                        <input type="hidden" value="{{ $item->product_id }}" class="product_id">
                                        <input type="hidden" value="{{ $item->product_barcode ?? '' }}" class="pd_barcode">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td data-id="pd_product_name">{{ $item->product_name }}</td>
                                    <td class="border_right">{{ $item->product_code }}</td>
                                    {{-- opening Balance --}}
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->open_qty)){
                                            $oq = number_format($item->open_qty,4,'.','');
                                        }else{
                                            $oq = number_format(0,4);
                                        }
                                        if(!\App\Helpers\Helper::NumberEmpty($item->op_avg_rate) && !\App\Helpers\Helper::NumberEmpty($item->open_qty)){
                                            $oa = number_format($item->op_avg_rate,4,'.','');
                                        }else{
                                            $oa = number_format(0,4);
                                        }
                                        $open_unit_price_val = $oa * $oq;

                                    @endphp
                                    <td class="text-right">{{ $oq }}</td>
                                    <td class="text-right">{{ $oa }}</td>
                                    <td class="text-right border_right">{{ number_format($open_unit_price_val,4)  }} </td>
                                    @php
                                        $total_open_qty_price += $oq;
                                            if($oq < 0){
                                                $total_nev_open_qty_price += $oq;
                                            }else{
                                                $total_pos_open_qty_price += $oq;
                                            }
                                            $total_open_unit_price += $open_unit_price_val;
                                            if($open_unit_price_val < 0){
                                                $total_nev_open_unit_price += $open_unit_price_val;
                                            }else{
                                                $total_pos_open_unit_price += $open_unit_price_val;
                                            }

                                            $total_open_price += $open_unit_price_val;
                                            if($open_unit_price_val < 0){
                                                $total_nev_open_price += $open_unit_price_val;
                                            }else{
                                                $total_pos_open_price += $open_unit_price_val;
                                            }
                                    @endphp
                                    {{-- Inputs --}}
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->in_qty)){
                                            $iq = number_format($item->in_qty,4,'.','');
                                        }else{
                                            $iq = number_format(0,4);
                                        }
                                    @endphp
                                    <td class="text-right border_right">{{ $iq }}</td>
                                    @php
                                        $total_in_qty_price += $iq;
                                        if($iq < 0){
                                            $total_nev_in_qty_price += $iq;
                                        }else{
                                            $total_pos_in_qty_price += $iq;
                                        }
                                    @endphp
                                    {{-- Outputs --}}
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->out_qty)){
                                            $ouq = number_format($item->out_qty,4,'.','');
                                        }else{
                                            $ouq = number_format(0,4);
                                        }
                                    @endphp
                                    <td class="text-right border_right">{{ $ouq }}</td>
                                    @php
                                        $total_out_qty_price += $ouq;
                                        if($ouq < 0){
                                            $total_nev_out_qty_price += $ouq;
                                        }else{
                                            $total_pos_out_qty_price += $ouq;
                                        }
                                    @endphp
                                    {{-- Closing Balance --}}
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->bal_qty)){
                                            $bq = number_format($item->bal_qty,4,'.','');
                                        }else{
                                            $bq = number_format(0,4);
                                        }
                                        if(!\App\Helpers\Helper::NumberEmpty($item->cl_avg_rate) && !\App\Helpers\Helper::NumberEmpty($item->bal_qty)){
                                            $bar = number_format($item->cl_avg_rate,4,'.','');
                                        }else{
                                            $bar = number_format(0,4);
                                        }
                                        $total = $bq * $bar;
                                    @endphp

                                    <td class="text-right">{{ number_format($bq,4) }}</td>
                                    <td class="text-right">{{ number_format($bar , 4) }}</td>
                                    <td class="text-right">{{ number_format($total,4) }}</td>
                                    @php
                                        $total_close_qty_price += $bq;
                                        if($bq < 0){
                                            $total_nev_close_qty_price += $bq;
                                        }else{
                                            $total_pos_close_qty_price += $bq;
                                        }

                                        $total_close_price += $total;
                                        if($total < 0){
                                            $total_nev_close_price += $total;
                                        }else{
                                            $total_pos_close_price += $total;
                                        }

                                    @endphp
                                </tr>
                            @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td colspan="3" class="rep-font-bold">Total Price</td>
                            <td class="text-right rep-font-bold">{{number_format($total_open_qty_price,4)}}</td>
                            <td class="text-right rep-font-bold">{{--{{number_format($total_open_unit_price,3)}}--}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_open_price,4)}}</td>

                            <td class="text-right rep-font-bold">{{number_format($total_in_qty_price,4)}}</td>

                            <td class="text-right rep-font-bold">{{number_format($total_out_qty_price,4)}}</td>

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

                            <td class="text-right">{{number_format($total_nev_out_qty_price,4)}}</td>

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

                            <td class="text-right">{{number_format($total_pos_out_qty_price,4)}}</td>

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
                $("#product_and_group_activity_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



