@extends('layouts.layout')
@section('title', 'GRN')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
    @endphp
    <!--begin::Form-->
    @php $id = isset($data['current']->grn_id)?$data['current']->grn_id:'';  @endphp
    <form id="grn_form" class="kt-form" method="post" action="{{ action('Purchase\GRNController@store' , $id) }}">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    @if(isset($data['id']))
                                        {{$data['current']->grn_code}}
                                    @else
                                        {{$data['grn_code']}}
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
                                    @if(isset($data['id']))
                                        @php $due_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->grn_date)))); @endphp
                                    @else
                                        @php $due_date =  date('d-m-Y'); @endphp
                                    @endif
                                    <input type="text" name="grn_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$due_date}}" id="kt_datepicker_3" />
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
                                <div class="input-group open-modal-group">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                            </span>
                                    </div>
                                    <input type="text" id="supplier_name" value="{{isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" name="supplier_name" autocomplete="off" class="open-inline-help form-control erp-form-control-sm moveIndex">
                                    <input type="hidden" id="supplier_id" value="{{isset($data['current']->supplier->supplier_id)?$data['current']->supplier->supplier_id:''}}" name="supplier_id">
                                    <div class="input-group-append">
                                                <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                   <i class="la la-search"></i>
                                                </span>
                                    </div>
                                </div>
                                {{--<div class="input-group open-modal-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                             <i class="la la-minus-circle"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="supplier_name" value="{{isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:''}}" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" name="supplier_name" class="form-control erp-form-control-sm open_modal moveIndex" placeholder="Enter here">
                                    <input type="hidden" id="supplier_id" name="supplier_id" value="{{isset($data['current']->supplier->supplier_id)?$data['current']->supplier->supplier_id:''}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text btn-open-modal">
                                           <i class="la la-search"></i>
                                        </span>
                                    </div>
                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 col-form-label">PO:</label>
                            <div class="col-lg-6">
                                <div class="input-group open-modal-group">
                                    <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data-table">
                                                     <i class="la la-minus-circle"></i>
                                                </span>
                                    </div>
                                    <input type="text" value="{{isset($data['current']->PO->purchase_order_code)?$data['current']->PO->purchase_order_code:''}}" data-url="{{action('Common\DataTableController@helpOpen','poHelp')}}" id="purchase_order" name="purchase_order" class="open_modal form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                    <input type="hidden" id="purchase_order_id" name="purchase_order_id" value="{{isset($data['current']->PO->purchase_order_id)?$data['current']->PO->purchase_order_id:''}}"/>
                                    <div class="input-group-append">
                                                <span class="input-group-text btn-open-modal">
                                                   <i class="la la-search"></i>
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
                            <label class="col-lg-6 col-form-label">Currency:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm moveIndex currency" id="kt_select2_1" name="grn_currency">
                                        <option value="0">Select</option>
                                        @if(isset($data['current']->currency_id))
                                            @php $grn_currency = isset($data['current']->currency_id)?$data['current']->currency_id:0; @endphp
                                            @foreach($data['currency'] as $currency)
                                                <option value="{{$currency->currency_id}}" {{$currency->currency_id==$grn_currency?'selected':''}}>{{$currency->currency_name}}</option>
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
                            <label class="col-lg-6 col-form-label">Exchange Rate:</label>
                            <div class="col-lg-6">
                                <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($data['current']->grn_exchange_rate)?$data['current']->grn_exchange_rate:$exchange_rate}}" class="form-control erp-form-control-sm moveIndex validNumber">
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 col-form-label">Bill No:</label>
                            <div class="col-lg-6">
                                <input type="text" id="grn_bill_no" name="grn_bill_no" value="{{isset($data['current']->grn_bill_no)?$data['current']->grn_bill_no:''}}" class="form-control erp-form-control-sm moveIndex">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 col-form-label">Store:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm moveIndex" id="kt-select2_validate" name="grn_store">
                                        @foreach($data['store'] as $store)
                                            @if(isset($data['id']))
                                                @php $grn_store = isset($data['current']->store_id)?$data['current']->store_id:0; @endphp
                                            @else
                                                @php $grn_store = $store->store_default_value == 1 ? $store->store_id : 0 ; @endphp
                                            @endif
                                            <option value="{{$store->store_id}}" {{ $store->store_id == $grn_store?'selected':'' }}>{{$store->store_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 col-form-label">Payment Terms:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="input-group erp-select2-sm">
                                    <select name="grn_ageing_term_id"  id="grn_ageing_term_id" class="moveIndex kt-select2 width form-control erp-form-control-sm">
                                        <option value="0">Select</option>
                                        @foreach($data['payment_terms'] as $payment_term)
                                            @php $payment_terms_id = isset($data['current']->grn_ageing_term_id)?$data['current']->grn_ageing_term_id:''; @endphp
                                            <option value="{{$payment_term->payment_term_id}}" {{$payment_terms_id == $payment_term->payment_term_id?"selected":""}}>{{$payment_term->payment_term_name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append" style="width: 33%;">
                                        <input type="text" value="{{isset($data['current']->grn_ageing_term_value)?$data['current']->grn_ageing_term_value:''}}" id="grn_ageing_term_value" name="grn_ageing_term_value" class="moveIndex form-control erp-form-control-sm validNumber">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 col-form-label">Payment Type:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select name="payment_type_id"  id="payment_type_id" class="moveIndex kt-select2 form-control erp-form-control-sm">
                                        <option value="0">Select</option>
                                        @foreach($data['payment_type'] as $payment_type)
                                            @php $payment_type_id = isset($data['current']->payment_type_id)?$data['current']->payment_type_id:''; @endphp
                                            <option value="{{$payment_type->payment_type_id}}" {{$payment_type_id == $payment_type->payment_type_id?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row form-group-block" style="display:none;">
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
                </div>
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <div class="data_entry_header" style="margin-bottom: -30px;">
                            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                    <i class="flaticon-more" style="color: #666666;"></i>
                                </button>
                                @php
                                    $headings = ['Sr No','Sup Barcode','Barcode','Product Name','UOM','Packing','Qty',
                                                  'FOC Qty','FC Rate','Rate','Amount','Disc%','Disc Amt','VAT%','Vat Amt',
                                                  'Batch #','Production Date','Expiry Date','Gross Amt',];
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
                        </div>
                    </div>
                </div>
                <div class="form-group-block" style="overflow: auto;">
                    <table id="grnForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                        <thead>
                        <tr>
                            <th style="width: 35.6667px;">Sr No</th>
                            <th style="width: 42px;">Sup Barcode</th>
                            <th style="width: 68.6667px;">
                                Barcode
                                <button type="button" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                    <i class="la la-barcode"></i>
                                </button>
                            </th>
                            <th style="width: 78px;">Product Name</th>
                            <th style="width: 25.3333px;">UOM</th>
                            <th style="width: 25.3333px;">Pack ing</th>
                            <th style="width: 24.6667px;">Qty</th>
                            <th style="width: 24.6667px;">FOC Qty</th>
                            <th style="width: 24.6667px;">FC Rate</th>
                            <th style="width: 40.6667px;">Rate</th>
                            <th style="width: 40.6667px;">Amount</th>
                            <th style="width: 38.6667px;">Disc %</th>
                            <th style="width: 44.6667px;">Disc Amt</th>
                            {{--<th style="width: 34.6667px;">GST %</th>--}}
                            <th style="width: 38.6667px;">VAT %</th>
                            <th style="width: 44.6667px;">Vat Amt</th>
                            <th style="width: 31.3333px;">Batch #</th>
                            <th style="width: 77.3333px;">Production Date</th>
                            <th style="width: 73.3333px;">Expiry Date</th>
                            <th style="width: 45.3333px;">Gross Amt</th>
                            <th style="width: 33.3333px;">Action</th>
                        </tr>
                        <tr id="dataEntryForm">
                            <td><input readonly id="sr_no" type="text" class="form-control erp-form-control-sm">
                                <input readonly type="hidden" id="product_id" class="product_id form-control erp-form-control-sm">
                                <input readonly type="hidden" id="uom_id" class="uom_id form-control erp-form-control-sm">
                                <input readonly type="hidden" id="product_barcode_id" class="product_barcode_id form-control erp-form-control-sm">
                                <input readonly type="hidden" id="supplier_id"  class="supplier_id form-control erp-form-control-sm handle">
                            </td>
                            <td><input id="grn_supplier_barcode" type="text" class="moveIndex form-control erp-form-control-sm"></td>
                            <td><input id="pd_barcode" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="open-inline-help pd_barcode moveIndex2 form-control erp-form-control-sm" autocomplete="off"></td>
                            <td><input readonly id="product_name" type="text" class="pd_product_name form-control erp-form-control-sm"></td>
                            <td>
                                <select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" id="uom">
                                    <option value="">Select</option>
                                </select>
                            </td>
                            <td><input readonly id="packing" type="text" class="pd_packing form-control erp-form-control-sm"></td>
                            <td><input id="quantity" type="text" class="moveIndex tblGridCal_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                            <td><input id="foc_qty" type="text" class="form-control erp-form-control-sm validNumber"></td>
                            <td><input id="fc_rate" type="text" class="fc_rate form-control erp-form-control-sm validNumber"></td>
                            <td><input id="rate" type="text" class="moveIndex tblGridCal_rate form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="amount" type="text" class="tblGridCal_amount form-control erp-form-control-sm validNumber"></td>
                            <td><input id="discount" type="text" class="moveIndex tblGridCal_discount form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="discount_val" type="text" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber"></td>
                            {{--<td><input readonly id="grn_gst" type="text" class="form-control erp-form-control-sm validNumber"></td>--}}
                            <td><input id="vat_perc" type="text" class="moveIndex tblGridCal_vat_perc form-control erp-form-control-sm validNumber"></td>
                            <td><input readonly id="vat_val" type="text" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber"></td>
                            <td><input id="grn_batch_no" type="text" class="moveIndex form-control form-control-sm"></td>
                            <td><input readonly type="text" id="grn_production_date" class="form-control form-control-sm c-date-p" value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}" /></td>
                            <td><input readonly type="text" id="grn_expiry_date" class="form-control form-control-sm c-date-p" value="{{date('d-m-Y')}}" title="{{date('d-m-Y')}}" /></td>
                            <td><input readonly id="gross_amount" type="text" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber"></td>
                            <td class="text-center">
                                <button type="button" id="addData" class="moveIndexBtn moveIndex gridBtn btn btn-primary btn-sm">
                                    <i class="la la-plus"></i>
                                </button>
                            </td>
                        </tr>
                        </thead>
                        <tbody id="repeated_data">
                        @if(isset($data['current']->grn_dtl))
                            @foreach($data['current']->grn_dtl as $dtl)
                                <tr>
                                    <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                        <input readonly id="sr_no" type="text" name="pd[{{$loop->iteration}}][sr_no]" title="{{$loop->iteration}}" value="{{$loop->iteration}}"  class="form-control erp-form-control-sm handle">
                                        <input readonly type="hidden" data-id="purc_grn_dtl_id" name="pd[{{$loop->iteration}}][purc_grn_dtl_id]" value="{{ $dtl->purc_grn_dtl_id }}" class="purc_grn_dtl_id form-control erp-form-control-sm handle">
                                        <input readonly type="hidden" data-id="purchase_order_id" name="pd[{{$loop->iteration}}][purchase_order_id]" value="{{ $dtl->purchase_order_id }}" class="purchase_order_id form-control erp-form-control-sm handle">
                                        <input readonly type="hidden" data-id="product_id" name="pd[{{$loop->iteration}}][product_id]" value="{{ $dtl->product->product_id }}" class="product_id form-control erp-form-control-sm handle">
                                        <input readonly type="hidden" data-id="uom_id" name="pd[{{$loop->iteration}}][uom_id]" value="{{ $dtl->uom->uom_id }}" class="uom_id form-control erp-form-control-sm handle">
                                        <input readonly type="hidden" data-id="product_barcode_id" name="pd[{{$loop->iteration}}][product_barcode_id]" value="{{ $dtl->product_barcode_id }}" class="product_barcode_id form-control erp-form-control-sm">
                                        <input readonly type="hidden" data-id="supplier_id" name="pd[{{$loop->iteration}}][supplier_id]" value="" class="supplier_id form-control erp-form-control-sm handle">
                                    </td>
                                    <td><input type="text" data-id="grn_supplier_barcode" name="pd[{{$loop->iteration}}][grn_supplier_barcode]" title="" value=""  class="moveIndex form-control erp-form-control-sm"></td>
                                    <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" title="{{$dtl->barcode->product_barcode_barcode}}" value="{{$dtl->barcode->product_barcode_barcode}}" class="form-control erp-form-control-sm" readonly></td>
                                    <td><input readonly type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{ $dtl->product->product_name }}" title="{{ $dtl->product->product_name }}"  class="pd_product_name form-control erp-form-control-sm"></td>
                                    <td>
                                        <select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][uom]" data-id="uom" title="{{ $dtl->uom->uom_name }}">
                                            <option value="{{ $dtl->uom->uom_id }}">{{ $dtl->uom->uom_name }}</option>
                                        </select>
                                    </td>
                                    <td><input readonly type="text" data-id="packing" name="pd[{{$loop->iteration}}][packing]" value="{{ $dtl->tbl_purc_grn_dtl_packing }}" title="{{ $dtl->tbl_purc_grn_dtl_packing }}" class="pd_packing form-control erp-form-control-sm"></td>
                                    <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" title="{{ $dtl->tbl_purc_grn_dtl_quantity }}" value="{{ $dtl->tbl_purc_grn_dtl_quantity }}" class="moveIndex tblGridCal_qty form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                    <td><input type="text" data-id="foc_qty" name="pd[{{$loop->iteration}}][foc_qty]" title="{{ $dtl->tbl_purc_grn_dtl_foc_quantity }}" value="{{ $dtl->tbl_purc_grn_dtl_foc_quantity }}" class="form-control erp-form-control-sm validNumber"></td>
                                    <td><input type="text" data-id="fc_rate" name="pd[{{$loop->iteration}}][fc_rate]" title="{{ $dtl->tbl_purc_grn_dtl_fc_rate }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_fc_rate,3) }}" class="fc_rate form-control erp-form-control-sm validNumber"></td>
                                    <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_rate) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_rate,3) }}" class="moveIndex tblGridCal_rate form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly data-id="amount" type="text" name="pd[{{$loop->iteration}}][amount]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_amount) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_amount,3) }}" class="tblGridCal_amount form-control erp-form-control-sm validNumber"></td>
                                    <td><input type="text" data-id="discount" name="pd[{{$loop->iteration}}][discount]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_disc_percent) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_disc_percent,3) }}" class="moveIndex tblGridCal_discount form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly type="text" data-id="discount_val" name="pd[{{$loop->iteration}}][discount_val]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_disc_amount) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_disc_amount,3) }}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber"></td>
                                    {{--<td><input readonly type="text" data-id="grn_gst" name="pd[{{$loop->iteration}}][grn_gst]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_gst_percent) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_gst_percent,3) }}" class="form-control erp-form-control-sm validNumber"></td>--}}
                                    <td><input type="text" data-id="vat_perc" name="pd[{{$loop->iteration}}][vat_perc]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_vat_percent) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_percent,3) }}" class="moveIndex tblGridCal_vat_perc form-control erp-form-control-sm validNumber"></td>
                                    <td><input readonly type="text" data-id="vat_val" name="pd[{{$loop->iteration}}][vat_val]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_vat_amount) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_amount,3) }}" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber"></td>
                                    <td><input type="text" data-id="grn_batch_no" name="pd[{{$loop->iteration}}][grn_batch_no]" title="{{ $dtl->tbl_purc_grn_dtl_batch_no }}" value="{{ $dtl->tbl_purc_grn_dtl_batch_no }}" class="moveIndex form-control form-control-sm"></td>
                                    @php $Proddate= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_production_date)))); @endphp
                                    <td><input readonly type="text" data-id="grn_production_date" name="pd[{{$loop->iteration}}][grn_production_date]" value="{{($Proddate =='01-01-1970')?'':$Proddate}}" title="{{($Proddate =='01-01-1970')?'':$Proddate}}" class="form-control form-control-sm" id="kt_datepicker_3" /></td>
                                    @php $Expdate= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_expiry_date)))); @endphp
                                    <td><input readonly type="text" data-id="grn_expiry_date" name="pd[{{$loop->iteration}}][grn_expiry_date]" value="{{($Expdate =='01-01-1970')?'':$Expdate}}" title="{{($Expdate =='01-01-1970')?'':$Expdate}}" class="form-control form-control-sm" id="kt_datepicker_3" /></td>
                                    <td><input readonly type="text" data-id="gross_amount"name="pd[{{$loop->iteration}}][gross_amount]" title="{{ sprintf('%0.3f',$dtl->tbl_purc_grn_dtl_total_amount) }}" value="{{ number_format($dtl->tbl_purc_grn_dtl_total_amount,3) }}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber"></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="tableTotal">
                            <tbody>
                            <tr>
                                <td><div class="t_total_label">Total Amount:</div></td>
                                <td class="text-right"><span class="t_gross_total t_total">0</span><input type="hidden" id="pro_tot"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-5">
                        <div class="row">
                            <label class="col-lg-2 erp-col-form-label">Notes:</label>
                            <div class="col-lg-10">
                                <textarea type="text" rows="3" id="grn_notes" name="grn_notes" maxlength="255" class="moveIndex form-control erp-form-control-sm">{{isset($data['current']->grn_remarks)?$data['current']->grn_remarks:""}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-2">
                                <label class="col-lg-12 erp-col-form-label">Expense:</label>
                            </div>
                            <div class="col-lg-10">
                                <div class="form-group-block" style="overflow:auto; max-height:200px;">
                                    <table id="grnaccForm" class="ErpFormsm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable" style="margin-top:0px;">
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
                                            <td><input  id="expense_amount" type="text" class="expense_amount form-control erp-form-control-sm moveIndexsm validNumber"></td>
                                            <td class="text-center">
                                                <button type="button" id="addDatasm" class="moveIndexBtnsm moveIndexsm gridBtn btn btn-primary btn-sm">
                                                    <i class="la la-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </thead>
                                        <tbody id="repeated_datasm">
                                        @if(isset($data['current']->grn_expense))
                                            @foreach($data['current']->grn_expense as $expense)
                                                <tr>
                                                    <td><input  type="text" name="pd[{{ $loop->iteration }}][sr_no]" value="{{ $loop->iteration }}" class=" form-control erp-form-control-sm" readonly>
                                                        <input readonly type="hidden" name="pdsm[{{ $loop->iteration }}][account_id]" value="{{ $expense->chart_account_id }}" data-id="account_id"  class="acc_id form-control erp-form-control-sm">
                                                        <input readonly type="hidden" name="pdsm[{{ $loop->iteration }}][grn_expense_id]" value="{{ $expense->grn_expense_id }}" data-id="grn_expense_id" class="grn_expense_id form-control erp-form-control-sm" readonly>
                                                    </td>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_code]" data-id="account_code" data-url="{{action('Common\DataTableController@helpOpen','accountsHelp')}}" value="{{ $expense->accounts->chart_code }}" class="acc_code open_js_modal masking moveIndexsm validNumber form-control erp-form-control-sm text-left" maxlength="12"></td>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_name]" data-id="account_name" value="{{ $expense->accounts->chart_name }}" class="acc_name form-control erp-form-control-sm " readonly></td>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][expense_amount]" data-id="expense_amount" value="{{ number_format($expense->grn_expense_amount,3) }}" class="expense_amount form-control erp-form-control-sm moveIndexsm validNumber"></td>
                                                    <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delDatasm"><i class="la la-trash"></i></button></div></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tbody>
                                        <tr height="25">
                                            <td colspan="3" class="voucher-total-title align-middle">Total Expenses :</td>
                                            <td class="voucher-total-amt align-middle">
                                                <span id="tot_expenses" ></span><input type="hidden" name='TotExpen' id='TotExpen'>
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
    </form>
                <!--end::Form-->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/grn.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        if($( window ).width() <= 1024){
            var MobHiddenTd = '1,4,5,7,8,10,11,12,13,14,15,16,17,18';
            var hiddenFieldsFormName = 'grnFormMob';
        }else{
            var hiddenFieldsFormName = 'grnForm';
        }
        var formcase = '{{$case}}';
        $(".expense_amount").keyup(function(){
            TotalExpenseAmount();
        });
    </script>
    <script src="{{ asset('js/pages/js/hidden-fields.js') }}" type="text/javascript"></script>
    <script>
        function selectPO(){
            $('#help_datatable_poHelp').on('click', 'tbody>tr', function (e) {
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
                    url: '/grn/po/'+po_id,
                    success: function(response, data){
                        console.log(response);
                        console.log(data);
                        if(data) {
                            $('#repeated_data>tr>td:first-child').each(function(){
                                var purchase_order_id = $(this).find('input[data-id="purchase_order_id"]').val();
                                if(purchase_order_id){
                                    $(this).parents('tr').remove();
                                }
                            });
                            updateKeys();
                            var tr = '';
                            var total_length = $('#repeated_data>tr').length;
                            function notNullNo(val){
                                if(val == null){
                                    return "";
                                }else{
                                    return val = parseFloat(val).toFixed(3);
                                }
                            }
                            for(var p=0; p < response['all']['po_details'].length; p++ ){
                                total_length++;
                                var  row = response['all']['po_details'][p];
                                tr +='<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                            '<input type="text" name="pd['+total_length+'][sr_no]" value="'+total_length+'" title="'+total_length+'" class="form-control sr_no erp-form-control-sm handle" readonly>'+
                                            '<input type="hidden" name="pd['+total_length+'][purchase_order_id]" data-id="purchase_order_id" value="'+po_id+'" class="purchase_order_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+notNull(row['product_id'])+'" class="product_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+notNull(row['uom_id'])+'"class="uom_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+notNull(row['product_barcode_id'])+'"class="product_barcode_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+total_length+'][supplier_id]" data-id="supplier_id" value="" class="supplier_id form-control erp-form-control-sm " readonly>'+
                                    '</td>'+
                                    '<td><input type="text" name="pd['+total_length+'][grn_supplier_barcode]" data-id="grn_supplier_barcode" value="" title="" class="form-control erp-form-control-sm moveIndex"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][pd_barcode]" data-id="pd_barcode" value="'+notNull(row['barcode']['product_barcode_barcode'])+'" title="'+notNull(row['barcode']['product_barcode_barcode'])+'" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" value="'+notNull(row['product']['product_name'])+'" title="'+notNull(row['product']['product_name'])+'" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td>'+
                                        '<select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" name="pd['+total_length+'][uom]" data-id="uom" title="'+row['uom']['uom_name']+'">'+
                                            '<option value="'+notNull(row['uom']['uom_id'])+'">'+notNull(row['uom']['uom_name'])+'</option>'+
                                        '</select>'+
                                    '</td>' +
                                    '<td><input type="text" name="pd['+total_length+'][packing]" data-id="packing" value="'+notNull(row['purchase_order_dtlpacking'])+'" title="'+notNull(row['purchase_order_dtlpacking'])+'" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][quantity]" data-id="quantity" value="'+notNull(row['purchase_order_dtlquantity'])+'" title="'+notNull(row['purchase_order_dtlquantity'])+'" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][foc_qty]" data-id="foc_qty" value="'+notNull(row['purchase_order_dtlfoc_quantity'])+'" title="'+notNull(row['purchase_order_dtlfoc_quantity'])+'" class="form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][fc_rate]" data-id="fc_rate" value="'+notNull(row['purchase_order_dtlfc_rate'])+'" title="'+notNull(row['purchase_order_dtlfc_rate'])+'" class="fc_rate form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][rate]" data-id="rate" value="'+notNullNo(row['purchase_order_dtlrate'])+'" title="'+notNullNo(row['purchase_order_dtlrate'])+'" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][amount]" data-id="amount" value="'+notNullNo(row['purchase_order_dtlamount'])+'" title="'+notNullNo(row['purchase_order_dtlamount'])+'" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount]" data-id="discount" value="'+notNullNo(row['purchase_order_dtldisc_percent'])+'" title="'+notNullNo(row['purchase_order_dtldisc_percent'])+'" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount_val]" data-id="discount_val" value="'+notNullNo(row['purchase_order_dtldisc_amount'])+'" title="'+notNullNo(row['purchase_order_dtldisc_amount'])+'" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    /*'<td><input type="text" name="pd['+total_length+'][grn_gst]" data-id="grn_gst" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>' +*/
                                    '<td><input type="text" name="pd['+total_length+'][vat_perc]" data-id="vat_perc" value="'+notNullNo(row['purchase_order_dtlvat_percent'])+'" title="'+notNullNo(row['purchase_order_dtlvat_percent'])+'" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][vat_val]" data-id="vat_val" value="'+notNullNo(row['purchase_order_dtlvat_amount'])+'" title="'+notNullNo(row['purchase_order_dtlvat_amount'])+'" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][grn_batch_no]" data-id="grn_batch_no" class="moveIndex form-control form-control-sm"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][grn_production_date]" data-id="grn_production_date" value="" class="form-control form-control-sm moveIndex kt_datepicker_3" /></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][grn_expiry_date]" data-id="grn_expiry_date" value="" class="form-control form-control-sm moveIndex kt_datepicker_3" /></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][gross_amount]" data-id="gross_amount" value="'+notNullNo(row['purchase_order_dtltotal_amount'])+'" title="'+notNullNo(row['purchase_order_dtltotal_amount'])+'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
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
        var accountsHelpUrl = "{{url('/common/help-open/accountsHelp')}}";
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'grn_supplier_barcode',
                'fieldClass':'moveIndex'
            },
            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode open-inline-help',
                'require':true,
                'readonly':true
                //'data-url' : productHelpUrl
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
                'readonly':true
            },
            {
                'id':'fc_rate',
                'readonly':true
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
                'fieldClass':'tblGridCal_discount_amount validNumber',
                'readonly':true
            },
            /*{
                'id':'grn_gst',
                'fieldClass':'validNumber'
            },*/
            {
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc moveIndex validNumber'
            },
            {
                'id':'vat_val',
                'fieldClass':'tblGridCal_vat_amount validNumber',
                'readonly':true
            },
            {
                'id':'grn_batch_no',
                'fieldClass':'moveIndex'
            },
            {
                'id':'grn_production_date',
                'fieldClass':'kt_datepicker_3',
                'readonly':true
            },
            {
                'id':'grn_expiry_date',
                'fieldClass':'kt_datepicker_3',
                'readonly':true
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['purc_grn_dtl_id','product_id','product_barcode_id','uom_id','supplier_id'];
        $('input').attr('autocomplete', 'off');
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/product-inline-ajax2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-expense-row-repeated.js') }}" type="text/javascript"></script>
@endsection


