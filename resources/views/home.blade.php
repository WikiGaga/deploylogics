@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('pageCSS')
    <style>
        #dashboard_tabs{
            background: #fff;
            padding: 20px 0;
            margin-right: -10px;
            margin-left: -10px;
        }
        #dashboard_tabs>.row{
            margin-right: 0 !important;
            margin-left: 0 !important;
        }
        .fz-32{
            font-size: 32px !important;
        }
        .erp-widget {
            padding: 10px;
            color: #fff;
            background-image: url(/images/erp_widget01_bg.png);
            background-size: cover;
            background-attachment: scroll;
            background-position: 32%;
            background-repeat: no-repeat;
            border-radius: 4px;
            cursor: pointer;
            opacity: 0.4;
        }
        .erp-widget:hover{
            opacity: 1.0;
        }
        .erp-widget--title {
            font-size: 14px;
            font-weight: 400;
            font-family: inherit;
            padding: 3px 6px;
        }
        #erp_dashboard{
            background-color: #2196f3;
        }
        #restaurants_dashboard{
            background-color: #f39521;
        }
        #dashboard_data{
            margin-top: 25px;
        }
    </style>
@endsection
@permission(['dash-view'])
@section('content')
    <!--Begin::Section-->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::Section-->
        <div id="dashboard_tabs">
            <div class="row">
                <div class="col-lg-6">
                    <div class="erp-widget" id="erp_dashboard">
                        <div class="erp-widget--img">
                        <span class="kt-menu__link-icon">
                            <i class="la la-signal fz-32"></i>
                        </span>
                        </div>
                        <div class="erp-widget--title">
                            ERP Dashboard
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="erp-widget" id="restaurants_dashboard">
                        <div class="erp-widget--img">
                        <span class="kt-menu__link-icon">
                            <i class="la la-cutlery fz-32"></i>
                        </span>
                        </div>
                        <div class="erp-widget--title">
                            Restaurants Dashboard
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <div id="dashboard_data" >
            {{--@include('dashboard.dummy.sale')--}}
        </div>
    </div>
    <!-- end:: Content -->
@endsection
@endpermission

@section('pageJS')

@endsection

@section('customJS')
    <script src="/assets/chart_apex/apexcharts.js" type="text/javascript"></script>
    <script src="/js/pages/js/dashboard/home.js" type="text/javascript"></script>
    <script src="/js/pages/js/dashboard/restaurant.js" type="text/javascript"></script>
@endsection
