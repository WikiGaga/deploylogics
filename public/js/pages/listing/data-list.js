"use strict";
var inline_filter_data = {};
// Class definition

var KTDatatableRemoteAjaxDemo = function() {
    // Private functions

    function normalizeRow(row) {
        Object.keys(row).forEach(function (key) {
            var value = row[key];
            if (typeof value === 'string') {
                // .12 -> 0.12
                if (/^\.\d+$/.test(value)) {
                    value = '0' + value;
                    row[key] = value;
                }
                // -.12 -> -0.12
                else if (/^-\.\d+$/.test(value)) {
                    value = '-0' + value.substring(1);
                    row[key] = value;
                }
                // Format numeric decimal strings to exactly 3 decimal places (e.g. 0.12 -> 0.120)
                if (/^-?\d+\.\d+$/.test(value)) {
                    row[key] = parseFloat(value).toFixed(3);
                }
            }
        });
        return row;
    }

    // basic demo
    var demo = function() {
        localStorage.removeItem('ajax_data-1-meta');
        var table = $('.kt-datatable');
        var tableUrl = table.attr('data-url');
        var dataColumns = [];
        for (var key in dataFields) {
            var fieldType = dataFields[key]['type'];
            var treatAsTemporal = fieldType == 'date' || fieldType == 'datetime'
                || /(_date|^date|created_at|updated_at)$/i.test(key);
            if(fieldType == 'string' && !treatAsTemporal){
                var obj = {
                    field: key,
                    title: dataFields[key]['title'],
                };
            }
            if(treatAsTemporal){
                var obj = {
                    field: key,
                    title: dataFields[key]['title'],
                    template: (function (fieldKey) {
                        return function(row) {
                            var val = row[fieldKey];
                            if (val === undefined || val === null || val === '') {
                                val = row[String(fieldKey).toUpperCase()];
                            }
                            return funcSmartDateTimeFormat(val);
                        };
                    })(key),
                };
            }
            dataColumns.push(obj);
        }
        var lastColumn = {
            field: 'Actions',
            title: 'Actions',
            sortable: false,
            width: 110,
            overflow: 'visible',
            autoHide: false,
            template: function(row) {
                var key_id = row[table_id];
                var dropdownLink = false;
                var btnDropdownLink = "";
                var btnEdit = "";
                var btnDel = "";
                var btnPrint = "";
                if(btnPrintView){
                    var btnPrint = '<a class="dropdown-item" href="'+pathAction+'/print/'+key_id+'"><i class="la la-edit"></i>Print</a>';
                    dropdownLink = true;
                }
                if(btnEditView){
                    var btnEdit = '<a href="'+pathAction+'/form/'+key_id+'" class="btn btn-sm btn-soft btn-icon btn-icon-sm" title="Edit">\
                                        <i class="la la-edit"></i>\
                                    </a>';
                }
                if(btnDelView){
                    var btnDel = '<button type="button" data-url="'+pathAction+'/delete/'+key_id+'" id="del"  class="btn btn-sm btn-soft btn-icon btn-icon-sm mlr" title="Delete">\
                                        <i class="la la-trash"></i>\
                                    </button>';
                }
                if(dropdownLink){
                    var btnDropdownLink = '<div class="dropdown">'+
                        '<a href="javascript:;" class="btn btn-sm btn-soft btn-icon btn-icon-sm" data-toggle="dropdown">'+
                        '<i class="la la-bars"></i>'+
                        '</a>'+
                        '<div class="dropdown-menu dropdown-menu-right">'+
                        btnPrint +
                        '</div>'+
                        '</div>';
                }

                return  btnEdit + btnDel + btnDropdownLink;
            }
        };
        dataColumns.push(lastColumn);


        var datatable = $('.kt-datatable').KTDatatable({
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
                            if (Array.isArray(dataSet)) {
                                dataSet = dataSet.map(function(row) {
                                    return normalizeRow(row);
                                });
                            }
                            return dataSet;
                        },
                    },
                },
                pageSize: 500,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
                deferLoading: false, // here
            },
            // layout definition
            layout: {
                scroll: true,
                height: 400,
                footer: false,
            },

            // column sorting
            sortable: false,
            filterable: true,
            pagination: true,
            toolbar: {
                items: {
                    pagination: {
                        pageSizeSelect: [100, 200, 500, 1000, 2000],
                    },
                },
            },
            search: {
                // input: $('#generalSearch'),
            },

            rows: {
                callback: function() {},
                // auto hide columns, if rows overflow. work on non locked columns
                autoHide: false,
            },
            // columns definition
            columns: dataColumns,
        });

        $('body').on('submit', 'form[name="getRecordsByDateFilter"]', function(event) {
            event.preventDefault();
            var filterData = {};
            var date_type = $(document).find('form input[name="radioDate"]:checked').val();
            filterData.date = date_type;

            filterData.time_from = $(document).find('form input[name="time_from"]').val();
            filterData.time_to = $(document).find('form input[name="time_to"]').val();
            if(date_type == 'custom_date'){
                filterData.from = $(document).find('form input[name="from"]').val();
                filterData.to = $(document).find('form input[name="to"]').val();
            }
            var global_search = $('#generalSearch').val();
            if(!valueEmpty(global_search)){
                filterData.global_search = global_search;
            }

            // custom filter of some forms
            filterData.pds_status = $(document).find('input[name="pds_status"]:checked').val();


            // inline column filter
            filterData.inline = {};
            var tr = $('.listing_data_table>table>thead>tr');

            for (var key in dataFields) {
                var val = tr.find('input[name='+key+']').val();
                if(!valueEmpty(val)){
                    filterData.inline[key] = val;
                }
            }



            $('.kt-container').css({'pointer-events':'none','opacity':'0.5'});

            localStorage.removeItem('ajax_data-1-meta');

            datatable.search(filterData, 'globalFilters');
        });

        $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
            var val = $(this).val();
            $('.listing_data_table').find('thead>tr>th').each(function(){
                var th_val = $(this).attr('data-field');
                if(val == th_val){
                    $(this).toggle();
                }
            });
            $('.listing_data_table').find('tbody>tr>td').each(function(){
                var td_val = $(this).attr('data-field');
                if(val == td_val){
                    $(this).toggle();
                }
            });
        });
    };

    var eventsCapture = function() {
        $('.kt-datatable').on('kt-datatable--on-init', function() {
            console.log("f");
        }).on('kt-datatable--on-layout-updated', function() {
            console.log("f1");
            $(document).find('.kt_datepicker_inline').datepicker('disable');
            /* for 2nd tr th
            $('.inline_filter').remove();
            $('.kt-datatable thead').append('<tr class="kt-datatable__row inline_filter"></tr>')
            var newTr = $('.inline_filter');*/

            var date_fields = [];
            for (var key in dataFields) {
                if(['date','datetime'].includes(dataFields[key]['type']) || /(_date|^date)$/i.test(key)){
                    date_fields.push(key);
                }
            }
            $('.kt-datatable thead tr:first-child th').each(function() {
                var thix = $(this);
                var data_field = thix.attr('data-field');
                var name_title = thix.find('span').text();
                var width = thix.find('span').width();
                width = parseFloat(width);
                var widthPx = "width:"+width+"px";
                var className = 'class="'+data_field+'"';
                var val = "";
                if(!valueEmpty(inline_filter_data[data_field])){
                    val = inline_filter_data[data_field];
                }
                if(date_fields.includes(data_field)){
                    className = 'class="'+data_field+' kt_datepicker_inline"';
                }
                // for 2nd tr th // newTr.append('<th class="kt-datatable__cell"><span style="'+widthPx+'"><input type="text" name="'+data_field+'" '+className+' value="'+val+'" style="width: 100%;"/></span></th>');
                thix.html('<span style="'+widthPx+'">'+name_title+'<input type="text" name="'+data_field+'" '+className+' value="'+val+'" style="width: 100%;"/></span>');

            });
            // for 2nd tr th // $('.kt-datatable thead tr.inline_filter th:last-child').find('span').html("");
            $('.kt-datatable thead tr:last-child th:last-child').find('span input').remove();

            $(document).find('.kt_datepicker_inline').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });

            var net_amt = 0;
            $('table>tbody>tr').each(function(){
                var amt = $(this).find("td[data-field='purchase_order_total_net_amount']>span").text();
                net_amt += parseFloat(amt);
            });
            $('.grn_total_amount').html(net_amt.toLocaleString());

        }).on('kt-datatable--on-ajax-done', function() {
            console.log("f2");
            $('.kt-container').css({'pointer-events':'','opacity':''});
        });
    };


    var funcSmartDateTimeFormat = function(date){
        if (valueEmpty(date)) {
            return '';
        }
        var raw = String(date).trim();
        var ymd = raw.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?)?/);
        if (ymd) {
            var datePart = ymd[3] + '-' + ymd[2] + '-' + ymd[1];
            var hasTime = typeof ymd[4] !== 'undefined';
            var isMidnight = !hasTime || (ymd[4] === '00' && ymd[5] === '00' && ymd[6] === '00');
            if (isMidnight) {
                return datePart;
            }
            return datePart + ' ' + ymd[4] + ':' + ymd[5] + ':' + ymd[6];
        }
        var dmy = raw.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})(?:[ T](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?)?/);
        if (dmy) {
            var dmyDate = dmy[1] + '-' + dmy[2] + '-' + dmy[3];
            var dmyHasTime = typeof dmy[4] !== 'undefined';
            var dmyMidnight = !dmyHasTime || (dmy[4] === '00' && dmy[5] === '00' && dmy[6] === '00');
            if (dmyMidnight) {
                return dmyDate;
            }
            return dmyDate + ' ' + dmy[4] + ':' + dmy[5] + ':' + dmy[6];
        }
        var d = new Date(raw);
        if (isNaN(d.getTime())) {
            return raw;
        }
        var day = (d.getDate() < 10) ? '0' + d.getDate() : d.getDate();
        var month = ((d.getMonth() + 1) < 10) ? '0' + (d.getMonth() + 1) : (d.getMonth() + 1);
        var year = d.getFullYear();
        var dateOnly = day + '-' + month + '-' + year;
        if (d.getHours() === 0 && d.getMinutes() === 0 && d.getSeconds() === 0) {
            return dateOnly;
        }
        var hh = (d.getHours() < 10) ? '0' + d.getHours() : d.getHours();
        var mm = (d.getMinutes() < 10) ? '0' + d.getMinutes() : d.getMinutes();
        var ss = (d.getSeconds() < 10) ? '0' + d.getSeconds() : d.getSeconds();
        return dateOnly + ' ' + hh + ':' + mm + ':' + ss;
    };

    var funcDateFormat =  function(date){
        return funcSmartDateTimeFormat(date);
    };

    var funcDateTimeFormat =  function(date){
        return funcSmartDateTimeFormat(date);
    };

    return {
        // public functions
        init: function() {
            demo();
            eventsCapture();
            funcDateFormat();
            funcDateTimeFormat();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableRemoteAjaxDemo.init();
    $(document).on('keyup change','.kt-datatable thead tr th input',function(){
        $('.kt-datatable thead tr th').each(function() {
            var val = $(this).find('input').val();
            var key = $(this).find('input').attr('name');
            inline_filter_data[key] = val;
        });
    })
});
