@extends('layouts.template')
@section('title', 'Location')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $store_id = (!empty(Session::get('display_location_id'))) ? Session::get('display_location_id') : "";
            $parent_id = (!empty(Session::get('parent_display_location_id'))) ? Session::get('parent_display_location_id') : "";;
        }
        if($case == 'edit'){
            $id = $data['current']->display_location_id;
            $name = $data['current']->display_location_name;
            $store_id = $data['current']->store_id;
            $parent_id = $data['current']->parent_display_location_id;
            $status = $data['current']->display_location_entry_status;
           // dd($parent_id);
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="display_location_form" class="master_form kt-form" method="post" action="{{ action('Setting\DisplayLocationController@store', isset($id)?$id:'') }}">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="form-group-block row">
                    <label class="col-lg-3 erp-col-form-label">Store:<span class="required">*</span></label>
                    <div class="col-lg-6">
                        <div class="erp-select2">
                            @php $select_store = isset($store_id)?$store_id:''@endphp
                            @if($case=='edit')
                                <input type="hidden" name="store_id" value="{{$select_store}}">
                                <select class="form-control kt-select2 form-control-sm store_id" id="kt_select2_2" disabled>
                                    <option value="0">Select</option>
                                    @foreach($data['store'] as $store)
                                        <option value="{{$store->store_id}}" {{$store->store_id == $select_store ? 'selected' : ''}}>{{$store->store_name}}</option>
                                    @endforeach
                                </select>
                            @else
                                 <select class="form-control kt-select2 form-control-sm store_id" id="kt_select2_2" name="store_id" >
                                     <option value="0">Select</option>
                                     @foreach($data['store'] as $store)
                                         <option value="{{$store->store_id}}" {{$store->store_id == $select_store ? 'selected' : ''}}>{{$store->store_name}}</option>
                                     @endforeach
                                 </select>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <label class="col-lg-3 erp-col-form-label">Name:<span class="required">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" value="{{isset($name)?$name:''}}" class="form-control erp-form-control-sm medium_text" name="name">
                    </div>
                </div>
                <div class="form-group-block row">
                    <label class="col-lg-3 erp-col-form-label">Parent Name:</label>
                    <div class="col-lg-6">
                        <div class="erp-select2">
                            @php $display_parent_id = isset($parent_id)?$parent_id:'' @endphp
                            @if(isset($data['parent']))
                                <select class="form-control kt-select2 form-control-sm parent_display_location_id" id="kt_select2_1" @if($case == "edit") disabled @endif>
                                    <option value="0">Select</option>
                                    @foreach($data['parent'] as $parent)
                                        <option value="{{$parent->display_location_id}}" {{$parent->display_location_id == $display_parent_id ?"selected":"" }}>{{$parent->display_location_name_string}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="parent_display_location_id" value="{{$parent_id}}">
                            @else
                                <select class="form-control kt-select2 form-control-sm parent_display_location_id" id="kt_select2_1" name="parent_display_location_id">
                                    <option value="0">Select</option>
                                </select>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group-block  row">
                    <label class="col-lg-3 erp-col-form-label">Status:</label>
                    <div class="col-lg-6">
                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                <label>
                                    @if($case == 'edit')
                                        @php $entry_status = isset($status)?$status:""; @endphp
                                        <input type="checkbox" name="display_location_entry_status" {{ $entry_status == 1 ?"checked":""}}>
                                    @else
                                        <input type="checkbox" name="display_location_entry_status" checked="checked">
                                    @endif
                                    <span></span>
                                </label>
                            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
                <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
    <script>
        $('.store_id').change(function(){
            $('#display_location_form').find('.kt-portlet__body').addClass('pointerEventsNone');
            var that = $(this);
            var formData = {
                store_id : that.val()
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : '/location/get-store-locations',
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    if(response['status'] == 'success'){
                        var option = '<option value="0">Select</option>';
                        response['data'].forEach(function(row){
                            option += '<option value="'+row.display_location_id+'">'+row.display_location_name_string+'</option>';
                        });
                        $('#display_location_form').find('.parent_display_location_id').html(option);
                        $('#display_location_form').find('.kt-portlet__body').removeClass('pointerEventsNone');
                    }
                }
            });
        });
    </script>
@endsection
