@extends('layouts.report')
@section('title', 'POS Closing Report')

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

        /* Session group styling */
        .session-group {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .session-header {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
            font-size: 1.1rem;
            color: #1976d2;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
        }

        .session-header .session-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            flex: 1;
        }

        .session-header .session-stats {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .session-totals {
            background-color: #fff3e0;
            padding: 10px 15px;
            border-radius: 0 0 5px 5px;
            font-weight: 600;
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

        /* Grand total styling */
        .grand-total-row {
            background-color: #e8f5e8 !important;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .grand-total-row td {
            border-top: 3px solid #28a745 !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $data = Session::get('data');
        $gTotalGrossAmt = 0;
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
            </div>
        </div>
        <div class="kt-portlet__body">
            <?php
            $qry = "SELECT
                o.ID,
                o.ORDER_SERIAL,
                o.ORDER_AMOUNT,
                o.TOTAL_TAX_AMOUNT,
                o.DELIVERY_CHARGE,
                o.RESTAURANT_DISCOUNT_AMOUNT,
                o.PAYMENT_STATUS,
                o.ORDER_STATUS,
                o.ORDER_TYPE,
                o.ORDER_TAKEN_BY,
                o.CREATED_AT,
                o.SESSION_ID,
                d.CUSTOMER_NAME,
                d.CAR_NUMBER,
                d.PHONE,
                d.cash_paid,
                d.card_paid,
                COALESCE(SUM(od.price), 0) AS gross_amount
            FROM
                ORDERS o
            LEFT JOIN
                POS_ORDER_ADDITIONAL_DTL d ON d.ORDER_ID = o.ID
            LEFT JOIN
                order_details od ON od.order_id = o.ID
            WHERE
                o.CREATED_AT BETWEEN '{$data['date_time_from']}' AND '{$data['date_time_to']}'
                AND o.SESSION_ID IS NOT NULL
                AND o.SESSION_ID != ''
                AND o.SESSION_ID != '0'
                AND TRIM(o.SESSION_ID) != ''
            GROUP BY
                o.ID,
                o.ORDER_SERIAL,
                o.ORDER_AMOUNT,
                o.TOTAL_TAX_AMOUNT,
                o.DELIVERY_CHARGE,
                o.RESTAURANT_DISCOUNT_AMOUNT,
                o.PAYMENT_STATUS,
                o.ORDER_STATUS,
                o.ORDER_TYPE,
                o.ORDER_TAKEN_BY,
                o.CREATED_AT,
                o.SESSION_ID,
                d.CUSTOMER_NAME,
                d.CAR_NUMBER,
                d.PHONE,
                d.cash_paid,
                d.card_paid
            ORDER BY
                o.SESSION_ID,
                o.CREATED_AT DESC,
                o.ORDER_SERIAL DESC";

            $list = \Illuminate\Support\Facades\DB::select($qry);

            // Group orders by session_id, filtering out invalid sessions
            $groupedOrders = [];
            foreach ($list as $order) {
                $sessionId = $order->session_id;

                // Skip orders with invalid session IDs
                if (is_null($sessionId) || $sessionId === '' || $sessionId === '0' || trim($sessionId) === '') {
                    continue;
                }

                if (!isset($groupedOrders[$sessionId])) {
                    $groupedOrders[$sessionId] = [];
                }
                $groupedOrders[$sessionId][] = $order;
            }

            ?>

            <div class="row row-block">
                <div class="col-lg-12">
                    @if(empty($groupedOrders))
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i> No orders found with valid session IDs for the selected date range.
                        </div>
                    @else
                        @foreach ($groupedOrders as $sessionId => $sessionOrders)
                        @php
                            $sessionTotalGrossAmt = 0;
                            $sessionTotalDiscount = 0;
                            $sessionTotalDeliveryCharge = 0;
                            $sessionTotalTax = 0;
                            $sessionTotalCash = 0;
                            $sessionTotalCard = 0;
                            $sessionTotalAmount = 0;

                            // Session information
                        @endphp

                        <div class="session-group">
                            <div class="session-header">
                                <div class="session-info">
                                    <span>
                                        <i class="fas fa-cash-register"></i>
                                        <strong>Session:</strong> {{ $sessionId }}
                                    </span>
                                </div>
                                <div class="session-stats">
                                    <span>
                                        <i class="fas fa-shopping-cart"></i> {{ count($sessionOrders) }} Orders
                                    </span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table width="100%" class="table table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-left">Order ID</th>
                                            <th class="text-center">Order Date</th>
                                            <th class="text-center">Gross Amount</th>
                                            <th class="text-center">Discount</th>
                                            <th class="text-center">Delivery Charges</th>
                                            <th class="text-center">VAT</th>
                                            <th class="text-center">Net Amount</th>
                                            <th class="text-center">Cash Amount</th>
                                            <th class="text-center">Visa Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sessionOrders as $k => $detail)
                                            @php
                                                $sessionTotalGrossAmt += $detail->gross_amount;
                                                $sessionTotalDiscount += $detail->restaurant_discount_amount;
                                                $sessionTotalDeliveryCharge += $detail->delivery_charge;
                                                $sessionTotalTax += $detail->total_tax_amount;
                                                $sessionTotalCash += $detail->cash_paid;
                                                $sessionTotalCard += $detail->card_paid;
                                                $sessionTotalAmount += $detail->order_amount;

                                                // Add to grand totals
                                                $gTotalGrossAmt += $detail->gross_amount;
                                                $gTotalDiscount += $detail->restaurant_discount_amount;
                                                $gTotalDeliveryCharge += $detail->delivery_charge;
                                                $gTotalTax += $detail->total_tax_amount;
                                                $gTotalCash += $detail->cash_paid;
                                                $gTotalCard += $detail->card_paid;
                                                $gTotalAmount += $detail->order_amount;
                                            @endphp
                                            <tr>
                                                <td class="text-left">{{ $detail->order_serial }}</td>
                                                <td class="text-center">{{ date('d-m-Y H:i', strtotime($detail->created_at)) }}</td>
                                                <td class="text-center">{{ number_format($detail->gross_amount, 3) }}</td>
                                                <td class="text-center">{{ number_format($detail->restaurant_discount_amount, 3) }}</td>
                                                <td class="text-center">{{ number_format($detail->delivery_charge, 3) }}</td>
                                                <td class="text-center">{{ number_format($detail->total_tax_amount, 3) }}</td>
                                                <td class="text-center">{{ number_format($detail->order_amount, 3) }}</td>
                                                <td class="text-center">{{ number_format($detail->cash_paid, 3) }}</td>
                                                <td class="text-center">{{ number_format($detail->card_paid, 3) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>


                            <!-- Session Totals -->
                            <div class="session-totals">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3"><i class="fas fa-calculator"></i> Session Sales Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span><strong>Session Total:</strong></span>
                                            <div class="d-flex">
                                                <span class="mr-4"><strong>Gross:</strong> {{ number_format($sessionTotalGrossAmt, 3) }}</span>
                                                <span class="mr-4"><strong>Discount:</strong> {{ number_format($sessionTotalDiscount, 3) }}</span>
                                                <span class="mr-4"><strong>Delivery:</strong> {{ number_format($sessionTotalDeliveryCharge, 3) }}</span>
                                                <span class="mr-4"><strong>VAT:</strong> {{ number_format($sessionTotalTax, 3) }}</span>
                                                <span class="mr-4"><strong>Net:</strong> {{ number_format($sessionTotalAmount, 3) }}</span>
                                                <span class="mr-4"><strong>Cash:</strong> {{ number_format($sessionTotalCash, 3) }}</span>
                                                <span><strong>Card:</strong> {{ number_format($sessionTotalCard, 3) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                        <!-- Grand Total Section -->
                        <div class="mt-4">
                            <table width="100%" class="table table-bordered">
                                <tr class="grand-total-row">
                                    <td colspan="2" class="fw-bold rep-font-bold text-center">
                                        <i class="fas fa-calculator"></i> GRAND TOTAL (All Sessions)
                                    </td>
                                    <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalGrossAmt, 3) }}</td>
                                    <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalDiscount, 3) }}</td>
                                    <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalDeliveryCharge, 3) }}</td>
                                    <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalTax, 3) }}</td>
                                    <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalAmount, 3) }}</td>
                                    <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalCash, 3) }}</td>
                                    <td class="text-center fw-bold rep-font-bold">{{ number_format($gTotalCard, 3) }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif
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
<script>
$(document).ready(function() {
    // No row click functionality needed for this report
    console.log('POS Closing Report loaded successfully');
});
</script>
@endsection

@section('exportXls')
    @if ($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                // Export all tables including session groups
                var tables = document.querySelectorAll('table');
                tables.forEach(function(table, index) {
                    if (index === 0) return; // Skip the first table (grand total)

                    $(table).table2excel({
                        filename: "pos_closing_report_session_" + (index) + ".xls",
                    });
                });
            });
        </script>
    @endif
@endsection
