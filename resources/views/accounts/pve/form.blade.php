@extends('layouts.layout')
@section('title', 'Expense Voucher')

@section('pageCSS')
    <style>
        #account_code-error{
            display: none !important;
        }

    </style>
@endsection
@section('content')
        @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $voucher_no = $data['voucher_no'];
                $id = "";
                $payment_modes = $data['payment_mode'];
                $date =  date('d-m-Y');
                $is_deduction = 0;
            }
            if($case == 'new' && $data['copy_entry']){
                $voucher_no = $data['voucher_no'];
                $id = "";
                $payment_modes = $data['payment_mode'];
                $date =  date('d-m-Y');
                $dtls = isset($data['dtl'])? $data['dtl'] :[];
            }

            if($case == 'edit'){
                $id = $data['current']->voucher_id;
                $voucher_no= $data['current']->voucher_no;
                $payment_modes = $data['payment_mode'];
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
                $currency_id = $data['current']->currency_id;
                $exchange_rate = $data['current']->voucher_exchange_rate;
                $notes = $data['current']->voucher_notes;
                $is_deduction = $data['current']->is_deduction;
                $dtls = isset($data['dtl'])? $data['dtl'] :[];
                $credits = isset($data['credit'])? $data['credit'] :[];
                $debits = isset($data['credit'])? $data['debit'] :[];
                $deductions = isset($data['credit'])? $data['deduction'] :[];
            }
            $type = $data['type'];
            $form_type = $type;
        @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="voucher_form" class="kt-form" method="post" action="{{ action('Accounts\VoucherController@pveStore', [$type,isset($id)?$id:'']) }}">
    @csrf
    
    @if(session('msg'))
        <script>
            alert('This voucher enter in BRS!');
            document.location='/listing/accounts/{{ $type }}'; 
        </script>
    @endif
    <input type="hidden" name="form_type" id="form_type" value="{{$form_type}}">
    <input type="hidden" name="voucher_no" value="{{$voucher_no}}">
    <input type="hidden" id="voucher_id" value='{{$id}}' >
    @if($case == 'edit')
        <input type="hidden" id="form_id" value='{{$id}}' >
        <input type="hidden" id="menu_id" value="{{$data['stock_menu_id']}}">
    @endif
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                     @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{$voucher_no}}
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    @if($case == 'new')
                                    <div class="row">
                                        <label class="col-lg-6 erp-col-form-label text-right">Last Voucher No.</label>
                                        <div class="col-lg-6">
                                            <div class="erp_form___block">
                                                <div class="input-group open-modal-group">
                                                    <input type="text" id="last_voucher_no" value="{{isset($data['last_voucher_no'])?$data['last_voucher_no']:""}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','oExpVoucherHelp')}}" autocomplete="off" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text btn-open-mob-help" id="getDataByVoucherNo">
                                                           GO
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
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
                                        <input type="text" name="voucher_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
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
                                <label class="col-lg-6 erp-col-form-label">Currency:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm moveIndex kt-select2 currency" id="currency_id" name="currency_id">
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
                                <label class="col-lg-6 erp-col-form-label">Exchange Rate:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="dc_label">Debit Information</div>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table id="voucherDebit" data-prefix="debits" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="5%">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm" data-require="true" data-msg="Account Code is required">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Account Code
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="account_code" type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" data-require="true" data-readonly="true">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Account Name</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="account_name"  type="text" class="acc_name form-control erp-form-control-sm readonly" readonly data-readonly="true" data-require="true">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Narration</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_desc" type="text" class="voucher_desc tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_debit" type="text" class="debit validNumber validOnlyFloatNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col" width="5%">
                                            <div class="erp_form__grid_th_title">Action</div>
                                            <div class="erp_form__grid_th_btn">
                                                <button type="button" class="add_data tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                        @php
                                            $debit_amt = 0;
                                        @endphp
                                        @if(isset($debits) && count($debits) != 0)
                                            @foreach($debits as $data)
                                                @php
                                                    $debit_amt += $data->voucher_debit;
                                                @endphp
                                                <tr>
                                                    <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                        <input type="text" value="{{ $loop->iteration }}" name="debits[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}"  class=" form-control erp-form-control-sm handle" readonly>
                                                        <input readonly type="hidden" name="debits[{{ $loop->iteration }}][account_id]" data-id="account_id" value="{{$data->chart_account_id}}"  class="account_id form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input type="text" class=" acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" name="debits[{{ $loop->iteration }}][account_code]" value="{{$data->accounts->chart_code}}" data-id="account_code" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="acc_name form-control erp-form-control-sm readonly" readonly name="debits[{{ $loop->iteration }}][account_name]" value="{{$data->accounts->chart_name ?? ''}}" title="{{$data->accounts->chart_name}}" data-id="account_name">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="voucher_desc moveIndex2 tb_moveIndex form-control erp-form-control-sm" name="debits[{{ $loop->iteration }}][voucher_desc]" value="{{$data->voucher_descrip}}" data-id="voucher_desc">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="validNumber debit tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm" name="debits[{{ $loop->iteration }}][voucher_debit]" title="{{$data->voucher_debit}}" value="{{number_format($data->voucher_debit,3,'.','')}}" data-id="voucher_debit">
                                                    </td>
                                                    <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button></div></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9"></div>
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-6">Total:</div>
                                    <div class="col-lg-6">
                                        <div class="voucherDebitCalc">
                                            <span id="tot_debit"></span>
                                            <input id="tot_voucher_debit" name="tot_voucher_debit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="dc_label">Credit Information</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label class="kt-checkbox kt-checkbox--danger">
                                        <input type="checkbox" id="is_deduction" name="is_deduction" {{$is_deduction == 1?"checked":""}}> IS Deduction
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block {{$is_deduction == 0?" pointerEventsNone":""}}" id="deduction_block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table id="DeductionTbl"  data-prefix="duc" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="5%">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm" data-require="true" data-msg="Account Code is required">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Account Code
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="account_code" type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" data-require="true" data-readonly="true">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Account Name</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="account_name"  type="text" class="acc_name form-control erp-form-control-sm readonly" readonly data-readonly="true" data-require="true">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Narration</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_narration" type="text" class="voucher_narration tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_credit" type="text" class="deduct_credit validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col" width="5%">
                                            <div class="erp_form__grid_th_title">Action</div>
                                            <div class="erp_form__grid_th_btn">
                                                <button type="button" class="add_data tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body" id="deduction_table">
                                        @php
                                            $deduction_amt = 0;
                                        @endphp
                                        @if(isset($deductions) && count($deductions) != 0)
                                            @foreach($deductions as $duc)
                                                @php
                                                    $deduction_amt += $duc->voucher_credit;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <input value="{{$loop->iteration}}" readonly type="text" class="sr_no form-control erp-form-control-sm" autocomplete="off" name="duc[{{$loop->iteration}}][sr_no]" data-id="sr_no">
                                                        <input readonly value="{{$duc['chart_account_id']}}" type="hidden" class="account_id form-control erp-form-control-sm" autocomplete="off" name="duc[{{$loop->iteration}}][account_id]" data-id="account_id">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" name="duc[{{$loop->iteration}}][account_code]" value="{{$duc['chart_code']}}" data-id="account_code" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="acc_name form-control erp-form-control-sm readonly" readonly name="duc[{{$loop->iteration}}][account_name]" value="{{$duc->accounts->chart_name ?? ''}}" data-id="account_name">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="voucher_narration tb_moveIndex form-control erp-form-control-sm" name="duc[{{$loop->iteration}}][voucher_narration]" value="{{$duc['voucher_descrip']}}" data-id="voucher_narration">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="deduct_credit validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm" name="duc[{{$loop->iteration}}][voucher_credit]" value="{{number_format($duc['voucher_credit'],3,'.','')}}" data-id="voucher_credit">
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button>
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
                    <div class="row">
                        <div class="col-lg-9"></div>
                        <div class="col-lg-3">
                            <div class="row">
                                <div class="col-lg-6">Total:</div>
                                <div class="col-lg-6">
                                    <div class="DeductionTbl">
                                        <span id="tot_deduct_credit" ></span>
                                        <input id="tot_voucher_deduct_credit" name="tot_voucher_deduct_credit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><br>
                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table id="VoucherCreditBlock" data-prefix="pd" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                        <tr>
                                            <th scope="col" width="5%">
                                                <div class="erp_form__grid_th_title">Sr.</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                    <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm" data-require="true" data-msg="Account Code is required">
                                                    <input readonly id="supplier_bank_id" type="hidden" class="supplier_bank_id form-control erp-form-control-sm">
                                                    <input readonly id="supplier_account_id" type="hidden" class="supplier_account_id form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Account Code
                                                </div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="account_code" type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" data-require="true" data-readonly="true">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Account Name</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="account_name"  type="text" class="acc_name form-control erp-form-control-sm readonly" readonly data-readonly="true" data-require="true">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Amount</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="voucher_credit" type="text" class="credit tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Description</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="voucher_descrip" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Payment Mode</div>
                                                <div class="erp_form__grid_th_input">
                                                    <select id="payment_mode" class="payment_mode form-control erp-form-control-sm">
                                                        <option value="">Select</option>
                                                        @foreach($payment_modes as $payment_mode)
                                                            <option value="{{$payment_mode->payment_term_id}}">{{$payment_mode->payment_term_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Cheque No</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input  id="mode_no" type="text"  class="tb_moveIndex form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Cheque Date</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="mode_date" readonly value="" title="" type="text" class="c-date-p kt_datepicker_3 form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Payee Bank</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="supplier_bank" type="text" class="supplier_bank tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accSupplierBankHelp')}}">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Branch Code</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input readonly id="bank_branch_code" type="text" class="bank_branch_code form-control erp-form-control-sm readonly">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Payee AC</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input readonly id="supplier_bank_ac_no" type="text" class="supplier_bank_ac_no form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Payee Title</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="voucher_payee_title" type="text" class="voucher_payee_title tb_moveIndex form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col" width="5%">
                                                <div class="erp_form__grid_th_title">Action</div>
                                                <div class="erp_form__grid_th_btn">
                                                    <button type="button" class="add_data tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                        <i class="la la-plus"></i>
                                                    </button>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                        @php
                                            $credit_amt = 0;
                                        @endphp
                                        @if(isset($credits) && count($credits) != 0)
                                            @foreach($credits as $data)
                                                @php
                                                    $credit_amt += $data->voucher_credit;
                                                    $bank_name = '';
                                                    $bank =\App\Models\TblDefiBank::where('bank_id',$data->bank_id)->first();
                                                    if($bank != null){
                                                        $bank_name = $bank->bank_name;
                                                    }
                                                    $branch_code = '';
                                                    $acc_no = '';
                                                    $sup_acc =\App\Models\TblPurcSupplierAccount::where('supplier_account_id',$data->tbl_supplier_account_id)->first();
                                                    if($sup_acc != null){
                                                        $branch_code = $sup_acc->supplier_iban_no;
                                                        $acc_no = $sup_acc->supplier_account_no;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                        <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][sr_no]" title="{{ $loop->iteration }}"  class=" form-control erp-form-control-sm handle" readonly>
                                                        <input readonly type="hidden" name="pd[{{ $loop->iteration }}][account_id]" data-id="account_id" value="{{$data->chart_account_id}}"  class="account_id form-control erp-form-control-sm">
                                                        <input readonly data-id="supplier_bank_id" value="{{$data->bank_id}}"  type="hidden" name="pd[{{ $loop->iteration }}][supplier_bank_id]" class="supplier_bank_id form-control erp-form-control-sm">
                                                        <input readonly data-id="supplier_account_id" value="{{$data->tbl_supplier_account_id}}"  type="hidden" name="pd[{{ $loop->iteration }}][supplier_account_id]" class="supplier_account_id form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input type="text" class=" acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" name="pd[{{ $loop->iteration }}][account_code]" value="{{$data->accounts->chart_code}}" data-id="account_code" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="acc_name form-control erp-form-control-sm readonly" readonly name="pd[{{ $loop->iteration }}][account_name]" value="{{$data->accounts->chart_name ?? ''}}" title="{{$data->accounts->chart_name}}" data-id="account_name">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="pd[{{ $loop->iteration }}][voucher_credit]" data-id="voucher_credit" value="{{number_format($data->voucher_credit,3,'.','')}}" title="{{$data->voucher_credit}}" class="tb_moveIndex credit form-control erp-form-control-sm validNumber validOnlyFloatNumber" >
                                                    </td>
                                                    <td>
                                                        <input type="text" class="voucher_descrip moveIndex2 tb_moveIndex form-control erp-form-control-sm" name="pd[{{ $loop->iteration }}][voucher_descrip]" value="{{$data->voucher_descrip}}" data-id="voucher_descrip">
                                                    </td>
                                                    <td>
                                                        <select data-id="payment_mode" name="pd[{{ $loop->iteration }}][payment_mode]" class="form-control erp-form-control-sm tb_moveIndex">
                                                            <option value="">Select</option>
                                                            @foreach($payment_modes as $payment)
                                                                <option value="{{$payment->payment_term_id}}" {{$data->voucher_payment_mode == $payment->payment_term_id?"selected":""}}>{{$payment->payment_term_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" data-id="mode_no" name="pd[{{ $loop->iteration }}][mode_no]" value="{{$data->voucher_mode_no}}" title="{{$data->voucher_mode_no}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','chequebookHelp')}}" class="open_inline__help tb_moveIndex form-control erp-form-control-sm">
                                                    </td>
                                                    @php $mode_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data->voucher_mode_date)))); @endphp
                                                    <td>
                                                        <input type="text" data-id="mode_date" name="pd[{{ $loop->iteration }}][mode_date]" value="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" title="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" class="tb_moveIndex form-control erp-form-control-sm kt_datepicker_3" />
                                                    </td>
                                                    <td>
                                                        <input type="text" data-id="supplier_bank" name="pd[{{ $loop->iteration }}][supplier_bank]" value="{{$bank_name}}" class="supplier_bank form-control erp-form-control-sm readonly" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" data-id="bank_branch_code" name="pd[{{ $loop->iteration }}][bank_branch_code]" value="{{$branch_code}}" class="bank_branch_code form-control erp-form-control-sm readonly" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" data-id="payee_ac_no" name="pd[{{ $loop->iteration }}][payee_ac_no]" value="{{$acc_no}}" class="payee_ac_no form-control erp-form-control-sm" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="pd[{{ $loop->iteration }}][voucher_payee_title]" data-id="voucher_payee_title" value="{{$data->voucher_payee_title}}" title="{{$data->voucher_payee_title}}" class="tb_moveIndex voucher_payee_title form-control erp-form-control-sm" >
                                                    </td>
                                                    <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button></div></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-9"></div>
                        <div class="col-lg-3">
                            <div class="row">
                                <div class="col-lg-6">Total:</div>
                                <div class="col-lg-6">
                                    <div class="VoucherCreditBlock">
                                        <span id="vcb_tot_credit" ></span>
                                        <input id="vcb_tot_voucher_credit" name="tot_voucher_credit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row form-group-block">
                                <label class="col-lg-2 erp-col-form-label">Notes:</label>
                                <div class="col-lg-10">
                                    <textarea type="text" rows="2" id="voucher_notes" name="voucher_notes" class="form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
                <!--end::Form-->
    @php session()->forget('pve'); @endphp
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')

    <script src="{{ asset('js/pages/js/add-row-repeated_new.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/voucher.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/account-table-calculations.js') }}" type="text/javascript"></script>
    <script>
        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
        var budgetHelpUrl = "{{url('/common/inline-help/budgetHelp')}}";
        var chequebookHelpUrl = "{{url('/common/help-open/chequebookHelp')}}";
    </script>

    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js?v='.time()) }}" type="text/javascript"></script>
    <script>
        $(document).on('click','#is_deduction',function(){
            if($(this).prop('checked')) {
                $('#deduction_block').removeClass('pointerEventsNone')
            } else {
                $('#deduction_block').addClass('pointerEventsNone')
            }
        });
        function funcAfterAddRow(){
            funcCalcGridAmount()
        }

        function funcCalcGridAmount(){
            var voucherDebitCalc = 0;
            $(document).find('#voucherDebit>tbody.erp_form__grid_body>tr').each(function(){
                var val = $(this).find('.debit').val();
                voucherDebitCalc += funcCalcNumberFloat(val);
            })
            $('.voucherDebitCalc').find('#tot_debit').html(funcNumberFloat(voucherDebitCalc));
            $('.voucherDebitCalc').find('#tot_voucher_debit').val(funcNumberFloat(voucherDebitCalc));

            var DeductionTbl = 0;
            $(document).find('#DeductionTbl>tbody.erp_form__grid_body>tr').each(function(){
                var val = $(this).find('.deduct_credit').val();
                DeductionTbl += funcCalcNumberFloat(val);
            })
            $('.DeductionTbl').find('#tot_deduct_credit').html(funcNumberFloat(DeductionTbl));
            $('.DeductionTbl').find('#tot_voucher_deduct_credit').val(funcNumberFloat(DeductionTbl));

            var VoucherCreditBlock = 0;
            $(document).find('#VoucherCreditBlock>tbody.erp_form__grid_body>tr').each(function(){
                var val = $(this).find('.credit').val();
                VoucherCreditBlock += funcCalcNumberFloat(val);
            })
            var remain_credit = parseFloat(voucherDebitCalc) - parseFloat(DeductionTbl) - parseFloat(VoucherCreditBlock)
            $('#VoucherCreditBlock>thead>tr>th input#voucher_credit').val(funcNumberFloat(remain_credit));

            $('.VoucherCreditBlock').find('#vcb_tot_credit').html(funcNumberFloat(VoucherCreditBlock));
            $('.VoucherCreditBlock').find('#vcb_tot_voucher_credit').val(funcNumberFloat(VoucherCreditBlock));
        }
        $(document).on('keyup blur','.erp_form__grid_body .debit ,.erp_form__grid_body .deduct_credit ,.erp_form__grid_body .credit',function(){
            funcCalcGridAmount();
        })

        function voucher_posted()
        {
            var voucher_id = $('#voucher_id').val();
            var formData = {
                voucher_id : voucher_id,
            }
            var url = '{{action('Accounts\VoucherController@voucherpost')}}';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : url,
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    if(response['status'] == 'success'){
                        toastr.error('Successfully Voucher Posted..!');
                    }
                    if(response['status'] == 'error')
                    {

                    }
                },
            })
        }

    </script>
    @if($case == 'new')
        @include('partial_script.copy_voucher_data')
        <script>
            var date = localStorage.getItem('form_pve_date');
            if(!valueEmpty(date)){
                $('form').find('#kt_datepicker_3').val(date);
            }

            $(document).on('click','#btn-update-entry',function(){
                var thix = $(this);
                var form = thix.parents('form');
                var date = form.find('#kt_datepicker_3').val();
                localStorage.setItem('form_pve_date', date);
            });
        </script>
    @endif
    <script src="{{ asset('js/pages/js/common/add-row-repeated-rsp.js?v='.time()) }}" type="text/javascript"></script>

    <style>
        #DeductionTbl thead tr th:last-child,
        #voucherDebit thead tr th:last-child {
            width: 46px !important;
        }
    </style>


@endsection
