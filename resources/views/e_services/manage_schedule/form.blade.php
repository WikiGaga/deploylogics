@extends('layouts.layout')
@section('title', $data['page_data']['title'])

@section('pageCSS')
    <style>
        .erp-col-form-label{
            padding-top: calc(0.4rem + 1px);
        }
        #scheduleTable th{
            min-width: 115px;
        }
    </style>
@endsection

@section('content')

    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['document_code'];
                $date =  date('d-m-Y');
            }
            if($case == 'edit'){
                $id = $data['first_current']->schedule_id;
                $code = $data['first_current']->schedule_code;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['first_current']->schedule_date))));
                $start_time = $data['first_current']->schedule_start_time;
                $interval = $data['first_current']->schedule_interval_minutes;
                $salesman = $data['first_current']->schedule_assign_to;
                $notes = $data['first_current']->notes;
            }
    @endphp
    @permission($data['permission'])
    <form id="manage_schedule_form" class="manage_schedule_form kt-form" method="post" action="{{ action('EServices\ManageScheduleController@store', isset($id) ? $id : '') }}">
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
                                    {{isset($code)?$code:""}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <div class="pull-right">
                            <button type="button" class="btn btn-primary btn-sm" id="kt_quick_panel_toggler_btn">Filter Requests</button>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Schedule Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="schedule_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                            <label class="col-lg-6 erp-col-form-label">Start Time: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                <input class="form-control erp-form-control-sm" name="start_time" id="start_time" placeholder="Select time" value="{{ isset($start_time) ? $start_time : '' }}"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-clock-o"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Interval Minutes: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                <input class="form-control erp-form-control-sm validNumber onlyNumber" name="interval" id="interval" placeholder="Enter Interval" value="{{ isset($interval) ? $interval : '' }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label text-left">Assign To: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2" id="salesman" name="salesman">
                                        <option value="0">Select</option>
                                        @foreach($data['users'] as $user)
                                            @php $select_saleman = isset($salesman) ? $salesman : 0; @endphp
                                            <option value="{{ $user->id }}" {{$user->id==$select_saleman?"selected":''}}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Notes:</label>
                            <div class="col-lg-9">
                                <div class="input-group date">
                                <input class="form-control erp-form-control-sm" name="notes" id="notes" placeholder="" type="text" value="{{ isset($notes) ? $notes : '' }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-12">
                        <div class="row text-right">
                            <div class="col-12">
                                <button type="button" id="updateData" class="btn btn-success btn-sm">Update Data</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row form-group-block mt-3">
                    <table class="table table-bordered table-striped table-responsive table-sm @if($case == 'new') d-none @endif" id="scheduleTable">
                        <thead>
                            <th class="w-10">Job Scheduled</th>
                            <th class="">Sales Man</th>
                            <th class="">City</th>
                            <th class="">Area</th>
                            <th>Customer</th>
                            <th>Phone No</th>
                            <th>Request Date</th>
                            <th>Request No</th>
                            <th>Scheduled Date</th>
                            <th>Scheduled Time</th>
                            <th>Order Date</th>
                            <th>Order No</th>
                            <th>Quoted Amount</th>
                            <th>Actual Amount</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @if($case == 'edit')
                                @foreach($data['current'] as $current)
                                <tr>
                                    <td class="text-center">
                                        <label class="kt-checkbox kt-checkbox--bold {{ $current->schedule_status == 1 ? 'kt-checkbox--success' : 'kt-checkbox--primary' }}" style="vertical-align: inherit;">
                                            <input type="checkbox" autocomplete="off" {{ $current->schedule_status == 1 ? 'checked' : '' }} class="checkRow" name="pd[{{ $loop->iteration }}][checkRow]">
                                            <span></span>
                                        </label>
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][city_id]" autocomplete="off" value="{{ isset($current->quotation->city_id) ? $current->quotation->city_id : '' }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][area_id]" autocomplete="off" value="{{ isset($current->quotation->area) ? $current->quotation->area_id : '' }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][sales_order_id]" autocomplete="off" value="{{ isset($current->order_id) ? $current->order_id : '' }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][sales_quotation_id]" autocomplete="off" value="{{ $current->request_quotation_id }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][schedule_time]" class="scheduleTime" value="{{ $current->schedule_dtl_schedule_time }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][schedule_date]" class="scheduleDate" value="{{ date('d-m-Y', strtotime(trim(str_replace('/','-',$current->schedule_dtl_schedule_date)))) }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][sales_man_id]" class="sales_man_id" value="{{ $current->user->id }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][status_id]" class="status_id" value="1">
                                    </td>
                                    <td class="salesManText">{{ $current->user->name }}</td>
                                    <td>{{ $current->dtls->city->city_name }}</td>
                                    <td>{{ $current->dtls->area->area_name }}</td>
                                    <td>{{ $current->dtls->customer->customer_name }}</td>
                                    <td>{{ $current->quotation->sales_order_mobile_no }}</td>
                                    <td>{{ isset($current->quotation->sales_order_date) ? date('m/d/Y' , strtotime($current->quotation->sales_order_date)) : '' }}</td>
                                    <td>{{ isset($current->quotation->sales_order_code) ? $current->quotation->sales_order_code : ''  }}</td>
                                    <td class="scheduleDateText">{{ date('m/d/Y' , strtotime($current->schedule_dtl_schedule_date)) }}</td>
                                    <td class="scheduleTimeText">{{ $current->schedule_dtl_schedule_time }}</td>
                                    <td>{{ isset($current->order->sales_order_date) ? date('m-d-Y' , strtotime($current->order->sales_order_date)) : '' }}</td>
                                    <td>{{ isset($current->order->sales_order_code) ? $current->order->sales_order_code : '' }}</td>
                                    <td>{{ isset($current->quotation->net_total) ? $current->quotation->net_total : '' }}</td>
                                    <td>{{ isset($current->quotation->net_total) ? $current->quotation->net_total : '' }}</td>
                                    <td>
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2" disabled="">
                                                @foreach($data['order_status'] as $status)
                                                    @php $status_id = isset($current->order->sales_order_status) ? $current->order->sales_order_status : "" ;  @endphp
                                                    <option value="{{ $status->order_status_id }}" {{ $status->order_status_id == $status_id ? 'selected' : "" }} >{{ $status->order_status_names }}</option>
                                                @endforeach
                                            </select>
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
    @include('common.quick_panels.manage_schedule')
    </form>
    <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>

    <!-- Date and Time Library -->
    <script>
        var reqURL = "{{ route('getScheduleData') }}";
    </script>
    <script type="text/javascript" src="/js/datejs/build/date.js"></script>
    <script src="/js/pages/js/e-services/manage-schedule/manage-schedule.js" type="text/javascript"></script>
    <script src="/js/pages/js/e-services/manage-schedule/manage-schedule-form.js" type="text/javascript"></script>

@endsection

@section('customJS')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
@endsection
