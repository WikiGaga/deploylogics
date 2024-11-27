@extends('layouts.layout')
@section('title', 'Stock Audit')

@section('pageCSS')
    <style>
        .erp-col-form-label{
            padding:0 !important;
        }
        .erp_data__grid_fixed_header {
            position: relative;
            overflow: auto;
            max-height: 500px;
        }
        .erp_data__grid_fixed_header table>thead>tr>th, .erp_data__grid_fixed_header table>thead>tr>td {
            padding: 5px;
            position: sticky;
            top: -1px;
            color: #fff;
            background: #a2a2a2;
            z-index: 9;
        }
        table.data_grid__last_th_sticky>thead>tr>th:last-child, table.data_grid__last_th_sticky>thead>tr>td:last-child {
            right: 0;
            background: #6e6c6c;
        }
        table.data_grid__last_th_sticky>tbody>tr>td:last-child{
            position: sticky;
            right: 0;
            z-index: 9;
            background: #6e6c6c;
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
    <form class="product_item_tax_form kt-form" method="post" action="{{ action('Inventory\StockAuditAdjustmentController@store') }}">
        <input type="hidden" name="form_type" id="form_type" value="stock_audit">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <!--begin::Form-->
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <div class="col-lg-3" style="display:none;">
                            <label class="erp-col-form-label">Branch: <span class="required">*</span></label>
                            <div class="erp-select2">
                                <select class="form-control kt-select2 erp-form-control-sm" id="branch_id" name="branch_id[]" multiple>
                                    {{--<option value="all">All</option>--}}
                                    @foreach($data['branch'] as $branch)
                                        @if($branch->branch_id == auth()->user()->branch_id)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id==auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="erp-col-form-label">Name:</label>
                            <div class="erp-select2">
                                <input type="text" id="audit_name" name="audit_name" value="" class="form-control erp-form-control-sm">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="erp-col-form-label">Remarks:</label>
                            <input type="text" id="remarks" name="remarks" value="" class="form-control erp-form-control-sm">
                        </div>
                    </div>
                    <div class="form-group-block row mt-4">
                        <ul class="green_nav nav nav-tabs col-lg-12" role="tablist" style="margin-bottom: 10px;">
                            <li class="nav-item">
                                <a class="nav-link active selected_group_item" data-toggle="tab" href="#selected_group_item" role="tab">Group Item</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link selected_product_ds" data-toggle="tab" href="#selected_product_ds" role="tab">Product</a>
                            </li>
                        </ul>
                        <div class="tab-content col-lg-12">
                            <div class="tab-pane active selected_group_item_content" id="selected_group_item" role="tabpanel">
                                <style>
                                    span.first_level_name {
                                        margin-left: 35px;
                                        cursor: pointer;
                                    }
                                    .checkbox_block:hover {
                                        background: #f0f8ff;
                                    }
                                    .checkbox_block_selected{
                                        background: #dcdcdc;
                                    }
                                    span.selected_count {
                                        font-weight: 800;
                                    }
                                </style>
                                <div class="row">
                                    <div class="col-lg-12"></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label class="erp-col-form-label">Vendor Name:<span class="required">*</span></label>
                                                <div class="erp_form___block">
                                                    <div class="input-group open-modal-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text btn-minus-selected-data">
                                                                <i class="la la-minus-circle"></i>
                                                            </span>
                                                        </div>
                                                        <input type="text" value="{{isset($supplier_code)?$supplier_code:''}}" data-url="{{action('Common\DataTableController@inlineHelpOpen','supplierHelp')}}" id="supplier_name" name="supplier_name" class="open_inline__help form-control erp-form-control-sm moveIndex" placeholder="Enter here">
                                                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{isset($supplier_id)?$supplier_id:''}}"/>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text btn-open-mob-help" id="mobOpenInlineSupplierHelp">
                                                                <i class="la la-search"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3" id="product_brand_name_block">
                                        <label class="erp-col-form-label">Brand Name</label>
                                        <div class="erp-select2">
                                            <select class="form-control kt-select2 erp-form-control-sm" id="product_brand_name" name="product_brand_name">
                                                <option value="0">Select</option>
                                                @foreach($data['brand'] as $brand)
                                                    <option value="{{$brand->brand_id}}">{{$brand->brand_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <label class="erp-col-form-label">As On Date: <span class="required">*</span></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control erp-form-control-sm " value="{{ isset($start_date) ? $start_date:date('d-m-Y') }}" id="start_date" name="start_date">
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-clock-o glyphicon-th"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 text-right">
                                        <div style="height: 16px;"></div>
                                        <button type="button" id="get_products_list" class="btn btn-sm btn-primary">Get Products List</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="row" id="group_item_container">
                                    <div class="col-lg-6">
                                        <div id="first_level_block" class="kt-checkbox-list">
                                            @foreach($data['group_item'] as $group_item)
                                                <div class="checkbox_block">
                                                    <label class="kt-checkbox kt-checkbox--success first_level_checkbox">
                                                        <input type="checkbox" class="first_level_group" id="{{$group_item->group_item_id}}" autocomplete="off">
                                                       {{-- <span></span>--}}
                                                    </label>
                                                    <span class="first_level_name">{{$group_item->group_item_name}} ({{--<span class="selected_count">0</span> /--}} {{count($group_item->last_level)}})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="child-checkbox-list">
                                            @foreach($data['group_item'] as $child_group_items)
                                                <div class="kt-checkbox-list" id="{{$child_group_items->group_item_id}}" style="display:none;">
                                                    @foreach($child_group_items->last_level as $child_group_item)
                                                        <label class="kt-checkbox kt-checkbox--success last_level_checkbox">
                                                            @if($case == 'edit')
                                                                <input type="checkbox" class="last_group_item_id" id="{{$child_group_item->group_item_id}}" value="{{$child_group_item->group_item_id}}" name="group_item_id" {{in_array($child_group_item->group_item_id,$selected_group_item_list)?"checked":""}}> {{$child_group_item->group_item_name}}
                                                                <span></span>
                                                            @else
                                                                <input type="checkbox" class="last_group_item_id" id="{{$child_group_item->group_item_id}}" value="{{$child_group_item->group_item_id}}" name="group_item_id"> {{$child_group_item->group_item_name}}
                                                                <span></span>
                                                            @endif
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane selected_product_ds_content" id="selected_product_ds" role="tabpanel">
                                <div class="form-group-block row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <button type="button" id="data_clear" class="data_clear btn btn-sm btn-danger">Table Data Clear</button>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="row">
                                                    <div class="col-lg-2">
                                                        <select class="moveIndex form-control erp-form-control-sm" id="qty_action" name="qty_action">
                                                            <option value="0">Select</option>
                                                            <option value="<">Less < </option>
                                                            <option value=">">Greater > </option>
                                                            <option value="=">Equal = </option>
                                                            <option value="<>">Is Not Equal != </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input type="text" id="qty_filter" name="qty_filter" style="width: 100%;">
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <button type="button" id="data_sort" class="data_sort btn btn-sm btn-danger">Sort</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-right">
                                                <label class="kt-checkbox kt-checkbox--success">
                                                    <input type="checkbox" class="checkAll"> Check All
                                                     <span></span>
                                                </label>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="erp_data__grid_fixed_header">
                                            <table class="table table_pit_list erp_data__grid erp_data__grid_fixed_header data_grid__last_th_sticky erp_data__inline_filter" style="width: 100% !important;">
                                                <thead>
                                                <tr>
                                                    <th width="4%"> Sr. </th>
                                                    <th width="12%"> Barcode <input type="text" style="width: 100%;"></th>
                                                    <th width="36%"> Product Name <input type="text" style="width: 100%;"></th>
                                                    <th width="8%"> Quantity <input type="text" style="width: 100%;"></th>
                                                    <th width="6%"> Unit <input type="text" style="width: 100%;"></th>
                                                    <th width="6%"> Cost Rate <input type="text" style="width: 100%;"></th>
                                                    <th width="10%"> Category <input type="text" style="width: 100%;"></th>
                                                    <th width="10%"> Top Level Category <input type="text" style="width: 100%;"></th>
                                                    <th width="8%"> Action </th>
                                                </tr>
                                                </thead>
                                                <tbody class="erp_data__grid_body">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
    $(document).ready(function(){
        $("#all").click(function(){
            $("#inputDays").hide();
            $('#kt_datepicker_3').val('');
            var allDate = '01-01-2000';
            $('#start_date').val(allDate);
        });
        $("#today").click(function(){
            $("#inputDays").hide();
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();

            var today = (day<10 ? '0' : '') + day + '-' +
            (month<10 ? '0' : '') + month + '-' +
            d.getFullYear();
            $('#start_date').val(today);
        });
        $("#yesterday").click(function(){
            $("#inputDays").hide();
            var date = new Date();
            date.setDate(date.getDate() - 1);
            var nd = new Date(date);

            var month = nd.getMonth()+1;
            var day = nd.getDate();

            var yesterday = (day<10 ? '0' : '') + day + '-' +
            (month<10 ? '0' : '') + month + '-' +
            nd.getFullYear();
            $('#start_date').val(yesterday);
        });
        $("#last_7_days").click(function(){
            $("#inputDays").hide();
            var date = new Date();
            date.setDate(date.getDate() - 7);
            var nd = new Date(date);

            var month = nd.getMonth()+1;
            var day = nd.getDate();

            var last_7_days = (day<10 ? '0' : '') + day + '-' +
            (month<10 ? '0' : '') + month + '-' +
            nd.getFullYear();
            $('#start_date').val(last_7_days);
        });
        $("#last_30_days").click(function(){
            $("#inputDays").hide();
            var date = new Date();
            date.setDate(date.getDate() - 30);
            var nd = new Date(date);

            var month = nd.getMonth()+1;
            var day = nd.getDate();

            var last_30_days = (day<10 ? '0' : '') + day + '-' +
            (month<10 ? '0' : '') + month + '-' +
            nd.getFullYear();
            $('#start_date').val(last_30_days);
        });
        
        $("#last_days").click(function(){
            $("#inputDays").show();
            $("#days").keyup(function(){
                var daysNumber = $('#days').val();
                var date = new Date();
                date.setDate(date.getDate() - daysNumber);
                var nd = new Date(date);

                var month = nd.getMonth()+1;
                var day = nd.getDate();

                var manual_days = (day<10 ? '0' : '') + day + '-' +
                (month<10 ? '0' : '') + month + '-' +
                nd.getFullYear();
                $('#start_date').val(manual_days);
            });
        });
    });
</script>
    <script>
        var KTFormWidgets = function () {
            // Private functions
            var validator;
            var formId = $( ".product_item_tax_form" )
            $.validator.addMethod("valueNotEquals", function(value, element, arg){
                return arg !== value;
            }, "This field is required");
            var initValidation = function () {
                validator = formId.validate({
                    // define validation rules
                    //  debug: true,
                    rules: {

                    },

                    //display error alert on form submit
                    invalidHandler: function(event, validator) {
                        var alert = $('#kt_form_1_msg');
                        alert.removeClass('kt--hide').show();
                        KTUtil.scrollTo('m_form_1_msg', -200);
                    },
                    beforeSend: function(form) {

                    },
                    submitHandler: function (form) {
                        $("form").find(":submit").prop('disabled', true);
                        //form[0].submit(); // submit the form
                        var formData = new FormData(form);
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url         : form.action,
                            type        : form.method,
                            dataType	: 'json',
                            data        : formData,
                            cache       : false,
                            contentType : false,
                            processData : false,
                            success: function(response,status) {
                                if(response.status == 'success'){
                                    toastr.success(response.message);
                                    setTimeout(function () {
                                        $("form").find(":submit").prop('disabled', false);
                                    }, 2000);
                                    location.reload()
                                }else{
                                    toastr.error(response.message);
                                    setTimeout(function () {
                                        $("form").find(":submit").prop('disabled', false);
                                    }, 2000);
                                }
                            },
                            error: function(response,status) {
                                // console.log(response.responseJSON);
                                toastr.error(response.responseJSON.message);
                                setTimeout(function () {
                                    $("form").find(":submit").prop('disabled', false);
                                }, 2000);
                            },
                        });
                    }
                });
            }

            return {
                // public functions
                init: function() {
                    initValidation();
                }
            };
        }();

        jQuery(document).ready(function() {
            KTFormWidgets.init();
        });


        
        var dateToday = new Date();
        $('#start_date, #start_date_validate').datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd-mm-yyyy',
            todayBtn: true,
            minDate: new Date()
        });
    </script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/add-row-repeated_new.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>

    <script !src="">
        $(document).on('click','.first_level_name',function(){
            var thix = $(this);
            var checkbox_block = thix.parents('.checkbox_block');
            var data_id = checkbox_block.find('input').attr('id');
            var group_item_container = thix.parents('#group_item_container');
            if(!checkbox_block.hasClass('checkbox_block_selected')){
                group_item_container.find('#first_level_block').find('.checkbox_block').removeClass('checkbox_block_selected');
                group_item_container.find('#child-checkbox-list').find('.kt-checkbox-list').hide();
                checkbox_block.addClass('checkbox_block_selected');
                var child_list = group_item_container.find('#child-checkbox-list').find('#'+data_id);
                child_list.show();
            }else{
                group_item_container.find('#first_level_block').find('.checkbox_block').removeClass('checkbox_block_selected');
                group_item_container.find('#child-checkbox-list').find('.kt-checkbox-list').hide();
            }
        });
        $(document).on('click','.first_level_group',function(){
            var thix = $(this);
            var data_id = thix.attr('id');
            var checkbox_block = thix.parents('.checkbox_block');
            var group_item_container = thix.parents('#group_item_container');
            var child_list = group_item_container.find('#child-checkbox-list').find('#'+data_id)
            if(thix.prop('checked')) {
                child_list.find('input').prop('checked',true);
                /*var length = child_list.find('input').length;
                checkbox_block.find('.selected_count').html(length);*/
            } else {
                child_list.find('input').prop('checked',false);
                /*checkbox_block.find('.selected_count').html(0);*/
            }

        });
        $(document).on('click','.last_group_item_id',function(){
            var thix = $(this);
            var kt_checkbox_list = thix.parents('.kt-checkbox-list');
            var data_id = kt_checkbox_list.attr('id');
            var total_selected_count = 0;
            var total_length = kt_checkbox_list.find('input').length;
            kt_checkbox_list.find('input').each(function(){
                if($(this).prop('checked')){
                    total_selected_count += 1;
                }
            })
            var group_item_container = thix.parents('#group_item_container');
            var parent_list = group_item_container.find('#first_level_block').find('#'+data_id);
            var checkbox_block = parent_list.parents('.checkbox_block');
            /* Extra function.. added 18 jan 2023 2:35PM */
            group_item_container.find('#child-checkbox-list').find('.last_level_checkbox').each(function(){
                $(this).find('input').prop('checked',false);
            })
            /* End Extra function.. added 18 jan 2023 2:35PM */
            thix.prop('checked',true);
            if(total_length == total_selected_count) {
                parent_list.prop('checked',true);
                /*checkbox_block.find('.selected_count').html(total_selected_count);*/
            } else {
                parent_list.prop('checked',false);
                /*checkbox_block.find('.selected_count').html(total_selected_count);*/
            }
        });
        function funcFoundSelectedCheckedItems(){
            $('#child-checkbox-list>.kt-checkbox-list').each(function(){
                var thix = $(this);
                var data_id = thix.attr('id');
                var total_input = 0;
                var selected_input = 0;
                thix.find('.last_level_checkbox').each(function(){
                    total_input += 1;
                    if($(this).find('input.last_group_item_id').prop('checked')){
                        selected_input += 1;
                    }
                })
                var checkbox_block = $('#first_level_block').find('#'+data_id).parents('.checkbox_block');
                /*checkbox_block.find('.selected_count').html(selected_input);*/
                if(total_input == selected_input){
                    $('#first_level_block').find('#'+data_id).prop('checked','true');
                }
            })
        }
        funcFoundSelectedCheckedItems();
    </script>
    <script !src="">
        var pageSpinner = '<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span>';
        var xhrGetData = true;
        $(document).on('click','#get_products_list',function(){
            var thix = $(this);
            var selected_group_item = $('#selected_group_item');
            var supplier_id = selected_group_item.find('#supplier_id').val();
            var start_date = selected_group_item.find('#start_date').val();
            var branch_id = $('#branch_id').val();
            var group_item_id = selected_group_item.find('input[name="group_item_id"]:checked').val();
            var validate = true;

            /*if(valueEmpty(supplier_id) && valueEmpty(group_item_id)){
                toastr.error("at least One is required between Group Item Or Supplier");
                validate = false;
                return true;
            }*/

            if(valueEmpty(supplier_id))
            {
                if(valueEmpty(group_item_id)){
                    toastr.error("at least One is required between Group Item");
                    validate = false;
                    return true;
                }
            }

            if(valueEmpty(branch_id.length)){
                toastr.error("at least One Branch is required");
                validate = false;
                return true;
            }
            if(validate && xhrGetData){
                xhrGetData = false;
                $('.table_pit_list>tbody').html(pageSpinner);
                var formData = {
                    supplier_id: supplier_id,
                    start_date: start_date,
                    group_item_id: group_item_id,
                    branch_id: branch_id,
                };
                var url = '/stock-audit-adjustment/product-item-tax-product-list';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
                        $('#selected_group_item').addClass('pointerEventsNone');
                    },
                    success: function(response,data) {
                        if(response.status == 'success'){
                            var tbody = $('.table_pit_list>tbody.erp_data__grid_body');
                            tbody.html("");
                            var list = response.data.products;
                            var length = list.length;
                            var notShow = ["",undefined,null,NaN,"NaN"];
                            for(var i=0;i<length;i++){
                                var row = list[i];
                                var tds = "";
                                var sr = i+1;

                                var multi_val = row['product_id']+'@'+row['product_barcode_id']+'@'+row['product_barcode_barcode']+'@'+row['uom_id']+'@'+row['product_barcode_packing']+'@'+row['stock']+'@'+row['cost_rate'];

                                tds += "<td>"+sr+"</td>";
                                tds += "<td>"+row['product_barcode_barcode']+"</td>";
                                tds += "<td>"+row['product_name']+"</td>";
                                tds += "<td>"+row['stock']+"</td>";
                                tds += "<td>"+row['uom_name']+"</td>";
                                tds += "<td>"+row['cost_rate']+"</td>";
                                tds += "<td>"+row['group_item_name']+"</td>";
                                tds += "<td>"+row['group_item_parent_name']+"</td>";
                                tds += "<td> " +
                                            "<label class='kt-checkbox kt-checkbox--default'>"+
                                                '<input type="hidden" data-id="stock_quantity" class="stock_quantity" value="'+row['stock']+'" name="pdd['+ i +'][stock_quantity]">'+
                                                '<input type="hidden" data-id="product_barcode_packing" class="product_barcode_packing" value="'+row['product_barcode_packing']+'" name="pdd['+ i +'][product_barcode_packing]">'+
                                                '<input type="hidden" data-id="pd_barcode" class="pd_barcode" value="'+row['product_barcode_barcode']+'" name="pdd['+ i +'][pd_barcode]">'+
                                                '<input type="hidden" data-id="uom_id" class="uom_id" value="'+row['uom_id']+'" name="pdd['+ i +'][uom_id]">'+
                                                '<input type="hidden" data-id="cost_rate" class="cost_rate" value="'+row['cost_rate']+'" name="pdd['+ i +'][cost_rate]">'+
                                                '<input type="hidden" data-id="product_barcode_id" class="product_barcode_id" value="'+row['product_barcode_id']+'" name="pdd['+ i +'][product_barcode_id]">'+
                                                '<input type="hidden" data-id="product_id" class="product_id" value="'+row['product_id']+'" name="pdd['+i+'][product_id]">'+
                                                '<input type="checkbox" data-id="check_id" id="check_id'+sr+'" class="check_id" value="'+multi_val+'" name="check_id[]">'+
                                                '<span></span>'+
                                            "</label>"+
                                        "</td>";
                                var tr = "<tr>"+tds+"</tr>";
                                tbody.append(tr);
                            }
                            toastr.success(response.message);

                        }else{
                            toastr.error(response.message);
                            $('.table_pit_list>tbody').html("");
                        }
                        xhrGetData = true;
                        $('#selected_group_item').removeClass('pointerEventsNone');
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGetData = true;
                        $('#selected_group_item').removeClass('pointerEventsNone');
                        $('.table_pit_list>tbody').html("");
                    }
                });
            }
        });
        $(document).on('click','#data_sort',function(){
            var thix = $(this);
            var selected_group_item = $('#selected_group_item');
            var supplier_id = selected_group_item.find('#supplier_id').val();
            var start_date = selected_group_item.find('#start_date').val();
            var branch_id = $('#branch_id').val();
            var group_item_id = selected_group_item.find('input[name="group_item_id"]:checked').val();
            var validate = true;
            var qty_action = $('#qty_action').val();
            var qty_filter = $('#qty_filter').val();

            /*if(valueEmpty(supplier_id) && valueEmpty(group_item_id)){
                toastr.error("at least One is required between Group Item Or Supplier");
                validate = false;
                return true;
            }*/

            if(valueEmpty(supplier_id))
            {
                if(valueEmpty(group_item_id)){
                    toastr.error("at least One is required between Group Item");
                    validate = false;
                    return true;
                }
            }

            if(valueEmpty(branch_id.length)){
                toastr.error("at least One Branch is required");
                validate = false;
                return true;
            }
            if(validate && xhrGetData){
                xhrGetData = false;
                $('.table_pit_list>tbody').html(pageSpinner);
                var formData = {
                    supplier_id: supplier_id,
                    start_date: start_date,
                    group_item_id: group_item_id,
                    branch_id: branch_id,
                    qty_action: qty_action,
                    qty_filter: qty_filter,
                };
                var url = '/stock-audit-adjustment/product-item-tax-product-list';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    beforeSend: function( xhr ) {
                        $('#selected_group_item').addClass('pointerEventsNone');
                    },
                    success: function(response,data) {
                        if(response.status == 'success'){
                            var tbody = $('.table_pit_list>tbody.erp_data__grid_body');
                            tbody.html("");
                            var list = response.data.products;
                            var length = list.length;
                            var notShow = ["",undefined,null,NaN,"NaN"];
                            for(var i=0;i<length;i++){
                                var row = list[i];
                                var tds = "";
                                var sr = i+1;

                                var multi_val = row['product_id']+'@'+row['product_barcode_id']+'@'+row['product_barcode_barcode']+'@'+row['uom_id']+'@'+row['product_barcode_packing']+'@'+row['stock'];

                                tds += "<td>"+sr+"</td>";
                                tds += "<td>"+row['product_barcode_barcode']+"</td>";
                                tds += "<td>"+row['product_name']+"</td>";
                                tds += "<td>"+row['stock']+"</td>";
                                tds += "<td>"+row['uom_name']+"</td>";
                                tds += "<td>"+row['group_item_name']+"</td>";
                                tds += "<td>"+row['group_item_parent_name']+"</td>";
                                tds += "<td> " +
                                            "<label class='kt-checkbox kt-checkbox--default'>"+
                                                '<input type="hidden" data-id="stock_quantity" class="stock_quantity" value="'+row['stock']+'" name="pdd['+ i +'][stock_quantity]">'+
                                                '<input type="hidden" data-id="product_barcode_packing" class="product_barcode_packing" value="'+row['product_barcode_packing']+'" name="pdd['+ i +'][product_barcode_packing]">'+
                                                '<input type="hidden" data-id="pd_barcode" class="pd_barcode" value="'+row['product_barcode_barcode']+'" name="pdd['+ i +'][pd_barcode]">'+
                                                '<input type="hidden" data-id="uom_id" class="uom_id" value="'+row['uom_id']+'" name="pdd['+ i +'][uom_id]">'+
                                                '<input type="hidden" data-id="product_barcode_id" class="product_barcode_id" value="'+row['product_barcode_id']+'" name="pdd['+ i +'][product_barcode_id]">'+
                                                '<input type="hidden" data-id="product_id" class="product_id" value="'+row['product_id']+'" name="pdd['+i+'][product_id]">'+
                                                '<input type="checkbox" data-id="check_id" id="check_id'+sr+'" class="check_id" value="'+multi_val+'" name="check_id[]">'+
                                                '<span></span>'+
                                            "</label>"+
                                        "</td>";
                                var tr = "<tr>"+tds+"</tr>";
                                tbody.append(tr);
                            }
                            toastr.success(response.message);

                        }else{
                            toastr.error(response.message);
                            $('.table_pit_list>tbody').html("");
                        }
                        xhrGetData = true;
                        $('#selected_group_item').removeClass('pointerEventsNone');
                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        xhrGetData = true;
                        $('#selected_group_item').removeClass('pointerEventsNone');
                        $('.table_pit_list>tbody').html("");
                    }
                });
            }
        });

        $(document).on('click','.data_clear',function(){
            $('.erp_data__grid_body').html("");
        })
        $(document).on('click','.checkAll',function(){
            if($(this).prop('checked')) {
                $('.erp_data__grid_body>tr>td:last-child').each(function(){
                    $(this).find('input').prop('checked',true);
                });
            }else{
                $('.erp_data__grid_body>tr>td:last-child').each(function(){
                    $(this).find('input').prop('checked',false);
                });
            }
        })
    </script>
    <script>
        $(document).on('click','#header_input_clear_data',function(){
            $("#data_bank_reconciliation>thead input").val("");
            $('#data_bank_reconciliation>thead input').each(function(){
                var val = $(this).val();
                var index = $(this).parent('th').index();
                var arr = {
                    index : index,
                    val : val
                }
                funFilterDataRow1(arr);
            })
        })
        $(document).on('keyup','.erp_data__inline_filter>thead input',function(){
            var val = $(this).val();
            var index = $(this).parent('th').index();
            var arr = {
                index : index,
                val : val
            }
            funFilterDataRow1(arr);
        })

        function funFilterDataRow1(arr) {
            var input, filter, table, tr, td, i, txtValue;
            input = arr.val;
            var td_index = arr.index;
            filter = input;
            var id = $('.erp_data__inline_filter').attr('id');
            
            if(id == undefined){
                id = "table_filter";
                $('.erp_data__inline_filter').attr('id',id);
            }
            table = document.getElementById(id);
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[td_index];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    
                    if (txtValue.indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
@endsection
