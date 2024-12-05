"use strict";
// Class definition

var KTDatatableRemoteAjaxDemo = function() {
    // Private functions

    // basic demo
    var demo = function() {
        //window.localStorage.clear();
        localStorage.removeItem('ajax_data-1-meta');
        var statuses = [];
        var keyid = "";
        var table = $('.kt-datatable');
        var tableUrl = table.attr('data-url');
        var statusClasses = [' m-badge--metal', ' m-badge--success'];

        var colmnsData = [];
        for (var key in dataFields) {
            if (dataFields.hasOwnProperty(key)) {
                var colmnData = {
                    field: key,
                    title: dataFields[key],
                }
                colmnsData.push(colmnData)
            }
        }
        var action = {
            field: 'actions',
            title: 'Actions',
            sortable: false,
            width: 110,
            position:'relative',
            overflow: 'visible',
            autoHide: false,
            template: function(row, index, datatable) {
                var key_id = "";
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        if(keyid == key){
                            key_id = row[key];
                        }
                    }
                }
                if (typeof btnEditView !== 'undefined'){
                    if(btnEditView){
                        var btnEdit  = '<a href="/'+path_url.path_form+'/'+key_id+'" class="btn btn-sm btn-soft btn-icon btn-icon-sm" title="Edit">\
							<i class="la la-edit"></i>\
						</a>';
                    }
                }else{
                    var btnEdit = '';
                }
                if (typeof btnChangePassView !== 'undefined'){
                    if(btnChangePassView){
                        var btnChangePass  = '<a href="/change-password/form/'+key_id+'" class="btn btn-sm btn-soft btn-icon btn-icon-sm" title="Edit">\
							<i class="la la-key"></i>\
						</a>';
                    }
                }else{
                    var btnChangePass = '';
                }
                if (typeof btnDelView !== 'undefined'){
                    if(btnDelView){
                        var btnDel = '<button data-url="/'+path_url.path+'/delete/'+key_id+'" id="del" class="btn btn-soft btn-sm btn-icon btn-icon-sm" title="Delete">\
							<i class="la la-trash"></i>\
						</button>';
                    }
                }else{
                    var btnDel = '';
                }
                if (typeof btnPrintView !== 'undefined'){
                     if(btnPrintView){
                        if(casetype === 'sales-invoice' || casetype === 'pos-sales-invoice'){
                            var btnPrint = '<div class="dropdown">';
                            btnPrint += '<a href="javascript:;" class="btn btn-sm btn-soft btn-icon btn-icon-md" data-toggle="dropdown">';
                            btnPrint += '<i class="la la-bars"></i></a>';
                            btnPrint += '<div class="dropdown-menu dropdown-menu-right">';
                            btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/print/html/'+key_id+'" target="_blank"><i class="la la-print"></i> Html print</a>';
                            btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/print/thermal/'+key_id+'" target="_blank"><i class="la la-print"></i> Thermal print</a>';
                            btnPrint += '</div>';
                            btnPrint += '</div>';
                        }else if(casetype === 'barcode-labels-shelf-price'){
                            var btnPrint = '<div class="dropdown">';
                            btnPrint += '<a href="javascript:;" class="btn btn-sm btn-soft btn-icon btn-icon-md" data-toggle="dropdown">';
                            btnPrint += '<i class="la la-bars"></i></a>';
                            btnPrint += '<div class="dropdown-menu dropdown-menu-right">';
                            btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/print/price/'+key_id+'" target="_blank"><i class="la la-print"></i> Price</a>';
                            btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/print/shelf/'+key_id+'" target="_blank"><i class="la la-print"></i> Shelf</a>';
                            btnPrint += '</div>';
                            btnPrint += '</div>';
                        }else if(casetype === 'grn'){
                            var btnPrint = '<div class="dropdown">';
                            btnPrint += '<a href="javascript:;" class="btn btn-sm btn-soft btn-icon btn-icon-md" data-toggle="dropdown">';
                            btnPrint += '<i class="la la-bars"></i></a>';
                            btnPrint += '<div class="dropdown-menu dropdown-menu-right">';
                            btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/print/'+key_id+'" target="_blank"><i class="la la-print"></i> Print</a>';
                            // btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/view/'+key_id+'" target="_blank"><i class="la la-eye"></i> View</a>';
                            // btnPrint += '<a class="dropdown-item" href="/grn/grn-price-tag" target="_blank" data-id="'+key_id+'"><i class="la la-barcode"></i> Barcode Print</a>';
                            btnPrint += '<button type="button" class="dropdown-item generateTags" data-id='+key_id+' ><i class="la la-barcode"></i> Barcode Print</button>';
                            btnPrint += '<button type="button" class="dropdown-item generatePrice" data-id='+key_id+' ><i class="la la-tag"></i> Update Product Price</button>';
                            btnPrint += '</div>';
                            btnPrint += '</div>';
                        }else{
                            var btnPrint = '<div class="dropdown">';
                            btnPrint += '<a href="javascript:;" class="btn btn-sm btn-soft btn-icon btn-icon-md" data-toggle="dropdown">';
                            btnPrint += '<i class="la la-bars"></i></a>';
                            btnPrint += '<div class="dropdown-menu dropdown-menu-right">';
                            btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/print/'+key_id+'" target="_blank"><i class="la la-print"></i> Print</a>';
                            btnPrint += '<a class="dropdown-item" href="/'+path_url.path+'/view/'+key_id+'" target="_blank"><i class="la la-eye"></i> View</a>';
                            btnPrint += '</div>';
                            btnPrint += '</div>';
                        }
                    }
                 }else{
                    var btnPrint = '<a href="/'+path_url.path+'/view/'+key_id+'" target="_blank" class="btn btn-sm btn-soft btn-icon btn-icon-sm" title="View">\
                    <i class="la la-eye"></i>\
                    </a>';
                 }
                return btnChangePass+' '+btnEdit+' '+btnDel+' '+btnPrint;
                /*return '\
                <a href="/'+path_url.path_form+'/'+key_id+'" class="btn btn-sm btn-primary btn-icon btn-icon-sm" title="Edit">\
                    <i class="la la-edit"></i>\
                </a>\
                <button data-url="/'+path_url.path+'/delete/'+key_id+'" id="del" class="btn btn-danger btn-sm btn-icon btn-icon-sm" title="Delete">\
                    <i class="la la-trash"></i>\
                </button>\
                ';*/
            }
        }
        colmnsData.push(action);
       // console.log("L: "+ JSON.stringify(colmnsData));

        var datatable = table.KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        method: 'GET',
                        url: tableUrl,
                        map: function(raw) {
                            // sample data mapping
                            var dataSet = raw;
                            statuses = dataSet.statuses;
                            keyid = dataSet.keyid;
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
            /*columns: [
                {
                    field: 'group_item_id',
                    title: 'Chart Account Id',
                    sortable: 'asc',
                    width: 110,
                    type: 'number',
                    selector: false,
                    textAlign: 'center',
                }, {
                    field: 'group_item_name',
                    title: 'Chart Name',
                }, {
                    field: 'actions',
                    title: 'Actions',
                    sortable: false,
                    width: 110,
                    overflow: 'visible',
                    autoHide: false,
                    template: function() {
                        return '\
						<a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit details">\
							<i class="flaticon2-paper"></i>\
						</a>\
						<a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Delete">\
							<i class="flaticon2-trash"></i>\
						</a>\
					';
                    },
                }],*/

        });

        $('#kt_form_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });
        $('body').on('submit', 'form[name="filter-form"]', function(event) {
            event.preventDefault();
            $('.kt-container').css({'pointer-events':'none','opacity':'0.5'});
            closeUserFilterModal();
            localStorage.removeItem('ajax_data-1-meta');
            datatable.search($(this).serialize(), 'filters');
        });

        $('body').on('submit', 'form[name="getRecordsByDateFilter"]', function(event) {
            event.preventDefault();
            var filterData = {};
            var date_type = $(document).find('form input[name="radioDate"]').val()
            filterData.date = date_type;
            filterData.time_from = $(document).find('form input[name="time_from"]').val();;
            filterData.time_to = $(document).find('form input[name="time_to"]').val();;
            if(date_type == 'custom_date'){
                filterData.from = $(document).find('form input[name="from"]').val();
                filterData.to = $(document).find('form input[name="to"]').val();
            }
            var global_search = $('#generalSearch').val();
            if(!valueEmpty(global_search)){
                filterData.global_search = global_search;
            }

            $('.kt-container').css({'pointer-events':'none','opacity':'0.5'});
            localStorage.removeItem('ajax_data-1-meta');
            datatable.search(filterData, 'globalFilters');
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
            console.log("f");
            // matchDate();
        }).on('kt-datatable--on-layout-updated', function() {
            console.log("f1");
            setInterval(function(){
                $('#ajax_data').find('.kt-spinner').remove();
            },800);
            matchDate();
            var net_amt = 0;
            $('table>tbody>tr').each(function(){
                if(casetype == 'purchase-order'){
                    var amt = $(this).find("td[data-field='purchase_order_total_net_amount']>span").text();
                }
                if(casetype == 'grn' || casetype == 'purchase-return'){
                    var amt = $(this).find("td[data-field='grn_total_net_amount']>span").text();
                }
                net_amt += parseFloat(amt);
            });
            $('.grn_total_amount').html(net_amt.toLocaleString());

        }).on('kt-datatable--on-ajax-done', function() {
            console.log("f2");
            $('.kt-container').css({'pointer-events':'','opacity':''});
            var spinner = '<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style="position: absolute;z-index: 999;left: 0; right: 0;top: 16%;"></div>';
            $('#ajax_data').prepend(spinner);
            // matchDate();
        });
    };
    return {
        // public functions
        init: function() {
            demo();
            eventsCapture();
        },
    };
}();
function matchDate(){
    $('#ajax_data>table>tbody>tr>td[data-field="demand_date"] span , #ajax_data>table>tbody>tr>td[data-field="lpo_date"] span, #ajax_data>table>tbody>tr>td[data-field="purchase_order_entry_date"] span , #ajax_data>table>tbody>tr>td[data-field="grn_date"] span , #ajax_data>table>tbody>tr>td[data-field="bank_rec_date"] span , #ajax_data>table>tbody>tr>td[data-field="voucher_date"] span  , #ajax_data>table>tbody>tr>td[data-field="sales_order_date"] span , #ajax_data>table>tbody>tr>td[data-field="sales_contract_start_date"] span ,  #ajax_data>table>tbody>tr>td[data-field="sales_date"] span ,  #ajax_data>table>tbody>tr>td[data-field="day_date"] span , #ajax_data>table>tbody>tr>td[data-field="bd_date"] span , #ajax_data>table>tbody>tr>td[data-field="sales_delivery_date"] span , #ajax_data>table>tbody>tr>td[data-field="protection_date"] span , #ajax_data>table>tbody>tr>td[data-field="stock_date"] span ,  #ajax_data>table>tbody>tr>td[data-field="item_formulation_date"] span , #ajax_data>table>tbody>tr>td[data-field="mb_stock_transfer_entry_date"] span , #ajax_data>table>tbody>tr>td[data-field="brochure_date"] span , #ajax_data>table>tbody>tr>td[data-field="loan_date"] span' ).each(function(){
        var d_date = $(this);
        var str = d_date.text();
        var d = new Date(str);
        var returnDate = "";
        if(d){
            var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
            var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
            var year = d.getFullYear();
            if(!valueEmpty(day) && !valueEmpty(month) && !valueEmpty(year)){
                var returnDate =  day +'-'+ month +'-'+ year;
            }
        }

        d_date.html(returnDate);
    });
}

jQuery(document).ready(function() {
    KTDatatableRemoteAjaxDemo.init();
});
