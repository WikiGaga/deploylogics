@extends('layouts.layout')
@section('title', 'TP Analysis')

@section('pageCSS')
    <style>
        .gridDelBtn {
            padding: 0 0 0 5px !important;
        }
        .erp_form__grid_body>tr>td{
            padding: 5px !important;
        }
        .erp-col-form-label{
            padding-bottom:0 !important;
        }
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){

        }
    @endphp
    <form>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                @include('elements.page_header',['page_data' => $data['page_data']])
            </div>
            <!--begin::Form-->
            <div class="kt-portlet__body">
                <div class="form-group-block row">
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Branch</label>
                        <div class="erp-select2">
                            <select class="form-control kt-select2 erp-form-control-sm" id="branch_id" name="branch_id[]" >
                                <option value="all">All</option>
                                @foreach($data['branch'] as $branch)
                                    <option value="{{$branch->branch_id}}" {{$branch->branch_id==auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Barcode</label>
                        <div class="erp_form___block">
                            <div class="input-group open-modal-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text btn-minus-selected-data" id="btn-minus-selected-data">
                                        <i class="la la-minus-circle"></i>
                                    </span>
                                </div>
                                <input type="text" id="tp_barcode" name="tp_barcode" data-url="{{action('Common\DataTableController@inlineHelpOpen','productTPHelp')}}" class="open_inline__help pd_barcode moveIndex form-control erp-form-control-sm" placeholder="Enter Here">
                                <input type="hidden" id="tp_product_id" name="tp_product_id">
                                <input type="hidden" id="tp_product_barcode_id" name="tp_product_barcode_id">
                                <input type="hidden" id="tp_inv_type" name="tp_inv_type">
                                {{--<div class="input-group-append">
                                    <span class="input-group-text btn-open-modal">
                                    <i class="la la-search"></i>
                                    </span>
                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Get Records:</label>
                        <div class="erp-select2">
                        <button type="button" class="btn btn-sm btn-primary" id="get_products_tp_analysis">Continue</button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group-block row" style="border: 1px solid red;border-radius: 5px;padding-bottom: 2px;">
                            <div class="col-lg-12">
                                <label class="erp-col-form-label">Inventory</label>
                                <input type="text" id="inventory" class="form-control erp-form-control-sm validNumber validOnlyNumber readonly" readonly style="font-size: 20px;text-align: center;font-weight: 800;color: #ff0072;">
                            </div>
                            {{--<div class="col-lg-6">
                                <label class="erp-col-form-label">Avg. Rate</label>
                                <input type="text" id="avg_rate" class="form-control erp-form-control-sm validNumber validOnlyNumber readonly" readonly>
                            </div>--}}
                        </div>
                    </div>
                </div>
                <div class="form-group-block row">
                    <div class="col-lg-3">
                        <label class="erp-col-form-label">Product Name</label>
                        <input type="text" id="tp_product_name" name="f_product_name" class="form-control erp-form-control-sm readonly" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Cost Rate</label>
                        <input type="text" id="cost_rate" class="form-control erp-form-control-sm validNumber validOnlyNumber readonly" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Sale Rate</label>
                        <input type="text" id="sale_rate" class="form-control erp-form-control-sm validNumber validOnlyNumber readonly" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">MRP</label>
                        <input type="text" id="mrp" class="form-control erp-form-control-sm validNumber validOnlyNumber readonly" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Net TP</label>
                        <input type="text" id="net_tp" class="form-control erp-form-control-sm validNumber validOnlyNumber readonly" readonly>
                    </div>
                </div>
                <div class="form-group-block row mt-4">
                    <ul class="green_nav nav nav-tabs col-lg-12" role="tablist" style="margin-bottom: 10px;">
                        {{--<li class="nav-item">
                            <a class="nav-link active selected_product_ds" data-toggle="tab" href="#selected_product_ds" role="tab">Product</a>
                        </li>--}}
                        {{--<li class="nav-item">
                            <a class="nav-link selected_group_item" data-toggle="tab" href="#selected_group_item" role="tab">Group Item</a>
                        </li>--}}
                    </ul>
                    <div class="tab-content col-lg-12">
                        <div class="tab-pane active selected_product_ds_content" id="selected_product_ds" role="tabpanel">
                            <div class="form-group-block row mt-1">
                                <div class="col-lg-12">
                                    <div class="erp_form___block">
                                        <div class="table-scroll form_input__block">
                                            <table class="table_mmcl_list table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                <thead class="erp_form__grid_header">
                                                <tr>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title"></div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Vendor</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Date</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Net TP</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Cost Price</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Sale Rate</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Qty</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Cost Amount</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Disc. %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Disc. Amount</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">GST %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">GST Amount</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">FED %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">FED Amount</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Special Disc %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Special Disc Amount</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">FOC Item Disc</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Net Amount</div>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="erp_form__grid_body">
                                                    <tr>
                                                        <td><div style="padding:0 5px;">Max Net TP</div></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><div style="padding:0 5px;">Min Net TP</div></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><div style="padding:0 5px;">Current TP</div></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td><div style="padding:0 5px;">Last TP</div></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h3 style="padding:10px 0;">Vendor Wise Last Purchase</h3>
                            <div class="form-group-block row mt-1">
                                <div class="col-lg-12">
                                    <div class="erp_form___block">
                                        <div class="table-scroll form_input__block">
                                            <table class="vendor_last_purc table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                <thead class="erp_form__grid_header">
                                                <tr>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Sr.</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Vendor</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Branch</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">PI Code</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Date</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Net TP</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Cost Price</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Sale Rate</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Qty</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Cost Amount</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Disc. %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">GST %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">FED %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Special Disc %</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">FOC Item Disc</div>
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Net Amount</div>
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
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script>
        var reqSupplierProducts = true;
        $(document).on('click','#get_products_tp_analysis',function(e){
            e.preventDefault();
            $('#tp_inv_type').val("");
            getBarcodeTpData();
        })
        function getBarcodeTpData(){
            var validate = true;
            var branch_id = $(document).find('#branch_id').val();
            var product_barcode_barcode = $(document).find('#tp_barcode').val();
            var tp_inv_type = $(document).find('#tp_inv_type').val();
            if(valueEmpty(branch_id)){
                toastr.error('Branch is required');
                validate = false;
                return true;
            }
            if(valueEmpty(product_barcode_barcode)){
                toastr.error('Barcode is required');
                validate = false;
                return true;
            }
            if(validate && reqSupplierProducts){
                reqSupplierProducts = false;
                $('body').addClass('pointerEventsNone');
                var formData = {
                    'branch_id' : branch_id,
                    'product_barcode_barcode' : product_barcode_barcode,
                    'tp_inv_type' : tp_inv_type,
                }
                funPageDataValEmpty()
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : 'POST',
                    url         : '/smart-product/get-products-tp-analysis',
                    dataType	: 'json',
                    data        : formData,
                    success: function(response) {
                        if(response.data){
                            var store_stock = response.data.store_stock;
                            $('#inventory').val(store_stock);

                            var product = response.data.product;
                            $('#tp_product_id').val(product.product_id);
                            $('#tp_product_barcode_id').val(product.product_barcode_id);
                            $('#tp_product_name').val(product.product_name);
                            $('#cost_rate').val(product.product_barcode_cost_rate);
                            $('#sale_rate').val(product.sale_rate);
                            $('#mrp').val(product.mrp);
                            $('#net_tp').val(product.net_tp);

                            /* max net tp*/
                            var max_tp = response.data.max_net_tp;
                           if(!valueEmpty(max_tp)) {
                                var amount = parseFloat(max_tp.tbl_purc_grn_dtl_rate) * parseFloat(max_tp.tbl_purc_grn_dtl_quantity)
                                amount = funcNumberFloat(amount);
                                var tr = "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Max Net TP</td>";
                                tr += "<td style='padding:0 5px !important;'>" + max_tp.supplier_name + "</td>";
                                tr += "<td style='padding:0 5px !important;'>" + funcJsDate('d-m-y', max_tp.grn_date) + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_net_tp) ? max_tp.tbl_purc_grn_dtl_net_tp : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_rate) ? max_tp.tbl_purc_grn_dtl_rate : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_sale_rate) ? max_tp.tbl_purc_grn_dtl_sale_rate : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_quantity) ? max_tp.tbl_purc_grn_dtl_quantity : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(amount) ? amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_disc_percent) ? max_tp.tbl_purc_grn_dtl_disc_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_disc_amount) ? max_tp.tbl_purc_grn_dtl_disc_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_vat_percent) ? max_tp.tbl_purc_grn_dtl_vat_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_vat_amount) ? max_tp.tbl_purc_grn_dtl_vat_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_fed_percent) ? max_tp.tbl_purc_grn_dtl_fed_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_fed_amount) ? max_tp.tbl_purc_grn_dtl_fed_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_spec_disc_perc) ? max_tp.tbl_purc_grn_dtl_spec_disc_perc : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_spec_disc_amount) ? max_tp.tbl_purc_grn_dtl_spec_disc_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'></td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(max_tp.tbl_purc_grn_dtl_total_amount) ? max_tp.tbl_purc_grn_dtl_total_amount : '') + "</td>";
                                tr += "</tr>";
                            }else{
                                var tr = "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Max Net TP</td>";
                                for(var i=0;i<13;i++){
                                    tr += "<td></td>";
                                }
                                tr += "</tr>";
                            }
                            /* min net tp*/
                            var min_tp = response.data.min_net_tp;
                            if(!valueEmpty(min_tp)) {
                                var amount = parseFloat(min_tp.tbl_purc_grn_dtl_rate) * parseFloat(min_tp.tbl_purc_grn_dtl_quantity)
                                amount = funcNumberFloat(amount)
                                tr += "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Min Net TP</td>";
                                tr += "<td style='padding:0 5px !important;'>" + min_tp.supplier_name + "</td>";
                                tr += "<td style='padding:0 5px !important;'>" + funcJsDate('d-m-y', min_tp.grn_date) + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_net_tp) ? min_tp.tbl_purc_grn_dtl_net_tp : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_rate) ? min_tp.tbl_purc_grn_dtl_rate : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_sale_rate) ? min_tp.tbl_purc_grn_dtl_sale_rate : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_quantity) ? min_tp.tbl_purc_grn_dtl_quantity : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(amount) ? amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_disc_percent) ? min_tp.tbl_purc_grn_dtl_disc_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_disc_amount) ? min_tp.tbl_purc_grn_dtl_disc_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_vat_percent) ? min_tp.tbl_purc_grn_dtl_vat_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_vat_amount) ? min_tp.tbl_purc_grn_dtl_vat_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_fed_percent) ? min_tp.tbl_purc_grn_dtl_fed_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_fed_amount) ? min_tp.tbl_purc_grn_dtl_fed_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_spec_disc_perc) ? min_tp.tbl_purc_grn_dtl_spec_disc_perc : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_spec_disc_amount) ? min_tp.tbl_purc_grn_dtl_spec_disc_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'></td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(min_tp.tbl_purc_grn_dtl_total_amount) ? min_tp.tbl_purc_grn_dtl_total_amount : '') + "</td>";
                                tr += "</tr>";
                            }else{
                                tr += "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Min Net TP</td>";
                                for(var i=0;i<13;i++){
                                    tr += "<td></td>";
                                }
                                tr += "</tr>";
                            }

                            /* Current net tp*/
                            var current_tp = response.data.current_net_tp;
                            if(!valueEmpty(current_tp)){
                                var amount = parseFloat(current_tp.tbl_purc_grn_dtl_rate) * parseFloat(current_tp.tbl_purc_grn_dtl_quantity)
                                amount = funcNumberFloat(amount);
                                tr += "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Current Net TP</td>";
                                tr += "<td style='padding:0 5px !important;'>"+current_tp.supplier_name+"</td>";
                                tr += "<td style='padding:0 5px !important;'>"+funcJsDate('d-m-y',current_tp.grn_date)+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_net_tp)?current_tp.tbl_purc_grn_dtl_net_tp:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_rate)?current_tp.tbl_purc_grn_dtl_rate:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_sale_rate)?current_tp.tbl_purc_grn_dtl_sale_rate:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_quantity)?current_tp.tbl_purc_grn_dtl_quantity:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(amount)?amount:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_disc_percent)?current_tp.tbl_purc_grn_dtl_disc_percent:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_disc_amount)?current_tp.tbl_purc_grn_dtl_disc_amount:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_vat_percent)?current_tp.tbl_purc_grn_dtl_vat_percent:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_vat_amount)?current_tp.tbl_purc_grn_dtl_vat_amount:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_fed_percent)?current_tp.tbl_purc_grn_dtl_fed_percent:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_fed_amount)?current_tp.tbl_purc_grn_dtl_fed_amount:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_spec_disc_perc)?current_tp.tbl_purc_grn_dtl_spec_disc_perc:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_spec_disc_amount)?current_tp.tbl_purc_grn_dtl_spec_disc_amount:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'></td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(current_tp.tbl_purc_grn_dtl_total_amount)?current_tp.tbl_purc_grn_dtl_total_amount:'')+"</td>";
                                tr += "</tr>";
                            }else{
                                tr += "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Current Net TP</td>";
                                for(var i=0;i<13;i++){
                                    tr += "<td></td>";
                                }
                                tr += "</tr>";
                            }


                            /* last net tp*/
                            var last_tp = response.data.last_net_tp;
                            if(!valueEmpty(last_tp)) {
                                var amount = parseFloat(last_tp.tbl_purc_grn_dtl_rate) * parseFloat(last_tp.tbl_purc_grn_dtl_quantity)
                                amount = funcNumberFloat(amount);
                                tr += "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Last Net TP</td>";
                                tr += "<td style='padding:0 5px !important;'>" + last_tp.supplier_name + "</td>";
                                tr += "<td style='padding:0 5px !important;'>" + funcJsDate('d-m-y', last_tp.grn_date) + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_net_tp) ? last_tp.tbl_purc_grn_dtl_net_tp : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_rate) ? last_tp.tbl_purc_grn_dtl_rate : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_sale_rate) ? last_tp.tbl_purc_grn_dtl_sale_rate : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_quantity) ? last_tp.tbl_purc_grn_dtl_quantity : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(amount) ? amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_disc_percent) ? last_tp.tbl_purc_grn_dtl_disc_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_disc_amount) ? last_tp.tbl_purc_grn_dtl_disc_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_vat_percent) ? last_tp.tbl_purc_grn_dtl_vat_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_vat_amount) ? last_tp.tbl_purc_grn_dtl_vat_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_fed_percent) ? last_tp.tbl_purc_grn_dtl_fed_percent : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_fed_amount) ? last_tp.tbl_purc_grn_dtl_fed_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_spec_disc_perc) ? last_tp.tbl_purc_grn_dtl_spec_disc_perc : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_spec_disc_amount) ? last_tp.tbl_purc_grn_dtl_spec_disc_amount : '') + "</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'></td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>" + (!valueEmpty(last_tp.tbl_purc_grn_dtl_total_amount) ? last_tp.tbl_purc_grn_dtl_total_amount : '') + "</td>";
                                tr += "</tr>";
                            }else{
                                tr += "<tr>";
                                tr += "<td style='padding:0 5px !important;'>Last Net TP</td>";
                                for(var i=0;i<13;i++){
                                    tr += "<td></td>";
                                }
                                tr += "</tr>";
                            }

                            $('.table_mmcl_list tbody.erp_form__grid_body').html(tr);

                            var vendor_last_purc = response.data.vendor_last_purc;
                            var tr = "";
                            for(var i=0;i<vendor_last_purc.length;i++){
                                var vlast_purc = vendor_last_purc[i];
                                var amount = parseFloat(vlast_purc.tbl_purc_grn_dtl_rate) * parseFloat(vlast_purc.tbl_purc_grn_dtl_quantity)
                                amount = funcNumberFloat(amount);
                                tr += "<tr>";
                                tr += "<td style='padding:0 5px !important;'>"+(i+1)+"</td>";
                                tr += "<td style='padding:0 5px !important;'>"+vlast_purc.supplier_name+"</td>";
                                tr += "<td style='padding:0 5px !important;'>"+vlast_purc.branch_name+"</td>";
                                tr += "<td style='padding:0 5px !important;'>"+vlast_purc.grn_code+"</td>";
                                tr += "<td style='padding:0 5px !important;'>"+funcJsDate('d-m-y',vlast_purc.grn_date)+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_net_tp)?vlast_purc.tbl_purc_grn_dtl_net_tp:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_rate)?vlast_purc.tbl_purc_grn_dtl_rate:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_sale_rate)?vlast_purc.tbl_purc_grn_dtl_sale_rate:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_quantity)?vlast_purc.tbl_purc_grn_dtl_quantity:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(amount)?amount:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_disc_percent)?vlast_purc.tbl_purc_grn_dtl_disc_percent:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_vat_percent)?vlast_purc.tbl_purc_grn_dtl_vat_percent:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_fed_percent)?vlast_purc.tbl_purc_grn_dtl_fed_percent:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_spec_disc_perc)?vlast_purc.tbl_purc_grn_dtl_spec_disc_perc:'')+"</td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'></td>";
                                tr += "<td class='text-right' style='padding:0 5px !important;'>"+(!valueEmpty(vlast_purc.tbl_purc_grn_dtl_total_amount)?vlast_purc.tbl_purc_grn_dtl_total_amount:'')+"</td>";
                                tr += "</tr>";
                            }

                            $('.vendor_last_purc tbody.erp_form__grid_body').html(tr);
                            funcGridThResize([]);
                        }
                        $('body').removeClass('pointerEventsNone');
                        reqSupplierProducts = true;
                    },
                    error: function(){
                        $('body').removeClass('pointerEventsNone');
                        reqSupplierProducts = true;
                    }
                })
            }
        }
    </script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js?v='.time()) }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>

    <script>
        $(document).on('click','#btn-minus-selected-data',function(){
            funPageDataValEmpty()
        })
        function funPageDataValEmpty(){
            $('#tp_product_name').val("");
            $('#cost_rate').val("");
            $('#sale_rate').val("");
            $('#mrp').val("");
            $('#net_tp').val("");
            $('#inventory').val("");
            $('.table_mmcl_list').find('td').not('td:first-child').html("")
            $('.vendor_last_purc').find('.erp_form__grid_body').html("")
        }
        if(!valueEmpty(localStorage.getItem("product_barcode_barcode"))
        && !valueEmpty(localStorage.getItem("product_barcode_id"))
        && !valueEmpty(localStorage.getItem("product_id"))){
            document.getElementById('tp_barcode').value = localStorage.getItem("product_barcode_barcode");
            document.getElementById('tp_product_barcode_id').value = localStorage.getItem("product_barcode_id");
            document.getElementById('tp_product_id').value = localStorage.getItem("product_id");
            document.getElementById('tp_inv_type').value = localStorage.getItem("inv_type");
            getBarcodeTpData();
            localStorage.removeItem('product_barcode_barcode');
            localStorage.removeItem('product_barcode_id');
            localStorage.removeItem('product_id');
        }
    </script>
@endsection
