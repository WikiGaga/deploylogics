@extends('layouts.layout')
@section('title', 'Auto Purchase Demand')

@section('pageCSS')
    <style>
        .p-0{
            padding: 0!important;
        }
        #stepper{
            min-height: 160px;
        }
        .select2-selection__choice{
            white-space: normal;
        }
        .fa-rotate-180{
            padding: 0;
            font-size: 1.3rem;
        }
        .extra-stock-table thead.erp_form__grid_header>tr>th:last-child{
            /* position: unset; */
            background-color: #f9f9f9 !important;
        }
        .extra-stock-table tbody.erp_form__grid_body>tr>td:last-child{
            position: unset;
            background-color: inherit;
        }
        .extra-stock-table.JColResizer > tbody > tr > td{
            padding: 3px 5px !important;
        }
        .extra-stock-table thead.erp_form__grid_header>tr>th:nth-child(-n+4){
            position: sticky;
        }
        .grn_green {
            background: #4c9a2ac7 !important;
            color: #fff !important;
        }
        .radioInput{
            overflow: inherit !important;
            padding-left: 10px !important;
        }
        .erp_form__grid_body_total td{
            z-index: 9;
        }
        .radioPending{
            width: 51.2969px;min-width: 51.2969px;max-width: 51.2969px !important; text-align:center;position: sticky;right: 137px;background: #ddd !important; padding-left:8px !important;z-index: 997 !important;
        }
        .radioApprove{
            width: 51.2969px;min-width: 51.2969px;max-width: 51.2969px !important; text-align:center;position: sticky; right: 86px; background: rgb(221, 221, 221) !important; padding-left: 8px !important;z-index: 998 !important;
        }
        .radioReject{
            width: 51.2969px;min-width: 51.2969px;max-width: 51.2969px !important; text-align:center;position: sticky; right: 35px; background: rgb(221, 221, 221) !important; padding-left: 8px !important;z-index: 999 !important;
        }
        td .kt-radio.kt-radio--brand {
            margin-left: 10px !important;
        }
        .bottom-row td{
            z-index: 1000 !important;
        }
        .extra-stock-table thead.erp_form__grid_header>tr>th:last-child{
            position: unset !important;
            z-index: 0 !important;
        }
        .bottom-row td:last-child{
            /* position: initial !important; */
            z-index: 0 !important;
        }
        .kt-portlet__head.kt-portlet__head--lg.erp-header-sticky{
            z-index: 9999;
        }
        .kt-header.kt-grid__item.kt-header--fixed {
            z-index: 9999;
        }
        .toast,.toast div{
            z-index: 999999999999 !important;
        }
        #innerReqTable td{
            line-height: 29px;
        }
        #innerReqTable td{
            padding: 0px 5px !important;
        }
        #toast-container{
            display: inline-block;
            z-index: 999999999999999999;
        }
    </style>
@endsection

@section('content')

    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $id = '';
            $mainSuggesStockRequest = 0;
        }
        if($case == 'edit'){
            $id                     = $data['id'];
            $mainAdType             = $data['current']->ad_type;
            $mainProductGroup       = $data['current']->group_id;
            $mainDemandIds          = $data['current']->demand_id;
            $mainSupplierIds        = $data['current']->supplier_id;
            $mainLocationIds        = $data['current']->location_id;
            $mainPriorityCheck      = $data['current']->priority_check;
            $mainConsumptionType    = $data['current']->consumption_type;
            $mainConsumptionBase    = $data['current']->consumption_base;
            $mainConsumptionDays    = $data['current']->consumption_days;
            $mainConsumptionSDate   = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->consumption_start_date))));
            $mainConsumptionEDate   = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->consumption_end_date))));
            $mainConsumptionBranch  = $data['current']->consumption_branch_id;
            $mainSugRequestBranch   = $data['current']->suggest_stock_request_branch;
            $mainSuggesStockRequest = isset($data['current']->suggest_stock_request) ? $data['current']->suggest_stock_request : 0 ;
            $mainSuggestLeadDays    = $data['current']->suggest_stock_request_lead_days;
            $createStockRequests    = $data['current']->create_stock_request;

        }
        $form_type = $data['form_type'];
        $menu_id = $data['menu_id'];
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="auto_demand_form" class="kt-form" method="post" action="{{ action('Purchase\AutoDemandController@store' , isset($id)?$id:'') }}">
    <input type="hidden" value='{{$form_type}}' id="form_type">
    <input type="hidden" value='{{$menu_id}}' id="menu_id">
    <input type="hidden" value='{{isset($id)?$id:""}}' id="form_id">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block mb-4">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    {{$data['document_code']}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block" id="stepper">
                    <div class="col-lg-1" style="align-self: center;">
                        <button class="btn btn-outline-primary btn-navigation" id="stepBackward" disabled data-task="previous">
                            <i class="la la-angle-left la-4x"></i>
                        </button>
                    </div>
                    <div class="col-lg-10" style="align-self: center;">
                        <div class="row">
                            <div class="col-lg-12">
                                {{-- Put the Fields Here --}}
                                <div id="step1" class="stepactive"> {{-- Setp 1 Start --}}
                                    <div class="row form-group-block">
                                        <div class="col-lg-4">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Date:</label>
                                                <div class="col-lg-7">
                                                    <div class="input-group date">
                                                        @if(isset($data['id']))
                                                            @php $mainLeadDate =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->ad_date)))); @endphp
                                                        @else
                                                            @php $mainLeadDate =  date('d-m-Y'); @endphp
                                                        @endif
                                                        <input type="text" name="ad_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$mainLeadDate}}" id="kt_datepicker_3" @if($case == 'edit') disabled @endif/>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="la la-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Lead Date:</label>
                                                <div class="col-lg-7">
                                                    <div class="input-group date">
                                                        @if(isset($data['id']))
                                                            @php $lead_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->lead_date)))); @endphp
                                                        @else
                                                            @php $lead_date =  date('d-m-Y'); @endphp
                                                        @endif
                                                        <input type="text" name="lead_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$lead_date}}" id="kt_datepicker_3" @if($case == 'edit') disabled @endif/>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="la la-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">AD Type: <span class="required">*</span></label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select name="ad_type"  id="ad_type" class="moveIndex kt-select2 form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                                            <option value="NONE" @if(isset($mainAdType) && $mainAdType == "NONE") selected @endif>Select</option>
                                                            <option value="DEMAND" @if(isset($mainAdType) && $mainAdType == "DEMAND") selected @endif>DEMAND</option>
                                                            <option value="SUPPLIER" @if(isset($mainAdType) && $mainAdType == "SUPPLIER") selected @endif>SUPPLIER</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group-block">
                                        <div class="col-lg-4 @if($case == 'edit' && $mainAdType == 'DEMAND') d-block @else d-none @endif" id="demands_select">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Demands: <span class="required">*</span></label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select name="demands[]"  id="demands" multiple class="moveIndex kt-select2 form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                                            @if($case == 'new')
                                                                <option value="0">Select</option>
                                                                @foreach($data['demands'] as $demand)
                                                                    <option value="{{$demand->demand_id}}" @if($case == 'edit' && in_array($demand->demand_id , $mainDemandIds)) selected @endif >{{$demand->demand_no}}</option>
                                                                @endforeach
                                                            @else
                                                                {!! $data['demands'] !!}
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 @if($case == 'edit' && $mainAdType == 'SUPPLIER') d-block @else d-none @endif" id="suppliers_select">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Suppliers: <span class="required">*</span></label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select name="suppliers[]"  id="suppliers" multiple class="moveIndex kt-select2 form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                                            @if($case == 'new')
                                                                <option value="0">Select</option>
                                                                @foreach($data['suppliers'] as $supplier)
                                                                    <option value="{{$supplier->supplier_id}}" @if($case == 'edit' && in_array($supplier->supplier_id , $mainSupplierIds)) selected @endif>{{$supplier->supplier_name}}</option>
                                                                @endforeach
                                                            @else
                                                                {!! $data['suppliers'] !!}
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Product Group:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select name="product_groups[]"  id="product_groups" multiple class="moveIndex kt-select2 form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                                            @if($case == 'new')
                                                                <option value="0">Select</option>
                                                                @foreach($data['group_item'] as $group_item)
                                                                    <option value="{{$group_item->group_item_id}}" @if($case == 'edit' && in_array($group_item->group_item_id , $mainProductGroup)) selected @endif>{{$group_item->group_item_name_string}}</option>
                                                                @endforeach
                                                            @else
                                                                {!! $data['group_item'] !!}
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock Location:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control kt-select2 erp-form-control-sm moveIndex" multiple id="kt-select2_validate" name="locations[]" @if($case == 'edit') disabled @endif>
                                                        @if($case == "new")
                                                        @foreach($data['display_location'] as $display_location)
                                                            @if($case == "new")
                                                                <option value="{{$display_location->display_location_id}}">{{$display_location->display_location_name_string}}</option>
                                                            @endif
                                                        @endforeach
                                                        @else
                                                            {!! $data['display_location'] !!}
                                                        @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- Step 1 End --}}
                                <div id="step2" class="d-none"> {{-- Step 2 Start --}}
                                    <div class="form-group row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="erp-col-form-label">Priority Check Suggestion:</label>
                                                        </div>
                                                        <div class="col-lg-12 erp-col-form-label py-0">
                                                            <div class="radio-list">
                                                                <label class="kt-radio kt-radio--bold kt-radio--success">
                                                                    <input type="radio" checked id="priority-check-reorder" @if(isset($mainPriorityCheck) && $mainPriorityCheck == 'REORDER') selected @endif value="REORDER" name="priority_check" @if($case == 'edit') disabled @endif/>
                                                                    <span></span>
                                                                    Reorder Level
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 erp-col-form-label py-0">
                                                            <div class="radio-list">
                                                                <label class="kt-radio kt-radio--bold kt-radio--success">
                                                                    <input type="radio" id="priority-check-consumption" @if(isset($mainPriorityCheck) && $mainPriorityCheck == 'CONSUMPTION') selected @endif value="CONSUMPTION" name="priority_check" @if($case == 'edit') disabled @endif/>
                                                                    <span></span>
                                                                    Consumption
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label class="erp-col-form-label">Consumption:</label>
                                                        </div>
                                                        <div class="col-lg-12 erp-col-form-label py-0">
                                                            <div class="checkbox-list">
                                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                                    @if($case == 'new')
                                                                        <input type="checkbox" class="enable-on-consumption" value="NET SALE" checked id="consumption-net-sale" name="consumption_type[]" disabled/>
                                                                    @else
                                                                        <input type="checkbox" value="NET SALE" @if($mainConsumptionType == 'ALL' || $mainConsumptionType == 'NET SALE') selected @endif id="consumption-net-sale" name="consumption_type[]" disabled/>
                                                                    @endif
                                                                    <span></span>
                                                                    Net Sale
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 erp-col-form-label py-0">
                                                            <div class="checkbox-list">
                                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                                                                    @if($case == 'new')
                                                                        <input type="checkbox" class="enable-on-consumption" value="ST" id="consumption-net-stock" name="consumption_type[]" disabled/>
                                                                    @else 
                                                                        <input type="checkbox" value="ST" id="consumption-net-stock" @if($mainConsumptionType == 'ALL' || $mainConsumptionType == 'ST') selected @endif name="consumption_type[]" disabled/>
                                                                    @endif
                                                                    <span></span>
                                                                    Stock Transfer
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="erp-col-form-label">How System Should Suggest (Consumption Suggested QTY) ?</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 erp-col-form-label py-0 pl-0">
                                                <div class="radio-list">
                                                    <label class="kt-radio kt-radio--bold kt-radio--success">
                                                        @if($case == 'new')
                                                            <input type="radio" class="enable-on-consumption" checked id="priority-check-reorder" value="PRODUCT" name="consumption_base" disabled/>
                                                        @else
                                                            <input type="radio" id="priority-check-reorder" @if(isset($mainConsumptionBase) && $mainConsumptionBase == 'PRODUCT') checked @endif value="PRODUCT" name="consumption_base" disabled/>
                                                        @endif
                                                        <span></span>
                                                        Based On Settled Consumption Days In Product
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 erp-col-form-label py-0 pl-0">
                                                <div class="radio-list">
                                                    <label class="kt-radio kt-radio--bold kt-radio--success">
                                                        @if($case == 'new')
                                                            <input type="radio" class="enable-on-consumption" id="priority-check-reorder" value="DAYS" name="consumption_base" disabled/>
                                                        @else
                                                            <input type="radio" id="priority-check-reorder" value="DAYS" @if(isset($mainConsumptionBase) && $mainConsumptionBase == 'DAYS') checked @endif name="consumption_base" disabled/>
                                                        @endif
                                                        <span></span>
                                                        <div>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text erp-form-control-sm">Last</span>
                                                                </div>
                                                                <input type="text" class="form-control erp-form-control-sm validNumber enable-on-consumption" value="{{ isset($mainConsumptionDays) ? $mainConsumptionDays : 60 }}" name="consumption_days" autocomplete="off" aria-invalid="false" disabled/>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text erp-form-control-sm">of Days</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 erp-col-form-label py-0 pl-0">
                                                <div class="radio-list">
                                                    <label class="kt-radio kt-radio--bold kt-radio--success">
                                                        <input type="radio" id="priority-check-reorder" class="enable-on-consumption" value="DATE" @if(isset($mainConsumptionBase) && $mainConsumptionBase == 'DATE') selected @endif name="consumption_base" disabled/>
                                                        <span></span>
                                                        <div class="erp-selectDateRange">
                                                            <div class="input-daterange input-group kt_datepicker_3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text erp-form-control-sm">From</span>
                                                                </div>
                                                                <input type="text" class="form-control erp-form-control-sm c-date-p enable-on-consumption" value="{{ isset($mainConsumptionSDate) ? $mainConsumptionSDate : date('d-m-Y') }}" name="consumption_start_date" autocomplete="off" aria-invalid="false" disabled/>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text erp-form-control-sm">To</span>
                                                                </div>
                                                                <input type="text" class="form-control erp-form-control-sm c-date-p enable-on-consumption" value="{{ isset($mainConsumptionEDate) ? $mainConsumptionEDate : date('d-m-Y') }}" name="consumption_end_date" autocomplete="off" disabled/>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="erp-col-form-label">Consumption Branch:</label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="erp-select2">
                                                        <select name="consumption_branches[]"  id="consumption_branches" multiple class="moveIndex kt-select2 form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                                            <option value="0">Select</option>
                                                            @if($case == 'new')
                                                                @foreach($data['branches'] as $branch)
                                                                    <option value="{{ $branch->branch_id }}" @if($branch->branch_id == $data['current_branch']) selected @endif> {{ $branch->branch_short_name }}</option>    
                                                                @endforeach
                                                            @else
                                                                {!! $data['consumption_branches'] !!}
                                                            @endif
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- Step 2 End --}}
                                <div id="step3" class="d-none"> {{-- Step 3 Start --}}
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="erp-col-form-label">Select Multiple Branches In Case You Want To Take Consumption From Other Branches :</label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="erp-select2">
                                                        <select name="suggest_request_branches[]"  id="suggest_request_branches" multiple class="moveIndex kt-select2 form-control erp-form-control-sm" @if($case == 'edit') disabled @endif>
                                                        <option value="0">Select</option>
                                                        @if($case == 'new')
                                                            @foreach($data['branches'] as $branch)
                                                                @if($branch->branch_id != $data['current_branch'])
                                                                    <option value="{{ $branch->branch_id }}"> {{ $branch->branch_short_name }}</option>      
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            {!! $data['other_branches'] !!}
                                                        @endif
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-7" style="align-self: center;">
                                            <div class="row">
                                                <div class="col-lg-12 erp-col-form-label py-0">
                                                    <div class="checkbox-list">
                                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="margin-bottom: 0;">
                                                            <input type="checkbox" value="1" id="suggest_stock_request" name="suggesstockrequest" @if(isset($mainSuggesStockRequest) && !$mainSuggesStockRequest == 0) checked @endif @if($case == 'edit') disabled @endif/>
                                                            <span></span>
                                                            Suggest Stock Request From Other Branch In Case If Extra Stock Available
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="input-group date">
                                                <input type="text"  name="suggestion_lead_days" value="{{ isset($mainSuggestLeadDays) ? $mainSuggestLeadDays : '' }}" autocomplete="off" class="form-control erp-form-control-sm moveIndex" id="suggestion_lead_days" autofocus="" aria-invalid="false" placeholder="Suggestion Lead Days" @if($case == 'edit') disabled @endif>
                                            </div>
                                        </div>
                                    </div>
                                    @if($case == 'edit' && $mainSuggesStockRequest == '1')
                                        <div class="form-group row">
                                            <div class="col-lg-4">
                                                <div class="row">
                                                    <div class="col-lg-12 erp-col-form-label py-0">
                                                        <div class="checkbox-list">
                                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="margin-bottom: 0;">
                                                                <input type="checkbox" id="create_stock_request" name="create_stock_request" @if(isset($createStockRequests) && $createStockRequests == 1) checked disabled @endif/>
                                                                <span></span>
                                                                Create Stock Request To Other Branches
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                Other Branch(s) Stock Request Codes
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1" style="align-self: center;">
                        <button class="btn btn-outline-primary btn-navigation" id="stepForward" data-task="next">
                            <i class="la la-angle-right la-4x"></i>
                        </button>
                    </div>
                </div>
                {{-- MEGA GRID START --}}
                @if($case == 'edit')
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="data_entry_header">
                                <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = ['Sr No','Branch Name','Barcode','Product Name','UOM','Packing','Demand Qty','Stock Qty','Physical Stock',
                                                    'Suggested Reorder Qty','Suggested Consumption Qty','Approve Qty','Transfer Qty','Consumption Qty','Max Qty','Reorder Qty','Exp. Consumption Qty',
                                                    'FOC Qty','Rate','Lowest Purchase Rate','Lowest Purchase Date','Amount','Disc%','Disc Amt','Vat%','Vat Amt','Gross Amt','Active','Pending','Reject'];
                                    @endphp
                                    <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                        @foreach($headings as $key=>$heading)
                                            <li >
                                                <label>
                                                    <input value="{{$key}}" type="checkbox" checked> {{$heading}}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="kt-user-page-setting" style="display: inline-block">
                                    <button type="button" style="width: 30px;height: 30px;" title="Setting Save" data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon" id="pageUserSettingSave">
                                        <i class="la la-floppy-o"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                        <tr>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Sr.</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Branch Name</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Barcode
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Product Name</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">UOM</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Packing</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Demand Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Stock Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Physical Stock</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title" title="Suggest Reorder Qty">Sugg. Reorder Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title" title="Suggest Consumption Qty">Sugg. Consumption Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Approve Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Transfer Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Sale Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title" title="Total Consumption Qty">Consumption Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Max Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Reorder Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title" title="Expected Consumption Qty">Exp. Consumption Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">FOC Qty</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Rate</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title" >Sale Rate</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title" >Lowest Purchase Rate</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title" >Lowest Purchase Date</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Amount</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Disc %</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Disc Amt</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">VAT %</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">VAT Amt</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Gross Amt</div>
                                            </th>
                                            <th class="radioPending" style="z-index: 1000 !important;min-width:51.2969px;max-width:51.2969px !important;">
                                                <label class="kt-radio kt-radio--brand" style="padding-left: 17px; top: -5px;">
                                                    <input style="left:0;" type="radio" id="pendingAll" name="checkAllgrid" value="pending">
                                                    <span></span>
                                                </label> <div class="noselect">Pndng</div>
                                            </th>
                                            <th class="radioApprove" style="z-index: 1000 !important;min-width:51.2969px;max-width:51.2969px !important;">
                                                <label class="kt-radio kt-radio--success" style="padding-left: 17px; top: -5px;">
                                                    <input style="left:0;" type="radio" id="approveAll" name="checkAllgrid" value="approved">
                                                    <span></span>
                                                </label> <div class="noselect">Aprv</div>
                                            </th>
                                            <th class="radioReject" style="z-index: 1000 !important;min-width:51.2969px;max-width:51.2969px !important;">
                                                <label class="kt-radio kt-radio--danger" style="padding-left: 17px; top: -5px;">
                                                    <input style="left:0;" type="radio" id="rejectAll" name="checkAllgrid" value="reject">
                                                    <span></span>
                                                </label> <div class="noselect">Rjct</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Action</div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                        @php $sr_no = 0; @endphp
                                        @foreach($product_list as $productID => $product)
                                            {{-- Generaral Information About Product --}}
                                            @php
                                                $singleProduct = true;
                                                $approveQty=0;$stockQty=0;$physicalStock=0;$transferQty=0;$demandQty=0;$rate=0;$saleQty=0;$consumptionQty=0;
                                                $maxQty=0;$reorderQty=0;$expConsumptionQty=0;$amount=0;$discPer=0;$discAmount=0;$vatPer=0;$vatAmount=0;
                                                $sugQtyReorder =0;$sugQtyConsumption=0;
                                                $parentID = $loop->iteration;
                                                $prod = Illuminate\Support\Arr::first($product);
                                            @endphp
                                            {{-- If There are More Than One Product In Group Create Summary Row --}}
                                            @if(count($product) > 1)
                                                @php $singleProduct = false; @endphp
                                                @foreach($product as $productDetail)
                                                    @php 
                                                        // Variables
                                                        $approveQty += (float)$productDetail->approve_qty; 
                                                        $stockQty += (float)$productDetail->stock_qty; 
                                                        $physicalStock += (float)$productDetail->physical_stock; 
                                                        $transferQty += (float)$productDetail->transfer_qty; 
                                                        $demandQty += (float)$productDetail->demand_qty; 

                                                        $rate = $productDetail->pur_rate;

                                                        $saleQty += (float)$productDetail->sale_qty;
                                                        $consumptionQty += (float)$productDetail->total_consumption_qty;
                                                        $maxQty += (float)$productDetail->max_qty;
                                                        $reorderQty += (float)$productDetail->reorder_qty;
                                                        $expConsumptionQty += (float)$productDetail->expected_consumption_qty;
                                                        $sugQtyReorder += (float)$productDetail->suggest_qty_reorder;
                                                        $sugQtyConsumption += (float)$productDetail->suggest_qty_consumption;
                                                        
                                                        $amount = (float)($approveQty * $rate);
                                                        $discPer += (float)$productDetail->disc_perc;
                                                        $discAmount += (float)$productDetail->disc;
                                                        
                                                        $vatAmount += (float)(($productDetail->vat) / count($product));
                                                        $vatPer += (float)(($productDetail->vat_perc) / count($product));
                                                    @endphp
                                                @endforeach
                                                <tr id="row-{{$parentID}}" class="product_tr_no">
                                                    <td style="border-right: 0 !important;"> 
                                                        <div style="line-height: 28px;"></div>
                                                        <input type="hidden" name="pd[{{ $parentID }}][supplier_id]" data-id="supplier_id" value="{{ $prod->supplier_id }}" title="{{ $prod->supplier_id }}" class="supplier_id form-control erp-form-control-sm " readonly>
                                                        <input type="hidden" name="pd[{{ $parentID }}][product_id]" data-id="product_id" value="{{ $prod->product['product_id'] }}" title="{{ $prod->product['product_id'] }}" class="product_id form-control erp-form-control-sm " readonly>
                                                        <input type="hidden" name="pd[{{ $parentID }}][product_barcode_id]" data-id="product_barcode_id" value="{{ $prod->product_barcode_id }}" title="{{ $prod->product_barcode_id }}" class="product_barcode_id form-control erp-form-control-sm " readonly>
                                                        <input type="hidden" name="pd[{{ $parentID }}][uom_id]" data-id="uom_id" value="{{ $prod->product_unit_id }}" title="{{ $prod->product_unit_id }}" class="uom_id form-control erp-form-control-sm " readonly>
                                                        <input type="hidden" name="pd[{{ $parentID }}][demand_id]" data-id="demand_id" value="{{ $prod->demand_id }}" title="{{ $prod->demand_id }}" class="demand_dtl_id form-control erp-form-control-sm " readonly>
                                                        <input type="hidden" name="pd[{{ $parentID }}][demand_branch_id]" data-id="demand_branch_id" value="{{ $prod->demand_branch_id }}" class="demand_branch_id form-control erp-form-control-sm" readonly>
                                                        <input type="hidden" name="pd[{{ $parentID }}][demand_base_qty]" data-id="demand_base_qty" value="{{ $prod->demand_base_qty }}" class="demand_base_qty form-control erp-form-control-sm" readonly>
                                                    </td>
                                                    <td style="line-height: 28px;padding:0px 8px !important;">SUMMARY</td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][pd_barcode]" value="{{ $prod->barcode['product_barcode_barcode'] }}" title="{{ $prod->barcode['product_barcode_barcode'] }}"  class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][product_name]" data-id="pd_product_name" value="{{ $prod->product['product_name'] }}" title="{{ $prod->product['product_name'] }}" class="productHelp pd_product_name tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                    <td>
                                                        <select class="pd_uom form-control erp-form-control-sm" name="pd[{{ $parentID }}][pd_uom]" data-id="pd_uom" title="{{ isset($prod->uom) ? $prod->uom['uom_name'] : '' }}" disabled>
                                                            <option value="{{ $prod->product_unit_id }}">{{ isset($prod->uom) ? $prod->uom['uom_name'] : '' }}</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][pd_packing]" data-id="pd_packing" value="{{ $prod->product_barcode_packing }}" title="{{ $prod->product_barcode_packing }}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][demand_qty]" data-id="demand_qty" value="{{ $demandQty }}" title="{{ $demandQty }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][stock_qty]" data-id="stock_qty" value="{{ $stockQty }}" title="{{ $stockQty }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][stock_qty]" data-id="physical_stock" value="{{ $physicalStock }}" title="{{ $physicalStock }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][suggest_qty_reorder]" data-id="suggest_qty_reorder" value="{{ number_format($sugQtyReorder,3) }}" title="{{ number_format($sugQtyReorder,3) }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][suggest_qty_consumption]" data-id="suggest_qty_consumption" value="{{ number_format($sugQtyConsumption,3,'.','') }}" title="{{ number_format($sugQtyConsumption,3,'.','') }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][quantity]" data-id="quantity" value="{{ number_format($approveQty) }}" title="{{ number_format($approveQty,0,'.','') }}" parent-id="{{ $parentID }}" class="tblGridCal_qty tblGridCal_parent_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][tansfer_qty]" data-id="tansfer_qty" value="{{ number_format($transferQty,3) }}" title="{{ number_format($transferQty,3) }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sale_qty]" data-id="sale_qty" value="{{ $saleQty }}" title="{{ $saleQty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][consumption_qty]" data-id="consumption_qty" value="{{ number_format($consumptionQty,3,'.','') }}" title="{{ number_format($consumptionQty,3,'.','') }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][max_qty]" data-id="max_qty" value="{{ $maxQty }}" title="{{ $maxQty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][reorder_qty]" data-id="reorder_qty" value="{{ $reorderQty }}" title="{{ $reorderQty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][exp_cons_qty]" data-id="exp_cons_qty" value="{{ $expConsumptionQty }}" title="{{ $expConsumptionQty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][foc_qty]" data-id="foc_qty" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][rate]" data-id="rate" value="{{ $rate }}" title="{{ $rate }}" class="tblGridCal_rate tblGridCal_parent_rate tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sale_rate]" data-id="sale_rate" value="{{ $prod->sale_rate }}" title="{{ $prod->sale_rate }}" class="tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][low_purc_rate]" data-id="low_purc_rate" value="{{ $prod->lowest_pur_rate }}" title="{{ $prod->lowest_pur_rate }}" class="tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][low_purc_date]" data-id="low_purc_date" value="{{ $prod->lowest_rate_date }}" title="{{ $prod->lowest_rate_date }}" class="tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][amount]" data-id="amount" value="{{ $amount }}" title="{{ $amount }}" class="tblGridCal_amount tblGridCal_parent_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][dis_perc]" data-id="dis_perc" value="{{ $discPer }}" title="{{ $discPer }}" class="tblGridCal_discount_perc tblGridCal_parent_discount tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][dis_amount]" data-id="dis_amount" value="{{ $discAmount }}" title="{{ $discAmount }}" class="tblGridCal_discount_amount tblGridCal_parent_discount_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][vat_perc]" data-id="vat_perc" value="{{ $vatPer }}" title="{{ $vatPer }}" class="tblGridCal_vat_perc tblGridCal_parent_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][vat_amount]" data-id="vat_amount" value="{{ $vatAmount }}" title="{{ $vatAmount }}" class="tblGridCal_vat_amount tblGridCal_parent_vat_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][gross_amount]" data-id="gross_amount" value="" title="" class="tblGridCal_gross_amount tblGridCal_parent_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td class="text-center radioInput radioPending"><label class="kt-radio kt-radio--brand"><input type="radio" id="pending" class="smryActStatus pending" data-parent="{{ $parentID }}" value="pending" name="pd[{{ $parentID }}][action]"><span></span></label></td>
                                                    <td class="text-center radioInput radioApprove"><label class="kt-radio kt-radio--success"><input type="radio" id="approved" class="smryActStatus approved" data-parent="{{ $parentID }}" value="approved" name="pd[{{ $parentID }}][action]"><span></span></label></td>
                                                    <td class="text-center radioInput radioReject"><label class="kt-radio kt-radio--danger"><input type="radio"  id="reject" class="smryActStatus reject" data-parent="{{ $parentID }}" value="reject" name="pd[{{ $parentID }}][action]" ><span></span></label></td>
                                                    <td class="text-center" style="background: rgb(221, 221, 221) !important;">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-success gridBtn show_products" 
                                                            style="top: 0!important;background-color:var(--success)!important;border-color:var(--success)!important" 
                                                            data-id="{{$parentID}}">
                                                            <i class="la la-angle-down"></i>
                                                        </button>
                                                    </div>
                                                        <!-- <i class="la la-angle-up show_products" data-id="{{$parentID}}"></i> -->
                                                    </td>
                                                </tr>
                                            @endif
                                            @foreach($product as $productDetail)
                                                @php 
                                                    $dtlAmount = $productDetail->approve_qty * $productDetail->pur_rate; 
                                                    
                                                    $sr_no++;
                                                @endphp 
                                                <tr class="product_child_tr child-of-{{$parentID}} @if($singleProduct) single_product_tr_no @else d-none @endif" data-parent="{{$parentID}}">
                                                    <td>
                                                    <input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][sr_no]" value="{{ $sr_no }}" title="{{ $sr_no }}" class="form-control sr_no erp-form-control-sm" readonly>
                                                    <input type="hidden" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][supplier_id]" data-id="supplier_id" value="{{ $productDetail->supplier_id }}" class="supplier_id form-control erp-form-control-sm " readonly>
                                                    <input type="hidden" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][product_id]" data-id="product_id" value="{{ $productDetail->product_id }}" class="product_id form-control erp-form-control-sm " readonly>
                                                    <input type="hidden" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][uom_id]" data-id="uom_id" value="{{ $productDetail->product_unit_id }}" class="uom_id form-control erp-form-control-sm " readonly>
                                                    <input type="hidden" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][demand_id]" value="{{ $productDetail->demand_id }}" class="demand_id form-control erp-form-control-sm" readonly>
                                                    <input type="hidden" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][product_barcode_id]" data-id="product_barcode_id" value="{{ $productDetail->product_barcode_id }}" class="product_barcode_id form-control erp-form-control-sm" readonly>
                                                    <input type="hidden" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][demand_branch_id]" data-id="demand_branch_id" value="{{ $productDetail->demand_branch_id }}" class="demand_branch_id form-control erp-form-control-sm" readonly>
                                                    <input type="hidden" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][demand_base_qty]" data-id="demand_base_qty" value="{{ $productDetail->demand_base_qty }}" class="product_base_qty form-control erp-form-control-sm" readonly>
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][branc_name]" value="{{ $productDetail->branch['branch_short_name'] }}" title="{{ $productDetail->branch['branch_name'] }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][pd_barcode]" value="{{ $productDetail->barcode['product_barcode_barcode'] }}" title="{{ $productDetail->barcode['product_barcode_barcode'] }}" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][product_name]" data-id="product_name" value="{{ $productDetail->product['product_name'] }}" title="{{ $productDetail->product['product_name'] }}" class="productHelp pd_product_name tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                    <td>
                                                        <select class="pd_uom form-control erp-form-control-sm" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][uom]" title="{{ isset($productDetail->uom) ? $productDetail->uom['uom_name'] : '' }}" disabled>
                                                            <option value="{{ $productDetail->product_unit_id }}">{{ isset($productDetail->uom) ? $productDetail->uom['uom_name'] : '' }}</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][pd_packing]" data-id="pd_packing" value="{{ $productDetail->product_barcode_packing }}" title="{{ $productDetail->product_barcode_packing }}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][demand_qty]" data-id="demand_qty" value="{{ $productDetail->demand_qty }}" title="{{ $productDetail->demand_qty }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][stock_qty]" data-id="stock_qty" value="{{ number_format($productDetail->stock_qty) }}" title="{{ number_format($productDetail->stock_qty) }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][physical_stock]" data-id="physical_stock" value="{{ number_format($productDetail->physical_stock) }}" title="{{ number_format($productDetail->physical_stock) }}" class="form-control erp-form-control-sm grn_green"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][suggest_qty_reorder]" data-id="suggest_qty_reorder" value="{{ number_format($productDetail->suggest_qty_reorder,3) }}" title="{{ number_format($productDetail->suggest_qty_reorder,3) }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][suggest_qty_consumption]" data-id="suggest_qty_consumption" value="{{ number_format($productDetail->suggest_qty_consumption,3,'.','') }}" title="{{ number_format($productDetail->suggest_qty_consumption,3,'.','') }}" class="form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][quantity]" data-id="quantity" value="{{ number_format($productDetail->approve_qty,0,0,'') }}" title="{{ number_format($productDetail->approve_qty) }}" parent-id="{{ $parentID }}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber grn_green"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][tansfer_qty]" readonly data-id="tansfer_qty" value="{{ number_format($productDetail->transfer_qty,3) }}" title="{{ number_format($productDetail->transfer_qty,3) }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][sale_qty]" readonly data-id="sale_qty" value="{{ $productDetail->sale_qty }}" title="{{ $productDetail->sale_qty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][consumption_qty]" readonly data-id="consumption_qty" value="{{ number_format($productDetail->total_consumption_qty,3,'.','') }}" title="{{ number_format($productDetail->total_consumption_qty,3,'.','') }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][max_qty]" readonly data-id="max_qty" value="{{ $productDetail->max_qty }}" title="{{ $productDetail->max_qty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][reorder_qty]" readonly data-id="reorder_qty" value="{{ $productDetail->reorder_qty }}" title="{{ $productDetail->reorder_qty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][exp_cons_qty]" readonly data-id="exp_cons_qty" value="{{ $productDetail->expected_consumption_qty }}" title="{{ $productDetail->expected_consumption_qty }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][foc_qty]" data-id="foc_qty" value="" title="" parent-id="{{ $parentID }}" class="grn_green form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][rate]" data-id="rate" value="{{ number_format($productDetail->pur_rate,3) }}" title="{{ number_format($productDetail->pur_rate,3) }}" parent-id="{{ $parentID }}" class="grn_green tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][sale_rate]" readonly data-id="sale_rate" value="{{ number_format($productDetail->sale_rate,3) }}" title="{{ number_format($productDetail->sale_rate,3) }}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][low_purc_rate]" readonly data-id="low_purc_rate" value="{{ number_format($productDetail->lowest_pur_rate,3) }}" title="{{ number_format($productDetail->lowest_pur_rate,3) }}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][low_purc_date]" readonly data-id="low_purc_date" value="{{ $productDetail->lowest_rate_date }}" title="{{ $productDetail->lowest_rate_date }}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][amount]" readonly data-id="amount" value="{{ number_format($dtlAmount , 3) }}" title="{{ number_format($dtlAmount , 3) }}" class="tblGridCal_amount tblGridCal_parent_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][dis_perc]" readonly data-id="dis_perc" value="{{ number_format($productDetail->disc_perc,3) }}" title="{{ number_format($productDetail->disc_perc,3) }}" class="tblGridCal_discount_perc  tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][dis_amount]" readonly data-id="dis_amount" value="{{ number_format($productDetail->disc,3) }}" title="{{ number_format($productDetail->disc,3) }}" class="tblGridCal_discount_amount  form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][vat_perc]" readonly data-id="vat_perc" value="{{ number_format($productDetail->vat_perc,3) }}" title="{{ number_format($productDetail->vat_perc,3) }}" class="tblGridCal_vat_perc  tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][vat_amount]" readonly data-id="vat_amount" value="{{ number_format($productDetail->vat,3) }}" title="{{ number_format($productDetail->vat) }}" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td><input type="text" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][gross_amount]" readonly data-id="gross_amount" value="" title="" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                    <td class="text-center radioInput radioPending"><label class="kt-radio kt-radio--brand"><input type="radio" class="childActStatus pending" id="pending" data-parent="{{ $parentID }}" value="pending" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][action]" @if($productDetail->is_approve == "pending") checked @endif><span></span></label></td>
                                                    <td class="text-center radioInput radioApprove"><label class="kt-radio kt-radio--success"><input type="radio" class="childActStatus approved" id="approved" data-parent="{{ $parentID }}" value="approved" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][action]" @if($productDetail->is_approve == "approved") checked @endif><span></span></label></td>
                                                    <td class="text-center radioInput radioReject"><label class="kt-radio kt-radio--danger"><input type="radio" class="childActStatus reject" data-parent="{{ $parentID }}" id="reject" value="reject" name="pd[{{ $parentID }}][sub][{{ $loop->iteration }}][action]" @if($productDetail->is_approve == "reject") reject @endif><span></span></label></td>
                                                    <td class="text-center">
                                                        @if($singleProduct)
                                                        <div class="btn-group btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-success gridBtn" 
                                                                style="top: 0!important;background-color:var(--danger)!important;border-color:var(--danger)!important" >
                                                                <i class="la la-exclamation-circle"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                        <!-- <div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delDataRow" data-parent="{{ $parentID }}"><i class="la la-trash"></i></button></div> -->
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                    <tbody class="erp_form__grid_body_total">
                                    <tr class="bottom-row">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="total_grid_qty">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="total_grid_foc_qty">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="total_grid_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td class="total_grid_disc_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td class="total_grid_vat_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_gross_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td style="z-index: 1000;"></td>
                                        <td style="z-index: 1000;"></td>
                                        <td style="z-index: 1000;"></td>
                                        <td style="background: #f9f9f9 !important;"></td>
                                    </tr>
                                    </tbody>
                                </table>        
                            </div>
                        </div>
                    </div>
                   
                    @if($mainSuggesStockRequest == 1)
                    <div class="form-group-block mt-4" id="sub__grid_for_request">
                        <div class="erp_form___block">
                            <h5>Generate Stock Request(s) Auto</h5>
                            <div class="table-scroll form_input__block" id="sub__grid_for_request_content">
                                <div class="spinner-container text-center my-4" id="spinner">
                                    <div class="spinner-border text-dark" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                   
                @endif
                {{-- MEGA GRID END --}}
            </div>
        </div>
    </div>
    </form>
          <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>            
    <script async src="{{ asset('js/pages/js/auto-demand-calculation.js') }}" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase/auto_demand.js') }}" type="text/javascript"></script>    
    <script>
        $(document).ready(function(e){ // DOCUMENT READY
            var currentStep = 1;
            $('#getAutoDemandData').on('click',function(e){
                e.preventDefault();
            });
            // STEPS HANDELING
            $(document).on('click' , '.btn-navigation', function(e){
                e.preventDefault();
                var action = $(this).data('task');
                if(action == "next"){
                    updateSteps(++currentStep);
                }else{
                    updateSteps(--currentStep);
                }              
            });

            function updateSteps(step){
                console.log($('#step'+step));
                $('#step'+step).removeClass('d-none');
                $('#step'+step).siblings().addClass('d-none');
                
                if(step == 1){
                    $('#stepBackward').prop('disabled',true);
                }else if(step == 3){
                    $('#stepForward').prop('disabled',true);
                }else{
                    $('#stepBackward').prop('disabled',false);
                    $('#stepForward').prop('disabled',false);
                }
            }

            $(document).on('change','#ad_type',function(e){
                if($(this).val() == "DEMAND"){
                    $('#demands_select').removeClass('d-none');
                    $('#suppliers_select').addClass('d-none');
                }else if($(this).val() == "SUPPLIER"){
                    $('#suppliers_select').removeClass('d-none');
                    $('#demands_select').addClass('d-none');
                }else{
                    $('#suppliers_select').addClass('d-none');
                    $('#demands_select').addClass('d-none');
                }
            });

            showHideRow();
            function showHideRow(){
                $('.show_products').unbind();
                $('.show_products').click(function(){
                    var dataId = $(this).attr('data-id');
                    $(this).toggleClass('fa-rotate-180');
                    var tbody = $(this).parents('tbody');
                    tbody.find('tr.child-of-'+dataId).toggleClass('d-none');
                });
            }

            // Handeling Product Status
            $(document).on('click','input[name="checkAllgrid"]',function(e){
                var actionOn = $(this).val();
                $('.erp_form__grid_body').find('input.'+actionOn).prop('checked', true);
            });
            $(document).on('click' , 'input.smryActStatus' , function(e){
                $('input[name="checkAllgrid"]').prop('checked' , false);
                var parentID = $(this).data('parent');
                var currentSelection = $(this).val();
                $('.child-of-'+parentID+' .'+currentSelection).prop('checked' , true);
            })
            $(document).on('click' , 'input.childActStatus' , function(e){
                $('input[name="checkAllgrid"]').prop('checked' , false);
                var parentID = $(this).data('parent');
                var currentSelection = $(this).val();
                console.log($('.child-of-'+parentID).length);
                if($('.child-of-'+parentID).length == 1){
                    $('#row-'+parentID+' input.'+currentSelection).prop('checked' ,true);
                }else{
                    var vals = new Array();
                    $('.child-of-'+parentID).each(function(ele){
                        var row = $('.child-of-'+parentID)[ele];
                        console.log(row);
                        var v = row.querySelector('input[type=radio]:checked').value;
                        vals.push(v);
                    });
                    if(vals.every( (val, i, arr) => val === arr[0] )){
                        $('#row-'+parentID+' input.smryActStatus.'+vals[0]).prop('checked' ,true);
                    }else{
                        $('#row-'+parentID+' input.smryActStatus').prop('checked' ,false);
                    }
                }
            });

            // Load Seond Grid If the mainSuggesStockRequest = 1
            var ad_id = '{{ $id }}';
            var suggestStockRequest = '{{ $mainSuggesStockRequest }}';
            if(ad_id != "" && suggestStockRequest == '1'){
                // Disable the Button
                $("form").find(":submit").prop('disabled', true);

                setTimeout(function(){
                $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url         : '/auto-demand/load-inner-grid/' + ad_id,
                        type        : 'POST',
                        dataType	: 'json',
                        data        : {},
                        cache       : false,
                        contentType : false,
                        processData : false,
                        success: function(response,status) {
                            $("form").find(":submit").prop('disabled', false);
                            if(response.status == 'success'){
                                toastr.success(response.message);
                                var branches = response.data.branches;
                                var requestBranchs = response.data.requestBranchs;
                                var table = '';
                                table += '<table class="table extra-stock-table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline" id="innerReqTable">'+
                                    '<thead class="erp_form__grid_header">'+
                                        '<tr>'+
                                            '<th scope="col">'+
                                                '<div class="erp_form__grid_th_title">Barcode</div>'+
                                            '</th>'+
                                            '<th scope="col">'+
                                                '<div class="erp_form__grid_th_title">Product Name</div>'+
                                            '</th>'+
                                            '<th scope="col">'+
                                                '<div class="erp_form__grid_th_title">Packing</div>'+
                                            '</th>'+
                                            '<th scope="col">'+
                                                '<div class="erp_form__grid_th_title">Unit</div>'+
                                            '</th>';
                                            // Loop Of The Branches
                                            branches.forEach((el) => {
                                                table += '<th scope="col" colspan="2">'+
                                                    '<div class="erp_form__grid_th_title">'+ el.branch_short_name +'</div>'+
                                                '</th>';
                                            });
                                        table += '</tr>'+
                                        '<tr>'+
                                            '<th scope="col" colspan="4"><div class="erp_form__grid_th_title text-center">Product Detail</div></th>';
                                            branches.forEach((el) => {
                                                table += '<th scope="col" style="z-index:9;">'+
                                                    '<div class="erp_form__grid_th_title">Extra Qty</div>'+
                                                '</th>'+
                                                '<th scope="col" style="z-index:9;">'+
                                                    '<div class="erp_form__grid_th_title">Approve Qty</div>'+
                                                '</th>';
                                            });
                                        table += '</tr>'+
                                    '</thead>'+
                                    '<tbody class="erp_form__grid_body">';
                                        requestBranchs.forEach((el , index) => {
                                            table += '<tr>'+
                                                '<td class="pl">'+ el.barcode["product_barcode_barcode"] +'</td>'+
                                                '<td style="white-space:nowrap;" class="pl">'+ el.product["product_name"] +'</td>'+
                                                '<td class="pl">'+ el.barcode["product_barcode_packing"] +'</td>'+
                                                '<td class="pl">'+ el.uom.uom_name +'</td>';
                                                branches.forEach((branch, branchIndex) => {
                                                        if(el.req_branch_id == branch.branch_id){
                                                        var aprvQty = el.approve_qty;
                                                        var extraQty = el.extra_qty; 
                                                        var disabled = '';
                                                        var green = 'grn_green';
                                                        }else{
                                                            var aprvQty = '';
                                                            var extraQty = '';
                                                            disabled = 'disabled';
                                                            var green = '';
                                                        }
                                                    table += '<td>'+
                                                        '<input type="text" '+disabled+' name="req['+ branch.branch_id +']['+index+'][extra_qty]" data-id="extra_qty" value="'+ extraQty +'" title="'+ extraQty +'" class="tb_moveIndex form-control erp-form-control-sm validNumber field_readonly" readonly>'+
                                                    '</td>'+
                                                    '<td>'+
                                                        '<input type="text" '+disabled+' name="req['+ branch.branch_id +']['+index+'][aprv_qty]" data-id="aprv_qty" value="'+ aprvQty +'" title="'+ aprvQty +'" class="tb_moveIndex form-control erp-form-control-sm validNumber '+ green +'">'+                                               
                                                        '<input type="hidden" '+disabled+' name="req['+ branch.branch_id +']['+index+'][ad_id]" data-id="ad_id" value="'+ el.ad_id +'">'+                                               
                                                        '<input type="hidden" '+disabled+' name="req['+ branch.branch_id +']['+index+'][product_id]" data-id="product_id" value="'+ el.product_id +'">'+                                               
                                                        '<input type="hidden" '+disabled+' name="req['+ branch.branch_id +']['+index+'][stock_request_id]" data-id="product_id" value="'+ el.stock_request_id +'">'+                                               
                                                    '</td>';
                                                });
                                            '</tr>';
                                        });
                                    table += '</tbody>'+
                                    '<tbody class="erp_form__grid_body_total">'+
                                    '<tr class="bottom-row">'+
                                        '<td></td>'+
                                        '<td></td>'+
                                        '<td></td>'+
                                        '<td></td>';
                                        branches.forEach((branch, branchIndex) => {
                                            table += '<td colspan="2">'+
                                                '<input type="text" style="text-align:center;background:transparent!important;font-weight:bold;right:0;font-size:11px;" title="'+ branch.branch_short_name +'" value="'+ branch.branch_short_name +'" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>'+
                                            '</td>';
                                        });
                                    table += '</tr>'+
                                    '</tbody>'+
                                '</table>';
                                $('#sub__grid_for_request_content').html('').html(table);
                                table_th_resize();
                            }else{
                                toastr.error(response.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                            }
                        },
                        error: function(response,status) {
                            toastr.error(response.responseJSON.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                        },
                    });
                } , 10000);
            }
        });
    </script>
    @if($case == 'new')
        <script>
            $('input[name="priority_check"]').on('change',function(e){
                if($(this).val() == 'CONSUMPTION'){
                    $('.enable-on-consumption').removeAttr('disabled');
                }else{
                    $('.enable-on-consumption').attr('disabled','disabled');
                }
            });
        </script>
    @endif
@endsection


