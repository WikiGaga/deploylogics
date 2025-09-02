@extends('layouts.report')
@section('title', 'Sale Orders Report')

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

                /* Professional Modal Styling */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border-radius: 12px 12px 0 0;
            border-bottom: none;
            padding: 20px 25px;
        }

        .modal-title {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .modal-body {
            padding: 25px;
            background-color: #f8f9fa;
        }

        .modal-footer {
            background-color: #ffffff;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 12px 12px;
            padding: 15px 25px;
        }

        /* Card Styling */
        .info-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .info-card-header {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
            color: white;
            padding: 12px 18px;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .info-card-body {
            padding: 18px;
        }

        /* Table Styling */
        .order-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .order-table thead {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            color: white;
        }

        .order-table th {
            padding: 15px 12px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
        }

        .order-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f3f4;
            font-size: 1rem;
            vertical-align: top;
        }

        .order-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Variations and Addons styling */
        .variation-item {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 6px;
            font-size: 1rem;
        }

        .addon-item {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 6px;
            font-size: 1rem;
        }

        .variation-addon-item {
            background: #fff8e1;
            border: 1px solid #ff9800;
            padding: 6px 10px;
            margin: 3px 0;
            border-radius: 4px;
            font-size: 0.95rem;
        }

        .variation-name {
            font-weight: 700;
            color: #1976d2;
            font-size: 1.05rem;
            margin-bottom: 4px;
        }

        .addon-name {
            font-weight: 700;
            color: #388e3c;
            font-size: 1.05rem;
        }

        .variation-value {
            font-size: 0.95rem;
            color: #424242;
            font-weight: 500;
        }

        .variation-addon-name {
            font-weight: 600;
            color: #f57c00;
            font-size: 0.95rem;
        }

        /* Summary Styling */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .summary-item {
            background: white;
            padding: 12px 15px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .summary-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }

        .summary-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
        }

        /* Totals Styling */
        .totals-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .total-row:last-child {
            border-bottom: none;
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            padding-top: 12px;
        }

        .total-label {
            font-size: 1rem;
            font-weight: 600;
        }

        .total-value {
            font-size: 1rem;
            font-weight: 700;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-lg {
                max-width: 98%;
                margin: 5px auto;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .order-table {
                font-size: 0.9rem;
            }
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
                o.RESTAURANT_DISCOUNT_AMOUNT,
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
                COALESCE(SUM(od.price), 0) AS gross_amount
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
                o.RESTAURANT_DISCOUNT_AMOUNT,
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
                            <th class="text-center">Discount</th>
                            <th class="text-center">Delivery Charges</th>
                            <th class="text-center">VAT</th>
                            <th class="text-center">Net Amount</th>
                            <th class="text-center">Cash Amount</th>
                            <th class="text-center">Visa Amount</th>
                        </tr>
                        @foreach ($list as $k => $detail)
                            @php
                                $gTotalGrossAmt += $detail->gross_amount;
                                $gTotalDiscount += $detail->restaurant_discount_amount;
                                $gTotalDeliveryCharge += $detail->delivery_charge;
                                $gTotalTax += $detail->total_tax_amount;
                                $gTotalCash += $detail->cash_paid;
                                $gTotalCard += $detail->card_paid;
                                $gTotalAmount += $detail->order_amount;
                            @endphp
                            <tr class="order-row" data-order-id="{{ $detail->id }}" style="cursor: pointer;">
                                <td class="text-left">{{ $detail->order_serial }}</td>
                                <td class="text-center">{{ date('d-m-Y', strtotime($detail->created_at)) }}</td>
                                <td class="text-center">{{ $detail->customer_name ?? '' }} <br>
                                    {{ $detail->car_number ?? '' }} <br> {{ $detail->phone ?? '' }} </td>
                                <td class="text-center">{{ $detail->order_type }}</td>
                                <td class="text-center">{{ $detail->order_status }}</td>
                                <td class="text-center">{{ $detail->payment_status }}</td>
                                <td class="text-center">{{ $detail->gross_amount }}</td>
                                <td class="text-center">{{ $detail->restaurant_discount_amount }}</td>
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

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">
                        <i class="fas fa-receipt"></i> Order Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Order Summary -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-info-circle"></i> Order Summary
                        </div>
                        <div class="info-card-body">
                            <div class="summary-grid" id="orderSummary">
                                <!-- Order summary will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-list"></i> Order Items
                        </div>
                        <div class="info-card-body p-0">
                            <div class="table-responsive">
                                <table class="table order-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Item Details</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Discount</th>
                                            <th class="text-center">Addon</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orderItemsTable">
                                        <!-- Order items will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Totals -->
                    <div class="totals-section" id="orderTotals">
                        <!-- Order totals will be populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
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
    // Add hover effect to order rows
    $('.order-row').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );

    // Handle row click to show order details
    $('.order-row').click(function() {
        var orderId = $(this).data('order-id');
        if (orderId) {
            loadOrderDetails(orderId);
        }
    });

    function loadOrderDetails(orderId) {
        // Show loading state
        $('#orderDetailsModalLabel').html('<i class="fas fa-spinner fa-spin"></i> Loading Order Details...');
        $('#orderSummary, #orderItemsTable, #orderTotals').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

        // Show modal
        $('#orderDetailsModal').modal('show');

        // Make AJAX request
        $.ajax({
            url: '/reports/get-order-details',
            method: 'POST',
            data: {
                order_id: orderId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    populateOrderDetails(response.order_summary, response.order_details);
                    $('#orderDetailsModalLabel').html('<i class="fas fa-receipt"></i> Order Details - #' + response.order_summary.order_serial);
                } else {
                    showError('Failed to load order details');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading order details:', error);
                showError('Error loading order details: ' + error);
            }
        });
    }

    function populateOrderDetails(orderSummary, orderDetails) {
        // Populate order summary
        var summaryHtml = `
            <div class="summary-item">
                <div class="summary-label">Order ID</div>
                <div class="summary-value">${orderSummary.order_serial}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Date</div>
                <div class="summary-value">${new Date(orderSummary.created_at).toLocaleDateString()}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Customer</div>
                <div class="summary-value">${orderSummary.customer_name || 'N/A'}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Phone</div>
                <div class="summary-value">${orderSummary.phone || 'N/A'}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Car Number</div>
                <div class="summary-value">${orderSummary.car_number || 'N/A'}</div>
            </div>
        `;
        $('#orderSummary').html(summaryHtml);

        // Populate order items
        var itemsHtml = '';
        var subtotal = 0;

        orderDetails.forEach(function(item) {
            var itemTotal = parseFloat(item.net_amount || 0);
            subtotal += itemTotal;

            // Parse variations and addons
            var variationsHtml = '';
            var addonsHtml = '';

            // Handle variations
            if (item.variation) {
                try {
                    var variations = JSON.parse(item.variation);
                    if (Array.isArray(variations) && variations.length > 0) {
                        variationsHtml = '<div class="mt-1">';
                        variations.forEach(function(variation) {
                            if (variation.name && variation.values) {
                                variationsHtml += '<div class="variation-item mb-1">';
                                variationsHtml += '<div class="variation-name">' + variation.name + '</div>';
                                variation.values.forEach(function(value) {
                                    variationsHtml += '<div class="variation-value ml-1">';
                                    variationsHtml += value.label + ': <strong>' + parseFloat(value.optionPrice || 0).toFixed(3) + '</strong>';
                                    variationsHtml += '</div>';
                                });

                                // Handle variation-specific addons (at variation level, not value level)
                                if (variation.addons && Array.isArray(variation.addons) && variation.addons.length > 0) {
                                    variationsHtml += '<div class="ml-2 mt-1">';
                                    variation.addons.forEach(function(addon) {
                                        variationsHtml += '<div class="variation-addon-item">';
                                        variationsHtml += '<div class="variation-addon-name">';
                                        variationsHtml += '<i class="fas fa-plus-circle"></i> ' + addon.name;
                                        if (addon.quantity && addon.price) {
                                            variationsHtml += ' (' + addon.quantity + 'x' + parseFloat(addon.price).toFixed(3) + ')';
                                        }
                                        variationsHtml += '</div>';
                                        variationsHtml += '</div>';
                                    });
                                    variationsHtml += '</div>';
                                }

                                variationsHtml += '</div>';
                            }
                        });
                        variationsHtml += '</div>';
                    }
                } catch (e) {
                    console.log('Error parsing variations:', e);
                }
            }

            // Handle addons
            if (item.add_ons) {
                try {
                    var addons = JSON.parse(item.add_ons);
                    if (Array.isArray(addons) && addons.length > 0) {
                        addonsHtml = '<div class="mt-1">';
                        addons.forEach(function(addon) {
                            if (addon.name) {
                                addonsHtml += '<div class="addon-item mb-1">';
                                addonsHtml += '<div class="addon-name"><i class="fas fa-plus-circle"></i> ' + addon.name;
                                if (addon.quantity && addon.price) {
                                    addonsHtml += ' (' + addon.quantity + 'x' + parseFloat(addon.price).toFixed(3) + ')';
                                }
                                addonsHtml += '</div>';
                                addonsHtml += '</div>';
                            }
                        });
                        addonsHtml += '</div>';
                    }
                } catch (e) {
                    console.log('Error parsing addons:', e);
                }
            }

            itemsHtml += `
                <tr>
                    <td class="align-middle">
                        <div class="font-weight-bold" style="font-size: 1.1rem; color: #2c3e50;">${item.food_name || 'Unknown Item'}</div>
                        <small class="text-muted" style="font-size: 0.9rem;">ID: ${item.food_id}</small>
                        ${variationsHtml}
                        ${addonsHtml}
                    </td>
                    <td class="text-center align-middle" style="font-size: 1rem; font-weight: 600;">${parseFloat(item.price || 0).toFixed(3)}</td>
                    <td class="text-center align-middle" style="font-size: 1rem; font-weight: 600;">${item.quantity || 0}</td>
                    <td class="text-center align-middle text-danger" style="font-size: 1rem; font-weight: 600;">-${parseFloat(item.discount_on_food || 0).toFixed(3)}</td>
                    <td class="text-center align-middle text-success" style="font-size: 1rem; font-weight: 600;">+${parseFloat(item.total_add_on_price || 0).toFixed(3)}</td>
                    <td class="text-center align-middle font-weight-bold" style="font-size: 1.1rem; color: #2c3e50;">${itemTotal.toFixed(3)}</td>
                </tr>
            `;
        });

        $('#orderItemsTable').html(itemsHtml);

        // Populate order totals
        var taxAmount = parseFloat(orderSummary.total_tax_amount || 0);
        var deliveryCharge = parseFloat(orderSummary.delivery_charge || 0);
        var grandTotal = parseFloat(orderSummary.order_amount || 0);

        var totalsHtml = `
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">${subtotal.toFixed(3)}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Tax Amount:</span>
                <span class="total-value text-info">+${taxAmount.toFixed(3)}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Delivery Charge:</span>
                <span class="total-value text-warning">+${deliveryCharge.toFixed(3)}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Grand Total:</span>
                <span class="total-value text-primary">${grandTotal.toFixed(3)}</span>
            </div>
        `;
        $('#orderTotals').html(totalsHtml);
    }

    function showError(message) {
        $('#orderSummary, #orderItemsTable, #orderTotals').html(`
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </div>
        `);
        $('#orderDetailsModalLabel').html('<i class="fas fa-receipt"></i> Order Details');
    }
});
</script>
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
