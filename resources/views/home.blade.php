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
        /* Shimmer Loading Styles */
        .shimmer-container {
            display: none;
        }
        .shimmer-container.loading {
            display: block;
        }
        .shimmer-card {
            background: #fff;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .shimmer-small-card {
            background: #fff;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            height: 120px;
            position: relative;
            overflow: hidden;
        }
        .shimmer-small-card::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 15px;
            width: 32px;
            height: 32px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }
        .shimmer-small-title {
            height: 14px;
            width: 60%;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
            margin-top: 55px;
            margin-left: 15px;
        }
        .shimmer-small-value {
            height: 24px;
            width: 50%;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
            margin-top: 10px;
            margin-left: 15px;
        }
        .shimmer-header {
            height: 24px;
            width: 60%;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .shimmer-chart {
            height: 300px;
            width: 100%;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
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
        </div>
        <!-- Shimmer Loading Placeholder -->
        <div id="shimmer_loading" class="shimmer-container">
            <div class="row kt-margin-b-15">
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
            </div>
            <div class="row kt-margin-b-15">
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="shimmer-small-card">
                        <div class="shimmer-small-title"></div>
                        <div class="shimmer-small-value"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="shimmer-card">
                        <div class="shimmer-header"></div>
                        <div class="shimmer-chart"></div>
                    </div>
                </div>
            </div>
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
