@extends('layouts.layout')
@section('title', 'Food Recipe')

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
                $code = $data['document_code'];
                $date =  date('d-m-Y');
            }
            if($case == 'edit'){
                $id = $data['current']->item_formulation_id;
                $code = $data['current']->item_formulation_code;
                $date = date('d-m-Y', strtotime(trim(str_replace('/','-',$data['current']->item_formulation_date))));
                $product_id = $data['current']->product_id;
                //  $product_name = isset($data['current']->product->product_name)?$data['current']->product->product_name:"";
                $product_name = $data['current']->product->product_name;
                $product_barcode_id = $data['current']->product_barcode_id;
                $product_barcode_packing = $data['current']->product_barcode_packing;
                $product_barcode = $data['current']->product_barcode_barcode;
                $qty = $data['current']->item_formulation_qty;
                $purchase_unit = $data['current']->item_formulation_purchase_unit;
                $current_tp = $data['current']->item_formulation_current_tp;
                $remarks = $data['current']->item_formulation_remarks;
                $dtls = isset($data['current']->dtls)? $data['current']->dtls :[];
            }
            $form_type = $data['form_type'];
    @endphp
    @permission($data['permission'])
    <form id="formulation_form" class="kt-form" method="post" action="{{ action('Inventory\ItemFormulationController@store', isset($id)?$id:"") }}">
        <input type="hidden" value='{{$data['form_type']}}' id="form_type">
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
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Date:</label>
                                <div class="col-lg-6">
                                    <div class="input-group date">
                                        <input type="text" name="formulation_date" class="form-control erp-form-control-sm c-date-p" readonly value="{{isset($date)?$date:""}}" id="kt_datepicker_3" />
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
                                <label class="col-lg-6 erp-col-form-label text-center"> Assemble Product:<span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <div class="erp_form___block">
                                        <div class="input-group open-modal-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                                    <i class="la la-minus-circle"></i>
                                                </span>
                                            </div>
                                            <input type="text" id="f_barcode" name="f_barcode" value="{{isset($product_barcode)?$product_barcode:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','productFormulationHelp')}}" class="open_inline__help pd_barcode moveIndex form-control erp-form-control-sm" placeholder="Enter Here">
                                            <input type="hidden" id="f_product_id" name="f_product_id" value="{{isset($product_id)?$product_id:''}}" class="form-control erp-form-control-sm">
                                            <input type="hidden" id="f_product_barcode_id" name="f_product_barcode_id" value="{{isset($product_barcode_id)?$product_barcode_id:''}}" class="form-control erp-form-control-sm">
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
                                <label class="col-lg-6 erp-col-form-label">Assemble Qty: <span class="required">*</span></label>
                                <div class="col-lg-6">
                                    <input name="formulation_qty" type="text" value="{{isset($qty)?$qty:''}}" class="validNumber moveIndex form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Purchase Unit:</label>
                                <div class="col-lg-6">
                                    <input name="f_purchase_unit" type="text" value="{{isset($purchase_unit)?$purchase_unit:''}}" class="validNumber moveIndex form-control erp-form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Current TP:</label>
                                <div class="col-lg-6">
                                    <input name="f_current_tp" type="text" value="{{isset($current_tp)?$current_tp:''}}" class="validNumber moveIndex form-control erp-form-control-sm">
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
                                        $headings = ['Sr No','Barcode','Product Name','UOM','Packing','Qty','Purchase Unit','Percentage','Cost Price','Sale Type','Ingredient Type','Remarks'];
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
                                        <th scope="col" width="35px">
                                            <div class="erp_form__grid_th_title">Sr.</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                <input id="product_barcode_id" readonly type="hidden" class="product_barcode_id form-control erp-form-control-sm">
                                                <input id="uom_id" readonly type="hidden" class="uom_id form-control erp-form-control-sm">
                                                <input id="constants_id" readonly type="hidden" class="constants_id form-control erp-form-control-sm">
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
                                            <div class="erp_form__grid_th_title">Purchase Unit</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="purchase_unit" type="text" class="tblGridCal_purchase_unit validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Percentage</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="percentage" type="text" class="tblGridCal_percentage validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Cost Price</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="cost_price" type="text" class="tblGridCal_cost_price validNumber validOnlyNumber tb_moveIndex form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Sale Type</div>
                                            <div class="erp_form__grid_th_input">
                                                <select class="pd_sale_type tb_moveIndex form-control erp-form-control-sm" id="pd_sale_type" name="pd_sale_type">
                                                    <option value="0">Select</option>
                                                        @foreach($data['sale_type'] as $sale_type)
                                                            <option value="{{$sale_type->constants_id}}">{{$sale_type->constants_value}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Ingredient Type</div>
                                            <div class="erp_form__grid_th_input">
                                                <select id="pd_ingredient_type" class="pd_ingredient_type tb_moveIndex form-control erp-form-control-sm">
                                                    <option value="0">Select</option>
                                                    @foreach($data['ingrediant_type'] as $ingrediant_type)
                                                        <option value="{{$ingrediant_type->constants_id}}">{{$ingrediant_type->constants_value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="erp_form__grid_th_title">Remarks</div>
                                            <div class="erp_form__grid_th_input">
                                                <input id="remarks" type="text" class="tblGridCal_remarks form-control erp-form-control-sm">
                                            </div>
                                        </th>
                                        <th scope="col" width="48">
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
                                                    <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][product_id]" data-id="product_id" value="{{isset($dtl->product->product_id)?$dtl->product->product_id:""}}" class="product_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][uom_id]" data-id="uom_id" value="{{isset($dtl->uom->uom_id)?$dtl->uom->uom_id:""}}" class="uom_id form-control erp-form-control-sm handle" readonly>
                                                    <input type="hidden" name="pd[{{$loop->iteration}}][constants_id]" data-id="constants_id" value="{{isset($dtl->constants->constants_id)?$dtl->constants->constants_id:""}}" class="constants_id form-control erp-form-control-sm handle" readonly>
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
                                                <td><input type="text" data-id="quantity" name="pd[{{$loop->iteration}}][quantity]" value="{{$dtl->item_formulation_dtl_quantity}}" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="purchase_unit" name="pd[{{$loop->iteration}}][purchase_unit]" value="{{$dtl->item_formulation_dtl_purchase_unit}}" class="tblGridCal_purchase_unit tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="percentage" name="pd[{{$loop->iteration}}][percentage]" value="{{$dtl->item_formulation_dtl_percentage}}" class="tblGridCal_percentage tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td><input type="text" data-id="cost_price" name="pd[{{$loop->iteration}}][cost_price]" value="{{$dtl->item_formulation_dtl_cost_price}}" class="tblGridCal_cost_price tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
                                                <td>
                                                    <input readonly type="text" data-id="pd_sale_type" value="{{isset($dtl->contants_sale_type_id) ? $dtl->constants['constants_value'] : ''}}" class="pd_sale_type field_readonly tb_moveIndex form-control erp-form-control-sm">
                                                    <input type="hidden" data-id="pd_sale_type" name="pd[{{ $loop->iteration }}][pd_sale_type]" value="{{$dtl->contants_sale_type_id}}" class="pd_sale_type">
                                                </td>
                                                <td>
                                                    <input readonly type="text" data-id="pd_ingredient_type" value="{{isset($dtl->contants_ingredient_type_id) ? $dtl->constants['constants_value'] : ''}}" class="pd_ingredient_type field_readonly tb_moveIndex form-control erp-form-control-sm">
                                                    <input type="hidden" data-id="pd_ingredient_type" name="pd[{{ $loop->iteration }}][pd_ingredient_type]" value="{{$dtl->contants_ingredient_type_id}}" class="pd_ingredient_type">
                                                </td>
                                                <td><input type="text" data-id="remarks" name="pd[{{$loop->iteration}}][remarks]" value="{{$dtl->item_formulation_dtl_remarks}}" class="tblGridCal_remarks tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>
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
                                            <td class="total_grid_pu">
                                                <input value="0.000" readonly type="text" id="total_qty" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td class="total_grid_perc">
                                                <input value="0.000" readonly type="text" id="total_qty" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td class="total_grid_cost">
                                                <input value="0.000" readonly type="text" id="total_qty" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <label class="col-lg-2 erp-col-form-label">Notes:</label>
                        <div class="col-lg-10">
                            <textarea type="text" rows="2" maxlength="100"  name="formulation_remarks" class="moveIndex form-control erp-form-control-sm">{{isset($remarks)?$remarks:""}}</textarea>
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

    <script src="{{ asset('js/pages/js/table-calculations-new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/formulation.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/table-calculations.js') }}" type="text/javascript"></script>
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
                'id':'purchase_unit',
                'fieldClass':'tblGridCal_purchase_unit validNumber validOnlyNumber tb_moveIndex'
            },
            {
                'id':'percentage',
                'fieldClass':'tblGridCal_percentage validNumber validOnlyNumber tb_moveIndex'
            },
            {
                'id':'cost_price',
                'fieldClass':'tblGridCal_cost_price validNumber validOnlyNumber tb_moveIndex',
                'require':true,
                'message':'Enter Product Cost',
            },
            {
                'id':'pd_sale_type',
                'fieldClass': 'pd_sale_type field_readonly',
                'message': 'Select Sale-Type',
                'require': true,
                type:'select'
            },
            {
                'id':'pd_ingredient_type',
                'fieldClass': 'pd_ingredient_type field_readonly',
                'message': 'Select Ingrediant-Type',
                'require': true,
                type:'select'
            },
            {
                'id':'remarks',
                'fieldClass':'tblGridCal_remarks'
            }
        ];
        var arr_hidden_field = ['product_id','product_barcode_id','uom_id','constants_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
@endsection
