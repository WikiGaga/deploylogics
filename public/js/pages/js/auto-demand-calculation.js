/*
* tblGridCal_qty
* tblGridCal_rate
* tblGridCal_amount
* tblGridCal_discount_perc
* tblGridCal_discount_amount
* tblGridCal_vat_perc
* tblGridCal_vat_amount
* tblGridCal_gross_amount
* */
$(document).on('keyup' , '.tblGridPhysicalQty,.tblGrid_stockQty',function(){
    var tr = $(this).parents('tr');
    adjustmentQty(tr);
});

$(document).on('click' , "input[type='text']" , function(){
    $(this).select();
});


function qty(){
    $(".tblGridCal_qty,.tblGridCal_rate,.fc_rate,.tblGridCal_purc_rate").keyup(function(){
        var tr = $(this).parents('tr');
        amountCalc(tr); 
        discount(tr);
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
        totalStockAmount();
    });
}
$(document).on('keyup',".tblGridCal_parent_qty" , function(){
    var tr = $(this).parents('tr');
    amountCalc(tr);
    discount(tr);
    vat(tr);
    grossAmount(tr);
    totalAllGrossAmount();
    totalStockAmount();
});

function gridCalcByRow(tr){
    var tr = jQuery(tr);
    amountCalc(tr);
    discount(tr);
    vat(tr);
    grossAmount(tr);
    totalAllGrossAmount();
    totalStockAmount();
}

function amountCalc(tr){
    var qty = (tr.find(".tblGridCal_qty").val()== "")? 0 : tr.find(".tblGridCal_qty").val();

    var form_type = $('#form_type').val();
    var form_type_arr = ['st','ist'];
    if(form_type_arr.includes(form_type)){
        var rate = (tr.find(".tblGridCal_purc_rate").val() == "")? 0 : tr.find(".tblGridCal_purc_rate").val();
    }else{
        var rate = (tr.find(".tblGridCal_rate").val() == "")? 0 : tr.find(".tblGridCal_rate").val();
    }

    var v = parseFloat(qty)*parseFloat(rate);
    v = v.toFixed(3);
    $('span.t_total').html(v);
   
    var round_dec_arr = ['sale_invoice'];
    if(round_dec_arr.includes(form_type)){
        tr.find(".tblGridCal_amount").val(roundDecimalFive(v)).attr('title',roundDecimalFive(v));
    }else{
        tr.find(".tblGridCal_amount").val(v).attr('title',v);
    }
}
function amountRate(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var qty = (tr.find(".tblGridCal_qty").val()== "")? 0 : tr.find(".tblGridCal_qty").val();
    if(amount == "" || amount == NaN){
        tr.find(".tblGridCal_rate").val(0);
    }else{
        var v = (parseFloat(amount) / parseFloat(qty));
        v = v.toFixed(3);

        var form_type = $('#form_type').val();
        var form_type_arr = ['st','ist'];
        if(form_type_arr.includes(form_type)){
            tr.find(".tblGridCal_purc_rate").val(v).attr('title',v);
        }else{
            tr.find(".tblGridCal_rate").val(v).attr('title',v);
        }
    }
}
function discount(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount = (tr.find(".tblGridCal_discount_perc").val()== "")? 0 : tr.find(".tblGridCal_discount_perc").val();
    if(discount == "" || discount == NaN){
        tr.find(".tblGridCal_discount_amount").val(0);
    }else{
        var v = (parseFloat(amount)/100*parseFloat(discount));
        v = v.toFixed(3);
        tr.find(".tblGridCal_discount_amount").val(v).attr('title',v);
    }
}
function discountAmount(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount_amount = (tr.find(".tblGridCal_discount_amount").val()== "")? 0 : tr.find(".tblGridCal_discount_amount").val();
    if(discount_amount == "" || discount_amount == 0){
        tr.find(".tblGridCal_discount_perc").val(0);
    }else{
        var v = (parseFloat(discount_amount)*100)/parseFloat(amount);
        v = v.toFixed(3);
        tr.find(".tblGridCal_discount_perc").val(v).attr('title',v);
    }
}

function vat(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount_amount  = (tr.find(".tblGridCal_discount_amount").val()== "")? 0 : tr.find(".tblGridCal_discount_amount").val();
    var grossAmount = parseFloat(amount) - parseFloat(discount_amount);
    var lpo_vat_perc = (tr.find(".tblGridCal_vat_perc").val()== "")? 0 : tr.find(".tblGridCal_vat_perc").val();
    if(lpo_vat_perc == ""){
        tr.find(".tblGridCal_vat_amount").val("");
    }else{
        var v = (parseFloat(grossAmount)/100*parseFloat(lpo_vat_perc));
        v = math.round(v ,3);
        tr.find(".tblGridCal_vat_amount").val(v).attr('title',v);
    }
}
function vatAmount(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount_amount  = (tr.find(".tblGridCal_discount_amount").val()== "")? 0 : tr.find(".tblGridCal_discount_amount").val();
    var grossAmount = parseFloat(amount) - parseFloat(discount_amount);
    var lpo_vat_amount = (tr.find(".tblGridCal_vat_amount").val()== "")? 0 : tr.find(".tblGridCal_vat_amount").val();
    if(lpo_vat_amount == ""){
        tr.find(".tblGridCal_vat_perc").val("");
    }else{
        var v = (parseFloat(lpo_vat_amount)*100)/parseFloat(grossAmount);
        v = math.round(v ,3);
        tr.find(".tblGridCal_vat_perc").val(v).attr('title',v);
    }
}
function grossAmount(tr) {
    var amount = (tr.find(".tblGridCal_amount").val() == "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount_amount = (tr.find(".tblGridCal_discount_amount").val() == "" || tr.find(".tblGridCal_discount_amount").val() == undefined)?0:tr.find(".tblGridCal_discount_amount").val();
    var vat_amount = (tr.find(".tblGridCal_vat_amount").val() == "")?0:tr.find(".tblGridCal_vat_amount").val();
    var v = (parseFloat(amount) + parseFloat(vat_amount)) - parseFloat(discount_amount);
    v = v.toFixed(3);
    var form_type = $('#form_type').val();
    var round_dec_arr = ['sale_invoice'];
    if(round_dec_arr.includes(form_type)){
        tr.find(".tblGridCal_gross_amount").val(roundDecimalFive(v)).attr('title',roundDecimalFive(v));
    }else{
        tr.find(".tblGridCal_gross_amount").val(v).attr('title',v);
    }
}
function fcRate(tr) {
    var rate = (tr.find(".tblGridCal_rate").val() == "")? 0 : tr.find(".tblGridCal_rate").val();
    var exchange_rate = ($('form').find("#exchange_rate").val() == "")?0:$('form').find("#exchange_rate").val();
    if(rate == ""){
        tr.find(".fc_rate").val("");
    }else{
        var v = parseFloat(rate) * parseFloat(exchange_rate);
        v = v.toFixed(3);
        tr.find(".fc_rate").val(v).attr('title',v);
    }
}
function Rate(tr) {
  //  if(tr.find(".tblGridCal_rate").val() !=""){fcRate(tr);}
    var fc_rate = (tr.find(".fc_rate").val() == "")? 0 : tr.find(".fc_rate").val();
    var exchange_rate = ($('form').find("#exchange_rate").val() == "")?0:$('form').find("#exchange_rate").val();
    if(fc_rate != ""){
        var v = parseFloat(fc_rate) / parseFloat(exchange_rate);
        v = v.toFixed(3);
        tr.find(".tblGridCal_rate").val(v).attr('title',v);
    }
}

$(document).on('keyup' , '.product_child_tr input',function(e){
    $calcFields = ['quantity','rate','foc_qty'];
    var id = $(this).data('id');
    var parent = $(this).attr('parent-id');
    var value = 0;
    if($calcFields.includes(id)){
        $('.child-of-'+parent).each((ele)=>{
            var sum = notNullEmpty($('.child-of-'+parent)[ele].querySelector('input[data-id="'+id+'"]').value);
            (sum > 0) ? sum : 0;
            value += parseFloat(sum);
        });
        if(isNaN(value)){ value = 0; }
        if(id == 'quantity'){
            $('#row-'+parent).find("input[data-id='"+id+"']").val(value);
            gridCalcByRow($('#row-'+parent));
        }
        if(id == 'rate'){
            var childs = $('.child-of-'+parent).length;
            $('#row-'+parent).find("input[data-id='"+id+"']").val(value/childs);
            gridCalcByRow($('#row-'+parent));
        }
        if(id == "foc_qty"){
            $('#row-'+parent).find("input[data-id='"+id+"']").val(value);
        }
    }
});


function totalAllGrossAmount(){
    var t = 0;
    var v = 0;
    if($(".erp_form__grid_body>tr").hasClass('product_tr_no')){
        var tr = $(".erp_form__grid_body>tr.product_tr_no");
    }else{
        var tr = $(".erp_form__grid_body>tr");
    }
    tr.each(function( index ) {
        v = $(this).find('td>.tblGridCal_gross_amount').val();
        if($(this).find('td>.pd_demand_qty').length != 0){
            v = $(this).find('td>.pd_demand_qty').val();
        }
        v = (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
        t += parseFloat(v);
    });

    t = t.toFixed(3);
    $('.t_gross_total').html(t);
    $('#pro_tot').val(t);
    
    if($("#TotExpen").val() != undefined)
    {
        TotalExpenseAmount();
    }
}
function totalStockAmount(){
    var t = 0;
    var v = 0;
    $( ".erp_form__grid_body>tr" ).each(function( index ) {
        v = $(this).find('td>.stock_amount').val();
        v = (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
        t += parseFloat(v);
    });

    t = t.toFixed(3);
    $('.t_stock_gross_total').html(t);
}

//adjustment qty 
function adjustmentQty(tr){
    var pd_store_stock = (tr.find(".tblGrid_stockQty").val() == "")? 0 : tr.find(".tblGrid_stockQty").val();
    var tblGridCal_rate = (tr.find(".tblGridCal_rate").val() == "")? 0 : tr.find(".tblGridCal_rate").val();
    var tblGridCal_amount = (tr.find(".tblGridCal_amount").val() == "")? 0 : tr.find(".tblGridCal_amount").val();
    var physical_quantity = ($('form').find(".tblGridPhysicalQty").val() == "")?0:$('form').find(".tblGridPhysicalQty").val();
    
    if(pd_store_stock != ""){
        var v = parseFloat(pd_store_stock) - parseFloat(physical_quantity);
        v = v.toFixed(3);
        tr.find(".tblGridAdjustment").val(v).attr('title',v);
    }
    if(tblGridCal_amount != ""){
        var v = parseFloat(pd_store_stock) * parseFloat(tblGridCal_rate);   
        v = v.toFixed(3);
        tr.find(".tblGridCal_amount").val(v).attr('title',v);
    }
    
    $('.t_stock_gross_total').html(v);

}

function exRate(){
    if($('#exchange_rate').length > 0){
        var val = $('#exchange_rate').val();
    }else{
        var val = 1;
    }
    
    $('.erp_form__grid_body').find('tr').each(function(){
        var tr =$(this);
        var c = $(this).find('.tblGridCal_rate').val();
        var rate = (c == "")? 0 : c;
        var fc_rate = parseFloat(rate) * parseFloat(val);
        fc_rate= fc_rate.toFixed(3);
        $(this).find('.fc_rate').val(fc_rate);
    });
}

function allCalcFunc(){
    $(".tblGridCal_amount").keyup(function(){
        var tr = $(this).parents('tr');
        amountRate(tr);
        fcRate(tr);
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
    });
    $(".tblGridCal_discount_perc").keyup(function(){
        var tr = $(this).parents('tr');
        discount(tr)
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
    });
    $(".tblGridCal_discount_amount").keyup(function(){
        var tr = $(this).parents('tr');
        discountAmount(tr)
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
    });
    $(".tblGridCal_vat_perc").keyup(function(){
        var tr = $(this).parents('tr');
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
    });
    $(".tblGridCal_vat_amount").keyup(function(){
        var tr = $(this).parents('tr');
        vatAmount(tr);
        grossAmount(tr);
        totalAllGrossAmount();
    });
    $(".tblGridCal_rate").keyup(function(){
        var tr = $(this).parents('tr');
        fcRate(tr);
    });
    $(".fc_rate").keyup(function(){
        var tr = $(this).parents('tr');
        Rate(tr);

    });
    $('.stock_amount').keyup(function () {
        totalStockAmount();
    });
    $('.tblGridAdjustment').keyup(function () {
        adjustmentQty();
    });
    $('#exchange_rate').keyup(function () {
        exRate();
    });
    $(".currency").change(function(){
        var currency =  $(this).val();
        $.ajax({
            type:'GET',
            url:'/common/currency/'+ currency,
            success: function(response,  data){
                if(data)
                {
                    $("#exchange_rate").empty();
                    $("#exchange_rate").val(response.currency_rate);
                }
                exRate();
            }
        });
    });
    $('.erp_form__grid_body>tr>td>input').keyup(function(){
        $(this).attr('title',$(this).val());
    });
    qty();
    exRate();
    totalAllGrossAmount();
    totalStockAmount();
}
function roundDecimalFive(d){
    var m = parseFloat(d).toFixed(2);
    var v = parseFloat(d)-parseFloat(m);
    var vF = v.toFixed(3);
    var b = 0;
    if(vF == -0.002 || vF == -0.001){
        b = parseFloat(m);
    }
    if(vF == 0.005){
        b = parseFloat(m)+parseFloat(v);
    }
    if(vF == -0.005 || vF == -0.004 ||  vF == -0.003){
        v = 0.005;
        b = parseFloat(m)-parseFloat(v);
    }
    if(vF == 0.003 || vF == 0.004 ){
        v = 0.005;
        b = parseFloat(m)+parseFloat(v);
    }
    if(vF == 0.000 || vF == 0.001 || vF == 0.002){
        b = parseFloat(d)-parseFloat(v);
    }
    return b.toFixed(3);
}
$(document).on('keyup','.tblGridCal_qty,.tblGridCal_foc_qty,.tblGridCal_rate,.tblGridCal_amount,.tblGridCal_discount_perc,.tblGridCal_discount_amount,.tblGridCal_vat_perc,.tblGridCal_vat_amount,.tblGridCal_gross_amount',function(e){
    if($(this).hasClass('tblGridCal_qty')){
        var thix = $(this).parents('tr');
        var currentVal = $(this).val();
        var maxQty = '';
        var reOrder = '';
        if(currentVal > maxQty){
            console.log('Its Above Max : Red Both');
        }
        if(currentVal < reOrder){
            console.log('It Less Reorder: Red Both');
        }
        if(currentVal < maxQty && currentVal > reOrder){
            console.log('Everything is Fine : Remove Classes');
        }
    }
    allGridTotal();
});

// Row HighLiter
$(document).on('focus','.erp_form__grid_body input,.erp_form__grid_body td',function(e){
    var parent = $(this).parents('tr').find('input[readonly],input,td,select');
    parent.each(function(ele){
        $(this).parents('tr').find('input[readonly],input,td,select')[ele].style.setProperty('background', '#3f51b5', 'important');
        $(this).parents('tr').find('input[readonly],input,td,select')[ele].style.setProperty('color', '#fff', 'important');
        $(this).parents('tr').find('input.grn_green').removeAttr('style');
    });
});
$(document).on('blur','.erp_form__grid_body input,.erp_form__grid_body td',function(e){
    var parent = $(this).parents('tr').find('input[readonly],input,td,select');
    parent.each(function(ele){
        $(this).parents('tr').find('input[readonly],input,td,select')[ele].removeAttribute("style");
        // $('.radioInput').style('background', '#ddd', 'important');
    });
});

function dataDelete(e) {
    $(document).on('click' , '.delDataRow' , function(){
        $('body').addClass('pointerEventsNone');
        var parent = $(this).data('parent');
        var childs = $('.child-of-'+parent).length;
        if(childs == 1){
            $(this).parents("tr").remove();
            $('#row-'+parent).remove();
        }else{
            $(this).parents("tr").remove();
        }

        // updateKeys();
        calcAllRows();
        allCalcFunc();
        if(allGridTotal()){
            $('body').removeClass('pointerEventsNone');
        }
    });
}

function allGridTotal(){
    var t_qty = 0;
    var t_foc_qty = 0;
    var t_amount = 0;
    var t_disc_amount = 0;
    var t_vat_amount = 0;
    var t_gross_amount = 0;
    $('.erp_form__grid_body .product_tr_no,.erp_form__grid_body .single_product_tr_no').each(function(){
        var thix = $(this);

        if(parseFloat(thix.find('.tblGridCal_qty').val())){
            t_qty += parseFloat(thix.find('.tblGridCal_qty').val());
        }
        if(parseFloat(thix.find('.tblGridCal_foc_qty').val())){
            t_foc_qty += parseFloat(thix.find('.tblGridCal_foc_qty').val());
        }
        if(parseFloat(thix.find('.tblGridCal_amount').val())){
            t_amount += parseFloat(thix.find('.tblGridCal_amount').val());
        }
        if(parseFloat(thix.find('.tblGridCal_discount_amount').val())){
            t_disc_amount += parseFloat(thix.find('.tblGridCal_discount_amount').val());
        }
        if(parseFloat(thix.find('.tblGridCal_vat_amount').val())){
            t_vat_amount += parseFloat(thix.find('.tblGridCal_vat_amount').val());
        }
        if(parseFloat(thix.find('.tblGridCal_gross_amount').val())){
            t_gross_amount += parseFloat(thix.find('.tblGridCal_gross_amount').val());
        }
    });
    var tr = $('.erp_form__grid_body_total>tr:first-child');
    tr.find('.total_grid_qty>input').val(parseFloat(t_qty).toFixed(3));
    tr.find('.total_grid_foc_qty>input').val(parseFloat(t_foc_qty).toFixed(3));
    tr.find('.total_grid_amount>input').val(parseFloat(t_amount).toFixed(3));
    
    tr.find('.total_grid_disc_amount>input').val(parseFloat(t_disc_amount).toFixed(3));
    tr.find('.total_grid_vat_amount>input').val(parseFloat(t_vat_amount).toFixed(3));
    tr.find('.total_grid_gross_amount>input').val(parseFloat(t_gross_amount).toFixed(3));

    // TODO : Temporary Fix Only --Adnan (Assumble & Diassamble Form)
    // if($('.t_total').length > 0){
    //     $('.t_total').html(parseFloat(t_gross_amount).toFixed(3));
    // }
    return true;
}

function calcAllRows(){
    $('.erp_form__grid_body tr').each(el => {
        var tr = $('.erp_form__grid_body tr')[el];
        gridCalcByRow(tr);
    });
}

$(document).ready(function(){
    calcAllRows();
    allCalcFunc();
    allGridTotal();
    dataDelete();
});
