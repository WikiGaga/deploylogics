<div class="modal-header prod_head">
    <style>
        .table_lgModal >tbody.erp_form__grid_body>tr>td {
            padding: 5px !important;
        }
        .table_lgModal >tbody.erp_form__grid_body>tr:hover {
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
            <input type="text" class="form-control erp-form-control-sm" id="po_modal_filter_global_search">
        </div>
        <div class="col-lg-3">
            <label class="erp-col-form-label">Supplier:</label>
            <div class="erp-select2">
                <select class="form-control kt-select2 erp-form-control-sm" id="po_modal_filter_supplier_id">
                    <option value="0">Select</option>
                    @foreach($data['supplier'] as $supplier)
                        <option value="{{$supplier->supplier_id}}" {{$supplier->supplier_id == $supplier_id?"selected":""}} >{{$supplier->supplier_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{--<div class="col-lg-3">
            <label class="erp-col-form-label">Status:</label>
            <div class="kt-radio-inline">
                <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                    <input type="radio" name="radioStatus" value="all" checked> All
                    <span></span>
                </label>
                <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                    <input type="radio" name="radioStatus" value="pending"> Pending
                    <span></span>
                </label>
                <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                    <input type="radio" name="radioStatus" value="completed"> Completed
                    <span></span>
                </label>
            </div>
        </div>--}}
        <div class="col-lg-3">
            <button type="button" class="btn btn-danger btn-sm po_reset_all_filter" style="position: absolute;bottom: 0;">Clear Filter</button>
        </div>
    </div>
    <button type="button" class="close prod_help__close" data-dismiss="modal" aria-label="Close"></button>
</div>
