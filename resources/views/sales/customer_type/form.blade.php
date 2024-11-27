@extends('layouts.template')
@section('title', 'Customer Type')

@section('pageCSS')
@endsection

@section('content')
    @php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->customer_type_id;
            $name = $data['current']->customer_type_name;
            $status = $data['current']->customer_type_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <form id="custtype_form" class="master_form kt-form" method="post" action="{{ action('Sales\CustomerTypeController@store', isset($id)?$id:"") }}">
    @csrf
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <!--begin::Form-->
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Name:<span class="required" aria-required="true"> * </span></label>
                    <div class="col-lg-6">
                        <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control form-control-sm medium_text">
                    </div>
                </div>
                <div class="form-group  row">
                    <label class="col-lg-3 col-form-label">Status:</label>
                    <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @php $entry_status = isset($status)?$status:""; @endphp
                                        <input type="checkbox" checked="checked" name="customer_type_entry_status" {{$entry_status==1?"checked":""}}>
                                        <span></span>
                                    </label>
                                </span>
                    </div>
                </div>
            </div>
            <!--end::Form-->
        </div>
    </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
@endsection
