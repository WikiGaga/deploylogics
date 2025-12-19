<style>
    .chart-spinner{
        position: absolute;
        right: 55%;
        top: 35%;
    }
    .chart-spinner:before{
        width: 60px !important;
        height: 60px !important;
    }
    .chart_block{
        min-height: 300px !important;
        height: auto !important;
    }
    .kt-portlet.chart_block {
        overflow: visible !important;
        max-width: 100% !important;
    }
    .kt-portlet.chart_block .kt-portlet__body {
        overflow: visible !important;
        padding: 1.5rem !important;
        max-width: 100% !important;
        width: 100% !important;
        min-height: 350px !important;
    }
    .kt-portlet__body--fit {
        overflow: visible !important;
        position: relative !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    .kt-portlet__body--fit > div {
        width: 100% !important;
        max-width: 100% !important;
        overflow: visible !important;
        box-sizing: border-box !important;
        position: relative !important;
        min-height: 320px !important;
    }
    #rest_month_sale_branch,
    #payment_method_chart,
    #order_type_chart,
    #top_food_items,
    #branch_performance {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        position: relative !important;
    }
    #rest_month_sale_branch > *,
    #payment_method_chart > *,
    #order_type_chart > *,
    #top_food_items > *,
    #branch_performance > * {
        max-width: 100% !important;
        width: 100% !important;
    }
    .apexcharts-canvas {
        max-width: 100% !important;
        width: 100% !important;
    }
    .apexcharts-svg {
        max-width: 100% !important;
        width: 100% !important;
        height: auto !important;
    }
    @media (max-width: 768px) {
        .chart_block {
            min-height: 300px !important;
        }
        .kt-portlet.chart_block .kt-portlet__body {
            min-height: 320px !important;
        }
        .kt-portlet__body--fit > div {
            min-height: 300px !important;
        }
    }
    .col-lg-6,
    .col-lg-12 {
        overflow: hidden !important;
        max-width: 100% !important;
    }
</style>
<div class="row kt-margin-b-15">
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('/assets/images//abstract-1.svg');
            background-color:#0014ff1a;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#5d78ff"></path>
                                    <rect fill="#5d78ff" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) " x="16.3255682" y="2.94551858" width="3" height="18" rx="1"></rect>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#5d78ff !important;">{{ __('message.today_net_sales') }}</span>
                        <span class="erp-widget__desc" style="color:#5d78ff !important;"> {{number_format($data['today_net_sales'],3)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('/assets/images//abstract-2.svg');
            background-color:#ff00c81a;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#ff00c8"></path>
                                    <rect fill="#ff00c8" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) " x="16.3255682" y="2.94551858" width="3" height="18" rx="1"></rect>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#ff00c8 !important;">{{ __('message.week_net_sales') }}</span>
                        <span class="erp-widget__desc" style="color:#ff00c8 !important;"> {{number_format($data['week_net_sales'],3)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('/assets/images//abstract-3.svg');
            background-color:#00ff431a;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#00e03b"></path>
                                    <rect fill="#00e03b" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) " x="16.3255682" y="2.94551858" width="3" height="18" rx="1"></rect>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#00e03b !important;">{{ __('message.month_net_sales') }}</span>
                        <span class="erp-widget__desc" style="color:#00e03b !important;"> {{number_format($data['month_net_sales'],3)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('/assets/images//abstract-4.svg');
            background-color:#00d0ff1a;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#00d0ff"></path>
                                    <rect fill="#00d0ff" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) " x="16.3255682" y="2.94551858" width="3" height="18" rx="1"></rect>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#00d0ff !important;">{{ __('message.year_net_sales') }}</span>
                        <span class="erp-widget__desc" style="color:#00d0ff !important;"> {{number_format($data['year_net_sales'],3)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row kt-margin-b-15">
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('');
            background-color:#EEE5FF;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#8950FC" x="4" y="4" width="7" height="7" rx="1.5"></rect>
                                    <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#8950FC" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#8950FC !important;">{{ __('message.today_orders') }}</span>
                        <span class="erp-widget__desc" style="color:#8950FC !important;"> {{number_format($data['today_orders'],0)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('');
            background-color:#FFE2E5;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#F64E60" x="4" y="4" width="7" height="7" rx="1.5"></rect>
                                    <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#F64E60" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#F64E60 !important;">{{ __('message.average_bill') }}</span>
                        <span class="erp-widget__desc" style="color:#F64E60 !important;"> {{number_format($data['avg_bill'],3)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('');
            background-color:#E1F0FF;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#3699FF" x="4" y="4" width="7" height="7" rx="1.5"></rect>
                                    <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#3699FF" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#3699FF !important;">{{ __('message.unpaid_bills') }}</span>
                        <span class="erp-widget__desc" style="color:#3699FF !important;"> {{number_format($data['unpaid_bills'],0)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-repeat: no-repeat;
            background-position: right top;
            background-size: 30% auto;
            background-image:url('');
            background-color:#C9F7F5;">
            <div class="kt-portlet__body">
                <div class="kt-widget1 kt-widget1--fit">
                    <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#1BC5BD" x="4" y="4" width="7" height="7" rx="1.5"></rect>
                                    <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#1BC5BD" opacity="0.3"></path>
                                </g>
                            </svg>
                        </span>
                        <span class="erp-widget__subtitle" style="color:#1BC5BD  !important;">{{ __('message.total_discounts') }}</span>
                        <span class="erp-widget__desc" style="color:#1BC5BD  !important;"> {{number_format($data['total_discounts'],3)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile chart_block">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.monthly_sales_trend_branch_wise') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="rest_month_sale_branch">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile chart_block">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.payment_method_breakdown') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="payment_method_chart">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile chart_block">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.order_type_breakdown') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="order_type_chart">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile chart_block">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.top_5_food_items') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="top_food_items">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile chart_block">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.branch_performance') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="branch_performance">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
</div>


