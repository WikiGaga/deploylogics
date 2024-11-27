<div class="modal-header prod_head">
    <style>

    </style>
    <div class="row" style="width:100%;">
        <div class="col-lg-3">
            <label class="erp-col-form-label">Supplier:</label>
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm" id="supplier_id" name="supplier_id">
                    <option value="0">Select</option>
                    @foreach($data['supplier'] as $supplier)
                        <option value="{{$supplier->supplier_id}}" >{{$supplier->supplier_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <label class="erp-col-form-label">Product Group:</label>
            <div class="Product_group_block">
                <button type="button" class="" id="select_product_group_name" style="width:72%;border-radius: 3px;height: 28px;border:unset;background: #dfdfdf !important;text-align: left;">---</button>
                <button type="button" class="" id="select_product_group_tree" style="border-radius: 3px;height: 28px;border:unset;background: #dfdfdf !important;">Select</button>
                <button type="button" class="" id="unselect_product_group" style="border-radius: 3px;height: 28px;border:unset;background: #dfdfdf !important;"><i class="fa fa-trash"></i></button>
                <input type="hidden" id="product_group_id" class="form-control erp-form-control-sm readonly" readonly>
            </div>
        </div>
    </div>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div id="product_filters">
        <table class="table_pitModal table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
            <thead class="erp_form__grid_header">
            <tr>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Product Group</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Barcode</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Product Name</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Max Qty</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Min Qty</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Depth Qty</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Face Qty</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Reorder Point</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Sale Rate</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Cost Price</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Brand</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Product Type</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">M.R.P</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Supplier</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Action</div>
                </th>
            </tr>
            
            </thead>
            <tbody class="erp_form__grid_body">
                @foreach($data['product'] as $product)
                    <tr data-product_id="{{$product->product_id}}" data-barcode_id="{{$product->product_barcode_id}}" data-tax_id="{{$product->tax_group_id}}" data-gst_calc_id="{{$product->gst_calculation_id}}">
                        <td>{{$product->product_type_group_name}}</td>
                        <td>{{$product->product_barcode_barcode}}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->max_qty}}</td>
                        <td>{{$product->min_qty}}</td>
                        <td>{{$product->depth_qty}}</td>
                        <td>{{$product->face_qty}}</td>
                        <td>{{$product->reorder_point}}</td>
                        <td>{{$product->sale_rate}}</td>
                        <td>{{$product->cost_rate}}</td>
                        <td>{{$product->brand_name}}</td>
                        <td>{{$product->product_type_group_name}}</td>
                        <td>{{$product->mrp}}</td>
                        <td>{{$product->supplier_name}}</td>
                        <td class="text-center">
                            <div style="position: relative;top: -5px;">
                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                    <input type="checkbox" class="addProductForTax">
                                    <span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $('.kt-select2').select2();
</script>

