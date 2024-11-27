@extends('layouts.template')
@section('title', 'Membership Type')

@section('pageCSS')
@endsection

@section('content')
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    if($case == 'new'){
        
    }
    if($case == 'edit'){
        $id = $data['current']->membership_type_id;
        $name = $data['current']->membership_type_name;
        $short_name = $data['current']->membership_type_short_name;
        $qualification_amount = $data['current']->membership_type_qualification_amount;
        $invoice_number = $data['current']->membership_type_invoice_number;
        $incentive_type = $data['current']->membership_type_incentive_type_id;
        $discount = $data['current']->membership_type_discount;
        $min_amount = $data['current']->membership_type_min_amount;
        $point_value = $data['current']->membership_type_point_value;
        $discount_limit = $data['current']->membership_type_monthly_discount_limit;
        $sale_limit = $data['current']->membership_type_monthly_sale_limit;
        $status = $data['current']->membership_type_entry_status;
    }
@endphp
@permission($data['permission'])
<form id="membership_type_form" class="master_form kt-form" method="post" action="{{ action('Setting\MembershipTypeController@store', isset($id)?$id:"") }}">
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
                                <label class="col-lg-4 erp-col-form-label">Qualification Amount:<span class="required">* </span></label>
                                <div class="col-lg-8">
                                    <input type="number" name="qualification_amount" id="qualification_amount" value="{{isset($qualification_amount)?$qualification_amount:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">No. of Invoice:</label>
                                <div class="col-lg-8">
                                    <input type="number" name="invoice_number" value="{{isset($invoice_number)?$invoice_number:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Incentive Type: <span class="required">*</span></label>
                                <div class="col-lg-8">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="incentive_type">
                                            <option value="0">Select</option>
                                            @foreach($data['incentives'] as $incentive)
                                                @php $incentive_type_id = isset($incentive_type)?$incentive_type:"" @endphp
                                                <option value="{{$incentive->incentive_type_id}}" {{ $incentive_type_id == $incentive->incentive_type_id ? "selected" : "" }}>{{$incentive->incentive_type_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Discount%:</label>
                                <div class="col-lg-8">
                                    <input type="number" name="discount" id="discount" value="{{isset($discount)?$discount:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Minimun Inv. Amount:</label>
                                <div class="col-lg-8">
                                    <input type="number" name="min_amount" id="min_amount" value="{{isset($min_amount)?$min_amount:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Value For Point:</label>
                                <div class="col-lg-8">
                                    <input type="number" name="point_value" value="{{isset($point_value)?$point_value:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Monthly Discount Limit:</label>
                                <div class="col-lg-8">
                                    <input type="number" name="discount_limit" id="discount_limit" value="{{isset($discount_limit)?$discount_limit:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Monthly Sale Limit:</label>
                                <div class="col-lg-8">
                                    <input type="number" name="sale_limit" value="{{isset($sale_limit)?$sale_limit:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Status:</label>
                        <div class="col-lg-6">
                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                <label>
                                    @if($case == 'edit')
                                        @php $entry_status = isset($status)?$status:""; @endphp
                                        <input type="checkbox" name="membership_type_entry_status" {{$entry_status==1?"checked":""}}>
                                    @else
                                        <input type="checkbox" name="membership_type_entry_status" checked>
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
    <script src="{{ asset('js/pages/js/setting/membership_type/form.js') }}" type="text/javascript"></script>
    <script>
        $('#discount').keyup(function(){
            if ($(this).val() > 100){
                // alert("No numbers above 100");
                $(this).val('100');
            }
        });
    </script>
@endsection