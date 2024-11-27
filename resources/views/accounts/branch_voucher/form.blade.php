@extends('layouts.layout')
@section('title', 'Branch Payment Voucher')

@section('pageCSS')
@endsection
@section('content')
@php
    $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    $type = $data['type'];
    if($case == 'new'){
        $id = "";
        $voucher_no = $data['voucher_no'];
        $date =  date('d-m-Y');
        $voucher_bill = [];
        $is_deduction = 0;
    }
    if($case == 'edit'){
        $id = $data['current']->voucher_id;
        $voucher_no = $data['current']->voucher_no;
        $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
        $currency_id = $data['current']->currency_id;
        $exchange_rate = $data['current']->voucher_exchange_rate;
        $cash_type = $data['current']->chart_account_id;
        $narration = $data['current']->voucher_descrip;
        $bill_amount = $data['current']->voucher_debit;
        $saleman = $data['current']->saleman_id;
        $payment_mode = $data['current']->voucher_payment_mode;
        $mode = $data['current']->voucher_mode_no;
        $is_deduction = $data['current']->is_deduction;

        $notes = $data['current']->voucher_notes;
        $dtls = isset($data['dtl'])? $data['dtl'] :[];
        $voucher_bill = isset($data['current']->voucher_bill)? $data['current']->voucher_bill :[];
    }

    $payment_modes = $data['payment_mode'];
    $form_type = $type;
@endphp
@permission($data['permission'])
<!--begin::Form-->
<form id="voucher_form" class="kt-form" method="post" action="{{action('Accounts\VoucherController@brpvstore', [$type,isset($id)?$id:''])}}">
    @csrf
    
    @if(session('msg'))
        <script>
            alert('This voucher enter in BRS!');
            document.location='/listing/accounts/{{ $type }}'; 
        </script>
    @endif
    <input type="hidden" value='{{ $form_type }}' id="form_type">
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
                <div class="form-group-block row">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Salesman:</label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select name="saleman_id" id="salesman" class="form-control erp-form-control-sm moveIndex moveIndex2 kt-select2">
                                        <option value="">Select</option>
                                        @if($case == 'edit')
                                            @php $$saleman = isset($$saleman)?$$saleman:""; @endphp
                                            @foreach($data['users'] as $user)
                                                <option value="{{$user->id}}" {{$user->id ==$saleman ?"selected":""}}>{{$user->name}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['users'] as $user)
                                                <option value="{{$user->id}}" {{Auth::user()->id == $user->id?'selected':''}}>{{$user->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
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
                
                <div class="form-group-block row">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Payment To:<span class="required">*</span></label>
                                <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select class="form-control erp-form-control-sm moveIndex kt-select2" id="kt_select2_1" name="cash_type">
                                        <option value="">Select</option>
                                        @php $cash_type = isset($cash_type)?$cash_type:''@endphp
                                        @foreach($data['acc_code'] as $acc_code)
                                            <option value="{{$acc_code->chart_account_id}}" {{$acc_code->chart_account_id==$cash_type?'selected':''}}>{{$acc_code->chart_code}} - {{$acc_code->chart_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-5 erp-col-form-label">Amount:</label>
                            <div class="col-lg-7">
                                <input type="text" id="bill_amount" name="bill_amount" value="{{isset($bill_amount)?$bill_amount:''}}" class="moveIndex form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-5 erp-col-form-label">Narration:</label>
                            <div class="col-lg-7">
                                <input type="text" id="narration" name="narration" value="{{isset($narration)?$narration:''}}" class="moveIndex form-control erp-form-control-sm">
                            </div>
                        </div>
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="dc_label">Credit Information</div>
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
                            <table id="AccForm" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                <tr>
                                    <th scope="col" width="35px">
                                        <div class="erp_form__grid_th_title">Sr.</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                            <input readonly id="c_account_id" type="hidden" class="c_account_id form-control erp-form-control-sm">
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
                                            <input id="c_account_code" type="text" class="acc_code  tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','cAccountsHelp')}}">
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
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                <input readonly type="hidden" name="pd[{{ $loop->iteration }}][c_account_id]" data-id="c_account_id" value="{{$data->chart_account_id}}"  class="account_id form-control erp-form-control-sm">
                                            </td>
                                            <td><input type="text" data-id="c_account_code" name="pd[{{ $loop->iteration }}][c_account_code]" value="{{$data->accounts->chart_code ?? ''}}" title="{{$data->accounts->chart_code ?? ''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code open_inline__help tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="c_account_name" name="pd[{{ $loop->iteration }}][c_account_name]" value="{{$data->accounts->chart_name ?? ''}}" title="{{$data->accounts->chart_name ?? ''}}" class="acc_name form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="voucher_descrip" name="pd[{{ $loop->iteration }}][voucher_descrip]" value="{{$data->voucher_descrip}}" title="{{$data->voucher_descrip}}" class="tb_moveIndex form-control erp-form-control-sm" ></td>
                                            <td>
                                                <select data-id="payment_mode" name="pd[{{ $loop->iteration }}][payment_mode]" class="form-control erp-form-control-sm tb_moveIndex">
                                                    <option value="">Select</option>
                                                    @foreach($payment_modes as $payment)
                                                        <option value="{{$payment->payment_term_id}}" {{$data->voucher_payment_mode == $payment->payment_term_id?"selected":""}}>{{$payment->payment_term_name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="mode_no" name="pd[{{ $loop->iteration }}][mode_no]" value="{{$data->voucher_mode_no}}" title="{{$data->voucher_mode_no}}" class="open_inline__help tb_moveIndex form-control erp-form-control-sm"></td>
                                            @php $mode_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data->voucher_mode_date)))); @endphp
                                            <td><input type="text" data-id="mode_date" name="pd[{{ $loop->iteration }}][mode_date]" value="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" title="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" class="tb_moveIndex form-control erp-form-control-sm kt_datepicker_3" /></td>
                                            @php $credit = $data->voucher_credit; $fc_credit = $data->voucher_fc_credit; @endphp
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
                'id':'voucher_credit',
                'fieldClass':'tb_moveIndex credit validNumber validOnlyFloatNumber'
            },
            {
                'id':'voucher_fc_credit',
                'fieldClass':'tb_moveIndex fccredit validNumber validOnlyFloatNumber'
            }
        ];
        var arr_hidden_field = ['c_account_id'];

        var form_type = $('#form_type').val();
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
            $(document).on('click','#is_deduction',function(){
            if($(this).prop('checked')) {
                $('#deduction_block').removeClass('pointerEventsNone')
            } else {
                $('#deduction_block').addClass('pointerEventsNone')
            }
        });

        $(document).on('keyup','#bill_amount',function(){
            funcSumGrnInvs();
        });
        $(document).on('click','#addData',function(){
            funcSumGrnInvs();
        });
        
        function funcSumGrnInvs(){
            /*
            var bill_amount = $('#bill_amount').val();
            if(valueEmpty(bill_amount)){
                bill_amount = 0;
            }
            var tbody = $('#grnVoucherDtls').find('tbody.erp_form__grid_body');
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
            var credit = parseFloat(bill_amount) - (parseFloat(amount) + parseFloat(acc_credit));
            if(valueEmpty(credit)){
                credit = 0;
            }
            $('#AccForm').find('#voucher_credit').val(parseFloat(credit).toFixed(3));
            $('#AccForm').find('#voucher_fc_credit').val(parseFloat(credit).toFixed(3));
            rem = $('#remaining').val(parseFloat(credit).toFixed(3));
            */
        }
    </script>
    <script src="{{ asset('js/pages/js/common/add-row-repeated-rsp.js?v='.time()) }}" type="text/javascript"></script>

@endsection

