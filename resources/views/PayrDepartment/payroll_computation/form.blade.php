@extends('layouts.layout')
@section('title', 'Payroll Computation')
@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $date =  date('d-m-Y');
            $code =  "DEFAULT-0000001";
        }
        if($case == 'edit'){
          
            $id = $data['current']->payroll_computation_id;
            $name = $data['current']->payroll_computation_name; 
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->payroll_computation_date))));
            $entry_status = $data['current']->payroll_computation_entry_status;
            $payroll_allowance = isset($data['current']->payroll_allowance) ? $data['current']->payroll_allowance : [] ;
            $payroll_deduction = isset($data['current']->payroll_deduction) ? $data['current']->payroll_deduction : [] ;  

        }
    @endphp
    @permission($data['permission'])
    <form id="payroll_computation_form" class="kt-form" method="post" action="{{ action('PayrDepartment\PayrollComputation@store',isset($id)?$id:'') }}">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="erp-page--title">
                                {{isset($code)?$code:""}}
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <label class="col-lg-6 col-form-label">Name:</label>
                                <div class="col-lg-6">
                                    <input type="text" name="name" class="form-control erp-form-control-sm" value="{{isset($name) ? $name:" "}}">
                                </div>
                            </div>{{-- /name row --}}
                        </div>
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <label class="col-lg-6 col-form-label">Document Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="date" autocomplete="off" class="form-control erp-form-control-sm c-date-p kt_date" readonly value="{{isset($date)?$date:""}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- /date row --}}
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Status:</label>
                                <div class="col-lg-6">
                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                        <label>
                                            @if($case == 'edit')
                                                @php $entry_status = isset($entry_status)?$entry_status:""; @endphp
                                                <input type="checkbox" name="entry_status" {{$entry_status==1?"checked":""}}>
                                            @else
                                                <input type="checkbox" name="entry_status" checked>
                                            @endif
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>{{-- /status row --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet kt-portlet--mobile" id="kt_repeater_allowance">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Allowances
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="salary_computation">
                        <div data-repeater-list="repeater_list">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="erp-col-form-label">Salary Head:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="erp-col-form-label">Salary Type:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="erp-col-form-label">Value:</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($case == 'new')
                            <div data-repeater-item class="repeater_list_item">
                              
                                <div class="row  form-group-block salary_cal_block">
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm salaryHeadName salary_head" name="n" value="">
                                                        <option value="1">Basic Salary</option>
                                                        <option value="2">House Rent Allow</option>
                                                        <option value="3">Over Time</option>
                                                    </select>

                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm salary_type" name="t">
                                                        <option value="Fixed">Fixed</option>
                                                        <option value="formula">Formula</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="value_input_block">

                                                    <input type="text" name="v" class="salary_head_val form-control erp-form-control-sm" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 text-right">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger" style="padding: 4.67px 13px;">
                                                    <i class="la la-trash-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            @endif
                            @if(isset($payroll_allowance) && $case == 'edit')
                            @foreach($payroll_allowance as $allowance)
                            <div data-repeater-item class="repeater_list_item">
                              
                                <div class="row  form-group-block salary_cal_block">
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm salaryHeadName salary_head" name="n" value="">
                                                        <option value="1" @if($allowance->allowance_salary_head == "1") selected @endif>Basic Salary</option>
                                                        <option value="2"@if($allowance->allowance_salary_head == "2") selected @endif >House Rent Allow</option>
                                                        <option value="3" @if($allowance->allowance_salary_head == "3") selected @endif>Over Time</option>
                                                    </select>   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm salary_type" name="t">
                                                        <option value="Fixed" @if($allowance->allowance_salary_type == "Fixed") selected @endif>Fixed</option>
                                                        <option value="formula" @if($allowance->allowance_salary_type == "formula") selected @endif>Formula</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="value_input_block">

                                                    <input type="text" name="v" class="salary_head_val form-control erp-form-control-sm" value="{{$allowance->allowance_value}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="col-lg-3 text-right">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger" style="padding: 4.67px 13px;">
                                                    <i class="la la-trash-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__foot">
                    <div class="row">
                        <div class="col-lg-12 kt-align-right">
                            <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                Add
                            </a>
                        </div>
                    </div>
                </div>
            </div>
          
            <div class="kt-portlet kt-portlet--mobile" id="kt_repeater_deduction">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Deductions
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="deduct_salary_computation">
                        <div data-repeater-list="deduct_repeater_list">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="erp-col-form-label">Salary Head:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="erp-col-form-label">Salary Type:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="erp-col-form-label">Value:</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($case == 'new')
                           <div data-repeater-item class="deduct_repeater_list_item">
                            <div class="row  form-group-block salary_cal_block">
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm salaryHeadName d_salary_head" name="n">
                                                    <option value="1" >Basic Salary</option>
                                                    <option value="2" >House Rent Allow</option>
                                                    <option value="3" >Over Time</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="erp-select2">
                                                <select class="form-control erp-form-control-sm d_salary_type" name="t">
                                                    <option value="Fixed" >Fixed</option>
                                                    <option value="formula">Formula</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="value_input_block">
                                                <input type="text" name="v" class="salary_head_val form-control erp-form-control-sm" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 text-right">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger" style="padding: 4.67px 13px;">
                                                <i class="la la-trash-o"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>     
                           @endif
                            @if(isset($payroll_deduction) && $case == 'edit')
                          
                            @foreach($payroll_deduction as $deduction )

                            <div data-repeater-item class="deduct_repeater_list_item">
                                <div class="row  form-group-block salary_cal_block">
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm salaryHeadName d_salary_head" name="n">
                                                        <option value="1" @if($allowance->deduction_salary_head == "1") selected @endif>Basic Salary</option>
                                                        <option value="2" @if($allowance->deduction_salary_head == "2") selected @endif>House Rent Allow</option>
                                                        <option value="3" @if($allowance->deduction_salary_head == "3") selected @endif>Over Time</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="erp-select2">
                                                    <select class="form-control erp-form-control-sm d_salary_type" name="t">
                                                        <option value="Fixed" @if($allowance->deduction_salary_type == "Fixed") selected @endif>Fixed</option>
                                                        <option value="formula" @if($allowance->deduction_salary_type == "formula") selected @endif>Formula</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="value_input_block">
                                                    <input type="text" name="v" class="salary_head_val form-control erp-form-control-sm" value="{{$deduction->deduction_value}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 text-right">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-sm btn-label-danger" style="padding: 4.67px 13px;">
                                                    <i class="la la-trash-o"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__foot">
                    <div class="row">
                        <div class="col-lg-12 kt-align-right">
                            <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                Add
                            </a>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </form>
    @endpermission
@endsection

@section('pageJS')
@endsection
@section('customJS')
    <script src="{{ asset('js/pages/js/payr-department/payroll-computation.js') }}" type="text/javascript"></script>
    <script>
        var KTFormRepeater = function() {
            var kt_repeater_allowance = function() {
                $('#kt_repeater_allowance').repeater({
                    initEmpty: false,
                    isFirstItemUndeletable: true,
                    defaultValues: {
                        // 'text-input': 'foo'
                    },
                    show: function() {
                        $(this).find('.salary_type').select2({
                            placeholder: "Select"
                        });
                        $(this).find('.salary_head').select2({
                            placeholder: "Select"
                        });
                        $(this).slideDown();
                    },
                    ready: function (setIndexes) {
                        $('.salary_type').select2({
                            placeholder: "Select"
                        });
                        $('.salary_head').select2({
                            placeholder: "Select"
                        });
                    },

                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                });
            }
            var kt_repeater_deduction = function() {
                $('#kt_repeater_deduction').repeater({
                    initEmpty: false,
                    isFirstItemUndeletable: true,
                    defaultValues: {
                        // 'text-input': 'foo'
                    },
                    show: function() {
                        $(this).find('.d_salary_type').select2({
                            placeholder: "Select"
                        });
                        $(this).find('.d_salary_head').select2({
                            placeholder: "Select"
                        });
                        $(this).slideDown();
                    },
                    ready: function (setIndexes) {
                        $('.d_salary_type').select2({
                            placeholder: "Select"
                        });
                        $('.d_salary_head').select2({
                            placeholder: "Select"
                        });
                    },
                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                });
            }
            return {
                // public functions
                init: function() {
                    kt_repeater_allowance();
                    kt_repeater_deduction();
                }
            };
        }();
        jQuery(document).ready(function() {
            KTFormRepeater.init();

        });


        function findIndex(name){
            var index = name.split('[')[1].split(']')[0];
            return index;
        }
        $(document).on('change','.salary_type,.d_salary_type',function(){
            var thix = $(this);
            let val = thix.val();
            var parent_class = thix.parents('.salary_cal_block');
            var parent = thix.parents('div[data-repeater-list]');
            var identitfier = parent.attr('data-repeater-list');
            var index = findIndex(thix.attr('name'));
            var field_input_only = '<input type="text" name="'+identitfier+'['+index+'][v]" class="salary_head_val form-control erp-form-control-sm">';
            var field_input_with_btn = '<div class="input-group">' +
                                        field_input_only +
                                        '<div class="input-group-append"> <span class="input-group-text"> <i class="la la-eye"></i> </span></div>' +
                                     '</div>';
            if(val == 'fixed'){
                var input_field = field_input_only;
            }
            if(val == 'formula'){
                var input_field = field_input_with_btn;
            }
         
            parent_class.find('.value_input_block').html(input_field);
        });
        var selected_salary_head = '';
        $(document).on('click','.la-eye',function(event){
            selected_salary_head = {
                main_row : $(this).parents('.salary_cal_block'),
                current_field : $(this).parents('.salary_cal_block').find('.salary_head_val'),
            }
            event.preventDefault();
            var formData = {
                value_input : $(this).parents('.value_input_block').find('input').val(),
                salary_head : $(this).parents('.salary_cal_block').find('.salaryHeadName option:selected').val(),
            }
            $("#kt_modal_KTDatatable_local").modal('show');
            var url = '/payroll-computation/open-formula-modal';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType	: 'json',
                data        : formData,
                success: function(res) {
                    var data = res.body;
                    $('.modal-content').html(data);
                },

            })
        });
        $(".modal").on('click', '.close', function (e) {
            close()
        });
        $(document).on('click','.salary_head_list>div,.salary_operators>div',function(){
            $('.salary_head_list>div').css({'background':'','color':''});
            $('.salary_operators>div').css({'background':'','color':''});
            $(this).css({'background':'#cecece','color':'#000000'});
        });
        $(document).on('dblclick','.salary_head_list>div,.salary_operators>div',function(){
           var thix_val = $(this).text();
           $(this).parents('.modal-body').find('#formula').val($(this).parents('.modal-body').find('#formula').val() + thix_val);
        });
        $(document).on('click','#calculate_generate',function(){
            var thix_val = $(this).parent().siblings('.modal-body').find('#formula').val();
            selected_salary_head.current_field.val(thix_val);
            close();
        });
        function getValue(str){
            var arr = str.split('');
            var myArr = [];
            for(var i=0;i<arr.length;i++){
                if(arr[i] == '['){
                    var a = '';
                    for(var j=i+1;j<arr.length;j++){
                        if(arr[j] == ']'){
                            var i = j-1;
                            break;
                        }else{
                            a += arr[j];
                        }
                    }
                    myArr.push(a);
                }
                var operatorsArr = ['+','-','*','/','%'];
                if(operatorsArr.includes(arr[i])){
                    myArr.push(arr[i]);
                }
                var numbArr = ['0','1','2','3','4','5','6','7','8','9'];
                if(numbArr.includes(arr[i])){
                    var a = '';
                    for(var j=i;j<arr.length;j++){
                        if(numbArr.includes(arr[j])){
                            a += arr[j];
                        }else{
                            var i = j-1;
                            break;
                        }
                    }
                    myArr.push(parseInt(a));
                }
            }
            var totalAmount = '';
             return parseInt(totalAmount);
        }
        function close(){
            $('#kt_modal_KTDatatable_local').find('.modal-content').empty();
            $('#kt_modal_KTDatatable_local').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
            $('#kt_modal_KTDatatable_local').modal('hide');
        }
    </script>
@endsection
