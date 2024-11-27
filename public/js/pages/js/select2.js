// Class definition
var KTSelect2 = function() {
    // Private functions
    var demos = function() {
        // business add & edit
        $('#business_type, #business_type_validate').select2({
            placeholder: "Select"
        });
        $('#business_currency, #business_currency_validate').select2({
            placeholder: "Select"
        });
        $('#business_nature, #business_nature_validate').select2({
            placeholder: "Select"
        });
        // Product add & edit
        $('#product_control_group, #product_control_group_validate').select2({
            placeholder: "Select"
        });
        $('#product_item_type, #product_item_type_validate').select2({
            placeholder: "Select"
        });
        $('#product_manufacturer, #product_manufacturer_validate').select2({
            placeholder: "Select"
        });
        $('#product_country, #product_country_validate').select2({
            placeholder: "Select"
        });
        $('#product_brand_name, #product_brand_name_validate').select2({
            placeholder: "Select"
        });
        $('#kt_select2_3, #kt_select2_3_validate').select2({
            placeholder: ""
        });
        $('#product_item_tags, #product_item_tags_validate').select2({
            placeholder: ""
        });
        $('#product_color, #product_color_validate').select2({
            placeholder: ""
        });
        $('#product_warranty_period, #product_warranty_period_validate').select2({
            placeholder: "Select"
        });
        $('#product_expiry_base_on, #product_expiry_base_on_validate').select2({
            placeholder: "Select"
        });
        $('#grn_currency, #grn_currency_validate').select2({
            placeholder: "Select"
        });
        $('#currency_id, #currency_id_validate').select2({
            placeholder: "Select"
        });
        $('#PaymentMode, #PaymentMode_validate').select2({
            placeholder: "Select"
        });
        $('#grn_store, #grn_store_validate').select2({
            placeholder: "Select"
        });

        $('#supplier_type, #supplier_type_validate').select2({
            placeholder: "Select"
        });
        $('#country_id, #country_id_validate').select2({
            placeholder: "Select"
        });
        $('#city_id, #city_id_validate').select2({
            placeholder: "Select"
        });

        $('#bank_id, #bank_id_validate').select2({
            placeholder: "Select"
        });
        $('#parent_account_code, #parent_account_code_validate').select2({
            placeholder: "Select"
        });
        $('#tax_type_id, #tax_type_id_validate').select2({
            placeholder: "Select"
        });
        $('#quotation_currency, #quotation_currency_validate').select2({
            placeholder: "Select"
        });
        $('#payment_terms, #payment_terms_validate').select2({
            placeholder: "Select"
        });
        $('#menu_flow_criteria_name, #menu_flow_criteria_name_validate').select2({
            placeholder: "Select"
        });
        $('.kt-select2, #kt-select2_validate').select2({
            placeholder: "Select"
        });

        
        $('select').change(function(){
                $(this).valid();
        });
    }
     var tags = function(){
         $('.kt_select2_tags').select2({
             placeholder: "Select Multiple Items",
             tags: true
         });
         $('.kt_select2_options').select2({
            placeholder: "Select Multiple Options",
            tags: true
        });
         $('#product_specification_tags').select2({
             placeholder: "Add a tag",
             tags: true
         });
         $('#product_item_tags').select2({
             placeholder: "Add a tag",
             tags: true
         });
         $('.tag-select2, #tag-select2_validate').select2({
             placeholder: "Add a tag",
             tags: true
         });
         $('.tag-user2, #tag-user2_validate').select2({
             placeholder: "Add a tag",
             tags: true
         });
         $('#reporting_dimension_column_title, #reporting_dimension_column_title_validate').select2({
             placeholder: "Dimension Titles",
             tags: true
         });
         $('#x_axis, #x_axis_validate').select2({
             placeholder: "Add X_Axis Titles",
             tags: true
         });
     }
    // Public functions
    var timePicker = function() {
        $('.kt_datetimepicker_1').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd-mm-yyyy hh:ii'
        });
    }
    var datePicker = function() {
        var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }
        $('.kt_date, .kt_date_validate').datepicker({
            rtl: KTUtil.isRTL(),
            todayBtn: "linked",
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            templates: arrows,
            // endDate: '+0d',
        });
    }
    return {
        init: function() {
            demos();
            tags();
            timePicker();
            datePicker();
        }
    };
}();

// Initialization
jQuery(document).ready(function() {
    KTSelect2.init();
});
