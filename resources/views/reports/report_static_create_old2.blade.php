@extends('layouts.template')
@section('title', 'Create Report')

@section('pageCSS')
    <style>
        .erp-card{
            min-height:160px;
            box-shadow:rgba(82, 63, 105, 0.05) 0px 0px 30px 0px;
            display:flex;
            flex-direction:column;
            position:relative;
            margin-bottom: 20px;
        }
        .erp-card-search {
            background-color: #FFF4DE;
            min-height: 160px;
            width: 30% !important;
            padding-left: 65px;
            padding-right: 0px;
            padding-top: 20px;
        }
        .erp-card-search>h1 {
            font-size: 17.55px;
            font-weight: 600 !important;
            padding-top: 20px;
        }
        .erp-card-search>button {
            margin: 28px 0 20px 0;
        }
        .erp-company-detail{
            width: 40% !important;
            background-color: #FFF4DE;
            padding: 25px 40px;
        }
        .erp-bg-cover {
            background-color: #FFF4DE;
            width: 30% !important;
            background-position-x: 100%;
            background-position-y: -37px;
            background-size: 165px;
            background-repeat: no-repeat;
        }
        .business-name {
            color: #fd397a;
            font-weight: 400;
            padding-top: 11px;
        }
        .business-name>span {
            color: #737373;
            font-size: 18px;
            font-weight: 500;
            margin-left: 5px;
        }
        .inner-report-filter {
            padding-top: 15px;
        }
        #progressBar{
            position: absolute;
            top: 0;
            z-index: 999999;
            width: 100%;
            right: 0;
        }
    </style>
    <style>
        .kt_select_pro_container .select2-dropdown--above{
            width: 602px;
        }
        ul#select2-kt_select_pro-results {
            width: 600px;
            background: #fff;
        }
        ul#select2-kt_select_pro-results>li.select2-results__option {
            border-bottom: 1px solid #d5e4f7;
            padding: 0 10px !important;
        }
        ul#select2-kt_select_pro-results>li.loading-results,
        ul#select2-kt_select_pro-results>li.select2-results__message {
            padding: 5px 15px !important;
        }
        ul#select2-kt_select_pro-results .select2-resp_meta {
            display: flex;
        }
        ul#select2-kt_select_pro-results .select2-resp_list{
            padding: 5px;
        }
        ul#select2-kt_select_pro-results .select2-resp_list.select2-result-repository__title {
            width: 50%;
            border-right: 1px solid #fff7f7;
        }
        ul#select2-kt_select_pro-results .select2-resp_list.select2-result-repository__desc {
            width: 30%;
            border-right: 1px solid #fff7f7;
        }

        ul#select2-kt_select_pro-results .select2-resp_list.select2-result-repository__col_1 {
            width: 10%;
            border-right: 1px solid #fff7f7;
        }
        ul#select2-kt_select_pro-results .select2-resp_list.select2-result-repository__col_2 {
            width: 10%;
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $id = "";
        }
        if($case == 'edit'){
            $id = $data['id'];
        }
        $view = $data['report']->menu_dtl_id.'-view';
    @endphp
    @permission($view)
    <div id="progressBar"></div>
    @if($data['report']->report_static_dynamic == 'dynamic')
        <form id="report_static_form" class="kt-form" method="post" action="{{ action('Report\UserReportsController@dynamicStore', [$data['report']->report_static_dynamic,$data['report']->report_case,$id]) }}" enctype="multipart/form-data">
            <input type="hidden" id="report_listing_type" name="report_listing_type" value="{{$data['report']->report_table_style_layout}}">
    @endif
    @if($data['report']->report_static_dynamic == 'static')
                <form id="report_static_form" class="kt-form" method="post" action="{{ action('Report\UserReportsController@staticStore', [$data['report']->report_static_dynamic,$data['report']->report_case,$id]) }}" enctype="multipart/form-data">
    @endif
        @csrf
        <input type="hidden" id="report_id"  name="report_id" value="{{$data['report']->report_id}}">
        <input type="hidden" id="report_case" name="report_case" value="{{$data['report']->report_case}}">
        <input type="hidden" id="report_type" name="report_type" value="{{$data['report']->report_static_dynamic}}">
        <input type="hidden" name="report_business_id" value="{{auth()->user()->business->business_id}}">
        <div class="col-lg-12">
            <div class="erp-card">
                <div class="erp-card-body rounded kt-padding-0 d-flex bg-light">
                    <div class="erp-card-search">
                        <h1 class="text-danger font-weight-bolder m-0">{{$data['report']->report_title}}</h1>
                        <!--begin::Form-->
                        <div class="btn-group btn-group-sm mt-2" role="group" aria-label="Button group with nested dropdown">
                            <button type="submit" name="form_file_type" value="report" class="btn btn-danger font-weight-bold py-2 px-6">Generate</button>
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                    <button type="submit" name="form_file_type" value="pdf" class="dropdown-item">Pdf</button>
                                    <button type="submit" name="form_file_type" value="xls" class="dropdown-item">Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="erp-company-detail">
                        <div class="business-name">Business Name:
                            <span>{{auth()->user()->business->business_name}}</span>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-12">

                                <label class="erp-col-form-label">Branch Name:</label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" multiple id="report_branch_name" name="report_branch_ids[]">
                                        @foreach($data['branches'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="erp-bg-cover" style="background-image: url(/assets/media/custom/custom-10.svg);"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                        <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"/>
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"/>
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"/>
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"/>
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"/>
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"/>
                                    </g>
                                </svg>
                                <h3 class="kt-portlet__head-title" style="color: #5d78ff;">
                                    Filters
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <a href="{{isset($data['page_data']['path_index'])?$data['page_data']['path_index']:''}}" id="btn-back" class="btn btn-clean btn-icon-sm back check_value">
                                        <i class="la la-long-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="inner-report-filter">
                                <div class="col-lg-12">
                                    @if(count($data['selected_criteria']) == 0)
                                        No Criteria Found...
                                    @endif
                                    {{--
                                        # Criteria List
                                        between_date , single_date
                                        sale_types , chart_account
                                        customer_list , product_list

                                    --}}
                                    @if(in_array('single_date',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select Date:</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="erp-selectDateRange">
                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{date('d-m-Y')}}" name="date" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('between_date',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select Date Range:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-selectDateRange">
                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{$data['date_from']->format("d-m-Y")}}" name="date_from" autocomplete="off">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                        </div>
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{$data['date_to']->format("d-m-Y")}}" name="date_to" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('between_date_time',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select Date Time Range:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class='input-group pull-right' id='kt_daterangepicker_4'>
                                                    <input type="text" class="form-control" readonly placeholder="Select date & time range" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                                    </div>
                                                    <input type="hidden" name="between_date_time_from" class="between_date_time_from" />
                                                    <input type="hidden" name="between_date_time_to" class="between_date_time_to"/>
                                                </div>

                                                {{--<div class="erp-selectDateRange">
                                                    <div class="input-daterange input-group kt_datetimepicker_1" id="kt_datetimepicker_1">
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{$data['date_from']->format("d-m-Y")}}" name="date_from" autocomplete="off">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                        </div>
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{$data['date_to']->format("d-m-Y")}}" name="date_to" autocomplete="off">
                                                    </div>
                                                </div>--}}
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('sale_types',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Sale Type:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" name="sales_type">
                                                        <option value="SI">Sale Invoice</option>
                                                        <option value="SR">Sale Return</option>
                                                        <option value="POS">POS</option>
                                                        <option value="RPOS">RPOS</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('sale_types_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Sale Types Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="sale_types_multiple[]">
                                                        @foreach($data['sales_type_list'] as $sale_type)
                                                            <option value="{{$sale_type->sales_type}}">{{strtoupper($sale_type->sales_type)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('product_list',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Product Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm" id="kt_select_pro" name="product_id">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('product_multiple_list',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Product Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2" id="product_name_multi_select">
                                                    <select class="form-control erp-form-control-sm" id="kt_select_pro" multiple name="product_ids[]">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('chart_account',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Chart Account:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" name="chart_account">
                                                        @foreach($data['chart_accounts'] as $chart_account)
                                                            <option value="{{$chart_account->chart_account_id}}">{{$chart_account->chart_code}} - {{$chart_account->chart_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('chart_account_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Chart Account Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="chart_account_multiple[]">
                                                        @foreach($data['chart_accounts'] as $chart_account)
                                                            <option value="{{$chart_account->chart_account_id}}">{{$chart_account->chart_code}} - {{$chart_account->chart_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('customer_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Customer Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt_select2_tags erp-form-control-sm" multiple name="customer_ids[]">
                                                        @foreach($data['customer_list'] as $customer_list)
                                                            <option value="{{$customer_list->customer_id}}">{{$customer_list->customer_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('supplier_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Supplier Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt_select2_tags erp-form-control-sm" multiple name="supplier_ids[]">
                                                        @foreach($data['supplier_list'] as $supplier_list)
                                                            <option value="{{$supplier_list->supplier_id}}">{{$supplier_list->supplier_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('voucher_type_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Voucher Type Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="voucher_types[]">
                                                        @foreach($data['voucher_type_list'] as $voucher_types)
                                                            <option value="{{$voucher_types->voucher_type}}">{{strtoupper($voucher_types->voucher_type)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('payment_types',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Payment Types:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="payment_types[]">
                                                        @foreach($data['payment_types'] as $payment_type)
                                                            <option value="{{$payment_type->payment_type_id}}">{{ucwords(strtolower($payment_type->payment_type_name))}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('supplier_group_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Supplier Group Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="supplier_group[]">
                                                        @foreach($data['supplier_group'] as $supplier_group)
                                                            <option value="{{$supplier_group->supplier_type_id}}">{{strtoupper($supplier_group->supplier_type_name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('customer_group',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Customer Group:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="customer_group[]">
                                                        @foreach($data['customer_group'] as $customer_group)
                                                            <option value="{{$customer_group->customer_type_id}}">{{strtoupper($customer_group->customer_type_name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('product_group_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Product Group Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" multiple name="product_group[]">
                                                    @foreach($data['group_item'] as $group_item)
                                                        <option value="{{$group_item->group_item_id}}">{{$group_item->group_item_name_string}}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('rate_type',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Rate Type:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" name="rate_type">
                                                        <option value="0">Select</option>
                                                        <option value="1">Sale Rate</option>
                                                        <option value="2">Cost Rate</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('rate',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Rate:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" name="rate_between">
                                                        <option value="0">Select</option>
                                                        <option value="1">Zero Rate</option>
                                                        <option value="2">Zero & Less than Zero Rate</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('store_multiple_none',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Store:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="store[]">
                                                        @foreach($data['store'] as $store_id=>$store_name)
                                                            <option value="{{$store_id}}">{{ucwords(strtolower(strtoupper($store_name)))}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('users',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Salesman:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt_select2_tags erp-form-control-sm" multiple name="users_ids[]">
                                                        @foreach($data['users'] as $users)
                                                            <option value="{{$users->id}}">{{$users->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('product_grouping_type',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Grouping Type:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" name="f_product_group">
                                                        <option value="0">Select</option>
                                                        <option value="brand_id~brand_name">Brand</option>
                                                        <option value="country_id~country_name">Country</option>
                                                        <option value="group_item_id~group_item_name">Product Group</option>
                                                        <option value="manufacturer_id~manufacturer_name">Manufacturer</option>
                                                        <option value="product_type_id~product_type_name">Product Type</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif





                                    {{-- sataic criteria--}}
                                    @include('reports.report_static_criteria')
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
    <script src="{{ asset('js/pages/js/report-static-form.js') }}" type="text/javascript"></script>
@endsection

@section('customJS')
    <script>
        var arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
        $('.kt_datepicker_5').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            format:'dd-mm-yyyy',
            templates: arrows,
            todayBtn:true
        });
        $('#kt_daterangepicker_4').daterangepicker({
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',

            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'DD-MM-YYYY h:mm A'
            }
        }, function(start, end, label) {
            $('#kt_daterangepicker_4 .form-control').val( start.format('DD-MM-YYYY h:mm A') + ' / ' + end.format('DD-MM-YYYY h:mm A'));
            $('#kt_daterangepicker_4 .between_date_time_from').val( start.format('DD-MM-YYYY h:mm A'));
            $('#kt_daterangepicker_4 .between_date_time_to').val(end.format('DD-MM-YYYY h:mm A'));
        });
        $(document).on('keyup','#product_multi_select .select2-search__field',function(){
            var thix = $(this);
            var val = thix.val();
            var thix_select = $('.product_multi_select');
            if(val.length >= 3){
              //  console.log("K: "+$(this).val());
                var formData = {
                    val : val,
                    caseName : $('#report_case').val()
                };
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : 'POST',
                    url         : '/barcode/get-product-by-name',
                    dataType	: 'json',
                    data        : formData,
                    success: function(response) {
                        if(response['status'] == 'success'){
                            var options = '';
                            var product = response['product'];
                            var pl = response['product'].length;
                            for(var i=0;i<pl;i++){
                                options += '<option value="'+product[i]['product_id']+'">'+product[i]['product_name']+'</option>';
                            }
                            var selected_options = $('.product_multi_select option:selected');
                            selected_options.each(function() {
                                // console.log($(this).val()+' = '+$(this).text())
                                options += '<option value="'+$(this).val()+'" selected>'+$(this).text()+'</option>';
                            });
                            thix_select.html(options);
                            thix_select.select2('close');
                            thix_select.select2('open');
                        }
                    }
                });
            }
        });

        function formatRepo(repo) {
            if (repo.loading) return repo.text;
            var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.product_name + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__desc'>" + repo.product_barcode_barcode + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__col_1'>" + repo.uom_name + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__col_2'>" + repo.packing + "</div>" +
                "</div></div>";
            return markup;
        }
        function formatRepoSelection(repo) {
            return repo.product_name || repo.text;
        }
        $("#kt_select_pro").select2({
            placeholder: "Search.....",
            allowClear: true,
            minimumInputLength: 3,
            ajax: {
                url: "/barcode/get-product-by-name",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });

        $(document).on('change','.sub_qry_condition',function(){
            var row_id =  $(this).parents('.sub_qry').attr('row-id');
            var val = $(this).parents('.sub_qry').find(".sub_qry_value_fields input[name='sub_qry["+row_id+"][val]']").val();
            if($(this).val() == 'between'){
                $(this).parents('.sub_qry').find('.sub_qry_value_fields').html('<div class="row"><div class="col-lg-6"><input type="text" value="'+val+'" class="form-control erp-form-control-sm text-right validNumber" name="sub_qry['+row_id+'][val]"></div><div class="col-lg-6"><input type="text" value="'+val+'" class="form-control erp-form-control-sm text-right" name="sub_qry['+row_id+'][val_to]"></div></div>');
            }else{
                $(this).parents('.sub_qry').find('.sub_qry_value_fields').html('<input type="text" value="'+val+'" class="form-control erp-form-control-sm text-right validNumber" name="sub_qry['+row_id+'][val]">');
            }
        });

        $("#kt_select_store").select2({
            placeholder: "Search.....",
            allowClear: true,
            minimumInputLength: 3,
            ajax: {
                url: "/reports/get-store-by-name",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            templateResult: function(repo){
                if (repo.loading) return repo.text;
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.store_name + "</div>" +
                    "</div></div>";
                return markup;
            }, // omitted for brevity, see the source of this page
            templateSelection: function(repo){return repo.store_name || repo.text;} // omitted for brevity, see the source of this page
        });

        $("#kt_select_display_location_name_string").select2({
            placeholder: "Search.....",
            allowClear: true,
            minimumInputLength: 3,
            ajax: {
                url: "/reports/get-display-location-name-string-by-name",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            templateResult: function(repo){
                if (repo.loading) return repo.text;
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.display_location_name_string + "</div>" +
                    "</div></div>";
                return markup;
            }, // omitted for brevity, see the source of this page
            templateSelection: function(repo){return repo.display_location_name_string || repo.text;} // omitted for brevity, see the source of this page
        });

        $('.kt_select_none').select2({
            placeholder: "Search....."
        });

    </script>
@endsection

