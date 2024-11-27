@include('purchase.product_smart.product_modal_help.modal_header')
<div class="modal-body">
    <div id="product_filters">
        <table class="table_pitModal table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
            <thead class="erp_form__grid_header">
            <tr>
                <th scope="col">
                    <div class="erp_form__grid_th_title">1st Level Category</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Last Level Category</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Barcode</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Product Name</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">MRP</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Sale Rate</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Cost Rate</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Trade Rate</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Vendor</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Vendor Rate</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Vendor TP</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Action</div>
                </th>
            </tr>

            </thead>
            <tbody class="erp_form__grid_body">
            @foreach($data['product'] as $product)
                @php
                    $i = $loop->iteration;
                @endphp
                <tr data-product_id="{{$product->product_id}}" data-barcode_id="{{$product->product_barcode_id}}">
                    <td>
                        <input type="hidden" data-id="product_id" value="{{$product->product_id}}">
                        <input type="hidden" data-id="product_barcode_id" value="{{$product->product_barcode_id}}">
                    </td>
                    <td>{{$product->group_item_name}}</td>
                    <td class="barcode">{{$product->product_barcode_barcode}}</td>
                    <td class="product_name">{{$product->product_name}}</td>
                    <td class='text-right mrp'>{{$product->mrp}}</td>
                    <td class='text-right sale_rate'>{{number_format($product->sale_rate,3,'.','')}}</td>
                    <td class='text-right cost_rate'>{{number_format($product->cost_rate,3,'.','')}}</td>
                    <td class='text-right trade_rate'>{{number_format($product->cost_rate,3,'.','')}}</td>
                    <td class='text-right supplier_name'>{{$product->supplier_name}}</td>
                    <td class='text-right supplier_rate'></td>
                    <td class='text-right supplier_tp'></td>
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
