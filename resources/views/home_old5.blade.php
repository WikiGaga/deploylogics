@extends('layouts.dashboard')
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
                                    <input readonly type="text" id="dashboard_from_date" class="form-control erp-form-control-sm kt_datepicker_3" value="{{$data['start_date']}}" title="{{$data['start_date']}}"/>
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
                                    <input readonly type="text" id="dashboard_to_date" class="form-control erp-form-control-sm kt_datepicker_3" value="{{$data['today']}}" title="{{$data['today']}}"/>
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
        <div class="row kt-margin-b-15">
            @if(isset($data['badges1_current_month_sale']))
                @php $badge = $data['badges1_current_month_sale']; @endphp
                <div class="col-lg-3" style="">
                    @if(!empty($badge['dash_widget_badge_bg_img']))
                        @php
                            $images = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                        @endphp
                    @endif
                    <div class="kt-portlet" style="background-repeat: no-repeat;
                        background-position: right top;
                        background-size: 30% auto;
                        background-image:url('{{isset($images)?$images:""}}');
                        background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                        <div class="kt-portlet__body">
                            <div class="kt-widget1 kt-widget1--fit">
                                <div class="erp-widget__item">
                            <span class="erp-widget__icon">
                                {!! $badge['dash_widget_badge_svg'] !!}
                            </span>
                                    <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{number_format($badge['total_count'],3)}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($data['badges1_current_week_sale']))
                @php $badge = $data['badges1_current_week_sale']; @endphp
                <div class="col-lg-3" style="">
                    @if(!empty($badge['dash_widget_badge_bg_img']))
                        @php
                            $images = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                        @endphp
                    @endif
                    <div class="kt-portlet" style="background-repeat: no-repeat;
                        background-position: right top;
                        background-size: 30% auto;
                        background-image:url('{{isset($images)?$images:""}}');
                        background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                        <div class="kt-portlet__body">
                            <div class="kt-widget1 kt-widget1--fit">
                                <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            {!! $badge['dash_widget_badge_svg'] !!}
                        </span>
                                    <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{number_format($badge['total_count'],3)}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($data['badges1_today_sale']))
                @php $badge = $data['badges1_today_sale']; @endphp
                <div class="col-lg-3" style="">
                    @if(!empty($badge['dash_widget_badge_bg_img']))
                        @php
                            $images = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                        @endphp
                    @endif
                    <div class="kt-portlet" style="background-repeat: no-repeat;
                        background-position: right top;
                        background-size: 30% auto;
                        background-image:url('{{isset($images)?$images:""}}');
                        background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                        <div class="kt-portlet__body">
                            <div class="kt-widget1 kt-widget1--fit">
                                <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            {!! $badge['dash_widget_badge_svg'] !!}
                        </span>
                                    <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{number_format($badge['total_count'],3)}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($data['badges1_current_year_sale']))
                    @php $badge = $data['badges1_current_year_sale']; @endphp
                    <div class="col-lg-3" style="">
                        @if(!empty($badge['dash_widget_badge_bg_img']))
                            @php
                                $images = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                            @endphp
                        @endif
                        <div class="kt-portlet" style="background-repeat: no-repeat;
                            background-position: right top;
                            background-size: 30% auto;
                            background-image:url('{{isset($images)?$images:""}}');
                            background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                            <div class="kt-portlet__body">
                                <div class="kt-widget1 kt-widget1--fit">
                                    <div class="erp-widget__item">
                            <span class="erp-widget__icon">
                                {!! $badge['dash_widget_badge_svg'] !!}
                            </span>
                                        <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                        <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{number_format($badge['total_count'],3)}} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
        </div>
        <!--End::Section-->
        <div class="row">
            <div class="col-lg-12">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Month and Year Wise Sale Comparison
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="mixed_chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <div class="row">
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Monthly Sale Branch Wise
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
                                Top 5 Item Sales
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="top_item_sales"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <div class="row">
            @if(isset($data['new_products']))
                @php $badge = $data['new_products']; @endphp
                <div class="col-lg-3" style="">
                    @if(!empty($badge['dash_widget_badge_bg_img']))
                        @php
                            $images2 = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                        @endphp
                    @endif
                    <div class="kt-portlet" style="background-repeat: no-repeat;
                        background-position: right top;
                        background-size: 30% auto;
                        background-image:url('{{isset($images2)?$images2:""}}');
                        background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                        <div class="kt-portlet__body">
                            <div class="kt-widget1 kt-widget1--fit">
                                <div class="erp-widget__item">
                            <span class="erp-widget__icon">
                                {!! $badge['dash_widget_badge_svg'] !!}
                            </span>
                                    <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{number_format($badge['total_count'],3)}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($data['new_customers']))
                    @php $badge = $data['new_customers']; @endphp
                    <div class="col-lg-3" style="">
                        @if(!empty($badge['dash_widget_badge_bg_img']))
                            @php
                                $images2 = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                            @endphp
                        @endif
                        <div class="kt-portlet" style="background-repeat: no-repeat;
                            background-position: right top;
                            background-size: 30% auto;
                            background-image:url('{{isset($images2)?$images2:""}}');
                            background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                            <div class="kt-portlet__body">
                                <div class="kt-widget1 kt-widget1--fit">
                                    <div class="erp-widget__item">
                            <span class="erp-widget__icon">
                                {!! $badge['dash_widget_badge_svg'] !!}
                            </span>
                                        <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                        <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{number_format($badge['total_count'],3)}} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @if(isset($data['avg_daily_invoices']))
                @php $badge = $data['avg_daily_invoices']; @endphp
                <div class="col-lg-3" style="">
                    @if(!empty($badge['dash_widget_badge_bg_img']))
                        @php
                            $images2 = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                        @endphp
                    @endif
                    <div class="kt-portlet" style="background-repeat: no-repeat;
                        background-position: right top;
                        background-size: 30% auto;
                        background-image:url('{{isset($images2)?$images2:""}}');
                        background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                        <div class="kt-portlet__body">
                            <div class="kt-widget1 kt-widget1--fit">
                                <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            {!! $badge['dash_widget_badge_svg'] !!}
                        </span>
                                    <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{isset($badge['total_count']->avg)?number_format($badge['total_count']->avg,3):0}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($data['avg_monthly_invoices']))
                @php $badge = $data['avg_monthly_invoices']; @endphp
                <div class="col-lg-3" style="">
                    @if(!empty($badge['dash_widget_badge_bg_img']))
                        @php
                            $images2 = '/assets/images/'. $badge['dash_widget_badge_bg_img'];
                        @endphp
                    @endif
                    <div class="kt-portlet" style="background-repeat: no-repeat;
                        background-position: right top;
                        background-size: 30% auto;
                        background-image:url('{{isset($images2)?$images2:""}}');
                        background-color:{{ $badge['dash_widget_badge_bg_color'] }};">
                        <div class="kt-portlet__body">
                            <div class="kt-widget1 kt-widget1--fit">
                                <div class="erp-widget__item">
                        <span class="erp-widget__icon">
                            {!! $badge['dash_widget_badge_svg'] !!}
                        </span>
                                    <span class="erp-widget__subtitle" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{ $badge['dash_widget_badge_name'] }}</span>
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{isset($badge['total_count']->avg)?number_format($badge['total_count']->avg,3):0}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!--End::Section-->
        <div class="row">
            <div class="col-lg-12">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Hours and Branches wise
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="mixed_chart_2"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::Section-->
        <div class="row">
            <div class="col-lg-4">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Sale Purchase Ratio
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="donut_chart_2"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Top 5 Customer
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="TopCustomers"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Monthly Sale Target Branch Wise
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar"></div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div id="kt_mixed_widget_14_chart"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- end:: Content -->
@endsection
@endpermission

@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')

    <script>
            @if(isset($data['branch_month_wise_sale']['Xaxis']) && isset($data['branch_month_wise_sale']['series']))
        var branch_month_wise_sale_Xaxis = <?php echo json_encode($data['branch_month_wise_sale']['Xaxis']); ?>;
        var branch_month_wise_sale_series = <?php echo json_encode($data['branch_month_wise_sale']['series']); ?>;
            @endif
            @if(isset($data['top_item_sales']['Xaxis']) && isset($data['top_item_sales']['Yaxis']) && isset($data['top_item_sales']['series']))
        var top_item_sales_Xaxis = <?php echo json_encode($data['top_item_sales']['Xaxis']); ?>;
        var top_item_sales_Yaxis = <?php echo json_encode($data['top_item_sales']['Yaxis']); ?>;
        var top_item_sales_series = <?php echo json_encode($data['top_item_sales']['series']); ?>;
            @endif
            @if(isset($data['graph']['Xaxis']) && isset($data['graph']['Yaxis']) && isset($data['graph']['series']))
        var graph_Xaxis = <?php echo json_encode($data['graph']['Xaxis']); ?>;
        var graph_Yaxis = <?php echo json_encode($data['graph']['Yaxis']); ?>;
        var graph_series = <?php echo json_encode($data['graph']['series']); ?>;
            @endif
            @if(isset($data['branches_sale']['Xaxis']) && isset($data['branches_sale']['Yaxis']) && isset($data['branches_sale']['series']))
        var branches_sale_Xaxis = <?php echo json_encode($data['branches_sale']['Xaxis']); ?>;
        var branches_sale_Yaxis = <?php echo json_encode($data['branches_sale']['Yaxis']); ?>;
        var branches_sale_series = <?php echo json_encode($data['branches_sale']['series']); ?>;
            @endif
            @if(isset($data['sale_purchase']['Xaxis']) && isset($data['sale_purchase']['Yaxis']) && isset($data['sale_purchase']['series']))
        var sale_purchase_Xaxis = <?php echo json_encode($data['sale_purchase']['Xaxis']); ?>;
        var sale_purchase_Yaxis = <?php echo json_encode($data['sale_purchase']['Yaxis']); ?>;
        var sale_purchase_series = <?php echo json_encode($data['sale_purchase']['series']); ?>;
            @endif
            @if(isset($data['top_customers']['Xaxis']) && isset($data['top_customers']['Yaxis']) && isset($data['top_customers']['series']))
        var top_customers_Xaxis = <?php echo json_encode($data['top_customers']['Xaxis']); ?>;
        var top_customers_Yaxis = <?php echo json_encode($data['top_customers']['Yaxis']); ?>;
        var top_customers_series = <?php echo json_encode($data['top_customers']['series']); ?>;
            @endif
            @if(isset($data['radial_bar']['Xaxis']) && isset($data['radial_bar']['Yaxis']) && isset($data['radial_bar']['series']))
        var radial_bar_Xaxis = <?php echo json_encode($data['radial_bar']['Xaxis']); ?>;
        var radial_bar_Yaxis = <?php echo json_encode($data['radial_bar']['Yaxis']); ?>;
        var radial_bar_series = <?php echo json_encode($data['radial_bar']['series']); ?>;
            @endif
            @if(isset($data['donut_chart']['Xaxis']) && isset($data['donut_chart']['Yaxis']) && isset($data['donut_chart']['series']))
        var donut_chart_Xaxis = <?php echo json_encode($data['donut_chart']['Xaxis']); ?>;
        var donut_chart_Yaxis = <?php echo json_encode($data['donut_chart']['Yaxis']); ?>;
        var donut_chart_series = <?php echo json_encode($data['donut_chart']['series']); ?>;
        @endif
    </script>
    <script src="/assets/chart_apex/apexcharts.js" type="text/javascript"></script>

    <script src="/assets/chart_apex/apexcharts_func.js" type="text/javascript"></script>
@endsection
