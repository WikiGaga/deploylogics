$(document).on('keydown','.pd_barcode',function (e) {
    thix = $(this);
    var tr = $(this).parents('tr');
    var code = $(this).val();
    if($(this).val() != ""){
        if(e.which === 13){
            $.ajax({
                type:'GET',
                url:'/demand/itembarcode/'+code,
                data:{},
                success: function(response, status){
                    if(response['data'] != null) {
                        if(response['data']['uom'] === null){
                            var uom_id = '';
                            var uom_name = '';
                        }else{
                            var uom_id = response['data']['uom']['uom_id'];
                            var uom_name = response['data']['uom']['uom_name'];
                        }
                        tr.find('td:eq(0)>input.product_barcode_id').val(response['data']['product_barcode_id']);
                        tr.find('td:eq(0)>input.product_id').val(response['data']['product']['product_id']);
                        tr.find('td:eq(0)>input.uom_id').val(uom_id);
                        tr.find('td>.pd_product_name').val(response['data']['product']['product_name']);
                        tr.find('td>.pd_uom').val(uom_name);
                        tr.find('td>.pd_packing').val(response['data']['product_barcode_packing']);
                        tr.find('td>.pd_store_stock').val(response['data']['store_stock']);
                        tr.find('td>.stock_match').val('');
                        tr.find('td>.suggest_qty_1').val('');

                        var options = '';
                        for(var i=0;response['uomData'].length>i;i++){
                            options += '<option value='+response['uomData'][i]['uom']['uom_id']+'>'+response['uomData'][i]['uom']['uom_name']+'</option>';
                        }
                        tr.find('.pd_uom').html(options);
                        tr.find('.pd_uom').val(uom_id);
                        tr.find('.pd_uom').focus();
                    }else{
                        var data_url = tr.find('td> .pd_barcode').attr('data-url');
                        openModal(data_url);
                    }
                }
            });
        }
    }else{
        thix = $(this);
        if(thix.val() == "" && e.which == 13){
            thix.focus();
            var data_url = thix.attr('data-url');
            openModal(data_url);
        }
    }
    tr.find('.pd_barcode').focus();
});
$(document).on('change','.pd_uom',function (e) {
    var Val = $(this).val();
    var that = $(this).parents('tr');
    var Id = that.find('td:eq(0)>input.product_id').val();
    if(Val != '')
    {
        $.ajax({
            type:'GET',
            url:'/demand/produom/'+Id,
            data:{},
            success: function(response, status){
                that.find('td:eq(0)>input.product_barcode_id').val('');
                that.find('td> .pd_packing').val('');
                that.find('td> .stock_match').val('');
                that.find('td> .suggest_qty_1').val('');
                for(var i=0;response['data'].length>i;i++){
                    if(Val == response['data'][i]['uom']['uom_id'])
                    {
                        that.find('td:eq(0)>input.product_barcode_id').val(response['data'][i]['product_barcode_id']);
                        that.find('td:eq(0)>input.uom_id').val(response['data'][i]['uom']['uom_id']);
                        that.find('td>.pd_barcode').val(response['data'][i]['product_barcode_barcode']);
                        that.find('td> .pd_packing').val(notNull(response['data'][i]['product_barcode_packing']));
                    }
                }
                //prodUOM();
            }
        });
    }
});
