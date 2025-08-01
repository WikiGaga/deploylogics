@extends('layouts.layout')
@section('title', 'Cash Voucher')

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
                $cash_type = $data['current']->chart_code;
                $narration = $data['current']->voucher_descrip;
                $saleman = $data['current']->saleman_id;
                $payment_mode = $data['current']->voucher_payment_mode;
                $mode = $data['current']->voucher_mode_no;

                $notes = $data['current']->voucher_notes;
                $dtls = isset($data['dtl'])? $data['dtl'] :[];
            }

            if($type == 'crv'){
                $InvHelp = 'SI';
            }else{
                $InvHelp = 'GRN';
            }
            $form_type = $type;
    @endphp
    @permission($data['permission'])
     <!--begin::Form-->
     @if($type == 'crv' || $type == 'lfv')
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
                                <label class="col-lg-6 erp-col-form-label">Voucher Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="voucher_date" class="moveIndex form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
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
                                @if($type == 'crv')
                                    <label class="col-lg-6 erp-col-form-label">Receive in :<span class="required">*</span></label>
                                @else
                                    <label class="col-lg-6 erp-col-form-label">Payment Through:<span class="required">*</span></label>
                                @endif
                                <div class="col-lg-6">
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
                                        <select name="saleman_id" id="salesman" class="form-control erp-form-control-sm moveIndex kt-select2">
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
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Payment Mode :</label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control erp-form-control-sm moveIndex kt-select2" id="payment_mode" name="payment_mode">
                                            <option value="">Select</option>
                                            @php $payment_mode = isset($payment_mode)?$payment_mode:''@endphp
                                            <option value="cash" {{$payment_mode == 'cash'?'selected':''}} selected>Cash</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block row">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Mode#:</label>
                                <div class="col-lg-6">
                                    <input type="text"  name="mode_no" value="{{isset($mode)?$mode:''}}" class="moveIndex moveIndex2 form-control erp-form-control-sm">
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
                                        $headings = ['Sr No','Account Code','Account Name','Narration','Budget','Reference No',"{$InvHelp}",
                                                      'Amount','VAT %','VAT','Net Amount'];
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
                                            <div class="erp_form__grid_th_title">
                                                Budget
                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="budget" type="text" class="budget_dscrp tb_moveIndex open_inline__help  form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Reference No</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="voucher_chqno" type="text" class="tb_moveIndex form-control erp-form-control-sm">
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
                                                <input id="voucher_credit" type="text" class="credit validNumber validOnlyFloatNumber tb_moveIndex validNumber form-control erp-form-control-sm" oninput="calculateAmounts()">
                                            </div>
                                        </th>
                                        {{-- <th scope="col" style="display: none">
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
                                                </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][account_code]" data-id="account_code" value="{{$data->accounts->chart_code}}" title="{{$data->accounts->chart_code}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','accountsHelp')}}" class="acc_code tb_moveIndex open_inline__help  form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][account_name]" data-id="account_name" value="{{$data->accounts->chart_name}}" title="{{$data->accounts->chart_name}}" class="acc_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_descrip]" data-id="voucher_descrip" value="{{$data->voucher_descrip}}" title="{{$data->voucher_descrip}}" class="tb_moveIndex form-control erp-form-control-sm" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][budget]" data-id="budget" value="{{isset($bgt_dsc)?$bgt_dsc:''}}" title="{{isset($bgt_dsc)?$bgt_dsc:''}}"   data-url="{{action('Common\DataTableController@inlineHelpOpen','budgetHelp')}}" class="budget_dscrp open-inline-help tb_moveIndex form-control erp-form-control-sm"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_chqno]" data-id="voucher_chqno" value="{{$data->voucher_chqno}}" title="{{$data->voucher_chqno}}" class="tb_moveIndex form-control erp-form-control-sm" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][invoice_code]" data-id="invoice_code" value="{{isset($data->voucher_invoice_code)?$data->voucher_invoice_code:''}}" title="{{isset($data->voucher_invoice_code)?$data->voucher_invoice_code:''}}"   data-url="{{action('Common\DataTableController@inlineHelpOpen','invoiceHelp')}}" class="invoice_code open-inline-help tb_moveIndex form-control erp-form-control-sm"></td>
                                                @if($type == 'crv' || $type == 'lfv')
                                                    @php $credit = $data->voucher_credit; $fc_credit = $data->voucher_fc_credit; @endphp
                                                @else
                                                    @php $credit = $data->voucher_debit; $fc_credit = $data->voucher_debit; @endphp
                                                @endif
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][voucher_credit]" data-id="voucher_credit" value="{{number_format($credit,3)}}" title="{{$credit}}" class="tb_moveIndex credit form-control erp-form-control-sm validNumber" oninput="calculateAmountsGrid(this)"></td>
                                                {{-- <td style="display: none"><input type="text" name="pd[{{ $loop->iteration }}][voucher_fc_credit]" data-id="voucher_fc_credit" value="{{number_format($fc_credit,3)}}" title="{{$fc_credit}}" class="tb_moveIndex fccredit form-control erp-form-control-sm validNumber" ></td> --}}
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
                                        <td colspan="7" class="voucher-total-title align-middle">Total :</td>
                                        <td class="voucher-total-amt align-middle">
                                            <span id="tot_credit" ></span>
                                            <input id="tot_voucher_credit" name="tot_voucher_credit" type="hidden" class="form-control erp-form-control-sm text-right" readonly>
                                        </td>
                                        {{-- <td class="voucher-total-amt align-middle" style="display: none">
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
        var accountsHelpUrl = "{{url('/common/inline-help/accountsHelp')}}";
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
                'id':'voucher_chqno',
                'fieldClass':'tb_moveIndex'
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
        var arr_hidden_field = ['account_id','budget_id','budget_branch_id','invoice_id'];


    </script>
    {{---<script src="{{ asset('js/pages/js/account-row-repeated.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection

