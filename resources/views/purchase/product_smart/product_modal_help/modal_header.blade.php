<div class="modal-header prod_head">
    <style>
        .table_pitModal >tbody.erp_form__grid_body>tr>td {
            padding: 5px !important;
        }
        .table_pitModal >tbody.erp_form__grid_body>tr:hover {
            background: #c4e5ff;
        }
        .selected_tr {
            background: #c4e5ff;
            color: #000;
        }

    </style>
    <div class="row" style="width:100%;">
        @php
            $supplier_id = "";
            if(isset($data['supplier_id'])){
                $supplier_id = $data['supplier_id'];
            }
        @endphp

        <div class="col-lg-3">
            <label class="erp-col-form-label">Search:</label>
            <input type="text" class="form-control erp-form-control-sm" id="modal_filter_global_search">
        </div>
        <div class="col-lg-3">
            <label class="erp-col-form-label">Supplier:</label>
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm" id="modal_filter_supplier_id">
                    <option value="0">Select</option>
                    @foreach($data['supplier'] as $supplier)
                        <option value="{{$supplier->supplier_id}}" {{$supplier->supplier_id == $supplier_id?"selected":""}} >{{$supplier->supplier_name}}</option>
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
        <div class="col-lg-3">
            <button type="button" class="btn btn-danger btn-sm clear_all_filter" style="position: absolute;bottom: 0;">Clear Filter</button>
        </div>
    </div>
    <button type="button" class="close prod_help__close" data-dismiss="modal" aria-label="Close"></button>
</div>
