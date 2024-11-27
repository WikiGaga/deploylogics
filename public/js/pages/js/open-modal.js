/*
    open_modal()
    selectSupplier()
    selectProduct()
    RejectReason()
    selectCustomer()
    selectAccount()
    selectBrand()
    selectGroup()
    selectbChequeBook()
    selectBudget()
    selectSaleOrder()
 */
/*****************
    empty method */
function selectDemandApproval(){}
function selectPO(){}
function selectQuotation(){}
function selectComparativeQuotation(){}
function selectLpo(){}
/*  empty method
******************/
var thix = '';
open_modal();
$('.btn-open-modal').on('click',function(e){
    //thix = $(this);
    var data_url = $(this).parents('.open-modal-group').find('input[type="text"]').attr('data-url');
    openModal(data_url);
});
/*$(document).on('click','.btn-minus-selected-data',function (e) {
    thix = $(this);
    thix.parents('.open-modal-group').find('input').val('');
})*/

$('.btn-minus-selected-data-table').on('click',function(e){
    thix = $(this);
    thix.parents('.open-modal-group').find('input').val('');
    $('#repeated_data>tr>td:first-child').each(function(){
        var purchase_order_id = $(this).find('input[data-id="purchase_order_id"]').val();
        var lpo_id = $(this).find('input[data-id="lpo_id"]').val();
        var quotation_id = $(this).find('input[data-id="quotation_id"]').val();
        var quotation_dtl_id = $(this).find('input[data-id="quotation_dtl_id"]').val();
        if(purchase_order_id){
            $(this).parents('tr').remove();
        }
        if(lpo_id){
            $(this).parents('tr').remove();
        }
        if(quotation_id){
            $(this).parents('tr').remove();
        }
        if(quotation_dtl_id){
            $(this).parents('tr').remove();
        }
    });
    updateKeys();
});
$('.btn-minus-selected-data-sec-table').on('click',function(e){
    thix = $(this);
    thix.parents('.open-modal-group').find('input').val('');
    $('#repeated_data>tr>td:first-child').each(function(){
        var comparative_quotation_id =  $(this).find('input[data-id="comparative_quotation_id"]').val();
        if(comparative_quotation_id){
            $(this).parents('tr').remove();
        }
    });
    updateKeys();
});
$('.open_modal').on('keydown',function(e){
    if($(this).val() == "" && e.which == 13){
        var data_url = $(this).attr('data-url');
        openModal(data_url);
    }
});
function open_modal(){
    $('.open_js_modal').on('keydown',function(e){
        thix = $(this);
        if(thix.val() == "" && e.which == 13){
            thix.focus();
            var data_url = thix.attr('data-url');
            openModal(data_url);
        }
    });
}
function selectSupplier(){
    $('#help_datatable_supplierHelp').on('click', 'tbody>tr', function (e) {
        var data_name = 'supplier_name';
        var data_id = 'supplier_id';
        if (thix == ''){
            $('#supplier_name').val($(this).find('td[data-field="'+data_name+'"]').text()).attr('title',$(this).find('td[data-field="'+data_name+'"]').text());
            $('#supplier_id').val($(this).find('td[data-field="'+data_id+'"]').text());
            closeModal();
            $('#supplier_name').focus();
        }else{
            thix.parents('tr').find('.supplier_id').val($(this).find('td[data-field="'+data_id+'"]').text()).attr('title',$(this).find('td[data-field="'+data_id+'"]').text());
            thix.parents('tr').find('.supplier_name').val($(this).find('td[data-field="'+data_name+'"]').text()).attr('title',$(this).find('td[data-field="'+data_name+'"]').text());
            closeModal();
            thix.parents('tr').find('.supplier_name').focus();
        }
    });
};
function selectProduct(){
    $('#help_datatable_productHelp').on('click', 'tbody>tr', function (e) {
        var tr = thix.parents('tr');
        var help_barcode = $(this).find('td[data-field="product_barcode_barcode"]').text();
        tr.find('.pd_barcode').val($(this).find('td[data-field="product_barcode_barcode"]').text());
        $.ajax({
            type:'GET',
            url:'/demand/itembarcode/'+help_barcode,
            data:{},
            success: function(response, status){
                var c = response['data']['product_barcode_id'];
                var p = response['data']['product']['product_id'];
                if(response['data'] != null)
                {
                    if(response['data']['uom'] === null){
                        var uom_id = '';
                        var uom_name = '';
                    }else{
                        var uom_id = response['data']['uom']['uom_id'];
                        var uom_name = response['data']['uom']['uom_name'];
                    }
                    tr.find('td:eq(0)>input.product_barcode_id').val(response['data']['product_barcode_id']);
                    tr.find('td:eq(0)>input.product_id').val(response['data']['product']['product_id']);
                    tr.find('td:eq(0)>input.uom_id').val(uom_id);
                    tr.find('td>.pd_product_name').val(response['data']['product']['product_name']);
                    tr.find('td>.pd_uom').val(uom_name);
                    tr.find('td>.pd_packing').val(response['data']['product_barcode_packing']);
                    tr.find('td>.pd_store_stock').val(response['data']['store_stock']);
                    tr.find('td>.stock_match').val('');
                    tr.find('td>.suggest_qty_1').val('');
                    var options = '';
                    for(var i=0;response['uomData'].length>i;i++){
                        options += '<option value='+response['uomData'][i]['uom']['uom_id']+'>'+response['uomData'][i]['uom']['uom_name']+'</option>';
                    }
                    tr.find('.pd_uom').html(options);
                    tr.find('.pd_uom').val(uom_id);
                }
            }
        });
        closeModal();
        tr.find('.pd_uom').focus();
        //tr.find('.pd_barcode').focus();
        //tr.find('.pd_product_name').focus();
    });
};

function RejectReason(){
    $('#help_datatable_RejectReasonHelp').on('click', 'tbody>tr', function (e) {
        thix.val($(this).find('td[data-field="reason_remarks"]').text());
        thix.parents('tr').find('td>input#notes_id').val($(this).find('td[data-field="reason_id"]').text());
        thix.focus();
        closeModal();
    });
}

function selectCustomer(){
    $('#help_datatable_customerHelp').on('click', 'tbody>tr', function (e) {
        console.log(thix);
        if (thix == ''){
            $('#customer_name').val($(this).find('td[data-field="customer_name"]').text()).attr('title',$(this).find('td[data-field="customer_name"]').text())
            $('#customer_id').val($(this).find('td[data-field="customer_id"]').text());
            closeModal();
            $('#customer_name').focus();
        }else{
            thix.parents('tr').find('.customer_id').val($(this).find('td[data-field="customer_id"]').text());
            thix.parents('tr').find('.customer_name').val($(this).find('td[data-field="customer_name"]').text()).attr('title',$(this).find('td[data-field="customer_name"]').text());
            closeModal();
            thix.parents('tr').find('.customer_name').focus();
        }
    });
};

function selectSaleOrder(){
    $('#help_datatable_saleorderHelp').on('click', 'tbody>tr', function (e) {
        $('#sales_order_code').val($(this).find('td[data-field="sales_order_code"]').text()).attr('title',$(this).find('td[data-field="sales_order_code"]').text())
        $('#sales_order_booking_id').val($(this).find('td[data-field="sales_order_id"]').text());
        closeModal();
        $('#sales_order_code').focus();
    });
};

if (document.getElementById("btn-minus-selected-data") != null){
    document.getElementById("btn-minus-selected-data").onclick = function(e){
        var parent = e.target.parentNode.parentNode.parentNode.childNodes;
        var length = parent.length;
        for(var i=0;length>i;i++ ){
            if(parent[i].nodeName == 'INPUT') {
                parent[i].value = '';
                parent[i].title = '';
            }
        }
    }
}

function selectAccount(){
    $('#help_datatable_accountsHelp').on('click', 'tbody>tr', function (e) {
        var data_name = 'chart_name';
        var data_code = 'chart_code';
        var data_id = 'chart_account_id';
        if (thix == ''){
            $('#account_name').val($(this).find('td[data-field="'+data_name+'"]').text()).attr('title',$(this).find('td[data-field="'+data_name+'"]').text());
            $('#account_id').val($(this).find('td[data-field="'+data_id+'"]').text());
            closeModal();
            $('#account_name').focus();
        }else{
            thix.parents('tr').find('.acc_code').val($(this).find('td[data-field="'+data_code+'"]').text()).attr('title',$(this).find('td[data-field="'+data_code+'"]').text());
            thix.parents('tr').find('.acc_name').val($(this).find('td[data-field="'+data_name+'"]').text()).attr('title',$(this).find('td[data-field="'+data_name+'"]').text());
            thix.parents('tr').find('.acc_id').val($(this).find('td[data-field="'+data_id+'"]').text());
            closeModal();
            if(thix.parents('tr').find('.acc_code').val() == undefined){
                thix.parents('tr').find('.acc_name').focus();
            }else{
                thix.parents('tr').find('.acc_code').focus();
            }
            
        }
    });
};
function selectBrand(){
    $('#help_datatable_brandHelp').on('click', 'tbody>tr', function (e) {
        thix.val($(this).find('td[data-field="brand_name"]').text());
        thix.parents('tr').find('td>input#brand_id').val($(this).find('td[data-field="brand_id"]').text());
        thix.focus();
        closeModal();
    });
}
function selectGroup(){
    $('#help_datatable_groupHelp').on('click', 'tbody>tr', function (e) {
        thix.val($(this).find('td[data-field="group_item_name_string"]').text());
        thix.parents('tr').find('td>input#group_id').val($(this).find('td[data-field="group_item_id"]').text());
        thix.focus();
        closeModal();
    });
}

function selectbChequeBook(){
    $('#help_datatable_chequebookHelp').on('click', 'tbody>tr', function (e) {
        thix.val($(this).find('td[data-field="cheque_book_name"]').text());
        thix.focus();
        closeModal();
    });
}
function selectBudget(){
    $('#help_datatable_budgetHelp').on('click', 'tbody>tr', function (e) {
        thix.val($(this).find('td[data-field="budget_budgetart_position"]').text());
        thix.parents('tr').find('.budget_id').val($(this).find('td[data-field="budget_id"]').text());
        thix.parents('tr').find('.budget_branch_id').val($(this).find('td[data-field="branch_id"]').text());
        thix.focus();
        closeModal();
    });
}
function selectProductFormulation(){
    $('#help_datatable_productFormulationHelp').on('click', 'tbody>tr', function (e) {
        $('#f_barcode').val($(this).find('td[data-field="product_barcode_barcode"]').text());
        $('#f_product_id').val($(this).find('td[data-field="product_id"]').text());
        $('#f_product_barcode_id').val($(this).find('td[data-field="product_barcode_id"]').text());
        $('#f_product_name').val($(this).find('td[data-field="product_name"]').text());
        $('#f_barcode').focus();
        closeModal();
    });
}
