@php
    $col_specific = [];
    $col_item = [];
    if($case == 'new'){
        $product_group = 0;
        $product_type_group_id = request()->session()->get('product_item_type');
        $product_type_id = 0;
        $product_manufacturer_id = 0;
        $supplier_id = 0;
        $manufacturer_id = 0;
        $country_id = 0;
        $product_brand_id = 0;
        $product_demand_active_status = 0;
        $product_warranty_status = 0;
        $product_warranty_period_id = 0;
        $product_warranty_period_mode = "";
        $product_perishable = 0;
        $product_tracing_days = "";
        $product_batch_req = 0;
        $product_expiry_return_allow = 0;
        $product_damage_return_allow = 0;
        $product_expiry_required = 0;
        $product_barcode_length_calc = 0;
        $product_expiry_base = 0;
        $product_shelf_life_minimum = "";
        $product_remarks = "";
        $flavour_id = 0;
        $season_id = 0;
    }
    if($case == 'edit' || $case == 'view'){
        $product_group = $current->group_item_id;
        $product_manufacturer_id = $current->product_manufacturer_id;
        $product_type_group_id = $current->product_item_type;
        $product_type_id = $current->product_type_id;
        $supplier_id = $current->supplier_id;
        $manufacturer_id = $current->manufacturer_id;
        $country_id = $current->country_id;
        $product_brand_id = $current->product_brand_id;
        $flavour_id = $current->flavour_id;
        $season_id = $current->season_id;
        foreach($current->specification_tags as $specification_tags){
            array_push($col_specific,$specification_tags->tag_id);
        }
        foreach($current->item_tags as $item_tags){
            array_push($col_item,$item_tags->tag_id);
        }
        $product_demand_active_status = $current->product_demand_active_status;
        $product_warranty_status = $current->product_warranty_status;
        $product_warranty_period_id = $current->product_warranty_period_id;
        $product_warranty_period_mode = $current->product_warranty_period_mode;
        $product_perishable = $current->product_perishable;
        $product_tracing_days = $current->product_tracing_days;
        $product_batch_req = $current->product_batch_req;
        $product_expiry_return_allow = $current->product_expiry_return_allow;
        $product_damage_return_allow = $current->product_damage_return_allow;
        $product_expiry_required = $current->product_expiry_required;
        $product_barcode_length_calc = $current->product_barcode_length_calc;
        $product_expiry_base = $current->product_expiry_base;
        $product_shelf_life_minimum = $current->product_shelf_life_minimum;
        $product_manufacturer_id = $current->product_manufacturer_id;
        $product_remarks = $current->product_remarks;
    }
@endphp
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Product Group: <span class="required">* </span></label>
            <div class="col-lg-6" id="product_control_group_block">
                <div class="erp-select2 form-group-block">
                    <select class="form-control kt-select2 erp-form-control-sm" id="product_control_group" name="product_control_group">
                        <option value="0">Select</option>
                        @foreach($data['group_item'] as $group_item)
                            <option value="{{$group_item->group_item_id}}" data-refno="{{ $group_item->group_item_ref_no }}"  {{$product_group==$group_item->group_item_id?"selected":""}}>{{$group_item->group_item_name_string}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Product Type: <span class="required">* </span></label>
            <div class="col-lg-6" id="product_item_type_block">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" id="product_item_type" name="product_item_type">
                        <option value="0">Select</option>
                        @foreach($data['item_type'] as $item_type)
                            <option value="{{$item_type->product_type_group_id}}"  {{$product_type_group_id==$item_type->product_type_group_id?"selected":""}}>{{$item_type->product_type_group_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Product Generic:</label>
            <div class="col-lg-6" id="product_type_block">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" id="product_type" name="product_type">
                        <option value="0">Select</option>
                        @foreach($data['product_type'] as $product_type)
                            <option value="{{$product_type->product_type_id}}" {{$product_type_id == $product_type->product_type_id?"selected":""}}>{{$product_type->product_type_name}}</option>
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
                            <option value="{{$supplier->supplier_id}}" {{$supplier_id == $supplier->supplier_id?"selected":""}}>{{$supplier->supplier_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Manufacturer:</label>
            <div class="col-lg-6" id="product_manufacturer_block">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" id="product_manufacturer" name="product_manufacturer">
                        <option value="0">Select</option>
                        @foreach($data['manufacturer'] as $manufacturer)
                            <option value="{{$manufacturer->manufacturer_id}}" {{$product_manufacturer_id==$manufacturer->manufacturer_id?"selected":""}}>{{$manufacturer->manufacturer_name}}</option>
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
                            <option value="{{$country->country_id}}" {{$country_id==$country->country_id?"selected":""}}>{{$country->country_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Brand Name:</label>
            <div class="col-lg-6" id="product_brand_name_block">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" id="product_brand_name" name="product_brand_name">
                        <option value="0">Select</option>
                        @foreach($data['brand'] as $brand)
                            <option value="{{$brand->brand_id}}" {{$product_brand_id==$brand->brand_id?"selected":""}}>{{$brand->brand_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Flavour:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" id="flavour" name="flavour_id">
                        <option value="0">Select</option>
                        @foreach($data['flavour'] as $flavour)
                            <option value="{{$flavour->flavour_id}}" {{ ($flavour->flavour_id == $flavour_id) ? 'selected' : '' }}>{{$flavour->flavour_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Item Tags:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" id="product_item_tags" multiple name="product_item_tags[]">
                        @foreach($data['item'] as $tag)
                            <option value="{{$tag->tags_id}}" {{ (in_array($tag->tags_id, $col_item)) ? 'selected' : '' }}>{{$tag->tags_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Season:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select class="form-control kt-select2 erp-form-control-sm" id="season_id" name="season_id">
                        <option value="0">Select</option>
                        @foreach($data['season'] as $season)
                            <option value="{{$season->season_id}}" {{ ($season->season_id == $season_id) ? 'selected' : '' }}>{{$season->season_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Central Rate:</label>
            <div class="col-lg-6">
                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                        <label>
                            <input type="checkbox" name="product_warranty_status" {{$product_warranty_status==1?"checked":""}}>
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
                                <option value="{{$wp->warrenty_period_id}}" {{$product_warranty_period_id==$wp->warrenty_period_id?"selected":""}}>{{$wp->warrenty_period_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="width: 33.33%;">
                        <input type="text" id="product_warranty_mode" value="{{$product_warranty_period_mode}}" name="product_warranty_mode" class="form-control erp-form-control-sm mob_no validNumber">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Perishable:</label>
            <div class="col-lg-6">
                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                        <label>
                            <input type="checkbox" name="product_perishable" {{$product_perishable==1?"checked":""}}>
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
                <input class="form-control erp-form-control-sm mob_no validNumber text-left" placeholder="(Days Before Expiry)" value="{{$product_tracing_days}}" id="product_tracing_days" name="product_tracing_days">
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Batch No Required:</label>
            <div class="col-lg-6">
                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                        <label>
                            <input type="checkbox" name="product_batch_no_required" {{$product_batch_req==1?"checked":""}}>
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
                            <input type="checkbox" name="product_expiry_return_allow" {{$product_expiry_return_allow==1?"checked":""}}>
                            <span></span>
                        </label>
                    </span>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Damage Return Allow:</label>
            <div class="col-lg-6">
                    <span class="kt-switch kt-switch--sm kt-switch--icon">
                        <label>
                            <input type="checkbox" name="product_damage_return_allow" {{$product_damage_return_allow==1?"checked":""}}>
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
                            <input type="checkbox" name="product_expiry_required" {{$product_expiry_required==1?"checked":""}}>
                            <span></span>
                        </label>
                    </span>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Demand Active Status:</label>
            <div class="col-lg-6">
                <span class="kt-switch kt-switch--sm kt-switch--icon">
                    <label>
                        <input type="checkbox" checked="checked" name="product_demand_active_status" {{$product_demand_active_status==1?"checked":""}}>
                        <span></span>
                    </label>
                </span>
            </div>
        </div>
    </div>
</div>{{-- end row--}}
{{--<div class="row form-group-block">
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Expiry Base On:</label>
            <div class="col-lg-6">
                <div class="erp-select2">
                    <select type="text" class="form-control erp-form-control-sm kt-select2" id="product_expiry_base_on" name="product_expiry_base_on">
                        <option value="0">Select</option>
                        <option value="Expire Date" {{$product_expiry_base=="Expire Date"?"selected":""}}>Expire Date</option>
                        <option value="Production Date" {{$product_expiry_base=="Production Date"?"selected":""}}>Production Date</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <label class="col-lg-6 erp-col-form-label">Shelf Life Minimum%:</label>
            <div class="col-lg-6">
                <input type="text" value="{{$product_shelf_life_minimum}}" class="form-control erp-form-control-sm mob_no validNumber validOnlyFloatNumber" name="product_shelf_life_minimum">
            </div>
        </div>
    </div>
</div>--}}{{-- end row--}}
<div class="row form-group-block">
    <label class="col-lg-3 erp-col-form-label">Notes:</label>
    <div class="col-lg-9">
        <textarea type="text" class="form-control erp-form-control-sm large_text" rows="3" name="product_remarks">{{$product_remarks}}</textarea>
    </div>
</div>{{-- end row--}}
{{--<div class="row form-group-block">
    <div class="col-lg-12">
        <label><b>Product Life:</b></label>
        @include('purchase.product.element.product_life')
    </div>
</div>--}}
<div class="row form-group-block">
    <div class="col-lg-12">
        <label><b>Product Supplier:</b></label>
        @include('purchase.product.element.purchase_foc')
    </div>
</div>
