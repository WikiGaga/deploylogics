"use strict";
var inline_filter_data = {};
// Class definition

var downloadClicked = false;

var KTDatatableRemoteAjaxDemo = function() {
    // Private functions

    // basic demo
    var demo = function() {
        localStorage.removeItem('ajax_data-1-meta');
        var table = $('.kt-datatable');
        var tableUrl = table.attr('data-url');
        var dataColumns = [];
        for (var key in dataFields) {
            if(dataFields[key]['type'] == 'string'){
                var obj = {
                    field: key,
                    title: dataFields[key]['title'],
                };
            }
            if(dataFields[key]['type'] == 'date'){
                var obj = {
                    field: key,
                    title: dataFields[key]['title'],
                    template: function(row) {
                        return funcDateFormat(row[key]);
                    },
                };
            }
            if(dataFields[key]['type'] == 'datetime'){
                var obj = {
                    field: key,
                    title: dataFields[key]['title'],
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
            template: function(row)
            {
                // console.log(row);
                var key_id = row[table_id];
                var voucher_status = row['voucher_status'];
                var dropdownLink = false;
                var btnDropdownLink = "";
                var btnEdit = "";
                var btnDel = "";
                var btnPrint = "";

                if(btnPrintView){
                    if(casetype != 'pos-sales-invoice' && casetype != 'pos-sales-return' && casetype != 'stock-audit-adjustment')
                    {
                        var btnPrint = '<a class="dropdown-item" href="'+pathAction+'/print/'+key_id+'" target="_blank"><i class="la la-print"></i>Print</a>';
                    }

                    if(casetype == 'stock-audit-adjustment')
                    {
                        btnPrint += '<button class="dropdown-item Adjustment" data-id="'+key_id+'" ><i class="la la-forward"></i>Adjustment</button>';
                        if(btnCloseAuditView){
                            btnPrint += '<button class="dropdown-item AuditClose" style="background-color:#D98880;color:#FFFF;" data-id="'+key_id+'" ><i class="la la-tag"></i>Close Audit</button>';
                        }
                        //btnPrint += '<button class="dropdown-item AuditSuspend" style="background-color:#5DADE2;color:#FFFF;" data-id="'+key_id+'" ><i class="la la-tag"></i>Suspend Audit</button>';
                        if(btnCompleteAuditView){
                            btnPrint += '<button class="dropdown-item AuditComplete" style="background-color:#58D68D;color:#FFFF;" data-id="'+key_id+'" ><i class="la la-tag"></i>Complete Audit</button>';
                        }
                        if(btnunpostAuditView){
                            btnPrint += '<button class="dropdown-item UnPost" style="background-color:#34495E;color:#FFFF;" data-id="'+key_id+'" ><i class="la la-tag"></i>Un-Post Audit</button>';
                        }
                        btnPrint += '<a class="dropdown-item" href="'+pathAction+'/print/'+key_id+'" target="_blank"><i class="la la-print"></i>Print</a>';
                    }
                    if(casetype === 'grn'){
                        btnPrint += '<button class="dropdown-item generateTags" data-id="'+key_id+'" ><i class="la la-barcode"></i>Barcode Print</button>';
                        btnPrint += '<button class="dropdown-item generatePrice" data-id="'+key_id+'" ><i class="la la-tag"></i>Update Product Price</button>';
                    }
                    if(casetype === 'pos-sales-invoice' || casetype === 'pos-sales-return'){
                        btnPrint += '<a class="dropdown-item" href="'+pathAction+'/print/html/'+key_id+'" target="_blank"><i class="la la-print"></i>Html Print</a>';
                        btnPrint += '<a class="dropdown-item" href="'+pathAction+'/print/thermal/'+key_id+'" target="_blank"><i class="la la-print"></i>Thermal Print</a>';
                    }
                    dropdownLink = true;
                }
                if(btnpostView){
                    if(casetype == 'pve' ||
                        casetype == 'pv' ||
                        casetype == 'cpv' ||
                        casetype == 'crv'||
                        casetype == 'lv'||
                        casetype == 'jv'||
                        casetype == 'rv'||
                        casetype == 'brpv'||
                        casetype == 'brrv'
                    ){
                        if(voucher_status == 'Un-Posted')
                        {
                            btnPrint += '<button class="dropdown-item Posted" style="background-color:#2471A3;color:#FFFF;" data-id="'+key_id+'">Posted</button>';
                        }
                        btnPrint += '<button class="dropdown-item UnPosted" style="background-color:#7D3C98;color:#FFFF;" data-id="'+key_id+'">Un-Posted</button>';
                    }
                    dropdownLink = true;
                }
                if(btnEditView){
                    if(casetype == 'pve' ||
                        casetype == 'pv' ||
                        casetype == 'cpv' ||
                        // casetype == 'crv'||
                        casetype == 'lv'||
                        casetype == 'jv'||
                        casetype == 'rv'||
                        casetype == 'brpv'||
                        casetype == 'brrv'
                    ){
                        if(voucher_status == "Un-Posted")
                        {
                            var btnEdit = '<a href="'+pathAction+'/form/'+key_id+'" class="btn btn-sm btn-icon btn-icon-sm btn-warning" title="Edit">\
                                <i class="la la-edit"></i>\
                            </a>';
                        }
                    }else{
                        var btnEdit = '<a href="'+pathAction+'/form/'+key_id+'" class="btn btn-sm btn-icon btn-icon-sm btn-warning" title="Edit">\
                            <i class="la la-edit"></i>\
                        </a>';
                    }
                }
                if(btnDelView){
                    if(casetype == 'pve' ||
                        casetype == 'pv' ||
                        casetype == 'cpv' ||
                        casetype == 'crv'||
                        casetype == 'lv'||
                        casetype == 'jv'||
                        casetype == 'rv'||
                        casetype == 'brpv'||
                        casetype == 'brrv'
                    ){
                        if(voucher_status == "Un-Posted")
                        {
                            var btnDel = '<button type="button" data-url="'+pathAction+'/delete/'+key_id+'" id="del"  class="btn btn-sm btn-icon btn-icon-sm btn-danger mlr" title="Delete">\
                                <i class="la la-trash"></i>\
                            </button>';
                        }
                    }else{
                        var btnDel = '<button type="button" data-url="'+pathAction+'/delete/'+key_id+'" id="del"  class="btn btn-sm btn-icon btn-icon-sm btn-danger mlr" title="Delete">\
                            <i class="la la-trash"></i>\
                        </button>';
                    }
                }
                if(dropdownLink){
                    var btnDropdownLink = '<div class="dropdown">'+
                        '<a href="javascript:;" class="btn btn-sm btn-icon btn-icon-sm btn-success" data-toggle="dropdown">'+
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
                            if (raw.downloadMessage) {
                                toastr.success(raw.downloadMessage);
                            }

                            // sample data mapping
                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
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
            var date_type = $(document).find('form select[name="radioDate"]').val();
            filterData.date = date_type;

            filterData.download = '';
            if(downloadClicked != false) {
                filterData.download = downloadClicked;
            }

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
            filterData.post_status = $(document).find('input[name="post_status"]:checked').val();
            filterData.voucher_from = $(document).find('input[name="voucher_from"]').val();
            filterData.voucher_to = $(document).find('input[name="voucher_to"]').val();

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
            downloadClicked = false;
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
           // console.log("f");
        }).on('kt-datatable--on-layout-updated', function() {
            //console.log("f1");
            $(document).find('.kt_datepicker_inline').datepicker('disable');
            /* for 2nd tr th
            $('.inline_filter').remove();
            $('.kt-datatable thead').append('<tr class="kt-datatable__row inline_filter"></tr>')
            var newTr = $('.inline_filter');*/

            var date_fields = [];
            for (var key in dataFields) {
                if(['date','datetime'].includes(dataFields[key]['type'])){
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
                thix.html('<span style="'+widthPx+'">'+name_title+'<input type="text" name="'+data_field+'" '+className+' value="'+val+'" placeholder="Search.." style="width: 100%;"/></span>');

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
                if(casetype == 'purchase-order'){
                    var amt = $(this).find("td[data-field='purchase_order_total_net_amount']>span").text();
                }
                if(casetype == 'grn' || casetype == 'purchase-return'){
                    var amt = $(this).find("td[data-field='grn_total_net_amount']>span").text();
                }
                if(casetype == 'pv'){
                    var amt = $(this).find("td[data-field='amount']>span").text();
                }
                net_amt += parseFloat(amt);
            });
            $('.grn_total_amount').html(net_amt.toLocaleString());

        }).on('kt-datatable--on-ajax-done', function() {
            //console.log("f2");
            $('.kt-container').css({'pointer-events':'','opacity':''});
        });
    };


    var funcDateFormat =  function(date){
        var dd = new Date(date).toLocaleString();
        var d = new Date(dd);
        var returnDate = "";
        if(d){
            var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
            var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
            var year = d.getFullYear();
            if(!valueEmpty(day) && !valueEmpty(month) && !valueEmpty(year)){
                returnDate =  day +'-'+ month +'-'+ year;
            }
        }
        return returnDate;
    };

    var funcDateTimeFormat =  function(date){
        // console.log(date);
        var dd = new Date(date).toLocaleString();
        var d = new Date(dd);
        var returnDate = "";
        if(d){
            //console.log(d);
            var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
            var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
            var year = d.getFullYear();
            var time = d.toLocaleTimeString();
            if(!valueEmpty(day) && !valueEmpty(month) && !valueEmpty(year) && !valueEmpty(time)){
                returnDate =  day +'-'+ month +'-'+ year +' '+ time;
            }
        }
        return returnDate;
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


$('body').on('click', '#export_csv, #export_pdf', function () {
    let exportType = $(this).attr('id') === 'export_csv' ? 'csv' : 'pdf';
    downloadClicked = exportType;
    let $form = $('form[name="getRecordsByDateFilter"]');
    // $form.find('input[name="export_type"]').remove(); // Ensure no duplicate fields
    // $form.append('<input type="hidden" name="export_type" value="' + exportType + '">');
    $form.trigger('submit');
});

$(document).on('keypress', 'input', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        $('#getRecordsByDateFilter').click(); // Trigger the form submit
    }
});
