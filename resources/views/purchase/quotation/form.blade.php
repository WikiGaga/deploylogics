@extends('layouts.template')
@section('title', 'Quotation')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
            $menu_id = $data['menu_id'];
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $lpo_id = isset($data['current']->lpo)?$data['current']->lpo->lpo_id:"";
            $lpo_code = isset($data['current']->lpo)?$data['current']->lpo->lpo_code:"";
        }
    @endphp
    <!--begin::Form-->
    @php $id = isset($data['current']->quotation_id)?$data['current']->quotation_id:''; @endphp
    <form id="quotation_form" class="kt-form" method="post" action="{{ action('Purchase\QuotationController@store', $id) }}">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    @if(isset($data['id']))
                                        {{$data['current']->quotation_code}}
                                    @else
                                        {{$data['document_code']}}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            @if(isset($data['id']))
                                @php $date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->quotation_entry_date)))); @endphp
                            @else
                                @php $date =  date('d-m-Y'); @endphp
                            @endif
                            <label class="col-lg-6 erp-col-form-label">Document Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="quot_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{$date}}" id="kt_datepicker_3" />
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
                            <label class="col-lg-6 erp-col-form-label">LPO Generation No:</label>
                            <div class="col-lg-6">
                                <div class="erp_form___block" id="select_lpo">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                                </span>
                                        </div>
                                        <input type="text" value="{{isset($lpo_code)?$lpo_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','lpoPoQuotationHelp')}}" id="lpo_generation_no" name="lpo_generation_no" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="lpo_generation_no_id" name="lpo_generation_no_id" value="{{isset($lpo_id)?$lpo_id:''}}"/>
                                        <div class="input-group-append">
                                                <span class="input-group-text group-input-btn get-lpo-data" id="lpoGetData">
                                                Go
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Supplier:</label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                                </span>
                                        </div>
                                        <input type="text" value="{{isset($supplier_code)?$supplier_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" id="supplier_name" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{isset($supplier_id)?$supplier_id:''}}"/>
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
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Payment Terms:</label>
                            <div class="col-lg-6">
                                <div class="input-group erp-select2-sm">
                                    <select name="payment_terms" id="payment_terms" class="moveIndex kt-select2 form-control erp-form-control-sm">
                                        <option value="0">Select</option>
                                        @foreach($data['payment_terms'] as $payment_term)
                                            @php $payment_terms = isset($data['current']->quotation_payment_mode_id)?$data['current']->quotation_payment_mode_id:0; @endphp
                                            <option value="{{$payment_term->payment_term_id}}" {{$payment_term->payment_term_id == $payment_terms ?"selected":""}}>{{$payment_term->payment_term_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append" style="width: 33%;">
                                        <input type="text" id="payment_mode" name="payment_mode" value="{{isset($data['current']->quotation_credit_days)?$data['current']->quotation_credit_days:''}}" class="moveIndex form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row form-group-block">
                            <label class="col-lg-6 erp-col-form-label">Currency:</label>
                            <div class="col-lg-6 quotation_currency">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2" id="quotation_currency" name="quotation_currency">
                                        <option value="0">Select</option>
                                        @foreach($data['currency'] as $currency)
                                            @php $quotation_currency = isset($data['current']->quotation_currency_id)?$data['current']->quotation_currency_id:0; @endphp
                                            <option value="{{$currency->currency_id}}" {{$currency->currency_id == $quotation_currency ?"selected":""}}>{{$currency->currency_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Exchange Rate:</label>
                            <div class="col-lg-6">
                                <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($data['current']->quotation_exchange_rate)?$data['current']->quotation_exchange_rate:''}}" class="moveIndex form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-block" style="overflow: auto;">
                    <table id="quotaForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
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
                                <input readonly type="hidden" id="packing_id" class="packing_id form-control erp-form-control-sm">
                            </td>
                            <td><input id="barcode" type="text" class="productHelp moveIndex form-control erp-form-control-sm"></td>
                            <td><input readonly id="product_name" type="text" class="product_name form-control erp-form-control-sm"></td>
                            <td><input readonly id="uom" type="text" class="uom_name form-control erp-form-control-sm"></td>
                            <td><input readonly id="packing" type="text" class="packing_name form-control erp-form-control-sm"></td>
                            <td><input id="quantity" type="text" class="moveIndex tblGridCal_qty form-control erp-form-control-sm validNumber validOnlyNumber"></td>
                            <td><input readonly id="foc_qty" type="text" class="form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="fc_rate" type="text" class="form-control erp-form-control-sm validNumber"></td>
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
                        @if(isset($data['current']->dtls))
                            @foreach($data['current']->dtls as $dtl)
                                <tr>
                                    <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                        <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                        @if(!isset($dtl->quotation_dtl_barcode))
                                            <input type="hidden" name="pd[{{$loop->iteration}}][lpo_id]" data-id="lpo_id" value="{{isset($data['current']->lpo)?$data['current']->lpo->lpo_id:""}}" class="lpo_id form-control erp-form-control-sm" readonly>
                                        @endif
                                        <input type="hidden" name="pd[{{$loop->iteration}}][q_dtl_id]" data-id="q_dtl_id" value="{{$dtl->quotation_dtl_id}}" class="q_dtl_id form-control erp-form-control-sm handle" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$dtl->product->product_id}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$dtl->uom->uom_id}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                        <input type="hidden" name="pd[{{$loop->iteration}}][packing_id]" data-id="packing_id" value="{{$dtl->packing->packing_id}}" class="packing_id form-control erp-form-control-sm handle" readonly>
                                    </td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][barcode]" value="{{$dtl->quotation_dtl_barcode}}" class="productHelp moveIndex form-control erp-form-control-sm" ></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][product_name]" value="{{$dtl->product->product_name}}" class="form-control erp-form-control-sm" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][uom]" value="{{$dtl->uom->uom_name}}" class="form-control erp-form-control-sm" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][packing]" value="{{$dtl->packing->packing_name}}" class="form-control erp-form-control-sm" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->quotation_dtl_quantity}}" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" value="{{$dtl->quotation_dtl_foc_quantity}}" class="form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" value="{{$dtl->quotation_dtl_fc_rate}}" class="form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][rate]" value="{{number_format($dtl->quotation_dtl_rate,2)}}" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][amount]" value="{{number_format($dtl->quotation_dtl_amount,3)}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][discount]" value="{{number_format($dtl->quotation_dtl_disc_percent,2)}}" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][discount_val]" value="{{number_format($dtl->quotation_dtl_disc_amount,3)}}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" value="{{number_format($dtl->quotation_dtl_vat_percent,2)}}" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][vat_val]" value="{{number_format($dtl->quotation_dtl_vat_amount,3)}}" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>
                                    <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" value="{{number_format($dtl->quotation_dtl_total_amount,3)}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                    <td class="text-center">
                                        @if(isset($dtl->quotation_dtl_barcode))
                                            <div class="btn-group btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                            </div>
                                        @endif
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
                        <div class="row form-group-block">
                            <label class="col-lg-3 erp-col-form-label">Notes:</label>
                            <div class="col-lg-9">
                                <textarea type="text" rows="2" id="quotation_notes" name="quotation_notes" class="moveIndex form-control erp-form-control-sm">{{isset($data['current']->quotation_remarks)?$data['current']->quotation_remarks:""}}</textarea>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <label class="col-lg-3 erp-col-form-label">Terms & Conditions:</label>
                            <div class="col-lg-9">
                                <textarea type="text" rows="2" id="quotation_terms" name="quotation_terms" class="moveIndex form-control erp-form-control-sm">{{isset($data['current']->quotation_terms)?$data['current']->quotation_terms:""}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-2">
                                <label class="col-lg-12 erp-col-form-label">Expense:</label>
                            </div>
                            <div class="col-lg-10">
                                <div class="form-group-block" style="overflow:auto; height:88px;">
                                    <table id="quotaaccForm" class="ErpFormsm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable" style="margin-top:0px;">
                                        <thead>
                                        <tr>
                                            <th width="10%">Sr No</th>
                                            <th width="30%">Acc code</th>
                                            <th width="35%">Acc Name</th>
                                            <th width="20%">Amount</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                        <tr id="dataEntryFormsm">
                                            <td><input  id="sr_no" type="text" class=" form-control erp-form-control-sm" readonly></td>
                                            <td><input  id="chart_code" type="text" class=" form-control erp-form-control-sm accountsHelp masking moveIndexsm validNumber text-left" maxlength="12"></td>
                                            <td><input  id="Acc_name" type="text" class=" form-control erp-form-control-sm " readonly></td>
                                            <td><input  id="Acc_amount" type="text" class=" form-control erp-form-control-sm moveIndexsm validNumber"></td>
                                            <td class="text-center">
                                                <button type="button" id="addDatasm" class="moveIndexBtnsm moveIndexsm gridBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </thead>
                                        <tbody id="repeated_datasm">
                                        @if(isset($data['current']->accounts))
                                            @foreach($data['current']->accounts as $account)
                                                <tr>
                                                    <td><input  type="text" name="pd[{{ $loop->iteration }}][sr_no]" value="{{ $loop->iteration }}" class=" form-control erp-form-control-sm" readonly>
                                                        <input  type="hidden" name="pd[{{ $loop->iteration }}][quotation_acc_id]" value="{{ $account->quotation_acc_id }}" data-id="quotation_acc_id" class="quotation_acc_id form-control erp-form-control-sm" readonly></td>
                                                    <td><input  type="text" name="pd[{{ $loop->iteration }}][chart_code]" value="{{ $account->quotation_acc_chart_code }}" data-id="chart_code" class=" form-control erp-form-control-sm accountsHelp masking moveIndexsm validNumber text-left" maxlength="12"></td>
                                                    <td><input  type="text" name="pd[{{ $loop->iteration }}][Acc_name]" value="{{ $account->quotation_acc_name }}" data-id="Acc_name" class=" form-control erp-form-control-sm " readonly></td>
                                                    <td><input  type="text" name="pd[{{ $loop->iteration }}][Acc_amount]" value="{{ $account->quotation_acc_amount }}" data-id="Acc_amount" class=" form-control erp-form-control-sm moveIndexsm validNumber"></td>
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
                                <td><div class="t_total_label">Net Total:</div></td>
                                <td><span class="t_total" id="total_amountsm">0</span></td>
                            </tr>
                            </tbody>
                        </table>
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
    <script src="{{ asset('js/pages/js/quotation.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/data-repeated-quotation.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>

        function TotalAmount()
        {
            var tot_amount = 0;
            var gtot_amount = 0;
            var pro_toal = 0;
            for(var i=0; $('#repeated_datasm>tr').length>i;i++){
                var amount = $('#repeated_datasm').find("tr:eq("+i+")").find("td:eq(3)>input").val();
                    amount = (amount == '')? 0 : amount;
                tot_amount = (parseFloat(tot_amount)+parseFloat(amount));

            }
             pro_toal = $("#pro_tot").val();
             gtot_amount = (parseFloat(tot_amount)+parseFloat(pro_toal));
            tot_amount= tot_amount.toFixed(3);
            gtot_amount= gtot_amount.toFixed(3);
            $("#total_amountsm").html(gtot_amount);
            $("#tot_expenses").html(tot_amount);
        }
        function moveIndexsm(){
            // 13 = enter
            $('.ErpFormsm .moveIndexsm').keydown(function (e) {
                var currentTd = $(this).parents('td').index();
                if(e.which === 13){
                    if($(this).hasClass('moveIndexBtnsm')){
                        $('tr#dataEntryFormsm').find('td:eq(1)>input').focus();
                    }else{
                        var index = $('.moveIndexsm').index(this) + 1;
                        $('.moveIndexsm').eq(index).focus();
                    }
                }
            });
        }

    </script>
    <script>
        var text_Fields = [
        // keys = id, fieldClass, readonly(boolean), require(boolean)
        {
            'id':'chart_code',
            'fieldClass':'accountsHelp masking moveIndexsm validNumber text-left',
        },
        {
            'id':'Acc_name',
            'readonly':true
        },
        {
            'id':'Acc_amount',
            'fieldClass':'moveIndexsm validNumber',
        }

        ];
    var hidden_fields = ['quotation_acc_id'];
    </script>
    <script src="{{ asset('js/pages/quotation-acc-data-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
