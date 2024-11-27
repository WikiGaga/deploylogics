@extends('layouts.template')
@section('title', 'Grade')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->grade_id;
            $name = $data['current']->grade_name;
            $short_name = $data['current']->grade_short_name;
            $upper_grade = $data['current']->grade_upper_grade;
            $gross_minimum = $data['current']->grade_min_range;
            $gross_maximum = $data['current']->grade_max_range;
            $status = $data['current']->grade_entry_status;
        }
    @endphp

@permission($data['permission']);
<form id="grade_form" class="hr_department kt-form" method="post" action="{{ action('PayrDepartment\GradeController@store', isset($id)?$id:"") }}">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="kt-portlet__body">

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Grade Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Grade Short Name: </label>
                            <div class="col-lg-6">
                                <input type="text" name="short_name" value="{{isset($short_name)?$short_name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Upper Grade: </label>
                            <div class="col-lg-6">
                                <input type="text" name="upper_grade" value="{{isset($upper_grade)?$upper_grade:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Gross Range Minimum: </label>
                            <div class="col-lg-6">
                                <input type="text" name="gross_minimum" value="{{isset($gross_minimum)?$gross_minimum:""}}" maxlength="100" class=" form-control erp-form-control-sm validNumber">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Grooss Range Maximum: </label>
                            <div class="col-lg-6">
                                <input type="text" name="gross_maximum" value="{{isset($gross_maximum)?$gross_maximum:""}}" maxlength="100" class=" form-control erp-form-control-sm validNumber">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Inactive:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="grade_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="grade_entry_status" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission

@endsection



@section('customJS')
    <script src="{{ asset('js/pages/js/hr_department.js') }}" type="text/javascript"></script>
@endsection