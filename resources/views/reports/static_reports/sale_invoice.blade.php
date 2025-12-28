@extends('layouts.report')
@section('title', 'Sale Invoice Report')

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
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->where(\App\Library\Utilities::currentBC())->where('branch_active_status',1)->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['sale_types_multiple']) && count($data['sale_types_multiple']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Sales Type:</span>
                        @foreach($data['sale_types_multiple'] as $sales_type)
                            <span style="color: #5578eb;">{{" ".$sales_type." "}}</span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['product_ids']) && count($data['product_ids']) != 0)
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Product:</span>
                        @foreach($data['product_ids'] as $product_name)
                            <span style="color: #5578eb;">{{" ".$product_name." "}}</span>
                        @endforeach
                    </h6>
                @endif
                @if(isset($data['customer_ids']) && !empty($data['customer_ids']))
                    @php 
                        $customerDtl = \Illuminate\Support\Facades\DB::table('tbl_sale_customer')->where('customer_id',$data['customer_ids'])->first();
                    @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Customer:</span>
                        <span style="color: #5578eb;">{{" ".$customerDtl->customer_name." "}}</span>
                    </h6>
                @endif
                @if(isset($data['users']) && count($data['users']) != 0)
                @php
                    $data['Salesman'] = \App\Models\User::whereIn('id',$data['users'])->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Salesman:</span>
                        @foreach($data['Salesman'] as $Salesman)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($Salesman->name))}}</span><span style="color: #ff0000">,</span>
                        @endforeach
                    </h6>
                @endif
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
        @php
           /// dd($data['product_ids']);
            $getdata = DB::table('vw_sale_sales_invoice')->whereIn('branch_id', $data['branch_ids']);
            if($data['from_date'] == $data['to_date']){
                $getdata = $getdata->whereDate('sales_date', $data['to_date']);
            }else{
                $getdata = $getdata->whereBetween('sales_date',[$data['from_date'],$data['to_date']]);
            }
            if(isset($data['customer_ids']) && !empty($data['customer_ids']) != 0){
                $getdata = $getdata->where('customer_id',$customerDtl->customer_id);
            }
            if(count($data['product_ids']) != 0){
                $getdata = $getdata->whereIn('product_name',$data['product_ids']);
            }
            if(count($data['sale_types_multiple']) != 0){
                $getdata = $getdata->whereIn('sales_type',$data['sale_types_multiple']);
            }
            if(count($data['users']) != 0){
                $getdata = $getdata->whereIn('sales_sales_man',$data['users']);
            }
            if(count($data['payment_types']) != 0){
                $getdata = $getdata->whereIn('sales_sales_type',$data['payment_types']);
            }
            $getdata = $getdata->orderby('sales_date')->orderby('sales_code')->get();
           // dd($getdata);
            $list = [];
            foreach ($getdata as $row)
            {
                $today = date('Y-m-d', strtotime($row->sales_date));
                $list[$today][$row->sales_code][] = $row;
            }
            $data['list'] = $list;
           // dd($data['list']);
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <thead>
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
                        </thead>
                        @php
                            $grand_total_quantity = 0;
                            $grand_total_amount = 0;
                            $grand_total_disc_amount = 0;
                            $grand_total_vat_amount = 0;
                            $grand_total_total_amount = 0;
                        @endphp
                        <tbody>
                            @foreach($data['list'] as $key=>$list)
                                @php
                                    ksort($list);
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
                                        @php
                                            $payment_type = '';
                                            if(isset($invoice[0]->sales_sales_type) && $invoice[0]->sales_sales_type != ''){
                                                $payment_type = \Illuminate\Support\Facades\DB::table('tbl_defi_payment_type')->where('payment_type_id',$invoice[0]->sales_sales_type)->first();
                                                $payment_type = $payment_type->payment_type_name;
                                            }
                                        @endphp
                                        <td colspan="10">{{$k}} - {{isset($invoice[0]->customer_name)?$invoice[0]->customer_name:""}} - {{isset($invoice[0]->sales_sales_man_name)?$invoice[0]->sales_sales_man_name:""}} - {{$payment_type}}</td>
                                    </tr>
                                    @foreach($invoice as $product)
                                        <tr>
                                            <td>{{$product->product_barcode_barcode}}</td>
                                            <td>{{$product->product_name}}</td>
                                            <td class="text-center">{{$product->uom_name}}</td>
                                            <td class="text-center">{{$product->sales_dtl_packing}}</td>
                                            @php
                                                $PerishableItems = \Illuminate\Support\Facades\DB::table('tbl_purc_product_barcode')->where('product_barcode_barcode',$product->product_barcode_barcode)->select('product_barcode_weight_apply')->pluck('product_barcode_weight_apply')->first();
                                                if($PerishableItems == 1){
                                                    $sales_dtl_quantity = number_format($product->sales_dtl_quantity,3);
                                                }else{
                                                    $sales_dtl_quantity = number_format($product->sales_dtl_quantity);
                                                }
                                            @endphp
                                            <td class="text-right">{{$sales_dtl_quantity}}</td>
                                            <td class="text-right">{{number_format($product->sales_dtl_rate,3)}}</td>
                                            <td class="text-right">{{number_format($product->sales_dtl_amount,3)}}</td>
                                            <td class="text-right">{{number_format($product->sales_dtl_disc_amount,3)}}</td>
                                            <td class="text-right">{{number_format($product->sales_dtl_vat_amount,3)}}</td>
                                            <td class="text-right">{{number_format($product->sales_dtl_total_amount,3)}}</td>
                                        </tr>
                                        @php
                                            $total_quantity += $product->sales_dtl_quantity;
                                            $total_amount += $product->sales_dtl_amount;
                                            $total_disc_amount += $product->sales_dtl_disc_amount;
                                            $total_vat_amount += $product->sales_dtl_vat_amount;
                                            $total_total_amount += $product->sales_dtl_total_amount;
                                        @endphp
                                    @endforeach
                                    <tr class="total">
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
                        </tbody>
                        <tfoot style="background: #f7f8fa">
                            <tr>
                                <td colspan="10"  style="background: #f7f8fa">
                                    <div class="kt-portlet__foot sale_invoice_footer"  style="background: #f7f8fa">
                                        <div class="row">
                                            <div class="col-lg-12 kt-align-right">
                                                <div class="date"><span>Date: </span>{{ date('d-m-Y') }} - <span>User: </span>{{auth()->user()->name}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
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



