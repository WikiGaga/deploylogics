@extends('layouts.layout')
@section('title', 'Configuration')

@section('pageCSS')
    <style>
        .section{
            height:160px;
            width:100%;
            background-repeat: no-repeat;
            background-color: #31aef5;
            margin-bottom: 5px;
        }
        .section-container{
            width: 30% !important;
            height:120px;
            color: #fff;
            margin-top: 11px;
            margin-left: 90px;
            float:left;
            padding: 10px;
        }
        .section-container>h1 {
            font-size: 17.55px;
            font-weight: 600 !important;
            padding-top: 20px;
        }
        .section-container>button {
            border: 1px solid #fff !important;
            border-radius: 4px;
            font-family: 'Lato', Helvetica, Arial, sans-serif !important;
            font-size: 12px !important;
            text-transform: capitalize !important;
            font-weight: bold !important;
            background-color: #fff !important;
            width: 20% !important;
            text-decoration: none;
            margin-top:15px;
            text-align:center;
            color: #31aef5 !important;
        }
        .section-container>button:hover {
            color: #1ea4ed !important;
        }
        .section-bg-cover {
            background-repeat: no-repeat;
            background-position:right;
            background-size:320px;
            z-index:5;
        }
        .branchInveAccounts th{
            font-size: 13px  !important;
            padding: 10px !important;
        }
        .branchInveAccounts .branch_name{
            font-size: 13px  !important;
        }
    </style>
@endsection
@section('content')

    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            $customer_group = $data['current']->customer_group;
            $sale_income = $data['current']->sale_income;
            $sale_discount = $data['current']->sale_discount;
            $sale_vat_payable = $data['current']->sale_vat_payable;
            $sale_stock = $data['current']->sale_stock;
            $sale_stock_consumption = $data['current']->sale_stock_consumption;
            $sale_cash_ac = $data['current']->sale_cash_ac;

            $sale_return_customer_group = $data['current']->sale_return_customer_group;
            $sale_return_income = $data['current']->sale_return_income;
            $sale_return_discount = $data['current']->sale_return_discount;
            $sale_return_vat_payable = $data['current']->sale_return_vat_payable;
            $sale_return_stock = $data['current']->sale_return_stock;
            $sale_return_stock_consumption = $data['current']->sale_return_stock_consumption;
            $sale_return_cash_ac = $data['current']->sale_return_cash_ac;

            $sale_fee_income = $data['current']->sale_fee_income;
            $sale_fee_discount = $data['current']->sale_fee_discount;
            $sale_fee_vat_payable = $data['current']->sale_fee_vat_payable;
            $sale_fee_stock = $data['current']->sale_fee_stock;
            $sale_fee_stock_consumption = $data['current']->sale_fee_stock_consumption;
            $sale_fee_cash_ac = $data['current']->sale_fee_cash_ac;

            $display_rent_fee_income = $data['current']->display_rent_fee_income;
            $display_rent_fee_discount = $data['current']->display_rent_fee_discount;
            $display_rent_fee_vat_payable = $data['current']->display_rent_fee_vat_payable;
            $display_rent_fee_stock = $data['current']->display_rent_fee_stock;
            $display_rent_fee_stock_consumption = $data['current']->display_rent_fee_stock_consumption;
            $display_rent_fee_cash_ac = $data['current']->display_rent_fee_cash_ac;

            $rebate_invoice_income = $data['current']->rebate_invoice_income;
            $rebate_invoice_discount = $data['current']->rebate_invoice_discount;
            $rebate_invoice_vat_payable = $data['current']->rebate_invoice_vat_payable;
            $rebate_invoice_stock = $data['current']->rebate_invoice_stock;
            $rebate_invoice_stock_consumption = $data['current']->rebate_invoice_stock_consumption;
            $rebate_invoice_cash_ac = $data['current']->rebate_invoice_cash_ac;

            $supplier_group = $data['current']->supplier_group;
            $purchase_stock = $data['current']->purchase_stock;
            $purchase_discount = $data['current']->purchase_discount;
            $purchase_vat = $data['current']->purchase_vat;

            $bank_group = $data['current']->bank_group;
            $cash_group = $data['current']->cash_group;
            $payment_receive_dr_ac = $data['current']->payment_receive_dr_ac;
            $payment_receive_cr_ac = $data['current']->payment_receive_cr_ac;
            $general_cash_ac = $data['current']->general_cash_ac;
            $excess_cash_ac = $data['current']->excess_cash_ac;
            $bank_distribution_cr_ac = $data['current']->bank_distribution_cr_ac;

            $qty_decimal = $data['short_keys']->shortcut_keys_form_qty_decimal;
            $rate_decimal = $data['short_keys']->shortcut_keys_form_rate_decimal;
            $amount_decimal = $data['short_keys']->shortcut_keys_form_amount_decimal;

            $qty_decimal = $data['short_keys']->shortcut_keys_form_qty_decimal;
            $rate_decimal = $data['short_keys']->shortcut_keys_form_rate_decimal;
            $amount_decimal = $data['short_keys']->shortcut_keys_form_amount_decimal;

            $form_save = $data['short_keys']->shortcut_keys_form_save;
            $form_create = $data['short_keys']->shortcut_keys_form_create;
            $form_back= $data['short_keys']->shortcut_keys_form_back;
        }


        $bank_group_name = 'bank_group';
        $cash_group_name = 'cash_group';
        $customer_account_name = 'customer_account';
        $supplier_account_name = 'supplier_account';
        $save_form_name = 'saveBtn';
        $create_form_name = 'createBtn';
        $back_form_name = 'backBtn';
        $qty_decimal_name = 'qty_decimal';
        $rate_decimal_name = 'rate_decimal';
        $amount_decimal_name = 'amount_decimal';
    @endphp

    @permission($data['permission'])
            <!--begin::Form-->
    <form id="configuration_form" class="master_form kt-form" method="post" action="{{ action('Setting\ConfigurationController@store', isset($id)?$id:'') }}">
        @csrf
        <div class="col-lg-12">
            <div class="content-header">
                <div id="block-section-header-reference">
                    <div class="section">
                        <section class="section section--pad-top-small section--pad-bottom-small hide-background-on-mobile section-bg-cover" style="background-image: url(/assets/media/custom/config_header.png);">
                            <div class="section-container">
                                <h1>
                                    {{$data['page_data']['title']}}
                                </h1>
                                <button type="submit" class="btn btn-danger font-weight-bold py-2 px-6">Save</button>
                            </div>
                        </section>
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="kt-portlet">
                        <div class="kt-portlet__body" >
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#sale" role="tab">Sales</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#sale_return" role="tab">Sales Return</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#sale_fee" role="tab">Sales Fee</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#display_rent_fee" role="tab">Display Rent Fee</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#rebate_invoice" role="tab">Rebate Invoice</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#purchase" role="tab">Purchase</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#accounts" role="tab">Accounts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#decimal" role="tab">Decimals</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#shortkeys" role="tab">Short Keys</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="sale" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Customer Group A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="customer_group" name="customer_group" autofocus>
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L2'] as $chart)
                                                            @php $customer_group = isset($customer_group)?$customer_group:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$customer_group?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Income A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_income" name="sale_income">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $sale_income = isset($sale_income)?$sale_income:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_income?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Discount A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_discount" name="sale_discount">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $sale_discount = isset($sale_discount)?$sale_discount:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_discount?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Vat Payable A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_vat_payable" name="sale_vat_payable">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $sale_vat_payable = isset($sale_vat_payable)?$sale_vat_payable:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_vat_payable?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_stock" name="sale_stock">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $sale_stock = isset($sale_stock)?$sale_stock:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_stock?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock Consumption A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_stock_consumption" name="sale_stock_consumption">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $sale_stock_consumption = isset($sale_stock_consumption)?$sale_stock_consumption:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_stock_consumption?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Cash A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_cash_ac" name="sale_cash_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $sale_cash_ac = isset($sale_cash_ac)?$sale_cash_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_cash_ac?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="sale_return" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Customer Group A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_return_customer_group" name="sale_return_customer_group" autofocus>
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L2'] as $chart)
                                                                @php $sale_return_customer_group = isset($sale_return_customer_group)?$sale_return_customer_group:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_return_customer_group?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Income A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_return_income" name="sale_return_income">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_return_income = isset($sale_return_income)?$sale_return_income:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_return_income?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Discount A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_return_discount" name="sale_return_discount">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_return_discount = isset($sale_return_discount)?$sale_return_discount:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_return_discount?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Vat Payable A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_return_vat_payable" name="sale_return_vat_payable">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_return_vat_payable = isset($sale_return_vat_payable)?$sale_return_vat_payable:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_return_vat_payable?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_return_stock" name="sale_return_stock">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_return_stock = isset($sale_return_stock)?$sale_return_stock:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_return_stock?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock Consumption A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_return_stock_consumption" name="sale_return_stock_consumption">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_return_stock_consumption = isset($sale_return_stock_consumption)?$sale_return_stock_consumption:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_return_stock_consumption?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Cash A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_return_cash_ac" name="sale_return_cash_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_return_cash_ac = isset($sale_return_cash_ac)?$sale_return_cash_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_return_cash_ac?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="sale_fee" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Cash A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_fee_cash_ac" name="sale_fee_cash_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_fee_cash_ac = isset($sale_fee_cash_ac)?$sale_fee_cash_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_fee_cash_ac?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Income A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_fee_income" name="sale_fee_income">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_fee_income = isset($sale_fee_income)?$sale_fee_income:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_fee_income?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Discount A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_fee_discount" name="sale_fee_discount">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_fee_discount = isset($sale_fee_discount)?$sale_fee_discount:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_fee_discount?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Vat Payable A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_fee_vat_payable" name="sale_fee_vat_payable">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_fee_vat_payable = isset($sale_fee_vat_payable)?$sale_fee_vat_payable:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_fee_vat_payable?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_fee_stock" name="sale_fee_stock">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_fee_stock = isset($sale_fee_stock)?$sale_fee_stock:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_fee_stock?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock Consumption A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="sale_fee_stock_consumption" name="sale_fee_stock_consumption">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $sale_fee_stock_consumption = isset($sale_fee_stock_consumption)?$sale_fee_stock_consumption:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$sale_fee_stock_consumption?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="display_rent_fee" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Cash A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="display_rent_fee_cash_ac" name="display_rent_fee_cash_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $display_rent_fee_cash_ac = isset($display_rent_fee_cash_ac)?$display_rent_fee_cash_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$display_rent_fee_cash_ac?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Income A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="display_rent_fee_income" name="display_rent_fee_income">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $display_rent_fee_income = isset($display_rent_fee_income)?$display_rent_fee_income:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$display_rent_fee_income?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Discount A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="display_rent_fee_discount" name="display_rent_fee_discount">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $display_rent_fee_discount = isset($display_rent_fee_discount)?$display_rent_fee_discount:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$display_rent_fee_discount?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Vat Payable A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="display_rent_fee_vat_payable" name="display_rent_fee_vat_payable">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $display_rent_fee_vat_payable = isset($display_rent_fee_vat_payable)?$display_rent_fee_vat_payable:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$display_rent_fee_vat_payable?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="display_rent_fee_stock" name="display_rent_fee_stock">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $display_rent_fee_stock = isset($display_rent_fee_stock)?$display_rent_fee_stock:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$display_rent_fee_stock?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock Consumption A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="display_rent_fee_stock_consumption" name="display_rent_fee_stock_consumption">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $display_rent_fee_stock_consumption = isset($display_rent_fee_stock_consumption)?$display_rent_fee_stock_consumption:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$display_rent_fee_stock_consumption?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="rebate_invoice" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Cash A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rebate_invoice_cash_ac" name="rebate_invoice_cash_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $rebate_invoice_cash_ac = isset($rebate_invoice_cash_ac)?$rebate_invoice_cash_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$rebate_invoice_cash_ac?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Income A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rebate_invoice_income" name="rebate_invoice_income">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $rebate_invoice_income = isset($rebate_invoice_income)?$rebate_invoice_income:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$rebate_invoice_income?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Discount A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rebate_invoice_discount" name="rebate_invoice_discount">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $rebate_invoice_discount = isset($rebate_invoice_discount)?$rebate_invoice_discount:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$rebate_invoice_discount?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Vat Payable A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rebate_invoice_vat_payable" name="rebate_invoice_vat_payable">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $rebate_invoice_vat_payable = isset($rebate_invoice_vat_payable)?$rebate_invoice_vat_payable:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$rebate_invoice_vat_payable?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rebate_invoice_stock" name="rebate_invoice_stock">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $rebate_invoice_stock = isset($rebate_invoice_stock)?$rebate_invoice_stock:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$rebate_invoice_stock?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock Consumption A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rebate_invoice_stock_consumption" name="rebate_invoice_stock_consumption">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $rebate_invoice_stock_consumption = isset($rebate_invoice_stock_consumption)?$rebate_invoice_stock_consumption:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$rebate_invoice_stock_consumption?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="purchase" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Supplier Group A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="supplier_group" name="supplier_group">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L2'] as $chart)
                                                            @php $supplier_group = isset($supplier_group)?$supplier_group:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$supplier_group?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Stock A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="purchase_stock" name="purchase_stock">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $purchase_stock = isset($purchase_stock)?$purchase_stock:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$purchase_stock?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Discount A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="purchase_discount" name="purchase_discount">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $purchase_discount = isset($purchase_discount)?$purchase_discount:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$purchase_discount?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-5 erp-col-form-label">Purchase Vat A/C:</label>
                                                <div class="col-lg-7">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="purchase_vat" name="purchase_vat">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                            @php $purchase_vat = isset($purchase_vat)?$purchase_vat:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$purchase_vat?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="accounts" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Bank Group A/C:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="bank_group" name="bank_group">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L3'] as $chart)
                                                            @php $bank_group = isset($bank_group)?$bank_group:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$bank_group?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Cash Group A/C:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="cash_group" name="cash_group">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L3'] as $chart)
                                                            @php $cash_group = isset($cash_group)?$cash_group:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$cash_group?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Payment Receive Dr. A/C:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="payment_receive_dr_ac" name="payment_receive_dr_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $payment_receive_dr_ac = isset($payment_receive_dr_ac)?$payment_receive_dr_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id == $payment_receive_dr_ac ?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Payment Receive Cr. A/C:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="payment_receive_cr_ac" name="payment_receive_cr_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $payment_receive_cr_ac = isset($payment_receive_cr_ac)?$payment_receive_cr_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$payment_receive_cr_ac?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Debit A/C:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="general_cash_ac" name="general_cash_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $general_cash_ac = isset($general_cash_ac)?$general_cash_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id == $general_cash_ac ?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Credit A/C:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="excess_cash_ac" name="excess_cash_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $excess_cash_ac = isset($excess_cash_ac)?$excess_cash_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id ==$excess_cash_ac?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Bank Distribution Credit A/C:</label>
                                                <div class="col-lg-6">
                                                    <div class="erp-select2">
                                                        <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="bank_distribution_cr_ac" name="bank_distribution_cr_ac">
                                                            <option value="0">Select</option>
                                                            @foreach($data['Chart_L4'] as $chart)
                                                                @php $bank_distribution_cr_ac = isset($bank_distribution_cr_ac)?$bank_distribution_cr_ac:'' @endphp
                                                                <option value="{{$chart->chart_account_id}}" {{ $chart->chart_account_id == $bank_distribution_cr_ac ?'selected':'' }}>{{$chart->chart_code}}-{{$chart->chart_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5"></div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <table style="table-layout:fixed;" class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed branchInveAccounts">
                                                <thead>
                                                    <tr>
                                                        <th width="10%">Branch</th>
                                                        <th width="11%" class="text-center">Stock Transfer Income A/C</th>
                                                        <th width="11%" class="text-center">Stock Transfer Stock A/C</th>
                                                        <th width="11%" class="text-center">Stock Transfer Branch A/C</th>
                                                        <th width="10%" class="text-center">Stock Transfer Cash A/C</th>
                                                        <th width="10%" class="text-center">Stock Transfer Vat A/C</th>
                                                        <th width="10%" class="text-center">Stock Transfer Disc A/C</th>

                                                        <th width="10%" class="text-center">Store Receive Stock A/C</th>
                                                        <th width="10%" class="text-center">Stock Receive Cash A/C</th>
                                                        <th width="10%" class="text-center">Stock Receive Branch A/C</th>
                                                        <th width="10%" class="text-center">Stock Receive Vat A/C</th>
                                                        <th width="10%" class="text-center">Stock Receive Disc A/C</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for($i=0;count($data['branches'])>$i; $i++)
                                                        @php
                                                                $stock_transfer = "";
                                                                $stock_receive = "";
                                                                $branch_id = $data['branches'][$i]['branch_id'];
                                                                if(isset($data['branch_wise_acc'])){
                                                                    foreach($data['branch_wise_acc'] as $branch_wise_acc){
                                                                        if($branch_wise_acc->acc_branch_id == $branch_id){
                                                                            $stock_transfer_income = $branch_wise_acc->stock_transfer_income;
                                                                            $stock_transfer_stock = $branch_wise_acc->stock_transfer_stock;
                                                                            $stock_transfer_branch = $branch_wise_acc->stock_transfer_branch;
                                                                            $stock_transfer_income = $branch_wise_acc->stock_transfer_income;
                                                                            $stock_transfer_cash = $branch_wise_acc->stock_transfer_cash;
                                                                            $stock_transfer_vat = $branch_wise_acc->stock_transfer_vat;
                                                                            $stock_transfer_discount = $branch_wise_acc->stock_transfer_discount;
                                                                            $store_receive_stock = $branch_wise_acc->store_receive_stock;
                                                                            $stock_receive_cash = $branch_wise_acc->stock_receive_cash;
                                                                            $stock_receive_branch = $branch_wise_acc->stock_receive_branch;
                                                                            $stock_receive_vat = $branch_wise_acc->stock_receive_vat;
                                                                            $stock_receive_discount = $branch_wise_acc->stock_receive_discount;
                                                                            break;
                                                                        }
                                                                    }
                                                                }

                                                        @endphp
                                                        <tr>
                                                            <td><span class="branch_name">{{$data['branches'][$i]['branch_name']}}</span><input name="acc[{{$i}}][branch_id]" type="hidden" value="{{$branch_id}}" ></td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_transfer_income]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_transfer_income = isset($stock_transfer_income)?$stock_transfer_income :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_transfer_income == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_transfer_stock]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_transfer_stock = isset($stock_transfer_stock)?$stock_transfer_stock :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_transfer_stock == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_transfer_branch]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_transfer_branch = isset($stock_transfer_branch)?$stock_transfer_branch :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_transfer_branch == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_transfer_cash]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_transfer_cash = isset($stock_transfer_cash)?$stock_transfer_cash :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_transfer_cash == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_transfer_vat]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_transfer_vat = isset($stock_transfer_vat)?$stock_transfer_vat :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_transfer_vat == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_transfer_discount]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_transfer_discount = isset($stock_transfer_discount)?$stock_transfer_discount :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_transfer_discount == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][store_receive_stock]">
                                                                        <option value="0">Select</option>
                                                                        @php $store_receive_stock = isset($store_receive_stock)?$store_receive_stock :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($store_receive_stock == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_receive_cash]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_receive_cash = isset($stock_receive_cash)?$stock_receive_cash :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_receive_cash == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_receive_branch]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_receive_branch = isset($stock_receive_branch)?$stock_receive_branch :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_receive_branch == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_receive_vat]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_receive_vat = isset($stock_receive_vat)?$stock_receive_vat :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_receive_vat == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="erp-select2">
                                                                    <select class="form-control erp-form-control-sm kt-acc-select2 moveIndex" name="acc[{{$i}}][stock_receive_discount]">
                                                                        <option value="0">Select</option>
                                                                        @php $stock_receive_discount = isset($stock_receive_discount)?$stock_receive_discount :''; @endphp
                                                                        @foreach($data['Chart_L4'] as $chart)
                                                                            <option value="{{$chart->chart_account_id}}" {{($stock_receive_discount == $chart->chart_account_id)?"selected":""}}>{{$chart->chart_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="decimal" role="tabpanel">
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Qty Decimal:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="qty_decimal" value="{{isset($qty_decimal)?$qty_decimal:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex decimal validNumber" autocomplete="off">
                                            <span class="required NoMsg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Rate Decimal:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="rate_decimal" value="{{isset($rate_decimal)?$rate_decimal:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex decimal validNumber" autocomplete="off">
                                            <span class="required NoMsg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Amount Decimal:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="amount_decimal" value="{{isset($amount_decimal)?$amount_decimal:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex decimal validNumber" autocomplete="off">
                                            <span class="required NoMsg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="shortkeys" role="tabpanel">
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Form Save:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="form_save" value="{{isset($form_save)?$form_save:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex formbtn" autocomplete="off">
                                            <span class="required msg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Form Create:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="form_create" value="{{isset($form_create)?$form_create:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex formbtn" autocomplete="off">
                                            <span class="required msg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Form Back:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="form_back" value="{{isset($form_back)?$form_back:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex formbtn" autocomplete="off">
                                            <span class="required msg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
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
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/master-form.js') }}" type="text/javascript"></script>
    <script>

        function formkesys(){
            var res='';
            var that = '';
            var arr = ['alt+z','alt+x','alt+m','alt+d','alt+f','alt+a'];
            var mesg = 'Press one of them alt+z, alt+x, alt+m, alt+d, alt+f, alt+a';
            $(".formbtn").keydown(function(e){
                that = $(this);
                if(e.altKey && e.keyCode == 90){
                    res = 'alt+z';
                }
                if(e.altKey && e.keyCode == 88){
                    res = 'alt+x';
                }
                if(e.altKey && e.keyCode == 68){
                    res = 'alt+d';
                }
                if(e.altKey && e.keyCode == 77){
                    res = 'alt+m';
                }
                if(e.altKey && e.keyCode == 70){
                    res = 'alt+f';
                }
                if(e.altKey && e.keyCode == 65){
                    res = 'alt+a';
                }
                if(e.keyCode == 8){
                    res = '';
                }

                that.val(res);

                if(jQuery.inArray(res, arr) <= 0){
                    that.closest('div').find('.msg').html(mesg)
                }else{
                    that.closest('div').find('.msg').html('')
                }
            });
        }
        function validateinput(event) {
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 18 || (event.keyCode  >=65 && event.keyCode  <=88)) {
                return true;
            }else {
                return false;
            }
        }

        function decimals(){
            $(".decimal").keyup(function(){
                var inputval = $(this).val();
                if(parseFloat(inputval) > 5){
                    $(this).closest('div').find('.NoMsg').html('The value should not be greater than 5');
                }else{
                    $(this).closest('div').find('.NoMsg').html('');
                }
            });
        }

        $(document).ready(function(){
            formkesys();
            decimals();
            $('.formbtn').keypress(validateinput);
            $('.kt-acc-select2').select2();
        });
    </script>
@endsection

