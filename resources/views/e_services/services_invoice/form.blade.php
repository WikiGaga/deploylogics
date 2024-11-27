@extends('layouts.layout')
@section('title', 'Services Invoice')

@section('pageCSS')
@endsection

@section('content')

    <style>
        .erp-select2-sm>.select2 {
            width: 55% !important;
        }
        #changeGridItemRate:hover{
            background: #3f51b5;
        }
        #changeGridItemRate:hover i{
            color: #fff;
        }
    </style>
    <!--begin::Form-->
    @php
        // dd($data['current']);
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code               = $data['document_code'];
            $date               =  date('d-m-Y');
            $user_id            = Auth::user()->id;
            $customer_id        = $data['customer']->customer_id ?? '';
            $customer_name      = $data['current']->customer->customer_name ?? '';
            $customer_code      = $data['current']->customer->customer_code ?? '';
            $selected_rate_type = 'item_sale_rate';
            $select_bank_acc    = "";
            $mobile             = $data['current']->sales_order_mobile_no ?? '';
            $services_order_id      = $data['current']->sales_order_id ?? '';
            $services_order_code    = $data['current']->sales_order_code ?? '';
            // $dtls                = isset($data['current']->dtls)? $data['current']->dtls :[];
            $expense_dtls           = isset($data['current']->expense)? $data['current']->expense :[];
            $length                 = count($expense_dtls);
        }
        if($case == 'edit'){
            $id = $data['current']->sales_id;
            $code = $data['current']->sales_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->sales_date))));
            $credit_days = $data['current']->sales_credit_days;
            $payment_term = $data['current']->payment_term_id;
            $currency_id = $data['current']->currency_id;
            $customer_id = isset($data['current']->customer_view)?$data['current']->customer_view->customer_id:"";
            $customer_name = isset($data['current']->customer_view)?$data['current']->customer_view->customer_name:"";
            $customer_code = isset($data['current']->customer_view)?$data['current']->customer_view->customer_code:"";
            $sales_order_id = isset($data['current']->SO->sales_order_id)?$data['current']->SO->sales_order_id:'';
            $sales_order_code = isset($data['current']->SO->sales_order_code)?$data['current']->SO->sales_order_code:'';
            $delivery_id = $data['current']->sales_delivery_id;
            $type = $data['current']->sales_sales_type;
            $user_id = $data['current']->sales_sales_man;
            $payment_mode = $data['current']->payment_mode_id;
            $exchange_rate = $data['current']->sales_exchange_rate;
            $mobile = $data['current']->sales_mobile_no;
            $address = $data['current']->sales_address;
            $remarks = $data['current']->sales_remarks;
            $select_bank_acc = $data['current']->bank_id;
            $sales_contract_id = isset($data['current']->sales_contract)?$data['current']->sales_contract->sales_contract_id:"";
            $sales_contract_code = isset($data['current']->sales_contract)?$data['current']->sales_contract->sales_contract_code:"";
            $selected_rate_type = $data['current']->sales_rate_type;
            $rate_perc = $data['current']->sales_rate_perc;
            $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
            $expense_dtls = isset($data['current']->expense)? $data['current']->expense :[];
            $length = count($expense_dtls);
           
            $services_order_code = $data['current']->SO->sales_order_code ?? '';
            $services_order_id = $data['current']->SO->sales_order_id ?? '';
        }
    @endphp
    @permission($data['permission'])
    @php $form_type = 'request_invoice'; @endphp
    <form id="sales_form" class="kt-form" method="post" action="{{ route('sales_invoice-c', isset($id)?$id:"") }}">
    <input type="hidden" value='{{$form_type}}' id="form_type" name="form_type">
    
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <div class="row form-group-block mb-3">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="erp-page--title">
                                    {{isset($code)?$code:""}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="erp-page--title bg-danger text-white rounded d-inline-block p-2" id="customer-code-no">
                           {{ isset($customer_code)?$customer_code:"" }} 
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
                    <div class="col-lg-4 d-none">
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
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Customer: <span class="required">*</span></label>
                            <div class="col-lg-9">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text group-input-btn">
                                                <a data-toggle="modal" data-target="#addCustomerModal" type="button">
                                                    <i class="la la-plus" style="line-height:normal;"></i>
                                                </a>    
                                            </span>
                                        </div>
                                        <input type="text" id="customer_name" value="{{isset($customer_name)?$customer_name:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','customerHelp')}}" autocomplete="off" name="customer_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                        <input type="hidden" id="customer_id" name="customer_id" value="{{isset($customer_id)?$customer_id:''}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text group-input-btn" id="searchCustomer">
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
                            <label class="col-lg-6 erp-col-form-label">Mobile No: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="sales_mobile_no" id="sales_mobile_no" maxlength="15" value="{{isset($mobile)?$mobile:''}}" class="form-control erp-form-control-sm AllowNumberDash text-left moveIndex">
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
                                <input type="text" id="sales_delivery_id" name="sales_delivery_id" readonly value="{{isset($delivery_id)?$delivery_id:''}}" class="moveIndex form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Bank Account:</label>
                            <div class="col-lg-9">
                                <div class="erp-select2 form-group">
                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="bank_acc_id" name="bank_acc_id">
                                        <option value="0">Select</option>
                                        @foreach($data['bank_acc'] as $bank_acc)
                                            <option value="{{$bank_acc->chart_account_id}}" {{$bank_acc->chart_account_id == $select_bank_acc?"selected":""}}>{{$bank_acc->chart_code." - ".$bank_acc->chart_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Store: </label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm moveIndex" id="kt-select2_validate" name="store_id">
                                        @foreach($data['store'] as $store)
                                            @if(isset($data['id']))
                                                @php $store_id = isset($data['current']->store_id)?$data['current']->store_id:0; @endphp
                                            @else
                                                @php $store_id = $store->store_default_value == 1 ? $store->store_id : 0 ; @endphp
                                            @endif
                                            <option value="{{$store->store_id}}" {{ $store->store_id == $store_id?'selected':'' }}>{{$store->store_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block d-none">
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Rate Type <span class="required">*</span></label>
                            <div class="col-lg-9">
                                <div class="ChangeRateBlock input-group erp-select2-sm">
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
                                        <span class="input-group-text group-input-btn" id="changeGridItemRate" title="Change Rate Apply">
                                                    <i class="la la-refresh"></i>
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
                            <label class="col-lg-6 erp-col-form-label">Status: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm moveIndex" id="kt-select2_order_status" name="order_status">
                                        @foreach($data['order_status'] as $status)
                                            @if(isset($data['id']))
                                                @php $status_id = isset($data['current']->order_status_id)?$data['current']->order_status_id:10424722121324; @endphp
                                            @else
                                                @php $status_id = 10424722121324; @endphp
                                            @endif
                                            <option value="{{$status->order_status_id}}" {{ $status->order_status_id == $status_id?'selected':'' }}>{{$status->order_status_arabic_names}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                        <label class="col-lg-3 erp-col-form-label">Order Code:</label>
                            <div class="col-lg-9">
                                <div class="erp_form___block">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                    <span class="input-group-text btn-minus-selected-data">
                                                        <i class="la la-minus-circle"></i>
                                                    </span>
                                        </div>
                                        <input type="text" value="{{isset($services_order_code)?$services_order_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','servicesOrderHelp')}}" id="services_order_code" name="services_order_code" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter Service Order">
                                        <input type="hidden" id="services_order_id" name="services_order_id" value="{{isset($services_order_id)?$services_order_id:''}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text group-input-btn" id="getServicesOrderData">
                                                GO
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
                                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                    <i class="flaticon-more" style="color: #666666;"></i>
                                </button>
                                @php
                                    $headings = ['Sr No','Barcode','Product Name','Product Arabic Name','UOM','Packing','Length','Width','Qty',
                                                  'FOC Qty','FC Rate','G.Rate','Rate','Amount','Disc%','Disc Amt','VAT%','Vat Amt','Gross Amt','Note'];
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
                                        <div class="erp_form__grid_th_title">Product Arabic Name</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="product_arabic_name" readonly type="text" class="product_arabic_name form-control erp-form-control-sm">
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
                                    <th scope="col" class="d-none">
                                        <div class="erp_form__grid_th_title">Packing</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    @if($data['form_type'] == 'sales-invoice-c')
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Length</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="pd_length" type="text" class="pd_length validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Width</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="pd_width" type="text" class="pd_width validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                    @endif
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
                                    @if($data['form_type'] == 'sales-invoice-c')
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Notes</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_notes" type="text" name="pd_notes" class="form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    @endif
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
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sales_dtl_id]" data-id="sales_dtl_id" value="{{$dtl->sales_dtl_id}}" class="sales_dtl_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_barcode]" data-id="pd_barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelpSI')}}" value="{{isset($dtl->barcode->product_barcode_barcode)?$dtl->barcode->product_barcode_barcode:''}}" title="{{isset($dtl->barcode->product_barcode_barcode)?$dtl->barcode->product_barcode_barcode:''}}" class="pd_barcode form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][product_name]" data-id="product_name" value="{{ $dtl->product->product_name }}"  title="{{ $dtl->product->product_name }}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][product_arabic_name]" data-id="product_name" value="{{isset($dtl->product->product_arabic_name)?$dtl->product->product_arabic_name:""}}" class="product_arabic_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][pd_uom]" data-id="pd_uom" title="{{ $dtl->barcode->uom->uom_name }}">
                                                    <option value="{{ $dtl->barcode->uom->uom_id }}">{{ $dtl->barcode->uom->uom_name }}</option>
                                                </select>
                                            </td>
                                            <td class="d-none"><input type="text" name="pd[{{$loop->iteration}}][pd_packing]" data-id="pd_packing"  value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            
                                            
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_length]" data-id="pd_length"  value="{{isset($dtl->sales_dtl_length)?$dtl->sales_dtl_length:""}}" class="pd_length form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_width]" data-id="pd_width"  value="{{isset($dtl->sales_dtl_width)?$dtl->sales_dtl_width:""}}" class="pd_width form-control erp-form-control-sm"></td>
                                            
                                            
                                            <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity"  value="{{$dtl->sales_dtl_quantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty"  value="{{$dtl->sales_dtl_foc_qty}}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" data-id="fc_rate"  value="{{number_format($dtl->sales_dtl_fc_rate,3)}}" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input readonly type="text" name="pd[{{$loop->iteration}}][g_rate]" data-id="g_rate"  value="{{number_format($dtl->sales_dtl_gross_rate,3)}}" class="g_rate form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate"  value="{{number_format($dtl->sales_dtl_rate,3)}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount"  value="{{number_format($dtl->sales_dtl_amount,3)}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_perc]" data-id="dis_perc"  value="{{number_format($dtl->sales_dtl_disc_per,2)}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_amount]" data-id="dis_amount"  value="{{number_format($dtl->sales_dtl_disc_amount,3)}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc"  value="{{number_format($dtl->sales_dtl_vat_per,2)}}" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_amount]" data-id="vat_amount"  value="{{number_format($dtl->sales_dtl_vat_amount,3)}}" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount"  value="{{number_format($dtl->sales_dtl_total_amount,3)}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][pd_notes]" data-id="pd_notes"  value="{{isset($dtl->sales_dtl_notes) ? $dtl->sales_dtl_notes : '' }}" class="pd_notes form-control erp-form-control-sm"></td>
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
                                        @if($data['form_type'] == 'sales-invoice-c')
                                        <td></td>
                                        <td></td>
                                        @endif
                                        <td class="total_grid_qty">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td class="total_grid_foc_qty">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
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
                                        
                                        <td class="total_grid_gross_amount">
                                            <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        @if($data['form_type'] == 'sales-invoice-c')
                                        <td></td>
                                        @endif
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
                                                    if($case == 'new' && !empty($services_order_id)){
                                                        $expense =\App\Models\TblSaleSalesOrderExpense::where('sales_order_id',$data['current']->sales_order_id)->where('chart_account_id',$expense_accounts->chart_account_id)->first('sales_order_expense_amount');
                                                        if($expense != Null){
                                                            $expense_amount = number_format($expense->sales_order_expense_amount,3);
                                                        }
                                                    }else{
                                                        $expense_amount = '';
                                                        $expense =\App\Models\TblSaleSalesExpense::where('chart_account_id',$expense_accounts->chart_account_id)->first('sales_expense_amount');
                                                        if($expense != Null){
                                                            $expense_amount = number_format($expense->sales_expense_amount,3);
                                                        }
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
    </form>

    @include('sales.customer.partials.modal')
    <!--end::Form-->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/sales-invoice.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var formcase = '{{$case}}';
        $(".expense_amount").keyup(function(){
            TotalExpenseAmount();
        });
        $(document).on('keyup' , '.pd_length,.pd_width' , function(e){
            var tr = $(this).parents('tr');
            var rowLength = notEmptyZero(tr.find('input.pd_length').val());
            var rowWidth = notEmptyZero(tr.find('input.pd_width').val());

            tr.find('input.tblGridCal_qty').val(parseFloat(rowWidth)*parseFloat(rowLength).toFixed(2));
            amountCalc(tr);
            discount(tr);
            vat(tr);
            grossAmount(tr);
            totalAllGrossAmount();
            totalStockAmount();
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
                'id':'product_arabic_name',
                'fieldClass':'product_arabic_name',
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
                'fieldClass':'pd_packing d-none',
                'readonly':true,
                'skip':true
            },
            {
                'id':'pd_length',
                'fieldClass':'pd_length tb_moveIndex validNumber validOnlyFloatNumber'
            },
            {
                'id':'pd_width',
                'fieldClass':'pd_width tb_moveIndex validNumber validOnlyFloatNumber'
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
            },
            {
                'id':'pd_notes',
                'fieldClass':'pd_notes tb_moveIndex'
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>

    <script>
        // Stock Qutation Data
        $('#getServicesOrderData').click(function(){
            var thix = $(this);
            var val = thix.parents('.input-group').find('input#services_order_id').val();
            if(val){
                swal.fire({
                    title: 'Alert!',
                    text: "Are You Sure To Get Data!",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then(function(result) {
                    if (result.value) {
                        var formData = {
                            quotation_id : val,
                        };
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type        : 'POST',
                            url         : '/services-sales-order/get-services-order-dtl',
                            dataType	: 'json',
                            data        : formData,
                            success: function(response) {
                                if(response['status'] == 'success'){
                                    toastr.success(response.message);

                                    // Set The Customer Details
                                    var customer = response.data.quotation.customer;
                                    var quotation = response.data.quotation.dtls;
                                    
                                    $('#customer_name').val(customer.customer_name);
                                    $('#customer_id').val(response.data.quotation.customer_id);
                                    $('#sales_mobile_no').val(response.data.quotation.sales_order_mobile_no);


                                    var tr = "";
                                    var iteration = $('.erp_form__grid_body').find('tr').length + 1;
                                    for(var i=0;i < quotation.length;i++){
                                        var quotationi = quotation[i];
                                        var product_id = quotationi['product_id'];
                                        var barcode_id = quotationi['product_barcode_id'];
                                        var uom_id = quotationi['barcode']['uom']['uom_id'];
                                        var barcode = quotationi['barcode']['product_barcode_barcode'];
                                        var product_name = quotationi['product']['product_name'];
                                        var product_arabic_name = quotationi['product']['product_arabic_name'];
                                        var uom_name = quotationi['barcode']['uom']['uom_name'];
                                        var packing = notNullEmpty(quotationi['sales_order_dtl_packing']);
                                        var dtl_quantity = quotationi['sales_order_dtl_quantity'];
                                        var foc_quantity = notNullEmpty(quotationi['sales_order_dtl_foc_qty'],3);
                                        var fc_rate = quotationi['sales_order_dtl_fc_rate'];
                                        var rate = notNullEmpty(quotationi['sales_order_dtl_rate'] , 3);
                                        var amount = notNullEmpty(quotationi['sales_order_dtl_amount'] , 3);
                                        var discount_per = notNullEmpty(quotationi['sales_order_dtl_disc_per'],3);
                                        var discount_amount = notNullEmpty(quotationi['sales_order_dtl_disc_amount'],3);
                                        var vat_per = quotationi['sales_order_dtl_vat_per'];
                                        var vat_amount = notNullEmpty(quotationi['sales_order_dtl_vat_amount'],3);
                                        var gross_amount = notNullEmpty(quotationi['sales_order_dtl_total_amount'],3);
                                        var product_length = notNullEmpty(quotationi['sales_order_dtl_length']);
                                        var product_width = notNullEmpty(quotationi['sales_order_dtl_width']);
                                        var product_notes = notNullEmpty(quotationi['sales_order_dtl_notes']);
                                        tr += '<tr>'+
                                            '<td class="handle">'+
                                                '<i class="fa fa-arrows-alt-v handle"></i>'+
                                                '<input type="text" value="1" name="pd['+iteration+'][sr_no]" title="1" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off">'+
                                                '<input type="hidden" name="pd['+iteration+'][product_id]" data-id="product_id" value="'+product_id+'" class="product_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                                '<input type="hidden" name="pd['+iteration+'][product_barcode_id]" data-id="product_barcode_id" value="'+barcode_id+'" class="product_barcode_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                                '<input type="hidden" name="pd['+iteration+'][uom_id]" data-id="uom_id" value="'+uom_id+'" class="uom_id form-control erp-form-control-sm" readonly="" autocomplete="off">'+
                                                '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][pd_barcode]" data-id="pd_barcode" data-url="" value="'+barcode+'" title="'+barcode+'" class="form-control erp-form-control-sm pd_barcode tb_moveIndex open_inline__help" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][product_name]" data-id="product_name" data-url="" value="'+product_name+'" title="'+product_name+'" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][product_arabic_name]" data-id="product_arabic_name" data-url="" value="'+product_arabic_name+'" title="'+product_arabic_name+'" class="form-control erp-form-control-sm product_name" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<div class="erp-select2">'+
                                                    '<select name="pd['+iteration+'][pd_uom]" class="pd_uom field_readonly form-control erp-form-control-sm">'+
                                                        '<option value="'+uom_id+'">'+uom_name+'</option>'+
                                                    '</select>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td class="d-none">'+
                                                '<input type="text" name="pd['+iteration+'][pd_packing]" data-id="pd_packing" data-url="" value="'+packing+'" title="'+packing+'" class="form-control erp-form-control-sm pd_packing" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][pd_length]" data-id="pd_length" data-url="" value="'+product_length+'" title="'+product_length+'" class="form-control erp-form-control-sm pd_length" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][pd_width]" data-id="pd_width" data-url="" value="'+product_width+'" title="'+product_width+'" class="form-control erp-form-control-sm pd_width" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][quantity]" data-id="quantity" data-url="" value="'+dtl_quantity+'" title="'+dtl_quantity+'" class="form-control erp-form-control-sm tblGridCal_qty tb_moveIndex validNumber validOnlyFloatNumber" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][foc_qty]" data-id="foc_qty" data-url="" value="'+foc_quantity+'" title="'+foc_quantity+'" class="form-control erp-form-control-sm tb_moveIndex validNumber tblGridCal_foc_qty" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][fc_rate]" data-id="fc_rate" data-url="" value="" title="" class="form-control erp-form-control-sm fc_rate tb_moveIndex validNumber" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][g_rate]" data-id="g_rate" data-url="" value="'+rate+'" title="'+rate+'" class="form-control erp-form-control-sm g_rate validNumber" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][rate]" data-id="rate" data-url="" value="'+rate+'" title="'+rate+'" class="form-control erp-form-control-sm tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][amount]" data-id="amount" data-url="" value="'+amount+'" title="'+amount+'" class="form-control erp-form-control-sm tblGridCal_amount validNumber" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][dis_perc]" data-id="dis_perc" data-url="" value="'+discount_per+'" title="" class="form-control erp-form-control-sm tblGridCal_discount_perc tb_moveIndex validNumber" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][dis_amount]" data-id="dis_amount" data-url="" value="'+discount_amount+'" title="" class="form-control erp-form-control-sm tblGridCal_discount_amount tb_moveIndex validNumber" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][vat_perc]" data-id="vat_perc" data-url="" value="'+vat_per+'" title="'+vat_per+'" class="form-control erp-form-control-sm tblGridCal_vat_perc tb_moveIndex validNumber" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][vat_amount]" data-id="vat_amount" data-url="" value="'+vat_amount+'" title="'+vat_amount+'" class="form-control erp-form-control-sm tblGridCal_vat_amount tb_moveIndex validNumber" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][gross_amount]" data-id="gross_amount" data-url="" value="'+gross_amount+'" title="'+gross_amount+'" class="form-control erp-form-control-sm tblGridCal_gross_amount validNumber" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][pd_notes]" data-id="pd_notes" value="'+product_notes+'" title="'+product_notes+'" class="form-control erp-form-control-sm" autocomplete="off">'+
                                            '</td>'+
                                            '<td class="text-center">'+
                                                '<div class="btn-group btn-group btn-group-sm" role="group">'+
                                                    '<button type="button" class="btn btn-danger gridBtn delData">'+
                                                        '<i class="la la-trash"></i>'+
                                                    '</button>'+
                                                '</div>'+
                                            '</td>'+
                                        '</tr>';
                                        iteration += 1;
                                    }
                                    $('.erp_form__grid_body').append(tr);
                                    allCalcFunc();
                                    $('input').attr('autocomplete', 'off');
                                    updateKeys();
                                    dataDelete();
                                    table_td_sortable();
                                    allGridTotal();
                                }
                            }
                        });
                    }
                });

            }else{
                toastr.error("Select first Qutation Code");
            }
        });

        $('#searchCustomer').on('click' , function(e){
            if($('#customer_name').val() == ''){
                toastr.error("Please Enter Customer Mobile");
            }else{
                var mobile = $('#customer_name').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '{{ route("get-customer-by-phone") }}',
                    method : 'POST',
                    data : {"mobile" : mobile},
                    async: false,
                    beforeSend : function(){
                        $('body').addClass('pointerEventsNone');
                    },
                    success : function(response,status){
                        $('body').removeClass('pointerEventsNone');
                        if(response.status == 'success'){
                            var customer = response.data;
                            toastr.success(response.message);
                            $('#customer_name').val(customer.customer_name);
                            $('#customer_id').val(customer.customer_id);
                            $('#sales_mobile_no').val(customer.customer_phone_1);

                            $('#customer-code-no').html('').html(customer.customer_code);

                            $('#sales_order_city_id').val(customer.city_id);
                            $('#sales_order_city_id').trigger('change');

                            $('#sales_order_area').val(customer.region_id);
                        }
                    },
                    error: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
                        $('#customer_id').val('');
                        $('#addCustomerModal').modal('toggle');
                        toastr.error(response.responseJSON.message);
                    },
                });
            }
        });

    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/add-expense-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/change-grid-item-rate.js') }}" type="text/javascript"></script>
    @if($case == 'new' && !empty($services_order_id))
        <script>
            // Load the Grid Data
            $(document).ready(function(){
                $('#getServicesOrderData').trigger('click');
            });
        </script>
    @endif
@endsection
