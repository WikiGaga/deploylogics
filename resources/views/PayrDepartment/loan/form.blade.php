@extends('layouts.template')
@section('title', 'Loan')

@section('pageCSS')
    <style>
        #repeated_data .bodyRow td{
            padding: 0px !important;
        }
        #repeated_data .bodyRow input{
            border: none !important;
        }
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $loan_type = 0;
            $date =  date('d-m-Y');
            $user_id = Auth::user()->id;
        }
        if($case == 'edit'){
            $id = $data['current']->loan_id;
            $name = $data['current']->description;
            $loan_confi_type_name = $data['current']->loan_type;
            $department = $data['current']->department;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->loan_date))));
            $start_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->loan_start_date))));
            $end_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->loan_end_date))));
            $loan_amount = $data['current']->loan_amount;
            $installment_amount = $data['current']->installment_amount;       
            $installment_no = $data['current']->installment_no;
            $loan_deduction = $data['current']->loan_deduction;
            $loan_paid = $data['current']->loan_paid;
            $balance = $data['current']->balance_loan;      
            $remark = $data['current']->remarks;
            $loan_installment_dtl = isset($data['current']->loan_installment_dtl) ? $data['current']->loan_installment_dtl : [] ;

        }
    @endphp
    {{-- @permission($data['permission']) --}}
    <form id="loan_form" class="kt-form" method="post" action="{{ action('PayrDepartment\LoanController@store', isset($id)?$id:'') }}">
        @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="erp-page--title">
                                            {{isset($code)?$code:""}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Employee Name: <span class="required">*</span></label>
                                    <div class="col-lg-8">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text btn-minus-selected-data">
                                                            <i class="la la-minus-circle"></i>
                                                        </span>
                                                    </div>
                                                <input type="text" name="employee_name"  id="employee_name" value="{{isset($employee_name)?$employee_name:''}}" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'employeeHelp') }}" maxlength="100" class="open_inline__help form-control erp-form-control-sm">
                                                <input type="hidden" id="employee_id" name="employee_id" value="{{isset($employee_id)?$employee_id:''}}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Date:</label>
                                    <div class="col-lg-8">
                                        <div class="input-group date">
                                            <input type="text" name="loan_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="loan_start_date" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{--end row--}}
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Department:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="department"  id="department" class="moveIndex form-control erp-form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Designation:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="designation"  id="designation" class="moveIndex form-control erp-form-control-sm" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Description: <span class="required">*</span></label>
                                    <div class="col-lg-8">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text btn-minus-selected-data">
                                                            <i class="la la-minus-circle"></i>
                                                        </span>
                                                    </div>
                                                <input type="text" name="loan_confi_name"  id="loan_confi_name" value="{{isset($loan_confi_type_name)?$loan_confi_type_name:''}}" maxlength="100" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'loanConfiHelp') }}" class="open_inline__help form-control erp-form-control-sm">
                                                <input type="hidden" id="loan_confi_id" name="loan_confi_id" value="{{isset($loan_confi_id)?$loan_confi_id:''}}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Loan Type:</label>
                                    <div class="col-lg-8">
                                        <div class="erp-select2">
                                            <input type="text" readonly id="loan_confi_type_name" name="loan_confi_type_name" class="form-control erp-form-control-sm" value="{{isset($loan_confi_type_name)?$loan_confi_type_name:''}}"> 
                                            <input type="hidden" readonly id="loan_confi_type_id" value="loan_confi_type_name" value="{{isset($loan_confi_type_id)?$loan_confi_type_id:''}}">
                                            {{--<select class="form-control erp-form-control-sm kt-select2 moveIndex" id="loan_type" name="loan_type">
                                                <option value="0">Select</option>                                      
                                                @foreach($data['loan_type'] as $loan)
                                                    <option value="{{$loan->advance_type_id}}" {{ $loan_type == $loan->advance_type_id?"selected":""}}>{{ucfirst(strtolower($loan->advance_type_name))}}</option>
                                                @endforeach
                                             
                                            </select>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{--end row--}}
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Loan Start Date:</label>
                                    <div class="col-lg-8">
                                        <div class="input-group date">
                                            <input type="text" name="loan_start_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($start_date)?$start_date:""}}" id="kt_datepicker_3" />
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
                                    <label class="col-lg-4 erp-col-form-label">Loan End Date:</label>
                                    <div class="col-lg-8">
                                        <div class="input-group date">
                                            <input type="text" name="loan_end_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($end_date)?$end_date:""}}" id="kt_datepicker_3" />
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
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">Loan Amount:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="loan_amount"  id ="loan_amount" class="moveIndex form-control erp-form-control-sm" value="{{isset($loan_amount) ? $loan_amount:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                        <label class="col-lg-4 erp-col-form-label">Per Installment Amount:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="installment_amount" id="installment_amount" class="moveIndex per_installment valid_number form-control erp-form-control-sm" value="{{isset($installment_amount) ? $installment_amount:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label">No of Installment:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="installment_no"  id="installment_no"  class="installment_no moveIndex form-control erp-form-control-sm"  value="{{isset($installment_no) ? $installment_no:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                        <label class="col-lg-4 erp-col-form-label">Loan Deduction  Amount:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="loan_deduction" id="loan_deduction"  class="moveIndex form-control erp-form-control-sm" value="{{isset($loan_deduction) ? $loan_deduction:""}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <div class="row">
                                        <label class="col-lg-4 erp-col-form-label"> Loan Paid:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="loan_paid" id="loan_paid" class="moveIndex form-control erp-form-control-sm" value="{{isset($loan_paid) ? $loan_paid:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-4 erp-col-form-label"> Balance:</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="balance_loan" id="balance_loan" class="moveIndex form-control erp-form-control-sm" value="{{isset($balance)? $balance:" "}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <label class="col-lg-2 erp-col-form-label">Remarks:</label>
                                    <div class="col-lg-10">
                                        <textarea type="text" rows="2" name="remarks" maxlength="255" class="form-control erp-form-control-sm moveIndex">{{isset($remark)?$remark:''}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-12">
                                <button class="btn btn-success btn-sm pull-right mt-3" id="generateInstallments" type="button">Generate Installments</button>
                            </div>
                        </div>
{{----}}    
                        <div class="kt-portlet d-none" id="kt_portlet_table" style="margin-top:10px;">
                            <div class="row row-block">
                                <div class="col-lg-12">
                                    {{-- <button class="btn btn-success btn-sm pull-right mt-3 mb-2" id="printInstallments">Print</button> --}}
                                    <table width="100%" id="rep_datatable" class="table bt-datatable table-bordered data-table" >
                                        <thead>
                                            <tr>
                                                <th class="text-center">Sr</th>
                                                <th class="text-center">Date</th>
                                                <th class="text-center">Amount</th>
                                                <th class="text-center">Paid Amount</th>
                                                <th class="text-center">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody id="repeated_data">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
{{----}}
                    </div>
                </div>
            </div>
        </div>
    </form>
                <!--end::Form-->
    {{-- @endpermission --}}
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
@endsection
     
@section('customJS')
    <script src="{{ asset('js/pages/js/hr_department.js') }}" type="text/javascript"></script>
{{-- @endsection

@section('customJS') --}}
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/payr-department/loan.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>


    {{--table script--}}
    <script>
        $('#printInstallments').on('click',function(e){
            e.preventDefault();
            // var divToPrint= $('#repeated_data');
            // newWin= window.open("" , "_blank");
            // newWin.document.write(divToPrint.html());
            // newWin.print();
            // newWin.close();
        });

        $('#generateInstallments').on('click',function(e){
            e.preventDefault();
            var thix = $(this);
            var loan_amount = $('form').find('#loan_amount').val();
            var installment_amount = $('form').find('#installment_amount').val();
            var totalInstallments = $('form').find('#installment_no').val();

            if(loan_amount == ""){
                toastr.error('Please Enter Loan Ammount');
                $('form').find('#loan_amount').focus();
                return false;
            }
            if(installment_amount != "" && parseInt(installment_amount) > 0){
                var totalInstallments = filterNum(parseInt(loan_amount)/parseInt(installment_amount));
                totalInstallments = Math.round(totalInstallments);
                
            }else if(totalInstallments != "" && parseInt(totalInstallments) > 0){
                var installment_amount = filterNum(parseInt(loan_amount)/parseInt(totalInstallments));
                var installmentField = $('form').find('#installment_amount').val(parseInt(installment_amount));
            }

            var loanStartDate = $('form').find('#loan_start_date').val();
            // loanStartDate = loanStartDate.replace(/-/g , '/');
            var loanEndDate = $('form').find('#loan_end_date');
            if(parseInt(installment_amount) > parseInt(loan_amount)){
                toastr.error('Invalid Installment Value');
                var installment_amount = $('form').find('#installment_amount').val('');
                var totalInstallments = $('form').find('#installment_no').val('');
                $('#kt_portlet_table').addClass('d-none');
                $('#repeated_data').empty();
                return false;
            }else{
                $('form').find('#installment_no').val(totalInstallments);
                if (totalInstallments >= 1) {
                    $('#kt_portlet_table').removeClass('d-none');
                    $('#repeated_data').empty();
                    var tr = '';
                    var fixer = 0;
                    var balance_amount = 0;
                    for (var i = 1; i <= totalInstallments; i++) {
                        fixer = parseInt(fixer) + parseInt(installment_amount);
                        var tb_length = $('#repeated_data>tr[id]').length;                       
                        var total_length = i + parseInt(tb_length);
                        var sr_no = i + parseInt(tb_length);
                        if(i == totalInstallments){
                            var per_installment_amount = parseInt((loan_amount - fixer)) + parseInt(installment_amount); 
                        }else{
                            var per_installment_amount = parseInt(installment_amount); 
                        }
                        balance_amount = balance_amount + per_installment_amount;
                        myDate = new Date(validDateFormat(loanStartDate));
                        myDate.setMonth(myDate.getMonth() + i);
                        myDate = formatDate(myDate);
                        loanEndDate.val(myDate);
                        tr += '<tr class="bodyRow">' +
                            '<td class="handle">' +
                            '<input type="text" id="sr_no" name="pd[' + total_length + '][sr_no]"  value="' + sr_no + '"  class="form-control form-control-sm " readonly>' +
                            '<input type="hidden" id="loan_installment_id" data-id="loan_installment_id" name="pd[' + total_length + '][loan_installment_id]" value="" class="form-control form-control-sm " readonly>' +'</td>' +
                            '<td><input type="text" name="pd['+total_length+'][date]" data-id="date" readonly value="'+myDate+'" class="form-control form-control-sm moveIndex kt_datepicker_3" id="kt_datepicker_3" /></td>' +
                            '<td><input type="text" id="per_installment_amount" name="pd[' + total_length + '][per_installment_amount]" value="'+per_installment_amount +'" title="'  + '" class="form-control form-control-sm text-right" readonly></td>' +
                            '<td><input type="text" id="paid_amount" name="pd[' + total_length + '][paid_amount]" title="'  + '" class="form-control form-control-sm validNumber text-right" value="0" ></td>' +
                            '<td><input type="text" id="balance_amount" name="pd[' + total_length + '][balance_amount]" title="'  + '" class="form-control form-control-sm validNumber text-right" value="'+ balance_amount +'" ></td>' +
                            '</tr>';
                    }
                        tr +=   '<tr>' + 
                                    '<td colspan="2"><strong>Total Loan Amount:</strong></td>' + 
                                    '<td>'+loan_amount+'</td>' +
                                    '<td>0</td>' +
                                    '<td>'+balance_amount+'</td>' +
                                '</tr>';
                    $('#repeated_data').empty();
                    $('#repeated_data').append(tr);            
                }else{
                    $('#kt_portlet_table').addClass('d-none');
                }
            }
        });
    </script>
    

    <script>

        // function gi() {
        //     alert(132123123123);
        // }

        function filterNum(val) {
            if (isNaN(val)) {
                return '';
            }
            return val;
        }
        function validDateFormat(date){
            var d = date.split('-');
            return [d[1], d[0], d[2]].join('-');
        }
        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;

            return [day, month, year].join('-');
        }
    </script>
  

    <script>
        // $(document).on('change','.per_installment',function(){
        //     var thix = $(this);
        //     var loan_amount = $('form').find('#loan_amount').val();
        //     var installment_amount = $('form').find('#installment_amount').val();
        //     var totalInstallments = filterNum(parseInt(loan_amount)/parseInt(installment_amount));
        //     totalInstallments = Math.round(totalInstallments);
        //     var loanStartDate = $('form').find('#loan_start_date').val();
        //     loanStartDate = loanStartDate.replace(/-/g , '/');
        //     var loanEndDate = $('form').find('#loan_end_date');
        //     if(parseInt(installment_amount) > parseInt(loan_amount)){
        //         toastr.error('Invalid Installment Value');
        //         $('#kt_portlet_table').addClass('d-none');
        //         $('#repeated_data').empty();
        //         return false;
        //     }else{
        //         $('form').find('#installment_no').val(totalInstallments);
        //         if (totalInstallments >= 1) {
        //             $('#kt_portlet_table').removeClass('d-none');
        //             $('#repeated_data').empty();
        //             var tr = '';
        //             var fixer = 0;
        //             var balance_amount = 0;
        //             for (var i = 1; i <= totalInstallments; i++) {
        //                 fixer = parseInt(fixer) + parseInt(installment_amount);
        //                 var tb_length = $('#repeated_data>tr[id]').length;                       
        //                 var total_length = i + parseInt(tb_length);
        //                 var sr_no = i + parseInt(tb_length);
        //                 if(i == totalInstallments){
        //                     var per_installment_amount = parseInt((loan_amount - fixer)) + parseInt(installment_amount); 
        //                 }else{
        //                     var per_installment_amount = parseInt(installment_amount); 
        //                 }
        //                 balance_amount = balance_amount + per_installment_amount;
        //                 myDate = new Date(loanStartDate);
        //                 myDate.setMonth(myDate.getMonth() + i);
        //                 myDate = formatDate(myDate);
        //                 loanEndDate.val(myDate);
        //                 tr += '<tr class="bodyRow">' +
        //                     '<td class="handle">' +
        //                     '<input type="text" id="sr_no" name="pd[' + total_length + '][sr_no]"  value="' + sr_no + '"  class="form-control form-control-sm " readonly>' +
        //                     '<input type="hidden" id="installment_dtl_id" data-id="installment_dtl_id" name="pd[' + total_length + '][installment_dtl_id]" value="" class="form-control form-control-sm " readonly>' +'</td>' +
        //                     '<td><input type="text" name="pd['+total_length+'][installment_date]" data-id="installment_date" readonly value="'+myDate+'" class="form-control form-control-sm moveIndex kt_datepicker_3" id="kt_datepicker_3" /></td>' +
        //                     '<td><input type="text" id="per_installment_amount" name="pd[' + total_length + '][per_installment_amount]" value="'+per_installment_amount +'" title="'  + '" class="form-control form-control-sm" readonly></td>' +
        //                     '<td><input type="text" id="paid_amount" name="pd[' + total_length + '][paid_amount]" title="'  + '" class="form-control form-control-sm" value="0" ></td>' +
        //                     '<td><input type="text" id="balance_amount" name="pd[' + total_length + '][balance_amount]" title="'  + '" class="form-control form-control-sm validNumber" value="'+ balance_amount +'" ></td>' +
        //                     '</tr>';
        //             }
        //                 tr +=   '<tr>' + 
        //                             '<td colspan="2"><strong>Total Loan Amount:</strong></td>' + 
        //                             '<td>'+loan_amount+'</td>' +
        //                             '<td>0</td>' +
        //                             '<td>'+balance_amount+'</td>' +
        //                         '</tr>';
        //             $('#repeated_data').empty();
        //             $('#repeated_data').append(tr);            
        //         }else{
        //             $('#kt_portlet_table').addClass('d-none');
        //         }
        //     }
        // });
    </script>
    {{--table script--}}
@endsection
