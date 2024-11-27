@extends('layouts.report')
@section('title', 'Supplier Wise Sale')

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
                        <tr class="sticky-header">
                            <th>Barcode</th>
                            <th>Product Name</th>
                            <th>Half Qty</th>
                            <th>Sale Rate</th>
                            <th>Purchase Rate</th>
                        </tr>
                        @php $sum_sale_rate = 0;$sum_purc_rate = 0;$sum_half_qty = 0; @endphp
                        @foreach($data['supplier_ids'] as $supplier)
                        @php $supp = \Illuminate\Support\Facades\DB::table('tbl_purc_supplier')->where('supplier_id',$supplier)->first(); @endphp
                            <tr>
                                <th colspan="5" class="rep-font-bold">{{ $supp->supplier_name }}</th>
                            </tr>
                            @php
                                $query = "select X.*, Y.SALE_RATE, Z.pur_rate from 
                                    (select A.product_id, A.PRODUCT_NAME, A.half_qty , B.PRODUCT_BARCODE_ID, B.PRODUCT_BARCODE_BARCODE from 
                                    (select product_id, product_name, sum(qty_base_unit) base_qty, ((sum(qty_base_unit) / 7) / 2) half_qty from VW_SALE_SALES_INVOICE 
                                    where branch_id = (".implode(",",$data['branch_ids']).") AND TO_CHAR(SALES_DATE, 'yyyy/mm/dd') >= '". $data['from_date'] ."' AND TO_CHAR(SALES_DATE, 'yyyy/mm/dd') <= '".$data['to_date']."' AND product_id in (select product_id from vw_purc_grn where supplier_id = ". $supplier ." AND branch_id = (".implode(",",$data['branch_ids']).")) 
                                    group by product_id, product_name) A JOIN (select PRODUCT_BARCODE_BARCODE, PRODUCT_BARCODE_ID, product_id From  TBL_PURC_PRODUCT_BARCODE where product_barcode_packing = 1 ) B ON A.product_id = B.product_id) X 
                                    JOIN (select nvl(PRODUCT_BARCODE_SALE_RATE_RATE,0) SALE_RATE , PRODUCT_BARCODE_ID FROM TBL_PURC_PRODUCT_BARCODE_SALE_RATE where branch_id = (".implode(",",$data['branch_ids']).") AND product_category_id = 2) Y ON X.PRODUCT_BARCODE_ID =  Y.PRODUCT_BARCODE_ID 
                                    LEFT JOIN (select nvl(PRODUCT_BARCODE_PURCHASE_RATE,0) pur_rate , PRODUCT_BARCODE_ID FROM TBL_PURC_PRODUCT_BARCODE_PURCH_RATE  where branch_id = (".implode(",",$data['branch_ids']).") ) Z ON X.PRODUCT_BARCODE_ID =  Z.PRODUCT_BARCODE_ID ";
                                $ResultList = DB::select($query);                              
                            @endphp
                            @foreach($ResultList as $list)
                                @php 
                                    $sum_sale_rate = $list->sale_rate + $sum_sale_rate;
                                    $sum_purc_rate = $list->pur_rate + $sum_purc_rate;
                                    $sum_half_qty += $list->half_qty;
                                @endphp
                                <tr>
                                    <td>{{ $list->product_barcode_barcode }}</td>
                                    <td>{{ $list->product_name }}</td>
                                    <td>{{ number_format($list->half_qty , 3) }}</td>
                                    <td>{{ number_format($list->sale_rate , 3) }}</td>
                                    <td>{{ number_format($list->pur_rate, 3) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr>
                            <td class="rep-font-bold">Total : </td>
                            <td></td>
                            <td>{{ number_format($sum_half_qty , 3) }}</td>
                            <td></td>
                            <td></td>
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
                $("#rep_sale_invoice_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



