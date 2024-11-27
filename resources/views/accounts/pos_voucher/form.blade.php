@extends('layouts.layout')
@section('title', 'POS Voucher')

@section('pageCSS')
    <style>
        #account_code-error{
            display: none !important;
        }
    </style>
@endsection
@section('content')
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $date =  date('d-m-Y');
            }
            if($case == 'edit'){

            }
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="voucher_form" class="kt-form" method="post" action="{{ action('Accounts\POSVoucherController@store', [isset($id)?$id:'']) }}">
    @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block  row">
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <div class="col-lg-4">
                                    <label class="erp-col-form-label">Voucher Posting:<span class="required">*</span></label>
                                </div>
                                <div class="col-lg-4">
                                        <span >
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                                <input type="checkbox" name="pos_voucher" value=1 > POS
                                                <span></span>
                                            </label>
                                        </span>
                                </div>
                                <div class="col-lg-4">
                                        <span >
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                                <input type="checkbox" name="cash_voucher" value=1 > Cash
                                                <span></span>
                                            </label>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Date:<span class="required">*</span></label>
                                </div>
                                <div class="col-lg-9">
                                    <div class="erp-selectDateRange">
                                        <div class="input-daterange input-group kt_datepicker_5">
                                            <input type="text" class="pos_date form-control erp-form-control-sm" value="{{$date}}" name="date_from" autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text erp-form-control-sm">To</span>
                                            </div>
                                            <input type="text" class="pos_date form-control erp-form-control-sm" value="{{$date}}" name="date_to" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Branch:<span class="required">*</span>  </label>
                                </div>
                                <div class="col-lg-9">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" multiple id="pos_branch_ids" name="pos_branch_ids[]">
                                            @foreach($data['branches'] as $branch)
                                                <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/voucher-posting.js') }}" type="text/javascript"></script>
    <script>
        var arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
        $('.pos_date').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            format:'dd-mm-yyyy',
            templates: arrows,
            todayBtn:true
        });
    </script>
@endsection

