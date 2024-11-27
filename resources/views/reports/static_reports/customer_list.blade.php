@extends('layouts.report')
@section('title', 'Customer List Report')

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
                @if(isset($data['customer_group']) && count($data['customer_group']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Customer Group:</span>
                        @foreach($data['customer_group'] as $customer_group)
                            @php
                                $customer_type = \App\Models\TblSaleCustomerType::where('customer_type_id',$customer_group)->first();
                            @endphp
                            <span style="color: #5578eb;">{{" ".$customer_type->customer_type_name." "}}</span>
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
                            <th class="text-left">Name</th>
                            <th class="text-center">City</th>
                            <th class="text-center">Mobile No</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Tax No</th>
                            <th width="20%" class="text-left">Address</th>
                        </tr>
                        @foreach($data['customer'] as $customer)
                        @php
                            $city = \App\Models\TblDefiCity::where('city_id',$customer->city_id)->first();
                        @endphp
                            <tr>
                                <td>{{$customer->customer_name}}</td>
                                <td class="text-center">{{isset($city->city_name)? $city->city_name:''}}</td>
                                <td class="text-center">{{$customer->customer_mobile_no}}</td>
                                <td class="text-right">{{$customer->customer_email}}</td>
                                <td class="text-right">{{$customer->customer_tax_no}}</td>
                                <td class="text-left">{{$customer->customer_address}}</td>
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



