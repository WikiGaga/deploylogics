
@extends('layouts.layout')
 @section('title', 'Employee Attendance')

@section('pageCSS')
@endsection
@section('content')
    @php

   
            $employees = $data['employee'];
            $allEmployees = $data['all_employee'];
            $id = $data['att_id'];
            $att_no = $data['att_no'];
            $form_type = $data['form_type'];
            $att_data = $data['att_data'];
            $att_note = $data['att_note'];

            if(empty($id)){
                $date =  date('d-m-Y');
            }else{
                $date = date('d-m-Y', strtotime($data['att_date']));
            }

    @endphp


   
  
    <!--begin::Form-->
        <form id="voucher_form" class="kt-form" method="post" action="{{action('PayrDepartment\EmployeeAttendanceController@store', [$id])}}">
 
    @csrf
        @if(!empty($data['attendance_snapshot']))
            <input type="hidden" name="attendance_snapshot" value="{{ $data['attendance_snapshot'] }}">
        @endif
        <input type="hidden" value='{{$form_type}}' id="form_type">
        <input type="hidden" value='{{$att_no}}' name="att_no">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{$att_no}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-5 erp-col-form-label">All Employees:</label>
                                <div class="col-lg-7">
                                    <select id="all_employee_select" class="form-control erp-form-control-sm">
                                        <option value="">{{ __('message.select') }}</option>
                                        @foreach ($allEmployees as $employee)
                                            <option value="{{ $employee->employee_id }}" data-out-side-branch="{{ $employee->out_side_branch }}">
                                                {{ $employee->employee_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-5 erp-col-form-label">Date:</label>
                                <div class="col-lg-7">
                                    <div class="input-group date">
                                        <input type="text" name="date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{ $date}}"  id="kt_datepicker_3" autofocus/>
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
                
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="data_entry_header">
                                <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> {{ __('message.fields_hide') }}</div>
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = ['Sr No','Employee Name','Attendance Type','Source','Out Side Branch','Time'];
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
                                @include('layouts.pageSettingBtn')
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table id="AccForm" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">{{ __('message.sr') }}</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <!-- <input readonly id="budget_branch_id" type="hidden" class="budget_branch_id form-control erp-form-control-sm"> -->
                                                <!-- <input readonly id="cheque_book_id" type="hidden" class="cheque_book_id form-control erp-form-control-sm"> -->
                                            </div>
                                        </th>
                                        <th scope="col">
                                                <div class="erp_form__grid_th_title">Employee</div>
                                                <div class="erp_form__grid_th_input">
                                                    <select id="employee_select" name="employee_select" class="form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                           @foreach ($employees as $employee)
                                                                    <option value="{{ $employee->employee_id }}" data-out-side-branch="{{ $employee->out_side_branch }}">
                                                                        {{ $employee->employee_name }}</option>
                                                            @endforeach

                                                        <!-- @end -->
                                                    </select>
                                                </div>
                                            </th>

                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Type</div>
                                                <div class="erp_form__grid_th_input">
                                                    <select id="type_select" name="type_select" class="form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                        <option value="check_in">Check-In</option>
                                                        <option value="check_out">Check-Out</option>
                                                    </select>
                                                </div>
                                            </th>

                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Source</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input type="text" id="attendance_source" value="web" class="form-control erp-form-control-sm" readonly>
                                                </div>
                                            </th>

                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Out Side Branch</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input type="text" id="out_side_branch_display" value="No" class="form-control erp-form-control-sm" readonly>
                                                </div>
                                            </th>

                                            <th cope="col">
                                                <div class="erp_form__grid_th_title">Time</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input type="text"  id="attendance_time" name="attendance_time" class="form-control erp-form-control-sm moveIndex c-date-p kt_datepicker_33" readonly
                                                    value="" autofocus />
                                                </div>
                                            </th>

                                        <th scope="col" width="48">
                                            <div class="erp_form__grid_th_title">{{ __('message.action') }}</div>
                                            <div class="erp_form__grid_th_btn">
                                                <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                    @if(isset($att_data))
                                        @foreach($att_data as $rec)
                                          
                                            <tr data-original-employee="{{ $rec->emp_id }}" data-original-outside="{{ (int) ($rec->out_side_branch ?? 0) }}">
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" data-id="attendance_detail_id" name="pd[{{$loop->iteration}}][attendance_detail_id]" value="{{$rec->id}}">
                                                    <!-- <input readonly type="hidden" name="pd[{{ $loop->iteration }}][cheque_book_id]" data-id="cheque_book_id" value=""  class="cheque_book_id form-control erp-form-control-sm"> -->
                                                </td>
                                               <td>
                                                    <select data-id="employee_select" name="pd[{{$loop->iteration}}][employee_select]" class=" form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                           @foreach ($employees as $employee)
                                                                    <option value="{{ $employee->employee_id }}" data-out-side-branch="{{ $employee->out_side_branch }}" {{$rec->emp_id == $employee->employee_id?'selected':''}} >{{ $employee->employee_name }}</option>
                                                            @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <select data-id="type_select" name="pd[{{$loop->iteration}}][type_select]" class=" form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                        <option value="check_in" {{$rec->attendance_type == 'check_in'?'selected':''}}>Check-In</option>
                                                        <option value="check_out" {{$rec->attendance_type == 'check_out'?'selected':''}}>Check-Out</option>
                                                    </select> 
                                                </td>
                                                <td>
                                                    <input type="text" data-id="attendance_source" value="{{ strtolower($rec->type ?? 'app') }}" class="form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" data-id="out_side_branch_display" value="{{ (int) ($rec->out_side_branch ?? 0) === 1 ? 'Yes' : 'No' }}" class="form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td>
                                                <input type="text" data-id="attendance_time" name="pd[{{$loop->iteration}}][attendance_time]" class="form-control erp-form-control-sm kt_datepicker_33" readonly
                                                    value="{{ !empty($rec->attendance_time) ? date('h:i A', strtotime($rec->attendance_time)) : '' }}"   />
                                                    
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
                                    <tbody>
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <style>
                        .bill_list_block{
                            border: 1px solid #d6d6d6;
                            margin-bottom: 20px;
                            padding: 10px 0;
                        }
                        table#bill_list_data {
                            border-bottom: 2px solid #a5a5a5;
                        }
                        table#bill_list_data th {
                            border: 0px solid #cecece;
                            background: #ececec;
                            font-size: 12px;
                            font-weight: 500 !important;
                            text-align: center;
                            padding: 12px 3px !important;
                            font-family: Roboto;
                        }
                        table#bill_list_data td {
                            font-size: 12px;
                            font-weight: 400;
                            padding: 5px 3px !important;
                            /*border: 1px solid #ebedf2;*/
                        }
                        table#bill_list_data tr:nth-child(even)>td {
                            background: #fbfbfb;
                            border-bottom: 2px solid #dadada;
                        }
                        table#bill_list_data tr:nth-child(even)>td input {
                            background: #fbfbfb;
                        }
                        .pd_bank_recon_input{
                            width: 100%;
                            border: none;
                        }
                        .pd_bank_recon_input_open{
                            width: 100%;
                            border: 1px solid #ececec;
                            border-radius: 3px;
                        }
                        .pd_bank_recon_input:focus{
                            outline: 0;
                        }
                    </style>
                    <div class="form-group-block row">
                        <label class="col-lg-2 erp-col-form-label">{{ __('message.notes') }}:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" id="att_note" name="att_note" class="form-control erp-form-control-sm">{{$att_note}}</textarea>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </form>
                <!--end::Form-->

   
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')

    <script src="{{ asset('js/pages/js/voucher.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/account-table-calculations.js') }}" type="text/javascript"></script>
    <script>
        var var_form_name = 'bank_voucher';
    </script>
    <script>


 // Change .datepicker to .datetimepicker
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
            
            // The attendance date comes only from the header date field.
            format: "HH:ii P",
            showMeridian: true,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0,
            minuteStep: 5
        }); 

        $('#attendance_time').datetimepicker('setDate', new Date());

        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
        var chequebookHelpUrl = "{{url('/common/help-open/chequebookHelp')}}";
        var budgetHelpUrl = "{{url('/common/inline-help/budgetHelp')}}";
        var invoiceHelp = "{{url('/common/inline-help/invoiceHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'employee_select',
                'fieldClass':'',
                'message':'Select Employee',
                'require':true,
                'readonly':true,
                'type':'select'

            },
            {
                'id':'type_select',
                'fieldClass':'Select Type',
                'message':'Select Type',
                'require':true,
                'readonly':true,
                'type':'select'

            },
            {
                'id':'attendance_source',
                'fieldClass':'',
                'message':'',
                'require':false,
                'readonly':true,

            }, {
                'id':'attendance_time',
                'fieldClass':'',
                'message':'Time is Required',
                'require':true,
                'readonly':true,

            },
           
           
        ];
        var arr_hidden_field = [];
        var form_type = $('#form_type').val();
       
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            // Attendance has select and time-only columns. Build its rows locally so
            // the generic voucher repeater cannot drop columns on subsequent adds.
            $('#addData').off('click').on('click.attendance', function (event) {
                event.preventDefault();

                var $table = $(this).closest('table');
                var employeeId = $('#employee_select').val();
                var attendanceType = $('#type_select').val();
                var attendanceTime = $('#attendance_time').val();

                if (!employeeId) {
                    alert('Select Employee');
                    return;
                }
                if (!attendanceType) {
                    alert('Select Type');
                    return;
                }
                if (!attendanceTime) {
                    alert('Time is Required');
                    return;
                }

                var rowNumber = $table.find('tbody.erp_form__grid_body > tr').length + 1;
                var $row = $('<tr>', {class: 'new-row'});
                var $serialCell = $('<td>', {class: 'handle'})
                    .append($('<i>', {class: 'fa fa-arrows-alt-v handle'}))
                    .append($('<input>', {
                        type: 'text',
                        name: 'pd[' + rowNumber + '][sr_no]',
                        value: rowNumber,
                        title: rowNumber,
                        class: 'form-control erp-form-control-sm handle',
                        readonly: true
                    }));

                var $employee = $('#employee_select').clone()
                    .removeAttr('id')
                    .attr('data-id', 'employee_select')
                    .attr('name', 'pd[' + rowNumber + '][employee_select]')
                    .val(employeeId);
                var $type = $('#type_select').clone()
                    .removeAttr('id')
                    .attr('data-id', 'type_select')
                    .attr('name', 'pd[' + rowNumber + '][type_select]')
                    .val(attendanceType);
                var $source = $('<input>', {
                    type: 'text',
                    name: 'pd[' + rowNumber + '][attendance_source]',
                    'data-id': 'attendance_source',
                    value: 'web',
                    class: 'form-control erp-form-control-sm',
                    readonly: true
                });
                var $outsideBranch = $('<input>', {
                    type: 'text',
                    name: 'pd[' + rowNumber + '][out_side_branch_display]',
                    'data-id': 'out_side_branch_display',
                    value: $('#out_side_branch_display').val() || 'No',
                    class: 'form-control erp-form-control-sm',
                    readonly: true
                });
                var $time = $('<input>', {
                    type: 'text',
                    name: 'pd[' + rowNumber + '][attendance_time]',
                    'data-id': 'attendance_time',
                    value: attendanceTime,
                    class: 'form-control erp-form-control-sm kt_datepicker_33',
                    readonly: true
                });
                var $delete = $('<button>', {
                    type: 'button',
                    class: 'btn btn-danger gridBtn delData'
                }).append($('<i>', {class: 'la la-trash'}));

                $row.append($serialCell)
                    .append($('<td>').append($employee))
                    .append($('<td>').append($type))
                    .append($('<td>').append($source))
                    .append($('<td>').append($outsideBranch))
                    .append($('<td>').append($time))
                    .append($('<td>', {class: 'text-center'}).append(
                        $('<div>', {class: 'btn-group btn-group-sm', role: 'group'}).append($delete)
                    ));

                $table.find('tbody.erp_form__grid_body').append($row);
                $time.datetimepicker({
                    rtl: KTUtil.isRTL(),
                    todayBtn: 'linked',
                    autoclose: true,
                    pickerPosition: 'bottom-left',
                    todayHighlight: true,
                    templates: arrows,
                    format: 'HH:ii P',
                    showMeridian: true,
                    startView: 1,
                    minView: 0,
                    maxView: 1,
                    forceParse: 0,
                    minuteStep: 5
                });

                $('#employee_select, #type_select').prop('selectedIndex', 0);
                $('#all_employee_select').val('');
                $('#attendance_source').val('web');
                $('#out_side_branch_display').val('No');
                $('#attendance_time').datetimepicker('setDate', new Date());

                if (typeof updateKeys === 'function') {
                    updateKeys();
                }
            });

            $('#all_employee_select').on('change', function () {
                var $selected = $(this).find('option:selected');
                var employeeId = $selected.val();
                if (!employeeId) {
                    $('#employee_select').val('').trigger('change');
                    return;
                }

                var $gridEmployee = $('#employee_select');
                if (!$gridEmployee.find('option[value="' + employeeId + '"]').length) {
                    $gridEmployee.append($('<option>', {
                        value: employeeId,
                        text: $selected.text().trim(),
                        'data-out-side-branch': $selected.data('out-side-branch')
                    }));
                }
                $gridEmployee.val(employeeId).trigger('change');
            });

            $('#employee_select').on('change', function () {
                var outsideBranch = $(this).find('option:selected').data('out-side-branch');
                $('#out_side_branch_display').val(outsideBranch == 1 ? 'Yes' : 'No');
            });

            $('#AccForm').on('change', 'tbody select[data-id="employee_select"]', function () {
                var $row = $(this).closest('tr');
                var outsideBranch = $(this).find('option:selected').data('out-side-branch');
                if (String($(this).val()) === String($row.attr('data-original-employee'))) {
                    outsideBranch = $row.attr('data-original-outside');
                }
                $row.find('input[data-id="out_side_branch_display"]').val(outsideBranch == 1 ? 'Yes' : 'No');
            });
        });
    </script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection

