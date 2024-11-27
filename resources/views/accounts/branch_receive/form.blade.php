@extends('layouts.layout')
@section('title', 'Branch Receive Voucher')

@section('pageCSS')
    <style>
        .rsp_table__grid{

        }

        .rsp_table__grid>.rsp_table__grid_header{

        }
        .rsp_table__grid>.rsp_table__grid_header>tr>th .rsp_table__grid_th_title{
            padding: 3px;
            border-bottom: 2px solid #d5d5d5 !important;
            text-align: center;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .rsp_table__grid>.rsp_table__grid_header>tr>th .rsp_table__grid_th_input{
            border-bottom: 2px solid #d5d5d5 !important;
            text-align: center;
            border-top: 2px solid #b1b1b1;
        }
        .rsp_table__grid>.rsp_table__grid_header>tr>th:first-child .rsp_table__grid_th_input input{
            background: #e6e6e6 !important;
        }
        .rsp_table__grid>.rsp_table__grid_header>tr>th .rsp_table__grid_th_input input.rsp_readonly{
            background: #e6e6e6 !important;
        }
        .rsp_table__grid>.rsp_table__grid_header>tr>th .rsp_table__grid_th_input input{
            border:0;
        }
        .rsp_table__grid>.rsp_table__grid_header>tr>th .rsp_table__grid_th_btn{
            border-bottom: 2px solid #d5d5d5 !important;
            text-align: center;
            border-top: 2px solid #b1b1b1;
        }

        .rsp_table__grid>.rsp_table__grid_body{

        }

        .rsp_table__grid>.rsp_table__grid_body>tr>td{

        }

        .rsp_table__grid>.rsp_table__grid_body>tr>td input{

        }

        .rsp_table__grid>.rsp_table__grid_footer{

        }

        .rsp_table__grid>.rsp_table__grid_footer>tr>td{

        }

        .rsp_table__grid>.rsp_table__grid_footer>tr>td input{

        }
        .rsp_table__grid .rsp_table__grid_newBtn {
            padding: 4.25px 0 4.25px 5px !important;
            border-radius: 0 !important;
            margin: 0.2px 0;
        }
    </style>
@endsection
@section('content')
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    $type = $data['type'];
    if($case == 'new'){
        $voucher_no = $data['voucher_no'];
        $date =  date('d-m-Y');
        $voucher_debit_amount = 0;
        $bill_total_amount = 0;
        $ledger_bal = 0;
        $is_deduction = 0;
        $on_account_voucher = 0;
        $voucher_bills = [];
    }
    if($case == 'edit'){
        $id = $data['current']->voucher_id;
        $voucher_no = $data['current']->voucher_no;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
        $currency_id = $data['current']->currency_id;
        $exchange_rate = $data['current']->voucher_exchange_rate;
        $up_chart_account_id = $data['current']->chart_account_id;
        $up_chart_account_name = $data['current']->accounts->chart_name;
        $up_chart_account_code = $data['current']->accounts->chart_code;
        $narration = $data['current']->voucher_descrip;
        $payment_mode = $data['current']->voucher_payment_mode;
        $mode = $data['current']->voucher_mode_no;
        $is_deduction = $data['current']->is_deduction;
        $on_account_voucher = $data['current']->on_account_voucher;
        $voucher_debit_amount = \App\Library\Utilities::NumFormat($data['current']->voucher_debit);

        $bill_total_amount = 0;
        $ledger_bal = 0;
        $notes = $data['current']->voucher_notes;
        $dtls = isset($data['dtl'])? $data['dtl'] :[];
        $voucher_bills = (isset($data['current']->voucher_bill) && count($data['current']->voucher_bill) != 0)? $data['current']->voucher_bill :[];
    }
    $payment_modes = $data['payment_mode'];
@endphp
@permission($data['permission'])
<!--begin::Form-->
<form id="voucher_form" class="kt-form" method="post" action="{{action('Accounts\VoucherController@brrvstore', [$type,isset($id)?$id:''])}}">
    @csrf
    
    @if(session('msg'))
        <script>
            alert('This voucher enter in BRS!');
            document.location='/listing/accounts/{{ $type }}'; 
        </script>
    @endif
    <input type="hidden" value='{{$type}}' id="form_type">
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
                                    {{$voucher_no}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-5 erp-col-form-label">Voucher Date:</label>
                            <div class="col-lg-7">
                                <div class="input-group date">
                                    <input type="text" name="voucher_date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}"  id="kt_datepicker_3" autofocus/>
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
                            <label class="col-lg-4 erp-col-form-label">Currency:<span class="required">*</span></label>
                            <div class="col-lg-8">
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
                        <div class="dc_label">Credit Information</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group-block">
                            <div class="erp_form___block">
                                <div class="table-scroll form_input__block">
                                    <table id="grnVoucherDtls" data-prefix="inv" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                        <thead class="erp_form__grid_header">
                                        <tr>
                                            <th scope="col" width="35px">
                                                <div class="erp_form__grid_th_title">Sr.</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Branch</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Invoice No.</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Invoice Date</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Current Amount</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Current Balance</div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Select
                                                </div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="erp_form__grid_body">
                                        @if($case == 'edit')
                                            @foreach($voucher_bills as $bill)
                                                @php
                                                    $bi = $loop->iteration;
                                                    $bra = \App\Models\TblSoftBranch::where('branch_id',$bill->paymnet_branch_id)->first();
                                                    // dd($bra->toArray());
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <input readonly type="text" value="{{$bi}}" name="inv[{{$bi}}][sr_no]" data-id="sr_no" class="form-control erp-form-control-sm">
                                                        <input readonly type="hidden" value="{{$bill->paymnet_branch_id}}" name="inv[{{$bi}}][branch_id]" data-id="branch_id" class="form-control erp-form-control-sm">
                                                        <input readonly type="hidden" value="{{$bill->voucher_document_id}}" name="inv[{{$bi}}][document_id]" data-id="document_id" class="document_id form-control erp-form-control-sm">
                                                        <input readonly type="hidden" value="{{$bill->chart_account_id}}" name="inv[{{$bi}}][chart_id]" data-id="chart_id" class="chart_id form-control erp-form-control-sm">

                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="{{$bra->branch_name}}" name="inv[{{$bi}}][branch_name]" data-id="branch_name" class="form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="{{$bill->voucher_document_code}}" name="inv[{{$bi}}][document_code]" data-id="document_code" class="document_code form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        @php $voucher_document_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$bill->voucher_document_date)))); @endphp
                                                        <input readonly type="text" value="{{$voucher_document_date}}" name="inv[{{$bi}}][document_date]" data-id="document_date" class="document_date form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="{{\App\Library\Utilities::NumFormat($bill->voucher_bill_bal_amount)}}" name="inv[{{$bi}}][current_amount]" data-id="current_amount" class="current_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="{{\App\Library\Utilities::NumFormat($bill->voucher_bill_net_bal_amount)}}" name="inv[{{$bi}}][current_balance]" data-id="current_balance" class="current_balance validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $sts = ($bill->voucher_bill_bal_amount == 0)?'checked':'';
                                                        @endphp
                                                        <input type="checkbox" name="inv[{{$bi}}][voucher_bill_grn_paid_status]" data-id="checkbox" class="checkedDocInv" {{ $sts }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            {{--new entry create--}}
                                            @foreach($data['branch_payment_list'] as $bill)
                                                @php
                                                    $bi = $loop->iteration;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <input readonly type="text" value="{{$bi}}" name="inv[{{$bi}}][sr_no]" data-id="sr_no" class="form-control erp-form-control-sm">
                                                        <input readonly type="hidden" value="{{$bill->branch_id}}" name="inv[{{$bi}}][branch_id]" data-id="branch_id" class="branch_id form-control erp-form-control-sm">
                                                        <input readonly type="hidden" value="{{$bill->voucher_id}}" name="inv[{{$bi}}][document_id]" data-id="document_id" class="document_id form-control erp-form-control-sm">
                                                        <input readonly type="hidden" value="{{$bill->chart_account_id}}" name="inv[{{$bi}}][chart_id]" data-id="chart_id" class="chart_id form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="{{$bill->branch_name}}" name="inv[{{$bi}}][branch_name]" data-id="branch_name" class="form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="{{$bill->voucher_no}}" name="inv[{{$bi}}][document_code]" data-id="document_code" class="document_code form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        @php $voucher_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$bill->voucher_date)))); @endphp
                                                        <input readonly type="text" value="{{$voucher_date}}" name="inv[{{$bi}}][document_date]" data-id="document_date" class="document_date form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="{{\App\Library\Utilities::NumFormat($bill->balance_amount)}}" name="inv[{{$bi}}][current_amount]" data-id="current_amount" class="current_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </td>
                                                    <td>
                                                        <input readonly type="text" value="0" name="inv[{{$bi}}][current_balance]" data-id="current_balance" class="current_balance validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" data-id="checkbox" class="checkedDocInv">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tbody class="erp_form__grid_footer">
                                        <tr style="border-top: 2px solid #007df6;border-bottom: 2px solid #007df6;">
                                            <td></td>
                                            <td style="vertical-align: middle;"><b>Total Amount</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="background: unset !important;">
                                                <input type="text" value="{{$voucher_debit_amount}}" id="total_current_amount" name="total_current_amount" class="total_current_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm"  style="background: unset !important;">
                                            </td>
                                            <td class="text-center"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="dc_label">Debit Information</div>
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
                                    $headings = ['Sr No','Account Code','Account Name','Narration',
                                                  'Payment Mode','Mode No','Mode Date','Amount','FC Amount'];
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
                            <table id="AccForm" data-prefix="pd" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
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
                                        <div class="erp_form__grid_th_title">
                                            Account Code
                                            <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                <i class="la la-barcode"></i>
                                            </button>
                                        </div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="account_code" type="text" class="acc_code  tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Account Name</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="account_name" readonly type="text" class="acc_name form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Narration</div>
                                        <div class="erp_form__grid_th_input">
                                            <input  id="voucher_descrip" type="text" class="tb_moveIndex form-control erp-form-control-sm">
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
                                        <div class="erp_form__grid_th_title">Mode No</div>
                                        <div class="erp_form__grid_th_input">
                                            <input  id="mode_no" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','chequebookHelp')}}" class="tb_moveIndex open_inline__help form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Mode Date</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="mode_date" readonly value="" title="" type="text" class="c-date-p kt_datepicker_3 form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Bank</div>
                                        <div class="erp_form__grid_th_input">
                                            <select id="bank" class="bank form-control erp-form-control-sm">
                                                <option value="">Select</option>
                                                @foreach($data['bank'] as $bank)
                                                    <option value="{{$bank->bank_id}}" data-code="{{$bank->bank_branch_code}}">{{$bank->bank_name}} - {{$bank->bank_branch_name}}</option>
                                                @endforeach
                                            </select>
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
                                            <input id="payee_ac_no" type="text" class="payee_ac_no form-control erp-form-control-sm tb_moveIndex">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="voucher_credit" type="text" class="credit validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">FC Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="voucher_fc_credit" type="text" class="fccredit validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm">
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
                                <tbody class="erp_form__grid_body">
                                @if(isset($dtls))
                                    @foreach($dtls as $row)
                                        @php
                                            $bgt_dsc = '';
                                            $budget =\App\Models\TblAccBudget::where('budget_id',$row->budget_id)->where('budget_branch_id',$row->budget_branch_id)->first();
                                            if($budget != Null){
                                                $bgt_dsc = $budget->budget_budgetart_position;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                <input readonly type="hidden" name="pd[{{ $loop->iteration }}][account_id]" data-id="account_id" value="{{$row->chart_account_id}}"  class="account_id form-control erp-form-control-sm">
                                            </td>
                                            <td><input type="text" data-id="account_code" name="pd[{{ $loop->iteration }}][account_code]" value="{{$row->accounts->chart_code ?? ''}}" title="{{$row->accounts->chart_code ?? ''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code open_inline__help tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="account_name" name="pd[{{ $loop->iteration }}][account_name]" value="{{$row->accounts->chart_name ?? ''}}" title="{{$row->accounts->chart_name ?? ''}}" class="acc_name form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="voucher_descrip" name="pd[{{ $loop->iteration }}][voucher_descrip]" value="{{$row->voucher_descrip}}" title="{{$row->voucher_descrip}}" class="tb_moveIndex form-control erp-form-control-sm" ></td>
                                            <td>
                                                <select data-id="payment_mode" name="pd[{{ $loop->iteration }}][payment_mode]" class="form-control erp-form-control-sm tb_moveIndex">
                                                    <option value="">Select</option>
                                                    @foreach($payment_modes as $payment)
                                                        <option value="{{$payment->payment_term_id}}" {{$row->voucher_payment_mode == $payment->payment_term_id?"selected":""}}>{{$payment->payment_term_name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="mode_no" name="pd[{{ $loop->iteration }}][mode_no]" value="{{$row->voucher_mode_no}}" title="{{$row->voucher_mode_no}}" class="open_inline__help tb_moveIndex form-control erp-form-control-sm"></td>
                                            @php $mode_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$row->voucher_mode_date)))); @endphp
                                            <td><input type="text" data-id="mode_date" name="pd[{{ $loop->iteration }}][mode_date]" value="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" title="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" class="tb_moveIndex form-control erp-form-control-sm kt_datepicker_3" /></td>
                                            @php $credit = $row->voucher_debit; $fc_credit = $row->voucher_fc_debit; @endphp
                                            <td>
                                                <select data-id="bank" name="pd[{{ $loop->iteration }}][bank]" class="bank form-control erp-form-control-sm">
                                                    <option value="">Select</option>
                                                    @foreach($data['bank'] as $bank)
                                                        <option value="{{$bank->bank_id}}" data-code="{{$bank->bank_branch_code}}" {{$row->bank_id == $bank->bank_id?"selected":""}}>{{$bank->bank_name}} - {{$bank->bank_branch_name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="bank_branch_code" name="pd[{{ $loop->iteration }}][bank_branch_code]" value="{{$row->bank_branch_code}}" class="bank_branch_code form-control erp-form-control-sm readonly" readonly></td>
                                            <td><input type="text" data-id="payee_ac_no" name="pd[{{ $loop->iteration }}][payee_ac_no]" value="{{$row->payee_ac_no}}" class="payee_ac_no tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" data-id="voucher_credit" name="pd[{{ $loop->iteration }}][voucher_credit]" value="{{\App\Library\Utilities::NumFormat($credit)}}" title="{{$credit}}" class="tb_moveIndex credit form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" data-id="voucher_fc_credit" name="pd[{{ $loop->iteration }}][voucher_fc_credit]" value="{{\App\Library\Utilities::NumFormat($fc_credit)}}" title="{{$fc_credit}}" class="tb_moveIndex fccredit form-control erp-form-control-sm validNumber" ></td>
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
                                    <td class="voucher-total-title align-middle">Total:</td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
                                    <td class="voucher-total-title align-middle"></td>
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
                <div class="form-group-block row">
                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="2" id="voucher_notes" name="voucher_notes" class="form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
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

    <script src="{{ asset('js/pages/js/voucher.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/account-table-calculations.js') }}" type="text/javascript"></script>
    <script>
        var var_form_name = 'branch_voucher';
    </script>
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'account_code',
                'fieldClass':'acc_code open_inline__help',
                'message':'Enter Account Detail',
                'require':true,
                'readonly':true,
                /*'data-url' : accountsHelpUrl*/
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
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'payment_mode',
                'fieldClass':'payment_mode',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'mode_no',
                'fieldClass':'open_inline__help tb_moveIndex',
            },
            {
                'id':'mode_date',
                'fieldClass':'c-date-p kt_datepicker_3 tb_moveIndex'
            },
            {
                'id':'bank',
                'fieldClass':'bank',
                'type':'select'
            },
            {
                'id':'bank_branch_code',
                'fieldClass':'bank_branch_code',
                'readonly':true,
            },
            {
                'id':'payee_ac_no',
                'fieldClass':'payee_ac_no tb_moveIndex',
            },
            {
                'id':'voucher_credit',
                'fieldClass':'tb_moveIndex credit validNumber validOnlyFloatNumber'
            },
            {
                'id':'voucher_fc_credit',
                'fieldClass':'tb_moveIndex fccredit validNumber validOnlyFloatNumber'
            }
        ];
        var arr_hidden_field = ['account_id'];

        var form_type = $('#form_type').val();

    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        $(document).on('click','.checkedDocInv',function(){
            var thix = $(this);
            var tr = $(this).parents('tr');
            // debugger
            if($(this).prop('checked')) {
                var balance_amount = tr.find('.current_amount').val();
                var current_balance = !valueEmpty(balance_amount)?(parseFloat(balance_amount).toFixed(3)):0;
                tr.find('.current_amount').val(0);
                tr.find('.current_balance').val(current_balance);
            } else {
                var balance_amount = tr.find('.current_balance').val();
                var current_amount = !valueEmpty(balance_amount)?(parseFloat(balance_amount).toFixed(3)):0;
                tr.find('.current_amount').val(current_amount);
                tr.find('.current_balance').val(0);
            }

            funcSumGrnInvs();
        });
        function funcSumGrnInvs(){
            var tbody = $('#grnVoucherDtls').find('tbody.erp_form__grid_body');
            var total_current_amount = 0;
            tbody.find('tr').each(function(){
                var balance_amount = $(this).find('.current_balance').val();
                if(!valueEmpty(balance_amount)){
                    total_current_amount += parseFloat(balance_amount);
                }
            })
            var tbody_footer = $('#grnVoucherDtls').find('tbody.erp_form__grid_footer');
            tbody_footer.find('.total_current_amount').val(parseFloat(total_current_amount).toFixed(3));
            $('#AccForm').find('#voucher_credit').val(parseFloat(total_current_amount).toFixed(3));
        }
        $(document).on('change','.bank',function(){
            var thix = $(this);
            var tr = thix.parents('tr');
            var bank_branch_code = thix.find('option:selected').attr('data-code');
            tr.find('.bank_branch_code').val(bank_branch_code);
        });
    </script>
@endsection

