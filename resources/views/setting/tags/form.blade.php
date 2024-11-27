@extends('layouts.template')
@section('title', 'Tags')

@section('pageCSS')
@endsection

@section('content')
    @php
        // dd($data);
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->tags_id;
            $type = $data['current']->tags_type;
            $name = $data['current']->tags_name;
            $status = $data['current']->tags_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <form id="city_form" class="master_form kt-form" method="post" action="{{ action('Setting\TagsController@store', isset($id)?$id:"") }}">
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
                                <input type="text" name="name" value="{{isset($name)?$name:""}}"  class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Type:</label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control erp-form-control-sm kt-select2 shelf_stock_salesman" name="type">
                                        <option value="0">Select</option>
                                        @foreach($data['tags'] as $key=>$tags_type)
                                            @php $type_selected = isset($type)?$type:''; @endphp
                                            <option value="{{$key}}" {{$type_selected == $tags_type ? "selected" : ""}}>{{$tags_type}}</option>
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
                                            <input type="checkbox" name="tags_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="tags_entry_status" checked>
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
