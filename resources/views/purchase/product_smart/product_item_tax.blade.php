@extends('layouts.layout')
@section('title', 'Product Item tax')

@section('pageCSS')
    <style>
        .gridDelBtn {
            padding: 0 0 0 5px !important;
        }
        .erp_form__grid_body>tr>td{
            padding: 5px !important;
        }

    </style>

    <link href="/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />

@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){

        }

    @endphp
    <form class="master_form kt-form">
        <input type="hidden" name="form_type" id="form_type" value="product_item_tax">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <!--begin::Form-->
                <div class="kt-portlet__body">
                    {{-- <div class="form-group-block row">
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Branch: <span class="required">*</span></label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="branch_id" name="branch_id">
                                    <option value="0">Select</option>
                                    @foreach($data['branch'] as $branch)
                                        <option value="{{$branch->branch_id}}" >{{$branch->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}
                    <div class="form-group-block row">
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Tax Group:</label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="tax_group_id" name="tax_group_id">
                                    <option value="0">Select</option>
                                    @foreach($data['tax_group'] as $tax_group)
                                        <option value="{{$tax_group->tax_group_id}}" >{{$tax_group->tax_group_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">GST Calculation:</label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="gst_calculation_id" name="gst_calculation_id">
                                    <option value="0">Select</option>
                                    @foreach($data['gst_clac'] as $gst_clac )
                                        <option value="{{$gst_clac->gst_calculation_id}}" >{{$gst_clac->gst_calculation_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">HS Code:</label>
                            <input type="text" id="hs_code" name="hs_code" value="" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                    <div class="form-group-block row mt-4">
                        <ul class="green_nav nav nav-tabs col-lg-12" role="tablist" style="margin-bottom: 10px;">
                            <li class="nav-item">
                                <a class="nav-link active selected_product_ds" data-toggle="tab" href="#selected_product_ds" role="tab">Product</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link selected_group_item" data-toggle="tab" href="#selected_group_item" role="tab">Group Item</a>
                            </li> --}}
                        </ul>
                        <div class="tab-content col-lg-12">
                            <div class="tab-pane active selected_product_ds_content" id="selected_product_ds" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button type="button" id="getListOfProduct" class="btn btn-sm btn-primary">Select Product by Filter</button>
                                    </div>
                                </div>
                                <div class="form-group-block row mt-1">
                                    <div class="col-lg-12">
                                        <div class="erp_form___block">
                                            <div class="table-scroll form_input__block">
                                                <table class="table_pit_list table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                    <thead class="erp_form__grid_header">
                                                    <tr>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Sr.</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                                <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                                <input id="product_barcode_id" readonly type="hidden"  class="product_barcode_id form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Product Group</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input id="product_group" readonly type="text" class="product_group form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">
                                                                Barcode
                                                                <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                                    <i class="la la-barcode"></i>
                                                                </button>
                                                            </div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}"   data-url_popup="{{ action('Common\DataTableController@helpOpen', 'productHelp') }}">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Product Name</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Tax Group</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input id="tax_group" readonly type="text" class="tax_group validNumber validOnlyNumber form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">GST Calc.</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input id="gst_calculation" readonly type="text" class="gst_calculation validNumber validOnlyNumber form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">HS Code</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input readonly id="hs_code" type="text" class="hs_code validNumber validOnlyNumber form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Sale Rate</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input readonly id="sale_rate" type="text" class="sale_rate validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Cost Price</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input readonly id="cost_price" type="text" class="cost_price validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Brand</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input readonly id="brand" type="text" class="brand form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Product Type</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input readonly id="product_type" type="text" class="product_type form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">M.R.P</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input readonly id="mrp" type="text" class="mrp validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Supplier</div>
                                                            {{-- <div class="erp_form__grid_th_input">
                                                                <input readonly id="supplier" type="text"class="supplier form-control erp-form-control-sm">
                                                            </div> --}}
                                                        </th>
                                                        <th scope="col">
                                                            <div class="erp_form__grid_th_title">Action</div>
                                                            {{-- <div class="erp_form__grid_th_btn">
                                                                <button type="button" id="addData" class="tb_moveIndex tb_moveIndexBtn erp_form__grid_newBtn btn btn-primary btn-sm">
                                                                    <i class="la la-plus"></i>
                                                                </button>
                                                            </div> --}}
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="erp_form__grid_body">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane selected_group_item_content" id="selected_group_item" role="tabpanel">
                                <div class="row">
                                    {{--<div class="col-lg-3">
                                        <label class="erp-col-form-label">Group Item: <span class="required">*</span></label>
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="group_item_id" name="group_item_id">
                                                <option value="0">Select</option>
                                                @foreach($data['group_item'] as $group_item)
                                                    <option value="{{$group_item->group_item_id}}" >{{$group_item->group_item_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
        </div>

    </form>
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script>
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'product_group',
                'fieldClass':'product_group',
                'message':'Enter Product Group',
                'require':true,
                'readonly':true
                //  'data-url' : productHelpUrl
            },
            {
                'id':'pd_barcode',
                'fieldClass':'pd_barcode open_inline__help',
                'message':'Enter Barcode',
                'require':true,
                'readonly':true
                //  'data-url' : productHelpUrl
            },
            {
                'id':'product_name',
                'fieldClass':'product_name',
                'message':'Enter Product Detail',
                'require':true,
                'readonly':true
            },
            {
                'id':'tax_group',
                'fieldClass':'tax_group validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id':'gst_calculation',
                'fieldClass':'gst_calculation validNumber validOnlyNumber',
                'type':'select'
            },
            {
                'id':'hs_code',
                'fieldClass':'hs_code validNumber validOnlyNumber',
                'readonly':true
            },
            {
                'id': 'sale_rate',
                'fieldClass': 'sale_rate validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'cost_price',
                'fieldClass': 'cost_price validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'brand',
                'fieldClass': 'brand',
                'readonly':true
            },
            {
                'id': 'product_type',
                'fieldClass': 'product_type',
                'readonly':true
            },
            {
                'id': 'mrp',
                'fieldClass': 'mrp validNumber validOnlyFloatNumber',
                'readonly':true
            },
            {
                'id': 'supplier',
                'fieldClass': 'supplier',
                'readonly':true
            },
        ];
        var arr_hidden_field = ['product_id','product_barcode_id'];
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script>
        $(document).on('click','.btn-minus-selected-data',function(e){
            $('input').val("");
            $('.erp_form__grid_body').html("");
        });

        $(document).on('click','#getListOfProduct',function(e){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {

            }
            var url = '{{action('Purchase\ProductSmartController@openModalProductFilter')}}';

            $('#kt_modal_xl').modal('show').find('.modal-content').load(url,formData);
        });

        var treeUrlList = '{{action('Purchase\ProductTreeController@productGroupTreeList')}}';

        requestgetProdBarcodeSupplier = true;
        $(document).on('change','#supplier_id',function(){
            var thix = $(this);
            var val = thix.find('option:selected').val();
            var product_group_id = $('product_group_id').val();
            var validate = true;
            /*if(valueEmpty(val)){
                toastr.error('Barcode is required.');
                validate = false;
                return false;
            }*/

            funGetProductCustomFilter(val,product_group_id,validate,requestgetProdBarcodeSupplier);
        });

        $(document).on('click','#select_product_group_tree',function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = {

            }
            var url = '{{action('Purchase\ProductSmartController@openModalProductGroup')}}';

            $('#kt_modal_md').modal('show').find('.modal-content').load(url,formData);
        })

        $(document).on('click','#unselect_product_group',function(){
            $('#select_product_group_name').html("---");
            $('#product_group_id').val("");
            var thix = $(this);
            var supplier_id = $('#supplier_id').find('option:selected').val();
            var product_group_id = $('#product_group_id').val();
            var validate = true;

            funGetProductTaxFilter(supplier_id,product_group_id,validate,requestgetProdBarcodeSupplier);
        })

        function funGetProductTaxFilter(supplier_id,product_group_id,validate,requestgetProdBarcodeSupplier){
            if(validate && requestgetProdBarcodeSupplier){
                requestgetProdBarcodeSupplier = false;
                var spinner = '<div class="kt-spinner kt-spinner--sm kt-spinner--success kt-spinner-center" style="width: 24.5px;height: 17px;"></div>';
                $('table.table_pitModal').find('tbody.erp_form__grid_body').html(spinner);
                var formData = {
                    supplier_id : supplier_id,
                    product_group_id : product_group_id
                };
                var url = '{{action('Purchase\ProductSmartController@openModalProductFilter')}}';
                // $.ajax({
                //     headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     },
                //     type: "POST",
                //     url: url,
                //     dataType	: 'json',
                //     data        : formData,
                //     success: function(response,data) {
                //         // console.log(response);
                //         if(response.status == 'success'){
                //             $('table.table_pitModal').find('tbody.erp_form__grid_body').html("");
                //             var data = response.data;
                //             var product = response.data.product;
                //             var len = product.length;
                //             var trs = ""
                //             for (var i =0;i<len;i++){
                //                 var row = product[i];
                //                 var newTr = "<tr  data-product_id='"+row['product_id']+"' data-barcode_id='"+row['product_barcode_id']+"' data-tax_id='"+row['tax_group_id']+"' data-gst_calc_id='"+row['gst_calculation_id']+"'>";
                //                 newTr += "<td>"
                //                     +(!valueEmpty(row['product_type_group_name'])?row['product_type_group_name']:"")+
                //                     "<input type='hidden' data-id='product_id' value='"+row['product_barcode_id']+"'>"+
                //                     "<input type='hidden' data-id='product_barcode_id' value='"+row['product_barcode_id']+"'>"+
                //                     "</td>";
                //                 newTr += "<td>"+(!valueEmpty(row['product_barcode_barcode'])?row['product_barcode_barcode']:"")+"</td>";
                //                 newTr += "<td>"+(!valueEmpty(row['product_name'])?row['product_name']:"")+"</td>";
                //                 newTr += "<td class='text-right max_qty'>"+(!valueEmpty(row['max_qty'])?row['max_qty']:"")+"</td>";
                //                 newTr += "<td class='text-right min_qty'>"+(!valueEmpty(row['min_qty'])?row['min_qty']:"")+"</td>";
                //                 newTr += "<td class='text-right depth_qty'>"+(!valueEmpty(row['depth_qty'])?row['depth_qty']:"")+"</td>";
                //                 newTr += "<td class='text-right face_qty'>"+(!valueEmpty(row['face_qty'])?row['face_qty']:"")+"</td>";
                //                 newTr += "<td class='text-right reorder_point'>"+(!valueEmpty(row['reorder_point'])?row['reorder_point']:"")+"</td>";
                //                 newTr += "<td class='text-right'>"+parseFloat(row['sale_rate']).toFixed(3)+"</td>";
                //                 newTr += "<td class='text-right'>"+parseFloat(row['cost_rate']).toFixed(3)+"</td>";
                //                 newTr += "<td>"+(!valueEmpty(row['brand_name'])?row['brand_name']:"")+"</td>";
                //                 newTr += "<td>"+(!valueEmpty(row['product_type_group_name'])?row['product_type_group_name']:"")+"</td>";
                //                 newTr += "<td class='text-right'>"+parseFloat(row['mrp']).toFixed(3)+"</td>";
                //                 newTr += "<td>"+(!valueEmpty(row['supplier_name'])?row['supplier_name']:"")+"</td>";
                //                 newTr += '<td class="text-center">\n' +
                //                     '                            <div style="position: relative;top: -5px;">\n' +
                //                     '                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">\n' +
                //                     '                                    <input type="checkbox" class="addCheckedProduct" data-id="add_prod" >\n' +
                //                     '                                    <span></span>\n' +
                //                     '                                </label>\n' +
                //                     '                            </div></td>';
                //                 newTr += "</tr>";

                //                 $('table.table_pitModal').find('tbody.erp_form__grid_body').append(newTr);
                //             }
                //         }else{
                //             toastr.error(response.message);
                //         }
                //         requestgetProdBarcodeSupplier = true;

                //     },
                //     error: function(response,status) {
                //         requestgetProdBarcodeSupplier = true;

                //     }
                // });
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        // console.log(response);
                        if(response.status == 'success'){
                            $('table.table_pitModal').find('tbody.erp_form__grid_body').html("");
                            var data = response.data;
                            var product = response.data.product;
                            var len = product.length;
                            var trs = ""
                            for (var i =0;i<len;i++){
                                var row = product[i];
                                var newTr = "<tr  data-product_id='"+row['product_id']+"' data-barcode_id='"+row['product_barcode_id']+"' data-tax_id='"+row['tax_group_id']+"' data-gst_calc_id='"+row['gst_calculation_id']+"'>";
                                newTr += "<td>"+(!valueEmpty(row['product_type_group_name'])?row['product_type_group_name']:"")+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['product_barcode_barcode'])?row['product_barcode_barcode']:"")+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['product_name'])?row['product_name']:"")+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['tax_group_name'])?row['tax_group_name']:"")+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['gst_calculation_name'])?row['gst_calculation_name']:"")+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['hs_code'])?row['hs_code']:"")+"</td>";
                                newTr += "<td>"+row['sale_rate']+"</td>";
                                newTr += "<td>"+row['cost_rate']+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['brand_name'])?row['brand_name']:"")+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['product_type_group_name'])?row['product_type_group_name']:"")+"</td>";
                                newTr += "<td>"+row['mrp']+"</td>";
                                newTr += "<td>"+(!valueEmpty(row['supplier_name'])?row['supplier_name']:"")+"</td>";
                                newTr += '<td class="text-center">\n' +
                                    '                            <div style="position: relative;top: -5px;">\n' +
                                    '                                <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">\n' +
                                    '                                    <input type="checkbox" class="addProductForTax">\n' +
                                    '                                    <span></span>\n' +
                                    '                                </label>\n' +
                                    '                            </div></td>';
                                newTr += "</tr>";

                                $('table.table_pitModal').find('tbody.erp_form__grid_body').append(newTr);
                            }
                        }else{
                            toastr.error(response.message);
                        }
                        requestgetProdBarcodeSupplier = true;

                    },
                    error: function(response,status) {
                        requestgetProdBarcodeSupplier = true;

                    }
                });
            }
        }
        // $(document).on('click','.addCheckedProduct',function(){
        //     var thix = $(this);
        //     if(thix.is(':checked')){
        //         var tr = thix.parents('tr');
        //         var cloneTr = tr.clone();
        //         var len = $('table.table_pit_list').find('tbody.erp_form__grid_body>tr').length + 1;
        //         $(cloneTr).prepend("<td>"+len+"</td>")

        //         var product_id = $(cloneTr).attr('data-product_id');
        //         var addProd = true;
        //         $('table.table_pit_list').find('tbody.erp_form__grid_body>tr').each(function(){
        //             var thix = $(this);
        //             var data_product_id = thix.attr('data-product_id');
        //             if(product_id == data_product_id){
        //                 toastr.error("Product already added");
        //                 addProd = false;
        //             }
        //         })
        //         if(addProd){
        //             $(cloneTr).find('td').attr('style','padding: 5px !important;');
        //             $(cloneTr).find('td:last-child').find('input').removeClass('addCheckedProduct');
        //             $(cloneTr).find('td:last-child').find('input').addClass('setCheckedProduct');
        //             $('table.table_pit_list').find('tbody.erp_form__grid_body').append(cloneTr);
        //             funcSrReInit();
        //         }
        //     }
        // });

        $(document).on('click','.addProductForTax',function(){
            var thix = $(this);
            if(thix.is(':checked')){
                var tr = thix.parents('tr');
                var cloneTr = tr.clone();
                var len = $('table.table_pit_list').find('tbody.erp_form__grid_body>tr').length + 1;
                $(cloneTr).prepend("<td>"+len+"</td>")

                var product_id = $(cloneTr).attr('data-product_id');
                var addProd = true;
                $('table.table_pit_list').find('tbody.erp_form__grid_body>tr').each(function(){
                    var thix = $(this);
                    var data_product_id = thix.attr('data-product_id');
                    if(product_id == data_product_id){
                        toastr.error("Product already added");
                        addProd = false;
                    }
                })
                if(addProd){
                    $(cloneTr).find('td:last-child').find('input').removeClass('addProductForTax');
                    $(cloneTr).find('td:last-child').find('input').addClass('setProductForTax');
                    $('table.table_pit_list').find('tbody.erp_form__grid_body').append(cloneTr);
                }
            }
        });

        // function funcSrReInit(){
        //     var sr_no = 1;
        //     var max_qty = $(document).find('#maximum_qty').val();
        //     var min_qty = $(document).find('#minimum_qty').val();
        //     var depth_qty = $(document).find('#depth_qty').val();
        //     var face_qty = $(document).find('#face_quantity').val();
        //     var reorder_point = $(document).find('#rec_point').val();
        //     $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
        //         $(this).find('td:first-child').html(sr_no);
        //         var allInput = $(this).find('input');
        //         var len = allInput.length
        //         for(v=0;v<len;v++){
        //             var dataId = $(allInput[v]).attr('data-id');
        //             var newNameVal = "pd["+sr_no+"]["+dataId+"]"
        //             $(allInput[v]).attr('name',newNameVal);
        //         }

        //         if(!emptyArr.includes(max_qty)){
        //             $(this).find('.max_qty').html(max_qty);
        //         }
        //         if(!emptyArr.includes(min_qty)){
        //             $(this).find('.min_qty').html(min_qty);
        //         }
        //         if(!emptyArr.includes(depth_qty)){
        //             $(this).find('.depth_qty').html(depth_qty);
        //         }
        //         if(!emptyArr.includes(face_qty)){
        //             $(this).find('.face_qty').html(face_qty);
        //         }
        //         if(!emptyArr.includes(reorder_point)){
        //             $(this).find('.reorder_point').html(reorder_point);
        //         }
        //         sr_no = sr_no + 1;
        //     });
        // }
        // $(document).on('keyup','#maximum_qty',function(){
        //     var val = $(this).val();
        //     $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
        //         if(!emptyArr.includes(val)){
        //             $(this).find('.max_qty').html(val)
        //         }
        //     })
        // })
        // $(document).on('keyup','#minimum_qty',function(){
        //     var val = $(this).val();
        //     $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
        //         if(!emptyArr.includes(val)){
        //             $(this).find('.min_qty').html(val)
        //         }
        //     })
        // })
        // $(document).on('keyup','#depth_qty',function(){
        //     var val = $(this).val();
        //     $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
        //         if(!emptyArr.includes(val)){
        //             $(this).find('.depth_qty').html(val)
        //         }
        //     })
        // })
        // $(document).on('keyup','#face_quantity',function(){
        //     var val = $(this).val();
        //     $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
        //         if(!emptyArr.includes(val)){
        //             $(this).find('.face_qty').html(val)
        //         }
        //     })
        // })
        // $(document).on('keyup','#rec_point',function(){
        //     var val = $(this).val();
        //     $('.table_pit_list>tbody.erp_form__grid_body>tr').each(function(){
        //         if(!emptyArr.includes(val)){
        //             $(this).find('.reorder_point').html(val)
        //         }
        //     })
        // })
    </script>
@endsection
