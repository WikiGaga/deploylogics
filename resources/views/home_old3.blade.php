@extends('layouts.template')
@section('title', 'Dashboard')
@section('pageCSS')
@endsection
@permission(['dash-view'])
@section('content')
    <!--Begin::Section-->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::Section-->
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-light alert-elevate fade show" role="alert">
                    <table width="100%">
                        <tr>
                            <td width="11%" class="text-center">From Date:</td>
                            <td width="12%">
                                <div class="input-group date">
                                    <input readonly type="text" id="dashboard_from_date" class="form-control erp-form-control-sm kt_datepicker_3" value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td width="11%" class="text-center">To Date:</td>
                            <td width="12%">
                                <div class="input-group date">
                                    <input readonly type="text" id="dashboard_to_date" class="form-control erp-form-control-sm kt_datepicker_3" value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td width="11%" class="text-center">Branch:</td>
                            <td width="14%">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="grn_currency">
                                        <option value="0">Select</option>
                                        @foreach($data['branch'] as $branch)
                                        <option value="{{$branch->branch_id}}" {{$branch->branch_id==auth()->user()->branch_id?'selected':''}}>{{$branch->branch_short_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td width="12%" class="text-center"><button type="submit" class="btn dashboard-generate-btn btn-success btn-sm ">Generate</button></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <!--Begin::Section-->
        <div class="row">
            @foreach($data['badges'] as $badge)
                <div class="col-lg-3" style="">
                    <div class="kt-portlet" style="background-repeat: no-repeat;
                            background-position: right top;
                            background-size: 30% auto;
                            background-image:url('{{ $badge['dash_widget_badge_bg_img'] }}');
                            background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                        <div class="kt-portlet__body">
                            <div class="kt-widget1 kt-widget1--fit">
                                <div class="erp-widget__item">
                            <span class="erp-widget__icon">
                                {!! $badge['dash_widget_badge_svg'] !!}
                            </span>
                                    <span class="erp-widget__subtitle"> {{ $badge['dash_widget_badge_name'] }}</span>
                                    @php
                                        $query = ''.$badge['dash_widget_badge_query'].'';
                                        $count = \Illuminate\Support\Facades\DB::select($query);
                                    @endphp
                                    <span class="erp-widget__desc"> {{isset($count[0]->count)?$count[0]->count:0}} {{ $badge['dash_widget_badge_detail'] }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!--End::Section-->
        <!--Begin::Section-->
        <div class="row">
            <div class="col-lg-4">
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head erp-kt-portlet-head2">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Cash and Bank Accounts Balance
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-widget1 kt-widget1--fit">
                            <div class="kt-widget1__item erp-acc-widget__item_head">
                                <div class="kt-widget1__info">
                                    <h3 class="kt-widget1__title fz-20">Cash Balance</h3>
                                </div>
                                <span class="kt-widget1__number kt-font-success">9000.00 Cr</span>
                            </div>
                            <div class="erp-acc-widget__item">
                                <div class="erp-acc-widget__info">
                                    <span class="erp-acc-widget__desc">Awerage Weekly Profit</span>
                                </div>
                                <span class="erp-acc-widget__number">4500.00 Cr</span>
                            </div>
                            <div class="erp-acc-widget__item">
                                <div class="erp-acc-widget__info">
                                    <span class="erp-acc-widget__desc">Awerage Weekly Profit</span>
                                </div>
                                <span class="erp-acc-widget__number">4500.00 Cr</span>
                            </div>
                        </div>
                        <div class="kt-widget1 kt-widget1--fit">
                            <div class="kt-widget1__item erp-acc-widget__item_head">
                                <div class="kt-widget1__info">
                                    <h3 class="kt-widget1__title fz-20">Bank Balance</h3>
                                </div>
                                <span class="kt-widget1__number kt-font-success">7000.00 Cr</span>
                            </div>
                            <div class="erp-acc-widget__item">
                                <div class="erp-acc-widget__info">
                                    <span class="erp-acc-widget__desc">Awerage Weekly Profit</span>
                                </div>
                                <span class="erp-acc-widget__number">3000.00 Cr</span>
                            </div>
                            <div class="erp-acc-widget__item">
                                <div class="erp-acc-widget__info">
                                    <span class="erp-acc-widget__desc">Awerage Weekly Profit</span>
                                </div>
                                <span class="erp-acc-widget__number">4000.00 Cr</span>
                            </div>
                        </div>
                        {{--<div class="kt-widget1 kt-widget1--fit">
                            <div class="kt-widget1__item erp-acc-widget__item_head">
                                <div class="kt-widget1__info">
                                    <h3 class="kt-widget1__title">Imprest</h3>
                                </div>
                                <span class="kt-widget1__number kt-font-success">12500.00 Cr</span>
                            </div>
                            <div class="erp-acc-widget__item">
                                <div class="erp-acc-widget__info">
                                    <span class="erp-acc-widget__desc">Awerage Weekly Profit</span>
                                </div>
                                <span class="erp-acc-widget__number">7800.00 Cr</span>
                            </div>
                            <div class="erp-acc-widget__item">
                                <div class="erp-acc-widget__info">
                                    <span class="erp-acc-widget__desc">Awerage Weekly Profit</span>
                                </div>
                                <span class="erp-acc-widget__number">5000.00 Cr</span>
                            </div>
                        </div>--}}
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Minimum Item Stock Level
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <!--begin: Datatable -->
                        <div class="kt-datatable2" id="minimum_item_stock_level"></div>
                    {{--<div class="kt-datatable size" id="kt_datatable_latest_orders" data-url="{{action('Purchase\ProductController@MaximumItem')}}"></div>--}}
                    <!--end: Datatable -->
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <!--Begin::Section-->
        <div class="row">
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {{$data['graph']['widget_title']}}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="column_chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Area Chart
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="area_chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <!--Begin::Section-->
        <div class="row">
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Line Chart
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="line_chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Area Chart
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="area2_chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <!--Begin::Section-->
        <div class="row">
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Mixed Chart
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="mixed_chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Radial Bar Chart
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="radial_chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <!--Begin::Section-->
        <div class="row">
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Donut Chart
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="donut_chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Pie Chart
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="pie_chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
    </div>
    <!-- end:: Content -->
@endsection
@endpermission

@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="/assets/chart_apex/apexcharts.js" type="text/javascript"></script>
    
    <script>
       var Xaxis = <?php echo json_encode($data['graph']['Xaxis']); ?>;
        var Yaxis = <?php echo json_encode($data['graph']['Yaxis']); ?>;
        var series = <?php echo json_encode($data['graph']['series']); ?>;
    </script>
    <script src="/assets/chart_apex/apexcharts_func.js" type="text/javascript"></script>

@endsection
