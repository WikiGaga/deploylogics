@extends('layouts.report')
@section('title', 'Product Wise Profit')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        tbody.product_wise_profit tr:hover {
            background: antiquewhite;
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
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_from']))." to ". date('d-m-Y', strtotime($data['date_to']))." "}}</span>
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get(['branch_id','branch_name']); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="gross_profit" class="static_report_table table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-center">Order Type</th>
                            <th class="text-center">Food Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Discount Type</th>
                            <th class="text-center">Discount Amount</th>
                            <th class="text-center">Add On Price</th>
                            <th class="text-center">Net Amount</th>
                        </tr>

                        <tbody class="product_wise_profit">
                            @php
                                $start_date = \Carbon\Carbon::parse($data['date_from']);
                                $end_date = \Carbon\Carbon::parse($data['date_to']);
                                $grand_total_quantity = 0;
                                $grand_total_price = 0;
                                $grand_total_discount = 0;
                                $grand_total_add_on_price = 0;
                                $grand_total_net_amount = 0;
                            @endphp
                            @foreach ($data['branch_ids'] as $branch_id)
                                <tr>
                                    <td colspan="8">{{ $start_date->format('Y-m-d') }} - {{ $end_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td>Branch:</td>
                                    <td colspan="7">
                                        @php
                                            $branch_name = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')
                                                ->where('branch_id', $branch_id)
                                                ->value('branch_name');
                                        @endphp
                                        {{ $branch_name }}
                                    </td>
                                </tr>
                                @php
                                    $report_data = \DB::table('vw_rest_order_dtl')
                                        ->whereBetween('order_date', [$data['date_from'], $data['date_to']])
                                        ->where('branch_id', $branch_id)
                                        ->where('is_deleted', 'N')
                                        ->where('item_discount_type_id', '!=', null)
                                        ->select(\DB::raw('order_type, food_name, item_discount_type, SUM(quantity) as total_quantity, sum(price) as total_price, sum(item_discount) as total_discount, sum(total_add_on_price) as total_add_on_price, sum(item_net_amount) as total_net_amount'))
                                        ->groupBy('order_type', 'food_name', 'item_discount_type')
                                        ->get();
                                @endphp
                                @forelse ($report_data as $data_row)
                                    @php 
                                        $grand_total_quantity += $data_row->total_quantity;
                                        $grand_total_price += $data_row->total_price;
                                        $grand_total_discount += $data_row->total_discount;
                                        $grand_total_add_on_price += $data_row->total_add_on_price;
                                        $grand_total_net_amount += $data_row->total_net_amount;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $data_row->order_type }}
                                        </td>
                                        <td>
                                            {{ $data_row->food_name }}
                                        </td>
                                        <td class="text-right">
                                            {{ $data_row->total_quantity }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($data_row->total_price, 2) }}
                                        </td>
                                        <td class="text-center">
                                            {{ $data_row->item_discount_type }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($data_row->total_discount, 2) }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($data_row->total_add_on_price, 2) }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($data_row->total_net_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-left">No data available for this branch.</td>
                                    </tr>
                                @endforelse
                            @endforeach
                        </tbody>
                        <tr class="grand_total">
                            <td colspan="2" class="rep-font-bold">Grand Total:</td>
                            <td class="text-right rep-font-bold">{{ $grand_total_quantity }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($grand_total_price, 2) }}</td>
                            <td class="text-right rep-font-bold">&nbsp;</td>
                            <td class="text-right rep-font-bold">{{ number_format($grand_total_discount, 2) }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($grand_total_add_on_price, 2) }}</td>
                            <td class="text-right rep-font-bold">{{ number_format($grand_total_net_amount, 2) }}
                        </tr>
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



