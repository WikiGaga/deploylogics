@extends('layouts.layout')
@section('title', 'Sales Delivery')

@section('pageCSS')
@endsection

@section('content')
    <!--begin::Form-->
    @php
        // dd($data['current']);
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
            $date =  date('d-m-Y');
            $user_id = Auth::user()->id;
            $customer_id = isset($data['customer']->customer_id)?$data['customer']->customer_id:'';
            $customer_name = isset($data['customer']->customer_name)?$data['customer']->customer_name:'';
            $length = 0;
            $selected_rate_type = 'item_sale_rate';
        }
        if($case == 'edit'){
            $id = $data['current']->sales_delivery_id;
            $code = $data['current']->sales_delivery_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_delivery_date))));
            $credit_days = $data['current']->sales_delivery_credit_days;
            $payment_term = $data['current']->payment_term_id;
            $currency_id = $data['current']->currency_id;
            $customer_id = isset($data['current']->customer)?$data['current']->customer->customer_id:"";
            $customer_name = isset($data['current']->customer)?$data['current']->customer->customer_name:"";
            $sales_order_id = isset($data['current']->SO->sales_order_id)?$data['current']->SO->sales_order_id:'';
            $sales_order_code = isset($data['current']->SO->sales_order_code)?$data['current']->SO->sales_order_code:'';
            $type = $data['current']->sales_delivery_sales_type;
            $user_id = $data['current']->sales_delivery_sales_man;
            $payment_mode = $data['current']->payment_mode_id;
            $exchange_rate = $data['current']->sales_delivery_exchange_rate;
            $mobile = $data['current']->sales_delivery_mobile_no;
            $remarks = $data['current']->sales_delivery_remarks;
            $sales_contract_id = isset($data['current']->sales_contract)?$data['current']->sales_contract->sales_contract_id:"";
            $sales_contract_code = isset($data['current']->sales_contract)?$data['current']->sales_contract->sales_contract_code:"";
            $delivery_no = $data['current']->sales_delivery_no;
            $sales_id = isset($data['current']->sales_invoice)?$data['current']->sales_invoice->sales_id:"";
            $sales_code = isset($data['current']->sales_invoice)?$data['current']->sales_invoice->sales_code:"";
            $selected_rate_type = $data['current']->sales_delivery_rate_type;
            $rate_perc = $data['current']->sales_delivery_rate_perc;
            $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
            $expense_dtls = isset($data['current']->expense)? $data['current']->expense :[];
            $length = count($expense_dtls);
        }
        $form_type = 'sale_delivery';
    @endphp
    @permission($data['permission'])
           <form id="delivery_form" class="kt-form" method="post" action="{{ action('Sales\SalesDeliveryController@store', isset($id)?$id:"") }}">
           <input type="hidden" value='{{$form_type}}' id="form_type" name="form_type">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
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
                                            <input type="text" name="sales_date" class="form-control moveIndex erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" autofocus/>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="col-lg-4" style="display:none;">
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
                                                <input type="text" id="sales_credit_days" name="sales_credit_days" value="{{isset($credit_days)?$credit_days:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>--->
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Sales Invoice:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" value="{{isset($sales_code)?$sales_code:""}}" id="sales_code" data-url="{{action('Common\DataTableController@inlineHelpOpen','salesInvoiceHelp')}}" autocomplete="off" name="sales_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                <input type="hidden" value="{{isset($sales_id)?$sales_id:""}}" id="sales_id" name="sales_id"/>
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
                                    <label class="col-lg-6 erp-col-form-label">Currency: <span class="required">*</span></label>
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
                                    <label class="col-lg-6 erp-col-form-label">Customer: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="customer_name" value="{{isset($customer_name)?$customer_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','customerHelp')}}" autocomplete="off" name="customer_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                <input type="hidden" id="customer_id" name="customer_id" value="{{isset($customer_id)?$customer_id:''}}"/>
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
                                    <label class="col-lg-6 erp-col-form-label">Booking No:</label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="sales_order_code" value="{{isset($sales_order_code)?$sales_order_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','saleorderHelp')}}" autocomplete="off" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                <input type="hidden" id="sales_order_booking_id" name="sales_order_booking_id" value="{{isset($sales_order_id)?$sales_order_id:''}}"/>
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
                                    <label class="col-lg-6 erp-col-form-label">Exchange Rate:<span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Salesman: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sales_sales_man" name="sales_sales_man">
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
                                    <label class="col-lg-6 erp-col-form-label">Sales Type: <span class="required">*</span></label>
                                    <div class="col-lg-6">
                                        <div class="erp-select2 form-group">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sales_sales_type" name="sales_sales_type">
                                                <option value="0">Select</option>
                                                @if($case == 'edit')
                                                    @php $select_type = isset($type)?$type:""; @endphp
                                                    @foreach($data['payment_type'] as $payment_type)
                                                        <option value="{{$payment_type->payment_type_id}}" {{$select_type == $payment_type->payment_type_id?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                                    @endforeach
                                                @else
                                                    @foreach($data['payment_type'] as $payment_type)
                                                        @if($payment_type->payment_type_name ==='Cash')
                                                            @php $select_type = $payment_type->payment_type_id  @endphp
                                                        @endif
                                                        <option value="{{$payment_type->payment_type_id}}" {{$select_type == $payment_type->payment_type_id?"selected":""}}>{{$payment_type->payment_type_name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Delivery No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" id="sales_delivery_no" name="sales_delivery_no" value="{{isset($delivery_no)?$delivery_no:''}}" class="moveIndex form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Mobile No:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="sales_mobile_no" maxlength="15" value="{{isset($mobile)?$mobile:''}}" class="form-control erp-form-control-sm AllowNumberDash text-left moveIndex">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Sales Contract:</label>
                                    <div class="col-lg-6">
                                        <div class="erp_form___block">
                                            <div class="input-group open-modal-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                                </div>
                                                <input type="text" value="{{isset($sales_contract_code)?$sales_contract_code:""}}" id="sales_contract_code" data-url="{{action('Common\DataTableController@inlineHelpOpen','salesContractHelp')}}" autocomplete="off" name="sales_contract_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                <input type="hidden" value="{{isset($sales_contract_id)?$sales_contract_id:""}}" id="sales_contract_id" name="sales_contract_id"/>
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
                            <div class="col-lg-8">
                                <div class="row">
                                    <label class="col-lg-3 erp-col-form-label">Rate Type <span class="required">*</span></label>
                                    <div class="col-lg-9">
                                        <div class="input-group erp-select2-sm">
                                            <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rate_type" name="rate_type">
                                                <option value="0">Select</option>
                                                @foreach($data['rate_types'] as $key=>$rate_type)
                                                    <option value="{{$key}}" {{$selected_rate_type==$key?"selected":""}}>{{$rate_type}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="la la-plus"></i>
                                        </span>
                                            </div>
                                            <input type="text" id="rate_perc" name="rate_perc" value="{{isset($rate_perc)?$rate_perc:''}}" class="moveIndex form-control erp-form-control-sm validNumber">
                                            <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-percent"></i>
                                        </span>
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
                                        <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                            <i class="flaticon-more" style="color: #666666;"></i>
                                        </button>
                                        @php
                                            $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Qty',
                                                          'FOC Qty','FC Rate','G.Rate','Rate','Amount','Disc%','Disc Amt','VAT%','Vat Amt','Gross Amt'];
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
                                    <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                        <thead class="erp_form__grid_header">
                                            <tr id="erp_form_grid_header_row">
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Sr.</div>
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                        <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                        <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                        <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
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
                                                        <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}">
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
                                                        <select id="pd_uom" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </th>
                                                <th scope="col">
                                                    <div class="erp_form__grid_th_title">Packing</div>
                                                    <div class="erp_form__grid_th_input">
                                                        <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
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
                                                    <div class="erp_form__grid_th_title">G.Rate</div>
                                                    <div class="erp_form__grid_th_input">
                                                        <input readonly id="g_rate" type="text" class="g_rate validNumber form-control erp-form-control-sm">
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
                                                        <input id="amount" readonly type="text" class="tblGridCal_amount validNumber form-control erp-form-control-sm">
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
                                            @if(isset($dtls))
                                                @foreach($dtls as $dtl)
                                                    <tr>
                                                        <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                            <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="sr_no form-control erp-form-control-sm handle" readonly>
                                                            <input type="hidden" name="pd[{{$loop->iteration}}][sales_delivery_dtl_id]" data-id="sales_delivery_dtl_id" value="{{$dtl->sales_delivery_dtl_id}}" class="sales_delivery_dtl_id form-control erp-form-control-sm handle" readonly>
                                                            <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                            <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                            <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                        </td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][pd_barcode]" data-id="pd_barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}" value="{{isset($dtl->barcode->product_barcode_barcode)?$dtl->barcode->product_barcode_barcode:''}}" title="{{isset($dtl->barcode->product_barcode_barcode)?$dtl->barcode->product_barcode_barcode:''}}" class="pd_barcode form-control erp-form-control-sm" readonly></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][product_name]" data-id="product_name" value="{{ $dtl->product->product_name }}"  title="{{ $dtl->product->product_name }}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                        <td>
                                                            <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][pd_uom]" data-id="pd_uom" title="{{ $dtl->uom->uom_name }}">
                                                                <option value="{{ $dtl->uom->uom_id }}">{{ $dtl->uom->uom_name }}</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][pd_packing]" data-id="pd_packing"  value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity"  value="{{$dtl->sales_delivery_dtl_quantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty"  value="{{$dtl->sales_delivery_dtl_foc_qty}}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" data-id="fc_rate"  value="{{number_format($dtl->sales_delivery_dtl_fc_rate,3)}}" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                        <td><input readonly type="text" name="pd[{{$loop->iteration}}][g_rate]" data-id="g_rate"  value="{{number_format($dtl->sales_delivery_dtl_gross_rate,3)}}" class="g_rate form-control erp-form-control-sm validNumber" ></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate"  value="{{number_format($dtl->sales_delivery_dtl_rate,3)}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount"  value="{{number_format($dtl->sales_delivery_dtl_amount,3)}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][dis_perc]" data-id="dis_perc"  value="{{number_format($dtl->sales_delivery_dtl_disc_per,2)}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][dis_amount]" data-id="dis_amount"  value="{{number_format($dtl->sales__deliverydtl_disc_amount,3)}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc"  value="{{number_format($dtl->sales_delivery_dtl_vat_per,2)}}" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][vat_amount]" data-id="vat_amount"  value="{{number_format($dtl->sales_delivery_dtl_vat_amount,3)}}" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                        <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount"  value="{{number_format($dtl->sales_delivery_dtl_total_amount,3)}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-10 col-lg-2 text-right">
                                <table class="tableTotal" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td><div class="t_total_label">Total:</div></td>
                                            <td><span class="t_gross_total t_total">0</span><input type="hidden" id="pro_tot" name="pro_tot"></td>
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
                                        <textarea type="text" rows="4" id="sales_remarks" maxlength="255" name="sales_remarks" class="form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
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
                                                                $expense =\App\Models\TblSaleSalesExpense::where('chart_account_id',$expense_accounts->chart_account_id)->first('sales_expense_amount');
                                                                if($expense != Null){
                                                                    $expense_amount = number_format($expense->sales_expense_amount,3);
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
                                            <td><span class="t_total" id="total_amountsm">0</span><input type="hidden" name='Total_Amount' id='TotalAmtSM'></td>
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
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/sales-delivery.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{$case}}';
        $(".expense_amount").keyup(function(){
            TotalExpenseAmount();
        });
    </script>
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelpSI')}}";
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
                'id':'g_rate',
                'fieldClass':'g_rate validNumber',
                'readonly':true
            },
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate tb_moveIndex validNumber'
            },
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount validNumber',
                'readonly':true
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
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-expense-row-repeated.js') }}" type="text/javascript"></script>
@endsection
