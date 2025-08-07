@extends('layouts.layout')
@section('title', 'Bank Voucher')

@section('pageCSS')
@endsection
@section('content')
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $voucher_no = $data['voucher_no'];
                $date =  date('d-m-Y');
                $type = $data['type'];
                $voucher_bill = [];
            }
            if($case == 'edit'){
                $type = $data['type'];
                $id = $data['current']->voucher_id;
                $voucher_no= $data['current']->voucher_no;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->voucher_date))));
                $currency_id = $data['current']->currency_id;
                $exchange_rate = $data['current']->voucher_exchange_rate;
                $cash_type = $data['current']->chart_code;
                $narration = $data['current']->voucher_descrip;
                $saleman = $data['current']->saleman_id;
                $payment_mode = $data['current']->voucher_payment_mode;
                $mode = $data['current']->voucher_mode_no;

                $notes = $data['current']->voucher_notes;
                $dtls = isset($data['dtl'])? $data['dtl'] :[];
                $voucher_bill = isset($data['current']->voucher_bill)? $data['current']->voucher_bill :[];
            }

            if($type == 'brv'){
                $InvHelp = 'SI';
            }else{
                $InvHelp = 'GRN';
            }
        $form_type = $type;
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    @if($type == 'brv')
        <form id="voucher_form" class="kt-form" method="post" action="{{action('Accounts\VoucherController@rvstore', [$type,isset($id)?$id:''])}}">
    @else
        <form id="voucher_form" class="kt-form" method="post" action="{{action('Accounts\VoucherController@pvstore', [$type,isset($id)?$id:''])}}">
    @endif
    @csrf
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
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                @if($type == 'brv')
                                    <label class="col-lg-5 erp-col-form-label">Receive in :<span class="required">*</span></label>
                                @else
                                    <label class="col-lg-5 erp-col-form-label">Payment Through:<span class="required">*</span></label>
                                @endif
                                <div class="col-lg-7">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm moveIndex kt-select2" id="kt_select2_1" name="cash_type">
                                            <option value="">Select</option>
                                            @php $cash_type = isset($cash_type)?$cash_type:''@endphp
                                            @foreach($data['acc_code'] as $acc_code)
                                                <option value="{{$acc_code->chart_code}}" {{$acc_code->chart_code==$cash_type?'selected':''}}>{{$acc_code->chart_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--<div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Narration:</label>
                                <div class="col-lg-8">
                                    <input type="text"  name="narration" value="{{isset($narration)?$narration:''}}" class="moveIndex form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>--}}
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
                        <div class="col-lg-12 text-right">
                            <div class="data_entry_header">
                                <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = ['Sr No','Account Code','Account Name','Narration','Budget',
                                                      'Payment Mode','Mode No','Mode Date',"{$InvHelp}",'Amount','VAT %','VAT','Net Amount'];
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
                                                <input readonly id="account_id" type="hidden" class="account_id form-control erp-form-control-sm">
                                                <input readonly id="budget_id" type="hidden" class="budget_id form-control erp-form-control-sm">
                                                <input readonly id="invoice_id" type="hidden" class="invoice_id form-control erp-form-control-sm">
                                                <input readonly id="budget_branch_id" type="hidden" class="budget_branch_id form-control erp-form-control-sm">
                                                <input readonly id="cheque_book_id" type="hidden" class="cheque_book_id form-control erp-form-control-sm">
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
                                            <div class="erp_form__grid_th_title">Budget</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget" type="text" class="budget_dscrp tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Payment Mode</div>
                                            <div class="erp_form__grid_th_input">
                                                <select class="form-control erp-form-control-sm tb_moveIndex" id="payment_mode">
                                                    <option value="">Select</option>
                                                    <option value="atm">ATM Transfer</option>
                                                    <option value="cheque" selected>Cheque</option>
                                                    <option value="online">Online Payment</option>
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
                                                <input id="mode_date" readonly value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}" type="text" class="c-date-p kt_datepicker_3 form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                {{$InvHelp}}
                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="invoice_code" type="text" class="invoice_code tb_moveIndex open_inline__help  form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','invoiceHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_credit" type="text" class="credit validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        {{-- <th scope="col">
                                            <div class="erp_form__grid_th_title">FC Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_fc_credit" type="text" class="fccredit validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th> --}}
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">VAT %</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="vat_perc" type="text" class="vatperc validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm" oninput="calculateAmounts()">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">VAT</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="vat_amt" type="text" class="vatamt validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm" oninput="calculateVAT()">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Net Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="net_amt" type="text" class="netamt validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm">
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
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][account_id]" data-id="account_id" value="{{$data->chart_account_id}}"  class="account_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][budget_id]" data-id="budget_id" value="{{$data->budget_id}}"  class="budget_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][invoice_id]" data-id="invoice_id" value="{{$data->voucher_invoice_id}}"  class="invoice_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][budget_branch_id]" data-id="budget_branch_id" value="{{$data->budget_branch_id}}"  class="budget_branch_id form-control erp-form-control-sm">
                                                    <input readonly type="hidden" name="pd[{{ $loop->iteration }}][cheque_book_id]" data-id="cheque_book_id" value=""  class="cheque_book_id form-control erp-form-control-sm">
                                                </td>
                                                <td><input type="text" data-id="account_code" name="pd[{{ $loop->iteration }}][account_code]" value="{{$data->accounts->chart_code ?? ''}}" title="{{$data->accounts->chart_code ?? ''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code open_inline__help tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="account_name" name="pd[{{ $loop->iteration }}][account_name]" value="{{$data->accounts->chart_name ?? ''}}" title="{{$data->accounts->chart_name ?? ''}}" class="acc_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="voucher_descrip" name="pd[{{ $loop->iteration }}][voucher_descrip]" value="{{$data->voucher_descrip}}" title="{{$data->voucher_descrip}}" class="tb_moveIndex form-control erp-form-control-sm" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][budget]" data-id="budget" value="{{isset($bgt_dsc)?$bgt_dsc:''}}" title="{{isset($bgt_dsc)?$bgt_dsc:''}}"   data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}" class="budget_dscrp open_inline__help tb_moveIndex form-control erp-form-control-sm"></td>
                                                <td>
                                                    <select class="form-control erp-form-control-sm tb_moveIndex" data-id="payment_mode" name="pd[{{ $loop->iteration }}][payment_mode]" title="{{$data->voucher_payment_mode}}">
                                                        <option value="0">Select</option>
                                                        <option value="atm" {{$data->voucher_payment_mode == 'atm'?'selected':''}}>ATM Transfer</option>
                                                        <option value="cheque" {{$data->voucher_payment_mode == 'cheque'?'selected':''}}>Cheque</option>
                                                        <option value="online" {{$data->voucher_payment_mode == 'online'?'selected':''}}>Online Payment</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" data-id="mode_no" name="pd[{{ $loop->iteration }}][mode_no]" value="{{$data->voucher_mode_no}}" title="{{$data->voucher_mode_no}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','chequebookHelp')}}" class="open_inline__help tb_moveIndex form-control erp-form-control-sm"></td>
                                                @php $mode_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data->voucher_mode_date)))); @endphp
                                                <td><input type="text" data-id="mode_date" name="pd[{{ $loop->iteration }}][mode_date]" value="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" title="{{($mode_date =='01-01-1970' || $mode_date =='')?'':$mode_date}}" class="tb_moveIndex form-control erp-form-control-sm kt_datepicker_3" /></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][invoice_code]" data-id="invoice_code" value="{{isset($data->voucher_invoice_code)?$data->voucher_invoice_code:''}}" title="{{isset($data->voucher_invoice_code)?$data->voucher_invoice_code:''}}"   data-url="{{action('Common\DataTableController@inlineHelpOpen','invoiceHelp')}}" class="invoice_code open-inline-help tb_moveIndex form-control erp-form-control-sm"></td>
                                                @if($type == 'brv')
                                                    @php $credit = $data->voucher_credit; $fc_credit = $data->voucher_fc_credit; @endphp
                                                @else
                                                    @php $credit = $data->voucher_debit; $fc_credit = $data->voucher_debit; @endphp
                                                @endif
                                                <td><input type="text" data-id="voucher_credit" name="pd[{{ $loop->iteration }}][voucher_credit]" value="{{number_format($credit,3)}}" title="{{$credit}}" class="tb_moveIndex credit form-control erp-form-control-sm validNumber" ></td>
                                                {{-- <td><input type="text" data-id="voucher_fc_credit" name="pd[{{ $loop->iteration }}][voucher_fc_credit]" value="{{number_format($fc_credit,3)}}" title="{{$fc_credit}}" class="tb_moveIndex fccredit form-control erp-form-control-sm validNumber" ></td> --}}
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][vat_perc]" data-id="vat_perc" value="{{isset($data->vat_perc)?$data->vat_perc:''}}" title="{{isset($data->vat_perc)?$data->vat_perc:''}}" class="tb_moveIndex vatperc form-control erp-form-control-sm validNumber" oninput="calculateAmountsGrid(this)"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][vat_amt]" data-id="vat_amt" value="{{isset($data->vat_amt)?$data->vat_amt:''}}" title="{{isset($data->vat_amt)?$data->vat_amt:''}}" class="tb_moveIndex vatamt form-control erp-form-control-sm validNumber" oninput="calculateVATGrid(this)"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][net_amt]" data-id="net_amt" value="{{isset($data->net_amt)?$data->net_amt:''}}" title="{{isset($data->net_amt)?$data->net_amt:''}}" class="tb_moveIndex netamt form-control erp-form-control-sm validNumber" ></td>
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
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_credit" ></span>
                                            <input id="tot_voucher_credit" name="tot_voucher_credit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td>
                                        {{-- <td class="voucher-total-amt align-middle">
                                            <span id="tot_fccredit" ></span>
                                            <input id="tot_voucher_fccredit" name="tot_voucher_fccredit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td> --}}
                                        <td></td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_vat" ></span>
                                            <input id="tot_vat_amt" name="tot_vat_amt" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_net" ></span>
                                            <input id="tot_net_amt" name="tot_net_amt" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <style>
                        .bill_list_block{
                            border: 1px solid #d6d6d6;
                            margin-bottom: 20px;
                            padding: 10px 0;
                        }
                        table#bill_list_data {
                            border-bottom: 2px solid #a5a5a5;
                        }
                        table#bill_list_data th {
                            border: 0px solid #cecece;
                            background: #ececec;
                            font-size: 12px;
                            font-weight: 500 !important;
                            text-align: center;
                            padding: 12px 3px !important;
                            font-family: Roboto;
                        }
                        table#bill_list_data td {
                            font-size: 12px;
                            font-weight: 400;
                            padding: 5px 3px !important;
                            /*border: 1px solid #ebedf2;*/
                        }
                        table#bill_list_data tr:nth-child(even)>td {
                            background: #fbfbfb;
                            border-bottom: 2px solid #dadada;
                        }
                        table#bill_list_data tr:nth-child(even)>td input {
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
                    {{--<div class="bill_list_block">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 style="padding-left: 10px">Bill List</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="bill_list_data" class="table">
                                    <thead>
                                        <tr>
                                            <th width="100px">Inv No.</th>
                                            <th width="100px">Inv Date</th>
                                            <th width="100px">Ref</th>
                                            <th width="100px">Inv Amount</th>
                                            <th width="100px">Balance Amount</th>
                                            <th width="100px">Curr Pay</th>
                                            <th width="100px">Net Balance</th>
                                            <th width="7px">Marked</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($voucher_bill) != 0 && isset($voucher_bill))
                                            @foreach($voucher_bill as $bill)
                                                <tr>
                                                    <td>
                                                        <div>{{$bill['voucher_document_code']}}</div>
                                                        <input value="{{$bill['voucher_document_code']}}" readonly type="hidden" name="bl[{{$loop->iteration}}][grn_code]" class="pd_bank_recon_input">
                                                        <input value="{{$bill['voucher_document_id']}}" readonly type="hidden" name="bl[{{$loop->iteration}}][grn_id]" class="pd_bank_recon_input">
                                                    </td>
                                                    @php $grn_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$bill['voucher_document_date'])))); @endphp
                                                    <td>
                                                        <div>{{($grn_date =='01-01-1970' || $grn_date == '')?'':$grn_date}}</div>
                                                        <input value="{{($grn_date =='01-01-1970' || $grn_date == '')?'':$grn_date}}" readonly type="hidden" name="bl[{{$loop->iteration}}][grn_date]" class="pd_bank_recon_input">
                                                    </td>
                                                    <td>
                                                        <div>{{$bill['voucher_document_ref']}}</div>
                                                        <input value="{{$bill['voucher_document_ref']}}" readonly type="hidden" name="bl[{{$loop->iteration}}][grn_bill_no]" class="pd_bank_recon_input">
                                                    </td>
                                                    <td>
                                                        <div class="text-right">{{number_format($bill['voucher_bill_amount'],3)}}</div>
                                                        <input value="{{number_format($bill['voucher_bill_amount'],3)}}" readonly type="hidden" name="bl[{{$loop->iteration}}][grn_amount]" class="grn_amount pd_bank_recon_input text-right">
                                                    </td>
                                                    <td>
                                                        <div class="text-right">{{number_format($bill['voucher_bill_bal_amount'],3)}}</div>
                                                        <input value="{{number_format($bill['voucher_bill_bal_amount'],3)}}" readonly type="hidden" name="bl[{{$loop->iteration}}][balance_amount]" class="balance_amount pd_bank_recon_input text-right">
                                                    </td>
                                                    <td>
                                                        <input value="{{number_format($bill['voucher_bill_rec_amount'],3)}}" type="text" name="bl[{{$loop->iteration}}][curr_pay]" class="curr_pay pd_bank_recon_input_open text-right validNumber validOnlyFloatNumber">
                                                    </td>
                                                    @php
                                                        $net_balance = (float)$bill['voucher_bill_bal_amount'] - (float)$bill['voucher_bill_rec_amount'];
                                                    @endphp
                                                    <td>
                                                        <div class="net_balance_txt text-right">{{number_format($net_balance,3)}}</div>
                                                        <input value="{{number_format($net_balance,3)}}" readonly type="hidden" name="bl[{{$loop->iteration}}][net_balance]" class="net_balance pd_bank_recon_input text-right">
                                                    </td>
                                                    <td class="text-center">
                                                        <label class="kt-radio kt-radio--bold kt-radio--success" style="left: 7px;">
                                                            <input type="checkbox" class="marked" name="bl[{{$loop->iteration}}][marked]" {{$bill['voucher_bill_marke']==1?'checked':""}}>
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <td colspan="8">......</td>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9"></div>
                            <div class="col-lg-3">
                                <div class="row">
                                    <label for="" class="col-lg-6 erp-col-form-label">Total: </label>
                                    <div class="col-lg-6">
                                        <input name="total_curr_pay" class="total_curr_pay form-control erp-form-control-sm text-right" type="text" readonly value="0.000" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>--}}
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
        var var_form_name = 'bank_voucher';
    </script>
    <script>
        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
        var chequebookHelpUrl = "{{url('/common/help-open/chequebookHelp')}}";
        var budgetHelpUrl = "{{url('/common/inline-help/budgetHelp')}}";
        var invoiceHelp = "{{url('/common/inline-help/invoiceHelp')}}";
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
                'id':'budget',
                'fieldClass':'budget_dscrp open_inline__help tb_moveIndex',
                'data-url' : budgetHelpUrl
            },
            {
                'id':'payment_mode',
                'fieldClass':'tb_moveIndex',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'mode_no',
                'fieldClass':'open_inline__help tb_moveIndex',
                'data-url' : chequebookHelpUrl
            },
            {
                'id':'mode_date',
                'fieldClass':'c-date-p kt_datepicker_3 tb_moveIndex'
            },
            {
                'id':'invoice_code',
                'fieldClass':'invoice_code open_inline__help tb_moveIndex',
                'data-url' : invoiceHelp
            },
            {
                'id':'voucher_credit',
                'fieldClass':'tb_moveIndex credit validNumber validOnlyFloatNumber'
            },
            {
                'id':'vat_perc',
                'fieldClass':'tb_moveIndex vatperc validNumber validOnlyFloatNumber'
            },
            {
                'id':'vat_amt',
                'fieldClass':'tb_moveIndex vatamt validNumber validOnlyFloatNumber'
            },
            {
                'id':'net_amt',
                'fieldClass':'tb_moveIndex netamt validNumber validOnlyFloatNumber'
            }
        ];
        var arr_hidden_field = ['account_id','budget_id','budget_branch_id','cheque_book_id','invoice_id'];

        var form_type = $('#form_type').val();
        /*if(form_type == 'brv' || form_type == 'bpv'){
            var len = $('#AccForm>tbody.erp_form__grid_body>tr').length;
            if(len >= 1){
                $('.erp_form__grid>thead>tr').find('input').attr('disabled',true);
                $('.erp_form__grid>thead>tr').find('select').addClass('pointerEventsNone');
                $('.erp_form__grid>thead>tr').find('#addData').attr('disabled',true);
            }
        }*/

        $('#addData').click(function(){
            var account_id = $(this).parents('tr').find('#account_id').val();
            if(account_id != "" || account_id != undefined || account_id != null){
                var formData = {
                    account_id : account_id,
                }
                var url = '{{action('Accounts\VoucherController@getBillListdata')}}';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : "POST",
                    url         :  url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        if(response['status'] == 'success'){
                            var data = response['data']['items'];
                            var total_curr_pay = $('form').find('.total_curr_pay');;
                            var len = data.length;
                            var tbody = $('#bill_list_data>tbody');
                            var rows = '';
                            for(var i=0;i<len;i++){
                                var td = '';
                                var index = i+1;
                                var v_date = new Date(data[i]['grn_date']).getDate();
                                var v_month = new Date(data[i]['grn_date']).getMonth();
                                var v_year = new Date(data[i]['grn_date']).getFullYear();
                                v_date = (parseInt(v_date) < 10)?'0'+v_date:v_date;
                                v_month = parseInt(v_month) + 1;
                                v_month = (v_month < 10)?'0'+v_month:v_month;
                                var grn_date = v_date +'-'+ v_month +'-'+ v_year;
                                td += '<td>' +
                                    '<div>'+data[i]['grn_code']+'</div>' +
                                    '<input value="'+data[i]['grn_code']+'" readonly type="hidden" name="bl['+index+'][grn_code]" class="pd_bank_recon_input">' +
                                    '<input value="'+data[i]['grn_id']+'" readonly type="hidden" name="bl['+index+'][grn_id]" class="pd_bank_recon_input">' +
                                    '</td>';
                                td += '<td>' +
                                    '<div>'+grn_date+'</div>' +
                                    '<input value="'+grn_date+'" readonly type="hidden" name="bl['+index+'][grn_date]" class="pd_bank_recon_input">' +
                                    '</td>';
                                td += '<td>' +
                                    '<div>'+data[i]['grn_bill_no']+'</div>' +
                                    '<input value="'+data[i]['grn_bill_no']+'" readonly type="hidden" name="bl['+index+'][grn_bill_no]" class="pd_bank_recon_input">' +
                                    '</td>';
                                if(data[i]['grn_amount'] == 0 || data[i]['grn_amount'] == null || data[i]['grn_amount'] == ""){
                                    var grn_amount = 0;
                                }else{
                                    var grn_amount = data[i]['grn_amount'];
                                }
                                td += '<td>' +
                                    '<div class="text-right">'+parseFloat(grn_amount).toFixed(3)+'</div>' +
                                    '<input value="'+parseFloat(grn_amount).toFixed(3)+'" readonly type="hidden" name="bl['+index+'][grn_amount]" class="grn_amount pd_bank_recon_input text-right">' +
                                    '</td>';
                                if(data[i]['balance_amount'] == 0 || data[i]['balance_amount'] == null || data[i]['balance_amount'] == ""){
                                    var balance_amount = 0;
                                }else{
                                    var balance_amount = data[i]['balance_amount'];
                                }
                                td += '<td>' +
                                    '<div class="text-right">'+parseFloat(balance_amount).toFixed(3)+'</div>' +
                                    '<input value="'+parseFloat(balance_amount).toFixed(3)+'" readonly type="hidden" name="bl['+index+'][balance_amount]" class="balance_amount pd_bank_recon_input text-right">' +
                                    '</td>';
                                td += '<td>' +
                                    '<input value="'+parseFloat(0).toFixed(3)+'" type="text" name="bl['+index+'][curr_pay]" class="curr_pay pd_bank_recon_input_open text-right validNumber validOnlyFloatNumber">' +
                                    '</td>';

                                var net_balance = parseFloat(balance_amount) - parseFloat(0);
                                td += '<td>' +
                                    '<div class="net_balance_txt text-right">'+parseFloat(net_balance).toFixed(3)+'</div>' +
                                    '<input value="'+parseFloat(net_balance).toFixed(3)+'" readonly type="hidden" name="bl['+index+'][net_balance]" class="net_balance pd_bank_recon_input text-right">' +
                                    '</td>';

                                td += '<td class="text-center"><label class="kt-radio kt-radio--bold kt-radio--success" style="left: 7px;">' +
                                    '<input type="checkbox" class="marked" name="bl['+index+'][marked]" >'+
                                    '<span></span></label></td>';
                                var tr = '<tr>'+td+'</tr>';

                                if(data[i]['balance_amount'] != 0){
                                    rows += tr;
                                }
                            }

                            if(len == 0 || rows == ''){
                                rows = '<tr><td colspan="8">No Data Found...</td></tr>'
                            }
                            tbody.html(rows);
                        }
                    },
                    error: function(response,status) {}
                });
            }
        });

        $(document).on('click','.marked',function(){
            var balance_amount  = $(this).parents('tr').find('.balance_amount').val();
            var curr_pay = $(this).parents('tr').find('.curr_pay');
            if(typeof(balance_amount) == "string"){
                balance_amount = balance_amount.replace(",", "");
            }
            if($(this).is(':checked') == true){
                curr_pay.val(parseFloat(balance_amount).toFixed(3));
            }else{
                curr_pay.val(parseFloat(0).toFixed(3));
            }
            var net_balance =  parseFloat(balance_amount) - parseFloat(curr_pay.val());
            $(this).parents('tr').find('.net_balance').val(parseFloat(net_balance).toFixed(3));
            $(this).parents('tr').find('.net_balance_txt').html(parseFloat(net_balance).toFixed(3));
        });

        $(document).on('keyup','.curr_pay',function(){
            var balance_amount  = $(this).parents('tr').find('.balance_amount').val();
            var curr_pay = $(this).val();
            var net_balance =  parseFloat(balance_amount) - parseFloat(curr_pay);
            $(this).parents('tr').find('.net_balance').val(parseFloat(net_balance).toFixed(3));
            $(this).parents('tr').find('.net_balance_txt').html(parseFloat(net_balance).toFixed(3));
        })

        setInterval(function(){
            if($('#bill_list_data>tbody>tr>td').length != 1){
                var total_curr_pay  = $('form').find('.total_curr_pay');
                var val_total = 0;
                $('#bill_list_data>tbody>tr').each(function() {
                    var check = $(this).find('td input.marked:checked').val();
                    var str = $(this).find('td .curr_pay').val();
                    if(typeof(str) == "string"){
                        str = str.replace(",", "");
                    }
                    if(check == 'on'){
                        val_total = parseFloat(val_total) + parseFloat(str);
                    }else{
                        val_total = parseFloat(val_total) - parseFloat(str);
                    }
                });
                total_curr_pay.val(parseFloat(val_total).toFixed(3));
            }
        },1000)
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection

