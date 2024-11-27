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
    $(".tblGridCal_qty,.tblGridCal_rate").keyup(function(){
        var qty = ($(".tblGridCal_qty").val()== "")? 0 : $(".tblGridCal_qty").val();
        var rate = ($(".tblGridCal_rate").val() == "")? 0 : $(".tblGridCal_rate").val();
        $(".tblGridCal_amount").val(parseInt(qty)*parseInt(rate));
        discount();
        vat();
        grossAmount();
    });
}
$(".tblGridCal_discount").keyup(function(){
    discount()
    vat();
    grossAmount();
});
$(".tblGridCal_vat_perc").keyup(function(){
    vat();
    grossAmount();
});
function discount(){
    var amount = ($(".tblGridCal_amount").val()== "")? 0 : $(".tblGridCal_amount").val();
    var discount = ($(".tblGridCal_discount").val()== "")? 0 : $(".tblGridCal_discount").val();
    if(discount == ""){
        $(".tblGridCal_discount_amount").val("");
    }else{
        $(".tblGridCal_discount_amount").val(parseInt(amount)-(parseInt(amount)/100*parseInt(discount)));
    }
}
function vat(){
    var amount = ($(".tblGridCal_amount").val()== "")? 0 : $(".tblGridCal_amount").val();
    var lpo_vat_perc = ($(".tblGridCal_vat_perc").val()== "")? 0 : $(".tblGridCal_vat_perc").val();
    if(lpo_vat_perc == ""){
        $(".tblGridCal_vat_amount").val("");
    }else{
        var v = parseInt(amount)-(parseInt(amount)/100*parseInt(lpo_vat_perc));
        v = math.round(v , 3);
        $(".tblGridCal_vat_amount").val(v);
    }
}
function grossAmount() {
    var amount = ($(".tblGridCal_amount").val() == "")? 0 : $(".tblGridCal_amount").val();
    var discount_amount = ($(".tblGridCal_discount_amount").val() == "")?0:$(".tblGridCal_discount_amount").val();
    var vat_amount = ($(".tblGridCal_vat_amount").val() == "")?0:$(".tblGridCal_vat_amount").val();
    var am = (parseInt(amount) + parseInt(vat_amount)) - parseInt(discount_amount);
    $(".tblGridCal_gross_amount").val(math.round(am , 3));
}
$(document).ready(function(){
    qty();
    discount();
    vat();
    grossAmount();
});
