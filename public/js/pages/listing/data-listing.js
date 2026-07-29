"use strict";
var inline_filter_data = {};
// Class definition

var downloadClicked = false;


var KTDatatableRemoteAjaxDemo = function () {
    function accountingVoucherCasetype(ct) {
        return ['pve', 'pv', 'cpv', 'crv', 'lv', 'lfv', 'jv', 'rv', 'brpv', 'brrv', 'brv', 'bpv', 'obv', 'ipv', 'irv'].indexOf(ct) !== -1;
    }

    function listingSupportsUmPostActions() {
        return casetype === 'purchase-order'
            || casetype === 'grn'
            || casetype === 'shift_sessions'
            || casetype === 'stock-receiving'
            || accountingVoucherCasetype(casetype);
    }

    function rowField(row, key) {
        if (row[key] !== undefined && row[key] !== null && row[key] !== '') {
            return row[key];
        }
        var upper = key.toUpperCase();
        if (row[upper] !== undefined && row[upper] !== null && row[upper] !== '') {
            return row[upper];
        }
        return null;
    }

    function rowPostedState(row, voucher_status) {

        // console.log('rowPostedState', row, voucher_status);
        var vs = String(voucher_status || rowField(row, 'voucher_status') || '').toLowerCase().trim();
        if (vs === 'posted') {
            return 1;
        }
        if (vs === 'canceled' || vs === 'cancelled') {
            return 2;
        }
        if (vs === 'un-posted' || vs === 'unposted' || vs === 'draft') {
            return 0;
        }

        var postedVal = rowField(row, 'posted');
        if (postedVal !== null) {
            var posted = parseInt(postedVal, 10);
            if (!isNaN(posted) && (posted === 1 || posted === 2)) {
                return posted;
            }
            if (!isNaN(posted) && posted === 0) {
                return 0;
            }
        }

        var legacyPosted = rowField(row, 'voucher_posted');
        if (legacyPosted !== null) {
            var vp = parseInt(legacyPosted, 10);
            if (!isNaN(vp) && vp === 1) {
                return 1;
            }
        }

        if (postedVal !== null) {
            var n = parseInt(postedVal, 10);
            return isNaN(n) ? 0 : n;
        }

        return 0;
    }

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
    function isRowStagingEnrolled(row) {
        var sa = rowField(row, 'staging_apply');
        return sa == 1 || sa === '1';
    }

    function useUmListingActions(row) {
        if (!has_staging || has_staging === '0' || has_staging === '') {
            return true;
        }
        return !isRowStagingEnrolled(row);
    }

    function canListingDelete(row) {
        var posted = rowPostedState(row, rowField(row, 'voucher_status'));
        if (listingSupportsUmPostActions() && useUmListingActions(row)) {
            return posted === 0;
        }
        if (!has_staging || has_staging === '0' || has_staging === '') {
            return true;
        }
        if (isRowStagingEnrolled(row)) {
            return posted === 0;
        }
        return true;
    }

    function appendUmPostActionButtons(row, key_id) {
        var html = '';
        if (!listingSupportsUmPostActions() || !useUmListingActions(row)) {
            return html;
        }
        var posted = rowPostedState(row, rowField(row, 'voucher_status'));
        if (posted === 0 && btnpostView) {
            if (casetype === 'purchase-order') {
                html += '<button class="dropdown-item POPosted" style="background-color:#2471A3;color:#FFFF;" data-id="' + key_id + '">Post</button>';
            } else if (casetype === 'grn') {
                html += '<button class="dropdown-item GRNPosted" style="background-color:#2471A3;color:#FFFF;" data-id="' + key_id + '">Post</button>';
            } else if (casetype === 'shift_sessions') {
                html += '<button class="dropdown-item SSPosted" style="background-color:#2471A3;color:#FFFF;" data-id="' + key_id + '">Post</button>';
            } else if (casetype === 'stock-receiving') {
                html += '<button class="dropdown-item SRPosted" style="background-color:#2471A3;color:#FFFF;" data-id="' + key_id + '">Post</button>';
            } else if (accountingVoucherCasetype(casetype)) {
                html += '<button class="dropdown-item CPVPosted" style="background-color:#2471A3;color:#FFFF;" data-case="' + casetype + '" data-id="' + key_id + '">Post</button>';
            }
        }
        if ((posted === 1 || posted === 2) && btnUnpostView) {
            if (casetype === 'purchase-order') {
                html += '<button class="dropdown-item POUnPosted" style="background-color:#7D3C98;color:#FFFF;" data-id="' + key_id + '">Un-Post</button>';
            } else if (casetype === 'grn') {
                html += '<button class="dropdown-item GRNUnPosted" style="background-color:#7D3C98;color:#FFFF;" data-id="' + key_id + '">Un-Post</button>';
            } else if (casetype === 'shift_sessions') {
                html += '<button class="dropdown-item SSUnPosted" style="background-color:#7D3C98;color:#FFFF;" data-id="' + key_id + '">Un-Post</button>';
            } else if (casetype === 'stock-receiving') {
                html += '<button class="dropdown-item SRUnPosted" style="background-color:#7D3C98;color:#FFFF;" data-id="' + key_id + '">Un-Post</button>';
            } else if (accountingVoucherCasetype(casetype)) {
                html += '<button class="dropdown-item CPVUnPosted" style="background-color:#7D3C98;color:#FFFF;" data-case="' + casetype + '" data-id="' + key_id + '">Un-Post</button>';
            }
        }
        if (posted === 0 && btnCancelView) {
            if (casetype === 'purchase-order') {
                html += '<button class="dropdown-item POCancel" style="background-color:#C0392B;color:#FFFF;" data-id="' + key_id + '">Cancel</button>';
            } else if (casetype === 'grn') {
                html += '<button class="dropdown-item GRNCancel" style="background-color:#C0392B;color:#FFFF;" data-id="' + key_id + '">Cancel</button>';
            } else if (casetype === 'shift_sessions') {
                html += '<button class="dropdown-item SSCancel" style="background-color:#C0392B;color:#FFFF;" data-id="' + key_id + '">Cancel</button>';
            } else if (casetype === 'stock-receiving') {
                html += '<button class="dropdown-item SRCancel" style="background-color:#C0392B;color:#FFFF;" data-id="' + key_id + '">Cancel</button>';
            } else if (accountingVoucherCasetype(casetype)) {
                html += '<button class="dropdown-item CPVCancel" style="background-color:#C0392B;color:#FFFF;" data-case="' + casetype + '" data-id="' + key_id + '">Cancel</button>';
            }
        }
        return html;
    }

    var demo = function () {
        localStorage.removeItem('ajax_data-1-meta');
        var table = $('.kt-datatable');
        var tableUrl = table.attr('data-url');
        var dataColumns = [];
        for (var key in dataFields) {
            var fieldType = dataFields[key]['type'];
            var treatAsTemporal = fieldType == 'date' || fieldType == 'datetime'
                || /(_date|^date|created_at|updated_at)$/i.test(key);
            if (fieldType == 'string' && !treatAsTemporal) {
                var obj = {
                    field: key,
                    title: dataFields[key]['title'],
                };
            }
            if (treatAsTemporal) {
                var obj = {
                    field: key,
                    title: dataFields[key]['title'],
                    template: (function (fieldKey) {
                        return function (row) {
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
            template: function (row) {
                row = normalizeRow(row);
                console.log('row', row);
                var key_id = row[table_id];
                var voucher_status = row['voucher_status'];
                var posted = rowPostedState(row, voucher_status);
                var dropdownLink = false;
                var btnDropdownLink = "";
                var btnEdit = "";
                var btnDel = "";
                var btnPrint = "";



                if (btnPrintView) {
                    if (casetype != 'pos-sales-invoice' && casetype != 'pos-sales-return' && casetype != 'stock-audit-adjustment') {
                        var btnPrint = '<a class="dropdown-item" href="' + pathAction + '/print/' + key_id + '" target="_blank"><i class="la la-print"></i>Print</a>';
                    }

                    if (casetype == 'stock-audit-adjustment') {
                        btnPrint += '<button class="dropdown-item Adjustment" data-id="' + key_id + '" ><i class="la la-forward"></i>Adjustment</button>';
                        if (btnCloseAuditView) {
                            btnPrint += '<button class="dropdown-item AuditClose" style="background-color:#D98880;color:#FFFF;" data-id="' + key_id + '" ><i class="la la-tag"></i>Close Audit</button>';
                        }
                        //btnPrint += '<button class="dropdown-item AuditSuspend" style="background-color:#5DADE2;color:#FFFF;" data-id="'+key_id+'" ><i class="la la-tag"></i>Suspend Audit</button>';
                        if (btnCompleteAuditView) {
                            btnPrint += '<button class="dropdown-item AuditComplete" style="background-color:#58D68D;color:#FFFF;" data-id="' + key_id + '" ><i class="la la-tag"></i>Complete Audit</button>';
                        }
                        if (btnunpostAuditView) {
                            btnPrint += '<button class="dropdown-item UnPost" style="background-color:#34495E;color:#FFFF;" data-id="' + key_id + '" ><i class="la la-tag"></i>Un-Post Audit</button>';
                        }
                        btnPrint += '<a class="dropdown-item" href="' + pathAction + '/print/' + key_id + '" target="_blank"><i class="la la-print"></i>Print</a>';
                    }
                    if (casetype === 'grn') {
                        btnPrint += '<button class="dropdown-item generateTags" data-id="' + key_id + '" ><i class="la la-barcode"></i>Barcode Print</button>';
                        btnPrint += '<button class="dropdown-item generatePrice" data-id="' + key_id + '" ><i class="la la-tag"></i>Update Product Price</button>';
                    }
                    if (casetype === 'pos-sales-invoice' || casetype === 'pos-sales-return') {
                        btnPrint += '<a class="dropdown-item" href="' + pathAction + '/print/html/' + key_id + '" target="_blank"><i class="la la-print"></i>Html Print</a>';
                        btnPrint += '<a class="dropdown-item" href="' + pathAction + '/print/thermal/' + key_id + '" target="_blank"><i class="la la-print"></i>Thermal Print</a>';
                    }
                    dropdownLink = true;
                }
                if (btnpostView || btnUnpostView || btnCancelView) {
                    btnPrint += appendUmPostActionButtons(row, key_id);
                    if (btnPrint !== '') {
                        dropdownLink = true;
                    }
                }
                if (btnEditView) {
                    var btnEdit = '<a href="' + pathAction + '/form/' + key_id + '" class="btn btn-sm btn-icon btn-icon-sm btn-warning" title="Edit">\
                        <i class="la la-edit"></i>\
                    </a>';
                }
                if (btnDelView && canListingDelete(row)) {
                    if (casetype == 'purchase-order' || casetype == 'grn' || casetype == 'shift_sessions' || casetype == 'stock-receiving' || accountingVoucherCasetype(casetype)) {
                        var btnDel = '<button type="button" data-url="' + pathAction + '/delete/' + key_id + '" id="del"  class="btn btn-sm btn-icon btn-icon-sm btn-danger mlr" title="Delete">\
                            <i class="la la-trash"></i>\
                        </button>';
                    } else {
                        var btnDel = '<button type="button" data-url="' + pathAction + '/delete/' + key_id + '" id="del"  class="btn btn-sm btn-icon btn-icon-sm btn-danger mlr" title="Delete">\
                            <i class="la la-trash"></i>\
                        </button>';
                    }
                }
                if (dropdownLink) {
                    var btnDropdownLink = '<div class="dropdown ml-1">' +
                        '<a href="javascript:;" class="btn btn-sm btn-icon btn-icon-sm btn-success" data-toggle="dropdown">' +
                        '<i class="la la-bars"></i>' +
                        '</a>' +
                        '<div class="dropdown-menu dropdown-menu-right">' +
                        btnPrint +
                        '</div>' +
                        '</div>';
                }

                return btnEdit + btnDel + btnDropdownLink;
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
                        map: function (raw) {
                            console.log('rawwwwwwwwwww', raw);
                            if (raw.downloadMessage) {
                                toastr.success(raw.downloadMessage);
                                window.location.href = '/export-csv';
                            }

                            // sample data mapping
                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            if (Array.isArray(dataSet)) {
                                dataSet = dataSet.map(function (row) {
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
                height: 550,
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
                callback: function () { },
                // auto hide columns, if rows overflow. work on non locked columns
                autoHide: false,
            },
            // columns definition
            columns: dataColumns,
        });

        $('body').on('submit', 'form[name="getRecordsByDateFilter"]', function (event) {
            // alert('fsd');
            event.preventDefault();
            var filterData = {};
            var date_type = $(document).find('form select[name="radioDate"]').val();
            filterData.date = date_type;

            filterData.download = '';
            if (downloadClicked != false) {
                filterData.download = downloadClicked;
            }

            filterData.time_from = $(document).find('form input[name="time_from"]').val();
            filterData.time_to = $(document).find('form input[name="time_to"]').val();
            if (date_type == 'custom_date') {
                filterData.from = $(document).find('form input[name="from"]').val();
                filterData.to = $(document).find('form input[name="to"]').val();
            }
            var global_search = $('#generalSearch').val();
            if (!valueEmpty(global_search)) {
                filterData.global_search = global_search;
            }

            // custom filter of some forms
            filterData.pds_status = $(document).find('input[name="pds_status"]:checked').val();
            filterData.post_status = $(document).find('input[name="post_status"]:checked').val();
            filterData.voucher_from = $(document).find('input[name="voucher_from"]').val();
            filterData.voucher_to = $(document).find('input[name="voucher_to"]').val();

            console.log('b', filterData);
            // inline column filter
            filterData.inline = {};
            var tr = $('.listing_data_table>table>thead>tr');
            for (var key in dataFields) {
                console.log(key);
                var val = tr.find('input[name=' + key + ']').val();
                if (!valueEmpty(val)) {
                    filterData.inline[key] = val;
                }
            }

            var filterStateToSave = Object.assign({}, filterData);
            delete filterStateToSave.download;
            delete filterStateToSave.global_search;
            localStorage.setItem('listing_filter_state', JSON.stringify(filterStateToSave));

            $('.kt-container').css({ 'pointer-events': 'none', 'opacity': '0.5' });

            localStorage.removeItem('ajax_data-1-meta');

            console.log('a', filterData);

            datatable.search(filterData, 'globalFilters');
            downloadClicked = false;
        });

        $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function (e) {
            var val = $(this).val();
            $('.listing_data_table').find('thead>tr>th').each(function () {
                var th_val = $(this).attr('data-field');
                if (val == th_val) {
                    $(this).toggle();
                }
            });
            $('.listing_data_table').find('tbody>tr>td').each(function () {
                var td_val = $(this).attr('data-field');
                if (val == td_val) {
                    $(this).toggle();
                }
            });
        });
    };

    var eventsCapture = function () {
        $('.kt-datatable').on('kt-datatable--on-init', function () {
            // console.log("f");
        }).on('kt-datatable--on-layout-updated', function () {
            //console.log("f1");
            $(document).find('.kt_datepicker_inline').datepicker('disable');
            /* for 2nd tr th
            $('.inline_filter').remove();
            $('.kt-datatable thead').append('<tr class="kt-datatable__row inline_filter"></tr>')
            var newTr = $('.inline_filter');*/

            var date_fields = [];
            for (var key in dataFields) {
                if (['date', 'datetime'].includes(dataFields[key]['type']) || /(_date|^date)$/i.test(key)) {
                    date_fields.push(key);
                }
            }
            $('.kt-datatable thead tr:first-child th').each(function () {
                var thix = $(this);
                var data_field = thix.attr('data-field');
                var name_title = thix.find('span').text();
                var width = thix.find('span').width();
                width = parseFloat(width);
                var widthPx = "width:" + width + "px";
                var className = 'class="' + data_field + '"';
                var val = "";
                if (!valueEmpty(inline_filter_data[data_field])) {
                    val = inline_filter_data[data_field];
                }
                if (date_fields.includes(data_field)) {
                    className = 'class="' + data_field + ' kt_datepicker_inline"';
                }
                // for 2nd tr th // newTr.append('<th class="kt-datatable__cell"><span style="'+widthPx+'"><input type="text" name="'+data_field+'" '+className+' value="'+val+'" style="width: 100%;"/></span></th>');
                thix.html('<span style="' + widthPx + '">' + name_title + '<input type="text" name="' + data_field + '" ' + className + ' value="' + val + '" placeholder="Search.." style="width: 100%;"/></span>');

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
            $('table>tbody>tr').each(function () {
                if (casetype == 'purchase-order') {
                    var amt = $(this).find("td[data-field='purchase_order_total_net_amount']>span").text();
                }
                if (casetype == 'grn' || casetype == 'purchase-return') {
                    var amt = $(this).find("td[data-field='grn_total_net_amount']>span").text();
                }
                if (casetype == 'pv') {
                    var amt = $(this).find("td[data-field='amount']>span").text();
                }
                net_amt += parseFloat(amt);
            });
            $('.grn_total_amount').html(net_amt.toLocaleString());

        }).on('kt-datatable--on-ajax-done', function () {
            //console.log("f2");
            $('.kt-container').css({ 'pointer-events': '', 'opacity': '' });
        });
    };


    var funcSmartDateTimeFormat = function (date) {
        if (valueEmpty(date)) {
            return '';
        }
        var raw = String(date).trim();
        var ymd = raw.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{1,2}):(\d{2}):(\d{2})(?:\.\d+)?)?/);
        if (ymd) {
            var datePart = ymd[3] + '-' + ymd[2] + '-' + ymd[1];
            var matched = ymd[0];
            var rest = raw.substring(matched.length).trim().replace(/^(Z|[+-]\d{2}:?\d{2})$/i, '');
            var hasTime = typeof ymd[4] !== 'undefined';
            if (!hasTime && rest !== '') {
                var fallback = new Date(raw);
                if (!isNaN(fallback.getTime())) {
                    return formatDateParts(fallback, true);
                }
                return raw;
            }
            if (!hasTime) {
                return datePart;
            }
            var hh = ('0' + parseInt(ymd[4], 10)).slice(-2);
            var mm = ymd[5];
            var ss = ymd[6];
            if (hh === '00' && mm === '00' && ss === '00') {
                return datePart;
            }
            return datePart + ' ' + hh + ':' + mm + ':' + ss;
        }
        var dmy = raw.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})(?:[ T](\d{1,2}):(\d{2}):(\d{2})(?:\.\d+)?)?/);
        if (dmy) {
            var dmyDate = dmy[1] + '-' + dmy[2] + '-' + dmy[3];
            var dmyMatched = dmy[0];
            var dmyRest = raw.substring(dmyMatched.length).trim().replace(/^(Z|[+-]\d{2}:?\d{2})$/i, '');
            var dmyHasTime = typeof dmy[4] !== 'undefined';
            if (!dmyHasTime && dmyRest !== '') {
                var dmyFallback = new Date(raw);
                if (!isNaN(dmyFallback.getTime())) {
                    return formatDateParts(dmyFallback, true);
                }
                return raw;
            }
            if (!dmyHasTime) {
                return dmyDate;
            }
            var dmyHh = ('0' + parseInt(dmy[4], 10)).slice(-2);
            var dmyMm = dmy[5];
            var dmySs = dmy[6];
            if (dmyHh === '00' && dmyMm === '00' && dmySs === '00') {
                return dmyDate;
            }
            return dmyDate + ' ' + dmyHh + ':' + dmyMm + ':' + dmySs;
        }
        var d = new Date(raw);
        if (isNaN(d.getTime())) {
            return raw;
        }
        return formatDateParts(d, true);
    };

    var formatDateParts = function (d, allowTime) {
        var day = (d.getDate() < 10) ? '0' + d.getDate() : d.getDate();
        var month = ((d.getMonth() + 1) < 10) ? '0' + (d.getMonth() + 1) : (d.getMonth() + 1);
        var year = d.getFullYear();
        var dateOnly = day + '-' + month + '-' + year;
        if (!allowTime || (d.getHours() === 0 && d.getMinutes() === 0 && d.getSeconds() === 0)) {
            return dateOnly;
        }
        var hh = (d.getHours() < 10) ? '0' + d.getHours() : d.getHours();
        var mm = (d.getMinutes() < 10) ? '0' + d.getMinutes() : d.getMinutes();
        var ss = (d.getSeconds() < 10) ? '0' + d.getSeconds() : d.getSeconds();
        return dateOnly + ' ' + hh + ':' + mm + ':' + ss;
    };

    var funcDateFormat = function (date) {
        return funcSmartDateTimeFormat(date);
    };

    var funcDateTimeFormat = function (date) {
        return funcSmartDateTimeFormat(date);
    };

    return {
        // public functions
        init: function () {
            demo();
            eventsCapture();
            funcDateFormat();
            funcDateTimeFormat();
        },
    };
}();

jQuery(document).ready(function () {
    var savedFilterState = localStorage.getItem('listing_filter_state');
    if (savedFilterState) {
        var filterState = JSON.parse(savedFilterState);

        if (filterState.date) {
            $('form[name="getRecordsByDateFilter"] select[name="radioDate"]').val(filterState.date);
        }

        if (filterState.from) {
            $('form[name="getRecordsByDateFilter"] input[name="from"]').val(filterState.from);
        }
        if (filterState.to) {
            $('form[name="getRecordsByDateFilter"] input[name="to"]').val(filterState.to);
        }
        if (filterState.time_from) {
            $('form[name="getRecordsByDateFilter"] input[name="time_from"]').val(filterState.time_from);
        }
        if (filterState.time_to) {
            $('form[name="getRecordsByDateFilter"] input[name="time_to"]').val(filterState.time_to);
        }

        if (filterState.pds_status) {
            $('input[name="pds_status"][value="' + filterState.pds_status + '"]').prop('checked', true);
        }
        if (filterState.post_status) {
            $('input[name="post_status"][value="' + filterState.post_status + '"]').prop('checked', true);
        }
        if (filterState.voucher_from) {
            $('input[name="voucher_from"]').val(filterState.voucher_from);
        }
        if (filterState.voucher_to) {
            $('input[name="voucher_to"]').val(filterState.voucher_to);
        }

        if (filterState.inline) {
            inline_filter_data = filterState.inline;
        }

        setTimeout(function () {
            $('form[name="getRecordsByDateFilter"]').trigger('submit');
        }, 300);
    }

    KTDatatableRemoteAjaxDemo.init();
    $(document).on('keyup change', '.kt-datatable thead tr th input', function () {
        $('.kt-datatable thead tr th').each(function () {
            var val = $(this).find('input').val();
            var key = $(this).find('input').attr('name');
            inline_filter_data[key] = val;
        });
    })
});


$('body').on('click', '#export_csv, #export_pdf', function () {
    let exportType = $(this).attr('id') === 'export_csv' ? 'csv' : 'pdf';
    downloadClicked = exportType;
    let $form = $('form[name="getRecordsByDateFilter"]');
    // $form.find('input[name="export_type"]').remove(); // Ensure no duplicate fields
    // $form.append('<input type="hidden" name="export_type" value="' + exportType + '">');
    $form.trigger('submit');
});

$(document).on('click', '.POPosted', function () {
    erpDocumentAjax({
        url: '/purchase-order/post',
        data: { purchase_order_id: $(this).attr('data-id') },
        successMsg: 'Successfully Posted.'
    });
});

$(document).on('click', '.POUnPosted', function () {
    erpDocumentAjax({
        url: '/purchase-order/unposted',
        data: { data: [$(this).attr('data-id')] },
        successMsg: 'Successfully Un-Posted.'
    });
});

$(document).on('click', '.GRNPosted', function () {
    erpDocumentAjax({
        url: '/grn/post',
        data: { grn_id: $(this).attr('data-id') },
        successMsg: 'Successfully Posted.'
    });
});

$(document).on('click', '.GRNUnPosted', function () {
    erpDocumentAjax({
        url: '/grn/unposted',
        data: { data: [$(this).attr('data-id')] },
        successMsg: 'Successfully Un-Posted.'
    });
});

$(document).on('click', '.SSPosted', function () {
    erpDocumentAjax({
        url: '/shift_sessions/post',
        data: { session_id: $(this).attr('data-id') },
        successMsg: 'Successfully Posted.'
    });
});

$(document).on('click', '.SSUnPosted', function () {
    erpDocumentAjax({
        url: '/shift_sessions/unposted',
        data: { data: [$(this).attr('data-id')] },
        successMsg: 'Successfully Un-Posted.'
    });
});

$(document).on('click', '.SRPosted', function () {
    erpDocumentAjax({
        url: '/stock/stock-receiving/post',
        data: { stock_id: $(this).attr('data-id') },
        successMsg: 'Successfully Posted.'
    });
});

$(document).on('click', '.SRUnPosted', function () {
    erpDocumentAjax({
        url: '/stock/stock-receiving/unposted',
        data: { data: [$(this).attr('data-id')] },
        successMsg: 'Successfully Un-Posted.'
    });
});

$(document).on('click', '.CPVPosted', function () {
    var case_name = $(this).attr('data-case') || 'cpv';
    erpDocumentAjax({
        url: '/accounts/' + case_name + '/post',
        data: { voucher_id: $(this).attr('data-id') },
        successMsg: 'Successfully Posted.'
    });
});

$(document).on('click', '.CPVUnPosted', function () {
    var case_name = $(this).attr('data-case');
    erpDocumentAjax({
        url: '/accounts/' + case_name + '/unposted',
        data: { data: [$(this).attr('data-id')] },
        successMsg: 'Successfully Un-Posted.'
    });
});

function listingUmCancel(url, payload) {
    erpDocumentAjax({
        url: url,
        data: payload,
        successMsg: 'Canceled.'
    });
}

$(document).on('click', '.POCancel', function () {
    if (!confirm('Cancel this document?')) { return; }
    listingUmCancel('/purchase-order/cancel', { purchase_order_id: $(this).attr('data-id') });
});
$(document).on('click', '.GRNCancel', function () {
    if (!confirm('Cancel this document?')) { return; }
    listingUmCancel('/grn/cancel', { grn_id: $(this).attr('data-id') });
});
$(document).on('click', '.SSCancel', function () {
    if (!confirm('Cancel this document?')) { return; }
    listingUmCancel('/shift_sessions/cancel', { session_id: $(this).attr('data-id') });
});
$(document).on('click', '.SRCancel', function () {
    if (!confirm('Cancel this document?')) { return; }
    listingUmCancel('/stock/stock-receiving/cancel', { stock_id: $(this).attr('data-id') });
});
$(document).on('click', '.CPVCancel', function () {
    if (!confirm('Cancel this document?')) { return; }
    var case_name = $(this).attr('data-case') || 'cpv';
    listingUmCancel('/accounts/' + case_name + '/cancel', { voucher_id: $(this).attr('data-id') });
});

$(document).on('keypress', 'input', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        $('#getRecordsByDateFilter').click(); // Trigger the form submit
    }
});
