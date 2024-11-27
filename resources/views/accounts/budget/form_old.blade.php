@extends('layouts.template')
@section('title', 'Budget')

@section('pageCSS')
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
    @endphp
    @permission($data['permission']);
    <!--begin::Form-->
    <form id="budget_form" class="master_form kt-form" method="post" action="{{ action('Accounts\BudgetController@store',isset($id)?$id:'') }}">
     @csrf
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
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
                        <div class="form-group-block" style="overflow: auto;">
                            <table id="budgetForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                                <thead>
                                    <tr>
                                        <th width="4%">Sr No</th>
                                        <th width="11%">Branch</th>
                                        <th width="14%">Description</th>
                                        <th width="14%">Analytic Account</th>
                                        <th width="8%">Start Date</th>
                                        <th width="8%">End Date</th>
                                        <th width="11%">Alert Type</th>
                                        <th width="10%">Planned Amount</th>
                                        <th width="10%">Practical Amount</th>
                                        <th width="10%">Achievement</th>
                                        <th width="4%">Action</th>
                                    </tr>
                                    <tr id="dataEntryForm">
                                        <td><input readonly id="sr_no" type="text" class="form-control erp-form-control-sm">
                                            <input type="hidden" id="account_id" name="account_id" class="acc_id form-control erp-form-control-sm">
                                        </td>
                                        <td><select class="form-control erp-form-control-sm moveIndex" id="budget_branch_id">
                                                <option vlaue="0">Select</option> 
                                                @foreach($data['branch'] as $branch)
                                                <option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
                                                @endforeach
                                            </select></td>
                                        <td><input  id="budget_budgetart_position" type="text" class="moveIndex form-control erp-form-control-sm"></td>
                                        <td><input  id="account_name" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}"  class="acc_name open-inline-help moveIndex form-control erp-form-control-sm text-left"></td>
                                        <td><input  type="text" id="budget_start_date" class="form-control form-control-sm kt_datepicker_3 moveIndex" readonly value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}" /></td>
                                        <td><input  type="text" id="budget_end_date" class="form-control form-control-sm kt_datepicker_3 moveIndex" readonly value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}" /></td>
                                        <td><select class="form-control erp-form-control-sm moveIndex" id="budget_alert_type">
                                                <option vlaue="0">Select</option> 
                                                <option value="stop">Stop</option>
                                            </select></td>
                                        <td><input  id="budget_planned_amount" type="text"  class="moveIndex planned_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                        <td><input  id="budget_practical_amount" type="text" class=" moveIndex practical_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                        <td><input  id="budget_achievement" type="text" class="achievement_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" rreadonly></td>
                                        <td class="text-center">
                                            <button type="button" id="addData" class="moveIndexBtn moveIndex gridBtn btn btn-primary btn-sm">
                                                <i class="la la-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody id="repeated_data">
                                    @if(isset($dtls))
                                        @foreach($dtls as $dtl)
                                    
                                            <tr>
                                                <td><input readonly name="pd[{{$loop->iteration}}][sr_no]" title="{{$loop->iteration}}" value="{{$loop->iteration}}" type="text" class="form-control erp-form-control-sm">
                                                    
                                                    <input readonly type="hidden" data-id="account_id" name="pd[{{$loop->iteration}}][account_id]" value="{{$dtl->chart_account_id}}" class="acc_id form-control erp-form-control-sm handle">
                                                </td>
                                                <td><select class="form-control erp-form-control-sm moveIndex" data-id="budget_branch_id" name="pd[{{$loop->iteration}}][budget_branch_id]">
                                                        <option vlaue="0">Select</option> 
                                                        @foreach($data['branch'] as $branch)
                                                        <option value="{{$branch->branch_id}}" {{$branch->branch_id == $dtl->budget_branch_id?'selected':''}}>{{$branch->branch_name}}</option>
                                                        @endforeach
                                                    </select></td>
                                                <td><input data-id="budget_budgetart_position" name="pd[{{ $loop->iteration }}][budget_budgetart_position]" type="text" value="{{$dtl->budget_budgetart_position}}" class="moveIndex moveIndex2 form-control erp-form-control-sm"></td>
                                                <td><input data-id="account_name" name="pd[{{ $loop->iteration }}][account_name]" value="{{$dtl->accounts->chart_name}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}"  class="acc_name open-inline-help moveIndex2 form-control erp-form-control-sm text-left"></td>
                                                @php $start_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->budget_start_date)))); @endphp
                                                <td><input readonly type="text" data-id="budget_start_date" name="pd[{{$loop->iteration}}][budget_start_date]" value="{{($start_date =='01-01-1970')?'':$start_date}}" class="kt_datepicker_3 moveIndex moveIndex2 form-control form-control-sm"/></td>
                                                @php $end_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->budget_end_date)))); @endphp
                                                <td><input readonly type="text" data-id="budget_end_date" name="pd[{{$loop->iteration}}][budget_end_date]" value="{{($end_date =='01-01-1970')?'':$end_date}}" class="kt_datepicker_3 moveIndex form-control form-control-sm" /></td>
                                                <td><select class="form-control erp-form-control-sm moveIndex" data-id="budget_alert_type" name="pd[{{$loop->iteration}}][budget_alert_type]">
                                                        <option vlaue="0" {{$dtl->budget_alert_type == 0?'selected':''}}>Select</option> 
                                                        <option value="stop" {{$dtl->budget_alert_type == 'stop'?'selected':''}}>Stop</option>
                                                    </select></td>
                                                <td><input  data-id="budget_planned_amount" name="pd[{{$loop->iteration}}][budget_planned_amount]" type="text" value="{{number_format($dtl->budget_planned_amount,3)}}"  class="moveIndex planned_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input  data-id="budget_practical_amount" name="pd[{{$loop->iteration}}][budget_practical_amount]" type="text" value="{{number_format($dtl->budget_practical_amount,3)}}" class=" moveIndex practical_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input  data-id="budget_achievement" name="pd[{{$loop->iteration}}][budget_achievement]" type="text" value="{{number_format($dtl->budget_achievement,3)}}" class="moveIndex achievement_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
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
        </div>
    </form>
                <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        function AchvAmount(tr){
            var planned_amount = (tr.find(".planned_amount").val() == "")? 0 : tr.find(".planned_amount").val();
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
                v = $(this).find('td>.achievement_amount').val();
                v= (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
                t += parseFloat(v);
            });
            t = t.toFixed(3);
            $('.t_gross_total').html(t);
        }
        function BudgetCalcFunc(){
            $(".planned_amount").keyup(function(){
                var tr = $(this).parents('tr');
                AchvAmount(tr);
            });
            $(".practical_amount").keyup(function(){
                var tr = $(this).parents('tr');
                AchvAmount(tr);
            });
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
                'fieldClass':'moveIndex',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'budget_budgetart_position',
                'fieldClass':'moveIndex moveIndex2'
            },
            {
                'id':'account_name',
                'fieldClass':'acc_name open-inline-help moveIndex2 text-left',
                'message':'Enter Account Name',
                'require':true,
                'data-url' : accountsHelpUrl
            },
            {
                'id':'budget_start_date',
                'fieldClass':'kt_datepicker_3 moveIndex moveIndex2'
            },
            {
                'id':'budget_end_date',
                'fieldClass':'kt_datepicker_3 moveIndex'
            },
            {
                'id':'budget_alert_type',
                'fieldClass':'moveIndex',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'budget_planned_amount',
                'fieldClass':'planned_amount moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'budget_practical_amount',
                'fieldClass':'practical_amount moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'budget_achievement',
                'fieldClass':'achievement_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['account_id'];
    </script>
    <script src="{{ asset('js/pages/js/account-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    
@endsection


