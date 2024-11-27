<div class="modal-body">
    <style>
        @media (min-width: 320px) and (max-width: 1190px){
            #ajax_data,
            #selected_products{
                /*overflow: auto !important;*/
            }
        }

        #selected_products>table,
        #ajax_data>table{
            overflow-y: scroll !important;
            height: 75vh;
            width: 100% !important;
        }
        #selected_products>table>.kt-datatable__head,
        #ajax_data>table>.kt-datatable__head{
            position: absolute !important;
            width: calc(100% - 17px);
            top: -2px;
            border-top-width: 0px;
        }
        #selected_products>table>.kt-datatable__head>tr>th,
        #ajax_data>table>.kt-datatable__head>tr>th{
            background: #e3e3e3 !important;
        }
        #selected_products>table>.kt-datatable__body,
        #ajax_data>table>.kt-datatable__body{
            position: relative;
            top: 40px;
        }
        .erp-custom-select2>.select2>.selection>.select2-selection>.select2-selection__rendered{
            height: 34px;
            padding: 7px 8px;
        }
    </style>
    <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom: 0px;">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#smp_products" role="tab">Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " data-toggle="tab" href="#smp_selected_products" role="tab">Selected Products</a>
        </li>
        <li class="nav-item" style="position: absolute;right: 17px;top: 5px;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background: #f44336;color: #fff;padding: 8px;font-size: 12px;opacity: 1;"><i class="fa fa-times"></i></button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="smp_products" role="tabpanel">
            <div class="row" style="margin-bottom: 10px; margin-top: 10px">
                <div class="col-md-3">
                    {{--<label class="erp-col-form-label">General Search:</label>--}}
                    <div class="kt-input-icon kt-input-icon--left">
                        <input type="text" class="form-control form-control-sm" placeholder="Search..." id="generalSearch" autofocus="true" autocomplete="off" style="height: calc(1.5em + 1rem + 4px);">
                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kt-input-icon kt-input-icon--left">
                        @php
                            $data['supplier'] = \App\Models\ViewPurcSupplier::orderBy('supplier_name')->get(['supplier_id','supplier_name']);
                        @endphp
                        {{--<label class="erp-col-form-label">Supplier:</label>--}}
                        <div class="erp-select2">
                            <select class="form-control erp-form-control-sm" multiple id="supplierSearch" name="supplierSearch[]">
                                @foreach($data['supplier'] as $supplier)
                                    <option value="{{$supplier->supplier_id}}">{{$supplier->supplier_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kt-input-icon kt-input-icon--left">
                        @php
                            $data['group_item'] = \App\Models\ViewPurcGroupItem::orderBy('group_item_name_string')->get(['group_item_id','group_item_name_string']);
                        @endphp
                       {{-- <label class="erp-col-form-label">Product Group Multiple:</label>--}}
                        <div class="erp-select2 erp-custom-select2">
                            {{--<select class="form-control erp-form-control-sm productTypeSearch90 supplierSearch90" multiple id="productTypeSearch" name="productTypeSearch[]">
                                <option></option>
                            </select>--}}
                            <select class="form-control kt-select2 erp-form-control-sm" id="productGroupSearch" name="productGroupSearch">
                                <option value="">Select Product Group</option>
                                @foreach($data['group_item'] as $group_item)
                                    <option value="{{$group_item->group_item_id}}">{{$group_item->group_item_name_string}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-right">
                    <button type="button" class="btn btn-primary btn-sm" id="searchFilterProd">Search</button>
                </div>
            </div>
            <div class="kt-datatable ajax_data_table modal_data_table" data-url="{{ action('Common\GetAllData@selectMultipleProductsData','product') }}"  id="ajax_data" style="top:0;"></div>
        </div>
        <div class="tab-pane" id="smp_selected_products" role="tabpanel">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="button" class="btn btn-primary btn-sm" id="add_selected_products" style="margin: 10px 0;">Add Selected Products</button>
                </div>
            </div>
            {{--<div class="kt-datatable ajax_data_table modal_data_table" data-url="--}}{{--{{ action('Common\GetAllData@selectMultipleProductsData','product2') }}--}}{{--"  id="selected_products" style="top:0;"></div>--}}
            <div class="kt-datatable ajax_data_table modal_data_table kt-datatable--default kt-datatable--brand kt-datatable--loaded" id="selected_products" style="top:0;">
                <table class="kt-datatable__table" style="width: 100% !important;">
                    <thead class="kt-datatable__head">
                    <tr class="kt-datatable__row" style="left: 0px;">
                        <th data-field="product_barcode_barcode" class="kt-datatable__cell"><span style="width: 110px;">Barcode</span></th>
                        <th data-field="product_name" class="kt-datatable__cell"><span style="width: 110px;">Product</span></th>
                        <th data-field="uom_name" class="kt-datatable__cell"><span style="width: 110px;">Product Dtl</span></th>
                        <th data-field="product_barcode_purchase_rate" class="kt-datatable__cell"><span style="width: 110px;">Rate</span></th>
                        <th data-field="product_code" class="kt-datatable__cell"><span style="width: 110px;">Suggest</span></th>
                        <th data-field="product_barcode_shelf_stock_min_qty" class="kt-datatable__cell"><span style="width: 110px;">Level</span></th>
                        <th data-field="stock" class="kt-datatable__cell--right kt-datatable__cell"><span style="width: 110px;">Stock</span></th>
                        @if(isset($data['form_type']) && $data['form_type'] == 'grn')
                            <th data-field="qty" class="kt-datatable__cell">
                                <span style="width: 110px;">Qty</span>
                            </th>
                        @else
                            <th data-field="demand_qty" class="kt-datatable__cell">
                                <span style="width: 110px;">Demand Qty</span>
                            </th>
                        @endif
                        <th data-field="actions" class="kt-datatable__cell"><span style="width: 110px;">Actions</span></th>
                    </tr>
                    </thead>
                    <tbody class="kt-datatable__body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $('.kt-select2').select2()
    $('#supplierSearch').select2({
        'placeholder':'Select Supplier'
    });

    var FORM_TYPE = '{{ $data["form_type"] }}';
</script>
<script src="{{ asset('js/pages/js/common/multi-products-ajax.js') }}" type="text/javascript"></script>
