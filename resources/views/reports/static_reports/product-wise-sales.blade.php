@extends('layouts.report')
@section('title', 'Product Wise Sales Report')

@section('pageCSS')
    <style>
        @media print {
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            tfoot > tr > td {
                padding: 0 !important;
            }

            body {
                margin: 0;
            }
        }

        .report-group {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 25px;
            background-color: #ffffff;
        }

        .report-header {
            background-color: #f0f4ff;
            border-radius: 6px 6px 0 0;
            padding: 18px 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 15px;
            align-items: center;
        }

        .report-header .report-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-weight: 600;
            color: #1d4ed8;
        }

        .report-header .report-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            color: #0c51a1;
            font-weight: 500;
        }

        .report-summary {
            background-color: #fff3e0;
            padding: 12px 18px;
            font-weight: 600;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table thead th {
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            background-color: #e8f0ff;
        }

        .subtotal-row {
            background-color: #f1f3f5;
            font-weight: 600;
        }

        .grand-total-row {
            background-color: #e8f5e8 !important;
            font-weight: bold;
            font-size: 1.05rem;
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
        $grandTotals = [
            'qty' => 0.0,
            'amount' => 0.0,
            'discount' => 0.0,
            'net' => 0.0,
        ];
        $groupedSessions = [];
        $dateTimeFrom = $data['date_time_from'] ?? now()->startOfDay()->toDateTimeString();
        $dateTimeTo = $data['date_time_to'] ?? now()->endOfDay()->toDateTimeString();
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{ strtoupper($data['page_title'] ?? 'PRODUCT WISE SALES REPORT') }}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date From:</span>
                    <span style="color: #5578eb;">{{ ' ' . date('d-m-Y', strtotime($dateTimeFrom)) }}</span> -
                    <span style="color: #e27d00;">Date To:</span>
                    <span style="color: #5578eb;">{{ ' ' . date('d-m-Y', strtotime($dateTimeTo)) }}</span>
                </h6>
            </div>
        </div>
        <div class="kt-portlet__body">
            <?php
            $branchFilter = '';
            $rawBranchIds = $data['branch_ids'] ?? [];

            if (is_string($rawBranchIds)) {
                $decodedBranchIds = json_decode($rawBranchIds, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $rawBranchIds = $decodedBranchIds;
                }
            }

            if (is_array($rawBranchIds) && ! empty($rawBranchIds)) {
                $sanitizedBranchIds = array_values(
                    array_filter(
                        array_map(function ($value) {
                            if (is_numeric($value)) {
                                return (int) $value;
                            }
                            return null;
                        }, $rawBranchIds),
                        function ($value) {
                            return ! is_null($value);
                        }
                    )
                );

                if (! empty($sanitizedBranchIds)) {
                    $branchList = implode(',', $sanitizedBranchIds);
                    $branchFilter = ' AND bu.restaurant_id IN (' . $branchList . ') AND o.RESTAURANT_ID IN (' . $branchList . ')';
                }
            }

            $query = "
                WITH base_usage AS (
                    SELECT DISTINCT
                        oru.order_detail_id,
                        oru.order_id,
                        oru.option_list_id,
                        oru.food_recipe_id,
                        oru.restaurant_id,
                        CAST(oru.usage_date AS DATE) AS usage_date
                    FROM order_recipe_usages oru
                    WHERE oru.usage_date BETWEEN '{$dateTimeFrom}' AND '{$dateTimeTo}'
                )
                SELECT
                    bu.usage_date AS session_date,
                    COALESCE(ol.name, fr.food_name, 'N/A') AS option_list_name,
                    COALESCE(f.name, fr.food_name, ol.name, 'N/A') AS food_name,
                    SUM(COALESCE(od.quantity, 0)) AS total_qty,
                    SUM(COALESCE(od.price, 0) * COALESCE(od.quantity, 0)) AS total_amount,
                    SUM(COALESCE(od.discount_on_food, 0)) AS total_discount,
                    SUM(
                        (COALESCE(od.price, 0) * COALESCE(od.quantity, 0))
                        - COALESCE(od.discount_on_food, 0)
                        + COALESCE(od.total_add_on_price, 0)
                    ) AS total_net_amount
                FROM base_usage bu
                JOIN order_details od ON od.id = bu.order_detail_id
                JOIN orders o ON o.id = bu.order_id
                LEFT JOIN food_recipes fr ON fr.id = bu.food_recipe_id
                LEFT JOIN options_list ol ON ol.id = bu.option_list_id
                LEFT JOIN food f ON f.id = od.food_id
                WHERE o.CREATED_AT BETWEEN '{$dateTimeFrom}' AND '{$dateTimeTo}'
                    {$branchFilter}
                GROUP BY
                    bu.usage_date,
                    COALESCE(ol.name, fr.food_name, 'N/A'),
                    COALESCE(f.name, fr.food_name, ol.name, 'N/A')
                ORDER BY
                    bu.usage_date ASC,
                    COALESCE(ol.name, fr.food_name, 'N/A') ASC,
                    COALESCE(f.name, fr.food_name, ol.name, 'N/A') ASC
            ";

            $summaryRows = \Illuminate\Support\Facades\DB::select($query);

            echo '<!-- DEBUG: Product wise sales query returned ' . count($summaryRows) . ' rows -->';
            ?>
            @php
                foreach ($summaryRows as $row) {
                    $dateValue = $row->session_date ?? null;
                    $dateKey = 'N/A';
                    $displayDate = 'N/A';

                    if ($dateValue instanceof \DateTimeInterface) {
                        $dateKey = $dateValue->format('Y-m-d');
                        $displayDate = $dateValue->format('d-m-Y');
                    } else {
                        $timestamp = strtotime($dateValue);
                        if ($timestamp !== false) {
                            $dateKey = date('Y-m-d', $timestamp);
                            $displayDate = date('d-m-Y', $timestamp);
                        }
                    }

                    if (! isset($groupedSessions[$dateKey])) {
                        $groupedSessions[$dateKey] = [
                            'display_date' => $displayDate,
                            'rows' => [],
                            'totals' => [
                                'qty' => 0.0,
                                'amount' => 0.0,
                                'discount' => 0.0,
                                'net' => 0.0,
                            ],
                        ];
                    }

                    $rowQty = (float) ($row->total_qty ?? 0);
                    $rowAmount = (float) ($row->total_amount ?? 0);
                    $rowDiscount = (float) ($row->total_discount ?? 0);
                    $rowNet = (float) ($row->total_net_amount ?? 0);

                    $groupedSessions[$dateKey]['rows'][] = $row;
                    $groupedSessions[$dateKey]['totals']['qty'] += $rowQty;
                    $groupedSessions[$dateKey]['totals']['amount'] += $rowAmount;
                    $groupedSessions[$dateKey]['totals']['discount'] += $rowDiscount;
                    $groupedSessions[$dateKey]['totals']['net'] += $rowNet;

                    $grandTotals['qty'] += $rowQty;
                    $grandTotals['amount'] += $rowAmount;
                    $grandTotals['discount'] += $rowDiscount;
                    $grandTotals['net'] += $rowNet;
                }

                ksort($groupedSessions);
            @endphp

            <div class="row row-block">
                <div class="col-lg-12">
                    @if (empty($groupedSessions))
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i> No product wise sales data found for the selected filters.
                        </div>
                    @else
                        @foreach ($groupedSessions as $sessionKey => $sessionData)
                            @php
                                $rows = $sessionData['rows'];
                                $sessionTotals = $sessionData['totals'];
                                $optionCount = count(array_unique(array_map(function ($item) {
                                    return $item->option_list_name;
                                }, $rows)));
                                $foodCount = count($rows);
                            @endphp
                            <div class="report-group">
                                <div class="report-header">
                                    <div class="report-info">
                                        <span><i class="fas fa-calendar-day"></i> Session Date: {{ $sessionData['display_date'] }}</span>
                                    </div>
                                    <div class="report-stats">
                                        <span><i class="fas fa-th-list"></i> Options: {{ $optionCount }}</span>
                                        <span><i class="fas fa-pizza-slice"></i> Items: {{ $foodCount }}</span>
                                    </div>
                                </div>
                                <div class="report-summary">
                                    <span>Total Qty: {{ number_format($sessionTotals['qty'], 3) }}</span>
                                    <span>Total Amount: {{ number_format($sessionTotals['amount'], 3) }}</span>
                                    <span>Total Discount: {{ number_format($sessionTotals['discount'], 3) }}</span>
                                    <span>Net Amount: {{ number_format($sessionTotals['net'], 3) }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table width="100%" class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Option List Name</th>
                                                <th class="text-left">Food Name</th>
                                                <th class="text-center">Option List Qty</th>
                                                <th class="text-center">Amount</th>
                                                <th class="text-center">Discount</th>
                                                <th class="text-center">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $row)
                                                @php
                                                    $rowQty = (float) ($row->total_qty ?? 0);
                                                    $rowAmount = (float) ($row->total_amount ?? 0);
                                                    $rowDiscount = (float) ($row->total_discount ?? 0);
                                                    $rowNet = (float) ($row->total_net_amount ?? 0);
                                                @endphp
                                                <tr>
                                                    <td class="text-left">{{ $row->option_list_name }}</td>
                                                    <td class="text-left">{{ $row->food_name }}</td>
                                                    <td class="text-center">{{ number_format($rowQty, 3) }}</td>
                                                    <td class="text-center">{{ number_format($rowAmount, 3) }}</td>
                                                    <td class="text-center">{{ number_format($rowDiscount, 3) }}</td>
                                                    <td class="text-center">{{ number_format($rowNet, 3) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="subtotal-row">
                                                <td colspan="2" class="text-right"><strong>Subtotal ({{ $sessionData['display_date'] }})</strong></td>
                                                <td class="text-center">{{ number_format($sessionTotals['qty'], 3) }}</td>
                                                <td class="text-center">{{ number_format($sessionTotals['amount'], 3) }}</td>
                                                <td class="text-center">{{ number_format($sessionTotals['discount'], 3) }}</td>
                                                <td class="text-center">{{ number_format($sessionTotals['net'], 3) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <table width="100%" class="table table-bordered">
                                <tr class="grand-total-row">
                                    <td colspan="2" class="text-center">
                                        <i class="fas fa-calculator"></i> GRAND TOTAL (All Session Dates)
                                    </td>
                                    <td class="text-center">{{ number_format($grandTotals['qty'], 3) }}</td>
                                    <td class="text-center">{{ number_format($grandTotals['amount'], 3) }}</td>
                                    <td class="text-center">{{ number_format($grandTotals['discount'], 3) }}</td>
                                    <td class="text-center">{{ number_format($grandTotals['net'], 3) }}</td>
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
                    <div class="date">
                        <span>Date: </span>{{ date('d-m-Y') }} -
                        <span>User: </span>{{ auth()->user()->name }}
                    </div>
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
            console.log('Product Wise Sales Report loaded successfully');
        });
    </script>
@endsection

@section('exportXls')
    @if (($data['form_file_type'] ?? null) === 'xls')
        <script>
            $(document).ready(function() {
                var tables = document.querySelectorAll('table');
                tables.forEach(function(table, index) {
                    if (index === 0) {
                        return;
                    }

                    $(table).table2excel({
                        filename: "product_wise_sales_" + index + ".xls",
                    });
                });
            });
        </script>
    @endif
@endsection

