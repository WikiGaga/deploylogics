// Class definition
var KTFormRepeater = function() {
    var kt_repeater_report_filter = function() {
        $('#kt_repeater_report_filter').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,

            repeaters: [{
                // (Required)
                // Specify the jQuery selector for this nested repeater
                selector: '.inner-report-filter',
                isFirstItemUndeletable: true,
                show: function () {
                    $('.report_filter_name').select2({
                        placeholder: "Select"
                    });
                    allUserReportingFunc();
                    $(this).slideDown();
                },
                ready: function (setIndexes) {
                    $('.report_value').select2({
                        placeholder: "Select",
                        tags: true
                    });
                    // range picker
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
                    $('.validNumber').keypress(validateNumber);
                    $('.report_filter_name').select2({
                        placeholder: "Select"
                    });
                    $('.report_filter_type').select2({
                        placeholder: "Select"
                    });
                    /*$(document).on('change', '.report_filter_type', function(event) {
                        var that = $(this);
                        var val = $(this).val();
                        var field = '';
                        if(val == 'null' || val == 'not null'){
                            field += '<input type="hidden" value="'+column_type_name+'" name="report_value_column_type_name"/>' ;
                        }else if(column_type_name == 'number' && val == 'between'){
                            field +='<div class="row"><div class="col-lg-6">\n' +
                                '<input type="hidden" value="'+column_type_name+'" name="report_value_column_type_name"/>' +
                                '<label class="erp-col-form-label">From:</label>\n' +
                                '<input type="text" name="report_value_from" class="form-control erp-form-control-sm text-left validNumber">\n' +
                                '</div>'+
                                '<div class="col-lg-6">\n' +
                                '<label class="erp-col-form-label">To:</label>\n' +
                                '<input type="text" name="report_value_to" class="form-control erp-form-control-sm text-left validNumber">\n' +
                                '</div></div>';
                        }else if(val != 0 ){
                            var field_type = '';
                            if(data_case != undefined){
                                for(var i=0; data_case.length > i; i++){
                                    if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'manufacturer_id'){
                                        field_type += '<option value="'+data_case[i]['manufacturer_id']+'">'+data_case[i]['manufacturer_name']+'</option>';
                                    }
                                    if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'product_item_tags'){
                                        field_type += '<option value="'+data_case[i]['tags_id']+'">'+data_case[i]['tags_name']+'</option>';
                                    }
                                    if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'product_name'){
                                        field_type += '<option value="'+data_case[i]['product_name']+'">'+data_case[i]['product_name']+'</option>';
                                    }
                                    if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'group_item_name'){
                                        field_type += '<option value="'+data_case[i]['group_item_name']+'">'+data_case[i]['group_item_name']+'</option>';
                                    }
                                }
                            }
                            field +='<div class="row"><div class="col-lg-12">\n' +
                                '<input type="hidden" value="'+column_type_name+'" name="report_value_column_type_name"/>' +
                                '<label class="erp-col-form-label">Value:</label>\n' +
                                '<div class="erp-select2">' +
                                '<select class="form-control erp-form-control-sm report_value " multiple name="report_value">' +
                                '<option value="0">Select</option>' +
                                field_type +
                                '</select>' +
                                '</div>' +
                                '</div></div>';

                            if(val == 'yes' || val == 'no'){
                                field = '<input type="hidden" value="'+column_type_name+'" name="report_value_column_type_name"/>' ;
                            }
                        }
                        if(column_type_name == 'date' && val == 'between'){
                            field ='<div class="row"><div class="col-lg-12">' +
                                '<input type="hidden" value="'+column_type_name+'" name="report_value_column_type_name"/>' +
                                '<label class="erp-col-form-label">Select Date Range:</label>' +
                                '<div class="erp-selectDateRange"> <div class="input-daterange input-group" id="kt_datepicker_5">' +
                                '<input type="text" class="form-control erp-form-control-sm" name="report_value_from" />' +
                                '<div class="input-group-append">' +
                                '<span class="input-group-text erp-form-control-sm">To</span>' +
                                '</div>' +
                                '<input type="text" class="form-control erp-form-control-sm" name="report_value_to" />' +
                                '</div></div>' +
                                '</div></div>';
                        }
                        that.parents('.report_filter_block').find('#report_filter_filed').html(field);
                        $('.validNumber').keypress(validateNumber);
                        $('.report_value').select2({
                            placeholder: "Select",
                            tags: true
                        });
                        if(column_type_name == 'number'){
                            $('.select2-search__field').keypress(validateNumber);
                        }
                        // range picker
                        var arrows = {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        }
                        $('#kt_datepicker_5').datepicker({
                            rtl: KTUtil.isRTL(),
                            todayHighlight: true,
                            format:'dd-mm-yyyy',
                            templates: arrows
                        });
                        console.log('5');
                        setIndexes();
                    });*/
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            }],
            show: function () {
                $('.report_filter_name').select2({
                    placeholder: "Select"
                });
                allUserReportingFunc();
                $(this).slideDown();
            },
            ready: function (setIndexes) {
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    return {
        // public functions
        init: function() {
            kt_repeater_report_filter();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormRepeater.init();

});


