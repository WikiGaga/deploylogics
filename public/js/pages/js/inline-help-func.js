$(document).on('click','.data_tbody_row',function(e){
    var thix = $(this);
    var caseType = thix.parents('#inLineHelp').find('.data_thead_row').attr('id');
    var tr = $('.open_inline__help__focus').parents('tr');
    var selected_row = thix;
    var addRow = 0;
    var checkNewEntry = false;
    if(caseType == 'accountsHelp'){
        get_acc_detail(tr,selected_row,addRow,checkNewEntry);
    }
    if(caseType == 'budgetHelp'){
        get_budget_detail(tr,selected_row,addRow,checkNewEntry);
    }
    if(caseType == 'invoiceHelp'){
        get_invoice_detail(tr,selected_row,addRow,checkNewEntry);
    }
    if (caseType == 'oExpVoucherHelp'
    || caseType == 'oJVVoucherHelp'
    || caseType == 'oLVVoucherHelp'
    ) {
        get_last_voucher_detail(thix);
    }
});
$(document).on('keydown','#account_code',function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var tr = thix.parents('tr');
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    var addRow = 0;
    var checkNewEntry = false;

    if(code != ""){
        if(selected_row.length == 1){
            if(e.which === 13){
                addRow = 1;
                get_acc_detail(tr,selected_row,addRow,checkNewEntry);
            }
            if(e.which === 9){
                addRow = 2;
                get_acc_detail(tr,selected_row,addRow,checkNewEntry);
            }
        }else{
            if(e.which === 13){
                get_acc_detail_by_code(tr,code);
            }
        }
    }
    tr.find('.voucher_descrip').focus();
});
function get_acc_detail(tr,selected_row,addRow,checkNewEntry){
    var chart_id = selected_row.find('tr.d-none>td[data-field="chart_account_id"]').text();
    var chart_code = selected_row.find('tr.data-dtl>td[data-field="chart_code"]').text();
    var chart_name = selected_row.find('tr.data-dtl>td[data-field="chart_name"]').text();
    tr.find('.account_id').val(chart_id);
    tr.find('.acc_code').val(chart_code);
    tr.find('.acc_name').val(chart_name);
    $('#inLineHelp').remove();
    $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');
    var index = tr.find('.acc_code').index(this) + 2;
    // $('.tb_moveIndex').eq(index).focus();
    tr.find('.voucher_descrip').focus();
}
$(document).on('keydown','.budget_dscrp',function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var tr = thix.parents('tr');
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    var addRow = 0;
    var checkNewEntry = false;
    if(code != ""){
        if(e.which === 13){
            addRow = 1;
            get_budget_detail(tr,selected_row,addRow,checkNewEntry);
        }
        if(e.which === 9){
            addRow = 2;
            get_budget_detail(tr,selected_row,addRow,checkNewEntry);
        }
    }
//    tr.find('.voucher_chqno').focus();
});
function get_budget_detail(tr,selected_row,addRow,checkNewEntry){
    var budget_id = selected_row.find('tr.d-none>td[data-field="budget_id"]').text();
    var branch_id = selected_row.find('tr.d-none>td[data-field="branch_id"]').text();
    var description = selected_row.find('tr.data-dtl>td[data-field="budget_budgetart_position"]').text();
    tr.find('.budget_id').val(budget_id);
    tr.find('.budget_branch_id').val(branch_id);
    tr.find('.budget_dscrp').val(description);
    $('#inLineHelp').remove();
    $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');
}

$(document).on('keydown','.invoice_code',function (e) {
    var thix = $(this);
    var code = thix.val().trim();
    var tr = thix.parents('tr');
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    var addRow = 0;
    var checkNewEntry = false;
    if(code != ""){
        if(e.which === 13){
            addRow = 1;
            get_invoice_detail(tr,selected_row,addRow,checkNewEntry);
        }
        if(e.which === 9){
            addRow = 2;
            get_invoice_detail(tr,selected_row,addRow,checkNewEntry);
        }
    }
//    tr.find('.voucher_chqno').focus();
});
function get_invoice_detail(tr,selected_row,addRow,checkNewEntry){
    var invoice_id = selected_row.find('tr.d-none>td[data-field="grn_id"]').text();
    var invoice_code = selected_row.find('tr.data-dtl>td[data-field="grn_code"]').text();
    tr.find('.invoice_id').val(invoice_id);
    tr.find('.invoice_code').val(invoice_code);
    $('#inLineHelp').remove();
    $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');
}

function get_acc_detail_by_code(tr,code){
    if(code.length === 12){
        $.ajax({
            type:'GET',
            url:'/common/format/'+code,
            data:{},
            success: function(response, status){
                if(status)
                {
                    tr.find('.account_id').val(response.chart_account_id);
                    tr.find('.acc_code').val(response.chart_code);
                    tr.find('.acc_name').val(response.chart_name);
                    var index = tr.find('.acc_code').index(this) + 2;
                    $('.tb_moveIndex').eq(index).focus();
                }
            }
        });
    }
}


$('#last_voucher_no').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_last_voucher_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_last_voucher_detail(selected_row);
        }
    }
});
function get_last_voucher_detail(selected_row) {
    var voucher_no = selected_row.find('tr.data-dtl>td[data-field="voucher_no"]').text();
    selected_row.parents('.erp_form___block').find('#last_voucher_no').val(voucher_no.trim());
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}
