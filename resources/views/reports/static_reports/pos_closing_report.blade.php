@extends('layouts.report')
@section('title', 'POS Closing Report')

@section('pageCSS')
    <style>
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

        .payment-group {
            border: 1px solid #cce5ff;
            border-radius: 6px;
            background-color: #f8fbff;
            margin-bottom: 25px;
        }
        .payment-group--unassigned {
            border-color: #f8d7da;
            background-color: #fff5f5;
        }

        .payment-header {
            background-color: #dfefff;
            padding: 15px 20px;
            border-radius: 6px 6px 0 0;
        .payment-group--unassigned .payment-header {
            background-color: #fde2e4;
            color: #842029;
        }
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 15px;
            align-items: center;
        }

        .payment-header .payment-info {
            font-weight: 600;
            color: #0c51a1;
            display: flex;
            flex-direction: column;
        }

        .payment-header .payment-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #0c51a1;
            font-weight: 500;
        }

        .payment-group .session-group {
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

        .session-header .session-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-weight: 500;
        }

        .session-header .session-summary span {
            font-size: 0.9rem;
            color: #0d47a1;
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

        .subtotal-row {
            background-color: #f1f3f5;
            font-weight: 600;
        }

        .payment-group {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 30px;
            background-color: #ffffff;
        }

        .payment-header {
            background-color: #f0f4ff;
            border-radius: 6px 6px 0 0;
            padding: 18px 20px;
            font-weight: 700;
            font-size: 1.05rem;
            color: #1d4ed8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table th {
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
        }

        .grand-total-row {
            background-color: #e8f5e8 !important;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .grand-total-row td {
            border-top: 3px solid #28a745 !important;
        }

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
                o.ORDER_DATE,
                o.TOTAL_TAX_AMOUNT,
                o.DELIVERY_CHARGE,
                o.RESTAURANT_DISCOUNT_AMOUNT,
                o.PAYMENT_STATUS,
                o.ORDER_STATUS,
                o.ORDER_TYPE,
                o.ORDER_TAKEN_BY,
                o.PAYMENT_USER_ID AS payment_user_id,
                o.CREATED_AT,
                o.SESSION_ID,
                d.CUSTOMER_NAME,
                d.CAR_NUMBER,
                d.PHONE,
                d.cash_paid,
                d.card_paid,
                COALESCE(SUM(od.price), 0) AS gross_amount,
                order_taker.name AS order_taker_name,
                payment_user.name AS payment_user_name
            FROM
                ORDERS o
            LEFT JOIN
                POS_ORDER_ADDITIONAL_DTL d ON d.ORDER_ID = o.ID
            LEFT JOIN
                order_details od ON od.order_id = o.ID
            LEFT JOIN
                users order_taker ON order_taker.id = o.ORDER_TAKEN_BY
            LEFT JOIN
                users payment_user ON payment_user.id = o.PAYMENT_USER_ID
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
                o.ORDER_DATE,
                o.TOTAL_TAX_AMOUNT,
                o.DELIVERY_CHARGE,
                o.RESTAURANT_DISCOUNT_AMOUNT,
                o.PAYMENT_STATUS,
                o.ORDER_STATUS,
                o.ORDER_TYPE,
                o.ORDER_TAKEN_BY,
                o.PAYMENT_USER_ID,
                o.CREATED_AT,
                o.SESSION_ID,
                d.CUSTOMER_NAME,
                d.CAR_NUMBER,
                d.PHONE,
                d.cash_paid,
                d.card_paid,
                order_taker.name,
                payment_user.name
            ORDER BY
                o.PAYMENT_USER_ID,
                o.SESSION_ID,
                o.CREATED_AT DESC,
                o.ORDER_SERIAL DESC";

            $list = \Illuminate\Support\Facades\DB::select($qry);

            $groupedOrders = [];
            foreach ($list as $order) {
                $sessionId = $order->session_id;

                if (is_null($sessionId) || $sessionId === '' || $sessionId === '0' || (is_string($sessionId) && trim($sessionId) === '')) {
                    continue;
                }

                $paymentUserId = $order->payment_user_id ?? null;
                $paymentKey = $paymentUserId;

                if (is_null($paymentKey) || $paymentKey === '' || $paymentKey === '0' || (is_string($paymentKey) && trim($paymentKey) === '')) {
                    $paymentKey = 'unassigned';
                }

                if (!isset($groupedOrders[$paymentKey])) {
                    $groupedOrders[$paymentKey] = [
                        'payment_user_id' => $paymentUserId,
                        'payment_user_name' => $order->payment_user_name,
                        'is_unassigned' => $paymentKey === 'unassigned',
                        'sessions' => [],
                    ];
                }

                if (!isset($groupedOrders[$paymentKey]['sessions'][$sessionId])) {
                    $groupedOrders[$paymentKey]['sessions'][$sessionId] = [];
                }

                $groupedOrders[$paymentKey]['sessions'][$sessionId][] = $order;
            }

            ?>

            <div class="row row-block">
                <div class="col-lg-12">
                    @if(empty($groupedOrders))
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i> No orders found with valid session IDs for the selected date range.
                        </div>
                    @else
                        @foreach ($groupedOrders as $paymentKey => $paymentGroup)
                            @php
                                $paymentUserId = $paymentGroup['payment_user_id'] ?? null;
                                $paymentUserName = $paymentGroup['payment_user_name'] ?? null;
                                $paymentUserLabel = $paymentUserName ?: ($paymentUserId ? 'User #' . $paymentUserId : 'Unassigned');
                                $paymentUserIdLabel = $paymentUserId ?: 'N/A';
                                $paymentSessionCount = count($paymentGroup['sessions']);
                                $paymentOrderCount = 0;
                                foreach ($paymentGroup['sessions'] as $ordersWithinPayment) {
                                    $paymentOrderCount += count($ordersWithinPayment);
                                }
                            @endphp

                            <div class="payment-group {{ $paymentGroup['is_unassigned'] ? 'payment-group--unassigned' : '' }}">
                                <div class="payment-header">
                                    <div class="payment-info">
                                        <span><i class="fas fa-user-check"></i> Payment {{ $paymentGroup['is_unassigned'] ? 'User: Unassigned' : 'User: ' . $paymentUserLabel }}</span>
                                        <span><small>ID: {{ $paymentGroup['is_unassigned'] ? 'N/A' : $paymentUserIdLabel }}</small></span>
                                    </div>
                                    <div class="payment-stats">
                                        <span><i class="fas fa-layer-group"></i> Sessions: {{ $paymentSessionCount }}</span>
                                        <span><i class="fas fa-receipt"></i> Orders: {{ $paymentOrderCount }}</span>
                                    </div>
                                </div>

                                @foreach ($paymentGroup['sessions'] as $sessionId => $sessionOrders)
                                    @php
                                        $sessionOrderCount = count($sessionOrders);
                                        $sessionSummaryGross = 0;
                                        $sessionSummaryDiscount = 0;
                                        $sessionSummaryDelivery = 0;
                                        $sessionSummaryTax = 0;
                                        $sessionSummaryNet = 0;
                                        $sessionSummaryCash = 0;
                                        $sessionSummaryCard = 0;

                                        foreach ($sessionOrders as $summaryDetail) {
                                            $summaryIsCanceled = strtolower($summaryDetail->order_status ?? '') === 'canceled';
                                            if ($summaryIsCanceled) {
                                                continue;
                                            }

                                            $sessionSummaryGross += $summaryDetail->gross_amount;
                                            $sessionSummaryDiscount += $summaryDetail->restaurant_discount_amount;
                                            $sessionSummaryDelivery += $summaryDetail->delivery_charge;
                                            $sessionSummaryTax += $summaryDetail->total_tax_amount;
                                            $sessionSummaryNet += $summaryDetail->order_amount;
                                            $sessionSummaryCash += $summaryDetail->cash_paid;
                                            $sessionSummaryCard += $summaryDetail->card_paid;
                                        }
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
                                                    <i class="fas fa-shopping-cart"></i> {{ $sessionOrderCount }} Orders
                                                </span>
                                            </div>
                                            <div class="session-summary">
                                                <span>Gross: {{ number_format($sessionSummaryGross, 3) }}</span>
                                                <span>Discount: {{ number_format($sessionSummaryDiscount, 3) }}</span>
                                                <span>Delivery: {{ number_format($sessionSummaryDelivery, 3) }}</span>
                                                <span>VAT: {{ number_format($sessionSummaryTax, 3) }}</span>
                                                <span>Net: {{ number_format($sessionSummaryNet, 3) }}</span>
                                                <span>Cash: {{ number_format($sessionSummaryCash, 3) }}</span>
                                                <span>Card: {{ number_format($sessionSummaryCard, 3) }}</span>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table width="100%" class="table table-bordered mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="text-left">Order ID</th>
                                                        <th class="text-center">Order Date</th>
                                                        <th class="text-center">Created At</th>
                                                        <th class="text-center">Order By</th>
                                                        <th class="text-center">Order Status</th>
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
                                                    @php
                                                        $currentOrderDateKey = null;
                                                        $currentOrderDateLabel = '';
                                                        $dateSubtotalGross = 0;
                                                        $dateSubtotalDiscount = 0;
                                                        $dateSubtotalDelivery = 0;
                                                        $dateSubtotalTax = 0;
                                                        $dateSubtotalNet = 0;
                                                        $dateSubtotalCash = 0;
                                                        $dateSubtotalCard = 0;
                                                    @endphp
                                                    @foreach ($sessionOrders as $k => $detail)
                                                        @php
                                                            $isCanceled = strtolower($detail->order_status ?? '') === 'canceled';
                                                            $orderDateTime = $detail->order_date ? strtotime($detail->order_date) : null;
                                                            $orderDateKey = $orderDateTime ? date('Y-m-d', $orderDateTime) : 'unknown';
                                                            $orderDateLabel = $orderDateTime ? date('d-m-Y', $orderDateTime) : 'N/A';
                                                            $createdAtLabel = $detail->created_at ? date('d-m-Y H:i', strtotime($detail->created_at)) : 'N/A';

                                                            if ($currentOrderDateKey !== null && $currentOrderDateKey !== $orderDateKey) {
                                                                echo '<tr class="table-secondary subtotal-row">';
                                                                echo '<td colspan="5" class="text-right"><strong>Subtotal (' . $currentOrderDateLabel . ')</strong></td>';
                                                                echo '<td class="text-center">' . number_format($dateSubtotalGross, 3) . '</td>';
                                                                echo '<td class="text-center">' . number_format($dateSubtotalDiscount, 3) . '</td>';
                                                                echo '<td class="text-center">' . number_format($dateSubtotalDelivery, 3) . '</td>';
                                                                echo '<td class="text-center">' . number_format($dateSubtotalTax, 3) . '</td>';
                                                                echo '<td class="text-center">' . number_format($dateSubtotalNet, 3) . '</td>';
                                                                echo '<td class="text-center">' . number_format($dateSubtotalCash, 3) . '</td>';
                                                                echo '<td class="text-center">' . number_format($dateSubtotalCard, 3) . '</td>';
                                                                echo '</tr>';

                                                                $dateSubtotalGross = 0;
                                                                $dateSubtotalDiscount = 0;
                                                                $dateSubtotalDelivery = 0;
                                                                $dateSubtotalTax = 0;
                                                                $dateSubtotalNet = 0;
                                                                $dateSubtotalCash = 0;
                                                                $dateSubtotalCard = 0;
                                                            }

                                                            if ($currentOrderDateKey !== $orderDateKey) {
                                                                $currentOrderDateKey = $orderDateKey;
                                                                $currentOrderDateLabel = $orderDateLabel;
                                                            }

                                                            if (! $isCanceled) {
                                                                $dateSubtotalGross += $detail->gross_amount;
                                                                $dateSubtotalDiscount += $detail->restaurant_discount_amount;
                                                                $dateSubtotalDelivery += $detail->delivery_charge;
                                                                $dateSubtotalTax += $detail->total_tax_amount;
                                                                $dateSubtotalNet += $detail->order_amount;
                                                                $dateSubtotalCash += $detail->cash_paid;
                                                                $dateSubtotalCard += $detail->card_paid;

                                                                $gTotalGrossAmt += $detail->gross_amount;
                                                                $gTotalDiscount += $detail->restaurant_discount_amount;
                                                                $gTotalDeliveryCharge += $detail->delivery_charge;
                                                                $gTotalTax += $detail->total_tax_amount;
                                                                $gTotalCash += $detail->cash_paid;
                                                                $gTotalCard += $detail->card_paid;
                                                                $gTotalAmount += $detail->order_amount;
                                                            }
                                                        @endphp
                                                        <tr class="{{ $isCanceled ? 'table-danger' : '' }}">
                                                            <td class="text-left">{{ $detail->order_serial }}</td>
                                                            <td class="text-center">{{ $orderDateLabel }}</td>
                                                            <td class="text-center">{{ $createdAtLabel }}</td>
                                                            <td class="text-center">{{ $detail->order_taker_name ?? 'N/A' }}</td>
                                                            <td class="text-center">{{ $detail->order_status }}</td>
                                                            <td class="text-center">{{ number_format($detail->gross_amount, 3) }}</td>
                                                            <td class="text-center">{{ number_format($detail->restaurant_discount_amount, 3) }}</td>
                                                            <td class="text-center">{{ number_format($detail->delivery_charge, 3) }}</td>
                                                            <td class="text-center">{{ number_format($detail->total_tax_amount, 3) }}</td>
                                                            <td class="text-center">{{ number_format($detail->order_amount, 3) }}</td>
                                                            <td class="text-center">{{ number_format($detail->cash_paid, 3) }}</td>
                                                            <td class="text-center">{{ number_format($detail->card_paid, 3) }}</td>
                                                        </tr>
                                                    @endforeach
                                                    @if ($currentOrderDateKey !== null)
                                                        <tr class="table-secondary subtotal-row">
                                                            <td colspan="5" class="text-right"><strong>Subtotal ({{ $currentOrderDateLabel }})</strong></td>
                                                            <td class="text-center">{{ number_format($dateSubtotalGross, 3) }}</td>
                                                            <td class="text-center">{{ number_format($dateSubtotalDiscount, 3) }}</td>
                                                            <td class="text-center">{{ number_format($dateSubtotalDelivery, 3) }}</td>
                                                            <td class="text-center">{{ number_format($dateSubtotalTax, 3) }}</td>
                                                            <td class="text-center">{{ number_format($dateSubtotalNet, 3) }}</td>
                                                            <td class="text-center">{{ number_format($dateSubtotalCash, 3) }}</td>
                                                            <td class="text-center">{{ number_format($dateSubtotalCard, 3) }}</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <table width="100%" class="table table-bordered">
                                <tr class="grand-total-row">
                                    <td colspan="5" class="fw-bold rep-font-bold text-center">
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
    console.log('POS Closing Report loaded successfully');
});
</script>
@endsection

@section('exportXls')
    @if ($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                var tables = document.querySelectorAll('table');
                tables.forEach(function(table, index) {
                    if (index === 0) return;

                    $(table).table2excel({
                        filename: "pos_closing_report_session_" + (index) + ".xls",
                    });
                });
            });
        </script>
    @endif
@endsection
