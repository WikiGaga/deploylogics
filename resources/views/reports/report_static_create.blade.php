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
            margin-bottom: 5px;
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
        .erp_form__grid_body td, .erp_form__grid_header th {
            border: 1px solid #a0b0cc !important;
        }
    </style>
    <style>
        .kt_select_pro_container .select2-dropdown--above{
            width: 602px;
        }
        ul.kt_select_pro {
            width: 550px;
            background: #fff;
        }
        ul.kt_select_pro>li.select2-results__option {
            border-bottom: 1px solid #d5e4f7;
            padding: 0 10px !important;
        }
        ul.kt_select_pro>li.loading-results,
        ul.kt_select_pro>li.select2-results__message {
            padding: 5px 15px !important;
        }
        ul.kt_select_pro .select2-resp_meta {
            display: flex;
        }
        ul.kt_select_pro .select2-resp_list{
            padding: 5px;
        }
        ul.kt_select_pro .select2-resp_list.select2-result-repository__title {
            width: 50%;
            border-right: 1px solid #fff7f7;
            text-transform: capitalize;
        }
        ul.kt_select_pro .select2-resp_list.select2-result-repository__desc {
            width: 30%;
            border-right: 1px solid #fff7f7;
        }

        ul.kt_select_pro .select2-resp_list.select2-result-repository__col_1 {
            width: 10%;
            border-right: 1px solid #fff7f7;
        }
        ul.kt_select_pro .select2-resp_list.select2-result-repository__col_2 {
            width: 10%;
            text-align: center;
        }
        span.select2-selection__clear{display: none}
    </style>
@endsection
@section('content')
    <!--begin::Form-->
    @php

        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $id = "";
            $from_time =  '12:00 AM';
            $to_time =  '11:59 PM';
        }
        if($case == 'edit'){
            $id = $data['id'];
        }
        $view = $data['report']->menu_dtl_id.'-view';

        $data['page_sett'] = \App\Models\TblSoftUserPageSetting::where('user_page_setting_document_type',isset($data['report']->report_case)?$data['report']->report_case:"")->where('user_page_setting_user_id',auth()->user()->id)->first();
        $saveSaticCriteria = [];
        $saveDynamicCriteria = [];
        if($data['page_sett'] != null){
            $page_sett = unserialize($data['page_sett']->user_page_setting_data);
            $saveSaticCriteria = isset($page_sett->saveSaticCriteria)?$page_sett->saveSaticCriteria:[];
            $saveDynamicCriteria = isset($page_sett->saveDynamicCriteria)?$page_sett->saveDynamicCriteria:[];
        }
        $data['date_from'] = isset($saveSaticCriteria['from_date'])?$saveSaticCriteria['from_date']:$data['date_from']->format("d-m-Y");
        $data['date_to'] = isset($saveSaticCriteria['to_date'])?$saveSaticCriteria['to_date']:$data['date_to']->format("d-m-Y");

        $all_document_types = \App\Models\Defi\TblDefiDocumentType::orderby('document_type_module')->orderby('document_type_name')->get();
        $all_document_types_filter = [];
        foreach ($all_document_types as $all_document_type){
        $all_document_types_filter[$all_document_type->document_type_module][] = $all_document_type;
        }

        if($data['case_name'] == 'slow_moving_stock'){
        $data['report']->report_static_dynamic = 'static';
        }
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
        <input type="hidden" value='sale_report' id="form_type">
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
                                    <button type="submit" name="form_file_type" value="pdf" class="dropdown-item" style="color: #c50606;"><i class="fa fa-file-pdf" style="color: #c50606;"></i> Pdf</button>
                                    <button type="submit" name="form_file_type" value="xls" class="dropdown-item" style="color: #1f6c41;"><i class="fa fa-file-excel" style="color: #1f6c41;"></i> Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="erp-company-detail">
                        <div class="business-name">Business Name:
                            <span>{{auth()->user()->business->business_name}}</span>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-8">
                                <label class="erp-col-form-label">Branch Name:</label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" multiple id="report_branch_name" name="report_branch_ids[]">
                                        @if(isset($saveSaticCriteria['branch_id']))
                                            @foreach($data['branches'] as $branch)
                                                <option value="{{$branch->branch_id}}" {{in_array($branch->branch_id,$saveSaticCriteria['branch_id'])?"selected":""}}>{{$branch->branch_name}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['branches'] as $branch)
                                                <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label class="erp-col-form-label">ALL</label>
                                <div class="erp-select2">
                                    <div class="kt-checkbox-inline">
                                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                            <input type="checkbox" id="select_all_branch" name="select_all_branch" class="select_all_branch" autocomplete="off">
                                            <span></span>
                                        </label>
                                    </div>
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
                        <div class="kt-portlet__head" style="min-height: 40px;">
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
                                @if(auth()->user()->id == 91 || auth()->user()->id == 81 || auth()->user()->id == 80 || auth()->user()->id == 19138121131418)
                                <div class="kt-user-page-setting" style="display: inline-block;margin-right:10px; ">
                                    <button type="button" style="width: 30px;height: 30px;" title="Setting Save" data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon" id="reportUserSettingSave">
                                        <i class="la la-floppy-o"></i>
                                    </button>
                                </div>
                                @endif
                                <div class="kt-portlet__head-wrapper">
                                    <a href="{{isset($data['page_data']['path_index'])?$data['page_data']['path_index']:''}}" id="btn-back" class="btn btn-sm btn-clean btn-icon-sm back check_value">
                                        <i class="la la-long-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body" style="padding-top: 0px;">
                            <div class="inner-report-filter" style=" margin-bottom: -5px;">
                                <div class="col-lg-12">
                                    @if(count($data['selected_criteria']) == 0)
                                        {{--No Criteria Found...--}}
                                        <style>
                                            .inner-report-filter {
                                                padding: 0 !important;
                                            }
                                        </style>
                                    @endif
                                    {{--
                                        # Criteria List
                                        between_date , single_date
                                        sale_types , chart_account
                                        customer_list , product_list

                                    --}}
                                    @if(in_array('all_branches',$data['selected_criteria']))
                                        @php
                                            $branches = \App\Models\TblSoftBranch::where('branch_active_status',1)->where(\App\Library\Utilities::currentBC())->get();
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select  Branch:</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="all_branches[]">
                                                        @foreach($branches as $branch)
                                                            <option value="{{$branch->branch_id}}" >{{ucwords(strtolower(strtoupper($branch->branch_name)))}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('single_date',$data['selected_criteria']))
                                        @php
                                            $date = isset($saveSaticCriteria['date'])?$saveSaticCriteria['date']:date('d-m-Y');
                                        @endphp
                                        @include('reports.template.date_filter_report')
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select Date:</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="erp-selectDateRange">
                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{$date}}" name="date" id="date" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if(in_array('single_time',$data['selected_criteria']))
                                            <div class="row form-group-block">
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">Select Time:</label>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="erp-col-form-label">Time:</label>
                                                            <div class="input-group date">
                                                                <input type="text" name="time_from" class="form-control erp-form-control-sm" readonly value="{{isset($to_time)?$to_time:""}}" id="kt_from_time" />
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">
                                                                        <i class="la la-clock-o"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="erp-col-form-label">Date And Time Wise:</label>
                                                            <div class="kt-checkbox-inline">
                                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                                    <input type="checkbox" name="date_time_wise" autocomplete="off">
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('between_date',$data['selected_criteria']))
                                        @include('reports.template.date_filter_report')
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select Date Range:</label>
                                            </div>
                                            @if($data['case_name'] == 'accounting_ledger')
                                                @php $date_from = '01-01-'.date('Y'); @endphp
                                            @else
                                                @php $date_from = $data['date_from']; @endphp
                                            @endif
                                            <div class="col-lg-6">
                                                <div class="erp-selectDateRange">
                                                    <div class="input-daterange input-group kt_datepicker_5">
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{$date_from}}" name="date_from" id="date_from" autocomplete="off">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text erp-form-control-sm">To</span>
                                                        </div>
                                                        <input type="text" class="form-control erp-form-control-sm" value="{{$data['date_to']}}" name="date_to" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if(in_array('time',$data['selected_criteria']))
                                            <div class="row form-group-block">
                                                <div class="col-lg-3">
                                                    <label class="erp-col-form-label">Select Time Range:</label>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="erp-col-form-label">From Time:</label>
                                                            <div class="input-group date">
                                                                <input type="text" name="time_from" class="form-control erp-form-control-sm" readonly value="{{isset($from_time)?$from_time:""}}" id="kt_from_time" />
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">
                                                                        <i class="la la-clock-o"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="erp-col-form-label">To Time:</label>
                                                            <div class="input-group date">
                                                                <input type="text" name="time_to" class="to_time form-control erp-form-control-sm" readonly value="{{isset($to_time)?$to_time:""}}" id="kt_to_time" />
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">
                                                                        <i class="la la-clock-o"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="erp-col-form-label">Date And Time Wise:</label>
                                                            <div class="kt-checkbox-inline">
                                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                                    <input type="checkbox" name="date_time_wise" autocomplete="off">
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    
                                    @if(in_array('value_wise',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Value / WithOut Value:</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="kt-checkbox-inline">
                                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                        <input type="checkbox" name="with_value_wise" autocomplete="off">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('consolidate_wise',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Consolidate:</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="kt-checkbox-inline">
                                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                        <input type="checkbox" name="consolidate" autocomplete="off">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('posted_wise',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Posted:</label>
                                            </div>
                                            <div class="col-lg-6" style="padding:10px;">
                                                <div class="kt-radio-inline" style="float:left;">
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="all">
                                                        <input checked type="radio" name="post_wise" value="all" checked> ALL
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="post">
                                                        <input type="radio" name="post_wise" value="post"> Posted
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="unposted">
                                                        <input type="radio" name="post_wise" value="unposted"> Un-Posted
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if(in_array('between_date_time',$data['selected_criteria']))
                                        <!-- <div class="row form-group-block">
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
                                        </div> -->
                                        @include('reports.template.date_filter_report')
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label class="erp-col-form-label">From Date:</label>
                                                        <div class="input-group date">
                                                            <input type="text" name="date_from" class="form-control erp-form-control-sm c-date-p"  value="{{$data['date_from']}}" id="kt_datepicker_3" />
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i class="la la-calendar"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label class="erp-col-form-label">From Time:</label>
                                                        <div class="input-group date">
                                                            <input type="text" name="between_date_time_from" class="form-control erp-form-control-sm" readonly value="{{isset($from_time)?$from_time:""}}" id="kt_from_time" />
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i class="la la-clock-o"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label class="erp-col-form-label">To Date:</label>
                                                        <div class="input-group date">
                                                            <input type="text" name="date_to" class="form-control erp-form-control-sm c-date-p" readonly value="{{$data['date_to']}}" id="kt_to_date" />
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i class="la la-calendar"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label class="erp-col-form-label">To Time:</label>
                                                        <div class="input-group date">
                                                            <input type="text" name="between_date_time_to" class="to_time form-control erp-form-control-sm" readonly value="{{isset($to_time)?$to_time:""}}" id="kt_to_time" />
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i class="la la-clock-o"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('sale_types',$data['selected_criteria']))
                                        @php
                                            $sales_type = isset($saveSaticCriteria['sales_type'])?$saveSaticCriteria['sales_type']:"";
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Sale Type:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" name="sales_type">
                                                        <option value="SI" {{$sales_type == "SI"?"selected":""}}>Sale Invoice</option>
                                                        <option value="SR" {{$sales_type == "SR"?"selected":""}}>Sale Return</option>
                                                        <option value="POS" {{$sales_type == "POS"?"selected":""}}>POS</option>
                                                        <option value="RPOS" {{$sales_type == "RPOS"?"selected":""}}>RPOS</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('sale_types_multiple',$data['selected_criteria']))
                                        @php
                                            $data['sales_type_list'] = \Illuminate\Support\Facades\DB::select('select distinct sales_type from tbl_sale_sales');
                                            $SSC_sale_types_multiple = isset($saveSaticCriteria['sale_types_multiple'])?$saveSaticCriteria['sale_types_multiple']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Sale Types Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="sale_types_multiple[]">
                                                        @foreach($data['sales_type_list'] as $sale_type)
                                                            <option value="{{$sale_type->sales_type}}" {{in_array($sale_type->sales_type,$SSC_sale_types_multiple)?"selected":""}}>{{strtoupper($sale_type->sales_type)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('all_document_type',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">All Document Type:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="all_document_type[]">
                                                        @foreach($all_document_types_filter as $module=>$all_document_type_filter)
                                                            <optgroup label="{{strtoupper($module)}}">
                                                                @foreach($all_document_type_filter as $document_type)
                                                                <option value="{{strtoupper($document_type->document_type_name)}}" >{{strtoupper($document_type->document_type_name)}} - {{$document_type->document_type_description}}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('product_id',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Product Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm re_select_product90" name="product_id">
                                                        @if(isset($saveSaticCriteria['product_id']))
                                                            @php

                                                            @endphp
                                                            <option value="{{$saveSaticCriteria['product_id']}}" selected>{{$saveSaticCriteria['product_id']}}</option>
                                                        @else
                                                            <option></option>
                                                        @endif
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
                                                    <select class="form-control erp-form-control-sm kt_select_report_multi90" name="product_id">
                                                        @if(isset($saveSaticCriteria['product_id']))
                                                            <option value="{{$saveSaticCriteria['product_id']}}" selected>{{$saveSaticCriteria['product_id']}}</option>
                                                        @else
                                                            <option></option>
                                                        @endif
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
                                                    {{--<select class="form-control erp-form-control-sm kt_select_report_multi91" id="kt_select_pro" multiple name="product_ids[]">
                                                        <option></option>
                                                    </select>--}}
                                                    <select class="form-control erp-form-control-sm kt_select_report_multi91" multiple name="product_ids[]">
                                                        @if(isset($saveSaticCriteria['multi_products']))
                                                            @foreach($saveSaticCriteria['multi_products'] as $multi_products)
                                                                <option value="{{$multi_products}}" selected>{{$multi_products}}</option>
                                                            @endforeach
                                                        @else
                                                            <option></option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('chart_account',$data['selected_criteria']))
                                        @php
                                            if(isset($saveSaticCriteria['chart_account'])){
                                                $chart_account = \App\Models\TblAccCoa::where('chart_account_id',$saveSaticCriteria['chart_account'])->first();
                                            }
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Chart Account:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select_chart_account_multi90" name="chart_account">
                                                        @if(isset($chart_account))
                                                            <option value="{{$chart_account->chart_account_id}}" selected>{{$chart_account->chart_name}}</option>
                                                        @else
                                                            <option></option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('chart_account_multiple',$data['selected_criteria']))
                                        @php
                                            if(isset($saveSaticCriteria['chart_account_multiple'])){
                                                $chart_account_multiple = \App\Models\TblAccCoa::whereIn('chart_account_id',$saveSaticCriteria['chart_account_multiple'])->get();
                                            }
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Chart Account Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options kt_select_chart_account_multi91" multiple name="chart_account_multiple[]">
                                                        @if(isset($chart_account_multiple))
                                                            @foreach($chart_account_multiple as $uSSC_chart_account)
                                                                <option value="{{$uSSC_chart_account->chart_account_id}}" selected>{{$uSSC_chart_account->chart_name}}</option>
                                                            @endforeach
                                                        @else
                                                            <option></option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('customer_id',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Customer Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm re_select_customer92" name="customer_id">
                                                        @if(isset($saveSaticCriteria['customer_id']))
                                                            @php

                                                            @endphp
                                                            <option value="{{$saveSaticCriteria['customer_id']}}" selected>{{$saveSaticCriteria['customer_id']}}</option>
                                                        @else
                                                            <option></option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('customer_multiple',$data['selected_criteria']))
                                        @php
                                            $data['customer_list'] = \App\Models\ViewSaleCustomer::where('customer_entry_status',1)->get();
                                            $SSC_customer_ids = isset($saveSaticCriteria['customer_ids'])?$saveSaticCriteria['customer_ids']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Customer Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt_select2_tags erp-form-control-sm" multiple name="customer_ids[]">
                                                        @foreach($data['customer_list'] as $customer_list)
                                                            <option value="{{$customer_list->customer_id}}" {{in_array($customer_list->customer_id,$SSC_customer_ids)?"selected":""}}>{{$customer_list->customer_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('marchant_id',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Merchant Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm re_select_marchant92" name="marchant_id">
                                                        @if(isset($saveSaticCriteria['marchant_id']))
                                                            @php

                                                            @endphp
                                                            <option value="{{$saveSaticCriteria['marchant_id']}}" selected>{{$saveSaticCriteria['marchant_id']}}</option>
                                                        @else
                                                            <option></option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('supplier_id',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Vendor Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm re_select_supplier92" name="supplier_id">
                                                        @if(isset($saveSaticCriteria['supplier_id']))
                                                            @php
                                                            @endphp
                                                            <option value="{{$saveSaticCriteria['supplier_id']}}" selected>{{$saveSaticCriteria['supplier_id']}}</option>
                                                        @else
                                                            <option></option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('supplier_multiple',$data['selected_criteria']))
                                        @php
                                            $data['supplier_list'] = \App\Models\TblPurcSupplier::where('supplier_entry_status',1)->get();
                                            $SSC_supplier_ids = isset($saveSaticCriteria['supplier_ids'])?$saveSaticCriteria['supplier_ids']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Supplier Name:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt_select2_tags erp-form-control-sm" multiple name="supplier_ids[]">
                                                        @foreach($data['supplier_list'] as $supplier_list)
                                                            <option value="{{$supplier_list->supplier_id}}" {{in_array($supplier_list->supplier_id,$SSC_supplier_ids)?"selected":""}}>{{$supplier_list->supplier_name}}</option>
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
                                            @if($data['case_name'] == 'accounting_ledger' || $data['case_name'] == 'temp_accounting_ledger')
                                                <div class="col-lg-3">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm" name="voucher_types_selection">
                                                            <option value="contain" selected>Contain</option>
                                                            <option value="not_contain">Does not Contain</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt_select2_options" multiple name="voucher_types[]">
                                                            @foreach($all_document_types_filter as $module=>$all_document_type_filter)
                                                                <optgroup label="{{strtoupper($module)}}">
                                                                    @foreach($all_document_type_filter as $document_type)
                                                                        <option value="{{strtoupper($document_type->document_type_name)}}" >{{strtoupper($document_type->document_type_name)}} - {{$document_type->document_type_description}}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt_select2_options" multiple name="voucher_types[]">
                                                            @foreach($all_document_types_filter as $module=>$all_document_type_filter)
                                                                <optgroup label="{{strtoupper($module)}}">
                                                                    @foreach($all_document_type_filter as $document_type)
                                                                        <option value="{{strtoupper($document_type->document_type_name)}}" >{{strtoupper($document_type->document_type_name)}} - {{$document_type->document_type_description}}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @if(in_array('payment_types',$data['selected_criteria']))
                                        @php
                                            $data['payment_types'] = \Illuminate\Support\Facades\DB::select('select * from tbl_defi_payment_type');
                                            $SSC_payment_types = isset($saveSaticCriteria['payment_types'])?$saveSaticCriteria['payment_types']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Payment Types:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="payment_types[]">
                                                        @foreach($data['payment_types'] as $payment_type)
                                                            <option value="{{$payment_type->payment_type_id}}" {{in_array($payment_type->payment_type_id,$SSC_payment_types)?"selected":""}}>{{ucwords(strtolower($payment_type->payment_type_name))}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('supplier_group_multiple',$data['selected_criteria']))
                                        @php
                                            $data['supplier_group'] = \App\Models\TblPurcSupplierType::where('supplier_type_entry_status',1)->get();
                                            $SSC_supplier_group = isset($saveSaticCriteria['supplier_group'])?$saveSaticCriteria['supplier_group']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Supplier Group Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="supplier_group[]">
                                                        @foreach($data['supplier_group'] as $supplier_group)
                                                            <option value="{{$supplier_group->supplier_type_id}}" {{in_array($supplier_group->supplier_type_id,$SSC_supplier_group)?"selected":""}}>{{strtoupper($supplier_group->supplier_type_name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('customer_group',$data['selected_criteria']))
                                        @php
                                            $data['customer_group'] = \App\Models\TblSaleCustomerType::where('customer_type_entry_status',1)->get();
                                            $SSC_customer_group = isset($saveSaticCriteria['customer_group'])?$saveSaticCriteria['customer_group']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Customer Group:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="customer_group[]">
                                                        @foreach($data['customer_group'] as $customer_group)
                                                            <option value="{{$customer_group->customer_type_id}}" {{in_array($customer_group->customer_type_id,$SSC_customer_group)?"selected":""}}>{{strtoupper($customer_group->customer_type_name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('product_group_multiple',$data['selected_criteria']))
                                        @php
                                            if($data['case_name'] == 'slow_moving_stock'){
                                                $data['group_item'] = \App\Models\ViewPurcGroupItem::where('group_item_level' , 1)->orderBy('group_item_name_string')->get();
                                            }else{
                                                $data['group_item'] = \App\Models\ViewPurcGroupItem::orderBy('group_item_name_string')->get();
                                            }
                                            $SSC_product_group = isset($saveSaticCriteria['product_group'])?$saveSaticCriteria['product_group']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Product Group Multiple:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" multiple name="product_group[]">
                                                    @foreach($data['group_item'] as $group_item)
                                                        <option value="{{$group_item->group_item_id}}" {{in_array($group_item->group_item_id,$SSC_product_group)?"selected":""}}>{{$group_item->group_item_name_string}}</option>
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
                                    @if(in_array('store_multiple',$data['selected_criteria']))
                                        @php
                                            $data['store'] = \App\Models\TblDefiStore::where(\App\Library\Utilities::currentBCB())->pluck('store_name','store_id');
                                            $SSC_store = isset($saveSaticCriteria['store'])?$saveSaticCriteria['store']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Store:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm kt_select2_options" multiple name="store[]">
                                                        @foreach($data['store'] as $store_id=>$store_name)
                                                            <option value="{{$store_id}}" {{in_array($store_id,$SSC_store)?"selected":""}}>{{ucwords(strtolower(strtoupper($store_name)))}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('users',$data['selected_criteria']))
                                        @php
                                            $data['users'] = \App\Models\User::where('user_entry_status',1)->where(\App\Library\Utilities::currentBC())->orderby(\Illuminate\Support\Facades\DB::raw('lower(name)'))->get();
                                            $SSC_users_ids = isset($saveSaticCriteria['users_ids'])?$saveSaticCriteria['users_ids']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Salesman:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt_select2_tags erp-form-control-sm" multiple name="users_ids[]">
                                                        @foreach($data['users'] as $users)
                                                            <option value="{{$users->id}}" {{in_array($users->id,$SSC_users_ids)?"selected":""}}>{{ucwords($users->name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('product_grouping_type',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Grouping Type:<span class="required">*</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" name="f_product_group">
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
                                    @if(in_array('product_grouping_type_multiple',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Grouping Type Multiple:<span class="required">*</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" multiple name="f_product_group_multiple[]">
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
                                    @if(in_array('uom_list',$data['selected_criteria']))
                                        @php
                                            $data['uom_list'] = \App\Models\TblDefiUom::where('uom_entry_status',1)->where(\App\Library\Utilities::currentBC())->get();
                                            $SSC_uom_ids = isset($saveSaticCriteria['uom_ids'])?$saveSaticCriteria['uom_ids']:[];
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">UOM:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" multiple name="uom_list[]">
                                                        @foreach($data['uom_list'] as $uom)
                                                            <option value="{{$uom->uom_id}}" {{in_array($uom->uom_id,$SSC_uom_ids)?"selected":""}}>{{$uom->uom_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('cash_flow_type',$data['selected_criteria']))
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Cash Flow Type:</label>
                                            </div>
                                            <div class="col-lg-6" style="padding:10px;">
                                                <div class="kt-radio-inline" style="float:left;">
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="both">
                                                        <input checked type="radio" name="radiocashflow" value="both" checked> Both
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="bank">
                                                        <input type="radio" name="radiocashflow" value="bank"> Bank
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="cash">
                                                        <input type="radio" name="radiocashflow" value="cash"> Cash
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('level_list',$data['selected_criteria']))
                                        @php
                                            $level_list = isset($saveSaticCriteria['level_list'])?$saveSaticCriteria['level_list']:"";
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select Account Level:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm" name="level_list">
                                                        <option value="">ALL</option>
                                                        <option value="1" {{$level_list == "1"?"selected":""}}>Level 1</option>
                                                        <option value="2" {{$level_list == "2"?"selected":""}}>Level 2</option>
                                                        <option value="3" {{$level_list == "3"?"selected":""}}>Level 3</option>
                                                        <option value="4" {{$level_list == "4"?"selected":""}}>Level 4</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('product_sub_group',$data['selected_criteria']))
                                        @php
                                            $level_list = isset($saveSaticCriteria['level_list'])?$saveSaticCriteria['level_list']:"";
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Product Sub Group:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="kt-checkbox-inline">
                                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
                                                        <input type="checkbox" name="product_sub_group">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('order_by',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Order By:</label>
                                            </div>
                                            <div class="col-lg-6" style="padding:10px;">
                                                <div class="kt-radio-inline" style="float:left;">
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="code">
                                                        <input checked type="radio" name="OrderBy" value="code" checked> Code
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="name">
                                                        <input type="radio" name="OrderBy" value="name"> Name
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(in_array('negative_stock',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Negative / Zero Stock:</label>
                                            </div>
                                            <div class="col-lg-6" style="padding:10px;">
                                                <div class="kt-radio-inline" style="float:left;">
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="all">
                                                        <input checked type="radio" name="nagetivestock" value="all"> Both
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="negative">
                                                        <input type="radio" name="nagetivestock" value="negative"> Negative
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="zero">
                                                        <input type="radio" name="nagetivestock" value="zero"> Zero
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('pro_status',$data['selected_criteria']))
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Active/Inactive Items:</label>
                                            </div>
                                            <div class="col-lg-6" style="padding:10px;">
                                                <div class="kt-radio-inline" style="float:left;">
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="all">
                                                        <input checked type="radio" name="product_status" value="all"> Both
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="active">
                                                        <input type="radio" name="product_status" value="active"> Active
                                                        <span></span>
                                                    </label>
                                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0" id="inactive">
                                                        <input type="radio" name="product_status" value="inactive"> In-Active
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('dead_st',$data['selected_criteria']))
                                        @php
                                            $dead_st = isset($saveSaticCriteria['dead_st'])?$saveSaticCriteria['dead_st']:"";
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Stock Detail:</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm" name="dead_st">
                                                        <option value="Dead_Stock" {{$dead_st == "Dead_Stock"?"selected":""}}>Dead Stock View</option>
                                                        <!--<option value="Negative_Stock" {{$dead_st == "Negative_Stock"?"selected":""}}>Negative Stock</option>
                                                        <option value="Zero_Stock" {{$dead_st == "Zero_Stock"?"selected":""}}>Zero Stock</option>
                                                        <option value="Inactive_Items" {{$dead_st == "Inactive_Items"?"selected":""}}>Inactive Items</option>-->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="inputDays">
                                                <div class="row form-group-block">
                                                    <div class="col-sm-7">
                                                        <div class="input-group">
                                                            <input type="text" class="validNumber form-control erp-form-control-sm" name="dead_days" id="dead_days" value=""/>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text erp-form-control-sm">Days</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array('f_multi_product',$data['selected_criteria']))
                                        @php
                                            $level_list = isset($saveSaticCriteria['level_list'])?$saveSaticCriteria['level_list']:"";
                                        @endphp
                                        <div class="row form-group-block">
                                            <div class="col-lg-3">
                                                <label class="erp-col-form-label">Select Multiple Product:</label>
                                            </div>
                                            <div class="tab-content col-lg-9">
                                                <div class="tab-pane active slected_product_ds_content" id="slected_product_ds" role="tabpanel">
                                                    <div class="form-group-block row">
                                                        <div class="col-lg-12">
                                                            <div class="erp_form___block">
                                                                <div class="table-scroll form_input__block">
                                                                    <table class="table table_pit_list erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                                        <thead class="erp_form__grid_header">
                                                                        <tr>
                                                                            <th scope="col" width="10%">
                                                                                <div class="erp_form__grid_th_title">Sr.</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                                                    <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                                                    <input id="product_barcode_id" readonly type="hidden"  class="product_barcode_id form-control erp-form-control-sm">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" width="20%">
                                                                                <div class="erp_form__grid_th_title">
                                                                                    Barcode
                                                                                    <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                                                        <i class="la la-barcode"></i>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}"   data-url_popup="{{ action('Common\DataTableController@helpOpen', 'productHelp') }}">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" width="60%">
                                                                                <div class="erp_form__grid_th_title">Product Name</div>
                                                                                <div class="erp_form__grid_th_input">
                                                                                    <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" width="10%">
                                                                                <div class="erp_form__grid_th_title">Action</div>
                                                                                <div class="erp_form__grid_th_btn">
                                                                                    <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                                                        <i class="la la-plus"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody class="erp_form__grid_body">
                                                                             @if(isset($selected_product_list))
                                                                                @foreach($selected_product_list as $dtl)
                                                                                    @php
                                                                                        $i = $loop->iteration;
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                                                            <input type="text" value="{{$i}}" name="pd[{{$i}}][sr_no]"  class="sr_count form-control erp-form-control-sm handle" readonly>
                                                                                            <input type="hidden" name="pd[{{$i}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                                                            <input type="hidden" name="pd[{{$i}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" data-id="pd_barcode" name="pd[{{$i}}][pd_barcode]" value="{{isset($dtl->product_barcode_barcode)?$dtl->product_barcode_barcode:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" data-id="product_name" name="pd[{{$i}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <div class="btn-group btn-group btn-group-sm" role="group">
                                                                                                <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @include('reports.report_static_criteria_manual')
                                </div>
                            </div>
                            {{-- sataic criteria--}}
                            @php
                                $styles = isset($data['report']->report_styling)?$data['report']->report_styling:[];
                                $elements = [];
                                if(count($styles) != 0){
                                    foreach ($styles as $k=>$style){
                                        if($style['report_styling_column_type'] == 'element'){
                                             $elements[$style['report_styling_column_no']][$style['report_styling_key']] = $style['report_styling_value'];
                                        }
                                    }
                                }
                              // dd($elements);
                                $count_elements = 0;
                                $headings = [];
                                $column_keys = [];
                                $criteria_active = [];
                                $column_show = [];
                                $column_types = [];
                                if(count($elements) != 0){
                                    foreach ($elements as $element){
                                        if($element['criteria_active'] == 1){
                                            array_push($headings,$element['heading_name']);
                                            array_push($column_keys,$element['key_name']);
                                            array_push($column_types,$element['column_type']);
                                            array_push($column_show,$element['column_toggle']);
                                        }
                                    }
                                    $count_elements = count($column_show);
                                }
                                /*if($data['report']->report_column_toggle != "" && $data['report']->report_column_toggle != null){
                                    $headings = explode(',',$data['report']->report_column_titles);
                                    $column_types = explode(',',$data['report']->report_column_types);
                                    $column_keys = explode(',',$data['report']->report_column_key);
                                    $column_toggle = explode(',',$data['report']->report_column_toggle);
                                    $count_column_toggle = count($column_toggle);
                                }*/
 // end outer array
                            @endphp
                           {{-- @include('reports.report_static_criteria')--}}
                            @if($count_elements != 0 && count($saveDynamicCriteria) == 0 )
                                <style>
                                    #report_filter_block ul.select2-selection__rendered{    line-height: 0 !important;}
                                    #report_filter_block li.select2-selection__choice{font-size: 11px !important;padding: 7px 5px 8px 5px !important;    line-height: 0 !important;}
                                    #report_filter_block span.select2-selection__clear{display: none}
                                    .report-inner_clause-or-btn{
                                        position: relative;
                                        padding: 5px 4px 5px 4px;
                                    }
                                    .report-inner_clause-and-btn{
                                        position: relative;
                                        padding: 5px 4px 5px 4px;
                                    }
                                    .inner_clause_item{
                                        font-size: 9px;
                                        padding: 6px 9px 5px 7px;
                                        width: 30px;
                                    }
                                    .inner_clause_item:empty{padding: 0;}
                                    .report-filter-and-del-btn {
                                        margin-top: 6px !important;
                                        margin-bottom: unset !important;
                                    }
                                    .label-color{color: #2a8228;}
                                </style>
                            <div id="kt_repeater_1">
                                <div data-repeater-list="outer_filterList">
                                    <div data-repeater-item class="outer-filter_block" outer-id="0">
                                        <div class="col-lg-12" style="position: relative">
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <div class="row">
                                                        <div class="col-lg-3"><label class="erp-col-form-label label-color">Name:</label></div>
                                                        <div class="col-lg-3"><label class="erp-col-form-label label-color">Condition:</label></div>
                                                        <div class="col-lg-3"><label class="erp-col-form-label label-color">Value:</label></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 text-right">
                                                    <input type="hidden" value="" name="outer_clause" class="report-filter-and-del-btn_input">
                                                    <button data-repeater-delete="" type="button" class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                        ?
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inner-repeater">
                                            <div data-repeater-list="inner_filterList">
                                                <div data-repeater-item class="col-lg-12 filter_block" inner-id="0">
                                                    <div class="row form-group-block">
                                                        <div class="col-lg-10">
                                                            <div class="row">
                                                                <div class="col-lg-3">
                                                                    {{--<label class="erp-col-form-label">Filter Name:</label>--}}
                                                                    <div class="erp-select2 report-select2">
                                                                        <select class="form-control erp-form-control-sm report_fields_name" name="key">
                                                                            <option value="0">Select</option>
                                                                            @for($i=0;$i<$count_elements;$i++)
                                                                                <option value="{{strtolower($column_keys[$i])}}">{{ucwords($headings[$i])}}</option>
                                                                            @endfor
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-9">
                                                                    <div class="row">
                                                                        <input type="hidden" class="report_value_column_type_name" name="key_type"/>
                                                                        <div class="col-lg-4" id="report_filter_types">
                                                                            {{--<label class="erp-col-form-label">Condition:</label>--}}
                                                                            <div class="erp-select2 report-select2">
                                                                                <select class="form-control erp-form-control-sm report_condition" name="conditions">
                                                                                    <option value="0">Select</option>
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
                                                                        <div class="col-lg-8" id="report_filter_block">
                                                                            <div class="row" id="fields_values">
                                                                                <div class="col-lg-12 fields_values_append">
                                                                                    {{--<label class="erp-col-form-label">Value:</label>--}}
                                                                                    {{--
                                                                                    <div class="erp-select2">
                                                                                        <select disabled class="form-control erp-form-control-sm kt_select_none" multiple name="val">
                                                                                            <option></option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <input type="text" disabled name="val" class="form-control erp-form-control-sm text-right validNumber">
                                                                                    --}}
                                                                                </div>
                                                                            </div>
                                                                            <div class="row" id="number_between">
                                                                                <div class="col-lg-12">
                                                                                    <div class="erp-row">
                                                                                        <div class="col-lg-6">
                                                                                            {{--<label class="erp-col-form-label">From:</label>--}}
                                                                                            <input type="text" disabled name="val" class="form-control erp-form-control-sm text-right validNumber">
                                                                                        </div>
                                                                                        <div class="col-lg-6">
                                                                                            {{--<label class="erp-col-form-label">To:</label>--}}
                                                                                            <input type="text" disabled name="val_to" class="form-control erp-form-control-sm text-right validNumber">
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                            <div class="row" id="date_between">
                                                                                <div class="col-lg-12">
                                                                                    {{--<label class="erp-col-form-label">Select Date Range:</label>--}}
                                                                                    <div class="erp-selectDateRange">
                                                                                        <div class="input-daterange input-group kt_datepicker_5">
                                                                                            <input type="text" disabled class="form-control erp-form-control-sm" name="val" />
                                                                                            <div class="input-group-append">
                                                                                                <span class="input-group-text erp-form-control-sm">To</span>
                                                                                            </div>
                                                                                            <input type="text" disabled class="form-control erp-form-control-sm" name="val_to" />
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
                                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger" style="padding: 5px 0 4px 5px">
                                                                <i class="la la-minus-circle"></i>
                                                            </a>
                                                            <input type="hidden" name="inner_clause_item" class="inner_clause_item_input" value="">
                                                            <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand inner_clause_item" disabled readonly>
                                                                ?
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-10"></div>
                                                <div class="col-lg-2  text-right" style="padding-right: 20px;">
                                                    <div class="btn-group btn-group btn-group-sm" role="group" aria-label="...">
                                                        <button type="button" class="btn btn-success btn-sm" style="width: 30px;height: 30px;padding: 6px;"><i class="la la-filter" style="font-size: 12px"></i></button>
                                                        <button data-repeater-create type="button" class="btn btn-bold btn-sm btn-label-brand report-inner_clause-or-btn" style="width: 32px;font-size: 9px;height: 30px;padding: 8px 7px;">OR</button>
                                                        <button data-repeater-create type="button" class="btn btn-bold btn-sm btn-label-brand report-inner_clause-and-btn" style="width: 32px;font-size: 9px;height: 30px;padding: 8px 4px;">AND</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="btn-group btn-group btn-group-sm" role="group" aria-label="...">
                                            <button type="button" class="btn btn-success btn-sm" style="padding: 5px 2px 5px 5px;"><i class="la la-filter"></i></button>
                                            <button data-repeater-create type="button" class="btn btn-brand btn-sm outer_clause-or-btn" style="padding: 5px 9px 5px 8px;border-right: 2px solid #dadada;">OR</button>
                                            <button data-repeater-create type="button" class="btn btn-brand btn-sm outer_clause-and-btn" style="padding: 5px 6px 5px 5px;">AND</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(count($saveDynamicCriteria) != 0)
                                <style>
                                    #report_filter_block ul.select2-selection__rendered{    line-height: 0 !important;}
                                    #report_filter_block li.select2-selection__choice{font-size: 11px !important;padding: 7px 5px 8px 5px !important;    line-height: 0 !important;}
                                    #report_filter_block span.select2-selection__clear{display: none}
                                    .report-inner_clause-or-btn{
                                        position: relative;
                                        padding: 5px 4px 5px 4px;
                                    }
                                    .report-inner_clause-and-btn{
                                        position: relative;
                                        padding: 5px 4px 5px 4px;
                                    }
                                    .inner_clause_item{
                                        font-size: 9px;
                                        padding: 6px 9px 5px 7px;
                                        width: 30px;
                                    }
                                    .inner_clause_item:empty{padding: 0;}
                                    .report-filter-and-del-btn {
                                        margin-top: 6px !important;
                                        margin-bottom: unset !important;
                                    }
                                    .label-color{color: #2a8228;}
                                </style>
                                <div id="kt_repeater_1">
                                    <div data-repeater-list="outer_filterList">
                                        @foreach($saveDynamicCriteria as $uSDC)
                                            <div data-repeater-item class="outer-filter_block" outer-id="0">
                                                <div class="col-lg-12" style="position: relative">
                                                    <div class="row">
                                                        <div class="col-lg-10">
                                                            <div class="row">
                                                                <div class="col-lg-3"><label class="erp-col-form-label label-color">Name:</label></div>
                                                                <div class="col-lg-3"><label class="erp-col-form-label label-color">Condition:</label></div>
                                                                <div class="col-lg-3"><label class="erp-col-form-label label-color">Value:</label></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 text-right">
                                                            @if($uSDC['outer_clause'] != null)
                                                                <input type="hidden" value="{{$uSDC['outer_clause']}}" name="outer_clause" class="report-filter-and-del-btn_input">
                                                                <button data-repeater-delete="" type="button" class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                                    {{$uSDC['outer_clause']}}
                                                                </button>
                                                            @else
                                                                <input type="hidden" value="" name="outer_clause" class="report-filter-and-del-btn_input">
                                                                <button data-repeater-delete="" type="button" class="btn btn-danger btn-sm report-user-filter-and-del-btn report-filter-and-del-btn">
                                                                    ?
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="inner-repeater">
                                                    <div data-repeater-list="inner_filterList">
                                                        @foreach($uSDC['inner_filterList'] as $uSDC_inner)
                                                            @php
                                                                $key = $uSDC_inner['key'];
                                                                $key_type = $uSDC_inner['key_type'];
                                                                $condition_name = $uSDC_inner['conditions'];
                                                                $val = $uSDC_inner['val'];
                                                                $val_to = isset($uSDC_inner['val_to'])?$uSDC_inner['val_to']:"";
                                                                $inner_clause_item = $uSDC_inner['inner_clause_item'];
                                                            @endphp
                                                            <div data-repeater-item class="col-lg-12 filter_block" inner-id="0">
                                                                <div class="row form-group-block">
                                                                    <div class="col-lg-10">
                                                                        <div class="row">
                                                                            <div class="col-lg-3">
                                                                                {{--<label class="erp-col-form-label">Filter Name:</label>--}}
                                                                                <div class="erp-select2 report-select2">
                                                                                    <select class="form-control erp-form-control-sm report_fields_name" name="key">
                                                                                        <option value="0">Select</option>
                                                                                        @for($i=0;$i<$count_elements;$i++)
                                                                                            <option value="{{strtolower($column_keys[$i])}}" {{strtolower($key) == strtolower($column_keys[$i]) ? "selected":"" }}>{{ucwords($headings[$i])}}</option>
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-9">
                                                                                <div class="row">
                                                                                    <input type="hidden" value="{{isset($key_type)?$key_type:""}}" class="report_value_column_type_name" name="key_type"/>
                                                                                    <div class="col-lg-4" id="report_filter_types">
                                                                                        {{--<label class="erp-col-form-label">Condition:</label>--}}
                                                                                        @if($uSDC_inner['key_type'] != null)
                                                                                            @php
                                                                                                $conditions = \App\Models\TblSoftFilterType::where('filter_type_data_type_name',$key_type)->where('filter_type_entry_status',1)->get();
                                                                                            @endphp
                                                                                            <div class="erp-select2 report-select2">
                                                                                                <select class="form-control erp-form-control-sm report_condition" name="conditions">
                                                                                                    <option value="0">Select</option>
                                                                                                    @foreach($conditions as $condition)
                                                                                                        <option value="{{$condition->filter_type_value}}" {{$condition->filter_type_value == $condition_name?"selected":""}}>{{$condition->filter_type_title}}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="erp-select2 report-select2">
                                                                                                <select class="form-control erp-form-control-sm report_condition" name="conditions">
                                                                                                    <option value="0">Select</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    <style>
                                                                                        @php
                                                                                            $varchar_dip = "";
                                                                                            $date_dip = "";
                                                                                            $num_dip = "";
                                                                                            $num_betw_dip = "";
                                                                                        @endphp
                                                                                        @if($key_type == "varchar2")
                                                                                            @php $varchar_dip = "none"; @endphp
                                                                                        @endif
                                                                                        @if($key_type == "date")
                                                                                            @php $date_dip = "none";  @endphp
                                                                                        @endif
                                                                                        @if(($key_type == "number" || $key_type == "float") && $condition_name != 'between')
                                                                                            @php $num_dip = "none";  @endphp
                                                                                        @endif
                                                                                        @if(($key_type == "number" || $key_type == "float") && $condition_name == 'between')
                                                                                            @php $num_betw_dip = "none";  @endphp
                                                                                        @endif
                                                                                    .erp-row{
                                                                                            display: flex;
                                                                                            flex-wrap: wrap;
                                                                                            margin-right: -10px;
                                                                                            margin-left: -10px;
                                                                                        }
                                                                                    </style>
                                                                                    <div class="col-lg-8" id="report_filter_block">
                                                                                        <div style="display: {{$num_betw_dip}}{{$date_dip}}{{$date_dip}}" class="row" id="fields_values">
                                                                                            @if($key_type == "varchar2")
                                                                                                <div class="col-lg-12 fields_values_append">
                                                                                                    <div class="erp-select2">
                                                                                                        <select class="form-control erp-form-control-sm kt_select_report_multi91" multiple name="val[]">
                                                                                                            @foreach($val as $multiVal)
                                                                                                                <option value="{{$multiVal}}" selected>{{$multiVal}}</option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @elseif(($key_type == "number" || $key_type == "float") && $condition_name != 'between')
                                                                                                <div class="col-lg-12 fields_values_append">
                                                                                                    <input type="text" value="{{$val}}" name="val" class="form-control erp-form-control-sm text-right validNumber">
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="col-lg-12 fields_values_append">
                                                                                                    {{--<label class="erp-col-form-label">Value:</label>--}}
                                                                                                    {{--
                                                                                                    <div class="erp-select2">
                                                                                                        <select disabled class="form-control erp-form-control-sm kt_select_none" multiple name="val">
                                                                                                            <option></option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <input type="text" disabled name="val" class="form-control erp-form-control-sm text-right validNumber">
                                                                                                    --}}
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div style="display: {{$num_dip}}{{$date_dip}}{{$varchar_dip}}" class="row" id="number_between">
                                                                                            @if(($key_type == "number" || $key_type == "float") && $condition_name == 'between')
                                                                                                <div class="col-lg-12">
                                                                                                    <div class="erp-row">
                                                                                                        <div class="col-lg-6">
                                                                                                            {{--<label class="erp-col-form-label">From:</label>--}}
                                                                                                            <input type="text" name="val" value="{{$val}}" class="form-control erp-form-control-sm text-right validNumber">
                                                                                                        </div>
                                                                                                        <div class="col-lg-6">
                                                                                                            {{--<label class="erp-col-form-label">To:</label>--}}
                                                                                                            <input type="text" name="val_to" value="{{$val_to}}" class="form-control erp-form-control-sm text-right validNumber">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="col-lg-12">
                                                                                                    <div class="erp-row">
                                                                                                        <div class="col-lg-6">
                                                                                                            {{--<label class="erp-col-form-label">From:</label>--}}
                                                                                                            <input type="text" disabled name="val" class="form-control erp-form-control-sm text-right validNumber">
                                                                                                        </div>
                                                                                                        <div class="col-lg-6">
                                                                                                            {{--<label class="erp-col-form-label">To:</label>--}}
                                                                                                            <input type="text" disabled name="val_to" class="form-control erp-form-control-sm text-right validNumber">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div style="display: {{$num_betw_dip}}{{$num_dip}}{{$varchar_dip}}" class="row" id="date_between">
                                                                                            {{--<label class="erp-col-form-label">Select Date Range:</label>--}}
                                                                                            @if($key_type == "date")
                                                                                                <div class="col-lg-12">
                                                                                                    <div class="erp-selectDateRange">
                                                                                                        <div class="input-daterange input-group kt_datepicker_5">
                                                                                                            <input type="text" class="form-control erp-form-control-sm" name="val" value="{{$val}}"/>
                                                                                                            <div class="input-group-append">
                                                                                                                <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                            </div>
                                                                                                            <input type="text" class="form-control erp-form-control-sm" name="val_to"  value="{{$val_to}}"/>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="col-lg-12">
                                                                                                    <div class="erp-selectDateRange">
                                                                                                        <div class="input-daterange input-group kt_datepicker_5">
                                                                                                            <input type="text" disabled class="form-control erp-form-control-sm" name="val" />
                                                                                                            <div class="input-group-append">
                                                                                                                <span class="input-group-text erp-form-control-sm">To</span>
                                                                                                            </div>
                                                                                                            <input type="text" disabled class="form-control erp-form-control-sm" name="val_to" />
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 text-right">
                                                                        <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger" style="padding: 5px 0 4px 5px">
                                                                            <i class="la la-minus-circle"></i>
                                                                        </a>
                                                                        @if($inner_clause_item != null && $inner_clause_item != "")
                                                                            <input type="hidden" name="inner_clause_item" class="inner_clause_item_input" value="{{$inner_clause_item}}">
                                                                            <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand inner_clause_item" disabled readonly>
                                                                                {{$inner_clause_item}}
                                                                            </a>
                                                                        @else
                                                                            <input type="hidden" name="inner_clause_item" class="inner_clause_item_input" value="">
                                                                            <a href="javascript:;" class="btn btn-bold btn-sm btn-label-brand inner_clause_item" disabled readonly>
                                                                                ?
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach  {{--end inner cluse--}}
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-10"></div>
                                                        <div class="col-lg-2  text-right" style="padding-right: 20px;">
                                                            <div class="btn-group btn-group btn-group-sm" role="group" aria-label="...">
                                                                <button type="button" class="btn btn-success btn-sm" style="width: 30px;height: 30px;padding: 6px;"><i class="la la-filter" style="font-size: 12px"></i></button>
                                                                <button data-repeater-create type="button" class="btn btn-bold btn-sm btn-label-brand report-inner_clause-or-btn" style="width: 32px;font-size: 9px;height: 30px;padding: 8px 7px;">OR</button>
                                                                <button data-repeater-create type="button" class="btn btn-bold btn-sm btn-label-brand report-inner_clause-and-btn" style="width: 32px;font-size: 9px;height: 30px;padding: 8px 4px;">AND</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="btn-group btn-group btn-group-sm" role="group" aria-label="...">
                                                <button type="button" class="btn btn-success btn-sm" style="padding: 5px 2px 5px 5px;"><i class="la la-filter"></i></button>
                                                <button data-repeater-create type="button" class="btn btn-brand btn-sm outer_clause-or-btn" style="padding: 5px 9px 5px 8px;border-right: 2px solid #dadada;">OR</button>
                                                <button data-repeater-create type="button" class="btn btn-brand btn-sm outer_clause-and-btn" style="padding: 5px 6px 5px 5px;">AND</button>
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
    @endpermission
@endsection
@section('pageJS')
    <script src="{{ asset('js/pages/js/report-static-form.js') }}" type="text/javascript"></script>
    <script>
        $('.kt_select2').select2();
    </script>
@endsection

@section('customJS')
    <script>
        $("#kt_datepicker_3").datepicker({
            format: "dd-mm-yyyy",
        });
        $("#kt_to_date").datepicker({
            format: "dd-mm-yyyy",
        });
        $("#kt_from_time").timepicker({
            minuteStep:1,
        });
        $("#kt_to_time").timepicker({
            minuteStep:1,
        });
    </script>
    <script>
        var column_type_name = "";
        var cloumnsList = [];
        var column_keys = "";
@if(isset($count_elements) && $count_elements != 0)
    @for($i=0;$i<$count_elements;$i++)
        cloumnsList['{{$column_keys[$i]}}'] = '{{$column_types[$i]}}';
    @endfor
        column_keys = '<option value="0">Select</option>';
    @for($i=0;$i<$count_elements;$i++)
        column_keys += '<option value="{{strtolower($column_keys[$i])}}">{{ucwords($headings[$i])}}</option>';
    @endfor
@endif
    </script>
    <script src="{{ asset('js/pages/js/report/data-repeater.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/report/req-func.js') }}" type="text/javascript"></script>
    
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode open_inline__help',
                'message':'Enter Barcode',
                'require':true,
                'readonly':true
                //  'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id'];
        var remain_req = 0; // variable use start from in funcAddSelectedProductToFormGrid()
    </script>
    
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        var form_modal_type = 'sale_report';
    </script>
    @include('reports.script')
    <script>
        
        function funcAfterAddRow(){}

        var emptyArr = ["",undefined,'NaN',NaN,null,"0",0];
        function funcAddSelectedProductToFormGrid(tr){
            var cloneTr = tr.clone();
            var data_product_barcode = $(cloneTr).attr('data-product_barcode');
            var addProd = true;
            $('table.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
                var thix = $(this);
                var pd_barcode = thix.find('input[data-id="pd_barcode"]').val();
                if(pd_barcode == data_product_barcode){
                 //   toastr.error("Product already added");
                    addProd = false;
                }
            })
            if(addProd){
                remain_req += 1;
                /*cd("remain_req1: " + remain_req);*/
                $('table.table_pit_list>thead.erp_form__grid_header>tr').find('#pd_barcode').val(data_product_barcode);
                var trTh = $('table.table_pit_list>thead.erp_form__grid_header>tr').find('#pd_barcode').parents('tr');
                var formData = {
                    form_type : form_modal_type,
                    val : data_product_barcode,
                    autoClick : true
                }
                get_barcode_detail(13, trTh, form_modal_type, formData);
            }
        }
        function funSetProductCustomFilter(arr){
            var len = arr['len'];
            var product = arr['product'];

            for (var i =0;i<len;i++){
                var row = product[i];
                var newTr = "<tr  data-product_barcode='"+row['product_barcode_barcode']+"'>";
                newTr += "<td>"+(!valueEmpty(row['product_barcode_barcode'])?row['product_barcode_barcode']:"")+"</td>";
                newTr += "<td>"+(!valueEmpty(row['product_name'])?row['product_name']:"")+"</td>";
                newTr += "<td>"+(!valueEmpty(row['uom_name'])?row['uom_name']:"")+"</td>";
                newTr += "<td>"+(!valueEmpty(row['product_barcode_packing'])?row['product_barcode_packing']:"")+"</td>";
                newTr += "<td class='text-right'>"+(!valueEmpty(row['net_tp'])?parseFloat(row['net_tp']).toFixed(3):"")+"</td>";
                newTr += "<td class='text-right'>"+(!valueEmpty(row['sale_rate'])?parseFloat(row['sale_rate']).toFixed(3):"")+"</td>";
                newTr += '<td class="text-center">\n' +
                    '     <div style="position: relative;top: -5px;">\n' +
                    '       <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">\n' +
                    '           <input type="checkbox" class="addCheckedProduct" data-id="add_prod">\n' +
                    '               <span></span>\n' +
                    '        </label>\n' +
                    '     </div></td>';
                newTr += "</tr>";

                $('table.table_pitModal').find('tbody.erp_form__grid_body').append(newTr);
            }
        }
        function funcSrReInit(){
            var sr_no = 1;
            $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
                $(this).find('td:first-child').html(sr_no);
                var allInput = $(this).find('input');
                var len = allInput.length
                for(v=0;v<len;v++){
                    var dataId = $(allInput[v]).attr('data-id');
                    var newNameVal = "pd["+sr_no+"]["+dataId+"]"
                    $(allInput[v]).attr('name',newNameVal);
                }
                sr_no = sr_no + 1;
            });
        }
        
        $(document).on('click','.addCheckedProductAll',function(){
            if($(this).prop('checked')) {
                $('table.table_pitModal>tbody>tr').each(function(){
                    var thix = $(this);
                    thix.find('.addCheckedProduct').prop('checked',true)
                    funcAddSelectedProductToFormGrid(thix);
                })
            }
        });
    </script>
    <script>
        
        $(document).ready(function(){
            $("#all").click(function(){
                $("#inputDays").hide();
                $('#kt_datepicker_3').val('');
                var allDate = '01-01-2000';
                $('#kt_datepicker_3').val(allDate);
                $('#date_from').val(allDate);
                $('#date').val(allDate);
            });
            $("#today").click(function(){
                $("#inputDays").hide();
                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();

                var today = (day<10 ? '0' : '') + day + '-' +
                (month<10 ? '0' : '') + month + '-' +
                d.getFullYear();
                $('#kt_datepicker_3').val(today);
                $('#date_from').val(today);
                $('#date').val(today);
            });
            $("#yesterday").click(function(){
                $("#inputDays").hide();
                var date = new Date();
                date.setDate(date.getDate() - 1);
                var nd = new Date(date);

                var month = nd.getMonth()+1;
                var day = nd.getDate();

                var yesterday = (day<10 ? '0' : '') + day + '-' +
                (month<10 ? '0' : '') + month + '-' +
                nd.getFullYear();
                $('#kt_datepicker_3').val(yesterday);
                $('#date_from').val(yesterday);
                $('#date').val(yesterday);
            });
            $("#last_7_days").click(function(){
                $("#inputDays").hide();
                var date = new Date();
                date.setDate(date.getDate() - 7);
                var nd = new Date(date);

                var month = nd.getMonth()+1;
                var day = nd.getDate();

                var last_7_days = (day<10 ? '0' : '') + day + '-' +
                (month<10 ? '0' : '') + month + '-' +
                nd.getFullYear();
                $('#kt_datepicker_3').val(last_7_days);
                $('#date_from').val(last_7_days);
                $('#date').val(last_7_days);
            });
            $("#last_30_days").click(function(){
                $("#inputDays").hide();
                var date = new Date();
                date.setDate(date.getDate() - 30);
                var nd = new Date(date);

                var month = nd.getMonth()+1;
                var day = nd.getDate();

                var last_30_days = (day<10 ? '0' : '') + day + '-' +
                (month<10 ? '0' : '') + month + '-' +
                nd.getFullYear();
                $('#kt_datepicker_3').val(last_30_days);
                $('#date_from').val(last_30_days);
                $('#date').val(last_30_days);
            });
            
            $("#last_days").click(function(){
                $("#inputDays").show();
                $("#days").keyup(function(){
                    var daysNumber = $('#days').val();
                    var date = new Date();
                    date.setDate(date.getDate() - daysNumber);
                    var nd = new Date(date);

                    var month = nd.getMonth()+1;
                    var day = nd.getDate();

                    var manual_days = (day<10 ? '0' : '') + day + '-' +
                    (month<10 ? '0' : '') + month + '-' +
                    nd.getFullYear();
                    $('#kt_datepicker_3').val(manual_days);
                    $('#date_from').val(manual_days);
                    $('#date').val(manual_days);
                });
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            product_id_fun(90,false);
            product_name_fun(90,false);
            product_name_fun(91,true);
            chart_account_fun(90,false);
            chart_account_fun(91,false);
            customer_id_fun(92,false);
            supplier_id_fun(92,false);
            marchant_id_fun(92,false);
        })
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
        
        $(document).on('change','.sub_qry_condition',function(){
            var row_id =  $(this).parents('.sub_qry').attr('row-id');
            var val = $(this).parents('.sub_qry').find(".sub_qry_value_fields input[name='sub_qry["+row_id+"][val]']").val();
            if($(this).val() == 'between'){
                $(this).parents('.sub_qry').find('.sub_qry_value_fields').html('<div class="row"><div class="col-lg-6"><input type="text" value="'+val+'" class="form-control erp-form-control-sm text-right validNumber" name="sub_qry['+row_id+'][val]"></div><div class="col-lg-6"><input type="text" value="'+val+'" class="form-control erp-form-control-sm text-right" name="sub_qry['+row_id+'][val_to]"></div></div>');
            }else{
                $(this).parents('.sub_qry').find('.sub_qry_value_fields').html('<input type="text" value="'+val+'" class="form-control erp-form-control-sm text-right validNumber" name="sub_qry['+row_id+'][val]">');
            }
        });
        $(document).on('change','.select_all_branch',function(){
            var branches = <?=$data['branches']?>;
            
            var select_all_branch = document.getElementById('select_all_branch').checked;
            var newArr = [];
            if(select_all_branch == true)
            {
                var valArr = ["1","2","3","4","5","6","7","8","9","10","11","12"], // array of option values
                i = 0, size = valArr.length, // index and array size declared here to avoid overhead
                $options = $('#report_branch_name option'); // options cached here to avoid overhead of fetching inside loop

                // run the loop only for the given values
                for(i; i < size; i++){
                    // filter the options with the specific value and select them
                    $options.filter('[value="'+valArr[i]+'"]').prop('selected', true);
                }
            }
            if(select_all_branch == false)
            {
                $options = $('#report_branch_name option'); // options cached here to avoid overhead of fetching inside loop
                branches.forEach((element, index, array) => {
                    
                    $options.filter('[value="'+element.branch_id+'"]').prop('selected', false);
                    if(element.default_branch == "1"){
                         console.log(element.branch_id);
                        $options.filter('[value="'+element.branch_id+'"]').prop('selected', true);
                        $('#report_branch_name option').val().trigger('change');
                    }
                });
                
            }
            
        });

       /* $('.kt_select_none').select2({
            placeholder: "Search....."
        });*/

        $('#reportUserSettingSave').click(function(){
            var qryData = new FormData(document.getElementById('report_static_form'));
            console.log(qryData);
            var formData = {
                document_type : '{{isset($data["report"]->report_case)?$data["report"]->report_case:""}}',
                qryData : $('#report_static_form').serialize()
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : '/common/user-report-setting', //GetAllData userReportSetting
                dataType	: 'json',
                data        :  $('#report_static_form').serialize(),
                success: function(response) {
                    if(response.status == 'success'){
                        toastr.success(response.message);
                    }
                }
            });
        });
    </script>
    
@endsection

