$(document).on('keyup','.tblGridCal_qtyFinal',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var amount = 0;
        var rate = tr.find('.tblGridCal_rate').val();
        if(NotEmptyValJs(rate)){
            amount = parseFloat(val) * parseFloat(rate);
        }
        tr.find('.tblGridCal_amount').val(parseFloat(amount).toFixed(3))

        var discPerc = tr.find('.tblGridCal_discount_perc').val();
        var discAmount = tr.find('.tblGridCal_discount_amount').val();
        if(NotEmptyValJs(discPerc)){
            discAmount = ( parseFloat(amount) * parseFloat(discPerc) ) / 100;
            tr.find('.tblGridCal_discount_amount').val(parseFloat(discAmount).toFixed(3))
        }
        if(NotEmptyValJs(discAmount)){
            discPerc = ( parseFloat(discAmount) * 100 ) / parseFloat(amount);
            tr.find('.tblGridCal_discount_perc').val(parseFloat(discPerc).toFixed(3))
        }
        console.log(discAmount);

        if(!NotEmptyValJs(discAmount)){
            discAmount = 0;
        }
        if(!NotEmptyValJs(amount)){
            amount = 0;
        }
        var grossAmount =  parseFloat(amount) - parseFloat(discAmount);

        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();
        if(NotEmptyValJs(vatPerc)){
            vatAmount = ( parseFloat(grossAmount) * parseFloat(vatPerc) ) / 100;
            tr.find('.tblGridCal_vat_amount').val(parseFloat(vatAmount).toFixed(3))
        }
        if(NotEmptyValJs(vatAmount)){
            vatPerc = ( parseFloat(vatAmount) * 100 ) / parseFloat(grossAmount);
            tr.find('.tblGridCal_vat_perc').val(parseFloat(vatPerc).toFixed(3))
        }
        console.log(vatAmount);

        if(!NotEmptyValJs(grossAmount)){
            grossAmount = 0;
        }
        if(!NotEmptyValJs(vatAmount)){
            vatAmount = 0;
        }
        var grossNetAmount = parseFloat(grossAmount) + parseFloat(vatAmount);
        tr.find('.tblGridCal_gross_amount').val(parseFloat(grossNetAmount).toFixed(3))
    }
});
$(document).on('keyup','.tblGridCal_qty',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var qty = val;
        var rate = tr.find('.tblGridCal_rate').val();
        var amount = amountFunc(tr,qty,rate);

        var discPerc = tr.find('.tblGridCal_discount_perc').val();
        var discAmount = tr.find('.tblGridCal_discount_amount').val();

        discAmount = discAmountFunc(tr,amount,discPerc);
        discPerc = discPercFunc(tr,amount,discAmount);

        var grossAmount =  grossAmountFunc(amount,discAmount);

        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();

        vatAmount = vatAmountFunc(tr,vatPerc,grossAmount);
        vatPerc = vatPercFunc(tr,vatAmount,grossAmount);

        grossNetAmountFunc(tr,grossAmount,vatAmount);
    }
});

$(document).on('keyup','.tblGridCal_rate',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var qty = tr.find('.tblGridCal_qty').val()
        var rate = val;
        var amount = amountFunc(tr,qty,rate);

        var discPerc = tr.find('.tblGridCal_discount_perc').val();
        var discAmount = tr.find('.tblGridCal_discount_amount').val();

        discAmount = discAmountFunc(tr,amount,discPerc);
        discPerc = discPercFunc(tr,amount,discAmount);

        var grossAmount =  grossAmountFunc(amount,discAmount);

        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();

        vatAmount = vatAmountFunc(tr,vatPerc,grossAmount);
        vatPerc = vatPercFunc(tr,vatAmount,grossAmount);

        grossNetAmountFunc(tr,grossAmount,vatAmount);
    }
});

$(document).on('keyup','.tblGridCal_discount_perc',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var qty = tr.find('.tblGridCal_qty').val();
        var rate = tr.find('.tblGridCal_rate').val();
        var amount = amountFunc(tr,qty,rate);

        var discPerc = val;
        var discAmount = tr.find('.tblGridCal_discount_amount').val();

        discAmount = discAmountFunc(tr,amount,discPerc);

        var grossAmount =  grossAmountFunc(amount,discAmount);

        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();

        vatAmount = vatAmountFunc(tr,vatPerc,grossAmount);
        vatPerc = vatPercFunc(tr,vatAmount,grossAmount);

        grossNetAmountFunc(tr,grossAmount,vatAmount);
    }
});

$(document).on('keyup','.tblGridCal_discount_amount',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var qty = tr.find('.tblGridCal_qty').val();
        var rate = tr.find('.tblGridCal_rate').val();
        var amount = amountFunc(tr,qty,rate);

        var discPerc = tr.find('.tblGridCal_discount_perc').val();
        var discAmount = val;

        discPerc = discPercFunc(tr,amount,discAmount);

        var grossAmount =  grossAmountFunc(amount,discAmount);

        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();

        vatAmount = vatAmountFunc(tr,vatPerc,grossAmount);
        vatPerc = vatPercFunc(tr,vatAmount,grossAmount);

        grossNetAmountFunc(tr,grossAmount,vatAmount);
    }
});

$(document).on('keyup','.tblGridCal_vat_perc',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var amount = tr.find('.tblGridCal_amount').val();

        var discAmount = tr.find('.tblGridCal_discount_amount').val();

        var grossAmount =  grossAmountFunc(amount,discAmount);

        var vatPerc = val;
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();

        vatAmount = vatAmountFunc(tr,vatPerc,grossAmount);

        grossNetAmountFunc(tr,grossAmount,vatAmount);
    }
});

$(document).on('keyup','.tblGridCal_vat_amount',function(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var amount = tr.find('.tblGridCal_amount').val();

        var discAmount = tr.find('.tblGridCal_discount_amount').val();

        var grossAmount =  grossAmountFunc(amount,discAmount);

        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        var vatAmount = val;

        vatPerc = vatPercFunc(tr,vatAmount,grossAmount);

        grossNetAmountFunc(tr,grossAmount,vatAmount);
    }
});

function initTableCalc(){
    var thix = $(this);
    var tr = thix.parents('tr');
    var val = thix.val();
    if(NotEmptyValJs(val)){
        var qty = tr.find('.tblGridCal_qty').val();
        var rate = tr.find('.tblGridCal_rate').val();
        var amount = amountFunc(tr,qty,rate);

        var discPerc = tr.find('.tblGridCal_discount_perc').val();
        var discAmount = tr.find('.tblGridCal_discount_amount').val();

        discAmount = discAmountFunc(tr,amount,discPerc);
        discPerc = discPercFunc(tr,amount,discAmount);

        var grossAmount =  grossAmountFunc(amount,discAmount);

        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();

        vatAmount = vatAmountFunc(tr,vatPerc,grossAmount);
        vatPerc = vatPercFunc(tr,vatAmount,grossAmount);

        grossNetAmountFunc(tr,grossAmount,vatAmount);
    }
}
function amountFunc(tr,qty,rate){
    var amount = 0;
    if(NotEmptyValJs(rate)){
        amount = parseFloat(qty) * parseFloat(rate);
    }
    tr.find('.tblGridCal_amount').val(parseFloat(amount).toFixed(3))
    return amount;
}
function discAmountFunc(tr,amount,discPerc){
    if(NotEmptyValJs(discPerc)){
        var discAmount = ( parseFloat(amount) * parseFloat(discPerc) ) / 100;
        tr.find('.tblGridCal_discount_amount').val(parseFloat(discAmount).toFixed(3))
    }else{
        var discAmount = tr.find('.tblGridCal_discount_amount').val();
    }
    return discAmount;
}
function discPercFunc(tr,amount,discAmount){
    if(NotEmptyValJs(discAmount)){
        var discPerc = ( parseFloat(discAmount) * 100 ) / parseFloat(amount);
        tr.find('.tblGridCal_discount_perc').val(parseFloat(discPerc).toFixed(3))
    }else{
        var discPerc = tr.find('.tblGridCal_discount_perc').val();
        discAmountFunc(tr,amount,discPerc);
    }
    return discPerc;
}
function vatAmountFunc(tr,vatPerc,grossAmount){
    if(NotEmptyValJs(vatPerc)){
        var vatAmount = ( parseFloat(grossAmount) * parseFloat(vatPerc) ) / 100;
        tr.find('.tblGridCal_vat_amount').val(parseFloat(vatAmount).toFixed(3))
    }else{
        var vatAmount = tr.find('.tblGridCal_vat_amount').val();
    }
    return vatAmount;
}
function vatPercFunc(tr,vatAmount,grossAmount){
    if(NotEmptyValJs(vatAmount)){
        var vatPerc = ( parseFloat(vatAmount) * 100 ) / parseFloat(grossAmount);
        tr.find('.tblGridCal_vat_perc').val(parseFloat(vatPerc).toFixed(3))
    }else{
        var vatPerc = tr.find('.tblGridCal_vat_perc').val();
        vatAmountFunc(tr,vatPerc,grossAmount)
    }
    return vatPerc;
}
function grossAmountFunc(amount,discAmount){
    if(!NotEmptyValJs(amount)){
        amount = 0;
    }
    if(!NotEmptyValJs(discAmount)){
        discAmount = 0;
    }
    var grossAmt = parseFloat(amount) - parseFloat(discAmount);
    return grossAmt;
}
function grossNetAmountFunc(tr,grossAmount,vatAmount){
    if(!NotEmptyValJs(grossAmount)){
        grossAmount = 0;
    }
    if(!NotEmptyValJs(vatAmount)){
        vatAmount = 0;
    }
    var grossNetAmount = parseFloat(grossAmount) + parseFloat(vatAmount);
    tr.find('.tblGridCal_gross_amount').val(parseFloat(grossNetAmount).toFixed(3))
}
function NotEmptyValJs(val){
    if(val !== "" && val !== undefined && val !== null && val !== NaN && val !== 'Infinity'){
        return true;
    }
    return false;
}

