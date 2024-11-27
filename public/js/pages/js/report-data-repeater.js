// Class definition
var KTFormRepeater = function() {
    var dynamic_user_criteria_repeater = function() {
        $('#dynamic_user_criteria_repeater').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                // 'text-input': 'foo'
            },
            show: function() {
                $('.report_dynamic_column_type, #report_dynamic_column_type_validate').select2({
                    placeholder: "Select"
                });
                $(this).find('input[type="color"]').val('#e2e5ec');
                $(this).find('.transparent').prop('checked', true).attr('checked',true);
                $(this).find('.column_toggle').prop('checked', true).attr('checked',true);
                $(this).slideDown();
            },
            ready: function (setIndexes) {
                $('.report_dynamic_column_type, #report_dynamic_column_type_validate').select2({
                    placeholder: "Select"
                });
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    return {
        // public functions
        init: function() {
            dynamic_user_criteria_repeater();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormRepeater.init();

});
