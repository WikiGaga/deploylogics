@extends('layouts.template')
@section('title', 'Form Flow Criteria')

@section('pageCSS')
@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @php
                    $page_data = [
                        'page_title'=>'Form Flow Criteria',
                        'form_type'=> ''
                    ]
                @endphp
                @include('elements.page_header',['page_data'=>$page_data])
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <form id="FlowCriteria_form" class="kt-form" method="post" action="{{ action('Development\FlowCriteriaController@store') }}">
                    @csrf
                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">ID:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="menu_flow_criteria_dtl_id" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" class="form-control" readonly value="{{ date('d-m-Y') }}" name="menu_flow_criteria_apply_at" id="kt_datepicker_3" />
                                            <div class="input-group-append">
										<span class="input-group-text">
											<i class="la la-calendar"></i>
										</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Select Form:</label>
                                    <div class="col-lg-3" >
                                        <select class="form-control kt-select2" id="menu_flow_criteria_name" name="menu_flow_criteria_name">
                                             <option value="">Select</option>
                                             @foreach($data['menu'] as $menue)
                                            <option value="{{ $menue->menu_dtl_table_name }}">{{ $menue->menu_dtl_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- end row--}}
                        <ul class="erp-main-nav nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-primary" role="tablist">
                            <li class="nav-item active">
                                <a class="nav-link active" data-toggle="tab" href="#criteria" role="tab">Criteria</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#flow" role="tab">Flow</a>
                            </li>
                            <li class="nav-item d-none">
                                <a class="nav-link" data-toggle="tab" href="#event" role="tab">Events</a>
                            </li>
                            <li class="nav-item d-none">
                                <a class="nav-link" data-toggle="tab" href="#action" role="tab">Actions</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="criteria" role="tabpanel">
                                <div class="form-group row">
                                        <div class="col-lg-12">
                                            <table id="BarcodeProductLife" class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
                                                <thead>
                                                <tr>
                                                    <th width="10%">Sr No</th>
                                                    <th width="20%">Field Name</th>
                                                    <th width="20%">Operator</th>
                                                    <th width="20%">Value</th>
                                                    <th width="15%">Value</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr id="dataEntryForm">
                                                    <td><input readonly type="text"  class="form-control form-control-sm" id="flow_criteria_sr_number"></td>
                                                    <td>
                                                        <select id="menu_flow_criteria_dtl_field" class="form-control form-control-sm">
                                                            <option value="">Select</option>
                                                                <option value=""></option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="menu_flow_criteria_dtl_operator" class="form-control form-control-sm">
                                                            <option value="">Select</option>
                                                            <option value="=">Equal</option>
                                                            <option value="!=">Not equal</option>
                                                            <option value="Like">Like</option>
                                                            <option value="Between">Between</option>
                                                            <option value=">">Greater than</option>
                                                            <option value="<">Less than</option>
                                                            <option value="<=">Less than or equal to</option>
                                                            <option value=">=">Greater than or equal to</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input id="menu_flow_criteria_dtl_value" type="text" class="form-control form-control-sm">
                                                    </td>
                                                    <td>
                                                        <select id="menu_flow_criteria_dtl_operation" class="form-control form-control-sm">
                                                            <option value="">Select</option>
                                                            <option value="AND">AND</option>
                                                            <option value="OR">OR</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button type="button" id="addData" class="btn btn-primary btn-sm ">
                                                            <i class="la la-plus"></i> Add
                                                        </button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody id="repeated_data">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>{{-- end row--}}
                            </div>{{--tabend--}}
                            <div class="tab-pane" id="flow" role="tabpanel">
                                <div id="kt_repeater_flow">
                                    <div class="form-group row">
                                        <div data-repeater-list="flow_criteria_data" class="col-lg-12">
                                            <div data-repeater-item class="kt-margin-b-10 barcode">
                                                <div class="form-group row">
                                                    <label class="col-lg-2 erp-col-form-label">Flow Name:</label>
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <div class="erp-select2 form-group">
                                                                <select class="form-control kt-select2 erp-form-control-sm" name="form_flow_criteria">
                                                                    <option value="0">Select</option>
                                                                    <option value="1">Data Entry</option>
                                                                    <option value="2">Approval</option>
                                                                    <option value="3">Director Approval</option>
                                                                    <option value="4">Manager Approval </option>
                                                                    <option value="5">Posting</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm">
                                                            <i class="la la-remove"></i>
                                                        </a>
                                                    </div>
                                                </div>{{-- end row--}}
                                                <div class="row">
                                                    <ul class="nav nav-tabs col-lg-12" role="tablist" style="    background: #f2f3f7;">
                                                        <li class="nav-item">
                                                            <a class="nav-link active rep_action" data-toggle="tab" href="#rep_action" role="tab">Actions</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link rep_designation" data-toggle="tab" href="#rep_designation" role="tab">Designation / Users</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link rep_time" data-toggle="tab" href="#rep_time" role="tab">Time</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link rep_bypass" data-toggle="tab" href="#rep_bypass" role="tab">By Pass</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content col-lg-12">
                                                        <div class="tab-pane active rep_action_content" id="rep_action" role="tabpanel">
                                                            <div class="row">
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Archive
                                                                        <input type="checkbox" name="action">
                                                                        <span></span>
                                                                    </label>
                                                                    <div class="open_notification" data-url="{{action('Common\GetAllData@openNotification')}}">Send Notification..</div>

                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> New
                                                                        <input type="checkbox" name="action">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Pull Back
                                                                        <input type="checkbox" name="action">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Save
                                                                        <input type="checkbox" name="action">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane rep_designation_content" id="rep_designation" role="tabpanel">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Users:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="erp-select2 form-group">
                                                                                <select class="form-control tag-select2 erp-form-control-sm" multiple name="users[]">
                                                                                    <option value="1">Ehsan</option>
                                                                                    <option value="2">Ali</option>
                                                                                    <option value="3">Zaid</option>
                                                                                    <option value="4">Imran</option>
                                                                                    <option value="5">Khalid</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Designation:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="erp-select2 form-group">
                                                                                <select class="form-control tag-select2 erp-form-control-sm" multiple name="designation[]">
                                                                                    <option value="1">Manager</option>
                                                                                    <option value="2">Data Operator</option>
                                                                                    <option value="3">Accountant</option>
                                                                                    <option value="4">Cashier</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">All of them:</label>
                                                                        <div class="col-lg-9">
                                                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                                                <input type="radio" name="select_user">
                                                                                <span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Any of them:</label>
                                                                        <div class="col-lg-9">
                                                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                                                <input type="radio" name="select_user">
                                                                                <span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane rep_time_content" id="rep_time" role="tabpanel">
                                                            <div class="row form-group">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Lead Time:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="input-group">
                                                                                <div class="erp-select2" style="width: 66.66%;">
                                                                                    <select class="form-control erp-form-control-sm" id="product_warranty_period" name="product_warranty_period">
                                                                                        <option value="0">Select</option>
                                                                                        <option value="1">Minutes</option>
                                                                                        <option value="2">Hours</option>
                                                                                        <option value="3">Days</option>
                                                                                        <option value="4">Weeks</option>
                                                                                        <option value="5">Month</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div style="width: 33.33%;">
                                                                                    <input type="text" id="product_warranty_mode" name="product_warranty_mode" class="form-control erp-form-control-sm">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Reminder Time:</label>
                                                                        <div class="col-lg-9">
                                                                            <input type="text" name="reminder_time" class="form-control erp-form-control-sm">
                                                                            <span><small>Note: Time write in minute</small></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane rep_bypass_content" id="rep_bypass" role="tabpanel">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Users:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="erp-select2 form-group">
                                                                                <select class="form-control tag-select2 erp-form-control-sm" multiple name="bypass_users[]">
                                                                                    <option value="1">Ehsan</option>
                                                                                    <option value="2">Ali</option>
                                                                                    <option value="3">Zaid</option>
                                                                                    <option value="4">Imran</option>
                                                                                    <option value="5">Khalid</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Designation:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="erp-select2 form-group">
                                                                                <select class="form-control tag-select2 erp-form-control-sm" multiple name="bypass_designation[]">
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">Manager</option>
                                                                                    <option value="2">Data Operator</option>
                                                                                    <option value="3">Accountant</option>
                                                                                    <option value="4">Cashier</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-right">
                                            <div data-repeater-create="" class="btn btn btn-primary">
                                                <span id="new">
                                                    <i class="la la-plus"></i>
                                                    <span>Add</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>{{--tabend--}}
                            <div class="tab-pane d-none" id="flow_" role="tabpanel">
                                <div id="flow_content">
                                    <div class="form-group row">
                                        <div class="col-lg-3"></div>
                                        <div class="col-lg-6">
                                            <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed" >
                                                <thead>
                                                    <tr>
                                                        <th width="50%" colspan="2" class="text-center">Flow</th>
                                                        <th width="50%" colspan="{{ $data['length'] }}" class="text-center">Action</th>
                                                    </tr>
                                                    <tr style="background-color:#5867dd; color:white;">
                                                        <th width="40%">Description</th>
                                                        <th width="10%" class="text-center">Apply</th>
                                                        @foreach($data['action'] as $action)
                                                            <th  class="text-center">{{ $action->menu_action_name }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($data['flow'] as $i =>$flow)
                                                    <tr>
                                                        <td><input type="hidden" class="form-control form-control-sm" name="flowid" value="{{ $flow->menu_flow_id }}">{{ $flow->menu_flow_name }}</td>
                                                        <td class="text-center">
                                                            <label  class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                                                <input class="apply"  type="checkbox" name="action[{{ $flow->menu_flow_name }}]">
                                                                <span></span>
														    </label>
                                                        </td>

                                                        @foreach($data['action'] as $j=>$action)
                                                            <td class="text-center">
                                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                                                    <input disabled type="checkbox" name="action[{{ $action->menu_action_name }}]"  >
                                                                    <span></span>
                                                                </label>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>{{--rowend--}}
                                </div>
                            </div>{{--tabend--}}
                            <div class="tab-pane d-none" id="event" role="tabpanel">
                                <div id="event_content">
                                    <div class="form-group row">
                                        <div class="col-lg-3"></div>
                                        <div class="col-lg-6">
                                            <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed" >
                                                <thead>
                                                    <tr style="background-color:#5867dd; color:white;">
                                                         <th width="70%">Description</th>
                                                        <th width="30%" class="text-center">Apply</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($data['event'] as $event)
                                                    <tr>
                                                        <td><input type="hidden" class="form-control form-control-sm" name="eventid" value="{{ $event->menu_event_id }}">{{ $event->menu_event_name }}</td>
                                                        <td class="text-center">
                                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                                                <input type="checkbox">
                                                                <span></span>
														    </label>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>{{--rowend--}}
                                </div>
                            </div>{{--tabend--}}
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>

    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
@endsection

@section('customJS')
<script>
    $('.open_notification').on('click',function(e){
        var data_url = $(this).attr('data-url');
        openModal(data_url);
    });
   $(document).ready(function(){
  $("#menu_flow_criteria_name").change(function(){
    var formtable =  $(this).val();
    $.ajax({
            type:'GET',
            url:'/flow-criteria/menu-data/'+ formtable,
            success: function(response,  data){
                //console.log(response);
                if(data)
                {
                    $("#menu_flow_criteria_dtl_field").empty();
                    $("#menu_flow_criteria_dtl_field").append('<option>Select</option>');
                    $.each(response,function(key,value){
                        $("#menu_flow_criteria_dtl_field").append('<option value="'+key+'">'+value+'</option>');
                    });
                }
            }
        });
  });

    $('.apply').click(function(){
        var val = $(this).is(":checked");
        if(val == true)
        {
            $(this).parents('tr').find('input').attr('disabled',false);
        }else
        {
            $(this).parents('tr').find('input').attr('disabled',true);
            $(this).attr('disabled',false);
        }

    });
});

</script>
    <script src="{{ asset('js/pages/flowcriteria-rpeated.js') }}" type="text/javascript"></script>
@endsection

