@extends('layouts.template')
@section('title', 'Graph Bar')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
        }
        if($case == 'edit'){
            $id = $data['current']->dash_widget_graph_id;
            $name = $data['current']->dash_widget_graph_name;
            $case_name = $data['current']->dash_widget_case_name;
            $y_axis = $data['current']->y_axis;
            $x_axis = explode(',',$data['current']->x_axis);
            $x_axis_titles_qry = $data['current']->x_axis_titles_qry;
            $qrys = $data['qry'];
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="dashboard_graph_bar_form" class="erp_form_validation kt-form" method="post" action="{{action('Dashboard\DashboardStudioGraphBar@store',isset($id)?$id:'')}}">
     @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-md-6">
                                <div class="form-group-block row">
                                    <label class="col-lg-5 erp-col-form-label"> Case Name:<span class="required">* </span></label>
                                    <div class="col-lg-7">
                                        <input type="text" name="widget_case_name" maxlength="100" value="{{isset($case_name)?$case_name:''}}" class="form-control erp-form-control-sm" >
                                    </div>
                                </div>
                                <div class="form-group-block row">
                                    <label class="col-lg-5 erp-col-form-label"> Widget Name:<span class="required">* </span></label>
                                    <div class="col-lg-7">
                                        <input type="text" name="widget_name" maxlength="100" value="{{isset($name)?$name:''}}" class="form-control erp-form-control-sm" >
                                    </div>
                                </div>
                                <div class="form-group-block row">
                                    <label class="col-lg-5 erp-col-form-label">Y_AXIS:<span class="required">* </span></label>
                                    <div class="col-lg-7">
                                        <input type="text" name="y_axis" maxlength="100" value="{{isset($y_axis)?$y_axis:''}}" class="form-control erp-form-control-sm">
                                    </div>
                                </div>
                                <div class="form-group-block row">
                                    <label class="col-lg-5 erp-col-form-label">X_AXIS:<span class="required">* </span></label>
                                    <div class="col-lg-7">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2" id="x_axis" name="x_axis[]" multiple>
                                            @if($case == 'edit')
                                                @foreach($x_axis as $axis)
                                                <option value="{{ $axis }}" selected>{{$axis}}</option>
                                                @endforeach
                                            @else
                                            @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-block row">
                                    <label class="col-lg-5 erp-col-form-label">X_Axis Title Query:<span class="required">* </span></label>
                                    <div class="col-lg-7">
                                        <textarea type="text" rows="2" maxlength="250" name="x_axis_titles_qry" class="form-control erp-form-control-sm">{{isset($x_axis_titles_qry)?$x_axis_titles_qry:''}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="x_axis_values_query">
                                    <div data-repeater-list="x_axis_values_query">
                                    @if($case == 'edit')
                                        @if(isset($qrys))
                                            @foreach($qrys as $qry)
                                                @if(isset($qry))
                                                    <div data-repeater-item class="x_axis_values_query">
                                                        <div class="form-group-block row">
                                                            <label class="col-lg-3 erp-col-form-label">X_Axis Values Query:<span class="required">* </span></label>
                                                            <div class="col-lg-7">
                                                                <textarea type="text" rows="3" maxlength="250" name="x_axis_values_qry" class="form-control erp-form-control-sm">{{$qry}}</textarea>
                                                            </div>
                                                            <div class="col-lg-2 text-right">
                                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                                                                    <i class="la la-minus-circle"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @else
                                        <div data-repeater-item class="x_axis_values_query">
                                            <div class="form-group-block row">
                                                <label class="col-lg-3 erp-col-form-label">X_Axis Values Query:<span class="required">* </span></label>
                                                <div class="col-lg-7">
                                                    <textarea type="text" rows="3" maxlength="250" name="x_axis_values_qry" class="form-control erp-form-control-sm"></textarea>
                                                </div>
                                                <div class="col-lg-2 text-right">
                                                    <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger">
                                                        <i class="la la-minus-circle"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    </div>
                                    <div class="row text-right">
                                        <div class="col-lg-12">
                                            <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                                Add
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
                <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/dashboard-graph-bar.js') }}" type="text/javascript"></script>
    <script>
        // Class definition
        var KTFormRepeater = function() {
            var kt_repeater_report_filter = function() {
                $('#x_axis_values_query').repeater({
                    initEmpty: false,
                    show: function () {
                        $(this).slideDown();
                    },
                    hide: function (deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                });
            }
            return {
                // public functions
                init: function() {
                    kt_repeater_report_filter();
                }
            };
        }();
        jQuery(document).ready(function() {
            KTFormRepeater.init();

        });
    </script>
@endsection
