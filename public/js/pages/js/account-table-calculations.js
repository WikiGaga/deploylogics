/*
* credit
* fccredit
* exchange_rate
* currency

* */
function credit(tr){
        var credit = tr.find(".credit").val();
        if(credit > 0){
            var rate = $('#exchange_rate').val();
            var fccredit = credit * rate;
            fccredit= fccredit.toFixed(3);
            tr.find('.fccredit').val(fccredit);
            tr.find('.debit').val(0);
            tr.find('.fcdebit').val(0);
            TotalAmount();
        }
}
function debit(tr){
        var debit = tr.find(".debit").val();
        if(debit > 0){
            var rate = $('#exchange_rate').val();
            var fcdebit = debit * rate;
            fcdebit= fcdebit.toFixed(3);
            tr.find('.fcdebit').val(fcdebit);
            tr.find('.credit').val(0);
            tr.find('.fccredit').val(0);
            TotalAmount();
        }
}
function fccredit(tr){
        var fccredit = (tr.find(".fccredit").val()=='')?0:tr.find(".fccredit").val();
        if(fccredit > 0){
            var rate = ($('#exchange_rate').val() == '')? 0 : $('#exchange_rate').val();
            var credit = fccredit / rate;
            credit= credit.toFixed(3);
            credit = (credit == 'NaN')?'':credit;
            tr.find('.credit').val(credit);
            tr.find('.fcdebit').val(0);
            tr.find('.debit').val(0);
            TotalAmount();
        }
}
function fcdebit(tr){
        var fcdebit = (tr.find(".fcdebit").val()=='')?0:tr.find(".fcdebit").val();
        if(fcdebit > 0){
            var rate = ($('#exchange_rate').val() == '')? 0 : $('#exchange_rate').val();
            var debit = fcdebit / rate;
            debit= debit.toFixed(3);
            debit = (debit == 'NaN')? '' : debit;
            tr.find('.debit').val(debit);
            tr.find('.fccredit').val(0);
            tr.find('.credit').val(0);
            TotalAmount();
        }
}

function TotalAmount()
{
    var tot_debit = 0;
    var tot_credit = 0;
    var tot_fcdebit = 0;
    var tot_fccredit = 0;
    var tot_vatamt = 0;
    var tot_netamt = 0;
    $( ".erp_form__grid_body>tr" ).each(function( index ) {
        var debit = $(this).find(".debit").val();
            debit = (debit == '' || debit == undefined)? 0 : debit.replace( /,/g, '');
        tot_debit = (parseFloat(tot_debit)+parseFloat(debit));

        var credit = $(this).find(".credit").val();
            credit = (credit == '' || credit == undefined)? 0 : credit.replace( /,/g, '');
        tot_credit = (parseFloat(tot_credit)+parseFloat(credit));

        var fcdebit = $(this).find(".fcdebit").val();
            fcdebit = (fcdebit == '' || fcdebit == undefined)? 0 : fcdebit.replace( /,/g, '');
        tot_fcdebit = (parseFloat(tot_fcdebit)+parseFloat(fcdebit));

        var fccredit = $(this).find(".fccredit").val();
            fccredit = (fccredit == '' || fccredit == undefined)? 0 : fccredit.replace( /,/g, '');
        tot_fccredit = (parseFloat(tot_fccredit)+parseFloat(fccredit));

        var vatamt = $(this).find(".vatamt").val();
        vatamt = (vatamt == '' || vatamt == undefined)? 0 : vatamt.replace( /,/g, '');
        tot_vatamt = (parseFloat(tot_vatamt)+parseFloat(vatamt));

        var netamt = $(this).find(".netamt").val();
        netamt = (netamt == '' || netamt == undefined)? 0 : netamt.replace( /,/g, '');
        tot_netamt = (parseFloat(tot_netamt)+parseFloat(netamt));
    });

    tot_debit= tot_debit.toFixed(3);
    tot_credit= tot_credit.toFixed(3);
    tot_fcdebit= tot_fcdebit.toFixed(3);
    tot_fccredit= tot_fccredit.toFixed(3);
    tot_vatamt= tot_vatamt.toFixed(3);
    tot_netamt= tot_netamt.toFixed(3);

    $("#tot_debit").html(tot_debit);
    $("#tot_credit").html(tot_credit);
    $("#tot_fcdebit").html(tot_fcdebit);
    $("#tot_fccredit").html(tot_fccredit);
    $("#tot_vat").html(tot_vatamt);
    $("#tot_net").html(tot_netamt);

    $("#tot_voucher_debit").val(tot_debit);
    $("#tot_voucher_credit").val(tot_credit);
    $("#tot_voucher_fcdebit").val(tot_fcdebit);
    $("#tot_voucher_fccredit").val(tot_fccredit);
    $("#tot_vat_amt").val(tot_vatamt);
    $("#tot_net_amt").val(tot_netamt);

    var tot_diff = parseFloat(tot_debit) - parseFloat(tot_credit);
    $("#tot_jv_difference").val(tot_diff);
    $("#tot_difference").html(tot_diff);

}
function exRate(){
    var val = $('#exchange_rate').val();
    $(".erp_form__grid_body>tr").each(function(){
        var c = $(this).find('.credit').val();
        var d = $(this).find('.debit').val();
        var fc = val*c;
        var fd = val*d;

        fc= fc.toFixed(3);
        fd= fd.toFixed(3);

        $(this).find('.fccredit').val(fc)
        $(this).find('.fcdebit').val(fd)
        TotalAmount();
    });
}
function calcDC(){
    $(".credit").keyup(function(){
        var tr = $(this).parents('tr');
        credit(tr)
        TotalAmount();
    });
    $(".debit").keyup(function(){
        var tr = $(this).parents('tr');
        debit(tr)
        TotalAmount();
    });
    $(".fccredit").keyup(function(){
        var tr = $(this).parents('tr');
        fccredit(tr)
        TotalAmount();
    });
    $(".fcdebit").keyup(function(){
        var tr = $(this).parents('tr');
        fcdebit(tr)
        TotalAmount();
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
    TotalAmount();
}

function calculateAmounts() {
    var voucherCredit = parseFloat(document.getElementById('voucher_credit').value.replace(/,/g, '')) || 0;
    var vatPerc = parseFloat(document.getElementById('vat_perc').value.replace(/,/g, '')) || 0;

    var vatAmt = (voucherCredit * vatPerc) / 100;
    var netAmt = voucherCredit + vatAmt;

    document.getElementById('vat_amt').value = vatAmt.toFixed(3);
    document.getElementById('net_amt').value = netAmt.toFixed(3);
    TotalAmount();

}

function calculateVAT() {
    var voucherCredit = parseFloat(document.getElementById('voucher_credit').value.replace(/,/g, '')) || 0;
    var vatAmt = parseFloat(document.getElementById('vat_amt').value.replace(/,/g, '')) || 0;

    var vatPerc = (vatAmt / voucherCredit) * 100;
    var netAmt = voucherCredit + vatAmt;

    document.getElementById('vat_perc').value = vatPerc.toFixed(3);
    document.getElementById('net_amt').value = netAmt.toFixed(3);
    TotalAmount();

}

function calculateAmountsGrid(element) {
    var row = element.closest('tr');
    var voucherCredit = parseFloat(row.querySelector('[data-id="voucher_credit"]').value.replace(/,/g, '')) || 0;
    var vatPerc = parseFloat(row.querySelector('[data-id="vat_perc"]').value.replace(/,/g, '')) || 0;

    var vatAmt = (voucherCredit * vatPerc) / 100;
    var netAmt = voucherCredit + vatAmt;

    row.querySelector('[data-id="vat_amt"]').value = vatAmt.toFixed(3);
    row.querySelector('[data-id="net_amt"]').value = netAmt.toFixed(3);
    TotalAmount();

}

function calculateVATGrid(element) {
    var row = element.closest('tr');
    var voucherCredit = parseFloat(row.querySelector('[data-id="voucher_credit"]').value.replace(/,/g, '')) || 0;
    var vatAmt = parseFloat(row.querySelector('[data-id="vat_amt"]').value.replace(/,/g, '')) || 0;

    var vatPerc = (vatAmt / voucherCredit) * 100;
    var netAmt = voucherCredit + vatAmt;

    row.querySelector('[data-id="vat_perc"]').value = vatPerc.toFixed(3);
    row.querySelector('[data-id="net_amt"]').value = netAmt.toFixed(3);
    TotalAmount();

}


$(document).ready(function(){
    calcDC();
    /*setInterval(function(){
        var t_amount = 0;
        var fc_amount = 0;
        $('#AccForm tbody.erp_form__grid_body tr').each(function(){
            t_amount = t_amount + parseFloat($(this).find('.credit').val())
            fc_amount = fc_amount + parseFloat($(this).find('.fccredit').val())
        });
        $("#tot_credit").html(parseFloat(t_amount).toFixed(3));
        $("#tot_voucher_credit").val(parseFloat(t_amount).toFixed(3));
        $("#tot_fccredit").html(parseFloat(fc_amount).toFixed(3));
        $("#tot_voucher_credit").val(parseFloat(fc_amount).toFixed(3));

    },1000)*/
});
