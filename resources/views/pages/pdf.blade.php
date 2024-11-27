{{--@php
    $data = Session::get('data');
@endphp
<style>
    @font-face {
        font-family: 'roboto';
        font-style: normal;
        font-weight: 100;
        font-display: swap;
        src: url('Roboto/Roboto-Regular.ttf') format("truetype");
    }
    @font-face {
        font-family: 'roboto';
        font-style: normal;
        font-weight: 300;
        font-display: swap;
        src: url('Roboto/Roboto-Light.ttf') format("truetype");
    }
    @font-face {
        font-family: 'roboto';
        font-style: normal;
        font-weight: 500;
        font-display: swap;
        src: url('Roboto/Roboto-Medium.ttf') format("truetype");
    }
</style>--}}
<style>
    /*
     font-family: roboto !important;
     font-weight: 400,700
     font-style: normal,bold
   */
    /* Styles go here */
    body{
        font-family: roboto !important;
        font-style: normal;
        color: #646c9a;
        height: 100%;
        margin: 0px;
        padding: 0px;
        font-size: 13px;
        -ms-text-size-adjust: 100%;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    #kt_portlet_table{
        margin-bottom: 0 !important;
        border: 0;
        table-layout: fixed;
    }
    td, th {
        padding: 4px 5px !important;
    }
    th {
        font-size: 14px;
        color: #777676 !important;
    }
    td {
        font-size: 14px;
        color: #777676 !important;
    }
    .table-bordered {
        border: 1px solid #bbbbbb !important;
        border-spacing:0;
        /* border: 1px solid #777777 !important;*/
    }
    .table tr th {
        border-bottom: 1px solid #c7c7c7 !important;
        cursor: pointer;
    }
    .table tr th,
    .table tr td{
        border-right: 1px solid #ebedf2 !important;
    }
    tr:nth-child(even)>td {
        border-bottom: 1px solid #c7c7c7 !important;
    }
    tr:nth-child(odd)>td{
        border-bottom: 1px solid #ead8b1 !important;
    }

    table#rep_sale_invoice_datatable {
        border: 0 !important;
    }
    table#rep_sale_invoice_datatable tr>th:first-child,
    table#rep_sale_invoice_datatable tr>td:first-child {
        border-left: 0 !important;
    }
    table#rep_sale_invoice_datatable tr>th:last-child,
    table#rep_sale_invoice_datatable tr>td:last-child {
        border-right: 0 !important;
    }
    table#rep_sale_invoice_datatable tr>th{
        border-top: 1.5px solid #777777 !important;
        border-bottom: 1.5px solid #777777 !important;
        cursor: default;
    }
    table#rep_sale_invoice_datatable .total>td{
        border-top: 1px solid #000000 !important;
        border-bottom: 1px solid #000000 !important;
    }
    table#dynamic_report_table .sub_total>td,
    table#rep_sale_invoice_datatable .sub_total>td{
        border-bottom: 1px solid #000000 !important;
    }
    table#dynamic_report_table .grand_total>td,
    table#rep_sale_invoice_datatable .grand_total>td{
        border-bottom: 2px solid #969696 !important;
        border-top: 2px solid #cecece !important;
        background-color: #f7f8fa;
        font-size: 15px;
    }
    .sale_invoice_footer{
        background: #f7f8fa;
    }
    .sale_invoice_footer .date{
        color: #FE21BE;
    }
    .date {
        font-size: 12px;
        color: #7d7d7d;
    }
    .date>span {
        color: #000000;
    }
    .row.row-block {
        margin: 10px 0 !important;
        padding: 0 !important;
    }

    .kt-portlet {
        background-color: #ffffff;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .kt-portlet .kt-portlet__head {
        position: relative;
        width: 100%;
        border-bottom: 1px solid #ebedf2;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        padding-bottom: 60px;
        margin-bottom: 30px;
    }
    .kt-invoice__brand{
        width: 80%;
    }
    .kt-portlet__head-toolbar{
        position: absolute;
        top:0;
        right: 0;
        width: 18%;
        text-align: center;
    }
    .kt-invoice__title{
        font-size: 32.5px;
    }
    .kt-invoice__criteria{
        font-size: 13px;
    }
    .kt-invoice__desc{
        color: #646c9a;
    }
    a{
        text-decoration: unset;
    }
    h1,h6{
        margin-block-start: 0 !important;
        margin-block-end: 0 !important;
        margin-inline-start: 0 !important;
        margin-inline-end: 0 !important;
    }
    h1{
        font-family: roboto5 !important;
        font-style: normal !important;
        font-weight: 400;
        margin-top: -10px;
    }
    h6{
        margin-top: -30px;
        font-weight: 400;
    }
    .kt-invoice__title {
        font-family: roboto5 !important;
        font-weight: 400;
        font-style: normal;
    }
    .kt-invoice__criteria,
    .kt-invoice__desc{
        font-weight: 400;
    }
    .sale_invoice_footer{
        height: 25px;
    }
    .kt-align-center,.text-center{ text-align: center; }
    .kt-align-right,.text-right{ text-align: right; }
    .kt-align-left,.text-left{ text-align: left; }
</style>
<body>
<div class="kt-portlet" id="kt_portlet_table">
    <div class="kt-portlet__head">
        <div class="kt-invoice__brand">
            <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
            <h6 class="kt-invoice__criteria">
                <span style="color: #e27d00;">Date:</span>
                <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
            </h6>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-invoice__logo">
                <div>
                    @php
                        $path = base_path()."/public/images/".auth()->user()->business->business_profile;
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data_img = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
                    @endphp
                    <img src="{{$base64}}" width="60px">
                </div>
                <div class="kt-invoice__desc">
                    <div>{{strtoupper(auth()->user()->branch->branch_name)}}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="row row-block">
            <div class="col-lg-12">
                <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                    <tr>
                        <th class="text-center">Store </th>
                        @if($data['key'] == 'stock_transfer')
                            <th class="text-center">Branch From</th>
                            <th class="text-center">Branch To</th>
                        @endif
                        @if($data['key'] == 'stock_receiving')
                            <th class="text-center">Receiving From</th>
                            <th class="text-center">Receiving To</th>
                        @endif
                        <th class="text-left">Barcode</th>
                        <th class="text-left">Product Name</th>
                        <th class="text-center">UOM</th>
                        <th class="text-center">Packing</th>
                        @if($data['key'] == 'opening_stock')
                            <th class="text-left">Production Date</th>
                            <th class="text-left">Expiry Date</th>
                        @endif
                        @if($data['key'] == 'stock_adjustment')
                            <th class="text-center">Stock Qty</th>
                            <th class="text-center">Physical Stock Qty</th>
                        @endif
                        <th class="text-left">Batch No</th>
                        <th class="text-center">Quantity</th>
                        @if($data['key'] != 'stock_adjustment')
                            <th class="text-center">Rate</th>
                            <th class="text-center">Amount</th>
                        @endif
                    </tr>
                    @php
                        $grand_total_quantity = 0;
                        $grand_total_amount = 0;
                    @endphp
                    @foreach($data['list'] as $key=>$list)
                        @php
                            $sub_total_quantity = 0;
                            $sub_total_amount = 0;
                        @endphp
                        <tr>
                            @if($data['key'] == 'opening_stock' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                <td colspan="11"><b>{{date('d-m-Y', strtotime($key))}}</b></td>
                            @endif
                            @if($data['key'] == 'stock_adjustment' || $data['key'] == 'expired_items'  || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                <td colspan="9"><b>{{date('d-m-Y', strtotime($key))}}</b></td>
                            @endif
                        </tr>
                        @foreach($list as $k=>$inventory)
                            @php
                                $total_quantity = 0;
                                $total_amount = 0;
                            @endphp
                            <tr>
                                @if($data['key'] == 'opening_stock' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                    <td colspan="11">{{$k}}</td>
                                @endif
                                @if($data['key'] == 'stock_adjustment' || $data['key'] == 'expired_items'  || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                    <td colspan="9">{{$k}}</td>
                                @endif
                            </tr>
                            @foreach($inventory as $item)
                                <tr>
                                    <td>{{$item->stock_store_from_name}}</td>
                                    @if($data['key'] == 'stock_transfer')
                                        <td>{{$item->stock_branch_from_name}}</td>
                                        <td>{{$item->stock_branch_to_name}}</td>
                                    @endif
                                    @if($data['key'] == 'stock_receiving')
                                        <td>{{$item->stock_branch_from_name}}</td>
                                        <td>{{$item->stock_branch_to_name}}</td>
                                    @endif
                                    <td>{{$item->product_barcode_barcode}}</td>
                                    <td>{{$item->product_name}}</td>
                                    <td class="text-center">{{$item->uom_name}}</td>
                                    <td class="text-center">{{$item->stock_dtl_packing}}</td>
                                    @if($data['key'] == 'opening_stock')
                                        @if(!empty($item->stock_dtl_production_date))
                                            <td class="text-right" style="white-space: nowrap;">{{date('d-m-Y', strtotime($item->stock_dtl_production_date))}}</td>
                                        @else
                                            <td class="text-right"></td>
                                        @endif
                                        @if(!empty($item->stock_dtl_production_date))
                                            <td class="text-right" style="white-space: nowrap;">{{date('d-m-Y', strtotime($item->stock_dtl_expiry_date))}}</td>
                                        @else
                                            <td class="text-right"></td>
                                        @endif
                                    @endif
                                    @if($data['key'] == 'stock_adjustment')
                                        <td class="text-right">{{$item->stock_dtl_stock_quantity}}</td>
                                        <td class="text-right">{{$item->stock_dtl_physical_quantity}}</td>
                                    @endif
                                    <td class="text-right">{{$item->stock_dtl_batch_no}}</td>
                                    <td class="text-right">{{number_format($item->stock_dtl_quantity)}}</td>
                                    @if($data['key'] != 'stock_adjustment')
                                        <td class="text-right">{{number_format($item->stock_dtl_rate,3)}}</td>
                                        <td class="text-right">{{number_format($item->stock_dtl_amount,3)}}</td>
                                    @endif
                                </tr>
                                @php
                                    $total_quantity += $item->stock_dtl_quantity;
                                    $total_amount += $item->stock_dtl_amount;
                                @endphp
                            @endforeach
                            <tr>
                                @if($data['key'] == 'opening_stock'  || $data['key'] == 'stock_adjustment' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                    <td colspan="7" class="rep-font-bold">Total:</td>
                                @endif
                                @if($data['key'] == 'expired_items'  || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                    <td colspan="5" class="rep-font-bold">Total:</td>
                                @endif
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($total_quantity)}}</td>
                                @if($data['key'] != 'stock_adjustment')
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($total_amount,3)}}</td>
                                @endif
                            </tr>
                            @php
                                $sub_total_quantity += $total_quantity;
                                $sub_total_amount += $total_amount;
                            @endphp
                        @endforeach
                        <tr class="sub_total">
                            @if($data['key'] == 'opening_stock' || $data['key'] == 'stock_adjustment' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                <td colspan="7" class="rep-font-bold">( {{date('d-m-Y', strtotime($key))}} ) Sub Total:</td>
                            @endif
                            @if($data['key'] == 'expired_items'  || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                <td colspan="5" class="rep-font-bold">( {{date('d-m-Y', strtotime($key))}} ) Sub Total:</td>
                            @endif
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                            @if($data['key'] != 'stock_adjustment')
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_amount,3)}}</td>
                            @endif
                        </tr>
                        @php
                            $grand_total_quantity += $sub_total_quantity;
                            $grand_total_amount += $sub_total_amount;
                        @endphp
                    @endforeach
                    <tr class="grand_total">
                        @if($data['key'] == 'opening_stock'  || $data['key'] == 'stock_adjustment' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                            <td colspan="7" class="rep-font-bold">Grand Total:</td>
                        @endif
                        @if($data['key'] == 'expired_items'  || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                            <td colspan="5" class="rep-font-bold">Grand Total:</td>
                        @endif
                        <td class="text-right rep-font-bold"></td>
                        <td class="text-right rep-font-bold">{{number_format($grand_total_quantity)}}</td>
                        @if($data['key'] != 'stock_adjustment')
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_amount,3)}}</td>
                        @endif
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
</body>
