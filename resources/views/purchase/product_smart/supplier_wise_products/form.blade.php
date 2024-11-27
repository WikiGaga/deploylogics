@extends('layouts.layout')
@section('title', 'Vendor wise Product')

@section('pageCSS')
    <style>
        .gridDelBtn {
            padding: 0 0 0 5px !important;
        }
        .erp_form__grid_body>tr>td{
            padding: 5px !important;
        }
        .nowrap_text{
            clear: both;
            display: inline-block;
            overflow: hidden;
            white-space: nowrap;
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
                    <div class="col-lg-4">
                        <div class="row">
                            <label class="col-lg-4 erp-col-form-label">Vendor:</label>
                            <div class="col-lg-8">
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="supplier_id">
                                        <option value="0">Select</option>
                                        @foreach($data['supplier'] as $supplier)
                                            <option value="{{$supplier->supplier_id}}" >{{$supplier->supplier_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-sm btn-primary" id="get_products_by_vendor">Continue</button>
                    </div>
                </div>
                <hr>
                <div class="form-group-block row" style="
                            background: #d3d3d3;
                            box-shadow: 0px 6px 20px 7px rgb(0 0 0 / 10%);
                            -webkit-box-shadow: 0px 6px 20px 7px rgb(0 0 0 / 10%);
                            -moz-box-shadow: -2px 0px 20px 7px rgba(211,211,211,0.75);padding-bottom: 5px;">
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Barcode</label>
                        <input type="text" id="set_barcode" class="form-control erp-form-control-sm">
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Cost Price</label>
                        <input type="text" id="set_cost_price" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Disc %</label>
                        <input type="text" id="set_disc_perc" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">G.S.T</label>
                        <input type="text" id="set_gst" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">F.E.D %</label>
                        <input type="text" id="set_fed_perc" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">GST Criteria:</label>
                        <select id="set_gst_criteria" class="form-control erp-form-control-sm">
                            <option value="1">DA</option>
                        </select>
                    </div>
                    {{-- one more row --}}
                    <div class="col-lg-2">
                        <div class="erp-col-form-label">Extra Disc. Criteria:</div>
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                            <input type="checkbox" id="set_extra_disc_criteria">
                            <span></span>
                        </label>
                    </div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Extra Disc. %</label>
                        <input type="text" id="set_ectra_disc_perc" class="form-control erp-form-control-sm validNumber validOnlyFloatNumber">
                    </div>
                    <div class="col-lg-2">
                        <div class="erp-col-form-label">Fixed Price:</div>
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                            <input type="checkbox" id="set_fixed_price">
                            <span></span>
                        </label>
                    </div>
                    <div class="col-lg-2"></div>
                    <div class="col-lg-2"></div>
                    <div class="col-lg-2">
                        <label class="erp-col-form-label">Set Values:</label>
                        <div class="erp-select2">
                            <button type="button" class="btn btn-sm btn-success" id="set_value_continue" style="padding: 4px 20px;">Set values</button>
                        </div>
                    </div>
                    {{-- one more row --}}
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
                                            <table class="table_pit_list table erp_form__grid erp_form__grid_th_resize table-resizable dtr-inline">
                                                <thead class="erp_form__grid_header">
                                                <tr>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Sr.</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input id="sr_no" readonly type="text" class="sr_no form-control erp-form-control-sm">
                                                            <input id="product_id" readonly type="hidden" class="product_id form-control erp-form-control-sm">
                                                            <input id="product_barcode_id" readonly type="hidden"  class="product_barcode_id form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">
                                                            Barcode
                                                           {{-- <button type="button" id="mobOpenInlineHelp" class="btn btn-primary btn-sm">
                                                                <i class="la la-barcode"></i>
                                                            </button>--}}
                                                        </div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input id="pd_barcode" type="text" class="pd_barcode tb_moveIndex open_inline__help form-control erp-form-control-sm" data-url="{{ action('Common\DataTableController@inlineHelpOpen', 'productHelp') }}"   data-url_popup="{{ action('Common\DataTableController@helpOpen', 'productHelp') }}">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Product Name</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input id="product_name" readonly type="text" class="product_name form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Cost Price</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input readonly id="cost_price" type="text" class="cost_price validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Trade Rate</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input readonly id="trade_rate" type="text" class="trade_rate validNumber validOnlyFloatNumber form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Disc Perc</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input id="disc_perc" readonly type="text" class="disc_perc validNumber validOnlyNumber form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">GST</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input id="gst" readonly type="text" class="gst validNumber validOnlyNumber form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Excise Duty</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input readonly id="excise_duty" type="text" class="excise_duty validNumber validOnlyNumber form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">GST Criteria</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input readonly id="gst_criteria" type="text" class="face_qty validNumber validOnlyNumber form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Extra Disc Crt.</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input readonly id="extra_disc_criteria" type="text" class="extra_disc_criteria validNumber validOnlyNumber form-control erp-form-control-sm">
                                                        </div>--}}
                                                    </th>
                                                    <th scope="col">
                                                        <div class="erp_form__grid_th_title">Extra Disc</div>
                                                        {{--<div class="erp_form__grid_th_input">
                                                            <input readonly id="extra_disc" type="text" class="extra_disc form-control erp-form-control-sm">
                                                        </div>--}}
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
        $(document).on('click','#get_products_by_vendor',function(e){
            e.preventDefault();
            var validate = true;
            var supplier_id = $(document).find('#supplier_id option:selected').val();

            if(validate && reqSupplierProducts){
                reqSupplierProducts = false;
                var spinner = '<div class="kt-spinner kt-spinner--sm kt-spinner--success kt-spinner-center" style="width: 24.5px;height: 17px;"></div>';
                $('table.table_pit_list').find('tbody.erp_form__grid_body').html(spinner);
                var formData = {
                    'supplier_id' : supplier_id,
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : 'POST',
                    url         : '/smart-product/get-vendor-wise-product-list',
                    dataType	: 'json',
                    data        : formData,
                    success: function(response) {
                        if(response.data){
                            $('table.table_pit_list').find('tbody.erp_form__grid_body').html("");
                            var list = response.data.supplier;
                            var len = list.length;
                            var sr_no = 1;
                            for(var i=0;i<len;i++){
                                var row = list[i];
                                var css = "style='padding:5px !important;'";
                                var tr = "<tr>";
                                tr += "<td "+css+">"+(sr_no + i)+"</td>";
                                tr += "<td "+css+">"+row['product_barcode_barcode']+"</td>";
                          //      tr += "<td "+css+"><input type='text' value='HINZ WEST BAZOO 909# X.LARGE' class='product_name form-control erp-form-control-sm' readonly></td>";
                                tr += "<td "+css+">"+row['product_name']+"</td>";
                                var cost_price = (!valueEmpty(row['cost_price'])?row['cost_price']:0);
                                var trade_price = (!valueEmpty(row['trade_price'])?row['trade_price']:0);
                                var discount_perc = (!valueEmpty(row['discount_perc'])?row['discount_perc']:0);
                                var gst = (!valueEmpty(row['gst'])?row['gst']:0);
                                var excise_duty = (!valueEmpty(row['excise_duty'])?row['excise_duty']:0);
                                var gst_criteria = (!valueEmpty(row['gst_criteria'])?row['gst_criteria']:0);
                                var extra_discount_criteria = (!valueEmpty(row['extra_discount_criteria'])?row['extra_discount_criteria']:0);
                                var extra_discount = (!valueEmpty(row['extra_discount'])?row['extra_discount']:0);
                                tr += "<td class='text-right' "+css+">"+parseFloat(cost_price).toFixed(3)+"</td>";
                                tr += "<td class='text-right' "+css+">"+parseFloat(trade_price).toFixed(3)+"</td>";
                                tr += "<td class='text-right' "+css+">"+parseFloat(discount_perc).toFixed(3)+"</td>";
                                tr += "<td class='text-right' "+css+">"+parseFloat(gst).toFixed(3)+"</td>";
                                tr += "<td class='text-right' "+css+">"+parseFloat(excise_duty).toFixed(3)+"</td>";
                                tr += "<td class='text-right' "+css+">"+parseFloat(gst_criteria).toFixed(3)+"</td>";
                                tr += "<td class='text-right' "+css+">"+parseFloat(extra_discount_criteria).toFixed(3)+"</td>";
                                tr += "<td class='text-right' "+css+">"+parseFloat(extra_discount).toFixed(3)+"</td>";
                                tr += "</tr>";
                                $('table.table_pit_list').find('tbody.erp_form__grid_body').append(tr);
                            }
                        }
                        reqSupplierProducts = true;
                    },
                    error: function(response,status) {
                        reqSupplierProducts = true;
                        $('table.table_pit_list').find('tbody.erp_form__grid_body').html("");
                    }
                })
            }

        })
    </script>
@endsection
