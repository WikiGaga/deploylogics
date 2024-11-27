@php
    if($case == 'new'){
        $document_type_id = 0;
        $sponsorship_type_id = 0;
        $branch_contract_id = 0;
        $branch_working_id = 0;
    }
    if($case == 'edit'){
        $branch_contract_id = $current->branch_contract_id;
        $branch_working_id = $current->branch_working_id;
        $sponsorship_type_id = $current->sponsorship_type_id;
        $sponsorship_name = $current->employee_sponsorship_name;
        $sponsorship_no = $current->employee_sponsorship_no;
        $approval_authority_id = $current->employee_approval_authority_id;
        $probation_upto = date('d-m-Y', strtotime(trim(str_replace('/','-',$current->employee_probation_upto))));
        $contract_renewal_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$current->employee_contract_renewal_date))));
        $contract_renewal_upto = date('d-m-Y', strtotime(trim(str_replace('/','-',$current->employee_contract_renewal_upto))));
    }
@endphp

<div class="row">
    <div class="col-lg-12 text-right">
        <div class="data_entry_header">
            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
            <div class="dropdown dropdown-inline">
                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                    <i class="flaticon-more" style="color: #666666;"></i>
                </button>
                @php
                    $headings = ['Sr No','Date','Rating/Grade','Designation','Employee Type','Department'];
                @endphp
                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                    @foreach($headings as $key=>$heading)
                        <li >
                            <label>
                                <input value="{{$key}}" type="checkbox" checked> {{$heading}}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="form-group-block">
    <div class="erp_form___block">
        <div class="table-scroll form_input__block">
            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline" prefix="emp">
                <thead class="erp_form__grid_header">
                <tr>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Sr.</div>
                        <div class="erp_form__grid_th_input">
                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                            <input id="employee_emp_sr_no" readonly type="hidden" class="employee_emp_sr_no form-control erp-form-control-sm handle">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Date</div>
                        <div class="input-group date erp_form__grid_th_input">
                            <input type="text" name="employee_joining_date" class="form-control erp-form-control-sm c-date-p kt_datepicker" value="{{isset($joining_date)?$joining_date:$today}}" id="employee_joining_date" autocomplete="off">
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Rating/Grade</div>
                        <div class="erp-select2 erp_form__grid_th_input">
                            <select class="form-control kt-select2 erp-form-control-sm grade text-left" name="grade_id" id="grade_id">
                                <option value="0">Select</option>
                                @foreach($data['grade'] as $grade)
                                    <option value="{{$grade->grade_id}}">{{ucwords(strtolower($grade->grade_name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Designation</div>
                        <div class="erp-select2 erp_form__grid_th_input">
                            <select class="form-control kt-select2 erp-form-control-sm designation" name="designation_id" id="designation_id">
                                <option value="0">Select</option>
                                @foreach($data['designation'] as $designation)
                                    <option value="{{$designation->designation_id}}">{{ucfirst(strtolower($designation->designation_name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Employee Type</div>
                        <div class="erp-select2 erp_form__grid_th_input">
                            <select class="form-control kt-select2 erp-form-control-sm" name="employee_type_id" id="employee_type_id">
                                <option value="0">Select</option>
                                @foreach($data['employee_type'] as $employee_type)
                                    <option value="{{$employee_type->employee_type_id}}">{{ucfirst(strtolower($employee_type->employee_type_name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Department</div>
                        <div class="erp-select2 erp_form__grid_th_input">
                            <select class="form-control kt-select2 erp-form-control-sm" name="department_id" id="department_id">
                                <option value="0">Select</option>
                                @foreach($data['department'] as $department)
                                    <option value="{{$department->department_id}}">{{ucfirst(strtolower($department->department_name))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </th>
                    <th scope="col" class="col-div-8">
                        <div class="erp_form__grid_th_title">Action</div>
                        <div class="erp_form__grid_th_btn">
                            <button type="button" id="addData" class="addData tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                <i class="la la-plus"></i>
                            </button>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody class="erp_form__grid_body">
                @if(isset($data['current']->employment))
                    @foreach($data['current']->employment as $employment)
                        <tr>
                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                <input type="text" value="{{$employment->employee_employment_sr_no}}" name="emp[{{ $loop->iteration }}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                <input type="hidden" name="emp[{{ $loop->iteration }}][employee_employment_id]" data-id="employee_employment_id" value="{{$employment->employee_employment_id}}" class="employee_educational_id form-control erp-form-control-sm handle" readonly>
                            </td>
                            <td>
                                @php $joining_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$employment->employee_date))));  @endphp
                                <input type="text" name="emp[{{ $loop->iteration }}][employee_joining_date]" data-id="employee_joining_date" data-url="" value="{{ $joining_date }}" title="{{ $joining_date }}" class="form-control erp-form-control-sm employee_joining_date tb_moveIndex " autocomplete="off">
                            </td>
                            <td>
                                <div class="erp-select2">
                                    <select class="grade_id tb_moveIndex form-control erp-form-control-sm" name="emp[{{ $loop->iteration }}][grade_id]" data-select2-id="grade_id" tabindex="-1" aria-hidden="true">
                                        <option value="0">Select</option>
                                        @foreach($data['grade'] as $grade)
                                            @php $grade_id = isset($grade->grade_id) ? $grade->grade_id : 0 ; @endphp
                                            <option value="{{$grade->grade_id}}" {{ $employment->grade_id == $grade_id ? 'selected' : '' }}>{{ucwords(strtolower($grade->grade_name))}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="erp-select2">
                                    <select class="designation_id tb_moveIndex form-control erp-form-control-sm" name="emp[{{ $loop->iteration }}][designation_id]" data-select2-id="designation_id" tabindex="-1" aria-hidden="true">
                                        <option value="0">Select</option>
                                        @foreach($data['designation'] as $designation)
                                            @php $designation_id = isset($designation->designation_id) ? $designation->designation_id : 0 ; @endphp
                                            <option value="{{$designation->designation_id}}" {{ $employment->designation_id == $designation_id ? 'selected' : '' }}>{{ucfirst(strtolower($designation->designation_name))}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="erp-select2">
                                    <select class="employee_type_id tb_moveIndex form-control erp-form-control-sm" name="emp[{{ $loop->iteration }}][employee_type_id]" data-select2-id="employee_type_id" tabindex="-1" aria-hidden="true">
                                        <option value="0">Select</option>
                                        @foreach($data['employee_type'] as $employee_type)
                                            @php $employee_type_id = isset($employee_type->employee_type_id) ? $employee_type->employee_type_id : 0 ; @endphp
                                            <option value="{{$employee_type->employee_type_id}}" {{ $employment->employee_type_id == $employee_type_id ? 'selected' : '' }}>{{ucfirst(strtolower($employee_type->employee_type_name))}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="erp-select2">
                                    <select class="department_id tb_moveIndex form-control erp-form-control-sm" name="emp[{{ $loop->iteration }}][department_id]" data-select2-id="department_id" tabindex="-1" aria-hidden="true">
                                        <option value="0">Select</option>
                                        @foreach($data['department'] as $department)
                                            @php $department_id = isset($department->department_id) ? $department->department_id : 0 ; @endphp
                                            <option value="{{$department->department_id}}" {{ $employment->department_id == $department_id ? 'selected' : '' }}>{{ucfirst(strtolower($department->department_name))}}</option>
                                        @endforeach                            
                                    </select>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<hr>
{{--Employment Tab - part 2--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Branch Contract:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="branch_contract_id">
                        <option value="0">Select</option>
                        @foreach($data['branch'] as $branch)
                            <option value="{{$branch->branch_id}}" {{ $branch->branch_id == $branch_contract_id ? 'selected' : '' }}>{{ucfirst(strtolower($branch->branch_name))}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Branch Working:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="branch_working_id">
                        <option value="0">Select</option>
                        @foreach($data['branch'] as $branch)
                            <option value="{{$branch->branch_id}}" {{ $branch->branch_id == $branch_working_id ? 'selected' : '' }}>{{ucfirst(strtolower($branch->branch_name))}}</option>
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
            <label class="col-lg-6 erp-col-form-label">Sponsorship Type:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" name="sponsorship_type_id">
                        <option value="0">Select</option>
                        @foreach($data['sponsorship'] as $sponsorship)
                            <option value="{{$sponsorship->sponsorship_id}}" {{ $sponsorship->sponsorship_id == $sponsorship_type_id ? 'selected' : '' }}>{{ucfirst(strtolower($sponsorship->sponsorship_name))}}</option>
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
            <label class="col-lg-6 erp-col-form-label">Sponsor Name:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($sponsorship_name)?$sponsorship_name:""}}" name="employee_sponsorship_name" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Sponsorship No:</label>
            <div class="col-lg-6">
                <input type="text" maxlength="100" value="{{isset($sponsorship_no)?$sponsorship_no:""}}" name="employee_sponsorship_no" class="form-control erp-form-control-sm">
            </div>
        </div>
    </div>
</div>{{-- /row --}}
<div class="row form-group-block d-none">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Probation Upto:</label>
            <div class="col-lg-6">
                <div class="input-group date">
                    <input type="text" name="employee_probation_upto" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($probation_upto)?$probation_upto:$today}}" id="kt_datepicker_3"/>
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
            <label class="col-lg-6 erp-col-form-label">Contract Renewal Date:</label>
            <div class="col-lg-6">
                <div class="input-group date">
                    <input type="text" name="employee_contract_renewal_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($contract_renewal_date)?$contract_renewal_date:$today}}" id="kt_datepicker_3"/>
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
            <label class="col-lg-6 erp-col-form-label">Contract Renewal Upto:</label>
            <div class="col-lg-6">
                <div class="input-group date">
                    <input type="text" name="employee_contract_renewal_upto" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($contract_renewal_upto)?$contract_renewal_upto:$today}}" id="kt_datepicker_3"/>
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
