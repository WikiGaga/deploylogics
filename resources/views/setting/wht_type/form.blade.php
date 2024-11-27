@extends('layouts.template')
@section('title', 'WHT Type')

@section('pageCSS')
@endsection

@section('content')
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    if($case == 'new'){

    }
    if($case == 'edit'){
        $id = $data['current']->wht_type_id;
        $name = $data['current']->wht_type_name;
        $short_name = $data['current']->wht_type_short_name;
        $wht_type_rate = $data['current']->wht_type_rate;
        $wht_type_section = $data['current']->wht_type_section;
        $wht_type_description = $data['current']->wht_type_description;
        $status = $data['current']->wht_type_entry_status;
    }
@endphp
@permission($data['permission'])
<form id="wht_type_form" class="master_form kt-form" method="post" action="{{ action('Setting\WHTController@store', isset($id)?$id:"") }}">
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
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Name:<span class="required">* </span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="name" id="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Short Name:<span class="required">* </span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="short_name" value="{{isset($short_name)?$short_name:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">WHT Rate:<span class="required">* </span></label>
                                <div class="col-lg-8">
                                    <input type="number" name="wht_type_rate" id="wht_type_rate" value="{{isset($wht_type_rate)?$wht_type_rate:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Section:</label>
                                <div class="col-lg-8">
                                    <input type="number" name="wht_type_section" value="{{isset($wht_type_section)?$wht_type_section:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Description:</label>
                                <div class="col-lg-8">
                                    <input type="text" name="wht_type_description" id="wht_type_description" value="{{isset($wht_type_description)?$wht_type_description:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
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
    <script src="{{ asset('js/pages/js/setting/wht_type/form.js') }}" type="text/javascript"></script>
    <script>
        $(function() {
        enable_input();
        $("#create_coa_cb").click(enable_input);
        });

        function enable_input() {
            if (this.!checked) {
                $("#create_coa").removeAttr("disabled");
            } else {
                $("#create_coa").attr("disabled", true);
            }
        }
    </script>
@endsection
