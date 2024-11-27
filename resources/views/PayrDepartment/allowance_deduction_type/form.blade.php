@extends('layouts.template')
@section('title', 'Allowance Deduction')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->allowance_deduction_id;
            $name = $data['current']->allowance_deduction_name;
            $short_name=$data['current']->allowance_deduction_short_name;
            $tag_name=$data['current']->allowance_deduction_tag_name;
            $allowance = $data['current']->allowance_deduction_allowance_status;
            $adjust = $data['current']->allowance_deduction_adjust_attendance;
            $status = $data['current']->allowance_deduction_entry_status;
        }
    @endphp

@permission($data['permission']);
<form id="hr_department" class="hr_department kt-form" method="post" action="{{ action('PayrDepartment\AllowanceDeductionController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Allowance/Deduction Type: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Short Name: </label>
                            <div class="col-lg-6">
                                <input type="text" name="short_name" value="{{isset($short_name)?$short_name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Tags Name: </label>
                            <div class="col-lg-6">
                                <input type="text" name="tag_name" value="{{isset($tag_name)?$tag_name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Is Allowance ?:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $is_allowance = isset($allowance)?$allowance:""; @endphp
                                            <input type="checkbox" name="allowance_deduction_allowance" {{$is_allowance==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="allowance_deduction_allowance" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Adjust With Attendance:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $adjustment = isset($adjust)?$adjust:""; @endphp
                                            <input type="checkbox" name="allowance_deduction_adjustment" {{$adjustment==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="allowance_deduction_adjustment" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Inactive status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $inactive_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="allowance_deduction_entry_status" {{$inactive_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="allowance_deduction_entry_status" checked>
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