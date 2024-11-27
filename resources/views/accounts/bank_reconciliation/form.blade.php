@extends('layouts.layout')
@section('title', 'Bank Reconciliation')

@section('pageCSS')
@endsection
@section('content')
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    if($case == 'new'){
        $document_no = $data['document_no'];
        $date =  date('d-m-Y');
        $from_date =  date('d-m-Y');
        $to_date =  date('d-m-Y');
        $satement_date =  date('d-m-Y');
        $dtls = [];
        $bank_acco = [];
        $bank_rec_reconciled = 'unreconciled';
    }
    if($case == 'edit'){
        $id = $data['current']->bank_rec_id;
        $document_no = $data['current']->bank_rec_code;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_date))));
        $account_id = $data['current']->bank_rec_bank_id;
        $bank_balance = $data['current']->bank_rec_bank_balance;
        $from_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_start_date))));
        $to_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_end_date))));
        $satement_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->bank_rec_satement_date))));
        $closing_balance = $data['current']->bank_rec_closing_balance;
        $opening_balance = $data['current']->bank_rec_opening_balance;
        $uncleared = $data['current']->bank_rec_uncleared_balance;
        $notes = $data['current']->bank_rec_notes;
        $bank_rec_reconciled = $data['current']->bank_rec_reconciled;

        $dtls = isset($data['current']->dtl)?$data['current']->dtl:[];
        $bank_acco = isset($data['current']->bank_acco)?$data['current']->bank_acco:[];
    }
@endphp
@permission($data['permission'])
<!--begin::Form-->
<form id="bank_reconciliation_form" class="kt-form" method="post" action="{{action('Accounts\BankReconciliationController@create', isset($id)?$id:"") }}">
    @csrf
    <input type="hidden" value="bank_recon" id="form_type">
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="form-group-block row">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="erp-page--title">
                                    {{$document_no}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">

                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-5 erp-col-form-label">Document Date:</label>
                            <div class="col-lg-7">
                                <div class="input-group date">
                                    <input type="text" name="document_date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}"  id="kt_datepicker_3"/>
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
                <div class="form-group-block row">
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Bank Account: <span class="required">*</span></label>
                            <div class="col-lg-8">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" id="account_code" name="account_code" value="{{isset($bank_acco['chart_code'])?$bank_acco['chart_code']:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" autocomplete="off" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="text" id="account_name" name="account_name" value="{{isset($bank_acco['chart_name'])?$bank_acco['chart_name']:''}}" autocomplete="off" class="form-control erp-form-control-sm moveIndex" readonly style="background: #f9f9f9;">
                                        <input type="hidden" id="account_id" name="account_id" value="{{isset($account_id)?$account_id:''}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                <i class="la la-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row form-group-block">
                            <div class="col-lg-5">
                                <label class="erp-col-form-label">Opening Balance:</label>
                            </div>
                            <div class="col-lg-7">
                                <input readonly type="text" id="opening_balance" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" name="opening_balance" value="{{isset($opening_balance)?number_format($opening_balance,3):0}}" autocomplete="off" style="background: #f9f9f9;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <div class="col-lg-6">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <label class="erp-col-form-label">Select Date:</label>
                            </div>
                            <div class="col-lg-8">
                                <div class="erp-selectDateRange">
                                    <div class="input-daterange input-group kt_datepicker_5">
                                        <input type="text" class="form-control erp-form-control-sm kt_datepicker_bcs" format="dd-mm-yyyy" value="{{isset($from_date)?$from_date:""}}" id="from_date" name="from_date" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text erp-form-control-sm">To</span>
                                        </div>
                                        <input type="text" class="form-control erp-form-control-sm kt_datepicker_bcs" value="{{isset($to_date)?$to_date:""}}" id="to_date" name="to_date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row form-group-block">
                            <div class="col-lg-6">
                                <label class="erp-col-form-label">Bank Statement Balance:</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" value="{{isset($bank_balance)?number_format($bank_balance,3):0}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" name="bank_balance" id="bank_balance" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <div class="col-lg-6">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <label class="erp-col-form-label">Sort By:</label>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-control erp-form-control-sm" id="sort_by" name="sort_by">
                                    <option value="">Select | Sort By</option>
                                    <option value="voucher_date">Voucher Date</option>
                                    <option value="voucher_no">Voucher No</option>
                                    <option value="voucher_mode_date">Cheque Date</option>
                                    <option value="voucher_chqno">Cheque No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <div class="col-lg-6">
                        <div class="row form-group">
                            <div class="col-lg-12">
                                <div class="kt-radio-list">
                                    <label class="kt-radio kt-radio--bold kt-radio--brand">
                                        <input type="radio" id="unreconciled" value="unreconciled" name="transactions" {{$bank_rec_reconciled=='unreconciled'?'checked':""}}>
                                        Show unreconciled transactions only
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--bold kt-radio--brand">
                                        <input type="radio" id="transactions" value="all_transactions" name="transactions" {{$bank_rec_reconciled=='all_transactions'?'checked':""}}>
                                        Show all transactions
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Bank Statement Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="statement_date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p kt_datepicker_bcs" readonly value="{{isset($satement_date)?$satement_date:""}}"  id="statement_date"/>
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
                    <div class="col-lg-12">
                        <div class="row">
                            <label class="col-lg-2 erp-col-form-label">Branches:</label>
                            <div class="col-lg-10">
                                <div class="erp-select2">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm" multiple id="branch_name" name="branch_ids[]">
                                            @foreach($data['branches'] as $branch)
                                                <option value="{{$branch->branch_id}}" {{$branch->branch_id == auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-sm btn-primary" id="get_data">Get Data</button>
                    </div>
                    <div class="col-lg-6">
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group date">
                            <input type="text" name="reconciled_date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p reconciled_date" readonly value="{{isset($date)?$date:""}}"  id="kt_datepicker_3" style="margin-right: 3px;"/>
                            <button type="button" class="btn btn-sm btn-primary" id="go_date" style="padding: 0px 10px;">Set Cleared Date</button>
                        </div>
                    </div>
                </div>
                <style>
                    table#data_bank_reconciliation {
                        border-bottom: 2px solid #a5a5a5;
                    }
                    table#data_bank_reconciliation th {
                        border: 0px solid #cecece;
                        background: #ececec;
                        font-size: 12px;
                        font-weight: 500 !important;
                        text-align: center;
                        padding: 12px 3px !important;
                        font-family: Roboto;
                    }
                    table#data_bank_reconciliation td {
                        font-size: 12px;
                        font-weight: 400;
                        padding: 5px 3px !important;
                        /*border: 1px solid #ebedf2;*/
                    }
                    table#data_bank_reconciliation tr:nth-child(even)>td {
                        background: #fbfbfb;
                        border-bottom: 2px solid #dadada;
                    }
                    table#data_bank_reconciliation tr:nth-child(even)>td input {
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
                <div class="row">
                    <div class="col-lg-12">
                        <table id="data_bank_reconciliation" class="table">
                            <thead>
                            <tr>
                                <th width="90px">
                                    <div>Branch</div>
                                    <input type="text" class="filter_branch" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Description</div>
                                    <input type="text" class="filter_description" style="width: 100%;">
                                </th>
                                <th width="100px">
                                    <div>Voucher Date</div>
                                    <input type="text" class="filter_voucher_date" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Voucher No</div>
                                    <input type="text" class="filter_voucher_no" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Cheque Date</div>
                                    <input type="text" class="filter_cheque_date" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Cheque No</div>
                                    <input type="text" class="filter_cheque_no" style="width: 100%;">
                                </th>
                                <th width="180px">
                                    <div>Narration</div>
                                    <input type="text" class="filter_narration" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Debit</div>
                                    <input type="text" class="filter_narration" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Credit</div>
                                    <input type="text" class="filter_credit" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Balance</div>
                                    <input type="text" class="filter_balance" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Cleared Date</div>
                                    <input type="text" class="filter_cleared_date" style="width: 100%;">
                                </th>
                                <th width="90px">
                                    <div>Status</div>
                                    <input type="text" class="filter_status" style="width: 100%;">
                                </th>
                                {{--<th width="90px">
                                    <div>Notes</div>
                                    <input type="text" class="filter_notes" style="width: 100%;">
                                </th>--}}
                                <th width="70px">
                                    <div>Reconciled</div>
                                    <button type="button" class="btn btn-sm btn-danger" id="header_input_clear_data" style="padding: 3px;"><i class="la la-times"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dtls as $dtl)
                                <tr>
                                    <td>
                                        <div>{{$dtl['bank_rec_voucher_branch']}}</div>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][voucher_type]" class="pd_bank_recon_input" value="{{$dtl['voucher_type']}}" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][branch_name]" class="pd_bank_recon_input" value="{{$dtl['bank_rec_voucher_branch']}}" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][voucher_id]" class="pd_bank_recon_input" value="{{$dtl['bank_rec_voucher_id']}}">
                                    </td>
                                    @php $voucher_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl['bank_rec_voucher_date'])))); @endphp
                                    <td>
                                        <div>{{($voucher_date =='01-01-1970' || $voucher_date == '')?'':$voucher_date}}</div>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][voucher_date]" class="pd_bank_recon_input cheque_date" value="{{($voucher_date =='01-01-1970' || $voucher_date == '')?'':$voucher_date}}" readonly>
                                    </td>
                                    <td>
                                        <div>{{$dtl['bank_rec_voucher_no']}}</div>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][voucher_no]" class="pd_bank_recon_input" value="{{$dtl['bank_rec_voucher_no']}}" readonly>
                                    </td>
                                    @php $cheque_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl['bank_rec_voucher_chqdate'])))); @endphp
                                    <td>
                                        <div>{{($cheque_date =='01-01-1970' || $cheque_date == '')?'':$cheque_date}}</div>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][cheque_date]" class="pd_bank_recon_input cheque_date" value="{{($cheque_date =='01-01-1970' || $cheque_date == '')?'':$cheque_date}}" readonly>
                                    </td>
                                    <td>
                                        <div>{{$dtl['bank_rec_voucher_chqno']}}</div>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][cheque_no]" class="pd_bank_recon_input" value="{{$dtl['bank_rec_voucher_chqno']}}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][narration]" class="pd_bank_recon_input" value="{{ucwords(strtolower(strtoupper($dtl['bank_rec_voucher_descrip'])))}}">
                                        <div class="narration">
                                            {{ucwords(strtolower(strtoupper($dtl['bank_rec_voucher_descrip'])))}}
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div>{{number_format($dtl['bank_rec_voucher_debit'],3)}}</div>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][debit]" class="voucher_debit pd_bank_recon_input text-right" value="{{number_format($dtl['bank_rec_voucher_debit'],3)}}" readonly>
                                    </td>
                                    <td class="text-right">
                                        <div>{{number_format($dtl['bank_rec_voucher_credit'],3)}}</div>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][credit]" class="voucher_credit pd_bank_recon_input text-right" value="{{number_format($dtl['bank_rec_voucher_credit'],3)}}" readonly>
                                    </td>
                                    @php $cleared_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl['bank_rec_voucher_cleared_date'])))); @endphp
                                    <td class="text-right">
                                        <input type="text" name="pd[{{$loop->iteration}}][cleared_date]" class="cleared_date_input pd_bank_recon_input_open date_inputmask" value="{{($cleared_date =='01-01-1970' || $cleared_date == '')?'':$cleared_date}}">
                                    </td>
                                    <td class="text-right">
                                        <div class="erp-select2">
                                            <select class="cheque_status" name="cheque_status">
                                                <option value="">Select</option>
                                                @foreach($data['cheque_status'] as $cheque_status)
                                                    <option value="{{$cheque_status->cheque_status_id}}" {{$cheque_status->cheque_status_id==$dtl['bank_rec_cheque_status'] ?'selected':''}}>{{$cheque_status->cheque_status_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" name="pd[{{$loop->iteration}}][voucher_notes]" class="pd_bank_recon_input_open" value="{{$dtl['bank_rec_voucher_notes']}}">
                                    </td>
                                    <td class="text-center">
                                        <label class="kt-radio kt-radio--bold kt-radio--success" style="left: 7px;">
                                            <input type="checkbox" class="marked" name="pd[{{$loop->iteration}}][marked]" {{$dtl['bank_rec_voucher_mode_no']==1?'checked':""}}>
                                            <span></span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                            {{--@for($i=0;$i<9;$i++)
                                <tr>
                                    <td>
                                        <input type="text" name="pd[{{$i}}][voucher_date]" class="pd_bank_recon_input" value="03-03-2021" readonly>
                                        <input type="hidden" name="pd[{{$i}}][voucher_id]" class="pd_bank_recon_input" value="124234241">
                                        <input type="hidden" name="pd[{{$i}}][narration]" class="pd_bank_recon_input" value="{{ucwords(strtolower(strtoupper("BEING CHQ PAYMENT AGAINST MONTH OF OCT INV")))}}">
                                    </td>
                                    <td><input type="text" name="pd[{{$i}}][voucher_no]" class="pd_bank_recon_input" value="BCR-000001" readonly></td>
                                    <td><input type="text" name="pd[{{$i}}][cheque_date]" class="pd_bank_recon_input" value="03-03-2021" readonly></td>
                                    <td><input type="text" name="pd[{{$i}}][cheque_no]" class="pd_bank_recon_input" value="123-456-897" readonly></td>
                                    <td>
                                        <div class="narration">
                                            {{ucwords(strtolower(strtoupper("BEING CHQ PAYMENT AGAINST MONTH OF OCT INV")))}}
                                        </div>
                                    </td>
                                    <td class="text-right"><input type="text" name="pd[{{$i}}][debit]" class="pd_bank_recon_input" value="5000.000" readonly></td>
                                    <td class="text-right"><input type="text" name="pd[{{$i}}][credit]" class="pd_bank_recon_input" value="0.000" readonly></td>
                                    <td class="text-right"><input type="text" name="pd[{{$i}}][balance]" class="pd_bank_recon_input" value="255000.000" readonly></td>
                                    <td class="text-center">
                                        <label class="kt-radio kt-radio--bold kt-radio--success" style="left: 7px;">
                                            <input type="checkbox" name="pd[{{$i}}][marked]">
                                            <span></span>
                                        </label>
                                    </td>
                                </tr>
                            @endfor--}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group-block row" style="color:#000;">
                    <div class="offset-lg-6 col-lg-6">
                        <div class="form-group-block row">
                            <div class="col-lg-3">As Per Ledger</div>
                            <div class="col-lg-3 text-right">(DR) <span class="as_per_ledger_dr">0</span></div>
                            <div class="col-lg-3 text-right">(CR) <span class="as_per_ledger_cr">0</span></div>
                            <div class="col-lg-3 text-right">(Bal) <span class="as_per_ledger_bal">0</span></div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">Un Present Cheques (Issue / Forward):</div>
                            <div class="col-lg-3 text-right" style="background-color:#ADD8E6;"><span class="un_present_cheques">0</span></div>
                            <div class="col-lg-3"></div>
                        </div>
                        <div class="form-group-block row">
                            <div class="col-lg-6">Balance As Per Ledger Statement:</div>
                            <div class="col-lg-3"></div>
                            <div class="col-lg-3">
                                <input type="text" value="{{isset($closing_balance)?number_format($closing_balance,3):0}}" id="closing_balance" name="closing_balance" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber readonly" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block row d-none">
                    <div class="offset-lg-8 col-lg-4">
                        <div class="row d-none">
                            <div class="col-lg-6">
                                <label class="erp-form-control-sm">Uncleared:</label>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" name="uncleared" id="uncleared" value="{{isset($uncleared)?number_format($uncleared,3):0}}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="2" id="bank_rec_notes" name="bank_rec_notes" class="form-control erp-form-control-sm">{{isset($notes)?$notes:''}}</textarea>
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
    <script src="{{ asset('js/pages/js/accounts/bank-reconciliation.js') }}" type="text/javascript"></script>

    <script>
        var onPageSpinner = "<div class='kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center'> <span>loading..</span></div>";

        $(document).on('click','#go_date',function(){
            if($(document).find('#data_bank_reconciliation>tbody>tr').length != 0){
                var reconciled_date = $('.reconciled_date').val();
                $(document).find('#data_bank_reconciliation>tbody>tr').each(function() {
                    var check = $(this).find('td input.marked');
                    if(check.prop('checked')){
                        $(this).find('td .cleared_date_input').val(reconciled_date);
                    }
                });
            }
        });
        var xhrGetDataStatus = true;
        $(document).on('click','#get_data',function(){
            var validate = true;
            var account_id = $('#account_id').val();
            var sort_by = $('#sort_by').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var unreconciled = $('#unreconciled:checked').val();
            var transactions = $('#transactions:checked').val();
            var branches = [];
            $("#branch_name :selected").map(function(i, el) {
                branches.push($(el).val());
            }).get();
            if(valueEmpty(account_id)){
                toastr.error("Account is required");
                validate = false;
                return true;
            }
            if(branches.length == 0){
                toastr.error("Branch is required");
                validate = false;
                return true;
            }
            if(validate && xhrGetDataStatus){
                xhrGetDataStatus = false;
                $('#data_bank_reconciliation>tbody').html(onPageSpinner);
                var formData = {
                    account_id : account_id,
                    from_date : from_date,
                    to_date : to_date,
                    unreconciled : unreconciled,
                    transactions : transactions,
                    branches : branches,
                    sort_by : sort_by,
                }
                var url = '{{action('Accounts\BankReconciliationController@getAccData')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : "POST",
                    url         :  url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
                        $('#data_bank_reconciliation').addClass('pointerEventsNone');
                    },
                    success: function(response,data) {
                        if(response['status'] == 'success'){
                            var data = response['data']['items'];
                            var opening_balance = response['data']['opening_balance'];
                            var payment_mode = response['data']['payment_mode'];
                            console.log(payment_mode);
                            var bank_balance = $('form').find('#bank_balance').val();
                            var closing_balance = parseFloat(bank_balance).toFixed(3);
                            var uncleared = 0;
                            $('form').find('#opening_balance').val(opening_balance);
                            var len = data.length;
                            var tbody = $('#data_bank_reconciliation>tbody');
                            var rows = '';
                            var voucher_balance = 0;
                            var closing_balance_cr = 0;
                            var un_present_cheques = 0;
                            var as_per_ledger_dr = 0;
                            var un_present_cheques_count = 0;
                            for(var i=0;i<len;i++){
                                // console.log(data[i]);
                                var td = '';
                                var index = i+1;
                                var v_date = new Date(data[i]['voucher_date']).getDate();
                                var v_month = new Date(data[i]['voucher_date']).getMonth();
                                var v_year = new Date(data[i]['voucher_date']).getFullYear();
                                v_date = (parseInt(v_date) < 10)?'0'+v_date:v_date;
                                v_month = parseInt(v_month) + 1;
                                v_month = (v_month < 10)?'0'+v_month:v_month;

                                var voucher_date = v_date +'-'+ v_month +'-'+ v_year;

                                var brcd_date = new Date(data[i]['bank_rec_cleared_date']).getDate();
                                var brcd_month = new Date(data[i]['bank_rec_cleared_date']).getMonth();
                                var brcd_year = new Date(data[i]['bank_rec_cleared_date']).getFullYear();
                                brcd_date = (parseInt(brcd_date) < 10)?'0'+brcd_date:brcd_date;
                                brcd_month = parseInt(brcd_month) + 1;
                                brcd_month = (brcd_month < 10)?'0'+brcd_month:brcd_month;

                                var bank_rec_cleared_date = brcd_date +'-'+ brcd_month +'-'+ brcd_year;

                                var running_balance = parseFloat(voucher_balance) + parseFloat(data[i]['voucher_debit']) - parseFloat(data[i]['voucher_credit']);

                                voucher_balance = running_balance;

                                td += '<td>' +
                                '<div>'+data[i]['branch_name']+'</div>' +
                                '<input value="'+data[i]['voucher_type']+'" readonly type="hidden" name="pd['+index+'][voucher_type]" class="pd_bank_recon_input">' +
                                '<input value="'+data[i]['branch_name']+'" readonly type="hidden" name="pd['+index+'][branch_name]" class="pd_bank_recon_input">' +
                                '<input value="'+data[i]['voucher_id']+'" readonly type="hidden" name="pd['+index+'][voucher_id]" class="pd_bank_recon_input">' +
                                '<input value="'+data[i]['chart_account_id']+'" readonly type="hidden" name="pd['+index+'][bank_chart_account_id]" class="pd_bank_recon_input">' +
                                '</td>';
                                td += '<td>' +
                                '<div>'+(!valueEmpty(data[i]['contra_chart_name'])?data[i]['contra_chart_name']:"")+'</div>' +
                                '</td>';
                                td += '<td>' +
                                '<div>'+voucher_date+'</div>' +
                                '<input value="'+voucher_date+'" readonly type="hidden" name="pd['+index+'][voucher_date]" class="pd_bank_recon_input cheque_date">' +
                                '</td>';
                                td += '<td>' +
                                '<div>'+data[i]['voucher_no']+'</div>' +
                                '<input value="'+data[i]['voucher_no']+'" readonly type="hidden" name="pd['+index+'][voucher_no]" class="pd_bank_recon_input">' +
                                '</td>';
                                var voucher_mode_date = '';
                                if(!valueEmpty(data[i]['voucher_mode_date'])){
                                    var vm_date = new Date(data[i]['voucher_mode_date']).getDate();
                                    var vm_month = new Date(data[i]['voucher_mode_date']).getMonth();
                                    var vm_year = new Date(data[i]['voucher_mode_date']).getFullYear();
                                    vm_date = (parseInt(vm_date) < 10)?'0'+vm_date:vm_date;
                                    vm_month = parseInt(vm_month) + 1;
                                    vm_month = (vm_month < 10)?'0'+vm_month:vm_month;
                                    voucher_mode_date = vm_date +'-'+ vm_month +'-'+ vm_year;
                                }
                                td += '<td>' +
                                '<div>'+voucher_mode_date+'</div>' +
                                '<input value="'+voucher_mode_date+'" readonly type="hidden" name="pd['+index+'][cheque_date]" class="pd_bank_recon_input cheque_date">' +
                                '</td>';
                                td += '<td>' +
                                '<div>'+(data[i]['voucher_mode_no'] != null?data[i]['voucher_mode_no']:"")+'</div>' +
                                '<input value="'+(data[i]['voucher_mode_no'] != null?data[i]['voucher_mode_no']:"")+'" readonly type="hidden" name="pd['+index+'][cheque_no]" class="pd_bank_recon_input">' +
                                '</td>';
                                var voucher_descrip = "";
                                if(!valueEmpty(data[i]['voucher_descrip'])){
                                    voucher_descrip = data[i]['voucher_descrip'];
                                }
                                td += '<td>' +
                                '<input value="'+data[i]['voucher_descrip']+'" readonly type="hidden" name="pd['+index+'][narration]" class="pd_bank_recon_input text-center">' +
                                '<div class="narration">' +
                                voucher_descrip +
                                '</div></td>';
                                if(data[i]['bank_rec_posted'] == 0 && parseFloat(data[i]['voucher_debit']) == 0 && parseFloat(data[i]['voucher_credit']) != 0){
                                    td += '<td>' +
                                    '<div class="text-right">'+parseFloat(data[i]['voucher_debit']).toFixed(3)+'</div>' +
                                    '<input value="'+parseFloat(data[i]['voucher_debit']).toFixed(3)+'" readonly type="hidden" name="pd['+index+'][debit]" class="voucher_debit pd_bank_recon_input text-right">' +
                                    '</td>';
                                    td += '<td>' +
                                        '<div class="text-right" style="background-color:#ADD8E6;">'+parseFloat(data[i]['voucher_credit']).toFixed(3)+'</div>' +
                                        '<input value="'+parseFloat(data[i]['voucher_credit']).toFixed(3)+'" readonly type="hidden" name="pd['+index+'][credit]" class="voucher_credit pd_bank_recon_input text-right">' +
                                        '</td>';
                                    un_present_cheques_count += 1;
                                }else{
                                    td += '<td>' +
                                    '<div class="text-right">'+parseFloat(data[i]['voucher_debit']).toFixed(3)+'</div>' +
                                    '<input value="'+parseFloat(data[i]['voucher_debit']).toFixed(3)+'" readonly type="hidden" name="pd['+index+'][debit]" class="voucher_debit pd_bank_recon_input text-right">' +
                                    '</td>';
                                    td += '<td>' +
                                        '<div class="text-right">'+parseFloat(data[i]['voucher_credit']).toFixed(3)+'</div>' +
                                        '<input value="'+parseFloat(data[i]['voucher_credit']).toFixed(3)+'" readonly type="hidden" name="pd['+index+'][credit]" class="voucher_credit pd_bank_recon_input text-right">' +
                                        '</td>';
                                }
                                td += '<td>' +
                                    '<div class="text-right">'+parseFloat(voucher_balance).toFixed(3)+'</div>' +
                                    '<input value="'+parseFloat(voucher_balance).toFixed(3)+'" readonly type="hidden" class="voucher_balance pd_bank_recon_input text-right">' +
                                    '</td>';
                                if(data[i]['bank_rec_posted'] == 1){
                                    td += '<td>' +
                                    '<div>'+(bank_rec_cleared_date != '01-01-1970'?bank_rec_cleared_date:"")+'</div>' +
                                    '<input value="'+(bank_rec_cleared_date != '01-01-1970'?bank_rec_cleared_date:"")+'" type="hidden" name="pd['+index+'][cleared_date]">' +
                                    '</td>';
                                }else{
                                    td += '<td>' +
                                    '<input value="" type="text" name="pd['+index+'][cleared_date]" class="cleared_date_input pd_bank_recon_input_open date_inputmask">' +
                                    '</td>';
                                }

                                var voucher_payment_mode_name = "";
                                var voucher_payment_mode_val = "";
                                if(!valueEmpty(payment_mode[data[i]['voucher_payment_mode']])){
                                    voucher_payment_mode_name = '<div class="text-right">'+payment_mode[data[i]['voucher_payment_mode']]+'</div>';
                                    voucher_payment_mode_val = data[i]['voucher_payment_mode'];
                                }
                                td += '<td>' +
                                    voucher_payment_mode_name +
                                    '<input value="'+voucher_payment_mode_val+'" readonly type="hidden" name="pd['+index+'][cheque_status]" class="cheque_status">' +
                                    '</td>';

                                /*if(data[i]['bank_rec_posted'] == 1){
                                    td += '<td>' +
                                    '<input type="hidden" name="pd['+index+'][voucher_notes]" value="'+(!valueEmpty(data[i]['bank_rec_voucher_notes'])?data[i]['bank_rec_voucher_notes']:"")+'">' +
                                    '</td>';
                                }else{
                                    td += '<td>' +
                                    '<input type="text" name="pd['+index+'][voucher_notes]" class="pd_bank_recon_input_open" value="'+(!valueEmpty(data[i]['bank_rec_voucher_notes'])?data[i]['bank_rec_voucher_notes']:"")+'">' +
                                    '</td>';
                                }*/

                                if (data[i]['bank_rec_posted']==0 && parseFloat(data[i]['voucher_debit']) == 0 && parseFloat(data[i]['voucher_credit']) != 0) {
                                    td += '<td class="text-center"><label class="kt-radio kt-radio--bold kt-radio--success" style="left: 7px;">' +
                                    '<input type="checkbox" class="marked" name="pd['+index+'][marked]"' +'>'+
                                    '<span></span></label></td>';
                                }else{
                                    td += '<td></td>';
                                }
                                var tr = '<tr>'+td+'</tr>';
                                closing_balance_cr += parseFloat(data[i]['voucher_credit']);
                                as_per_ledger_dr += parseFloat(data[i]['voucher_debit']);
                                if(parseFloat(data[i]['voucher_debit']) == 0 && data[i]['bank_rec_posted'] == 1){
                                    closing_balance = parseFloat(closing_balance) - parseFloat(data[i]['voucher_credit']);
                                }else{
                                    closing_balance = parseFloat(closing_balance) + parseFloat(data[i]['voucher_debit']);
                                }
                                if(data[i]['bank_rec_posted'] == 0){
                                    uncleared += parseFloat(data[i]['voucher_debit']);
                                    uncleared += parseFloat(data[i]['voucher_credit']);
                                    un_present_cheques += parseFloat(data[i]['voucher_credit']);
                                }
                                rows += tr;
                            }
                            if(len == 0){
                                rows = '<tr><td class="text-center" colspan="11">No Data Found...</td></tr>'
                                uncleared = 0;
                            }
                            tbody.html(rows);
                            $('.narration').css('text-transform', 'capitalize');
                            $('form').find('#closing_balance').val(parseFloat(closing_balance).toFixed(3));
                            $('form').find('#closing_balance_cr').val(parseFloat(closing_balance_cr).toFixed(3));

                            $('form').find('.as_per_ledger_dr').html(parseFloat(as_per_ledger_dr).toFixed(3));
                            $('form').find('.as_per_ledger_cr').html(parseFloat(closing_balance_cr).toFixed(3));
                            var as_per_ledger_bal = parseFloat(as_per_ledger_dr) - parseFloat(closing_balance_cr);
                            $('form').find('.as_per_ledger_bal').html(parseFloat(as_per_ledger_bal).toFixed(3));
                            $('form').find('.un_present_cheques').html("("+parseInt(un_present_cheques_count)+") "+ parseFloat(un_present_cheques).toFixed(3));

                            $('form').find('#uncleared').val(parseFloat(uncleared).toFixed(3));
                            date_inputmask();

                        }else{
                            toastr.error(response.message);
                            $('#data_bank_reconciliation>tbody').html("No Data Found...");
                        }
                        xhrGetDataStatus = true;
                        $('#data_bank_reconciliation').removeClass('pointerEventsNone');
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGetDataStatus = true;
                        $('#data_bank_reconciliation').removeClass('pointerEventsNone');
                        $('#data_bank_reconciliation>tbody').html("No Data Found...");
                    }
                });

            }
        });

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
        $('.kt_datepicker_bcs, .kt_datepicker_bcs_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows
        });
        $('.filter_cleared_date').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows
        });

        setInterval(function(){
            if($('#data_bank_reconciliation>tbody>tr>td').length != 1){
                var bank_balance = $('form').find('#bank_balance').val();
                var closing_balance = parseFloat(bank_balance).toFixed(3);
                var uncleared = 0;
                $('#data_bank_reconciliation>tbody>tr').each(function() {
                    var check = $(this).find('td input.marked:checked').val();
                    var voucher_debit = parseFloat($(this).find('td .voucher_debit').val());
                    var marked = $(this).find('td:last-child input').hasClass('marked');
                    if(check == 'on' || (!marked && voucher_debit == 0)){
                        closing_balance = parseFloat(closing_balance) - parseFloat($(this).find('td .voucher_credit').val());
                    }else{
                        closing_balance = parseFloat(closing_balance) + parseFloat($(this).find('td .voucher_debit').val());
                    }
                });
                $('form').find('#closing_balance').val(parseFloat(closing_balance).toFixed(3));
                $('form').find('#uncleared').val(parseFloat(uncleared).toFixed(3));
            }
        },1000)
        function date_inputmask(){
            $(".date_inputmask").inputmask("99-99-9999", {
                "mask": "99-99-9999",
                "placeholder": "dd-mm-yyyy",
                autoUnmask: true
            });
        }
        date_inputmask();
        $(document).on('click','.marked',function(){
            if($(this).is(':checked')){
                var cheque_date = $('.reconciled_date').val();
                $(this).parents('tr').find('.cleared_date_input').val(cheque_date);
            }else{
                $(this).parents('tr').find('.cleared_date_input').val('');
            }
        });


        $(document).on('click','#header_input_clear_data',function(){
            $("#data_bank_reconciliation>thead input").val("");
            $('#data_bank_reconciliation>thead input').each(function(){
                var val = $(this).val();
                var index = $(this).parent('th').index();
                var arr = {
                    index : index,
                    val : val
                }
                funFilterDataRow1(arr);
            })
        })
        $(document).on('keyup','#data_bank_reconciliation>thead input',function(){
            var val = $(this).val();
            var index = $(this).parent('th').index();
            var arr = {
                index : index,
                val : val
            }
            funFilterDataRow1(arr);
        })
        $(document).on('change','#data_bank_reconciliation>thead input.filter_cleared_date',function(){
            var val = $(this).val();
            var index = $(this).parent('th').index();
            var arr = {
                index : index,
                val : val
            }
            funFilterDataRow1(arr);
        })

        function funFilterDataRow1(arr) {
            var input, filter, table, tr, td, i, txtValue;
            input = arr.val;
            var td_index = arr.index;
            filter = input;
            table = document.getElementById("data_bank_reconciliation");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[td_index];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection

