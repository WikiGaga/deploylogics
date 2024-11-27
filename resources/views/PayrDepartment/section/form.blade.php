@extends('layouts.template')
@section('title', 'Section')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->section_id;
            $department_id = $data['current']->department_id;
            $name = $data['current']->section_name;
            $status = $data['current']->section_entry_status;
        }
    @endphp

    @permission($data['permission']);
<form id="section_form" class="hr_department kt-form" method="post" action="{{ action('PayrDepartment\SectionController@store', isset($id)?$id:"") }}">
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
                    <div class="form-group row">
                        <label class="col-lg-3  erp-col-form-label">Department:</label>
                        <div class="col-lg-6">
                            <div class="erp-select2">
                                <select class="form-control erp-form-control-sm kt-select2" name="department_id">
                                    <option value="0">Select</option>
                                    @foreach($data['department'] as $department)
                                        @php  $department_id = isset($department_id)?$department_id:''@endphp
                                        <option value="{{$department->department_id}}" {{$department->department_id==$department_id?'selected':''}}>{{$department->department_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label"> Section Name: <span class="required">*</span></label>
                        <div class="col-lg-6">
                            <input type="text" name="name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Status:</label>
                        <div class="col-lg-6">
                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                <label>
                                    @if($case == 'edit')
                                        @php $entry_status = isset($status)?$status:""; @endphp
                                        <input type="checkbox" name="section_entry_status" {{$entry_status==1?"checked":""}}>
                                    @else
                                        <input type="checkbox" name="section_entry_status" checked>
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