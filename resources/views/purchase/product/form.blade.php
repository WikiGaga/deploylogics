@extends('layouts.template')
@section('title', 'Product')

@section('pageCSS')
    <style>
        .barcode_nav .nav-item .nav-link{
            color: #0abb87 !important;
            background: #e6f8f3 !important;
            border-right: 1px solid #0abb87 !important;
        }
        .barcode_nav .nav-item .nav-link.active,
        .barcode_nav .nav-item .nav-link:active,
        .barcode_nav .nav-item .nav-link:hover {
            background: #0abb87 !important;
            color: #fff !important;
        }
        /*.auto-barcode-generate{
            cursor:pointer;
        }*/
        .invalid-feedback:empty {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        $new = 'new';
        $edit = 'edit';
        $view = 'view';
        if($case == $new){
            $code = $data['document_code'];
            $product_entry_status = 1;
            $product_can_sale = 1;
        }
        if($case == $edit || $case == $view){
            $current = $data['current'];
            $id = $current->product_id;
            $code = $current->product_code;
            $product_name = $current->product_name;
            $product_short_name = $current->product_short_name;
            $product_arabic_name = $current->product_arabic_name;
            $product_arabic_short_name = $current->product_arabic_short_name;
            $product_entry_status = $current->product_entry_status;
            $product_can_sale = $current->product_can_sale;
        }
        $url = "";
        if($case == $edit || $case == $new){
            $url = action('Purchase\ProductCardController@store',isset($id)?$id:"");
        }

    @endphp
    @permission($data['permission'])
    <form id="product_form" class="master_form kt-form" method="post" action="{{ $url }}" enctype="multipart/form-data">
    @csrf
    @if($case == $new)
        <input type="hidden" value='product_add' id="form_type">
    @endif
    @if($case == $edit || $case == $view)
        <input type="hidden" value='product_edit' id="form_type">
        <input type="hidden" value='{{$data['page_data']['title']}}' id="document_title">
        <input type="hidden" value='product' id="document_name">
        <input type="hidden" value='{{$data['current']->product_id}}' id="document_id">
        <input type="hidden" value='product' id="prefix_url">
    @endif

    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <div class="kt-portlet__body">
                    <div class="row form-group-block">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="erp-page--title">
                                        {{isset($code)?$code:""}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 text-right">
                            @if($case == $view)
                                @permission($data['permission'])
                                <a href="/product/form/{{isset($id)?$id:""}}" class="btn btn-sm btn-success">Edit</a>
                                @endpermission
                            @endif
                            @if($case == $edit || $case == $view)
                            <a href="javascript:;" class="btn btn-sm btn-primary product_card_detail" data-id="{{isset($id)?$id:""}}" data-val="{{isset($product_name)?$product_name:""}}">Product Detail</a>
                            <a class="product_card_activity_report btn btn-sm btn-primary " href="javascript:;" data-toggle="modal" data-id="{{isset($id)?$id:""}}" data-val="{{isset($product_name)?$product_name:""}}" data-barcode="">Product Activity</a>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Product Name:<span class="required">* </span></label>
                                <div class="col-lg-6">
                                    <input type="text" name="product_name" id="product_name" class="form-control erp-form-control-sm medium_text" value="{{isset($product_name)?$product_name:""}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Product Short Name:</label>
                                <div class="col-lg-6">
                                    <input type="text" name="product_short_name" value="{{isset($product_short_name)?$product_short_name:""}}" class="form-control erp-form-control-sm medium_text">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Local Name:</label>
                                <div class="col-lg-6">
                                    <input type="text" dir="auto" name="product_arabic_name" id="product_arabic_name" class="form-control erp-form-control-sm medium_text" value="{{isset($product_arabic_name)?$product_arabic_name:""}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Local Short Name:</label>
                                <div class="col-lg-6">
                                    <input type="text" dir="auto" name="product_arabic_short_name" class="form-control erp-form-control-sm medium_text" value="{{isset($product_arabic_short_name)?$product_arabic_short_name:""}}">
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <div class="row form-group-block">
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Status:</label>
                                <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                <input type="checkbox" name="product_entry_status" {{$product_entry_status==1?"checked":""}}>
                                                <span></span>
                                            </label>
                                        </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label class="col-lg-6 erp-col-form-label">Can Sale:</label>
                                <div class="col-lg-6">
                                        <span class="kt-switch kt-switch--sm kt-switch--icon">
                                            <label>
                                                <input type="checkbox" name="product_can_sale" {{$product_can_sale==1?"checked":""}}>
                                                <span></span>
                                            </label>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>{{-- end row--}}
                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#barcodes" role="tab">Barcodes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#general_information" role="tab">General Information</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="barcodes" role="tabpanel">
                            @include('purchase.product.element.barcodes')
                        </div>
                        <div class="tab-pane" id="general_information" role="tabpanel">
                            @include('purchase.product.element.general_information')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')
    <script src="/assets/js/pages/crud/file-upload/ktavatar.js" type="text/javascript"></script>
    <script src="/assets/js/pages/crud/forms/widgets/form-repeater.js" type="text/javascript"></script>
@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/purchase/product.js') }}" type="text/javascript"></script>
    <script>
        @if($case == $new)
        if(localStorage.getItem('product_group')){
            $('#product_control_group').val(localStorage.getItem('product_group')).change();
            localStorage.removeItem('product_group');
        }
        @endif
        $(document).ready(function(){
            $("#product_control_group").change(function(){
                var group =  $(this).val();
                if(group) {
                    $.ajax({
                        type:'GET',
                        url:'/product/form-itemtype-data/'+ group,
                        success: function(response, data) {
                            $("#product_item_type").val(0).trigger("change")
                            if(response.data.product_type_group_id != undefined && response.data.product_type_group_id != "" && response.data.product_type_group_id != null){
                                $("#product_item_type").val(response.data.product_type_group_id).trigger("change")
                            }
                        }
                    });
                }
            });
        });
        var arr_text_Field = [
            // keys = id, fieldClass, readonly(boolean), require(boolean)
            {
                'id':'product_life_country_name',
                'fieldClass':'product_life_country_name field_readonly',
                'message':'Select Country Name',
                'require':true,
                'readonly':true,
                'type':'select',
                'convertType':'input',
                'getVal':'text',
                'defaultValue':true,
            },
            {
                'id':'period_type',
                'fieldClass':'period_type field_readonly',
                'message':'Select Period Type',
                'require':true,
                'readonly':true,
                'type':'select',
                'convertType':'input',
                'getVal':'text',
                'defaultValue':true,
            },
            {
                'id':'period',
                'fieldClass':'period large_no validNumber validOnlyFloatNumber',
                'message':'Enter period',
                'require':true
            },
        ]
        var arr_hidden_field = ['country'];
        $('.erp_form__grid_header #product_life_country_name').on('change', function() {
            $('.erp_form__grid_header>tr>th:first-child>div>input#country').val($(this).val());
        })
        $('.supplier_branch_name').on('change', function() {
            var thix = $(this);
            var tr = thix.parents('tr');
            var val = thix.find('option:selected').val();
            tr.find('.supplier_branch_id').val(val);
        })

    </script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/pages/js/open-inline-help.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/common/table-row-repeated.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/purchase/barcode-get-detail.js') }}" type="text/javascript"></script>
    <script>
        $('.validNumber').keypress(validateNumber);
        $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
        // .create_print_shelf_barcode
        $(document).on('click','.create_print_barcode',function(){
            var data_id = $(this).attr('data-id');
            var product_name = $('#product_name').val();
            var product_arabic_name = $('#product_arabic_name').val();
            var barcode = $(this).parents('.barcode').find('.barcode_repeat_b').val();
            if(data_id != 1){
                if(data_id != 2){
                    toastr.error("Something wrong...");
                    return false;
                }
            }
            if(data_id == 1 || data_id == 2){
                if(product_name.length == 0){
                    toastr.error("Required field product name");
                    return false;
                }
                if(barcode.length == 0){
                    toastr.error("Required field barcode");
                    return false;
                }
            }
            if(data_id == 2){
                if(product_arabic_name.length == 0){
                    toastr.error("Required field product arabic name");
                    return false;
                }
            }
            var formData = {
                data_id : data_id,
                product_name : product_name,
                product_arabic_name : product_arabic_name,
                barcode : barcode,
                rate : $(this).parents('.barcode').find('.label_print_price').val(),
                qty : $(this).parents('.barcode').find('.label_print_total').val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : '{{action('Purchase\ProductController@BarcodeTagPrintGenerate')}}',
                dataType	: 'json',
                data        : formData,
                success: function(response) {
                    console.log(response)
                    if(response['status'] == 'success'){
                        toastr.success("Barcode label ready..");
                        window.open(response['data']['url'], "barcode");
                    }else{

                    }
                }
            });
        });

        //------tax function-----
        $(document).on('click','.tax_status',function(){
            var val = $(this).is(":checked");
            if(val == true){
                $(this).parents('tr').find('.tax_value').attr('required',true);
            }else{
                $(this).parents('tr').find('.tax_value').attr('required',false);
            }
        });

        $(document).on('click' , '.btn-refresh-barcode' , function(e){
            var thix = $(this);
            var product_group = $('#product_control_group').val();
            var product_group_ref = $('#product_control_group').find(':selected').data('refno');
            var current_branch_rate = thix.parents('.barcode').find('#sale_cb_0').val();
            var requiredBarcodeLength = 13;
            if(product_group == ""){
                toastr.error('Please Select Product Group');
                return false;
            }
            if(product_group_ref == ""){
                toastr.error("Selected Product Group don't have any Ref No.");
                return false;
            }
            if(current_branch_rate == ""){
                toastr.error("Enter Rate(s).");
                return false;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type        : 'POST',
                url         : '{{action('Purchase\ProductController@countProductByGroupId')}}',
                dataType	: 'json',
                data        : { id: product_group },
                beforeSend  : function(){
                    $('body').addClass('pointerEventsNone');
                },
                success: function(response) {
                    $('body').removeClass('pointerEventsNone');
                    if(response['status'] == 'success'){
                        var randoms = generate(2);
                        var barcode = `${product_group_ref}${response.data.count}${randoms}`;
                        current_branch_rate = `${current_branch_rate}`.replace('.' , '');
                        var rateLength = `${current_branch_rate}`.length;
                        var zeros = 5 - rateLength;
                        if(rateLength < 5){
                            for (let index = 0; index < zeros; index++) {
                                current_branch_rate = `${0}${current_branch_rate}`;
                            }
                        }
                        barcode = `${barcode}${current_branch_rate}`;
                        thix.parents('.form-group-block.input-group').find('.barcode_repeat_b').val('').val(barcode);
                    }else{
                        toastr.error('Something went wrong!');
                    }
                }
            });
        });

        function generate(n) {
            var add = 1, max = 12 - add;   // 12 is the min safe number Math.random() can generate without it starting to pad the end with zeros.

            if ( n > max ) {
                    return generate(max) + generate(n - max);
            }

            max        = Math.pow(10, n+add);
            var min    = max/10; // Math.pow(10, n) basically
            var number = Math.floor( Math.random() * (max - min + 1) ) + min;

            return ("" + number).substring(add);
        }

        $(document).on('change','.tax_group_block',function(){
            var tax_value = $(this).find('option:selected').attr('data-id');
            var tax_value_val = $(this).find('option:selected').val();
            var ctabTable = $(this).parents('.rate_content').find('.tblR');
            ctabTable.find('tbody>tr').each(function(){
                $(this).find('.tax_group').val(tax_value_val).change();
                $(this).find('.tax_value').val(tax_value);
                var sale_rate = $(this).find('.sale_rate').val();
                if(sale_rate && sale_rate != 0 && tax_value != 0 && tax_value){
                    var calc_tax = parseFloat(sale_rate) / 100 * parseFloat(tax_value);
                    var inclusive_tax_price = parseFloat(sale_rate) + parseFloat(calc_tax);
                    $(this).find('.inclusive_tax_price').val(parseFloat(inclusive_tax_price).toFixed(3));
                }else{
                    $(this).find('.inclusive_tax_price').val(sale_rate);
                }
                funcGPAmountCalc($(this));
            })
        })
        $(document).on('keyup','.hs_code_block',function(){
            var hs_code_val = $(this).val();
            var ctabTable = $(this).parents('.rate_content').find('.tblR');
            ctabTable.find('tbody>tr').each(function(){
                $(this).find('.hs_code').val(hs_code_val);
            })
        })
        $(document).on('change','.gst_calculation_block',function(){
            var gst_val = $(this).find('option:selected').val();
            var ctabTable = $(this).parents('.rate_content').find('.tblR');
            ctabTable.find('tbody>tr').each(function(){
                $(this).find('.gst_calculation_id').val(gst_val).change();
            })
        })

        $(document).on('change','.tax_group',function(){
            var tax_value = $(this).find('option:selected').attr('data-id');
            var tr = $(this).parents('tr');
            tr.find('.tax_value').val(tax_value);

            var sale_rate = tr.find('.sale_rate').val();
            if(sale_rate && sale_rate != 0 && tax_value != 0 && tax_value){
                var calc_tax = parseFloat(sale_rate) / 100 * parseFloat(tax_value);
                var inclusive_tax_price = parseFloat(sale_rate) + parseFloat(calc_tax);
                tr.find('.inclusive_tax_price').val(parseFloat(inclusive_tax_price).toFixed(3));
            }else{
                tr.find('.inclusive_tax_price').val(sale_rate);
            }
            funcGPAmountCalc($(this).parents('tr'));
        })
        $(document).on('keyup','.sale_rate',function(){
            var tax_value = $(this).parents('tr').find('.tax_value').val();
            var sale_rate = $(this).val();
            if(sale_rate && sale_rate != 0 && tax_value != 0 && tax_value){
                var calc_tax = parseFloat(sale_rate) / 100 * parseFloat(tax_value);
                var inclusive_tax_price = parseFloat(sale_rate) + parseFloat(calc_tax);
                $(this).parents('tr').find('.inclusive_tax_price').val(parseFloat(inclusive_tax_price).toFixed(3));
            }else{
                $(this).parents('tr').find('.inclusive_tax_price').val(sale_rate);
            }
            funcGPAmountCalc($(this).parents('tr'));
        })
        $(document).on('keyup','.cost_rate',function(){
            var tax_value = $(this).parents('tr').find('.tax_value').val();
            var cost_rate = $(this).val();
            if(cost_rate && cost_rate != 0 && tax_value != 0 && tax_value){
                var calc_tax = parseFloat(cost_rate) / 100 * parseFloat(tax_value);
                var inclusive_tax_price = parseFloat(cost_rate) + parseFloat(calc_tax);
                $(this).parents('tr').find('.inclusive_tax_price').val(parseFloat(inclusive_tax_price).toFixed(3));
            }else{
                $(this).parents('tr').find('.inclusive_tax_price').val(cost_rate);
            }
            funcGPAmountCalc($(this).parents('tr'));
        })
        function funcGPAmountCalc(tr){
            var sale_rate = tr.find('.sale_rate').val();
            var cost_rate = tr.find('.cost_rate').val();
            if(sale_rate && sale_rate != 0 && cost_rate && cost_rate != 0){
                var gp_amount = parseFloat(sale_rate) - parseFloat(cost_rate);
                tr.find('.gp_amount').val(parseFloat(gp_amount).toFixed(3));
                var gp_perc = (parseFloat(gp_amount) / parseFloat(cost_rate)) * 100;
                tr.find('.gp_perc').val(parseFloat(gp_perc).toFixed(3));
            }
        }

        var reqAutoBarcodeGenerate = true;
        $(document).on('click' , '.auto-barcode-generate' , function(e){
            var thix = $(this);
            if(thix.prop('checked')) {
                if(reqAutoBarcodeGenerate){
                    reqAutoBarcodeGenerate = false;
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type        : 'POST',
                        url         : '{{action('Purchase\ProductCardController@autoBarcodeGenerate')}}',
                        dataType	: 'json',
                        data        : { },
                        beforeSend  : function(){
                            $('body').addClass('pointerEventsNone');
                        },
                        success: function(response) {
                            $('body').removeClass('pointerEventsNone');
                            if(response['status'] == 'success'){
                                var barcode = response.data.barcode;
                                thix.parents('.form-group-block.input-group').find('.barcode_repeat_b').val('').val(barcode);
                            }else{
                                toastr.error('Something went wrong!');
                            }
                            reqAutoBarcodeGenerate = true;
                        },
                        error: function(response,status) {
                            reqAutoBarcodeGenerate = true;
                            $('body').removeClass('pointerEventsNone');
                        }
                    });
                }
            } else {
                thix.parents('.form-group-block.input-group').find('.barcode_repeat_b').val('');
            }
        });

    </script>
@endsection
