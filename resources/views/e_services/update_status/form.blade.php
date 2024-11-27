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
                $id = $data['first_current']->update_status_id;
                $code = $data['first_current']->update_status_code;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['first_current']->status_date))));;
                $notes = $data['first_current']->notes;
            }
    @endphp
    @permission($data['permission'])
    <form id="update_status_form" class="update_status_form kt-form" method="post" action="{{ action('EServices\UpdateStatusController@store', isset($id) ? $id : '') }}">
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
                            <label class="col-lg-6 erp-col-form-label">Date: <span class="required">*</span></label>
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

                <div class="row form-group-block mt-3">
                    <table class="table table-bordered table-striped table-responsive table-sm" id="scheduleTable">
                        <thead>
                            <th class="w-10">Job Scheduled</th>
                            <th class="">Sales Man</th>
                            <th class="">City</th>
                            <th class="">Area</th>
                            <th>Customer</th>
                            <th>Phone No</th>
                            <th>Request Date</th>
                            <th>Request No</th>
                            <th>Schedule Date</th>
                            <th>Order Date</th>
                            <th>Order No</th>
                            <th>Quoted Amount</th>
                            <th>Actual Amount</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            {{-- This Array is comming from Update Status Table --}}
                            @if(isset($data['current']) && $data['search_applied'] == FALSE)
                                @foreach($data['current'] as $current)
                                    <tr>
                                        <td class="text-center">
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="vertical-align: inherit;">
                                                <input type="checkbox" autocomplete="off" checked class="checkRow" name="pd[{{ $loop->iteration }}][checkRow]">
                                                <span></span>
                                            </label>
                                            <input type="hidden" name="pd[{{ $loop->iteration }}][city_id]" autocomplete="off" value="{{ isset($current->order->city_id) ? $current->order->city_id : '' }}">
                                            <input type="hidden" name="pd[{{ $loop->iteration }}][area_id]" autocomplete="off" value="{{ isset($current->order->area_id) ? $current->order->area_id : '' }}">
                                            <input type="hidden" name="pd[{{ $loop->iteration }}][sales_order_id]" autocomplete="off" value="{{ $current->order_id }}">
                                            <input type="hidden" name="pd[{{ $loop->iteration }}][sales_quotation_id]" autocomplete="off" value="{{ $current->quotation_id }}">
                                            <input type="hidden" name="pd[{{ $loop->iteration }}][sales_man_id]" class="sales_man_id" value="{{ $current->quotation->assigned_sales_man }}">
                                            <input type="hidden" name="pd[{{ $loop->iteration }}][schedule_id]" class="schedule_id" value="{{ $current->schedule_id }}">
                                        </td>
                                        <td class="salesManText">{{ $current->quotation->AssignedSalesMan->name ?? '' }}</td>
                                        <td>{{ $current->order->city->city_name }}</td>
                                        <td>{{ $current->order->area->area_name }}</td>
                                        <td>{{ $current->order->customer->customer_name }}</td>
                                        <td>{{ $current->order->sales_order_mobile_no }}</td>
                                        <td>{{ isset($current->quotation->sales_order_date) ? date('m/d/Y' , strtotime($current->quotation->sales_order_date)) : '' }}</td>
                                        <td>{{ isset($current->quotation->sales_order_code) ? $current->quotation->sales_order_code : ''  }}</td>
                                        <td>{{ isset($current->schedule->schedule_dtl_schedule_date) ? date('m/d/Y' , strtotime($current->schedule->schedule_dtl_schedule_date)) : '' }}</td>
                                        <td>{{ isset($current->order->sales_order_date) ? date('m/d/Y' , strtotime($current->order->sales_order_date)) : '' }}</td>
                                        <td>{{ isset($current->order->sales_order_code) ? $current->order->sales_order_code : ''  }}</td>
                                        <td>{{ isset($current->order->sub_total) ? $current->quotation->sub_total : ''  }}</td>
                                        <td>{{ isset($current->order->net_total) ? $current->quotation->net_total : ''  }}</td>
                                        <td>
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm kt-select2" name="pd[{{ $loop->iteration }}][order_status]">
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
                            
                            {{-- This Array is Comming From Sales Table --}}
                            @if(isset($data['records']))
                                @foreach($data['records'] as $current)
                                <tr>
                                    <td class="text-center">
                                        <label class="kt-checkbox kt-checkbox--bold {{ $current["schedule_status"] == 1 ? 'kt-checkbox--success' : 'kt-checkbox--primary' }}" style="vertical-align: inherit;">
                                            <input type="checkbox" autocomplete="off" {{ $current["schedule_status"] == 1 ? 'checked' : "" }} class="checkRow" name="pd[{{ $loop->iteration }}][checkRow]">
                                            <span></span>
                                        </label>
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][city_id]" autocomplete="off" value="{{ isset($current['city_id']) ? $current['city_id'] : '' }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][area_id]" autocomplete="off" value="{{ isset($current['area']) ? $current['area_id'] : '' }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][sales_order_id]" autocomplete="off" value="{{ $current['sales_order_id'] }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][sales_quotation_id]" autocomplete="off" value="{{ $current['sales_quotation_id'] }}">
                                        <input type="hidden" name="pd[{{ $loop->iteration }}][schedule_id]" class="schedule_id" value="{{ $current['schedule_id'] }}">
                                    </td>
                                    <td class="salesManText">{{ $current['schedule_salesman'] }}</td>
                                    <td>{{ $current['city'] }}</td>
                                    <td>{{ $current['area'] }}</td>
                                    <td>{{ $current['customer_name'] }}</td>
                                    <td>{{ $current['phone_no'] }}</td>
                                    <td>{{ isset($current['request_date']) ? $current['request_date'] : '' }}</td>
                                    <td>{{ isset($current['request_no']) ? $current['request_no'] : ''  }}</td>
                                    <td>{{ $current['schedule_dt_date'] }}</td>
                                    <td>{{ $current['order_date'] }}</td>
                                    <td>{{ $current['order_no'] }}</td>
                                    <td>{{ isset($current['quoted_amount']) ? $current['quoted_amount'] : '' }}</td>
                                    <td>{{ $current['actual_amount'] }}</td>
                                    <td>
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2" name="pd[{{ $loop->iteration }}][order_status]">
                                                @foreach($data['order_status'] as $status)
                                                    @php $status_id = isset($current['status_id']) ? $current['status_id'] : "" ;  @endphp
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
    </form>
    <form action="{{ action('EServices\UpdateStatusController@create' , isset($id) ? $id : '') }}" method="GET" id="updateStatusForm">
        @include('common.quick_panels.update_status')
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
    <script src="/js/pages/js/e-services/update-status/update-status.js" type="text/javascript"></script>
    <script src="/js/pages/js/e-services/update-status/update-status-form.js" type="text/javascript"></script>

@endsection

@section('customJS')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
@endsection
