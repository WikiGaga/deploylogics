@extends('layouts.template')
@section('title', 'Advance Type')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->advance_type_id;
            $name = $data['current']->advance_type_name;
            $short_name=$data['current']->advance_type_short_name;
             $notes = $data['current']->advance_type_notes;
            $status = $data['current']->advance_type_entry_status;
        }
    @endphp

@permission($data['permission']);
<form id="advance_type_form" class="hr_department kt-form" method="post" action="{{ action('PayrDepartment\AdvanceTypeController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label"> Advance Type Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label"> Short Name: </label>
                            <div class="col-lg-6">
                                <input type="text" name="short_name" value="{{isset($short_name)?$short_name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>
                        <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Notes:</label>
                        <div class="col-lg-6">
                            <textarea type="text" name="notes" maxlength="250"  class="form-control erp-form-control-sm" rows="4" >{{ isset($notes)?$notes:''}}</textarea>
                        </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="advance_type_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="advance_type_entry_status" checked>
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