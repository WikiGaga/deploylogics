@extends('layouts.template')
@section('title', 'Reject Reason')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->reason_id;
            $submenu_id = $data['current']->reason_submenu_id;
            $remarks = $data['current']->reason_remarks;
            $status = $data['current']->reason_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="reason_form" class="master_form kt-form" method="post" action="{{ action('Setting\ReasonController@store', isset($id)?$id:'') }}">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Select Form:<span class="required">*</span></label>
                        <div class="col-lg-6">
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="reason_form">
                                    <option value="0">Select</option>
                                    @php $submenu_id = isset($submenu_id)?$submenu_id:'' @endphp
                                    @foreach($data['menu'] as $menu)
                                        <optgroup label="{{$menu->menu_name}}">
                                            @foreach($menu->submenu as $submenu)
                                                <option value="{{$submenu->menu_dtl_id}}" {{ ($submenu_id  == $submenu->menu_dtl_id) ?'selected':'' }}>{{$submenu->menu_dtl_name}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Remarks:<span class="required">*</span></label>
                        <div class="col-lg-6">
                            <textarea type="text" name="reason_remarks"  class="form-control erp-form-control-sm large_text" rows="5">{{ isset($remarks)?$remarks:'' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Status:</label>
                        <div class="col-lg-6">
                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                <label>
                                        @if($case == 'edit')
                                        @php $entry_status = isset($status)?$status:""; @endphp
                                        <input type="checkbox" name="reason_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                        <input type="checkbox" checked="checked" name="reason_entry_status">
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
    </form>
                <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')
<script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
@endsection
