$(document).on('keyup blur','.tblGridCal_qty,.tblGridCal_rate,.tblGridCal_amount,.tblGridCal_discount_perc,.tblGridCal_discount_amount,.tblGridCal_vat_perc,.tblGridCal_vat_amount',function(){
    var thix = $(this);
    var id = thix.attr('id');
    if(!id){
        id = thix.attr('data-id');
    }
    cd(id);
    var tr = thix.parents('tr');
    rowCalc(tr,id)
});
function rowCalc(tr,id){
    var qty = tr.find('.tblGridCal_qty').val();
    var rate = tr.find('.tblGridCal_rate').val();
    var amount = tr.find('.tblGridCal_amount').val();
    if(rate && id != 'amount'){
        var calc_amount =  parseFloat(qty) * parseFloat(rate);
        if(calc_amount){
            tr.find('.tblGridCal_amount').val(float3(calc_amount));
        }
        var amount = tr.find('.tblGridCal_amount').val();
    }else{
        var calc_rate =  parseFloat(amount) / parseFloat(qty);
        if(calc_rate){
            tr.find('.tblGridCal_rate').val(float3(calc_rate));
        }
        var rate = tr.find('.tblGridCal_rate').val();
    }

    var disc_perc = tr.find('.tblGridCal_discount_perc').val();
    if(disc_perc && id != 'dis_amount'){
        var calc_disc_amount = parseFloat(amount) * parseFloat(disc_perc) / 100;
        if(calc_disc_amount){
            tr.find('.tblGridCal_discount_amount').val(float3(calc_disc_amount));
            var disc_amount = tr.find('.tblGridCal_discount_amount').val();
        }else{
            tr.find('.tblGridCal_discount_amount').val(float3(0));
        }
    }else{
        var disc_amount = tr.find('.tblGridCal_discount_amount').val();
        if(disc_amount){
            var calc_disc_perc = parseFloat(disc_amount) * 100 / parseFloat(amount);
            if(calc_disc_perc){
                tr.find('.tblGridCal_discount_perc').val(float3(calc_disc_perc));
            }else{
                tr.find('.tblGridCal_discount_perc').val(float3(0));
            }
        }else{
            disc_amount = 0;
        }
    }
    var tax_perc = tr.find('.tblGridCal_vat_perc').val();
    if(tax_perc && id != 'vat_amount'){
        var calc_tax_amount = parseFloat(amount) * parseFloat(tax_perc) / 100;
        if(calc_tax_amount){
            tr.find('.tblGridCal_vat_amount').val(float3(calc_tax_amount));
            var tax_amount = tr.find('.tblGridCal_vat_amount').val();
        }else{
            tr.find('.tblGridCal_vat_amount').val(float3(0));
        }
    }else{
        var tax_amount = tr.find('.tblGridCal_vat_amount').val();
        if(tax_amount){
            var calc_tax_perc = parseFloat(tax_amount) * 100 / parseFloat(amount);
            if(calc_tax_perc){
                tr.find('.tblGridCal_vat_perc').val(float3(calc_tax_perc));
            }else{
                tr.find('.tblGridCal_vat_perc').val(float3(0));
            }
        }else{
            tax_amount = 0;
        }
    }
    var net_amount = (float3(amount) - float3(disc_amount)) - float3(tax_amount) ;
    if(net_amount){
        tr.find('.tblGridCal_gross_amount').val(float3(net_amount));
    }else{
        tr.find('.tblGridCal_gross_amount').val(float3(0));
    }
}

function float2(num){
    if(num == null){
        return parseFloat(0).toFixed(2);
    }else{
        return parseFloat(num).toFixed(2);
    }
}
function float3(num){
    if(num == null){
        return parseFloat(0).toFixed(3);
    }else{
        return parseFloat(num).toFixed(3);
    }
}
$(document).on('blur','.tblGridCal_qty,.tblGridCal_rate,.tblGridCal_amount,.tblGridCal_discount_perc,.tblGridCal_discount_amount,.tblGridCal_vat_perc,.tblGridCal_vat_amount,.tblGridCal_gross_amount',function(){
    var num = float3($(this).val());
    $(this).val(float3(num));
    if(num == 'NaN' || num == NaN){
        $(this).val(float3(0));
    }
});
/*$('.tblGridCal_qty,.tblGridCal_rate,.tblGridCal_amount,.tblGridCal_discount_perc,.tblGridCal_discount_amount,.tblGridCal_vat_perc,.tblGridCal_vat_amount,.tblGridCal_gross_amount').on('input', function () {
    this.value = this.value.match(/^\d+\.?\d{0,2}/);
});*/
