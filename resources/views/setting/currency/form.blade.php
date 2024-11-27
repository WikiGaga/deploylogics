@extends('layouts.template')
@section('title', 'Create Currency')

@section('pageCSS')
@endsection

@section('content')

    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->currency_id;
            $currency_country = $data['current']->currency_country;
            $name = $data['current']->currency_name;
            $decimal = $data['current']->currency_decimal_precision;
            $rate = $data['current']->currency_rate;
            $symbol = $data['current']->currency_symbol;
            $remarks = $data['current']->currency_remarks;
            $default_currency = $data['current']->currency_default;
            $status = $data['current']->currency_entry_status;
        }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="currency_form" class="master_form kt-form" method="post" action="{{ action('Setting\CurrencyController@store',isset($id)?$id:'') }}">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Country:<span class="required" aria-required="true"> * </span></label>
                        <div class="col-lg-6">
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="kt_select2_1" name="currency_country">
                                    <option value="0">Select</option>
                                    @foreach($data['country'] as $country)
                                        @php $countryid = isset($currency_country)?$currency_country:"" @endphp
                                        <option value="{{$country->country_id}}" {{$country->country_id == $countryid?"selected":""  }}>{{$country->country_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Name:<span class="required" aria-required="true"> * </span></label>
                        <div class="col-lg-6">
                            <input type="text" name="name" value="{{ isset($name)?$name:'' }}" class="form-control erp-form-control-sm medium_text">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Decimal Precision:</label>
                        <div class="col-lg-6">
                            <input type="text" name="currency_decimal_precision" placeholder="Length of Number after decimal" value="{{ isset($decimal)?$decimal:'' }}" class="form-control erp-form-control-sm small_no validNumber text-left">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Currency Rate:</label>
                        <div class="col-lg-6">
                            <input type="text" name="currency_rate" value="{{ isset($rate)?$rate:'' }}" class="validNumber form-control erp-form-control-sm large_no text-left">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Currency Symbol:</label>
                        <div class="col-lg-6">
                            <input type="text" name="currency_symbol" value="{{ isset($symbol)?$symbol:'' }}" class="form-control erp-form-control-sm small_no">
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                        <div class="col-lg-6">
                            <textarea type="text" name="currency_remarks"  class="form-control erp-form-control-sm large_text" rows="3">{{ isset($remarks)?$remarks:'' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Default Currency:</label>
                        <div class="col-lg-6">
                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                <label>
                                    @if($case == 'edit')
                                        @php $default_currency = isset($default_currency)?$default_currency:""; @endphp
                                        <input type="checkbox" name="currency_default" {{$default_currency==1?'checked':''}}>
                                    @else
                                        <input type="checkbox" name="currency_default">
                                    @endif
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="form-group-block  row">
                        <label class="col-lg-3 erp-col-form-label">Status:</label>
                        <div class="col-lg-6">
                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                <label>
                                    @if($case == 'edit')
                                        @php $entry_status = isset($status)?$status:""; @endphp
                                        <input type="checkbox" name="currency_entry_status" {{$entry_status==1?'checked':''}}>
                                    @else
                                        <input type="checkbox" name="currency_entry_status" checked>
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
