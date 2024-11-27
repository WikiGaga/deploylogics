"use strict";
// Class definition

var KTDatatableRemoteAjaxDemo = function() {
    // Private functions

    if(FORM_TYPE == 'grn'){
        var qty_title = 'Qty';
        var qty_class = 'grn_qty';
    }else{
        var qty_title = 'Demand Qty';
        var qty_class = 'demand_qty';
    }

    // basic demo
    var demo = function() {
        //window.localStorage.clear();
        localStorage.removeItem('ajax_data-1-meta');
        var statuses = [];
        var keyid = "";
        var table = $('#ajax_data');
        var tableUrl = table.attr('data-url');
        var statusClasses = [' m-badge--metal', ' m-badge--success'];
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
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },
            layout: {
                scroll: false,
                footer: false,
                height: null
            },
            sortable: false,
            pagination: false,
            toolbar: {
                items: {
                    pagination: {
                        pageSizeSelect: [10, 20, 30, 50, 100],
                    },
                },
            },
            search: {
                /*input: $('#generalSearch'),*/
            },
            rows: {
                callback: function() {},
                // auto hide columns, if rows overflow. work on non locked columns
                autoHide: false,
            },
            // columns definition
            columns: [
                /*{
                    field: 'product_barcode_barcode',
                    title: 'Barcode',
                    width:250,
                    template: function(row) {
                        var product_barcode_barcode = row.product_barcode_barcode;
                        var product_name = row.product_name;
                        var col = "<div>";
                        col += "<div><span style=' width: 60px; display: inline-block; font-weight: 500; '>Barcode:</span> "+product_barcode_barcode+"</div>";
                        col += "<div><span style=' width: 60px; display: inline-block; font-weight: 500; '>Product:</span> "+product_name+"</div>";
                        col += "</div>";

                        return col;
                    }
                },*/{
                    field: 'product_barcode_barcode',
                    title: 'Barcode',
                    width:100,
                },{
                    field: 'product_name',
                    title: 'Product',
                    width:200,
                    template: function(row) {
                        var product_name = row.product_name;
                        var product_type_name = row.group_item_name_string;
                        var uom_name = row.uom_name;
                        var product_barcode_packing = row.product_barcode_packing;
                        var col = "<div>";
                        col += "<div><span style=' width: 40px; display: inline-block; font-weight: 500; '>Name:</span> "+product_name+"</div>";
                        col += "<div><span style=' width: 40px; display: inline-block; font-weight: 500; '>UOM:</span> "+uom_name +" <span style=' width: 40px; display: inline-block; font-weight: 500; '> PACK:</span> "+product_barcode_packing+"</div>";
                       // col += "<div><span style=' width: 60px; display: inline-block; font-weight: 500; '>Category:</span> "+product_type_name+"</div>";
                        col += "</div>";

                        return col;
                    }
                },{
                    field: 'uom_name',
                    title: 'Product Dtl',
                    width:200,
                    template: function(row) {
                        var uom_name = row.uom_name;
                        var product_type_name = row.group_item_name_string;
                        var supplier_name = row.supplier_name;
                        var col = "<div>";
                      //  col += "<div><span style=' width: 60px; display: inline-block; font-weight: 500; '>UOM:</span> "+uom_name+"</div>";
                        col += "<div><span style=' width: 60px; display: inline-block; font-weight: 500; '>Category:</span> "+product_type_name+"</div>";
                        col += "<div><span style=' width: 60px; display: inline-block; font-weight: 500; '>Supplier:</span> "+supplier_name+"</div>";
                        col += "</div>";

                        return col;
                    }
                }, {
                    field: 'product_barcode_purchase_rate',
                    title: 'Rate',
                    width:140,
                    template: function(row) {
                        var rate = "";
                        if(row.product_barcode_purchase_rate){
                            rate = parseFloat(row.product_barcode_purchase_rate).toFixed(3);
                        }
                        var qty_last_consumption_days = row.qty_last_consumption_days;
                        var last_consumption_days = row.last_consumption_days;
                        var col = "<div>";
                        col += "<div><span style=' width: 85px; display: inline-block; font-weight: 500; '>Purc.Rate:</span> "+rate+"</div>";
                        col += "<div><span style=' width: 85px; display: inline-block; font-weight: 500; '>Last "+last_consumption_days+" days:</span>"+qty_last_consumption_days+"</div>";
                        col += "</div>";
                        return col;
                    }
                },{
                    field: 'suggest',
                    title: 'Suggest',
                    width:110,
                    template: function(row) {
                        var suggest_qty_1 = row.suggest_qty_1;
                        var suggest_qty_2 = row.suggest_qty_2;
                        var col = "<div>";
                        col += "<div><span style=' width: 40px; display: inline-block; font-weight: 500; '>Qty 1:</span> "+suggest_qty_1+" </div>";
                        col += "<div><span style=' width: 40px; display: inline-block; font-weight: 500; '>Qty 2:</span> "+suggest_qty_2+" </div>";
                        col += "</div>";
                        return col;
                    }
                },  {
                    field: 'product_barcode_shelf_stock_min_qty',
                    title: 'Level',
                    width:140,
                    template: function(row) {
                        var min_qty = row.product_barcode_shelf_stock_min_qty;
                        var reorder_qty = row.product_barcode_stock_limit_reorder_qty;
                        var max_qty = row.product_barcode_shelf_stock_max_qty;

                        var col = "<div>";
                        col += "<div><span style=' width: 90px; display: inline-block; font-weight: 500; '>Min Level:</span> "+(min_qty!=null?min_qty:"")+"</div>";
                        col += "<div><span style=' width: 90px; display: inline-block; font-weight: 500; '>Re Order Level:</span> "+(reorder_qty!=null?reorder_qty:"")+"</div>";
                        col += "<div><span style=' width: 90px; display: inline-block; font-weight: 500; '>Max Level:</span> "+(max_qty!=null?max_qty:"")+"</div>";
                        col += "</div>";
                        return col;
                    }
                }, {
                    field: 'stock',
                    title: 'Stock',
                    textAlign: 'right',
                    width:50,
                }, {
                    field: qty_class,
                    title: qty_title,
                    width:80,
                    template: function() {
                        return '\
						<input type="text" class="form-control form-control-sm '+ qty_class +' validNumber" style="height: 26px;padding: 0 5px;">\
					';
                    }
                }, {
                    field: 'actions',
                    title: 'Actions',
                    width:80,
                    template: function() {
                        return '\
                        <div>\
						    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-sm btn_add">Add</a>\
                        </div>\
					';
                    },
                }],
        });
        $(document).on('click','#searchFilterProd',function(event) {
            event.preventDefault();
            $('#ajax_data').prepend('<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style=" position: absolute; z-index: 1; left: 50%; "> <span>loading..</span></div>');
            $('#ajax_data>table').css({opacity:'0.4','user-select': 'none'});
            $('#ajax_data>table input').attr('readonly',true);
            var thix = $(this);
            var row = $(this).parents('.row');
            var data = {};
            if(row.find('#generalSearch').val()){
                data.generalSearch =  row.find('#generalSearch').val()
            }
            if(row.find('#supplierSearch').val()){
                data.supplierSearch =  row.find('#supplierSearch').val()
            }
            if(row.find('#productGroupSearch').val()){
                data.productGroupSearch =  row.find('#productGroupSearch').val()
            }
            datatable.search(data, 'filters');
        });
        $(document).on('keyup','#generalSearch',function(event) {
            if(event.keyCode == 13){ // press enter
                $('#ajax_data').prepend('<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style=" position: absolute; z-index: 1; left: 50%; "> <span>loading..</span></div>');
                $('#ajax_data>table').css({opacity:'0.4','user-select': 'none'});
                $('#ajax_data>table input').attr('readonly',true);
                var data = {};
                data.generalSearch = $(this).val()
                datatable.search(data, 'filters');
            }
            if(event.keyCode == 40){ // press ArrowDown
                $('#ajax_data>table>.kt-datatable__body>tr:first-child').find('td .'+qty_class).focus();
            }
        });



    };
    var eventsCapture = function() {
        $('.kt-datatable').on('kt-datatable--on-init', function() {
            console.log("f1");

        }).on('kt-datatable--on-layout-updated', function() {
            console.log("f2");
            $('#ajax_data').find('.kt-spinner').remove();
            $('#ajax_data>table').css({opacity:'','user-select': ''});
            $('#ajax_data>table input').attr('readonly',false);

        }).on('kt-datatable--on-ajax-done', function() {
            console.log("f3");

        })
    };
    var dataScroll = function() {
        var sendRequest = true;
        $('.kt-datatable__table').on('scroll',function(){
           // console.log("document");
            var height = $('.kt-datatable__table').height();
            var scrollHeight = $('.kt-datatable__table')[0].scrollHeight;
            var scrollTop = $('.kt-datatable__table').scrollTop();
            var l = "height:"+height+", scrollTop:"+scrollTop+" = scrollHeight:"+scrollHeight;
            console.log(l+ " ~R= " + sendRequest);
            var scrollTopHeight = scrollTop + height;
            if((scrollHeight - 10) < scrollTopHeight && sendRequest == true){
                $('#ajax_data').append('<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style="position: absolute;top: 0;z-index: 9;left: 0;right: 0;text-align: center;background: #ffffff;color: #0eb887;margin: 0 auto;width: 10%;border-radius: 4px;"> <span>loading..</span></div>');
               // $('#ajax_data>table').css({opacity: 0.3,'user-select': 'none'})
                sendRequest = false;
                var meta = JSON.parse(localStorage.getItem('ajax_data-1-meta'));
                localStorage.removeItem('ajax_data-1-meta');
                console.log("jkk");
               // demo();
                var page = parseInt(meta.pagination.page) + 1;
                var perpage = parseInt(meta.pagination.perpage);
                var rowStart = page * perpage;
                var url =  "/common/select-multiple-products-data/product?pagination%5Bpage%5D="+page+"&pagination%5Bperpage%5D="+perpage+"&query=";
                var formData = {};
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "GET",
                    url: url,
                    dataType	: 'json',
                    data        : formData,
                    success: function(response,data) {
                        $('#ajax_data').find('.kt-spinner').remove();
                        console.log(response);
                        var len = response.data.length;
                        var data = response.data;
                        if(len > 0){
                            var meta = {
                                pagination : {
                                    page : response.meta.page,
                                    perpage : response.meta.perpage
                                }
                            };

                            localStorage.setItem('ajax_data-1-meta',JSON.stringify(meta));
                            sendRequest = true;
                            for (var i=0;i<len;i++){
                                var item = data[i];
                                var oddEven = (i % 2 == 0)?"even":"odd";
                                var data_row = rowStart+1;
                                var td = "";
                                // '+item['product_barcode_barcode']+'
                                td += '<td data-field="product_barcode_barcode" class="kt-datatable__cell"><span style="width: 100px;">'+notNull(item['product_barcode_barcode'])+'</span></td>';
                                td += '<td data-field="product_name" class="kt-datatable__cell">\n' +
                                    '      <span style="width: 200px;">\n' +
                                    '         <div>\n' +
                                    '            <div><span style=" width: 40px; display: inline-block; font-weight: 500; ">Name:</span> '+notNull(item['product_name'])+'</div>\n' +
                                    '            <div><span style=" width: 40px; display: inline-block; font-weight: 500; ">UOM:</span> '+notNull(item['uom_name'])+' <span style=" width: 40px; display: inline-block; font-weight: 500; "> PACK:</span> '+notNull(item['product_barcode_packing'])+'</div>\n' +
                                    '         </div>\n' +
                                    '      </span>\n' +
                                    '   </td>';
                                td += '<td data-field="uom_name" class="kt-datatable__cell">\n' +
                                    '      <span style="width: 200px;">\n' +
                                    '         <div>\n' +
                                    '            <div><span style=" width: 60px; display: inline-block; font-weight: 500; ">Category:</span> '+notNull(item['group_item_name_string'])+'</div>\n' +
                                    '            <div><span style=" width: 60px; display: inline-block; font-weight: 500; ">Supplier:</span> '+notNull(item['supplier_name'])+'</div>\n' +
                                    '         </div>\n' +
                                    '      </span>\n' +
                                    '   </td>';
                                td += '<td data-field="product_barcode_purchase_rate" class="kt-datatable__cell">\n' +
                                    '      <span style="width: 140px;">\n' +
                                    '         <div>\n' +
                                    '            <div><span style=" width: 85px; display: inline-block; font-weight: 500; ">Purc.Rate:</span> '+notNull(item['product_barcode_purchase_rate'])+'</div>\n' +
                                    '            <div><span style=" width: 85px; display: inline-block; font-weight: 500; ">Last '+item['last_consumption_days']+' days:</span>'+notNull(item['qty_last_consumption_days'])+'</div>\n' +
                                    '         </div>\n' +
                                    '      </span>\n' +
                                    '   </td>';
                                td += '<td data-field="suggest" class="kt-datatable__cell">\n' +
                                    '      <span style="width: 110px;">\n' +
                                    '         <div>\n' +
                                    '            <div><span style=" width: 40px; display: inline-block; font-weight: 500; ">Qty 1:</span> '+notNull(item['suggest_qty_1'])+' </div>\n' +
                                    '            <div><span style=" width: 40px; display: inline-block; font-weight: 500; ">Qty 2:</span> '+notNull(item['suggest_qty_2'])+' </div>\n' +
                                    '         </div>\n' +
                                    '      </span>\n' +
                                    '   </td>';
                                td += '<td data-field="product_barcode_shelf_stock_min_qty" class="kt-datatable__cell">\n' +
                                    '      <span style="width: 140px;">\n' +
                                    '         <div>\n' +
                                    '            <div><span style=" width: 90px; display: inline-block; font-weight: 500; ">Min Level:</span> '+notNull(item['product_barcode_shelf_stock_min_qty'])+'</div>\n' +
                                    '            <div><span style=" width: 90px; display: inline-block; font-weight: 500; ">Re Order Level:</span> '+notNull(item['product_barcode_stock_cons_day'])+'</div>\n' +
                                    '            <div><span style=" width: 90px; display: inline-block; font-weight: 500; ">Max Level:</span> '+notNull(item['product_barcode_shelf_stock_max_qty'])+'</div>\n' +
                                    '         </div>\n' +
                                    '      </span>\n' +
                                    '   </td>';
                                td += '<td class="kt-datatable__cell--right kt-datatable__cell" data-field="stock"><span style="width: 50px;">'+notNull(item['stock'])+'</span></td>';
                                td += '<td data-field="'+qty_class+'" class="kt-datatable__cell">\n' +
                                    '    <span style="width: 80px;">\n' +
                                    '         <input type="text" class="form-control form-control-sm '+qty_class+' validNumber" style="height: 26px;padding: 0 5px;">' +
                                    '    </span>\n' +
                                    '</td>';
                                td += '<td data-field="actions" class="kt-datatable__cell">\n' +
                                    '      <span style="width: 80px;">\n' +
                                    '         <div><a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-sm btn_add">Add</a></div>\n' +
                                    '      </span>\n' +
                                    '   </td>';
                                var tr = '<tr data-row="'+data_row+'" class="kt-datatable__row kt-datatable__row--'+oddEven+'" style="left: 0;">'+td+'</tr>';
                                $('#ajax_data>table>.kt-datatable__body').append(tr);
                            }
                        }
                    },
                    error: function(response,status) {
                        $('#ajax_data').find('.kt-spinner').remove();
                    }
                });
            }
        })
    };


    return {
        // public functions
        init: function() {
            demo();
            eventsCapture();
            dataScroll();
           // selected_products();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableRemoteAjaxDemo.init();

    function notNull(val){
        if(val == null){ return ""; }
        if(val == ""){ return ""; }
        if(val == undefined){ return ""; }
        if(val == "undefined"){ return ""; }
        if(val == NaN){ return ""; }
        if(val == "NaN"){ return ""; }

        return val;
    }
});
