@extends('layouts.template')
@section('title', 'Incentive Type')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->incentive_type_id;
            $name = $data['current']->incentive_type_name;
            $status = $data['current']->incentive_type_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <form id="incentive_type_form" class="master_form kt-form" method="post" action="{{ action('Setting\IncentiveTypeController@store', isset($id)?$id:"") }}">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="incentive_type_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="incentive_type_entry_status" checked>
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
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/setting/incentive_type/form.js') }}" type="text/javascript"></script>
@endsection