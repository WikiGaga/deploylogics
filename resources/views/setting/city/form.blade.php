@extends('layouts.template')
@section('title', 'City')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->city_id;
            $name = $data['current']->city_name;
            $arabic_name = $data['current']->arabic_name;
            $serial = $data['current']->serial;
            $country_id = isset($data['current']->city_country->country_id)?$data['current']->city_country->country_id:'';
            $status = $data['current']->city_entry_status;
            $default = $data['current']->city_default_status;
        }
    @endphp
    @permission($data['permission'])
    <form id="city_form" class="master_form kt-form" method="post" action="{{ action('Setting\CityController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Country: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="city_country">
                                        <option value="0">Select</option>
                                        @foreach($data['country'] as $country)
                                            @php $country_id_var = isset($country_id)?$country_id:"" @endphp
                                            <option value="{{$country->country_id}}" {{ $country_id_var == $country->country_id ? "selected" : "" }}>{{$country->country_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">City Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Local Name:</label>
                            <div class="col-lg-6">
                                <input type="text" name="arabic_name" value="{{isset($arabic_name)?$arabic_name:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Serial:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="serial" value="{{isset($serial)?$serial:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-4 erp-col-form-label">Status:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        @if($case == 'edit')
                                                            @php $entry_status = isset($status)?$status:""; @endphp
                                                            <input type="checkbox" name="city_entry_status" {{$entry_status==1?"checked":""}}>
                                                        @else
                                                            <input type="checkbox" name="city_entry_status" checked>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-4 erp-col-form-label">Default:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        @if($case == 'edit')
                                                            @php $default_status = isset($default)?$default:""; @endphp
                                                            <input type="checkbox" name="city_default_status" {{$default_status==1?"checked":""}}>
                                                        @else
                                                            <input type="checkbox" name="city_default_status">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
@endsection
