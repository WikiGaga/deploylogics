@extends('layouts.layout')
@section('title', 'Budget')

@section('pageCSS')
    <style>
        .bg_green {
            background: #4c9a2a !important;
            color: #fff !important;
        }

        .bg_red {
            background: #e9414e !important;
            color: #fff !important;
        }

        .bg_yellow {
            background: #f0e130 !important;
            color: #000 !important;
        }
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $document_code = $data['document_code'];
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $id = $data['current']->budget_id;
            $document_code = $data['current']->budget_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->budget_entry_date))));
            $budget_notes = $data['current']->budget_notes;
            $dtls = isset($data['dtl'])? $data['dtl'] :[];
        }
        $form_type = 'budget_form';
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="budget_form" class="account_global kt-form" method="post" action="{{ action('Accounts\BudgetController@store',isset($id)?$id:'') }}">
     @csrf
        <input type="hidden" value='{{$form_type}}' id="form_type">
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
                                        {{$document_code}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-8"></div>
                                <div class="col-lg-4">
                                    <div class="input-group date">
                                        <input type="text" name="budget_entry_date" class="form-control erp-form-control-sm moveIndex kt_datepicker_3 c-date-p" readonly value="{{isset($date)?$date:''}}" autofocus/>
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
                                <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = ['Sr No','Branch','Description','Analytic Account','Start Date',
                                                      'End Date','Alert Type','Credit Amount','Debit Amount','Practical Amount','Achievement'];
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
                                <table id="BudgetForm" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Branch</div>
                                            <div class="erp_form__grid_th_input">
                                                <select class="form-control erp-form-control-sm tb_moveIndex" id="budget_branch_id">
                                                    <option value="0">Select</option>
                                                    @foreach($data['branch'] as $branch)
                                                        <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Analytic Account
                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="account_name" type="text" class="acc_name tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Description</div>
                                            <div class="erp_form__grid_th_input">
                                                <input  id="budget_budgetart_position" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Start Date</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget_start_date" readonly value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}" type="text" class="c-date-p kt_datepicker_3 form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">End Date</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget_end_date" readonly value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}" type="text" class="c-date-p kt_datepicker_3 form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Alert Type</div>
                                            <div class="erp_form__grid_th_input">
                                                <select class="form-control erp-form-control-sm tb_moveIndex" id="budget_alert_type">
                                                    <option value="0">Select</option>
                                                    <option value="stop">Stop</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Exceeded Limit</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget_exceeded_limit" type="text" class="exceeded_limit validNumber validOnlyFloatNumber tb_moveIndex form-control erp-form-control-sm" readonly>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Credit Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget_credit_amount" type="text" class="budget_credit_amount validNumber validOnlyFloatNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Debit Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget_debit_amount" type="text" class="budget_debit_amount validNumber validOnlyFloatNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Practical Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget_practical_amount" type="text" class="practical_amount validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm" readonly>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Achievement</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget_achievement" type="text" class="achievement_amount validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col" width="48">
                                            <div class="erp_form__grid_th_title">Action</div>
                                            <div class="erp_form__grid_th_btn">
                                                <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body" id="repeated_data">
                                    @if(isset($dtls))
                                        @foreach($dtls as $dtl)
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input readonly type="hidden" data-id="account_id" name="pd[{{$loop->iteration}}][account_id]" value="{{$dtl->chart_account_id}}" class="account_id form-control erp-form-control-sm handle">
                                                </td>
                                                <td>
                                                    <select class="form-control erp-form-control-sm tb_moveIndex" data-id="budget_branch_id" name="pd[{{$loop->iteration}}][budget_branch_id]">
                                                        <option vlaue="0">Select</option>
                                                        @foreach($data['branch'] as $branch)
                                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id == $dtl->budget_branch_id?'selected':''}}>{{$branch->branch_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" data-id="account_name" name="pd[{{ $loop->iteration }}][account_name]" value="{{$dtl->accounts->chart_name}}" title="{{$dtl->accounts->chart_name}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_name open_inline__help tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input data-id="budget_budgetart_position" name="pd[{{ $loop->iteration }}][budget_budgetart_position]" type="text" value="{{$dtl->budget_budgetart_position}}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                                @php $start_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->budget_start_date)))); @endphp
                                                <td><input readonly type="text" data-id="budget_start_date" name="pd[{{$loop->iteration}}][budget_start_date]" value="{{($start_date =='01-01-1970')?'':$start_date}}" class="kt_datepicker_3 budget_start_date tb_moveIndex form-control erp-form-control-sm"/></td>
                                                @php $end_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->budget_end_date)))); @endphp
                                                <td><input readonly type="text" data-id="budget_end_date" name="pd[{{$loop->iteration}}][budget_end_date]" value="{{($end_date =='01-01-1970')?'':$end_date}}" class="kt_datepicker_3 tb_moveIndex budget_end_date form-control erp-form-control-sm" /></td>
                                                <td>
                                                    <select class="form-control erp-form-control-sm tb_moveIndex" data-id="budget_alert_type" name="pd[{{$loop->iteration}}][budget_alert_type]">
                                                        <option vlaue="0" {{$dtl->budget_alert_type == 0?'selected':''}}>Select</option>
                                                        <option value="stop" {{$dtl->budget_alert_type == 'stop'?'selected':''}}>Stop</option>
                                                    </select>
                                                </td>
                                                <td><input  data-id="budget_exceeded_limit" readonly name="pd[{{$loop->iteration}}][budget_exceeded_limit]" type="text" value="{{number_format($dtl->budget_exceeded_limit,3)}}"  class="exceeded_limit tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input  data-id="budget_credit_amount" name="pd[{{$loop->iteration}}][budget_credit_amount]" type="text" value="{{number_format($dtl->budget_credit_amount,3)}}"  class="tb_moveIndex budget_credit_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input  data-id="budget_debit_amount" name="pd[{{$loop->iteration}}][budget_debit_amount]" type="text" value="{{number_format($dtl->budget_debit_amount,3)}}"  class="tb_moveIndex budget_debit_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input  data-id="budget_practical_amount" readonly name="pd[{{$loop->iteration}}][budget_practical_amount]" type="text" value="{{number_format($dtl->budget_practical_amount,3)}}" class=" tb_moveIndex practical_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input  data-id="budget_achievement" readonly name="pd[{{$loop->iteration}}][budget_achievement]" type="text" value="{{number_format($dtl->budget_achievement,3)}}" class="tb_moveIndex achievement_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
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
                                    <tr height="30">
                                        <td colspan="8" class="voucher-total-title align-middle">Total :</td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_credit" ></span>
                                            <input id="tot_voucher_credit" name="tot_voucher_credit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_fccredit" ></span>
                                            <input id="tot_voucher_fccredit" name="tot_voucher_fccredit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="offset-md-10 col-lg-2 text-right">
                            <table class="tableTotal" style="width: 100%;">
                                <tbody>
                                <tr>
                                    <td><div class="t_total_label">Total:</div></td>
                                    <td><span class="t_gross_total t_total">0</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="3" name="budget_notes" maxlength="255" class="form-control erp-form-control-sm moveIndex">{{isset($budget_notes)?$budget_notes:''}}</textarea>
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
    <script src="{{ asset('js/pages/js/accounts/budget.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        function AchvAmount(tr){
            var planned_amount = (tr.find(".budget_debit_amount").val() == "")? 0 : tr.find(".budget_debit_amount").val();
            var practical_amount = (tr.find(".practical_amount").val() == "")? 0 : tr.find(".practical_amount").val();
            var planned_amount = (planned_amount== "")? 0 : planned_amount;
            var practical_amount = (practical_amount == "")? 0 : practical_amount;
            var v = (parseFloat(practical_amount) / parseInt(planned_amount))* 100;
            v = v.toFixed(3);
            v=(v == NaN)?0.00:v;
            tr.find(".achievement_amount").val(v).attr('title',v);
            TotalAmt();
        }
        function TotalAmt(){
            var t = 0;
            var v = 0;
            $( "#repeated_data>tr" ).each(function( index ) {
                v = $(this).find('td>.budget_debit_amount').val();
                v= (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
                t += parseFloat(v);
            });
            t = t.toFixed(3);
            $('.t_gross_total').html(t);
        }
        function BudgetCalcFunc(){
            $(".budget_debit_amount").keyup(function(){
                // var tr = $(this).parents('tr');
                // AchvAmount(tr);
                TotalAmt();
            });
            // $(".planned_credit_amount").keyup(function(){
            //     // var tr = $(this).parents('tr');
            //     // AchvAmount(tr);
            //     TotalAmt();
            // });
            // $(".practical_amount").keyup(function(){
            //     var tr = $(this).parents('tr');
            //     // AchvAmount(tr);
            // });
            // $(".exceeded_limit").keyup(function(){
            //     var limit = $(this).parents('tr').find(".exceeded_limit").val();
            //     if(limit > 100){
            //             toastr.error("Please Enter Exceeded Limit Less Then or Equal to 100");
            //             $(this).parents('tr').find(".exceeded_limit").val('');
            //             return false;
            //     }
            // });
            TotalAmt();
        }
        $( document ).ready(function() {
            BudgetCalcFunc();
        });
    </script>
    <script>
        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
    </script>
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'budget_branch_id',
                'fieldClass':'tb_moveIndex',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'account_name',
                'fieldClass':'account_name open_inline__help',
                'message':'Enter Account Detail',
                'require':true,
                'readonly':true,
                'data-url' : accountsHelpUrl
            },
            {
                'id':'budget_budgetart_position',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'budget_start_date',
                'fieldClass':'c-date-p kt_datepicker_3 tb_moveIndex budget_start_date'
            },
            {
                'id':'budget_end_date',
                'fieldClass':'c-date-p kt_datepicker_3 tb_moveIndex budget_end_date'
            },
            {
                'id':'budget_alert_type',
                'fieldClass':'tb_moveIndex',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'budget_exceeded_limit',
                'fieldClass':'exceeded_limit tb_moveIndex validNumber validOnlyFloatNumber',
                'readonly':true,
            },
            {
                'id':'budget_credit_amount',
                'fieldClass':'budget_credit_amount tb_moveIndex validNumber validOnlyFloatNumber',
                'require':true,
            },
            {
                'id':'budget_debit_amount',
                'fieldClass':'budget_debit_amount tb_moveIndex validNumber validOnlyFloatNumber',
                'require':true,
            },
            {
                'id':'budget_practical_amount',
                'fieldClass':'practical_amount tb_moveIndex validNumber validOnlyFloatNumber',
                'readonly':true,
            },
            {
                'id':'budget_achievement',
                'fieldClass':'achievement_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['account_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>

@endsection


