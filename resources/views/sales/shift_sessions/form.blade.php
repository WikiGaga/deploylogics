@extends('layouts.layout')
@section('title', 'Shift Session Form')

@section('pageCSS')
    <style>
        input[readonly] {
            background-color: #e9ecef !important;
            opacity: 1;
        }
    </style>
@endsection

@section('content')
    <!--begin::Form-->
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            $branch = $data['branch'];
            
            $readonly='required';


            if($case == 'new'){
                $readonly='required';
                $code = $data['session_no'];
                
            }
            if($case == 'edit'){
                $id = $data['current']->session_id;
                $code = $data['current']->session_no;
                $readonly='readonly';
            }
        $form_type = 'shift_sessions';
    @endphp
    @permission($data['permission'])
    <form id="shift_sessions_form" class="kt-form" method="post" action="{{ action('Sales\ShiftSessionsController@store', isset($id)?$id:"") }}">
    <input type="hidden" value='{{$form_type}}' id="form_type" name="form_type">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    Session # {{isset($code)?$code:"-"}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Session Start Date:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    <input type="hidden"  name="session_date" class="session_no" value="{{$code}}"/>
                                    <input type="text" {{$readonly}} name="session_date" class="moveIndex form-control erp-form-control-sm c-date-p kt_datepicker_33" value="{{isset($data['current']->start_date)?$data['current']->start_date:""}}" autofocus/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Session Branch:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    @if($case == 'edit')
                                        <input type="text" {{$readonly}} name="session_branch" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{isset($data['current']->branch->branch_name)?$data['current']->branch->branch_name:""}}"/>
                                
                                    @else
                                        <select id="" name="session_branch" class="form-control erp-form-control-sm">
                                            <option value="">{{ __('message.select') }}</option>
                                            @foreach ($branch as $b)
                                                    <option value="{{ $b->branch_id }}" >
                                                        {{ $b->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Opening Cash:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    <input type="text" name="opening_cash" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{isset($data['current']->opening_cash)?$data['current']->opening_cash:""}}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Closing Cash:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    <input type="text" name="closing_cash" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{isset($data['current']->closing_cash)?$data['current']->closing_cash:""}}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Opening Visa:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    <input type="text" name="opening_visa" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{isset($data['current']->opening_visa)?$data['current']->opening_visa:""}}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Closing Visa:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    <input type="text" name="closing_visa" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{isset($data['current']->closing_visa)?$data['current']->closing_visa:""}}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Session End At:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    <input type="text" {{$readonly}} name="session_end_date" class="moveIndex form-control erp-form-control-sm c-date-p kt_datepicker_33" value="{{isset($data['current']->end_date)?$data['current']->end_date:""}}"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-12 erp-col-form-label">Session Status:</label>
                            <div class="col-lg-12">
                                <div class="input-group date">
                                    @if($case == 'edit')
                                        <input type="text" {{$readonly}} name="session_status" class="moveIndex form-control erp-form-control-sm c-date-p" value="{{isset($data['current']->session_status) ? ($data['current']->session_status == 'close' ? "Closed" : "Open") : ""}}"/>
                                    @else
                                        <select id="" name="session_status" class="form-control erp-form-control-sm">
                                            <option value="close" >close</option>
                                            <option value="open" >open</option>
                                            @end
                                        </select>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-2">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <!-- <button 
                                    type="submit"
                                    @if(!isset($data['current']->session_status) || $data['current']->session_status == 'open') disabled @endif
                                    id="shift_session_submit"
                                    class="btn btn-success btn-sm"
                                >
                                    Update
                                </button> -->
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
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/sale/shift_sessions.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }

        $('.kt_datepicker_33').datetimepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            pickerPosition: 'bottom-left',
            todayHighlight: true,
            templates: arrows,
            
            // Updated Format: dd-mm-yyyy followed by hours:minutes
            // 'hh' is 12-hour, 'HH' is 24-hour. 'ii' is minutes.
            format: "dd-mm-yyyy HH:ii", 
            
            showMeridian: true, // Set to true for AM/PM support
            minuteStep: 5      // Optional: interval between minutes
        }); 
    </script>
@endsection
