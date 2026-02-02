<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" id="htmlRoot">

<!-- begin::Head -->
<head>
    <base href="">
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{--<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    --}}<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @yield('pageCSS')
    <!--begin::Global Theme Styles(used by all pages) -->
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <!--end::Global Theme Styles -->
    <link href="{{ asset('css/report.css') }}" rel="stylesheet" type="text/css" />
    <!-- RTL CSS - loaded dynamically based on user preference -->
    <link href="{{ asset('css/rtl.css') }}" rel="stylesheet" type="text/css" id="rtlStylesheet" disabled />

    <script>
        (function() {
            var rtlEnabled = localStorage.getItem('rtl_enabled') === 'true';
            if (rtlEnabled) {
                document.documentElement.setAttribute('dir', 'rtl');
                document.getElementById('rtlStylesheet').disabled = false;
            }
        })();

        function toggleRTL(enabled) {
            localStorage.setItem('rtl_enabled', enabled);
            document.documentElement.setAttribute('dir', enabled ? 'rtl' : 'ltr');
            document.getElementById('rtlStylesheet').disabled = !enabled;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var rtlToggle = document.getElementById('rtlToggle');
            if (rtlToggle) rtlToggle.checked = localStorage.getItem('rtl_enabled') === 'true';
        });
    </script>
    <script src="/js/pages/js/lang/en.js" type="text/javascript"></script>
    <style>
        /*
    right click menu
*/
    #contextRateMenu,
    #contextCopyDataMenu,
    #contextChartMenu,
    #contextMenu {
        position: absolute;
        display: none;
        z-index: 99;
    }
    div#contextChartMenu a,
    div#contextCopyDataMenu a,
    div#contextMenu a {
        color: #6b6a6a;
        font-family: Verdana;
        font-weight: 400;
    }
    div#contextChartMenu a:hover,
    div#contextCopyDataMenu a:hover,
    div#contextMenu a:hover {
        background: #FFA800;
        color: #fff;
    }

    div#contextRateMenu table.right_rate_list,
    div#contextMenu ul {
        box-shadow: 0px -2px 20px 0px rgba(0, 0, 0, 0.35);
        -webkit-box-shadow: 0px -2px 20px 0px rgba(0, 0, 0, 0.35);
        -moz-box-shadow: 0px -2px 20px 0px rgba(0, 0, 0, 0.35);
    }


       .clickable-cell {
            cursor: pointer;
            /* Force the default blue color */
            color: #17a2b8 !important;
            text-decoration: none;
        }

        .clickable-cell:hover {
            /* Force the slightly darker blue on hover */
            text-decoration: underline;
            color: #117a8b !important;
        }
    </style>

    <script src="{{ asset('/js/generate_pdf.js') }}"></script>
    <style>
        @media print {
            #downloadBtn{
                display: none;
            }
        }
    </style>
</head>

<body>
@if(isset($data['form_file_type']) && $data['form_file_type'] == 'pdf')
    @include('reports.pdfCss')
@endif
@include('elements/popup')
<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="orderDetailsModalLabel">
                    <i class="fas fa-receipt"></i> Order Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Order Summary -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle text-primary"></i> Order Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="orderSummary">
                                    <!-- Order summary will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list text-primary"></i> Order Items</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="border-0">Item</th>
                                                <th class="border-0 text-center">Price</th>
                                                <th class="border-0 text-center">Qty</th>
                                                <th class="border-0 text-center">Discount</th>
                                                <th class="border-0 text-center">Addon</th>
                                                <th class="border-0 text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderItemsTable">
                                            <!-- Order items will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-calculator text-primary"></i> Order Totals</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="orderTotals">
                                    <!-- Order totals will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@include('reports.template.download_btn')

<div id="content">
    @yield('content')
</div>

</body>
@yield('pageJS')
<script>
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "dark": "#282a3c",
                "light": "#ffffff",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": [
                    "#c5cbe3",
                    "#a1a8c3",
                    "#3d4465",
                    "#3e4466"
                ],
                "shape": [
                    "#f0f3ff",
                    "#d9dffa",
                    "#afb4d4",
                    "#646c9a"
                ]
            }
        }
    };
    var dataSession = <?php echo json_encode(Session::get('dataSession')); ?>;
    function valueEmpty(val){
        if(val == 0 || val == undefined || val == "" || val == null || val == NaN || val == 'NaN' || !val){
            return true;
        }
        return false;
    }
</script>
<!--begin::Global Theme Bundle(used by all pages) -->
<script src="/assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
<script src="/assets/js/scripts.bundle.js" type="text/javascript"></script>
<script src="{{ asset('js/pages/js/shortcuts.js') }}" type="text/javascript"></script>
<!--begin::Page Scripts(used by this page) -->
<script src="{{ asset('js/pages/js/report-user-html-table.js') }}" type="text/javascript"></script>
<!--end::Page Scripts -->

@yield('customJS')

@include('layouts.commonJSFunc')

<!--end::Global Theme Bundle -->

<script>
    var table_width = $(".static_report_table").width();
    if(table_width > 1300){
        table_width = parseInt(table_width) + 200;
        table_width = table_width+'px';
        $("#kt_portlet_table").css({'width':'100%'});
    }
    $(".generate_report").click(function(e){
        var id = $(this).data('id');
        var type = $(this).data('type');
        var path = '';

        // accounts
        var accountsTypeList = ['crv','cpv','brv','bpv','jv','obv','lv'];
        if(accountsTypeList.includes(type)) {
            path = '/accounts/'+type+'/form/'+id;
        }

        // purchase
        if(type == 'GRN' || type == 'GRNM'){path = '/grn/form/'+id;}
        if(type == 'PR'){path = '/purchase-return/form/'+id;}
        if(type == 'PO'){path = '/purchase-order/form/'+id;}
        // sale
        if(type == 'SI'){path = '/sales-invoice/form/'+id;}
        if(type == 'SR'){path = '/sale-return/form/'+id;}
        if(type == 'POS'){path = '/pos-sales-invoice/form/'+id;}
        if(type == 'RPOS'){path = '/pos-sales-return/form/'+id;}
        if(type == 'SD'){path = '/sales-delivery/form/'+id;}
        if(type == 'LFS'){path = '/sales-fee/form/'+id;}
        if(type == 'RI'){path = '/rebate-invoice/form/'+id;}
        if(type == 'DRF'){path = '/display-rent-fee/form/'+id;}
        // stock inventory
        if(type == 'OS'){path = '/stock/opening-stock/form/'+id;}
        if(type == 'EI'){path = '/stock/expired-items/form/'+id;}
        if(type == 'ST'){path = '/stock/stock-transfer/form/'+id;}
        if(type == 'STR'){path = '/stock/stock-receiving/form/'+id;}
        if(type == 'SA'){path = '/stock/stock-adjustment/form/'+id;}
        if(type == 'SP'){path = '/stock/sample-items/form/'+id;}
        if(type == 'DI'){path = '/stock/damaged-items/form/'+id;}
        if(type == 'IST'){path = '/stock/internal-stock-transfer/form/'+id;}

        if(path != ''){
            window.open(path, "_blank");
        }
    });
    $(document).on('ready' , function(e){
        $('body').removeClass('pointerEventsNone');
    });
</script>
<!-- end::Body -->
<script src="{{ asset('/js/jquery.table2excel.min.js') }}"></script>

@yield('exportXls')

<script>

    $(document).find('table:first-child').addClass('table2ExcelExport');

    $(document).on('click', '.btnReportExport', function(e) {
        e.preventDefault();

        var exportType = ($(this).data('export') || '').toString().toLowerCase();
        if (!exportType) return false;

        window.open("{{ route('reports.export') }}?type=" + encodeURIComponent(exportType), '_blank');
        return false;
    });
</script>
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
            <div class="col-md-6">
                <div class="mb-2">
                    <strong>Order ID:</strong> ${orderSummary.order_serial}
                </div>
                <div class="mb-2">
                    <strong>Date:</strong> ${new Date(orderSummary.created_at).toLocaleDateString()}
                </div>
                <div class="mb-2">
                    <strong>Customer:</strong> ${orderSummary.customer_name || 'N/A'}
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    <strong>Phone:</strong> ${orderSummary.phone || 'N/A'}
                </div>
                <div class="mb-2">
                    <strong>Car Number:</strong> ${orderSummary.car_number || 'N/A'}
                </div>
            </div>
        `;
        $('#orderSummary').html(summaryHtml);

        // Populate order items
        var itemsHtml = '';
        var subtotal = 0;
        var totalDiscount = 0;
        var totalAddon = 0;

        orderDetails.forEach(function(item) {
            totalDiscount += parseFloat(item.discount_on_food || 0);
            var itemTotal = parseFloat((item.price * item.quantity) + item.total_add_on_price || 0);
            itemTotal = itemTotal - (item.discount_on_food || 0);
            subtotal += parseFloat(item.price * item.quantity || 0);            
            totalAddon += parseFloat(item.total_add_on_price || 0);

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
                                variation.values.forEach(function(value) {
                                    variationsHtml += '<div class="variation-value ml-1">';
                                    if(value.is_deleted == 'Y'){
                                        if(variation.printing_option == 'option_list_name'){
                                            variationsHtml += '- <del>' + value.options_list_name + '</del>';
                                        }else{
                                            variationsHtml += '- <del>' + value.label + '</del>';
                                        }
                                    }else{
                                        if(variation.printing_option == 'option_list_name'){
                                            variationsHtml += '- ' + value.options_list_name + '</strong>';
                                        }else{
                                            variationsHtml += '- ' + value.label + '</strong>';
                                        }
                                    }
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
                    <td class="align-middle" style="font-size: 0.95rem;">
                        <div class="font-weight-bold" style="font-size: 1rem;">${item.food_name || 'Unknown Item'}</div>
                        ${variationsHtml}
                        ${addonsHtml}
                    </td>
                    <td class="text-center align-middle" style="font-size: 0.95rem;">${parseFloat(item.price || 0).toFixed(3)}</td>
                    <td class="text-center align-middle" style="font-size: 0.95rem;">${item.quantity || 0}</td>
                    <td class="text-center align-middle text-danger" style="font-size: 0.95rem;">-${parseFloat(item.discount_on_food * item.quantity || 0).toFixed(3)} (${(((item.discount_on_food * item.quantity) / item.price) * 100 || 0).toFixed(0)}%)</td>
                    <td class="text-center align-middle text-success" style="font-size: 0.95rem;">+${parseFloat(item.total_add_on_price || 0).toFixed(3)}</td>
                    <td class="text-center align-middle font-weight-bold" style="font-size: 0.95rem;">${parseFloat(item.net_amount || 0).toFixed(3)}</td>
                </tr>
                <tr>
                    <td colspan="6">
                        <strong>Notes:</strong> ${item.notes || 'N/A'}
                    </td>    
                </tr>
            `;
        });

        $('#orderItemsTable').html(itemsHtml);

        // Populate order totals
        var taxAmount = parseFloat(orderSummary.total_tax_amount || 0);
        var deliveryCharge = parseFloat(orderSummary.delivery_charge || 0);
        var grandTotal = parseFloat(orderSummary.order_amount || 0);
        var discountTotal = totalDiscount;

        var totalsHtml = `
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span class="font-weight-bold">${subtotal.toFixed(3)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Addons:</span>
                    <span class="font-weight-bold text-info">+${totalAddon.toFixed(3)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax Amount:</span>
                    <span class="font-weight-bold text-info">+${taxAmount.toFixed(3)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Discount:</span>
                    <span class="font-weight-bold text-danger">-${discountTotal.toFixed(3)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Delivery Charge:</span>
                    <span class="font-weight-bold text-warning">+${deliveryCharge.toFixed(3)}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="h5">Notes:</div>
                    <p>${orderDetails.order_notes || 'N/A'}</p>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="h5">Grand Total:</span>
                    <span class="h5 text-primary font-weight-bold">${grandTotal.toFixed(3)}</span>
                </div>
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
<script>
// RTL Form Layout - Swap labels and inputs, reverse column order
document.addEventListener('DOMContentLoaded', function() {
    function applyRTLFormLayout() {
        if (document.documentElement.getAttribute('dir') === 'rtl') {
            const mainFormRows = document.querySelectorAll('.form-group-block.row, .kt-portlet__body > .form-group-block.row');
            mainFormRows.forEach(function(row) {
                const columns = Array.from(row.querySelectorAll(':scope > [class*="col-lg-"], :scope > [class*="col-md-"]'));
                if (columns.length > 1) {
                    columns.reverse().forEach(function(col) {
                        row.appendChild(col);
                    });
                }
            });

            const innerRows = document.querySelectorAll('.form-group-block .col-lg-4 > .row, .form-group-block .col-lg-6 > .row, .form-group-block .col-md-4 > .row, .form-group-block .col-md-6 > .row, .kt-portlet__body .col-lg-4 > .row, .kt-portlet__body .col-lg-6 > .row');
            innerRows.forEach(function(row) {
                const label = row.querySelector('label[class*="col-"]');
                const inputDiv = row.querySelector('div[class*="col-"]');
                if (label && inputDiv && label.nextElementSibling === inputDiv) {
                    row.insertBefore(inputDiv, label);
                }
            });
        }
    }
    applyRTLFormLayout();
});
</script>
</html>
