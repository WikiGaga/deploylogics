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
        height: 275px !important;
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
                        <span class="erp-widget__subtitle" style="color:#5d78ff !important;">  Current Month Sales</span>
                        <span class="erp-widget__desc" style="color:#5d78ff !important;"> {{number_format($data['monthly_sale'],3)}} </span>
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
                        <span class="erp-widget__subtitle" style="color:#ff00c8 !important;">  Current Week Sales</span>
                        <span class="erp-widget__desc" style="color:#ff00c8 !important;"> {{number_format($data['weekly_sale'],3)}} </span>
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
                        <span class="erp-widget__subtitle" style="color:#00e03b !important;">  Today Sales</span>
                        <span class="erp-widget__desc" style="color:#00e03b !important;"> {{number_format($data['today_sale'],3)}} </span>
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
                        <span class="erp-widget__subtitle" style="color:#00d0ff !important;">  Current Year Sale</span>
                        <span class="erp-widget__desc" style="color:#00d0ff !important;"> {{number_format($data['year_sale'],3)}} </span>
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
                        Monthly Sale Branch Wise
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="month_sale_branch">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
            <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Profit Margin
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="profit_margin">
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
                        Top 5 Item Sales
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="top_item_sales">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3" style="">
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
                        <span class="erp-widget__subtitle" style="color:#8950FC !important;"> New Products</span>
                        <span class="erp-widget__desc" style="color:#8950FC !important;"> {{number_format($data['new_products'],0)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3" style="">
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
                        <span class="erp-widget__subtitle" style="color:#F64E60 !important;"> New Customers</span>
                        <span class="erp-widget__desc" style="color:#F64E60 !important;"> {{number_format($data['new_customers'],0)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3" style="">
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
                        <span class="erp-widget__subtitle" style="color:#3699FF !important;"> Avg Daily Invoices</span>
                        <span class="erp-widget__desc" style="color:#3699FF !important;"> {{number_format($data['avg_daily_invoices'],3)}} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3" style="">
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
                        <span class="erp-widget__subtitle" style="color:#1BC5BD  !important;"> Avg Monthly Invoices</span>
                        <span class="erp-widget__desc" style="color:#1BC5BD  !important;"> {{number_format($data['avg_monthly_invoices'],3)}} </span>
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
                        Hours and Branch wise
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="hours_branch_wise">
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
                        Gross Sale and Profit
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="sale_purchase_ratio">
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
                        Product Group Wise Sale
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div id="product_group_wise">
                    <div class="chart-spinner kt-spinner kt-spinner--sm kt-spinner--brand"></div>
                </div>
            </div>
        </div>
    </div>
</div>
