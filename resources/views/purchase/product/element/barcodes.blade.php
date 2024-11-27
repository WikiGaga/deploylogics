<div id="kt_repeater_barcode">
    @php
        if($case == $new){
            $product_barcode = [
                (object)[
                    'product_barcode_id' => "",
                    'product_barcode_barcode' => ""
                ]
            ];
        }
        if($case == $edit || $case == $view){
            $product_barcode = $data['current']->product_barcode;
        }
    @endphp
    <div class="form-group-block row">
        <div data-repeater-list="product_barcode_data" class="col-lg-12">
            @foreach($product_barcode as $key=>$pb)
                @php
                    $current_bra_sale_rate = 0;
                    if($case == $new){
                        $product_barcode_id = "";
                        $product_barcode_barcode = "";
                        $product_barcode_weight_apply = 0;
                        $base_barcode = 1;
                        $image = "";
                    }
                    if($case == $edit || $case == $view){
                        $product_barcode_id = $pb->product_barcode_id;
                        $product_barcode_barcode = $pb->product_barcode_barcode;
                        $product_barcode_weight_apply = $pb->product_barcode_weight_apply;
                        $base_barcode = $pb->base_barcode;
                        foreach($pb['sale_rate'] as $pb_sale_rate){
                            if($pb_sale_rate['branch_id'] == auth()->user()->branch_id && $pb_sale_rate['product_category_id'] == 2){
                                $current_bra_sale_rate = $pb_sale_rate['product_barcode_sale_rate_rate'];
                                break;
                            }
                        }
                        $image = isset($pb->product_image_url)?'/products/'.$pb->product_image_url:"";
                    }
                @endphp
                <div data-repeater-item class="kt-margin-b-10 barcode" item-id="1">
                    <div class="form-group-block row">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-4 erp-col-form-label">Product Barcode:<span class="required">* </span></label>
                                <div class="col-lg-8">
                                    <div class="form-group-block input-group" style="width: 203px;">
                                        <input type="text" class="form-control erp-form-control-sm small_text barcode_repeat_b" value="{{ $product_barcode_barcode }}" name="v_product_barcode" style="width: 250px;">
                                        <input type="hidden" class="form-control erp-form-control-sm barcode_repeat_b_id" value="{{ $product_barcode_id }}" name="product_barcode_id">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="position: relative;">
                                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--primary" style="position: absolute;top: 3px;left: 4px;">
                                                    <div style="position: absolute;top: -4px;background: #f7f8fa;padding: 4px 4px 3px 25px;border: 1px solid #e2e5ec;border-left: 0;">
                                                        Auto Generate
                                                    </div>
                                                    <input type="checkbox" class="auto-barcode-generate">
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
                                        <label class="col-lg-6 erp-col-form-label">Weight Apply:</label>
                                        <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                <input type="checkbox" class="product_barcode_weight_apply" name="product_barcode_weight_apply" {{$product_barcode_weight_apply==1?"checked":""}}>
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
                                                <input type="checkbox" class="base_barcode" name="base_barcode" {{$base_barcode==1?"checked":""}}>
                                                <span></span>
                                            </label>
                                        </span>
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
                    <ul class="barcode_nav nav nav-tabs col-lg-12" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active uom_packing" data-toggle="tab" href="#uom_packing{{$product_barcode_id}}" role="tab">UOM & Packing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rate" data-toggle="tab" href="#rate{{$product_barcode_id}}" role="tab">Rate and Tax</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link inventory_shelf_stock" data-toggle="tab" href="#inventory_shelf_stock{{$product_barcode_id}}" role="tab">Inventory & Shelf Stock</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link barcode_print" data-toggle="tab" href="#barcode_print_content{{$product_barcode_id}}" role="tab">Barcode Print</a>
                        </li>
                    </ul>
                    <div class="tab-content col-lg-12">
                        <div class="tab-pane active uom_packing_content" id="uom_packing{{$product_barcode_id}}" role="tabpanel">
                            @include('purchase.product.element.bar_uom_packing')
                        </div>
                        <div class="tab-pane rate_content" id="rate{{$product_barcode_id}}" role="tabpanel">
                            @include('purchase.product.element.bar_rate_and_tax')
                        </div>
                        <div class="tab-pane inventory_shelf_stock_content" id="inventory_shelf_stock{{$product_barcode_id}}" role="tabpanel">
                            @include('purchase.product.element.bar_inventory_shelf_stock')
                        </div>
                        <div class="tab-pane barcode_print_content" id="barcode_print_content{{$product_barcode_id}}" role="tabpanel">
                            @include('purchase.product.element.bar_barcode_print_content')
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
