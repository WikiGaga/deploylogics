@extends('layouts.layout')
@section('title', 'Leave Policy')
@section('pageCSS')
@endsection

@section('content')
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    if($case == 'new'){
        $date =  date('d-m-Y');
        $year = 0;
        $leave_type_id = 0;
        $leave_policy_dtls = [];
        $data['files'] = [];
    }
    if($case == 'edit'){
        $id = $data['current']->leave_policy_id;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->leave_policy_date))));
        $name = $data['current']->leave_policy_name;
        $year = $data['current']->leave_policy_year;
        $leave_type_id = $data['current']->leave_type_id;
        $leaves_allowed = $data['current']->leaves_allowed;
        $leave_policy_notes = $data['current']->leave_policy_notes;
        $leave_policy_dtls = isset($data['current']->leave_policy_dtls)?$data['current']->leave_policy_dtls:[];
    }
@endphp
@permission($data['permission'])
<form id="leave_policy_form" class="kt-form" method="post" action="{{ action('PayrDepartment\LeavePolicyController@store',isset($id)?$id:'') }}" enctype="multipart/form-data">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="fileupload-tab-nav nav nav-pills pull-right" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab_1" role="tab"><i class="la la-file-text" style="font-size: 32px;"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_2" role="tab"><i class="la la-file-movie-o" style="font-size: 32px;"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1" role="tabpanel">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        Basic Info
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="row form-group-block">
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <label class="col-lg-3 erp-col-form-label">Name:<span class="required">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" value="{{isset($name)?$name:""}}" name="leave_policy_name" class="form-control erp-form-control-sm"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Date:<span class="required">*</span></label>
                                            <div class="col-lg-6">
                                                <div class="input-group date">
                                                    <input type="text" name="leave_policy_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{$date}}" id="kt_datepicker_3" />
                                                    <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group-block">
                                    <div class="col-lg-4">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Select Year:<span class="required">*</span></label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm leave_policy_year" name="leave_policy_year">
                                                        <option value="0">Select</option>
                                                        <option value="2021" {{$year=='2021'?"selected":""}}>2021</option>
                                                        <option value="2022" {{$year=='2022'?"selected":""}}>2022</option>
                                                        <option value="2023" {{$year=='2023'?"selected":""}}>2023</option>
                                                        <option value="2024" {{$year=='2024'?"selected":""}}>2024</option>
                                                        <option value="2025" {{$year=='2025'?"selected":""}}>2025</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Leave Type:<span class="required">*</span></label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm leave_type_id" name="leave_type_id">
                                                        <option value="0">Select</option>
                                                        @foreach($data['leave_type'] as $leave_type)
                                                            <option value="{{$leave_type->leave_type_id}}" {{$leave_type_id == $leave_type->leave_type_id?"selected":""}}>{{ucfirst(strtolower($leave_type->leave_type_name))}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Leaves Allowed:<span class="required">*</span></label>
                                            <div class="col-lg-6">
                                                <input type="text" value="{{isset($leaves_allowed)?$leaves_allowed:""}}" name="leaves_allowed" class="form-control erp-form-control-sm"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group-block">
                                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                                    <div class="col-lg-10">
                                        <textarea type="text" rows="2" name="leave_policy_notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($leave_policy_notes)?$leave_policy_notes:""}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- Basic info--}}
                        <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                            Criteria
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    @php
                                        $religion_col = [];
                                        $grade_col = [];
                                        $designation_col = [];
                                        $department_col = [];
                                    @endphp
                                    @foreach($leave_policy_dtls as $selected)
                                        @if($selected->criteria_tag_type == 'religion')
                                            @php array_push($religion_col,$selected->criteria_tag_value_id); @endphp
                                        @endif
                                        @if($selected->criteria_tag_type == 'grade')
                                            @php array_push($grade_col,$selected->criteria_tag_value_id); @endphp
                                        @endif
                                        @if($selected->criteria_tag_type == 'designation')
                                            @php array_push($designation_col,$selected->criteria_tag_value_id); @endphp
                                        @endif
                                        @if($selected->criteria_tag_type == 'department')
                                            @php array_push($department_col,$selected->criteria_tag_value_id); @endphp
                                        @endif
                                    @endforeach

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row form-group-block">
                                                <label class="col-lg-4 erp-col-form-label">Religion:</label>
                                                <div class="col-lg-8">
                                                    <div class="erp-select2 erp-multiselect">
                                                        <select class="form-control kt-select2 erp-form-control-sm criteria_religion_id" multiple name="criteria[religion][]">
                                                            <option value="">Select</option>
                                                            @foreach($data['religion'] as $religion)
                                                                <option value="{{$religion->religion_id}}" {{ (in_array($religion->religion_id, $religion_col)) ? 'selected' : '' }}>{{ucfirst(strtolower($religion->religion_name))}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group-block">
                                                <label class="col-lg-4 erp-col-form-label">Grade:</label>
                                                <div class="col-lg-8">
                                                    <div class="erp-select2 erp-multiselect">
                                                        <select class="form-control kt-select2 erp-form-control-sm criteria_grade_id" multiple name="criteria[grade][]">
                                                            <option value="">Select</option>
                                                            @foreach($data['grade'] as $grade)
                                                                <option value="{{$grade->grade_id}}" {{ (in_array($grade->grade_id, $grade_col)) ? 'selected' : '' }}>{{ucwords(strtolower($grade->grade_name))}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group-block">
                                                <label class="col-lg-4 erp-col-form-label">Designation:</label>
                                                <div class="col-lg-8">
                                                    <div class="erp-select2 erp-multiselect">
                                                        <select class="form-control kt-select2 erp-form-control-sm criteria_grade_id" multiple name="criteria[designation][]">
                                                            <option value="">Select</option>
                                                            @foreach($data['designation'] as $designation)
                                                                <option value="{{$designation->designation_id}}" {{ (in_array($designation->designation_id, $designation_col)) ? 'selected' : '' }}>{{ucwords(strtolower($designation->designation_name))}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group-block">
                                                <label class="col-lg-4 erp-col-form-label">Department:</label>
                                                <div class="col-lg-8">
                                                    <div class="erp-select2 erp-multiselect">
                                                        <select class="form-control kt-select2 erp-form-control-sm criteria_grade_id" multiple name="criteria[department][]">
                                                            <option value="">Select</option>
                                                            @foreach($data['department'] as $department)
                                                                <option value="{{$department->department_id}}" {{ (in_array($department->department_id, $department_col)) ? 'selected' : '' }}>{{ucwords(strtolower($department->department_name))}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- criteria portlet--}}
                    </div>{{-- general info--}}
                    <div class="tab-pane" id="tab_2" role="tabpanel">
                        @include('common.upload_documents')
                    </div>{{-- upload documents --}}
                </div>
            </div>
        </div>{{-- main portlet--}}
    </div>{{-- main container--}}
</form>
@endpermission
@endsection

@section('pageJS')
@endsection
@section('customJS')
<script src="{{ asset('js/pages/js/payr-department/leave-policy.js') }}" type="text/javascript"></script>

@yield('customJS2')
@endsection
