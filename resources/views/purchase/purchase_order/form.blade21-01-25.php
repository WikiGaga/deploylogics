@extends('layouts.layout')
@section('title', 'Purchase Order')

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
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
            $menu_id = $data['menu_id'];
            $date =  date('d-m-Y');
            $delivery_date =  date('d-m-Y');
            if(session('po_draft_id') != null){
                  $draft_id = session('po_draft_id');
                  $data['current'] = \App\Models\Draft\DraftPurcPurchaseOrder::with('po_details','supplier')->where('purchase_order_id',$draft_id)->where(\App\Library\Utilities::currentBCB())->first();
            }

            $dataGenerateBarcodes = session('dataGenerateBarcodes');
        }
        if($case == 'edit'){
            $id = $data['current']->purchase_order_id;
            $code = $data['current']->purchase_order_code;
        }
        if($case == 'edit' || isset($draft_id)){
            $menu_id = $data['menu_id'];
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_entry_date))));
            $delivery_date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_delivery_date))));
            $lpo_id = isset($data['current']->lpo)?$data['current']->lpo->lpo_id:"";
            $lpo_code = isset($data['current']->lpo)?$data['current']->lpo->lpo_code:"";
            $comparative_quotation_id = isset($data['current']->comparative_quotation)?$data['current']->comparative_quotation->comparative_quotation_id:"";
            $comparative_quotation_code = isset($data['current']->comparative_quotation)?$data['current']->comparative_quotation->comparative_quotation_code:"";
            $supplier_id = isset($data['current']->supplier)?$data['current']->supplier->supplier_id:"";;
            $supplier_code = isset($data['current']->supplier)?$data['current']->supplier->supplier_name:"";;
            $payment_terms = $data['current']->payment_mode_id;
            $credit_days = $data['current']->purchase_order_credit_days;
            $currency_id = $data['current']->currency_id;
            $payment_mode_id = $data['current']->payment_mode_id;
            $priority_value = $data['current']->priority_value;
            $exchange_rate = $data['current']->purchase_order_exchange_rate;
            $po_details = $data['current']->po_details;
            $remarks = $data['current']->purchase_order_remarks;
            $purchase_order_overall_discount = $data['current']->purchase_order_overall_discount;
            $purchase_order_overall_disc_amount = $data['current']->purchase_order_overall_disc_amount;
        }
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="purchase_order_form" class="kt-form" method="post" action="{{ action('Purchase\PurchaseOrderController@store',isset($id)?$id:"") }}">
    <input type="hidden" value='{{$form_type}}' id="form_type" name="form_type">
    <input type="hidden" value='{{$menu_id}}' id="menu_id">
    <input type="hidden" value='{{isset($id)?$id:""}}' id="form_id">
    <input type="hidden" value='{{isset($draft_id)?$draft_id:""}}' name="po_draft_id" id="po_draft_id">
    @csrf
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    {{isset($code)?$code:""}}
                                </div>
                            </div>
                            <div class="col-lg-6 text-right">
                                @if($case == 'new')
                                <button type="button" id="saveDraftEntry" class="btn btn-sm btn-warning">Save as Draft</button>
                                @endif
                                <button type="button" id="getListOfDraftEntries" class="btn btn-sm btn-primary">Draft List</button>
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
                                    <input type="text" name="po_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4">
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
                                        <input type="text" value="{{isset($lpo_code)?$lpo_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','lpoPoHelp')}}" id="lpo_generation_no" name="lpo_generation_no" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
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
                    </div> --}}
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Vendor Name:<span class="required">*</span></label>
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
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Delivery Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="po_delivery_date" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{isset($delivery_date)?$delivery_date:""}}" id="kt_datepicker_3" autofocus/>
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
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Payment Terms:</label>
                            <div class="col-lg-6">
                                <div class="input-group erp-select2-sm">
                                    <select name="payment_terms"  id="payment_terms" class="moveIndex kt-select2 form-control erp-form-control-sm">
                                        <option value="0">Select</option>
                                        @foreach($data['payment_terms'] as $payment_term)
                                            @php $payment_terms_id = isset($payment_terms)?$payment_terms:""; @endphp
                                            <option value="{{$payment_term->payment_term_id}}" {{$payment_terms_id == $payment_term->payment_term_id?"selected":""}}>{{$payment_term->payment_term_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append" style="width: 33%;">
                                        <input type="text" value="{{isset($credit_days)?$credit_days:""}}" id="payment_mode" name="payment_mode" class="moveIndex form-control erp-form-control-sm validNumber">
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
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 currency" id="po_currency" name="po_currency">
                                        <option value="0">Select</option>
                                        @if($case == 'edit' || isset($draft_id))
                                            @php $currency_id = isset($currency_id)?$currency_id:'';@endphp
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
                                <input type="text" value="{{isset($exchange_rate)?$exchange_rate:""}}" id="exchange_rate" name="exchange_rate" class="moveIndex validNumber form-control erp-form-control-sm">
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
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 payment_mode" id="payment_mode_id" name="payment_mode_id">
                                        <option value="0">Select</option>
                                        @if($case == 'edit' || isset($draft_id))
                                            @php $payment_mode_id = isset($payment_mode_id)?$payment_mode_id:'';@endphp
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
                        <div class="row form-group-block">
                            <label class="col-lg-6 erp-col-form-label">Priority:</label>
                            <div class="col-lg-6 priority">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 priority" id="priority_id" name="priority_id">
                                        <option value="0">Select</option>
                                        @if($case == 'edit' || isset($draft_id))
                                            @foreach($data['po_priority'] as $priority)
                                                <option value="{{strtolower($priority->constants_value)}}" {{strtolower($priority->constants_value)==$priority_value?'selected':''}}>{{$priority->constants_value}}</option>
                                            @endforeach
                                        @else
                                            @foreach($data['po_priority'] as $priority)
                                                <option value="{{strtolower($priority->constants_value)}}">{{$priority->constants_value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Shipment Mode: </label>
                            <div class="col-lg-6 priority">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 priority" id="shipment_mode_id" name="shipment_mode_id">
                                        <option value="0">Select</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Shipment Provided By: </label>
                            <div class="col-lg-6 priority">
                                <div class="erp-select2">
                                    <select class="moveIndex form-control erp-form-control-sm kt-select2 priority" id="shipment_provided_id" name="shipment_provided_id">
                                        <option value="0">Select</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4"></div>
                    <div class="col-lg-4 text-right">
                        <div class="input-group">
                            <div class="input-group-prepend"><button type="button" class="btn btn-sm btn-label-danger btn-bold" id="tb_product_detail" style="padding: 0 15px;font-weight: 500;">Stock</button></div>
                            <input type="text" class="form-control erp-form-control-sm" value="0" id="current_product_stock" readonly style="font-size: 18px;background: rgba(253, 57, 122, 0.1);color: #fd397a;font-weight: 500;text-align: center;">
                            <div class="input-group-append"><button type="button" class="btn btn-sm btn-label-success btn-bold" id="tb_analysis_detail" style="padding: 0 15px;">TP Analysis</button></div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Auto Demand Refrence:</label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{isset($autoDemandCode)?$autoDemandCode:''}}" name="auto_demand_code" id="auto_demand_code" data-url="{{action('Common\DataTableController@inlineHelpOpen','autoDemandHelp')}}" class="open_inline__help form-control erp-form-control-sm open_modal moveIndex" placeholder="Auto Demand Refrence">
                                        <input type="hidden" value="{{isset($auto_demand_id)?$auo_demand_id:''}}" name="auto_demand_id" id="auto_demand_id" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text group-input-btn get-ad-data" id="adGetData">
                                                Go
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
                {{-- <div class="row form-group-block">
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
                                        <input type="text" value="{{isset($comparative_quotation_code)?$comparative_quotation_code:""}}" name="comparative_quotation_code" id="comparative_quotation_code" data-url="{{action('Common\DataTableController@helpOpen','comparativeQuotationHelp')}}" class="form-control erp-form-control-sm open_modal moveIndex moveIndex2 OnlyEnterAllow" placeholder="Enter here">
                                        <input type="hidden" value="{{isset($comparative_quotation_id)?$comparative_quotation_id:""}}" name="comparative_quotation_id" id="comparative_quotation_id" readonly>
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
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-brand btn-sm" id="makePD" style="padding: 4px 6px;">
                            Select Multiple Products
                        </button>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-lg-6">
                        <button type="button" id="getListOfProduct" class="btn btn-sm btn-primary">Product help</button>
                        <div style="font-size: 9px;color: red;">(Click Here or Press F4)</div>
                    </div>
                    <div class="col-lg-6 text-right">
                        <div class="data_entry_header">
                            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
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
                                    @foreach($headings as $key=>$heading)
                                        <li >
                                            <label>
                                                <input value="{{$key}}" type="checkbox" checked> {{$heading}}
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
                        </div>
                    </div>
                </div>
                <div class="form-group-block">
                    <div class="erp_form___block">
                        <div class="table-scroll form_input__block">
                            <table class="po_table table_pit_list table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                    <tr>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                                <input id="constants_id" readonly type="hidden" class="constants_id form-control erp-form-control-sm">
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
                                                <input readonly id="sale_rate" type="text" class="tblGridCal_sale_rate validNumber form-control erp-form-control-sm">
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
                                                <input id="dis_amount" type="text" class="tblGridCal_discount_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
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
                                                <select class="pd_tax_on form-control erp-form-control-sm" id="pd_tax_on" name="pd_tax_on">
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
                                                <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
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
                                @if(isset($po_details))
                                    @foreach($po_details as $dtl)
                                        @php
                                            $i = $loop->iteration;
                                        @endphp
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$i}}" name="pd[{{$i}}][sr_no]"  class="sr_count form-control erp-form-control-sm handle" readonly>
                                                @if(isset($draft_id))
                                                    <input type="hidden" name="pd[{{$i}}][purchase_order_dtl_id]" data-id="purchase_order_dtl_id" class="purchase_order_dtl_id form-control erp-form-control-sm handle" readonly>
                                                @else
                                                    <input type="hidden" name="pd[{{$i}}][purchase_order_dtl_id]" data-id="purchase_order_dtl_id" value="{{$dtl->purchase_order_dtl_id}}" class="purchase_order_dtl_id form-control erp-form-control-sm handle" readonly>
                                                @endif
                                                <input type="hidden" name="pd[{{$i}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$i}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$i}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$i}}][lpo_id]" data-id="lpo_id" value="{{$dtl->lpo_id}}" class="lpo_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$i}}][comparative_quotation_id]" data-id="comparative_quotation_id" value="{{$dtl->comparative_quotation_id}}" class="comparative_quotation_id form-control erp-form-control-sm" readonly>
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$i}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$i}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>

                                            <td>
                                                <input type="text" name="pd[{{ $i }}][quantity]" data-id="quantity" value="{{ $dtl->purchase_order_dtlquantity }}" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][rate]" data-id="rate" value="{{number_format($dtl->purchase_order_dtlrate,3, '.', '')}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" >
                                            </td>
                                            <td>
                                                <input readonly type="text" name="pd[{{$i}}][sale_rate]" data-id="sale_rate"  value="{{number_format($dtl->purchase_order_dtlsale_rate,3, '.', '')}}" class="tblGridCal_sale_rate form-control erp-form-control-sm validNumber">
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][sys_qty]" data-id="sys_qty" value="{{isset($dtl->purchase_order_dtlsys_quantity)?$dtl->purchase_order_dtlsys_quantity:""}}" class="tblGridCal_sys_qty form-control erp-form-control-sm validNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][mrp]" data-id="mrp"  value="{{number_format($dtl->purchase_order_dtlmrp,3, '.', '')}}" class="tblGridCal_mrp tb_moveIndex form-control erp-form-control-sm validNumber" >
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][cost_amount]" data-id="cost_amount"  value="{{number_format($dtl->purchase_order_dtlamount,3, '.', '')}}" class="tblGridCal_cost_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly >
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][dis_perc]" data-id="dis_perc"  value="{{number_format($dtl->purchase_order_dtldisc_percent,3, '.', '')}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" >
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][dis_amount]" data-id="dis_amount"  value="{{number_format($dtl->purchase_order_dtldisc_amount,3, '.', '')}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][after_dis_amount]" data-id="after_dis_amount" value="{{number_format($dtl->purchase_order_dtlafter_dis_amount,3, '.', '')}}" class="tblGridCal_after_discount_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <select class="pd_tax_on form-control erp-form-control-sm" data-id="pd_tax_on" name="pd[{{$i}}][pd_tax_on]">
                                                    @foreach($data['tax_on'] as $tax_on)
                                                        <option value="{{strtolower($tax_on->constants_value)}}" {{strtolower($dtl->purchase_order_dtltax_on) == strtolower($tax_on->constants_value)?"selected":""}}>{{$tax_on->constants_value}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][gst_perc]" data-id="gst_perc"  value="{{number_format($dtl->purchase_order_dtlvat_percent,4, '.', '')}}" class="tblGridCal_gst_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" >
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][gst_amount]" data-id="gst_amount"  value="{{number_format($dtl->purchase_order_dtlvat_amount,4, '.', '')}}" class="tblGridCal_gst_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" >
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][fed_perc]" data-id="fed_perc"  value="{{number_format($dtl->purchase_order_dtlfed_perc,4, '.', '')}}" class="tblGridCal_fed_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" >
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][fed_amount]" data-id="fed_amount"  value="{{number_format($dtl->purchase_order_dtlfed_amount,4, '.', '')}}"  class="tblGridCal_fed_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" >
                                            </td>
                                            <td>
                                                <select class="form-control erp-form-control-sm pd_disc" data-id="pd_disc" name="pd[{{$i}}][pd_disc]">
                                                    @foreach($data['disc_on'] as $disc_on)
                                                        <option value="{{strtolower($disc_on->constants_value)}}" {{strtolower($dtl->purchase_order_dtldisc_on) == strtolower($disc_on->constants_value)?"selected":""}}>{{$disc_on->constants_value}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][spec_disc_perc]" data-id="spec_disc_perc"  value="{{number_format($dtl->purchase_order_dtlspec_disc_perc,3, '.', '')}}" class="tblGridCal_spec_disc_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" >
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][spec_disc_amount]" data-id="spec_disc_amount"  value="{{number_format($dtl->purchase_order_dtlspec_disc_amount,3, '.', '')}}"  class="tblGridCal_spec_disc_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][gross_amount]" data-id="gross_amount"  value="{{number_format($dtl->purchase_order_dtlgross_amount,3, '.', '')}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][net_amount]" data-id="net_amount"  value="{{number_format($dtl->purchase_order_dtltotal_amount,3, '.', '')}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][net_tp]" data-id="net_tp"  value="{{number_format($dtl->purchase_order_dtlnet_tp,3, '.', '')}}" class="tblGridCal_net_tp form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][last_tp]" data-id="last_tp"  value="{{number_format($dtl->purchase_order_dtllast_tp,3, '.', '')}}" class="tblGridCal_last_tp form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][vend_last_tp]" data-id="vend_last_tp"  value="{{number_format($dtl->purchase_order_dtlvend_last_tp,3, '.', '')}}" class="tblGridCal_vend_last_tp form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][tp_diff]" data-id="tp_diff"  value="{{number_format($dtl->purchase_order_dtltp_diff,3, '.', '')}}" class="tblGridCal_tp_diff form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][gp_perc]" data-id="gp_perc"  value="{{number_format($dtl->purchase_order_dtlgp_perc,3, '.', '')}}" class="tblGridCal_gp_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][gp_amount]" data-id="gp_amount"  value="{{number_format($dtl->purchase_order_dtlgp_amount,3, '.', '')}}" class="tblGridCal_gp_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber" readonly>
                                            </td>
                                            <td>
                                                <input type="text" data-id="remarks" name="pd[{{$i}}][remarks]" value="{{isset($dtl->purchase_order_dtl_remarks)?$dtl->purchase_order_dtl_remarks:""}}" class="form-control erp-form-control-sm tb_moveIndex">
                                            </td>
                                            <td>
                                                <input type="text" name="pd[{{$i}}][fc_rate]" data-id="fc_rate" value="{{number_format($dtl->purchase_order_dtlfc_rate,3, '.', '')}}" class="tblGridCal_fc_rate tb_moveIndex form-control erp-form-control-sm validNumber">
                                            </td>
                                            <td>
                                                <select class="pd_uom form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$i}}][pd_uom]">
                                                    @if(isset($dtl->uom_list) && is_array($dtl->uom_list) && count($dtl->uom_list) > 0)
                                                        @foreach($dtl->uom_list as $unit)
                                                            <option value="{{ $unit->uom_id }}" {{(isset($dtl->uom->uom_id) == $unit->uom_id)?"selected":""}}>{{ $unit->uom_name ?? "" }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" data-id="pd_packing" name="pd[{{$i}}][pd_packing]" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
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
                {{--<div class="row">
                    <div class="offset-md-10 col-lg-2 text-right">
                        <table class="tableTotal" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td><div class="t_total_label">Total:</div></td>
                                <td><span class="t_total t_gross_total">0</span><input type="hidden" id="pro_tot"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>--}}
                @include('purchase.purchase_order.summary_total')
                <div class="row form-group-block">
                    <div class="col-lg-6">
                        <label class="erp-col-form-label">Remarks:</label>
                        <textarea type="text" rows="5" id="po_notes" name="po_notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($remarks)?$remarks:''}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    <!-- end:: Content -->
    @php
        session()->forget('po_draft_id');
    @endphp
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase-order.js?v=2') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{$case}}';
        var data_po_selected = "";
    </script>
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode tb_moveIndex open_inline__help',
                'message':'Enter Barcode',
                'require':true,
                'readonly':true
                //  'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id': 'quantity',
                'fieldClass': 'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber',
                'message': 'Enter Quantity',
                'require':true,
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id':'sale_rate',
                'fieldClass':'sale_rate validNumber',
                'readonly':true
            },
            {
                'id':'sys_qty',
                'fieldClass':'validNumber tblGridCal_sys_qty',
                'readonly':true
            },
            {
                'id':'mrp',
                'fieldClass':'tblGridCal_mrp tb_moveIndex validNumber'
            },
            {
                'id':'cost_amount',
                'fieldClass':'tblGridCal_cost_amount validNumber',
                'readonly':true
            },
            {
                'id':'dis_perc',
                'fieldClass':'tblGridCal_discount_perc tb_moveIndex validNumber'
            },
            {
                'id':'dis_amount',
                'fieldClass':'tblGridCal_discount_amount tb_moveIndex validNumber',
            },
            {
                'id':'after_dis_amount',
                'fieldClass':'tblGridCal_after_discount_amount validNumber',
                'readonly':true
            },
            {
                'id':'pd_tax_on',
                'fieldClass': 'pd_tax_on',
                'message':'Select Tax ON',
                'require':true,
                'type':'select'
            },
            {
                'id':'gst_perc',
                'fieldClass':'tblGridCal_gst_perc tb_moveIndex validNumber'
            },
            {
                'id':'gst_amount',
                'fieldClass':'tblGridCal_gst_amount validNumber',
            },
            {
                'id':'fed_perc',
                'fieldClass':'tblGridCal_fed_perc tb_moveIndex validNumber'
            },
            {
                'id':'fed_amount',
                'fieldClass':'tblGridCal_fed_amount validNumber',
                'readonly':true
            },
            {
                'id':'pd_disc',
                'fieldClass':'pd_disc',
                'message':'Select Disc ON',
                'require':true,
                'type':'select'
            },
            {
                'id':'spec_disc_perc',
                'fieldClass':'tblGridCal_spec_disc_perc tb_moveIndex validNumber'
            },
            {
                'id':'spec_disc_amount',
                'fieldClass':'tblGridCal_spec_disc_amount validNumber',
                'readonly':true
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            },
            {
                'id':'net_amount',
                'fieldClass':'tblGridCal_amount validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id':'net_tp',
                'fieldClass':'tblGridCal_net_tp validNumber',
                'readonly':true
            },
            {
                'id':'last_tp',
                'fieldClass':'tblGridCal_last_tp validNumber',
                'readonly':true
            },
            {
                'id':'vend_last_tp',
                'fieldClass':'tblGridCal_vend_last_tp validNumber',
                'readonly':true
            },
            {
                'id':'tp_diff',
                'fieldClass':'tblGridCal_tp_diff validNumber',
                'readonly':true
            },
            {
                'id':'gp_perc',
                'fieldClass':'tblGridCal_gp_perc validNumber',
                'readonly':true
            },
            {
                'id':'gp_amount',
                'fieldClass':'tblGridCal_gp_amount validNumber',
                'readonly':true
            },
            {
                'id':'remarks',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'fc_rate',
                'fieldClass':'tblGridCal_fc_rate tb_moveIndex validNumber'
            },
            {
                'id':'pd_uom',
                'fieldClass':'pd_uom field_readonly',
                'type':'select'
            },
            {
                'id':'pd_packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
        ];
        var  arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>

    @yield('summary_total_pageJS')

    <script src="{{ asset('js/pages/js/add-row-repeated_new.js?v='.time()) }}" type="text/javascript"></script>
    @include('partial_script.po_header_calc');
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js?v='.time()) }}" type="text/javascript"></script>
    <script>


        $(document).on('keyup', '.erp_form__grid_header input', function() {
            getRate($(this));
        })

        function getRate(thix)
        {
            var net_tp_field = thix.parents('tr').find('.tblGridCal_sale_rate');
            var sale_rate = net_tp_field.val();
            var net_tp = thix.parents('tr').find('.tblGridCal_net_tp').val();

            if(parseFloat(net_tp) > parseFloat(sale_rate)){
                toastr.warning("Sale Rate is less than Net TP.");
            }

        }

        var supplierFocQtyArr = [];
        $(document).on('keyup','.tblGridCal_qty_not',function(){
            var thix = $(this);
            var tr =  thix.parents('tr');
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
            supplierFocQtyArr.forEach(function(item){
                if(product_id == item['product_id'] && supplier_id == item['supplier_id']){
                    purc_qty = item['purc_qty']
                    foc_qty = item['foc_qty']
                    base_unit = item['base_unit']
                    sendAjaxReq = false;
                    checkPurc = true;
                }
            });
            if(checkPurc){
                var totalQty = packing * base_unit * qty;
                foc = ( totalQty / purc_qty) * foc_qty;
                tr.find('#foc_qty').val(foc.toFixed(3));
                tr.find('.foc_qty').val(foc.toFixed(3));
            }
            if(sendAjaxReq){
                var supplierFocQty = [];
                supplierFocQty['product_id'] = product_id;
                supplierFocQty['supplier_id'] = supplier_id;
                var formData = {
                    'product_id' : product_id,
                    'supplier_id' : supplier_id,
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : 'POST',
                    url         : '/barcode/get-supplier-foc',
                    dataType	: 'json',
                    data        : formData,
                    success: function(response) {
                        if(response.data.supplier_foc != null){
                            if(response.data.supplier_foc.purc_qty !== undefined &&
                                response.data.supplier_foc.foc_qty !== undefined &&
                                response.data.supplier_foc.base_unit !== undefined){

                                supplierFocQty['purc_qty'] = response.data.supplier_foc.purc_qty;
                                supplierFocQty['foc_qty'] = response.data.supplier_foc.foc_qty;
                                supplierFocQty['base_unit'] = response.data.supplier_foc.base_unit;
                                purc_qty = response.data.supplier_foc.purc_qty;
                                foc_qty = response.data.supplier_foc.foc_qty;
                                base_unit = response.data.supplier_foc.base_unit;
                                supplierFocQtyArr.push(supplierFocQty);
                                var totalQty = packing * base_unit * qty;
                                foc = ( totalQty / purc_qty) * foc_qty;
                                tr.find('#foc_qty').val(foc.toFixed(3));
                                tr.find('.foc_qty').val(foc.toFixed(3));
                            }
                        }
                    }
                })
            }
        })
        $(document).on('click','#upload_documents',function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                form_id : $('#form_id').val(),
                form_type : $('#form_type').val(),
                menu_id : $('#menu_id').val(),
                form_code : $('.erp-page--title').text().trim(),

            }
            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,formData);
        })
       // $(document).on('click','#makePD',function(){
        $('#makePD').click(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {
                supplier_id : $('#supplier_id').val(),
                form_type : $('#form_type').val(),
            }
            var data_url = '/common/select-multiple-products';
            $('#kt_modal_xl').modal('show').find('.modal-content').load(data_url,formData);
        })
        $(document).on('click','.btn_add',function(e){
            e.preventDefault();
            var thix = $(this);
            addRow(thix)

        })
        function addRow(thix){
            var parentTr = thix.parents('tr');
            if(!parentTr.find('.demand_qty').val())
            {
                alert("Please add demand qty");
            }else
            {
                var item_duplicate = false;
                $(document).find('#smp_selected_products table tbody tr').each(function(){
                    if($(this).find('td[data-field="product_barcode_barcode"]>span').text() == parentTr.find('td[data-field="product_barcode_barcode"]>span').text()){
                        toastr.warning("Item alread added.");
                        item_duplicate = true;
                    }
                })
                if(item_duplicate == true){
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
                lastTd += "<i class='la la-times del_row' style='position: relative;background: #f44336;padding: 2px 2px;color: #fff;margin-left: 3px;top: -3px;'></i>";
                lastTd += "</span>";
                $(document).find('#smp_selected_products table tbody tr:last-child td:last-child').html(lastTd);
                toastr.success("Item added.");
            }
        }
        $(document).on('click','.del_row',function(e){
            e.preventDefault();
            $(this).parents('tr').remove();
        })


    </script>

    <script>
        var form_modal_type = 'purchase_order';
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
                pd_disc = 'ga';
            }
            if(valueEmpty(pd_tax_on)){
                pd_tax_on = 'da';
            }
            $('.erp_form__grid_header').find('.pd_tax_on').val(pd_tax_on).change();
            $('.erp_form__grid_header').find('.pd_disc').val(pd_disc).change();
        }
    </script>

    <script !src="">
            var draft_purchase_order_id = '';
            @if($case == 'new')
                @if(isset($draft_id))
                    var draft_purchase_order_id = '{{$draft_id}}';
                @endif
            var baseUrl = '/purchase-order-draft/form';
            @endif
            @if($case == 'edit')
                @if(isset($id))
                    var draft_purchase_order_id = '{{$id}}';
                @endif
            var baseUrl = '/purchase-order/form';
            @endif

           var xhrGetData = true;
           var saveAsDraft = false;
           var sendReq = true;
           var fieldsList = [
               'purchase_order_dtl_id',
               'product_id',
               'uom_id',
               'product_barcode_id',
               'lpo_id',
               'comparative_quotation_id',
               'pd_barcode',
               'product_name',
               'quantity',
               'rate',
               'sale_rate',
               'sys_qty',
               'mrp',
               'cost_amount',
               'dis_perc',
               'dis_amount',
               'after_dis_amount',
               'pd_tax_on',
               'gst_perc',
               'gst_amount',
               'fed_perc',
               'fed_amount',
               'pd_disc',
               'spec_disc_perc',
               'spec_disc_amount',
               'gross_amount',
               'net_amount',
               'net_tp',
               'last_tp',
               'vend_last_tp',
               'tp_diff',
               'gp_perc',
               'gp_amount',
               'remarks',
               'fc_rate',
               'pd_uom',
               'pd_packing',
           ];
           var f_len = fieldsList.length;
           var old_data = [];
           function funcPurchaseOrderDraft(){
               var tr_len = $('.erp_form__grid_body>tr').length;
               var validate = true;
               if(valueEmpty(tr_len)){
                   validate = false;
               }

               if(validate && xhrGetData){
                   xhrGetData = false;
                   old_data = [];
                   $('.erp_form__grid_body>tr').each(function(index){
                       var thix = $(this);
                       var m_data = [];
                       fieldsList.forEach(function(name){
                           m_data[name] = thix.find('[data-id="'+name+'"]').val();
                       });
                       old_data.push(m_data);
                   });
                   var form = document.getElementById('purchase_order_form');
                   var formData = new FormData(form);
                   var url = baseUrl;
                   if(!valueEmpty(draft_purchase_order_id)){
                       url = baseUrl + '/'+ draft_purchase_order_id;
                   }
                  // cd(formData);
                   $.ajax({
                       /*headers: {
                           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                       },*/
                       type: "POST",
                       url: url,
                       dataType	: 'json',
                       data        : formData,
                       contentType : false,
                       processData : false,
                       beforeSend: function( xhr ) {
                           // $('body').addClass('pointerEventsNone');
                       },
                       success: function(response,data) {
                           if(response.status == 'success'){
                               // toastr.success(response.message);
                               draft_purchase_order_id = response['data']['purchase_order_id'];
                               $('#po_draft_id').val(draft_purchase_order_id);
                               sendReq = false;
                               if(saveAsDraft){
                                   toastr.success("Entry Save as Draft");
                               }
                           }else{
                               toastr.error(response.message);
                           }
                           xhrGetData = true;
                           $('#saveDraftEntry').attr('disabled',false)
                           saveAsDraft = false;
                       },
                       error: function(response,status) {
                           // toastr.error(response.responseJSON.message);
                           xhrGetData = true;
                           $('#saveDraftEntry').attr('disabled',false)
                           saveAsDraft = false;
                       }
                   });
               }else{
                   $('#saveDraftEntry').attr('disabled',false)
                   saveAsDraft = false;
               }
           }
           function initFuncPurchaseOrderDraft(){
                if(old_data.length != 0){
                    var update_data = [];
                    $('.erp_form__grid_body>tr').each(function(index){
                        var thix = $(this);
                        var m_data = [];
                        fieldsList.forEach(function(name){
                            m_data[name] = thix.find('[data-id="'+name+'"]').val();
                        });
                        update_data.push(m_data);
                    });

                    var update_bar = [];
                    var old_bar = [];
                    old_data.forEach(function (it) {
                        old_bar.push(it.product_barcode_id);
                    });
                    if(!sendReq) {
                        update_data.forEach(function (item) {
                            update_bar.push(item.product_barcode_id);
                            var product_id = item.product_id;
                            var product_barcode_id = item.product_barcode_id;
                            old_data.forEach(function (it) {
                                if (product_id == it.product_id
                                    && product_barcode_id == it.product_barcode_id) {
                                    fieldsList.forEach(function (name) {
                                        if (item[name] != it[name]) {
                                            sendReq = true;
                                        }
                                    });
                                    return true
                                }
                            })
                        })
                    }
                    if(!sendReq){
                        update_bar.forEach(function(val){
                            if(!old_bar.includes(val)){
                                sendReq = true;
                                // cd("includes");
                            }
                        });
                    }
                    if(!sendReq){
                    if(update_bar.length !== old_bar.length){
                        sendReq = true;
                        //cd("i sequal");
                    }
                }
                }
                if(sendReq){
                    //  cd("F: " + f);
                    funcPurchaseOrderDraft();
                }else{
                    $('#saveDraftEntry').attr('disabled',false)
                    saveAsDraft = false;
                }
           }
           @if(!$data['already_exits'])
           var f = 0;
           setInterval(function(){
               f = f+1;
               initFuncPurchaseOrderDraft()
           },15000);
           @endif

            $(document).on('click','#saveDraftEntry',function(e){
                $('#saveDraftEntry').attr('disabled',true)
                saveAsDraft = true;
                initFuncPurchaseOrderDraft();
            });
        $(document).on('click','#getListOfDraftEntries',function(e){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {

            }
            var url = '{{action('Purchase\PurchaseOrderController@listDraft')}}';

            $('#kt_modal_md').modal('show').find('.modal-content').load(url,formData);
        });
        $(document).on('click','#po_list_draft>table>tbody>tr>td',function(e){
            var thix = $(this);
            var val  = thix.parents('tr').find('.purchase_order_id').val();

            var validate = true;
            var removeEntry = false;
            var baseUrl = '/purchase-order-draft/create-draft/';
            if(thix.find('button').length != 0){
                removeEntry = true;
                baseUrl = '/purchase-order-draft/delete/';
            }
            if(valueEmpty(val)){
                validate = false;
            }
            if(validate){
                var formData = {};
                var url = baseUrl+val;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
                        // $('body').addClass('pointerEventsNone');
                    },
                    success: function(response,data) {
                        if(removeEntry){
                            thix.parents('tr').remove();
                            var k = 1;
                            $('#po_list_draft>table>tbody>tr').each(function(){
                                $(this).find('.sr_no').html(k);
                                k += 1;
                            });
                            toastr.success(response.message);
                        }else{
                            window.location.href = '/purchase-order/form';
                        }
                    },
                    error: function(response,status) {
                        // toastr.error(response.responseJSON.message);
                    }
                });
            }
        });
    </script>

    @if($case == 'new' && isset($dataGenerateBarcodes) && count($dataGenerateBarcodes) != 0)
        <script>
            var listBarcode = [];
        </script>
        @foreach($dataGenerateBarcodes as $dataGenerateBarcode)
            <script>
                var obj = {
                    'barcode' : '{{$dataGenerateBarcode['barcode']}}',
                    'qty' : '{{$dataGenerateBarcode['qty']}}',
                    'supplier_name' : '{{$dataGenerateBarcode['supplier_name']}}',
                    'supplier_id' : '{{$dataGenerateBarcode['supplier_id']}}',
                }
                listBarcode.push(obj);
            </script>
        @endforeach
        <script>
            if(listBarcode.length != 0){
                var listBarcodeLength  = listBarcode.length;
                for(var i=0;i<listBarcodeLength ;i++){
                    var table_pit_list = $('table.table_pit_list');
                    table_pit_list.addClass('pointerEventsNone');
                    var tr = table_pit_list.find('.erp_form__grid_header>tr:first-child');
                    var barcode = listBarcode[i].barcode;
                    var qty = listBarcode[i].qty;
                    var supplier_name = listBarcode[i].supplier_name;
                    var supplier_id = listBarcode[i].supplier_id;
                    var last_barcode = false;
                    if((listBarcodeLength-1) == i){
                        last_barcode = true;
                    }
                    tr.find('#pd_barcode').val(barcode);
                    $('#supplier_name').val(supplier_name);
                    $('#supplier_id').val(supplier_id);

                    var form_type = $('#form_type').val();
                    //var supplier_id = $('#supplier_id').val();
                    var formData = {
                        form_type : form_type,
                        val : barcode,
                        qty : qty,
                        supplier_name : supplier_name,
                        supplier_id : supplier_id,
                        reorder_action : true,
                        last_barcode : last_barcode,
                    }
                    /*if (!valueEmpty(supplier_id)) {
                        formData.supplier_id = supplier_id;
                    }*/
                    initBarcode(13, tr, form_type, formData);
                }
            }
        </script>
        @php
            session()->forget('dataGenerateBarcodes');
        @endphp
    @endif
@endsection
