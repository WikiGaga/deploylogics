@extends('layouts.layout')
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
        $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="lpo_form" class="kt-form" method="post" action="{{ action('Purchase\LPOGenerationController@store',isset($id)?$id:"") }}">
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
                                    <input type="text" autofocus name="lpo_date" class="tb_moveIndex form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                            <label class="col-lg-4 erp-col-form-label">Currency:<span class="required">*</span></label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm tb_moveIndex currency" id="kt_select2_1" name="lpo_currency">
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
                            <label class="col-lg-4 erp-col-form-label">Exchange Rate:<span class="required">*</span></label>
                            <div class="col-lg-8">
                                <input type="text" id="exchange_rate" name="exchange_rate" value="{{isset($exchange_rate)?$exchange_rate:""}}" class="form-control erp-form-control-sm tb_moveIndex validNumber">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        @if($case == 'new')
                            <div class="row form-group-block">
                                <label class="col-lg-6 erp-col-form-label">Get Demand:</label>
                                <div class="col-lg-6">
                                    <button data-url="{{action('Common\DataTableController@helpOpen','demandApprovalHelp')}}" class="tb_moveIndex open_js_modal btn btn-sm btn-primary" id="get_demand_data">Click Here</button>
                                </div>
                            </div>
                        @endif
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
                                    $headings = ['Sr No','Branch Name','Barcode','Product Name','UOM','Packing','Sup. Name','Payment mode','Qty',
                                                  'FOC Qty','FC Rate','Rate','Amount','Disc%','Disc Amt','VAT%','Vat Amt',
                                                  'Gross Amt','Gnrte Quot','Gnrte PO'];
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
                                            <input id="supplier_id" readonly type="hidden" class="supplier_id form-control erp-form-control-sm handle">
                                            <input id="demand_id" readonly type="hidden" class="demand_id form-control erp-form-control-sm handle">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Branch Name</div>
                                        <div class="erp_form__grid_th_input">
                                            <input readonly id="branch_name" type="text" class="form-control erp-form-control-sm">
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
                                        <div class="erp_form__grid_th_title">
                                            Sup. Name
                                            <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                <i class="la la-barcode"></i>
                                            </button>
                                        </div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="supplier_name" type="text" class="supplier_name tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Payment mode</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="payment_mode" readonly type="text" class="form-control erp-form-control-sm">
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
                                        <div class="erp_form__grid_th_title">
                                            <label class="kt-radio kt-radio--brand" style="padding-left: 17px; top: -5px;">
                                                <input style="left:0;" type="radio" id="checkAllGnrteQuot" name="checkAllGnrteQuot" value="on">
                                                <span></span>
                                            </label> <div class="noselect">Gnrte Quot</div>
                                        </div>
                                        <div class="erp_form__grid_th_input" style="height: 30px;">
                                            <label class="kt-radio kt-radio--brand" style="padding-left: 17px;">
                                                <input type="radio" class="" id="generate_quotation" name="lpo" value="on">
                                                <span></span>
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">
                                            <label class="kt-radio kt-radio--success" style="padding-left: 17px; top: -5px;">
                                                <input style="left:0;" type="radio" id="checkAllGnrtePO" name="checkAllGnrteQuot" value="off">
                                                <span></span>
                                            </label> <div class="noselect">Gnrte PO</div>
                                        </div>
                                        <div class="erp_form__grid_th_input" style="height: 30px;">
                                            <label class="kt-radio kt-radio--success" style="padding-left: 17px;">
                                                <input type="radio" class="" id="generate_lpo" name="lpo" value="off">
                                                <span></span>
                                            </label>
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
                                @if(isset($details))
                                    @foreach($details as $dtl)
                                        @php $random_no = rand(); @endphp
                                        <tr class="product_tr_no ">
                                            <td>
                                                <input type="text" name="pd[{{$loop->iteration}}][sr_no]" value="{{$loop->iteration}}" title="{{$loop->iteration}}" class="form-control sr_no erp-form-control-sm" readonly="" aria-invalid="false">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][lpo_dtl]" data-id="random_no" value="{{$dtl->lpo_id}}" class="random_no form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][lpo_dtl_id]" data-id="lpo_dtl_id" value="{{$dtl->lpo_dtl_id}}" class="lpo_dtl_id form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][supplier_id]" data-id="supplier_id" value="{{$dtl->supplier_id}}" class="supplier_id form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$dtl->product_id}}"  class="product_id form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$dtl->uom_id}}" class="uom_id form-control erp-form-control-sm " readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][demand_id]" data-id="demand_id" value="{{$dtl->demand_id}}" class="demand_id form-control erp-form-control-sm" readonly="">
                                                <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$dtl->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][lpo_dtl_branch_id]" data-id="lpo_dtl_branch_id" value="{{$dtl->lpo_dtl_branch_id}}" class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>
                                                <input type="hidden" name="pd[{{$loop->iteration}}][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="{{$dtl->demand_approval_dtl_id}}" class="demand_approval_dtl_id form-control erp-form-control-sm" readonly>
                                            </td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][branch_name]" data-id="branch_name" value="{{isset($dtl->branch->branch_name)?$dtl->branch->branch_name:""}}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                            <td>
                                                <select class="pd_uom field_readonly form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][pd_uom]">
                                                    <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                </select>
                                            </td>
                                            <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][pd_packing]" value="{{isset($dtl->lpo_dtl_packing)?$dtl->lpo_dtl_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][supplier_name]" data-id="supplier_name" value="{{isset($dtl->supplier->supplier_name)?$dtl->supplier->supplier_name:""}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" class="supplier_name tb_moveIndex form-control erp-form-control-sm"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][payment_mode]" data-id="payment_mode" value="{{$dtl->payment_mode_id}}" title="{{$dtl->payment_mode_id}}" class="form-control erp-form-control-sm" readonly=""></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][quantity]" data-id="quantity"  value="{{$dtl->lpo_dtl_quantity}}" parent-id="{{$random_no}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][foc_qty]" data-id="foc_qty"  value="{{$dtl->lpo_dtl_foc_quantity}}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][fc_rate]" data-id="fc_rate"  value="{{$dtl->lpo_dtl_fc_rate}}" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][rate]" data-id="rate"  value="{{number_format($dtl->lpo_dtl_rate,2)}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][amount]" data-id="amount"  value="{{number_format($dtl->lpo_dtl_amount,3)}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_perc]" data-id="dis_perc"  value="{{number_format($dtl->lpo_dtl_disc_percent,2)}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][dis_amount]" data-id="dis_amount"  value="{{number_format($dtl->lpo_dtl_disc_amount,3)}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_perc]" data-id="vat_perc"  value="{{number_format($dtl->lpo_dtl_vat_percent,2)}}" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][vat_amount]" data-id="vat_amount"  value="{{number_format($dtl->lpo_dtl_vat_amount,3)}}" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                            <td><input type="text" name="pd[{{$loop->iteration}}][gross_amount]" data-id="gross_amount"  value="{{number_format($dtl->lpo_dtl_gross_amount,3)}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                            <td class="text-center">
                                                <label class="kt-radio kt-radio--brand" style="padding-left: 17px;">
                                                    <input type="radio" value="quot" class="quot" name="pd[{{$loop->iteration}}][lpo]" {{$dtl->lpo_dtl_generate_quotation==1?"checked":""}}><span></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="kt-radio kt-radio--success" style="padding-left: 17px;">
                                                    <input type="radio" value="lpo" class="lpo" name="pd[{{$loop->iteration}}][lpo]" {{$dtl->lpo_dtl_generate_lpo==1?"checked":""}}><span></span>
                                                </label>
                                            </td>
                                            @if(count($dtl->sub_dtls) == 0)
                                                <td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>
                                            @else
                                                <td class="text-center"><i class="la la-angle-down show_products" data-id="{{$random_no}}"></i></td>
                                            @endif
                                        </tr>
                                        @foreach($dtl->sub_dtls as $subDtls)
                                            <tr class="product_child_tr {{$random_no}} d-none">
                                                <td>
                                                    <input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][sr_no]" value="" title="" class="form-control sr_no erp-form-control-sm" readonly="" aria-invalid="false">
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][lpo_dtl_id]" data-id="lpo_dtl_id" value="{{$subDtls->lpo_dtl_id}}" class="random_no form-control erp-form-control-sm " readonly="">
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][lpo_dtl_dtl_id]" data-id="lpo_dtl_dtl_id" value="{{$subDtls->lpo_dtl_dtl_id}}" class="random_no form-control erp-form-control-sm " readonly="">
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][supplier_id]" data-id="supplier_id" value="{{$subDtls->supplier_id}}" class="supplier_id form-control erp-form-control-sm " readonly="">
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][product_id]" data-id="product_id" value="{{$subDtls->product_id}}" class="product_id form-control erp-form-control-sm " readonly="">
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{$subDtls->uom_id}}" class="uom_id form-control erp-form-control-sm " readonly="">
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][demand_id]" data-id="demand_id" value="{{$subDtls->demand_id}}" title="{{$subDtls->demand_id}}" class="demand_id form-control erp-form-control-sm" readonly="">
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{$subDtls->product_barcode_id}}" class="product_barcode_id form-control erp-form-control-sm" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][lpo_dtl_branch_id]" data-id="lpo_dtl_branch_id" value="{{$subDtls->lpo_dtl_branch_id}}" class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="{{$subDtls->demand_approval_dtl_id}}" class="demand_approval_dtl_id form-control erp-form-control-sm" readonly>
                                                </td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][branch_name]" data-id="branch_name" value="{{$subDtls->branch->branch_name}}" class="form-control erp-form-control-sm" readonly=""></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][pd_barcode]" data-id="pd_barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" value="{{$subDtls->barcode->product_barcode_barcode}}" title="{{$subDtls->barcode->product_barcode_barcode}}" class="pd_barcode form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][product_name]" data-id="product_name" value="{{isset($subDtls->product->product_name)?$subDtls->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>
                                                <td>
                                                    <select class="pd_uom form-control erp-form-control-sm" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][pd_uom]" data-id="pd_uom" title="{{isset($subDtls->uom->uom_name)?$subDtls->uom->uom_name:""}}">
                                                        <option value="{{isset($subDtls->uom->uom_id)?$subDtls->uom->uom_id:""}}">{{isset($subDtls->uom->uom_name)?$subDtls->uom->uom_name:""}}</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][pd_packing]" data-id="pd_packing" value="{{isset($subDtls->lpo_dtl_packing)?$subDtls->lpo_dtl_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly=""></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][supplier_name]" data-id="supplier_name" value="{{isset($subDtls->supplier->supplier_name)?$subDtls->supplier->supplier_name:""}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" class="supplier_name form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][payment_mode]" data-id="payment_mode" value="{{$subDtls->payment_mode_id}}" class="form-control erp-form-control-sm" readonly=""></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][quantity]" data-id="quantity" value="{{$subDtls->lpo_dtl_quantity}}" parent-id="" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][foc_qty]" data-id="foc_qty" value="{{$subDtls->lpo_dtl_foc_quantity}}" title="{{$subDtls->lpo_dtl_foc_quantity}}" class="tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][fc_rate]" data-id="fc_rate" value="{{$subDtls->lpo_dtl_fc_rate}}" title="{{$subDtls->lpo_dtl_fc_rate}}" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][rate]" data-id="rate" value="{{$subDtls->lpo_dtl_rate}}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][amount]" data-id="amount" value="{{$subDtls->lpo_dtl_amount}}" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][dis_perc]" data-id="dis_perc" value="{{$subDtls->lpo_dtl_disc_percent}}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][dis_amount]" data-id="dis_amount" value="{{$subDtls->lpo_dtl_disc_amount}}" class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][vat_perc]" data-id="vat_perc" value="{{$subDtls->lpo_dtl_vat_percent}}" class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][vat_amount]" data-id="vat_amount" value="{{$subDtls->lpo_dtl_vat_amount}}" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>
                                                <td><input type="text" name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][gross_amount]" data-id="gross_amount" value="{{$subDtls->lpo_dtl_gross_amount}}" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>
                                                <td class="text-center">
                                                    {{-- <label class="kt-radio kt-radio--brand">
                                                        <input type="radio" value="quot" class="quot" disabled name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][action]" {{$subDtls->lpo_dtl_generate_quotation==1?"checked":""}}><span></span>
                                                    </label> --}}
                                                </td>
                                                <td class="text-center">
                                                    {{-- <label class="kt-radio kt-radio--success">
                                                        <input type="radio" value="lpo" class="lpo" disabled name="pd[{{$loop->iteration}}][sub][{{$loop->iteration}}][action]" {{$subDtls->lpo_dtl_generate_lpo==1?"checked":""}}><span></span>
                                                    </label> --}}
                                                </td>
                                                <td></td>
                                            </tr>
                                        @endforeach
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
                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="2" id="lpo_remarks" name="lpo_remarks" maxlength="255" class="form-control erp-form-control-sm">{{ isset($lpo_remarks)?$lpo_remarks:'' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    @endpermission
    <!-- end:: Content  -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/lpo-generation.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/data-repeated-lpo.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script>
        $('#lpo_form').on('click', '#get_demand_data', function (e) {
            e.preventDefault();
            var data_url = $(this).attr('data-url');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#kt_modal_xl').modal('show').find('.modal-content').load('/common/help-open/demandApprovalHelp');
            $('.modal-dialog').draggable({
                handle: ".modal-header"
            });
        });
        // function selectDemandApproval(){
        $(document).on('click', '#help_datatable_demandApprovalHelp tbody>tr', function (e) {
            var demand_approval_dtl_id = $(this).find('td[data-field="demand_approval_dtl_id"]').text();
            var alreadyAdded = 0;
            $('.erp_form__grid>tbody.erp_form__grid_body>tr.product_tr_no').each(function(){
                var thix = $(this);
                var td_demand_approval_dtl_id = thix.find('td:first-child>input[data-id="demand_approval_dtl_id"]').val();
                if(demand_approval_dtl_id == td_demand_approval_dtl_id){
                    alreadyAdded = 1;
                }
            });
            if(alreadyAdded == 1){
                swal.fire({
                    title: 'Already added this item.',
                    text: "",
                    type: 'info',
                })
                return true;
            }
            url = '/lpo/demand/'+demand_approval_dtl_id;
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'GET',
                url: url,
                data:{_token: CSRF_TOKEN},
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                beforeSend : function(){
                    $('body').addClass('pointerEventsNone');
                },
                success: function(response, status){
                    $('body').removeClass('pointerEventsNone');
                    if(response.status == 'success'){
                        toastr.success(response.message);
                        var tr = '';
                        var product_list = [];
                        var product_id = [];
                        for(var i=0;i < response.data['all'].length;i++){
                            var  row = response.data['all'][i];
                            if(!product_id.includes(row['product_id']+row['uom_id'])){
                                product_id.push(row['product_id']+row['uom_id']);
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
                            if(product_list[p]['supplier_id'] != "" && product_list[p]['supplier_name'] != ""){
                                var supplier_id = notNull(product_list[p]['supplier_id']);
                                var supplier_name = notNull(product_list[p]['supplier_name']);
                            }else{
                                var supplier_id = notNull(product_list[p]['product_dtl']['supplier']['supplier_id']);
                                var supplier_name = notNull(product_list[p]['product_dtl']['supplier']['supplier_name']);
                            }
                            var ammount = parseFloat(approval_qty * product_list[p]['rate']['purc_rate']);
                            tr += '<tr class="product_tr_no">'+
                                '<td>'+
                                '<input type="text" name="pd['+product_tr_no+'][sr_no]" value="'+product_tr_no+'" title="'+product_tr_no+'" class="form-control sr_no erp-form-control-sm" readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][lpo_dtl]" data-id="random_no" value="'+random_no+'" class="random_no form-control erp-form-control-sm " readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][supplier_id]" data-id="supplier_id" value="'+supplier_id+'" title="'+supplier_id+'" class="supplier_id form-control erp-form-control-sm " readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][product_id]" data-id="product_id" value="'+product_list[p]['product_id']+'" title="'+product_list[p]['product_id']+'" class="product_id form-control erp-form-control-sm " readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][product_barcode_id]" data-id="product_barcode_id" value="'+product_list[p]['product_barcode_id']+'" title="'+product_list[p]['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm " readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][uom_id]" data-id="uom_id" value="'+product_list[p]['uom_id']+'" title="'+product_list[p]['uom_id']+'" class="uom_id form-control erp-form-control-sm " readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][demand_dtl_id]" data-id="demand_dtl_id" value="'+product_list[p]['demand_dtl_id']+'" title="'+product_list[p]['demand_dtl_id']+'" class="demand_dtl_id form-control erp-form-control-sm " readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="'+product_list[p]['demand_approval_dtl_id']+'" title="'+product_list[p]['demand_approval_dtl_id']+'" class="demand_approval_dtl_id form-control erp-form-control-sm " readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][demand_id]" data-id="demand_id" value="'+product_list[p]['demand_id']+'" class="demand_id form-control erp-form-control-sm" readonly>'+
                                '<input type="hidden" name="pd['+product_tr_no+'][lpo_dtl_branch_id]" data-id="lpo_dtl_branch_id" value="{{ auth()->user()->branch_id }}"  class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>'+
                                '</td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][branch_name]" data-id="branch_name" value="'+response.data['current_branch']['branch_name']+'" title="'+response.data['current_branch']['branch_name']+'" class="form-control erp-form-control-sm" readonly></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][pd_barcode]" data-id="pd_barcode" data-url="" value="'+product_list[p]['product_barcode_barcode']+'" title="'+product_list[p]['product_barcode_barcode']+'" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][product_name]" data-id="product_name" value="'+product_list[p]['product_name']+'" title="'+product_list[p]['product_name']+'" class="productHelp pd_product_name tb_moveIndex form-control erp-form-control-sm" readonly></td>'+
                                '<td>'+
                                    '<select class="pd_uom form-control erp-form-control-sm" name="pd['+product_tr_no+'][pd_uom]" data-id="pd_uom" title="'+product_list[p]['uom_name']+'">'+
                                        '<option value="'+product_list[p]['uom_id']+'">'+product_list[p]['uom_name']+'</option>'+
                                    '</select>'+
                                '</td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][pd_packing]" data-id="pd_packing" value="'+product_list[p]['product_barcode_packing']+'" title="'+product_list[p]['packing_name']+'" class="pd_packing form-control erp-form-control-sm" readonly></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][supplier_name]" data-id="supplier_name" value="'+supplier_name+'" title="'+supplier_name+'" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" class="open_inline__help supplier_name tb_moveIndex form-control erp-form-control-sm"></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][payment_mode]" data-id="payment_mode" value="" title="" class="form-control erp-form-control-sm" readonly></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][quantity]" data-id="quantity" value="'+approval_qty+'" title="'+approval_qty+'" parent-id="'+random_no+'" class="tblGridCal_qty tblGridCal_parent_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][foc_qty]" data-id="foc_qty" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][fc_rate]" data-id="fc_rate" value="' + product_list[p]['rate']['fc_rate'] + '" title="' + product_list[p]['rate']['fc_rate'] + '" class="fc_rate fc_parent_rate form-control erp-form-control-sm tb_moveIndex validNumber"></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][rate]" data-id="rate" value="' + product_list[p]['rate']['purc_rate'] + '" title="' + product_list[p]['rate']['purc_rate'] + '" class="tblGridCal_rate tblGridCal_parent_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][amount]" data-id="amount" value="'+ ammount +'" title="'+ ammount +'" class="tblGridCal_amount tblGridCal_parent_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][dis_perc]" data-id="dis_perc" value="' + product_list[p]['rate']['disc_per'] + '" title="' + product_list[p]['rate']['disc_per'] + '" class="tblGridCal_discount_perc tblGridCal_parent_discount tb_moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][dis_amount]" data-id="dis_amount" value="' + product_list[p]['rate']['disc_amount'] + '" title="' + product_list[p]['rate']['disc_amount'] + '" class="tblGridCal_discount_amount tblGridCal_parent_discount_amount form-control erp-form-control-sm validNumber"></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][vat_perc]" data-id="vat_perc" value="' + product_list[p]['rate']['vat_per'] + '" title="' + product_list[p]['rate']['vat_per'] + '" class="tblGridCal_vat_perc tblGridCal_parent_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][vat_amount]" data-id="vat_amount" value="' + product_list[p]['rate']['vat_amount'] + '" title="' + product_list[p]['rate']['vat_amount'] + '" class="tblGridCal_vat_amount tblGridCal_parent_vat_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                '<td><input type="text" name="pd['+product_tr_no+'][gross_amount]" data-id="gross_amount" value="" title="" class="tblGridCal_gross_amount tblGridCal_parent_gross_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                '<td class="text-center">'+
                                '<label class="kt-radio kt-radio--brand" style="padding-left: 17px;"><input type="radio" value="quot" class="quot" name="pd['+product_tr_no+'][action]" checked><span></span></label>'+
                                '</td>'+
                                '<td class="text-center">'+
                                '<label class="kt-radio kt-radio--success" style="padding-left: 17px;"><input type="radio" value="lpo" class="lpo" name="pd['+product_tr_no+'][action]"><span></span></label>'+
                                '</td>'+
                                '<td class="text-center"><i class="la la-angle-down show_products" data-id="'+random_no+'"></i></td>'+
                                '</tr>';

                            for(var i=0;i < response.data['all'].length;i++){
                                var  row = response.data['all'][i];
                                var j = i+1;
                                if(product_list[p]['product_id'] == row['product_id'] && product_list[p]['uom_id'] == row['uom_id']){
                                    product_child_tr_length++;
                                    //var product_child_tr = product_child_tr_length;
                                    var product_child_tr = parseInt(product_child_tr_length);
                                    var dtl_ammount = parseFloat(row['demand_approval_dtl_approve_qty'] * product_list[p]['rate']['purc_rate']).toFixed(3);
                                    tr += '<tr class="product_child_tr '+random_no+' d-none">'+
                                        '<td>'+
                                        '<input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][sr_no]" value="" title="" class="form-control sr_no erp-form-control-sm" readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][lpo_dtl]" data-id="random_no" value="'+random_no+'" class="random_no form-control erp-form-control-sm " readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][supplier_id]" data-id="supplier_id" value="'+notNull(row['supplier_id'])+'" title="'+notNull(row['supplier_id'])+'" class="supplier_id form-control erp-form-control-sm " readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][product_id]" data-id="product_id" value="'+row['product_id']+'" title="'+row['product_id']+'" class="product_id form-control erp-form-control-sm " readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][uom_id]" data-id="uom_id" value="'+row['uom_id']+'" title="'+row['uom_id']+'" class="uom_id form-control erp-form-control-sm " readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][demand_dtl_id]" data-id="demand_dtl_id" value="'+row['demand_dtl_id']+'" title="'+row['demand_dtl_id']+'" class="demand_dtl_id form-control erp-form-control-sm " readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][demand_id]" value="'+row['demand_id']+'" class="demand_id form-control erp-form-control-sm" readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][demand_approval_dtl_id]" data-id="demand_approval_dtl_id" value="'+row['demand_approval_dtl_id']+'" title="'+row['demand_approval_dtl_id']+'" class="demand_approval_dtl_id form-control erp-form-control-sm " readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][product_barcode_id]" data-id="product_barcode_id" value="'+row['product_barcode_id']+'" class="product_barcode_id form-control erp-form-control-sm" readonly>'+
                                        '<input type="hidden" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][lpo_dtl_branch_id]" value="'+row['branch_id']+'" class="lpo_dtl_branch_id form-control erp-form-control-sm" readonly>'+
                                        '</td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][branch_name]" value="'+row['branch_name']+'" title="'+row['branch_name']+'" class="form-control erp-form-control-sm" readonly></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][pd_barcode]" value="'+row['product_barcode_barcode']+'" title="'+row['product_barcode_barcode']+'" data-url="{{action('Common\DataTableController@helpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][product_name]" value="'+row['product_name']+'" title="'+row['product_name']+'" class="productHelp pd_product_name tb_moveIndex form-control erp-form-control-sm" readonly></td>'+
                                        '<td>'+
                                            '<select class="pd_uom form-control erp-form-control-sm" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][uom]" title="'+row['uom_name']+'">'+
                                                '<option value="'+row['uom_id']+'">'+row['uom_name']+'</option>'+
                                            '</select>'+
                                        '</td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][pd_packing]" value="'+row['product_barcode_packing']+'" title="'+row['product_barcode_packing']+'" class="pd_packing form-control erp-form-control-sm" readonly></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][supplier_name]" value="'+notNull(row['supplier_name'])+'" title="'+notNull(row['supplier_name'])+'" data-url="{{action('Common\DataTableController@helpOpen','supplierHelp')}}" class="open_inline__help supplier_name tb_moveIndex form-control erp-form-control-sm"></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][payment_mode]" value="" title="" class="form-control erp-form-control-sm" readonly></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][quantity]" value="'+row['demand_approval_dtl_approve_qty']+'" title="'+row['demand_approval_dtl_approv_qty']+'" child-id="'+random_no+'" class="tblGridCal_qty tblGridCal_child_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][foc_qty]" value="" title="" class="form-control erp-form-control-sm validNumber" readonly></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][fc_rate]" value="' + product_list[p]['rate']['fc_rate'] + '" title="' + product_list[p]['rate']['fc_rate'] + '" class="fc_rate fc_child_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][rate]" value="' + product_list[p]['rate']['purc_rate'] + '" title="' + product_list[p]['rate']['purc_rate'] + '" class="tblGridCal_rate tblGridCal_child_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][amount]" value="'+dtl_ammount+'" title="'+dtl_ammount+'" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][dis_perc]" value="' + product_list[p]['rate']['disc_per'] + '" title="' + product_list[p]['rate']['disc_per'] + '" class="tblGridCal_discount_perc tblGridCal_child_discount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][dis_amount]" value="' + product_list[p]['rate']['disc_amount'] + '" title="' + product_list[p]['rate']['disc_amount'] + '" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber"></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][vat_perc]" value="' + product_list[p]['rate']['vat_per'] + '" title="' + product_list[p]['rate']['vat_per'] + '" class="tblGridCal_vat_perc tblGridCal_child_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][vat_amount]" value="' + product_list[p]['rate']['vat_amount'] + '" title="' + product_list[p]['rate']['vat_amount'] + '" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                        '<td><input type="text" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][gross_amount]" value="" title="" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>'+
                                        '<td class="text-center">'+
                                        '<!-- <label class="kt-radio kt-radio--brand"><input type="radio" value="quot" class="quot" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][action]" disabled><span></span></label> -->'+
                                        '</td>'+
                                        '<td class="text-center">'+
                                        '<!-- <label class="kt-radio kt-radio--success"><input type="radio" value="lpo" class="lpo" name="pd['+product_tr_no+'][sub]['+product_child_tr+'][action]" disabled><span></span></label> -->'+
                                        '</td>'+
                                        '<td class="text-center"></td>'+
                                        '</tr>';
                                }
                            }
                        }
                        $('.erp_form__grid_body').append(tr);
                        showHideRow();
                        childCalculateTotalQty();
                        open_modal();
                        // Dont Touch This (Lmao) Its Working
                        $('.erp_form__grid_body .product_tr_no').each(el => {
                            var tr = $('.erp_form__grid_body .product_tr_no')[el];
                            gridCalcByRow(tr);
                        });
                        // =============== //
                        updateKeys();
                        $('.OnlyEnterAllow').keypress(OnlyEnterAllow);
                        $('input').attr('autocomplete', 'off');
                    }else{
                        toastr.error(response.message);
                    }
                },
                error: function(response,status) {
                    $('body').removeClass('pointerEventsNone');
                    console.log(response);
                },
            });
            closeModal();
        });
        // };
        showHideRow();
        function showHideRow(){
            $('.show_products').unbind();
            $('.show_products').click(function(){
                var dataId = $(this).attr('data-id');
                $(this).toggleClass('fa-rotate-180');
                var tbody = $(this).parents('tbody');
                tbody.find('tr.product_child_tr.'+dataId).toggleClass('d-none');
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
        $(document).on('click','#checkAllGnrteQuot',function(){
            $('.erp_form__grid>tbody>tr.product_tr_no>td').find('input.lpo').each(function(){
                $(this).prop('checked', false).attr('checked', false);
            })
            $('.erp_form__grid>tbody>tr.product_tr_no>td').find('input.quot').each(function(){
                $(this).prop('checked', true).attr('checked', true);
            })
        })
        $(document).on('click','#checkAllGnrtePO',function(){
            $('.erp_form__grid>tbody>tr.product_tr_no>td').find('input.lpo').each(function(){
                $(this).prop('checked', true).attr('checked', true);
            })
            $('.erp_form__grid>tbody>tr.product_tr_no>td').find('input.quot').each(function(){
                $(this).prop('checked', false).attr('checked', false);
            })
        })
    </script>
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var supplierHelpUrl = "{{url('/common/inline-help/supplierHelp')}}";
        var var_form_name = 'lpo_generation';
        var arr_text_Field = [
        // keys = id, fieldClass, message, readonly(boolean), require(boolean)
            {
                'id':'branch_name',
                'readonly':true,
            },{
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
                'id':'supplier_name',
                'data-url' : supplierHelpUrl,
                'fieldClass':'supplier_name tb_moveIndex open_inline__help',
            },
            {
                'id':'payment_mode',
                'readonly':true,
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
        var  arr_hidden_field = ['demand_id','supplier_id','product_id','product_barcode_id','uom_id'];

        $('input').attr('autocomplete', 'off');
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection


