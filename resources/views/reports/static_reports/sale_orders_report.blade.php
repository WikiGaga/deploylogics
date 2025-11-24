@extends('layouts.report')
@section('title', 'POS Orders Report')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            tfoot>tr>td {
                padding: 0 !important;
            }

            body {
                margin: 0;
            }
        }

        /* Order row hover effects */
        .order-row {
            transition: all 0.3s ease;
        }

        .order-row:hover {
            background-color: #f8f9fa !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Cancelled order highlighting */
        .cancelled-order {
            background-color: #f8d7da !important;
            border-left: 4px solid #dc3545 !important;
        }

        .cancelled-order:hover {
            background-color: #f5c6cb !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
        }

        .cancelled-order td {
            color: #721c24;
            font-weight: 500;
        }

        /* Modal styling */
        .modal-lg {
            max-width: 900px;
        }

        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-header {
            border-radius: 10px 10px 0 0;
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
            border-radius: 0 0 10px 10px;
        }

        /* Card styling */
        .card {
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }

        .card-header {
            border-radius: 8px 8px 0 0;
            border-bottom: 1px solid #e9ecef;
        }

        /* Table styling */
        .table th {
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
        }

        /* Image styling */
        .img-thumbnail {
            border-radius: 6px;
            border: 2px solid #e9ecef;
        }

        /* Loading animation */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

                /* Variations and Addons styling */
        .variation-item {
            background-color: #f8f9fa;
            border-left: 2px solid #007bff;
            padding: 3px 8px;
            margin: 1px 0;
            border-radius: 2px;
            font-size: 0.9rem;
        }

        .addon-item {
            background-color: #f8f9fa;
            border-left: 2px solid #28a745;
            padding: 3px 8px;
            margin: 1px 0;
            border-radius: 2px;
            font-size: 0.9rem;
        }

        .variation-addon-item {
            background-color: #fff3cd;
            border-left: 2px solid #ffc107;
            padding: 2px 6px;
            margin: 1px 0;
            border-radius: 2px;
            font-size: 0.85rem;
        }

        .variation-name {
            font-weight: 600;
            color: #007bff;
            font-size: 0.9rem;
        }

        .addon-name {
            font-weight: 600;
            color: #28a745;
            font-size: 0.9rem;
        }

        .variation-value, .addon-details {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .variation-addon-name {
            font-weight: 500;
            color: #856404;
            font-size: 0.85rem;
        }

        /* Order Details Modal Font Sizes - Bigger fonts for easy reading */
        #orderDetailsModal .modal-title {
            font-size: 1.4rem !important;
            font-weight: 600 !important;
        }

        #orderDetailsModal .card-header h6 {
            font-size: 1.2rem !important;
            font-weight: 600 !important;
        }

        #orderDetailsModal .card-body {
            font-size: 1.1rem !important;
        }

        #orderDetailsModal .card-body strong {
            font-size: 1.15rem !important;
            font-weight: 600 !important;
        }

        #orderDetailsModal .table th {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            padding: 12px 8px !important;
        }

        #orderDetailsModal .table td {
            font-size: 1.05rem !important;
            padding: 10px 8px !important;
            vertical-align: middle !important;
        }

        #orderDetailsModal .table td .font-weight-bold {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
        }

        #orderDetailsModal .table td small {
            font-size: 0.95rem !important;
        }

        #orderDetailsModal .variation-item,
        #orderDetailsModal .addon-item,
        #orderDetailsModal .variation-addon-item {
            font-size: 1rem !important;
            padding: 6px 10px !important;
        }

        #orderDetailsModal .variation-name,
        #orderDetailsModal .addon-name {
            font-size: 1.05rem !important;
        }

        #orderDetailsModal .variation-value,
        #orderDetailsModal .addon-details,
        #orderDetailsModal .variation-addon-name {
            font-size: 1rem !important;
        }

        #orderDetailsModal .d-flex span {
            font-size: 1.1rem !important;
        }

        #orderDetailsModal .h5 {
            font-size: 1.3rem !important;
        }

        #orderDetailsModal .btn {
            font-size: 1.1rem !important;
            padding: 8px 16px !important;
        }

        #orderDetailsModal .alert {
            font-size: 1.1rem !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-lg {
                max-width: 95%;
                margin: 10px auto;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            /* Adjust modal fonts for mobile */
            #orderDetailsModal .modal-title {
                font-size: 1.2rem !important;
            }

            #orderDetailsModal .card-header h6 {
                font-size: 1.1rem !important;
            }

            #orderDetailsModal .card-body {
                font-size: 1rem !important;
            }

            #orderDetailsModal .table th,
            #orderDetailsModal .table td {
                font-size: 0.95rem !important;
            }
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
        $gTotalGrossAmt = 0;
        $gTotalAddons = 0;
        $gTotalDiscount = 0;
        $gTotalDeliveryCharge = 0;
        $gTotalTax = 0;
        $gTotalCash = 0;
        $gTotalCard = 0;
        $gTotalAmount = 0;
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{ strtoupper($data['page_title']) }}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date From:</span>
                    <span style="color: #5578eb;">{{ ' ' . date('d-m-Y', strtotime($data['date_time_from'])) }}</span> - <span
                        style="color: #e27d00;">Date To:</span>
                    <span style="color: #5578eb;">{{ ' ' . date('d-m-Y', strtotime($data['date_time_to'])) }}</span>
                </h6>
                {{-- @if (count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach ($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif --}}
                {{-- @if (isset($data['customer_ids']) && count($data['customer_ids']) != 0)
                @php
                    $data['selected_customer'] = \App\Models\TblSaleCustomer::whereIn('customer_id',$data['customer_ids'])->get();
                @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Customer:</span>
                        @foreach ($data['selected_customer'] as $selected_customer)
                            <span style="color: #5578eb;">{{" ".ucfirst(strtolower($selected_customer->customer_name))}}</span><span style="color: #ff0000">,</span>
                        @endforeach
                    </h6>
                @endif --}}
            </div>
            {{-- @include('reports.template.branding') --}}
        </div>
        <div class="kt-portlet__body">
            <?php
            $qry = "SELECT
                o.ID,
                o.ORDER_SERIAL,
                o.ORDER_AMOUNT,
                o.TOTAL_TAX_AMOUNT,
                o.DELIVERY_CHARGE,
                o.PAYMENT_STATUS,
                o.ORDER_STATUS,
                o.ORDER_TYPE,
                o.ORDER_TAKEN_BY,
                o.CREATED_AT,
                d.CUSTOMER_NAME,
                d.CAR_NUMBER,
                d.PHONE,
                d.cash_paid,
                d.card_paid,
                COALESCE(SUM(CASE WHEN (od.is_deleted IS NULL OR od.is_deleted <> 'Y') THEN od.price * od.quantity ELSE 0 END), 0) AS gross_amount,
                COALESCE(SUM(CASE WHEN (od.is_deleted IS NULL OR od.is_deleted <> 'Y') THEN od.total_add_on_price ELSE 0 END), 0) AS total_addons,
                COALESCE(SUM(CASE WHEN (od.is_deleted IS NULL OR od.is_deleted <> 'Y') THEN od.discount_on_food ELSE 0 END), 0) AS discount_on_food,
                MAX(CASE WHEN od.is_deleted = 'Y' THEN 1 ELSE 0 END) as has_cancelled_items
            FROM
                ORDERS o
            LEFT JOIN
                POS_ORDER_ADDITIONAL_DTL d ON d.ORDER_ID = o.ID
            LEFT JOIN
                order_details od ON od.order_id = o.ID
            WHERE
                o.CREATED_AT BETWEEN '{$data['date_time_from']}' AND '{$data['date_time_to']}'
            GROUP BY
                o.ID,
                o.ORDER_SERIAL,
                o.ORDER_AMOUNT,
                o.TOTAL_TAX_AMOUNT,
                o.DELIVERY_CHARGE,
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
            ORDER BY
                o.CREATED_AT DESC,
                o.ORDER_SERIAL DESC";

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
                            <th class="text-center">Gross Amount</th>
                            <th class="text-center">Addons</th>
                            <th class="text-center">Discount</th>
                            <th class="text-center">Delivery Charges</th>
                            <th class="text-center">VAT</th>
                            <th class="text-center">Net Amount</th>
                            <th class="text-center">Cash Amount</th>
                            <th class="text-center">Visa Amount</th>
                        </tr>
                        @foreach ($list as $k => $detail)
                            @php
                                $isCanceled = strtolower($detail->order_status ?? '') === 'canceled' || strtolower($detail->order_status ?? '') === 'cancelled';
                                $hasCancelledItems = isset($detail->has_cancelled_items) && $detail->has_cancelled_items == 1;
                                $shouldHighlight = $isCanceled || $hasCancelledItems;
                                $gTotalGrossAmt += $detail->gross_amount;
                                $gTotalAddons += $detail->total_addons ?? 0;
                                $gTotalDiscount += $detail->discount_on_food;
                                $gTotalDeliveryCharge += $detail->delivery_charge;
                                $gTotalTax += $detail->total_tax_amount;
                                $gTotalCash += $detail->cash_paid;
                                $gTotalCard += $detail->card_paid;
                                $gTotalAmount += $detail->order_amount;
                            @endphp
                            <tr class="order-row {{ $shouldHighlight ? 'table-danger cancelled-order' : '' }}" data-order-id="{{ $detail->id }}" style="cursor: pointer;">
                                <td class="text-left">{{ $detail->order_serial }}</td>
                                <td class="text-center">{{ date('d-m-Y', strtotime($detail->created_at)) }}</td>
                                <td class="text-center">{{ $detail->customer_name ?? '' }} <br>
                                    {{ $detail->car_number ?? '' }} <br> {{ $detail->phone ?? '' }} </td>
                                <td class="text-center">{{ $detail->order_type }}</td>
                                <td class="text-center">{{ $detail->order_status }}</td>
                                <td class="text-center">{{ $detail->payment_status }}</td>
                                <td class="text-center">{{ $detail->gross_amount }}</td>
                                <td class="text-center">{{ $detail->total_addons ?? 0 }}</td>
                                <td class="text-center">{{ $detail->discount_on_food }}</td>
                                <td class="text-center">{{ $detail->delivery_charge }}</td>
                                <td class="text-center">{{ $detail->total_tax_amount }}</td>
                                <td class="text-center">{{ $detail->order_amount }}</td>
                                <td class="text-center">{{ $detail->cash_paid }}</td>
                                <td class="text-center">{{ $detail->card_paid }}</td>
                            </tr>
                        @endforeach
                        <tr class="grand_total">
                            <td colspan="6" class="fw-bold rep-font-bold">Total</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalGrossAmt, 3) }}</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalAddons, 3) }}</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalDiscount, 3) }}</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalDeliveryCharge, 3) }}</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalTax, 3) }}</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalAmount, 3) }}</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalCash, 3) }}</td>
                            <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalCard, 3) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot sale_invoice_footer" style="background: #f7f8fa">
            <div class="row">
                <div class="col-lg-12 kt-align-right">
                    <div class="date"><span>Date: </span>{{ date('d-m-Y') }} - <span>User:
                        </span>{{ auth()->user()->name }}</div>
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
    @if ($data['form_file_type'] == 'xls')
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
