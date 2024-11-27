@extends('layouts.template')
@section('title', 'Sales Order')

@section('pageCSS')
@endsection

@section('content')
    <!--begin::Form-->
    @php
            $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){
                $code = $data['document_code'];
                $date =  date('d-m-Y');
                $user_id = Auth::user()->id;
                $customer_id = $data['customer']->customer_id;
                $customer_name = $data['customer']->customer_name;
            }
            if($case == 'edit'){
                $id = $data['current']->sales_order_id;
                $code = $data['current']->sales_order_code;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_order_date))));
                $credit_days = $data['current']->sales_order_credit_days;
                $payment_term = $data['current']->payment_term_id;
                $currency_id = $data['current']->currency_id;
                $exchange_rate = $data['current']->sales_order_exchange_rate;
                $customer_id = isset($data['current']->customer)?$data['current']->customer->customer_id:"";
                $customer_name = isset($data['current']->customer)?$data['current']->customer->customer_name:"";
                $delivery_id = $data['current']->sales_order_delivery_id;
                $type = $data['current']->sales_order_sales_type;
                $user_id = $data['current']->sales_order_sales_man;
                $payment_mode = $data['current']->payment_mode_id;
                $address = $data['current']->sales_order_address;
                $remarks = $data['current']->sales_order_remarks;
                $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
                $expense_dtls = isset($data['current']->expense)? $data['current']->expense :[];
            }
    @endphp
    <form id="sales_order_form" class="kt-form" method="post" action="{{ action('Sales\SalesOrderController@store', isset($id)?$id:"") }}">
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
                                            {{isset($code)?$code:""}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" name="sales_order_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                                    <label class="col-lg-6 erp-col-form-label">Payment Terms:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group erp-select2-sm">
                                            <select name="payment_term_id" id="payment_term_id" class="moveIndex kt-select2 form-control erp-form-control-sm">
                                                <option value="0">Select</option>
                                                @foreach($data['payment_terms'] as $paymentTerm)
                                                    @php $select_payment_terms = isset($payment_term)?$payment_term:0; @endphp
                                                    <option value="{{$paymentTerm->payment_term_id}}" {{$paymentTerm->payment_term_id == $select_payment_terms ?"selected":""}}>{{$paymentTerm->payment_term_name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append" style="width: 33%;">
                                                <input type="text" id="sales_order_credit_days" name="sales_order_credit_days" value="{{isset($credit_days)?$credit_days:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
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
                                            <select class="moveIndex form-control erp-form-control-sm kt-select2 currency" id="currency_id" name="currency_id">
                                                <option value="0">Select</option>
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
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Customer:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                            </div>
                                            <input type="text" value="{{isset($customer_name)?$customer_name:""}}" title="{{isset($customer_name)?$customer_name:""}}" id="customer_name" data-url="{{action('Common\DataTableController@helpOpen','customerHelp')}}" name="customer_name" class="form-control erp-form-control-sm open_modal moveIndex OnlyEnterAllow" placeholder="Enter here">
                                            <input type="hidden" value="{{isset($customer_id)?$customer_id:""}}" id="customer_id" name="customer_id">
                                            <div class="input-group-append">
                                                <span class="input-group-text btn-open-modal">
                                                   <i class="la la-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Delivery No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="sales_order_delivery_id" name="sales_order_delivery_id" value="{{isset($delivery_id)?$delivery_id:''}}" class="moveIndex form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Exchange Rate:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Salesman:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2" id="sales_order_sales_man" name="sales_order_sales_man">
                                                <option value="0">Select</option>
                                                @php $select_user = isset($user_id)?$user_id:""; @endphp
                                                @foreach($data['users'] as $users)
                                                    <option value="{{$users->id}}" {{$users->id == $select_user?"selected":""}}>{{$users->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Sales Type:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2" id="sales_order_sales_type" name="sales_order_sales_type">
                                                @php $select_type = isset($type)?$type:""; @endphp
                                                <option value="0">Select</option>
                                                <option value="cash" {{$select_type == "cash"?"selected":""}} selected>Cash</option>
                                                <option value="credit" {{$select_type == "credit"?"selected":""}}>Credit</option>
                                                <option value="bankpayment" {{$select_type == "bankpayment"?"selected":""}}>Bank Payment</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Payment Mode:</label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2" id="payment_mode_id" name="payment_mode_id">
                                                <option value="0">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-block" style="overflow: auto;">
                            <table id="salesOrderForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                                <thead>
                                <tr>
                                    <th width="3%">Sr No</th>
                                    <th width="7%">Barcode code</th>
                                    <th width="10%">Product Name</th>
                                    <th width="3%">UOM</th>
                                    <th width="3%">Packing</th>
                                    <th width="3%">Qty</th>
                                    <th width="4%">FOC Qty</th>
                                    <th width="5%">FC Rate</th>
                                    <th width="3%">Rate</th>
                                    <th width="4%">Amount</th>
                                    <th width="4%">Disc %</th>
                                    <th width="4%">Disc Amt</th>
                                    <th width="4%">VAT%</th>
                                    <th width="4%">Vat Amt</th>
                                    <th width="5%">Gross Amt</th>
                                    <th width="1%">Action</th>
                                </tr>
                                <tr id="dataEntryForm">
                                    <td><input readonly id="sr_no" type="text" class="form-control erp-form-control-sm">
                                        <input readonly type="hidden" id="product_id" class="product_id form-control erp-form-control-sm">
                                        <input readonly type="hidden" id="uom_id" class="uom_id form-control erp-form-control-sm">
                                        <input readonly type="hidden" id="product_barcode_id" class="product_barcode_id form-control erp-form-control-sm">
                                    </td>
                                    <td><input id="barcode" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="open-inline-help pd_barcode moveIndex form-control erp-form-control-sm" autocomplete="off"></td>
                                    <td><input readonly id="product_name" type="text" class="pd_product_name form-control erp-form-control-sm"></td>
                                    <td>
                                        <select class="pd_uom moveIndex form-control erp-form-control-sm" id="uom">
                                            <option value="">Select</option>
                                        </select>
                                    </td>
                                    <td><input readonly id="packing" type="text" class="pd_packing form-control erp-form-control-sm"></td>
                                    <td><input id="quantity" type="text" class="moveIndex tblGridCal_qty form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                                    <td><input readonly id="foc_qty" type="text" class="form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly id="fc_rate" type="text" class="fc_rate form-control erp-form-control-sm validNumber"></td>
                                    <td><input id="rate" type="text" class="moveIndex tblGridCal_rate form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly id="amount" type="text" class="tblGridCal_amount form-control erp-form-control-sm validNumber"></td>
                                    <td><input id="discount" type="text" class="moveIndex tblGridCal_discount form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly id="discount_val" type="text" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber"></td>
                                    <td><input  id="vat_perc" type="text" class="moveIndex tblGridCal_vat_perc form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly id="vat_val" type="text" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly id="gross_amount" type="text" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber"></td>
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
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sales_order_dtl_id]" data-id="sales_order_dtl_id" value="{{$dtl->sales_order_dtl_id}}" class="sales_order_dtl_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][barcode]" data-id="barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" value="{{isset($dtl->barcode->product_barcode_barcode)?$dtl->barcode->product_barcode_barcode:''}}" title="{{isset($dtl->barcode->product_barcode_barcode)?$dtl->barcode->product_barcode_barcode:''}}" class="open-inline-help pd_barcode moveIndex form-control erp-form-control-sm" ></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][product_name]" data-id="product_name" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="form-control erp-form-control-sm" readonly></td>
                                                <td>
                                                    <select class="pd_uom moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][uom]" data-id="uom" title="{{ $dtl->uom->uom_name }}">
                                                        <option value="{{ $dtl->uom->uom_id }}">{{ $dtl->uom->uom_name }}</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][packing]" data-id="packing" value="{{isset($dtl->sales_order_dtl_packing)?$dtl->sales_order_dtl_packing:""}}" class="form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity" value="{{$dtl->sales_order_dtl_quantity}}" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty" value="{{$dtl->sales_order_dtl_foc_qty}}" class="form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" data-id="fc_rate" value="{{$dtl->sales_order_dtl_fc_rate}}" class="fc_rate form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate" value="{{number_format($dtl->sales_order_dtl_rate,2)}}" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount" value="{{number_format($dtl->sales_order_dtl_amount,3)}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][discount]" data-id="discount" value="{{number_format($dtl->sales_order_dtl_disc_per,2)}}" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][discount_val]" data-id="discount_val" value="{{number_format($dtl->sales_order_dtl_disc_amount,3)}}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc" value="{{number_format($dtl->sales_order_dtl_vat_per,2)}}" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][vat_val]" data-id="vat_val" value="{{number_format($dtl->sales_order_dtl_vat_amount,3)}}" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount" value="{{number_format($dtl->sales_order_dtl_total_amount,3)}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                    </div>
                                                </td>
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
                                            <td><span class="t_gross_total t_total">0</span><input type="hidden" id="pro_tot"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-5">
                                <div class="row form-group-block" style="display:none;">
                                    <label class="col-lg-3 erp-col-form-label">Address:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" rows="2" id="sales_order_address" name="sales_order_address" class="moveIndex form-control erp-form-control-sm">{{isset($address)?$address:""}}</textarea>
                                    </div>
                                </div>
                                <div class="row form-group-block">
                                    <label class="col-lg-3 erp-col-form-label">Remarks:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" rows="4" id="sales_order_remarks" name="sales_order_remarks" class="moveIndex form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label class="col-lg-12 erp-col-form-label">Expense:</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group-block" style="overflow:auto; height:200px;">
                                            <table id="SalesAccForm" class="ErpFormsm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable" style="margin-top:0px;">
                                                <thead>
                                                    <tr>
                                                        <th width="10%">Sr No</th>
                                                        <th width="30%">Acc code</th>
                                                        <th width="35%">Acc Name</th>
                                                        <th width="20%">Amount</th>
                                                        <th width="5%">Action</th>
                                                    </tr>
                                                    <tr id="dataEntryFormsm">
                                                        <td><input  id="sr_no" type="text" class=" form-control erp-form-control-sm" readonly>
                                                            <input readonly id="account_id" type="hidden" class="acc_id form-control erp-form-control-sm">
                                                        </td>
                                                        <td><input  id="account_code" type="text" data-url="{{action('Common\DataTableController@helpOpen','accountsHelp')}}" class="acc_code open_js_modal masking moveIndexsm validNumber form-control erp-form-control-sm text-left" maxlength="12"></td>
                                                        <td><input  id="account_name" type="text" class="acc_name form-control erp-form-control-sm" readonly></td>
                                                        <td><input  id="expense_amount" type="text" class=" form-control erp-form-control-sm moveIndexsm validNumber"></td>
                                                        <td class="text-center">
                                                            <button type="button" id="addDatasm" class="moveIndexBtnsm moveIndexsm gridBtn btn btn-primary btn-sm">
                                                                <i class="la la-plus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody id="repeated_datasm">
                                                @if(isset($expense_dtls))
                                                    @foreach($expense_dtls as $expense)
                                                        <tr>
                                                            <td><input  type="text" name="pd[{{ $loop->iteration }}][sr_no]" value="{{ $loop->iteration }}" class=" form-control erp-form-control-sm" readonly>
                                                                <input readonly type="hidden" name="pdsm[{{ $loop->iteration }}][account_id]" value="{{ $expense->chart_account_id }}" data-id="account_id"  class="acc_id form-control erp-form-control-sm">
                                                                <input readonly type="hidden" name="pdsm[{{ $loop->iteration }}][sales_order_expense_id]" value="{{ $expense->sales_order_expense_id }}" data-id="sales_order_expense_id" class="sales_order_expense_id form-control erp-form-control-sm" readonly>
                                                            </td>
                                                            <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_code]" data-id="account_code" data-url="{{action('Common\DataTableController@helpOpen','accountsHelp')}}" value="{{ $expense->accounts->chart_code }}" class="acc_code open_js_modal masking moveIndexsm validNumber form-control erp-form-control-sm text-left" maxlength="12"></td>
                                                            <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_name]" data-id="account_name" value="{{ $expense->accounts->chart_name }}" class="acc_name form-control erp-form-control-sm " readonly></td>
                                                            <td><input  type="text" name="pdsm[{{ $loop->iteration }}][expense_amount]" data-id="expense_amount" value="{{ number_format($expense->sales_order_expense_amount,3) }}" class="expense_amount form-control erp-form-control-sm moveIndexsm validNumber"></td>
                                                            <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delDatasm"><i class="la la-trash"></i></button></div></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                                <tbody>
                                                    <tr height="25">
                                                        <td colspan="3" class="voucher-total-title align-middle">Total Expenses :</td>
                                                        <td class="voucher-total-amt align-middle">
                                                            <span id="tot_expenses" ></span>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-10 col-lg-2 text-right">
                                <table class="tableTotal" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><div class="t_total_label">NetTotal:</div></td>
                                            <td><span class="t_total" id="total_amountsm">0</span></td>
                                        </tr>
                                    </tbody>
                                </table>
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
    <script src="{{ asset('js/pages/js/sales_order.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        $(".expense_amount").keyup(function(){
            TotalExpenseAmount();
        });
        
    </script>
    <script>
        var accountsHelpUrl = "{{url('/common/help-open/accountsHelp')}}";
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'barcode',
                'fieldClass':'open-inline-help pd_barcode moveIndex',
                'require':true,
                'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'pd_product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'uom',
                'fieldClass':'pd_uom',
                'readonly':true,
                'type':'select'
            },
            {
                'id':'packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'foc_qty',
                'fieldClass':'moveIndex validNumber'
            },
            {
                'id':'fc_rate',
                'fieldClass':'fc_rate moveIndex validNumber'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate moveIndex validNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount validNumber',
                'readonly':true
            },
            {
                'id':'discount',
                'fieldClass':'tblGridCal_discount moveIndex validNumber'
            },
            {
                'id':'discount_val',
                'fieldClass':'tblGridCal_discount_amount moveIndex validNumber'
            },
            {
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc moveIndex validNumber'
            },
            {
                'id':'vat_val',
                'fieldClass':'tblGridCal_vat_amount moveIndex validNumber'
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['sales_order_dtl_id','product_id','product_barcode_id','uom_id',];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/product-inline-ajax.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-expense-row-repeated.js') }}" type="text/javascript"></script>
@endsection
