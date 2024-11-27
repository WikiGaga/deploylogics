@extends('layouts.template')
@section('title', 'Purchase Order')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
            $date =  date('d-m-Y');
        }
        if($case == 'edit'){
            $id = $data['current']->purchase_order_id;
            $code = $data['current']->purchase_order_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->purchase_order_entry_date))));
            $lpo_id = isset($data['current']->lpo)?$data['current']->lpo->lpo_id:"";
            $lpo_code = isset($data['current']->lpo)?$data['current']->lpo->lpo_code:"";
            $comparative_quotation_id = isset($data['current']->comparative_quotation)?$data['current']->comparative_quotation->comparative_quotation_id:"";
            $comparative_quotation_code = isset($data['current']->comparative_quotation)?$data['current']->comparative_quotation->comparative_quotation_code:"";
            $supplier_id = isset($data['current']->supplier)?$data['current']->supplier->supplier_id:"";;
            $supplier_code = isset($data['current']->supplier)?$data['current']->supplier->supplier_name:"";;
            $payment_terms = $data['current']->payment_mode_id;
            $credit_days = $data['current']->purchase_order_credit_days;
            $currency_id = $data['current']->currency_id;
            $exchange_rate = $data['current']->purchase_order_exchange_rate;
            $po_details = $data['current']->po_details;
            $remarks = $data['current']->purchase_order_remarks;
        }
    @endphp
    <form id="purchase_order_form" class="kt-form" method="post" action="{{ action('Purchase\PurchaseOrderController@store',isset($id)?$id:"") }}">
    @csrf
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
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
                                <label class="col-lg-6 erp-col-form-label">Document Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="po_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data-table">
                                                 <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{isset($lpo_code)?$lpo_code:""}}" id="lpo_generation_no" name="lpo_generation_no" data-url="{{action('Common\DataTableController@helpOpen','lpoPoHelp')}}" class="form-control erp-form-control-sm open_modal moveIndex OnlyEnterAllow" placeholder="Enter here">
                                        <input type="hidden" value="{{isset($lpo_id)?$lpo_id:""}}" name="lpo_generation_no_id" id="lpo_generation_no_id" readonly>
                                        <div class="input-group-append">
                                                <span class="input-group-text btn-open-modal">
                                                   <i class="la la-search"></i>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Supplier:</label>
                                <div class="col-lg-6">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                     <i class="la la-minus-circle"></i>
                                                </span>
                                        </div>
                                        <input type="text" value="{{isset($supplier_code)?$supplier_code:""}}" id="supplier_name" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" name="supplier_name" class="form-control erp-form-control-sm open_modal moveIndex OnlyEnterAllow" placeholder="Enter here">
                                        <input type="hidden" value="{{isset($supplier_id)?$supplier_id:""}}" id="supplier_id" name="supplier_id">
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
                                <label class="col-lg-6 erp-col-form-label">Payment Terms:</label>
                                <div class="col-lg-6">
                                    <div class="input-group erp-select2-sm">
                                        <select name="payment_terms"  id="payment_terms" class="moveIndex kt-select2 width form-control erp-form-control-sm">
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
                                <label class="col-lg-6 erp-col-form-label">Exchange Rate:</label>
                                <div class="col-lg-6">
                                    <input type="text" value="{{isset($exchange_rate)?$exchange_rate:""}}" id="exchange_rate" name="exchange_rate" class="moveIndex validNumber form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Comparative Quotation:</label>
                                <div class="col-lg-6">
                                    <div class="input-group open-modal-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text btn-minus-selected-data-sec-table">
                                                 <i class="la la-minus-circle"></i>
                                            </span>
                                        </div>
                                        <input type="text" value="{{isset($comparative_quotation_code)?$comparative_quotation_code:""}}" name="comparative_quotation_code" id="comparative_quotation_code" data-url="{{action('Common\DataTableController@helpOpen','comparativeQuotationHelp')}}" class="form-control erp-form-control-sm open_modal moveIndex moveIndex2 OnlyEnterAllow" placeholder="Enter here">
                                        <input type="hidden" value="{{isset($comparative_quotation_id)?$comparative_quotation_id:""}}" name="comparative_quotation_id" id="comparative_quotation_id" readonly>
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

                    <div class="form-group-block" style="overflow: auto;">
                        <table id="quotaForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                            <thead>
                            <tr>
                                <th width="3%">Sr No</th>
                                <th width="7%">Barcode code</th>
                                <th width="10%">Product Name</th>
                                <th width="4%">UOM</th>
                                <th width="2%">Packing</th>
                                <th width="3%">Qty</th>
                                <th width="4%">FOC Qty</th>
                                <th width="5%">FC Rate</th>
                                <th width="3%">Rate</th>
                                <th width="4%">Amount</th>
                                <th width="4%">Disc %</th>
                                <th width="4%">Disc Amt</th>
                                <th width="4%">VAT%</th>
                                <th width="4%">Vat Amt</th>
                                <th width="5%">Gross Amt</th>
                                <th width="1%">Action</th>
                            </tr>
                            <tr id="dataEntryForm">
                                <td><input readonly id="sr_no" type="text" class="form-control erp-form-control-sm">
                                    <input readonly type="hidden" id="product_id" class="product_id form-control erp-form-control-sm">
                                    <input type="hidden" id="product_barcode_id" class="product_barcode_id form-control erp-form-control-sm">
                                    <input readonly type="hidden" id="uom_id" class="uom_id form-control erp-form-control-sm">
                                </td>
                                <td><input id="barcode" type="text" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="open-inline-help pd_barcode moveIndex2 form-control erp-form-control-sm" autocomplete="off"></td>
                                <td><input readonly id="product_name" type="text" class="pd_product_name form-control erp-form-control-sm"></td>
                                <td>
                                    <select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" id="uom">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td><input readonly id="packing" type="text" class="pd_packing form-control erp-form-control-sm"></td>
                                <td><input id="quantity" type="text" class="moveIndex tblGridCal_qty form-control erp-form-control-sm validNumber"></td>
                                <td><input id="foc_qty" type="text" class="moveIndex foc_qty form-control erp-form-control-sm validNumber"></td>
                                <td><input id="fc_rate" type="text" class="moveIndex fc_rate form-control erp-form-control-sm validNumber"></td>
                                <td><input id="rate" type="text" class="moveIndex tblGridCal_rate form-control erp-form-control-sm validNumber"></td>
                                <td><input readonly id="amount" type="text" class="tblGridCal_amount form-control erp-form-control-sm validNumber"></td>
                                <td><input id="discount" type="text" class="moveIndex tblGridCal_discount form-control erp-form-control-sm validNumber"></td>
                                <td><input id="discount_val" type="text" class="moveIndex tblGridCal_discount_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                <td><input id="vat_perc" type="text" class="moveIndex tblGridCal_vat_perc form-control erp-form-control-sm validNumber"></td>
                                <td><input id="vat_val" type="text" class="moveIndex tblGridCal_vat_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                <td><input readonly id="gross_amount" type="text" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber"></td>
                                <td class="text-center">
                                    <button type="button" id="addData" class="moveIndexBtn moveIndex gridBtn btn btn-primary btn-sm">
                                        <i class="la la-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            </thead>
                            <tbody id="repeated_data">
                                @if(isset($po_details))
                                    @foreach($po_details as $pd)
                                        <tr>
                                            <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]" title="{{$loop->iteration}}" class="form-control erp-form-control-sm handle" readonly aria-invalid="false">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][purchase_order_dtl_id]" data-id="purchase_order_dtl_id" value="{{$pd->purchase_order_dtl_id}}" class="purchase_order_dtl_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][lpo_id]" data-id="lpo_id" value="{{$pd->lpo_id}}" class="lpo_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][comparative_quotation_id]" data-id="comparative_quotation_id" value="{{$pd->comparative_quotation_id}}" class="comparative_quotation_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$pd->product_id}}" class="product_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$pd->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$pd->uom_id}}" class="uom_id form-control erp-form-control-sm" readonly>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][barcode]" data-id="barcode" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" value="{{$pd->barcode->product_barcode_barcode}}" title="{{$pd->barcode->product_barcode_barcode}}" class="form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][product_name]" data-id="product_name" value="{{isset($pd->product->product_name)?$pd->product->product_name:""}}" title="{{isset($pd->product->product_name)?$pd->product->product_name:""}}" class="form-control erp-form-control-sm pd_product_name" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][uom]" data-id="uom" title="{{isset($pd->uom->uom_name)?$pd->uom->uom_name:""}}">
                                                    <option value="{{isset($pd->uom->uom_id)?$pd->uom->uom_id:""}}">{{isset($pd->uom->uom_name)?$pd->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][packing]" data-id="packing" value="{{isset($pd->purchase_order_dtlpacking)?$pd->purchase_order_dtlpacking:""}}" title="{{isset($pd->purchase_order_dtlpacking)?$pd->purchase_order_dtlpacking:""}}" class="form-control erp-form-control-sm pd_packing" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity" value="{{number_format($pd->purchase_order_dtlquantity,0)}}" title="{{$pd->purchase_order_dtlquantity}}" class="form-control erp-form-control-sm tblGridCal_qty moveIndex validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty" value="{{number_format($pd->purchase_order_dtlfoc_quantity,0)}}" title="{{$pd->purchase_order_dtlfoc_quantity}}" class="foc_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" data-id="fc_rate" value="{{number_format($pd->purchase_order_dtlfc_rate,2)}}" title="{{$pd->purchase_order_dtlfc_rate}}" class="fc_rate moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate" value="{{number_format($pd->purchase_order_dtlrate,2)}}" title="{{$pd->purchase_order_dtlrate}}" class="form-control erp-form-control-sm tblGridCal_rate moveIndex validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount" value="{{number_format($pd->purchase_order_dtlamount,3)}}" title="{{$pd->purchase_order_dtlamount}}" class="form-control erp-form-control-sm tblGridCal_amount validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][discount]" data-id="discount" value="{{number_format($pd->purchase_order_dtldisc_percent,2)}}" title="{{$pd->purchase_order_dtldisc_percent}}" class="form-control erp-form-control-sm tblGridCal_discount moveIndex validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][discount_val]" data-id="discount_val" value="{{number_format($pd->purchase_order_dtldisc_amount,3)}}" title="{{$pd->purchase_order_dtldisc_amount}}" class="form-control erp-form-control-sm tblGridCal_discount_amount validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc" value="{{number_format($pd->purchase_order_dtlvat_percent,2)}}" title="{{$pd->purchase_order_dtlvat_percent}}" class="form-control erp-form-control-sm tblGridCal_vat_perc moveIndex validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_val]" data-id="vat_val" value="{{number_format($pd->purchase_order_dtlvat_amount,3)}}" title="{{$pd->purchase_order_dtlvat_amount}}" class="form-control erp-form-control-sm tblGridCal_vat_amount validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount" value="{{number_format($pd->purchase_order_dtltotal_amount,3)}}" title="{{$pd->purchase_order_dtltotal_amount}}" class="form-control erp-form-control-sm tblGridCal_gross_amount validNumber" readonly></td>
                                            <td class="text-center">
                                                @if(isset($pd->purchase_order_dtlbarcode))
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
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
                    </div>
                    <div class="row form-group-block">
                        <label class="col-lg-2 erp-col-form-label">Remarks:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" id="po_notes" name="po_notes" maxlength="255" class="moveIndex form-control erp-form-control-sm">{{isset($remarks)?$remarks:''}}</textarea>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
        </div>
    </div>
    </form>
    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase-order.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script>
        function selectComparativeQuotation(){
            $('#help_datatable_comparativeQuotationHelp').on('click', 'tbody>tr', function (e) {
                $('#repeated_data>tr').each(function(){
                    $(this).find('td:eq(0)>input[data-id="comparative_quotation_id"]').parents('tr').remove();
                })
                dataDeleteInit();
                var code = $(this).find('td[data-field="comparative_quotation_code"]').text();
                var id = $(this).find('td[data-field="comparative_quotation_id"]').text();
                //var supplier_id = $(this).find('td[data-field="supplier_id"]').text();
                $('form').find('#comparative_quotation_code').val(code);
                $('form').find('#comparative_quotation_id').val(id);
                //console.log("demand_approval_dtl_id: "+ demand_approval_dtl_id);
                url = '/purchase-order/quotation/'+id;
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data:{_token: CSRF_TOKEN},
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    success: function(response, status){
                        console.log(response.data['all']);
                        if(response.status == 'success') {
                            var tr = '';
                            var total_length = $('#repeated_data>tr').length;
                            for(var p=0; p < response.data['all'].length; p++ ){
                                total_length++;
                                var  row = response.data['all'][p];
                                console.log("rate: " + parseFloat(row['comparative_quotation_dtl_rate']).toFixed(2));
                                tr +='<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd['+total_length+'][sr_no]" value="'+total_length+'" title="'+total_length+'" class="form-control sr_no erp-form-control-sm handle" readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][comparative_quotation_id]" data-id="comparative_quotation_id" value="'+notNull(row['comparative_quotation_id'])+'" class="comparative_quotation_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][comparative_quotation_dtl_id]" data-id="comparative_quotation_dtl_id" value="'+notNull(row['comparative_quotation_dtl_id'])+'" class="comparative_quotation_dtl_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][supplier_id]" data-id="supplier_id" value="'+notNull(row['supplier_id'])+'" class="supplier_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+notNull(row['prod_id'])+'" class="product_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+notNull(row['uom_id'])+'"class="uom_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+notNull(row['product_barcode_id'])+'" class="product_barcode_id form-control erp-form-control-sm " readonly>'+
                                    '</td>'+
                                    '<td><input type="text" name="pd['+total_length+'][barcode]" data-id="barcode" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" value="'+notNull(row['comparative_quotation_dtl_barcode'])+'" title="'+notNull(row['comparative_quotation_dtl_barcode'])+'" class="form-control erp-form-control-sm" field_readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" value="'+notNull(row['product']['product_name'])+'" title="'+notNull(row['product']['product_name'])+'" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td>'+
                                        '<select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" name="pd['+total_length+'][uom]" data-id="uom" title="'+notNull(row['uom']['uom_name'])+'">'+
                                            '<option value="'+notNull(row['uom']['uom_id'])+'">'+notNull(row['uom']['uom_name'])+'</option>'+
                                        '</select>'+
                                    '</td>' +
                                    '<td><input type="text" name="pd['+total_length+'][packing]" data-id="packing" value="'+notNull(row['packing']['packing_name'])+'" title="'+notNull(row['packing']['packing_name'])+'" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][quantity]" data-id="quantity" value="'+notNull(row['comparative_quotation_dtl_quantity'])+'" title="'+notNull(row['comparative_quotation_dtl_quantity'])+'" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][foc_qty]" data-id="foc_qty" value="'+notNull(row['comparative_quotation_dtl_foc_quantity'])+'" title="'+notNull(row['comparative_quotation_dtl_foc_quantity'])+'" class="foc_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][fc_rate]" data-id="fc_rate" value="'+notNull(row['comparative_quotation_dtl_fc_rate'])+'" title="'+notNull(row['comparative_quotation_dtl_fc_rate'])+'" class="fc-rate moveIndex form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][rate]" data-id="rate" value="'+notNullEmpty(row['comparative_quotation_dtl_rate'],twoDecimal)+'" title="'+notNullEmpty(row['comparative_quotation_dtl_rate'],twoDecimal)+'" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][amount]" data-id="amount" value="'+notNullEmpty(row['comparative_quotation_dtl_amount'],threeDecimal)+'" title="'+notNullEmpty(row['comparative_quotation_dtl_amount'],threeDecimal)+'" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount]" data-id="discount" value="'+notNullEmpty(row['comparative_quotation_dtl_disc_percent'],twoDecimal)+'" title="'+notNullEmpty(row['comparative_quotation_dtl_disc_percent'],twoDecimal)+'" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount_val]" data-id="discount_val" value="'+notNullEmpty(row['comparative_quotation_dtl_disc_amount'],threeDecimal)+'" title="'+notNullEmpty(row['comparative_quotation_dtl_disc_amount'],threeDecimal)+'" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][vat_perc]" data-id="vat_perc" value="'+notNullEmpty(row['comparative_quotation_dtl_vat_percent'],twoDecimal)+'" title="'+notNullEmpty(row['comparative_quotation_dtl_vat_percent'],twoDecimal)+'" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][vat_val]" data-id="vat_val" value="'+notNullEmpty(row['comparative_quotation_dtl_vat_amount'],threeDecimal)+'" title="'+notNullEmpty(row['comparative_quotation_dtl_vat_amount'],threeDecimal)+'" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][gross_amount]" data-id="gross_amount" value="'+notNullEmpty(row['comparative_quotation_dtl_total_amount'],threeDecimal)+'" title="'+notNullEmpty(row['comparative_quotation_dtl_total_amount'],threeDecimal)+'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td class="text-center"></td>' +
                                    '</tr>';
                            }
                            $('#repeated_data').append(tr);
                            addDataInit();

                            toastr.success(response.message);
                        }
                        else{
                            toastr.error(response.message);
                        }
                    },
                    error: function(response,status) {
                        console.log(response);
                    },
                });
                closeModal();
            });
        };
        function selectLpo(){
            $('#help_datatable_lpoPoHelp, #help_datatable_lpoPoQuotationHelp').unbind();
            $('#help_datatable_lpoPoHelp, #help_datatable_lpoPoQuotationHelp').on('click', 'tbody>tr', function (e) {
                $('#repeated_data>tr').each(function(){
                    $(this).find('td:eq(0)>input[data-id="lpo_id"]').parents('tr').remove();
                })
                dataDeleteInit();
                var lpo_code = $(this).find('td[data-field="lpo_code"]').text();
                var lpo_id = $(this).find('td[data-field="lpo_id"]').text();
                var supplier_id = $(this).find('td[data-field="supplier_id"]').text();
                var supplier_name = $(this).find('td[data-field="supplier_name"]').text();
                var payment_term_id = $(this).find('td[data-field="payment_term_id"]').text();
                var payment_term_days = $(this).find('td[data-field="supplier_ageing_terms_value"]').text();
                var currency_id = $(this).find('td[data-field="currency_id"]').text();
                var exchange_rate = $(this).find('td[data-field="exchange_rate"]').text();
                $('form').find('#lpo_generation_no').val(lpo_code);
                $('form').find('#lpo_generation_no_id').val(lpo_id);
                $('form').find('#supplier_name').val(supplier_name);
                $('form').find('#supplier_id').val(supplier_id);
                $('form').find(".currency").val(currency_id).trigger('change');
                $('form').find('#exchange_rate').val(exchange_rate);
                $("#payment_terms").val(payment_term_id).trigger('change');
                $('form').find('#payment_mode').val(payment_term_days);
                //console.log("demand_approval_dtl_id: "+ demand_approval_dtl_id);
                var url = '/purchase-order/lpo/'+lpo_id+'/'+supplier_id;
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data:{_token: CSRF_TOKEN},
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    success: function(response, status){
                        console.log(response.data['all']);
                        if(response.status == 'success') {
                            var tr = '';
                            var total_length = $('#repeated_data>tr').length;
                            for(var p=0; p < response.data['all'].length; p++ ){
                                total_length++;
                                var  row = response.data['all'][p];
                                tr +='<tr>' +
                                    '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                                    '<input type="text" name="pd['+total_length+'][sr_no]" value="'+total_length+'" title="'+total_length+'" class="form-control sr_no erp-form-control-sm handle" readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][lpo_id]" data-id="lpo_id" value="'+notNull(row['lpo_id'])+'" class="lpo_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][lpo_dtl_id]" data-id="lpo_dtl_id" value="'+notNull(row['lpo_dtl_id'])+'" class="lpo_dtl_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+notNull(row['product_id'])+'" class="product_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+notNull(row['uom_id'])+'"class="uom_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+notNull(row['product_barcode_id'])+'" class="product_barcode_id form-control erp-form-control-sm " readonly>'+
                                    '</td>'+
                                    '<td><input type="text" name="pd['+total_length+'][barcode]" data-id="barcode" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" value="'+notNull(row['product_barcode_barcode'])+'" title="'+notNull(row['product_barcode_barcode'])+'" class="form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" value="'+notNull(row['product_name'])+'" title="'+notNull(row['product_name'])+'" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                                    '<td>'+
                                        '<select class="pd_uom field_readonly moveIndex form-control erp-form-control-sm" name="pd['+total_length+'][uom]" data-id="uom" title="'+notNull(row['uom_name'])+'">'+
                                            '<option value="'+notNull(row['uom_id'])+'">'+notNull(row['uom_name'])+'</option>'+
                                        '</select>'+
                                    '</td>' +
                                    '<td><input type="text" name="pd['+total_length+'][packing]" data-id="packing" value="'+notNull(row['packing_name'])+'" title="'+notNull(row['packing_name'])+'" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][quantity]" data-id="quantity" value="'+notNull(row['lpo_dtl_quantity'])+'" title="'+notNull(row['lpo_dtl_quantity'])+'" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][foc_qty]" data-id="foc_qty" value="'+notNull(row['lpo_dtl_foc_quantity'])+'" title="'+notNull(row['lpo_dtl_foc_quantity'])+'" class="form-control moveIndex erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][fc_rate]" data-id="fc_rate" value="'+notNull(row['lpo_dtl_fc_rate'])+'" title="'+notNull(row['lpo_dtl_fc_rate'])+'" class="fc_rate moveIndex form-control erp-form-control-sm validNumber"></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][rate]" data-id="rate" value="'+notNullEmpty(row['lpo_dtl_rate'],twoDecimal)+'"  title="'+notNullEmpty(row['lpo_dtl_rate'],twoDecimal)+'"  class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][amount]" data-id="amount" value="'+notNullEmpty(row['lpo_dtl_amount'],threeDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_amount'],threeDecimal)+'"  class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount]" data-id="discount" value="'+notNullEmpty(row['lpo_dtl_disc_percent'],twoDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_disc_percent'],twoDecimal)+'"  class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][discount_val]" data-id="discount_val" value="'+notNullEmpty(row['lpo_dtl_disc_amount'],threeDecimal)+'"  title="'+notNullEmpty(row['lpo_dtl_disc_amount'],threeDecimal)+'"    class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][vat_perc]" data-id="vat_perc" value="'+notNullEmpty(row['lpo_dtl_vat_percent'],twoDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_vat_percent'],twoDecimal)+'"   class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][vat_val]" data-id="vat_val" value="'+notNullEmpty(row['lpo_dtl_vat_amount'],threeDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_vat_amount'],threeDecimal)+'" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td><input type="text" name="pd['+total_length+'][gross_amount]" data-id="gross_amount" value="'+notNullEmpty(row['lpo_dtl_gross_amount'],threeDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_gross_amount'],threeDecimal)+'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                                    '<td class="text-center"></td>' +
                                    '</tr>';
                            }
                            $('#repeated_data').append(tr);
                            addDataInit();
                            toastr.success(response.message);
                        }
                        else{
                            toastr.error(response.message);
                        }
                    },
                    error: function(response,status) {
                    },
                });
                closeModal();
            });
        }
    </script>


    <script>
        var formcase = '{{$case}}';
        var productHelpUrl = "{{url('/common/help-open/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, message, readonly(boolean), require(boolean)
            {
                'id':'barcode',
                'fieldClass':'pd_barcode moveIndex',
                'data-url' : productHelpUrl,
                'require':true,
                'message':'Enter Name'
            },{
                'id':'product_name',
                'fieldClass':'pd_product_name',
                'readonly':true,
            },{
                'id':'uom',
                'fieldClass':'pd_uom moveIndex',
                'readonly':true,
                'type':'select'
            },{
                'id':'packing',
                'fieldClass':'pd_packing',
                'readonly':true,
            },{
                'id':'quantity',
                'fieldClass':'tblGridCal_qty moveIndex validNumber validOnlyFloatNumber',
            },{
                'id':'foc_qty',
                'fieldClass':'foc_qty moveIndex validNumber validOnlyFloatNumber',
            },{
                'id':'fc_rate',
                'fieldClass':'fc_rate moveIndex validNumber',
            },{
                'id':'rate',
                'fieldClass':'tblGridCal_rate moveIndex validNumber'
            },{
                'id':'amount',
                'fieldClass':'tblGridCal_amount validNumber',
                'readonly':true,
            },{
                'id':'discount',
                'fieldClass':'tblGridCal_discount moveIndex validNumber',
            },{
                'id':'discount_val',
                'fieldClass':'tblGridCal_discount_amount validNumber validOnlyFloatNumber',
            },{
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc moveIndex validNumber'
            },{
                'id':'vat_val',
                'fieldClass':'tblGridCal_vat_amount validNumber validOnlyFloatNumber',
            },{
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true,
            },
        ];
        var  arr_hidden_field = ['product_id','product_barcode_id','uom_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/product-inline-ajax2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/inline-help-func.js') }}" type="text/javascript"></script>
@endsection
