//file copy of product-inline-ajax.js
$(document).on('keydown','.pd_barcode',function (e) {
    var form_type = $('#form_type').val();
    var thix = $(this);
    var code = thix.val().trim();
    var tr = thix.parents('tr');
    var addRow = 0;
    var checkNewEntry = false;
    if(tr.attr('id') == 'erp_form_grid_header_row'){
        checkNewEntry = true;
    }
    if(code != ""){
        //var barcode_id = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row tr.d-none>td[data-field="product_barcode_id"]').text();
        if(e.which === 13 && tr.find('.product_id').val() == '' && tr.find('.product_barcode_id').val() == ''){
            e.preventDefault()
            addRow = 1;
            if(form_type == 'grn'){
                var po_id = $('#grn_form').find('#purchase_order_id').val();
                get_po_product_detail(tr,po_id,code,addRow);
            }else{
                get_product_detail(tr,code,addRow,checkNewEntry);
            }
        }
        if(e.which === 9 && tr.find('.product_id').val() == '' && tr.find('.product_barcode_id').val() == ''){
            addRow = 2;
            if(form_type == 'grn'){
                var po_id = $('#grn_form').find('#purchase_order_id').val();
                get_po_product_detail(tr,po_id,code,addRow);
            }else{
                get_product_detail(tr,code,addRow,checkNewEntry);
            }
        }
    }
});
/*$(document).on('focusout','.pd_barcode',function (e) {});*/
$(document).on('click','.data_tbody_row',function(){
    var form_type = $('#form_type').val();
    var thix = $(this);
    var caseType = thix.parents('#inLineHelp').find('.data_thead_row').attr('id');
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    var tr = $('.open_inline__help__focus').parents('tr');
    var addRow = 0;
    var checkNewEntry = false;
    if(tr.attr('id') == 'erp_form_grid_header_row'){
        checkNewEntry = true;
    }
    var code = thix.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text();
    if(caseType == 'productHelpSI' || caseType == 'productHelp' ){
        if(form_type == 'grn'){
            var po_id = $('#grn_form').find('#purchase_order_id').val();
            var barcode_id = thix.find('tr.d-none>td[data-field="product_barcode_id"]').text();
            get_po_product_detail(tr,po_id,barcode_id,addRow);
        }else{
            get_product_detail(tr,code,addRow,checkNewEntry);
        }
    }
    if(caseType == 'supplierHelp'){
        get_supplier_detail(thix);
    }
    if(caseType == 'poHelp'){
        get_purchase_order_detail(thix);
    }
    if(caseType == 'customerHelp'){
        get_customer_detail(thix);
    }
    if(caseType == 'saleorderHelp'){
        get_sale_order_detail(thix);
    }
    if(caseType == 'lpoPoHelp'){
        get_lpo_detail(thix);
    }
    $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');
});
function clearHeaderProductData(tr) {
    tr.find('th:first-child input').val('');
    tr.find('input#product_name').val('');
    tr.find('input#pd_packing').val('');
    tr.find('input#quantity').val('');
    tr.find('input#rate').val(notNullEmpty(0, 3));
    tr.find('input#amount').val(notNullEmpty(0, 3));
    tr.find('input#gross_amount').val(notNullEmpty(0, 3));
    tr.find('input#foc_qty').val("");
    tr.find('input#fc_rate').val("");
    tr.find('input#dis_perc').val("");
    tr.find('input#dis_amount').val("");
    tr.find('input#vat_perc').val("");
    tr.find('input#vat_amount').val("");
    tr.find('select#pd_uom').html('<option>Select</option>');
    tr.find('input#returnable_quantity').val("");
}
$('#pd_barcode').bind('paste', null, function() {
    var tr = $(this).parents('tr');
    clearHeaderProductData(tr);
});
/*
$(document).on('click',function(e){

// if focus on uom then call this function again
//  $('.open_inline__help__focus') has then again again call this function
if(!$(e.target).hasClass('data_tbody_row')){
     console.log($(e.target));
     var that = $('.open_inline__help__focus');
     var tr = that.parents('tr');
     var addRow = 0;
     var checkNewEntry = false;
     var code = that.val();
     console.log("code " + code);
     if(code != ''){
         code = code.trim();
         get_product_detail(tr,code,addRow,checkNewEntry);
     }
 }
 $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');
});
*/

$(document).on("mouseover", '.data_tbody_row',function(e) {
    var barcode = $(this).find('table>tbody>tr.data-dtl>td[data-view="show"]').text();
    var tr = $('.open_inline__help__focus').parents('tr');
    tr.find('.pd_barcode').val(barcode);
});

function get_po_product_detail(tr,po_id,code,addRow){
    $.ajax({
        type:'GET',
        url:'/grn/get-po-product/'+code+'/'+po_id,
        data:{},
        success: function(response, status) {
            console.log(response);
            if (response['data']['product'] != null) {
                var product =response['data']['product'];
                var uom_list =response['data']['uom_list'];
                tr.find('input.product_barcode_id').val(product['product_barcode_id']);

                if(response['data']['selected_po_code']){
                    if(product['uom'] === null){
                        var uom_id = '';
                    }else{
                        var uom_id = product['uom']['uom_id'];
                    }
                    var options = '<option value='+product['uom']['uom_id']+'>'+product['uom']['uom_name']+'</option>';
                    /*var options = '';
                    for(var i=0;uom_list.length>i;i++){
                        var selected = (uom_id == uom_list[i]['uom_id'])?"selected":"";
                        options += '<option value='+uom_list[i]['uom_id']+' '+selected +'>'+uom_list[i]['uom_name']+'</option>';
                    }*/
                    tr.find('input.product_id').val(product['product']['product_id']);
                    tr.find('.product_name').val(product['product']['product_name']);
                    tr.find('input.uom_id').val(uom_id);
                    tr.find('.pd_uom').html(options);
                    tr.find('.pd_packing').val(product['purchase_order_dtlpacking']);
                    tr.find('.tblGridCal_qty').val(product['purchase_order_dtlquantity']);
                    tr.find('.foc_qty').val(product['purchase_order_dtlfoc_quantity']);
                    tr.find('.fc_rate').val(product['purchase_order_dtlfc_rate']);
                    tr.find('.tblGridCal_rate').val(product['purchase_order_dtlrate']);
                    tr.find('.tblGridCal_amount').val(product['purchase_order_dtlamount']);
                    tr.find('.tblGridCal_discount_perc').val(product['purchase_order_dtldisc_percent']);
                    tr.find('.tblGridCal_discount_amount').val(product['purchase_order_dtldisc_amount']);
                    tr.find('.tblGridCal_vat_perc').val(product['purchase_order_dtlvat_percent']);
                    tr.find('.tblGridCal_vat_amount').val(product['purchase_order_dtlvat_amount']);
                    tr.find('.tblGridCal_gross_amount').val(product['purchase_order_dtltotal_amount']);
                }else{
                    tr.find('input.product_id').val(product['product_id']);
                    tr.find('.product_name').val(product['product_name']);
                    var options = '<option value='+product['uom_id']+'>'+product['uom_name']+'</option>';
                    tr.find('input.uom_id').val(product['uom_id']);
                    tr.find('.pd_uom').html(options);
                    tr.find('.pd_packing').val(product['product_barcode_packing']);
                }
                if(addRow == 1){
                    $('#addData').click();
                    tr.find('.pd_barcode').focus();
                }
                if(addRow == 0 || addRow == 2 || addRow == 3){
                    tr.find('.pd_barcode').focus();
                }
            } else{
                $(document).find('.erp_form__grid #pd_barcode').focus();
                swal.fire({
                    title: response.message,
                    type: 'warning',
                    showConfirmButton: false
                });
            }
            $('#inLineHelp').remove();
        }
    });
}

function get_product_detail(tr,code,addRow,checkNewEntry){
  //  debugger
    var form_type = $('#form_type').val();
    var store = $('#store_id').val();
    if(form_type == 'purc_demand'){
        var url = '/demand/itembarcode/'+code+'/'+form_type;
    }else if(form_type == 'sa'){
        var url = '/demand/itembarcode/'+code+'/'+form_type+'/'+store;
    }else{
        var url = '/demand/itembarcode/'+code;
    }
    $.ajax({
        type:'GET',
        url:url,
        data:{},
        success: function(response, status){
            var StockAdj = false;
            if(form_type == 'sa' && typeof response['product_exists'] !== undefined && response['product_exists'] == true){
                swal.fire({
                    title: code,
                    text: response['product_exists_msg'],
                    type: 'error',
                    showConfirmButton:true,
                    showCancelButton: true,
                    confirmButtonText: 'Ok',
                    cancelButtonText: 'Cancel',
                    focusConfirm:true
                }).then(function(result){
                    if(result.value){
                        console.log("klklklklk");
                        if(response['data'] != null) {
                            var returnBack = false;
                            var default_qty = 1;
                            var sale_rate = 0.000;
                            if(form_type == 'sale_invoice'){
                                if(response['rate'] === null){
                                    sale_rate = 0.000;
                                }else{
                                    sale_rate = notNullEmpty(response['rate']['product_barcode_sale_rate_rate'],3);
                                    if(parseFloat(sale_rate) <= 0){
                                        sale_rate = (parseFloat(sale_rate)*parseFloat(1)).toFixed(0); // commit on 3:47 01/12/T20
                                        // sale_rate = parseFloat(sale_rate);
                                    } else {
                                        sale_rate = parseFloat(sale_rate)*parseFloat(1);
                                    }
                                }
                                // var str = response['data']['product_barcode_barcode'];
                                var vegetableProduct = false;
                                var substr = code.substring(0, 2);
                                if(response['data']['product_barcode_weight_apply'] == 1){
                                    var weight = code.substring(7, 12);
                                    default_qty = parseFloat(weight/1000);
                                    vegetableProduct = true;
                                }
                            }
                            if(checkNewEntry && form_type == 'sale_invoice'){
                                $('.erp_form__grid_body>tr').each(function () {
                                    var val = $(this).find('.product_barcode_id').val();
                                    if(val == response['data']['product_barcode_id']){
                                        var qty = $(this).find('.tblGridCal_qty').val();
                                        var rate = notNullEmpty($(this).find('.tblGridCal_rate').val(),3);
                                        qty = parseFloat(qty)+parseFloat(default_qty);
                                        var amt = notNullEmpty((parseFloat(qty)*parseFloat(rate)),3);
                                        if(vegetableProduct){
                                            $(this).find('.tblGridCal_qty').val(notNullEmpty(qty,3));
                                        }else{
                                            $(this).find('.tblGridCal_qty').val(qty);
                                        }
                                        $(this).find('.tblGridCal_amount').val(amt);
                                        $(this).find('.tblGridCal_gross_amount').val(amt);
                                        formClear();
                                        allCalcFunc();
                                        $(document).find('#inLineHelp').remove();
                                        returnBack = true;
                                        return true;
                                    }
                                });
                            }
                            if(returnBack){
                                tr.find('.pd_barcode').focus();
                                return true;
                            }
                            if(response['data']['uom'] === null){
                                var uom_id = '';
                                var uom_name = '';
                            }else{
                                var uom_id = response['data']['uom']['uom_id'];
                                var uom_name = response['data']['uom']['uom_name'];
                            }
                            var pd_store_stock = 0;
                            var pd_store_stock_exists = false;
                            $('.erp_form__grid_body>tr').each(function () {
                                var val = $(this).find('.product_barcode_id').val();
                                if(val == response['data']['product_barcode_id']){
                                    pd_store_stock_exists = true;
                                    return true;
                                }
                            })
                            if(pd_store_stock_exists == false){
                                pd_store_stock = response['data']['store_stock'];
                            }
                            var amount = (parseFloat(default_qty)*parseFloat(sale_rate)).toFixed(3);
                            tr.find('input.product_barcode_id').val(response['data']['product_barcode_id']);
                            tr.find('input.product_id').val(response['data']['product']['product_id']);
                            tr.find('input.uom_id').val(uom_id);
                            tr.find('.product_name').val(response['data']['product']['product_name']);
                            tr.find('.pd_uom').val(uom_name);
                            if(vegetableProduct){
                                tr.find('.tblGridCal_qty').val(default_qty.toFixed(3));
                            }else{
                                tr.find('.tblGridCal_qty').val(default_qty);
                            }
                            tr.find('.tblGridCal_rate').val(notNullEmpty(sale_rate,3));
                            tr.find('.pd_packing').val(response['data']['product_barcode_packing']);
                            tr.find('.pd_store_stock').val(pd_store_stock);
                            tr.find('.stock_match').val('');
                            tr.find('.suggest_qty_1').val('');
                            tr.find('#batch_no').val('');
                            tr.find('#production_date').val($('.erp_form__grid_header').find('#batch_no').val());
                            tr.find('#expiry_date').val($('.erp_form__grid_header').find('#batch_no').val());
                            tr.find('.tblGridCal_amount').val(notNullEmpty(amount,3));
                            tr.find('.tblGridCal_gross_amount').val(notNullEmpty(amount,3));

                            /* os = opening stock */
                            if(form_type == 'os'){
                                var barcode_shelf_stock_location = '';
                                for (var j=0;j < response['data']['barcode_dtl'].length; j++ ){
                                    if(parseInt(response['data']['barcode_dtl'][j]['branch_id']) == parseInt(response['data']['branch_id'])){
                                        barcode_shelf_stock_location = response['data']['barcode_dtl'][j]['product_barcode_shelf_stock_location'];
                                    }
                                }
                                var display_location_data = response['display_location'];
                                var shelf_stock_options = '';
                                if(barcode_shelf_stock_location != null && barcode_shelf_stock_location != '' && barcode_shelf_stock_location != 0){
                                    for (var j=0;j < display_location_data.length; j++ ){
                                        if(parseInt(display_location_data[j]['display_location_id']) == parseInt(barcode_shelf_stock_location)){
                                            shelf_stock_options = '<option value='+barcode_shelf_stock_location+'>'+display_location_data[j]['display_location_name_string']+'</option>';
                                        }
                                    }
                                }else{
                                    shelf_stock_options += '<option value="0">Select</option>';
                                    for (var j=0;j < display_location_data.length; j++ ){
                                        shelf_stock_options += '<option value='+display_location_data[j]['display_location_id']+'>'+display_location_data[j]['display_location_name_string']+'</option>';
                                    }
                                }
                                tr.find('.stock_location').html(shelf_stock_options);
                                tr.find('.stock_location_id').val(barcode_shelf_stock_location);
                                $('#stock_location').select2({
                                    placeholder: "Select",
                                });
                            }
                            var options = '';
                            for(var i=0;response['uomData'].length>i;i++){
                                options += '<option value='+response['uomData'][i]['uom']['uom_id']+'>'+response['uomData'][i]['uom']['uom_name']+'</option>';
                            }
                            tr.find('.pd_uom').html(options);
                            tr.find('.pd_uom').val(uom_id);

                            if(tr.find('.product_name') == ""){
                                alert('please enter product name');
                                return false
                            }
                            tr.find('#physical_quantity').focus();
                        } else{
                            tr.find('.pd_barcode').focus();
                            swal.fire({
                                title: code,
                                text: "not found",
                                type: 'warning',
                                showConfirmButton: false
                            });
                        }
                        tr.find('#physical_quantity').focus();
                    }
                });
            }
            if(form_type == 'sa' && typeof response['product_exists'] !== undefined && response['product_exists'] == false){
                var StockAdj = true;
            }
            if(form_type != 'sa' || StockAdj == true){
                if(response['data'] != null) {
                    var returnBack = false;
                    var default_qty = 1;
                    var sale_rate = 0.000;
                    if(form_type == 'sale_invoice'){
                        if(response['rate'] === null){
                            sale_rate = 0.000;
                        }else{
                            sale_rate = notNullEmpty(response['rate']['product_barcode_sale_rate_rate'],3);
                            if(parseFloat(sale_rate) <= 0){
                                sale_rate = (parseFloat(sale_rate)*parseFloat(1)).toFixed(0); // commit on 3:47 01/12/T20
                                // sale_rate = parseFloat(sale_rate);
                            } else {
                                sale_rate = parseFloat(sale_rate)*parseFloat(1);
                            }
                        }
                        // var str = response['data']['product_barcode_barcode'];
                        var vegetableProduct = false;
                        var substr = code.substring(0, 2);
                        if(response['data']['product_barcode_weight_apply'] == 1){
                            var weight = code.substring(7, 12);
                            default_qty = parseFloat(weight/1000);
                            vegetableProduct = true;
                        }
                    }
                    if(checkNewEntry && form_type == 'sale_invoice'){
                        $('.erp_form__grid_body>tr').each(function () {
                            var val = $(this).find('.product_barcode_id').val();
                            if(val == response['data']['product_barcode_id']){
                                var qty = $(this).find('.tblGridCal_qty').val();
                                var rate = notNullEmpty($(this).find('.tblGridCal_rate').val(),3);
                                qty = parseFloat(qty)+parseFloat(default_qty);
                                var amt = notNullEmpty((parseFloat(qty)*parseFloat(rate)),3);
                                if(vegetableProduct){
                                    $(this).find('.tblGridCal_qty').val(notNullEmpty(qty,3));
                                }else{
                                    $(this).find('.tblGridCal_qty').val(qty);
                                }
                                $(this).find('.tblGridCal_amount').val(amt);
                                $(this).find('.tblGridCal_gross_amount').val(amt);
                                formClear();
                                allCalcFunc();
                                $(document).find('#inLineHelp').remove();
                                returnBack = true;
                                return true;
                            }
                        });
                    }
                    if(returnBack){
                        tr.find('.pd_barcode').focus();
                        return true;
                    }
                    if(response['data']['uom'] === null){
                        var uom_id = '';
                        var uom_name = '';
                    }else{
                        var uom_id = response['data']['uom']['uom_id'];
                        var uom_name = response['data']['uom']['uom_name'];
                    }
                    var pd_store_stock = 0;
                    if(form_type == 'sa'){
                        var pd_store_stock_exists = false;
                        $('.erp_form__grid_body>tr').each(function () {
                            var val = $(this).find('.product_barcode_id').val();
                            if(val == response['data']['product_barcode_id']){
                                pd_store_stock_exists = true;
                                return true;
                            }
                        })
                        if(pd_store_stock_exists == false){
                            pd_store_stock = response['data']['store_stock'];
                        }
                    }else{
                        pd_store_stock = response['data']['store_stock'];
                    }

                    var amount = (parseFloat(default_qty)*parseFloat(sale_rate)).toFixed(3);
                    tr.find('input.product_barcode_id').val(response['data']['product_barcode_id']);
                    tr.find('input.product_id').val(response['data']['product']['product_id']);
                    tr.find('input.uom_id').val(uom_id);
                    tr.find('.product_name').val(response['data']['product']['product_name']);
                    tr.find('.pd_uom').val(uom_name);
                    if(vegetableProduct){
                        tr.find('.tblGridCal_qty').val(default_qty.toFixed(3));
                    }else{
                        tr.find('.tblGridCal_qty').val(default_qty);
                    }
                    tr.find('.tblGridCal_rate').val(notNullEmpty(sale_rate,3));
                    tr.find('.pd_packing').val(response['data']['product_barcode_packing']);
                    tr.find('.pd_store_stock').val(pd_store_stock);
                    tr.find('.stock_match').val('');
                    tr.find('.suggest_qty_1').val('');
                    tr.find('#batch_no').val('');
                    tr.find('#production_date').val($('.erp_form__grid_header').find('#batch_no').val());
                    tr.find('#expiry_date').val($('.erp_form__grid_header').find('#batch_no').val());
                    tr.find('.tblGridCal_amount').val(notNullEmpty(amount,3));
                    tr.find('.tblGridCal_gross_amount').val(notNullEmpty(amount,3));

                    /* os = opening stock */
                    if(form_type == 'os'){
                        var barcode_shelf_stock_location = '';
                        for (var j=0;j < response['data']['barcode_dtl'].length; j++ ){
                            if(parseInt(response['data']['barcode_dtl'][j]['branch_id']) == parseInt(response['data']['branch_id'])){
                                barcode_shelf_stock_location = response['data']['barcode_dtl'][j]['product_barcode_shelf_stock_location'];
                            }
                        }
                        var display_location_data = response['display_location'];
                        var shelf_stock_options = '';
                        if(barcode_shelf_stock_location != null && barcode_shelf_stock_location != '' && barcode_shelf_stock_location != 0){
                            for (var j=0;j < display_location_data.length; j++ ){
                                if(parseInt(display_location_data[j]['display_location_id']) == parseInt(barcode_shelf_stock_location)){
                                    shelf_stock_options = '<option value='+barcode_shelf_stock_location+'>'+display_location_data[j]['display_location_name_string']+'</option>';
                                }
                            }
                        }else{
                            shelf_stock_options += '<option value="0">Select</option>';
                            for (var j=0;j < display_location_data.length; j++ ){
                                shelf_stock_options += '<option value='+display_location_data[j]['display_location_id']+'>'+display_location_data[j]['display_location_name_string']+'</option>';
                            }
                        }
                        tr.find('.stock_location').html(shelf_stock_options);
                        tr.find('.stock_location_id').val(barcode_shelf_stock_location);
                        $('#stock_location').select2({
                            placeholder: "Select",
                        });
                    }
                    var options = '';
                    for(var i=0;response['uomData'].length>i;i++){
                        options += '<option value='+response['uomData'][i]['uom']['uom_id']+'>'+response['uomData'][i]['uom']['uom_name']+'</option>';
                    }
                    tr.find('.pd_uom').html(options);
                    tr.find('.pd_uom').val(uom_id);

                    if(tr.find('.product_name') == ""){
                        alert('please enter product name');
                        return false
                    }
                    if(form_type != 'sa'){
                        if(addRow == 1){
                            $('#addData').click();
                            tr.find('.pd_barcode').focus();
                        }
                        if(addRow == 0 || addRow == 2 || addRow == 3){
                            tr.find('.pd_barcode').focus();
                        }
                    }
                } else{
                    tr.find('.pd_barcode').focus();
                    swal.fire({
                        title: code,
                        text: "not found",
                        type: 'warning',
                        showConfirmButton: false
                    });
                }
            }

            $('#inLineHelp').remove();
            $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');
            tr.find('#physical_quantity').focus();
        }
    });
}
$(document).on('change','.stock_location',function (e) {
    var val = $(this).val();
    var tr = $(this).parents('tr');
    tr.find('.stock_location_id').val(val);
});
$(document).on('change','.pd_uom',function (e) {
    var Val = $(this).val();
    var that = $(this).parents('tr');
    var Id = that.find('td:eq(0)>input.product_id').val();
    var qty = that.find('td>input.tblGridCal_qty').val();
    if(Val != '')
    {
        $.ajax({
            type:'GET',
            url:'/demand/produom/'+Id,
            data:{},
            success: function(response, status){
                that.find('td:eq(0)>input.product_barcode_id').val('');
                that.find('td>.pd_packing').val('');
                that.find('td>.stock_match').val('');
                that.find('td>.suggest_qty_1').val('');
                for(var i=0;response['data'].length>i;i++){
                    if(Val == response['data'][i]['uom']['uom_id'])
                    {
                        $.ajax({
                            type:'GET',
                            url:'/demand/prodrate/'+response['data'][i]['product_barcode_id'],
                            data:{},
                            success: function(response, status){
                                if(response!= null){
                                    var amount = (parseFloat(qty)*parseFloat(response['product_barcode_sale_rate_rate'])).toFixed(3);
                                    that.find('td>.tblGridCal_rate').val(response['product_barcode_sale_rate_rate']);
                                    that.find('td>.tblGridCal_amount').val(amount);
                                    that.find('td>.tblGridCal_gross_amount').val(amount);
                                }
                            }
                        });
                        that.find('td:eq(0)>input.product_barcode_id').val(response['data'][i]['product_barcode_id']);
                        that.find('td:eq(0)>input.uom_id').val(response['data'][i]['uom']['uom_id']);
                        that.find('td>.pd_barcode').val(response['data'][i]['product_barcode_barcode']);
                        that.find('td>.pd_packing').val(notNull(response['data'][i]['product_barcode_packing']));
                    }
                }
                //prodUOM();
            }
        });
    }
});

$('#supplier_name').keydown(function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if(code != ""){
        if(e.which === 13){
            e.preventDefault();
            get_supplier_detail(selected_row);
        }
        if(e.which === 9 && thix.val() != ''){
            get_supplier_detail(selected_row);
        }
    }
});

function get_supplier_detail(selected_row){
    var supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();
    var supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();
    selected_row.parents('.erp_form___block').find('#supplier_name').val(supplier_name);
    selected_row.parents('.erp_form___block').find('#supplier_id').val(supplier_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$(document).on('keydown','#purchase_order',function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if(code != ""){
        if(e.which === 13){
            e.preventDefault();
            get_purchase_order_detail(selected_row);
        }
        if(e.which === 9 && thix.val() != ''){
            get_purchase_order_detail(selected_row);
        }
    }
});

function get_purchase_order_detail(selected_row){
    $('.erp_form__grid_body').html('');
    $('#pro_tot').val(notNullEmpty(0,3));
    $('.t_gross_total').text(notNullEmpty(0,3));
    formClear();
    TotalExpenseAmount();
    var purchase_order = selected_row.find('tr.data-dtl>td[data-field="purchase_order_code"]').text();
    var purchase_order_id = selected_row.find('tr.d-none>td[data-field="purchase_order_id"]').text();
    var supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();
    var supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();
    selected_row.parents('.erp_form___block').find('#purchase_order').val(purchase_order).attr('title',purchase_order);
    selected_row.parents('.erp_form___block').find('#purchase_order_id').val(purchase_order_id);
    $('#grn_form').find('#supplier_name').val(supplier_name).attr('title',supplier_name);
    $('#grn_form').find('#supplier_id').val(supplier_id);
    $('#inLineHelp').remove();
    $('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#customer_name').keydown(function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if(code != ""){
        if(e.which === 13){
            e.preventDefault();
            get_customer_detail(selected_row);
        }
        if(e.which === 9 && thix.val() != ''){
            get_customer_detail(selected_row);
        }
    }
});

function get_customer_detail(selected_row){
    var supplier_name = selected_row.find('tr.data-dtl>td[data-field="customer_name"]').text();
    var supplier_id = selected_row.find('tr.d-none>td[data-field="customer_id"]').text();
    selected_row.parents('.erp_form___block').find('#customer_name').val(supplier_name);
    selected_row.parents('.erp_form___block').find('#customer_id').val(supplier_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$(document).on('keydown','#sales_order_code',function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if(code != ""){
        if(e.which === 13){
            e.preventDefault();
            get_sale_order_detail(selected_row);
        }
        if(e.which === 9 && thix.val() != ''){
            get_sale_order_detail(selected_row);
        }
    }
});

function get_sale_order_detail(selected_row){
    var sale_order = selected_row.find('tr.data-dtl>td[data-field="sales_order_code"]').text();
    var sale_order_id = selected_row.find('tr.d-none>td[data-field="sales_order_id"]').text();
    selected_row.parents('.erp_form___block').find('#sales_order_code').val(sale_order);
    selected_row.parents('.erp_form___block').find('#sales_order_booking_id').val(sale_order_id);
    $('#inLineHelp').remove();
    $('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}


$(document).on('keydown','#lpo_generation_no',function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if(code != ""){
        if(e.which === 13){
            e.preventDefault();
            get_lpo_detail(selected_row);
        }
        if(e.which === 9 && thix.val() != ''){
            get_lpo_detail(selected_row);
        }
    }
});

function get_lpo_detail (selected_row){
    var form = selected_row.parents('.erp_form___block').closest('form');
    form.find('.erp_form__grid_body>tr').each(function(){
        $(this).find('td:eq(0)>input[data-id="lpo_id"]').parents('tr').remove();
    })
    dataDeleteInit();
    var lpo_code = selected_row.find('tr.data-dtl>td[data-field="lpo_code"]').text();
    var supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();
    var lpo_id = selected_row.find('tr.d-none>td[data-field="lpo_id"]').text();
    var supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();
    var currency_id = selected_row.find('tr.d-none>td[data-field="currency_id"]').text();
    var exchange_rate = selected_row.find('tr.d-none>td[data-field="exchange_rate"]').text();
    var payment_term_id = selected_row.find('tr.d-none>td[data-field="payment_term_id"]').text();
    var payment_term_days = selected_row.find('tr.d-none>td[data-field="supplier_ageing_terms_value"]').text();

    form.find('#lpo_generation_no').val(lpo_code);
    form.find('#lpo_generation_no_id').val(lpo_id);
    form.find('#supplier_name').val(supplier_name);
    form.find('#supplier_id').val(supplier_id);
    form.find(".currency").val(currency_id).trigger('change');
    form.find('#exchange_rate').val(exchange_rate);
    form.val(payment_term_id).trigger('change');
    form.find('#payment_mode').val(payment_term_days);
    $.ajax({
        type:'GET',
        url:'/purchase-order/lpo/'+lpo_id+'/'+supplier_id,
        data:{},
        success: function(response, status){
            if(response.status == 'success') {
                var tr = '';
                var total_length = $('#repeated_data>tr').length;
                for(var p=0; p < response.data['all'].length; p++ ){
                    total_length++;
                    var  row = response.data['all'][p];
                    tr +='<tr>' +
                        '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                        '<input type="text" name="pd['+total_length+'][sr_no]" value="'+total_length+'" title="'+total_length+'" class="form-control sr_no erp-form-control-sm handle" readonly>'+
                        '<input type="hidden" name="pd['+total_length+'][lpo_id]" data-id="lpo_id" value="'+notNull(row['lpo_id'])+'" class="lpo_id form-control erp-form-control-sm " readonly>'+
                        '<input type="hidden" name="pd['+total_length+'][lpo_dtl_id]" data-id="lpo_dtl_id" value="'+notNull(row['lpo_dtl_id'])+'" class="lpo_dtl_id form-control erp-form-control-sm " readonly>'+
                        '<input type="hidden" name="pd['+total_length+'][product_id]" data-id="product_id" value="'+notNull(row['product_id'])+'" class="product_id form-control erp-form-control-sm " readonly>'+
                        '<input type="hidden" name="pd['+total_length+'][uom_id]" data-id="uom_id" value="'+notNull(row['uom_id'])+'"class="uom_id form-control erp-form-control-sm " readonly>'+
                        '<input type="hidden" name="pd['+total_length+'][product_barcode_id]" data-id="product_barcode_id" value="'+notNull(row['product_barcode_id'])+'" class="product_barcode_id form-control erp-form-control-sm " readonly>'+
                        '</td>'+
                        '<td><input type="text" name="pd['+total_length+'][pd_barcode]" data-id="pd_barcode" value="'+notNull(row['product_barcode_barcode'])+'" title="'+notNull(row['product_barcode_barcode'])+'" class="form-control erp-form-control-sm" readonly></td>' +
                        '<td><input type="text" name="pd['+total_length+'][product_name]" data-id="product_name" value="'+notNull(row['product_name'])+'" title="'+notNull(row['product_name'])+'" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                        '<td>'+
                            '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd['+total_length+'][pd_uom]" data-id="pd_uom" title="'+notNull(row['uom_name'])+'">'+
                                '<option value="'+notNull(row['uom_id'])+'">'+notNull(row['uom_name'])+'</option>'+
                            '</select>'+
                        '</td>' +
                        '<td><input type="text" name="pd['+total_length+'][pd_packing]" data-id="pd_packing" value="'+notNull(row['packing_name'])+'" title="'+notNull(row['packing_name'])+'" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                        '<td><input type="text" name="pd['+total_length+'][quantity]" data-id="quantity" value="'+notNull(row['lpo_dtl_quantity'])+'" title="'+notNull(row['lpo_dtl_quantity'])+'" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                        '<td><input type="text" name="pd['+total_length+'][foc_qty]" data-id="foc_qty" value="'+notNull(row['lpo_dtl_foc_quantity'])+'" title="'+notNull(row['lpo_dtl_foc_quantity'])+'" class="form-control tb_moveIndex erp-form-control-sm validNumber"></td>' +
                        '<td><input type="text" name="pd['+total_length+'][fc_rate]" data-id="fc_rate" value="'+notNull(row['lpo_dtl_fc_rate'])+'" title="'+notNull(row['lpo_dtl_fc_rate'])+'" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>' +
                        '<td><input type="text" name="pd['+total_length+'][rate]" data-id="rate" value="'+notNullEmpty(row['lpo_dtl_rate'],twoDecimal)+'"  title="'+notNullEmpty(row['lpo_dtl_rate'],threeDecimal)+'"  class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                        '<td><input type="text" name="pd['+total_length+'][amount]" data-id="amount" value="'+notNullEmpty(row['lpo_dtl_amount'],threeDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_amount'],threeDecimal)+'"  class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                        '<td><input type="text" name="pd['+total_length+'][dis_perc]" data-id="dis_perc" value="'+notNullEmpty(row['lpo_dtl_disc_percent'],twoDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_disc_percent'],twoDecimal)+'"  class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>' +
                        '<td><input type="text" name="pd['+total_length+'][dis_amount]" data-id="dis_amount" value="'+notNullEmpty(row['lpo_dtl_disc_amount'],threeDecimal)+'"  title="'+notNullEmpty(row['lpo_dtl_disc_amount'],threeDecimal)+'"    class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                        '<td><input type="text" name="pd['+total_length+'][vat_perc]" data-id="vat_perc" value="'+notNullEmpty(row['lpo_dtl_vat_percent'],twoDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_vat_percent'],twoDecimal)+'"   class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>' +
                        '<td><input type="text" name="pd['+total_length+'][vat_amount]" data-id="vat_amount" value="'+notNullEmpty(row['lpo_dtl_vat_amount'],threeDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_vat_amount'],threeDecimal)+'" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                        '<td><input type="text" name="pd['+total_length+'][gross_amount]" data-id="gross_amount" value="'+notNullEmpty(row['lpo_dtl_gross_amount'],threeDecimal)+'" title="'+notNullEmpty(row['lpo_dtl_gross_amount'],threeDecimal)+'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                        '<td class="text-center"></td>' +
                        '</tr>';
                }
                form.find('.erp_form__grid_body').append(tr);
                addDataInit();
                toastr.success(response.message);
            }
            else{
                toastr.error(response.message);
            }
        },
        error: function(response,status) {
        },
    });
    $('#inLineHelp').remove();
    $('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

