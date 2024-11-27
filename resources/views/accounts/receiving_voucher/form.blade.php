@extends('layouts.layout')
@section('title', 'Received Voucher')

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
            $id = "";
            $date =  date('d-m-Y');
            $voucher_credit_amount = 0;
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
            $up_chart_account_id = isset($data['current']->chart_account_id)?$data['current']->chart_account_id:"";
            $up_chart_account_name = isset($data['current']->accounts->chart_name)?$data['current']->accounts->chart_name:"";
            $up_chart_account_code = isset($data['current']->accounts->chart_code)?$data['current']->accounts->chart_code:"";
            $narration = $data['current']->voucher_descrip;
            $payment_mode = $data['current']->voucher_payment_mode;
            $mode = $data['current']->voucher_mode_no;
            $is_deduction = $data['current']->is_deduction;
            $on_account_voucher = $data['current']->on_account_voucher;
            $voucher_credit_amount = number_format($data['current']->voucher_credit,3);

            $bill_total_amount = 0;
            $ledger_bal = 0;
            $notes = $data['current']->voucher_notes;
            $dtls = isset($data['dtl'])? $data['dtl'] :[];
            $voucher_bills = (isset($data['current']->voucher_bill) && count($data['current']->voucher_bill) != 0)? $data['current']->voucher_bill :[];

        }
        $form_type = $type;
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    <form id="voucher_form" class="kt-form" method="post" action="{{action('Accounts\VoucherController@receivedVoucherStore', [$type,isset($id)?$id:''])}}">
        @csrf
        
    @if(session('msg'))
        <script>
            alert('This voucher enter in BRS!');
            document.location='/lissting/accounts/{{ $type }}'; 
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
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-3 erp-col-form-label">Account: <span class="required">*</span></label>
                                <div class="col-lg-9">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                 <i class="la la-minus-circle"></i>
                                            </span>
                                            </div>
                                            <input type="text" id="up_chart_account_code" name="up_chart_account_code" value="{{isset($up_chart_account_code)?$up_chart_account_code:""}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','upAccountsHelp')}}" autocomplete="off" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                            <input type="text" id="up_chart_account_name" name="up_chart_account_name" value="{{isset($up_chart_account_name)?$up_chart_account_name:""}}" autocomplete="off" class="form-control erp-form-control-sm moveIndex" readonly style="background: #f9f9f9;">
                                            <input type="hidden" id="up_chart_account_id" name="up_chart_account_id" value="{{isset($up_chart_account_id)?$up_chart_account_id:""}}"/>
                                            <div class="input-group-append">
                                            <span class="input-group-text btn-open-mob-help" id="getDatafromGrnBill">
                                               GO
                                            </span>
                                                <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                               <i class="la la-search"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="row">
                                <label class="col-lg-5 erp-col-form-label">Ledger Bal:</label>
                                <div class="col-lg-7">
                                    <input type="text" id="ledger_bal" name="ledger_bal" value="{{$ledger_bal}}" autocomplete="off" class="validNumber validOnlyFloatNumber form-control erp-form-control-sm moveIndex" readonly style="background: #f9f9f9;">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="row">
                                <label class="col-lg-5 erp-col-form-label">Amount:</label>
                                <div class="col-lg-7">
                                    <input type="text" id="bill_total_amount" name="bill_total_amount" value="{{$voucher_credit_amount}}" autocomplete="off" class="validNumber validOnlyFloatNumber form-control erp-form-control-sm {{$on_account_voucher == 1?"":"readonly"}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label class="kt-checkbox kt-checkbox--danger">
                                        <input type="checkbox" id="on_account_voucher" name="on_account_voucher" {{$on_account_voucher == 1?"checked":""}}> On Account
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Narration:</label>
                                <div class="col-lg-8">
                                    <input type="text" id="narration" name="narration" value="{{isset($narration)?$narration:''}}" class="moveIndex form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group-block">
                                <div class="erp_form___block">
                                    <div class="table-scroll form_input__block">
                                        <table id="grnVoucherDtls" data-prefix="inv" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline {{$on_account_voucher == 1?" pointerEventsNone":""}}">
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
                                                    <div class="erp_form__grid_th_title">Remarks</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Bill No.</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Invoice Date</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Due Date</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Invoice Amount</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Paid Amount</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Inv. Balance</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Current Amt.</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Current Bal.</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">
                                                        Select All
                                                        <input type="checkbox" class="selectAllGrnInvAmt">
                                                    </div>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody class="erp_form__grid_body">
                                            @if($on_account_voucher == 0)
                                                @foreach($voucher_bills as $bill)
                                                    @php
                                                        $bi = $loop->iteration;
                                                        $bra = \App\Models\TblSoftBranch::where('branch_id',$bill->branch_id)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <input readonly type="text" value="{{$bi}}" name="inv[{{$bi}}][sr_no]" data-id="sr_no" class="form-control erp-form-control-sm">
                                                            <input readonly type="hidden" value="{{$bill->branch_id}}" name="inv[{{$bi}}][branch_id]" data-id="branch_id" class="form-control erp-form-control-sm">
                                                            <input readonly type="hidden" value="{{$bill->voucher_document_id}}" name="inv[{{$bi}}][grn_id]" data-id="grn_id" class="form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{$bra->branch_name}}" name="inv[{{$bi}}][branch_name]" data-id="branch_name" class="form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{$bill->voucher_document_code}}" name="inv[{{$bi}}][grn_code]" data-id="grn_code" class="form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{$bill->voucher_grn_remarks}}" name="inv[{{$bi}}][grn_remarks]" data-id="grn_remarks" class="form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{$bill->voucher_document_ref}}" name="inv[{{$bi}}][grn_bill_no]" data-id="grn_bill_no" class="form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            @php $voucher_document_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$bill->voucher_document_date)))); @endphp
                                                            <input readonly type="text" value="{{$voucher_document_date}}" name="inv[{{$bi}}][grn_date]" data-id="grn_date" class="form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="" name="inv[{{$bi}}][due_date]" data-id="due_date" class="form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{number_format($bill->voucher_bill_amount,3)}}" name="inv[{{$bi}}][grn_total_net_amount]" data-id="grn_total_net_amount" class="grn_total_net_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{number_format($bill->voucher_bill_paid_amount,3)}}" name="inv[{{$bi}}][paid_amount]" data-id="paid_amount" class="paid_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{number_format($bill->voucher_bill_bal_amount,3)}}" name="inv[{{$bi}}][balance_amount]" data-id="balance_amount" class="balance_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input type="text" value="{{number_format($bill->voucher_bill_rec_amount,3)}}" name="inv[{{$bi}}][current_amount]" data-id="current_amount" class="current_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                        </td>
                                                        <td>
                                                            <input readonly type="text" value="{{number_format($bill->voucher_bill_net_bal_amount,3)}}" name="inv[{{$bi}}][current_balance]" data-id="current_balance" class="current_balance validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $sts = ($bill->voucher_bill_net_bal_amount == 0)?'checked':'';
                                                            @endphp
                                                            <input type="checkbox" name="inv[{{$bi}}][voucher_bill_grn_paid_status]"  data-id="voucher_bill_grn_paid_status" class="checkedGrnInv" {{$sts}}>
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
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <input type="text" readonly value="{{$voucher_credit_amount}}" id="total_current_amount" name="total_current_amount" class="total_current_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                </td>
                                                <td></td>
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
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm text-center">
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
                                                <input id="amount" type="text" class="amount validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
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
                                    @if(isset($data['deduction']) && count($data['deduction']) != 0)
                                        @foreach($data['deduction'] as $duc)
                                            <tr>
                                                <td>
                                                    <input value="{{$loop->iteration}}" readonly type="text" class="sr_no form-control erp-form-control-sm text-center" autocomplete="off" name="duc[{{$loop->iteration}}][sr_no]" data-id="sr_no">
                                                    <input readonly value="{{$duc['chart_account_id']}}" type="hidden" class="account_id form-control erp-form-control-sm" autocomplete="off" name="duc[{{$loop->iteration}}][account_id]" data-id="account_id">
                                                </td>
                                                <td>
                                                    <input type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" name="duc[{{$loop->iteration}}][account_code]" value="{{$duc['chart_code']}}" data-id="account_code" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="acc_name form-control erp-form-control-sm readonly" readonly name="duc[{{$loop->iteration}}][account_name]" value="{{$duc->accounts->chart_name ?? ''}}" data-id="account_name">
                                                </td>
                                                <td>
                                                    <input type="text" class="voucher_narration tb_moveIndex form-control erp-form-control-sm" name="duc[{{$loop->iteration}}][voucher_descrip]" value="{{$duc['voucher_descrip']}}" data-id="voucher_narration">
                                                </td>
                                                <td>
                                                    <input type="text" class="amount validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm" name="duc[{{$loop->iteration}}][amount]" value="{{number_format($duc['voucher_debit'],3)}}" data-id="amount">
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

                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table id="AccForm" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline account_table">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                {{--<button type="button" class="removeHeaderContent removeHeaderContentStyle btn btn-danger btn-sm">
                                                    <i class="la la-remove"></i>
                                                </button>--}}
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input readonly id="c_account_id" type="hidden" class="c_account_id form-control erp-form-control-sm">
                                                <input readonly id="supplier_bank_id" type="hidden" class="supplier_bank_id form-control erp-form-control-sm">
                                                <input readonly id="supplier_account_id" type="hidden" class="supplier_account_id form-control erp-form-control-sm">
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
                                                <input id="c_account_code" type="text" class="acc_code tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','cAccountsHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Account Name</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="c_account_name" readonly type="text" class="acc_name form-control erp-form-control-sm">
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
                                                <select class="form-control erp-form-control-sm" id="payment_mode">
                                                    <option value="">Select</option>
                                                    @foreach($data['payment_terms'] as $payment_terms)
                                                        <option value="{{$payment_terms->payment_term_id}}">{{$payment_terms->payment_term_name}}</option>
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
                                                $bank_name = '';
                                                $bank =\App\Models\TblDefiBank::where('bank_id',$row->bank_id)->first();
                                                if($bank != null){
                                                    $bank_name = $bank->bank_name;
                                                }
                                                $branch_code = '';
                                                $acc_no = '';
                                                $sup_acc =\App\Models\TblPurcSupplierAccount::where('supplier_account_id',$row->tbl_supplier_account_id)->first();
                                                if($sup_acc != null){
                                                    $branch_code = $sup_acc->supplier_iban_no;
                                                    $acc_no = $sup_acc->supplier_account_no;
                                                }
                                            @endphp
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][c_account_id]" data-id="c_account_id" value="{{$row->chart_account_id}}"  class="account_id form-control erp-form-control-sm">
                                                    <input readonly data-id="supplier_bank_id" value="{{$row->bank_id}}"  type="hidden" name="pd[{{ $loop->iteration }}][supplier_bank_id]" class="supplier_bank_id form-control erp-form-control-sm">
                                                    <input readonly data-id="supplier_account_id" value="{{$row->tbl_supplier_account_id}}"  type="hidden" name="pd[{{ $loop->iteration }}][supplier_account_id]" class="supplier_account_id form-control erp-form-control-sm">
                                                </td>
                                                <td><input type="text" data-id="c_account_code" name="pd[{{ $loop->iteration }}][c_account_code]" value="{{$row->accounts->chart_code ?? ''}}" title="{{$row->accounts->chart_code ?? ''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code open_inline__help tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="c_account_name" name="pd[{{ $loop->iteration }}][c_account_name]" value="{{$row->accounts->chart_name ?? ''}}" title="{{$row->accounts->chart_name ?? ''}}" class="acc_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="voucher_descrip" name="pd[{{ $loop->iteration }}][voucher_descrip]" value="{{$row->voucher_descrip}}" title="{{$row->voucher_descrip}}" class="tb_moveIndex form-control erp-form-control-sm" ></td>
                                                <td>
                                                    <select class="form-control erp-form-control-sm" data-id="payment_mode" name="pd[{{ $loop->iteration }}][payment_mode]" title="{{$row->voucher_payment_mode}}">
                                                        <option value="0">Select</option>
                                                        @foreach($data['payment_terms'] as $payment_terms)
                                                            <option value="{{$payment_terms->payment_term_id}}" {{$row->voucher_payment_mode == $payment_terms->payment_term_id?'selected':''}}>{{$payment_terms->payment_term_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" data-id="mode_no" name="pd[{{ $loop->iteration }}][mode_no]" value="{{$row->voucher_mode_no}}" title="{{$row->voucher_mode_no}}" class="open_inline__help tb_moveIndex form-control erp-form-control-sm"></td>
                                                @php $mode_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$row->voucher_mode_date)))); @endphp
                                                <td><input type="text" data-id="mode_date" name="pd[{{ $loop->iteration }}][mode_date]" value="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" title="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" class="tb_moveIndex form-control erp-form-control-sm kt_datepicker_3" /></td>
                                                @php $credit = $row->voucher_debit; $fc_credit = $row->voucher_fc_debit; @endphp
                                                <td><input type="text" data-id="supplier_bank" name="pd[{{ $loop->iteration }}][supplier_bank]" value="{{$bank_name}}" class="supplier_bank form-control erp-form-control-sm readonly" readonly></td>
                                                <td><input type="text" data-id="bank_branch_code" name="pd[{{ $loop->iteration }}][bank_branch_code]" value="{{$branch_code}}" class="bank_branch_code form-control erp-form-control-sm readonly" readonly></td>
                                                <td><input type="text" data-id="payee_ac_no" name="pd[{{ $loop->iteration }}][payee_ac_no]" value="{{$acc_no}}" class="payee_ac_no form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="voucher_credit" name="pd[{{ $loop->iteration }}][voucher_credit]" value="{{number_format($credit,3)}}" title="{{$credit}}" class="tb_moveIndex credit form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" data-id="voucher_fc_credit" name="pd[{{ $loop->iteration }}][voucher_fc_credit]" value="{{number_format($fc_credit,3)}}" title="{{$fc_credit}}" class="tb_moveIndex fccredit form-control erp-form-control-sm validNumber" ></td>
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
                                    <tr class="remain">
                                        <td class="voucher-total-title align-middle">Remaining:</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_rem" ></span>
                                            <input id="remaining" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
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
                'id':'c_account_code',
                'fieldClass':'acc_code open_inline__help',
                'message':'Enter Account Detail',
                'require':true,
                'readonly':true,
                /*'data-url' : accountsHelpUrl*/
            },
            {
                'id':'c_account_name',
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
                'id':'supplier_bank',
                'fieldClass':'supplier_bank',
                'readonly':true,
            },
            {
                'id':'bank_branch_code',
                'fieldClass':'bank_branch_code',
                'readonly':true,
            },
            {
                'id':'supplier_bank_ac_no',
                'fieldClass':'supplier_bank_ac_no',
                'readonly':true,
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
        var arr_hidden_field = ['c_account_id','supplier_account_id','supplier_bank_id'];

        var form_type = $('#form_type').val();
        function funLocalStoragePV(){
            var date = localStorage.getItem('form_pv_date');
            if(!valueEmpty(date)){
                $('form').find('#kt_datepicker_3').val(date);
            }
            var accAccObj = JSON.parse(localStorage.getItem('accAccObj'));
            if(!valueEmpty(accAccObj)){
                if(!valueEmpty(accAccObj.id)){
                    $('#AccForm').find('thead').find('#c_account_id').val(accAccObj.id);
                    $('#AccForm').find('thead').find('#c_account_code').val(accAccObj.code);
                    $('#AccForm').find('thead').find('#c_account_name').val(accAccObj.name);
                }
            }
            var accDeductObj = JSON.parse(localStorage.getItem('accDeductObj'));
            if(!valueEmpty(accDeductObj)){
                if(!valueEmpty(accDeductObj.id)){
                    $('#DeductionTbl').find('thead').find('#account_id').val(accDeductObj.id);
                    $('#DeductionTbl').find('thead').find('#account_code').val(accDeductObj.code);
                    $('#DeductionTbl').find('thead').find('#account_name').val(accDeductObj.name);
                }
            }
        }

        function funcFormWise(){
            funLocalStoragePV()
        }
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js?v='.time()) }}" type="text/javascript"></script>
    <script>
        $('.remain').hide();
        $(document).on('click','.selectAllGrnInvAmt',function(){
            var tbody = $('#grnVoucherDtls').find('tbody.erp_form__grid_body');
            if($(this).prop('checked')) {
                tbody.find('tr').each(function(){
                    $(this).find('.checkedGrnInv').prop('checked',true);
                    var balance_amount = $(this).find('.balance_amount').val();
                    var current_amount = !valueEmpty(balance_amount)?(funcNumberFloat(balance_amount)):0;
                    $(this).find('.current_amount').val(current_amount);
                    $(this).find('.current_balance').val(0);
                })
            } else {
                tbody.find('tr').each(function(){
                    $(this).find('.checkedGrnInv').prop('checked',false);
                    var balance_amount = $(this).find('.balance_amount').val();
                    var current_balance = !valueEmpty(balance_amount)?(funcNumberFloat(balance_amount)):0;
                    $(this).find('.current_amount').val(0);
                    $(this).find('.current_balance').val(current_balance);
                })
            }
            funcSumGrnInvs()
        });
        $(document).on('click','.checkedGrnInv',function(){
            var tr = $(this).parents('tr');
            if($(this).prop('checked')) {
                var balance_amount = tr.find('.balance_amount').val();
                var current_amount = !valueEmpty(balance_amount)?(funcNumberFloat(balance_amount)):0;
                tr.find('.current_amount').val(current_amount);
                tr.find('.current_balance').val(0);
            } else {
                var balance_amount = tr.find('.balance_amount').val();
                var current_balance = !valueEmpty(balance_amount)?(funcNumberFloat(balance_amount)):0;
                tr.find('.current_amount').val(0);
                tr.find('.current_balance').val(current_balance);
            }
            funcSumGrnInvs()
        });
        $(document).on('keyup','.current_amount',function(){
            var tr = $(this).parents('tr');
            var val = $(this).val();
            val = funcNumberFloat(val);
            var balance_amount = tr.find('.balance_amount').val();
            var balance_amt = !valueEmpty(balance_amount)?(funcNumberFloat(balance_amount)):0;

            if(parseFloat(val) == parseFloat(balance_amt)){
                tr.find('.checkedGrnInv').prop('checked',true);
            }else{
                tr.find('.checkedGrnInv').prop('checked',false);
            }

            if(parseFloat(val) > parseFloat(balance_amt)){
                tr.find('.current_balance').val(0);
                $(this).val(parseFloat(balance_amt).toFixed(3))
                tr.find('.checkedGrnInv').prop('checked',true);
            }else{
                var current_balance = parseFloat(balance_amt) - parseFloat(val);
                tr.find('.current_balance').val(parseFloat(current_balance).toFixed(3));
            }

            funcSumGrnInvs()
        });
        function funcSumGrnInvs(){
            var tbody = $('#grnVoucherDtls').find('tbody.erp_form__grid_body');
            var total_current_amount = 0;
            if(!$('#on_account_voucher').prop('checked')) {
                tbody.find('tr').each(function(){
                    var balance_amount = $(this).find('.current_amount').val();
                    var amt = funcNumberFloat(balance_amount);
                    if(valueEmpty(amt)){
                        amt = 0;
                    }
                    total_current_amount += parseFloat(amt);
                })
                var tbody_footer = $('#grnVoucherDtls').find('tbody.erp_form__grid_footer');

                tbody_footer.find('.total_current_amount').val(parseFloat(total_current_amount).toFixed(3));
                $('#bill_total_amount').val(parseFloat(total_current_amount).toFixed(3));
            }else{
                total_current_amount =  $('#bill_total_amount').val();
                total_current_amount = funcNumberFloat(total_current_amount);
            }
            var amount = 0;
            var rem = 0;
            $('#DeductionTbl>tbody>tr').each(function(){
                var amt = $(this).find('.amount').val();
                var amt = funcNumberFloat(amt);
                if(valueEmpty(amt)){
                    amt = 0;
                }
                amount += parseFloat(amt);
            })
            var acc_credit = 0
            $('#AccForm>tbody>tr').each(function(){
                var amt = $(this).find('.credit').val();
                var amt = funcNumberFloat(amt);
                if(valueEmpty(amt)){
                    amt = 0;
                }
                acc_credit += parseFloat(amt);
            })
            var credit = parseFloat(total_current_amount) - (parseFloat(amount) + parseFloat(acc_credit));
            if(valueEmpty(credit)){
                credit = 0;
            }
            $('#AccForm').find('#voucher_credit').val(parseFloat(credit).toFixed(3));
            $('#AccForm').find('#voucher_fc_credit').val(parseFloat(credit).toFixed(3));
            rem = $('#remaining').val(parseFloat(credit).toFixed(3));
        }

        $(document).on('click','#addData',function(){
            funcSumGrnInvs();
        });

        $(document).on('click','#on_account_voucher',function(){
            $('#total_current_amount').val(0);
            if($(this).prop('checked')) {
                $('#grnVoucherDtls').addClass('pointerEventsNone');
                $('#bill_total_amount').removeClass('readonly');
            } else {
                $('#grnVoucherDtls').removeClass('pointerEventsNone');
                $('#bill_total_amount').addClass('readonly');
            }
        })
        $(document).on('click','#is_deduction',function(){
            if($(this).prop('checked')) {
                $('#deduction_block').removeClass('pointerEventsNone')
            } else {
                $('#deduction_block').addClass('pointerEventsNone')
            }
        })
        $(document).on('click','.btn-minus-selected-data',function(){
            $('#grnVoucherDtls').find('tbody.erp_form__grid_body').html('')
        })
        $(document).on('click','#getDatafromGrnBill',function(){
            var account_id = $('#up_chart_account_id').val();
            var arr = {
                account_id : account_id
            };
            getDatafromGrnBill(arr)
        })
        function getDatafromGrnBill(arr){
            var validate = true;
            var account_id = arr.account_id;
            if(valueEmpty(account_id)){
                toastr.error("Chart Account not valid");
                validate = false;
                return true;
            }
            if(validate){
                var formData = {
                    supplier_account_id : account_id,
                };
                var url = '/accounts/get-Receive-inv-detail';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        console.log(response);
                        if(!valueEmpty(response['data']['list'])){
                            toastr.success(response.message);
                            var tbody = $('#grnVoucherDtls').find('tbody.erp_form__grid_body');
                            var list = response['data']['list'];
                            var tr = "";
                            var i = 0;
                            list.forEach(function(item,index){
                                var grn_bill_no = !valueEmpty(item.grn_bill_no)?item.grn_bill_no:'';
                                var grn_date = !valueEmpty(item.grn_date)?item.grn_date:'';
                                var grn_total_net_amount = !valueEmpty(item.grn_total_net_amount)?(parseFloat(item.grn_total_net_amount).toFixed(3)):0;
                                var paid_amount = !valueEmpty(item.paid_amount)?(parseFloat(item.paid_amount).toFixed(3)):0;
                                var balance_amount = !valueEmpty(item.balance_amount)?(parseFloat(item.balance_amount).toFixed(3)):0;
                                var grn_remarks = !valueEmpty(item.grn_remarks)?item.grn_remarks:'';
                                // if((item.grn_type).toLowerCase() == 'pr'){
                                //     grn_total_net_amount = parseFloat(grn_total_net_amount) * -1;
                                //     paid_amount = parseFloat(paid_amount) * -1;
                                //     balance_amount = parseFloat(balance_amount) * -1;
                                // }
                                i += 1;
                                tr += '<tr>' +
                                    '<td>' +
                                    '<input readonly type="text" value="'+i+'" name="inv['+i+'][sr_no]" data-id="sr_no" class="form-control erp-form-control-sm">' +
                                    '<input readonly type="hidden" value="'+item.branch_id+'" name="inv['+i+'][branch_id]" data-id="branch_id" class="form-control erp-form-control-sm">' +
                                    '<input readonly type="hidden" value="'+item.grn_id+'" name="inv['+i+'][grn_id]" data-id="grn_id" class="form-control erp-form-control-sm"> ' +
                                    '</td>' +
                                    '<td> <input readonly type="text" value="'+item.branch_name+'" name="inv['+i+'][branch_name]" data-id="branch_name" class="form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+item.grn_code+'" name="inv['+i+'][grn_code]" data-id="grn_code" class="form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+grn_remarks+'" name="inv['+i+'][grn_remarks]" data-id="grn_remarks" class="form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+grn_bill_no+'" name="inv['+i+'][grn_bill_no]" data-id="grn_bill_no" class="form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+grn_date+'" name="inv['+i+'][grn_date]" data-id="grn_date" class="form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="" name="inv['+i+'][due_date]" data-id="due_date" class="form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+grn_total_net_amount+'" name="inv['+i+'][grn_total_net_amount]" data-id="grn_total_net_amount" class="validNumber validOnlyFloatNumber form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+paid_amount+'" name="inv['+i+'][paid_amount]" data-id="paid_amount" class="validNumber validOnlyFloatNumber form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+balance_amount+'" name="inv['+i+'][balance_amount]" data-id="balance_amount" class="balance_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm"> </td>' +
                                    '<td> <input type="text" value="0" name="inv['+i+'][current_amount]" data-id="current_amount" class="current_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm"> </td>' +
                                    '<td> <input readonly type="text" value="'+balance_amount+'" name="inv['+i+'][current_balance]" data-id="current_balance" class="current_balance validNumber validOnlyFloatNumber form-control erp-form-control-sm"> </td>' +
                                    '<td class="text-center"> <input type="checkbox" name="inv['+i+'][voucher_bill_grn_paid_status]" data-id="voucher_bill_grn_paid_status" class="checkedGrnInv"> </td>' +
                                    '</tr>';
                            })
                            tbody.html(tr);
                        }else{
                            toastr.error('Data not found..');
                        }
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                    }
                });
            }
        }

        $(document).on('change','.bank',function(){
            var thix = $(this);
            var tr = thix.parents('tr');
            var bank_branch_code = thix.find('option:selected').attr('data-code');
            tr.find('.bank_branch_code').val(bank_branch_code);
        });

        $(document).on('keyup','#bill_total_amount',function(){
            funcAfterAddRow();
        });

        $(document).on('click','#btn-update-entry',function(){
            var thix = $(this);
            var form = thix.parents('form');
            var date = form.find('#kt_datepicker_3').val();
            localStorage.setItem('form_pv_date', date);
        });

        funLocalStoragePV();

        function funcAfterAddRow(){
            funcSumGrnInvs()
        }
    </script>
    <script src="{{ asset('js/pages/js/common/add-row-repeated-rsp.js?v='.time()) }}" type="text/javascript"></script>
@endsection
