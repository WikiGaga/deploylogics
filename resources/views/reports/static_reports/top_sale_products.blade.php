@extends('layouts.report')
@section('title', 'Reporting')

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
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            @include('reports.template.criteria')
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    @php
                        $query = "select * from (
                                select product_barcode_id,product_barcode_barcode,product_name,sum(sales_dtl_quantity) sales_dtl_quantity
                                from vw_sale_sales_invoice where sales_date between to_date('".$data['from_date']."', 'yyyy/mm/dd') and to_date('".$data['to_date']."', 'yyyy/mm/dd')
                                group by product_barcode_id,product_barcode_barcode,product_name
                            ) abc where rownum <= 500 order by sales_dtl_quantity desc ";
                        $list = DB::select($query);
                    @endphp
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center" width="1%">Sr</th>
                            <th class="text-left" width="30%">Product Name</th>
                            <th class="text-left" width="30%">Barcode</th>
                            <th class="text-left" width="30%">Barcode ISBN</th>
                            <th class="text-center" width="9%">Qty</th>
                        </tr>
                        @php
                            $total_qty = 0;
                        @endphp
                        @foreach($list as $product)
                            @php
                                    $code = '';
                                    if(in_array(strlen($product->product_barcode_barcode),[9,10,12,13])){
                                        $barcode = new \TheUmar98\BarcodeBundle\Utils\BarcodeGenerator();
                                        $barcode->setText($product->product_barcode_barcode);
                                        $barcode->setType('CINisbn');
                                        $barcode->setScale(2);
                                        $barcode->setLabel('');
                                        $barcode->setThickness(20);
                                        $barcode->setFontSize(14);
                                        $code = $barcode->generate();
                                    }
                            @endphp
                            <tr>
                                <td class="text-center">{{$loop->iteration}}</td>
                                <td class="text-left">{{$product->product_name}}</td>
                                <td class="text-left">{{$product->product_barcode_barcode}}</td>
                                <td class="text-left">
                                    @if($code != '')
                                        <img src="data:image/png;base64,{{$code}}" />
                                    @endif
                                </td>
                                <td class="text-center">{{$product->sales_dtl_quantity}}</td>
                            </tr>
                            @php $total_qty += $product->sales_dtl_quantity ; @endphp
                        @endforeach

                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Total:</td>
                            <td class="text-center rep-font-bold"></td>
                            <td class="text-center rep-font-bold"></td>
                            <td class="text-center rep-font-bold">{{$total_qty}}</td>
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



