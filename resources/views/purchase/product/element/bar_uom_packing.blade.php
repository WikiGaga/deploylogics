@php
    $col_color = [];
    $col_size = [];
    if($case == $new){
        $uom_id = \App\Helpers\Helper::constantValue('uom_id');
        $product_barcode_packing = 1;
        $pb_packing_readonly = "readonly";
        $product_barcode_variant = "";
        $variant_id = 0;
        $color_id = 0;
        $size_id = 0;
        $weight_id = 0;
    }
    if($case == $edit || $case == $view){
        $uom_id = $pb->uom_id;
        $product_barcode_packing = $pb->product_barcode_packing;
        $pb_packing_readonly = ($pb->product_barcode_sr_no == 1)?"readonly":"";
        /*foreach($pb['color'] as $color){
            array_push($col_color,$color->color_id);
        }
        foreach($pb['size'] as $size){
            array_push($col_size,$size->size_id);
        }*/
        $product_barcode_variant = $pb->product_barcode_variant;
        $variant_id = $pb->variant_id;
        $color_id = $pb->color_id;
        $size_id = $pb->size_id;
        $weight_id = $pb->weight_id;
    }
    $width = 100/6;
@endphp
<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline collapsed tblPack">
    <thead>
    <tr>
        <th width="{{$width}}%">UOM <span class="required">* </span></th>
        <th width="{{$width}}%">Packing <span class="required">* </span></th>
        <th width="{{$width}}%">Color</th>
        <th width="{{$width}}%">Conv. Qty</th>
        <th width="{{$width}}%">Variant</th>
        <th width="{{$width}}%">Weight</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <div class="erp-select2 form-group-block">
                <select class="form-control erp-form-control-sm kt-select2 uom_packing_uom" name="uom_packing_uom" id="uom_packing_uom">
                    <option value="0">Select</option>
                    @foreach($data['uom'] as $uom )
                        <option value="{{$uom->uom_id}}" {{$uom->uom_id==$uom_id?'selected':''}}>{{$uom->uom_name}}</option>
                    @endforeach
                </select>
            </div>
        </td>
        <td>
            <div class="form-group-block">
                <input type="text" class="form-control erp-form-control-sm mob_no validNumber barcode_packing" name="product_barcode_packing" value="{{$product_barcode_packing}}">
            </div>
        </td>
        <td class="tag_select2_block">
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm uom_packing_color_tag" name="uom_packing_color_tag">
                    <option value="0">Select</option>
                    @foreach($data['color'] as $color)
                        <option value="{{$color->color_id}}" {{ $color->color_id == $color_id ? 'selected' : '' }}>{{$color->color_name}}</option>
                    @endforeach
                </select>
            </div>
        </td>
        <td>
            <div class="form-group-block">
                <input type="text" class="form-control erp-form-control-sm mob_no validNumber uom_packing_size_tag" name="uom_packing_size_tag" value="{{$size_id}}">
            </div>
        </td>
        <td class="tag_select2_block">
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm uom_packing_other_tag" name="uom_packing_other_tag">
                    <option value="0">Select</option>
                    @foreach($data['variant'] as $variant)
                        <option value="{{$variant->variant_id}}" {{ $variant->variant_id == $variant_id ? 'selected' : '' }}>{{$variant->variant_name}}</option>
                    @endforeach
                </select>
            </div>
        </td>
        <td class="tag_select2_block">
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm weight_id" name="weight_id">
                    <option value="0">Select</option>
                    @foreach($data['weight'] as $weight)
                        <option value="{{$weight->weight_id}}" {{ $weight->weight_id == $weight_id ? 'selected' : '' }}>{{$weight->weight_name}}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    </tbody>
</table>
