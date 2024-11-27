@extends('layouts.layout')
@section('title', 'Purchase Return Temporary')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $length = 0;
        }
        if($case == 'edit'){
            $expense_dtls = isset($data['current']->grn_expense)? $data['current']->grn_expense :[];
            $length = count($expense_dtls);
        }
$form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    @php $id = isset($data['current']->grn_id)?$data['current']->grn_id:'';  @endphp
    <form id="pr_form" class="kt-form" method="post" action="{{ action('Purchase\PurchaseReturnTempController@store' , $id) }}">
        <input type="hidden" value='{{$form_type}}' id="form_type">
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
                            <label class="col-lg-6 erp-col-form-label">Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    @if(isset($data['id']))
                                        @php $due_date =  date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->grn_date)))); @endphp
                                    @else
                                        @php $due_date =  date('d-m-Y'); @endphp
                                    @endif
                                    <input type="text" name="grn_date" autocomplete="off" class="form-control erp-form-control-sm moveIndex c-date-p" readonly value="{{$due_date}}" id="kt_datepicker_3" autofocus/>
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
                            <label class="col-lg-6 erp-col-form-label">Supplier: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" id="supplier_name" value="{{isset($data['current']->supplier->supplier_name)?$data['current']->supplier->supplier_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" autocomplete="off" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{isset($data['current']->supplier->supplier_id)?$data['current']->supplier->supplier_id:''}}"/>
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
                            <label class="col-lg-6 erp-col-form-label">Payment Terms:</label>
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
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Currency: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm moveIndex currency" id="kt_select2_1" name="grn_currency">
                                        <option value="0">Select</option>
                                        @if(isset($data['current']->currency_id))
                                            @php $grn_currency = isset($data['current']->currency_id)?$data['current']->currency_id:0;  $exchange_rate = '';@endphp
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
                            <label class="col-lg-6 erp-col-form-label">Exchange Rate:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($data['current']->grn_exchange_rate)?$data['current']->grn_exchange_rate:$exchange_rate}}" class="form-control erp-form-control-sm moveIndex validNumber">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Bill No:</label>
                            <div class="col-lg-6">
                                <input type="text" id="grn_bill_no" name="grn_bill_no" value="{{isset($data['current']->grn_bill_no)?$data['current']->grn_bill_no:''}}" class="form-control erp-form-control-sm moveIndex">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Store: <span class="required">*</span></label>
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
                            <label class="col-lg-6 erp-col-form-label">Payment Type: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select name="payment_type_id"  id="payment_type_id" class="moveIndex kt-select2 form-control erp-form-control-sm">
                                        {{--<option value="0">Select</option>--}}
                                        @foreach($data['payment_type'] as $payment_type)
                                            @php $payment_type_id = isset($data['current']->payment_type_id)?$data['current']->payment_type_id:2; @endphp
                                            <option value="{{$payment_type->payment_type_id}}" {{$payment_type_id == $payment_type->payment_type_id?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Internal Stock Transfer:</label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" value="{{isset($ist_code)?$ist_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','InternalStockTransferHelp')}}" id="ist_code" name="ist_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="ist_id" name="ist_id" value="{{isset($ist_id)?$ist_id:''}}"/>
                                        <div class="input-group-append">
                                                    <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                        <i class="la la-search"></i>
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
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
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Sup Barcode','Qty',
                                                'FOC Qty','FC Rate','Rate','Amount','Disc%','Disc Amt','VAT%','Vat Amt',
                                                'Batch #','Production Date','Expiry Date','Gross Amt',];
                                @endphp
                                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                    @foreach($headings as $key=>$heading)
                                        <li >
                                            <label>
                                                <input value="{{$key}}" name="{{trim($key)}}" type="checkbox" checked> {{$heading}}
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
                            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                <tr>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sr.</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                            <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                            <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                            <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                            <input id="grn_supplier_id" readonly type="hidden" class="grn_supplier_id form-control erp-form-control-sm handle">
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
                                        <div class="erp_form__grid_th_title">Sup Barcode</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="grn_supplier_barcode" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">FOC Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="foc_qty" type="text" class="validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">FC Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="fc_rate" type="text" class="fc_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="rate" type="text" class="tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="amount" type="text" class="tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
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
                                        <div class="erp_form__grid_th_title">VAT %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="vat_perc" type="text" class="tblGridCal_vat_perc validNumber tb_moveIndex validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">VAT Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="vat_amount" type="text" class="tblGridCal_vat_amount tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Batch No</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="batch_no" type="text" class="tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Production Date</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="production_date" readonly value="" title="{{date('d-m-Y')}}" type="text" class="c-date-p kt_datepicker_3 form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Expiry Date</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="expiry_date" readonly value="" title="{{date('d-m-Y')}}" type="text" class="c-date-p kt_datepicker_3 form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Gross Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="gross_amount" readonly type="text" class="tblGridCal_gross_amount validNumber form-control erp-form-control-sm">
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
                                @if(isset($data['current']->grn_dtl))
                                    @foreach($data['current']->grn_dtl as $dtl)
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][purc_grn_dtl_id]" data-id="purc_grn_dtl_id" value="{{$dtl->purc_grn_dtl_id}}" class="purc_grn_dtl_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                <input readonly type="hidden" data-id="grn_supplier_id" name="pd[{{$loop->iteration}}][grn_supplier_id]" value="" class="grn_supplier_id form-control erp-form-control-sm">
                                                <input readonly type="hidden" data-id="purchase_order_id" name="pd[{{$loop->iteration}}][purchase_order_id]" value="{{ $dtl->purchase_order_id }}" class="purchase_order_id form-control erp-form-control-sm handle">
                                            </td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][pd_uom]">
                                                    <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][pd_packing]" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][grn_supplier_barcode]" data-id="grn_supplier_barcode" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity"  value="{{$dtl->tbl_purc_grn_dtl_quantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty"  value="{{$dtl->tbl_purc_grn_dtl_foc_quantity}}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" data-id="fc_rate"  value="{{number_format($dtl->tbl_purc_grn_dtl_fc_rate,3, '.', '')}}" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate"  value="{{number_format($dtl->tbl_purc_grn_dtl_rate,3, '.', '')}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount"  value="{{number_format($dtl->tbl_purc_grn_dtl_amount,3, '.', '')}}" class="tblGridCal_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_perc]" data-id="dis_perc"  value="{{number_format($dtl->tbl_purc_grn_dtl_disc_percent,2, '.', '')}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_amount]" data-id="dis_amount"  value="{{number_format($dtl->tbl_purc_grn_dtl_disc_amount,3, '.', '')}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc"  value="{{number_format($dtl->tbl_purc_grn_dtl_vat_percent,2, '.', '')}}" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_amount]" data-id="vat_amount"  value="{{number_format($dtl->tbl_purc_grn_dtl_vat_amount,3, '.', '')}}" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][batch_no]" data-id="batch_no"  value="{{isset($dtl->tbl_purc_grn_dtl_batch_no)?$dtl->tbl_purc_grn_dtl_batch_no:""}}" class="tb_moveIndex form-control erp-form-control-sm"></td>
                                            @php $prod_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_production_date)))); @endphp
                                            <td><input type="text" data-id="production_date" name="pd[{{$loop->iteration}}][production_date]" value="{{($prod_date =='01-01-1970')?'':$prod_date}}" title="{{($prod_date =='01-01-1970')?'':$prod_date}}" class="date_inputmask tb_moveIndex form-control erp-form-control-sm"/></td>
                                            @php $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_expiry_date)))); @endphp
                                            <td><input type="text" data-id="expiry_date" name="pd[{{$loop->iteration}}][expiry_date]" value="{{($expiry_date =='01-01-1970')?'':$expiry_date}}" title="{{($expiry_date =='01-01-1970')?'':$expiry_date}}" class="date_inputmask tb_moveIndex form-control erp-form-control-sm"/></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount"  value="{{number_format($dtl->tbl_purc_grn_dtl_total_amount,3, '.', '')}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
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
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="total_grid_qty">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td class="total_grid_foc_qty">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td class="total_grid_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td></td>
                                    <td class="total_grid_disc_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td></td>
                                    <td class="total_grid_vat_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="total_grid_gross_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
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
                                <td><div class="t_total_label">Total:</div></td>
                                <td><span class="t_gross_total t_total">0</span><input type="hidden" id="pro_tot"></td>
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
                                <textarea type="text" rows="4" id="grn_notes" name="grn_notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($data['current']->grn_remarks)?$data['current']->grn_remarks:""}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-2">
                                <label class="col-lg-12 erp-col-form-label">Expense:</label>
                            </div>
                            <div class="col-lg-10">
                                <div class="form-group-block" style="overflow:auto; height:120px;">
                                    <table id="SalesAccForm" class="ErpFormsm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable" style="margin-top:0px;">
                                        <thead>
                                        <tr>
                                            <th width="10%">Sr No</th>
                                            <th width="30%">Acc code</th>
                                            <th width="35%">Acc Name</th>
                                            <th width="25%">Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody id="repeated_datasm">
                                        @if($length > 0)
                                            @foreach($data['accounts'] as $expense_accounts)
                                                @php
                                                    $expense_amount = '';
                                                    $expense =\App\Models\TblPurcGrnExpense::where('chart_account_id',$expense_accounts->chart_account_id)->first('grn_expense_amount');
                                                    if($expense != Null){
                                                        $expense_amount = number_format($expense->grn_expense_amount,3);
                                                    }
                                                @endphp
                                                <tr>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][sr_no]" value="{{ $loop->iteration }}" class=" form-control erp-form-control-sm" readonly>
                                                        <input  type="hidden" name="pdsm[{{ $loop->iteration }}][account_id]" value="{{ $expense_accounts->chart_account_id }}" data-id="account_id"  class="acc_id form-control erp-form-control-sm">
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_code]" value="{{ $expense_accounts->chart_code }}" data-id="account_code" class="acc_code masking moveIndexsm validNumber form-control erp-form-control-sm text-left" maxlength="12" readonly></td>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_name]" value="{{ $expense_accounts->chart_name }}" data-id="account_name" class="acc_name form-control erp-form-control-sm " readonly></td>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][expense_amount]" value="{{isset($expense_amount)?$expense_amount:''}}" data-id="expense_amount" class="expense_amount form-control erp-form-control-sm moveIndexsm validNumber validOnlyFloatNumber"></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach($data['accounts'] as $expense_accounts)
                                                <tr>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][sr_no]" value="{{ $loop->iteration }}" class=" form-control erp-form-control-sm" readonly>
                                                        <input  type="hidden" name="pdsm[{{ $loop->iteration }}][account_id]" value="{{ $expense_accounts->chart_account_id }}" data-id="account_id"  class="acc_id form-control erp-form-control-sm">
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_code]" value="{{ $expense_accounts->chart_code }}" data-id="account_code" class="acc_code masking moveIndexsm validNumber form-control erp-form-control-sm text-left" maxlength="12" readonly></td>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][account_name]" value="{{ $expense_accounts->chart_name }}" data-id="account_name" class="acc_name form-control erp-form-control-sm " readonly></td>
                                                    <td><input  type="text" name="pdsm[{{ $loop->iteration }}][expense_amount]" data-id="expense_amount" class="expense_amount form-control erp-form-control-sm moveIndexsm validNumber validOnlyFloatNumber" autocomplete="off"></td>
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
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/pr.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{$case}}';
        $(".expense_amount").keyup(function(){
            TotalExpenseAmount();
        });
    </script>
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
                $('#pr_form').find('#purchase_order').val(code);
                $('#pr_form').find('#purchase_order_id').val(po_id);
                $("#grn_ageing_term_id").val(payment_term_id).trigger('change');
                $('#pr_form').find('#grn_ageing_term_value').val(payment_term_days);
                $('#pr_form').find('#supplier_name').val(supplier_name);
                $('#pr_form').find('#supplier_id').val(supplier_id);
                $('#pr_form').find(".currency").val(currency_id).trigger('change');
                $('#pr_form').find('#exchange_rate').val(exchange_rate);



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
                                    '<td><input type="text" name="pd['+total_length+'][barcode]" data-id="barcode" value="'+notNull(row['barcode']['product_barcode_barcode'])+'" title="'+notNull(row['barcode']['product_barcode_barcode'])+'" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" value="'+notNull(row['product']['product_name'])+'" title="'+notNull(row['product']['product_name'])+'" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td>'+
                                        '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd['+total_length+'][uom]" data-id="uom" title="'+row['uom']['uom_name']+'">'+
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
                'id':'pd_uom',
                'fieldClass':'pd_uom field_readonly',
                'type':'select'
            },
            {
                'id':'pd_packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
            {
                'id':'grn_supplier_barcode',
                'fieldClass':'moveIndex'
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'foc_qty',
                'fieldClass':'tb_moveIndex validNumber'
            },
            {
                'id':'fc_rate',
                'fieldClass':'fc_rate tb_moveIndex validNumber'
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'dis_perc',
                'fieldClass':'tblGridCal_discount_perc tb_moveIndex validNumber'
            },
            {
                'id':'dis_amount',
                'fieldClass':'tblGridCal_discount_amount tb_moveIndex validNumber'
            },
            {
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc tb_moveIndex validNumber'
            },
            {
                'id':'vat_amount',
                'fieldClass':'tblGridCal_vat_amount tb_moveIndex validNumber'
            },
            {
                'id':'batch_no',
                'fieldClass':'tb_moveIndex'
            },
            {
                'id':'production_date',
                'fieldClass':'date_inputmask tb_moveIndex',
            },
            {
                'id':'expiry_date',
                'fieldClass':'date_inputmask tb_moveIndex',
            },
            {
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['purc_grn_dtl_id','product_id','product_barcode_id','uom_id','grn_supplier_id'];
        $(".date_inputmask").inputmask("99-99-9999", {
            "mask": "99-99-9999",
            "placeholder": "dd-mm-yyyy",
            autoUnmask: true
        });
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-expense-row-repeated.js') }}" type="text/javascript"></script>
@endsection


