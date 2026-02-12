
@extends('layouts.layout')
 @section('title', 'Employee Attendance')

@section('pageCSS')
@endsection
@section('content')
    @php

   
            $employees = $data['employee'];


            $id = $data['att_id'];
            $att_no = $data['att_no'];
            $form_type = $data['form_type'];
            $att_data = $data['att_data'];
            $att_note = $data['att_note'];

            if(empty($id)){
                $date =  date('d-m-Y');
            }
            else{
                $date = date('d-m-Y', strtotime($data['att_date']));
            }

    @endphp


   
  
    <!--begin::Form-->
        <form id="voucher_form" class="kt-form" method="post" action="{{action('PayrDepartment\EmployeeAttendanceController@store', [$id])}}">
 
    @csrf
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
                                        $headings = ['Sr No','Employee Name','Type','Date Time'];
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
                                                <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm">
                                                <input readonly id="budget_id" type="hidden" class="budget_id form-control erp-form-control-sm">
                                                <input readonly id="invoice_id" type="hidden" class="invoice_id form-control erp-form-control-sm">
                                                <input readonly id="budget_branch_id" type="hidden" class="budget_branch_id form-control erp-form-control-sm">
                                                <input readonly id="cheque_book_id" type="hidden" class="cheque_book_id form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                                <div class="erp_form__grid_th_title">Employee</div>
                                                <div class="erp_form__grid_th_input">
                                                    <select id="employee_select" name="employee_select" class="form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                           @foreach ($employees as $employee)
                                                                    <option value="{{ $employee->employee_id }}" >
                                                                        {{ $employee->employee_name }}</option>
                                                            @endforeach

                                                        @end
                                                    </select>
                                                </div>
                                            </th>

                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Type</div>
                                                <div class="erp_form__grid_th_input">
                                                    <select id="type_select" name="type_select" class="form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                        <option value="Check-In" >Check-In</option>
                                                        <option value="Check-Out" >Check-Out</option>
                                                        @end
                                                    </select>
                                                </div>
                                            </th>

                                            <th cope="col">
                                                <div class="erp_form__grid_th_title">Date</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input type="text"  id="att_date" name="att_date" class="form-control erp-form-control-sm moveIndex c-date-p kt_datepicker_33" readonly
                                                    value="{{ isset($date) ? $date : '' }}" id="kt_datepicker_33" autofocus />
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
                                          
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][cheque_book_id]" data-id="cheque_book_id" value=""  class="cheque_book_id form-control erp-form-control-sm">
                                                </td>
                                               <td>
                                                    <select id="pd_uom" class="pd_uom form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                           @foreach ($employees as $employee)
                                                                    <option value="{{ $employee->employee_id }}" {{$rec->emp_id == $employee->employee_id?'selected':''}} >{{ $employee->employee_name }}</option>
                                                            @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <select id="pd_uom" class="pd_uom form-control erp-form-control-sm">
                                                        <option value="">{{ __('message.select') }}</option>
                                                        <option value="Check-In" {{$rec->attendance_type == 'Check-In'?'selected':''}}>Check-In</option>
                                                        <option value="Check-Out" {{$rec->attendance_type == 'Check-Out'?'selected':''}}>Check-Out</option>
                                                    </select> 
                                                </td>

                                                <input type="text" name="po_date" class="form-control erp-form-control-sm kt_datepicker_33" readonly
                                                    value="{{ isset($date) ? $date : '' }}" id="kt_datepicker_33"  />

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
            
            // Updated Format: dd-mm-yyyy followed by hours:minutes
            // 'hh' is 12-hour, 'HH' is 24-hour. 'ii' is minutes.
            format: "dd-mm-yyyy HH:ii", 
            
            showMeridian: true, // Set to true for AM/PM support
            minuteStep: 5      // Optional: interval between minutes
        }); 

        $('.kt_datepicker_33').datetimepicker('setDate', new Date());

        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
        var chequebookHelpUrl = "{{url('/common/help-open/chequebookHelp')}}";
        var budgetHelpUrl = "{{url('/common/inline-help/budgetHelp')}}";
        var invoiceHelp = "{{url('/common/inline-help/invoiceHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'employee_select',
                'fieldClass':'Select Employee',
                'message':'',
                'require':true,
                'readonly':true,

            },
            {
                'id':'type_select',
                'fieldClass':'Select Type',
                'message':'',
                'require':true,
                'readonly':true,

            }, {
                'id':'att_date',
                'fieldClass':'Date is Required',
                'message':'',
                'require':true,
                'readonly':true,

            },
           
           
        ];
        var arr_hidden_field = [];
        var form_type = $('#form_type').val();
       
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection

