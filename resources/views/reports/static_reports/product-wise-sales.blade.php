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
        $reportError = null;
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
                    $branchFilter = ' AND o.RESTAURANT_ID IN (' . $branchList . ')';
                }
            }

            try {
                $query = "
                    SELECT
                        od.id AS order_detail_id,
                        od.order_id,
                        od.food_id,
                        od.quantity,
                        od.price,
                        od.discount_on_food,
                        od.total_add_on_price,
                        od.variation,
                        o.created_at,
                        o.restaurant_id
                    FROM order_details od
                    JOIN orders o ON o.id = od.order_id
                    WHERE o.CREATED_AT BETWEEN '{$dateTimeFrom}' AND '{$dateTimeTo}'
                        {$branchFilter}
                ";

                $detailRows = \Illuminate\Support\Facades\DB::select($query);
                echo '<!-- DEBUG: Product wise sales retrieved ' . count($detailRows) . ' order detail rows -->';

                $decodeVariation = static function ($payload) {
                    if (empty($payload)) {
                        return [];
                    }

                    if (is_string($payload)) {
                        $payload = html_entity_decode($payload, ENT_QUOTES | ENT_HTML5);
                        $payload = stripslashes($payload);
                    }

                    if (is_array($payload)) {
                        return $payload;
                    }

                    if (! is_string($payload)) {
                        return [];
                    }

                    $payload = trim($payload);

                    if ($payload === '' || $payload === 'null') {
                        return [];
                    }

                    $decoded = json_decode($payload, true);

                    if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                        return [];
                    }

                    if (isset($decoded['variations']) && is_array($decoded['variations'])) {
                        return $decoded['variations'];
                    }

                    if (isset($decoded['variation']) && is_array($decoded['variation'])) {
                        return $decoded['variation'];
                    }

                    if (isset($decoded['data']) && is_array($decoded['data'])) {
                        return $decoded['data'];
                    }

                    return $decoded;
                };

                $normalizeVariationEntry = static function ($variation) {
                    if (is_string($variation)) {
                        $decodedEntry = json_decode($variation, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedEntry)) {
                            $variation = $decodedEntry;
                        }
                    }

                    if (! is_array($variation)) {
                        return [];
                    }

                    $ids = [];

                    if (! empty($variation['option_list_id'])) {
                        $ids[] = (int) $variation['option_list_id'];
                    }

                    if (! empty($variation['options_list_id'])) {
                        $ids[] = (int) $variation['options_list_id'];
                    }

                    if (! empty($variation['values']) && is_array($variation['values'])) {
                        foreach ($variation['values'] as $value) {
                            if (! is_array($value)) {
                                continue;
                            }

                            if (! empty($value['options_list_id'])) {
                                $ids[] = (int) $value['options_list_id'];
                            } elseif (! empty($value['option_list_id'])) {
                                $ids[] = (int) $value['option_list_id'];
                            } else {
                                $nested = json_decode(is_string($value) ? $value : json_encode($value), true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($nested)) {
                                    if (! empty($nested['option_list_id'])) {
                                        $ids[] = (int) $nested['option_list_id'];
                                    }
                                    if (! empty($nested['options_list_id'])) {
                                        $ids[] = (int) $nested['options_list_id'];
                                    }
                                }
                            }
                        }
                    }

                    if (! empty($variation['options']) && is_array($variation['options'])) {
                        foreach ($variation['options'] as $option) {
                            if (is_array($option)) {
                                if (! empty($option['option_list_id'])) {
                                    $ids[] = (int) $option['option_list_id'];
                                }
                                if (! empty($option['options_list_id'])) {
                                    $ids[] = (int) $option['options_list_id'];
                                }
                            }
                        }
                    }

                    if (! empty($variation['variation_options']) && is_array($variation['variation_options'])) {
                        foreach ($variation['variation_options'] as $option) {
                            if (! empty($option['option_list_id'])) {
                                $ids[] = (int) $option['option_list_id'];
                            }
                            if (! empty($option['options_list_id'])) {
                                $ids[] = (int) $option['options_list_id'];
                            }
                        }
                    }

                    return array_values(array_unique(array_filter($ids)));
                };

                $extractOptionListIds = static function ($variation, $normalizeEntry) {
                    $normalizedIds = [];

                    if (is_array($variation) && array_keys($variation) !== range(0, count($variation) - 1)) {
                        $variation = [$variation];
                    }

                    foreach ((array) $variation as $entry) {
                        $ids = $normalizeEntry($entry);
                        if (! empty($ids)) {
                            $normalizedIds = array_merge($normalizedIds, $ids);
                        }
                    }

                    return array_values(array_unique(array_filter($normalizedIds)));
                };

                $aggregated = [];
                $optionIds = [];
                $foodIds = [];

                foreach ($detailRows as $row) {
                    $sessionDate = $row->created_at ? date('Y-m-d', strtotime($row->created_at)) : null;
                    if (! $sessionDate) {
                        continue;
                    }

                    $variations = $decodeVariation($row->variation);
                    if (empty($variations) && ! empty($row->food_id)) {
                        $variations = [];
                    }

                    $resolvedOptionIds = $extractOptionListIds($variations, $normalizeVariationEntry);

                    if (empty($resolvedOptionIds)) {
                        continue;
                    }

                    $foodId = (int) ($row->food_id ?? 0);
                    if ($foodId <= 0) {
                        continue;
                    }

                    $quantity = (float) ($row->quantity ?? 0);
                    $grossAmount = (float) ($row->price ?? 0) * $quantity;
                    $discount = (float) ($row->discount_on_food ?? 0);
                    $addons = (float) ($row->total_add_on_price ?? 0);
                    $netAmount = $grossAmount - $discount + $addons;

                    $optionCount = count($resolvedOptionIds);
                    if ($optionCount === 0) {
                        continue;
                    }

                    $splitQuantity = $quantity / $optionCount;
                    $splitGross = $grossAmount / $optionCount;
                    $splitDiscount = $discount / $optionCount;
                    $splitNet = $netAmount / $optionCount;

                    foreach ($resolvedOptionIds as $optionId) {
                        $aggregateKey = $sessionDate . '|' . $optionId . '|' . $foodId;

                        if (! isset($aggregated[$aggregateKey])) {
                            $aggregated[$aggregateKey] = [
                                'session_date' => $sessionDate,
                                'option_list_id' => $optionId,
                                'food_id' => $foodId,
                                'qty' => 0.0,
                                'amount' => 0.0,
                                'discount' => 0.0,
                                'net' => 0.0,
                            ];
                        }

                        $aggregated[$aggregateKey]['qty'] += $splitQuantity;
                        $aggregated[$aggregateKey]['amount'] += $splitGross;
                        $aggregated[$aggregateKey]['discount'] += $splitDiscount;
                        $aggregated[$aggregateKey]['net'] += $splitNet;

                        $optionIds[$optionId] = true;
                        $foodIds[$foodId] = true;
                    }
                }

                if (empty($aggregated)) {
                    echo '<!-- DEBUG: No aggregated records generated -->';
                    if (empty($detailRows)) {
                        $reportError = 'No order details found for the supplied filters.';
                    } else {
                        $reportError = 'Order details retrieved but no option list identifiers were resolved from variations.';
                    }
                }

                $optionNames = [];
                if (! empty($optionIds)) {
                    $optionNames = \Illuminate\Support\Facades\DB::table('options_list')
                        ->whereIn('id', array_keys($optionIds))
                        ->pluck('name', 'id')
                        ->map(function ($name) {
                            return $name ?: 'N/A';
                        })
                        ->toArray();
                }

                $foodNames = [];
                if (! empty($foodIds)) {
                    $foodNames = \Illuminate\Support\Facades\DB::table('food')
                        ->whereIn('id', array_keys($foodIds))
                        ->pluck('name', 'id')
                        ->map(function ($name) {
                            return $name ?: 'N/A';
                        })
                        ->toArray();
                }

                foreach ($aggregated as $entry) {
                    $dateKey = $entry['session_date'];
                    $displayDate = date('d-m-Y', strtotime($entry['session_date']));
                    $optionId = $entry['option_list_id'];
                    $foodId = $entry['food_id'];

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

                    $row = (object) [
                        'option_list_name' => $optionNames[$optionId] ?? 'N/A',
                        'food_name' => $foodNames[$foodId] ?? 'N/A',
                        'total_qty' => $entry['qty'],
                        'total_amount' => $entry['amount'],
                        'total_discount' => $entry['discount'],
                        'total_net_amount' => $entry['net'],
                    ];

                    $groupedSessions[$dateKey]['rows'][] = $row;
                    $groupedSessions[$dateKey]['totals']['qty'] += $entry['qty'];
                    $groupedSessions[$dateKey]['totals']['amount'] += $entry['amount'];
                    $groupedSessions[$dateKey]['totals']['discount'] += $entry['discount'];
                    $groupedSessions[$dateKey]['totals']['net'] += $entry['net'];

                    $grandTotals['qty'] += $entry['qty'];
                    $grandTotals['amount'] += $entry['amount'];
                    $grandTotals['discount'] += $entry['discount'];
                    $grandTotals['net'] += $entry['net'];
                }

                ksort($groupedSessions);
            } catch (\Throwable $exception) {
                $reportError = $exception->getMessage();
                echo '<!-- DEBUG: Product wise sales processing failed with message: ' . e($reportError) . ' -->';
            }
            ?>

            <div class="row row-block">
                <div class="col-lg-12">
            @if ($reportError)
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-circle"></i> {{ $reportError }}
                </div>
            @elseif (empty($groupedSessions))
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

