@extends('layouts.template')
@section('title', 'Notification Settings')

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
                <form id="NotificationSettings_form" class="kt-form" method="post" action="{{ isset($data['notification']) ? action('Development\NotificationSettingsController@update', $data['notification']->id) : action('Development\NotificationSettingsController@store') }}">
                    @csrf
                    @if(isset($data['notification']))
                        <input type="hidden" name="_method" value="PUT">
                    @endif
                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Name:</label>
                                    <div class="col-lg-12">
                                        <input type="text" name="notification_title" class="form-control form-control-sm" value="{{ isset($data['notification']) ? $data['notification']->title : '' }}" placeholder="Enter Name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Select Form:</label>
                                    <div class="col-lg-12">
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
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-3 col-form-label">
                                        <label class="rtl-toggle" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">Push Notification</span>
                                            <input type="checkbox" id="rtlToggle" onchange="toggleRTL(this.checked)" autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                    <div class="col-lg-3 col-form-label">
                                        <label class="rtl-toggle" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">Email</span>
                                            <input type="checkbox" id="rtlToggle" onchange="toggleRTL(this.checked)" autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                    <div class="col-lg-3 col-form-label">
                                        <label class="rtl-toggle" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">Whatsapp</span>
                                            <input type="checkbox" id="rtlToggle" onchange="toggleRTL(this.checked)" autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                    <div class="col-lg-3 col-form-label">
                                    
                                        <label class="rtl-toggle" title="Toggle Right-to-Left Layout">
                                            <span class="rtl-label">SMS</span>
                                            <input type="checkbox" id="rtlToggle" onchange="toggleRTL(this.checked)" autocomplete="off">
                                            <span class="rtl-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Whatsapp Template Name:</label>
                                    <div class="col-lg-12">
                                        <input type="text" name="whatsapp_template" class="form-control form-control-sm" placeholder="Enter Whatsapp Template Name"></input>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-12 col-form-label">Notification Message:</label>
                                    <div class="col-lg-12">
                                        <textarea name="notification_message" rows="5" class="form-control form-control-sm" placeholder="Enter Notification Message">{{ isset($data['notification']) ? $data['notification']->message : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end row--}}
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

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Form</th>
                            <th>Message</th>
                            <th>Push Notification</th>
                            <th>Email</th>
                            <th>Whatsapp</th>
                            <th>SMS</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['notification_settings'] as $setting)
                            <tr>
                                <td>{{ $setting->title }}</td>
                                <td>App\\Models\\GRN</td>
                                <td>You have a notification from the GRN</td>
                                <td>
                                    <span class="badge bg-success text-white p-2 rounded-full">Enable</span>
                                </td>
                                <td><span class="badge bg-success text-white p-2 rounded-full">Enable</span></td>
                                <td><span class="badge bg-success text-white p-2 rounded-full">Enable</span></td>
                                <td><span class="badge bg-success text-white p-2 rounded-full">Enable</span></td>
                                <td><button class="btn btn-sm btn-primary open_notification" data-url="{{ action('Development\NotificationSettingsController@index', $setting->id) }}">Edit</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
<script>
    $('.open_notification').on('click',function(e){
        var data_url = $(this).attr('data-url');
        openModal(data_url);
    });
   $(document).ready(function(){
  $("#menu_flow_criteria_name").change(function(){
    var formtable =  $(this).find('option:selected').attr('data-table-name');
    if (!formtable) return;
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

