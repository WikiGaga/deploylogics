@extends('layouts.report')
@section('title', 'Purchase Order Report')

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
        $podata = \Illuminate\Support\Facades\DB::table('vw_purc_purchase_order')->whereIn('branch_id', $data['branch_ids'])
            ->whereBetween('purchase_order_entry_date',[$data['from_date'],$data['to_date']])
            ->orderby('purchase_order_entry_date')->orderby('purchase_order_code')
            ->get();
        if(!empty($data['product_ids'])){
                    $podata = $podata->whereIn('product_name',$data['product_ids']);
        }
        if(!empty($data['supplier_ids'])){
                    $podata = $podata->whereIn('supplier_id',$data['supplier_ids']);
        }
      //  dd($podata);

        $list = [];
        foreach ($podata as $row)
        {
            $today = date('Y-m-d', strtotime($row->purchase_order_entry_date));
            $list[$today][$row->purchase_order_code][] = $row;
        }
       // dd($list);
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" >
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
                @if(isset($data['product_ids']) && count($data['product_ids']) != 0)
                @php
                    $data['selected_product'] = \App\Models\ViewPurcProduct::whereIn('product_name',$data['product_ids'])->groupBy('product_id','product_name')->select('product_id','product_name')->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['selected_product'] as $selected_product)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($selected_product->product_name))}}</span><span style="color: #ff0000">,</span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['supplier_ids']) && count($data['supplier_ids']) != 0)
                @php
                    $data['selected_supplier'] = \App\Models\TblPurcSupplier::whereIn('supplier_id',$data['supplier_ids'])->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Supplier:</span>
                        @foreach($data['selected_supplier'] as $selected_supplier)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($selected_supplier->supplier_name))}}</span><span style="color: #ff0000">,</span>
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
                            <th class="text-center">Barcode</th>
                            <th class="text-left">Product Name</th>
                            <th class="text-center">UOM</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Disc Amount</th>
                            <th class="text-center">Vat Amount</th>
                            <th class="text-center">Net Amount</th>
                        </tr>
                        @php
                            $grand_total_quantity = 0;
                            $grand_total_amount = 0;
                            $grand_total_disc_amount = 0;
                            $grand_total_vat_amount = 0;
                            $grand_total_total_amount = 0;
                        @endphp
                        @foreach($list as $key=>$list)
                            @php
                                $sub_total_quantity = 0;
                                $sub_total_amount = 0;
                                $sub_total_disc_amount = 0;
                                $sub_total_vat_amount = 0;
                                $sub_total_total_amount = 0;
                            @endphp
                            <tr>
                                <td colspan="10"><b>{{date('d-m-Y', strtotime($key))}}</b></td>
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
                                    <td colspan="10">{{$k}} {{isset($invoice[0]->supplier_name)?$invoice[0]->supplier_name:''}}</td>
                                </tr>
                                @foreach($invoice as $product)

                                    <tr>
                                        <td>{{$product->product_barcode_barcode}}</td>
                                        <td>{{$product->product_name}}</td>
                                        <td class="text-center">{{$product->uom_name}}</td>
                                        <td class="text-center">{{$product->purchase_order_dtlpacking}}</td>
                                        <td class="text-right">{{number_format($product->purchase_order_dtlquantity)}}</td>
                                        <td class="text-right">{{number_format($product->purchase_order_dtlrate,3)}}</td>
                                        <td class="text-right">{{number_format($product->purchase_order_dtlamount,3)}}</td>
                                        <td class="text-right">{{number_format($product->purchase_order_dtldisc_amount,3)}}</td>
                                        <td class="text-right">{{number_format($product->purchase_order_dtlvat_amount,3)}}</td>
                                        <td class="text-right">{{number_format($product->purchase_order_dtltotal_amount,3)}}</td>
                                    </tr>
                                    @php
                                        $total_quantity += $product->purchase_order_dtlquantity;
                                        $total_amount += $product->purchase_order_dtlamount;
                                        $total_disc_amount += $product->purchase_order_dtldisc_amount;
                                        $total_vat_amount += $product->purchase_order_dtlvat_amount;
                                        $total_total_amount += $product->purchase_order_dtltotal_amount;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="4" class="rep-font-bold">Total:</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_quantity)}}</td>
                                    <td class="text-right rep-font-bold"></td>
                                    <td class="text-right rep-font-bold">{{number_format($total_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_disc_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_vat_amount,3)}}</td>
                                    <td class="text-right rep-font-bold">{{number_format($total_total_amount,3)}}</td>
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
                                <td colspan="4" class="rep-font-bold">( {{date('d-m-Y', strtotime($key))}} ) Sub Total:</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_quantity)}}</td>
                                <td class="text-right rep-font-bold"></td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_disc_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_vat_amount,3)}}</td>
                                <td class="text-right rep-font-bold">{{number_format($sub_total_total_amount,3)}}</td>
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
                            <td colspan="4" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_quantity)}}</td>
                            <td class="text-right rep-font-bold"></td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_disc_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_vat_amount,3)}}</td>
                            <td class="text-right rep-font-bold">{{number_format($grand_total_total_amount,3)}}</td>
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



