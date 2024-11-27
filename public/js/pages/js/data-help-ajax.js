"use strict";
// Class definition

var KTDatatableRemoteAjaxDemo = function() {
    // Private functions

    // basic demo
    var demo = function() {
        window.localStorage.clear();
        localStorage.removeItem('help_datatable_supplierHelp-1-meta');
        var statuses = [];
        var table = $('.kt-datatable');
        var tableUrl = table.attr('data-url');
        var statusClasses = [' m-badge--metal', ' m-badge--success'];

        var colmnsData = [];
        for (var key in dataFields) {
            if (dataFields.hasOwnProperty(key)) {
                var colmnData = {
                    field: key,
                    sortable: 'asc',
                    title: dataFields[key],
                }
                colmnsData.push(colmnData)
            }
        }
        console.log("dataFields: "+ JSON.stringify(dataFields));
        console.log("dataHideFields: "+ JSON.stringify(dataHideFields));
        for (var key in dataHideFields) {
            if (dataHideFields.hasOwnProperty(key)) {
                var colmnData = {
                    field: dataHideFields[key],
                    visible : true,
                    title: dataHideFields[key],
                }
                colmnsData.push(colmnData)
            }
        }

        //console.log("L: "+ JSON.stringify(colmnsData));

        var datatable = table.KTDatatable({
            // datasource definition

            data: {
                type: 'remote',
                source: {
                    read: {
                        method: 'POST',
                        url: tableUrl,
                        map: function(raw) {
                            // sample data mapping
                            console.log(raw);
                            var dataSet = raw;
                            statuses = dataSet.statuses;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            return dataSet;
                        },
                        params: {
                            help_view: 'popup',
                        }
                    },
                },
                pageSize: 200,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },
            layout: {
                scroll: false,
                footer: false,
                height: null
            },
            sortable: true,
            pagination: false,
            toolbar: {
                items: {
                    pagination: {
                        pageSizeSelect: [10, 20, 30, 50, 100],
                    },
                },
            },
            search: {
                input: $('#generalSearch'),
            },
            rows: {
                callback: function() {},
                // auto hide columns, if rows overflow. work on non locked columns
                autoHide: false,
            },
            // columns definition
            columns: colmnsData,
        });

        $('#kt_form_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });
        $('#generalSearch').on('keydown', function(e) {
            if(e.which == 40){
                e.preventDefault();
                if($('.kt-datatable tbody tr').hasClass('highlight')){
                    var b = $('.ajax_data_table tbody tr.highlight').index();
                    highlight(b + 1);
                }else{
                    highlight(0);
                }
            }
            if(e.which == 38){
                e.preventDefault();
                if($('.kt-datatable tbody tr').hasClass('highlight')){
                    var b = $('.ajax_data_table tbody tr.highlight').index();
                    highlight(b - 1);
                }else{
                    highlight(-1);
                }
            }
            if($('.kt-datatable tbody tr').hasClass('highlight')){
                $("#KTScrollContainer .kt-datatable__table").scrollTop(0);//set to top
                $("#KTScrollContainer .kt-datatable__table").scrollTop($('.highlight:first').offset().top-$("#KTScrollContainer .kt-datatable__table").height());
            }
            if(e.which == 39){
                $('.kt-datatable tbody tr').removeClass('highlight');
                $('input#generalSearch').focus();
            }
            if(e.which == 13 && $('.kt-datatable tbody tr').hasClass('highlight')){
                e.preventDefault();
                var id = $(this).parents('.modal-content').find('div.modal_help').attr('id');
                $("#"+id+' tr.kt-datatable__row--hover').dblclick();
            }
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
    var eventsCapture = function() {
        $('.kt-datatable').on('kt-datatable--on-init', function() {

        }).on('kt-datatable--on-layout-updated', function() {
            matchDate();
        }).on('kt-datatable--on-ajax-done', function() {

        })
    };
    return {
        // public functions
        init: function() {
            demo();
            eventsCapture();
        },
    };
}();
function highlight(tableIndex) {
    // Just a simple check. If .highlight has reached the last, start again
    if( (tableIndex+1) > $('.kt-datatable tbody tr').length )
        tableIndex = 0;

    // Element exists?
    if($('.kt-datatable tbody tr:eq('+tableIndex+')').length > 0)
    {
        // Remove other highlights
        if($('.kt-datatable tbody tr').hasClass('highlight kt-datatable__row--hover')){
            $('.kt-datatable tbody tr').removeClass('highlight kt-datatable__row--hover');
        }
        // Highlight your target
        $('.kt-datatable tbody tr:eq('+tableIndex+')').addClass('highlight kt-datatable__row--hover');
    }
}

function matchDate(){
    $('.modal_help>table>tbody>tr>td[data-field="demand_approval_dtl_date"] span' ).each(function(){
        var d_date = $(this)
        var str = d_date.text();
        var d = new Date(str);
        var month = '' + (d.getMonth() + 1);
        var day = '' + d.getDate();
        var year = d.getFullYear();
        var returnDate =  pad(day) +'-'+ pad(month) +'-'+ year;
        d_date.html(returnDate);
    });
}
function pad(d) {
    return (d < 10) ? '0' + d.toString() : d.toString();
}
jQuery(document).ready(function() {
    KTDatatableRemoteAjaxDemo.init();
});
