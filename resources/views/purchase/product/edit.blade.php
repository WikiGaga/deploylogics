@extends('layouts.template')
@section('title', ucfirst($data['page_data']['type']).' '.$data['page_data']['title'])

@section('pageCSS')
@endsection
@permission($data['permission'])
@section('content')
    <!-- begin:: Content -->
    @php
        $case = $data['page_data']['type'];
        $url = "";
        if($case == 'edit'){
            $url = action('Purchase\ProductController@update', $data['current']->product_id);
        }
    @endphp
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <div class="kt-portlet__body">
                <!--begin::Form-->
                <form id="product_form" class="kt-form" method="post" action="{{ $url }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value='product_edit' id="form_type">
                    <input type="hidden" value='{{$data['page_data']['title']}}' id="document_title">
                    <input type="hidden" value='product' id="document_name">
                    <input type="hidden" value='{{$data['current']->product_id}}' id="document_id">
                    <input type="hidden" value='product' id="prefix_url">
                    <div class="kt-portlet__body">
                        <div class="row form-group-block">
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="erp-page--title">
                                            {{$data['current']->product_code}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4 text-right">
                                <a href="javascript:;" class="btn btn-sm btn-primary product_card_detail" data-id="{{$data['current']->product_id}}" data-val="{{$data['current']->product_name}}">Product Detail</a>
                                <a class="product_card_activity_report btn btn-sm btn-primary " href="javascript:;" data-toggle="modal" data-id="{{$data['current']->product_id}}" data-val="{{$data['current']->product_name}}" data-barcode="">Product Activity</a>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Product Name:<span class="required">* </span></label>
                                    <div class="col-lg-6">
                                        <input type="text" value="{{$data['current']->product_name}}" name="product_name" id="product_name" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Product Short Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" value="{{$data['current']->product_short_name}}"  name="product_short_name" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                        </div>{{-- end row--}}
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Arabic Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" value="{{$data['current']->product_arabic_name}}" dir="auto" name="product_arabic_name" id="product_arabic_name" class="form-control erp-form-control-sm medium_text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <label class="col-lg-6 erp-col-form-label">Arabic Short Name:</label>
                                    <div class="col-lg-6">
                                        <input type="text" value="{{$data['current']->product_arabic_short_name}}" dir="auto" name="product_arabic_short_name" class="form-control erp-form-control-sm medium_text">
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
                                                <input type="checkbox" name="product_entry_status" {{$data['current']->product_entry_status==1?"checked":""}}>
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
                                                <input type="checkbox"  name="product_can_sale" {{$data['current']->product_can_sale==1?"checked":""}}>
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
                                <a class="nav-link" data-toggle="tab" href="#barcodes" role="tab">Barcodes</a>
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
                                                        @foreach($data['group_item'] as $group_item)
                                                            <option value="{{$group_item->group_item_id}}" {{$data['current']->group_item_id==$group_item->group_item_id?"selected":""}}>{{$group_item->group_item_name_string}}</option>
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
                                                        @foreach($data['item_type'] as $item_type)
                                                            <option value="{{$item_type->product_type_group_id}}" {{$data['current']->product_item_type==$item_type->product_type_group_id?"selected":""}}>{{$item_type->product_type_group_name}}</option>
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
                                                            <option value="{{$product_type->product_type_id}}" {{$data['current']->product_type_id == $product_type->product_type_id?"selected":""}}>{{$product_type->product_type_name}}</option>
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
                                                            <option value="{{$supplier->supplier_id}}" {{$data['current']->supplier_id == $supplier->supplier_id?"selected":""}}>{{$supplier->supplier_name}}</option>
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
                                                            <option value="{{$manufacturer->manufacturer_id}}" {{$data['current']->product_manufacturer_id==$manufacturer->manufacturer_id?"selected":""}}>{{$manufacturer->manufacturer_name}}</option>
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
                                                            <option value="{{$country->country_id}}" {{$data['current']->country_id==$country->country_id?"selected":""}}>{{$country->country_name}}</option>
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
                                                            <option value="{{$brand->brand_id}}" {{$data['current']->product_brand_id==$brand->brand_id?"selected":""}}>{{$brand->brand_name}}</option>
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
                                                        @php $col = []; @endphp
                                                        @foreach($data['current']->specification_tags as $variant)
                                                            @php array_push($col,$variant->tag_id); @endphp
                                                        @endforeach
                                                        @foreach($data['specific'] as $tag)
                                                            <option value="{{ $tag->tags_id }}" {{ (in_array($tag->tags_id, $col)) ? 'selected' : '' }}>{{$tag->tags_name}}</option>
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
                                                        @php $col = []; @endphp
                                                        @foreach($data['current']->item_tags as $variant)
                                                            @php array_push($col,$variant->tag_id); @endphp
                                                        @endforeach
                                                        @foreach($data['item'] as $tag)
                                                            <option value="{{ $tag->tags_id }}" {{ (in_array($tag->tags_id, $col)) ? 'selected' : '' }}>{{$tag->tags_name}}</option>
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
                                                        <input type="checkbox" name="product_demand_active_status" {{$data['current']->product_demand_active_status==1?"checked":""}}>
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
                                                        <input type="checkbox" {{$data['current']->product_warranty_status==1?"checked":""}} name="product_warranty_status">
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
                                                            <option value="{{$wp->warrenty_period_id}}" {{$data['current']->product_warranty_period_id==$wp->warrenty_period_id?"selected":""}}>{{$wp->warrenty_period_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div style="width: 33.33%;">
                                                        <input type="text" id="product_warranty_mode" value="{{$data['current']->product_warranty_period_mode}}" name="product_warranty_mode" class="form-control erp-form-control-sm mob_no validNumber">
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
                                                        <input type="checkbox" {{$data['current']->product_perishable==1?"checked":""}} name="product_perishable">
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
                                                <input class="form-control erp-form-control-sm mob_no validNumber text-left" value="{{$data['current']->product_tracing_days}}" placeholder="(Days Before Expiry)" id="product_tracing_days" name="product_tracing_days">
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
                                                        <input type="checkbox" {{$data['current']->product_batch_req==1?"checked":""}} name="product_batch_no_required">
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
                                                        <input type="checkbox" {{$data['current']->product_expiry_return_allow==1?"checked":""}} name="product_expiry_return_allow">
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
                                                        <input type="checkbox" {{$data['current']->product_damage_return_allow==1?"checked":""}} name="product_damage_return_allow">
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
                                                        <input type="checkbox" {{$data['current']->product_expiry_required==1?"checked":""}} name="product_expiry_required">
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
                                                        <input type="checkbox" name="product_barcode_length_calc" {{$data['current']->product_barcode_length_calc==1?"checked":""}}>
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
                                                        <option value="Expire Date" {{$data['current']->product_expiry_base=="Expire Date"?"selected":""}}>Expire Date</option>
                                                        <option value="Production Date" {{$data['current']->product_expiry_base=="Production Date"?"selected":""}}>Production Date</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <label class="col-lg-6 erp-col-form-label">Shelf Life Minimum%:</label>
                                            <div class="col-lg-6">
                                                <input type="text" value="{{$data['current']->product_shelf_life_minimum}}" class="form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber" name="product_shelf_life_minimum">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <label class="col-lg-3 erp-col-form-label">Notes:</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control erp-form-control-sm large_text" rows="3" name="product_remarks">{{$data['current']->product_remarks}}</textarea>
                                    </div>
                                </div>{{-- end row--}}
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label><b>Product Life:</b></label>
                                        <div class="form-group-block">
                                            <div class="erp_form___block">
                                                <div class="table-scroll form_input__block">
                                                    <table data-prefix="pd"  class="table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
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
                                                        <tbody class="erp_form__grid_body">
                                                        @foreach($data['current']->product_life as $pl)
                                                        <tr>
                                                            <td class="handle">
                                                                <input type="text" value="{{$loop->iteration}}" name="pd[{{$loop->iteration}}][sr_no]"  class="form-control erp-form-control-sm handle" readonly>
                                                                <input type="hidden" name="pd[{{$loop->iteration}}][country]" data-id="country" value="{{$pl->country->country_id}}" class="country form-control erp-form-control-sm handle" readonly>
                                                            </td>
                                                            <td><input type="text" readonly name="pd[{{$loop->iteration}}][product_life_country_name]" data-id="product_life_country_name"  value="{{$pl->country->country_name}}" class="product_life_country_name form-control erp-form-control-sm" ></td>
                                                            <td><input type="text" readonly name="pd[{{$loop->iteration}}][period_type]" data-id="period_type"  value="{{$pl->product_life_period_type}}" class="period_type form-control erp-form-control-sm" ></td>
                                                            <td><input type="text" name="pd[{{$loop->iteration}}][period]" data-id="period"  value="{{$pl->product_life_period}}" class="period form-control erp-form-control-sm large_no validNumber validOnlyFloatNumber" ></td>
                                                            <td class="text-center">
                                                                <div class="btn-group btn-group btn-group-sm" role="group">
                                                                    <button type="button" class="btn btn-danger gridBtn del_row"><i class="la la-trash"></i></button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                        </tbody>
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
                                                @foreach($data['current']->product_barcode as $key=>$pb)
                                                    <div data-repeater-item class="kt-margin-b-10 barcode">
                                                        <div class="form-group row">
                                                            <div class="col-lg-6">
                                                                <div class="row">
                                                                    <label class="col-lg-4 erp-col-form-label">Product Barcode:<span class="required">* </span></label>
                                                                    <div class="col-lg-8">
                                                                        <div class="form-group">
                                                                            <input type="text" class="form-control erp-form-control-sm small_text barcode_repeat_b" value="{{ $pb->product_barcode_barcode }}" name="v_product_barcode">
                                                                            <input type="hidden" class="form-control erp-form-control-sm barcode_repeat_b_id" value="{{ $pb->product_barcode_id }}" name="product_barcode_id">
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
                                                                                        <input type="checkbox" class="product_barcode_weight_apply" name="product_barcode_weight_apply"  {{$pb->product_barcode_weight_apply==1?"checked":""}}>
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
                                                                                        <input type="checkbox" class="base_barcode" name="base_barcode"  {{$pb->base_barcode==1?"checked":""}}>
                                                                                        <span></span>
                                                                                    </label>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @php $current_bra_sale_rate = 0; @endphp
                                                                @if(count($pb['sale_rate']) != 0)
                                                                    @foreach($pb['sale_rate'] as $pb_sale_rate)
                                                                        @if($pb_sale_rate['branch_id'] == auth()->user()->branch_id && $pb_sale_rate['product_category_id'] == 2)
                                                                            @php $current_bra_sale_rate = $pb_sale_rate['product_barcode_sale_rate_rate']; @endphp
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                                <div class="row">
                                                                    <label class="col-lg-3 erp-col-form-label">Barcode Print:</label>
                                                                    <div class="col-lg-9">
                                                                        <div class="form-group  input-group-sm">
                                                                            <div class="input-group input-group-sm">
                                                                                <div class="input-group-prepend"><span class="input-group-text"><i class="la la-money"></i></span></div>
                                                                                <input type="text" value="{{number_format($current_bra_sale_rate,3)}}" class="form-control label_print_price validNumber validOnlyFloatNumber" >
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
                                                                        <a href="javascript:;" data-repeater-delete="" class="btn btn-danger btn-icon btn-sm" style="height: 28px;">
                                                                            <i class="la la-remove"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-lg-8">
                                                                        <div class="kt-avatar kt-avatar--outline product_img" id="kt_user_avatar_{{$key+1}}" >
                                                                            @php
                                                                                $image = isset($pb->product_image_url)?'/products/'.$pb->product_image_url:"";
                                                                            @endphp
                                                                            @if($image)
                                                                                <div class="kt-avatar__holder" style="background-image: url({{$image}})"></div>
                                                                            @else
                                                                                <div class="kt-avatar__holder" style="background-image: url(/assets/media/custom/select_image.png)"></div>
                                                                            @endif
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
                                                                <a class="nav-link active uom_packing" data-toggle="tab" href="#uom_packing{{ $pb->product_barcode_id }}" role="tab">UOM & Packing</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link rate" data-toggle="tab" href="#rate{{ $pb->product_barcode_id }}" role="tab">Rate</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link inventory_shelf_stock" data-toggle="tab" href="#inventory_shelf_stock{{ $pb->product_barcode_id }}" role="tab">Inventory & Shelf Stock</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link tax" data-toggle="tab" href="#tax{{ $pb->product_barcode_id }}" role="tab">Tax</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link purchase_foc" data-toggle="tab" href="#purchase_foc{{ $pb->product_barcode_id }}" role="tab">Purchase & FOC</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content col-lg-12">
                                                            <div class="tab-pane active uom_packing_content" id="uom_packing{{ $pb->product_barcode_id }}" role="tabpanel">
                                                                <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed">
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
                                                                                <select class="form-control erp-form-control-sm kt-select2 uom_packing_uom" name="uom_packing_uom">
                                                                                    <option value="0">Select</option>
                                                                                    @php $pb_uom_id = isset($pb->uom->uom_id)?$pb->uom->uom_id:""@endphp
                                                                                    @foreach($data['uom'] as $uom )
                                                                                        <option value="{{$uom->uom_id}}" {{$pb_uom_id==$uom->uom_id?"selected":""}}>{{$uom->uom_name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                @if($pb->product_barcode_sr_no == 1)
                                                                                    <input type="text" class="form-control erp-form-control-sm mob_no validNumber barcode_packing" readonly name="product_barcode_packing" value="{{$pb->product_barcode_packing}}">
                                                                                @else
                                                                                    <input type="text" class="form-control erp-form-control-sm mob_no validNumber barcode_packing" name="product_barcode_packing" value="{{$pb->product_barcode_packing}}">
                                                                                @endif
                                                                           </div>
                                                                        </td>
                                                                        <td class="tag_select2_block">
                                                                            <div class="erp-select2">
                                                                                <select class="form-control kt-select2 erp-form-control-sm tag-select2" multiple name="uom_packing_color_tag">
                                                                                    @php $col = []; @endphp
                                                                                    @foreach($pb['color'] as $color)
                                                                                        @php array_push($col,$color->color_id); @endphp
                                                                                    @endforeach
                                                                                    @foreach($data['color'] as $tag)
                                                                                        <option value="{{ $tag->color_id }}" {{ (in_array($tag->color_id, $col)) ? 'selected' : '' }}>{{$tag->color_name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td class="tag_select2_block">
                                                                            <div class="erp-select2">
                                                                                <select class="form-control kt-select2 erp-form-control-sm tag-select2"  multiple name="uom_packing_size_tag">
                                                                                    @php $col = []; @endphp
                                                                                    @foreach($pb['size'] as $size)
                                                                                        @php array_push($col,$size->size_id); @endphp
                                                                                    @endforeach
                                                                                    @foreach($data['size'] as $tag)
                                                                                        <option value="{{ $tag->size_id }}" {{ (in_array($tag->size_id, $col)) ? 'selected' : '' }}>{{$tag->size_name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td class="tag_select2_block">
                                                                            <input type="text" class="form-control erp-form-control-sm small_text" name="uom_packing_other_tag" value="{{$pb->product_barcode_variant}}">
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="tab-pane rate_content" id="rate{{ $pb->product_barcode_id }}" role="tabpanel">
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
                                                                                @foreach($pb['sale_rate'] as $saleRate)
                                                                                    @php $sale_rate_rate = '';@endphp
                                                                                    @if($saleRate->branch_id == $branch->branch_id && $saleRate->product_category_id == $rate_category->rate_category_id)
                                                                                        @php $sale_rate_rate = $saleRate->product_barcode_sale_rate_rate @endphp
                                                                                        @break
                                                                                    @endif
                                                                                @endforeach
                                                                                <td>
                                                                                    <input type="hidden" class="rate_R" id="rate_R_{{$sr}}_{{$r}}" name="rate_categoryId_{{$sr}}_{{$r}}" value="{{$rate_category->rate_category_id}}">
                                                                                    <input type="text" class="form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber sale_rate_rate" value="{{isset($sale_rate_rate) && $sale_rate_rate != "" ? number_format($sale_rate_rate,3):""}}" name="rate_categoryVal_{{$sr}}_{{$r}}">
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
                                                                                        <option value="Fix Rate" {{$pb->product_barcode_purchase_rate_base=="Fix Rate"?"selected":""}}>Fix Rate</option>
                                                                                        <option value="Supplier Rate" {{$pb->product_barcode_purchase_rate_base=="Supplier Rate"?"selected":""}}>Supplier Rate</option>
                                                                                        <option value="None"  {{$pb->product_barcode_purchase_rate_base=="None"?"selected":""}}>None</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6" style="display: none">
                                                                        <div class="row">
                                                                            <label class="col-lg-6 erp-col-form-label">Purchase Rate Type:</label>
                                                                            <div class="col-lg-6">
                                                                                <div class="erp-select2">
                                                                                    <select class="form-control erp-form-control-sm kt-select2 barcode_rate_purchase_rate_type" name="barcode_rate_purchase_rate_type">
                                                                                        <option value="">Select</option>
                                                                                        <option value="Cost Rate" {{$pb->product_barcode_purchase_rate_type=="Cost Rate"?"selected":""}}>Cost Rate</option>
                                                                                        <option value="Actual Cost Rate" {{$pb->product_barcode_purchase_rate_type=="Actual Cost Rate"?"selected":""}}>Actual Cost Rate</option>
                                                                                        <option value="Avg Rate" {{$pb->product_barcode_purchase_rate_type=="Avg Rate"?"selected":""}}>Avg Rate</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6" style="display:block;">
                                                                        <div class="row">
                                                                            <label class="col-lg-6 erp-col-form-label">Cost Rate:</label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber text-left" name="product_barcode_cost_rate" value="{{number_format($pb->product_barcode_cost_rate,3)}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>{{-- end row--}}
                                                                <div class="form-group row d-none">
                                                                    <div class="col-lg-6">
                                                                        <div class="row">
                                                                            <label class="col-lg-6 erp-col-form-label">Minimum Profit Margin:</label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" value="{{ $pb->product_barcode_minimum_profit }}" maxlength="3" class="form-control erp-form-control-sm validNumber" name="barcode_minimum_profit_margin" >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6" style="display:block;">
                                                                        <div class="row">
                                                                            <label class="col-lg-6 erp-col-form-label">Purchase Rate:</label>
                                                                            <div class="col-lg-6">
                                                                                <input type="text" value="{{ number_format($pb->product_barcode_purchase_rate,3) }}" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber text-left" name="barcode_rate_purchase_rate" >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>{{-- end row--}}
                                                                @include('purchase.product.purchase_rate')
                                                            </div>
                                                            <div class="tab-pane inventory_shelf_stock_content" id="inventory_shelf_stock{{ $pb->product_barcode_id }}" role="tabpanel">
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
                                                                            <th width="6.66%" class="text-center">Comsumption Days</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @php $sr = 0; @endphp
                                                                    @foreach($data['branch'] as $key=>$branch)
                                                                        <tr>
                                                                            <td><input type="hidden" class="branch_SL" name="branch_id_{{$sr}}" value="{{$branch->branch_id}}"><b>{{$branch->branch_name}}</b></td>
                                                                            @foreach($pb['barcode_dtl'] as $b_dtl)
                                                                                @if($b_dtl->branch_id == $branch->branch_id)
                                                                                    @php
                                                                                        $product_barcode_stock_limit_neg_stock =  isset($b_dtl->product_barcode_stock_limit_neg_stock)?$b_dtl->product_barcode_stock_limit_neg_stock:0;
                                                                                        $product_barcode_stock_limit_reorder_qty =  $b_dtl->product_barcode_stock_limit_reorder_qty;
                                                                                        $product_barcode_shelf_stock_max_qty =  $b_dtl->product_barcode_shelf_stock_max_qty;
                                                                                        $product_barcode_shelf_stock_min_qty =  $b_dtl->product_barcode_shelf_stock_min_qty;
                                                                                        $product_barcode_consumption_days =  $b_dtl->product_barcode_stock_cons_day;
                                                                                        $product_barcode_stock_limit_limit_apply =  isset($b_dtl->product_barcode_stock_limit_limit_apply)?$b_dtl->product_barcode_stock_limit_limit_apply:0;
                                                                                        $product_barcode_stock_limit_status =  isset($b_dtl->product_barcode_stock_limit_status)?$b_dtl->product_barcode_stock_limit_status:0;
                                                                                    @endphp
                                                                                    @break
                                                                                @endif
                                                                            @endforeach

                                                                            <td class="text-center">
                                                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                                    @php
                                                                                        $stock_limit_neg_stock =  isset($product_barcode_stock_limit_neg_stock)?$product_barcode_stock_limit_neg_stock:0;
                                                                                    @endphp
                                                                                    <label>
                                                                                        <input type="checkbox" {{$stock_limit_neg_stock==1?"checked":""}} name="stock_limit_neg_stock_{{$sr}}">
                                                                                        <span></span>
                                                                                    </label>
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_stock_limit_reorder_qty)?$product_barcode_stock_limit_reorder_qty:""}}" name="stock_qty_level_{{$sr}}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_shelf_stock_max_qty)?$product_barcode_shelf_stock_max_qty:""}}" name="stock_max_limit_{{$sr}}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_shelf_stock_min_qty)?$product_barcode_shelf_stock_min_qty:""}}" name="stock_min_limit_{{$sr}}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_consumption_days)?$product_barcode_consumption_days:""}}" name="stock_consumption_days_{{$sr}}">
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                                    @php
                                                                                        $stock_limit_limit_apply =  isset($product_barcode_stock_limit_limit_apply)?$product_barcode_stock_limit_limit_apply:0;
                                                                                    @endphp
                                                                                    <label>
                                                                                        <input type="checkbox" {{$stock_limit_limit_apply==1?"checked":""}} name="stock_limit_apply_status_{{$sr}}">
                                                                                        <span></span>
                                                                                    </label>
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                                    @php
                                                                                        $stock_limit_status =  isset($product_barcode_stock_limit_status)?$product_barcode_stock_limit_status:0;
                                                                                    @endphp
                                                                                    <label>
                                                                                        <input type="checkbox" {{$stock_limit_status==1?"checked":""}} name="stock_status_{{$sr}}">
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
                                                                        @foreach($pb['barcode_dtl'] as $b_dtl)
                                                                            @if($b_dtl->branch_id == $branch->branch_id)
                                                                                @php
                                                                                    $product_barcode_shelf_stock_location =  isset($b_dtl->product_barcode_shelf_stock_location)?$b_dtl->product_barcode_shelf_stock_location:'';
                                                                                    $user_id =  isset($b_dtl->user->id)?$b_dtl->user->id:"";
                                                                                    $product_barcode_stock_limit_max_qty =  $b_dtl->product_barcode_stock_limit_max_qty;
                                                                                    $product_barcode_stock_limit_min_qty =  $b_dtl->product_barcode_stock_limit_min_qty;
                                                                                @endphp
                                                                                @break
                                                                            @endif
                                                                        @endforeach
                                                                        <td>
                                                                            <div class="erp-select2 form-group">
                                                                                <select class="form-control kt-select2 erp-form-control-sm shelf_stock_location" name="shelf_stock_location_{{$sr}}">
                                                                                    <option value="">Select</option>
                                                                                    @php $location = isset($product_barcode_shelf_stock_location)?$product_barcode_shelf_stock_location:'' @endphp
                                                                                    @foreach($data['display_location'] as $display_location)
                                                                                        <option value="{{$display_location->display_location_id}}" {{ $location == $display_location->display_location_id?'selected':'' }} >{{$display_location->display_location_name_string}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="erp-select2">
                                                                                <select class="form-control erp-form-control-sm kt-select2 shelf_stock_salesman" name="shelf_stock_salesman_{{$sr}}">
                                                                                    <option value="">Select</option>
                                                                                    @foreach($data['users'] as $user)
                                                                                        @php $u_id = isset($user_id)?$user_id:"" @endphp
                                                                                        <option value="{{$user->id}}" {{$u_id == $user->id?"selected":"" }}>{{$user->name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_stock_limit_max_qty)?$product_barcode_stock_limit_max_qty:""}}" name="shelf_stock_max_qty_{{$sr}}"></td>
                                                                        <td><input type="text" class="form-control erp-form-control-sm mob_no validNumber" value="{{isset($product_barcode_stock_limit_min_qty)?$product_barcode_stock_limit_min_qty:""}}" name="shelf_stock_min_qty_{{$sr}}"></td>
                                                                    </tr>
                                                                        @php $sr++; @endphp
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="tab-pane tax_content" id="tax{{ $pb->product_barcode_id }}" role="tabpanel">
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
                                                                        @foreach($pb['barcode_dtl'] as $b_dtl)
                                                                            @php
                                                                                $product_barcode_tax_value =  "";
                                                                                $product_barcode_tax_apply =  "";
                                                                            @endphp
                                                                            @if($b_dtl->branch_id == $branch->branch_id)
                                                                                @php
                                                                                   $product_barcode_tax_value =  $b_dtl->product_barcode_tax_value;
                                                                                   $product_barcode_tax_apply =  isset($b_dtl->product_barcode_tax_apply)?$b_dtl->product_barcode_tax_apply:0;
                                                                                @endphp
                                                                                @break
                                                                            @endif
                                                                        @endforeach
                                                                        <td>
                                                                            <input type="text" value="{{isset($product_barcode_tax_value)?$product_barcode_tax_value:""}}" class="form-control erp-form-control-sm tax_value mob_no validNumber validOnlyFloatNumber" name="tax_tax_value_{{$sr}}">
                                                                        </td>
                                                                        <td>
                                                                            <span class="kt-switch kt-switch--sm kt-switch--icon">
                                                                                <label>
                                                                                    @php
                                                                                        $tax_apply =  isset($product_barcode_tax_apply)?$product_barcode_tax_apply:0;
                                                                                    @endphp
                                                                                    <input type="checkbox" class="tax_status" {{$tax_apply==1?"checked":""}} name="tax_tax_status_{{$sr}}">
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
                                                            <div class="tab-pane purchase_foc_content" id="purchase_foc{{ $pb->product_barcode_id }}" role="tabpanel">
                                                                Product Purchase FOC
                                                            </div>
                                                        </div>
                                                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
                                                    </div>
                                                @endforeach
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
                                    @if($case == 'edit')
                                        <button type="submit" class="btn btn-success moveIndexSubmit moveIndex">Update</button>
                                    @endif
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
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/table-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    {{--<script src="{{ asset('js/pages/data-repeated.js') }}" type="text/javascript"></script>--}}
    <script src="{{ asset('js/pages/product-edit.js') }}" type="text/javascript"></script>

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
                        window.open(response['data']['url'], "newbarcode");
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
