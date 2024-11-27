@extends('layouts.template')
@section('title', 'Configuration')

@section('pageCSS')
@endsection
<style>
    .section{
        height:160px;
        width:100%;
        background-repeat: no-repeat;
        background-color: #31aef5;
        margin-bottom: 20px;
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
   
</style>
@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){}
        if($case == 'edit'){
            
            $purchase_discount_val = $data['data'][0]->purchase_discount;
            $purchase_tax_val = $data['data'][0]->purchase_tax;
            $purchase_freight_val = $data['data'][0]->purchase_freight;
            $purchase_other_charges_val = $data['data'][0]->purchase_other_charges;
            $bank_group_val = $data['data'][0]->bank_group;
            $cash_group_val = $data['data'][0]->cash_group;
            $customer_account_val = $data['data'][0]->customer_account;
            $supplier_account_val = $data['data'][0]->supplier_account;
            $sale_discount_val = $data['data'][0]->sale_discount;
            $sale_tax_val = $data['data'][0]->sale_tax;
            $sale_freight_val = $data['data'][0]->sale_freight;
            $sale_other_charges_val = $data['data'][0]->sale_other_charges;
            $save_form_val = $data['data'][0]->saveBtn;
            $create_form_val = $data['data'][0]->createBtn;
            $back_form_val = $data['data'][0]->backBtn;
            $qty_decimal_val = $data['data'][0]->qty_decimal;
            $rate_decimal_val = $data['data'][0]->rate_decimal;
            $amount_decimal_val = $data['data'][0]->amount_decimal;
        }

        $purchase_discount_name = 'purchase_discount';
        $purchase_tax_name = 'purchase_tax';
        $purchase_freight_name = 'purchase_freight';
        $purchase_other_charges_name = 'purchase_other_charges';
        $bank_group_name = 'bank_group';
        $cash_group_name = 'cash_group';
        $customer_account_name = 'customer_account';
        $supplier_account_name = 'supplier_account';
        $sale_discount_name = 'sale_discount';
        $sale_tax_name = 'sale_tax';
        $sale_freight_name = 'sale_freight';
        $sale_other_charges_name = 'sale_other_charges';
        $save_form_name = 'saveBtn';
        $create_form_name = 'createBtn';
        $back_form_name = 'backBtn';
        $qty_decimal_name = 'qty_decimal';
        $rate_decimal_name = 'rate_decimal';
        $amount_decimal_name = 'amount_decimal';
    @endphp
    

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
                        <div class="kt-portlet__body" style="padding-top:10px;">
                            <ul style="padding-top:20px;" class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#purchase" role="tab">Purchase</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#accounts" role="tab">Accounts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#sale" role="tab">Sales</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#decimal" role="tab">No Decimals</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#shortkeys" role="tab">Short Keys</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="purchase" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Purchase Discount:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="purchase_discount[name]" value="{{$purchase_discount_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="purchase_discount[val]" value="{{isset($purchase_discount_val)?$purchase_discount_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Purchase Tax:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="purchase_tax[name]" value="{{$purchase_tax_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="purchase_tax[val]" value="{{isset($purchase_tax_val)?$purchase_tax_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Purchase Freight:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="purchase_freight[name]" value="{{$purchase_freight_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="purchase_freight[val]" value="{{isset($purchase_freight_val)?$purchase_freight_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Purchase Other Charges:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="purchase_other_charges[name]" value="{{$purchase_other_charges_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="purchase_other_charges[val]" value="{{isset($purchase_other_charges_val)?$purchase_other_charges_val:''}}" class="form-control erp-form-control-sm moveIndex">
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
                                                    <input type="hidden" name="bank_group[name]" value="{{$bank_group_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="bank_group[val]" value="{{isset($bank_group_val)?$bank_group_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Cash Group A/C:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="cash_group[name]" value="{{$cash_group_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="cash_group[val]" value="{{isset($cash_group_val)?$cash_group_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Customer A/C:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="customer_account[name]" value="{{$customer_account_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="customer_account[val]" value="{{isset($customer_account_val)?$customer_account_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Supplier A/C:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="supplier_account[name]" value="{{$supplier_account_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="supplier_account[val]" value="{{isset($supplier_account_val)?$supplier_account_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="sale" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Sale Discount:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="sale_discount[name]" value="{{$sale_discount_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="sale_discount[val]" value="{{isset($sale_discount_val)?$sale_discount_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Sale Tax:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="sale_tax[name]" value="{{$sale_tax_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="sale_tax[val]" value="{{isset($sale_tax_val)?$sale_tax_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group row">
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Sale Freight:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="sale_freight[name]" value="{{$sale_freight_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="sale_freight[val]" value="{{isset($sale_freight_val)?$sale_freight_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-5">
                                            <div class="row">
                                                <label class="col-lg-6 erp-col-form-label">Sale Other Charges:</label>
                                                <div class="col-lg-6">
                                                    <input type="hidden" name="sale_other_charges[name]" value="{{$sale_other_charges_name}}" class="form-control erp-form-control-sm moveIndex">
                                                    <input type="text" name="sale_other_charges[val]" value="{{isset($sale_other_charges_val)?$sale_other_charges_val:''}}" class="form-control erp-form-control-sm moveIndex">
                                                </div>
                                            </div>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="decimal" role="tabpanel">
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Qty Decimal:</label>
                                        <div class="col-lg-4">
                                            <input type="hidden" name="qty_decimal[name]" value="{{$qty_decimal_name}}" maxlength="100" class="form-control erp-form-control-sm">
                                            <input type="text" name="qty_decimal[val]" value="{{isset($qty_decimal_val)?$qty_decimal_val:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex decimal validNumber" autocomplete="off">
                                            <span class="required NoMsg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Rate Decimal:</label>
                                        <div class="col-lg-4">
                                            <input type="hidden" name="rate_decimal[name]" value="{{$rate_decimal_name}}" maxlength="100" class="form-control erp-form-control-sm">
                                            <input type="text" name="rate_decimal[val]" value="{{isset($rate_decimal_val)?$rate_decimal_val:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex decimal validNumber" autocomplete="off">
                                            <span class="required NoMsg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Value Decimal:</label>
                                        <div class="col-lg-4">
                                            <input type="hidden" name="amount_decimal[name]" value="{{$amount_decimal_name}}" maxlength="100" class="form-control erp-form-control-sm">
                                            <input type="text" name="amount_decimal[val]" value="{{isset($amount_decimal_val)?$amount_decimal_val:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex decimal validNumber" autocomplete="off">
                                            <span class="required NoMsg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                </div>
                                <div class="tab-pane" id="shortkeys" role="tabpanel">
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Form Save:</label>
                                        <div class="col-lg-4">
                                            <input type="hidden" name="save_form[name]" value="{{$save_form_name}}" maxlength="100" class="form-control erp-form-control-sm moveIndex">
                                            <input type="text" name="save_form[val]" value="{{isset($save_form_val)?$save_form_val:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex formbtn" autocomplete="off">
                                            <span class="required msg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Form Create:</label>
                                        <div class="col-lg-4">
                                            <input type="hidden" name="create_form[name]" value="{{$create_form_name}}" maxlength="100" class="form-control erp-form-control-sm moveIndex">
                                            <input type="text" name="create_form[val]" value="{{isset($create_form_val)?$create_form_val:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex formbtn" autocomplete="off">
                                            <span class="required msg" style="font-size:11px;"></span>
                                        </div>
                                    </div>{{-- end row--}}
                                    <div class="form-group-block row">
                                        <label class="col-lg-3 erp-col-form-label">Form Back:</label>
                                        <div class="col-lg-4">
                                            <input type="hidden" name="back_form[name]" value="{{$back_form_name}}" maxlength="100" class="form-control erp-form-control-sm moveIndex">
                                            <input type="text" name="back_form[val]" value="{{isset($back_form_val)?$back_form_val:''}}" maxlength="100" class="form-control erp-form-control-sm moveIndex formbtn" autocomplete="off">
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
        });
    </script>
@endsection

