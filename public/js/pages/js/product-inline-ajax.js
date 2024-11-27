//file copy of product-ajax.js
$(document).on('keydown','.pd_barcode',function (e) {
    var selected_row_val = $('#inLineHelp>.selected_row>table>tbody>tr.data-dtl>td:first-child').text();
    var thix = $(this);
    var addRow = 0;
    var checkNewEntry = false;
    var tr = $(this).parents('tr');
    if(tr.attr('id') == 'dataEntryForm'){
        checkNewEntry = true;
    }
    var code = $(this).val().trim();
    if(code != ""){
        console.log("ppp3" + $(this).val());
        if(e.which === 13){
            var id = thix.parents('tr').attr('id');
            if(id == 'dataEntryForm'){
                addRow = 1;
                get_product_detail(tr,code,addRow,checkNewEntry);
            }else{
                addRow = 3;
                get_product_detail(tr,code,addRow,checkNewEntry);
            }
        }
        if(e.which === 9){
            addRow = 2;
            get_product_detail(tr,code,addRow,checkNewEntry);
        }
    }else{
       // tr.find('.pd_barcode').focus();
    }
    tr.find('.pd_barcode').focus();
});

$(document).on('focusout','.pd_barcode',function (e) {
    var selected_row_val = $('#inLineHelp>.selected_row>table>tbody>tr.data-dtl>td:first-child').text();
    thix = $(this);
    var addRow = 0;
    var checkNewEntry = false;
    var tr = $(this).parents('tr');
    /*if(tr.has('#dataEntryForm')){
        checkNewEntry = true;
    }*/
    var code = $(this).val().trim();
    if(selected_row_val != code){
       addRow = 0;
       console.log("ppp");
       get_product_detail(tr,code,addRow,checkNewEntry);
    }
});

function get_product_detail(tr,code,addRow,checkNewEntry){
    var form_type = $('#form_type').val();
    $.ajax({
        type:'GET',
        url:'/demand/itembarcode/'+code,
        data:{},
        success: function(response, status){
            if(response['data'] != null) {
                var returnBack = false;
                var default_qty = 1;
                var sale_rate = 0.00;
                if(form_type == 'sale_invoice' || form_type == 'sale_return' ){
                    if(response['rate'] === null){
                        sale_rate = 0.00;
                    }else{
                        sale_rate = response['rate']['product_barcode_sale_rate_rate'];
                        if(parseFloat(sale_rate) <= 0){
                            sale_rate = (parseFloat(sale_rate)*parseFloat(1)).toFixed(0);
                        }
                        else{
                            sale_rate = (parseFloat(sale_rate)*parseFloat(1)).toFixed(2);
                        }
                    }
                    var str = response['data']['product_barcode_barcode'];
                    var substr = str.substring(0, 2);
                    if(substr == 22){
                        var weight = str.substring(7, 13);
                        default_qty = parseFloat(weight/10000);
                    }
                }
                if(checkNewEntry){
                    $('#repeated_data>tr').each(function () {
                        var val = $(this).find('td:first-child>.product_barcode_id').val();
                        if(val == response['data']['product_barcode_id']){
                            var qty = $(this).find('td>.tblGridCal_qty').val();
                            var rate = $(this).find('td>.tblGridCal_rate').val();
                            qty = parseFloat(qty)+parseFloat(default_qty);
                            var amt = (parseFloat(qty)*parseFloat(rate)).toFixed(3);
                            $(this).find('td>.tblGridCal_qty').val(qty);
                            $(this).find('td>.tblGridCal_amount').val(amt);
                            $(this).find('td>.tblGridCal_gross_amount').val(amt);
                            formClear();
                            allCalcFunc();
                            $(document).find('#inLineHelp').remove();
                            returnBack = true;
                            return true;
                        }
                    });
                }
                if(returnBack){
                    return true;
                }
                if(response['data']['uom'] === null){
                    var uom_id = '';
                    var uom_name = '';
                }else{
                    var uom_id = response['data']['uom']['uom_id'];
                    var uom_name = response['data']['uom']['uom_name'];
                }
                var amount = (parseFloat(default_qty)*parseFloat(sale_rate)).toFixed(3);
                tr.find('td:eq(0)>input.product_barcode_id').val(response['data']['product_barcode_id']);
                tr.find('td:eq(0)>input.product_id').val(response['data']['product']['product_id']);
                tr.find('td:eq(0)>input.uom_id').val(uom_id);
                tr.find('td>.pd_product_name').val(response['data']['product']['product_name']);
                tr.find('td>.pd_uom').val(uom_name);
                tr.find('td>.tblGridCal_qty').val(default_qty);
                tr.find('td>.tblGridCal_rate').val(sale_rate);
                tr.find('td>.pd_packing').val(response['data']['product_barcode_packing']);
                tr.find('td>.pd_store_stock').val(response['data']['store_stock']);
                tr.find('td>.stock_match').val('');
                tr.find('td>.suggest_qty_1').val('');
                tr.find('td>.tblGridCal_amount').val(amount);
                tr.find('td>.tblGridCal_gross_amount').val(amount);

                var options = '';
                for(var i=0;response['uomData'].length>i;i++){
                    options += '<option value='+response['uomData'][i]['uom']['uom_id']+'>'+response['uomData'][i]['uom']['uom_name']+'</option>';
                }
                tr.find('.pd_uom').html(options);
                tr.find('.pd_uom').val(uom_id);

                if(tr.find('td>.pd_product_name') == ""){
                    alert('please enter product name');
                    return false
                }
                
                if(addRow == 1){
                    $('#addData').click();
                    if(form_type == 'sale_invoice' || form_type == 'sale_return'){
                        field_readonly();
                    }
                }
                if(addRow == 0 || addRow == 2 || addRow == 3){
                    tr.find('.pd_uom').focus();
                }
                
            }else{
                /*var data_url = tr.find('td> .pd_barcode').attr('data-url');
                openModal(data_url);*/
            }
        }
    });
}
$(document).on("mouseover", '.data_tbody_row',function(e) {
    var barcode = $(this).find('table>tbody>tr.data-dtl>td[data-view="show"]').text();
    $(this).parents('#inLineHelp').siblings('.pd_barcode').val(barcode);
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

$(document).on('keydown','#supplier_name',function (e) {
    thix = $(this);
    var code = $(this).val();
    if($(this).val() != ""){
        if(e.which === 13){
            $.ajax({
                type:'GET',
                url:'/supplier/supplier-code/'+code,
                data:{},
                success: function(response, status){
                    if(response['data'] != null) {
                        thix.parents('.open-modal-group').find('input#supplier_id').val(response['data']['supplier_id']);
                        thix.parents('.open-modal-group').find('input#supplier_name').val(response['data']['supplier_name']);
                        var index =  thix.parents('.open-modal-group').find('.moveIndex').index() + 1;
                        $('.moveIndex').eq(index).focus();
                    }
                }
            });
        }
    }else{

    }

});
