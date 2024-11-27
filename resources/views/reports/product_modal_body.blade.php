@include('reports.modal_header')
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
                    <div class="erp_form__grid_th_title">Barcode</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Product Name</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">UOM</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Packing</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Current TP</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Sale Rate</div>
                </th>
                <th scope="col">
                    <div class="erp_form__grid_th_title">Action</div>
                </th>
            </tr>

            </thead>
            <tbody class="erp_form__grid_body">
            @foreach($data['product'] as $product)
                <tr data-product_barcode="{{$product->product_barcode_barcode}}">
                    <td>{{$product->product_barcode_barcode}}</td>
                    <td>{{$product->product_name}}</td>
                    <td>{{$product->uom_name}}</td>
                    <td>{{$product->product_barcode_packing}}</td>
                    <td class="text-right">{{number_format($product->net_tp,3,'.','')}}</td>
                    <td class="text-right">{{number_format($product->sale_rate,3,'.','')}}</td>
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
    //funcGridThResize([]);
    $("#modal_filter_global_search").focus();
</script>
