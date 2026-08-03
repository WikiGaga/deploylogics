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
                    <div class="form-group-block row">
                        <div class="col-lg-8">
                            <div class="row form-group-block">
                                <div class="col-lg-3">
                                    <label class="erp-col-form-label">Voucher Posting:<span class="required">*</span></label>
                                </div>
                                <div class="col-lg-4">
                                        <span>
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                                <input type="checkbox" name="pos_voucher" value=1 > POS Sale Vouchers
                                                <span></span>
                                            </label>
                                        </span>
                                </div>
                                <div class="col-lg-5">
                                        <span>
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand moveIndex">
                                                <input type="checkbox" name="cash_voucher" value=1 > Cash-Visa Adjustment Vouchers
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
                                <div class="col-lg-7">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" multiple id="pos_branch_ids" name="pos_branch_ids[]">
                                            @foreach($data['branches'] as $branch)
                                                <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label class="erp-col-form-label mb-0" style="margin-right: 5px;">ALL</label>
                                    <div class="erp-select2 d-inline-block">
                                        <div class="kt-checkbox-inline">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand mb-0">
                                                <input type="checkbox" id="select_all_branch" name="select_all_branch" class="select_all_branch" autocomplete="off">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Ingredient Usage Sync
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <form id="ingredient_usage_form" class="form">
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row form-group-block">
                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">Date:<span class="required">*</span></label>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="erp-selectDateRange">
                                            <div class="input-daterange input-group kt_datepicker_5">
                                                <input type="text" class="pos_date form-control erp-form-control-sm ingredient-date" value="{{$date}}" name="ingredient_date_from" autocomplete="off">
                                                <div class="input-group-append">
                                                    <span class="input-group-text erp-form-control-sm">To</span>
                                                </div>
                                                <input type="text" class="pos_date form-control erp-form-control-sm ingredient-date" value="{{$date}}" name="ingredient_date_to" autocomplete="off">
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
                                        <label class="erp-col-form-label">Branch:<span class="required">*</span></label>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="ingredient_branch_id" name="ingredient_branch_id" data-placeholder="Select branch">
                                                <option value="">Select branch</option>
                                                @foreach($data['branches'] as $branch)
                                                    <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <button type="button" id="ingredient_usage_sync_btn" class="btn btn-brand btn-sm">
                                    Sync Ingredient Usage
                                </button>
                            </div>
                        </div>
                        <div id="ingredient_usage_feedback" class="mt-3"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Forms-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/voucher-posting.js?v=1.3') }}" type="text/javascript"></script>
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

        $(document).on('change', '.select_all_branch', function() {
            var isChecked = $(this).is(':checked');
            var $select = $('#pos_branch_ids');

            if (isChecked) {
                $select.find('option').prop('selected', true);
            } else {
                $select.find('option').prop('selected', false);
                $select.find('option[value="{{ auth()->user()->branch_id }}"]').prop('selected', true);
            }

            $select.trigger('change');
        });
    </script>
@endsection

