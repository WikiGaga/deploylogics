/*
* tblGridCal_qty
* tblGridCal_rate
* tblGridCal_amount
* tblGridCal_discount
* tblGridCal_discount_amount
* tblGridCal_vat_perc
* tblGridCal_vat_amount
* tblGridCal_gross_amount
* */

function qty(){
    $(".tblGridCal_qty,.tblGridCal_rate,.fc_rate ").keyup(function(){
        var tr = $(this).parents('tr');
        amountCalc(tr);
        discount(tr);
        vat(tr);
        grossAmount(tr);
        totalAllGrossAmount();
        totalStockAmount();
    });
}
function amountCalc(tr){
    var qty = (tr.find(".tblGridCal_qty").val()== "")? 0 : tr.find(".tblGridCal_qty").val();
    var rate = (tr.find(".tblGridCal_rate").val() == "")? 0 : tr.find(".tblGridCal_rate").val();
    var v = parseInt(qty)*parseFloat(rate);
    v = v.toFixed(3);
    tr.find(".tblGridCal_amount").val(v).attr('title',v);
}
function discount(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount = (tr.find(".tblGridCal_discount").val()== "")? 0 : tr.find(".tblGridCal_discount").val();
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
        tr.find(".tblGridCal_discount").val(0);
    }else{
        var v = (parseFloat(discount_amount)*100)/parseFloat(amount);
        v = v.toFixed(3);
        tr.find(".tblGridCal_discount").val(v).attr('title',v);
    }
}

function vat(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var lpo_vat_perc = (tr.find(".tblGridCal_vat_perc").val()== "")? 0 : tr.find(".tblGridCal_vat_perc").val();
    if(lpo_vat_perc == ""){
        tr.find(".tblGridCal_vat_amount").val("");
    }else{
        var v = (parseFloat(amount)/100*parseFloat(lpo_vat_perc));
        v = math.round(v ,3);
        tr.find(".tblGridCal_vat_amount").val(v).attr('title',v);
    }
}
function vatAmount(tr){
    var amount = (tr.find(".tblGridCal_amount").val()== "")? 0 : tr.find(".tblGridCal_amount").val();
    var lpo_vat_amount = (tr.find(".tblGridCal_vat_amount").val()== "")? 0 : tr.find(".tblGridCal_vat_amount").val();
    if(lpo_vat_amount == ""){
        tr.find(".tblGridCal_vat_perc").val("");
    }else{
        var v = (parseFloat(lpo_vat_amount)*100)/parseFloat(amount);
        v = math.round(v ,3);
        tr.find(".tblGridCal_vat_perc").val(v).attr('title',v);
    }
}
function grossAmount(tr) {
    var amount = (tr.find(".tblGridCal_amount").val() == "")? 0 : tr.find(".tblGridCal_amount").val();
    var discount_amount = (tr.find(".tblGridCal_discount_amount").val() == "")?0:tr.find(".tblGridCal_discount_amount").val();
    var vat_amount = (tr.find(".tblGridCal_vat_amount").val() == "")?0:tr.find(".tblGridCal_vat_amount").val();
    var v = (parseFloat(amount) + parseFloat(vat_amount)) - parseFloat(discount_amount);
    v = math.round(v , 3);
    tr.find(".tblGridCal_gross_amount").val(v).attr('title',v);

}
function fcRate(tr) {
    var rate = (tr.find(".tblGridCal_rate").val() == "")? 0 : tr.find(".tblGridCal_rate").val();
    var exchange_rate = ($('form').find("#exchange_rate").val() == "")?0:$('form').find("#exchange_rate").val();
    if(rate == ""){
        tr.find(".fc_rate").val("");
    }else{
        var v = parseFloat(rate) * parseFloat(exchange_rate);
        v = v.toFixed(2);
        tr.find(".fc_rate").val(v).attr('title',v);
    }
}
function Rate(tr) {
    if(tr.find(".tblGridCal_rate").val() !=""){fcRate(tr);}
    var fc_rate = (tr.find(".fc_rate").val() == "")? 0 : tr.find(".fc_rate").val();
    var exchange_rate = ($('form').find("#exchange_rate").val() == "")?0:$('form').find("#exchange_rate").val();
    if(fc_rate == ""){
        tr.find(".tblGridCal_rate").val("");
    }else{
        var v = parseFloat(fc_rate) / parseFloat(exchange_rate);
        v = v.toFixed(2);
        tr.find(".tblGridCal_rate").val(v).attr('title',v);
    }
}
function totalAllGrossAmount(){
    var t = 0;
    var v = 0;
    if($("#repeated_data>tr").hasClass('product_tr_no')){
        var tr = $("#repeated_data>tr.product_tr_no");
    }else{
        var tr = $("#repeated_data>tr");
    }
    tr.each(function( index ) {
        v = $(this).find('td>.tblGridCal_gross_amount').val();
        if($(this).find('td>.pd_demand_qty').length != 0){
            v = $(this).find('td>.pd_demand_qty').val();
        }
        v = (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
        t += parseFloat(v);
    });

    t = math.round(t , 3);
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
    $( "#repeated_data>tr" ).each(function( index ) {
        v = $(this).find('td>.stock_amount').val();
        v = (v == '' || v == undefined)? 0 : v.replace( /,/g, '');
        t += parseFloat(v);
    });

    t = t.toFixed(3);
    $('.t_stock_gross_total').html(t);
}

function exRate(){
    var val = $('#exchange_rate').val();
    $('#repeated_data').find('tr').each(function(){
        var tr =$(this); 
        var c = $(this).find('.tblGridCal_rate').val();
        var rate = (c == "")? 0 : c;
        var fc_rate = parseFloat(rate) * parseFloat(val);

        fc_rate= fc_rate.toFixed(2);

        $(this).find('.fc_rate').val(fc_rate);
    });
}

function allCalcFunc(){
    $(".tblGridCal_discount").keyup(function(){
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
    $('#repeated_data>tr>td>input').keyup(function(){
        $(this).attr('title',$(this).val());
    });
    qty();
    exRate();
    totalAllGrossAmount();
    totalStockAmount();
}
$(document).ready(function(){
    allCalcFunc();
});
