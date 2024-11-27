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
                @foreach($data['criteria'] as $show)
                    @php
                        $val_1 = isset($show['value_1'])?$show['value_1']:"";
                        $val_2 = isset($show['value_2'])?$show['value_2']." to ":"";
                        $val_3 = isset($show['value_3'])?$show['value_3']:"";
                    @endphp
                    <h6 class="kt-invoice__title">
                        <span style="color: #e27d00;">{{$show['name']}}</span>
                        <span style="color: #5578eb;">{{" ".$show['type']." "}}</span>
                        {{$val_1.''.$val_2.''.$val_3}}</h6>
                @endforeach
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
                            <th>Sales Type</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Disc Amount</th>
                            <th>Vat Amount</th>
                            <th>Net Amount</th>
                        </tr>
                        @php
                            $grand_total_quantity = 0;
                            $grand_total_amount = 0;
                            $grand_total_disc_amount = 0;
                            $grand_total_vat_amount = 0;
                            $grand_total_total_amount = 0;
                        @endphp
                        @foreach($data['list'] as $key=>$list)
                            @php
                                $sub_total_quantity = 0;
                                $sub_total_amount = 0;
                                $sub_total_disc_amount = 0;
                                $sub_total_vat_amount = 0;
                                $sub_total_total_amount = 0;
                            @endphp
                            <tr>
                                <td colspan="9"><b>{{$key}}</b></td>
                            </tr>
                            @foreach($list as $k=>$invoice)
                                @php
                                    $total_quantity = 0;
                                    $total_amount = 0;
                                    $total_disc_amount = 0;
                                    $total_vat_amount = 0;
                                    $total_total_amount = 0;
                                @endphp
                                <tr>
                                    <td colspan="9">{{$k}}</td>
                                </tr>
                                @foreach($invoice as $product)
                                    <tr>
                                        <td>{{$product->sales_sales_type}}</td>
                                        <td>{{$product->product_name}}</td>
                                        <td class="text-right">{{number_format($product->sales_dtl_quantity)}}</td>
                                        <td class="text-right">{{number_format($product->sales_dtl_rate)}}</td>
                                        <td class="text-right">{{number_format($product->sales_dtl_amount)}}</td>
                                        <td class="text-right">{{number_format($product->sales_dtl_disc_amount)}}</td>
                                        <td class="text-right">{{number_format($product->sales_dtl_vat_amount)}}</td>
                                        <td class="text-right">{{number_format($product->sales_dtl_total_amount)}}</td>
                                    </tr>
                                    @php
                                        $total_quantity += $product->sales_dtl_quantity;
                                        $total_amount += $product->sales_dtl_amount;
                                        $total_disc_amount += $product->sales_dtl_disc_amount;
                                        $total_vat_amount += $product->sales_dtl_vat_amount;
                                        $total_total_amount += $product->sales_dtl_total_amount;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="2" class="rep-font-bold">Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_quantity)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($total_amount)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_disc_amount)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_vat_amount)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_total_amount)}}</td>
                                </tr>
                                @php
                                    $sub_total_quantity += $total_quantity;
                                    $sub_total_amount += $total_amount;
                                    $sub_total_disc_amount += $total_disc_amount;
                                    $sub_total_vat_amount += $total_vat_amount;
                                    $sub_total_total_amount += $total_total_amount;
                                @endphp
                            @endforeach
                            <tr class="sub_total">
                                <td colspan="2" class="rep-font-bold">Sub Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_amount)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_disc_amount)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_vat_amount)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_total_amount)}}</td>
                            </tr>
                            @php
                                $grand_total_quantity += $sub_total_quantity;
                                $grand_total_amount += $sub_total_amount;
                                $grand_total_disc_amount += $sub_total_disc_amount;
                                $grand_total_vat_amount += $sub_total_vat_amount;
                                $grand_total_total_amount += $sub_total_total_amount;
                            @endphp
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_quantity)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_amount)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_disc_amount)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_vat_amount)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_total_amount)}}</td>
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



