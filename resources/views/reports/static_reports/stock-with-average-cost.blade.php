@extends('layouts.report')
@section('title', 'Stock & Average Cost Rate')

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
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date']))." "}}</span>
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
                        $filter_product = "";
                        if($data['product'] != "" && $data['product'] != ""){
                            $filter_product= " AND inner_qry.PRODUCT_ID  = ". $data['product']." ";
                        }
                        $query = "SELECT T1.product_id,
                                    product_name,
                                    product_code,
                                    group_item_name_code_string,
                                    group_item_name_string,
                                    group_item_level,
                                    open_qty,
                                    T2.op_avg_rate
                                FROM   (SELECT inner_qry.product_id,
                                            PRODUCT.product_name,
                                            PRODUCT.product_code,
                                            group_item_name_code_string,
                                            group_item_name_string,
                                            group_item_level,
                                            Sum(open_qty)    OPEN_QTY,
                                            Sum(open_amount) AS OPEN_AMOUNT,
                                            Sum(in_qty)      IN_QTY,
                                            Sum(in_amount)   IN_AMOUNT,
                                            Sum(out_qty)     OUT_QTY,
                                            Sum(out_amount)  OUT_AMOUNT
                                        FROM   (SELECT product_id,
                                                    Sum(qty_base_unit_value)             OPEN_QTY,
                                                    Sum(qty_base_unit_value * cost_rate) OPEN_AMOUNT,
                                                    0                                    IN_QTY,
                                                    0                                    IN_AMOUNT,
                                                    0                                    OUT_QTY,
                                                    0                                    OUT_AMOUNT
                                                FROM   vw_purc_stock_dtl
                                                WHERE  branch_id IN (".implode(",",$data['branch_ids']).")
                                                    AND Trunc(document_date) <=
                                                    TO_DATE('".$data['date']."', 'yyyy/mm/dd')
                                                    AND ( stock_calculation_effect = '+'
                                                            OR stock_calculation_effect = '-' )
                                                GROUP  BY product_id) inner_qry,
                                            vw_purc_product PRODUCT,
                                            vw_purc_group_item GRP_PRODUCT
                                        WHERE  PRODUCT.product_id = inner_qry.product_id
                                            AND PRODUCT.group_item_id = GRP_PRODUCT.group_item_id
                                            $filter_product
                                        GROUP  BY inner_qry.product_id,
                                                PRODUCT.product_name,
                                                PRODUCT.product_code,
                                                group_item_name_code_string,
                                                group_item_name_string,
                                                group_item_level) T1
                                    LEFT JOIN (SELECT product_id,
                                                        CASE
                                                        WHEN Sum(op_qty) <> 0 THEN (
                                                        Sum(op_amount) / Sum(op_qty) )
                                                        ELSE 0
                                                        END OP_AVG_RATE,
                                                        0 CL_AVG_RATE
                                                FROM   (SELECT product_id,
                                                                ( Sum(tbl_purc_grn_dtl_amount) - Sum(
                                                                tbl_purc_grn_dtl_disc_amount) )
                                                                        OP_AMOUNT,
                                                                Sum(qty_base_unit)
                                                                        OP_QTY,
                                                                0
                                                                        CL_AMOUNT,
                                                                0
                                                                        CL_QTY
                                                        FROM   tbl_purc_grn_dtl A
                                                                INNER JOIN tbl_purc_grn B
                                                                        ON A.grn_id = B.grn_id
                                                        WHERE  B.grn_date <= TO_DATE('".$data['date']."', 'yyyy/mm/dd')
                                                                AND B.branch_id IN (".implode(",",$data['branch_ids']).")
                                                                AND Upper(B.grn_type) = 'GRN'
                                                        GROUP  BY product_id
                                                        UNION ALL
                                                        SELECT c.product_id,
                                                                Sum(stock_dtl_amount)        OP_AMOUNT,
                                                                Sum(stock_dtl_qty_base_unit) OP_QTY,
                                                                0                            CL_AMOUNT,
                                                                0                            CL_QTY
                                                        FROM   tbl_inve_stock_dtl C
                                                                INNER JOIN tbl_inve_stock D
                                                                        ON C.stock_id = D.stock_id
                                                        WHERE  D.stock_date <= TO_DATE('".$data['date']."', 'yyyy/mm/dd')
                                                                AND D.branch_id IN (".implode(",",$data['branch_ids']).")
                                                                AND ( Upper(D.stock_code_type) = 'STR'
                                                                        OR Upper(D.stock_code_type) = 'OS' )
                                                        GROUP  BY c.product_id) XX
                                                GROUP  BY product_id) T2
                                            ON ( T1.product_id = T2.product_id )
                                ORDER  BY open_qty,product_name ";
                        // echo $query;
                        // die();
                        $get_data = DB::select($query);

                    @endphp
                    <table width="100%" id="product_and_group_activity_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                        <tr class="sec-sticky-header">
                            <th class="text-center">SR</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Product Code</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Cost Price</th>
                            <th class="text-center">Total Price</th>
                        </tr>
                        <tbody>
                            @php
                                $total_open_qty_price = 0;$total_open_unit_price = 0;$total_open_price = 0;$total_in_qty_price = 0;$total_in_unit_price = 0;$total_in_price = 0;$total_out_qty_price = 0;$total_out_unit_price = 0;$total_out_price = 0;$total_close_qty_price = 0;$total_close_unit_price = 0;$total_close_price = 0;
                                $total_nev_open_qty_price = 0;$total_nev_open_unit_price = 0;$total_nev_open_price = 0;$total_nev_in_qty_price = 0;$total_nev_in_unit_price = 0;$total_nev_in_price = 0;$total_nev_out_qty_price = 0;$total_nev_out_unit_price = 0;$total_nev_out_price = 0;$total_nev_close_qty_price = 0;$total_nev_close_unit_price = 0;$total_nev_close_price = 0;
                                $total_pos_open_qty_price = 0;$total_pos_open_unit_price = 0;$total_pos_open_price = 0;$total_pos_in_qty_price = 0;$total_pos_in_unit_price = 0;$total_pos_in_price = 0;$total_pos_out_qty_price = 0;$total_pos_out_unit_price = 0;$total_pos_out_price = 0;$total_pos_close_qty_price = 0;$total_pos_close_unit_price = 0;$total_pos_close_price = 0;
                            @endphp
                            @foreach ($get_data as $item)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td data-id="pd_product_name">{{ $item->product_name }}</td>
                                    <td>{{ $item->product_code }}</td>
                                    @php
                                        if(!\App\Helpers\Helper::NumberEmpty($item->open_qty)){
                                            $oq = number_format($item->open_qty,3,'.','');
                                        }else{
                                            $oq = number_format(0,4);
                                        }
                                        if(!\App\Helpers\Helper::NumberEmpty($item->op_avg_rate) && !\App\Helpers\Helper::NumberEmpty($item->open_qty)){
                                            $oa = number_format($item->op_avg_rate,3,'.','');
                                        }else{
                                            $oa = number_format(0,3);
                                        }
                                        $open_unit_price_val = $oa * $oq;

                                    @endphp
                                    <td class="text-right">{{ $oq }}</td>
                                    <td class="text-right">{{ $oa }}</td>
                                    <td class="text-right">{{ number_format($open_unit_price_val,3)  }} </td>
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
                                </tr>
                            @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td></td>
                            <td class="rep-font-bold">Total Price</td>
                            <td></td>
                            <td class="text-right rep-font-bold">{{number_format($total_open_qty_price,3)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($total_open_price,3)}}</td>
                        </tr>
                        <tr class="grand_total">
                            <td></td>
                            <td>Total Negative Price</td>
                            <td></td>
                            <td class="text-right">{{number_format($total_nev_open_qty_price,3)}}</td>
                            <td class="text-right"></td>
                            <td class="text-right">{{number_format($total_nev_open_price,3)}}</td>
                        </tr>
                        <tr class="grand_total">
                            <td></td>
                            <td>Total Positive Price</td>
                            <td></td>
                            <td class="text-right">{{number_format($total_pos_open_qty_price,3)}}</td>
                            <td class="text-right"></td>
                            <td class="text-right">{{number_format($total_pos_open_price,3)}}</td>
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



