@extends('layouts.report')
@section('title', 'Sales Invoice HS Code Report')

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
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h3 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h3>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_time_from']))." to ". date('d-m-Y', strtotime($data['date_time_to']))." "}}</span>
                </h6>
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                @php 
                $qry = "select distinct
                    product_id, 
                    product_name,
                    product_barcode_barcode,
                    PRODUCT_BARCODE_ID ,
                    group_item_parent_name,
                    group_item_name,
                    hs_code,
                    sales_code,
                    SALES_DATE
                from 
                    VW_SALE_SALES_INVOICE
                where (sales_date between to_date ('".$data['date_time_from']."', 'yyyy/mm/dd HH24:MI') and to_date ('".$data['date_time_to']."', 'yyyy/mm/dd HH24:MI'))
                    and (hs_code is null or hs_code <> 0)
                ORDER BY sales_code desc, sales_date desc";

                //dd($qry);

                $getdata = \Illuminate\Support\Facades\DB::select($qry);
                //dd($getdata);
                $list = [];
                foreach ($getdata as $row){
                    $list[] = $row;
                }
                //dd($list);
                @endphp

                <div class="col-lg-12">
                    <table width="100%" id="rep_fbr_sales_data_datatable" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <td class="text-center">S.#</td>
                            <td class="text-center">Inv #</td>
                            <td class="text-center">Inv date</td>
                            <td class="text-center">Product Id </td>
                            <td class="text-center">Barcode </td>
                            <td class="text-left">Product Parent Group </td>
                            <td class="text-left">Product Group </td>
                            <td class="text-left">Product Name</td>
                            <td class="text-center">HS Code</td>
                        </tr>
                        @php
                            $ki = 1;
                        @endphp
                        @foreach($list as $inv_k=>$si_detail)
                            <tr>
                                <td class="text-center">{{$ki}}</td>
                                <td class="text-center">{{$si_detail->sales_code}}</td>
                                <td class="text-center">{{date('d-m-Y', strtotime($si_detail->sales_date))}}</td>
                                <td class="text-center">{{$si_detail->product_id}}</td>
                                <td class="text-center">{{$si_detail->product_barcode_barcode}}</td>
                                <td class="text-left">{{$si_detail->group_item_parent_name}}</td>
                                <td class="text-left">{{$si_detail->group_item_name}}</td>
                                <td class="text-left">{{$si_detail->product_name}}</td>
                                <td class="text-center">{{$si_detail->hs_code}}</td>
                            </tr>
                            @php
                                $ki += 1;
                            @endphp
                        @endforeach
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
{{-- @section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_fbr_sales_data_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection --}}
