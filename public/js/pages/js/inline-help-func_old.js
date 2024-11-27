$(document).on('focusin','.open-inline-help',function(e){
    var that = $(this);

})/*.focusout(function(e){
    e.preventDefault();
    if (($(e.target).closest(".inline_help").length === 0
        || $(e.target).closest(".inline_help_table").length === 0)
        && $(e.target).closest(".open-inline-help").length === 1)
    {
        $('.inline_help_table').remove();
        $('.inline_help').remove();
    }
});*/


if($( window ).width() <= 1024){
    $(document).on('click','#mobOpenInlineSupplierHelp',function (e) {
        var that = $(this).parents('.open-modal-group').find('#supplier_id');
        var thix = $(this).parents('.open-modal-group').find('#supplier_name');
        mobileHelpOpen(that,thix)
    });
    $(document).on('click','#mobOpenInlineHelp',function (e) {
        var that = $('.ErpForm>thead>tr#dataEntryForm>td>#pd_barcode');
        var thix = $(this);
        mobileHelpOpen(that,thix)
    });
    function mobileHelpOpen(that,thix){
        $('#inLineHelp').remove();
        if (that.siblings('#inLineHelp').length == 0) {
            that.after('<div id="inLineHelp"><div class="inLineHelp"></div></div>');
            //   var inLineHelp = $(this).parents('.open-modal-group').find('#inLineHelp');
            var inLineHelp = that.siblings('#inLineHelp').find('.inLineHelp');
            inLineHelp.addClass("inline_help");
            var data_url = thix.attr('data-url');
            inLineHelp.load(data_url);
        } else {
            //  var inLineHelp = $(this).parents('.open-modal-group').find('#inLineHelp');
            var inLineHelp = that.siblings('#inLineHelp');
            inLineHelp.show();
        }
    }
}

$(document).on('keydown','.open-inline-help',function (e) {
    var that = $(this);
    if (e.which === 113) {
        $('#inLineHelp').remove();
        if (that.siblings('#inLineHelp').length == 0) {
            that.after('<div id="inLineHelp"><div class="inLineHelp"></div></div>');
            //   var inLineHelp = $(this).parents('.open-modal-group').find('#inLineHelp');
            var inLineHelp = that.siblings('#inLineHelp').find('.inLineHelp');
            if (that.parents('.open-modal-group').length != 0) {
                inLineHelp.addClass("inline_help");
            } else {
                inLineHelp.addClass("inline_help_table");
            }
            var data_url = that.attr('data-url');
            inLineHelp.load(data_url);
        } else {
            //  var inLineHelp = $(this).parents('.open-modal-group').find('#inLineHelp');
            var inLineHelp = that.siblings('#inLineHelp');
            inLineHelp.show();
        }
    }
    if(e.which === 40){
       // var inLineHelp = that.parents('.open-modal-group').find('#inLineHelp');
        var inLineHelp = that.siblings('#inLineHelp').find('.inLineHelp');
        if(inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false){
                inLineHelp.find('.data_tbody_row:eq(0)').addClass('selected_row');
        }else{
            var index = inLineHelp.find('.data_tbody_row.selected_row').index();
            var ww_index =  index - 2;
            index = index - 1;
            inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
            inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
        }
        var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
        that.val(val);
    }
    if(e.which === 38){
        var inLineHelp = that.siblings('#inLineHelp').find('.inLineHelp');
        if(inLineHelp.find('.data_tbody_row').hasClass('selected_row') == true){
            var index = inLineHelp.find('.data_tbody_row.selected_row').index();
            var ww_index =  index - 2;
            index = index - 3;
            inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
            inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
        }
        var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
        that.val(val);
    }
    if(e.which === 13){
        var selected_row = $('div.inLineHelp>.data_tbody_row.selected_row');
        var caseType = selected_row.parents('#inLineHelp').find('.data_thead_row').attr('id');
        if(caseType == undefined){
                // if barcode val empty then move focus to date
            // var index = $('.moveIndex').index(this) + 1;
            // $('.moveIndex').eq(index).focus();
        }else{
            runFuncForData(selected_row,caseType);
        }
    }
    if($('.inLineHelp .data_tbody_row').hasClass('selected_row')){
        $("#inLineHelp .inline_help_table").scrollTop(0);//set to top
        $("#inLineHelp .inline_help_table").scrollTop($('.selected_row:first').offset().top-350-$("#inLineHelp .inline_help_table").height());
    }

});
$(document).on('keyup','.open-inline-help',function (e) {
    var that = $(this);
    var mobileRequest = true;
    var inLineHelp = that.siblings('#inLineHelp').find('.inLineHelp');
    if ((e.keyCode >= 48 && e.keyCode <= 57) ||
        (e.keyCode >= 65 && e.keyCode <= 90) ||
        $(this).val() == '' || e.keyCode === 8 ) {
        inLineHelp.find('.data_tbody_row').removeClass('selected_row');
        if(inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false) {
            var data_url = $(this).attr('data-url');
            var url = data_url + '/' + encodeURIComponent($(this).val());
            inLineHelp.load(url);
            if($(this).parents('.open-modal-group').length != 0){
                inLineHelp.has('.inline_help').show();
            }else{
                inLineHelp.has('.inline_help_table').show();
            }
            mobileRequest = false;
        }
    }
    if($( window ).width() <= 1024 && mobileRequest == true){
        var data_url = $(this).attr('data-url');
        var url = data_url + '/' + encodeURIComponent($(this).val());
        inLineHelp.load(url);
        if($(this).parents('.open-modal-group').length != 0){
            inLineHelp.has('.inline_help').show();
        }else{
            inLineHelp.has('.inline_help_table').show();
        }
    }
});
/*$(document).on('keyup',function(e){
    console.log("document on keyup");
    if(e.which === 13 || e.which === 9){
        if(!$(e.target).hasClass('open-inline-help')){
            $('.inline_help_table').remove();
            $('.inline_help').remove();
        }
    }
});*/
$(document).on('click',function(e){
    if(!$(e.target).hasClass('open-inline-help')){
        if($( window ).width() <= 1024) {
            $('#inLineHelp').hide();
        }else{
            $('#inLineHelp').remove();
        }
    }
    if($(e.target).is('#mobOpenInlineHelp') || $(e.target).is('#mobOpenInlineHelp>i')
    || $(e.target).is('#mobOpenInlineSupplierHelp') || $(e.target).is('#mobOpenInlineSupplierHelp>i')){
        $('#inLineHelp').show();
    }
});
/*$(document).on('focusout','.open-inline-help',function(e){
  //  $('#inLineHelp').hide();
    console.log('xx');
});*/
$(document).on('click','.data_tbody_row',function(){
    console.log('fff');
    var that = $(this);
    var caseType = that.parents('#inLineHelp').find('.data_thead_row').attr('id');
    runFuncForData(that,caseType);
});
function runFuncForData(that,caseType){
    if(caseType == 'supplierHelp'){
        supplierInlineHelp(that);
    }
    if($( window ).width() <= 1024) {
        if (caseType == 'productHelp') {
            productInlineHelp(that);
        }
    }
    if(caseType == 'accountsHelp'){
        accountsInlineHelp(that);
    }
    if(caseType == 'budgetHelp'){
        budgetInlineHelp(that);
    }
    $(document).find('#inLineHelp').remove();
    that.removeClass('selected_row');
}
function supplierInlineHelp(that){
    var row_name = that.find('tr.data-dtl>td[data-field="supplier_name"]').text();
    var row_id = that.find('tr.d-none>td[data-field="supplier_id"]').text();
    that.parents('.open-modal-group').find('input#supplier_id').val(row_id);
    that.parents('.open-modal-group').find('input#supplier_name').val(row_name);
    var index =  that.parents('.open-modal-group').find('.moveIndex').index() + 1;
    $('.moveIndex').eq(index).focus();
    $('#inLineHelp').remove();
}
function productInlineHelp(that){
    console.log('kkkk');
    var tr = that.parents('tr');
    var help_barcode = that.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text();
    tr.find('.pd_barcode').val(that.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text());
    $.ajax({
        type:'GET',
        url:'/demand/itembarcode/'+help_barcode,
        data:{},
        success: function(response, status){
            var c = response['data']['product_barcode_id'];
            var p = response['data']['product']['product_id'];
            if(response['data'] != null)
            {
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
                tr.find('td>#pd_barcode').val(help_barcode);
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
            }
        }
    });
    tr.find('#pd_barcode').focus();
}
function accountsInlineHelp(that){
    var chart_id = that.find('tr.d-none>td[data-field="chart_account_id"]').text();
    var chart_code = that.find('tr.data-dtl>td[data-field="chart_code"]').text();
    var chart_name = that.find('tr.data-dtl>td[data-field="chart_name"]').text();
    that.parents('tr').find('td>.acc_id').val(chart_id);
    that.parents('tr').find('td>.acc_code').val(chart_code);
    that.parents('tr').find('td>.acc_name').val(chart_name);
    if(that.parents('tr').find('td>.acc_code').val() == undefined){
        var index =  that.parents('tr').find('.acc_name').index() + 2;
        that.parents('tr').find('.moveIndex').eq(index).focus();
        $('#inLineHelp').remove();
    }else{
        var index =  that.parents('tr').find('.moveIndex2').index() + 1;
        that.parents('tr').find('.moveIndex2').eq(index).focus();
        $('#inLineHelp').remove();
    }
}
function budgetInlineHelp(that){
    var budget_id = that.find('tr.d-none>td[data-field="budget_id"]').text();
    var branch_id = that.find('tr.d-none>td[data-field="branch_id"]').text();
    var description = that.find('tr.data-dtl>td[data-field="budget_budgetart_position"]').text();
    var amount = that.find('tr.data-dtl>td[data-field="budget_planned_amount"]').text();
    that.parents('tr').find('td>.budget_id').val(budget_id);
    that.parents('tr').find('td>.budget_branch_id').val(branch_id);
    that.parents('tr').find('td>.budget_dscrp').val(description);
    that.parents('tr').find('td>.budget_dscrp').focus();
    $('#inLineHelp').remove();
}