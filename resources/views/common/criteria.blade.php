@php
    if($case == 'edit'){
        $criterias =  $data['current']->leave_policy_dtls;
    }
@endphp

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
            $data = [];
            $data['religion'] = \App\Models\TblHrReligion::get();
            $data['grade'] = \App\Models\TblHrGrade::get();
            $data['designation'] = \App\Models\TblHrDesignation::get();
            $data['department'] = \App\Models\TblHrDepartment::get();

            
        @endphp

        @if(isset($criterias))
            @foreach($criterias as $selected)
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
        @endif

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