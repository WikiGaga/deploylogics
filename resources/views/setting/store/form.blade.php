@extends('layouts.template')
@section('title', 'Store')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->store_id;
            $name = $data['current']->store_name;
            $store_branch = $data['current']->store_branch;
            $default = $data['current']->store_default_value;
            $status = $data['current']->store_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <form id="city_form" class="master_form kt-form" method="post" action="{{ action('Setting\StoreController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Branch:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 form-control-sm" id="kt_select2_1" name="branch_id">
                                        <option value="{{auth()->user()->branch_id}}">{{auth()->user()->branch->branch_name}}</option>
                                        {{--@php $store_branch = isset($store_branch)?$store_branch:''@endphp
                                        @foreach($data['branches'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == $store_branch ? 'selected' : ''}}>{{$branch->branch_name}}</option>
                                        @endforeach--}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Default Value:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $default_val = isset($default)?$default:""; @endphp
                                            <input type="checkbox" name="store_default_value" {{$default_val==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="store_default_value">
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
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="store_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="store_entry_status" checked>
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
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
@endsection
