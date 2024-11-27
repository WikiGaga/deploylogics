@include('purchase.product_smart.product_modal_help.modal_header')
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 text-right">
            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
                <input type="checkbox" class="addCheckedProductAll" data-id="add_prod"> Checked All
                <span></span>
            </label>
        </div>
    </div>
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
                    <div class="erp_form__grid_th_title">Tax Group</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">GST Calc.</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">HS Code</div>
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
                    <td>{{$product->product_type_group_name}}
                        <input type="hidden" data-id="product_id" value="{{$product->product_id}}">
                        <input type="hidden" data-id="product_barcode_id" value="{{$product->product_barcode_id}}">
                    </td>
                    <td>{{$product->product_barcode_barcode}}</td>
                    <td>{{$product->product_name}}</td>
                    <td>{{$product->tax_group_name}}</td>
                    <td>{{$product->gst_calculation_name}}</td>
                    <td>{{$product->hs_code}}</td>
                    <td class="text-right">{{number_format($product->sale_rate,3,'.','')}}</td>
                    <td class="text-right">{{number_format($product->cost_rate,3,'.','')}}</td>
                    <td>{{$product->Wbrand_name}}</td>
                    <td>{{$product->product_type_group_name}}</td>
                    <td>{{$product->mrp}}</td>
                    <td>{{$product->supplier_name}}</td>
                    <td class="text-center">
                        <div style="position: relative;top: -5px;">
                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                <input type="checkbox" class="addCheckedProduct" data-id="add_prod">
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
    funcGridThResize([]);
    $("#modal_filter_global_search").focus();
</script>
