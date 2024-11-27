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
        <div class="row kt-margin-b-15">
            @foreach($data['badges'] as $badge)
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
                                    @php
                                        $query = ''.$badge['dash_widget_badge_query'].'';
                                        $count = \Illuminate\Support\Facades\DB::select($query);
                                    @endphp
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{isset($count[0]->count)?$count[0]->count:0}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{--@foreach($data['badges'] as $badge)
                <div class="col-lg-3">
                    <div class="card card-custom bg-primary gutter-b" style="height: 110px;background-color:{{$badge['dash_widget_badge_bg_color']}} !important;">
                        <div class="card-body">
                            <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">
                                {!! $badge['dash_widget_badge_svg'] !!}
                            </span>
                            @php
                                $query = ''.$badge['dash_widget_badge_query'].'';
                                $count = \Illuminate\Support\Facades\DB::select($query);
                            @endphp
                            <div class="text-inverse-primary font-weight-bolder font-size-h2" style="color:{{$badge['dash_widget_badge_color']}} !important;">{{isset($count[0]->count)?$count[0]->count:0}}</div>
                            <a href="#" class="text-inverse-primary font-weight-bold font-size-lg mt-1" style="color:{{$badge['dash_widget_badge_color']}} !important;">{{ $badge['dash_widget_badge_name'] }}</a>
                        </div>
                    </div>
                </div>
            @endforeach--}}
        </div>
        <!--End::Section-->
        <div class="row">
            <div class="col-lg-12">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Sale Comparison
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
                                {{$data['branches_sale']['widget_title']}}
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
        </div>
        <!--End::Section-->
        <div class="row">
            @foreach($data['badges2'] as $badge)
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
                                    @php
                                        $query = ''.$badge['dash_widget_badge_query'].'';
                                        $count = \Illuminate\Support\Facades\DB::select($query);
                                    @endphp
                                    <span class="erp-widget__desc" style="color:{{$badge['dash_widget_badge_color']}} !important;"> {{isset($count[0]->count)?$count[0]->count:0}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
                                {{$data['sale_purchase']['widget_title']}}
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
                                {{$data['top_customers']['widget_title']}}
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
                                Monthly Sale Branch Wise
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
        var graph_Xaxis = <?php echo json_encode($data['graph']['Xaxis']); ?>;
        var graph_Yaxis = <?php echo json_encode($data['graph']['Yaxis']); ?>;
        var graph_series = <?php echo json_encode($data['graph']['series']); ?>;

        var branches_sale_Xaxis = <?php echo json_encode($data['branches_sale']['Xaxis']); ?>;
        var branches_sale_Yaxis = <?php echo json_encode($data['branches_sale']['Yaxis']); ?>;
        var branches_sale_series = <?php echo json_encode($data['branches_sale']['series']); ?>;

        var sale_purchase_Xaxis = <?php echo json_encode($data['sale_purchase']['Xaxis']); ?>;
        var sale_purchase_Yaxis = <?php echo json_encode($data['sale_purchase']['Yaxis']); ?>;
        var sale_purchase_series = <?php echo json_encode($data['sale_purchase']['series']); ?>;

        var top_customers_Xaxis = <?php echo json_encode($data['top_customers']['Xaxis']); ?>;
        var top_customers_Yaxis = <?php echo json_encode($data['top_customers']['Yaxis']); ?>;
        var top_customers_series = <?php echo json_encode($data['top_customers']['series']); ?>;

        var radial_bar_Xaxis = <?php echo json_encode($data['radial_bar']['Xaxis']); ?>;
        var radial_bar_Yaxis = <?php echo json_encode($data['radial_bar']['Yaxis']); ?>;
        var radial_bar_series = <?php echo json_encode($data['radial_bar']['series']); ?>;

        var donut_chart_Xaxis = <?php echo json_encode($data['donut_chart']['Xaxis']); ?>;
        var donut_chart_Yaxis = <?php echo json_encode($data['donut_chart']['Yaxis']); ?>;
        var donut_chart_series = <?php echo json_encode($data['donut_chart']['series']); ?>;
    </script>
    <script src="/assets/chart_apex/apexcharts.js" type="text/javascript"></script>

    <script src="/assets/chart_apex/apexcharts_func.js" type="text/javascript"></script>
@endsection
