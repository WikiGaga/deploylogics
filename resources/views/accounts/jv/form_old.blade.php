@extends('layouts.template')
@section('title', 'Journal/Opening Balance')

@section('pageCSS')
@endsection
@section('content')
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $voucher_no = $data['voucher_no'];
                $date =  date('d-m-Y');
                $type = $data['type'];
            }
            if($case == 'edit'){
                $type = $data['type'];
                $id = $data['current']->voucher_id;
                $voucher_no= $data['current']->voucher_no;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
                $currency_id = $data['current']->currency_id;
                $exchange_rate = $data['current']->voucher_exchange_rate;
                $notes = $data['current']->voucher_notes;
                $dtls = isset($data['dtl'])? $data['dtl'] :[];
            }
    @endphp
    <!--begin::Form-->
    <form id="voucher_form" class="kt-form" method="post" action="{{ action('Accounts\VoucherController@jvStore', [$type,isset($id)?$id:'']) }}">
    @csrf
    <input type="hidden" name="voucher_no" value="{{$voucher_no}}">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-portlet__body">
                        <div class="form-group-block row">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="erp-page--title">
                                            {{$voucher_no}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Voucher Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" name="voucher_date" class="form-control erp-form-control-sm moveIndex2 c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
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
                                    <label class="col-lg-6 erp-col-form-label">Currency:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm moveIndex2 kt-select2 currency" id="currency_id" name="currency_id">
                                                <option value="">Select</option>
                                                @if($case == 'edit')
                                                    @php $currency_id = isset($currency_id)?$currency_id:''@endphp
                                                    @foreach($data['currency'] as $currency)
                                                    <option value="{{$currency->currency_id}}" {{$currency->currency_id==$currency_id?'selected':''}}>{{$currency->currency_name}}</option>
                                                    @endforeach
                                                @else
                                                    @foreach($data['currency'] as $currency)
                                                    @if($currency->currency_default=='1')
                                                        @php $exchange_rate = $currency->currency_rate; @endphp
                                                    @endif
                                                    <option value="{{$currency->currency_id}}" {{$currency->currency_default=='1'?'selected':''}}>{{$currency->currency_name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Exchange Rate:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:''}}" class="moveIndex2 form-control erp-form-control-sm validNumber">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block" style="overflow: auto;">
                            <table id="AccForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                                <thead>
                                <tr>
                                    <th width="5%">Sr No</th>
                                    <th width="12%">Account Code</th>
                                    <th width="15%">Account Name</th>
                                    <th width="15%">Description</th>
                                    <th width="8%">Budget</th>
                                    <th width="10%">Debit</th>
                                    <th width="10%">Credit</th>
                                    <th width="10%">FC Debit</th>
                                    <th width="10%">FC Credit</th>
                                    <th width="5%">Action</th>
                                </tr>
                                <tr id="dataEntryForm">
                                    <td><input readonly id="voucher_sr_number" type="text" class="form-control erp-form-control-sm">
                                        <input readonly id="account_id" type="hidden" class="acc_id form-control erp-form-control-sm">
                                        <input readonly id="budget_id" type="hidden" class="budget_id form-control erp-form-control-sm">
                                        <input readonly id="budget_branch_id" type="hidden" class="budget_branch_id form-control erp-form-control-sm">
                                    </td>
                                    <td><input  id="account_code" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code open-inline-help masking moveIndex2 validNumber form-control erp-form-control-sm text-left" maxlength="12"></td>
                                    <td><input  id="account_name" type="text" class="acc_name form-control erp-form-control-sm" readonly></td>
                                    <td><input  id="voucher_descrip" type="text" class="moveIndex moveIndex2 form-control erp-form-control-sm"></td>
                                    <td><input  id="budget" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}" class="budget_dscrp open-inline-help masking moveIndex2 form-control erp-form-control-sm"></td>
                                    <td><input  id="voucher_debit" type="text"  class="moveIndex moveIndex2 debit  form-control erp-form-control-sm validNumber"></td>
                                    <td><input  id="voucher_credit" type="text" class="moveIndex  moveIndex2 credit  form-control erp-form-control-sm validNumber "></td>
                                    <td><input  id="voucher_fc_debit" type="text" class="moveIndex moveIndex2 fcdebit form-control erp-form-control-sm validNumber "></td>
                                    <td><input  id="voucher_fc_credit" type="text" class="moveIndex moveIndex2 fccredit form-control erp-form-control-sm validNumber "></td>
                                    <td class="text-center">
                                        <button type="button" id="addData" class="moveIndex2Btn moveIndex2 gridBtn btn btn-primary btn-sm">
                                            <i class="la la-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody id="repeated_data">
                                    @if(isset($dtls))
                                        @foreach($dtls as $data)
                                            @php
                                                $bgt_dsc = '';
                                                $budget =\App\Models\TblAccBudget::where('budget_id',$data->budget_id)->where('budget_branch_id',$data->budget_branch_id)->first();
                                                if($budget != Null){
                                                    $bgt_dsc = $budget->budget_budgetart_position;
                                                }
                                            @endphp
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][voucher_sr_number]" title="{{ $loop->iteration }}"  class=" form-control erp-form-control-sm handle" readonly>
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][account_id]" data-id="account_id" value="{{$data->chart_account_id}}"  class="acc_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][budget_id]" data-id="budget_id" value="{{$data->budget_id}}"  class="budget_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][budget_branch_id]" data-id="budget_branch_id" value="{{$data->budget_branch_id}}"  class="budget_branch_id form-control erp-form-control-sm">
                                                </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][account_code]" data-id="account_code" value="{{$data->accounts->chart_code}}" title="{{$data->accounts->chart_code}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code open-inline-help moveIndex2 validNumber  form-control erp-form-control-sm text-left" maxlength="12" readonly></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][account_name]" data-id="account_name" value="{{$data->accounts->chart_name}}" title="{{$data->accounts->chart_name}}" class="acc_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_descrip]" data-id="voucher_descrip" value="{{$data->voucher_descrip}}" title="{{$data->voucher_descrip}}" class="moveIndex moveIndex2  form-control erp-form-control-sm" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][budget]" data-id="budget" value="{{isset($bgt_dsc)?$bgt_dsc:''}}" title="{{isset($bgt_dsc)?$bgt_dsc:''}}"   data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}" class="budget_dscrp open-inline-help moveIndex2 form-control erp-form-control-sm"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_debit]" data-id="voucher_debit" value="{{number_format($data->voucher_debit,3)}}" title="{{$data->voucher_debit}}" class="moveIndex moveIndex2 debit form-control erp-form-control-sm validNumber text-right" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_credit]" data-id="voucher_credit" value="{{number_format($data->voucher_credit,3)}}" title="{{$data->voucher_credit}}" class="moveIndex moveIndex2 credit form-control erp-form-control-sm validNumber text-right" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_fc_debit]" data-id="voucher_fc_debit" value="{{number_format($data->voucher_fc_debit,3)}}" title="{{$data->voucher_fc_debit}}" class="moveIndex moveIndex2 fcdebit form-control erp-form-control-sm validNumber text-right" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_fc_credit]" data-id="voucher_fc_credit" value="{{number_format($data->voucher_fc_credit,3)}}" title="{{$data->voucher_fc_credit}}" class="moveIndex  moveIndex2 fccredit form-control erp-form-control-sm validNumber text-right" ></td>
                                                <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tbody>
                                    <tr height="30">
                                        <td colspan="2" class="text-right align-middle font-weight-bold">Total Difference :</td>
                                        <td class="text-center align-middle font-weight-bold" >
                                            <span id="tot_difference" ></span>
                                            <input type="hidden" name="tot_jv_difference" id="tot_jv_difference">
                                        </td>
                                        <td colspan="2" class="text-right align-middle font-weight-bold">Total :</td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_debit" ></span>
                                    </td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_credit" ></span>
                                        </td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_fcdebit" ></span>
                                        </td>
                                        <td class="voucher-total-amt align-middle"> 
                                            <span id="tot_fccredit" ></span>
                                        </td>

                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row form-group-block">
                            <label class="col-lg-2 erp-col-form-label">Notes:</label>
                            <div class="col-lg-10">
                                <textarea type="text" rows="2" id="voucher_notes" name="voucher_notes" class="moveIndex  form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
                            </div>
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
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/account-table-calculations.js') }}" type="text/javascript"></script>
    <script>
        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
        var budgetHelpUrl = "{{url('/common/inline-help/budgetHelp')}}";
    </script>
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            
            {
                'id':'account_code',
                'fieldClass':'acc_code open-inline-help masking moveIndex2 validNumber text-left',
                'message':'Enter Account Detail',
                'require':true,
                'readonly':true,
                'data-url' : accountsHelpUrl
            },
            {
                'id':'account_name',
                'fieldClass':'acc_name',
                'message':'Enter Account Name',
                'require':true,
                'readonly':true
            },
            {
                'id':'voucher_descrip',
                'fieldClass':'moveIndex  moveIndex2'
            },
            {
                'id':'budget',
                'fieldClass':'budget_dscrp open-inline-help moveIndex2',
                'data-url' : budgetHelpUrl
            },
            {
                'id':'voucher_debit',
                'fieldClass':'moveIndex moveIndex2 debit validNumber'
            },
            {
                'id':'voucher_credit',
                'fieldClass':'moveIndex moveIndex2 credit validNumber'
            },
            {
                'id':'voucher_fc_debit',
                'fieldClass':'moveIndex moveIndex2 fcdebit validNumber'
            },
            {
                'id':'voucher_fc_credit',
                'fieldClass':'moveIndex  moveIndex2 fccredit validNumber'
            }
        ];
        var arr_hidden_field = ['account_id','budget_id','budget_branch_id'];
    </script>
    <script src="{{ asset('js/pages/js/account-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
@endsection

