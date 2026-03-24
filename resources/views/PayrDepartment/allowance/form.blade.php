@extends('layouts.template')
@section('title', 'Allowance Deduction')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $id = '';
            $name = '';
            $type='';
            $status='';
            $is_tax_apply = '';
            $repetition = '';
            }
        if($case == 'edit'){
            $id = $data['current']->id;
            $name = $data['current']->name;
            $type=$data['current']->type;
            $status=$data['current']->status;
            $is_tax_apply = $data['current']->is_tax_apply;
            $repetition = $data['current']->repetition;
        }
    @endphp

@permission($data['permission']);
<form id="hr_department" class="hr_department kt-form" method="post" action="{{ action('PayrDepartment\AllowanceController@store', isset($id)?$id:"") }}">
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
                                <input type="text" name="name" value="{{$name}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Repetition: </label>
                            <div class="col-lg-6">
                                <select class="form-control kt-select2 erp-form-control-sm"  name="repetition">
                                    <option value="Monthly" {{ ($repetition=="Monthly")?"Selected":"" }} >Monthly </option>
                                    <option value="Quarterly" {{ ($repetition=="Quarterly")?"Selected":"" }}>Quarterly </option>
                                    <option value="Biannually" {{ ($repetition=="Biannually")?"Selected":"" }}>Biannually </option>
                                    <option value="Annually" {{ ($repetition=="Annually")?"Selected":"" }}>Annually </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Type: </label>
                            <div class="col-lg-6">
                                <select class="form-control kt-select2 erp-form-control-sm"  name="type">
                                    <option value="Addition" {{ ($repetition=="Addition")?"Selected":"" }} >Addition </option>
                                    <option value="Deduction" {{ ($repetition=="Deduction")?"Selected":"" }}>Deduction </option>
                                    
                                </select>
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Is Tax Apply ?:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $is_allowance = isset($allowance)?$allowance:""; @endphp
                                            <input type="checkbox" name="is_tax_apply" {{$is_tax_apply==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="is_tax_apply" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            <input type="checkbox" name="status" {{$status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="status" checked>
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