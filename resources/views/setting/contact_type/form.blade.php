@extends('layouts.template')
@section('title', 'Contact Type')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->contact_type_id;
            $name = $data['current']->contact_type_name;
            $short_name = $data['current']->contact_type_short_name;
            $contact_parent_type = $data['current']->contact_type_parent_type_id;
            // @dd($parent_type);
            $status = $data['current']->contact_type_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <form id="contact_type_form" class="master_form kt-form" method="post" action="{{ action('Setting\ContactTypeController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Short Name: </label>
                            <div class="col-lg-6">
                                <input type="text" name="short_name" value="{{isset($short_name)?$short_name:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Parent Type: </label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="parent_type_id">
                                        <option value="0">Select</option>
                                        @foreach($data['contact_parents'] as $parent_type)
                                        @if ($case == 'new')
                                            <option value="{{$parent_type->contact_parent_type_id}}">{{$parent_type->contact_parent_type_name}}</option>
                                        @else
                                            <option value="{{$parent_type->contact_parent_type_id}}" {{ $contact_parent_type == $parent_type->contact_parent_type_id? "selected" : "" }}>{{$parent_type->contact_parent_type_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="contact_type_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="contact_type_entry_status" checked>
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
    <script src="{{ asset('js/pages/js/setting/contact_type/form.js') }}" type="text/javascript"></script>
@endsection