// Class definition
var KTFormRepeater = function() {
    var kt_repeater_user_filter = function() {
        $('#kt_repeater_user_filter').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                // 'text-input': 'foo'
            },
            show: function() {
                $(this).find('.listing_studio_user_filter_name').html(cloumnsList);
                $(this).find('.listing_studio_user_filter_name').select2({
                    placeholder: "Select"
                });
                $(this).find('.listing_studio_user_filter_type').select2({
                    placeholder: "Select"
                });
                $(this).find('.listing_studio_user_case_name').select2({
                    placeholder: "Select"
                });
                $(this).slideDown();
            },
            ready: function (setIndexes) {
                $('.listing_studio_user_filter_name').select2({
                    placeholder: "Select"
                });
                $('.listing_studio_user_filter_type').select2({
                    placeholder: "Select"
                });
                $('.listing_studio_user_case_name').select2({
                    placeholder: "Select"
                });
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    var kt_repeater_1 = function() {
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
                    /* start for Case Edit */
                    var filter_block_len = $(this).find('.filter_block').length
                    for(var i=0; i < filter_block_len ; i++){
                        $(this).find('.filter_block:eq(1)').remove();
                    }
                    $(this).find(".report_fields_name ").val(-1).trigger('change');
                    /* end for Case Edit */
                    $(this).slideDown();
                },
                ready: function (setIndexes) {
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
                /* start for Case Edit */
                    var filter_block_len = $(this).find('.filter_block').length
                    for(var i=1; i < filter_block_len ; i++){
                        $(this).find('.filter_block:eq(1)').remove();
                    }
                    $(this).find(".report_fields_name").val(-1).trigger('change');
                /* end for Case Edit */
                $(this).slideDown();
            },
            ready:function(){
                $('.report_fields_name').select2({
                    placeholder: "Select"
                });
                $('.report_condition').select2({
                    placeholder: "Select"
                });
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
                // 'text-input': 'foo'
            },
            show: function() {
                $(this).find('.listing_studio_metric_column_name').html(cloumnsList);
                $(this).find('.listing_studio_metric_column_name').select2({
                    placeholder: "Select"
                });
                $(this).find('.listing_studio_metric_aggregation').select2({
                    placeholder: "Select"
                });
                $(this).slideDown();
            },
            ready: function (setIndexes) {
                $('.listing_studio_metric_column_name').select2({
                    placeholder: "Select"
                });
                $('.listing_studio_metric_aggregation').select2({
                    placeholder: "Select"
                });
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }

    var kt_user_listing_repeater = function() {
        $('#kt_user_listing_repeater').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,

            repeaters: [{
                // (Required)
                // Specify the jQuery selector for this nested repeater
                selector: '.inner-repeater',
                isFirstItemUndeletable: true,
                show: function () {
                    $('.report_fields_name').select2({
                        placeholder: "Select"
                    });
                    $('.report_condition').select2({
                        placeholder: "Select"
                    });
                    /* start for -Case Edit- */
                    var filter_block_len = $(this).find('.filter_block').length
                    for(var i=0; i < filter_block_len ; i++){
                        $(this).find('.filter_block').eq(i).remove();
                    }
                    $(this).find(".report_fields_name ").val(-1).trigger('change');
                    /* end for -Case Edit- */
                    $(this).slideDown();
                },
                ready: function (setIndexes) {
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
               // $(this).find('.report_fields_name').html(cloumnsList);
                $('.report_fields_name').select2({
                    placeholder: "Select"
                });
                $('.report_condition').select2({
                    placeholder: "Select"
                });
                /* start for -Case Edit- */
                var filter_block_len = $(this).find('.filter_block').length
                for(var i=1; i < filter_block_len ; i++){
                    $(this).find('.filter_block').eq(i).remove();
                }
                $(this).find(".report_fields_name").val(-1).trigger('change');
                /* end for -Case Edit- */
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    return {
        // public functions
        init: function() {
            kt_repeater_user_filter();
            kt_repeater_1();
            kt_repeater_metric();
            kt_user_listing_repeater();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormRepeater.init();

});
