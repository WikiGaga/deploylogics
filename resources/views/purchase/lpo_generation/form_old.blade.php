@extends('layouts.template')
@section('title', 'LPO Generation')

@section('pageCSS')
@endsection

@section('content')
    <!-- begin:: Content -->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['document_code'];
            $date =  date('d-m-Y');
            $exchange_rate = 10;
        }
        if($case == 'edit'){
            $id = $data['current']->lpo_id;
            $code = $data['current']->lpo_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->lpo_date))));
            $currency_id = $data['current']->currency_id;
            $exchange_rate = $data['current']->lpo_exchange_rate;
            $lpo_remarks = $data['current']->lpo_remarks;
            $details = $data['current']->dtls;
        }
    @endphp
    <form id="lpo_form" class="kt-form" method="post" action="{{ action('Purchase\LPOGenerationController@store',isset($id)?$id:"") }}">
    @csrf
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
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
                        <div class="col-lg-3">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Date:</label>
                                <div class="col-lg-8">
                                    <div class="input-group date">
                                        <input type="text" autofocus name="lpo_date" class="moveIndex form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
                                        <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar"></i>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group-block row">
                                <label class="col-lg-4 erp-col-form-label">Currency:</label>
                                <div class="col-lg-8">
                                    <div class="erp-select2">
                                        <select class="form-control kt-select2 erp-form-control-sm moveIndex currency" id="kt_select2_1" name="lpo_currency">
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
                        <div class="col-lg-3">
                            <div class="form-group-block row">
                                <label class="col-lg-4 erp-col-form-label">Exchange Rate:</label>
                                <div class="col-lg-8">
                                    <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:""}}" class="form-control erp-form-control-sm moveIndex validNumber">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                        @if($case == 'new')
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Get Demand:</label>
                                <div class="col-lg-6">
                                    <button data-url="{{action('Common\DataTableController@helpOpen','demandApprovalHelp')}}" class="moveIndex open_js_modal btn btn-sm btn-primary" id="get_demand_data">Click Here</button>
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>
                    <div class="form-group-block" style="overflow: auto;">
                        <table id="lopGenerationForm" class="ErpForm table table-striped table-responsive table-bordered table-hover table-checkable no-footer dtr-inline collapsed table-resizable">
                            <thead>
                            <tr>
                                <th style="width: 18px;">Sr No</th>
                                <th style="width: 92.6667px;">Branch Name</th>
                                <th style="width: 92.6667px;">Barcode</th>
                                <th style="width: 85.6667px;">Product Name</th>
                                <th style="width: 47.6667px;">UOM</th>
                                <th style="width: 15.3333px;">Pcking</th>
                                <th style="width: 56px;">Sup. Name</th>
                                <th style="width: 44.6667px;">Payment mode</th>
                                <th style="width: 38.6667px;">Qty</th>
                                <th style="width: 33.3333px;">FOC Qty</th>
                                <th style="width: 29.3333px;">FC Rate</th>
                                <th style="width: 43.3333px;">Rate</th>
                                <th style="width: 43.3333px;">Amunt</th>
                                <th style="width: 43.3333px;">Dis %</th>
                                <th style="width: 43.3333px;">Dis Amt</th>
                                <th style="width: 43.3333px;">VAT%</th>
                                <th style="width: 43.3333px;">Vat Amt</th>
                                <th style="width: 43.3333px;">Gross Amt</th>
                                <th style="width: 36.6667px;">
                                    <label class="kt-radio kt-radio--brand" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="radio" id="checkQuotAll" name="checkAllgrid" value="quot">
                                        <span></span>
                                    </label> <div class="noselect">Gnrte Quot</div>
                                </th>
                                <th style="width: 36.6667px;">
                                    <label class="kt-radio kt-radio--success" style="padding-left: 17px; top: -5px;">
                                        <input style="left:0;" type="radio" id="checkLpoAll" name="checkAllgrid" value="lpo">
                                        <span></span>
                                    </label> <div class="noselect">Gnrte PO</div>
                                </th>
                                <th style="width: 37.3333px;">Action</th>
                            </tr>
                            <tr id="dataEntryForm">
                                <td>
                                    <input type="text" id="sr_no" class="form-control erp-form-control-sm">
                                    <input type="hidden" id="supplier_id" class="supplier_id form-control erp-form-control-sm">
                                    <input type="hidden" id="product_id" class="product_id form-control erp-form-control-sm">
                                    <input type="hidden" id="product_barcode_id" class="product_barcode_id form-control erp-form-control-sm">
                                    <input type="hidden" id="uom_id" class="uom_id form-control erp-form-control-sm">
                                    <input type="hidden" id="demand_id" class="demand_id form-control erp-form-control-sm">
                                </td>
                                <td><input readonly id="branch_name" type="text" class="moveIndex form-control erp-form-control-sm"></td>
                                <td><input id="pd_barcode" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" type="text" class="pd_barcode moveIndex form-control erp-form-control-sm"></td>
                                <td><input readonly id="product_name" type="text" class="pd_product_name OnlyEnterAllow form-control erp-form-control-sm"></td>
                                <td>
                                    <select class="pd_uom moveIndex form-control erp-form-control-sm" id="uom">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td><input readonly id="packing" type="text" class="pd_packing form-control erp-form-control-sm"></td>
                                <td><input id="supplier_name" type="text" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" class="moveIndex open_js_modal supplier_name OnlyEnterAllow form-control erp-form-control-sm"></td>
                                <td><input readonly id="payment_mode" type="text" class="form-control erp-form-control-sm"></td>
                                <td><input id="quantity" type="text" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                <td><input readonly id="foc_qty" type="text" class="form-control erp-form-control-sm"></td>
                                <td><input id="fc_rate" type="text" class="fc_rate moveIndex validNumber form-control erp-form-control-sm"></td>
                                <td><input id="rate" type="text" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber"></td>
                                <td><input readonly id="amount" type="text" class="tblGridCal_amount form-control erp-form-control-sm validNumber"></td>
                                <td><input id="discount" type="text" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber"></td>
                                <td><input id="discount_val" type="text" class="tblGridCal_discount_amount moveIndex form-control erp-form-control-sm validNumber"></td>
                                <td><input id="vat_perc" type="text" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber"></td>
                                <td><input id="vat_val" type="text" class="tblGridCal_vat_amount moveIndex form-control erp-form-control-sm validNumber"></td>
                                <td><input readonly id="gross_amount" type="text" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber"></td>
                                <td class="text-center">
                                    <label class="kt-radio kt-radio--brand" >
                                        <input type="radio" class="" id="generate_quotation" name="lpo" value="on">
                                        <span></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="kt-radio kt-radio--success">
                                        <input type="radio" class="" id="generate_lpo" name="lpo" value="off">
                                        <span></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <button type="button" id="addData" class="moveIndexBtn moveIndex gridBtn btn btn-primary btn-sm">
                                        <i class="la la-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            </thead>
                            <tbody id="repeated_data">
                            @if(isset($details))
                                @foreach($details as $detail)
                                    @php $random_no = rand(); @endphp
                                    <tr class="product_tr_no ">
                                        <td>
                                            <input type="text" name="pd[{{$loop->iteration}}][sr_no]" value="{{$loop->iteration}}" title="{{$loop->iteration}}" class="form-control sr_no erp-form-control-sm" readonly="" aria-invalid="false">
                                            <input type="hidden" name="pd[{{$loop->iteration}}][lpo_dtl]" data-id="random_no" value="{{$detail->lpo_id}}" class="random_no form-control erp-form-control-sm " readonly="">
                                            <input type="hidden" name="pd[{{$loop->iteration}}][lpo_dtl_id]" data-id="lpo_dtl_id" value="{{$detail->lpo_dtl_id}}" class="lpo_dtl_id form-control erp-form-control-sm " readonly="">
                                            <input type="hidden" name="pd[{{$loop->iteration}}][supplier_id]" data-id="supplier_id" value="{{$detail->supplier_id}}" class="supplier_id form-control erp-form-control-sm " readonly="">
                                            <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$detail->product_id}}"  class="product_id form-control erp-form-control-sm " readonly="">
                                            <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$detail->uom_id}}" class="uom_id form-control erp-form-control-sm " readonly="">
                                            <input type="hidden" name="pd[{{$loop->iteration}}][demand_id]" data-id="demand_id" value="{{$detail->demand_id}}" class="demand_id form-control erp-form-control-sm" readonly="">
                                            <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$detail->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm" readonly>
                                            <input type="hidden" name="pd[{{$loop->iteration}}][lpo_dtl_branch_id]" data-id="lpo_dtl_branch_id" value="{{$detail->lpo_dtl_branch_id}}" class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>
                                            <input type="hidden" name="pd[{{$loop->iteration}}][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="{{$detail->demand_approval_dtl_id}}" class="demand_approval_dtl_id form-control erp-form-control-sm" readonly>
                                        </td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][branch_name]" data-id="branch_name" value="{{$detail->branch->branch_name}}" title="{{$detail->branch->branch_name}}" class="form-control erp-form-control-sm" readonly=""></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][pd_barcode]" data-id="pd_barcode" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" value="{{$detail->barcode->product_barcode_barcode}}" title="{{$detail->lpo_dtl_barcode}}" class="pd_barcode moveIndex form-control erp-form-control-sm"></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][product_name]" data-id="product_name" value="{{isset($detail->product->product_name)?$detail->product->product_name:""}}" title="{{isset($detail->product->product_name)?$detail->product->product_name:""}}" class="productHelp pd_product_name moveIndex form-control erp-form-control-sm" readonly=""></td>
                                        <td>
                                            <select class="pd_uom moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][uom]" data-id="uom" title="{{isset($detail->uom->uom_name)?$detail->uom->uom_name:""}}">
                                                <option value="{{isset($detail->uom->uom_id)?$detail->uom->uom_id:""}}">{{isset($detail->uom->uom_name)?$detail->uom->uom_name:""}}</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][packing]" data-id="packing" value="{{isset($detail->lpo_dtl_packing)?$detail->lpo_dtl_packing:""}}" title="{{isset($detail->lpo_dtl_packing)?$detail->lpo_dtl_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly=""></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][supplier_name]" data-id="supplier_name" value="{{isset($detail->supplier->supplier_name)?$detail->supplier->supplier_name:""}}" title="{{isset($detail->supplier->supplier_name)?$detail->supplier->supplier_name:""}}" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" class="open_js_modal OnlyEnterAllow supplier_name moveIndex form-control erp-form-control-sm"></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][payment_mode]" data-id="payment_mode" value="{{$detail->payment_mode_id}}" title="{{$detail->payment_mode_id}}" class="form-control erp-form-control-sm" readonly=""></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity" value="{{$detail->lpo_dtl_quantity}}" title="{{$detail->lpo_dtl_quantity}}" parent-id="{{$random_no}}" class="tblGridCal_qty tblGridCal_parent_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty"  value="{{$detail->lpo_dtl_foc_quantity}}" title="{{$detail->lpo_dtl_foc_quantity}}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" data-id="fc_rate" value="{{$detail->lpo_dtl_fc_rate}}" title="{{$detail->lpo_dtl_fc_rate}}" class="fc_rate form-control erp-form-control-sm moveIndex validNumber" ></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate" value="{{number_format($detail->lpo_dtl_rate,2)}}" title="{{number_format($detail->lpo_dtl_rate,2)}}" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber"></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount" value="{{number_format($detail->lpo_dtl_amount,3)}}" title="{{number_format($detail->lpo_dtl_amount,3)}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly=""></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][discount]" data-id="discount" value="{{number_format($detail->lpo_dtl_disc_percent,2)}}" title="{{number_format($detail->lpo_dtl_disc_percent,2)}}" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber"></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][discount_val]" data-id="discount_val" value="{{number_format($detail->lpo_dtl_disc_amount,3)}}" title="{{number_format($detail->lpo_dtl_disc_amount,3)}}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" ></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc" value="{{number_format($detail->lpo_dtl_vat_percent,2)}}" title="{{number_format($detail->lpo_dtl_vat_percent,2)}}" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber"></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][vat_val]" data-id="vat_val" value="{{number_format($detail->lpo_dtl_vat_amount,3)}}" title="{{number_format($detail->lpo_dtl_vat_amount,3)}}" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly=""></td>
                                        <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount" value="{{number_format($detail->lpo_dtl_gross_amount,3)}}" title="{{number_format($detail->lpo_dtl_gross_amount,3)}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly=""></td>
                                        <td class="text-center">
                                            <label class="kt-radio kt-radio--brand">
                                                <input type="radio" value="quot" class="quot" name="pd[{{$loop->iteration}}][lpo]" {{$detail->lpo_dtl_generate_quotation==1?"checked":""}}><span></span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="kt-radio kt-radio--success">
                                                <input type="radio" value="lpo" class="lpo" name="pd[{{$loop->iteration}}][lpo]" {{$detail->lpo_dtl_generate_lpo==1?"checked":""}}><span></span>
                                            </label>
                                        </td>
                                        @if(count($detail->sub_dtls) == 0)
                                            <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>
                                        @else
                                            <td class="text-center"><i class="la la-angle-down show_products" data-id="{{$random_no}}"></i></td>
                                        @endif
                                    </tr>
                                    @foreach($detail->sub_dtls as $subDtls)
                                        <tr class="product_child_tr {{$random_no}}" style="display:none;">
                                            <td>
                                                <input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][sr_no]" value="" title="" class="form-control sr_no erp-form-control-sm" readonly="" aria-invalid="false">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][lpo_dtl_id]" data-id="lpo_dtl_id" value="{{$subDtls->lpo_dtl_id}}" class="random_no form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][lpo_dtl_dtl_id]" data-id="lpo_dtl_dtl_id" value="{{$subDtls->lpo_dtl_dtl_id}}" class="random_no form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][supplier_id]" data-id="supplier_id" value="{{$subDtls->supplier_id}}" class="supplier_id form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$subDtls->product_id}}" title="24719920240624" class="product_id form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$subDtls->uom_id}}" class="uom_id form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][demand_id]" data-id="demand_id" value="{{$subDtls->demand_id}}" title="{{$subDtls->demand_id}}" class="demand_id form-control erp-form-control-sm" readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$subDtls->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][lpo_dtl_branch_id]" data-id="lpo_dtl_branch_id" value="{{$subDtls->lpo_dtl_branch_id}}" class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="{{$subDtls->demand_approval_dtl_id}}" class="demand_approval_dtl_id form-control erp-form-control-sm" readonly>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][branch_name]" data-id="branch_name" value="{{$subDtls->branch->branch_name}}" title="{{$subDtls->branch->branch_name}}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][pd_barcode]" data-id="pd_barcode" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" value="{{$subDtls->barcode->product_barcode_barcode}}" title="{{$subDtls->barcode->product_barcode_barcode}}" class="pd_barcode moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][product_name]" data-id="product_name" value="{{isset($subDtls->product->product_name)?$subDtls->product->product_name:""}}" title="{{isset($subDtls->product->product_name)?$subDtls->product->product_name:""}}" class="productHelp pd_product_name moveIndex form-control erp-form-control-sm" readonly=""></td>
                                            <td>
                                                <select class="pd_uom moveIndex form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][uom]" data-id="uom" title="{{isset($subDtls->uom->uom_name)?$subDtls->uom->uom_name:""}}">
                                                    <option value="{{isset($subDtls->uom->uom_id)?$subDtls->uom->uom_id:""}}">{{isset($subDtls->uom->uom_name)?$subDtls->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][packing]" data-id="packing" value="{{isset($subDtls->lpo_dtl_packing)?$subDtls->lpo_dtl_packing:""}}" title="{{isset($subDtls->lpo_dtl_packing)?$subDtls->lpo_dtl_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][supplier_name]" data-id="supplier_name" value="{{isset($subDtls->supplier->supplier_name)?$subDtls->supplier->supplier_name:""}}" title="{{isset($subDtls->supplier->supplier_name)?$subDtls->supplier->supplier_name:""}}" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" class="open_js_modal OnlyEnterAllow supplier_name moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][payment_mode]" data-id="payment_mode" value="{{$subDtls->payment_mode_id}}" title="{{$subDtls->payment_mode_id}}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][quantity]" data-id="quantity" value="{{$subDtls->lpo_dtl_quantity}}" title="{{$subDtls->lpo_dtl_quantity}}" parent-id="320508" class="tblGridCal_qty tblGridCal_parent_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][foc_qty]" data-id="foc_qty" value="{{$subDtls->lpo_dtl_foc_quantity}}" title="{{$subDtls->lpo_dtl_foc_quantity}}" class="form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][fc_rate]" data-id="fc_rate" value="{{$subDtls->lpo_dtl_fc_rate}}" title="{{$subDtls->lpo_dtl_fc_rate}}" class="fc_rate form-control erp-form-control-sm moveIndex validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][rate]" data-id="rate" value="{{$subDtls->lpo_dtl_rate}}" title="{{$subDtls->lpo_dtl_rate}}" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][amount]" data-id="amount" value="{{$subDtls->lpo_dtl_amount}}" title="{{$subDtls->lpo_dtl_amount}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][discount]" data-id="discount" value="{{$subDtls->lpo_dtl_disc_percent}}" title="{{$subDtls->lpo_dtl_disc_percent}}" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][discount_val]" data-id="discount_val" value="{{$subDtls->lpo_dtl_disc_amount}}" title="{{$subDtls->lpo_dtl_disc_amount}}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][vat_perc]" data-id="vat_perc" value="{{$subDtls->lpo_dtl_vat_percent}}" title="{{$subDtls->lpo_dtl_vat_percent}}" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][vat_val]" data-id="vat_val" value="{{$subDtls->lpo_dtl_vat_amount}}" title="{{$subDtls->lpo_dtl_vat_amount}}" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][gross_amount]" data-id="gross_amount" value="{{$subDtls->lpo_dtl_gross_amount}}" title="{{$subDtls->lpo_dtl_gross_amount}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly=""></td>
                                            <td class="text-center">
                                                <label class="kt-radio kt-radio--brand">
                                                    <input type="radio" value="quot" class="quot" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][action]" {{$subDtls->lpo_dtl_generate_quotation==1?"checked":""}}><span></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="kt-radio kt-radio--success">
                                                    <input type="radio" value="lpo" class="lpo" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][action]" {{$subDtls->lpo_dtl_generate_lpo==1?"checked":""}}><span></span>
                                                </label>
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach
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
                                    <td><span class="t_gross_total t_total">0</span><input type="hidden" id="pro_tot" name="pro_tot"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" id="lpo_remarks" name="lpo_remarks" maxlength="255" class="moveIndex form-control erp-form-control-sm">{{ isset($lpo_remarks)?$lpo_remarks:'' }}</textarea>
                        </div>
                    </div>
                </div>
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
    {{--<script src="{{ asset('js/pages/data-repeated-lpo.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/lpo-generation.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script>
        $('#lpo_form').on('click', '#get_demand_data', function (e) {
            e.preventDefault();
            var data_url = $(this).attr('data-url');
            openModal(data_url);
        });
        function selectDemandApproval(){
            $('#help_datatable_demandApprovalHelp').on('click', 'tbody>tr', function (e) {
                var demand_approval_dtl_id = $(this).find('td[data-field="demand_approval_dtl_id"]').text();
                //console.log("demand_approval_dtl_id: "+ demand_approval_dtl_id);
                url = '/lpo/demand/'+demand_approval_dtl_id;
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data:{_token: CSRF_TOKEN},
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    success: function(response, status){
                        if(response.status == 'success'){
                            toastr.success(response.message);
                            var tr = '';
                            var product_list = [];
                            var product_id = [];
                            for(var i=0;i < response.data['all'].length;i++){
                                var  row = response.data['all'][i];
                                if(!product_id.includes(row['product_id'])){
                                    product_id.push(row['product_id']);
                                    product_list.push(row);
                                }
                            }
                            var product_tr_length = $('tr.product_tr_no').length;
                            var product_child_tr_length = $('tr.product_child_tr').length;
                            for(var p=0; p < product_list.length; p++ ){
                                var approval_qty = 0;
                                for(var i=0;i < response.data['all'].length;i++) {
                                    var row = response.data['all'][i];
                                    if (product_list[p]['product_id'] == row['product_id']) {
                                        approval_qty += parseInt(row['demand_approval_dtl_approve_qty']);
                                    }
                                }
                                var product_tr_no = product_tr_length;
                                var q = parseInt(p)+1;
                                product_tr_no = parseInt(product_tr_no) + parseInt(q);
                                var random_no = Math.floor(Math.random() * 999999);
                                tr += '<tr class="product_tr_no">'+
                                    '<td>'+
                                    '<input type="text" name="pd['+product_tr_no+'][sr_no]" value="'+product_tr_no+'" title="'+product_tr_no+'" class="form-control sr_no erp-form-control-sm" readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][lpo_dtl]" data-id="random_no" value="'+random_no+'" class="random_no form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][supplier_id]" data-id="supplier_id" value="" title="" class="supplier_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][product_id]" data-id="product_id" value="'+product_list[p]['product_id']+'" title="'+product_list[p]['product_id']+'" class="product_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][product_barcode_id]" data-id="product_barcode_id" value="'+product_list[p]['product_barcode_id']+'" title="'+product_list[p]['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][uom_id]" data-id="uom_id" value="'+product_list[p]['uom_id']+'" title="'+product_list[p]['uom_id']+'" class="uom_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][demand_dtl_id]" data-id="demand_dtl_id" value="'+product_list[p]['demand_dtl_id']+'" title="'+product_list[p]['demand_dtl_id']+'" class="demand_dtl_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="'+product_list[p]['demand_approval_dtl_id']+'" title="'+product_list[p]['demand_approval_dtl_id']+'" class="demand_approval_dtl_id form-control erp-form-control-sm " readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][demand_id]" data-id="demand_id" value="'+product_list[p]['demand_id']+'" class="demand_id form-control erp-form-control-sm" readonly>'+
                                    '<input type="hidden" name="pd['+product_tr_no+'][lpo_dtl_branch_id]" data-id="lpo_dtl_branch_id" value="'+product_list[p]['branch_id']+'"  class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>'+
                                    '</td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][branch_name]" data-id="branch_name" value="'+product_list[p]['branch_name']+'" title="'+product_list[p]['branch_name']+'" class="form-control erp-form-control-sm" readonly></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][pd_barcode]" data-id="pd_barcode" data-url="" value="'+product_list[p]['product_barcode_barcode']+'" title="'+product_list[p]['product_barcode_barcode']+'" class="pd_barcode moveIndex form-control erp-form-control-sm" readonly></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][product_name]" data-id="product_name" value="'+product_list[p]['product_name']+'" title="'+product_list[p]['product_name']+'" class="productHelp pd_product_name moveIndex form-control erp-form-control-sm" readonly></td>'+
                                    '<td>'+
                                        '<select class="pd_uom moveIndex form-control erp-form-control-sm" name="pd['+product_tr_no+'][uom]" data-id="uom" title="'+product_list[p]['uom_name']+'">'+
                                            '<option value="'+product_list[p]['uom_id']+'">'+product_list[p]['uom_name']+'</option>'+
                                        '</select>'+
                                    '</td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][packing]" data-id="packing" value="'+product_list[p]['product_barcode_packing']+'" title="'+product_list[p]['packing_name']+'" class="pd_packing form-control erp-form-control-sm" readonly></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][supplier_name]" data-id="supplier_name" value="" title="" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" class="open_js_modal OnlyEnterAllow supplier_name moveIndex form-control erp-form-control-sm"></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][payment_mode]" data-id="payment_mode" value="" title="" class="form-control erp-form-control-sm" readonly></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][quantity]" data-id="quantity" value="'+approval_qty+'" title="'+approval_qty+'" parent-id="'+random_no+'" class="tblGridCal_qty tblGridCal_parent_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][foc_qty]" data-id="foc_qty" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][fc_rate]" data-id="fc_rate" value="" title="" class="fc_rate fc_parent_rate form-control erp-form-control-sm moveIndex validNumber"></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][rate]" data-id="rate" value="" title="" class="tblGridCal_rate tblGridCal_parent_rate moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][amount]" data-id="amount" value="" title="" class="tblGridCal_amount tblGridCal_parent_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][discount]" data-id="discount" value="" title="" class="tblGridCal_discount tblGridCal_parent_discount moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][discount_val]" data-id="discount_val" value="" title="" class="tblGridCal_discount_amount tblGridCal_parent_discount_amount form-control erp-form-control-sm validNumber"></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][vat_perc]" data-id="vat_perc" value="" title="" class="tblGridCal_vat_perc tblGridCal_parent_vat_perc moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][vat_val]" data-id="vat_val" value="" title="" class="tblGridCal_vat_amount tblGridCal_parent_vat_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                    '<td><input type="text" name="pd['+product_tr_no+'][gross_amount]" data-id="gross_amount" value="" title="" class="tblGridCal_gross_amount tblGridCal_parent_gross_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                    '<td class="text-center">'+
                                    '<label class="kt-radio kt-radio--brand"><input type="radio" value="quot" class="quot" name="pd['+product_tr_no+'][action]" checked><span></span></label>'+
                                    '</td>'+
                                    '<td class="text-center">'+
                                    '<label class="kt-radio kt-radio--success"><input type="radio" value="lpo" class="lpo" name="pd['+product_tr_no+'][action]"><span></span></label>'+
                                    '</td>'+
                                    '<td class="text-center"><i class="la la-angle-down show_products" data-id="'+random_no+'"></i></td>'+
                                    '</tr>';

                                for(var i=0;i < response.data['all'].length;i++){
                                    var  row = response.data['all'][i];
                                    var j = i+1;
                                    if(product_list[p]['product_id'] == row['product_id']){
                                        product_child_tr_length++;
                                        //var product_child_tr = product_child_tr_length;
                                        var product_child_tr = parseInt(product_child_tr_length);
                                        tr += '<tr class="product_child_tr '+random_no+'" style="display: none">'+
                                            '<td>'+
                                            '<input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][sr_no]" value="" title="" class="form-control sr_no erp-form-control-sm" readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][lpo_dtl]" data-id="random_no" value="'+random_no+'" class="random_no form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][supplier_id]" data-id="supplier_id" value="" title="" class="supplier_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][product_id]" data-id="product_id" value="'+row['product_id']+'" title="'+row['product_id']+'" class="product_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][uom_id]" data-id="uom_id" value="'+row['uom_id']+'" title="'+row['uom_id']+'" class="uom_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][demand_dtl_id]" data-id="demand_dtl_id" value="'+row['demand_dtl_id']+'" title="'+row['demand_dtl_id']+'" class="demand_dtl_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][demand_id]" value="'+row['demand_id']+'" class="demand_id form-control erp-form-control-sm" readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="'+row['demand_approval_dtl_id']+'" title="'+row['demand_approval_dtl_id']+'" class="demand_approval_dtl_id form-control erp-form-control-sm " readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][product_barcode_id]" data-id="product_barcode_id" value="'+row['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm" readonly>'+
                                            '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][lpo_dtl_branch_id]" value="'+row['branch_id']+'" class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>'+
                                            '</td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][branch_name]" value="'+row['branch_name']+'" title="'+row['branch_name']+'" class="form-control erp-form-control-sm" readonly></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][pd_barcode]" value="'+row['product_barcode_barcode']+'" title="'+row['product_barcode_barcode']+'" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" class="pd_barcode moveIndex form-control erp-form-control-sm" readonly></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][product_name]" value="'+row['product_name']+'" title="'+row['product_name']+'" class="productHelp pd_product_name moveIndex form-control erp-form-control-sm" readonly></td>'+
                                            '<td>'+
                                                '<select class="pd_uom moveIndex form-control erp-form-control-sm" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][uom]" title="'+row['uom_name']+'">'+
                                                    '<option value="'+row['uom_id']+'">'+row['uom_name']+'</option>'+
                                                '</select>'+
                                            '</td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][packing]" value="'+row['product_barcode_packing']+'" title="'+row['product_barcode_packing']+'" class="pd_packing form-control erp-form-control-sm" readonly></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][supplier_name]" value="" title="" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" class="open_js_modal OnlyEnterAllow supplier_name moveIndex form-control erp-form-control-sm"></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][payment_mode]" value="" title="" class="form-control erp-form-control-sm" readonly></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][quantity]" value="'+row['demand_approval_dtl_approve_qty']+'" title="'+row['demand_approval_dtl_approv_qty']+'" child-id="'+random_no+'" class="tblGridCal_qty tblGridCal_child_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][foc_qty]" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][fc_rate]" value="" title="" class="fc_rate fc_child_rate moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][rate]" value="" title="" class="tblGridCal_rate tblGridCal_child_rate moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][amount]" value="" title="" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][discount]" value="" title="" class="tblGridCal_discount tblGridCal_child_discount moveIndex form-control erp-form-control-sm validNumber" ></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][discount_val]" value="" title="" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber"></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][vat_perc]" value="" title="" class="tblGridCal_vat_perc tblGridCal_child_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][vat_val]" value="" title="" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                            '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][gross_amount]" value="" title="" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                            '<td class="text-center">'+
                                            '<label class="kt-radio kt-radio--brand"><input type="radio" value="quot" class="quot" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][action]" checked><span></span></label>'+
                                            '</td>'+
                                            '<td class="text-center">'+
                                            '<label class="kt-radio kt-radio--success"><input type="radio" value="lpo" class="lpo" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][action]"><span></span></label>'+
                                            '</td>'+
                                            '<td class="text-center"></td>'+
                                            '</tr>';
                                    }
                                }
                            }
                            $('#repeated_data').append(tr);
                            showHideRow();
                            childCalculateTotalQty();
                            open_modal();
                            moveIndex();
                            allCalcFunc();
                            focusOnTableInput();
                            $('.OnlyEnterAllow').keypress(OnlyEnterAllow);
                            $('input').attr('autocomplete', 'off');
                        }else{
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
        showHideRow();
        function showHideRow(){
            $('.show_products').unbind();
            $('.show_products').click(function(){
                var dataId = $(this).attr('data-id');
                $(this).toggleClass('fa-rotate-180');
                $('#repeated_data').find('tr.'+dataId).toggle();
            });
        }
        function childCalculateTotalQty(){
            $('.tblGridCal_child_qty').keyup(function(){
                var child_id = $(this).attr('child-id');
                var total = 0;
                $($('#repeated_data>tr>td').find('input[child-id='+child_id+']')).each(function(){
                    var value = ($(this).val() == '')? 0 : $(this).val();
                    total += parseInt(value);
                });
                $($('#repeated_data>tr>td').find('input[parent-id='+child_id+']')).each(function(){
                    $(this).val(total);
                    var tr = $(this).parents('tr');
                    amountCalc(tr);
                    discount(tr);
                    vat(tr);
                    grossAmount(tr);
                    totalAllGrossAmount();
                });

            });
        }
        function checkedAllInGrid(){
            $('#lopGenerationForm>thead>tr>th>label>input#checkQuotAll').unbind()
            $('#lopGenerationForm>thead>tr>th>label>input#checkLpoAll').unbind()
            $('input#checkQuotAll, input#generate_lpo').unbind()

            $('#lopGenerationForm>thead>tr>th>label>input#checkQuotAll').click(function(){
                $('#lopGenerationForm>thead>tr>th>label>input#checkLpoAll').prop('checked', false).attr('checked', false);
                var appAllcheck = $(this).is(":checked");
                $('#lopGenerationForm>tbody>tr>td').find('input#generate_quotation').each(function(){
                    if(appAllcheck) {
                        $(this).prop('checked', true).attr('checked', true);
                        $('#lopGenerationForm>tbody>tr>td').find('input#generate_lpo').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                    }else{
                        $(this).prop('checked', false).attr('checked', false);
                        $('#lopGenerationForm>tbody>tr>td').find('input#generate_lpo').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                    }
                })
            })
            $('#lopGenerationForm>thead>tr>th>label>input#checkLpoAll').click(function(){
                $('#lopGenerationForm>thead>tr>th>label>input#checkQuotAll').prop('checked', false).attr('checked', false);
                $('#lopGenerationForm>thead>tr>th>label>input#rejectAll').prop('checked', false).attr('checked', false);
                var appAllcheck = $(this).is(":checked");
                $('#lopGenerationForm>tbody>tr>td').find('input#generate_lpo').each(function(){
                    if(appAllcheck) {
                        $(this).prop('checked', true).attr('checked', true)
                        $('#lopGenerationForm>tbody>tr>td').find('input#generate_quotation').each(function(){
                            $(this).prop('checked', false).attr('checked', false);
                        })
                    }else{
                        $(this).prop('checked', false).attr('checked', false);
                        $('#lopGenerationForm>tbody>tr>td').find('input#generate_quotation').each(function(){
                            $(this).prop('checked', true).attr('checked', true);
                        })
                    }
                })
            })

            $('input#generate_quotation, input#generate_lpo').click(function(){
                $('input#checkQuotAll').prop('checked', false).attr('checked', false);
                $('input#checkLpoAll').prop('checked', false).attr('checked', false);
            });
        }
        $('#lopGenerationForm>thead>tr>th>label>input[type="radio"]').click(function(){
            var val = $(this).val();
            if(val == 'lpo'){
                $('#lopGenerationForm>tbody>tr>td').find('input[type="radio"].lpo').each(function(){
                    $(this).prop('checked', true).attr('checked', true);
                })
                $('#lopGenerationForm>tbody>tr>td').find('input[type="radio"].quot').each(function(){
                    $(this).prop('checked', false).attr('checked', false);
                })
            }
            if(val == 'quot'){
                $('#lopGenerationForm>tbody>tr>td').find('input[type="radio"].quot').each(function(){
                    $(this).prop('checked', true).attr('checked', true);
                })
                $('#lopGenerationForm>tbody>tr>td').find('input[type="radio"].lpo').each(function(){
                    $(this).prop('checked', false).attr('checked', false);
                })
            }
        });
    </script>
    <script>
        var productHelpUrl = "{{url('/common/help-open/productHelp')}}";
        var supplierHelpUrl = "{{url('/common/help-open/supplierHelp')}}";
        var var_form_name = 'lpo_generation';
        var arr_text_Field = [
        // keys = id, fieldClass, message, readonly(boolean), require(boolean)
            {
                'id':'branch_name',
                'readonly':true,
            },{
                'id':'pd_barcode',
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
                'fieldClass':'pd_uom',
                'readonly':true,
                'type':'select'
            },{
                'id':'packing',
                'fieldClass':'pd_packing',
                'readonly':true,
            },{
                'id':'supplier_name',
                'data-url' : supplierHelpUrl,
                'fieldClass':'moveIndex OnlyEnterAllow open_js_modal supplier_name',
            },{
                'id':'payment_mode',
                'readonly':true,
            },{
                'id':'quantity',
                'fieldClass':'tblGridCal_qty moveIndex validNumber validOnlyFloatNumber',
            },{
                'id':'foc_qty',
                'readonly':true,
            },{
                'id':'fc_rate',
                'fieldClass':'fc_rate moveIndex validNumber'
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
                'fieldClass':'tblGridCal_discount_amount validNumber',
                'readonly':true,
            },{
                'id':'vat_perc',
                'fieldClass':'tblGridCal_vat_perc moveIndex validNumber',
            },{
                'id':'vat_val',
                'fieldClass':'tblGridCal_vat_amount validNumber',
                'readonly':true,
            },{
                'id':'gross_amount',
                'fieldClass':'tblGridCal_gross_amount validNumber',
                'readonly':true,
            },
        ];
        var  arr_radio_field = [
            // keys = id, labelClass, inputClass, checked(boolean), value
            {
                'id':'generate_quotation',
                'labelClass':'kt-radio--brand',
                'inputClass':'quot',
                'value':'quot'

            },{
                'id':'generate_lpo',
                'labelClass':'kt-radio--success',
                'inputClass':'lpo',
                'value':'lpo'
            }
        ];
        var  arr_select_field = [
            // keys = id, inputClass
            {
                'id':'uom',
                'inputClass':'pd_uom'
            }
        ];
        var  arr_hidden_field = ['demand_id','supplier_id','product_id','product_barcode_id','uom_id'];

        $('input').attr('autocomplete', 'off');
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/product-ajax.js') }}" type="text/javascript"></script>
@endsection


