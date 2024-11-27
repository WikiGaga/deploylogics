@extends('layouts.template')
@section('title', 'Merchant')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->merchant_id;
            $name = $data['current']->merchant_name;
            $short_name = $data['current']->merchant_short_name;
            $gst = $data['current']->merchant_gst;
            $excise_duty = $data['current']->merchant_excise_duty;
            $consume_amount = $data['current']->merchant_max_consume_amount;
            $commission = $data['current']->merchant_commission;
            $status = $data['current']->merchant_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <form id="merchant_form" class="master_form kt-form" method="post" action="{{ action('Setting\MerchantController@store', isset($id)?$id:"") }}">
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
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-5 erp-col-form-label">Name: <span class="required">*</span></label>
                                    <div class="col-lg-7">
                                        <input type="text" name="name" value="{{isset($name)?$name:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-5 erp-col-form-label">Short Name:</label>
                                    <div class="col-lg-7">
                                        <input type="text" name="short_name" value="{{isset($short_name)?$short_name:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-5 erp-col-form-label">GST(%):</label>
                                    <div class="col-lg-7">
                                        <input type="text" name="gst" id="gst" value="{{isset($gst)?$gst:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-5 erp-col-form-label">Excise Duty(%):</label>
                                    <div class="col-lg-7">
                                        <input type="text" id="excise_duty" name="excise_duty" value="{{isset($excise_duty)?$excise_duty:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-5 erp-col-form-label">Max Consume Amount:</label>
                                    <div class="col-lg-7">
                                        <input type="text" name="consume_amount" value="{{isset($consume_amount)?$consume_amount:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-lg-5 erp-col-form-label">Merchant Commission:</label>
                                    <div class="col-lg-7">
                                        <input type="text" name="merchant_commission" value="{{isset($commission)?$commission:""}}" class="form-control erp-form-control-sm medium_text">
                                    </div>
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
                                            <input type="checkbox" name="merchant_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="merchant_entry_status" checked>
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
    <script src="{{ asset('js/pages/js/setting/merchant/form.js') }}" type="text/javascript"></script>
    <script>
        $('#gst').keyup(function(){
            if ($(this).val() > 100){
                $(this).val('100');
            }
        });

        $('#excise_duty').keyup(function(){
            if ($(this).val() > 100){
                $(this).val('100');
            }
        });
    </script>
@endsection