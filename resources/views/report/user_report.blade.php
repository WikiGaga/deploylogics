@extends('layouts.template')
@section('title', 'Reporting')

@section('pageCSS')
@endsection
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
</style>
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $id = "";
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $id = $data['id'];
            $date =  date('d-m-Y');
        }
       // dd($data['report']);
    @endphp

    <form id="report_user_form" class="kt-form" method="post" action="{{ action('Report\UserReportController@store', $id) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="reporting_id"  name="reporting_id" value="{{$data['report']->reporting_id}}">
        <input type="hidden" id="reporting_case" name="reporting_case" value="{{$data['report']->reporting_case}}">
        <input type="hidden" name="report_business_name" value="{{auth()->user()->business->business_id}}">
        <div class="col-lg-12">
            <div class="erp-card">
                <div class="erp-card-body rounded kt-padding-0 d-flex bg-light">
                    <div class="erp-card-search">
                        <h1 class="text-danger font-weight-bolder m-0">{{$data['report']->reporting_title}}</h1>
                        <!--begin::Form-->
                        <button type="submit" class="btn btn-danger font-weight-bold py-2 px-6">Generate</button>
                        <!--end::Form-->
                    </div>
                    <div class="erp-company-detail">
                        <div class="business-name">Business Name:
                            <span>{{auth()->user()->business->business_name}}</span>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-12">
                                <label class="erp-col-form-label">Branch Name:</label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="report_branch_name" name="report_branch_name">
                                        <option value="{{auth()->user()->branch->branch_id}}">{{auth()->user()->branch->branch_name}}</option>
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
                        </div>
                        <div class="kt-portlet__body">
                        @if($data['case_name'] != 'closing_day' && $data['case_name'] != 'sales_type_wise' )
                            <div id="kt_repeater_report_filter">
                                <div data-repeater-list="outer_report_filter">
                                    @if(isset($data['max']))
                                        @for($i=1; $data['max'] >= $i; $i++)
                                            <div data-repeater-item class="outer_report_filter_block">
                                                <div class="row">
                                                    <div class="col-lg-12" style="position: relative">
                                                        <button data-repeater-delete="" type="button" class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                            <i class="la la-trash-o"></i>  AND
                                                        </button>
                                                        <i class="la la-level-down user-report-and-down"></i>
                                                    </div>
                                                </div>
                                                <div class="inner-report-filter">
                                                    <div data-repeater-list="report_filter">
                                                        @if(isset($data['user_studio']->user_studio_dtl))
                                                            @foreach($data['user_studio']->user_studio_dtl as $user_studio_dtl)
                                                                @if($user_studio_dtl['reporting_user_studio_dtl_sr'] == $i)
                                                                    <div data-repeater-item class="col-lg-12 report_filter_block">
                                                                        <div class="row form-group-block">
                                                                            <div class="col-lg-10">
                                                                                <div class="row">
                                                                                    <div class="col-lg-3">
                                                                                        <label class="erp-col-form-label">Filter Name:</label>
                                                                                        <div class="erp-select2">
                                                                                            <select class="form-control erp-form-control-sm report_filter_name" name="report_filter_name">
                                                                                                <option value="">Select</option>
                                                                                                @foreach($data['report']->user_filter as $user_filter)
                                                                                                    <option value="{{$user_filter['reporting_user_filter_type']}}" {{ ($user_studio_dtl['reporting_user_studio_dtl_name'] == $user_filter['reporting_user_filter_type'])?"selected":"" }}>{{$user_filter['reporting_user_filter_title']}}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-9">
                                                                                        <div class="row">
                                                                                            <div class="col-lg-4" id="report_filter_types">
                                                                                                <label class="erp-col-form-label">Filter Types:</label>
                                                                                                <div class="erp-select2">
                                                                                                    @php
                                                                                                        $datatype = \App\Models\TblSoftReportingUserFilter::where('reporting_id', $data['user_studio']->reporting_id)
                                                                                                                              ->where('reporting_user_filter_field_name', $user_studio_dtl['reporting_user_studio_dtl_name'])
                                                                                                                              ->first();
                                                                                                        $types = \App\Models\TblSoftFilterType::where('filter_type_data_type_name', $datatype->reporting_user_filter_field_type)->get();
                                                                                                    @endphp
                                                                                                    <select class="form-control erp-form-control-sm report_filter_type" name="report_filter_type">
                                                                                                        <option value="">Select</option>
                                                                                                        @foreach($types as $type)
                                                                                                            <option value="{{$type->filter_type_value}}" {{ ($type->filter_type_value == $user_studio_dtl['reporting_user_studio_dtl_type'])?"selected":"" }}>{{$type->filter_type_title}}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                                                                            <style>
                                                                                                #number_between,
                                                                                                #date_between,
                                                                                                #fields_values{
                                                                                                    display: none;
                                                                                                }
                                                                                                .erp-row{
                                                                                                    display: flex;
                                                                                                    flex-wrap: wrap;
                                                                                                    margin-right: -10px;
                                                                                                    margin-left: -10px;
                                                                                                }
                                                                                            </style>
                                                                                            <input type="hidden" id="report_value_column_type_name" name="report_value_column_type_name" value="{{$datatype->reporting_user_filter_field_type}}"/>
                                                                                            <div class="col-lg-8" id="report_filter_filed">
                                                                                                @if($datatype->reporting_user_filter_field_type == 'varchar2' || $datatype->reporting_user_filter_field_type == 'number')
                                                                                                    <div class="row" id="fields_values" style="display: block">
                                                                                                        <div class="col-lg-12">
                                                                                                            <label class="erp-col-form-label">Value:</label>
                                                                                                            <div class="erp-select2">
                                                                                                                @php
                                                                                                                    $filtercase = \App\Models\TblSoftReportingFilterCase::where('reporting_filter_case_name', $user_studio_dtl['reporting_user_studio_dtl_name'])->first();
                                                                                                                    $data_values = "";
                                                                                                                    if(!empty($filtercase)){
                                                                                                                        if(!empty($filtercase->reporting_filter_case_query)){
                                                                                                                            $data_values = DB::select($filtercase->reporting_filter_case_query);
                                                                                                                        }
                                                                                                                    }
                                                                                                                    $dtl_value = explode(',', $user_studio_dtl['reporting_user_studio_dtl_value']);
                                                                                                                @endphp
                                                                                                                <select class="form-control erp-form-control-sm report_value " multiple name="report_value">
                                                                                                                    <option value="0">Select</option>
                                                                                                                    @foreach($data_values as $data_value)
                                                                                                                        @if($user_studio_dtl['reporting_user_studio_dtl_name'] == 'manufacturer_id')
                                                                                                                            <option value="{{$data_value->manufacturer_id}}" {{ (in_array($data_value->manufacturer_id, $dtl_value)) ? 'selected' : '' }}>{{$data_value->manufacturer_name}}</option>
                                                                                                                        @endif
                                                                                                                        @if($user_studio_dtl['reporting_user_studio_dtl_name'] == 'product_item_tags')
                                                                                                                            <option value="{{$data_value->tags_id}}" {{ (in_array($data_value->tags_id, $dtl_value)) ? 'selected' : '' }}>{{$data_value->tags_name}}</option>
                                                                                                                        @endif
                                                                                                                        @if($user_studio_dtl['reporting_user_studio_dtl_name'] == 'product_name')
                                                                                                                            <option value="{{$data_value->product_name}}" {{ (in_array($data_value->product_name, $dtl_value)) ? 'selected' : '' }}>{{$data_value->product_name}}</option>
                                                                                                                        @endif
                                                                                                                        @if($user_studio_dtl['reporting_user_studio_dtl_name'] == 'group_item_name')
                                                                                                                            <option value="{{$data_value->group_item_name}}" {{ (in_array($data_value->group_item_name, $dtl_value)) ? 'selected' : '' }}>{{$data_value->group_item_name}}</option>
                                                                                                                        @endif
                                                                                                                        @if($user_studio_dtl['reporting_user_studio_dtl_name'] == 'supplier_type_name')
                                                                                                                            <option value="{{$data_value->supplier_type_name}}" {{ (in_array($data_value->supplier_type_name, $dtl_value)) ? 'selected' : '' }}>{{$data_value->supplier_type_name}}</option>
                                                                                                                        @endif
                                                                                                                        @if($user_studio_dtl['reporting_user_studio_dtl_name'] == 'chart_code')
                                                                                                                            <option value="{{$data_value->chart_code}}" {{ (in_array($data_value->chart_code, $dtl_value)) ? 'selected' : '' }}>{{$data_value->chart_code}}</option>
                                                                                                                        @endif
                                                                                                                    @endforeach
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @else
                                                                                                    <div class="row" id="fields_values">
                                                                                                        <div class="col-lg-12">
                                                                                                            <label class="erp-col-form-label">Value:</label>
                                                                                                            <div class="erp-select2">
                                                                                                                <select class="form-control erp-form-control-sm report_value " multiple name="report_value" disabled>
                                                                                                                    <option value="0">Select</option>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @endif
                                                                                                @if($datatype->reporting_user_filter_field_type == 'number' && $datatype->reporting_user_filter_field_type == 'number')
                                                                                                    <div class="row" id="number_between" style="display: block">
                                                                                                        <div class="col-lg-12">
                                                                                                            <div class="erp-row">
                                                                                                                <div class="col-lg-6">
                                                                                                                    <label class="erp-col-form-label">From:</label>
                                                                                                                    <input type="text" disabled name="report_value_from" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                </div>
                                                                                                                <div class="col-lg-6">
                                                                                                                    <label class="erp-col-form-label">To:</label>
                                                                                                                    <input type="text" disabled name="report_value_to" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                </div>
                                                                                                            </div>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                @else
                                                                                                    <div class="row" id="number_between">
                                                                                                        <div class="col-lg-12">
                                                                                                            <div class="erp-row">
                                                                                                                <div class="col-lg-6">
                                                                                                                    <label class="erp-col-form-label">From:</label>
                                                                                                                    <input type="text" disabled name="report_value_from" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                </div>
                                                                                                                <div class="col-lg-6">
                                                                                                                    <label class="erp-col-form-label">To:</label>
                                                                                                                    <input type="text" disabled name="report_value_to" class="form-control erp-form-control-sm text-left validNumber">
                                                                                                                </div>
                                                                                                            </div>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                @endif
                                                                                                @if($datatype->reporting_user_filter_field_type == 'date')
                                                                                                        @php
                                                                                                            $filtercase = \App\Models\TblSoftReportingFilterCase::where('reporting_filter_case_name', $user_studio_dtl['reporting_user_studio_dtl_name'])->first();
                                                                                                            $data_values = "";
                                                                                                            if(!empty($filtercase)){
                                                                                                                if(!empty($filtercase->reporting_filter_case_query)){
                                                                                                                    $data_values = DB::select($filtercase->reporting_filter_case_query);
                                                                                                                }
                                                                                                            }
                                                                                                        @endphp
                                                                                                    <div class="row" id="date_between" style="display: block">
                                                                                                        <div class="col-lg-12">
                                                                                                            <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                            <div class="erp-selectDateRange">
                                                                                                                <div class="input-daterange input-group kt_datepicker_5">
                                                                                                                    <input type="text" value="{{$user_studio_dtl['reporting_user_studio_dtl_value']}}" class="form-control erp-form-control-sm" name="report_value_from" />
                                                                                                                    <div class="input-group-append">
                                                                                                                        <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                                    </div>
                                                                                                                    <input type="text" value="{{$user_studio_dtl['reporting_user_studio_dtl_and']}}" class="form-control erp-form-control-sm" name="report_value_to" />
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @else
                                                                                                    <div class="row" id="date_between">
                                                                                                        <div class="col-lg-12">
                                                                                                            <label class="erp-col-form-label">Select Date Range:</label>
                                                                                                            <div class="erp-selectDateRange">
                                                                                                                <div class="input-daterange input-group kt_datepicker_5">
                                                                                                                    <input type="text" disabled class="form-control erp-form-control-sm" name="report_value_from" />
                                                                                                                    <div class="input-group-append">
                                                                                                                        <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                                    </div>
                                                                                                                    <input type="text" disabled class="form-control erp-form-control-sm" name="report_value_to" />
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 text-right">
                                                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-user-filter-del-btn">
                                                                                    <i class="la la-minus-circle"></i>
                                                                                </a>
                                                                                <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn" disabled readonly >
                                                                                    OR
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <div class="row ">
                                                        <div class="col-lg-9"></div>
                                                        <div class="col-lg-3  text-right">
                                                            <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand report-user-sec-filter-or-btn report-user-filter-or-btn">
                                                                OR
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @else
                                        <div data-repeater-item class="outer_report_filter_block">
                                            <div class="row">
                                                <div class="col-lg-12" style="position: relative">
                                                    <button data-repeater-delete="" type="button" class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                        <i class="la la-trash-o"></i>  AND
                                                    </button>
                                                    <i class="la la-level-down user-report-and-down"></i>
                                                </div>
                                            </div>
                                            <div class="inner-report-filter">
                                                <div data-repeater-list="report_filter">
                                                    <div data-repeater-item class="col-lg-12 report_filter_block">
                                                        <div class="row form-group-block">
                                                            <div class="col-lg-10">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <label class="erp-col-form-label">Filter Name:</label>
                                                                        <div class="erp-select2">
                                                                            <select class="form-control erp-form-control-sm report_filter_name" name="report_filter_name">
                                                                                <option value="">Select</option>
                                                                                @foreach($data['report']->user_filter as $user_filter)
                                                                                    <option value="{{$user_filter['reporting_user_filter_type']}}">{{$user_filter['reporting_user_filter_title']}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-9">
                                                                        <div class="row">
                                                                            <div class="col-lg-4" id="report_filter_types">
                                                                                <label class="erp-col-form-label">Filter Types:</label>
                                                                                <div class="erp-select2">
                                                                                    <select class="form-control erp-form-control-sm report_filter_type" name="report_filter_type">
                                                                                        <option value="">Select</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <style>
                                                                                #number_between,
                                                                                #date_between,
                                                                                #fields_values{
                                                                                    display: none;
                                                                                }
                                                                                .erp-row{
                                                                                    display: flex;
                                                                                    flex-wrap: wrap;
                                                                                    margin-right: -10px;
                                                                                    margin-left: -10px;
                                                                                }
                                                                            </style>
                                                                            <input type="hidden" id="report_value_column_type_name" name="report_value_column_type_name"/>
                                                                            <div class="col-lg-8" id="report_filter_filed">
                                                                                <div class="row" id="fields_values">
                                                                                    <div class="col-lg-12">
                                                                                        <label class="erp-col-form-label">Value:</label>
                                                                                        <div class="erp-select2">
                                                                                            <select class="form-control erp-form-control-sm report_value " multiple name="report_value" disabled>
                                                                                                <option value="0">Select</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" id="number_between">
                                                                                    <div class="col-lg-12">
                                                                                        <div class="erp-row">
                                                                                            <div class="col-lg-6">
                                                                                                <label class="erp-col-form-label">From:</label>
                                                                                                <input type="text" disabled name="report_value_from" class="form-control erp-form-control-sm text-left validNumber">
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <label class="erp-col-form-label">To:</label>
                                                                                                <input type="text" disabled name="report_value_to" class="form-control erp-form-control-sm text-left validNumber">
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" id="date_between">
                                                                                    <div class="col-lg-12">
                                                                                        <label class="erp-col-form-label">Select Date Range:</label>
                                                                                        <div class="erp-selectDateRange">
                                                                                            <div class="input-daterange input-group kt_datepicker_5">
                                                                                                <input type="text" disabled class="form-control erp-form-control-sm" name="report_value_from" />
                                                                                                <div class="input-group-append">
                                                                                                    <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                </div>
                                                                                                <input type="text" disabled class="form-control erp-form-control-sm" name="report_value_to" />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2 text-right">
                                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger report-user-filter-del-btn">
                                                                    <i class="la la-minus-circle"></i>
                                                                </a>
                                                                <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand report-user-filter-or-btn" disabled readonly >
                                                                    OR
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row ">
                                                    <div class="col-lg-9"></div>
                                                    <div class="col-lg-3  text-right">
                                                        <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand report-user-sec-filter-or-btn report-user-filter-or-btn">
                                                            OR
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button data-repeater-create="" type="button" class="btn btn-brand btn-sm">AND</button>
                            </div>
                        @endif
                        @if($data['case_name'] == 'closing_day')
                            <div class="inner-report-filter">
                                <div class="col-lg-12">
                                    <div class="row form-group-block">
                                        <div class="col-lg-10">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="erp-col-form-label">Filter Name:</div>
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm report_filter_name" name="outer_report_filter[0][report_filter][0][report_filter_name]">
                                                            <option value="sales_date" selected>Date</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="erp-col-form-label">Filter Types:</div>
                                                            <div class="erp-select2">
                                                                <select class="form-control erp-form-control-sm report_filter_type" name="outer_report_filter[0][report_filter][0][report_filter_type]">
                                                                    <option value="between" selected>between</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="report_value_column_type_name" name="outer_report_filter[0][report_filter][0][report_value_column_type_name]" value="date" autocomplete="off">
                                                        <div class="col-lg-8">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <label class="erp-col-form-label">Select Date Range:</label>
                                                                    <div class="erp-selectDateRange">
                                                                        <div class="input-daterange input-group kt_datepicker_5">
                                                                            @php
                                                                                $val_1 = isset($data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_value'])?$data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_value']:'';
                                                                                $val_2 = isset($data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_and'])?$data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_and']:'';
                                                                            @endphp
                                                                            <input type="text" value="{{$val_1}}" class="form-control erp-form-control-sm" name="outer_report_filter[0][report_filter][0][report_value_from]" />
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text erp-form-control-sm">To</span>
                                                                            </div>
                                                                            <input type="text" value="{{$val_2}}"class="form-control erp-form-control-sm" name="outer_report_filter[0][report_filter][0][report_value_to]" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($data['case_name'] == 'sales_type_wise')
                            <div class="inner-report-filter">
                                <div class="col-lg-12">
                                    <div class="row form-group-block">
                                        <div class="col-lg-10">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="erp-col-form-label">Filter Name:</div>
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm report_filter_name" name="outer_report_filter[0][report_filter][0][report_filter_name]">
                                                            <option value="sales_date" selected>Date</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="erp-col-form-label">Filter Types:</div>
                                                            <div class="erp-select2">
                                                                <select class="form-control erp-form-control-sm report_filter_type" name="outer_report_filter[0][report_filter][0][report_filter_type]">
                                                                    <option value="between" selected>between</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="report_value_column_type_name" name="outer_report_filter[0][report_filter][0][report_value_column_type_name]" value="date" autocomplete="off">
                                                        <div class="col-lg-8">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <label class="erp-col-form-label">Select Date Range:</label>
                                                                    <div class="erp-selectDateRange">
                                                                        <div class="input-daterange input-group kt_datepicker_5">
                                                                            @php
                                                                                $val_1 = isset($data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_value'])?$data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_value']:'';
                                                                                $val_2 = isset($data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_and'])?$data['user_studio']->user_studio_dtl[0]['reporting_user_studio_dtl_and']:'';
                                                                            @endphp
                                                                            <input type="text" value="{{$val_1}}" class="form-control erp-form-control-sm" name="outer_report_filter[0][report_filter][0][report_value_from]" />
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text erp-form-control-sm">To</span>
                                                                            </div>
                                                                            <input type="text" value="{{$val_2}}"class="form-control erp-form-control-sm" name="outer_report_filter[0][report_filter][0][report_value_to]" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <!--end::Form-->
@endsection
@section('pageJS')
    <script>

        var data_case = '';
        var column_type_name = '';
        var funRunCount = 0;
    </script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/report-user.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/report-user-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/report-user-form-repeater.js') }}" type="text/javascript"></script>
    @if($data['case_name'] == 'closing_day')
        <script>
            var arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
            $('.kt_datepicker_5').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                format:'dd-mm-yyyy',
                templates: arrows
            });
        </script>
    @endif
@endsection

