@extends('layouts.layout')
@section('title', 'GRN')

@section('pageCSS')
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
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : '';
        if ($case == 'new') {
            $length = 0;
            $currencySymbol = 'OMR';
        }
        if ($case == 'edit') {
            $expense_dtls = isset($data['current']->grn_expense) ? $data['current']->grn_expense : [];
            $length = count($expense_dtls);
            $currencySymbol = $data['current']->currency->currency_symbol ?? 'OMR';
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
        <!--begin::Form-->
        @php $id = isset($data['current']->grn_id)?$data['current']->grn_id:'';  @endphp
        <form id="grn_form" class="kt-form" method="post" action="{{ action('Purchase\GRNController@store', $id) }}">
            @csrf
            <input type="hidden" value='{{ $form_type }}' id="form_type">
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
                                    <label class="col-lg-6 col-form-label">Date:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group date">
                                            @if (isset($data['id']))
                                                @php $due_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->grn_date)))); @endphp
                                            @else
                                                @php $due_date =  date('d-m-Y'); @endphp
                                            @endif
                                            <input type="text" name="grn_date" autocomplete="off"
                                                class="form-control erp-form-control-sm moveIndex c-date-p" readonly
                                                value="{{ $due_date }}" id="kt_datepicker_3" autofocus />
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
                                    <label class="col-lg-6 col-form-label">Supplier:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="supplier_name"
                                                    value="{{ isset($data['current']->supplier->supplier_name) ? $data['current']->supplier->supplier_name : '' }}"
                                                    data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'supplierHelp') }}"
                                                    autocomplete="off" name="supplier_name"
                                                    class="open_inline__help form-control erp-form-control-sm moveIndex"
                                                    placeholder="Enter here">
                                                <input type="hidden" id="supplier_id" name="supplier_id"
                                                    value="{{ isset($data['current']->supplier->supplier_id) ? $data['current']->supplier->supplier_id : '' }}" />
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
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">PO:</label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block" id="select_po">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text"
                                                    data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'poHelp') }}"
                                                    value="{{ isset($data['current']->PO->purchase_order_code) ? $data['current']->PO->purchase_order_code : '' }}"
                                                    id="purchase_order" name="purchase_order"
                                                    class="open_inline__help form-control erp-form-control-sm moveIndex"
                                                    placeholder="Enter here">
                                                <input type="hidden" id="purchase_order_id" name="purchase_order_id"
                                                    value="{{ isset($data['current']->PO->purchase_order_id) ? $data['current']->PO->purchase_order_id : '' }}" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text btn-open-mob-help"
                                                        id="mobOpenInlineSupplierHelp">
                                                        <i class="la la-search"></i>
                                                    </span>
                                                    <span class="input-group-text group-input-btn" id="getPOData">
                                                        GO
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
                                    <label class="col-lg-6 col-form-label">Currency:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm moveIndex currency"
                                                id="kt_select2_1" name="grn_currency">
                                                <option value="0">Select</option>
                                                @if (isset($data['current']->currency_id))
                                                    @php
                                                        $grn_currency = isset($data['current']->currency_id)
                                                            ? $data['current']->currency_id
                                                            : 0;
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
                                                            @php
                                                                $currencySymbol = $currency->currency_symbol;
                                                                $exchange_rate = $currency->currency_rate;
                                                            @endphp
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
                                    <label class="col-lg-6 col-form-label">Exchange Rate:<span
                                            class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" id="exchange_rate" name="exchange_rate"
                                            value="{{ isset($data['current']->grn_exchange_rate) ? $data['current']->grn_exchange_rate : $exchange_rate }}"
                                            class="form-control erp-form-control-sm moveIndex validNumber">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">Bill No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="grn_bill_no" name="grn_bill_no"
                                            value="{{ isset($data['current']->grn_bill_no) ? $data['current']->grn_bill_no : '' }}"
                                            class="form-control erp-form-control-sm moveIndex">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">Payment Terms:</label>
                                    <div class="col-lg-6">
                                        <div class="input-group erp-select2-sm">
                                            <select name="grn_ageing_term_id" id="grn_ageing_term_id"
                                                class="moveIndex kt-select2 width form-control erp-form-control-sm">
                                                <option value="0">Select</option>
                                                @foreach ($data['payment_terms'] as $payment_term)
                                                    @php $payment_terms_id = isset($data['current']->grn_ageing_term_id)?$data['current']->grn_ageing_term_id:''; @endphp
                                                    <option value="{{ $payment_term->payment_term_id }}"
                                                        {{ $payment_terms_id == $payment_term->payment_term_id ? 'selected' : '' }}>
                                                        {{ $payment_term->payment_term_name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append" style="width: 33%;">
                                                <input type="text"
                                                    value="{{ isset($data['current']->grn_ageing_term_value) ? $data['current']->grn_ageing_term_value : '' }}"
                                                    id="grn_ageing_term_value" name="grn_ageing_term_value"
                                                    class="moveIndex form-control erp-form-control-sm validNumber">
                                            </div>
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

                            {{-- <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">Payment Type:
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm moveIndex"
                                                id="payment_type_id" name="payment_type_id">
                                                @foreach ($data['payment_type'] as $payment_type)
                                                    @if (isset($data['id']))
                                                        @php $payment_type_id = isset($data['current']->payment_type_id)?$data['current']->payment_type_id:''; @endphp
                                                    @else
                                                        @php $payment_type_id = '2'; @endphp
                                                    @endif
                                                    <option value="{{ $payment_type->payment_type_id }}"
                                                        {{ $payment_type_id == $payment_type->payment_type_id ? 'selected' : '' }}>
                                                        {{ $payment_type->payment_type_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 col-form-label">Select Products:</label>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn btn-brand btn-sm" id="makePD"
                                            style="padding: 4px 6px;">
                                            Select Multiple Products
                                        </button>
                                    </div>
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
                            <div class="col-lg-12 text-right">
                                <div class="data_entry_header">
                                    <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide
                                    </div>
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
                                                'Sup Barcode',
                                                'Qty',
                                                'FOC Qty',
                                                'Sale Rate',
                                                '<span class="fc_dynamic_title">' .
                                                $currencySymbol .
                                                ' Rate (FC)</span>',
                                                'Rate',
                                                'Amount',
                                                'Disc%',
                                                'Disc Amt',
                                                'VAT%',
                                                'Vat Amt',
                                                'Batch #',
                                                'Production Date',
                                                'Expiry Date',
                                                'Gross Amt',
                                            ];
                                        @endphp
                                        <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown"
                                            style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                            @foreach ($headings as $key => $heading)
                                                <li>
                                                    <label>
                                                        <input value="{{ $key }}" name="{{ trim($key) }}"
                                                            type="checkbox" checked> {!! $heading !!}
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
                                    <div class="kt-user-page-setting" style="display: inline-block">
                                        <button type="button" style="width: 30px;height: 30px;" title="Barcode Print"
                                            data-toggle="tooltip" class="btn btn-brand btn-elevate btn-circle btn-icon"
                                            id="generatePriceTags">
                                            <i class="la la-barcode"></i>
                                        </button>
                                    </div>
                                    <div class="kt-user-page-setting" style="display: inline-block">
                                        <button type="button" style="width: 30px;height: 30px;" title="Shelf Barcode Print"
                                            data-toggle="tooltip"
                                            class="btn btn-brand btn-success btn-elevate btn-circle btn-icon"
                                            id="generateShelfPriceTags">
                                            <i class="la la-barcode"></i>
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
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">
                                                        Barcode
                                                        <button type="button" id="mobOpenInlineHelp"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="la la-barcode"></i>
                                                        </button>
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Product Name</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">UOM</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Packing</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Sup Barcode</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Qty</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">FOC Qty</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title btn btn-sm sale_rate_barcode"
                                                        id="sale_barcode">Sale Rate</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title fc_dynamic_title">
                                                        {{ $currencySymbol }} Rate (FC)</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Rate</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Amount</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Disc %</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Disc Amt</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">VAT %</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">VAT Amt</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Batch No</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Production Date</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Expiry Date</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Gross Amt</div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Action</div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="erp_form__grid_body">

                                            <tr>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="sr_no" readonly type="text"
                                                            class="sr_no form-control erp-form-control-sm">
                                                        <input id="product_id" readonly type="hidden"
                                                            class="product_id form-control erp-form-control-sm">
                                                        <input id="product_barcode_id" readonly type="hidden"
                                                            class="product_barcode_id form-control erp-form-control-sm">
                                                        <input id="uom_id" readonly type="hidden"
                                                            class="uom_id form-control erp-form-control-sm">
                                                        <input id="grn_supplier_id" readonly type="hidden"
                                                            class="grn_supplier_id form-control erp-form-control-sm handle">
                                                        <input id="grn_dtl_po_rate" readonly type="hidden"
                                                            class="grn_dtl_po_rate form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="pd_barcode" type="text"
                                                            class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm"
                                                            data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}"
                                                            data-url_popup="{{ action('Common\DataTableController@helpOpen', 'productHelp') }}">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="product_name" readonly type="text"
                                                            class="product_name form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <select id="pd_uom"
                                                            class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="pd_packing" readonly type="text"
                                                            class="pd_packing form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="grn_supplier_barcode" type="text"
                                                            class="sup_barcode tb_moveIndex form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="quantity" type="text"
                                                            class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="foc_qty" type="text"
                                                            class="tblGridCal_foc_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="sale_rate" data-id="sale_rate" type="text"
                                                            class="tblGridSale_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"
                                                            readonly>
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="fc_rate" type="text"
                                                            class="fc_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="rate" type="text"
                                                            class="tblGridCal_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="amount" type="text"
                                                            class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="dis_perc" type="text"
                                                            class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="dis_amount" type="text"
                                                            class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="vat_perc" type="text"
                                                            class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="vat_amount" type="text"
                                                            class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="batch_no" type="text"
                                                            class="tb_moveIndex form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="production_date" value=""
                                                            title="{{ date('d-m-Y') }}" type="text"
                                                            class="date_inputmask tb_moveIndex form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="expiry_date" value="" title="{{ date('d-m-Y') }}"
                                                            type="text"
                                                            class="date_inputmask tb_moveIndex form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="gross_amount" readonly type="text"
                                                            class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_btn">
                                                        <button type="button" id="addData"
                                                            class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                            <i class="la la-plus"></i>
                                                        </button>
                                                    </div>
                                                </th>
                                            </tr>

                                            @if (isset($data['current']->grn_dtl))
                                                @foreach ($data['current']->grn_dtl as $dtl)
                                                    @php
                                                        $rateColorClass = '';
                                                        $rate = number_format($dtl->tbl_purc_grn_dtl_rate, 3);
                                                        $po_rate =
                                                            isset($dtl->grn_dtl_po_rate) &&
                                                            $dtl->grn_dtl_po_rate != null &&
                                                            $dtl->grn_dtl_po_rate != ''
                                                                ? number_format($dtl->grn_dtl_po_rate, 3)
                                                                : '';
                                                        if ($rate == $po_rate) {
                                                            $rateColorClass = 'grn_green';
                                                        }
                                                        if ($rate > $po_rate && $po_rate != '') {
                                                            $rateColorClass = 'grn_red';
                                                        }
                                                        if ($rate < $po_rate) {
                                                            $rateColorClass = 'grn_yellow';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                            <input type="text" value="{{ $loop->iteration }}"
                                                                name="pd[{{ $loop->iteration }}][sr_no]"
                                                                class="form-control erp-form-control-sm handle" readonly>
                                                            <input type="hidden"
                                                                name="pd[{{ $loop->iteration }}][purc_grn_dtl_id]"
                                                                data-id="purc_grn_dtl_id" value="{{ $dtl->purc_grn_dtl_id }}"
                                                                class="purc_grn_dtl_id form-control erp-form-control-sm handle"readonly>
                                                            <input type="hidden"
                                                                name="pd[{{ $loop->iteration }}][product_id]"
                                                                data-id="product_id"
                                                                value="{{ isset($dtl->product->product_id) ? $dtl->product->product_id : '' }}"
                                                                class="product_id form-control erp-form-control-sm handle"
                                                                readonly>
                                                            <input type="hidden" name="pd[{{ $loop->iteration }}][uom_id]"
                                                                data-id="uom_id"
                                                                value="{{ isset($dtl->uom->uom_id) ? $dtl->uom->uom_id : '' }}"
                                                                class="uom_id form-control erp-form-control-sm handle"
                                                                readonly>
                                                            <input type="hidden"
                                                                name="pd[{{ $loop->iteration }}][product_barcode_id]"
                                                                data-id="product_barcode_id"
                                                                value="{{ isset($dtl->product_barcode_id) ? $dtl->product_barcode_id : '' }}"
                                                                class="product_barcode_id form-control erp-form-control-sm handle"
                                                                readonly>
                                                            <input readonly type="hidden" data-id="grn_supplier_id"
                                                                name="pd[{{ $loop->iteration }}][grn_supplier_id]"
                                                                value=""
                                                                class="grn_supplier_id form-control erp-form-control-sm">
                                                            <input readonly type="hidden" data-id="grn_dtl_po_rate"
                                                                name="pd[{{ $loop->iteration }}][grn_dtl_po_rate]"
                                                                value="{{ $po_rate }}"
                                                                class="grn_dtl_po_rate form-control erp-form-control-sm">
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
                                                            <select
                                                                class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm"
                                                                data-id="pd_uom" name="pd[{{ $loop->iteration }}][pd_uom]">
                                                                <option
                                                                    value="{{ isset($dtl->uom->uom_id) ? $dtl->uom->uom_id : '' }}">
                                                                    {{ isset($dtl->uom->uom_name) ? $dtl->uom->uom_name : '' }}
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" data-id="pd_packing"
                                                                name="pd[{{ $loop->iteration }}][pd_packing]"
                                                                value="{{ isset($dtl->barcode->product_barcode_packing) ? $dtl->barcode->product_barcode_packing : '' }}"
                                                                class="pd_packing form-control erp-form-control-sm" readonly>
                                                        </td>
                                                        <td><input type="text"
                                                                name="pd[{{ $loop->iteration }}][grn_supplier_barcode]"
                                                                data-id="grn_supplier_barcode"
                                                                class="sup_barcode tb_moveIndex form-control erp-form-control-sm"
                                                                readonly></td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][quantity]"
                                                                data-id="quantity"
                                                                value="{{ $dtl->tbl_purc_grn_dtl_quantity }}"
                                                                class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                        </td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][foc_qty]"
                                                                data-id="foc_qty"
                                                                value="{{ $dtl->tbl_purc_grn_dtl_foc_quantity }}"
                                                                class="tblGridCal_foc_qty tb_moveIndex form-control erp-form-control-sm validNumber">
                                                        </td>
                                                        <td><input type="text"
                                                                name="pd[{{ $loop->iteration }}][sale_rate]"
                                                                data-id="sale_rate"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_sale_rate, 3, '.', '') }}"
                                                                class="tblGridSale_rate tb_moveIndex form-control erp-form-control-sm validNumber"
                                                                readonly></td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][fc_rate]"
                                                                data-id="fc_rate"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_fc_rate, 3, '.', '') }}"
                                                                class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber">
                                                        </td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][rate]"
                                                                data-id="rate"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_rate, 3, '.', '') }}"
                                                                class="{{ $rateColorClass }} tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                        </td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][amount]"
                                                                data-id="amount"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_amount, 3, '.', '') }}"
                                                                class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                        </td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][dis_perc]"
                                                                data-id="dis_perc"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_disc_percent, 2, '.', '') }}"
                                                                class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                        </td>
                                                        <td><input type="text"
                                                                name="pd[{{ $loop->iteration }}][dis_amount]"
                                                                data-id="dis_amount"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_disc_amount, 3, '.', '') }}"
                                                                class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                        </td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][vat_perc]"
                                                                data-id="vat_perc"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_percent, 2, '.', '') }}"
                                                                class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                        </td>
                                                        <td><input type="text"
                                                                name="pd[{{ $loop->iteration }}][vat_amount]"
                                                                data-id="vat_amount"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_amount, 3, '.', '') }}"
                                                                class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                        </td>
                                                        <td><input type="text" name="pd[{{ $loop->iteration }}][batch_no]"
                                                                data-id="batch_no"
                                                                value="{{ isset($dtl->tbl_purc_grn_dtl_batch_no) ? $dtl->tbl_purc_grn_dtl_batch_no : '' }}"
                                                                class="tb_moveIndex form-control erp-form-control-sm"></td>
                                                        @php $prod_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_production_date)))); @endphp
                                                        <td><input type="text"
                                                                name="pd[{{ $loop->iteration }}][production_date]"
                                                                data-id="production_date"
                                                                value="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}"
                                                                title="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}"
                                                                class="date_inputmask tb_moveIndex form-control form-control-sm" />
                                                        </td>
                                                        @php $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_expiry_date)))); @endphp
                                                        <td><input type="text"
                                                                name="pd[{{ $loop->iteration }}][expiry_date]"
                                                                data-id="expiry_date"
                                                                value="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}"
                                                                title="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}"
                                                                class="date_inputmask tb_moveIndex form-control form-control-sm" />
                                                        </td>
                                                        <td><input type="text"
                                                                name="pd[{{ $loop->iteration }}][gross_amount]"
                                                                data-id="gross_amount"
                                                                value="{{ number_format($dtl->tbl_purc_grn_dtl_total_amount, 3, '.', '') }}"
                                                                class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber"
                                                                readonly></td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group btn-group-sm" role="group">
                                                                <button type="button"
                                                                    class="btn btn-danger gridBtn delData"><i
                                                                        class="la la-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        {{-- <tbody class="erp_form__grid_header erp_form__grid_header_bottom">

                                        </tbody> --}}
                                        <tbody class="erp_form__grid_body_total">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="total_grid_qty">
                                                    <input value="0.000" readonly
                                                        type="text"class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                </td>
                                                <td class="total_grid_foc_qty">
                                                    <input value="0.000" readonly type="text"
                                                        class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                                </td>
                                                <td></td>
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
                                                <td></td>
                                                <td></td>
                                                <td></td>
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
                            <div class="col-lg-12">
                                <table class="tableTotal">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="t_total_label">Total Amount:</div>
                                            </td>
                                            <td class="text-right"><span class="t_gross_total t_total">0</span><input
                                                    type="hidden" id="pro_tot"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- <div class="row">
                    <div class="col-lg-12">
                        <div id="discount_calc_block">
                            <div class="row" style="float: right;">
                                <span class="col-sm-3 col-lg-3 erp-col-form-label">Discount</span>
                                <div class="col-lg-9 col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="padding: 5px;">
                                                 <i class="fa fa-percent" style="font-size: 10px;"></i>
                                            </span>
                                        </div>
                                        <div style="width: 50px;">
                                            <input type="text" id="overall_discount" name="overall_discount" value="{{isset($data['current']->grn_overall_discount)?number_format($data['current']->grn_overall_discount,3):0.000}}" class="moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </div>
                                        <div style="width: 88px;">
                                            <input type="text" id="overall_disc_amount" name="overall_disc_amount" value="{{isset($data['current']->grn_overall_disc_amount)?number_format($data['current']->grn_overall_disc_amount,3):0.000}}" class="moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                        <div class="row form-group-block">
                            <div class="col-lg-5">
                                <div class="row">
                                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                                    <div class="col-lg-10">
                                        <textarea type="text" rows="3" id="grn_notes" name="grn_notes" maxlength="255"
                                            class="form-control erp-form-control-sm">{{ isset($data['current']->grn_remarks) ? $data['current']->grn_remarks : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="discount_calc_block">
                                            <div class="row" style="float: right;">
                                                <span class="col-sm-3 col-lg-3 erp-col-form-label">Discount</span>
                                                <div class="col-lg-9 col-sm-9">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" style="padding: 5px;">
                                                                <i class="fa fa-percent" style="font-size: 10px;"></i>
                                                            </span>
                                                        </div>
                                                        <div style="width: 50px;">
                                                            <input readonly type="text"
                                                                {{-- id="overall_discount" name="overall_discount" --}}value="{{ isset($data['current']->grn_overall_discount) ? number_format($data['current']->grn_overall_discount, 3) : 0.0 }}"
                                                                class="moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"
                                                                style="    background: #f6f7f7;">
                                                        </div>
                                                        <div style="width: 88px;">
                                                            <input readonly type="text"
                                                                {{-- id="overall_disc_amount" name="overall_disc_amount" --}}value="{{ isset($data['current']->grn_overall_disc_amount) ? number_format($data['current']->grn_overall_disc_amount, 3) : 0.0 }}"
                                                                class="moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"
                                                                style="    background: #f6f7f7;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (count($data['accounts']) != 0)
                                    <div class="row">
                                        <div class="col-lg-12" style="font-weight: 500;">
                                            Expense:
                                        </div>
                                    </div>{{-- /row --}}
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group-block" style="overflow:auto; height:120px;">
                                                <table id="SalesAccForm"
                                                    class="ErpFormsm expense_acc_table table table-striped table-bordered"
                                                    style="margin-top:0px;">
                                                    <thead>
                                                        <tr>
                                                            <th width="7%">Sr No</th>
                                                            <th width="18%">Acc code</th>
                                                            <th width="40%">Acc Name</th>
                                                            <th width="5%">+ / -</th>
                                                            <th width="15%">Perc</th>
                                                            <th width="15%">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="repeated_datasm">
                                                        @foreach ($data['accounts'] as $expense_accounts)
                                                            @php
                                                                $expense_amount = '';
                                                                $expense_perc = '';
                                                                $expense = \App\Models\TblPurcGrnExpense::where(
                                                                    'grn_id',
                                                                    $id)
                                                                    ->where(
                                                                        'chart_account_id',
                                                                        $expense_accounts->chart_account_id,
                                                                    )
                                                                    ->first(['grn_expense_amount', 'grn_expense_perc']);
                                                                if ($expense != null) {
                                                                    $expense_amount = number_format(
                                                                        $expense->grn_expense_amount,
                                                                        3,
                                                                    );
                                                                }
                                                                if ($expense != null) {
                                                                    $expense_perc = number_format(
                                                                        $expense->grn_expense_perc,
                                                                        3,
                                                                    );
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <input type="text"
                                                                        name="pdsm[{{ $loop->iteration }}][sr_no]"
                                                                        value="{{ $loop->iteration }}"
                                                                        class=" form-control erp-form-control-sm" readonly>
                                                                    <input type="hidden"
                                                                        name="pdsm[{{ $loop->iteration }}][account_id]"
                                                                        value="{{ $expense_accounts->account->chart_account_id }}"
                                                                        data-id="account_id"
                                                                        class="acc_id form-control erp-form-control-sm">
                                                                    <input type="hidden"
                                                                        name="pdsm[{{ $loop->iteration }}][expense_dr_cr]"
                                                                        value="{{ $expense_accounts->expense_accounts_dr_cr }}"
                                                                        data-id="expense_dr_cr"
                                                                        class="expense_dr_cr form-control erp-form-control-sm">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="pdsm[{{ $loop->iteration }}][account_code]"
                                                                        value="{{ $expense_accounts->account->chart_code }}"
                                                                        data-id="account_code"
                                                                        class="acc_code masking moveIndexsm validNumber form-control erp-form-control-sm text-left"
                                                                        maxlength="12" readonly></td>
                                                                <td><input type="text"
                                                                        name="pdsm[{{ $loop->iteration }}][account_name]"
                                                                        value="{{ $expense_accounts->account->chart_name }}"
                                                                        data-id="account_name"
                                                                        class="acc_name form-control erp-form-control-sm "
                                                                        readonly></td>
                                                                <td><input type="text"
                                                                        name="pdsm[{{ $loop->iteration }}][expense_plus_minus]"
                                                                        value="{{ $expense_accounts->expense_accounts_plus_minus }}"
                                                                        data-id="expense_plus_minus"
                                                                        class="expense_plus_minus form-control erp-form-control-sm text-center"
                                                                        readonly></td>
                                                                <td><input type="text"
                                                                        name="pdsm[{{ $loop->iteration }}][expense_perc]"
                                                                        value="{{ isset($expense_perc) ? $expense_perc : '' }}"
                                                                        data-id="expense_perc"
                                                                        class="expense_perc form-control erp-form-control-sm moveIndexsm validNumber validOnlyFloatNumber">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="pdsm[{{ $loop->iteration }}][expense_amount]"
                                                                        value="{{ isset($expense_amount) ? $expense_amount : '' }}"
                                                                        data-id="expense_amount"
                                                                        class="expense_amount form-control erp-form-control-sm moveIndexsm validNumber validOnlyFloatNumber">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>{{-- /row --}}
                                @else
                                    <div class="row">
                                        <div class="offset-lg-6 col-lg-6" style="font-weight: 500;">
                                            Expense accounts not available.
                                        </div>
                                    </div>{{-- /row --}}
                                @endif

                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-9 col-lg-3 text-right">
                                <table class="tableTotal" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="t_total_label">Total Expense:</div>
                                            </td>
                                            <td class="voucher-total-amt align-middle">
                                                <span id="tot_expenses">0</span>
                                                <input type="hidden" name='TotExpen' id='TotExpen' value="0">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="t_total_label">NetTotal:</div>
                                            </td>
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
    @endpermission
@endsection
@section('pageJS')

    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/grn.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{ $case }}';
        $(".expense_amount").keyup(function() {
            TotalExpenseAmount();
        });
    </script>
    <script>

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
                        console.log(response);
                        console.log(data);
                        if (data) {
                            $('#repeated_data>tr>td:first-child').each(function() {
                                var purchase_order_id = $(this).find(
                                    'input[data-id="purchase_order_id"]').val();
                                if (purchase_order_id) {
                                    $(this).parents('tr').remove();
                                }
                            });
                            updateKeys();
                            var tr = '';
                            var total_length = $('#repeated_data>tr').length;

                            function notNullNo(val) {
                                if (val == null) {
                                    return "";
                                } else {
                                    return val = parseFloat(val).toFixed(3);
                                }
                            }
                            for (var p = 0; p < response['all']['po_details'].length; p++) {
                                total_length++;
                                var row = response['all']['po_details'][p];
                                tr += '<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd[' + total_length +
                                    '][sr_no]" value="' + total_length + '" title="' + total_length +
                                    '" class="form-control sr_no erp-form-control-sm handle" readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][purchase_order_id]" data-id="purchase_order_id" value="' +
                                    po_id +
                                    '" class="purchase_order_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_id]" data-id="product_id" value="' + notNull(row[
                                        'product_id']) +
                                    '" class="product_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][uom_id]" data-id="uom_id" value="' + notNull(row['uom_id']) +
                                    '"class="uom_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][product_barcode_id]" data-id="product_barcode_id" value="' +
                                    notNull(row['product_barcode_id']) +
                                    '"class="product_barcode_id form-control erp-form-control-sm " readonly>' +
                                    '<input type="hidden" name="pd[' + total_length +
                                    '][supplier_id]" data-id="supplier_id" value="" class="supplier_id form-control erp-form-control-sm " readonly>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][grn_supplier_barcode]" data-id="grn_supplier_barcode" value="" title="" class="sup_barcode form-control erp-form-control-sm moveIndex" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][pd_barcode]" data-id="pd_barcode" value="' + notNull(row[
                                        'barcode']['product_barcode_barcode']) + '" title="' + notNull(
                                        row['barcode']['product_barcode_barcode']) +
                                    '" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}" class="form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][product_name]" data-id="product_name" value="' + notNull(row[
                                        'product']['product_name']) + '" title="' + notNull(row[
                                        'product']['product_name']) +
                                    '" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td>' +
                                    '<select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" name="pd[' +
                                    total_length + '][uom]" data-id="uom" title="' + row['uom'][
                                        'uom_name'
                                    ] + '">' +
                                    '<option value="' + notNull(row['uom']['uom_id']) + '">' + notNull(
                                        row['uom']['uom_name']) + '</option>' +
                                    '</select>' +
                                    '</td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][packing]" data-id="packing" value="' + notNull(row[
                                        'purchase_order_dtlpacking']) + '" title="' + notNull(row[
                                        'purchase_order_dtlpacking']) +
                                    '" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][quantity]" data-id="quantity" value="' + notNull(row[
                                        'purchase_order_dtlquantity']) + '" title="' + notNull(row[
                                        'purchase_order_dtlquantity']) +
                                    '" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][foc_qty]" data-id="foc_qty" value="' + notNull(row[
                                        'purchase_order_dtlfoc_quantity']) + '" title="' + notNull(row[
                                        'purchase_order_dtlfoc_quantity']) +
                                    '" class="tblGridCal_foc_qty form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][fc_rate]" data-id="fc_rate" value="' + notNull(row[
                                        'purchase_order_dtlfc_rate']) + '" title="' + notNull(row[
                                        'purchase_order_dtlfc_rate']) +
                                    '" class="fc_rate form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][rate]" data-id="rate" value="' + notNullNo(row[
                                        'purchase_order_dtlrate']) + '" title="' + notNullNo(row[
                                        'purchase_order_dtlrate']) +
                                    '" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][amount]" data-id="amount" value="' + notNullNo(row[
                                        'purchase_order_dtlamount']) + '" title="' + notNullNo(row[
                                        'purchase_order_dtlamount']) +
                                    '" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][discount]" data-id="discount" value="' + notNullNo(row[
                                        'purchase_order_dtldisc_percent']) + '" title="' + notNullNo(
                                        row['purchase_order_dtldisc_percent']) +
                                    '" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][discount_val]" data-id="discount_val" value="' + notNullNo(row[
                                        'purchase_order_dtldisc_amount']) + '" title="' + notNullNo(row[
                                        'purchase_order_dtldisc_amount']) +
                                    '" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    /*'<td><input type="text" name="pd['+total_length+'][grn_gst]" data-id="grn_gst" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>' +*/
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_perc]" data-id="vat_perc" value="' + notNullNo(row[
                                        'purchase_order_dtlvat_percent']) + '" title="' + notNullNo(row[
                                        'purchase_order_dtlvat_percent']) +
                                    '" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][vat_val]" data-id="vat_val" value="' + notNullNo(row[
                                        'purchase_order_dtlvat_amount']) + '" title="' + notNullNo(row[
                                        'purchase_order_dtlvat_amount']) +
                                    '" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][batch_no]" data-id="batch_no" class="moveIndex form-control form-control-sm"></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][production_date]" data-id="production_date" value="" class="form-control form-control-sm date_inputmask tb_moveIndex" /></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][expiry_date]" data-id="expiry_date" value="" class="form-control form-control-sm date_inputmask tb_moveIndex" /></td>' +
                                    '<td><input type="text" name="pd[' + total_length +
                                    '][gross_amount]" data-id="gross_amount" value="' + notNullNo(row[
                                        'purchase_order_dtltotal_amount']) + '" title="' + notNullNo(
                                        row['purchase_order_dtltotal_amount']) +
                                    '" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td class="text-center"></td>' +
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
        var accountsHelpUrl = "{{ url('/common/help-open/accountsHelp') }}";
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
                'id': 'grn_supplier_barcode',
                'fieldClass': 'sup_barcode moveIndex',
                'readonly': true
            },
            {
                'id': 'quantity',
                'fieldClass': 'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id': 'foc_qty',
                'fieldClass': 'tblGridCal_foc_qty tb_moveIndex validNumber'
            },
            {
                'id': 'sale_rate',
                'fieldClass': 'tblGridSale_rate tb_moveIndex validNumber',
                'readonly': true
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
                'fieldClass': 'tblGridCal_vat_amount tb_moveIndex validNumber'
            },
            {
                'id': 'batch_no',
                'fieldClass': 'tb_moveIndex'
            },
            {
                'id': 'production_date',
                'fieldClass': 'date_inputmask tb_moveIndex'
            },
            {
                'id': 'expiry_date',
                'fieldClass': 'date_inputmask tb_moveIndex'
            },
            {
                'id': 'gross_amount',
                'fieldClass': 'tblGridCal_gross_amount validNumber',
                'readonly': true
            }
        ];
        var arr_hidden_field = ['purc_grn_dtl_id', 'product_id', 'product_barcode_id', 'uom_id', 'grn_supplier_id',
            'grn_dtl_po_rate'
        ];

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

        $(document).on('keyup', '.tblGridCal_rate', function() {
            changeRateColor($(this))
        })

        function changeRateColor(thix) {
            var val = thix.val();
            var po_val = thix.parents('tr').find('.grn_dtl_po_rate').val();
            thix.removeClass('grn_green grn_red grn_yellow')
            if (val == po_val) {
                thix.addClass('grn_green')
            }
            if (val > po_val && po_val != "") {
                thix.addClass('grn_red')
            }
            if (val < po_val) {
                thix.addClass('grn_yellow')
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

        $('#generatePriceTags').click(function() {

            var formData = {};
            formData.data = [];
            $('.erp_form__grid>tbody.erp_form__grid_body>tr').each(function() {
                var thix = $(this)
                var v = thix.find('input[data-id="production_date"]').val();
                var w = thix.find('input[data-id="expiry_date"]').val();

                var production_date = (v == "") ? "" : v.slice(0, 2) + "-" + v.slice(2, 4) + "-" + v.slice(
                    4);
                var expiry_date = (w == "") ? "" : w.slice(0, 2) + "-" + w.slice(2, 4) + "-" + w.slice(4);

                var tr = {
                    'barcode_id': thix.find('input[data-id="product_barcode_id"]').val(),
                    'qty': thix.find('input[data-id="quantity"]').val(),
                    'packing_date': production_date,
                    'expiry_date': expiry_date,
                }
                formData.data.push(tr);
            });
            console.log(formData);
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
                    var win = window.open(url, "generateBarcodeTags");
                    win.location.reload();
                    if (response) {}
                },
                error: function(response, status) {}
            });
        })

        $('#generateShelfPriceTags').click(function() {
            var formData = {};
            formData.data = [];
            if ($('.erp_form__grid>tbody.erp_form__grid_body>tr').length < 1) {
                toastr.error('Add Some Products');
                return false;
            }
            $('.erp_form__grid>tbody.erp_form__grid_body>tr').each(function() {
                var thix = $(this);
                var tr = {
                    'barcode_id': thix.find('input[data-id="product_barcode_id"]').val(),
                    'qty': thix.find('input[data-id="quantity"]').val(),
                    'gross_amount': thix.find('input[data-id="gross_amount"]').val(),
                }

                formData.data.push(tr);
            });
            console.log(formData);
            var url = '/grn/shelfbarcode-price-tag';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    var win = window.open(url, "generateShelfBarcodeTags");
                    win.location.reload();
                    if (response) {}
                },
                error: function(response, status) {}
            });
        })

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
                                var parent = $('input[value="' + element.product_barcode_id +
                                    '"]').parents('tr');
                                var value = parseFloat(element.product_barcode_sale_rate_rate)
                                    .toFixed(3);
                                parent.find('input[data-id="sale_rate"]').val(value);
                            });
                        }
                    },
                    error: function(response, status) {}
                });
            }
        });


        // Select Multiple Products Functionalities
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
        });
        $(document).on('click', '.btn_add', function(e) {
            e.preventDefault();
            var thix = $(this);
            addRow(thix)

        })

        function addRow(thix) {
            var parentTr = thix.parents('tr');
            if (!parentTr.find('.grn_qty').val()) {
                alert("Please add Qty");
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
                var demand_qty = thix.find('td[data-field="grn_qty"] input.grn_qty').val();
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
        });


        var poXhr = true;
        $(document).on('click', '#getPOData', function() {
            var purchase_order_id = $('#purchase_order_id').val();
            var code = $('#purchase_order').val().trim();
            var validate = true;

            if (valueEmpty(code) && valueEmpty(purchase_order_id)) {
                toastr.error("PO No must be selected.");
                validate = false;
            }

            if (poXhr && validate) {
                poXhr = false;
                $('body').addClass('pointerEventsNone');

                $.ajax({
                    type: 'GET',
                    url: '/grn/po/' + code,
                    success: function(response) {
                        if (response['status'] === 'success') {
                            var total_length = $('.erp_form__grid_body>tr').length;
                            var html = '';

                            function notNull(val) {
                                return val == null ? "" : val;
                            }

                            function notNullNo(val) {
                                return val == null ? "" : parseFloat(val).toFixed(3);
                            }

                            for (var i = 0; i < response['all'].length; i++) {
                                var row = response['all'][i];
                                total_length++;

                                html += `<tr>
                            <th scope="row" style="padding:0">
                                <div class="erp_form__grid_th_input">
                                    <input type="text" readonly name="pd[${total_length}][sr_no]" value="${total_length}" class="sr_no form-control erp-form-control-sm">
                                    <input type="hidden" name="pd[${total_length}][product_id]" value="${notNull(row.product_id)}" class="product_id form-control erp-form-control-sm">
                                    <input type="hidden" name="pd[${total_length}][product_barcode_id]" value="${notNull(row.product_barcode_id)}" class="product_barcode_id form-control erp-form-control-sm">
                                    <input type="hidden" name="pd[${total_length}][uom_id]" value="${notNull(row.uom_id)}" class="uom_id form-control erp-form-control-sm">
                                    <input type="hidden" name="pd[${total_length}][grn_supplier_id]" value="${notNull(row.grn_supplier_id)}" class="grn_supplier_id form-control erp-form-control-sm">
                                    <input type="hidden" name="pd[${total_length}][grn_dtl_po_rate]" value="${notNullNo(row.purchase_order_dtlrate)}" class="grn_dtl_po_rate form-control erp-form-control-sm">
                                </div>
                            </th>
                            <td><input type="text" readonly name="pd[${total_length}][pd_barcode]" value="${notNull(row.product_barcode_barcode)}" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm"></td>
                            <td><input type="text" readonly name="pd[${total_length}][product_name]" value="${notNull(row.product_name)}" class="product_name form-control erp-form-control-sm"></td>
                            <td>
                                <select name="pd[${total_length}][pd_uom]" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                    <option value="${notNull(row.uom_id)}" selected>${notNull(row.uom_name)}</option>
                                </select>
                            </td>
                            <td><input type="text" readonly name="pd[${total_length}][pd_packing]" value="${notNull(row.purchase_order_dtlpacking)}" class="pd_packing form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][sup_barcode]" value="${notNull(row.grn_supplier_barcode)}" class="sup_barcode tb_moveIndex form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][quantity]" value="${notNull(row.purchase_order_dtlquantity)}" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][foc_qty]" value="${notNull(row.purchase_order_dtlfoc_quantity)}" class="tblGridCal_foc_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][sale_rate]" value="${notNullNo(row.purchase_order_dtlsale_rate)}" readonly class="tblGridSale_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][fc_rate]" value="${notNullNo(row.purchase_order_dtlfc_rate)}" class="fc_rate tb_moveIndex validNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][rate]" value="${notNullNo(row.purchase_order_dtlrate)}" class="tblGridCal_rate tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][amount]" value="${notNullNo(row.purchase_order_dtlamount)}" class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][dis_perc]" value="${notNullNo(row.purchase_order_dtldisc_percent)}" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][dis_amount]" value="${notNullNo(row.purchase_order_dtldisc_amount)}" class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][vat_perc]" value="${notNullNo(row.purchase_order_dtlvat_percent)}" class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][vat_amount]" value="${notNullNo(row.purchase_order_dtlvat_amount)}" class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][batch_no]" value="${notNull(row.batch_no)}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                            <td><input type="text" name="pd[${total_length}][production_date]" value="${notNull(row.production_date)}" class="date_inputmask tb_moveIndex form-control erp-form-control-sm" title="${notNull(row.production_date)}"></td>
                            <td><input type="text" name="pd[${total_length}][expiry_date]" value="${notNull(row.expiry_date)}" class="date_inputmask tb_moveIndex form-control erp-form-control-sm" title="${notNull(row.expiry_date)}"></td>
                            <td><input type="text" name="pd[${total_length}][gross_amount]" value="${notNullNo(row.purchase_order_dtltotal_amount)}" readonly class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm"></td>
                            <td>
                                <div class="erp_form__grid_th_btn">
                                    <button type="button" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-danger btn-sm del_row">
                                        <i class="la la-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                            }

                            $('.erp_form__grid_body').append(html);

                            $('.erp_form__grid_body tr').each(function() {
                                var row = $(this);
                                var product_id = row.find('.product_id');
                                // funcHeaderCalc(row);
                                // changeRateColor(product_id);
                            });

                            // funcRowInit();
                            // updateHiddenFields();
                        } else {
                            toastr.error("PO No is not correct.");
                        }

                        poXhr = true;
                        $('body').removeClass('pointerEventsNone');
                    },
                    error: function() {
                        poXhr = true;
                        $('body').removeClass('pointerEventsNone');
                    }
                });
            }
        });
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js?v=1') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('js/pages/js/common/user-form-setting.js') }}" type="text/javascript"></script> --}}
@endsection
