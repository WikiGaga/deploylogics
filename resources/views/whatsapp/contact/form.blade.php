@extends('layouts.template')
@section('title', 'WhatsApp Contact')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->cnt_id;
            $group_id = $data['current']->grp_id;
            $name = $data['current']->cnt_name;
            $is_verified = $data['current']->is_verified;
            $is_active = $data['current']->is_active;
            $phone_no = $data['current']->phone_no;
            $country_id = isset($data['current']->city_country->country_id)?$data['current']->city_country->country_id:'';
        }
    @endphp
    @permission($data['permission'])
    <form id="city_form" class="master_form kt-form" method="post" action="{{ action('WhatsApp\WAContactController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Contact Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Phone No: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="phone_no" value="{{isset($phone_no)?$phone_no:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Group: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="group" name="group">
                                        <option value="0">Select</option>
                                        @foreach($data['groups'] as $group)
                                            @php $group_id_var = isset($group_id)?$group_id:"" @endphp
                                            <option value="{{$group->grp_id}}" {{ $group_id_var == $group->grp_id ? "selected" : "" }}>{{$group->grp_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
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
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Verified:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'edit')
                                                    @php $default_status = isset($default)?$default:""; @endphp
                                                    <input type="checkbox" name="is_verified" {{$is_verified==1?"checked":""}}>
                                                @else
                                                    <input type="checkbox" name="is_verified">
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Active:</label>
                                    <div class="col-lg-4">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if($case == 'edit')
                                                    @php $default_status = isset($default)?$default:""; @endphp
                                                    <input type="checkbox" name="is_active" {{$is_active==1?"checked":""}}>
                                                @else
                                                    <input type="checkbox" name="is_active">
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
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
