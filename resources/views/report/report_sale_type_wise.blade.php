@extends('layouts.report')
@section('title', 'Reporting')

@section('pageCSS')
    <style>

    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" style="padding: 36px">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                
            </div>
            <div class="kt-portlet__head-toolbar text-center">
                <div href="#" class="kt-invoice__logo">
                    <a href="#"><img src="/images/1601992238.jpeg" width="60px"></a>
                    <div class="kt-invoice__desc">
                    <div>Cecilia Chapman, 711-2880 Nulla St, Mankato</div>
                    <div>Mississippi 96522</div>
                </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr>
                            <th class="text-center">Document Code</th>
                            <th class="text-center">Document Date</th>
                            <th class="text-left">Customer Name</th>
                            <th class="text-center">Credit Net Amount</th>
                            <th class="text-center">Cash Net Amount</th>
                            <th class="text-center">Net Amount</th>
                        </tr>
                        @php 
                            $total_CrAmount = 0;
                            $total_CaAmount = 0;
                        @endphp
                        @foreach($data['list'] as $key=>$list)
                            <tr>
                                <td class="text-center">{{$list->sales_code}}</td>
                                <td class="text-center">{{$list->sales_date}}</td>
                                <td>{{$list->customer_name}}</td>
                                @if($list->sales_sales_type == '2')
                                    @php $total_CrAmount += $list->sales_net_amount; @endphp
                                    <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                                @else
                                    <td class="text-right">{{number_format(0,3)}}</td>
                                @endif
                                
                                @if($list->sales_sales_type == '1')
                                    @php $total_CaAmount += $list->sales_net_amount; @endphp
                                    <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                                @else
                                    <td class="text-right">{{number_format(0,3)}}</td>
                                @endif
                                <td class="text-right">{{number_format($list->sales_net_amount,3)}}</td>
                            </tr>
                        @endforeach
                        @php $total_Amount = $total_CrAmount + $total_CaAmount ; @endphp
                        <tr>
                            <td colspan="3" class="rep-font-bold">Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CrAmount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_CaAmount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($total_Amount,3)}}</td>
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



