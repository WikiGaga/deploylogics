"use strict";
// Class definition

var KTDatatableRemoteAjaxDemo = function() {
    // Private functions

    // basic demo
    var demo = function() {
        var table = $('.kt-datatable');
        var tableUrl = table.attr('data-url');
        var datatable = table.KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        method: 'GET',
                        url: tableUrl,
                        // sample custom headers
                        // headers: {'x-my-custom-header': 'some value', 'x-test-header': 'the value'},
                        map: function(raw) {
                            // sample data mapping
                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            return dataSet;
                        },
                    },
                },
                pageSize: 200,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },

            // layout definition
            layout: {
                scroll: false,
                footer: false,
                height: null
            },

            // column sorting
            sortable: true,

            pagination: false,

            search: {
                input: $('#generalSearch'),
            },

            // columns definition
            columns: [
                {
                    field: 'product_name',
                    title: 'Name',
                    sortable: 'asc',
                    width: 110,
                    textAlign: 'center',
                },
                {
                    field: 'product_arabic_name',
                    title: 'Arabic Name',
                    width: 110,
                },{
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    width: 110,
                    overflow: 'visible',
                    autoHide: false,
                    template: function() {
                        return '\
						<a href="/" class="btn btn-sm btn-primary btn-icon btn-icon-sm" title="Edit">\
							<i class="la la-edit"></i>\
						</a>\
						<button data-url="/" id="del" class="btn btn-danger btn-sm btn-icon btn-icon-sm" title="Delete">\
							<i class="la la-trash"></i>\
						</button>\
					';
                    },
                }],

        });

        $('#kt_form_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });

        $('#kt_form_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Type');
        });

        $('#kt_form_status,#kt_form_type').selectpicker();

        $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
            var val = $(this).val();
            // var th_val = $('#data_table').find('thead>tr>th').attr('data-field');
            $('.ajax_data_table').find('thead>tr>th').each(function(){
                var th_val = $(this).attr('data-field');
                if(val == th_val){
                    $(this).toggle();
                }
            });
            $('.ajax_data_table').find('tbody>tr>td').each(function(){
                var td_val = $(this).attr('data-field');
                if(val == td_val){
                    $(this).toggle();
                }
            });
        });

    };

    return {
        // public functions
        init: function() {
            demo();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableRemoteAjaxDemo.init();
});
