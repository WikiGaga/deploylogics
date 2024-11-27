@extends('layouts.template')
@section('title', 'Constants')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){
            $id = $data['current']->constants_id;
            $constant_value = $data['current']->constants_value;
            $constant_key = $data['current']->constants_key;
            $constant_type = $data['current']->constants_type;
            $status = $data['current']->constants_status;
        }
    @endphp
    <form id="contants_form" class="master_form kt-form" method="post" action="{{ action('Setting\ConstantsController@store', isset($data['current']->constants_id)?$data['current']->constants_id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Value:</label>
                            <div class="col-lg-6">
                                <input type="text" name="value" value="{{isset($data['current']->constants_value)?$data['current']->constants_value:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Key:</label>
                            <div class="col-lg-6">
                                <input type="text" name="key" value="{{isset($data['current']->constants_key)?$data['current']->constants_key:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Type:</label>
                            <div class="col-lg-6">
                                <input type="text" name="type" value="{{isset($data['current']->constants_type)?$data['current']->constants_type:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        {{-- <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                            <input type="checkbox" name="status" value="{{isset(($data['current']->constants_status==1)?"checked":"")}}">
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div> --}}
                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
@endsection
@section('pageJS')

@endsection

@section('customJS')
<script src="{{ asset('js/pages/js/setting/constants/form.js') }}" type="text/javascript"></script>
@endsection