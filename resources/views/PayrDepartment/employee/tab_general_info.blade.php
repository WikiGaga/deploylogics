@php
    if($case == 'new'){
        $gender_id = 0;
        $blood_group_id = 0;
        $marital_status_id = 0;
        $nationality_id = 0;
        $religion_id = 0;
        $language_id = 0;
    }
    if($case == 'edit'){
        $gender_id = $current->gender_id;
        $date_of_birth = date('d-m-Y', strtotime(trim(str_replace('/','-',$current->employee_date_of_birth))));
        $blood_group_id = $current->blood_group_id;
        $marital_status_id = $current->marital_status_id;
        $nationality_id = $current->nationality_id;
        $religion_id = $current->religion_id;
        $man_power_no = $current->employee_man_power_no;
        $id_no = $current->employee_id_no;
        $cpr_no = $current->employee_cpr_no;
        $eobi_no = $current->employee_eobi_no;
        $language_id = 0;
    }
@endphp
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Gender:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="gender_id">
                        <option value="0">Select</option>
                        @foreach($data['gender'] as $gender)
                            <option value="{{$gender->gender_id}}" {{$gender_id == $gender->gender_id?"selected":""}}>{{ucfirst(strtolower($gender->gender_name))}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Nationality:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="nationality_id">
                        <option value="0">Select</option>
                        @foreach($data['nationality'] as $nationality)
                            <option value="{{$nationality->nationality_id}}" {{ $nationality->nationality_id == $nationality_id ? 'selected' : '' }}>{{ucfirst(strtolower($nationality->nationality_name))}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Religion:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm religion" name="religion_id">
                        <option value="0">Select</option>
                        @foreach($data['religion'] as $religion)
                            <option value="{{$religion->religion_id}}" {{ $religion->religion_id == $religion_id ? 'selected' : '' }}>{{ucfirst(strtolower($religion->religion_name))}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Blood Group:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="blood_group_id">
                        <option value="0">Select</option>
                        @foreach($data['blood_group'] as $key=>$blood_group)
                            <option value="{{$key}}" {{$blood_group_id == $key?"selected":""}}>{{$blood_group}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Marital Status:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="marital_status_id">
                        <option value="0">Select</option>
                        @foreach($data['martial_status'] as $key=>$martial_status)
                            <option value="{{$key}}" {{$marital_status_id == $key?"selected":""}}>{{$martial_status}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Date of Birth:</label>
            <div class="col-lg-6">
                <div class="input-group date">
                    <input type="text" name="employee_date_of_birth" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date_of_birth)?$date_of_birth:$today}}" id="kt_datepicker_3"/>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Man Power No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($man_power_no)?$man_power_no:""}}" name="employee_man_power_no" class="form-control erp-form-control-sm validNumber">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">ID No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($id_no)?$id_no:""}}" name="employee_id_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">CPR No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($cpr_no)?$cpr_no:""}}" name="employee_cpr_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">EOBI:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($eobi_no)?$eobi_no:""}}" name="employee_eobi_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Language Known:</label>
            <div class="col-lg-6">
                <div class="erp-select2 erp-multiselect">
                    @php $col = []; @endphp
                    @if(isset($current))
                    @foreach($current->language as $language)
                        @php array_push($col,$language->language_id); @endphp
                    @endforeach
                    @endif
                    <select class="form-control kt-select2 erp-form-control-sm" multiple name="language_known[]">
                        <option value="0">Select</option>
                        @foreach($data['language'] as $language)
                            <option value="{{$language->language_id}}" {{ in_array($language->language_id,$col) ? 'selected' : '' }}>{{ucfirst(strtolower($language->language_name))}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /row --}}
