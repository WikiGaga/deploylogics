@extends('layouts.report')
@section('title', 'Sale Orders Report')

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
        $gTotalAmount = 0;
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date From:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_time_from']))}}</span> - <span style="color: #e27d00;">Date To:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['date_time_to']))}}</span>
                </h6>
                {{-- @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif --}}
                {{-- @if(isset($data['customer_ids']) && count($data['customer_ids']) != 0)
                @php
                    $data['selected_customer'] = \App\Models\TblSaleCustomer::whereIn('customer_id',$data['customer_ids'])->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Customer:</span>
                        @foreach($data['selected_customer'] as $selected_customer)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($selected_customer->customer_name))}}</span><span style="color: #ff0000">,</span>
                        @endforeach
                    </h6>
                @endif --}}
            </div>
            {{-- @include('reports.template.branding') --}}
        </div>
        <div class="kt-portlet__body">
        <?php
       $qry = "SELECT DISTINCT
            o.ID,
            o.ORDER_SERIAL,
            o.ORDER_AMOUNT,
            o.PAYMENT_STATUS,
            o.ORDER_STATUS,
            o.ORDER_TYPE,
            o.ORDER_TAKEN_BY,
            o.CREATED_AT,
            d.CUSTOMER_NAME,
            d.CAR_NUMBER,
            d.PHONE,
            d.cash_paid,
            d.card_paid
        FROM
            ORDERS o
        LEFT JOIN
            POS_ORDER_ADDITIONAL_DTL d ON d.ORDER_ID = o.ID
        WHERE
            o.CREATED_AT BETWEEN '{$data['date_time_from']}' AND '{$data['date_time_to']}'
        ORDER BY
            o.ORDER_SERIAL";



        // where BRANCH_ID IN (".implode(",",$data['branch_ids']).")
        //     and CUSTOMER_NAME NOT IN ('DELETE IT','Delete It')
        //     and CUSTOMER_ENTRY_STATUS <> '0'

        $list = \Illuminate\Support\Facades\DB::select($qry);
        // $list = [];
        // foreach ($getdata as $row){
        //     $list[] = $row;
        // }
?>
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            <th class="text-left">Order ID</th>
                            <th class="text-center">Order Date</th>
                            <th class="text-center">Customer Info</th>
                            <th class="text-center">Order Type</th>
                            <th class="text-center">Order Status</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">Cash Amount</th>
                            <th class="text-center">Card Amount</th>
                            <th class="text-center">Total Amount</th>
                        </tr>
                        @foreach($list as $k=>$detail)
                        @php
                        $gTotalCash += $detail->cash_paid;
                        $gTotalCard += $detail->card_paid;
                        $gTotalAmount += $detail->order_amount;
                        @endphp
                            <tr>
                                <td class="text-left">{{$detail->order_serial}}</td>
                                <td class="text-center">{{date('d-m-Y', strtotime($detail->created_at))}}</td>
                                <td class="text-center">{{$detail->customer_name ?? ''}} <br> {{$detail->car_number ?? ''}} <br> {{$detail->phone ?? ''}} </td>
                                <td class="text-center">{{$detail->order_type}}</td>
                                <td class="text-center">{{$detail->order_status}}</td>
                                <td class="text-center">{{$detail->payment_status}}</td>
                                <td class="text-center">{{$detail->cash_paid}}</td>
                                <td class="text-center">{{$detail->card_paid}}</td>
                                <td class="text-center">{{$detail->order_amount}}</td>
                           </tr>

                        @endforeach
                        <tr class="grand_total">
                            <td colspan="6" class="fw-bold rep-font-bold">Total</td>
                            <td class="text-center fw-bold rep-font-bold">{{number_format($gTotalCash,3)}}</td>
                            <td class="text-center fw-bold rep-font-bold">{{number_format($gTotalCard,3)}}</td>
                            <td class="text-center fw-bold rep-font-bold">{{number_format($gTotalAmount,3)}}</td>
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
