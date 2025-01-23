@extends('layouts.layout')
@section('title', 'Purchase Order')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : '';
        if ($case == 'new') {
            $code = $data['document_code'];
            $menu_id = $data['menu_id'];
            $date = date('d-m-Y');
        }
        if ($case == 'edit') {
            $id = $data['current']->purchase_order_id;
            $code = $data['current']->purchase_order_code;
            $menu_id = $data['menu_id'];
            $date = date('d-m-Y', strtotime(trim(str_replace('/', '-', $data['current']->purchase_order_entry_date))));
            $lpo_id = isset($data['current']->lpo) ? $data['current']->lpo->lpo_id : '';
            $lpo_code = isset($data['current']->lpo) ? $data['current']->lpo->lpo_code : '';
            $comparative_quotation_id = isset($data['current']->comparative_quotation)
                ? $data['current']->comparative_quotation->comparative_quotation_id
                : '';
            $comparative_quotation_code = isset($data['current']->comparative_quotation)
                ? $data['current']->comparative_quotation->comparative_quotation_code
                : '';
            $supplier_id = isset($data['current']->supplier) ? $data['current']->supplier->supplier_id : '';
            $supplier_code = isset($data['current']->supplier) ? $data['current']->supplier->supplier_name : '';
            $payment_terms = $data['current']->payment_mode_id;
            $credit_days = $data['current']->purchase_order_credit_days;
            $currency_id = $data['current']->currency_id;
            $exchange_rate = $data['current']->purchase_order_exchange_rate;
            $po_details = $data['current']->po_details;
            $remarks = $data['current']->purchase_order_remarks;
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
        <form id="purchase_order_form" class="kt-form" method="post"
            action="{{ action('Purchase\PurchaseOrderController@store', isset($id) ? $id : '') }}">
            <input type="hidden" value='{{ $form_type }}' id="form_type">
            <input type="hidden" value='{{ $menu_id }}' id="menu_id">
            <input type="hidden" value='{{ isset($id) ? $id : '' }}' id="form_id">
            @csrf
            <!-- begin:: Content -->
            <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                        @include('elements.page_header', ['page_data' => $data['page_data']])
                    </div>
                    <div class="kt-portlet__body">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="erp-page--title">
                                            {{ isset($code) ? $code : '' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Document Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            <input type="text" name="po_date"
                                                class="form-control erp-form-control-sm moveIndex c-date-p" readonly
                                                value="{{ isset($date) ? $date : '' }}" id="kt_datepicker_3" autofocus />
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
                                                <input type="text" value="{{ isset($lpo_code) ? $lpo_code : '' }}"
                                                    data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'lpoPoHelp') }}"
                                                    id="lpo_generation_no" name="lpo_generation_no"
                                                    class="open_inline__help form-control erp-form-control-sm moveIndex"
                                                    placeholder="Enter here">
                                                <input type="hidden" id="lpo_generation_no_id" name="lpo_generation_no_id"
                                                    value="{{ isset($lpo_id) ? $lpo_id : '' }}" />
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
                                    <label class="col-lg-6 erp-col-form-label">Supplier:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" value="{{ isset($supplier_code) ? $supplier_code : '' }}"
                                                    data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'supplierHelp') }}"
                                                    id="supplier_name" name="supplier_name"
                                                    class="open_inline__help form-control erp-form-control-sm moveIndex"
                                                    placeholder="Enter here">
                                                <input type="hidden" id="supplier_id" name="supplier_id"
                                                    value="{{ isset($supplier_id) ? $supplier_id : '' }}" />
                                                {{-- <div class="input-group-append">
                                                    <button type="button" class="input-group-text btn-open-mob-help"
                                                        id="OpenInlineSupplierHelp"
                                                        data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'supplierHelp') }}">
                                                        <i class="la la-search"></i>
                                                    </button>
                                                </div> --}}
                                                {{-- <div class="input-group-append">
                                                    <span class="input-group-text btn-open-mob-help"
                                                        id="mobOpenInlineSupplierHelp">
                                                        <i class="la la-search"></i>
                                                    </span>
                                                </div> --}}
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
                                        <select name="payment_terms" id="payment_terms"
                                            class="moveIndex kt-select2 form-control erp-form-control-sm">
                                            <option value="0">Select</option>
                                            @foreach ($data['payment_terms'] as $payment_term)
                                                @php $payment_terms_id = isset($payment_terms)?$payment_terms:""; @endphp
                                                <option value="{{ $payment_term->payment_term_id }}"
                                                    {{ $payment_terms_id == $payment_term->payment_term_id ? 'selected' : '' }}>
                                                    {{ $payment_term->payment_term_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append" style="width: 33%;">
                                            <input type="text" value="{{ isset($credit_days) ? $credit_days : '' }}"
                                                id="payment_mode" name="payment_mode"
                                                class="moveIndex form-control erp-form-control-sm validNumber">
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
                                        <select class="moveIndex form-control erp-form-control-sm kt-select2 currency"
                                            id="po_currency" name="po_currency">
                                            <option value="0">Select</option>
                                            @if ($case == 'edit')
                                                @php $currency_id = isset($currency_id)?$currency_id:'';@endphp
                                                @foreach ($data['currency'] as $currency)
                                                    <option value="{{ $currency->currency_id }}"
                                                        {{ $currency->currency_id == $currency_id ? 'selected' : '' }}>
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
                                <label class="col-lg-6 erp-col-form-label">Exchange Rate:<span
                                        class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input type="text" value="{{ isset($exchange_rate) ? $exchange_rate : '' }}"
                                        id="exchange_rate" name="exchange_rate"
                                        class="moveIndex validNumber form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Comparative Quotation:</label>
                                <div class="col-lg-6">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                                </span>
                                            </div>
                                            <input type="text"
                                                value="{{ isset($comparative_quotation_code) ? $comparative_quotation_code : '' }}"
                                                name="comparative_quotation_code" id="comparative_quotation_code"
                                                data-url="{{ action('Common\DataTableController@helpOpen', 'comparativeQuotationHelp') }}"
                                                class="form-control erp-form-control-sm open_modal moveIndex moveIndex2 OnlyEnterAllow"
                                                placeholder="Enter here">
                                            <input type="hidden"
                                                value="{{ isset($comparative_quotation_id) ? $comparative_quotation_id : '' }}"
                                                name="comparative_quotation_id" id="comparative_quotation_id" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text btn-open-mob-help"
                                                    id="mobOpenInlineSupplierHelp">
                                                    <i class="la la-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-brand btn-sm" id="makePD" style="padding: 4px 6px;">
                                Select Multiple Products
                            </button>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Auto Demand Refrence:</label>
                                <div class="col-lg-8">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                                </span>
                                            </div>
                                            <input type="text" value="{{ isset($autoDemandCode) ? $autoDemandCode : '' }}"
                                                name="auto_demand_code" id="auto_demand_code"
                                                data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'autoDemandHelp') }}"
                                                class="open_inline__help form-control erp-form-control-sm open_modal moveIndex"
                                                placeholder="Auto Demand Refrence">
                                            <input type="hidden" value="{{ isset($auto_demand_id) ? $auo_demand_id : '' }}"
                                                name="auto_demand_id" id="auto_demand_id" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text group-input-btn get-ad-data" id="adGetData">
                                                    Go
                                                </span>
                                            </div>
                                        </div>
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
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = [
                                            'Sr No',
                                            'Barcode',
                                            'Product Name',
                                            'UOM',
                                            'Packing',
                                            'Notes',
                                            'Qty',
                                            'FOC Qty',
                                            'FC Rate',
                                            'Rate',
                                            'Amount',
                                            'Disc%',
                                            'Disc Amt',
                                            'VAT%',
                                            'Vat Amt',
                                            'Gross Amt',
                                        ];
                                    @endphp
                                    <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown"
                                        style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                        @foreach ($headings as $key => $heading)
                                            <li>
                                                <label>
                                                    <input value="{{ $key }}" type="checkbox" checked>
                                                    {{ $heading }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="kt-user-page-setting" style="display: inline-block">
                                    <button type="button" style="width: 30px;height: 30px;" title="Setting Save"
                                        data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon"
                                        id="pageUserSettingSave">
                                        <i class="la la-floppy-o"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group-block">
                        <div class="erp_form___block">
                            <div class="table-scroll form_input__block">
                                <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                    <thead class="erp_form__grid_header">
                                        <tr>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Sr.</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="sr_no" readonly type="text"
                                                        class="sr_no form-control erp-form-control-sm">
                                                    <input id="product_id" readonly type="hidden"
                                                        class="product_id form-control erp-form-control-sm">
                                                    <input id="product_barcode_id" readonly type="hidden"
                                                        class="product_barcode_id form-control erp-form-control-sm">
                                                    <input id="uom_id" readonly type="hidden"
                                                        class="uom_id form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">
                                                    Barcode
                                                    <button type="button" id="mobOpenInlineHelp"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="la la-barcode"></i>
                                                    </button>
                                                </div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="pd_barcode" type="text"
                                                        class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm"
                                                        data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Product Name</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="product_name" readonly type="text"
                                                        class="product_name form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">UOM</div>
                                                <div class="erp_form__grid_th_input">
                                                    <select id="pd_uom" class="pd_uom form-control erp-form-control-sm">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </th>
                                            <th cope="col">
                                                <div class="erp_form__grid_th_title">Packing</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="pd_packing" readonly type="text"
                                                        class="pd_packing form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th cope="col">
                                                <div class="erp_form__grid_th_title">Notes</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="remarks" type="text"
                                                        class="form-control erp-form-control-sm tb_moveIndex">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Qty</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="quantity" type="text"
                                                        class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">FOC Qty</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="foc_qty" type="text"
                                                        class="validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">FC Rate</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="fc_rate" type="text"
                                                        class="fc_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Rate</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="rate" type="text"
                                                        class="tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Amount</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="amount" type="text"
                                                        class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Disc %</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="dis_perc" type="text"
                                                        class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Disc Amt</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="dis_amount" type="text"
                                                        class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">VAT %</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="vat_perc" type="text"
                                                        class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">VAT Amt</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="vat_amount" type="text"
                                                        class="tblGridCal_vat_amount validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Gross Amt</div>
                                                <div class="erp_form__grid_th_input">
                                                    <input id="gross_amount" readonly type="text"
                                                        class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
                                                </div>
                                            </th>
                                            <th scope="col">
                                                <div class="erp_form__grid_th_title">Action</div>
                                                <div class="erp_form__grid_th_btn">
                                                    <button type="button" id="addData"
                                                        class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                        <i class="la la-plus"></i>
                                                    </button>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="erp_form__grid_body">
                                        @if (isset($po_details))
                                            @foreach ($po_details as $dtl)
                                                <tr>
                                                    <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                        <input type="text" value="{{ $loop->iteration }}"
                                                            name="pd[{{ $loop->iteration }}][sr_no]"
                                                            class="form-control erp-form-control-sm handle" readonly>
                                                        <input type="hidden"
                                                            name="pd[{{ $loop->iteration }}][purchase_order_dtl_id]"
                                                            data-id="purchase_order_dtl_id"
                                                            value="{{ $dtl->purchase_order_dtl_id }}"
                                                            class="purchase_order_dtl_id form-control erp-form-control-sm handle"
                                                            readonly>
                                                        <input type="hidden" name="pd[{{ $loop->iteration }}][product_id]"
                                                            data-id="product_id"
                                                            value="{{ isset($dtl->product->product_id) ? $dtl->product->product_id : '' }}"
                                                            class="product_id form-control erp-form-control-sm handle"
                                                            readonly>
                                                        <input type="hidden" name="pd[{{ $loop->iteration }}][uom_id]"
                                                            data-id="uom_id"
                                                            value="{{ isset($dtl->uom->uom_id) ? $dtl->uom->uom_id : '' }}"
                                                            class="uom_id form-control erp-form-control-sm handle" readonly>
                                                        <input type="hidden"
                                                            name="pd[{{ $loop->iteration }}][product_barcode_id]"
                                                            data-id="product_barcode_id"
                                                            value="{{ isset($dtl->product_barcode_id) ? $dtl->product_barcode_id : '' }}"
                                                            class="product_barcode_id form-control erp-form-control-sm handle"
                                                            readonly>
                                                        <input type="hidden" name="pd[{{ $loop->iteration }}][lpo_id]"
                                                            data-id="lpo_id" value="{{ $dtl->lpo_id }}"
                                                            class="lpo_id form-control erp-form-control-sm" readonly>
                                                        <input type="hidden"
                                                            name="pd[{{ $loop->iteration }}][comparative_quotation_id]"
                                                            data-id="comparative_quotation_id"
                                                            value="{{ $dtl->comparative_quotation_id }}"
                                                            class="comparative_quotation_id form-control erp-form-control-sm"
                                                            readonly>
                                                    </td>
                                                    <td><input type="text" data-id="pd_barcode"
                                                            name="pd[{{ $loop->iteration }}][pd_barcode]"
                                                            value="{{ $dtl->barcode->product_barcode_barcode }}"
                                                            data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}"
                                                            class="pd_barcode tb_moveIndex form-control erp-form-control-sm"
                                                            readonly></td>
                                                    <td><input type="text" data-id="product_name"
                                                            name="pd[{{ $loop->iteration }}][product_name]"
                                                            value="{{ isset($dtl->product->product_name) ? $dtl->product->product_name : '' }}"
                                                            class="product_name form-control erp-form-control-sm" readonly>
                                                    </td>
                                                    <td>
                                                        <select class="pd_uom form-control erp-form-control-sm"
                                                            data-id="pd_uom" name="pd[{{ $loop->iteration }}][pd_uom]">
                                                            @if (isset($dtl->uom_list) && is_array($dtl->uom_list) && count($dtl->uom_list) > 0)
                                                                @foreach ($dtl->uom_list as $unit)
                                                                    <option value="{{ $unit->uom_id ?? '' }}"
                                                                        @if ($dtl->uom->uom_id == $unit->uom_id) selected @endif>
                                                                        {{ $unit->uom_name ?? '' }}</option>
                                                                @endforeach
                                                            @else
                                                                <option
                                                                    value="{{ isset($dtl->uom->uom_id) ? $dtl->uom->uom_id : '' }}">
                                                                    {{ isset($dtl->uom->uom_name) ? $dtl->uom->uom_name : '' }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td><input type="text" data-id="pd_packing"
                                                            name="pd[{{ $loop->iteration }}][pd_packing]"
                                                            value="{{ isset($dtl->barcode->product_barcode_packing) ? $dtl->barcode->product_barcode_packing : '' }}"
                                                            class="pd_packing form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" data-id="remarks"
                                                            name="pd[{{ $loop->iteration }}][remarks]"
                                                            value="{{ isset($dtl->purchase_order_dtl_remarks) ? $dtl->purchase_order_dtl_remarks : '' }}"
                                                            class="form-control erp-form-control-sm tb_moveIndex"></td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][quantity]"
                                                            data-id="quantity" value="{{ $dtl->purchase_order_dtlquantity }}"
                                                            class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][foc_qty]"
                                                            data-id="foc_qty"
                                                            value="{{ $dtl->purchase_order_dtlfoc_quantity }}"
                                                            class="tb_moveIndex foc_qty form-control erp-form-control-sm validNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][fc_rate]"
                                                            data-id="fc_rate"
                                                            value="{{ number_format($dtl->purchase_order_dtlfc_rate, 2, '.', '') }}"
                                                            class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][rate]"
                                                            data-id="rate"
                                                            value="{{ number_format($dtl->purchase_order_dtlrate, 3, '.', '') }}"
                                                            class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][amount]"
                                                            data-id="amount"
                                                            value="{{ number_format($dtl->purchase_order_dtlamount, 3, '.', '') }}"
                                                            class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][dis_perc]"
                                                            data-id="dis_perc"
                                                            value="{{ number_format($dtl->purchase_order_dtldisc_percent, 2, '.', '') }}"
                                                            class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][dis_amount]"
                                                            data-id="dis_amount"
                                                            value="{{ number_format($dtl->purchase_order_dtldisc_amount, 3, '.', '') }}"
                                                            class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][vat_perc]"
                                                            data-id="vat_perc"
                                                            value="{{ number_format($dtl->purchase_order_dtlvat_percent, 2, '.', '') }}"
                                                            class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][vat_amount]"
                                                            data-id="vat_amount"
                                                            value="{{ number_format($dtl->purchase_order_dtlvat_amount, 3, '.', '') }}"
                                                            class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][gross_amount]"
                                                            data-id="gross_amount"
                                                            value="{{ number_format($dtl->purchase_order_dtltotal_amount, 3, '.', '') }}"
                                                            class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber"
                                                            readonly></td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-danger gridBtn delData"><i
                                                                    class="la la-trash"></i></button>
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
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="total_grid_qty">
                                                <input value="0.000" readonly type="text"
                                                    class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td class="total_grid_foc_qty">
                                                <input value="0.000" readonly type="text"
                                                    class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td class="total_grid_amount">
                                                <input value="0.000" readonly type="text"
                                                    class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td></td>
                                            <td class="total_grid_disc_amount">
                                                <input value="0.000" readonly type="text"
                                                    class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td></td>
                                            <td class="total_grid_vat_amount">
                                                <input value="0.000" readonly type="text"
                                                    class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td class="total_grid_gross_amount">
                                                <input value="0.000" readonly type="text"
                                                    class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-md-10 col-lg-2 text-right">
                            <table class="tableTotal" style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="t_total_label">Total:</div>
                                        </td>
                                        <td><span class="t_total t_gross_total">0</span><input type="hidden" id="pro_tot">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <label class="col-lg-2 erp-col-form-label">Remarks:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" id="po_notes" name="po_notes" maxlength="255"
                                class="form-control erp-form-control-sm">{{ isset($remarks) ? $remarks : '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </form>
        <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase-order.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>

    <script>

        function selectComparativeQuotation() {
            $('#help_datatable_comparativeQuotationHelp').on('click', 'tbody>tr', function(e) {
                $('#repeated_data>tr').each(function() {
                    $(this).find('td:eq(0)>input[data-id="comparative_quotation_id"]').parents('tr')
                    .remove();
                })
                dataDeleteInit();
                var code = $(this).find('td[data-field="comparative_quotation_code"]').text();
                var id = $(this).find('td[data-field="comparative_quotation_id"]').text();
                //var supplier_id = $(this).find('td[data-field="supplier_id"]').text();
                $('form').find('#comparative_quotation_code').val(code);
                $('form').find('#comparative_quotation_id').val(id);
                //console.log("demand_approval_dtl_id: "+ demand_approval_dtl_id);
                url = '/purchase-order/quotation/' + id;
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        _token: CSRF_TOKEN
                    },
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    success: function(response, status) {
                        console.log(response.data['all']);
                        if (response.status == 'success') {
                            var tr = '';
                            var total_length = $('#repeated_data>tr').length;
                            for (var p = 0; p < response.data['all'].length; p++) {
                                total_length++;
                                var row = response.data['all'][p];
                                console.log("rate: " + parseFloat(row['comparative_quotation_dtl_rate'])
                                    .toFixed(2));
                                tr += '<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd[' + total_length +
                                    '][sr_no]" value="' + total_length + '" title="' + total_length +
                                    '" class="form-control sr_no erp-form-control-sm handle" readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][comparative_quotation_id]" data-id="comparative_quotation_id" value="' +
                                    notNull(row['comparative_quotation_id']) +
                                    '" class="comparative_quotation_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][comparative_quotation_dtl_id]" data-id="comparative_quotation_dtl_id" value="' +
                                    notNull(row['comparative_quotation_dtl_id']) +
                                    '" class="comparative_quotation_dtl_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][supplier_id]" data-id="supplier_id" value="' + notNull(row[
                                        'supplier_id']) +
                                    '" class="supplier_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_id]" data-id="product_id" value="' + notNull(row[
                                        'prod_id']) +
                                    '" class="product_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][uom_id]" data-id="uom_id" value="' + notNull(row['uom_id']) +
                                    '"class="uom_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_barcode_id]" data-id="product_barcode_id" value="' +
                                    notNull(row['product_barcode_id']) +
                                    '" class="product_barcode_id form-control erp-form-control-sm " readonly>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][barcode]" data-id="barcode" data-url="{{ action('Common\DataTableController@helpOpen', 'productHelp') }}" value="' +
                                    notNull(row['comparative_quotation_dtl_barcode']) + '" title="' +
                                    notNull(row['comparative_quotation_dtl_barcode']) +
                                    '" class="form-control erp-form-control-sm" field_readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][product_name]" data-id="product_name" value="' + notNull(row[
                                        'product']['product_name']) + '" title="' + notNull(row[
                                        'product']['product_name']) +
                                    '" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td>' +
                                    '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd[' +
                                    total_length + '][uom]" data-id="uom" title="' + notNull(row['uom'][
                                        'uom_name'
                                    ]) + '">' +
                                    '<option value="' + notNull(row['uom']['uom_id']) + '">' + notNull(
                                        row['uom']['uom_name']) + '</option>' +
                                    '</select>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][packing]" data-id="packing" value="' + notNull(row['packing'][
                                        'packing_name'
                                    ]) + '" title="' + notNull(row['packing']['packing_name']) +
                                    '" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][quantity]" data-id="quantity" value="' + notNull(row[
                                        'comparative_quotation_dtl_quantity']) + '" title="' + notNull(
                                        row['comparative_quotation_dtl_quantity']) +
                                    '" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][foc_qty]" data-id="foc_qty" value="' + notNull(row[
                                        'comparative_quotation_dtl_foc_quantity']) + '" title="' +
                                    notNull(row['comparative_quotation_dtl_foc_quantity']) +
                                    '" class="foc_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][fc_rate]" data-id="fc_rate" value="' + notNull(row[
                                        'comparative_quotation_dtl_fc_rate']) + '" title="' + notNull(
                                        row['comparative_quotation_dtl_fc_rate']) +
                                    '" class="fc-rate moveIndex form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][rate]" data-id="rate" value="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_rate'], twoDecimal) + '" title="' +
                                    notNullEmpty(row['comparative_quotation_dtl_rate'], twoDecimal) +
                                    '" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][amount]" data-id="amount" value="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_amount'], threeDecimal) +
                                    '" title="' + notNullEmpty(row['comparative_quotation_dtl_amount'],
                                        threeDecimal) +
                                    '" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][discount]" data-id="discount" value="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_disc_percent'], twoDecimal) +
                                    '" title="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_disc_percent'], twoDecimal) +
                                    '" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][discount_val]" data-id="discount_val" value="' + notNullEmpty(
                                        row['comparative_quotation_dtl_disc_amount'], threeDecimal) +
                                    '" title="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_disc_amount'], threeDecimal) +
                                    '" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_perc]" data-id="vat_perc" value="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_vat_percent'], twoDecimal) +
                                    '" title="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_vat_percent'], twoDecimal) +
                                    '" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_val]" data-id="vat_val" value="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_vat_amount'], threeDecimal) +
                                    '" title="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_vat_amount'], threeDecimal) +
                                    '" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][gross_amount]" data-id="gross_amount" value="' + notNullEmpty(
                                        row['comparative_quotation_dtl_total_amount'], threeDecimal) +
                                    '" title="' + notNullEmpty(row[
                                        'comparative_quotation_dtl_total_amount'], threeDecimal) +
                                    '" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td class="text-center"></td>' +
                                    '</tr>';
                            }
                            $('#repeated_data').append(tr);
                            addDataInit();

                            $(".date_inputmask").inputmask("99-99-9999", {
                                "mask": "99-99-9999",
                                "placeholder": "dd-mm-yyyy",
                                autoUnmask: true
                            });
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(response, status) {
                        console.log(response);
                    },
                });
                closeModal();
            });
        };
    </script>
    <script>
        var formcase = '{{ $case }}';
        var data_po_selected = "";
    </script>
    <script>
        var productHelpUrl = "{{ url('/common/inline-help/productHelp') }}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id': 'pd_barcode',
                'fieldClass': 'pd_barcode tb_moveIndex open_inline__help',
                'message': 'Enter Barcode',
                'require': true,
                'readonly': true
                //  'data-url' : productHelpUrl
            },
            {
                'id': 'product_name',
                'fieldClass': 'product_name',
                'message': 'Enter Product Detail',
                'require': true,
                'readonly': true
            },
            {
                'id': 'pd_uom',
                'fieldClass': 'pd_uom field_readonly',
                'type': 'select'
            },
            {
                'id': 'pd_packing',
                'fieldClass': 'pd_packing',
                'readonly': true
            },
            {
                'id': 'remarks',
                'fieldClass': 'tb_moveIndex'
            },
            {
                'id': 'quantity',
                'fieldClass': 'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id': 'foc_qty',
                'fieldClass': 'tb_moveIndex validNumber foc_qty'
            },
            {
                'id': 'fc_rate',
                'fieldClass': 'fc_rate tb_moveIndex validNumber'
            },
            {
                'id': 'rate',
                'fieldClass': 'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id': 'amount',
                'fieldClass': 'tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id': 'dis_perc',
                'fieldClass': 'tblGridCal_discount_perc tb_moveIndex validNumber'
            },
            {
                'id': 'dis_amount',
                'fieldClass': 'tblGridCal_discount_amount tb_moveIndex validNumber'
            },
            {
                'id': 'vat_perc',
                'fieldClass': 'tblGridCal_vat_perc tb_moveIndex validNumber'
            },
            {
                'id': 'vat_amount',
                'fieldClass': 'tblGridCal_vat_amount validNumber'
            },
            {
                'id': 'gross_amount',
                'fieldClass': 'tblGridCal_gross_amount validNumber',
                'readonly': true
            }
        ];
        var arr_hidden_field = ['product_id', 'product_barcode_id', 'uom_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        var supplierFocQtyArr = [];
        $(document).on('keyup', '.tblGridCal_qty', function() {
            var thix = $(this);
            var tr = thix.parents('tr');
            var qty = thix.val();
            var packing = tr.find('.pd_packing').val();
            var product_id = tr.find('.product_id').val();
            var supplier_id = $('form').find('#supplier_id').val();
            var foc = 0;
            var foc_qty = 0;
            var purc_qty = 0;
            var base_unit = 0;
            var checkPurc = false;
            var sendAjaxReq = true;
            supplierFocQtyArr.forEach(function(item) {
                if (product_id == item['product_id'] && supplier_id == item['supplier_id']) {
                    purc_qty = item['purc_qty']
                    foc_qty = item['foc_qty']
                    base_unit = item['base_unit']
                    sendAjaxReq = false;
                    checkPurc = true;
                }
            });
            if (checkPurc) {
                var totalQty = packing * base_unit * qty;
                foc = (totalQty / purc_qty) * foc_qty;
                tr.find('#foc_qty').val(foc.toFixed(3));
                tr.find('.foc_qty').val(foc.toFixed(3));
            }
            if (sendAjaxReq) {
                var supplierFocQty = [];
                supplierFocQty['product_id'] = product_id;
                supplierFocQty['supplier_id'] = supplier_id;
                var formData = {
                    'product_id': product_id,
                    'supplier_id': supplier_id,
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/barcode/get-supplier-foc',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        if (response.data.supplier_foc != null) {
                            if (response.data.supplier_foc.purc_qty !== undefined &&
                                response.data.supplier_foc.foc_qty !== undefined &&
                                response.data.supplier_foc.base_unit !== undefined) {

                                supplierFocQty['purc_qty'] = response.data.supplier_foc.purc_qty;
                                supplierFocQty['foc_qty'] = response.data.supplier_foc.foc_qty;
                                supplierFocQty['base_unit'] = response.data.supplier_foc.base_unit;
                                purc_qty = response.data.supplier_foc.purc_qty;
                                foc_qty = response.data.supplier_foc.foc_qty;
                                base_unit = response.data.supplier_foc.base_unit;
                                supplierFocQtyArr.push(supplierFocQty);
                                var totalQty = packing * base_unit * qty;
                                foc = (totalQty / purc_qty) * foc_qty;
                                tr.find('#foc_qty').val(foc.toFixed(3));
                                tr.find('.foc_qty').val(foc.toFixed(3));
                            }
                        }
                    }
                })
            }
        })
        $(document).on('click', '#upload_documents', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                form_id: $('#form_id').val(),
                form_type: $('#form_type').val(),
                menu_id: $('#menu_id').val(),
                form_code: $('.erp-page--title').text().trim(),

            }
            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url, formData);
        })
        // $(document).on('click','#makePD',function(){
        $('#makePD').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                supplier_id: $('#supplier_id').val(),
                form_type: $('#form_type').val(),
            }
            var data_url = '/common/select-multiple-products';
            $('#kt_modal_xl').modal('show').find('.modal-content').load(data_url, formData);
        })
        $(document).on('click', '.btn_add', function(e) {
            e.preventDefault();
            var thix = $(this);
            addRow(thix)

        })

        function addRow(thix) {
            var parentTr = thix.parents('tr');
            if (!parentTr.find('.demand_qty').val()) {
                alert("Please add demand qty");
            } else {
                var item_duplicate = false;
                $(document).find('#smp_selected_products table tbody tr').each(function() {
                    if ($(this).find('td[data-field="product_barcode_barcode"]>span').text() == parentTr.find(
                            'td[data-field="product_barcode_barcode"]>span').text()) {
                        toastr.warning("Item alread added.");
                        item_duplicate = true;
                    }
                })
                if (item_duplicate == true) {
                    return true;
                }
                var tr = thix.parents('tr').clone();
                var thead = $(document).find('#smp_products table thead>tr').clone();
                $(document).find('#smp_selected_products table thead').html(thead);
                $(document).find('#smp_selected_products table tbody').append(tr);
                $(document).find('#smp_selected_products .ajax_data_table').removeClass('kt-datatable--error');
                $(document).find('#smp_selected_products table tbody>.kt-datatable--error').remove();
                var lastTd = "<span>";
                lastTd += "<input type='checkbox' style='height: 16px;width: 16px;' checked>";
                lastTd +=
                    "<i class='la la-times del_row' style='position: relative;background: #f44336;padding: 2px 2px;color: #fff;margin-left: 3px;top: -3px;'></i>";
                lastTd += "</span>";
                $(document).find('#smp_selected_products table tbody tr:last-child td:last-child').html(lastTd);
                toastr.success("Item added.");
            }
        }
        $(document).on('click', '.del_row', function(e) {
            e.preventDefault();
            $(this).parents('tr').remove();
        })
        $(document).on('click', '.close', function(e) {
            e.preventDefault();
            $('.modal').find('.modal-content').empty();
            $('.modal').find('.modal-content').html(
                ' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>'
                );
            $('.modal').modal('hide');
        })
        $(document).on('click', '#add_selected_products', function(e) {
            e.preventDefault();
            if ($(document).find('#smp_selected_products table tbody tr').length == 0) {
                toastr.warning("Please first select items");
                return true;
            }
            $(this).attr('disabled', true);
            $('.modal').find('.modal-header>button').attr('disabled', true);
            $('.modal').find('.modal-content').prepend(
                '<div style="position: absolute;left: 50%;" class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span style="margin-left: 30px;">insert data,please wait..</span></div>'
                );
            $(document).find('#smp_selected_products table input').attr('readonly', true)
            $(document).find('#smp_selected_products table tbody tr').each(function() {
                var thix = $(this);
                var barcode = thix.find('td[data-field="product_barcode_barcode"]>span').text();
                var demand_qty = thix.find('td[data-field="demand_qty"] input.demand_qty').val();
                var keycodeNo = 13;
                var tr = $(document).find('.erp_form__grid>.erp_form__grid_header>tr');
                var form_type = $('#form_type').val();
                var formData = {
                    form_type: form_type,
                    val: barcode,
                    demand_qty: demand_qty,
                    selection: "multi",
                }
                initBarcode(keycodeNo, tr, form_type, formData);
            });
            $(document).ajaxStop(function(e, d) {
                // place code to be executed on completion of last outstanding ajax call here
                $('.modal').find('.modal-content').empty();
                $('.modal').find('.modal-content').html(
                    ' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>'
                    );
                $('.modal').modal('hide');
                $(document).unbind("ajaxStop");
            });
        })

        $(document).on('keyup', '.demand_qty', function(e) {
            if (e.keyCode == 13) { // press enter
                e.preventDefault();
                var thix = $(this);
                addRow(thix)
            }
            if (e.keyCode == 38) { // press ArrowUp

            }
            if (e.keyCode == 40) { // press ArrowDown

            }
        })

        $(document).on('click', '#lpoGetData', function(e) {
            var errors = 0;
            var required = ['lpo_generation_no_id', 'supplier_id'];
            required.forEach(function(el) {
                if ($('#' + el).val() == "") {
                    errors++;
                }
            });
            if (errors > 0) {
                toastr.error('Please Select LPO Generation No. First');
            } else {
                var lpo_id = $('#lpo_generation_no_id').val();
                var supplier_id = $('#supplier_id').val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET',
                    url: 'lpo/' + lpo_id + '/' + supplier_id,
                    beforeSend: function() {
                        $('body').addClass('pointerEventsNone');
                    },
                    success: function(response) {
                        $('body').removeClass('pointerEventsNone');
                        if (response.status == 'success') {
                            var lpos = response.data['all'];
                            var tr = '';
                            var total_length = $('tbody.erp_form__grid_body tr').length;
                            for (var p = 0; p < lpos.length; p++) {
                                total_length++;
                                var row = lpos[p];
                                console.log("rate: " + parseFloat(lpos[p].lpo_dtl_rate).toFixed(2));
                                tr += '<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd[' + total_length +
                                    '][sr_no]" value="' + total_length + '" title="' + total_length +
                                    '" class="form-control sr_no erp-form-control-sm handle" readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_id]" data-id="product_id" value="' + lpos[p].product_id +
                                    '" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_barcode_id]" data-id="product_barcode_id" value="' +
                                    lpos[p].product_barcode_id +
                                    '" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][uom_id]" data-id="uom_id" value="' + lpos[p].uom_id +
                                    '" class="uom_id form-control erp-form-control-sm" readonly="" autocomplete="off">' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][pd_barcode]" data-id="pd_barcode" data-url="" value="' + lpos[p]
                                    .product_barcode_barcode + '" title="' + lpos[p]
                                    .product_barcode_barcode +
                                    '" class="form-control erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" readonly="" autocomplete="off"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][product_name]" data-id="product_name" data-url="" value="' +
                                    lpos[p].product_name + '" title="' + lpos[p].product_name +
                                    '" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off"></td>' +
                                    '<td>' +
                                    '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd[' +
                                    total_length + '][uom]" data-id="uom" title="' + notNull(lpos[p]
                                        .uom_name) + '">' +
                                    '<option value="' + notNull(lpos[p].uom_id) + '">' + notNull(lpos[p]
                                        .uom_name) + '</option>' +
                                    '</select>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][packing]" data-id="packing" value="' + notNull(lpos[p]
                                        .product_barcode_packing) + '" title="' + notNull(lpos[p]
                                        .product_barcode_packing) +
                                    '" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][remarks]" data-id="notes" value="' + notNull(lpos[p]
                                    .lpo_remarks) + '" title="' + notNull(lpos[p].lpo_remarks) +
                                    '" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][quantity]" data-id="quantity" value="' + notNull(lpos[p]
                                        .lpo_dtl_quantity) + '" title="' + notNull(lpos[p]
                                        .lpo_dtl_quantity) +
                                    '" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][foc_qty]" data-id="foc_qty" value="' + notNull(lpos[p]
                                        .lpo_dtl_foc_quantity) + '" title="' + notNull(lpos[p]
                                        .lpo_dtl_foc_quantity) +
                                    '" class="foc_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][fc_rate]" data-id="fc_rate" value="' + notNull(lpos[p]
                                        .lpo_dtl_fc_rate) + '" title="' + notNull(lpos[p]
                                        .lpo_dtl_fc_rate) +
                                    '" class="fc-rate moveIndex form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][rate]" data-id="rate" value="' + notNullEmpty(lpos[p]
                                        .lpo_dtl_rate, threeDecimal) + '" title="' + notNullEmpty(lpos[
                                        p].lpo_dtl_rate, threeDecimal) +
                                    '" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][amount]" data-id="amount" value="' + notNullEmpty(lpos[p]
                                        .lpo_dtl_amount, threeDecimal) + '" title="' + notNullEmpty(
                                        lpos[p].lpo_dtl_amount, threeDecimal) +
                                    '" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][dis_perc]" data-id="dis_perc" value="' + notNullEmpty(lpos[p]
                                        .lpo_dtl_disc_percent, threeDecimal) + '" title="' +
                                    notNullEmpty(lpos[p].lpo_dtl_disc_percent, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_discount_perc tb_moveIndex validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][dis_amount]" data-id="dis_amount" value="' + notNullEmpty(lpos[p]
                                        .lpo_dtl_disc_amount, threeDecimal) + '" title="' +
                                    notNullEmpty(lpos[p].lpo_dtl_disc_amount, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_discount_amount tb_moveIndex validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_perc]" data-id="vat_perc" value="' + notNullEmpty(lpos[p]
                                        .lpo_dtl_vat_percent, threeDecimal) + '" title="' +
                                    notNullEmpty(lpos[p].lpo_dtl_vat_percent, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_vat_perc tb_moveIndex validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_amount]" data-id="vat_amount" value="' + notNullEmpty(lpos[p]
                                        .lpo_dtl_vat_amount, threeDecimal) + '" title="' + notNullEmpty(
                                        lpos[p].lpo_dtl_vat_amount, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_vat_amount validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][gross_amount]" data-id="gross_amount" value="' + notNullEmpty(
                                        lpos[p].lpo_dtl_gross_amount, threeDecimal) + '" title="' +
                                    notNullEmpty(lpos[p].lpo_dtl_gross_amount, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_gross_amount validNumber" readonly></td>' +
                                    '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>' +
                                    '</tr>';
                            }
                            $('tbody.erp_form__grid_body').append(tr);
                            addDataInit();
                            $(".date_inputmask").inputmask("99-99-9999", {
                                "mask": "99-99-9999",
                                "placeholder": "dd-mm-yyyy",
                                autoUnmask: true
                            });
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, response) {
                        $('body').removeClass('pointerEventsNone');
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });
        // Get Data From Auto Demand
        $(document).on('click', '#adGetData', function(e) {
            var errors = 0;
            var required = ['auto_demand_id'];
            required.forEach(function(el) {
                if ($('#' + el).val() == "") {
                    errors++;
                }
            });
            if (errors > 0) {
                toastr.error('Please Select Auto Demand No. First');
            } else {
                var ad_id = $('#auto_demand_id').val();
                var supplier_id = $('#supplier_id').val();
                if (supplier_id == "") {
                    var url = 'ad/' + ad_id + '/';
                } else {
                    var url = 'ad/' + ad_id + '/' + supplier_id;
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET',
                    url: 'ad/' + ad_id + '/' + supplier_id,
                    beforeSend: function() {
                        $('body').addClass('pointerEventsNone');
                    },
                    success: function(response) {
                        $('body').removeClass('pointerEventsNone');
                        if (response.status == 'success') {
                            var lpos = response.data['all'];
                            var tr = '';
                            var total_length = $('tbody.erp_form__grid_body tr').length;
                            for (var p = 0; p < lpos.length; p++) {
                                total_length++;
                                var row = lpos[p];
                                var amount = parseFloat(lpos[p].qty * lpos[p].rate);
                                tr += '<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd[' + total_length +
                                    '][sr_no]" value="' + total_length + '" title="' + total_length +
                                    '" class="form-control sr_no erp-form-control-sm handle" readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_id]" data-id="product_id" value="' + lpos[p].product_id +
                                    '" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_barcode_id]" data-id="product_barcode_id" value="' +
                                    lpos[p].product_barcode_id +
                                    '" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][uom_id]" data-id="uom_id" value="' + lpos[p].uom_id +
                                    '" class="uom_id form-control erp-form-control-sm" readonly="" autocomplete="off">' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][pd_barcode]" data-id="pd_barcode" data-url="" value="' + lpos[p]
                                    .product_barcode_barcode + '" title="' + lpos[p]
                                    .product_barcode_barcode +
                                    '" class="form-control erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" readonly="" autocomplete="off"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][product_name]" data-id="product_name" data-url="" value="' +
                                    lpos[p].product_name + '" title="' + lpos[p].product_name +
                                    '" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off"></td>' +
                                    '<td>' +
                                    '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd[' +
                                    total_length + '][uom]" data-id="uom" title="' + notNull(lpos[p]
                                        .uom_name) + '">' +
                                    '<option value="' + notNull(lpos[p].uom_id) + '">' + notNull(lpos[p]
                                        .uom_name) + '</option>' +
                                    '</select>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][packing]" data-id="packing" value="' + notNull(lpos[p].packing) +
                                    '" title="' + notNull(lpos[p].packing) +
                                    '" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][remarks]" data-id="notes" value="" title="" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][quantity]" data-id="quantity" value="' + notNull(lpos[p].qty) +
                                    '" title="' + notNull(lpos[p].qty) +
                                    '" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][foc_qty]" data-id="foc_qty" value="' + notNull(lpos[p].foc_qty) +
                                    '" title="' + notNull(lpos[p].foc_qty) +
                                    '" class="foc_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][fc_rate]" data-id="fc_rate" value="" title="" class="fc-rate moveIndex form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][rate]" data-id="rate" value="' + notNullEmpty(lpos[p].rate,
                                        threeDecimal) + '" title="' + notNullEmpty(lpos[p].rate,
                                        threeDecimal) +
                                    '" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][amount]" data-id="amount" value="' + notNullEmpty(amount,
                                        threeDecimal) + '" title="' + notNullEmpty(lpos[p].amount,
                                        threeDecimal) +
                                    '" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][dis_perc]" data-id="dis_perc" value="' + notNullEmpty(lpos[p]
                                        .disc_perc, threeDecimal) + '" title="' + notNullEmpty(lpos[p]
                                        .disc_perc, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_discount_perc tb_moveIndex validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][dis_amount]" data-id="dis_amount" value="' + notNullEmpty(lpos[p]
                                        .disc, threeDecimal) + '" title="' + notNullEmpty(lpos[p].disc,
                                        threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_discount_amount tb_moveIndex validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_perc]" data-id="vat_perc" value="' + notNullEmpty(lpos[p]
                                        .vat_perc, threeDecimal) + '" title="' + notNullEmpty(lpos[p]
                                        .vat_perc, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_vat_perc tb_moveIndex validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_amount]" data-id="vat_amount" value="' + notNullEmpty(lpos[p]
                                        .vat, threeDecimal) + '" title="' + notNullEmpty(lpos[p].vat,
                                        threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_vat_amount validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][gross_amount]" data-id="gross_amount" value="' + notNullEmpty(
                                        lpos[p].gross_amount, threeDecimal) + '" title="' +
                                    notNullEmpty(lpos[p].gross_amount, threeDecimal) +
                                    '" class="form-control erp-form-control-sm tblGridCal_gross_amount validNumber" readonly></td>' +
                                    '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>' +
                                    '</tr>';
                            }
                            $('tbody.erp_form__grid_body').append(tr);
                            calcAllRows();
                            allCalcFunc();
                            addDataInit();
                            $(".date_inputmask").inputmask("99-99-9999", {
                                "mask": "99-99-9999",
                                "placeholder": "dd-mm-yyyy",
                                autoUnmask: true
                            });
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, response) {
                        $('body').removeClass('pointerEventsNone');
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });


    </script>
@endsection
