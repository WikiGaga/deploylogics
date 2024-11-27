"use strict";
// Class definition

var KTDatatableHtmlTableDemo = function() {
    // Private functions

    // demo initializer
    var demo = function() {

        var datatable = $('.kt-datatable').KTDatatable({
            data: {
                saveState: {cookie: false},
            },
            search: {
                input: $('#generalSearch'),
            },
            rows: {
                autoHide: false,
            },
            pagination: false,


        });

        $('#kt_form_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });

        $('#kt_form_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Type');
        });

        $('#kt_form_status,#kt_form_type').selectpicker();


        $(".checkbox-menu").on("change", "input[type='checkbox']", function() {
            $(this).closest("li").toggleClass("active", this.checked);
        });
        $(document).on('click', '.allow-focus', function (e) {
            e.stopPropagation();
        });
        $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
            var val = $(this).val();
           // var th_val = $('#data_table').find('thead>tr>th').attr('data-field');
            $('.data_table').find('thead>tr>th').each(function(){
                var th_val = $(this).attr('data-field');
                if(val == th_val){
                    $(this).toggle();
                }
            });
            $('.data_table').find('tbody>tr>td').each(function(){
                var td_val = $(this).attr('data-field');
                if(val == td_val){
                    $(this).toggle();
                }
            });
        });

    };

    return {
        // Public functions
        init: function() {
            // init dmeo
            demo();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableHtmlTableDemo.init();
});
