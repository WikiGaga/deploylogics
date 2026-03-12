@extends('layouts.template')
@section('title', 'Form Flow Criteria')

@section('pageCSS')
<style>
    /* Criteria table styling */
    #repeated_data tr {
        transition: background-color 0.2s;
    }

    #repeated_data tr:hover {
        background-color: #f7f8fa;
    }

    /* Fix for Select2 dropdown visibility */
    .select2-container {
        z-index: 9999 !important;
    }

    /* Flow tabs styling */
    .flow-tabs .nav-link {
        cursor: pointer;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
</style>
@endsection
@section('content')
    <!-- begin:: Content -->
    <form id="FlowCriteria_form" class="kt-form" method="post" action="{{ isset($data['flowCriteria']) ? action('Development\FlowCriteriaController@update', $data['flowCriteria']->menu_flow_criteria_id) : action('Development\FlowCriteriaController@store') }}">
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header', ['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                @csrf
                @if(isset($data['flowCriteria']))
                    <input type="hidden" name="_method" value="PUT">
                @endif
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">Reference ID:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="menu_flow_criteria_dtl_id" class="form-control form-control-sm" readonly value="{{ isset($data['flowCriteria']) ? $data['flowCriteria']->menu_flow_criteria_dtl_id : '' }}" placeholder="Auto-generated on save" style="background-color: #f7f8fa;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" class="form-control" readonly value="{{ isset($data['flowCriteria']) ? date('d-m-Y', strtotime($data['flowCriteria']->menu_flow_criteria_apply_at)) : date('d-m-Y') }}" name="menu_flow_criteria_apply_at" id="kt_datepicker_3" />
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
                                            <option value="{{ $menue->menu_dtl_name }}" data-menu-dtl-id="{{ $menue->menu_dtl_id }}" data-table-name="{{ $menue->menu_dtl_table_name }}" {{ (isset($data['flowCriteria']) && $data['flowCriteria']->menu_flow_criteria_name == $menue->menu_dtl_name) ? 'selected' : '' }}>{{ $menue->menu_dtl_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="menu_dtl_id" name="menu_dtl_id" value="{{ $data['flowCriteria']->menu_dtl_id ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Flow criteria / Staging:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                @if(isset($data['flowCriteria']))
                                                    <input type="checkbox" id="flow_criteria_enabled_switch" {{ ($data['flowCriteria']->menu_flow_criteria_status ?? 1) == 1 ? 'checked' : '' }}>
                                                @else
                                                    <input type="checkbox" id="flow_criteria_enabled_switch" checked>
                                                @endif
                                                <span></span>
                                            </label>
                                        </span>
                                        <span class="form-text text-muted">Turn on to enable flow criteria and staging for this form.</span>
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
                                                    <th width="15%">Logic Operator</th>
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
                                                        <input id="menu_flow_criteria_dtl_value" type="text" class="form-control form-control-sm" placeholder="Value">
                                                        <div id="between_value_wrapper" style="display:none; margin-top:4px;">
                                                            <div class="d-flex align-items-center" style="gap:4px;">
                                                                <input id="menu_flow_criteria_dtl_value_from" type="text" class="form-control form-control-sm" placeholder="From">
                                                                <span class="text-muted" style="white-space:nowrap; font-size:11px;">AND</span>
                                                                <input id="menu_flow_criteria_dtl_value_to" type="text" class="form-control form-control-sm" placeholder="To">
                                                            </div>
                                                        </div>
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
                                                                    <option value="2">Review</option>
                                                                    <option value="3">Manager Approval</option>
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
                                                    <div class="col-lg-12">
                                                        <ul class="nav nav-tabs flow-tabs" role="tablist" style="background: #f2f3f7; margin-bottom: 15px;">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-toggle="tab" data-target-tab="actions" role="tab">Actions</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" data-target-tab="designation" role="tab">Designation / Users</a>
                                                            </li>
                                                            <li class="nav-item d-none">
                                                                <a class="nav-link" data-toggle="tab" data-target-tab="time" role="tab">Time</a>
                                                            </li>
                                                            <li class="nav-item d-none">
                                                                <a class="nav-link" data-toggle="tab" data-target-tab="bypass" role="tab">By Pass</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content" style="padding: 15px; border: 1px solid #e2e5ec; border-top: none;">
                                                            <div class="tab-pane active" data-tab-pane="actions">
                                                            <div class="row">
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Save
                                                                        <input type="checkbox" name="action" data-action-code="save">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Forward
                                                                        <input type="checkbox" name="action" data-action-code="forward">
                                                                        <span></span>
                                                                    </label>
                                                                    {{-- <div class="open_notification" data-url="{{action('Common\GetAllData@openNotification')}}">Send Notification..</div> --}}
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Send Back
                                                                        <input type="checkbox" name="action" data-action-code="back">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Post
                                                                        <input type="checkbox" name="action" data-action-code="post">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand"> Cancel
                                                                        <input type="checkbox" name="action" data-action-code="cancel">
                                                                        <span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            </div>
                                                            <div class="tab-pane" data-tab-pane="designation">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Users:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="erp-select2 form-group">
                                                                                <select class="form-control tag-select2 erp-form-control-sm" multiple name="users[]">
                                                                                    <option value="">Select Users</option>
                                                                                    @foreach($data['users'] as $user)
                                                                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                                                    @endforeach
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
                                                                                    <option value="">Select Roles</option>
                                                                                    @foreach($data['roles'] as $role)
                                                                                        <option value="{{ $role->id }}">{{ $role->display_name ?? $role->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row d-none">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">All of them:</label>
                                                                        <div class="col-lg-9">
                                                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                                                <input type="radio" name="select_user_type" value="all">
                                                                                <span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row d-none">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Any of them:</label>
                                                                        <div class="col-lg-9">
                                                                            <label class="kt-radio kt-radio--bold kt-radio--brand">
                                                                                <input type="radio" name="select_user_type" value="any" checked>
                                                                                <span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </div>
                                                            <div class="tab-pane d-none" data-tab-pane="time">
                                                            <div class="row form-group">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Lead Time:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="input-group">
                                                                                <div class="erp-select2" style="width: 66.66%;">
                                                                                    <select class="form-control erp-form-control-sm" name="product_warranty_period">
                                                                                        <option value="0">Select</option>
                                                                                        <option value="1">Minutes</option>
                                                                                        <option value="2">Hours</option>
                                                                                        <option value="3">Days</option>
                                                                                        <option value="4">Weeks</option>
                                                                                        <option value="5">Month</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div style="width: 33.33%;">
                                                                                    <input type="text" name="product_warranty_mode" class="form-control erp-form-control-sm">
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
                                                            <div class="tab-pane d-none" data-tab-pane="bypass">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="row">
                                                                        <label class="col-lg-3 erp-col-form-label">Users:</label>
                                                                        <div class="col-lg-9">
                                                                            <div class="erp-select2 form-group">
                                                                                <select class="form-control tag-select2 erp-form-control-sm" multiple name="bypass_users[]">
                                                                                    <option value="">Select Users</option>
                                                                                    @foreach($data['users'] as $user)
                                                                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                                                    @endforeach
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
                                                                                    <option value="">Select Roles</option>
                                                                                    @foreach($data['roles'] as $role)
                                                                                        <option value="{{ $role->id }}">{{ $role->display_name ?? $role->name }}</option>
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
                <!--end::Form-->
            </div>
        </div>
    </div>
    </form>

    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
@if(isset($data['flowCriteria']))
<script>
    var flowCriteriaData = {!! json_encode([
        'conditions' => $data['flowCriteria']->conditions->map(function($c) {
            return [
                'condition_sr_number' => $c->condition_sr_number,
                'condition_field' => $c->condition_field,
                'condition_operator' => $c->condition_operator,
                'condition_value' => $c->condition_value,
                'condition_logic_operator' => $c->condition_logic_operator
            ];
        })->values(),
        'flows' => $data['flowCriteria']->flows->map(function($f) {
            return [
                'stg_flows_id' => $f->stg_flows_id,
                'flow_name' => $f->flow_name,
                'flow_order' => $f->flow_order,
                'lead_time_value' => $f->lead_time_value,
                'lead_time_unit' => $f->lead_time_unit,
                'reminder_time_minutes' => $f->reminder_time_minutes,
                'require_all_users' => $f->require_all_users,
                'actions' => $f->actions->map(function($a) {
                    return ['action_name' => $a->action_name];
                })->values(),
                'users' => $f->users->map(function($u) {
                    return ['user_id' => $u->user_id];
                })->values(),
                'designations' => $f->designations->map(function($d) {
                    return ['designation_id' => $d->designation_id];
                })->values(),
                'bypasses' => $f->bypasses->map(function($b) {
                    return [
                        'bypass_type' => $b->bypass_type,
                        'bypass_user_id' => $b->bypass_user_id,
                        'bypass_designation_id' => $b->bypass_designation_id
                    ];
                })->values()
            ];
        })->values()
    ]) !!};
</script>
@else
<script>
    var flowCriteriaData = null;
</script>
@endif
    <script src="{{ asset('js/pages/flowcriteria-rpeated.js') }}" type="text/javascript"></script>
@endsection

