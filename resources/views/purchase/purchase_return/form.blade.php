@extends('layouts.layout')
@section('title', 'Purchase Return')

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
            $retRef_code = isset($data['current']->refPurcReturn->grn_code) ?  $data['current']->refPurcReturn->grn_code : "";
            $retRef_id = isset($data['current']->refPurcReturn->grn_id) ?  $data['current']->refPurcReturn->grn_id : "";
            $ref_supplier_id = isset($data['current']->refPurcReturn->supplier_id) ? $data['current']->refPurcReturn->supplier_id : "";

        }
$form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <!--begin::Form-->
    @php $id = isset($data['current']->grn_id)?$data['current']->grn_id:'';  @endphp
    <form id="pr_form" class="kt-form" method="post" action="{{ action('Purchase\PurchaseReturnController@store' , $id) }}">
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
                                <label class="col-lg-6 erp-col-form-label">Document Date:</label>
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
                                <label class="col-lg-6 erp-col-form-label">Vendor Name: <span class="required">*</span></label>
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
                    </div>
                    <div class="row form-group-block">
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
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Payment Type:</label>
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
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="data_entry_header">
                                <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                                <div class="dropdown dropdown-inline">
                                    <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                        <i class="flaticon-more" style="color: #666666;"></i>
                                    </button>
                                    @php
                                        $headings = ['Sr No','Barcode','Product Name','Unit Price','Sale Rate','Qty','Sys Qty','M.R.P',
                                                    'Amount','Disc %','Disc Amt','After Disc Amt','Tax on','GST %','GST Amt','FED %',
                                                    'FED Amt','Disc on','Special Disc%','Special Disc Amt','Gross Amt','Net Amount',
                                                    'Net TP','Last TP','Vend Last TP','TP Difference','GP%','GP Amount','Notes',
                                                    'FC Rate','UOM','Packing','GRN'];
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
                                                    <input type="hidden" id="purc_grn_dtl_id" class="purc_grn_dtl_id form-control erp-form-control-sm">
                                                    <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                    <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                    {{--<input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                                     <input id="grn_supplier_id" readonly type="hidden" class="grn_supplier_id form-control erp-form-control-sm handle"> --}}
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
                                                    <input id="sale_rate" type="text" class="tblGridCal_sale_rate validNumber form-control erp-form-control-sm">
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
                                                    <input id="fed_amount" type="text" readonly class="tblGridCal_fed_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
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
                                                    <input id="spec_disc_amount" readonly type="text" class="tblGridCal_spec_disc_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
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
                                                        <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                        {{--<input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                        <input readonly type="hidden" data-id="grn_supplier_id" name="pd[{{$loop->iteration}}][grn_supplier_id]" value="" class="grn_supplier_id form-control erp-form-control-sm">
                                                        <input readonly type="hidden" data-id="purchase_order_id" name="pd[{{$loop->iteration}}][purchase_order_id]" value="{{ $dtl->purchase_order_id }}" class="purchase_order_id form-control erp-form-control-sm handle">--}}
                                                    </td>
                                                    <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" data-id="product_name" name="pd[{{ $loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name) ? $dtl->product->product_name : '' }}" class="product_name form-control erp-form-control-sm" readonly> </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][quantity]" data-id="quantity" value="{{ $dtl->tbl_purc_grn_dtl_quantity }}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][rate]" data-id="rate" value="{{ number_format($dtl->tbl_purc_grn_dtl_rate, 3, '.', '') }}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][sale_rate]" data-id="sale_rate" value="{{ number_format($dtl->tbl_purc_grn_dtl_sale_rate, 3, '.', '') }}" class="tblGridCal_sale_rate form-control erp-form-control-sm validNumber"></td>
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
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][gst_perc]" data-id="gst_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_percent, 3, '.', '') }}"  class="tblGridCal_gst_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][gst_amount]" data-id="gst_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_vat_amount, 3, '.', '') }}" class="tblGridCal_gst_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][fed_perc]" data-id="fed_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_fed_percent, 3, '.', '') }}"  class="tblGridCal_fed_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][fed_amount]" data-id="fed_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_fed_amount, 3, '.', '') }}" class="tblGridCal_fed_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td>
                                                        <select class="form-control erp-form-control-sm pd_disc" name="pd[{{ $loop->iteration }}][pd_disc]"  data-id="pd_disc">
                                                            @foreach($data['disc_on'] as $disc_on)
                                                                <option value="{{strtolower($disc_on->constants_value)}}" {{strtolower($dtl->tbl_purc_grn_dtl_disc_on) == strtolower($disc_on->constants_value)?"selected":""}}>{{$disc_on->constants_value}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][spec_disc_perc]" data-id="spec_disc_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_spec_disc_perc, 3, '.', '') }}"  class="tblGridCal_spec_disc_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][spec_disc_amount]" data-id="spec_disc_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_spec_disc_amount, 3, '.', '') }}" class="tblGridCal_spec_disc_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][gross_amount]" data-id="gross_amount" value="{{ number_format($dtl->tbl_purc_grn_dtl_gross_amount, 3, '.', '') }}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" ></td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][net_amount]" data-id="net_amount" value="{{ number_format($dtl->tbl_purc_grn_dtl_total_amount, 3, '.', '') }}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" ></td>
                                                    {{-- <td><input type="text" name="pd[{{ $loop->iteration }}][net_tp]" data-id="net_tp" value="{{ number_format($dtl->tbl_purc_grn_dtl_net_tp, 3, '.', '') }}" class="{{ $rateColorClass }}  tblGridCal_net_tp form-control erp-form-control-sm validNumber"></td> --}}
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][net_tp]" data-id="net_tp" value="{{ number_format($dtl->tbl_purc_grn_dtl_net_tp, 3, '.', '') }}" class="tblGridCal_net_tp form-control erp-form-control-sm validNumber"></td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][last_tp]" data-id="last_tp" value="{{ number_format($dtl->tbl_purc_grn_dtl_last_tp, 3, '.', '') }}" class="tblGridCal_last_tp form-control erp-form-control-sm validNumber" ></td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][vend_last_tp]" data-id="vend_last_tp" value="{{ number_format($dtl->tbl_purc_grn_dtl_vend_last_tp, 3, '.', '') }}" class="tblGridCal_vend_last_tp form-control erp-form-control-sm validNumber" ></td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][tp_diff]" data-id="tp_diff" value="{{ number_format($dtl->tbl_purc_grn_dtl_tp_diff, 3, '.', '') }}" class="tblGridCal_tp_diff form-control erp-form-control-sm validNumber"></td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][gp_perc]" data-id="gp_perc" value="{{ number_format($dtl->tbl_purc_grn_dtl_gp_perc, 3, '.', '') }}"  class="tblGridCal_gp_perc form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input readonly type="text" name="pd[{{ $loop->iteration }}][gp_amount]" data-id="gp_amount"  value="{{ number_format($dtl->tbl_purc_grn_dtl_gp_amount, 3, '.', '') }}" class="tblGridCal_gp_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][remarks]" data-id="remarks"  value="{{ $dtl->tbl_purc_grn_dtl_remarks }}" class="tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][fc_rate]" data-id="fc_rate" value="{{ number_format($dtl->tbl_purc_grn_dtl_fc_rate, 3, '.', '') }}" class="tblGridCal_fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                    {{--<td><input type="text" name="pd[{{ $loop->iteration }}][grn_no]" data-id="grn_no" value="" class="grn_no form-control erp-form-control-sm" readonly></td>
                                                     <td><input type="text" name="pd[{{ $loop->iteration }}][po_no]" data-id="po_no" value="{{ isset($dtl->purchase_order->purchase_order_code)?$dtl->purchase_order->purchase_order_code:"" }}" class="po_no tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][po_net_tp]" data-id="po_net_tp" value="{{ isset($dtl->po_net_tp)?$dtl->po_net_tp:'' }}" class="po_net_tp tb_moveIndex form-control erp-form-control-sm validNumber" readonly></td> --}}
                                                    {{-- <td><input type="text"  name="pd[{{ $loop->iteration }}][grn_supplier_barcode]"  data-id="grn_supplier_barcode"  class="sup_barcode tb_moveIndex form-control erp-form-control-sm"   readonly></td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][batch_no]" data-id="batch_no" value="{{ isset($dtl->tbl_purc_grn_dtl_batch_no) ? $dtl->tbl_purc_grn_dtl_batch_no : '' }}"  class="tb_moveIndex form-control erp-form-control-sm"></td>
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][foc_qty]" data-id="foc_qty" value="{{ $dtl->tbl_purc_grn_dtl_foc_quantity }}" class="tblGridCal_foc_qty tb_moveIndex form-control erp-form-control-sm validNumber"> </td>
                                                    @php $prod_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_production_date)))); @endphp
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][production_date]" data-id="production_date" value="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}" title="{{ $prod_date == '01-01-1970' ? '' : $prod_date }}" class="date_inputmask tb_moveIndex form-control form-control-sm" /> </td>
                                                    @php $expiry_date= date('d-m-Y', strtotime(trim(str_replace('/','-',$dtl->tbl_purc_grn_dtl_expiry_date)))); @endphp
                                                    <td><input type="text" name="pd[{{ $loop->iteration }}][expiry_date]"  data-id="expiry_date" value="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}" title="{{ $expiry_date == '01-01-1970' ? '' : $expiry_date }}" class="date_inputmask tb_moveIndex form-control form-control-sm" /> </td> --}}
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
                                                <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td class="total_grid_gst_amount">
                                                <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td class="total_grid_fed_perc">
                                                <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td class="total_grid_fed_amount">
                                                <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
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
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @include('purchase.purchase_return.summary_total')
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <label class="erp-col-form-label">Remarks:</label>
                            <textarea type="text" rows="4" id="po_notes" name="po_notes" maxlength="255" class="form-control erp-form-control-sm">{{isset($data['current']->grn_remarks)?$data['current']->grn_remarks:''}}</textarea>
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
    @include('partial_script.po_header_calc');
    <script src="{{ asset('js/pages/js/pr.js?v=3') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{$case}}';
        $(".expense_amount").keyup(function(){
            TotalExpenseAmount();
        });
    </script>

    <script>
        // Click on Purchase Return (Go) Button to Load the Data
        $(document).on('click' , '#pucRetGetData' , function(e){

            var required = ['retqty_code','retqty_id','ref_supplier_id'];
            var validation = true;
            required.forEach((el) => {
               if($("#" + el).val() == ""){
                    validation = false;
               }
            });
            if($('#supplier_id').val() == ""){
                toastr.error("Please Select Supplier");
                $('#retqty_code,#retqty_id,#ref_supplier_id').val("");
                $('#supplier_name').focus();
                validation = false;
                return false;
            }else if($('#supplier_id').val() != $('#ref_supplier_id').val()){
                toastr.error("Supplier Do Not Match With Reference Supplier");
                $('#retqty_code,#retqty_id,#ref_supplier_id').val("");
                $('#supplier_name').focus();
                validation = false;
                return false;
            }
            if(validation){
                var pr_id = $("#retqty_id").val();
                var pr_supplier_id = $("#ref_supplier_id").val();
                var pr_grn_id = $("#retqty_id").val();
                $.ajax({
                    headers : {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/purchase-return/pr/'+pr_id,
                    data : {
                        "supplier_id" : pr_supplier_id,
                        "grn_id" : pr_grn_id
                    },
                    beforeSend: function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success: function(response, data){
                        $('body').removeClass('pointerEventsNone');
                        if(data == "success") {
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
                            for(var p=0; p < response['data']['all']['grn_dtl'].length; p++ ){
                                total_length++;
                                var  row = response['data']['all']['grn_dtl'][p];
                                console.log(row);
                                var retnableQty = parseFloat(row['purc_return_returnable_qty']);
                                var collected_qty = parseFloat(row['purc_return_collected_qty']);
                                var retnablePendingQty = parseFloat(row['purc_return_waiting_qty']);
                                var productionDate = '{{ (date("d-m-Y", strtotime(trim(str_replace("/","-",'+row["tbl_purc_grn_dtl_production_date"]+'))))) }}';
                                var expiryDate = '{{ (date("d-m-Y", strtotime(trim(str_replace("/","-",'+row["tbl_purc_grn_dtl_expiry_date"]+'))))) }}';
                                if(productionDate == "01-01-1970"){productionDate = "";}
                                if(expiryDate == "01-01-1970"){expiryDate = "";}
                                tr += '<tr class="new-row">'+
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>'+
                                    '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off" aria-invalid="false">'+
                                    '<input type="hidden" name="pd['+total_length+'][purc_grn_dtl_id]" data-id="purc_grn_dtl_id" value="" class="purc_grn_dtl_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                    '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+row['product_id']+'" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                    '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+row['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                    '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+row['uom_id']+'" class="uom_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                    '<input type="hidden" name="pd['+total_length+'][grn_supplier_id]" data-id="grn_supplier_id" value="" class="grn_supplier_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                    '</td>'+
                                    '<td><input type="text" name="pd['+total_length+'][pd_barcode]" data-id="pd_barcode" data-url="" value="'+row['product_barcode_barcode']+'" title="'+row['product_barcode_barcode']+'" class="form-control erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" readonly="" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" data-url="" value="'+row['product']['product_name']+'" title="'+row['product']['product_name']+'" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off"></td>'+
                                    '<td>'+
                                        '<div class="erp-select2">'+
                                            '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd['+total_length+'][pd_uom]">'+
                                                '<option value="'+row['uom_id']+'">'+row['uom']['uom_name']+'</option>'+
                                            '</select>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td><input type="text" name="pd['+total_length+'][pd_packing]" data-id="pd_packing" data-url="" value="'+row['barcode']['product_barcode_packing']+'" title="'+row['tbl_purc_grn_dtl_packing']+'" class="form-control erp-form-control-sm pd_packing" readonly="" autocomplete="off"></td>'+
                                    // '<td><input type="text" name="pd['+total_length+'][grn_supplier_barcode]" data-id="grn_supplier_barcode" data-url="" value="" title="" class="form-control erp-form-control-sm moveIndex" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][quantity]" data-id="quantity" data-url="" value="" title="" class="form-control erp-form-control-sm tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][rtrnpending_quantity]" readonly data-id="rtrnpending_quantity" data-url="" value="'+notEmptyZero(retnablePendingQty)+'" title="'+notEmptyZero(retnablePendingQty)+'" class="form-control erp-form-control-sm tb_moveIndex validNumber validOnlyFloatNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][returnable_quantity]" data-id="returnable_quantity" data-url="" value="'+notEmptyZero(retnableQty)+'" title="'+notEmptyZero(retnableQty)+'" class="form-control erp-form-control-sm tb_moveIndex validNumber validOnlyFloatNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][foc_qty]" data-id="foc_qty" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_foc_quantity'])+'" title="'+notNull(row['tbl_purc_grn_dtl_foc_quantity'])+'" class="form-control erp-form-control-sm tb_moveIndex validNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][fc_rate]" data-id="fc_rate" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_fc_rate'])+'" title="'+notNull(row['tbl_purc_grn_dtl_fc_rate'])+'" class="form-control erp-form-control-sm fc_rate tb_moveIndex validNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][rate]" data-id="rate" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_rate'])+'" title="'+notNull(row['tbl_purc_grn_dtl_rate'])+'" class="form-control erp-form-control-sm tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][amount]" data-id="amount" data-url="" value="0" title="0" class="form-control erp-form-control-sm tblGridCal_amount tb_moveIndex validNumber validOnlyFloatNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][dis_perc]" data-id="dis_perc" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_disc_percent'])+'" title="'+notNull(row['tbl_purc_grn_dtl_disc_percent'])+'" class="form-control erp-form-control-sm tblGridCal_discount_perc tb_moveIndex validNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][dis_amount]" data-id="dis_amount" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_disc_amount'])+'" title="'+notNull(row['tbl_purc_grn_dtl_disc_amount'])+'" class="form-control erp-form-control-sm tblGridCal_discount_amount tb_moveIndex validNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][vat_perc]" data-id="vat_perc" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_vat_percent'])+'" title="'+notNull(row['tbl_purc_grn_dtl_vat_percent'])+'" class="form-control erp-form-control-sm tblGridCal_vat_perc tb_moveIndex validNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][vat_amount]" data-id="vat_amount" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_vat_amount'])+'" title="'+notNull(row['tbl_purc_grn_dtl_vat_amount'])+'" class="form-control erp-form-control-sm tblGridCal_vat_amount tb_moveIndex validNumber" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][batch_no]" data-id="batch_no" data-url="" value="'+notNull(row['tbl_purc_grn_dtl_batch_no'])+'" title="'+notNull(row['tbl_purc_grn_dtl_batch_no'])+'" class="form-control erp-form-control-sm tb_moveIndex" autocomplete="off"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][production_date]" data-id="production_date" data-url="" value="'+productionDate+'" title="'+productionDate+'" class="form-control erp-form-control-sm date_inputmask tb_moveIndex" autocomplete="off" im-insert="true"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][expiry_date]" data-id="expiry_date" data-url="" value="'+expiryDate+'" title="'+expiryDate+'" class="form-control erp-form-control-sm date_inputmask tb_moveIndex" autocomplete="off" im-insert="true"></td>'+
                                    '<td><input type="text" name="pd['+total_length+'][gross_amount]" data-id="gross_amount" data-url="" value="" title="" class="form-control erp-form-control-sm tblGridCal_gross_amount validNumber" readonly="" autocomplete="off"></td>'+
                                    '<td class="text-center">'+
                                        '<div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div>'+
                                    '</td>'+
                                '</tr>';
                            }

                            var actionAlert = swal.fire({
                                title: "Select Action?",
                                html: '<button type="button" role="button" tabindex="0" class="clearAppendDataBtn btn btn-warning">' + 'Clear & Append' + '</button>' +
                                      '<button type="button" role="button" tabindex="0" class="appendDataBtn btn btn-success">' + 'Append' + '</button>' +
                                      '<button type="button" role="button" tabindex="0" class="cancelDataBtn btn btn-danger">' + 'Cancel' + '</button>',
                                type: "warning",
                                showConfirmButton: false,
                                showCancelButton: false
                            });
                            $(document).on('click' , '.appendDataBtn',function(e){
                                $('.erp_form__grid_body').append(tr);
                                tr = '';
                                actionAlert.close();
                                gridCacl();
                                return true;
                            });
                            $(document).on('click' , '.clearAppendDataBtn',function(e){
                                $('.erp_form__grid_body').html('').append(tr);
                                tr = '';
                                actionAlert.close();
                                gridCacl();
                                return true;
                            });
                            $(document).on('click' , '.cancelDataBtn',function(e){
                                tr = '';
                                actionAlert.close();
                                return true;
                            });

                            // addDataInit();

                        }else{
                            toastr.error("There is something wrong");
                        }
                    },
                    error : function(){
                        $('body').removeClass('pointerEventsNone');
                        toastr.error("There is something wrong");
                    }
                });
            }else{
                toastr.error("Something went wrong! Refresh Page.")
            }
        });
        function gridCacl(){
            allCalcFunc();
            $('input').attr('autocomplete', 'off');
            updateKeys();
            table_td_sortable();
            allGridTotal();
            $(".date_inputmask").inputmask("99-99-9999", {
                "mask": "99-99-9999",
                "placeholder": "dd-mm-yyyy",
                autoUnmask: true
            });
        }
        function createButton(text, cb) {
            return $('<button>' + text + '</button>').on('click', cb);
        }

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
                                    // '<td><input type="text" name="pd['+total_length+'][grn_supplier_barcode]" data-id="grn_supplier_barcode" value="" title="" class="form-control erp-form-control-sm moveIndex"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][barcode]" data-id="barcode" value="'+notNull(row['barcode']['product_barcode_barcode'])+'" title="'+notNull(row['barcode']['product_barcode_barcode'])+'" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" value="'+notNull(row['product']['product_name'])+'" title="'+notNull(row['product']['product_name'])+'" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td>'+
                                        '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd['+total_length+'][uom]" data-id="uom" title="'+row['uom']['uom_name']+'">'+
                                            '<option value="'+notNull(row['uom']['uom_id'])+'">'+notNull(row['uom']['uom_name'])+'</option>'+
                                        '</select>'+
                                    '</td>' +
                                    '<td><input type="text" name="pd['+total_length+'][packing]" data-id="packing" value="'+notNull(row['purchase_order_dtlpacking'])+'" title="'+notNull(row['purchase_order_dtlpacking'])+'" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][quantity]" data-id="quantity" value="'+notNull(row['purchase_order_dtlquantity'])+'" title="'+notNull(row['purchase_order_dtlquantity'])+'" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][rtrnpending_quantity]" readonly data-id="rtrnpending_quantity" value="'+notNull(row['tbl_purc_grn_dtl_retpend_qty'])+'" title="'+notNull(row['tbl_purc_grn_dtl_retpend_qty'])+'" class="rtrnpending_quantity moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][returnable_quantity]" data-id="returnable_quantity" value="'+notNull(row['tbl_purc_grn_dtl_retable_qty'])+'" title="'+notNull(row['tbl_purc_grn_dtl_retable_qty'])+'" class="returnable_quantity moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][foc_qty]" data-id="foc_qty" value="'+notNull(row['purchase_order_dtlfoc_quantity'])+'" title="'+notNull(row['purchase_order_dtlfoc_quantity'])+'" class="form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][fc_rate]" data-id="fc_rate" value="'+notNull(row['purchase_order_dtlfc_rate'])+'" title="'+notNull(row['purchase_order_dtlfc_rate'])+'" class="fc_rate form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][rate]" data-id="rate" value="'+notNullNo(row['purchase_order_dtlrate'])+'" title="'+notNullNo(row['purchase_order_dtlrate'])+'" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][amount]" data-id="amount" value="'+notNullNo(row['purchase_order_dtlamount'])+'" title="'+notNullNo(row['purchase_order_dtlamount'])+'" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount]" data-id="discount" value="'+notNullNo(row['purchase_order_dtldisc_percent'])+'" title="'+notNullNo(row['purchase_order_dtldisc_percent'])+'" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount_val]" data-id="discount_val" value="'+notNullNo(row['purchase_order_dtldisc_amount'])+'" title="'+notNullNo(row['purchase_order_dtldisc_amount'])+'" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber"></td>' +
                                    /*'<td><input type="text" name="pd['+total_length+'][grn_gst]" data-id="grn_gst" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>' +*/
                                    '<td><input type="text" name="pd['+total_length+'][vat_perc]" data-id="vat_perc" value="'+notNullNo(row['purchase_order_dtlvat_percent'])+'" title="'+notNullNo(row['purchase_order_dtlvat_percent'])+'" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][vat_val]" data-id="vat_val" value="'+notNullNo(row['purchase_order_dtlvat_amount'])+'" title="'+notNullNo(row['purchase_order_dtlvat_amount'])+'" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber"></td>' +
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
                'fieldClass':'sale_rate validNumber'
            },
            // {
            //     'id': 'grn_supplier_barcode',
            //     'fieldClass': 'sup_barcode moveIndex',
            //     'readonly': true
            // },
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
                'fieldClass':'tblGridCal_discount_amount validNumber',
            },
            {
                'id':'after_dis_amount',
                'fieldClass':'tblGridCal_after_discount_amount validNumber',
                'readonly':true
            },
            {
                'id':'pd_tax_on',
                'fieldClass':'pd_tax_on',
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
                'fieldClass':'tblGridCal_net_tp validNumber'
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
        ];
        var arr_hidden_field = ['purc_grn_dtl_id', 'product_id', 'product_barcode_id'];
        $(".date_inputmask").inputmask("99-99-9999", {
            "mask": "99-99-9999",
            "placeholder": "dd-mm-yyyy",
            autoUnmask: true
        });
    </script>
    @yield('summary_total_pageJS')
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>

    <script>
        var form_modal_type = 'purc_return';
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
                pd_disc = 'GA';
            }
            if(valueEmpty(pd_tax_on)){
                pd_tax_on = 'DA';
            }
            $('.erp_form__grid_header').find('.pd_tax_on').val(pd_tax_on).change();
            $('.erp_form__grid_header').find('.pd_disc').val(pd_disc).change();
        }
    </script>
@endsection


