$(document).on('click','#changeGridItemRate',function(){
    var thix = $(this);
    var form_type = $('#form_type').val();
    var block = thix.parents('.ChangeRateBlock');
    var rate_type = block.find('#rate_type>option:selected').val();
    var rate_perc = block.find('#rate_perc').val();
    var sales_contract = block.find('#sales_contract_id').val();
    var formData = {
        form_type: form_type,
        rate_type: rate_type,
        rate_perc: rate_perc,
        sales_contract: sales_contract
    }
    if(form_type == 'os'){
        formData.rate_type = block.find('#selected_barcode_rate>option:selected').val();
    }
    var url = '/barcode/change-grid-item-rate';
    swal.fire({
        title: 'Change all products rates in grid',
        text: 'Are you sure?',
        type: 'warning',
        showCancelButton: true,
        showConfirmButton: true
    }).then(function(result){
        if(result.value){
            $('.erp_form__grid>.erp_form__grid_body>tr').each(function(){
                var tr = $(this);
                formData.product_id = tr.find('input[data-id="product_id"]').val();
                formData.barcode_id = tr.find('input[data-id="product_barcode_id"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type        : 'POST',
                    url         : url,
                    dataType	: 'json',
                    data        : formData,
                    async       : false,
                    beforeSend: function () {

                    },
                    success: function(response) {
                        if(response['data']) {
                            var rate = response['data']['rate'];
                            update_rate(tr,rate);
                        }
                    }
                });
            });
            total_amount()
        }
    });
});

function update_rate(tr,rate){
    tr.find('.tblGridCal_rate').val(parseFloat(rate).toFixed(3));
    var id = "";
    rowCalc(tr,id)
}

function rowCalc(tr,id){
    var qty = tr.find('.tblGridCal_qty').val();
    var rate = tr.find('.tblGridCal_rate').val();

    var calc_amount =  parseFloat(qty) * parseFloat(rate);
    if(valueEmpty(calc_amount)){
        calc_amount = 0;
    }
    tr.find('.tblGridCal_amount').val(float3(calc_amount));
    var amount = tr.find('.tblGridCal_amount').val();

    var disc_perc = tr.find('.tblGridCal_discount_perc').val();
    var disc_amount = 0;
    if(disc_perc){
        var calc_disc_amount = parseFloat(amount) * parseFloat(disc_perc) / 100;
        if(calc_disc_amount){
            tr.find('.tblGridCal_discount_amount').val(float3(calc_disc_amount));
            disc_amount = tr.find('.tblGridCal_discount_amount').val();
        }else{
            tr.find('.tblGridCal_discount_amount').val(float3(0));
        }
    }else{
        disc_amount = tr.find('.tblGridCal_discount_amount').val();
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
    var tax_amount = 0;
    if(tax_perc){
        var calc_tax_amount = parseFloat(amount) * parseFloat(tax_perc) / 100;
        if(calc_tax_amount){
            tr.find('.tblGridCal_vat_amount').val(float3(calc_tax_amount));
            tax_amount = tr.find('.tblGridCal_vat_amount').val();
        }else{
            tr.find('.tblGridCal_vat_amount').val(float3(0));
        }
    }else{
        tax_amount = tr.find('.tblGridCal_vat_amount').val();
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
    var gross_amount = parseFloat(amount) + parseFloat(tax_amount);
    var net_amount = parseFloat(gross_amount) - parseFloat(disc_amount);
    if(net_amount){
        tr.find('.tblGridCal_gross_amount').val(float3(net_amount));
    }else{
        tr.find('.tblGridCal_gross_amount').val(float3(0));
    }

    var exchange_rate = ($('form').find("#exchange_rate").val() == "")?0:$('form').find("#exchange_rate").val();
    var v = parseFloat(rate) * parseFloat(exchange_rate);
    tr.find(".fc_rate").val(parseFloat(v).toFixed(3)).attr('title',parseFloat(v).toFixed(3));
}
function float3(num){
    if(num == null){
        return parseFloat(0).toFixed(3);
    }else{
        return parseFloat(num).toFixed(3);
    }
}
function total_amount(){
    var total_net_amount = 0;
    $('.erp_form__grid>.erp_form__grid_body>tr').each(function() {
        var tr = $(this);
        total_net_amount += parseFloat(tr.find('.tblGridCal_gross_amount').val());
    });

    $('.t_gross_total').text(float3(total_net_amount));
    $('#pro_tot').val(float3(total_net_amount));

    var tot_expenses = $('#tot_expenses').text();

    var net_amount =  parseFloat(total_net_amount) - parseFloat(tot_expenses)
    $('#total_amountsm').text(float3(net_amount));
    $('#TotalAmtSM').val(float3(net_amount));

}
