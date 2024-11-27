@extends('layouts.report')
@section('title', 'Product Rate Report')

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
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                @if(count($data['product_group']) != 0 && $data['product_group'] != "" && $data['product_group'] != null)
                    @php
                        $data['group_item'] = \App\Models\ViewPurcGroupItem::where('group_item_id',$data['product_group'])->first();
                    @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product Group:</span>
                        {{$data['group_item']->group_item_name_string}}
                    </h6>
                @endif
                @php
                    $rate_type = false;
                    $rate_between = false;
                @endphp
                @if(isset($data['rate_type']) && $data['rate_type'] != ("" || 0))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Rate Type:</span>
                        @if($data['rate_type'] == 1)
                            Sale Rate
                        @elseif($data['rate_type'] == 2)
                            Cost Rate
                        @endif
                    </h6>
                    @php
                        $rate_type = true;
                    @endphp
                @endif
                @if(isset($data['rate_between']) && $data['rate_between'] != ("" || 0) && isset($data['rate_type']) && $data['rate_type'] != ("" || 0))
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Rate:</span>
                        @if($data['rate_between'] == 1)
                            Zero Rate
                        @elseif($data['rate_between'] == 2)
                            Zero & Less than Zero Rate
                        @endif
                    </h6>
                    @php
                        $rate_between = true;
                    @endphp
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-left">Sr.No</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">UOM Name</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Sale Rate</th>
                            <th class="text-center">Cost Rate</th>
                            <th class="text-center">Difference</th>
                            <th class="text-center">Difference %</th>
                        </tr>
                        @php
                            $rate = '';
                            if($rate_type && $rate_between){
                                if($data['rate_type'] == 1){
                                    if($data['rate_between'] == 1){
                                       $rate =  'AND c.product_barcode_sale_rate_rate = 0';
                                    }elseif($data['rate_between'] == 2){
                                        $rate =  'AND c.product_barcode_sale_rate_rate <= 0';
                                    }
                                }
                                if($data['rate_type'] == 2){
                                    if($data['rate_between'] == 1){
                                       $rate =  'AND b.product_barcode_purchase_rate = 0';
                                    }elseif($data['rate_between'] == 2){
                                        $rate =  'AND b.product_barcode_purchase_rate <= 0';
                                    }
                                }
                            }
                            $qry = "select * from TBL_PURC_PRODUCT a
                                  join tbl_purc_product_barcode b on a.product_id = b.product_id
                                  join tbl_purc_product_barcode_sale_rate c on c.product_barcode_id = b.product_barcode_id
                                  where c.product_category_id = 2
                                  AND (c.product_barcode_sale_rate_rate <= b.product_barcode_purchase_rate OR c.product_barcode_sale_rate_rate = 0)
                                  AND c.branch_id = ".implode(",",$data['branch_ids'])." ". $rate;
                            $data['barcodes'] = \Illuminate\Support\Facades\DB::select($qry);
                        @endphp
                        @foreach($data['barcodes'] as $barcode)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$barcode->product_barcode_barcode}}</td>
                                <td>{{$barcode->product_name}}</td>
                                <td>{{$barcode->uom_name}}</td>
                                <td>{{$barcode->product_barcode_packing}}</td>
                                <td class="text-right">{{number_format($barcode->product_barcode_sale_rate_rate,3)}}</td>
                                <td class="text-right">{{number_format($barcode->product_barcode_purchase_rate,3)}}</td>
                                @php
                                    if($barcode->product_barcode_purchase_rate == null || $barcode->product_barcode_purchase_rate == ""){
                                       $product_barcode_purchase_rate  = 0;
                                    }else{
                                       $product_barcode_purchase_rate  = $barcode->product_barcode_purchase_rate;
                                    }
                                    if($barcode->product_barcode_sale_rate_rate == null || $barcode->product_barcode_sale_rate_rate == ""){
                                       $product_barcode_sale_rate_rate  = 0;
                                    }else{
                                       $product_barcode_sale_rate_rate  = $barcode->product_barcode_sale_rate_rate;
                                    }
                                    $diff = (float)$product_barcode_sale_rate_rate - (float)$product_barcode_purchase_rate;

                                    if($product_barcode_purchase_rate == 0){
                                        $diffPerc = 0;
                                    }else{
                                        $diffPerc = ((float)$diff/(float)$product_barcode_purchase_rate)*100;
                                    }
                                @endphp
                                <td class="text-right">{{number_format($diff,3)}}</td>
                                <td class="text-right">{{number_format($diffPerc,3).'%'}}</td>
                            </tr>
                        @endforeach
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



