@extends('layouts.layout')
@section('title', 'GRN')

@section('pageCSS')
    <link href="/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        .total-summary-label {
            padding-top: 3px;
            padding-bottom: 3px;
            margin-bottom: 0;
            font-size: 13px;
            line-height: 1.2;
            color: #ffffff;
            font-family: Roboto;
            font-weight: 400;
        }
        .color-dark-green{
            background: #1dbc66;
        }
    </style>
    <style>
        .grn_green {
            background: #4c9a2a !important;
            color: #fff !important;
        }

        .grn_red {
            background: #e9414e !important;
            color: #fff !important;
        }

        .grn_yellow {
            background: #f0e130 !important;
            color: #000 !important;
        }
        label   {color: #000;}


        .no_change_color{
            background: #f9f9f9 !important;
            color: #000 !important;
            pointer-events: none;
            touch-action: none;
            user-select: none;
        }
        .tp_increase_color{
            background: #FF9E79 !important;
            color: #000 !important;
            pointer-events: none;
            touch-action: none;
            user-select: none;
        }
        .tp_decrease_color{
            background: #20B1A9 !important;
            color: #000 !important;
            pointer-events: none;
            touch-action: none;
            user-select: none;
        }
        .new_rate_color{
            background: #B2C4DA !important;
            color: #000 !important;
            pointer-events: none;
            touch-action: none;
            user-select: none;
        }
        .foc_item_color{
            background: #8FBB8F !important;
            color: #000 !important;
        }
    </style>
@endsection

@section('content')

    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : '';
        if ($case == 'new') {
            $length = 0;
        }
        if ($case == 'edit') {
            $expense_dtls = isset($data['current']->grn_expense) ? $data['current']->grn_expense : [];
            $length = count($expense_dtls);
        }
        $grn_overall_discount = isset($data['current']->grn_overall_discount) ? $data['current']->grn_overall_discount : '';
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    @php $id = isset($data['current']->grn_id)?$data['current']->grn_id:'';  @endphp
    <form id="grn_form" class="kt-form" method="post" action="{{ action('Purchase\GRNController@store', $id) }}">
        @csrf
        <input type="hidden" value='{{ $form_type }}' id="form_type">
        <input type="hidden" id="voucher_id" value='{{$id}}' >
        <input type="hidden" id="case" value='{{$case}}' >
        <input type="hidden" id="user_central_rate" value='{{auth()->user()->central_rate}}' >
        @if($case == 'edit')
            <input type="hidden" id="form_id" value='{{$id}}' >
            <input type="hidden" id="menu_id" value="{{$data['menu_dtl_id']}}">
        @endif
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
                                        @if (isset($data['id']))
                                            {{ $data['current']->grn_code }}
                                        @else
                                            {{ $data['grn_code'] }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">Document Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        @if (isset($data['id']))
                                            @php $due_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->grn_date)))); @endphp
                                        @else
                                            @php $due_date =  date('d-m-Y'); @endphp
                                        @endif
                                        <input type="text" name="grn_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{ $due_date }}" id="kt_datepicker_3" autofocus />
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
                                <label class="col-lg-6 col-form-label">Vendor Name:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                                </span>
                                            </div>
                                            <input type="text" id="supplier_name" value="{{ isset($data['current']->supplier->supplier_name) ? $data['current']->supplier->supplier_name : '' }}" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'supplierHelp') }}" autocomplete="off" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                            <input type="hidden" id="supplier_id" name="supplier_id" value="{{ isset($data['current']->supplier->supplier_id) ? $data['current']->supplier->supplier_id : '' }}" />
                                            <div class="input-group-append">
                                                <span class="input-group-text btn-open-mob-help"  id="mobOpenInlineSupplierHelp">
                                                    <i class="la la-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">PO:</label>
                                <div class="col-lg-6">
                                    <div class="erp_form___block" id="select_po">
                                        @if ($case == 'new')
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'poHelp') }}"  value="{{ isset($data['current']->PO->purchase_order_code) ? $data['current']->PO->purchase_order_code : '' }}" id="purchase_order" name="purchase_order" class="open_inline__help form-control erp-form-control-sm moveIndex"  placeholder="Enter here">
                                                <input type="hidden" id="purchase_order_id" name="purchase_order_id" value="{{ isset($data['current']->PO->purchase_order_id) ? $data['current']->PO->purchase_order_id : '' }}" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                        <i class="la la-search"></i>
                                                    </span>
                                                    <span class="input-group-text group-input-btn" id="getPOData">
                                                        GO
                                                    </span>
                                                </div>
                                            </div>
                                            @else
                                            <div class="input-group open-modal-group">
                                                <input type="text" value="{{ isset($data['current']->PO->purchase_order_code) ? $data['current']->PO->purchase_order_code : '' }}" id="purchase_order" name="purchase_order" class="open_inline__help form-control erp-form-control-sm moveIndex"  placeholder="Enter here" readonly>
                                                <input type="hidden" id="purchase_order_id" name="purchase_order_id" value="{{ isset($data['current']->PO->purchase_order_id) ? $data['current']->PO->purchase_order_id : '' }}" />
                                            </div>
                                        @endif
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
                                        <select name="grn_ageing_term_id" id="grn_ageing_term_id"
                                                class="moveIndex kt-select2 width form-control erp-form-control-sm">
                                            <option value="0">Select</option>
                                            @foreach ($data['payment_terms'] as $payment_term)
                                                @php $payment_terms_id = isset($data['current']->grn_ageing_term_id)?$data['current']->grn_ageing_term_id:''; @endphp
                                                <option value="{{ $payment_term->payment_term_id }}" {{ $payment_terms_id == $payment_term->payment_term_id ? 'selected' : '' }}>{{ $payment_term->payment_term_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append" style="width: 33%;">
                                            <input type="text"
                                                   value="{{ isset($data['current']->grn_ageing_term_value) ? $data['current']->grn_ageing_term_value : '' }}" id="grn_ageing_term_value" name="grn_ageing_term_value" class="moveIndex form-control erp-form-control-sm validNumber">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">Currency:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm moveIndex currency" id="kt_select2_1" name="grn_currency">
                                            <option value="0">Select</option>
                                            @if (isset($data['current']->currency_id))
                                                @php
                                                    $grn_currency = isset($data['current']->currency_id) ? $data['current']->currency_id : 0;
                                                    $exchange_rate = '';
                                                @endphp
                                                @foreach ($data['currency'] as $currency)
                                                    <option value="{{ $currency->currency_id }}"
                                                        {{ $currency->currency_id == $grn_currency ? 'selected' : '' }}>
                                                        {{ $currency->currency_name }}</option>
                                                @endforeach
                                            @else
                                                @foreach ($data['currency'] as $currency)
                                                    @if ($currency->currency_default == '1')
                                                        @php $exchange_rate = $currency->currency_rate; @endphp
                                                    @endif
                                                    <option value="{{ $currency->currency_id }}"
                                                        {{ $currency->currency_default == '1' ? 'selected' : '' }}>
                                                        {{ $currency->currency_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">Exchange Rate:<span  class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" id="exchange_rate" name="exchange_rate" value="{{ isset($data['current']->grn_exchange_rate) ? $data['current']->grn_exchange_rate : $exchange_rate }}" class="form-control erp-form-control-sm moveIndex validNumber">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Payment Mode: </label>
                                <div class="col-lg-6 payment_mode">
                                    <div class="erp-select2">
                                        <select class="moveIndex form-control erp-form-control-sm kt-select2 payment_mode_id" id="payment_mode_id" name="payment_mode_id">
                                            <option value="0">Select</option>
                                            @if($case == 'edit')
                                                @php $payment_mode_id = isset($data['current']->payment_mode_id)?$data['current']->payment_mode_id:'';@endphp
                                                @foreach($data['payment_mode'] as $payment)
                                                    <option value="{{$payment->payment_mode_id}}" {{$payment->payment_mode_id==$payment_mode_id?'selected':''}}>{{$payment->payment_mode_name}}</option>
                                                @endforeach
                                            @else
                                                @foreach($data['payment_mode'] as $payment)
                                                    <option value="{{$payment->payment_mode_id}}">{{$payment->payment_mode_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">Store:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm moveIndex"
                                                id="kt-select2_validate" name="grn_store">
                                            @foreach ($data['store'] as $store)
                                                @if (isset($data['id']))
                                                    @php $grn_store = isset($data['current']->store_id)?$data['current']->store_id:0; @endphp
                                                @else
                                                    @php $grn_store = $store->store_default_value == 1 ? $store->store_id : 0 ; @endphp
                                                @endif
                                                <option value="{{ $store->store_id }}"
                                                    {{ $store->store_id == $grn_store ? 'selected' : '' }}>
                                                    {{ $store->store_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">Vendor Invoice NO: <span class="required">*</span>  </label>
                                <div class="col-lg-6">
                                    <input type="text" id="grn_bill_no" name="grn_bill_no" value="{{ isset($data['current']->grn_bill_no) ? $data['current']->grn_bill_no : '' }}" class="form-control erp-form-control-sm moveIndex">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <div class="input-group-prepend"><button type="button" class="btn btn-sm btn-label-danger btn-bold" id="tb_product_detail" style="padding: 0 15px;font-weight: 500;">Stock</button></div>
                                <input type="text" class="form-control erp-form-control-sm" value="0" id="current_product_stock" readonly style="font-size: 18px;background: rgba(253, 57, 122, 0.1);color: #fd397a;font-weight: 500;text-align: center;">
                                <div class="input-group-append"><button type="button" class="btn btn-sm btn-label-success btn-bold" id="tb_analysis_detail" style="padding: 0 15px;">TP Analysis</button></div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">Freight:</label>
                                <div class="col-lg-6">
                                    <input type="text" id="grn_freight" name="grn_freight" value="{{isset($data['current']->grn_freight)?$data['current']->grn_freight:''}}" class="form-control erp-form-control-sm moveIndex">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 col-form-label">Other Expenses:</label>
                                <div class="col-lg-6">
                                    <input type="text" id="grn_other_expenses" name="grn_other_expenses" value="{{isset($data['current']->grn_other_expense)?$data['current']->grn_other_expense:''}}" class="form-control erp-form-control-sm moveIndex">
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-2">
                            <button type="button" id="getListOfProduct" class="btn btn-sm btn-primary">Product help</button>
                            <div style="font-size: 9px;color: red;">(Click Here or Press F4)</div>
                        </div>
                        <div class="col-lg-4">
                            <span style="height: 10px;width: 10px;background: #FF9E79;display: inline-block;"></span> TP Increase
                            <span style="height: 10px;width: 10px;background: #20B1A9;display: inline-block;"></span> TP Decrease
                            <br>
                            <span style="height: 10px;width: 10px;background: #B2C4DA;display: inline-block;"></span> New Rate
                            <span style="height: 10px;width: 10px;background: #B2C4DA;display: inline-block;"></span> FOC Item
                        </div>
                        <div class="col-lg-6 text-right">
                            <div class="data_entry_header">
                                <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide
                                </div>
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = ['Sr No','Barcode','Product Name','Qty','Unit Price','Sale Rate','Sys Qty','M.R.P',
                                                    'Amount','Disc %','Disc Amt','After Disc Amt','Tax on','GST %','GST Amt','FED %',
                                                    'FED Amt','Disc on','Special Disc%','Special Disc Amt','Gross Amt','Net Amount',
                                                    'Net TP','Last TP','Vend Last TP','TP Difference','GP%','GP Amount','Notes',
                                                    'FC Rate','UOM','Packing'];
                                    @endphp
                                    <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                        @foreach ($headings as $key => $heading)
                                            <li>
                                                <label>
                                                    <input value="{{ $key }}" name="{{ trim($key) }}"
                                                           type="checkbox" checked> {{ $heading }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="kt-user-page-setting" style="display: inline-block">
                                    <button type="button" style="width: 30px;height: 30px;" title="Setting Save" data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon" id="pageUserSettingSave">
                                        <i class="la la-floppy-o"></i>
                                    </button>
                                </div>
                                <div class="kt-user-page-setting" style="display: inline-block">
                                    <button type="button" style="width: 30px;height: 30px;" title="Barcode Print" class="btn btn-success btn-elevate btn-circle btn-icon" id="generatePriceTags">
                                        <i class="la la-barcode"></i>
                                    </button>
                                </div>
                                {{--<div class="kt-user-page-setting" style="display: inline-block">
                                    <button type="button" style="width: 30px;height: 30px;" title="Shelf Barcode Print" data-toggle="tooltip" class="btn btn-brand btn-success btn-elevate btn-circle btn-icon" id="generateShelfPriceTags">
                                        <i class="la la-barcode"></i>
                                    </button>
                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table id="grn_barcode_data_table" class="table table_column_switch table_pit_list erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                                <input id="po_id" readonly type="hidden" class="po_id form-control erp-form-control-sm">
                                                <input id="central_rate" readonly type="hidden" class="central_rate form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">
                                                Barcode
                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                    <i class="la la-barcode"></i>
                                                </button>
                                            </div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Product Name</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Qty</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Unit Price</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="rate" type="text" class="tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Sale Rate</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sale_rate" type="text" class="tblGridCal_sale_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Sys Qty</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sys_qty" readonly type="text" class="tblGridCal_sys_qty validNumber validOnlyNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">M.R.P</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="mrp" type="text" class="tblGridCal_mrp tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="cost_amount" readonly type="text" class="tblGridCal_cost_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Disc %</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="dis_perc" type="text" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Disc Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="dis_amount" type="text" class="tblGridCal_discount_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">After Disc Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="after_dis_amount" type="text" readonly class="tblGridCal_after_discount_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Tax on</div>
                                            <div class="erp_form__grid_th_input">
                                                <select class="form-control erp-form-control-sm pd_tax_on" id="pd_tax_on">
                                                    @foreach($data['tax_on'] as $tax_on)
                                                        <option value="{{strtolower($tax_on->constants_value)}}">{{$tax_on->constants_value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">GST %</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="gst_perc" type="text" class="tblGridCal_gst_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">GST Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="gst_amount" type="text" class="tblGridCal_gst_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">FED %</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="fed_perc" type="text" class="tblGridCal_fed_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">FED Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="fed_amount" type="text" class="tblGridCal_fed_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Disc on</div>
                                            <div class="erp_form__grid_th_input">
                                                <select class="form-control erp-form-control-sm pd_disc" id="pd_disc">
                                                    @foreach($data['disc_on'] as $disc_on)
                                                        <option value="{{strtolower($disc_on->constants_value)}}">{{$disc_on->constants_value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Special Disc%</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="spec_disc_perc" type="text" class="tblGridCal_spec_disc_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Special Disc Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="spec_disc_amount" type="text" class="tblGridCal_spec_disc_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Gross Amt</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="gross_amount" readonly type="text" class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Net Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="net_amount" readonly type="text" class="tblGridCal_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Net TP</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="net_tp" type="text" readonly class="tblGridCal_net_tp validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Last TP</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="last_tp" type="text" readonly class="tblGridCal_last_tp validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Vend Last TP</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="vend_last_tp" readonly type="text" class="tblGridCal_vend_last_tp validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">TP Difference</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="tp_diff" type="text" readonly class="tblGridCal_tp_diff validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">GP%</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="gp_perc" readonly type="text" class="tblGridCal_gp_perc validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">GP Amount</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="gp_amount" readonly type="text" class="tblGridCal_gp_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th cope="col">
                                            <div class="erp_form__grid_th_title">Notes</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="remarks"  type="text" class="form-control erp-form-control-sm tb_moveIndex">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">FC Rate</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="fc_rate" type="text" class="tblGridCal_fc_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">PO No#</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="po_no" readonly type="text" class="po_no form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th cope="col">
                                            <div class="erp_form__grid_th_title">PO NetTP</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="po_net_tp" readonly type="text" class="po_net_tp form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Action</div>
                                            <div class="erp_form__grid_th_btn">
                                                <button type="button" class="add_data_new add_data tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                    @if (isset($data['current']->grn_dtl))
                                        @foreach ($data['current']->grn_dtl as $dtl)
                                            @php
                                                $rateColorClass = '';
                                                $rate = number_format($dtl->tbl_purc_grn_dtl_rate, 3);
                                                $po_rate = isset($dtl->grn_dtl_po_rate) && $dtl->grn_dtl_po_rate != null && $dtl->grn_dtl_po_rate != '' ? number_format($dtl->grn_dtl_po_rate, 3) : '';
                                                $netTp = $dtl->tbl_purc_grn_dtl_net_tp;
                                                $lastTp = $dtl->tbl_purc_grn_dtl_last_tp;

                                                if (in_array($lastTp,[0,"",null])) {
                                                    $rateColorClass = 'new_rate_color';
                                                }
                                                if ($netTp > $lastTp && !in_array($lastTp,[0,"",null])) {
                                                    $rateColorClass = 'tp_increase_color';
                                                }
                                                if ($netTp < $lastTp && !in_array($lastTp,[0,"",null])) {
                                                    $rateColorClass = 'tp_decrease_color';
                                                }
                                                if ($netTp == $lastTp) {
                                                    $rateColorClass = 'no_change_color';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{ $loop->iteration }}" name="pd[{{ $loop->iteration }}][sr_no]" data-id="sr_no" class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][purc_grn_dtl_id]" data-id="purc_grn_dtl_id" value="{{ $dtl->purc_grn_dtl_id }}"  class="purc_grn_dtl_id form-control erp-form-control-sm handle"readonly>
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][product_id]"   data-id="product_id"  value="{{ isset($dtl->product->product_id) ? $dtl->product->product_id : '' }}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][product_barcode_id]"  data-id="product_barcode_id" value="{{ isset($dtl->product_barcode_id) ? $dtl->product_barcode_id : '' }}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][uom_id]" data-id="uom_id" value="{{ isset($dtl->uom->uom_id) ? $dtl->uom->uom_id : '' }}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{ $loop->iteration }}][po_id]"  data-id="po_id" value="{{ isset($dtl->purchase_order_id)?$dtl->purchase_order_id:'' }}" class="po_id form-control erp-form-control-sm handle" readonly>
                                                    {{-- <input readonly type="hidden" data-id="grn_supplier_id" name="pd[{{ $loop->iteration }}][grn_supplier_id]" value="" class="grn_supplier_id form-control erp-form-control-sm"> --}}
                                                </td>
                                                <td><input type="text" data-id="pd_barcode"   name="pd[{{ $loop->iteration }}][pd_barcode]" value="{{ $dtl->barcode->product_barcode_barcode }}"  data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}"  class="pd_barcode tb_moveIndex form-control erp-form-control-sm"  readonly></td>
                                                <td><input type="text" data-id="product_name"   name="pd[{{ $loop->iteration }}][product_name]"  value="{{ isset($dtl->product->product_name) ? $dtl->product->product_name : '' }}" class="product_name form-control erp-form-control-sm" readonly> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][quantity]" data-id="quantity" value="{{ $dtl->tbl_purc_grn_dtl_quantity }}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][rate]" data-id="rate" value="{{ number_format($dtl->tbl_purc_grn_dtl_rate, 3, '.', '') }}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][sale_rate]" data-id="sale_rate" value="{{ number_format($dtl->tbl_purc_grn_dtl_sale_rate, 3, '.', '') }}" class="tblGridCal_sale_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][sys_qty]" data-id="sys_qty" value="{{ $dtl->tbl_purc_grn_dtl_sys_quantity }}" class="tblGridCal_sys_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][mrp]" data-id="mrp" value="{{ number_format($dtl->tbl_purc_grn_dtl_mrp, 3, '.', '') }}" class="tblGridCal_mrp tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][cost_amount]" data-id="cost_amount" value="{{ number_format($dtl->tbl_purc_grn_dtl_amount, 3, '.', '') }}" class="tblGridCal_cost_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber">  </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][dis_perc]"  data-id="dis_perc"  value="{{ number_format($dtl->tbl_purc_grn_dtl_disc_percent, 3, '.', '') }}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][dis_amount]"  data-id="dis_amount" value="{{ number_format($dtl->tbl_purc_grn_dtl_disc_amount, 3, '.', '') }}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][after_dis_amount]"  data-id="after_dis_amount" value="{{ number_format($dtl->tbl_purc_grn_dtl_after_dis_amount, 3, '.', '') }}" class="tblGridCal_after_discount_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td>
                                                    <select class="form-control erp-form-control-sm pd_tax_on" name="pd[{{ $loop->iteration }}][pd_tax_on]" data-id="pd_tax_on">
                                                        @foreach($data['tax_on'] as $tax_on)
                                                            <option value="{{strtolower($tax_on->constants_value)}}" {{strtolower($dtl->tbl_purc_grn_dtl_tax_on) == strtolower($tax_on->constants_value)?"selected":""}}>{{$tax_on->constants_value}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][gst_perc]" data-id="gst_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_percent, 4, '.', '') }}"  class="tblGridCal_gst_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][gst_amount]" data-id="gst_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_amount, 4, '.', '') }}" class="tblGridCal_gst_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][fed_perc]" data-id="fed_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_fed_percent, 4, '.', '') }}"  class="tblGridCal_fed_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][fed_amount]" data-id="fed_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_fed_amount, 4, '.', '') }}" class="tblGridCal_fed_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td>
                                                    <select class="form-control erp-form-control-sm pd_disc" name="pd[{{ $loop->iteration }}][pd_disc]"  data-id="pd_disc">
                                                        @foreach($data['disc_on'] as $disc_on)
                                                            <option value="{{strtolower($disc_on->constants_value)}}" {{strtolower($dtl->tbl_purc_grn_dtl_disc_on) == strtolower($disc_on->constants_value)?"selected":""}}>{{$disc_on->constants_value}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][spec_disc_perc]" data-id="spec_disc_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_spec_disc_perc, 3, '.', '') }}"  class="tblGridCal_spec_disc_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][spec_disc_amount]" data-id="spec_disc_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_spec_disc_amount, 3, '.', '') }}" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][gross_amount]" data-id="gross_amount" value="{{ number_format($dtl->tbl_purc_grn_dtl_gross_amount, 3, '.', '') }}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" ></td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][net_amount]" data-id="net_amount" value="{{ number_format($dtl->tbl_purc_grn_dtl_total_amount, 3, '.', '') }}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" ></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][net_tp]" data-id="net_tp" value="{{ number_format($dtl->tbl_purc_grn_dtl_net_tp, 3, '.', '') }}" class="{{ $rateColorClass }}  tblGridCal_net_tp form-control erp-form-control-sm validNumber"></td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][last_tp]" data-id="last_tp" value="{{ number_format($dtl->tbl_purc_grn_dtl_last_tp, 3, '.', '') }}" class="tblGridCal_last_tp form-control erp-form-control-sm validNumber" ></td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][vend_last_tp]" data-id="vend_last_tp" value="{{ number_format($dtl->tbl_purc_grn_dtl_vend_last_tp, 3, '.', '') }}" class="tblGridCal_vend_last_tp form-control erp-form-control-sm validNumber" ></td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][tp_diff]" data-id="tp_diff" value="{{ number_format($dtl->tbl_purc_grn_dtl_tp_diff, 3, '.', '') }}" class="tblGridCal_tp_diff form-control erp-form-control-sm validNumber"></td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][gp_perc]" data-id="gp_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_gp_perc, 3, '.', '') }}"  class="tblGridCal_gp_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][gp_amount]" data-id="gp_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_gp_amount, 3, '.', '') }}" class="tblGridCal_gp_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][remarks]" data-id="remarks"  value="{{ $dtl->tbl_purc_grn_dtl_remarks }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][fc_rate]" data-id="fc_rate" value="{{ number_format($dtl->tbl_purc_grn_dtl_fc_rate, 3, '.', '') }}" class="tblGridCal_fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][po_no]" data-id="po_no" value="{{ isset($dtl->purchase_order->purchase_order_code)?$dtl->purchase_order->purchase_order_code:"" }}" class="po_no tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][po_net_tp]" data-id="po_net_tp" value="{{ isset($dtl->po_net_tp)?$dtl->po_net_tp:'' }}" class="po_net_tp tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td>
                                                {{-- <td><input type="text"  name="pd[{{ $loop->iteration }}][grn_supplier_barcode]"  data-id="grn_supplier_barcode"  class="sup_barcode tb_moveIndex form-control erp-form-control-sm"   readonly></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][batch_no]" data-id="batch_no" value="{{ isset($dtl->tbl_purc_grn_dtl_batch_no) ? $dtl->tbl_purc_grn_dtl_batch_no : '' }}"  class="tb_moveIndex form-control erp-form-control-sm"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][foc_qty]" data-id="foc_qty" value="{{ $dtl->tbl_purc_grn_dtl_foc_quantity }}" class="tblGridCal_foc_qty tb_moveIndex form-control erp-form-control-sm validNumber"> </td>
                                                @php $prod_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_production_date)))); @endphp
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][production_date]" data-id="production_date" value="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}" title="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}" class="date_inputmask tb_moveIndex form-control form-control-sm" /> </td>
                                                @php $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_expiry_date)))); @endphp
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][expiry_date]"  data-id="expiry_date" value="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}" title="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}" class="date_inputmask tb_moveIndex form-control form-control-sm" /> </td> --}}
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tbody class="erp_form__grid_body_total">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="total_grid_qty">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_rate">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_sale_rate">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_sys_qty">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_mrp">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_cost_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_disc_perc">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_disc_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_after_disc_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td class="total_grid_gst_perc">
                                            <input value="0.0000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_gst_amount">
                                            <input value="0.0000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_fed_perc">
                                            <input value="0.0000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_fed_amount">
                                            <input value="0.0000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td class="total_grid_spec_disc_perc">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_spec_disc_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_gross_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_net_tp">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_last_tp">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_vend_last_tp">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_tp_diff">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_gp_perc">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_gp_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td class="total_grid_fc_rate">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @include('purchase.grn.summary_total')
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <label class="erp-col-form-label">Remarks:</label>
                            <textarea type="text" rows="8" id="grn_notes" name="grn_notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($data['current']->grn_remarks)?$data['current']->grn_remarks:''}}</textarea>
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
    {{-- <script src="{{ asset('js/multiple-form-submission-prevention.js') }}" defer></script> --}}
@endsection

@section('customJS')
    @include('partial_script.po_header_calc');
    <script src="{{ asset('js/pages/js/grn.js?v=2') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{ $case }}';
        $(".expense_amount").keyup(function() {
            TotalExpenseAmount();
        });
    </script>
    <script>
       /* $(document).on('click','#btn-update-entry',function(){
            alert('fgdfg');
            var case = $('#case').val();
             if(case == "new")
            {
                var purchase_order_id = $('#purchase_order_id').val();
                var formData = {
                    'purchase_order_id' : purchase_order_id,
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : 'POST',
                    url         : '/grn/check-po/'+ purchase_order_id,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response)
                    {
                        if(response != "")
                        {
                            toastr.error("This PO is Already Enter In GRN");
                        }
                    }
                })
            }
        });*/


        $(document).on('click','.add_data_new',function(e){
            var user_central_rate = $('#user_central_rate').val();
            var central_rate = $('#central_rate').val();

            /*if (central_rate == 1 && user_central_rate == 0) {
                toastr.error("Central Product is blocked.");
                var thix = $(this);
                var table = thix.parents('table');
                $(this).parents("tr").clear();
            }*/
        });

        var poXhr = true;
        $(document).on('click','#getPOData',function(){
            var purchase_order_id = $('#purchase_order_id').val();
            var code = $('#purchase_order').val().trim();
            var validate = true;
            if(valueEmpty(code) && valueEmpty(purchase_order_id)){
                toastr.error("PO No must be selected.");
                validate = false;
            }
            if(poXhr && validate){
                poXhr = false;
                $('body').addClass('pointerEventsNone');
                $.ajax({
                    type: 'GET',
                    url: '/grn/po/' + code,
                    success: function(response, data) {
                        //console.log(response);
                        //console.log(data);
                        if (response['status'] == 'success') {
                            // $('.erp_form__grid_body>tr>td:first-child').each(function() {
                            //     var purchase_order_id = $(this).find('input[data-id="purchase_order_id"]').val();
                            //     if (purchase_order_id) {
                            //         $(this).parents('tr').remove();
                            //     }
                            // });
                            // updateKeys();
                            var tr = '';
                            var total_length = $('.erp_form__grid_body>tr').length;

                            function notNullNo(val) {
                                if (val == null) {
                                    return "";
                                } else {
                                    return val = parseFloat(val).toFixed(3);
                                }
                            }
                            var tax_on_list = response['tax_on'];
                            var disc_on_list = response['disc_on'];
                            for (var p = 0; p < response['all'].length; p++) {
                                total_length++;
                                var row = response['all'][p];
                                var tax_on_options = "";
                                tax_on_list.forEach(function(item){
                                    var item_val = (item.constants_value).toLowerCase();
                                    var selected_val = notNull(row['purchase_order_dtltax_on']);
                                    var option_select = (item_val == selected_val)?"selected":"";
                                    tax_on_options += '<option value="'+item_val+'" '+option_select+'>'+item.constants_value+'</option>';
                                })
                                var disc_on_options = "";
                                disc_on_list.forEach(function(item){
                                    var item_val = (item.constants_value).toLowerCase();
                                    var selected_val = notNull(row['purchase_order_dtldisc_on']);
                                    var option_select = (item_val == selected_val)?"selected":"";
                                    disc_on_options += '<option value="'+item_val+'" '+option_select+'>'+item.constants_value+'</option>';
                                })
                                tr += '<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd['+ total_length +'][sr_no]"  data-id="sr_no" value="' + total_length + '" title="' + total_length + '" class="form-control sr_no erp-form-control-sm handle" readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +'][po_id]" data-id="po_id" value="' + row['purchase_order_id'] + '" class="po_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length + '][product_id]" data-id="product_id" value="' + notNull(row['product_id']) + '" class="product_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +'][product_barcode_id]" data-id="product_barcode_id" value="' +notNull(row['product_barcode_id']) +'"class="product_barcode_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +'][uom_id]" data-id="uom_id" value="' + notNull(row['uom_id']) +'"class="uom_id form-control erp-form-control-sm " readonly>' +
                                    '</td>' +
                                    // '<td><input type="text" name="pd[' + total_length +'][grn_supplier_barcode]" data-id="grn_supplier_barcode" value="" title="" class="sup_barcode form-control erp-form-control-sm moveIndex" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][pd_barcode]" data-id="pd_barcode" value="' + notNull(row['product_barcode_barcode']) + '" title="' + notNull( row['product_barcode_barcode']) + '"  class="pd_barcode form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][product_name]" data-id="product_name" value="' + notNull(row['product_name']) + '" title="' + notNull(row['product_name']) + '" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][quantity]" data-id="quantity" value="' + notNull(row['purchase_order_dtlquantity']) + '" title="' + notNull(row['purchase_order_dtlquantity']) +'" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][rate]" data-id="rate" value="' + notNullNo(row['purchase_order_dtlrate']) + '" title="' + notNullNo(row['purchase_order_dtlrate']) +'" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][sale_rate]" data-id="sale_rate" value="' + notNullNo(row['purchase_order_dtlsale_rate']) + '" title="' + notNullNo(row['purchase_order_dtlsale_rate']) +'" class="tblGridCal_sale_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][sys_qty]" data-id="sys_qty" value="' + notNull(row['purchase_order_dtlsys_quantity']) + '" title="' + notNull(row['purchase_order_dtlsys_quantity']) +'" class="tblGridCal_sys_qty form-control erp-form-control-sm validNumber validOnlyNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][mrp]" data-id="mrp" value="' + notNull(row['purchase_order_dtlmrp']) + '" title="' + notNull(row['purchase_order_dtlmrp']) +'" class="tblGridCal_mrp tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][cost_amount]" data-id="cost_amount" value="' + notNullNo(row['purchase_order_dtlamount']) + '" title="' + notNullNo(row['purchase_order_dtlamount']) +'" class="tblGridCal_cost_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][dis_perc]" data-id="dis_perc" value="' + notNullNo(row['purchase_order_dtldisc_percent']) + '" title="' + notNullNo(row['purchase_order_dtldisc_percent']) +'" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][dis_amount]" data-id="dis_amount" value="' + notNullNo(row['purchase_order_dtldisc_amount']) + '" title="' + notNullNo(row['purchase_order_dtldisc_amount']) +'" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][after_dis_amount]" data-id="after_dis_amount" value="' + notNullNo(row['purchase_order_dtlafter_dis_amount']) + '" title="' + notNullNo(row['purchase_order_dtlafter_dis_amount']) +'" class="tblGridCal_after_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td>' +
                                    '<select class="pd_tax_on form-control erp-form-control-sm" name="pd[' + total_length + '][pd_tax_on]" data-id="pd_tax_on">' +
                                    tax_on_options +
                                    '</select>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gst_perc]" data-id="gst_perc" value="' + notNullNo(row['purchase_order_dtlvat_percent']) + '" title="' + notNullNo(row['purchase_order_dtlvat_perc']) +'" class="tblGridCal_gst_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][gst_amount]" data-id="gst_amount" value="' + notNullNo(row['purchase_order_dtlvat_amount']) + '" title="' + notNullNo(row['purchase_order_dtlvat_amount']) +'" class="tblGridCal_gst_amount form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][fed_perc]" data-id="fed_perc" value="' + notNullNo(row['purchase_order_dtlfed_perc']) + '" title="' + notNullNo(row['purchase_order_dtlfed_perc']) +'" class="tblGridCal_fed_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][fed_amount]" data-id="fed_amount" value="' + notNullNo(row['purchase_order_dtlfed_amount']) + '" title="' + notNullNo(row['purchase_order_dtlfed_amount']) +'" class="tblGridCal_fed_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td>' +
                                    '<select class="pd_disc form-control erp-form-control-sm" name="pd[' + total_length + '][pd_disc]" data-id="pd_disc">' +
                                    disc_on_options +
                                    '</select>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][spec_disc_perc]" data-id="spec_disc_perc" value="' + notNullNo(row['purchase_order_dtlspec_disc_perc']) + '" title="' + notNullNo(row['purchase_order_dtlspec_disc_perc']) +'" class="tblGridCal_spec_disc_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][spec_disc_amount]" data-id="spec_disc_amount" value="' + notNullNo(row['purchase_order_dtlspec_disc_amount']) + '" title="' + notNullNo(row['purchase_order_dtlspec_disc_amount']) +'" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gross_amount]" data-id="gross_amount" value="' + notNullNo(row['purchase_order_dtlgross_amount']) + '" title="' + notNullNo(row['purchase_order_dtlgross_amount']) +'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][net_amount]" data-id="net_amount" value="' + notNullNo(row['purchase_order_dtltotal_amount']) + '" title="' + notNullNo(row['purchase_order_dtltotal_amount']) +'" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][net_tp]" data-id="net_tp" value="' + notNullNo(row['purchase_order_dtlnet_tp']) + '" title="' + notNullNo(row['purchase_order_dtlnet_tp']) +'" class="tblGridCal_net_tp form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][last_tp]" data-id="last_tp" value="' + notNullNo(row['purchase_order_dtllast_tp']) + '" title="' + notNullNo(row['purchase_order_dtllast_tp']) +'" class="tblGridCal_last_tp form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][vend_last_tp]" data-id="vend_last_tp" value="' + notNullNo(row['purchase_order_dtlvend_last_tp']) + '" title="' + notNullNo(row['purchase_order_dtlvend_last_tp']) +'" class="tblGridCal_vend_last_tp form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][tp_diff]" data-id="tp_diff" value="' + notNullNo(row['purchase_order_dtltp_diff']) + '" title="' + notNullNo(row['purchase_order_dtltp_diff']) +'" class="tblGridCal_tp_diff form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gp_perc]" data-id="gp_perc" value="' + notNullNo(row['purchase_order_dtlgp_perc']) + '" title="' + notNullNo(row['purchase_order_dtlgp_perc']) +'" class="tblGridCal_gp_perc form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gp_amount]" data-id="gp_amount" value="' + notNullNo(row['purchase_order_dtlgp_amount']) + '" title="' + notNullNo(row['purchase_order_dtlgp_amount']) +'" class="tblGridCal_gp_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][remarks]" data-id="remarks" value="' + notNullNo(row['purchase_order_dtlremarks']) + '" title="' + notNullNo(row['purchase_order_dtlremarks']) +'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][fc_rate]" data-id="fc_rate" value="' + notNullNo(row['purchase_order_dtlfc_rate']) + '" title="' + notNullNo(row['purchase_order_dtlfc_rate']) +'" class="tblGridCal_fc_rate form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][po_no]" data-id="po_no" value="' + row['purchase_order_code'] + '" class="po_no form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][po_net_tp]" data-id="po_net_tp" value="' + notNullNo(row['purchase_order_dtlnet_tp']) + '" class="po_net_tp form-control erp-form-control-sm" readonly></td>' +
                                    '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group">' +
                                    '<button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button>' +
                                    '</div></td>' +
                                    '</tr>';


                            }
                            $('.erp_form__grid_body').append(tr);

                            $('.erp_form__grid_body tr').each(function(){
                                var thix = $(this);
                                var product_id = thix.find('.product_id');
                                funcHeaderCalc(thix);
                                changeRateColor(product_id);
                            })
                            funcRowInit();
                            updateHiddenFields();

                        }else{
                            toastr.error("PO No is not correct.");
                        }
                        poXhr = true;
                        $('body').removeClass('pointerEventsNone');

                    },
                    error: function(response,status) {
                        poXhr = true;
                        $('body').removeClass('pointerEventsNone');
                    }
                });
            }
        })
        function selectPO() {
            $('#help_datatable_poHelp').on('click', 'tbody>tr', function(e) {
                var code = $(this).find('td[data-field="purchase_order_code"]').text();
                var po_id = $(this).find('td[data-field="purchase_order_id"]').text();
                var payment_term_id = $(this).find('td[data-field="payment_mode_id"]').text();
                var payment_term_days = $(this).find('td[data-field="purchase_order_credit_days"]').text();
                var supplier_name = $(this).find('td[data-field="supplier_name"]').text();
                var supplier_id = $(this).find('td[data-field="supplier_id"]').text();
                var currency_id = $(this).find('td[data-field="currency_id"]').text();
                var exchange_rate = $(this).find('td[data-field="purchase_order_exchange_rate"]').text();
                $('#grn_form').find('#purchase_order').val(code);
                $('#grn_form').find('#purchase_order_id').val(po_id);
                $("#grn_ageing_term_id").val(payment_term_id).trigger('change');
                $('#grn_form').find('#grn_ageing_term_value').val(payment_term_days);
                $('#grn_form').find('#supplier_name').val(supplier_name);
                $('#grn_form').find('#supplier_id').val(supplier_id);
                $('#grn_form').find(".currency").val(currency_id).trigger('change');
                $('#grn_form').find('#exchange_rate').val(exchange_rate);

                $.ajax({
                    type: 'GET',
                    url: '/grn/po/' + po_id,
                    success: function(response, data) {
                        //console.log(response);
                        //console.log(data);
                        if (data) {
                            // $('#repeated_data>tr>td:first-child').each(function() {
                            //     var purchase_order_id = $(this).find(
                            //         'input[data-id="purchase_order_id"]').val();
                            //     if (purchase_order_id) {
                            //         $(this).parents('tr').remove();
                            //     }
                            // });
                            // updateKeys();
                            var tr = '';
                            var total_length = $('#repeated_data>tr').length;

                            function notNullNo(val) {
                                if (val == null) {
                                    return "";
                                } else {
                                    return val = parseFloat(val).toFixed(3);
                                }
                            }
                            for (var p = 0; p < response['all'].length; p++) {
                                total_length++;
                                var row = response['all'][p];
                                tr += '<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd['+ total_length +'][sr_no]" data-id="sr_no" value="' + total_length + '" title="' + total_length + '" class="form-control sr_no erp-form-control-sm handle" readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +'][purc_grn_dtl_id]" data-id="purc_grn_dtl_id" value="" class="purc_grn_dtl_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +'][po_id]" data-id="po_id" value="' + po_id + '" class="po_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length + '][product_id]" data-id="product_id" value="' + notNull(row['product_id']) + '" class="product_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +'][uom_id]" data-id="uom_id" value="' + notNull(row['uom_id']) +'"class="uom_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +'][product_barcode_id]" data-id="product_barcode_id" value="' +notNull(row['product_barcode_id']) +'"class="product_barcode_id form-control erp-form-control-sm " readonly>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][pd_barcode]" data-id="pd_barcode" value="' + notNull(row['barcode']['product_barcode_barcode']) + '" title="' + notNull( row['barcode']['product_barcode_barcode']) + '" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}" class="pd_barcode form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][product_name]" data-id="product_name" value="' + notNull(row['product']['product_name']) + '" title="' + notNull(row['product']['product_name']) + '" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][quantity]" data-id="quantity" value="' + notNull(row['purchase_order_dtlquantity']) + '" title="' + notNull(row['purchase_order_dtlquantity']) +'" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][rate]" data-id="rate" value="' + notNullNo(row['purchase_order_dtlrate']) + '" title="' + notNullNo(row['purchase_order_dtlrate']) +'" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][sale_rate]" data-id="sale_rate" value="' + notNullNo(row['purchase_order_dtlsale_rate']) + '" title="' + notNullNo(row['purchase_order_dtlsale_rate']) +'" class="tblGridCal_sale_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][sys_qty]" data-id="sys_qty" value="' + notNull(row['purchase_order_dtlsys_quantity']) + '" title="' + notNull(row['purchase_order_dtlsys_quantity']) +'" class="tblGridCal_sys_qty form-control erp-form-control-sm validNumber validOnlyNumber" readonly ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][mrp]" data-id="mrp" value="' + notNull(row['purchase_order_dtlmrp']) + '" title="' + notNull(row['purchase_order_dtlmrp']) +'" class="tblGridCal_mrp tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][cost_amount]" data-id="cost_amount" value="' + notNullNo(row['purchase_order_dtlcost_amount']) + '" title="' + notNullNo(row['purchase_order_dtlcost_amount']) +'" class="tblGridCal_cost_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][dis_perc]" data-id="dis_perc" value="' + notNullNo(row['purchase_order_dtldisc_percent']) + '" title="' + notNullNo(row['purchase_order_dtldisc_percent']) +'" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][dis_amount]" data-id="dis_amount" value="' + notNullNo(row['purchase_order_dtldisc_amount']) + '" title="' + notNullNo(row['purchase_order_dtldisc_amount']) +'" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][after_dis_amount]" data-id="after_dis_amount" value="' + notNullNo(row['purchase_order_dtlafter_disc_amount']) + '" title="' + notNullNo(row['purchase_order_dtlafter_disc_amount']) +'" class="tblGridCal_after_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][pd_tax_on]" data-id="pd_tax_on" value="' + notNull(row['purchase_order_dtltax_on']) + '" title="' + notNull(row['purchase_order_dtltax_on']) +'" class="pd_tax_on form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gst_perc]" data-id="gst_perc" value="' + notNullNo(row['purchase_order_dtlgst_perc']) + '" title="' + notNullNo(row['purchase_order_dtlgst_perc']) +'" class="tblGridCal_gst_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][gst_amount]" data-id="gst_amount" value="' + notNullNo(row['purchase_order_dtlgst_amount']) + '" title="' + notNullNo(row['purchase_order_dtlgst_amount']) +'" class="tblGridCal_gst_amount form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][fed_perc]" data-id="fed_perc" value="' + notNullNo(row['purchase_order_dtlfed_perc']) + '" title="' + notNullNo(row['purchase_order_dtlfed_perc']) +'" class="tblGridCal_fed_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][fed_amount]" data-id="fed_amount" value="' + notNullNo(row['purchase_order_dtlfed_amount']) + '" title="' + notNullNo(row['purchase_order_dtlfed_amount']) +'" class="tblGridCal_fed_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][pd_disc]" data-id="pd_disc" value="' + notNull(row['purchase_order_dtldisc_on']) + '" title="' + notNull(row['purchase_order_dtldisc_on']) +'" class="pd_disc form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][spec_disc_perc]" data-id="spec_disc_perc" value="' + notNullNo(row['purchase_order_dtlspec_disc_perc']) + '" title="' + notNullNo(row['purchase_order_dtlspec_disc_perc']) +'" class="tblGridCal_spec_disc_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][spec_disc_amount]" data-id="spec_disc_amount" value="' + notNullNo(row['purchase_order_dtlspec_disc_amount']) + '" title="' + notNullNo(row['purchase_order_dtlspec_disc_amount']) +'" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gross_amount]" data-id="gross_amount" value="' + notNullNo(row['purchase_order_dtltotal_amount']) + '" title="' + notNullNo(row['purchase_order_dtltotal_amount']) +'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][net_amount]" data-id="net_amount" value="' + notNullNo(row['purchase_order_dtlnet_amount']) + '" title="' + notNullNo(row['purchase_order_dtlnet_amount']) +'" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][net_tp]" data-id="net_tp" value="' + notNullNo(row['purchase_order_dtlnet_tp']) + '" title="' + notNullNo(row['purchase_order_dtlnet_tp']) +'" class="tblGridCal_net_tp form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][last_tp]" data-id="last_tp" value="' + notNullNo(row['purchase_order_dtllast_tp']) + '" title="' + notNullNo(row['purchase_order_dtllast_tp']) +'" class="tblGridCal_last_tp form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][vend_last_tp]" data-id="vend_last_tp" value="' + notNullNo(row['purchase_order_dtlvend_last_tp']) + '" title="' + notNullNo(row['purchase_order_dtlvend_last_tp']) +'" class="tblGridCal_vend_last_tp form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][tp_diff]" data-id="tp_diff" value="' + notNullNo(row['purchase_order_dtltp_diff']) + '" title="' + notNullNo(row['purchase_order_dtltp_diff']) +'" class="tblGridCal_tp_diff form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gp_perc]" data-id="gp_perc" value="' + notNullNo(row['purchase_order_dtlgp_perc']) + '" title="' + notNullNo(row['purchase_order_dtlgp_perc']) +'" class="tblGridCal_gp_perc form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][gp_amount]" data-id="gp_amount" value="' + notNullNo(row['purchase_order_dtlgp_amount']) + '" title="' + notNullNo(row['purchase_order_dtlgp_amount']) +'" class="tblGridCal_gp_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][remarks]" data-id="remarks" value="' + notNullNo(row['purchase_order_dtlremarks']) + '" title="' + notNullNo(row['purchase_order_dtlremarks']) +'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +'][fc_rate]" data-id="fc_rate" value="' + notNullNo(row['purchase_order_dtlfc_rate']) + '" title="' + notNullNo(row['purchase_order_dtlfc_rate']) +'" class="tblGridCal_fc_rate form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][po_no]" data-id="po_no" value="' + row['purchase_order_code'] + '" class="po_no form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length + '][po_net_tp]" data-id="po_net_tp" value="' + notNullNo(row['purchase_order_dtlnet_tp']) + '" class="po_net_tp form-control erp-form-control-sm" readonly></td>' +
                                    '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group">' +
                                    '<button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button>' +
                                    '</div></td>' +
                                    '</tr>';
                            }
                            $('#repeated_data').append(tr);
                            addDataInit();
                            allCalcFunc();
                        }
                    }
                });
                closeModal();
            });
        };
    </script>
    <script>

        $('.expense_acc_table input.expense_perc').bind('click keyup', function() {
            var thix = $(this);
            var val = thix.val();
            var tr = thix.parents('tr');
            var t_gross_total = $('#pro_tot').val();
            if (t_gross_total != 0) {
                var value = (parseFloat(val) * parseFloat(t_gross_total)) / 100;
                if (value) {
                    tr.find('.expense_amount').val(value.toFixed(3));
                }
                TotalExpenseAmount()
            }
            if (val == 0) {
                tr.find('.expense_amount').val('');
            }
        });
        $('.expense_acc_table input.expense_amount').bind('click keyup', function() {
            var thix = $(this);
            var val = thix.val();
            var tr = thix.parents('tr');
            var t_gross_total = $('#pro_tot').val();
            if (t_gross_total != 0) {
                var value = (parseFloat(val) / parseFloat(t_gross_total)) * 100;
                if (value) {
                    tr.find('.expense_perc').val(value.toFixed(3));
                }
                TotalExpenseAmount()
            }
            if (val == 0) {
                tr.find('.expense_perc').val('');
            }
        });

        $(document).on('keyup', '.erp_form__grid_header input', function() {
            changeRateColor($(this));
            getRate($(this));
        })

        function getRate(thix)
        {
            var net_tp_field = thix.parents('tr').find('.tblGridCal_sale_rate');
            var sale_rate = net_tp_field.val();
            var net_tp = thix.parents('tr').find('.tblGridCal_net_tp').val();

            if(parseFloat(sale_rate) < parseFloat(net_tp)){
                toastr.warning("Sale Rate is less than Net TP.");
            }

        }
        function changeRateColor(thix) {
            var net_tp_field = thix.parents('tr').find('.tblGridCal_net_tp');
            var net_tp = net_tp_field.val();
            var last_tp = thix.parents('tr').find('.tblGridCal_last_tp').val();
            net_tp = parseFloat(net_tp);
            last_tp = parseFloat(last_tp);
           // console.log("TP: "+net_tp+" == "+last_tp);
            net_tp_field.removeClass('tp_increase_color tp_decrease_color new_rate_color foc_item_color no_change_color')
            if (net_tp > last_tp && !valueEmpty(last_tp)) {
                net_tp_field.addClass('tp_increase_color')
            }
            if (net_tp < last_tp && !valueEmpty(last_tp)) {
                net_tp_field.addClass('tp_decrease_color')
            }
            if (valueEmpty(last_tp)) {
                net_tp_field.addClass('new_rate_color')
            }
            if (net_tp == last_tp) {
                net_tp_field.addClass('no_change_color')
            }
        }

        function TotalExpenseAmount() {
            var tot_amount = 0;
            var gtot_amount = 0;
            var pro_toal = 0;
            for (var i = 0; $('#repeated_datasm>tr').length > i; i++) {
                var amount = $('#repeated_datasm').find("tr:eq(" + i + ")").find("td>input.expense_amount").val();
                amount = (amount == '' || amount == undefined) ? 0 : amount.replace(/,/g, '');
                if ($('#repeated_datasm').find("tr:eq(" + i + ")").find("td>input.expense_plus_minus").val() == '+') {
                    tot_amount = (parseFloat(tot_amount) + parseFloat(amount));
                } else {
                    tot_amount = (parseFloat(tot_amount) - parseFloat(amount));
                }

            }
            pro_toal = $("#pro_tot").val();
            gtot_amount = (parseFloat(tot_amount) + parseFloat(pro_toal));
            tot_amount = tot_amount.toFixed(3);
            gtot_amount = gtot_amount.toFixed(3);
            $("#total_amountsm").html(gtot_amount);
            $("#tot_expenses").html(tot_amount);
            $("#TotExpen").val(tot_amount);
            $("#TotalAmtSM").val(gtot_amount);
        }
        TotalExpenseAmount();

        //change sale rate
        $('.sale_rate_barcode').click(function() {
            var barcodeData = {};
            barcodeData.data = [];
            var sale_rate = $('.erp_form__grid>thead.erp_form__grid_header>tr').find('#product_barcode_id').val();
            if (sale_rate) {
                barcodeData.data.push(sale_rate);
            }
            if ($('.erp_form__grid>tbody.erp_form__grid_body>tr').length > 0) {
                $('.erp_form__grid>tbody.erp_form__grid_body>tr').each(function() {
                    var thix = $(this)
                    var barcode = thix.find('input[data-id="product_barcode_id"]').val();
                    barcodeData.data.push(barcode);
                });
            }
            if (barcodeData.data.length > 0) {
                var url = '/grn/barcode-sale-price';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    data: barcodeData,
                    success: function(response, data) {
                        if (response['status'] == 'success') {
                            toastr.success('Sale Rate Updated');
                            var products = response.product_barcode;
                            products.forEach(element => {
                                var parent = $('input[value="' + element.product_barcode_id +'"]').parents('tr');
                                var value = parseFloat(element.product_barcode_sale_rate_rate).toFixed(3);
                                parent.find('input[data-id="sale_rate"]').val(value);
                            });
                        }
                    },
                    error: function(response, status) {}
                });
            }
        });

        $(document).on('click','.btn-minus-selected-data', function(){
            $('#grn_form').find('.erp_form___block').find('#supplier_name').removeClass('readonly');
            $('#grn_form').find('.erp_form___block').find('#supplier_name').parents('.open-modal-group').removeClass('readonly');
        })
    </script>
    @yield('summary_total_pageJS')
    {{--<script src="{{ asset ('js/pages/js/add-row-repeated_new.js?v=2') }}" type="text/javascript"></script>--}}
    <script>
        function funcRequiredFieldsDatatable(){
            var tbl = $('table#grn_barcode_data_table');
            tbl.attr('data-prefix','pd');
            var theadTr = tbl.find('thead>tr');

            var requiredFields = ['product_id','product_barcode_id','pd_barcode','product_name'];
            requiredFields.forEach(function(item){
                if(!valueEmpty(theadTr.find('#'+item))){
                    theadTr.find('#'+item).attr('data-require',true);
                }
            })
            var msgFields = [
                {'id':'product_id', 'msg':'Barcode is required'},
                {'id':'product_barcode_id', 'msg':'Barcode is required'},
            ];
            for(var i=0;i<msgFields.length;i++){
                if(!valueEmpty(theadTr.find('#'+msgFields[i]['id']))){
                    theadTr.find('#'+msgFields[i]['id']).attr('data-msg',msgFields[i]['msg']);
                }
            }

            var readonlyFields = ['pd_barcode','product_name','sys_qty','cost_amount',
                'after_dis_amount','fed_amount','spec_disc_amount','gross_amount','net_amount',
                'net_tp','last_tp','vend_last_tp','gp_perc','gp_amount','po_no','po_net_tp','jkhjklhlhk'];
            readonlyFields.forEach(function(item){
                if(!valueEmpty(theadTr.find('#'+item))){
                    theadTr.find('#'+item).attr('data-readonly',true);
                }
            })

        }
        funcRequiredFieldsDatatable();
    </script>
    <script src="{{ asset('js/pages/js/common/add-row-repeated-rsp.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        var form_modal_type = 'grn';
    </script>
    @include('purchase.product_smart.product_modal_help.script')

    <script>
        function funSetProductCustomFilter(arr) {
            var len = arr['len'];
            var product = arr['product'];
            for (var i =0;i<len;i++){
                var row = product[i];
                var sale_rate = !valueEmpty(row['sale_rate']) ? parseFloat(row['sale_rate']).toFixed(3) : '';
                var cost_rate = !valueEmpty(row['cost_rate']) ? parseFloat(row['cost_rate']).toFixed(3) : '';
                var newTr = "<tr data-product_id='"+row['product_id']+"' data-barcode_id='"+row['product_barcode_id']+"'>";
                newTr += "<td>" +
                    "<input type='hidden' data-id='product_id' value='"+row['product_barcode_id']+"'>"+
                    "<input type='hidden' data-id='product_barcode_id' value='"+row['product_barcode_id']+"'>"+
                    "</td>";
                newTr += "<td class='group_item_name'>"+(!valueEmpty(row['group_item_name'])?row['group_item_name']:"")+"</td>";
                newTr += "<td class='barcode'>"+(!valueEmpty(row['product_barcode_barcode'])?row['product_barcode_barcode']:"")+"</td>";
                newTr += "<td class='product_name'>"+(!valueEmpty(row['product_name'])?row['product_name']:"")+"</td>";
                newTr += "<td class='text-right mrp'>"+(!valueEmpty(row['mrp'])?row['mrp']:"")+"</td>";
                newTr += "<td class='text-right sale_rate'>"+sale_rate+"</td>";
                newTr += "<td class='text-right cost_rate'>"+cost_rate+"</td>";
                newTr += "<td class='text-right trade_rate'>"+cost_rate+"</td>";
                newTr += "<td class='supplier_name'>"+(!valueEmpty(row['supplier_name'])?row['supplier_name']:"")+"</td>";
                newTr += "<td class='text-right supplier_rate'>"+(!valueEmpty(row['min_qty'])?row['min_qty']:"")+"</td>";
                newTr += "<td class='text-right supplier_tp'>"+(!valueEmpty(row['depth_qty'])?row['depth_qty']:"")+"</td>";
                newTr += '<td class="text-center">\n' +
                    '                            <div style="position: relative;top: -5px;">\n' +
                    '                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">\n' +
                    '                                    <input type="checkbox" class="addCheckedProduct" data-id="add_prod" >\n' +
                    '                                    <span></span>\n' +
                    '                                </label>\n' +
                    '                            </div></td>';
                newTr += "</tr>";

                $('table.table_pitModal').find('tbody.erp_form__grid_body').append(newTr);
            }
        }

        function funcAddSelectedProductToFormGrid(tr){
            var cloneTr = tr.clone();
            var table_pit_list = $('table.table_pit_list');
            var tr = table_pit_list.find('.erp_form__grid_header>tr:first-child');
            var barcode = $(cloneTr).find('.barcode').text();
            tr.find('#pd_barcode').val(barcode);

            var form_type = $('#form_type').val();
            var supplier_id = $('#supplier_id').val();
            var formData = {
                form_type : form_type,
                val : barcode,
            }
            if (!valueEmpty(supplier_id)) {
                formData.supplier_id = supplier_id;
            }
            initBarcode(13, tr, form_type, formData)

            closeModal();
        }

        $('#tb_analysis_detail').click(function(){
            var pd_barcode = $('#pd_barcode').val();
            var product_barcode_id = $('#product_barcode_id').val();
            var product_id = $('#product_id').val();
            if(!valueEmpty(pd_barcode) && !valueEmpty(product_barcode_id) && !valueEmpty(product_id)){
                localStorage.setItem("product_barcode_barcode", pd_barcode);
                localStorage.setItem("product_barcode_id", product_barcode_id);
                localStorage.setItem("product_id", product_id);
                localStorage.setItem("inv_type", "grn");
                window.open('/smart-product/tp-analysis','_blank');
            }else{
                toastr.error("Please select barcode");
            }
        });
        $('#tb_product_detail').click(function(){
            var pd_barcode = $('#pd_barcode').val();
            var product_barcode_id = $('#product_barcode_id').val();
            var product_id = $('#product_id').val();
            if(!valueEmpty(pd_barcode) && !valueEmpty(product_barcode_id) && !valueEmpty(product_id)){
                var data_url = '/common/get-product-detail/get-product/'+product_id;
                $('#kt_modal_md').modal('show').find('.modal-content').load(data_url);
                $('.modal-dialog').draggable({
                    handle: ".prod_head"
                });
            }else{
                toastr.error("Please select barcode");
            }
        });

    </script>
    @include('help_lg.po.script')

    <script !src="">
        $(document).on('change','.erp_form__grid_header .pd_tax_on,.erp_form__grid_header .pd_disc',function(e){
            var pd_tax_on =  $('.erp_form__grid_header').find('.pd_tax_on option:selected').val();
            var pd_disc =  $('.erp_form__grid_header').find('.pd_disc option:selected').val();
            localStorage.setItem('pd_tax_on', pd_tax_on);
            localStorage.setItem('pd_disc', pd_disc);
        })
        function funcAfterAddRow(){
            var pd_tax_on = localStorage.getItem('pd_tax_on');
            var pd_disc = localStorage.getItem('pd_disc');
            if(valueEmpty(pd_disc)){
                pd_disc = 'GA';
            }
            if(valueEmpty(pd_tax_on)){
                pd_tax_on = 'DA';
            }
            $('.erp_form__grid_header').find('.pd_tax_on').val(pd_tax_on).change();
            $('.erp_form__grid_header').find('.pd_disc').val(pd_disc).change();
        }

        $('#generatePriceTags').click(function() {

            var formData = {};
            formData.data = [];
            $('.erp_form__grid>tbody.erp_form__grid_body>tr').each(function() {
                var thix = $(this)
                var tr = {
                    'product_id': thix.find('input[data-id="product_id"]').val(),
                    'barcode_id': thix.find('input[data-id="product_barcode_id"]').val(),
                    'qty': thix.find('input[data-id="quantity"]').val(),
                    'sale_rate': thix.find('input[data-id="sale_rate"]').val(),
                }
                formData.data.push(tr);
            });
            //console.log(formData);
            var url = '/grn/barcode-price-tag';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        var url = '/barcode-labels/multi-barcode-labels/form';
                        var win = window.open(url, "generateBarcodeTags");
                    }
                },
                error: function(response, status) {}
            });
        });

    </script>
    <script !src="">
        function funcFormWise(){
            /* after add row in datatable grid */
            allGridTotal();
        }
    </script>

    @include('partial_script.table_column_switch')
@endsection
