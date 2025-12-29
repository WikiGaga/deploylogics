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
        height: auto !important;
    }
    .kt-portlet.chart_block {
        overflow: visible !important;
        max-width: 100% !important;
    }
    .kt-portlet.chart_block .kt-portlet__body {
        overflow: visible !important;
        padding: 0.5rem !important;
        max-width: 100% !important;
        width: 100% !important;
        height: auto !important;
    }
    .kt-portlet__body--fit {
        overflow: visible !important;
        position: relative !important;
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
    }
    .kt-portlet__body--fit > div {
        width: 100% !important;
        max-width: 100% !important;
        overflow: visible !important;
        box-sizing: border-box !important;
        position: relative !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .kt-portlet.chart_block {
        display: flex !important;
        flex-direction: column !important;
    }
    .kt-portlet.chart_block .kt-portlet__body {
        flex: 0 0 auto !important;
    }
    #rest_month_sale_branch,
    #payment_method_chart,
    #order_type_chart,
    #top_food_items,
    #branch_performance,
    #sales_by_day_chart,
    #sales_by_hour_chart {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        position: relative !important;
        margin: 0 !important;
        padding: 0 !important;
        min-height: 300px !important;
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
        margin: 0 !important;
    }
    .apexcharts-svg {
        max-width: 100% !important;
        width: 100% !important;
        height: auto !important;
    }
    .kt-portlet.chart_block .kt-portlet__head {
        margin-bottom: 0.5rem !important;
    }
    .row {
        margin-bottom: 1rem !important;
    }
    @media (max-width: 768px) {
        .chart_block {
            height: auto !important;
        }
        .kt-portlet.chart_block .kt-portlet__body {
            height: auto !important;
        }
        .kt-portlet__body--fit > div {
            height: auto !important;
        }
    }
    .col-lg-6,
    .col-lg-12 {
        overflow: hidden !important;
        max-width: 100% !important;
    }
    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }
    .shimmer-row {
        height: 50px;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    .shimmer-cell {
        height: 20px;
        border-radius: 4px;
        margin: 15px 0;
    }
    .restaurant-filter-sidepane {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100vh;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        z-index: 1000;
        transition: right 0.3s ease;
        overflow-y: auto;
    }
    .restaurant-filter-sidepane.open {
        right: 0;
    }
    .restaurant-filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        display: none;
    }
    .restaurant-filter-overlay.show {
        display: block;
    }
    .daterangepicker {
        z-index: 10001 !important;
    }
    .restaurant-filter-sidepane .select2-container--open {
        z-index: 10001 !important;
    }
    @media (max-width: 768px) {
        .restaurant-filter-sidepane {
            width: 100%;
            right: -100%;
        }
    }
</style>
<div class="row kt-margin-b-15">
    <div class="col-lg-12">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary btn-sm" id="restaurant_filter_btn">
                <i class="la la-filter"></i> Apply Filters
            </button>
        </div>
    </div>
</div>
<div class="restaurant-filter-overlay" id="restaurant_filter_overlay"></div>
<div class="restaurant-filter-sidepane" id="restaurant_filter_sidepane">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Filter Dashboard
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <button type="button" class="btn btn-sm btn-icon" id="restaurant_filter_close_btn">
                    <i class="la la-times"></i>
                </button>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form id="restaurant_filter_form">
                <div class="form-group">
                    <label class="erp-col-form-label">Date Range:</label>
                    <input type="text" class="form-control erp-form-control-sm" id="restaurant_date_range" placeholder="Select Date Range" />
                    <input type="hidden" name="date_from" id="restaurant_date_from" />
                    <input type="hidden" name="date_to" id="restaurant_date_to" />
                </div>
                <div class="form-group">
                    <label class="erp-col-form-label">Branches:</label>
                    <div class="erp-select2">
                        <select class="form-control kt-select2 erp-form-control-sm" id="restaurant_branches" name="branches[]" multiple>
                            @php
                                $branches = App\Library\Utilities::getAllBranches();
                            @endphp
                            @foreach($branches as $branch)
                                <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm" id="restaurant_filter_apply_btn">
                        <i class="la la-check"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" id="restaurant_filter_reset_btn" style="margin-left: 10px;">
                        <i class="la la-refresh"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    <div class="col-lg-12">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile chart_block">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.sales_by_day') }}
                        <i class="la la-info-circle kt-font-info" style="font-size: 16px; margin-left: 5px;"></i>
                    </h3>
                    <div style="margin-top: 5px;">
                        <span class="kt-font-sm kt-font-muted" id="sales_by_day_summary">{{ __('message.sales') }} (0) - 0.000</span>
                    </div>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-secondary" style="margin-left: 5px;">
                        <i class="la la-download"></i>
                    </button>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="sales_by_day_chart">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
                <div class="row" style="margin-top: 20px; padding: 0 15px;">
                    <div class="col-lg-3 col-md-6" id="sales_breakdown_online">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <span class="kt-font-sm" style="margin-right: 10px;">{{ __('message.card') }}</span>
                            <span class="kt-font-sm kt-font-bold" id="online_sales_amount">0.000</span>
                        </div>
                        <div style="height: 3px; background: #f0f0f0; border-radius: 2px;">
                            <div style="height: 100%; background: #FFA800; width: 0%; border-radius: 2px;" id="online_sales_bar"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" id="sales_breakdown_cash">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <span class="kt-font-sm" style="margin-right: 10px;">{{ __('message.cash') }}</span>
                            <span class="kt-font-sm kt-font-bold" id="cash_sales_amount">0.000</span>
                        </div>
                        <div style="height: 3px; background: #f0f0f0; border-radius: 2px;">
                            <div style="height: 100%; background: #FFA800; width: 0%; border-radius: 2px;" id="cash_sales_bar"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" id="sales_breakdown_delivery">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <span class="kt-font-sm" style="margin-right: 10px;">{{ __('message.delivery') }}</span>
                            <span class="kt-font-sm kt-font-bold" id="delivery_sales_amount">0.000</span>
                        </div>
                        <div style="height: 3px; background: #f0f0f0; border-radius: 2px;">
                            <div style="height: 100%; background: #FFA800; width: 0%; border-radius: 2px;" id="delivery_sales_bar"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" id="sales_breakdown_pickup">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <span class="kt-font-sm" style="margin-right: 10px;">{{ __('message.takeaway') }}</span>
                            <span class="kt-font-sm kt-font-bold" id="pickup_sales_amount">0.000</span>
                        </div>
                        <div style="height: 3px; background: #f0f0f0; border-radius: 2px;">
                            <div style="height: 100%; background: #FFA800; width: 0%; border-radius: 2px;" id="pickup_sales_bar"></div>
                        </div>
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
                        {{ __('message.sales_by_hour') }}
                        <i class="la la-info-circle kt-font-info" style="font-size: 16px; margin-left: 5px;"></i>
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-secondary" style="margin-left: 5px;">
                        <i class="la la-download"></i>
                    </button>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div style="max-height: 550px; overflow-y: auto; overflow-x: auto;">
                    <div id="sales_by_hour_chart">
                        <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                    </div>
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

<div class="row">
    <div class="col-lg-6">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.sales_by_menu_item') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-secondary" id="menu_item_download_btn">
                        <i class="la la-download"></i>
                    </button>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div id="sales_by_menu_item_table">
                    <div class="text-center p-5">
                        <div class="kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{ __('message.sales_by_location') }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-secondary" id="location_download_btn">
                        <i class="la la-download"></i>
                    </button>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div id="sales_by_location_table">
                    <div class="text-center p-5">
                        <div class="kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var translations = {
        sales: '{{ __('message.sales') }}'
    };

    var restaurantFilters = {
        dateFrom: null,
        dateTo: null,
        branches: []
    };

    if(typeof window.restaurantFilters !== 'undefined' && window.restaurantFilters) {
        restaurantFilters.dateFrom = window.restaurantFilters.dateFrom || null;
        restaurantFilters.dateTo = window.restaurantFilters.dateTo || null;
        restaurantFilters.branches = window.restaurantFilters.branches || [];
    }

    $(document).ready(function() {
        $('#restaurant_branches').select2({
            placeholder: 'Select Branches',
            allowClear: true
        });

        var dateRangePicker = $('#restaurant_date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'DD-MM-YYYY',
                separator: ' to '
            },
            opens: 'left',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('#restaurant_date_range').on('show.daterangepicker', function(ev, picker) {
            setTimeout(function() {
                $('.daterangepicker').css('z-index', '10001');
                var sidepane = $('#restaurant_filter_sidepane');
                var pickerEl = $('.daterangepicker');
                if(pickerEl.length && sidepane.length) {
                    var sidepaneRight = sidepane.offset().left + sidepane.outerWidth();
                    var pickerLeft = pickerEl.offset().left;
                    var pickerWidth = pickerEl.outerWidth();
                    if(pickerLeft + pickerWidth > sidepaneRight) {
                        pickerEl.css({
                            'left': 'auto',
                            'right': '10px'
                        });
                    }
                }
            }, 10);
        });

        $('#restaurant_date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
            $('#restaurant_date_from').val(picker.startDate.format('DD-MM-YYYY'));
            $('#restaurant_date_to').val(picker.endDate.format('DD-MM-YYYY'));
        });

        $('#restaurant_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#restaurant_date_from').val('');
            $('#restaurant_date_to').val('');
        });

        $('#restaurant_filter_btn').click(function() {
            var currentFilters = restaurantFilters;

            if(typeof window.restaurantFilters !== 'undefined' && window.restaurantFilters) {
                currentFilters = window.restaurantFilters;
                restaurantFilters.dateFrom = currentFilters.dateFrom;
                restaurantFilters.dateTo = currentFilters.dateTo;
                restaurantFilters.branches = currentFilters.branches || [];
            }

            $('#restaurant_filter_overlay').addClass('show');
            $('#restaurant_filter_sidepane').addClass('open');

            setTimeout(function() {
                if(currentFilters.dateFrom && currentFilters.dateTo) {
                    var dateFromParts = currentFilters.dateFrom.split('-');
                    var dateToParts = currentFilters.dateTo.split('-');
                    var startDate = moment(dateFromParts[2] + '-' + dateFromParts[1] + '-' + dateFromParts[0], 'YYYY-MM-DD');
                    var endDate = moment(dateToParts[2] + '-' + dateToParts[1] + '-' + dateToParts[0], 'YYYY-MM-DD');

                    var daterangepicker = $('#restaurant_date_range').data('daterangepicker');
                    if(daterangepicker) {
                        daterangepicker.setStartDate(startDate);
                        daterangepicker.setEndDate(endDate);
                    }

                    $('#restaurant_date_range').val(startDate.format('DD-MM-YYYY') + ' to ' + endDate.format('DD-MM-YYYY'));
                    $('#restaurant_date_from').val(currentFilters.dateFrom);
                    $('#restaurant_date_to').val(currentFilters.dateTo);
                } else {
                    $('#restaurant_date_range').val('');
                    $('#restaurant_date_from').val('');
                    $('#restaurant_date_to').val('');
                    var daterangepicker = $('#restaurant_date_range').data('daterangepicker');
                    if(daterangepicker) {
                        daterangepicker.setStartDate(moment());
                        daterangepicker.setEndDate(moment());
                    }
                }

                if(currentFilters.branches && currentFilters.branches.length > 0) {
                    $('#restaurant_branches').val(currentFilters.branches).trigger('change');
                } else {
                    $('#restaurant_branches').val(null).trigger('change');
                }
            }, 100);
        });

        $('#restaurant_filter_close_btn, #restaurant_filter_overlay').click(function() {
            $('#restaurant_filter_overlay').removeClass('show');
            $('#restaurant_filter_sidepane').removeClass('open');
        });

        $('#restaurant_filter_sidepane').click(function(e) {
            e.stopPropagation();
        });

        $('#restaurant_filter_apply_btn').click(function() {
            var dateFrom = $('#restaurant_date_from').val();
            var dateTo = $('#restaurant_date_to').val();
            var branches = $('#restaurant_branches').val();

            restaurantFilters.dateFrom = dateFrom;
            restaurantFilters.dateTo = dateTo;
            restaurantFilters.branches = branches || [];

            window.restaurantFilters = {
                dateFrom: dateFrom,
                dateTo: dateTo,
                branches: branches || []
            };

            updateFilterButtonState();

            $('#restaurant_filter_overlay').removeClass('show');
            $('#restaurant_filter_sidepane').removeClass('open');

            reloadRestaurantDashboard();
        });

        $('#restaurant_filter_reset_btn').click(function() {
            $('#restaurant_date_range').val('');
            $('#restaurant_date_from').val('');
            $('#restaurant_date_to').val('');
            $('#restaurant_branches').val(null).trigger('change');

            if($('#restaurant_date_range').data('daterangepicker')) {
                $('#restaurant_date_range').data('daterangepicker').setStartDate(moment());
                $('#restaurant_date_range').data('daterangepicker').setEndDate(moment());
            }

            restaurantFilters.dateFrom = null;
            restaurantFilters.dateTo = null;
            restaurantFilters.branches = [];

            window.restaurantFilters = {
                dateFrom: null,
                dateTo: null,
                branches: []
            };

            updateFilterButtonState();

            reloadRestaurantDashboard();
        });

        function updateFilterButtonState() {
            var hasFilters = (restaurantFilters.dateFrom && restaurantFilters.dateTo) ||
                            (restaurantFilters.branches && restaurantFilters.branches.length > 0);

            var $btn = $('#restaurant_filter_btn');
            if(hasFilters) {
                $btn.addClass('btn-warning').removeClass('btn-primary');
                var filterText = [];
                if(restaurantFilters.dateFrom && restaurantFilters.dateTo) {
                    filterText.push('Date: ' + restaurantFilters.dateFrom + ' to ' + restaurantFilters.dateTo);
                }
                if(restaurantFilters.branches && restaurantFilters.branches.length > 0) {
                    filterText.push('Branches: ' + restaurantFilters.branches.length);
                }
                $btn.attr('title', 'Active Filters: ' + filterText.join(', '));
            } else {
                $btn.addClass('btn-primary').removeClass('btn-warning');
                $btn.attr('title', 'Apply Filters');
            }
        }

        window.updateFilterButtonState = updateFilterButtonState;

        setTimeout(function() {
            updateFilterButtonState();
        }, 500);
    });

    function reloadRestaurantDashboard() {
        $('#dashboard_data').html('');
        $('#shimmer_loading').addClass('loading');

        var formData = {
            date_from: restaurantFilters.dateFrom,
            date_to: restaurantFilters.dateTo,
            branches: restaurantFilters.branches
        };

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '/dashboard/get-restaurant-dashboard-detail',
            dataType: 'json',
            data: formData,
            success: function(response) {
                $('#shimmer_loading').removeClass('loading');

                var data = response['data'];
                var view = data['view'];
                $('#dashboard_data').html(view);

                setTimeout(function() {
                    if(typeof window.restaurantFilters !== 'undefined' && window.restaurantFilters) {
                        restaurantFilters.dateFrom = window.restaurantFilters.dateFrom;
                        restaurantFilters.dateTo = window.restaurantFilters.dateTo;
                        restaurantFilters.branches = window.restaurantFilters.branches || [];
                    }
                    if(typeof updateFilterButtonState === 'function') {
                        updateFilterButtonState();
                    }
                    if(typeof loadRestaurantCharts === 'function'){
                        loadRestaurantCharts();
                    }
                }, 200);
            },
            error: function() {
                $('#shimmer_loading').removeClass('loading');
                $('#dashboard_data').html('<div class="alert alert-danger">Error loading dashboard. Please try again.</div>');
            }
        });
    }

    window.getRestaurantFilters = function() {
        if(typeof window.restaurantFilters !== 'undefined' && window.restaurantFilters) {
            return window.restaurantFilters;
        }
        if(typeof restaurantFilters !== 'undefined') {
            return restaurantFilters;
        }
        return {
            dateFrom: null,
            dateTo: null,
            branches: []
        };
    };

    if(typeof window.restaurantFilters === 'undefined' || !window.restaurantFilters) {
        window.restaurantFilters = restaurantFilters;
    } else {
        restaurantFilters.dateFrom = window.restaurantFilters.dateFrom;
        restaurantFilters.dateTo = window.restaurantFilters.dateTo;
        restaurantFilters.branches = window.restaurantFilters.branches || [];
    }
</script>

