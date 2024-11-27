@extends('layouts.layout')
@section('title', 'Deal Setup')

@section('pageCSS')
    <style>
        div#f_product_barcode_id-error {
            position: absolute;
            top: 28px;
        }
    </style>
@endsection

@section('content')
    <!--begin::Form-->
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){
            $code = $data['stock_code'];
            $date =  date('d-m-Y');
            $id = '';
            if($data['stock_code_type'] == 'ass'){
                $rate_type = 'item_cost_rate';
            }else{
                $rate_type = 'item_sale_rate';
            }
        }
        if($case == 'edit'){
            // dd($data['current']->toArray());
            $id = $data['current']->stock_id;
            $code = $data['current']->stock_code;
            $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->stock_date))));
            $store_id = $data['current']->stock_store_from_id;
            $store_to = $data['current']->stock_store_to_id;
            $assamble_qty =  $data['current']->assamble_qty;
            $product_id = $data['current']->product_id;
            $uom_id = $data['current']->uom_id;
            $product_barcode_id = $data['current']->product_barcode_id;
            $product_barcode_packing = $data['current']->product_barcode_packing;
            $product_name = isset($data['current']->product->product_name)?$data['current']->product->product_name:"";
            $product_barcode = isset($data['current']->barcode->product_barcode_barcode)?$data['current']->barcode->product_barcode_barcode:"";
            $formulation_id = $data['current']->formulation_id;
            $formulation_code = isset($data['current']->formulation->item_formulation_code) ? $data['current']->formulation->item_formulation_code : '';
            $rate_type = $data['current']->stock_rate_type;
            $is_active = $data['current']->is_expiry;
            $start_date =  date('d-m-Y H:i', strtotime(trim(str_replace('/','-',$data['current']->start_date))));
            $end_date =  date('d-m-Y H:i', strtotime(trim(str_replace('/','-',$data['current']->end_date))));
            $rate_perc = $data['current']->stock_rate_perc;;
            $sale_rate = $data['current']->sale_rate;;
            $cost_rate = $data['current']->cost_rate;;
            $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
            $remarks = $data['current']->stock_remarks;
        }
        $type =$data['form_type'];
        $form_type = $data['stock_code_type'];
    @endphp
    @permission($data['permission'])
    <form id="deal_steup_form" class="deal_steup_form kt-form" method="post" action="{{  action('Inventory\DealSetupController@store', [$id])  }}">
    @csrf
    <input type="hidden" name="stock_code_type" value='{{$data['stock_code_type']}}' id="form_type">
    <input type="hidden" name="stock_menu_id" value='{{$data['stock_menu_id']}}'>
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
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Start Date/Time: </label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" class="form-control erp-form-control-sm " value="{{ isset($start_date) ? $start_date:date('d-m-Y H:i') }}" id="start_date" name="start_date">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-clock-o glyphicon-th"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">End Date/Time: </label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" class="form-control erp-form-control-sm " value="{{ isset($end_date) ? $end_date:date('d-m-Y H:i') }}" id="end_date" name="end_date">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-clock-o glyphicon-th"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label text-center">No Expiry:</label>
                            <div class="col-lg-6">
                                <div class="kt-checkbox-list">
                                    <label class="kt-checkbox kt-checkbox--success">
                                        @if ($case == 'edit')
                                            <input type="checkbox" id="is_active" name="is_active" {{ $is_active==1?'checked':'' }}>
                                        @else
                                            <input type="checkbox" id="is_active" name="is_active">
                                        @endif
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label"> Barcode: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                <div class="input-group open-modal-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                <i class="la la-minus-circle"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="f_barcode" name="f_barcode" value="{{isset($product_barcode)?$product_barcode:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productFormulationHelp')}}" class=" open_inline__help pd_barcode moveIndex form-control erp-form-control-sm" placeholder="Enter here">
                                    <input type="hidden" id="f_product_id" name="f_product_id" value="{{isset($product_id)?$product_id:''}}" class="form-control erp-form-control-sm">
                                    <input type="hidden" id="f_product_uom_id" name="uom_id" value="{{isset($uom_id)?$uom_id:''}}" class="form-control erp-form-control-sm">
                                    <input type="hidden" id="f_product_barcode_id" name="f_product_barcode_id" value="{{isset($product_barcode_id)?$product_barcode_id:''}}" class="form-control erp-form-control-sm">
                                    <input type="hidden" id="f_product_barcode_packing" name="product_barcode_packing" value="{{isset($product_barcode_packing)?$product_barcode_packing:''}}" class="form-control erp-form-control-sm">
                                    <!-- <div class="input-group-append">
                                        <span class="input-group-text btn-open-modal">
                                            <i class="la la-search"></i>
                                        </span>
                                    </div> -->
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <input id="f_product_name" name="f_product_name" value="{{isset($product_name)?$product_name:''}}" type="text" class="form-control erp-form-control-sm" readonly>
                    </div>
                    {{-- <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label text-center">Store:<span class="required">*</span></label>
                            <div class="col-lg-6">
                                <div class="erp-select2">
                                    <select class="pd_store moveIndex form-control erp-form-control-sm kt-select2" name="store" id="pd_store">
                                        <option value="0">Select</option>
                                        @php $storeid = isset($store_id)?$store_id:'2' @endphp
                                        @foreach($data['store'] as $store)
                                            <option value="{{$store->store_id}}" {{$store->store_id == $storeid?'selected':''}}>{{$store->store_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="row form-group-block">
                    {{-- <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label"> Assemble Qty: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input name="assamble_qty" id="assamble_qty" type="text" value="{{isset($assamble_qty)?$assamble_qty:''}}" class=" tblGridCal_assemble_qty validNumber moveIndex form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Formula Entry: </label>
                            <div class="col-lg-6">
                                <div class="erp_form___block">
                                <div class="input-group open-modal-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                            <i class="la la-minus-circle"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="formulation_code" name="formulation_code" value="{{isset($formulation_code)?$formulation_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','formulationEntryHelp')}}" class=" open_inline__help pd_barcode moveIndex form-control erp-form-control-sm">
                                    <input type="hidden" id="formulation_id" name="formulation_id" value="{{isset($formulation_id)?$formulation_id:''}}" class="form-control erp-form-control-sm">
                                    <div class="input-group-append">
                                        <span class="input-group-text group-input-btn" id="getFormulationRequestData">
                                            GO
                                        </span>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Sale Rate: </label>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <input type="text" class="form-control erp-form-control-sm validNumber validOnlyNumber" value="{{ isset($sale_rate) ? $sale_rate:'' }}" name="sale">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label">Cost Rate: </label>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <input type="text" class="form-control erp-form-control-sm validNumber validOnlyNumber" value="{{ isset($cost_rate) ? $cost_rate:'' }}" name="cost" id="cost" readonly>
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
                                    $headings = ['Sr No','Barcode','Product Name','Qty','UOM','Sale Rate','Sale Amount','Cost Rate','Cost Amount','Disc %', 'Disc Amt', 'Sale Net Amount'];
                                @endphp
                                <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="max-height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
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
                            <table id="grn_barcode_data_table" class="table table_column_switch table_pit_list erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                <tr>
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
                                            <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Product Name</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col" width="5%">
                                        <div class="erp_form__grid_th_title">UOM</div>
                                        <div class="erp_form__grid_th_input">
                                            <select id="pd_uom" class="pd_uom tb_moveIndex form-control erp-form-control-sm">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sale Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sale_rate" type="text" class="tblGridCal_sale_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Sale Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sale_amount" readonly type="text" class="tblGridCal_sale_amount tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Cost Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="cost_rate" readonly type="text" class="tblGridCal_cost_rate validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Cost Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="cost_amount" readonly type="text" class="tblGridCal_cost_amount tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    {{-- <th scope="col">
                                        <div class="erp_form__grid_th_title">Disc %</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="dis_perc" type="text" class="tblGridCal_discount_perc tb_moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Disc Amt</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="dis_amount" type="text" readonly class="tblGridCal_discount_amount validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                        </div>
                                    </th> 
                                    <th scope="col">
                                        <div class="erp_form__grid_th_title">Unit Price</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="rate" type="text" class="tblGridCal_rate tb_moveIndex validNumber form-control erp-form-control-sm">
                                        </div>
                                    </th>--}}
                                    {{-- <th scope="col">
                                        <div class="erp_form__grid_th_title">Action</div>
                                        <div class="erp_form__grid_th_btn">
                                            <button type="button" class="add_data tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                <i class="la la-plus"></i>
                                            </button>
                                        </div>
                                    </th> --}}
                                    <th scope="col" width="7%">
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
                                    @if(isset($dtls) && $dtls != [])
                                        @foreach($dtls as $dtl)
                                            @php
                                                $product_id = $dtl->product->f_product_id;
                                                $product_name = $dtl->product->product_name;
                                                $product_barcode =  $dtl->barcode->product_barcode_barcode;
                                                $product_barcode_id =  $dtl->barcode->product_barcode_id;
                                            @endphp
                                            <tr>
                                                <td class="handle"><i class="fa fa-arrows-alt-v handle"></i>
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_barcode_id]" data-id="product_barcode_id" value="{{isset($dtl->product_barcode_id)?$dtl->product_barcode_id:""}}" class="product_barcode_id form-control erp-form-control-sm handle" readonly>
                                                </td>
                                                <td><input type="text" data-id="pd_barcode" name="pd[{{$loop->iteration}}][pd_barcode]" value="{{$dtl->barcode->product_barcode_barcode}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productHelp')}}" class="pd_barcode tb_moveIndex form-control erp-form-control-sm" readonly></td>
                                                <td><input type="text" data-id="product_name"   name="pd[{{ $loop->iteration }}][product_name]"  value="{{ isset($dtl->product->product_name) ? $dtl->product->product_name : '' }}" class="product_name form-control erp-form-control-sm" readonly> </td>
                                                <td>
                                                    <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][uom]">
                                                        <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][quantity]" data-id="quantity" value="{{ $dtl->stock_dtl_quantity }}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][sale_rate]" data-id="sale_rate" value="{{ number_format($dtl->stock_dtl_sale_rate, 3, '.', '') }}" class="tblGridCal_sale_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][sale_amount]" data-id="sale_amount" value="{{ number_format($dtl->stock_dtl_sale_amount, 3, '.', '') }}" class="tblGridCal_sale_amount tb_moveIndex form-control erp-form-control-sm validNumber"></td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][cost_rate]" data-id="cost_rate" value="{{ number_format($dtl->cost_rate, 3, '.', '') }}" class="tblGridCal_cost_rate form-control erp-form-control-sm validNumber validOnlyFloatNumber">  </td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][cost_amount]" data-id="cost_amount" value="{{ number_format($dtl->cost_amount, 3, '.', '') }}" class="tblGridCal_cost_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber">  </td>
                                                {{-- <td><input type="text" name="pd[{{ $loop->iteration }}][dis_perc]"  data-id="dis_perc"  value="{{ number_format($dtl->stock_dtl_disc_percent, 3, '.', '') }}" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>
                                                <td><input readonly type="text" name="pd[{{ $loop->iteration }}][dis_amount]"  data-id="dis_amount" value="{{ number_format($dtl->stock_dtl_disc_amount, 3, '.', '') }}" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td> 
                                                <td><input type="text" name="pd[{{ $loop->iteration }}][rate]" data-id="rate" value="{{ number_format($dtl->stock_dtl_rate, 3, '.', '') }}" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"> </td>--}}
                                                <td class="text-center">
                                                    <div class="btn-group btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button>
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
                                    <td class="total_grid_qty">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td class="total_grid_sale_rate">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td class="total_grid_sale_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td class="total_grid_cost_rate">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    <td class="total_grid_cost_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>
                                    {{-- <td class="total_grid_disc_perc">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td> 
                                    <td class="total_grid_disc_amount">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td>--}}
                                    {{-- <td class="total_grid_rate">
                                        <input value="0.000" readonly type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                    </td> --}}
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
                                <td><span class="t_total">0</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row form-group-block">
                    <label class="col-lg-2 erp-col-form-label">Notes:</label>
                    <div class="col-lg-10">
                        <textarea type="text" rows="2" maxlength="100"  name="stock_remarks" class="moveIndex form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
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
    <script src="{{ asset('js/pages/js/deal-setup.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-modal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    
    <script>
        var productHelpUrl = "{{url('/common/inline-help/productHelp')}}";
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)

            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode tb_moveIndex open_inline__help',
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
                'id':'quantity',
                'fieldClass':'tblGridCal_qty validNumber validOnlyNumber tb_moveIndex'
            },
            {
                'id':'sale_rate',
                'fieldClass':'tblGridCal_sale_rate validNumber validOnlyNumber tb_moveIndex',
                // 'readonly':true
            },
            {
                'id':'sale_amount',
                'fieldClass':'tblGridCal_sale_amount validNumber validOnlyNumber tb_moveIndex',
                'readonly':true
            },
            {
                'id':'cost_rate',
                'fieldClass':'tblGridCal_cost_rate validNumber validOnlyNumber tb_moveIndex',
                'readonly':true
            },
            {
                'id':'cost_amount',
                'fieldClass':'tblGridCal_cost_amount validNumber validOnlyNumber tb_moveIndex',
                'readonly':true
            },
            // {
            //     'id':'dis_perc',
            //     'fieldClass':'tblGridCal_discount_perc validNumber validOnlyNumber tb_moveIndex'
            // },
            // {
            //     'id':'dis_amount',
            //     'fieldClass':'tblGridCal_discount_amount validNumber validOnlyNumber tb_moveIndex'
            // },
            // {
            //     'id':'rate',
            //     'fieldClass':'tblGridCal_rate validNumber validOnlyNumber tb_moveIndex'
            // },
            
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];

        // $('#getFormulationRequestData').click(function(){

        //     var errors = 0;
        //     let requireds = ['assamble_qty','pd_store'];
        //     requireds.forEach(( el ) => {
        //         if($('#' + el).val() == ""){
        //             errors++;
        //         }
        //     });

        //     if(errors == 0){
        //         var thix = $(this);
        //         var val = thix.parents('.input-group').find('input#formulation_id').val();
        //         if(val){
        //             swal.fire({
        //                 title: 'Alert!',
        //                 text: "Are You Sure To Get Data!",
        //                 type: 'question',
        //                 showCancelButton: true,
        //                 confirmButtonText: 'Yes'
        //             }).then(function(result) {
        //                 if (result.value) {
        //                     var formData = {
        //                         stock_id : val,
        //                         rate_type : $('#rate_type').val(),

        //                     };
        //                     $.ajax({
        //                         headers: {
        //                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                         },
        //                         type        : 'POST',
        //                         url         : '/stock/formulation/get-formulation-request-dtl-data',
        //                         dataType	: 'json',
        //                         data        : formData,
        //                         success: function(response) {
        //                             if(response['status'] == 'success'){
        //                                 toastr.success(response.message);
        //                                 console.log(response)

        //                                 var formulation_dtl = response.data.formulation.dtls;

        //                                 var tr = "";
        //                                 var iteration = $('.erp_form__grid_body').find('tr').length + 1;
        //                                 var length = formulation_dtl.length;
        //                                 for(var i=0;i < length; i++){
        //                                     var formulation = formulation_dtl[i];
        //                                     var product_id = formulation['product_id'];
        //                                     var barcode_id = formulation['product_barcode_id'];
        //                                     var uom_id = formulation['uom_id'];
        //                                     var barcode = formulation['barcode']['product_barcode_barcode'];
        //                                     var product_name = formulation['product']['product_name'];
        //                                     var uom_name = formulation['uom']['uom_name'];
        //                                     var packing = formulation['item_formulation_dtl_packing'];
        //                                     var dtl_quantity = formulation['item_formulation_dtl_quantity'];
        //                                     var formula_qty = formulation['formula_qty'];

        //                                     tr += '<tr>'+
        //                                     '<td class="handle">' +
        //                                     '<input type="text" name="pd['+ iteration +'][sr_no]"  data-id="sr_no" value="' + iteration + '" title="' + iteration + '"class="form-control sr_no erp-form-control-sm handle" readonly>' +
        //                                     '<input type="hidden" name="pd[' + iteration + '][product_id]" data-id="product_id" value="' + notNull(product_id) + '" class="product_id form-control erp-form-control-sm " readonly>' +
        //                                     '<input type="hidden" name="pd[' + iteration +'][product_barcode_id]" data-id="product_barcode_id" value="' +notNull(barcode_id) +'"class="product_barcode_id form-control erp-form-control-sm " readonly>' +
        //                                     '<input type="hidden" name="pd[' + iteration +'][uom_id]" data-id="uom_id" value="' + notNull(uom_id) +'"class="uom_id form-control erp-form-control-sm " readonly>' +
        //                                     '</td>' +
        //                                     '<td><input type="text" name="pd[' + iteration +'][pd_barcode]" data-id="pd_barcode" value="' + notNull(barcode) + '" title="' + notNull(barcode) + '"  class="form-control erp-form-control-sm" readonly></td>' +
        //                                     '<td><input type="text" name="pd[' + iteration +'][product_name]" data-id="product_name" value="' + notNull(product_name) + '" title="' + notNull(product_name) + '" class="product_name form-control erp-form-control-sm" readonly></td>' +
        //                                     '<td>'+
        //                                         '<div class="erp-select2">'+
        //                                             '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd['+iteration+'][pd_uom]">'+
        //                                                 '<option value="'+uom_id+'">'+uom_name+'</option>'+
        //                                             '</select>'+
        //                                         '</div>'+
        //                                     '</td>'+
        //                                     '<td><input type="text" name="pd[' + iteration + '][quantity]" data-id="quantity" value="' + notNull(dtl_quantity) + '" title="' + notNull(dtl_quantity) +'" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
        //                                     '<td><input type="text" name="pd[' + iteration +'][sale_rate]" data-id="sale_rate" value="" title="" class="tblGridCal_sale_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
        //                                     '<td><input type="text" name="pd[' + iteration +'][sale_amount]" data-id="sale_amount" value="" title="" class="tblGridCal_sale_amount tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
        //                                     '<td><input type="text" name="pd[' + iteration +'][cost_rate]" data-id="cost_rate" value="" title="" class="tblGridCal_cost_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
        //                                     '<td><input type="text" name="pd[' + iteration +'][cost_amount]" data-id="cost_amount" value="" title="" class="tblGridCal_cost_amount form-control erp-form-control-sm validNumber" readonly></td>' +
        //                                     // '<td><input type="text" name="pd[' + iteration +'][dis_perc]" data-id="dis_perc" value="" title="" class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
        //                                     // '<td><input type="text" name="pd[' + iteration + '][dis_amount]" data-id="dis_amount" value="" title="" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
        //                                     // '<td><input type="text" name="pd[' + iteration +'][rate]" data-id="rate" value="" title="" class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
        //                                     '<td class="text-center">'+
        //                                         '<div class="btn-group btn-group btn-group-sm" role="group">'+
        //                                             '<button type="button" class="btn btn-danger gridBtn delData">'+
        //                                                 '<i class="la la-trash"></i>'+
        //                                             '</button>'+
        //                                         '</div>'+
        //                                     '</td>'+
        //                                     '</tr>';
        //                                     iteration += 1;
        //                                 }
        //                                 $('.erp_form__grid_body').append(tr);
        //                                 // allCalcFunc();
        //                                 // $('input').attr('autocomplete', 'off');
        //                                 // updateKeys();
        //                                 // dataDelete();
        //                                 // table_td_sortable();
        //                                 // allGridTotal();
        //                             }
        //                         }
        //                     });
        //                 }
        //             });

        //         }else{
        //             toastr.error("Select First Entry Formula");
        //         }
        //     }else{
        //         toastr.error("Please Fill All Required Fields");
        //     }
        // });


 //change sale rate
 $('.sale_rate_barcode').click(function() {
            var barcodeData = {};
            barcodeData.data = [];
            var sale_rate = $('.erp_form__grid>thead.erp_form__grid_header>tr').find('#product_barcode_id').val();
            if (sale_rate) {
                barcodeData.data.push(sale_rate);
            }
            if ($('.erp_form__grid>tbody.erp_form__grid_body>tr').length > 0) {
                $('.erp_form__grid>tbody.erp_form__grid_body>tr').each(function() {
                    var thix = $(this)
                    var barcode = thix.find('input[data-id="product_barcode_id"]').val();
                    barcodeData.data.push(barcode);
                });
            }
            if (barcodeData.data.length > 0) {
                var url = '/grn/barcode-sale-price';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    data: barcodeData,
                    success: function(response, data) {
                        if (response['status'] == 'success') {
                            toastr.success('Sale Rate Updated');
                            var products = response.product_barcode;
                            products.forEach(element => {
                                var parent = $('input[value="' + element.product_barcode_id +'"]').parents('tr');
                                var value = parseFloat(element.product_barcode_sale_rate_rate).toFixed(3);
                                parent.find('input[data-id="sale_rate"]').val(value);
                            });
                        }
                    },
                    error: function(response, status) {}
                });
            }
        });



    </script>
    <script>
        $(document).on('keyup blur' , '.tblGridCal_qty, .tblGridCal_sale_rate',function(e){
            //Cost according to quantity
            var thix = this;
            var tr = $(this).parents('tr');
            funcHeaderCalc(tr,thix);
            if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
                allGridTotal();
            }
        });
        function funcHeaderCalc(tr,thix = null){
            var cListArr = [];
            if(!valueEmpty(thix)){
                var cList = thix.classList;
                for(var i=0;i<cList.length;i++){
                    cListArr.push(cList[i]);
                }
            }
            //debugger
            var qty = tr.find('.tblGridCal_qty').val();
            var sale = tr.find('.tblGridCal_sale_rate').val();
            var cost = tr.find('.tblGridCal_cost_rate').val();
            var sale_amount = funcCalcNumberFloat(qty) * funcCalcNumberFloat(sale);
            var cost_amount = funcCalcNumberFloat(qty) * funcCalcNumberFloat(cost);
            tr.find('.tblGridCal_sale_amount').val(funcNumberFloat(sale_amount));
            tr.find('.tblGridCal_cost_amount').val(funcNumberFloat(cost_amount));
        }
    
        function allGridTotal(){
            var t_qty = 0;
            var t_sale_rate = 0;
            var t_sale_amt = 0;
            var t_cost_rate = 0;
            var t_cost_amt = 0;
            var total_item = 0;
            $('.erp_form__grid_body>tr').each(function(){
                total_item += 1;
                var thix = $(this);
                if(funcNumValid(thix.find('.tblGridCal_qty').val())){
                    t_qty += funcNumValid(thix.find('.tblGridCal_qty').val());
                }
                if(funcNumValid(thix.find('.tblGridCal_sale_rate').val())){
                    t_sale_rate += funcNumValid(thix.find('.tblGridCal_sale_rate').val());
                }
                if(funcNumValid(thix.find('.tblGridCal_sale_amount').val())){
                    t_sale_amt += funcNumValid(thix.find('.tblGridCal_sale_amount').val());
                }
                if(funcNumValid(thix.find('.tblGridCal_cost_rate').val())){
                    t_cost_rate += funcNumValid(thix.find('.tblGridCal_cost_rate').val());
                }
                if(funcNumValid(thix.find('.tblGridCal_cost_amount').val())){
                    t_cost_amt += funcNumValid(thix.find('.tblGridCal_cost_amount').val());
                }
            });
            var tr = $('.erp_form__grid_body_total>tr:first-child');
            tr.find('.total_grid_qty>input').val(funcNumValid(t_qty));
            tr.find('.total_grid_sale_rate>input').val(funcNumValid(t_sale_rate).toFixed(3)); //.val(funcNumValid(t_sale_rate).toFixed(3));
            tr.find('.total_grid_sale_amount>input').val(funcNumValid(t_sale_amt).toFixed(3)); //.val(funcNumValid(t_sale_rate).toFixed(3));
            tr.find('.total_grid_cost_rate>input').val(funcNumValid(t_cost_rate).toFixed(3)); //.val(funcNumValid(t_sale_rate).toFixed(3));
            tr.find('.total_grid_cost_amount>input').val(funcNumValid(t_cost_amt).toFixed(3));
            $('span.t_total').html(funcNumValid(t_cost_amt).toFixed(3));
            $('#cost').val(funcNumValid(t_cost_amt).toFixed(3));
        }
    </script>
    <script>
        $('#start_date, #start_date_validate').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd-mm-yyyy hh:ii',
            todayBtn: true,
            minDate: new Date()
        });
        $('#end_date, #end_date_validate').datetimepicker({
            format: 'dd-mm-yyyy hh:ii',
            minDate: 0,
            todayHighlight: true,
            autoclose: true,
            todayBtn: true,
        });

        $(function() {
            enable_cb();
            $("#is_active").click(enable_cb);
        });

        function enable_cb() {
            if (this.checked) {
                $("#end_date").attr("disabled", true);
            } else {
                $("#end_date").removeAttr("disabled");
            }
        }
    </script>
<script>
//focus Out
function myFunction(){
  var x = document.getElementById("assamble_qty").value;
   var y = document.getElementById("formula_qty").value;
   var z = x*y;
   document.getElementById("formula_qty").value=z;
}
</script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/change-grid-item-rate.js') }}" type="text/javascript"></script>

@endsection