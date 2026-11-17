@extends('layouts.template')
@section('title', 'Discount Types')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $is_order_discount = false;
        $max_discount = null;
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->id;
            $name = $data['current']->name;
            $default_discount = $data['current']->default_discount;
            $status = $data['current']->status;
            $is_order_discount = $data['current']->is_order_discount ?? false;
            $max_discount = $data['current']->max_discount;
        }
    @endphp
    @permission($data['permission'])
    <form id="discount_type_form" class="master_form kt-form" method="post" action="{{ action('Setting\POSDiscountController@store', isset($id)?$id:"") }}">
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
                            <label class="col-lg-3 erp-col-form-label">Discount Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Default Discount:</label>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <input type="text" name="default_discount" value="{{isset($default_discount)?$default_discount:""}}" class="form-control erp-form-control-sm medium_text">
                                    <div class="input-group-append">
                                        <span class="input-group-text px-3 py-0" id="basic-addon1">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label" for="is_order_discount">Apply Discount To:</label>
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center">
                                    <span class="mr-3">Per Product</span>
                                    <input type="hidden" name="is_order_discount" value="0">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label class="mb-0">
                                            <input type="checkbox" id="is_order_discount" name="is_order_discount" value="1" aria-label="Apply discount to overall bill" aria-controls="max_discount_group" {{$is_order_discount ? 'checked' : ''}}>
                                            <span></span>
                                        </label>
                                    </span>
                                    <span class="ml-3">Overall Bill</span>
                                </div>
                            </div>
                        </div>
                        <div id="max_discount_group" class="form-group-block row" @if(!$is_order_discount) style="display: none;" @endif>
                            <label class="col-lg-3 erp-col-form-label" for="max_discount">Maximum Discount: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="number" id="max_discount" name="max_discount" value="{{$max_discount}}" min="0" max="999999999999.999" step="0.001" class="form-control erp-form-control-sm" {{$is_order_discount ? 'required' : 'disabled'}} aria-describedby="max_discount_help">
                                <small id="max_discount_help" class="form-text text-muted">Maximum discount amount allowed on the overall bill.</small>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="status" checked>
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
    <script src="{{ asset('js/pages/js/setting/pos-discount-types.js') }}?v=20261117" type="text/javascript"></script>
@endsection
