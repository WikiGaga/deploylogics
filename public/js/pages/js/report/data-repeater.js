// Class definition
var KTFormRepeater = function() {
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
                     var total_len = $(this).parents('div[data-repeater-list="inner_filterList"]').find('.filter_block').length;
                     for(var i=0;i<total_len;i++){
                         $(this).parents('div[data-repeater-list="inner_filterList"]').find('.filter_block:eq('+(i)+')').attr('inner-id',i);
                     }
                    $(this).find('.report_fields_name').html(column_keys);
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
                    $(this).attr('inner-id',0);
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

                    var that = $(this).parents('div[data-repeater-list="inner_filterList"]')
                    var total_len = that.find('.filter_block').length;
                    for(var i=0;i<total_len;i++){
                        that.find('.filter_block:eq('+(i)+')').attr('inner-id',i);
                    }
                },
                hide: function (deleteElement) {
                    var thix = $(this);
                    var that_eq = thix.attr('inner-id');

                    var that = thix.parents('div[data-repeater-list="inner_filterList"]')
                    var total_len = parseInt(that.find('.filter_block').length)-1;
                    thix.slideUp(deleteElement);
                    var newEq = 0;
                    for(var i=0;i<total_len;i++){
                        if(that_eq == newEq){
                            newEq = newEq+1;
                        }
                        that.find('.filter_block:eq('+newEq+')').attr('inner-id',i);
                        newEq += 1;
                    }
                    if(total_len == 1){
                        thix.parents('.inner-repeater').find('.inner_clause_item_input').val("");
                        thix.parents('.inner-repeater').find('.inner_clause_item').html("?");
                    }
                }
            }],
            show: function () {
                var total_len = $(this).parents('div[data-repeater-list="outer_filterList"]').find('.outer-filter_block').length;
                for(var i=0;i<total_len;i++){
                    $(this).parents('div[data-repeater-list="outer_filterList"]').find('.outer-filter_block:eq('+(i)+')').attr('outer-id',i);
                }
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
            ready: function(setIndexes){
                $(this).attr('outer-id',0);
                $('.report_fields_name').select2({
                    placeholder: "Select"
                });
                $('.report_condition').select2({
                    placeholder: "Select"
                });
            },
            hide: function (deleteElement) {
                var thix = $(this);
                var that_eq = thix.attr('outer-id');

                var that = thix.parents('div[data-repeater-list="outer_filterList"]')
                var total_len = parseInt(that.find('.outer-filter_block').length)-1;
                thix.slideUp(deleteElement);
                var newEq = 0;
                for(var i=0;i<total_len;i++){
                    if(that_eq == newEq){
                        newEq = newEq+1;
                    }
                    that.find('.outer-filter_block:eq('+newEq+')').attr('outer-id',i);
                    newEq += 1;
                }
            }
        });
    }
    return {
        // public functions
        init: function() {
            kt_repeater_1();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormRepeater.init();

});
