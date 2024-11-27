@extends('layouts.report')
@php
    $data = Session::get('data');
    //dd($data);
@endphp
@section('title', $data['page_title'])

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

    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            @include('reports.template.criteria')
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
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
                                <th class="text-center">Cost Rate</th>
                                <th class="text-center">Cost Amount</th>
                                <th class="text-center">Stock Qty</th>
                                <th class="text-center">Physical Stock Qty</th>
                            @endif
                            <th class="text-left">Batch No</th>
                            <th class="text-center">Quantity</th>
                            @if($data['key'] == 'stock_receiving' || $data['key'] == 'stock_transfer')
                                <th class="text-center">Sale Rate</th>
                                <th class="text-center">MRP</th>
                            @endif
                            @if($data['key'] != 'stock_adjustment')
                                <th class="text-center">Rate</th>
                                <th class="text-center">Amount</th>
                            @endif
                        </tr>
                        @php
                            $grand_total_quantity = 0;
                            $grand_total_amount = 0;
                            $grand_total_cost_amount=0;
                            $grand_total_stock_quantity=0;
                            $grand_total_phy_quantity=0;
                        @endphp
                        @foreach($data['list'] as $key=>$list)
                            @php
                                $sub_total_quantity = 0;
                                $sub_total_amount = 0;
                                $sub_total_cost_amount=0;
                                $sub_total_stock_quantity=0;
                                $sub_total_phy_quantity=0;
                            @endphp
                            <tr>
                                @if($data['key'] == 'opening_stock' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                    <td colspan="12"><b>{{date('d-m-Y', strtotime($key))}}</b></td>
                                @endif
                                @if($data['key'] == 'stock_adjustment' || $data['key'] == 'expired_items'  || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                    <td colspan="10"><b>{{date('d-m-Y', strtotime($key))}}</b></td>
                                @endif
                            </tr>
                            @foreach($list as $k=>$inventory)
                                @php
                                    $total_quantity = 0;
                                    $total_amount = 0;
                                    $total_cost_amount=0;
                                    $total_stock_quantity=0;
                                    $total_phy_quantity=0;
                                @endphp
                                <tr>
                                    @if($data['key'] == 'opening_stock' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                        <td colspan="12">{{$k}}</td>
                                    @endif
                                    @if($data['key'] == 'stock_adjustment' || $data['key'] == 'expired_items'  || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                        <td colspan="10">{{$k}}</td>
                                    @endif
                                </tr>
                                @foreach($inventory as $item)
                                    @php
                                        $total_quantity += $item->stock_dtl_quantity;
                                        $total_amount += $item->stock_dtl_amount;
                                        $total_cost_amount += $item->cost_amount;
                                        $total_stock_quantity += $item->stock_dtl_stock_quantity;
                                        $total_phy_quantity += $item->stock_dtl_physical_quantity;
                                        
                                        $sub_total_quantity += $item->stock_dtl_quantity;
                                        $sub_total_amount += $item->stock_dtl_amount;
                                        $sub_total_cost_amount += $item->cost_amount;
                                        $sub_total_stock_quantity += $item->stock_dtl_stock_quantity;
                                        $sub_total_phy_quantity += $item->stock_dtl_physical_quantity;
                                        
                                        $grand_total_quantity += $item->stock_dtl_quantity;
                                        $grand_total_amount += $item->stock_dtl_amount;
                                        $grand_total_cost_amount += $item->cost_amount;
                                        $grand_total_stock_quantity += $item->stock_dtl_stock_quantity;
                                        $grand_total_phy_quantity += $item->stock_dtl_physical_quantity;

                                    @endphp
                                    <tr>
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
                                            <td class="text-right">{{number_format($item->cost_rate,3)}}</td>
                                            <td class="text-right">{{number_format($item->cost_amount,3)}}</td>
                                            <td class="text-right">{{number_format($item->stock_dtl_stock_quantity,0)}}</td>
                                            <td class="text-right">{{number_format($item->stock_dtl_physical_quantity,0)}}</td>
                                        @endif
                                        <td class="text-right">{{$item->stock_dtl_batch_no}}</td>
                                        <td class="text-right">{{number_format($item->stock_dtl_quantity,0)}}</td>
                                        @if($data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                            <td class="text-right">{{number_format($item->stock_dtl_purc_rate,3)}}</td>
                                            <td class="text-right">{{number_format($item->mrp,3)}}</td>
                                        @endif
                                        @if($data['key'] != 'stock_adjustment')
                                            <td class="text-right">{{number_format($item->stock_dtl_rate,3)}}</td>
                                            <td class="text-right">{{number_format($item->stock_dtl_amount,3)}}</td>
                                        @endif
                                    </tr>
                                @endforeach
                                <tr>
                                    @if($data['key'] == 'opening_stock'  || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                        <td colspan="6" class="rep-font-bold">Total:</td>
                                    @endif
                                    @if($data['key'] == 'expired_items'  || $data['key'] == 'stock_adjustment' || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                        <td colspan="4" class="rep-font-bold">Total:</td>
                                    @endif
                                    @if($data['key'] == 'stock_adjustment')
                                        <td class="text-right rep-font-bold"></td>
                                        <td class="text-right rep-font-bold">{{number_format($total_cost_amount,3)}}</td>
                                        <td class="text-right rep-font-bold">{{number_format($total_stock_quantity,0)}}</td>
                                        <td class="text-right rep-font-bold">{{number_format($total_phy_quantity,0)}}</td>
                                    @endif
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($total_quantity,0)}}</td>
                                    
                                    @if($data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                        <td class="text-right rep-font-bold"></td>
                                        <td class="text-right rep-font-bold"></td>
                                    @endif
                                    @if($data['key'] != 'stock_adjustment')
                                        <td class="text-right rep-font-bold"></td>
                                        <td class="text-right rep-font-bold">{{number_format($total_amount,3)}}</td>
                                    @endif
                                </tr>
                            @endforeach
                            <tr class="sub_total">
                                @if($data['key'] == 'opening_stock' || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                    <td colspan="6" class="rep-font-bold">( {{date('d-m-Y', strtotime($key))}} ) Sub Total:</td>
                                @endif
                                @if($data['key'] == 'expired_items' || $data['key'] == 'stock_adjustment' || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                    <td colspan="4" class="rep-font-bold">( {{date('d-m-Y', strtotime($key))}} ) Sub Total:</td>
                                @endif
                                @if($data['key'] == 'stock_adjustment')
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_cost_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_stock_quantity,0)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_phy_quantity,0)}}</td>
                                @endif
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_quantity,0)}}</td>
                                @if($data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold"></td>
                                @endif
                                @if($data['key'] != 'stock_adjustment')
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($sub_total_amount,3)}}</td>
                                @endif
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            @if($data['key'] == 'opening_stock'  || $data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                <td colspan="6" class="rep-font-bold">Grand Total:</td>
                            @endif
                            @if($data['key'] == 'expired_items' || $data['key'] == 'stock_adjustment' || $data['key'] == 'sample_items' || $data['key'] == 'damaged_items')
                                <td colspan="4" class="rep-font-bold">Grand Total:</td>
                            @endif
                            @if($data['key'] == 'stock_adjustment')
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($grand_total_cost_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($grand_total_stock_quantity,0)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($grand_total_phy_quantity,0)}}</td>
                            @endif
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_quantity,0)}}</td>
                            @if($data['key'] == 'stock_transfer' || $data['key'] == 'stock_receiving')
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold"></td>
                            @endif
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



