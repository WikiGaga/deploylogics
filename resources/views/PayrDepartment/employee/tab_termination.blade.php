@php
    if($case == 'new'){
        $termination_type_id = 0;
        $termination_status_id = 0;
    }
    if($case == 'edit'){
        $termination_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$current->employee_termination_date))));
        $termination_type_id = $current->termination_type_id;
        $leaving_reason = $current->employee_leaving_reason;
        $termination_status_id = $current->termination_status_id;
    }
@endphp

<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Termination Date:</label>
            <div class="col-lg-6">
                <div class="input-group date">
                    <input type="text" name="employee_termination_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($termination_date)?$termination_date:$today}}" id="kt_datepicker_3"/>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Termination Type:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="termination_type_id">
                        <option value="0">Select</option>
                        @foreach($data['termination_type'] as $key=>$termination_type)
                            <option value="{{$key}}" {{ $termination_type_id == $key ? 'selected' : '' }}>{{$termination_type}}</option>
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
            <label class="col-lg-6 erp-col-form-label">Leaving Reason:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($leaving_reason)?$leaving_reason:""}}" name="employee_leaving_reason" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Termination Status:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="termination_status_id">
                        <option value="0">Select</option>
                        @foreach($data['termination_status'] as $key=>$termination_status)
                            <option value="{{$key}}" {{ $termination_status_id == $key ? 'selected' : '' }}>{{$termination_status}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /row --}}