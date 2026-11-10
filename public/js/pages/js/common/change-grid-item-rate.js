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
    if(form_type == 'os' || form_type == 'production-consumption'){
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
            if (['sa', 'st', 'sd', 'di', 'production-consumption'].includes(form_type) &&
                ['cost_rate', 'last_purchase_rate', 'average_rate'].includes(formData.rate_type)) {
                var formIds = { sa: '#stock_adjustment_form', st: '#stock_transfer_form', sd: '#stock_distribution_form', di: '#stock_item_form', 'production-consumption': '#formulation_form' };
                var $adjustmentForm = $(formIds[form_type]);
                var adjustmentBranch = $adjustmentForm.find('#new_branch_id').val();
                var dateFieldName = form_type === 'production-consumption' ? 'record_date' : 'stock_date';
                var rateFieldSelector = form_type === 'production-consumption' ? '#selected_barcode_rate' : '#rate_type';
                var adjustmentDate = $adjustmentForm.find('input[name="' + dateFieldName + '"]').val();
                if (!adjustmentBranch || !adjustmentDate) {
                    toastr.error('Select a branch and date first.');
                    return;
                }
                var $adjustmentRows = $adjustmentForm.find('.erp_form__grid>.erp_form__grid_body>tr').filter(function () {
                    return !!$(this).find('input[data-id="product_barcode_id"]').val();
                });
                if (!$adjustmentRows.length) return;
                var pending = $adjustmentRows.length;
                var missingRates = 0;
                $adjustmentForm.find('.erp_form___block').addClass('pointerEventsNone');
                $adjustmentRows.each(function () {
                    var row = $(this);
                    var barcodeId = row.find('input[data-id="product_barcode_id"]').val();
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST', url: url, dataType: 'json',
                        data: {
                            form_type: form_type, rate_type: formData.rate_type, barcode_id: barcodeId,
                            branch_id: adjustmentBranch, stock_date: adjustmentDate, rate_perc: formData.rate_perc
                        },
                        success: function (response) {
                            if (row.find('input[data-id="product_barcode_id"]').val() == barcodeId &&
                                $adjustmentForm.find('#new_branch_id').val() === adjustmentBranch &&
                                $adjustmentForm.find('input[name="' + dateFieldName + '"]').val() === adjustmentDate &&
                                $adjustmentForm.find(rateFieldSelector).val() === formData.rate_type &&
                                $adjustmentForm.find('#rate_perc').val() === formData.rate_perc) {
                                update_rate(row, response.data && response.data.found ? response.data.rate : 0);
                            } else {
                                missingRates++;
                            }
                        },
                        error: function () { missingRates++; },
                        complete: function () {
                            if (--pending === 0) {
                                $adjustmentForm.find('.erp_form___block').removeClass('pointerEventsNone');
                                if (form_type === 'sa' && typeof totalStockAmount === 'function') totalStockAmount();
                                else if (typeof total_amount === 'function') total_amount();
                                if (missingRates) toastr.warning(missingRates + ' row(s) had no available rate; their rates were kept.');
                            }
                        }
                    });
                });
                return;
            }
            if (form_type === 'os') {
                var $form = $('#opening_stock_form');
                var branchId = $form.find('#new_branch_id').val();
                var stockDate = $form.find('input[name="stock_date"]').val();
                if (!branchId || !stockDate || !formData.rate_type || formData.rate_type === '0') {
                    toastr.error('Select a branch, date and Rate By option first.');
                    return;
                }
                var $rows = $form.find('.erp_form__grid>.erp_form__grid_body>tr').filter(function () {
                    return !!$(this).find('input[data-id="product_barcode_id"]').val();
                });
                if (!$rows.length) return;
                var remaining = $rows.length;
                var missing = 0;
                $form.find('.erp_form___block').addClass('pointerEventsNone');
                $rows.each(function () {
                    var tr = $(this);
                    var barcodeId = tr.find('input[data-id="product_barcode_id"]').val();
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        type: 'POST', url: url, dataType: 'json',
                        data: {
                            form_type: 'os', rate_type: formData.rate_type,
                            barcode_id: barcodeId, branch_id: branchId, stock_date: stockDate
                        },
                        success: function (response) {
                            if (tr.find('input[data-id="product_barcode_id"]').val() == barcodeId &&
                                $form.find('#new_branch_id').val() === branchId &&
                                $form.find('input[name="stock_date"]').val() === stockDate &&
                                $form.find('#selected_barcode_rate').val() === formData.rate_type) {
                                update_rate(tr, response.data && response.data.found ? response.data.rate : 0);
                            } else {
                                missing++;
                            }
                        },
                        error: function () { missing++; },
                        complete: function () {
                            remaining--;
                            if (remaining === 0) {
                                $form.find('.erp_form___block').removeClass('pointerEventsNone');
                                if (typeof totalStockAmount === 'function') totalStockAmount();
                                if (missing) toastr.warning(missing + ' row(s) had no available rate; their rates were kept.');
                            }
                        }
                    });
                });
                return;
            }
            $('.erp_form__grid>.erp_form__grid_body>tr').each(function(){
                var tr = $(this);
                formData.product_id = tr.find('input[data-id="product_id"]').val();
                formData.barcode_id = tr.find('input[data-id="product_barcode_id"]').val();
                if (['sa', 'st', 'sd'].includes(form_type)) {
                    var legacyFormIds = { sa: '#stock_adjustment_form', st: '#stock_transfer_form', sd: '#stock_distribution_form' };
                    formData.branch_id = $(legacyFormIds[form_type]).find('#new_branch_id').val();
                }
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
            if (typeof totalStockAmount === 'function' && $('#form_type').val() === 'sa') {
                totalStockAmount();
            } else if (typeof total_amount === 'function') {
                total_amount();
            }
        }
    });
});

function gridRowQty(tr) {
    var form_type = $('#form_type').val();
    if (form_type === 'sa') {
        var adj = tr.find('.tblGridAdjustment').val();
        if (!valueEmpty(adj)) {
            return adj;
        }
        var physical = tr.find('.tblGridPhysicalQty').val();
        var stock = tr.find('.tblGrid_stockQty').val();
        if (!valueEmpty(physical) && !valueEmpty(stock)) {
            return parseFloat(physical) - parseFloat(stock);
        }
        return physical || 0;
    }
    return tr.find('.tblGridCal_qty').val();
}

function update_rate(tr,rate){
    tr.find('.tblGridCal_rate').val(parseFloat(rate).toFixed(3));
    var id = "";
    rowCalc(tr,id);
    if ($('#form_type').val() === 'sa' && typeof adjustmentQty === 'function') {
        adjustmentQty(tr);
    }
}

function rowCalc(tr,id){
    var qty = gridRowQty(tr);
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
    var taxable_amount = parseFloat(amount) - parseFloat(disc_amount);
    var tax_perc = tr.find('.tblGridCal_vat_perc').val();
    var tax_amount = 0;
    if(tax_perc){
        var calc_tax_amount = taxable_amount * parseFloat(tax_perc) / 100;
        if(calc_tax_amount){
            tr.find('.tblGridCal_vat_amount').val(float3(calc_tax_amount));
            tax_amount = tr.find('.tblGridCal_vat_amount').val();
        }else{
            tr.find('.tblGridCal_vat_amount').val(float3(0));
        }
    }else{
        tax_amount = tr.find('.tblGridCal_vat_amount').val();
        if(tax_amount && taxable_amount){
            var calc_tax_perc = parseFloat(tax_amount) * 100 / taxable_amount;
            if(calc_tax_perc){
                tr.find('.tblGridCal_vat_perc').val(float3(calc_tax_perc));
            }else{
                tr.find('.tblGridCal_vat_perc').val(float3(0));
            }
        }else{
            tax_amount = 0;
            tr.find('.tblGridCal_vat_amount').val(float3(0));
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
