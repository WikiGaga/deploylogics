@extends('layouts.template')
@section('title', 'Create Product')
@permission($data['permission'])
@section('pageCSS')
@endsection
@section('content')
    @php
        if (Session::has('ProLastData')){
            $product_name = isset(Session::get('ProLastData')['product_name'])?Session::get('ProLastData')['product_name']:'';
            $product_arabic_name = isset(Session::get('ProLastData')['product_arabic_name'])?Session::get('ProLastData')['product_arabic_name']:'';
            $product_group = isset(Session::get('ProLastData')['product_group'])?Session::get('ProLastData')['product_group']:'';
            $product_type_group = isset(Session::get('ProLastData')['product_item_type'])?Session::get('ProLastData')['product_item_type']:'';
        }
    @endphp
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <form id="product_form" class="kt-form" method="post" action="{{ action('Purchase\ProductController@store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value='product_add' id="form_type">
                    <div class="kt-portlet__body">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="erp-page--title">
                                            {{$data['document_code']}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Product Name:<span class="required">* </span></label>
                                    <div class="col-lg-6">
                                        <input type="text" name="product_name" id="product_name" class="form-control erp-form-control-sm medium_text" value="{{isset($product_name)?$product_name:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Product Short Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="product_short_name" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Arabic Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" dir="auto" name="product_arabic_name" id="product_arabic_name" class="form-control erp-form-control-sm medium_text" value="{{isset($product_arabic_name)?$product_arabic_name:""}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Arabic Short Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" dir="auto" name="product_arabic_short_name" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Status:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                <input type="checkbox" checked="checked" name="product_entry_status">
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Can Sale:</label>
                                    <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                <input type="checkbox" checked="checked" name="product_can_sale">
                                                <span></span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#general_information" role="tab">General Information</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " data-toggle="tab" href="#barcodes" role="tab">Barcodes</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="general_information" role="tabpanel">
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Product Group:<span class="required">* </span></label>
                                            <div class="col-lg-6" id="product_control_group_block">
                                                <div class="erp-select2 form-group">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_control_group" name="product_control_group">
                                                        <option value="0">Select</option>
                                                        @php $product_group = isset($product_group)?$product_group:""; @endphp
                                                        @foreach($data['group_item'] as $group_item)
                                                            <option value="{{$group_item->group_item_id}}" {{$product_group==$group_item->group_item_id?"selected":""}}>{{$group_item->group_item_name_string}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Product Type Group:</label>
                                            <div class="col-lg-6" id="product_item_type_block">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_item_type" name="product_item_type">
                                                        <option value="0">Select</option>
                                                        @php $product_type_group = isset($product_type_group)?$product_type_group:""; @endphp
                                                        @foreach($data['item_type'] as $item_type)
                                                            <option value="{{$item_type->product_type_group_id}}"  @if($item_type->product_type_group_id == request()->session()->get('product_item_type')) selected="selected" @endif>{{$item_type->product_type_group_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Product Type:</label>
                                            <div class="col-lg-6" id="product_type_block">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_type" name="product_type">
                                                        <option value="0">Select</option>
                                                        @foreach($data['product_type'] as $product_type)
                                                            <option value="{{$product_type->product_type_id}}">{{$product_type->product_type_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Supplier :</label>
                                            <div class="col-lg-6" id="product_country_block">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="supplier_id" name="supplier_id">
                                                        <option value="">Select</option>
                                                        @foreach($data['suppliers'] as $supplier)
                                                            <option value="{{$supplier->supplier_id}}">{{$supplier->supplier_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Manufacturer:</label>
                                            <div class="col-lg-6" id="product_manufacturer_block">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_manufacturer" name="product_manufacturer">
                                                        <option value="0">Select</option>
                                                        @foreach($data['manufacturer'] as $manufacturer)
                                                            <option value="{{$manufacturer->manufacturer_id}}">{{$manufacturer->manufacturer_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Country :</label>
                                            <div class="col-lg-6" id="product_country_block">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_country" name="product_country">
                                                        <option value="">Select</option>
                                                        @foreach($data['country'] as $country)
                                                            <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Brand Name:</label>
                                            <div class="col-lg-6" id="product_brand_name_block">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_brand_name" name="product_brand_name">
                                                        <option value="0">Select</option>
                                                        @foreach($data['brand'] as $brand)
                                                            <option value="{{$brand->brand_id}}">{{$brand->brand_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Specification Tags:</label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_specification_tags" multiple name="product_specification_tags[]">
                                                        @foreach($data['specific'] as $tag)
                                                            <option value="{{$tag->tags_id}}">{{$tag->tags_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Item Tags:</label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select class="form-control kt-select2 erp-form-control-sm" id="product_item_tags" multiple name="product_item_tags[]">
                                                        @foreach($data['item'] as $tag)
                                                            <option value="{{$tag->tags_id}}">{{$tag->tags_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Demand Active Status:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" checked="checked" name="product_demand_active_status">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Warranty Status:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" name="product_warranty_status">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Warranty Period:</label>
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <div class="erp-select2" style="width: 66.66%;">
                                                        <select class="form-control erp-form-control-sm" id="product_warranty_period" name="product_warranty_period">
                                                            <option value="0">Select</option>
                                                            @foreach($data['warranty_period'] as $wp)
                                                            <option value="{{$wp->warrenty_period_id}}">{{$wp->warrenty_period_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div style="width: 33.33%;">
                                                        <input type="text" id="product_warranty_mode" name="product_warranty_mode" class="form-control erp-form-control-sm mob_no validNumber">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Perishable:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" name="product_perishable">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Tracing Days:</label>
                                            <div class="col-lg-6">
                                                <input class="form-control erp-form-control-sm mob_no validNumber text-left" placeholder="(Days Before Expiry)" id="product_tracing_days" name="product_tracing_days">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Batch No Required:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" name="product_batch_no_required">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Expiry Return Allow:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" name="product_expiry_return_allow">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Damage Return Allow:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" name="product_damage_return_allow">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Expiry Required:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" name="product_expiry_required">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Length Calculate:</label>
                                            <div class="col-lg-6">
                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                    <label>
                                                        <input type="checkbox" class="product_barcode_length_calc" name="product_barcode_length_calc">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Expiry Base On:</label>
                                            <div class="col-lg-6">
                                                <div class="erp-select2">
                                                    <select type="text" class="form-control erp-form-control-sm kt-select2" id="product_expiry_base_on" name="product_expiry_base_on">
                                                        <option value="0">Select</option>
                                                        <option value="Expire Date">Expire Date</option>
                                                        <option value="Production Date">Production Date</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Shelf Life Minimum%:</label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber" name="product_shelf_life_minimum">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Notes:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control erp-form-control-sm large_text" rows="3" name="product_remarks"></textarea>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label><b>Product Life:</b></label>
                                        <div class="form-group-block">
                                            <div class="erp_form___block">
                                                <div class="table-scroll form_input__block">
                                                    <table data-prefix="pd" class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                        <thead class="erp_form__grid_header">
                                                            <tr>
                                                                <th scope="col" width="5%">
                                                                    <div class="erp_form__grid_th_title">Sr.</div>
                                                                    <div class="erp_form__grid_th_input">
                                                                        <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                                        <input id="country" readonly type="hidden" class="country form-control erp-form-control-sm">
                                                                    </div>
                                                                </th>
                                                                <th scope="col" width="30%">
                                                                    <div class="erp_form__grid_th_title">Country</div>
                                                                    <div class="erp_form__grid_th_input">
                                                                        <select id="product_life_country_name" class="product_life_country_name form-control erp-form-control-sm" data-readonly="true" data-convert="input">
                                                                            <option value="">Select</option>
                                                                            @foreach($data['country'] as $country)
                                                                                <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </th>
                                                                <th scope="col" width="30%">
                                                                    <div class="erp_form__grid_th_title">Period Type</div>
                                                                    <div class="erp_form__grid_th_input">
                                                                        <select id="period_type" class="period_type form-control erp-form-control-sm" data-readonly="true" data-convert="input">
                                                                            <option value="">Select</option>
                                                                            @foreach($data['warranty_period'] as $wp)
                                                                                <option value="{{$wp->warrenty_period_name}}">{{$wp->warrenty_period_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </th>
                                                                <th scope="col" width="30%">
                                                                    <div class="erp_form__grid_th_title">Period</div>
                                                                    <div class="erp_form__grid_th_input">
                                                                        <input id="period" type="text" class="period large_no validNumber validOnlyNumber form-control erp-form-control-sm">
                                                                    </div>
                                                                </th>
                                                                <th scope="col" width="5%">
                                                                    <div class="erp_form__grid_th_title">Action</div>
                                                                    <div class="erp_form__grid_th_btn">
                                                                        <button type="button" class="add_data tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                                            <i class="la la-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="erp_form__grid_body"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}

                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label><b>Product Purchase FOC:</b></label>
                                        @include('purchase.product.purchase_foc')
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="barcodes" role="tabpanel">
                                <div id="kt_repeater_barcode">
                                    <div class="form-group row">
                                        <div data-repeater-list="product_barcode_data" class="col-lg-12">
                                            <div data-repeater-item class="kt-margin-b-10 barcode" item-id="1">
                                                <div class="form-group row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <label class="col-lg-4 erp-col-form-label">Product Barcode:<span class="required">* </span></label>
                                                            <div class="col-lg-8">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control erp-form-control-sm small_text barcode_repeat_b" name="v_product_barcode">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Weight Apply:</label>
                                                                    <div class="col-lg-6">
                                                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                            <label>
                                                                                <input type="checkbox" class="product_barcode_weight_apply" name="product_barcode_weight_apply">
                                                                                <span></span>
                                                                            </label>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Base Barcode</label>
                                                                    <div class="col-lg-6">
                                                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                            <label>
                                                                                <input type="checkbox" class="base_barcode" name="base_barcode" checked>
                                                                                <span></span>
                                                                            </label>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Length Calculate:</label>
                                                                    <div class="col-lg-6">
                                                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                            <label>
                                                                                <input type="checkbox" class="product_barcode_length_calc" name="product_barcode_length_calc">
                                                                                <span></span>
                                                                            </label>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php $current_bra_purc_rate = 0; @endphp
                                                        <div class="row">
                                                            <label class="col-lg-3 erp-col-form-label">Barcode Print:</label>
                                                            <div class="col-lg-9">
                                                                <div class="form-group  input-group-sm">
                                                                    <div class="input-group input-group-sm">
                                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-money"></i></span></div>
                                                                        <input type="text" value="{{number_format($current_bra_purc_rate,3)}}" class="form-control medium_no label_print_price validNumber validOnlyFloatNumber" >
                                                                        <div class="input-group-prepend"><span class="input-group-text">
                                                                                        <i class="fa fa-stopwatch"></i>
                                                                                    </span></div>
                                                                        <input type="number" value="1" class="form-control label_print_total validNumber ">
                                                                        <div class="input-group-append" >
                                                                            <div class="dropdown">
                                                                                <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" style="border: 1px solid #e9ebf1;padding: 6.75px;">
                                                                                    <i class="la la-barcode"></i>
                                                                                </button>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <div class="dropdown-item create_print_barcode" data-id="1">Barcode Label</div>
                                                                                    <div class="dropdown-item create_print_barcode" data-id="2">Shelf Label</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm">
                                                                    <i class="la la-remove"></i>
                                                                </a>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar_1" >
                                                                    <div class="kt-avatar__holder"  style="background-image: url(/assets/media/project-logos/7.png)"></div>
                                                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                                                        <i class="fa fa-pen"></i>
                                                                        <input type="file" name="product_image" class="product_img" accept="image/png, image/jpg, image/jpeg">
                                                                    </label>
                                                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                                                        <i class="fa fa-times"></i>
                                                                    </span>
                                                                </div>
                                                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>{{-- end row--}}
                                                <ul class="nav nav-tabs col-lg-12" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active uom_packing" data-toggle="tab" href="#uom_packing" role="tab">UOM & Packing</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link rate" data-toggle="tab" href="#rate" role="tab">Rate</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link inventory_shelf_stock" data-toggle="tab" href="#inventory_shelf_stock" role="tab">Inventory & Shelf Stock</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link tax" data-toggle="tab" href="#tax" role="tab">Tax</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link purchase_foc" data-toggle="tab" href="#purchase_foc" role="tab">Purchase & FOC</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content col-lg-12">
                                                    <div class="tab-pane active uom_packing_content" id="uom_packing" role="tabpanel">
                                                        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblPack">
                                                            <thead>
                                                            <tr>
                                                                <th width="20%">UOM <span class="required">* </span></th>
                                                                <th width="20%">Packing <span class="required">* </span></th>
                                                                <th width="20%">Color</th>
                                                                <th width="20%">Size</th>
                                                                <th width="20%">Variant</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="erp-select2 form-group">
                                                                        <select class="form-control erp-form-control-sm kt-select2 uom_packing_uom" name="uom_packing_uom" id="uom_packing_uom">
                                                                            <option value="0">Select</option>
                                                                            @foreach($data['uom'] as $uom )
                                                                                <option value="{{$uom->uom_id}}" {{$uom->uom_name=='PCS'?'selected':''}}>{{$uom->uom_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber barcode_packing" name="product_barcode_packing" value="1" readonly>
                                                                    </div>
                                                                </td>
                                                                <td class="tag_select2_block">
                                                                    <div class="erp-select2">
                                                                        <select class="form-control kt-select2 erp-form-control-sm tag-select2" multiple name="uom_packing_color_tag">
                                                                            @foreach($data['color'] as $tag)
                                                                                <option value="{{$tag->color_id}}">{{$tag->color_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td class="tag_select2_block">
                                                                    <div class="erp-select2">
                                                                        <select class="form-control kt-select2 erp-form-control-sm tag-select2"  multiple name="uom_packing_size_tag">
                                                                            @foreach($data['size'] as $tag)
                                                                                <option value="{{$tag->size_id}}">{{$tag->size_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control erp-form-control-sm small_text" name="uom_packing_other_tag">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane rate_content" id="rate" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-lg-12 text-center">
                                                                <div class="product-barcode-innertabs--title">Sale Rate</div>
                                                            </div>
                                                        </div>{{-- end row--}}
                                                        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblR">
                                                            <thead>
                                                                <tr>
                                                                    <th width="40%">Branch Name</th>
                                                                    @php
                                                                        $rate_category_width = 60/(int)count($data['rate_category']);
                                                                        $iii = 2;
                                                                    @endphp
                                                                    @foreach($data['rate_category'] as $key=>$rate_category)
                                                                        <th data-id="{{$key+$iii}}" width="{{$rate_category_width}}%"><div style="/*display: inline-block;position: relative;top: 9px;*/">{{$rate_category->rate_category_name}}</div> <input type="text" id="SaleRateApplyAll" value="0" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="background: #f0f8ff;/*width: 75px;display: inline-block;float: right;*/"></th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php $sr = 0; @endphp
                                                            @foreach($data['branch'] as $key=>$branch)
                                                                <tr>
                                                                    <td><input type="hidden" class="branch_R" name="rate_branchId_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
                                                                    @php $r = 0; @endphp
                                                                    @foreach($data['rate_category'] as $rate_category)
                                                                        <td>
                                                                            <input type="hidden" class="rate_R" id="rate_R_{{$sr}}_{{$r}}" name="rate_categoryId_{{$sr}}_{{$r}}" value="{{$rate_category->rate_category_id}}">
                                                                            <input type="text" class="sale_rate_rate form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber" name="rate_categoryVal_{{$sr}}_{{$r}}">
                                                                        </td>
                                                                    @php $r++; @endphp
                                                                    @endforeach
                                                                </tr>
                                                                @php $sr++; @endphp
                                                            @endforeach

                                                            </tbody>
                                                        </table>
                                                        {{-- end row--}}
                                                        <div class="row">
                                                            <div class="col-lg-12 text-center">
                                                                <div class="product-barcode-innertabs--title">Purchase Rate</div>
                                                            </div>
                                                        </div>{{-- end row--}}
                                                        <div class="form-group row d-none">
                                                            <div class="col-lg-6">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Purchase Rate Base:</label>
                                                                    <div class="col-lg-6">
                                                                        <div class="erp-select2">
                                                                            <select class="form-control erp-form-control-sm kt-select2 barcode_rate_purchase_rate_base" name="barcode_rate_purchase_rate_base">
                                                                                <option value="">Select</option>
                                                                                <option value="Fix Rate">Fix Rate</option>
                                                                                <option value="Supplier Rate">Supplier Rate</option>
                                                                                <option value="None">None</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6" style="display:none;">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Purchase Rate Type:</label>
                                                                    <div class="col-lg-6">
                                                                        <div class="erp-select2">
                                                                            <select class="form-control erp-form-control-sm kt-select2 barcode_rate_purchase_rate_type" name="barcode_rate_purchase_rate_type">
                                                                                <option value="">Select</option>
                                                                                <option value="Cost Rate">Cost Rate</option>
                                                                                <option value="Actual Cost Rate">Actual Cost Rate</option>
                                                                                <option value="Avg Rate">Avg Rate</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6" style="display:block;">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Cost Rate:</label>
                                                                    <div class="col-lg-6">
                                                                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber text-left" name="product_barcode_cost_rate" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>{{-- end row--}}
                                                        <div class="form-group row d-none">
                                                            <div class="col-lg-6">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Minimum Profit Margin:</label>
                                                                    <div class="col-lg-6">
                                                                        <input type="text" class="form-control erp-form-control-sm validNumber" maxlength="3" name="barcode_minimum_profit_margin" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6" style="display:block;">
                                                                <div class="row">
                                                                    <label class="col-lg-6 erp-col-form-label">Purchase Rate:</label>
                                                                    <div class="col-lg-6">
                                                                        <input type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber text-left" name="barcode_rate_purchase_rate" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>{{-- end row--}}

                                                        @include('purchase.product.purchase_rate')
                                                    </div>
                                                    <div class="tab-pane inventory_shelf_stock_content" id="inventory_shelf_stock" role="tabpanel">
                                                        <div class="row">
                                                            <div class="col-lg-12 text-center">
                                                                <div class="product-barcode-innertabs--title">Stock Limits</div>
                                                            </div>
                                                        </div>{{-- end row--}}
                                                        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblSL">
                                                            <thead>
                                                                <tr>
                                                                    <th width="20%" rowspan="2" class="text-middle">Branch Name</th>
                                                                    <th width="15%" rowspan="2" class="text-middle text-center">Negative Stock Allow</th>
                                                                    <th width="15%" rowspan="2" class="text-middle text-center">Re Order Qty Level</th>
                                                                    <th width="20%" colspan="3" class="text-center">Stock Limit</th>
                                                                    <th width="15%" rowspan="2" class="text-middle text-center">Stock Limit Apply</th>
                                                                    <th width="15%" rowspan="2" class="text-middle text-center">Active Status</th>
                                                                </tr>
                                                                <tr class="height:25px;">
                                                                    <th width="6.66%" class="text-center">Max</th>
                                                                    <th width="6.66%" class="text-center">Min</th>
                                                                    <th width="6.66%" class="text-center">Consumption Days</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php $sr = 0; @endphp
                                                            @foreach($data['branch'] as $key=>$branch)
                                                                <tr>
                                                                    <td><input type="hidden" class="branch_SL" name="branch_id_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
                                                                    <td class="text-center">
                                                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                            <label>
                                                                                <input type="checkbox" name="stock_limit_neg_stock_{{$sr}}">
                                                                                <span></span>
                                                                            </label>
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber" name="stock_qty_level_{{$sr}}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber" name="stock_max_limit_{{$sr}}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber" name="stock_min_limit_{{$sr}}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control erp-form-control-sm mob_no validNumber" name="stock_consumption_days_{{$sr}}">
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                            <label>
                                                                                <input type="checkbox" name="stock_limit_apply_status_{{$sr}}">
                                                                                <span></span>
                                                                            </label>
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                            <label>
                                                                                <input type="checkbox" name="stock_status_{{$sr}}">
                                                                                <span></span>
                                                                            </label>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @php $sr++; @endphp
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                        <div class="row">
                                                            <div class="col-lg-12 text-center">
                                                                <div class="product-barcode-innertabs--title">Shelf Stock Limits</div>
                                                            </div>
                                                        </div>{{-- end row--}}
                                                        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblSSL">
                                                            <thead>
                                                            <tr>
                                                                <th width="25%">Branch Name</th>
                                                                <th width="20%">Stock Location</th>
                                                                <th width="19%">Saleman</th>
                                                                <th width="18%">Max Qty</th>
                                                                <th width="18%">Min Qty</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php $sr = 0; @endphp
                                                            @foreach($data['branch'] as $key=>$branch)
                                                            <tr>
                                                                <td><input type="hidden" class="branch_SSL" name="stock_branch_id_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
                                                                <td>
                                                                    <div class="erp-select2 form-group">
                                                                        <select class="form-control kt-select2 erp-form-control-sm shelf_stock_location" name="shelf_stock_location_{{$sr}}">
                                                                            <option value="0">Select</option>
                                                                            @foreach($data['display_location'] as $display_location)
                                                                                @if($display_location->branch_id == $branch->branch_id)
                                                                                    <option value="{{$display_location->display_location_id}}" {{Auth::user()->branch_id == $display_location->display_location_id?'selected':''}}>{{$display_location->display_location_name_string}}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="erp-select2">
                                                                        <select class="form-control erp-form-control-sm kt-select2 shelf_stock_salesman" name="shelf_stock_salesman_{{$sr}}">
                                                                            <option value="">Select</option>
                                                                            @foreach($data['users'] as $user)
                                                                                @if($user->branch_id == $branch->branch_id)
                                                                                    <option value="{{$user->id}}" {{Auth::user()->id == $user->id?'selected':''}}>{{$user->name}}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" name="shelf_stock_max_qty_{{$sr}}"></td>
                                                                <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" name="shelf_stock_min_qty_{{$sr}}"></td>
                                                            </tr>
                                                                @php $sr++; @endphp
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane tax_content" id="tax" role="tabpanel">
                                                        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblT">
                                                            <thead>
                                                            <tr>
                                                                <th width="55%">Branch Name</th>
                                                                <th width="30%"><div style="display: inline-block;position: relative;top: 9px;">Tax Value</div>  <input type="text" id="TaxValueApplyAll" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber" style="width: 100px;display: inline-block;float: right;"></th>
                                                                <th width="15%">
                                                                    <div class="kt-checkbox-inline">
                                                                        <div style="display: inline-block;">Apply Tax</div>
                                                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" style="float: right">
                                                                            <input type="checkbox" id="TaxStatusApplyAll">
                                                                            <span></span>
                                                                        </label>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php $sr = 0; @endphp
                                                            @foreach($data['branch'] as $key=>$branch)
                                                            <tr>
                                                                <td><input type="hidden" class="branch_T" name="tax_branch_id_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>

                                                                <td>
                                                                    <input type="text" class="form-control erp-form-control-sm tax_value mob_no validNumber validOnlyFloatNumber" name="tax_tax_value_{{$sr}}">
                                                                </td>
                                                                <td>
                                                                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                        <label>
                                                                            <input type="checkbox" class="tax_status" name="tax_tax_status_{{$sr}}">
                                                                            <span></span>
                                                                        </label>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                                @php $sr++; @endphp
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane purchase_foc_content" id="purchase_foc" role="tabpanel">
                                                        Product Purchase FOC
                                                    </div>
                                                </div>
                                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-right">
                                            <div class="btn-group" role="group" aria-label="First group">
												<button type="button" data-repeater-create="ppp" class="btn btn-success btn-sm"><i class="la la-plus"></i></button>
											</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-success moveIndexSubmit moveIndex">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>

    <!-- end:: Content -->
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script>
        if(localStorage.getItem('product_group')){
            $('#product_control_group').val(localStorage.getItem('product_group')).change();
            localStorage.removeItem('product_group');
        }
        $(document).ready(function(){
            $("#product_control_group").change(function(){
                var group =  $(this).val();
                if(group) {
                    $.ajax(
                    {
                        type:'GET',
                        url:'/product/form-itemtype-data/'+ group,
                        success: function(response, data)
                        {
                            $("#product_item_type").val(0).trigger("change")
                            if(response.data.product_type_group_id != undefined && response.data.product_type_group_id != "" && response.data.product_type_group_id != null){
                                $("#product_item_type").val(response.data.product_type_group_id).trigger("change")
                            }
                        }
                    });
                }
            });
        });
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'product_life_country_name',
                'fieldClass':'product_life_country_name field_readonly',
                'message':'Select Country Name',
                'require':true,
                'readonly':true,
                'type':'select',
                'convertType':'input',
                'getVal':'text',
                'defaultValue':true,
            },
            {
                'id':'period_type',
                'fieldClass':'period_type field_readonly',
                'message':'Select Period Type',
                'require':true,
                'readonly':true,
                'type':'select',
                'convertType':'input',
                'getVal':'text',
                'defaultValue':true,
            },
            {
                'id':'period',
                'fieldClass':'period large_no validNumber validOnlyFloatNumber',
                'message':'Enter period',
                'require':true
            },
        ]
        var arr_hidden_field = ['country'];
        $('.erp_form__grid_header #product_life_country_name').on('change', function() {
            $('.erp_form__grid_header>tr>th:first-child>div>input#country').val($(this).val());
        })
    </script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    {{--<script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/table-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/data-repeated.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/product-create.js') }}" type="text/javascript"></script>


    <script>
        $('.validNumber').keypress(validateNumber);
        $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
        // .create_print_shelf_barcode
        $(document).on('click','.create_print_barcode',function(){
            var data_id = $(this).attr('data-id');
            var product_name = $('#product_name').val();
            var product_arabic_name = $('#product_arabic_name').val();
            var barcode = $(this).parents('.barcode').find('.barcode_repeat_b').val();
            if(data_id != 1){
                if(data_id != 2){
                    toastr.error("Something wrong...");
                    return false;
                }
            }
            if(data_id == 1 || data_id == 2){
                if(product_name.length == 0){
                    toastr.error("Required field product name");
                    return false;
                }
                if(barcode.length == 0){
                    toastr.error("Required field barcode");
                    return false;
                }
            }
            if(data_id == 2){
                if(product_arabic_name.length == 0){
                    toastr.error("Required field product arabic name");
                    return false;
                }
            }
            var formData = {
                data_id : data_id,
                product_name : product_name,
                product_arabic_name : product_arabic_name,
                barcode : barcode,
                rate : $(this).parents('.barcode').find('.label_print_price').val(),
                qty : $(this).parents('.barcode').find('.label_print_total').val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : '{{action('Purchase\ProductController@BarcodeTagPrintGenerate')}}',
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    console.log(response)
                    if(response['status'] == 'success'){
                        toastr.success("Barcode label ready..");
                        window.open(response['data']['url'], "barcode");
                    }else{

                    }
                }
            });
        });

        //------tax function-----
        $(document).on('click','.tax_status',function(){
            var val = $(this).is(":checked");
            if(val == true){
                $(this).parents('tr').find('.tax_value').attr('required',true);
            }else{
                $(this).parents('tr').find('.tax_value').attr('required',false);
            }
        });
    </script>
@endsection
@endpermission
