// Class definition
var KTFormRepeater = function() {

    // Private functions
    var demo1 = function() {
        $('#kt_repeater_1').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,

            repeaters: [{
                // (Required)
                // Specify the jQuery selector for this nested repeater
                selector: '.inner-repeater',
                isFirstItemUndeletable: true,
                show: function () {
                    $(this).find('.report_fields_name').html(cloumnsList);
                    $('.report_fields_name').select2({
                        placeholder: "Select"
                    });
                    $('.report_condition').select2({
                        placeholder: "Select"
                    });
                    allReportingFunc();
                    $(this).slideDown();
                },
                ready: function (setIndexes) {
                    $('.report_fields_name').select2({
                        placeholder: "Select"
                    });
                    $('.report_condition').select2({
                        placeholder: "Select"
                    });
                    var arrows = {
                        leftArrow: '<i class="la la-angle-left"></i>',
                        rightArrow: '<i class="la la-angle-right"></i>'
                    }
                    $('.kt_datepicker_5').datepicker({
                        rtl: KTUtil.isRTL(),
                        todayHighlight: true,
                        format:'dd-mm-yyyy',
                        templates: arrows
                    });
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            }],
            show: function () {
                $(this).find('.report_fields_name').html(cloumnsList);

                $('.report_fields_name').select2({
                    placeholder: "Select"
                });
                $('.report_condition').select2({
                    placeholder: "Select"
                });
                allReportingFunc();
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    var kt_repeater_metric = function() {
        $('#kt_repeater_metric').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                'text-input': 'foo'
            },
            show: function () {
                $(this).find('.reporting_select_metric').html(cloumnsList);
                $('.reporting_select_metric').select2({
                    placeholder: "Select"
                });
                allReportingFunc();
                $(this).slideDown();
            },
            repeaters: [{
                // (Required)
                // Specify the jQuery selector for this nested repeater
                selector: '.metric_inner_repeater',
                isFirstItemUndeletable: true,
            }],
            ready: function (setIndexes) {
                $('body').on('change', '.reporting_select_metric', function(event) {
                    var that = $(this);
                    var table_name = $('#reporting_table_name').val();
                    var val = $(this).val();
                    url = '/report/get-filed-metric/'+table_name+'/'+val;
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data:{_token: CSRF_TOKEN},
                        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                        success: function(response, status){
                            console.log(response);
                            if(response.status == 'success') {
                                var metric_block_len  = $('.metric_block').length;
                                var data = response['data'];
                                if(data == null){
                                    that.parents('.metric_block').find('.metric_data').html('');
                                    return false;
                                }
                                var dataType = response['data']['data_type'];

                                if(dataType == "NUMBER"){
                                    var dataEle = '';
                                    dataEle +=  '<div class="col-lg-4">'+
                                        '<label class="erp-col-form-label">Aggregation:</label>'+
                                        '<div class="erp-select2 report-select2">'+
                                        '<select class="form-control erp-form-control-sm reporting_select_aggregation" name="reporting_select_aggregation">'+
                                        '<option value="0">Select</option>'+
                                        '<option value="sum">Sum</option>'+
                                        '<option value="avg">Average</option>'+
                                        '<option value="count">Count</option>'+
                                        '<option value="countdistinct">Count Distinct</option>'+
                                        '<option value="min">Min</option>'+
                                        '<option value="max">Max</option>'+
                                        '</select>'+
                                        '</div>'+
                                        '</div>';
                                    dataEle +=  '<div class="col-lg-4">'+
                                        '<label class="erp-col-form-label">Types:</label>'+
                                        '<div class="erp-select2 report-select2">'+
                                        '<select class="form-control erp-form-control-sm reporting_select_types" name="reporting_select_types">'+
                                        '<option value="0">Select</option>'+
                                        '<option value="number">Number</option>'+
                                        '<option value="percent">percent</option>'+
                                        '<option value="currency">Currency</option>'+
                                        '</select>'+
                                        '</div>'+
                                        '</div>';
                                    dataEle +=  '<div class="col-lg-4">'+
                                        '<label class="erp-col-form-label">Running Calculation:</label>'+
                                        '<div class="erp-select2 report-select2">'+
                                        '<select class="form-control erp-form-control-sm reporting_select_calculation" name="reporting_select_calculation">'+
                                        '<option value="0">Select</option>'+
                                        '<option value="sum">Running Sum</option>'+
                                        '<option value="min">Running Min</option>'+
                                        '<option value="max">Running Max</option>'+
                                        '<option value="count">Running Count</option>'+
                                        '<option value="avg">Running Average</option>'+
                                        '</select>'+
                                        '</div>'+
                                        '</div>';
                                    that.parents('.metric_block').find('.metric_data').html('<div class="row">'+dataEle+'</div>');

                                    $('.reporting_select_aggregation,.reporting_select_types,.reporting_select_calculation').select2({
                                        placeholder: "Select"
                                    });

                                    setIndexes(); // this will reindex the list
                                    console.log('');
                                }

                                //  toastr.success(response.message);
                            }
                            else{
                                toastr.error(response.message);
                            }
                        },
                        error: function(response,status) {
                            // console.log(response);
                        },
                    });
                });
              //  $dragAndDrop.on('drop', setIndexes);
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    var kt_repeater_user_filter = function() {
        $('#kt_repeater_user_filter').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
               // 'text-input': 'foo'
            },
            show: function () {
                $(this).find('.reporting_user_filter_name').html(cloumnsList);
                $('.reporting_user_filter_name').select2({
                    placeholder: "Select"
                });
                $('.reporting_user_filter_type').select2({
                    placeholder: "Select"
                });
                allReportingFunc();
                $(this).slideDown();
            },
            ready: function (setIndexes) {
                $('.reporting_user_filter_name').select2({
                    placeholder: "Select"
                });
                $('.reporting_user_filter_type').select2({
                    placeholder: "Select"
                });
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    var demo2 = function() {
        $('#kt_repeater_2').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        });
    }

    var kt_repeater_barcode = function() {

        $('#kt_repeater_barcode').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                var countAvatar = parseInt($('#product_form').find('.product_img').length)
                $(this).find('.kt-avatar').attr('id', 'kt_user_avatar_'+countAvatar);
                $(this).find('.barcode_packing').attr('readonly', false).val('1');
                $(this).find('.base_barcode').attr('checked', false);
                $('.validNumber').keypress(validateNumber);
                var d = new Date();
                var t = d.getTime();
                var i = 0;
                var j = 0;
                var x = 0;
                var y = 0;
                var z = 0;
                var count = 0;
                var totcount = 0;

                $('.barcode_repeat').each(function () {
                    count ++;
                });
                totcount = (count-1);

                var branchArray = [];
                $(".branch_R").each(function(){
                    if($(this).val()>0){
                        branchArray.push($(this).val());
                    }
                });

                var rateArray = [];
                $(".rate_R").each(function(){
                    if($(this).val()>0){
                        rateArray.push($(this).val());
                    }
                });
                var taxArray = [];
                $(".branch_T").each(function(){
                    if($(this).val()>0){
                        taxArray.push($(this).val());
                    }
                });

                $(this).attr('item-id',t)
                $(this).find('.rate').attr('href','#rate_'+t);
                $(this).find('.rate_content').attr('id','rate_'+t);

                var tblBranchArray = [];
                var tblBranchVal = $('#kt_repeater_barcode').find('.barcode:first-child').find(".tab-content>.rate_content>.tblR>tbody>tr>td:first-child")
                tblBranchVal.each(function(){
                    tblBranchArray.push($(this).find("input[type='hidden']").val());
                });

                var tblRArray = [];
                var tblRVal = $('#kt_repeater_barcode').find('.barcode:first-child').find(".tab-content>.rate_content>.tblR>tbody>tr")
                tblRVal.find("td>input[type='hidden']").each(function(){
                    tblRArray.push($(this).val());
                });

                var tblSaleRate = $("#rate_"+t).find('.tblR>tbody');
                tblSaleRate.find("td").each(function(){
                    $(this).find("input[type='hidden']").val(tblRArray[x]);
                    x++;
                });
                var tblPurcRate = $("#rate_"+t).find('.tblPurcRate>tbody>tr>td:first-child');
                tblPurcRate.each(function(index){
                    console.log(tblBranchArray[index]);
                    $(this).find("input[type='hidden']").val(tblBranchArray[index]);
                });

                $(this).find('.inventory_shelf_stock').attr('href','#inventory_shelf_stock_'+t);
                $(this).find('.inventory_shelf_stock_content').attr('id','inventory_shelf_stock_'+t);
                $( "#inventory_shelf_stock_"+t+'> .tblSL>tbody>tr' ).each(function( index ) {
                    $(this).find('td>.branch_SL').val(branchArray[i]);
                    i++;
                });
                $( "#inventory_shelf_stock_"+t+'> .tblSSL>tbody>tr' ).each(function( index ) {
                    $(this).find('td>.branch_SSL').val(branchArray[j]);
                    j++;
                });

                $(this).find('.tax').attr('href','#tax_'+t);
                $(this).find('.tax_content').attr('id','tax_'+t);
                $( '#tax_'+t+'>.tblT>tbody>tr' ).each(function( index ) {
                    $(this).find('td>.branch_T').val(branchArray[index]);
                });

                $(this).find('.uom_packing').attr('href','#uom_packing_'+t);
                $(this).find('.uom_packing_content').attr('id','uom_packing_'+t);
                $( '#uom_packing_'+t+'>.tblPack>tbody>tr' ).find('input[name="product_barcode_data['+totcount+'][uom_packing_packing]"]').attr('readonly',false);
                var uomVal = $( '#uom_packing>.tblPack>tbody>tr' ).find('#uom_packing_uom option:selected').val();
                $( '#uom_packing_'+t+'>.tblPack>tbody>tr' ).find('#uom_packing_uom').val(uomVal);

                $(this).find('.purchase_foc').attr('href','#purchase_foc_'+t);
                $(this).find('.purchase_foc_content').attr('id','purchase_foc_'+t);

                $('.tag-select2, #tag-select2_validate').select2({
                    placeholder: "Add a tag",
                    tags: true
                });

                $('.barcode_rate_purchase_rate_base, #barcode_rate_purchase_rate_base_validate').select2({
                    placeholder: "Select"
                });
                $('.shelf_stock_salesman, #shelf_stock_salesman_validate').select2({
                    placeholder: "Select"
                });
                $('.shelf_stock_location, #shelf_stock_location_validate').select2({
                    placeholder: "Select"
                });
                $('.uom_packing_uom, #uom_packing_uom_validate').select2({
                    placeholder: "Select"
                });
                $('.uom_packing_color_tag, #uom_packing_color_tag_validate').select2({
                    placeholder: "Select"
                });
                $('.uom_packing_size_tag, #uom_packing_size_tag_validate').select2({
                    placeholder: "Select"
                });
                $('.uom_packing_other_tag, #uom_packing_other_tag_validate').select2({
                    placeholder: "Select"
                });
                $('.weight_id, #weight_id_validate').select2({
                    placeholder: "Select"
                });
                $(this).find('.kt-switch').find('input[type="checkbox"]').prop('checked', false );
                $(this).find('.kt-switch').find('.product_barcode_weight_apply').prop('checked', false );
                formRepeaterValidation();
                $(this).slideDown();
                KTAvatarDemo.init();
            },
            hide: function(deleteElement) {
                var thix = $(this);
                var barcode_id = $(this).find('.barcode_repeat_b_id').val();
                if(barcode_id != "" && $('#form_type').val() == 'product_edit'){
                    swal.fire({
                        title: 'Alert?',
                        text: "Are you sure you want to delete this element?",
                        type: 'warning',
                        showCancelButton: false,
                        confirmButtonText: 'Ok'
                    }).then(function(result) {
                        if (result.value) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type        : 'GET',
                                url         : '/product/check-barcode/'+ barcode_id,
                                dataType	: 'json',
                                success: function(response,  data) {
                                    if(response['data']['check'] == true){
                                        swal.fire({
                                            title: 'Alert? Not Delete',
                                            text: "Barcode Exist in Detail Tables!",
                                            type: 'warning',
                                            showCancelButton: false,
                                            confirmButtonText: 'Ok'
                                        });
                                    }else{
                                        thix.slideUp(deleteElement);
                                    }
                                }
                            });
                        }
                    });
                }else{
                    thix.slideUp(deleteElement);
                }
            }
        });
    }

    var kt_repeater_slab = function() {

        $('#kt_repeater_slab').repeater({

            repeaters: [{
                selector: '.inner-repeater'
            }],

            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                var d = new Date();
                var t = $(this).index();
                $(this).attr('item-id',t);
                var prev = $(this).prev();
                var preRequired = prev.find('input.noEmpty');
                var validate = true;

                preRequired.each(element => {
                    var ele = preRequired[element];
                    if(ele.value == ""){
                        ele.focus();
                        validate = false;
                        $(this).remove();
                        return false;
                    }
                });

                // Notice : It will change the Names of the Grid Rows
                // So To, Change the Names of the Inputs and Selects.
                updateNames(true);

                if(validate){
                    $('.kt_datepicker_3').datepicker({
                        todayHighlight: true,
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    }).datepicker("setDate", new Date());
                    // Empty The New Grid
                    $(this).find('.erp_form__grid_body.slab_detail_grid_body').html('');

                    $(this).slideDown();
                }else{
                    toastr.error('Please Fill All The Required Fields');
                }
            },
            hide: function(deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(function(){
                        deleteElement();
                        updateNames();
                    });
                }
            },
            ready: function(){
                updateNames();
            }
        });
    }

    var kt_repeater_coupon = function() {
        var message = 'Please Fill All The Required Fields';
        $('#kt_repeater_coupon').repeater({

            repeaters: [{
                selector: '.inner-repeater'
            }],

            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                var d = new Date();
                var t = $(this).index();
                $(this).attr('item-id',t);
                var prev = $(this).prev();
                var active = $(this);
                var preRequired = prev.find('input.noEmpty');
                var validate = true;

                preRequired.each(element => {
                    var ele = preRequired[element];
                    if(ele.value == ""){
                        ele.focus();
                        validate = false;
                        $(this).remove();
                        return false;
                    }
                });

                // Valaidate The Budget
                var totBudget = $('#donater_budget').val();
                if(totBudget == ""){
                    $('#donater_budget').focus();
                    validate = false;
                }
                var cq = prev.find('.coupon_qty').val();
                var cv = prev.find('.coupon_value').val();
                if(cq == "0"  || cv == "0"){
                    prev.find('.coupon_qty').focus();
                    validate = false;
                    message = 'Coupon Value OR Coupon Qty must be grater than 0';
                    return false;
                }else{
                    if(calculateBudget()){
                        validate = true;
                    }else{
                        validate = false;
                        message = "Budget Limit will Exced.";
                    }
                }

                if(validate){
                    $(this).slideDown(function(){
                        $(this).find('#kt_daterangepicker_3').daterangepicker({
                            buttonClasses: ' btn',
                            applyClass: 'btn-primary',
                            cancelClass: 'btn-secondary'
                        },function(start, end, label) {
                            active.find('#kt_daterangepicker_3 .kt_daterangepicker_3.form-control').val( start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
                        });
                    });
                }else{
                    toastr.error(message);
                    $(this).remove();
                }
            },
            hide: function(deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(function(){
                        deleteElement();
                        calculateBudget();
                    });
                }
            }
        });
    }

    var flowCriteria = function() {
        $('#kt_repeater_flow').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                var d = new Date();
                var t = d.getTime();
                $(this).attr('item-id',t)
                $(this).find('.rep_action').attr('href','#rep_action_'+t);
                $(this).find('.rep_action_content').attr('id','rep_action_'+t);

                $(this).find('.rep_designation').attr('href','#rep_designation_'+t);
                $(this).find('.rep_designation_content').attr('id','rep_designation_'+t);

                $(this).find('.rep_time').attr('href','#rep_time_'+t);
                $(this).find('.rep_time_content').attr('id','rep_time_'+t);

                $(this).find('.rep_bypass').attr('href','#rep_bypass_'+t);
                $(this).find('.rep_bypass_content').attr('id','rep_bypass_'+t);
                $('.kt-select2, #kt-select2_validate').select2({
                    placeholder: "Select"
                });
                $('.tag-select2, #tag-select2_validate').select2({
                    placeholder: "Add a tag",
                    tags: true
                });
                $('.kt_datetimepicker_1').datetimepicker({
                    todayHighlight: true,
                    autoclose: true,
                    format: 'dd-mm-yyyy hh:ii'
                });
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        });
    }

    var bankDistribution = function() {
        $('#kt_repeater_distribution').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                var d = new Date();
                var t = d.getTime();
                var denomination_array = [];
                var x = 0;

                $(this).attr('item-id',t)
                $(this).find('.denomination_table').attr('id','denomination_table_'+t);

                var tbldenominationVal = $('#kt_repeater_distribution').find('.barcode:first-child').find(".denomination_table>.dataTable>tbody>tr>td:first-child")
                tbldenominationVal.each(function(){
                    denomination_array.push($(this).find("input[type='hidden']").val());
                });

                var tblDtlId = $("#denomination_table_"+t).find('.dataTable>tbody');
                tblDtlId.find("tr").each(function(){
                    $(this).find("input[type='hidden']").val(denomination_array[x]);
                    x++;
                });

                $('.kt-select2, #kt-select2_validate').select2({
                    placeholder: "Select"
                });
                $('.tag-select2, #tag-select2_validate').select2({
                    placeholder: "Add a tag",
                    tags: true
                });
                $('.kt_datetimepicker_1').datetimepicker({
                    todayHighlight: true,
                    autoclose: true,
                    format: 'dd-mm-yyyy hh:ii'
                });
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        });
    }

    var demo4 = function() {
        $('#kt_repeater_4').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }

    var demo5 = function() {
        $('#kt_repeater_5').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }

    var demo6 = function() {
        $('#kt_repeater_6').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }

    var TaxValueStatusApplyAll = function() {
        $(document).on('keyup','#TaxValueApplyAll',function(){
            var val = $(this).val();
            if(val == '' || val == NaN){
                val = 0;
            }
            $(this).parents('.tblT').find('tbody>tr').each(function(){
                $(this).find('input.tax_value').val(parseFloat(val).toFixed(3));
            });
        });
        $(document).on('click','#TaxStatusApplyAll',function(){
            if($(this).is(":checked") == true) {
                var checkAll = true
            }else{
                var checkAll = false
            }
            $(this).parents('.tblT').find('tbody>tr').each(function(){
                if(checkAll) {
                    $(this).find('input.tax_status').prop('checked',true)
                }else{
                    $(this).find('input.tax_status').prop('checked',false)
                }
            });
        });
    }

    var makeBaseBarcode = function() {
        $(document).on('click','.base_barcode',function(){
            var val = $(this).is(':checked');
            $(document).find('.base_barcode').prop('checked',false);
            if(val){
                $(this).prop('checked',true);
                toastr.success('Base Barcode update')
            }
        });
    }
    var RateApplyAll = function() {
        $(document).on('keyup','#SaleRateApplyAll',function(){
            var val = $(this).val();
            var th = $(this).parent('th').attr('data-id');
            if(val == '' || val == NaN){
                val = 0;
            }
            $(this).parents('.tblR').find('tbody>tr').each(function(){
                $(this).find('td:nth-child('+th+')>input.sale_rate_rate').val(parseFloat(val).toFixed(3));
            });
        });

        $(document).on('keyup','#PurcRateApplyAll',function(){
            var val = $(this).val();
            if(val == '' || val == NaN){
                val = 0;
            }
            $(this).parents('.tblPurcRate').find('tbody>tr').each(function(){
                $(this).find('input.purchase_rate').val(parseFloat(val).toFixed(3));
            });
        });
        $(document).on('keyup','#CostRateApplyAll',function(){
            var val = $(this).val();
            if(val == '' || val == NaN){
                val = 0;
            }
            $(this).parents('.tblPurcRate').find('tbody>tr').each(function(){
                $(this).find('input.cost_rate').val(parseFloat(val).toFixed(3));
            });
        });
        $(document).on('keyup','#AvgRateApplyAll',function(){
            var val = $(this).val();
            if(val == '' || val == NaN){
                val = 0;
            }
            $(this).parents('.tblPurcRate').find('tbody>tr').each(function(){
                $(this).find('input.avg_rate').val(parseFloat(val).toFixed(3));
            });
        });
    }

    // This Function is For Sales Scheme Form
    function updateNames(decrement = false , prefix = 'scheme_slab_data'){
        $('.kt-margin-b-10.slab.p-3.border.repeater-container').each(function( repeatItem ){
            var itemIndex = $(this).attr('item-id');
            // if(decrement){ itemIndex = parseInt(itemIndex) - 1; }
            var gridBodyRow = $(this).find('.slab_detail_grid_body>tr');
            gridBodyRow.each(function( trindex ){
                $(this).find('input,select').each(function( elementIndex ){
                    $(this).attr('name' , 'scheme_slab_data[' + itemIndex +'][sldtl]['+ trindex +']['+ $(this).data('id') +']');
                });
            });
        });
    }

    return {
        // public functions
        init: function() {
            demo1();
            demo2();
            kt_repeater_barcode();
            //demo3_2();
            demo4();
            demo5();
            demo6();
            flowCriteria();
            bankDistribution();
            kt_repeater_metric();
            kt_repeater_user_filter();
            TaxValueStatusApplyAll();
            RateApplyAll();
            makeBaseBarcode();
            kt_repeater_slab();
            kt_repeater_coupon();
        }
    };
}();
jQuery(document).ready(function() {
    KTFormRepeater.init();
});


