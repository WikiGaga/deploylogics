@extends('layouts.layout')
@section('title', 'Assemble/Disassemble Product')

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
                //dd($data['current']);
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
                $rate_perc = $data['current']->stock_rate_perc;;
                $dtls = isset($data['current']->stock_dtls)? $data['current']->stock_dtls :[];
                $remarks = $data['current']->stock_remarks;
            }
            $type =$data['form_type'];
            $form_type = $data['stock_code_type'];
    @endphp
    @permission($data['permission'])
    <form id="assemble_products_form" class="stock_form kt-form" method="post" action="{{  action('Inventory\StockController@store', [$type,$id])  }}">
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
                            <label class="col-lg-6 erp-col-form-label">Date:</label>
                            <div class="col-lg-6">
                                <div class="input-group date">
                                    <input type="text" name="stock_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                            <label class="col-lg-6 erp-col-form-label text-center"> Assemble Product: <span class="required">*</span></label>
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
                </div>
                <div class="row form-group-block">
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-6 erp-col-form-label"> Assemble Qty: <span class="required">*</span></label>
                            <div class="col-lg-6">
                                <input name="assamble_qty" id="assamble_qty" type="text" value="{{isset($assamble_qty)?$assamble_qty:''}}" class=" tblGridCal_assemble_qty validNumber moveIndex form-control erp-form-control-sm">
                            </div>
                        </div>
                    </div>
                    @if($data['stock_code_type'] == 'ass')
                        <div class="col-lg-4">
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
                        </div>
                    @else
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Store:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp-select2">
                                        <select class="moveIndex form-control erp-form-control-sm kt-select2" name="store_to">
                                            <option value="0">Select</option>
                                            @php $store_to = isset($store_to)?$store_to:'2' @endphp
                                            @foreach($data['store'] as $store)
                                                <option value="{{$store->store_id}}" {{$store->store_id == $store_to?'selected':''}}>{{$store->store_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($form_type=='ass')
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label text-center">Formula Entry: <span class="required">*</span></label>
                            <div class="col-lg-8">
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
                    </div>
                    @endif
                </div>
               
                <div class="row form-group-block">
                    <div class="col-lg-8">
                        <div class="row">
                            <label class="col-lg-3 erp-col-form-label">Rate Type <span class="required">*</span></label>
                            <div class="col-lg-9">
                                <div class="ChangeRateBlock input-group erp-select2-sm">
                                    @php $rate_type = isset($rate_type)?$rate_type:'3'; @endphp
                                    <select class="form-control erp-form-control-sm kt-select2 moveIndex" id="rate_type" name="rate_type">
                                        <option value="0">Select</option>
                                        @foreach($data['rate_types'] as $key => $value)
                                            <option value="{{$key}}" {{$rate_type == $key ? "selected" :""}}>{{$value}}</option>
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
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <div class="data_entry_header">
                            <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                    <i class="flaticon-more" style="color: #666666;"></i>
                                </button>
                                @php
                                    $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Qty','formula Qty', 'Batch No', 'Rate', 'Amount'];
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
                            <table class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                <thead class="erp_form__grid_header">
                                <tr>
                                    <th scope="col" width="6%">
                                        <div class="erp_form__grid_th_title">Sr.</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                            <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                            <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                            <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col" width="12%">
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
                                    <th scope="col" width="35%">
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
                                    <th scope="col" width="5%">
                                        <div class="erp_form__grid_th_title">Packing</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="pd_packing" readonly type="text" class="pd_packing form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col" width="15%">
                                        <div class="erp_form__grid_th_title">Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="quantity" type="text" class="tblGridCal_qty validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                        </div>
                                    </th>
                                    <th scope="col" width="15%">
                                        <div class="erp_form__grid_th_title">Formula Qty</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="formula_qty" type="text" class="tblGridCal_formula validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm" onfocusout="myFunction()">
                                        </div>
                                    </th>
                                    <th scope="col" width="15%">
                                        <div class="erp_form__grid_th_title">Store</div>
                                        <div class="erp_form__grid_th_input">
                                            <select class="pd_store form-control erp-form-control-sm tb_moveIndex" id="pd_store">
                                                <option value="">Select</option>
                                                @foreach($data['store'] as $store)
                                                    <option value="{{$store->store_id}}">{{$store->store_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th scope="col" width="15%">
                                        <div class="erp_form__grid_th_title">Bactch No</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="batch_no" type="text" class="tblGridCal_batchNo validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">

                                        </div>
                                    </th>
                                    <th scope="col" width="15%">
                                        <div class="erp_form__grid_th_title">Rate</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="rate" type="text" class="tblGridCal_rate validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">

                                        </div>
                                    </th>
                                    <th scope="col" width="15%">
                                        <div class="erp_form__grid_th_title">Amount</div>
                                        <div class="erp_form__grid_th_input">
                                            <input id="amount" type="text" class="tblGridCal_amount validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm" readonly>

                                        </div>
                                    </th>
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

                                            <td><input type="text" data-id="product_name" name="pd[{{$loop->iteration}}][product_name]" value="{{isset($dtl->product->product_name)?$dtl->product->product_name:""}}" class="product_name form-control erp-form-control-sm" readonly></td>

                                            <td>
                                                <select class="pd_uom field_readonly tb_moveIndex form-control erp-form-control-sm" data-id="pd_uom" name="pd[{{$loop->iteration}}][uom]">
                                                    <option value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}">{{isset($dtl->uom->uom_name)?$dtl->uom->uom_name:""}}</option>
                                                </select>
                                            </td>

                                            <td><input type="text" data-id="pd_packing" name="pd[{{$loop->iteration}}][packing]" value="{{isset($dtl->barcode->product_barcode_packing)?$dtl->barcode->product_barcode_packing:""}}" class="pd_packing form-control erp-form-control-sm" readonly></td>

                                            <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->stock_dtl_quantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td><input type="text" data-id="formula_qty" name="pd[{{$loop->iteration}}][formula_qty]" value="{{$dtl->stock_dtl_formula_qty}}" class="tblGridCal_formula tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                            <td>
                                                <select class="pd_store form-control erp-form-control-sm tb_moveIndex" data-id="pd_store" name="pd[{{ $loop->iteration }}][store]" title="{{$dtl->stock_dtl_store}}">
                                                    @foreach($data['store'] as $store)
                                                        <option value="{{$store->store_id}}" {{$store->store_id == $dtl->stock_dtl_store?'selected':''}}>{{$store->store_name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                         <td><input type="text" data-id="batch_no" name="pd[{{$loop->iteration}}][batch_no]" value="{{ $dtl->stock_dtl_batch_no }}" class="form-control erp-form-control-sm tblGridCal_batchNo validNumber validOnlyNumber tb_moveIndexr" ></td>

                                            <td><input type="text" data-id="rate" name="pd[{{$loop->iteration}}][rate]" value="{{ $dtl->stock_dtl_rate}}" class="form-control erp-form-control-sm tblGridCal_rate validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm" ></td>

                                            <td><input type="text" data-id="amount" name="pd[{{$loop->iteration}}][amount]" value="{{  $dtl->stock_dtl_amount }}" class="form-control erp-form-control-sm tblGridCal_amount validNumber validOnlyNumber tb_moveIndex" readonly></td>

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
                                        <td class="total_grid_qty">
                                            <input value="0.000" readonly type="text" id="total_qty" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="total_grid_amount">
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
    <script src="{{ asset('js/pages/js/stock.js') }}" type="text/javascript"></script>
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
                'id':'pd_packing',
                'fieldClass':'pd_packing',
                'readonly':true
            },
            {
                'id':'quantity',
                'fieldClass':'tblGridCal_qty validNumber validOnlyNumber tb_moveIndex'
            },
            {
                'id':'formula_qty',
                'fieldClass':'tblGridCal_formula validNumber validOnlyNumber tb_moveIndex'
            },
            {
                'id':'pd_store',
                'fieldClass':'pd_store field_readonly',
                'type':'select'
            }
            ,
            {
                'id':'batch_no',
                'fieldClass':'tblGridCal_batchNo validNumber validOnlyNumber tb_moveIndex',
            }
            ,
            {
                'id':'rate',
                'fieldClass':'tblGridCal_rate validNumber validOnlyNumber tb_moveIndex',
            }
            ,
            {
                'id':'amount',
                'fieldClass':'tblGridCal_amount validNumber validOnlyNumber tb_moveIndex',
                'readonly':true,
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id'];

        $('#getFormulationRequestData').click(function(){

            var errors = 0;
            let requireds = ['assamble_qty','pd_store'];
            requireds.forEach(( el ) => {
                if($('#' + el).val() == ""){
                    errors++;
                }
            });

            if(errors == 0){
                var thix = $(this);
                var val = thix.parents('.input-group').find('input#formulation_id').val();
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
                                stock_id : val,
                                rate_type : $('#rate_type').val(),

                            };
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type        : 'POST',
                                url         : '/stock/formulation/get-formulation-request-dtl-data',
                                dataType	: 'json',
                                data        : formData,
                                success: function(response) {
                                    if(response['status'] == 'success'){
                                        toastr.success(response.message);
                                        console.log(response)

                                        var formulation_dtl = response.data.formulation.dtls;

                                        var tr = "";
                                        var iteration = $('.erp_form__grid_body').find('tr').length + 1;
                                        var length = formulation_dtl.length;
                                        for(var i=0;i < length; i++){
                                            var formulation = formulation_dtl[i];
                                            var product_id = formulation['product_id'];
                                            var barcode_id = formulation['product_barcode_id'];
                                            var uom_id = formulation['uom_id'];
                                            var barcode = formulation['barcode']['product_barcode_barcode'];
                                            var product_name = formulation['product']['product_name'];
                                            var uom_name = formulation['uom']['uom_name'];
                                            var packing = formulation['item_formulation_dtl_packing'];
                                            var dtl_quantity = formulation['item_formulation_dtl_quantity'];
                                            var formula_qty = formulation['formula_qty'];

                                            tr += '<tr>'+
                                            '<td class="handle">'+
                                                '<i class="fa fa-arrows-alt-v handle"></i>'+
                                                '<input type="text" value="'+iteration+'" name="pd['+iteration+'][sr_no]" title="1" class="form-control erp-form-control-sm handle" readonly="" autocomplete="off">'+
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
                                                '<div class="erp-select2">'+
                                                    '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd['+iteration+'][pd_uom]">'+
                                                        '<option value="'+uom_id+'">'+uom_name+'</option>'+
                                                    '</select>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][pd_packing]" data-id="pd_packing" data-url="" value="'+packing+'" title="'+packing+'" class="form-control erp-form-control-sm pd_packing" readonly="" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][quantity]" data-id="quantity" data-url="" value="'+dtl_quantity+'" title="1" class="form-control erp-form-control-sm tblGridCal_qty validNumber validOnlyNumber tb_moveIndex" autocomplete="off" aria-invalid="false">'+
                                            '</td>'+
                                           '<td><input type="text" name="pd['+iteration+'][formula_qty]" data-id="formula_qty" data-url="'+formula_qty+'" value="" title="" class="form-control erp-form-control-sm tblGridCal_formula validNumber validOnlyNumber tb_moveIndex" autocomplete="off"></td>'+

                                            '<td>'+
                                                '<div class="erp-select2">'+
                                                    '<select class="validOnlyNumber tb_moveIndex form-control erp-form-control-sm" aria-invalid="false" name="pd['+iteration+'][store]">'+
                                                        '<option value="">Select</option>'+
                                                        '<option value="21143221261303">DAMAGE STORE</option>'+
                                                        '<option value="11113721261339">EXPIRED STORE</option>'+
                                                        '<option value="38563321271217">PURCHASE RETURN STORE</option>'+
                                                        '<option value="1">PURCHASE STORE</option>'+
                                                        '<option value="2">Showroom</option>'+
                                                    '</select>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][batch_no]" data-id="batch_no" data-url="" value="" title="" class="form-control erp-form-control-sm tblGridCal_batchNo validNumber validOnlyNumber tb_moveIndex" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][rate]" data-id="rate" data-url="" value="" title="" class="form-control erp-form-control-sm tblGridCal_rate validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm" autocomplete="off">'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="text" name="pd['+iteration+'][amount]" data-id="amount" data-url="" value="" title="" class="form-control erp-form-control-sm tblGridCal_amount validNumber validOnlyNumber tb_moveIndex" autocomplete="off" readonly>'+
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
                    toastr.error("Select first Entry Formula");
                }
            }else{
                toastr.error("Please Fill All Required Fields");
            }
        });
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
