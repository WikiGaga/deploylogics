@extends('layouts.report')
@section('title', 'Parent Group Wise Sale')

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
                    @php $product_groups = \Illuminate\Support\Facades\DB::table('vw_purc_group_item')->whereIn('group_item_id',$data['product_group'])->select('group_item_name_string','group_item_id')->get(); @endphp
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
                        $filter_group_item = "";
                        if(isset($product_groups) && count($product_groups) != 0)
                        {
                            $filter_group_item = " AND ( ";
                            $arr_count = count($product_groups) - 1;
                            foreach ($product_groups as $k=>$product_group){
                                $filter_group_item .= " SALE.GROUP_ITEM_PARENT_ID like '".$product_group->group_item_id."%'";
                                if($arr_count != $k){
                                    $filter_group_item .= " OR ";
                                }
                            }
                            $filter_group_item .= " ) ";
                        }


                        $query = "select      
                            PRODUCT.GROUP_ITEM_PARENT_NAME as name   ,              
                            SUM(SALE.SALES_DTL_QUANTITY)  QUANTITY,
                            SUM(SALE.SALES_DTL_AMOUNT) AMOUNT,
                            round(100*(SUM(SALE.SALES_DTL_AMOUNT) / sum(SUM(SALE.SALES_DTL_AMOUNT)) over ()),2) perc 
                        from 
                            VW_SALE_SALES_INVOICE SALE ,              
                            VW_PURC_PRODUCT_BARCODE PRODUCT               
                        WHERE PRODUCT.PRODUCT_BARCODE_ID = SALE.PRODUCT_BARCODE_ID 
                            and (SALE.SALES_DATE BETWEEN TO_DATE('".$data['from_date']."', 'yyyy/mm/dd') AND TO_DATE('".$data['to_date']."', 'yyyy/mm/dd')) 
                            and sale.branch_id IN (".implode(",",$data['branch_ids']).")
                            $filter_group_item
                        GROUP BY  PRODUCT.GROUP_ITEM_PARENT_ID,  PRODUCT.GROUP_ITEM_PARENT_NAME 
                        ORDER BY  SUM(SALES_DTL_AMOUNT) DESC";

                       // dd($query);
                        $get_data = DB::select($query);

                    @endphp
                    <table width="100%" id="product_parent_group_wise_sale_datatable" class="table bt-datatable table-bordered data_table_rows_total">
                       <tr class="sticky-header">
                            <th class="text-center">Sr#</th>
                            <th class="text-left">Group Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Perc(%)</th>
                        </tr>
                        <tbody>
                        @php
                            $total_qty = 0;
                            $total_amount = 0;
                        @endphp
                        @foreach ($get_data as $item)
                            @php
                            $total_qty +=$item->quantity;
                            $total_amount +=$item->amount;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }} </td>
                                <td class="text-left">{{ $item->name }}</td>
                                <td class="text-center">{{ @number_format($item->quantity,2) }}</td>
                                <td class="text-right">{{ @number_format($item->amount,2)  }} </td>
                                <td class="text-center">{{ @round($item->perc,2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td colspan="2" class="text-right rep-font-bold">Total</td>
                            <td class="text-center rep-font-bold">{{number_format($total_qty,4)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_amount,4)}}</td>
                            <td class="text-right rep-font-bold">&nbsp;</td>
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
                $("#product_parent_group_wise_sale_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



