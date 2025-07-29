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

function qty(){
    $(document).on('keyup' , '.tblGridCal_qty,.tblGridCal_rate,.fc_rate,.tblGridCal_purc_rate', function(){
        var tr = $(this).parents('tr');
        amountCalc(tr);
        discount(tr);
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
        totalStockAmount();
        if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
            allGridTotal();
        }
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

    if(form_type == "barcode_labels"){
        var qty = 1;
    }

    var form_type_arr = ['st','ist'];
    if(form_type_arr.includes(form_type)){
        var rate = (tr.find(".tblGridCal_purc_rate").val() == "")? 0 : tr.find(".tblGridCal_purc_rate").val();
    }else{
        var rate = (tr.find(".tblGridCal_rate").val() == "")? 0 : tr.find(".tblGridCal_rate").val();
    }

    var v = parseFloat(qty)*parseFloat(rate);
    v = js__number_format(v);
    // $('span.t_total').html(v);

    var round_dec_arr = ['sale_invoice'];
    v = valueEmpty(v)?0:v;
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
        v = js__number_format(v);
        if(discount > 100){
            v = 0;
           tr.find('.tblGridCal_discount_perc').val(v.toFixed(3));
        }
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
        if(v > 100){
            v = 0;
            tr.find('.tblGridCal_discount_amount').val(v.toFixed(3));
        }
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
        v = js__number_format(v);
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
        v = math.round(v , 1);
        tr.find(".tblGridCal_vat_perc").val(v).attr('title',v);
    }
}
function grossAmount(tr) {
    var amount = (tr.find(".tblGridCal_amount").val() == "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount_amount = (tr.find(".tblGridCal_discount_amount").val() == "" || tr.find(".tblGridCal_discount_amount").val() == undefined)?0:tr.find(".tblGridCal_discount_amount").val();
    var vat_amount = (tr.find(".tblGridCal_vat_amount").val() == "") ? 0 : tr.find(".tblGridCal_vat_amount").val();
    if(vat_amount == undefined) { vat_amount = 0; }
    var v = (parseFloat(amount) + parseFloat(vat_amount)) - parseFloat(discount_amount);
    console.log(v);
    v = js__number_format(v);
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
        v = js__number_format(v);
        tr.find(".fc_rate").val(v).attr('title',v);
    }
}
function Rate(tr) {
  //  if(tr.find(".tblGridCal_rate").val() !=""){fcRate(tr);}
    var fc_rate = (tr.find(".fc_rate").val() == "")? 0 : tr.find(".fc_rate").val();
    var exchange_rate = ($('form').find("#exchange_rate").val() == "")?0:$('form').find("#exchange_rate").val();
    if(fc_rate != ""){
        var v = parseFloat(fc_rate) / parseFloat(exchange_rate);
        v = js__number_format(v);
        tr.find(".tblGridCal_rate").val(v).attr('title',v);
    }
}
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

    t = js__number_format(t);
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

    t = js__number_format(t);
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
        v = valueEmpty(v)?0:v;
        v = js__number_format(v);
        tr.find(".tblGridAdjustment").val(v).attr('title',v);
    }
    if(tblGridCal_amount != ""){
        var v = parseFloat(pd_store_stock) * parseFloat(tblGridCal_rate);
        v = valueEmpty(v)?0:v;
        v = js__number_format(v);
        tr.find(".tblGridCal_amount").val(v).attr('title',v);
    }

    $('.t_stock_gross_total').html(v);

}

function exRate(){
    var val = $('#exchange_rate').val();
    $('.erp_form__grid_body').find('tr').each(function(){
        var tr =$(this);
        var c = $(this).find('.tblGridCal_rate').val();
        var rate = (c == "")? 0 : c;
        var fc_rate = parseFloat(rate) * parseFloat(val);
        fc_rate= js__number_format(fc_rate);
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
        if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
            allGridTotal();
        }
    });
    $(".tblGridCal_discount_amount").keyup(function(){
        var tr = $(this).parents('tr');
        discountAmount(tr)
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
        if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
            allGridTotal();
        }
    });
    $(".tblGridCal_vat_perc").keyup(function(){
        var tr = $(this).parents('tr');
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
        if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
            allGridTotal();
        }
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
    if (typeof allGridTotal !== 'undefined'){ // func make on GRN form
        allGridTotal();
    }
}
function roundDecimalFive(d){
    var m = js__number_format(d);
    var v = parseFloat(d)-parseFloat(m);
    var vF = js__number_format(v);
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
    return js__number_format(b);
}
/*$(document).on('keyup','.tblGridCal_qty,.tblGridCal_purchase_unit,.tblGridCal_percentage,.tblGridCal_cost_price,.tblGridCal_sys_qty,.tblGridCal_rate,.tblGridCal_amount,.tblGridCal_discount_perc,.tblGridCal_discount_amount,.tblGridCal_vat_perc,.tblGridCal_vat_amount,.tblGridCal_gross_amount,.tblGridCal_fc_rate,.tblGridCal_sale_rate,.tblGridCal_mrp,.tblGridCal_gst_perc,.tblGridCal_gst_amount,.tblGridCal_fed_perc,.tblGridCal_fed_amount,.tblGridCal_spec_disc_perc,.tblGridCal_spec_disc_amount,.tblGridCal_cost_amount,.tblGridCal_net_tp,.tblGridCal_last_tp,.tblGridCal_vend_last_tp,.tblGridCal_tp_diff,.tblGridCal_gp_perc,.tblGridCal_gp_amount,.tblGridCal_after_discount_amount',function(){
    allGridTotal();
});*/
function funcNumValid(num){
    if(!valueEmpty(num)){
        var str = num.toString();
        return parseFloat(str.replaceAll(',', ''))
    }
    return 0;
}
function allGridTotal(){
    var t_qty = 0;
    var t_pu = 0;
    var t_per = 0;
    var t_cost = 0;
    var t_sys_qty = 0;
    var t_fc_rate = 0;
    var t_rate = 0;
    var t_sale_rate = 0;
    var t_mrp = 0;
    var t_disc_perc = 0;
    var t_disc_amount = 0;
    var t_after_disc_amount = 0;
    var t_gst_perc = 0;
    var t_gst_amount = 0;
    var t_fed_perc = 0;
    var t_fed_amount = 0;
    var t_spec_disc_perc = 0;
    var t_spec_disc_amount = 0;
    var t_cost_amount = 0;
    var t_gross_amount = 0;
    var t_amount = 0;
    var t_net_tp = 0;
    var t_last_tp = 0;
    var t_vend_last_tp = 0;
    var t_tp_diff = 0;
    var t_gp_perc = 0;
    var t_gp_amount = 0;
    var t_vat_amount = 0;
    var total_item = 0;
    $('.erp_form__grid_body>tr').each(function(){
        total_item += 1;
        var thix = $(this);
        if(funcNumValid(thix.find('.tblGridCal_qty').val())){
            t_qty += funcNumValid(thix.find('.tblGridCal_qty').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_purchase_unit').val())){
            t_pu += funcNumValid(thix.find('.tblGridCal_purchase_unit').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_percentage').val())){
            t_per += funcNumValid(thix.find('.tblGridCal_percentage').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_cost_price').val())){
            t_cost += funcNumValid(thix.find('.tblGridCal_cost_price').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_sys_qty').val())){
            t_sys_qty += funcNumValid(thix.find('.tblGridCal_sys_qty').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_fc_rate').val())){
            t_fc_rate += funcNumValid(thix.find('.tblGridCal_fc_rate').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_rate').val())){
            t_rate += funcNumValid(thix.find('.tblGridCal_rate').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_sale_rate').val())){
            t_sale_rate += funcNumValid(thix.find('.tblGridCal_sale_rate').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_mrp').val())){
            t_mrp += funcNumValid(thix.find('.tblGridCal_mrp').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_discount_perc').val())){
            t_disc_perc += funcNumValid(thix.find('.tblGridCal_discount_perc').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_discount_amount').val())){
            t_disc_amount += funcNumValid(thix.find('.tblGridCal_discount_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_after_discount_amount').val())){
            t_after_disc_amount += funcNumValid(thix.find('.tblGridCal_after_discount_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_gst_perc').val())){
            t_gst_perc += funcNumValid(thix.find('.tblGridCal_gst_perc').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_gst_amount').val())){
            t_gst_amount += funcNumValid(thix.find('.tblGridCal_gst_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_fed_perc').val())){
            t_fed_perc += funcNumValid(thix.find('.tblGridCal_fed_perc').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_fed_amount').val())){
            t_fed_amount += funcNumValid(thix.find('.tblGridCal_fed_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_spec_disc_perc').val())){
            t_spec_disc_perc += funcNumValid(thix.find('.tblGridCal_spec_disc_perc').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_spec_disc_amount').val())){
            t_spec_disc_amount += funcNumValid(thix.find('.tblGridCal_spec_disc_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_cost_amount').val())){
            t_cost_amount += funcNumValid(thix.find('.tblGridCal_cost_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_gross_amount').val())){
            t_gross_amount += funcNumValid(thix.find('.tblGridCal_gross_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_amount').val())){
            t_amount += funcNumValid(thix.find('.tblGridCal_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_net_tp').val())){
            t_net_tp += funcNumValid(thix.find('.tblGridCal_net_tp').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_last_tp').val())){
            t_last_tp += funcNumValid(thix.find('.tblGridCal_last_tp').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_vend_last_tp').val())){
            t_vend_last_tp += funcNumValid(thix.find('.tblGridCal_vend_last_tp').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_tp_diff').val())){
            t_tp_diff += funcNumValid(thix.find('.tblGridCal_tp_diff').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_gp_perc').val())){
            t_gp_perc += funcNumValid(thix.find('.tblGridCal_gp_perc').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_gp_amount').val())){
            t_gp_amount += funcNumValid(thix.find('.tblGridCal_gp_amount').val());
        }
        if(funcNumValid(thix.find('.tblGridCal_vat_amount').val())){
            t_vat_amount += funcNumValid(thix.find('.tblGridCal_vat_amount').val());
        }
    });
    var tr = $('.erp_form__grid_body_total>tr:first-child');
    tr.find('.total_item>input').val(total_item);
    tr.find('.total_grid_qty>input').val(funcNumValid(t_qty).toFixed(3));
    tr.find('.total_grid_pu>input').val("") // val(funcNumValid(t_pu).toFixed(3));
    tr.find('.total_grid_sale_rate>input').val("") //.val(funcNumValid(t_sale_rate).toFixed(3));
    tr.find('.total_grid_sys_qty>input').val(funcNumValid(t_sys_qty).toFixed(3));
    tr.find('.total_grid_mrp>input').val("") // val(funcNumValid(t_mrp).toFixed(3));
    tr.find('.total_grid_cost_amount>input').val(funcNumValid(t_cost_amount).toFixed(3));
    tr.find('.total_grid_disc_perc>input').val("") // val(funcNumValid(t_disc_perc).toFixed(3));
    tr.find('.total_grid_disc_amount>input').val(funcNumValid(t_disc_amount).toFixed(3));
    tr.find('.total_grid_after_disc_amount>input').val(funcNumValid(t_after_disc_amount).toFixed(3));
    tr.find('.total_grid_gst_perc>input').val("") // val(funcNumValid(t_gst_perc).toFixed(3));
    tr.find('.total_grid_gst_amount>input').val(funcNumValid(t_gst_amount).toFixed(3));
    tr.find('.total_grid_fed_perc>input').val("") // val(funcNumValid(t_fed_perc).toFixed(3));
    tr.find('.total_grid_fed_amount>input').val(funcNumValid(t_fed_amount).toFixed(3));
    tr.find('.total_grid_spec_disc_perc>input').val("") // val(funcNumValid(t_spec_disc_perc).toFixed(3));
    tr.find('.total_grid_spec_disc_amount>input').val(funcNumValid(t_spec_disc_amount).toFixed(3));
    tr.find('.total_grid_gross_amount>input').val(funcNumValid(t_gross_amount).toFixed(3));
    // total_grid_amount is net amount
    tr.find('.total_grid_amount>input').val(funcNumValid(t_amount).toFixed(3));
    $('span.t_total').html(t_amount.toFixed(3));
    tr.find('.total_grid_net_tp>input').val("") // val(funcNumValid(t_net_tp).toFixed(3));
    tr.find('.total_grid_last_tp>input').val("") // val(funcNumValid(t_last_tp).toFixed(3));
    tr.find('.total_grid_vend_last_tp>input').val("") // val(funcNumValid(t_vend_last_tp).toFixed(3));
    tr.find('.total_grid_tp_diff>input').val("") // val(funcNumValid(t_tp_diff).toFixed(3));
    tr.find('.total_grid_gp_perc>input').val("") // val(funcNumValid(t_gp_perc).toFixed(3));
    tr.find('.total_grid_gp_amount>input').val(funcNumValid(t_gp_amount).toFixed(3));

    tr.find('.total_grid_perc>input').val(funcNumValid(t_per).toFixed(3));
    tr.find('.total_grid_cost>input').val(funcNumValid(t_cost).toFixed(3));
    tr.find('.total_grid_fc_rate>input').val(funcNumValid(t_fc_rate).toFixed(3));
    tr.find('.total_grid_rate>input').val("") // val(funcNumValid(t_rate).toFixed(3));
    tr.find('.total_grid_vat_amount>input').val(funcNumValid(t_vat_amount).toFixed(3));

    //Summary total
    $('.summary_total_item').val(funcNumValid(total_item).toFixed(3));
    $('.summary_qty_wt').val(funcNumValid(t_qty).toFixed(3));
    $('.summary_amount').val(funcNumValid(t_cost_amount).toFixed(3));
    $('.summary_disc_amount').val(funcNumValid(t_disc_amount).toFixed(3));
    $('.summary_after_disc_amount').val(funcNumValid(t_after_disc_amount).toFixed(3));
    $('.summary_gst_amount').val(funcNumValid(t_gst_amount).toFixed(3));
    $('.summary_fed_amount').val(funcNumValid(t_fed_amount).toFixed(3));
    $('.summary_spec_disc_amount').val(funcNumValid(t_spec_disc_amount).toFixed(3));
    $('.summary_net_amount').val(funcNumValid(t_amount).toFixed(3));

    // TODO : Temporary Fix Only --Adnan (Assumble & Diassamble Form)
    // if($('.t_total').length > 0){
    //     $('.t_total').html(funcNumValid(t_gross_amount).toFixed(3));
    // }

    if (typeof getValuesForDisc !== 'undefined'){ // func make on GRN form
        getValuesForDisc(arr = {});
    }
    if (typeof funcGetOverallNetAmount !== 'undefined'){ // func make on GRN form
        funcGetOverallNetAmount();;
    }
}

var rowCount = $('.sr_count').length;
$('div.summary_total_item').text(parseFloat(rowCount).toFixed(3));

function calcAllRows(){
    $('.erp_form__grid_body tr').each(el => {
        var tr = $('.erp_form__grid_body tr')[el];
        gridCalcByRow(tr);
    });
}

$(document).ready(function(){
    allCalcFunc();
});
