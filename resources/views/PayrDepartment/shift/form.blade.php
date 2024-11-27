@extends('layouts.template')
@section('title', 'Cost Of Hiring')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $id = $data['current']->shift_id;
            $name = $data['current']->shift_name;
            $short_name=$data['current']->shift_short_name;
            $shift_start = $data['current']->shift_start_time;
            $shift_break = $data['current']->shift_break_time;
            $shift_close = $data['current']->shift_close_time;
            $notes = $data['current']->shift_short_notes;
            $status = $data['current']->shift_entry_status;
        }
    @endphp

@permission($data['permission']);
<form id="cost_of_hiring_form" class="hr_department kt-form" method="post" action="{{ action('PayrDepartment\ShiftController@store', isset($id)?$id:"") }}">
    @csrf
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <!--begin::Form-->
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Shift Name: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="name" value="{{isset($name)?$name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Shift Short Name: </label>
                            <div class="col-lg-6">
                                <input type="text" name="short_name" value="{{isset($short_name)?$short_name:""}}" maxlength="100" class="form-control erp-form-control-sm">
                            </div>
                        </div>

                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Shift Start Time:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="shift_start"  readonly class="form-control  erp-form-control-sm"  value="{{isset($shift_start)?$shift_start:""}}"  id="kt_timepicker_3" />
                                         <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-clock-o"></i>
                                            </span>
                                        </div>
                                    </div>
                              </div>
                        </div>


                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Shift Break Time:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="shift_break" readonly class="form-control erp-form-control-sm c-time-p"  value="{{isset($shift_break)?$shift_break:""}}" id="kt_timepicker_3" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-clock-o"></i>
                                            </span>
                                        </div>
                                    </div>
                              </div>
                        </div>


                        <div class="form-group-block row">
                            <label class="col-lg-3 erp-col-form-label">Shift Close Time:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="shift_close" readonly  class="form-control  erp-form-control-sm c-time-p"  value="{{isset($shift_close)?$shift_close:""}}" id="kt_timepicker_3" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-clock-o"></i>
                                            </span>
                                        </div>
                                    </div>
                              </div>
                        </div>


                        <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                        <div class="col-lg-6">
                            <textarea type="text" name="notes" maxlength="250"  class="form-control erp-form-control-sm" rows="4" >{{ isset($notes)?$notes:''}}</textarea>
                        </div>
                        </div>

                        <div class="form-group-block row">
                        <label class="col-lg-3 erp-col-form-label">Status:</label>
                            <div class="col-lg-6">
                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                    <label>
                                        @if($case == 'edit')
                                            @php $entry_status = isset($status)?$status:""; @endphp
                                            <input type="checkbox" name="shift_entry_status" {{$entry_status==1?"checked":""}}>
                                        @else
                                            <input type="checkbox" name="shift_entry_status" checked>
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection



@section('customJS')
    <script src="{{ asset('js/pages/js/hr_department.js') }}" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-timepicker.js" type="text/javascript"></script>
@endsection