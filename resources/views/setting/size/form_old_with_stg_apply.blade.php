@extends('layouts.template')
@section('title', 'Size')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $current_case_id = isset($data['stg']['current_case']->stg_form_cases_id)?$data['stg']['current_case']->stg_form_cases_id:'';
        $current_flow_id = isset($data['stg']['flows']['current']->stg_flows_id)?$data['stg']['flows']['current']->stg_flows_id:'';
         if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->size_id;
            $name = $data['current']->size_name;
            $status = $data['current']->size_entry_status;
        }
    @endphp

    @stgaccess($current_case_id,$current_flow_id)
    @permission($data['permission']);
    <form id="city_form" class="master_form kt-form" method="post" action="{{ action('Setting\SizeController@store', isset($id)?$id:"") }}">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.stag_page_header',['header_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Name: <span class="required">*</span></label>
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
                                            <input type="checkbox" name="size_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="size_entry_status" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__body">
                    @include('staging_activity.action_notes')
                    @include('staging_activity.recent_activity')
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
    @endstgaccess
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
@endsection
