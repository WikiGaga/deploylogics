var alertShowed = false;

$(document).on("mouseover", '.data_tbody_row', function(e) {
    var barcode = $(this).find('table>tbody>tr.data-dtl>td[data-view="show"]').text();
    var caseType = $(this).parents('#inLineHelp').find('.data_thead_row').attr('id');
    var tr = $('.open_inline__help__focus').parents('tr');
    if (caseType == 'brandHelp') {
        tr.find('.brand').val(barcode);
    } else if (caseType == 'groupHelp') {
        tr.find('.group').val(barcode);
    } else {
        //tr.find('.pd_barcode').val(barcode);
    }
    //tr.find('.pd_barcode').val(barcode);
});
$(document).on('click', '.data_tbody_row', function() {
    var thix = $(this);
    var caseType = thix.parents('#inLineHelp').find('.data_thead_row').attr('id');
    var form_type = $('#form_type').val();
    var keycodeNo = 999; // mouse click
    var thix_val = thix.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text();
    if (caseType == 'productHelpSI' || caseType == 'productHelp') {
        var tr = $('.open_inline__help__focus').parents('tr');
        var formData = {
            form_type: form_type,
            val: thix_val,
        }
        var po_id = $('#purchase_order_id').val();
        if (form_type == 'grn') {
            formData.po_id = po_id;
        }
        var supplier_id = $('#supplier_id').val();
        if (form_type == 'purc_order') {
            formData.supplier_id = supplier_id;
        }
        var customer_id = $('#logged_customer option:selected').val();
        if (form_type == 'cso') {
            formData.customer_id = customer_id;
        }
        if ($('#sales_contract_id').val() != '' && form_type == 'sale_invoice') {
            formData.sales_contract_id = $('#sales_contract_id').val();
            formData.rate_type = $('#rate_type option:selected').val();
        }
        initBarcode(keycodeNo, tr, form_type, formData);
    }
    if (caseType == 'stockRequestHelp') {
        get_stock_request_detail(thix);
    }
    if (caseType == 'stockTransferHelp') {
        get_stock_transfer_detail(thix);
    }
    if (caseType == 'supplierHelp') {
        get_supplier_detail(thix);
    }
    if (caseType == 'poHelp') {
        get_purchase_order_detail(thix);
    }
    if (caseType == 'prHelp') {
        get_purchase_return_detail(thix);
    }
    if (caseType == 'salesContractHelp') {
        get_sales_contract_code_detail(thix);
    }
    if (caseType == 'saleorderHelp') {
        get_sales_order_code_detail(thix);
    }
    if (caseType == 'customerHelp') {
        get_customer_name_detail(thix);
    }
    if (caseType == 'InternalStockTransferHelp') {
        get_ist_code_detail(thix);
    }
    if (caseType == 'accountsHelp') {
        get_account_code_detail(thix);
    }
    if (caseType == 'upAccountsHelp') {
        func_up_get_account_code_detail(thix);
    }
    if (caseType == 'cAccountsHelp') {
        get_c_account_code_detail(thix);
    }
    if (caseType == 'accSupplierBankHelp') {
        get_supplier_bank_detail(thix);
    }
    if (caseType == 'salesInvoiceHelp') {
        get_sales_invoice_detail(thix);
    }
    if (caseType == 'brandHelp') {
        get_brand_detail(thix);
    }
    if (caseType == 'groupHelp') {
        get_group_detail(thix);
    }
    if (caseType == 'stockPurchasingHelp') {
        get_stock_purchasing_detail(thix);
    }
    if (caseType == 'employeeHelp') {
        get_employee_detail(thix);
    }
    if (caseType == 'loanConfiHelp') {
        get_loan_confi_detail(thix);
    }
    if(caseType == 'productFormulationHelp'){
        get_product_formulation_detail(thix);
    }
    if(form_type == 'food_recipes'){
        get_food_detail(thix);
    }
    if(caseType == 'formulationEntryHelp'){
        get_formula_entry(thix);
    }
    if(caseType == 'salesQuotationHelp'){
        get_sales_quotation_detail(thix);
    }
    if(caseType == 'salesRequestQuotationHelp'){
        get_sales_request_quotation_detail(thix);
    }
    if(caseType == 'servicesOrderHelp'){
        get_services_order_detail(thix);
    }
    if(caseType == 'lpoPoHelp'){
        get_lpo_po_detail(thix);
    }
    if(caseType == 'lpoPoQuotationHelp'){
        get_lpo_po_detail(thix);
    }
    if(caseType == 'autoDemandHelp'){
        get_auto_demand_detail(thix);
    }
    if(caseType == 'formulationEntryHelp'){
        get_item_formulation_detail(thix);
    }
    if(caseType == 'grnHelp'){
        get_grn_detail(thix);
    }
    if(caseType == 'stockReceivingHelp'){
        get_stock_receiving_detail(thix);
    }
    if(caseType == 'productTPHelp'){
        get_tp_barcode_detail(thix);
    }
    if(caseType == 'productMergedFromHelp'){
        get_merged_from_barcode_detail(thix);
    }
    if(caseType == 'productMergedToHelp'){
        get_merged_to_barcode_detail(thix);
    }
    $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');
});

function clearHeaderProductData(tr) {
    tr.find('th:first-child input').val('');
    tr.find('input#product_name').val('');
    tr.find('input#product_arabic_name').val('');
    tr.find('input#pd_packing').val('');
    tr.find('input#quantity').val('');
    tr.find('input#rate').val(notNullEmpty(0, 3));
    tr.find('input#amount').val(notNullEmpty(0, 3));
    tr.find('input#gross_amount').val(notNullEmpty(0, 3));
    tr.find('input#foc_qty').val("");
    tr.find('input#fc_rate').val("");
    tr.find('input#dis_perc').val("");
    tr.find('input#dis_amount').val("");
    tr.find('input#vat_perc').val("");
    tr.find('input#vat_amount').val("");
    tr.find('select#pd_uom').html('<option>Select</option>');
    tr.find('input#returnable_quantity').val("");
}
$('#pd_barcode').bind('paste', null, function() {
    var tr = $(this).parents('tr');
    clearHeaderProductData(tr);
});
$(document).on('keydown', '.pd_barcode', function(e) {
    var form_type = $('#form_type').val();
    var thix = $(this);
    var thix_val = thix.val().trim();
    var tr = thix.parents('tr');
    var keyboard_code = [9, 13, 16, 17, 18, 20, 37, 38, 39, 40];
    var keycodeNo = e.which;
    var current_keyCode = e.keyCode;
    // var table_block = thix.parents('.erp_form___block');
    // var inLineHelp = table_block.find('.inLineHelp');
    //var thix_val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();

    if (thix_val != '' && keyboard_code.includes(current_keyCode) && keyboard_code.includes(keycodeNo)) {
        clearHeaderProductData(tr);
    }
    if ((keycodeNo === 13 || keycodeNo === 9) && thix_val != "" && tr.find('.product_id').val() == '' && tr.find('.product_barcode_id').val() == '') {
        e.preventDefault()
        var supplier_id = $('#supplier_id').val();
        var formData = {};
        formData = {
            form_type: form_type,
            val: thix_val,
        }
        if (supplier_id !== "" && supplier_id !== undefined) {
            formData.sup_id = supplier_id;
        }
        var po_id = $('#purchase_order_id').val();
        if (form_type == 'grn') {
            formData.po_id = po_id;
        }
        if (form_type == 'purc_order' || form_type == 'purc_demand') {
            formData.supplier_id = supplier_id;
        }
        var customer_id = $('#logged_customer option:selected').val();
        if (form_type == 'cso') {
            formData.customer_id = customer_id;
        }
        if ($('#sales_contract_id').val() != '' && form_type == 'sale_invoice') {
            formData.sales_contract_id = $('#sales_contract_id').val();
            formData.rate_type = $('#rate_type option:selected').val();
        }
        if(form_type == "purc_return"){
            if(supplier_id == ""){
                toastr.error('Please Select Supplier First');
                $('#supplier_name').focus();
                return false;
            }
        }
        initBarcode(keycodeNo, tr, form_type, formData);
    }
});
$(document).on('change', '.pd_uom', function(e) {
    var form_type = $('#form_type').val();
    var thix = $(this);
    var thix_val = thix.val().trim();
    var tr = thix.parents('tr');
    var product_id = tr.find('input#product_id').val();

    // IF Form Type Puchase Order
    if(form_type == "purc_order"){
        if(product_id == "" || product_id == undefined || product_id == null){
            product_id = tr.find('[data-id="product_id"]').val();
        }
    }

    tr.find('input#product_id').val(); // ?
    var keycodeNo = 999; // mouse click
    //clearHeaderProductData(tr);
    if (form_type == 'purc_demand') {

    }
    var formData = {
        form_type: form_type,
        val: thix_val,
        product_id: product_id,
    };
    if (form_type == 'grn') {
        formData.po_id = $('#grn_form').find('#purchase_order_id').val();
    }
    /*if (form_type == 'sa') { // stock Adjustment
        formData.store_id = $('#stock_adjustment_form').find('#store_id').val();
    }*/
    //initBarcode(keycodeNo,tr,form_type,formData);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/barcode/get-barcode-detail-by-uom',
        dataType: 'json',
        data: formData,
        beforeSend : function(){
            $('body').addClass('pointerEventsNone');
        },
        success: function(response) {
            $('body').removeClass('pointerEventsNone');
            if (response['current_product'] != null) {
                var barcode = response['current_product'];
                tr.find('input.pd_barcode').val(barcode['product_barcode_barcode']);
                if (response['barcode_type'] == 'common') {
                    barcodeCommonData(tr, response, formData)
                }
                if (response['barcode_type'] == 'sa') {
                    get_stock_adjustment_detail(tr, response, formData)
                    return true;
                }
                // grn =  Goods Received Notes
                if (response['barcode_type'] == 'grn') {
                    if (response['current_product'] !== "") {
                        get_po_product_detail(tr, response, formData);
                    }
                }
                if (response['central_rate_type'] == 'grn_central_rate')
                {
                    if (response['current_product'] !== "") {
                        swal.fire({
                            title: $('#pd_barcode').val() + "<br> Central Product is blocked",
                            text: '',
                            type: 'warning',
                            showCancelButton: true,
                            showConfirmButton: true
                        }).then(function(result) {
                            //barcodeCommonData(tr, response, formData);
                        });
                    }
                }
                if (response['barcode_type'] == 'grn_verify') {
                    swal.fire({
                        title: $('#pd_barcode').val() + "<br> Barcode is not perishable <br>and not exit in selected PO",
                        text: 'Are you sure add this?',
                        type: 'warning',
                        showCancelButton: true,
                        showConfirmButton: true
                    }).then(function(result) {
                        if (result.value) {
                            barcodeCommonData(tr, response, formData);
                        }
                    });
                } else {
                    barcodeCommonData(tr, response, formData);
                }
                fcRate(tr);
                amountCalc(tr);
                discount(tr);
                vat(tr);
                grossAmount(tr);
                totalAllGrossAmount();
            }
        },
        error: function(){
            $('body').removeClass('pointerEventsNone');
        }
    });
});

function initBarcode(keycodeNo, tr, form_type, formData) {
    /*var formData = {
        form_type : form_type,
        val : thix_val,
    };*/
    if (form_type == 'grn') {
        formData.id = $('#grn_form').find('#purchase_order_id').val();
    }
    if (form_type == 'st') { // stock transfer
        formData.id = $('#stock_transfer_form').find('#stock_from_id').val();
    }
    if (form_type == 'str') { // stock receiving
        formData.id = $('#stock_transfer_receiving').find('#stock_from_id').val();
    }
    if (form_type == 'multi_barcode_labels') { // stock receiving
        formData.branch_id = $('#dynamic_barcode_tag_form').find('#branch_id').val();
    }
    /*if (form_type == 'sa') { // stock Adjustment
        formData.store_id = $('#stock_adjustment_form').find('#store_id').val();
    }*/
    if (form_type == 'purc_demand' || form_type == 'purc_return') { // Purchase Demand & Return (Supplier)
        if($('#supplier_id').val() != ""){
            formData.sup_id = $('#supplier_id').val();
        }
    }
    if(form_type == "purc_return"){
        if($('#supplier_id').val() == ""){
            toastr.error('Please Select Supplier First');
            $('#supplier_name').focus();
            return false;
        }
    }
    get_barcode_detail(keycodeNo, tr, form_type, formData);
}

function get_barcode_detail(keycodeNo, tr, form_type, formData) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/barcode/get-barcode-detail',
        dataType: 'json',
        data: formData,
        success: function(response) {
            if (response['current_product'] != null) {
                if (response['barcode_type'] == 'common') {
                    barcodeCommonData(tr, response, formData);
                    // get_po_product_detail(tr, response, formData);
                }
                // st = stock transfer
                if (response['barcode_type'] == 'stock_transfer') {
                    stockTransferBarcodeData(tr, response, formData);
                }
                // str =  stock receiving
                if (response['barcode_type'] == 'stock_receiving') {
                    stockReceivingBarcodeData(tr, response, formData);
                }
                // grn =  Goods Received Notes
                if (response['barcode_type'] == 'grn') {
                    if (response['current_product'] !== "") {
                        get_po_product_detail(tr, response, formData);
                    }
                }
                if (response['central_rate_type'] == 'grn_central_rate')
                {
                    if (response['current_product'] !== "") {
                        swal.fire({
                            title: $('#pd_barcode').val() + "<br> Central Product is blocked.",
                            text: '',
                            type: 'warning',
                            showCancelButton: true,
                            showConfirmButton: false
                        }).then(function(result) {
                            if (result.value) {
                                //barcodeCommonData(tr, response, formData);
                            }
                        });
                    }
                }
                if (response['barcode_type'] == 'grn_verify') {
                    if (response['current_product'] !== "") {
                        swal.fire({
                            title: $('#pd_barcode').val() + "<br> Barcode is not perishable <br>and not exit in selected PO",
                            text: 'Are you sure add this?',
                            type: 'warning',
                            showCancelButton: true,
                            showConfirmButton: true
                        }).then(function(result) {
                            if (result.value) {
                                barcodeCommonData(tr, response, formData);
                            }
                        });
                    }
                }
                // sales_contract =  Sales Contract
                if (response['barcode_type'] == 'sales_contract') {
                    if (response['current_product'] !== "") {
                        get_sales_contract_detail(tr, response, formData);
                    }
                }
                // sale_invoice =  Sales Invoice
                if (response['barcode_type'] == 'sale_invoice' || response['barcode_type'] == 'sales_quotation' || response['barcode_type'] == 'sale_return' || response['barcode_type'] == 'sale_fee') {
                    if (response['current_product'] !== "") {
                        var sale_invoice_detail = get_sale_invoice_detail(tr, response, formData);
                    }
                }
                // stock_adjustment =  Stock Adjustment
                if (response['barcode_type'] == 'sa') {
                    if (response['current_product'] !== "") {
                        var stock_adjustment_detail = get_stock_adjustment_detail(tr, response, formData);
                        return true;
                    }
                }
                // if (response['barcode_type'] == 'common') {
                //     if (response['current_product'] !== "") {
                //         var DynamicBarcodeLabels = setDynamicBarcodeLabels(tr, response, formData);
                //         return true;
                //     }
                // }
                /**************************************************/
                /* common data */
                if (tr.find('.product_name') == "") {
                    alert('please enter product name');
                    return false
                }

                if(form_type == "sales_scheme"){
                    // tr.find('.sldtl_disc_per').focus();
                }else if(form_type == 'request_quotation' || form_type == 'request_order' || form_type == 'request_invoice'){
                    tr.find('.pd_length').focus();
                }else{
                    tr.find('.tblGridCal_qty').focus();
                }



                if (typeof formData.selection !== 'undefined' && formData.selection == 'multi' && keycodeNo === 13) {
                    data_po_selected = "multi";
                    $('#addData').click();
                }
                if (typeof formData.reorder_action !== 'undefined' && formData.reorder_action && keycodeNo === 13) {
                    $('#addData').click();
                    if(formData.last_barcode){
                        table_pit_list.removeClass('pointerEventsNone');
                    }
                }
                /*var form_type = ['grn','os','purc_demand','purc_return','purc_order','lpo','ist','st','cso']
                if(keycodeNo === 13 && form_type.includes(formData.form_type)){
                    // not add product by default until click on Plus Btn
                    $('#quantity').select();
                }else if(keycodeNo === 13 && formData.form_type == 'sales_contract'){
                    // not add product by default until click on Plus Btn
                    tr.find('.tblGridCal_rate').select();
                }else if(keycodeNo === 13 && sale_invoice_detail == 1){
                    // not add product by default until click on Plus Btn
                    tr.find('.pd_barcode').focus();
                }else if((keycodeNo === 13 && (formData.form_type == 'sale_invoice' || formData.form_type == 'sale_return'))){
                    // not add product by default until click on Plus Btn
                    tr.find('.pd_barcode').focus();
                }else{
                    if(keycodeNo === 13){
                        $('#addData').click();
                    }
                    if(keycodeNo === 9 || keycodeNo === 13 || keycodeNo === 999){
                        tr.find('.pd_barcode').focus();
                    }
                }*/

                // TODO::Show Toast of Stock
                $('.toast-modal').remove();
                $('body').append(response['toast_stock_detail']);
                $('.toast-modal').modal('show',{keyboard : false}).draggable({
                    handle: ".modal-body,.modal-header"
                });


            } else {
                tr.find('.pd_barcode').focus();
                if (formData.form_type == 'grn') {
                    swal.fire({
                        title: formData.val,
                        text: response['msg'],
                        type: 'warning',
                        showConfirmButton: false
                    });
                } else {
                    swal.fire({
                        title: formData.val,
                        text: "Product not found",
                        type: 'warning',
                        showConfirmButton: false
                    });
                }
            }
            $('#inLineHelp').remove();
            $('.erp_form__grid').find('input').removeClass('open_inline__help__focus');

            /* if(response['barcode_type'] == 'sale_invoice' || response['barcode_type'] == 'sale_return' || response['barcode_type'] == 'sale_fee'){
                 tr.find('#pd_barcode').focus();
             }*/
        }
    });
}

function barcodeCommonData(tr, response, formData) {
    var barcode = response['current_product'];
    // var barcode_rate = response['barcode_rate'];
    var product2 = response['current_product2'];
    var product = response['current_product']['product'];
    var barcode_dtl = response['current_product']['barcode_dtl'];
    var uomDtl = response['current_product']['uom'];
    var group_item = response['group_item'];
    if (group_item != null) {
        var parent_group_item_name = group_item['parent_group_item_name'];
        var group_item_name = group_item['group_item_name'];
    } else {
        var parent_group_item_name = '';
        var group_item_name = '';
    }
    var uom_list = response['uom_list'];
    var current_sale_rate = response['rate'];
    var tbl_purc_rate = response['purc_rate'];
    var tbl_grn_purc_rate = response['grn_purc_rate'];
    var current_user_branch_id = response['current_user_branch_id'];
    var central_rate = response['central_rate'];
    if (tbl_purc_rate == null) {
        tbl_purc_rate = [];
        tbl_purc_rate['product_barcode_purchase_rate'] = 0.000;
        tbl_purc_rate['product_barcode_cost_rate'] = 0.000;
        tbl_purc_rate['product_barcode_avg_rate'] = 0.000;
        tbl_purc_rate['last_cost_rate'] = 0.000;
        tbl_purc_rate['supplier_cost_rate'] = 0.000;
    }

    var default_qty = 1;
    var rate = 0.000;
    var gridSale_rate = 0.000;
    var code = barcode['product_barcode_barcode'];
    var form_type = formData.form_type;
    var vegetable = vegetableProduct(formData.val, barcode['product_barcode_weight_apply']);
    if (uomDtl != null) {
        var selected_uom_id = uomDtl['uom_id'];
        var uom_name = uomDtl['uom_name'];
    } else {
        var selected_uom_id = '';
        var uom_name = '';
    }
    if (barcode['product_barcode_weight_apply'] == 1) {
        default_qty = vegetable.default_qty;
    } else {
        default_qty = default_qty;
    }

    if (response['pr_grn_rate']) {
        default_qty = response['pr_grn_rate'];
    }

    if (typeof formData.selection !== 'undefined' && formData.selection == 'multi' && (form_type === 'purc_order' || form_type === 'grn')) {
        default_qty = formData.demand_qty;
    }
    // var purchase_order = ['purc_order'];
    // if (purchase_order.includes(form_type)) {
    //     last_tp = tbl_purc_rate['last_cost_rate'];
    // }
    tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
    tr.find('input.product_id').val(product['product_id']);
    tr.find('input.uom_id').val(selected_uom_id);
    tr.find('.pd_barcode').val(barcode['product_barcode_barcode']);
    tr.find('.product_name').val(product['product_name']);
    tr.find('.product_arabic_name').val(product['product_arabic_name']);
    tr.find('.pd_uom').val(uom_name);
    tr.find('.pd_packing').val(barcode['product_barcode_packing']);
    tr.find('input.tblGridCal_sale_rate').val(tbl_purc_rate['sale_rate']);
    tr.find('input.tblGridCal_cost_rate').val(tbl_purc_rate['product_barcode_cost_rate']);
    tr.find('input.central_rate').val(central_rate);

    if(form_type == 'st' && response['batch_expiry_date'] !== undefined){
        var d = new Date(response['batch_expiry_date']);
        var returnDate = "";
        if(d){
            var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
            var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
            var year = d.getFullYear();
            var returnDate =  day + month + year;
        }
        tr.find('#expiry_date').val(returnDate);
    }
    if(form_type == "purc_return"){
        tr.find('.rtrnpending_quantity').val(response['purc_return_pending_qty']);
        tr.find('.returnable_quantity').val(response['purc_return_returnable_qty']);
        // tr.find('.tblGridCal_qty').val(response['purc_return_colleted_qty']);
    }
    if (form_type == "multi_barcode_labels") {
        tr.find('input.last_level_category').val(product['group_item_parent_id']);
        tr.find('input.last_level_category_id').val(product['group_item_id']);
        tr.find('.first_level_category').val(parent_group_item_name);
        tr.find('.last_level_category').val(group_item_name);
        tr.find('input.tblGridCal_sales_rate').val(tbl_purc_rate['sale_rate']);

        if(product2['weight_id'] != "" && product2['weight_id'] != "0")
        {
            var product_barcode_packing = !valueEmpty(product2['product_barcode_packing'])?product2['product_barcode_packing']:1;

            var amount = (parseFloat(tbl_purc_rate['sale_rate']) * parseFloat(product_barcode_packing));

            tr.find('input.tblGridCal_weight').val(product_barcode_packing);
            tr.find('input.tblGridCal_amount').val(amount);
        }else{
            var product_barcode_packing = 1;
            var amount = (parseFloat(tbl_purc_rate['sale_rate']) * parseFloat(product_barcode_packing));
            tr.find('input.tblGridCal_weight').val(product_barcode_packing);
            tr.find('input.tblGridCal_amount').val(amount);
        }
    }
    // if (form_type == "deal-setup") {
    //     alert('anbdu');
    //     tr.find('input.tblGridCal_sales_rate').val(purc_rate['sale_rate']);
    //     tr.find('input.tblGridCal_cost_rate').val(purc_rate['product_barcode_cost_rate']);
    // }
    if (form_type == 'barcode_labels') {
        tr.find('.arabic_name').val(product['product_arabic_name']);
    }
    // Product Rate
    var for_sale_rate_cate = ['barcode_labels', 'barcode_price_tag', 'ist', 'st', 'sup_prod_reg' , 'brochure','sales_quotation','request_quotation','request_order'];
    if (for_sale_rate_cate.includes(form_type) && current_sale_rate != null) {
        rate = current_sale_rate['product_barcode_sale_rate_rate'];
    }
    if (form_type == 'cso' && current_sale_rate != null) { // customer sales contract
        rate = current_sale_rate['sales_contract_dtl_rate'];
    }
    if (form_type == 'grn' && current_sale_rate != null) { // grn sale rate
        gridSale_rate = notNullEmpty(current_sale_rate['product_barcode_sale_rate_rate'], 3);
    }
    tr.find('.tblGridSale_rate').val(gridSale_rate);
    if (form_type == 'change_rate') {
        var current_branch_all_sale_rate = response['sale_rate'];
        var len = current_branch_all_sale_rate.length;
        for (var i = 0; i < len; i++) {
            if (current_branch_all_sale_rate[i]['product_category_id'] == 1 && ($('#change_rate_category').val() == 'all' || $('#change_rate_category').val() == 1)) {
                rate = current_branch_all_sale_rate[i]['product_barcode_sale_rate_rate'];
            }
            if ($('#change_rate_category').val() == 2 && current_branch_all_sale_rate[i]['product_category_id'] == 2) {
                rate = current_branch_all_sale_rate[i]['product_barcode_sale_rate_rate'];
            }
            if ($('#change_rate_category').val() == 3 && current_branch_all_sale_rate[i]['product_category_id'] == 3) {
                rate = current_branch_all_sale_rate[i]['product_barcode_sale_rate_rate'];
            }
        }

        //cost rate & margin
        tr.find('.cost_rate').val(response['cost_rate']['product_barcode_cost_rate']);
        tr.find('.min_margin').val(barcode['product_barcode_minimum_profit']);
    }
    var purc_grn_rate = ['purc_order'];
    if (purc_grn_rate.includes(form_type)) {
        rate = current_sale_rate['tbl_purc_grn_dtl_rate'];
        if (current_sale_rate.length == 0) {
            rate = tbl_purc_rate['product_barcode_purchase_rate'];
        }
    }
    var for_only_purc_rate = ['purc_return', 'purchasing'];
    var product_type = ['grn_perishable'];
    if (product_type.includes(response['product_type']) || for_only_purc_rate.includes(form_type)) {
        rate = tbl_purc_rate['product_barcode_purchase_rate'];
    }

    if (['di', 'sale_invoice','pos_sale_invoice', 'sales_quotation', 'sale_return', 'consumer_protection', 'sales_fee'].includes(formData.form_type)) {
        var calc_rate = rateFunc(current_user_branch_id, barcode, current_sale_rate, tbl_purc_rate);
        tr.find('.g_rate').val(calc_rate.origRate);
        rate = calc_rate.rate;
    }

    tr.find('.tblGridCal_rate').val(notNullEmpty(rate, 3));

    var amount = (parseFloat(default_qty) * parseFloat(rate));

    var for_purc_rate = ['st', 'ist', 'str', 'sup_prod_reg'];
    if (for_purc_rate.includes(formData.form_type)) {
        if (formData.form_type == 'ist' || formData.form_type == 'sup_prod_reg') {
            var purc_rate = tbl_purc_rate['product_barcode_purchase_rate'];
        } else {
            var calc_rate = rateFunc(current_user_branch_id, barcode, current_sale_rate, tbl_purc_rate);
            var purc_rate = calc_rate.origRate;
        }
        tr.find('.tblGridCal_purc_rate').val(notNullEmpty(purc_rate, 3));
        var amount = (parseFloat(default_qty)*parseFloat(purc_rate));
    }
    // end Product Rate
    tr.find('.tblGridCal_amount').val(notNullEmpty(amount, 3));

    // vat perc and vat amount apply
    var vatApply = ['purc_order', 'grn', 'purc_return', 'sale_invoice', 'sales_quotation', 'sale_return', 'st', 'str', 'consumer_protection', 'sup_prod_reg', 'sales_fee','barcode_price_tag','barcode_labels'];
    if (vatApply.includes(form_type)) {
        var vatPerc = 0;
        for (var i = 0; i < barcode_dtl.length; i++) {
            if (current_user_branch_id == barcode_dtl[i]['branch_id'] && barcode_dtl[i]['product_barcode_tax_apply'] == 1) {
                vatPerc = barcode_dtl[i]['product_barcode_tax_value'];
            }
        }
        if (vatPerc == null || vatPerc == NaN || vatPerc == undefined) {
            var vatPerc = 0;
        }

        tr.find('.tblGridCal_vat_perc').val(notNullEmpty(vatPerc, 3));

        var vatAmount = ((parseFloat(amount) / 100) * parseFloat(vatPerc));

        tr.find('.tblGridCal_vat_amount').val(notNullEmpty(vatAmount, 3));

        amount = (parseFloat(amount) + parseFloat(vatAmount));

    }

    tr.find('.tblGridCal_gross_amount').val(js__number_format(amount));

    uomList(tr, uom_list, selected_uom_id);

    if (form_type == 'purc_demand') {
        tr.find('.pd_store_stock').val(response['store_stock']);
        tr.find('.suggest_qty_1').val(response['suggestQty1']);
        tr.find('.suggest_qty_2').val(response['suggestQty2']);
        tr.find('.wiplpo_stock').val(response['lpo_quantity']);
        tr.find('.pur_ret').val(response['purc_return_waiting_qty']);
    }
    if (form_type == 'os') { // os = Opening Stock
        var selected_barcode_rate = $('#opening_stock_form').find('#selected_barcode_rate').val();
        var rate_list = ['0', 'cost_rate', 'last_stock_rate', 'last_purchase_rate', 'average_rate'];
        if (rate_list.includes(selected_barcode_rate)) {
            // if barcode found in dropdown
            var rateFound = {
                rate: tbl_purc_rate['product_barcode_purchase_rate'],
                selected_barcode_rate: selected_barcode_rate,
                barcode_id: barcode['product_barcode_id'],
                barcode_barcode: barcode['product_barcode_barcode'],
                store_id: $('#opening_stock_form').find('#store_id').val(),
                tr: tr
            }
            $('#opening_stock_form').find('.erp_form___block').addClass('pointerEventsNone');
            barcode_already_exits_in_grid(rateFound);
        }
    }
    if (typeof response['sup_product_dtl'] !== undefined && response['sup_product_dtl'] !== undefined && response['sup_product_dtl'] !== null) {
        tr.find('.sup_barcode').val(response['sup_product_dtl']['sup_prod_sup_barcode']);
    }
    // Supplier Product Registration
    if (form_type == 'sup_prod_reg') {
        if (response['prod_type'] != null) {
            tr.find('.pd_category').val(response['prod_type']['product_type_name']);
        }
        if (response['prod_brand'] != null) {
            tr.find('.pd_brand').val(response['prod_brand']['brand_name']);
        }
        tr.find('.pd_hs_code').val(product['product_hs_code']);
    }

    if(form_type == 'sales_scheme'){
        var defaultDiscount = $('#scheme_default_discount').val();
        tr.find('.grid_discount_perc').val(defaultDiscount);
        tr.find('.grid_discount_amount').val(0);
        tr.find('.grid_foc_qty').val(0);
        tr.find('.addData').click();
    }
    if(form_type == 'purc_order'){
        if ((typeof formData.reorder_action !== 'undefined' && formData.reorder_action)
        && (typeof formData.qty !== 'undefined' && formData.qty)) {
            var qty = formData.qty;
        }else{
            var qty = 1;
        }
        var cost_rate = !valueEmpty(tbl_purc_rate['product_barcode_cost_rate'])?tbl_purc_rate['product_barcode_cost_rate']:0;
        var sale_rate = !valueEmpty(tbl_purc_rate['sale_rate'])?tbl_purc_rate['sale_rate']:0;
        var mrp = !valueEmpty(tbl_purc_rate['mrp'])?tbl_purc_rate['mrp']:0;
        var gst_perc = !valueEmpty(tbl_purc_rate['tax_rate'])?tbl_purc_rate['tax_rate']:0;
        var store_stock = !valueEmpty(response['store_stock'])?response['store_stock']:0;
        var gp_perc = !valueEmpty(tbl_purc_rate['gp_perc'])?tbl_purc_rate['gp_perc']:0;
        var gp_amount = !valueEmpty(tbl_purc_rate['gp_amount'])?tbl_purc_rate['gp_amount']:0;
        var last_tp = !valueEmpty(tbl_purc_rate['last_tp'])?tbl_purc_rate['last_tp']:0;
        var vend_last_tp = !valueEmpty(tbl_purc_rate['supplier_last_tp'])?tbl_purc_rate['supplier_last_tp']:0;
        var last_gst_perc = !valueEmpty(tbl_purc_rate['last_gst_perc'])?tbl_purc_rate['last_gst_perc']:0;
        var last_disc_perc = !valueEmpty(tbl_purc_rate['last_disc_perc'])?tbl_purc_rate['last_disc_perc']:0;
        var pd_tax_on = !valueEmpty(tbl_purc_rate['pd_tax_on'])?tbl_purc_rate['pd_tax_on']:'da';
        var pd_disc = !valueEmpty(tbl_purc_rate['pd_disc'])?tbl_purc_rate['pd_disc']:'ga';

        tr.find('.tblGridCal_qty').val(qty);
        tr.find('.tblGridCal_rate').val(cost_rate);
        tr.find('.tblGridCal_sale_rate').val(sale_rate);
        tr.find('.tblGridCal_mrp').val(mrp);
        tr.find('.tblGridCal_sys_qty').val(store_stock);
        tr.find('.tblGridCal_discount_perc').val(last_disc_perc);
        tr.find('.tblGridCal_gst_perc').val(last_gst_perc);
        tr.find('.tblGridCal_last_tp').val(last_tp);
        tr.find('.tblGridCal_vend_last_tp').val(vend_last_tp);
        tr.find('.tblGridCal_gp_perc').val(gp_perc);
        tr.find('.tblGridCal_gp_amount').val(gp_amount);
        tr.find('.pd_tax_on').val(pd_tax_on);
        tr.find('.pd_disc').val(pd_disc);
        $('#current_product_stock').val(store_stock);
        funcHeaderCalc(tr); // function making on po form
    }
    if(form_type == 'grn'){
        var qty = 1;
        var cost_rate = !valueEmpty(tbl_purc_rate['product_barcode_cost_rate'])?tbl_purc_rate['product_barcode_cost_rate']:0;
        var sale_rate = !valueEmpty(tbl_purc_rate['sale_rate'])?tbl_purc_rate['sale_rate']:0;
        var gst_perc = !valueEmpty(tbl_purc_rate['tax_rate'])?tbl_purc_rate['tax_rate']:0;
        var mrp = !valueEmpty(tbl_purc_rate['mrp'])?tbl_purc_rate['mrp']:0;
        var store_stock = !valueEmpty(response['store_stock'])?response['store_stock']:0;
        var gp_perc = !valueEmpty(tbl_purc_rate['gp_perc'])?tbl_purc_rate['gp_perc']:0;
        var gp_amount = !valueEmpty(tbl_purc_rate['gp_amount'])?tbl_purc_rate['gp_amount']:0;
        var last_tp = !valueEmpty(tbl_purc_rate['last_tp'])?tbl_purc_rate['last_tp']:0;
        var vend_last_tp = !valueEmpty(tbl_purc_rate['supplier_last_tp'])?tbl_purc_rate['supplier_last_tp']:0;
        var last_gst_perc = !valueEmpty(tbl_purc_rate['last_gst_perc'])?tbl_purc_rate['last_gst_perc']:0;
        var pd_tax_on = !valueEmpty(tbl_purc_rate['pd_tax_on'])?tbl_purc_rate['pd_tax_on']:'da';
        var pd_disc = !valueEmpty(tbl_purc_rate['pd_disc'])?tbl_purc_rate['pd_disc']:'ga';

        tr.find('.tblGridCal_qty').val(qty);
        tr.find('.tblGridCal_rate').val(cost_rate);
        tr.find('.tblGridCal_sale_rate').val(sale_rate);
        tr.find('.tblGridCal_sys_qty').val(store_stock);
        tr.find('.tblGridCal_gst_perc').val(last_gst_perc);
        tr.find('.tblGridCal_mrp').val(mrp);
        tr.find('.tblGridCal_last_tp').val(last_tp);
        tr.find('.tblGridCal_vend_last_tp').val(vend_last_tp);
        tr.find('.tblGridCal_gp_perc').val(gp_perc);
        tr.find('.tblGridCal_gp_amount').val(gp_amount);
        tr.find('.pd_tax_on').val(pd_tax_on);
        tr.find('.pd_disc').val(pd_disc);
        $('#current_product_stock').val(store_stock);
        funcHeaderCalc(tr); // function making on po form
    }
    // if(form_type == "purc_demand" && response['supplier_has_returnable'] && !alertShowed){
    //     swal.fire({
    //         title: "This Supplier Have Some Products In Purchase Return",
    //         text: 'Are you sure add this?',
    //         type: 'warning',
    //         showConfirmButton: true
    //     });
    //     alertShowed = true;
    // }
    if (form_type == 'product_discount_setup') { // product_discount_setup
        setProductDiscountSetup(tr, response, formData)
    }
    if (form_type == 'sale_report') {
        setSaleReport(tr, response, formData)
    }
    if (form_type == 'purc_return') {
        setPurchaseReturn(tr, response, formData)
    }
    if (form_type == 'change_rate') {
        setProductChangeRate(tr, response, formData)
    }
    if(form_type == 'st'){
        var sale_rate = !valueEmpty(tbl_purc_rate['sale_rate'])?tbl_purc_rate['sale_rate']:0;
        tr.find('.tblGridCal_rate').val(sale_rate);
        var mrp = !valueEmpty(tbl_purc_rate['mrp'])?tbl_purc_rate['mrp']:0;
        tr.find('.mrp').val(mrp);
        var calc_rate = rateFunc(current_user_branch_id, barcode, current_sale_rate, tbl_purc_rate);
        tr.find('.tblGridCal_ex_net_tp').val(notNullEmpty(calc_rate.rate, 3));
        tr.find('.tblGridCal_purc_rate').val(notNullEmpty(calc_rate.rate, 3));
        var store_stock = !valueEmpty(response['store_stock'])?response['store_stock']:0;
        $('#current_product_stock').val(store_stock);
        $('#sys_qty').val(store_stock);


        var grn_qty = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_quantity'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_quantity']:0;
        var unit_price = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_rate'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_rate']:0;
        var discount_perc = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_disc_percent'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_disc_percent']:0;
        var discount_amount = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_disc_amount'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_disc_amount']:0;
        var after_discount_amount = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_after_dis_amount'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_after_dis_amount']:0;
        var gst_perc = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_vat_percent'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_vat_percent']:0;
        var gst_amount = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_vat_amount'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_vat_amount']:0;
        var fed_perc = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_fed_percent'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_fed_percent']:0;
        var fed_amount = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_fed_amount'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_fed_amount']:0;
        var spec_disc_perc = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_spec_disc_perc'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_spec_disc_perc']:0;
        var spec_disc_amount = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_spec_disc_amount'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_spec_disc_amount']:0;
        var gross_amount = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_gross_amount'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_gross_amount']:0;
        var net_amount = !valueEmpty(tbl_grn_purc_rate['tbl_purc_grn_dtl_total_amount'])?tbl_grn_purc_rate['tbl_purc_grn_dtl_total_amount']:0;


        tr.find('.tblGridCal_unit_price').val(unit_price);
        tr.find('.tblGridCal_discount_perc').val(discount_perc);
        tr.find('.tblGridCal_discount_amount').val(discount_amount);
        tr.find('.tblGridCal_after_discount_amount').val(after_discount_amount);
        tr.find('.tblGridCal_grn_qty').val(grn_qty);
        tr.find('.gst_perc').val(gst_perc);
        tr.find('.gst_amount').val(gst_amount);
        tr.find('.tblGridCal_fed_perc').val(fed_perc);
        tr.find('.tblGridCal_fed_amount').val(fed_amount);
        tr.find('.tblGridCal_spec_disc_perc').val(spec_disc_perc);
        tr.find('.tblGridCal_spec_disc_amount').val(spec_disc_amount);
        tr.find('.tblGridCal_gross_amount').val(gross_amount);
        tr.find('.tblGridCal_net_amount').val(net_amount);
    }
    if(form_type == 'os'){
        var sale_rate = !valueEmpty(tbl_purc_rate['product_barcode_cost_rate'])?tbl_purc_rate['product_barcode_cost_rate']:0;
        tr.find('.tblGridCal_rate').val(sale_rate);
    }
}
function setPurchaseReturn(tr, response, formData){
    var qty = 1;
    var cost_rate = 0;
    var sale_rate = 0;
    var mrp = 0;
    var store_stock = 0;

    var disc_perc = 0;
    var gst_perc = 0;
    var fed_perc = 0;
    var spec_disc_perc = 0;

    var last_tp = 0;
    var vend_last_tp = 0;
    var gp_perc = 0;
    var gp_amount = 0;
    var fc_rate = 0;
    var pd_tax_on = 0;
    var pd_disc = 0;
    var tbl_purc_rate= response['purc_rate'];
    if(!valueEmpty(tbl_purc_rate)){
        var cost_rate = !valueEmpty(tbl_purc_rate['product_barcode_cost_rate'])?tbl_purc_rate['product_barcode_cost_rate']:0;
        var sale_rate = !valueEmpty(tbl_purc_rate['sale_rate'])?tbl_purc_rate['sale_rate']:0;
        var mrp = !valueEmpty(tbl_purc_rate['mrp'])?tbl_purc_rate['mrp']:0;
        var store_stock = !valueEmpty(response['store_stock'])?response['store_stock']:0;

        var gst_perc = !valueEmpty(tbl_purc_rate['last_gst_perc'])?tbl_purc_rate['last_gst_perc']:0;

        var last_tp = !valueEmpty(tbl_purc_rate['last_tp'])?tbl_purc_rate['last_tp']:0;
        var vend_last_tp = !valueEmpty(tbl_purc_rate['supplier_last_tp'])?tbl_purc_rate['supplier_last_tp']:0;
        var gp_perc = !valueEmpty(tbl_purc_rate['gp_perc'])?tbl_purc_rate['gp_perc']:0;
        var gp_amount = !valueEmpty(tbl_purc_rate['gp_amount'])?tbl_purc_rate['gp_amount']:0;
    }
    var grn_retrn = response['grn_retrn'];
    if(!valueEmpty(grn_retrn)){
        var cost_rate = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_rate'])?grn_retrn['tbl_purc_grn_dtl_rate']:0;
        var sale_rate = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_sale_rate'])?grn_retrn['tbl_purc_grn_dtl_sale_rate']:0;
        var mrp = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_mrp'])?grn_retrn['tbl_purc_grn_dtl_mrp']:0;
        var store_stock = !valueEmpty(response['store_stock'])?response['store_stock']:0;

        var disc_perc = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_disc_percent'])?grn_retrn['tbl_purc_grn_dtl_disc_percent']:0;
        var gst_perc = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_vat_percent'])?grn_retrn['tbl_purc_grn_dtl_vat_percent']:0;
        var fed_perc = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_fed_percent'])?grn_retrn['tbl_purc_grn_dtl_fed_percent']:0;
        var spec_disc_perc = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_spec_disc_perc'])?grn_retrn['tbl_purc_grn_dtl_spec_disc_perc']:0;

        var last_tp = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_last_tp'])?grn_retrn['tbl_purc_grn_dtl_last_tp']:0;
        var vend_last_tp = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_vend_last_tp'])?grn_retrn['tbl_purc_grn_dtl_vend_last_tp']:0;
        var gp_perc = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_gp_perc'])?grn_retrn['tbl_purc_grn_dtl_gp_perc']:0;
        var gp_amount = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_gp_amount'])?grn_retrn['tbl_purc_grn_dtl_gp_amount']:0;
        var fc_rate = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_fc_rate'])?grn_retrn['tbl_purc_grn_dtl_fc_rate']:0;
        var pd_tax_on = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_tax_on'])?grn_retrn['tbl_purc_grn_dtl_tax_on']:0;
        var pd_disc = !valueEmpty(grn_retrn['tbl_purc_grn_dtl_disc_on'])?grn_retrn['tbl_purc_grn_dtl_disc_on']:0;
    }

    tr.find('.tblGridCal_qty').val(funcNumberFloat(qty,0));
    tr.find('.tblGridCal_rate').val(funcNumberFloat(cost_rate));
    tr.find('.tblGridCal_sale_rate').val(funcNumberFloat(sale_rate));
    tr.find('.tblGridCal_mrp').val(funcNumberFloat(mrp));
    tr.find('.tblGridCal_sys_qty').val(store_stock);
    tr.find('.tblGridCal_discount_perc').val(funcNumberFloat(disc_perc));
    if(!valueEmpty(pd_tax_on)){
        tr.find('.pd_tax_on').val(pd_tax_on).change()
    }
    tr.find('.tblGridCal_gst_perc').val(funcNumberFloat(gst_perc));
    tr.find('.tblGridCal_fed_perc').val(funcNumberFloat(fed_perc));
    if(!valueEmpty(pd_disc)){
        tr.find('.pd_disc').val(pd_disc).change()
    }
    tr.find('.tblGridCal_spec_disc_perc').val(funcNumberFloat(spec_disc_perc));

    tr.find('.tblGridCal_last_tp').val(funcNumberFloat(last_tp));
    tr.find('.tblGridCal_vend_last_tp').val(funcNumberFloat(vend_last_tp));
    tr.find('.tblGridCal_gp_perc').val(funcNumberFloat(gp_perc));
    tr.find('.tblGridCal_gp_amount').val(funcNumberFloat(gp_amount));
    tr.find('.tblGridCal_fc_rate').val(funcNumberFloat(fc_rate));
    $('#current_product_stock').val(store_stock);
    funcHeaderCalc(tr); // function making on po form
}
// function setDynamicBarcodeLabels(tr, response, formData){
//     var barcode = response['barcode_rate'];
//     console.log(barcode);
//     tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
//     tr.find('input.product_id').val(barcode['product_id']);
//     tr.find('input.first_level_category_id').val(barcode['group_item_parent_id']);
//     tr.find('input.last_level_category_id').val(barcode['group_item_id']);
//     tr.find('.pd_barcode').val(barcode['product_barcode_barcode']);
//     tr.find('.product_name').val(barcode['product_name']);
//     tr.find('.first_level_category').val(barcode['group_item_parent_name']);
//     tr.find('.last_level_category').val(barcode['group_item_name']);
//     tr.find('.tblGridCal_weight').val(1);
//     // tr.find('.tblGridCal_rate').val(barcode['sale_rate']);
//     tr.find('input.tblGridCal_rate').val(barcode['sale_rate']);
//     tr.find('.tblGridCal_qty').val(0);
//     tr.find('.tblGridCal_amount').val(0);
//     $('#inLineHelp').remove();
// }

function setProductDiscountSetup(tr, response, formData){
    var actionBol = true;
    if(!valueEmpty(response['purc_rate'])){
        actionBol = false;
        var current_tp = !valueEmpty(response['purc_rate']['product_barcode_cost_rate'])?response['purc_rate']['product_barcode_cost_rate']:0;
        var mrp = !valueEmpty(response['purc_rate']['mrp'])?response['purc_rate']['mrp']:0;
        var sale_rate = !valueEmpty(response['purc_rate']['sale_rate'])?response['purc_rate']['sale_rate']:0;
        tr.find('.current_tp').val(parseFloat(current_tp).toFixed(3));
        tr.find('.mrp').val(funcNumberFloat(mrp));
        tr.find('.sale_rate').val(parseFloat(sale_rate).toFixed(3));

        var gp_perc = 0;
        var gp_rate = 0;
        var disc_perc = 0;
        var disc_amt = 0;
        var after_disc_gp_amt = 0;
        var after_disc_gp_perc = 0;

        if(!valueEmpty(response['grn_dtl'])){
            var gp_perc = response['grn_dtl']['tbl_purc_grn_dtl_gp_perc'];
            var gp_rate = response['grn_dtl']['tbl_purc_grn_dtl_gp_amount'];
            var disc_perc = response['grn_dtl']['tbl_purc_grn_dtl_disc_percent'];
            var disc_amt = response['grn_dtl']['tbl_purc_grn_dtl_disc_amount'];

            var after_disc_gp_amt = parseFloat(gp_rate) - parseFloat(disc_amt);
            var after_disc_gp_perc = parseFloat(gp_rate) / parseFloat(current_tp) * 100;
        }

        tr.find('.gp_perc').val(funcNumberFloat(gp_perc));
        tr.find('.gp_rate').val(funcNumberFloat(gp_rate));

        tr.find('.disc_price').val(funcNumberFloat(disc_perc));
        tr.find('.disc_amt').val(funcNumberFloat(disc_amt));

        tr.find('.after_disc_gp_perc').val(funcNumberFloat(after_disc_gp_perc));
        tr.find('.after_disc_gp_amt').val(funcNumberFloat(after_disc_gp_amt));
        actionBol = true;
    }

    if(actionBol){
        if(!valueEmpty(formData.autoClick) && formData.autoClick){
            $('#addData').click();
            var calc = parseInt(remain_req)-1;
            remain_req = calc;
           // cd("remain_req2: " + calc);
            var msg = "Remaining request is: "+ calc;
            toastr.success(msg);
        }
    }
}
function setSaleReport(tr, response, formData){
    var actionBol = true;

    if(actionBol){
        if(!valueEmpty(formData.autoClick) && formData.autoClick){
            $('#addData').click();
            var calc = parseInt(remain_req)-1;
            remain_req = calc;
           // cd("remain_req2: " + calc);
            var msg = "Remaining request is: "+ calc;
            toastr.success(msg);
        }
    }
}

function setProductChangeRate(tr, response, formData)
{
    var actionBol = true;
    if(!valueEmpty(response['purc_rate']))
    {
        actionBol = false;
        var emptyArr = ["",undefined,'NaN',NaN,null,"0",0];
        //var current_tp = response['purc_rate']['product_barcode_cost_rate'];
        var current_tp = response['purc_rate']['net_tp'];
        //var last_tp = (emptyArr.includes(response['purc_rate']['last_cost_rate']))?0:response['purc_rate']['last_cost_rate'];
        var last_tp = (emptyArr.includes(response['purc_rate']['last_tp']))?0:response['purc_rate']['last_tp'];

        var mrp = response['purc_rate']['mrp'];
        var sale_rate = response['purc_rate']['sale_rate'];
        var gp_amount = response['purc_rate']['gp_amount'];
        var gp_perc = response['purc_rate']['gp_perc'];
        var whole_sale_rate = (emptyArr.includes(response['purc_rate']['whole_sale_rate']))?0:response['purc_rate']['whole_sale_rate'];
        tr.find('.current_tp').val(funcNumberFloat(current_tp));
        tr.find('.last_tp').val(funcNumberFloat(current_tp));
        tr.find('.sale_rate').val(funcNumberFloat(sale_rate));
        tr.find('.gp_rate').val(funcNumberFloat(gp_amount));
        tr.find('.gp_perc').val(funcNumberFloat(gp_perc));
        var gp_amount = 0;
        if(!valueEmpty(parseFloat(gp_perc)) && !valueEmpty(parseFloat(current_tp))){
            gp_amount = (parseFloat(gp_perc) / 100) * parseFloat(current_tp);
        }
        tr.find('.gp_amount').val(funcNumberFloat(gp_amount));
        tr.find('.mrp').val(funcNumberFloat(mrp));
        tr.find('.whole_sale_rate').val(funcNumberFloat(whole_sale_rate));

        tr.find('.new_tp').val(funcNumberFloat(current_tp));
        tr.find('.new_sale_rate').val(funcNumberFloat(sale_rate));
        tr.find('.new_mrp').val(funcNumberFloat(mrp));
        tr.find('.new_whole_sale_rate').val(funcNumberFloat(whole_sale_rate));
        if(!valueEmpty(sale_rate) && !valueEmpty(current_tp)){
            var gp_amount = parseFloat(sale_rate) - parseFloat(current_tp);
            tr.find('.new_gp_amount').val(funcNumberFloat(gp_amount));
            var gp_perc = (parseFloat(gp_amount) / parseFloat(current_tp)) * 100;
            tr.find('.new_gp_perc').val(funcNumberFloat(gp_perc));
        }else{
            tr.find('.new_gp_amount').val(0);
            tr.find('.new_gp_perc').val(0);
        }
        actionBol = true;
    }
    if(actionBol){
        if(!valueEmpty(formData.autoClick) && formData.autoClick){
            $('#addData').click();
            var calc = parseInt(remain_req)-1;
            remain_req = calc;
           // cd("remain_req2: " + calc);
            var msg = "Remaining request is: "+ calc;
            toastr.success(msg);
        }
    }
}

function stockTransferBarcodeData(tr, response, formData) {
    var barcode = response['current_product'];
    var uom_list = response['uom_list'];
    var selected_uom_id = barcode['uom_id'];
    var uom_name = barcode['uom_name'];
    var code = response['code'];
    default_qty = (barcode['demand_dtl_demand_quantity'] != '' || barcode['demand_dtl_demand_quantity'] != null) ? barcode['demand_dtl_demand_quantity'] : 1;
    var rate = response['rate']['product_barcode_sale_rate_rate'];
    var purc_rate = response['purc_rate']['product_barcode_purchase_rate'];
    var vat = response['vat'];
    var vat_purc = 0;
    if (vat != null || vat != "") {
        if (vat['product_barcode_tax_apply'] == 1) {
            vat_purc = vat['product_barcode_tax_value'];
        }
    }
    var store_stock = !valueEmpty(response['store_stock'])?response['store_stock']:0;
    var amount = parseFloat(purc_rate) * parseFloat(default_qty);
    var vat_amt = (parseFloat(amount) / 100) * parseFloat(vat_purc);
    var gross_amount = parseFloat(amount) + parseFloat(vat_amt);

    tr.find('.pd_barcode').val(code);
    tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
    tr.find('input.product_id').val(barcode['product_id']);
    tr.find('input.uom_id').val(selected_uom_id);
    tr.find('.product_name').val(barcode['product_name']);
    tr.find('.pd_uom').val(uom_name);
    tr.find('.pd_packing').val(barcode['product_barcode_packing']);
    tr.find('.tblGridCal_sys_qty').val(store_stock);
    tr.find('.demand_qty').val(default_qty);
    tr.find('.tblGridCal_qty').val(default_qty);
    tr.find('.tblGridCal_rate').val(notNullEmpty(rate, 3));
    tr.find('.tblGridCal_purc_rate').val(notNullEmpty(purc_rate, 3));
    tr.find('.tblGridCal_amount').val(notNullEmpty(amount, 3));
    tr.find('.tblGridCal_vat_perc').val(notNullEmpty(vat_purc, 3));
    tr.find('.tblGridCal_vat_amount').val(notNullEmpty(vat_amt, 3));
    tr.find('.tblGridCal_gross_amount').val(notNullEmpty(gross_amount, 3));
    uomList(tr, uom_list, selected_uom_id);
}

function stockReceivingBarcodeData(tr, response, formData) {
    var barcode = response['current_product'];
    var uom_list = response['uom_list'];
    var default_qty = 1;
    var rate = 0.000;
    var selected_uom_id = barcode['uom_id'];
    var uom_name = barcode['uom_name'];
    var code = response['code'];
    tr.find('.pd_barcode').val(code);
    tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
    tr.find('input.product_id').val(barcode['product_id']);
    tr.find('input.uom_id').val(selected_uom_id);
    tr.find('.product_name').val(barcode['product_name']);
    tr.find('.pd_uom').val(uom_name);
    tr.find('.pd_packing').val(barcode['stock_dtl_packing']);
    var demand_qty = (barcode['stock_dtl_demand_quantity'] != '' || barcode['stock_dtl_demand_quantity'] != null) ? barcode['stock_dtl_demand_quantity'] : '';
    var transfer_qty = (barcode['stock_dtl_quantity'] != '' || barcode['stock_dtl_quantity'] != null) ? barcode['stock_dtl_quantity'] : '';
    default_qty = (transfer_qty != '' || transfer_qty != null) ? transfer_qty : 1;
    tr.find('.demand_qty').val(demand_qty);
    tr.find('.stock_transfer_qty').val(transfer_qty);
    tr.find('.tblGridCal_qty').val(default_qty);
    rate = barcode['stock_dtl_rate'];
    tr.find('.tblGridCal_rate').val(notNullEmpty(rate, 3));
    var amount = (parseFloat(default_qty) * parseFloat(rate));
    tr.find('.tblGridCal_amount').val(notNullEmpty(amount, 3));
    tr.find('.tblGridCal_gross_amount').val(notNullEmpty(amount, 3));
    uomList(tr, uom_list, selected_uom_id);
}

function get_sales_contract_detail(tr, response, formData) {
    var barcode = response['current_product'];
    var uom_list = response['uom_list'];
    var current_sale_rate = response['rate'];
    var selected_uom_id = barcode['uom_id'];
    var tbl_purc_rate = response['purc_rate'];
    var current_user_branch_id = response['current_user_branch_id'];
    if (tbl_purc_rate == null) {
        tbl_purc_rate = [];
        tbl_purc_rate['product_barcode_purchase_rate'] = 0;
        tbl_purc_rate['product_barcode_cost_rate'] = 0;
        tbl_purc_rate['product_barcode_avg_rate'] = 0;
    }
    var uom_name = barcode['uom_name'];
    var code = response['code'];
    tr.find('.pd_barcode').val(code);
    tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
    tr.find('input.product_id').val(barcode['product_id']);
    tr.find('input.uom_id').val(selected_uom_id);
    tr.find('.product_name').val(barcode['product']['product_name']);
    tr.find('.pd_uom').val(uom_name);
    tr.find('.pd_packing').val(barcode['product_barcode_packing']);
    var calc_rate = rateFunc(current_user_branch_id, barcode, current_sale_rate, tbl_purc_rate);
    tr.find('.tblGridCal_rate').val(notNullEmpty(calc_rate.rate, 3));
    uomList(tr, uom_list, selected_uom_id);
}

function get_sale_invoice_detail(tr, response, formData) {
    tr.find('.product_name').val(response['current_product']['product']['product_name']);
    var code = formData.val;
    var vegetable = vegetableProduct(code, response['current_product']['product_barcode_weight_apply']);
    var returnBack = 0;
    $('.erp_form__grid_body>tr').each(function() {
        var val = $(this).find('.product_barcode_id').val();
        if (val == response['current_product']['product_barcode_id']) {
            var qty = $(this).find('.tblGridCal_qty').val();
            var rate = notNullEmpty($(this).find('.tblGridCal_rate').val(), 3);
            var vatAmount = notNullEmpty($(this).find('.tblGridCal_vat_amount').val(), 3);
            if (vegetable.vegetableProduct) {
                qty = parseFloat(qty) + parseFloat(vegetable.default_qty);
                qty = notNullEmpty(qty, 3);
            } else {
                qty = parseInt(qty) + parseInt(vegetable.default_qty);
            }
            var amt = notNullEmpty((parseFloat(qty) * parseFloat(rate)), 3);
            $(this).find('.tblGridCal_qty').val(qty);
            $(this).find('.tblGridCal_amount').val(amt);
            amt = parseFloat(amt) + parseFloat(vatAmount);
            $(this).find('.tblGridCal_gross_amount').val(notNullEmpty(amt, 3));
            formClear();
            allCalcFunc();
            $(document).find('#inLineHelp').remove();
            returnBack = 1;
            return false;
        }
    });
    if (returnBack == 0) {
        barcodeCommonData(tr, response, formData)
    } else {
        return returnBack;
    }
}

function get_stock_adjustment_detail(tr, response, formData){
    if(response['product_exists'] == true){
        swal.fire({
            title: response['current_product']['product_barcode_barcode'],
            text: response['product_exists_msg'],
            type: 'error',
            showConfirmButton:true,
            showCancelButton: true,
            confirmButtonText: 'Ok',
            cancelButtonText: 'Cancel',
            focusConfirm:true
        }).then(function(result){
            if(result.value){
                get_stock_adjustment_detail_detail(tr, response, formData)
            }
        })
    }else{
        get_stock_adjustment_detail_detail(tr, response, formData)
    }
}
function get_stock_adjustment_detail_detail(tr, response, formData){
    var barcode = response['current_product'];
    var product = response['current_product']['product'];
    var barcode_dtl = response['current_product']['barcode_dtl'];
    var uomDtl = response['current_product']['uom'];
    var uom_list = response['uom_list'];
    var current_sale_rate = response['rate'];
    var tbl_purc_rate = response['purc_rate'];
    var current_user_branch_id = response['current_user_branch_id'];
    var form_type = formData.form_type;
    if (uomDtl != null) {
        var selected_uom_id = uomDtl['uom_id'];
        var uom_name = uomDtl['uom_name'];
    } else {
        var selected_uom_id = '';
        var uom_name = '';
    }
    tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
    tr.find('input.product_id').val(product['product_id']);
    tr.find('input.uom_id').val(selected_uom_id);
    tr.find('.pd_barcode').val(barcode['product_barcode_barcode']);
    tr.find('.product_name').val(product['product_name']);
    tr.find('.pd_uom').val(uom_name);
    tr.find('.pd_packing').val(barcode['product_barcode_packing']);
    uomList(tr, uom_list, selected_uom_id);
    var pd_store_stock = response['store_stock'];
    tr.find('.pd_store_stock').val(pd_store_stock);
    var rate = 0;
    var qty = 0;
    var amount = rate * qty;
    tr.find('.tblGridCal_rate').val(notNullEmpty(rate,3));
    tr.find('.tblGridCal_amount').val(notNullEmpty(amount,3));

    // Temp Code
    if(form_type == 'sa'){
        tr.find('.tblGridPhysicalQty').val(1).focus();
        tr.find('.expiry-date').val('01-01-2025');
    }

}

function rateFunc(current_user_branch_id, barcode, current_sale_rate, tbl_purc_rate) {
    var data = {};
    var rate_type = $('form').find('#rate_type option:selected').val();
    var percentage = $('form').find('#rate_perc').val();
    if (valueEmpty(percentage)) {
        percentage = 0;
    }
    var nowRate = 0;
    if (rate_type == 'item_cost_rate') {
        nowRate = tbl_purc_rate['product_barcode_cost_rate'];
        data.origRate = tbl_purc_rate['product_barcode_cost_rate'];
    }
    if (rate_type == 'item_sale_rate') {
        nowRate = tbl_purc_rate['sale_rate'];
        data.origRate = tbl_purc_rate['sale_rate'];
    }
    if (rate_type == 'item_average_rate') {
        nowRate = tbl_purc_rate['product_barcode_avg_rate'];
        data.origRate = tbl_purc_rate['product_barcode_avg_rate'];
    }
    if (rate_type == 'item_purchase_rate') {
        nowRate = tbl_purc_rate['product_barcode_purchase_rate'];
        data.origRate = tbl_purc_rate['product_barcode_purchase_rate'];
    }
    if (rate_type == 'item_contract_rate') {
        nowRate = barcode['sales_contract_dtl_rate'];
        data.origRate = barcode['sales_contract_dtl_rate'];
    }
    if (rate_type == 'item_last_net_tp') {
        nowRate = tbl_purc_rate['net_tp'];
        data.origRate = tbl_purc_rate['net_tp'];
    }

    if (rate_type == 'item_retail_rate') {
        var item_retail_rate = 0;
        for (var i = 0; barcode['sale_rate'].length > i; i++) {
            if (barcode['sale_rate'][i]['product_category_id'] == 1 &&
                barcode['sale_rate'][i]['branch_id'] == current_user_branch_id) {
                item_retail_rate = barcode['sale_rate'][i]['product_barcode_sale_rate_rate'];
            }
        }
        nowRate = item_retail_rate;
        data.origRate = item_retail_rate;
    }

    if (rate_type == 'item_whole_sale_rate') {
        var item_whole_sale_rate = 0;
        for (var i = 0; barcode['sale_rate'].length > i; i++) {
            if (barcode['sale_rate'][i]['product_category_id'] == 3 &&
                barcode['sale_rate'][i]['branch_id'] == current_user_branch_id) {
                item_whole_sale_rate = barcode['sale_rate'][i]['product_barcode_sale_rate_rate'];
            }
        }
        nowRate = item_whole_sale_rate;
        data.origRate = item_whole_sale_rate;
    }
    if (nowRate == '' || nowRate == undefined || nowRate == null || nowRate == NaN) {
        nowRate = 0;
        data.origRate = 0;
    }
    var plusPerc = (parseFloat(nowRate) / 100) * parseFloat(percentage)
    data.rate = parseFloat(notNullEmpty(nowRate, 3)) + parseFloat(notNullEmpty(plusPerc, 3));
    if (data.rate == NaN || data.rate == undefined || data.rate == null) {
        data.rate = 0;
        data.origRate = 0;
    }
    return data;
}

function get_po_product_detail(tr, response, formData) {
    var barcode = response['current_product'];
    var tbl_purc_rate = response['purc_rate'];
    var code = response['code'];
    var uom_list = response['uom_list'];
    var selected_uom_id = barcode['uom']['uom_id'];
    var uom_name = barcode['uom']['uom_name'];
    tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
    tr.find('input.product_id').val(barcode['product_id']);
    tr.find('input.uom_id').val(selected_uom_id);
    tr.find('.product_name').val(barcode['product']['product_name']);
    tr.find('.pd_barcode').val(code);
    tr.find('.pd_uom').val(uom_name);
    if(valueEmpty(tbl_purc_rate)){
        tbl_purc_rate = [];
    }
    var qty = 1;
    var cost_rate = !valueEmpty(tbl_purc_rate['product_barcode_cost_rate'])?tbl_purc_rate['product_barcode_cost_rate']:0;
    var sale_rate = !valueEmpty(tbl_purc_rate['sale_rate'])?tbl_purc_rate['sale_rate']:0;
    var gst_perc = !valueEmpty(tbl_purc_rate['tax_rate'])?tbl_purc_rate['tax_rate']:0;
    var store_stock = !valueEmpty(response['store_stock'])?response['store_stock']:0;
    var gp_perc = !valueEmpty(tbl_purc_rate['gp_perc'])?tbl_purc_rate['gp_perc']:0;
    var gp_amount = !valueEmpty(tbl_purc_rate['gp_amount'])?tbl_purc_rate['gp_amount']:0;
    var last_tp = !valueEmpty(tbl_purc_rate['last_tp'])?tbl_purc_rate['last_tp']:0;
    var vend_last_tp = !valueEmpty(tbl_purc_rate['supplier_last_tp'])?tbl_purc_rate['supplier_last_tp']:0;
    var last_gst_perc = !valueEmpty(tbl_purc_rate['last_gst_perc'])?tbl_purc_rate['last_gst_perc']:0;
    var last_disc_perc = !valueEmpty(tbl_purc_rate['last_disc_perc'])?tbl_purc_rate['last_disc_perc']:0;

    tr.find('.tblGridCal_qty').val(qty);
    tr.find('.tblGridCal_rate').val(cost_rate);
    tr.find('.tblGridCal_sale_rate').val(sale_rate);
    tr.find('.tblGridCal_sys_qty').val(store_stock);
    tr.find('.tblGridCal_discount_perc').val(last_disc_perc);
    tr.find('.tblGridCal_gst_perc').val(last_gst_perc);
    tr.find('.tblGridCal_last_tp').val(last_tp);
    tr.find('.tblGridCal_vend_last_tp').val(vend_last_tp);
    tr.find('.tblGridCal_gp_perc').val(gp_perc);
    tr.find('.tblGridCal_gp_amount').val(gp_amount);
    $('#current_product_stock').val(store_stock);
    uomList(tr, uom_list, selected_uom_id);
    funcHeaderCalc(tr); // function making on po form
}
function get_po_product_detail_old(tr, response, formData) {
    var barcode = response['current_product'];
    var code = response['code'];
    var uom_list = response['uom_list'];
    var selected_uom_id = barcode['uom']['uom_id'];
    var uom_name = barcode['uom']['uom_name'];
    var current_sale_rate = response['rate'];
    var last_tp = response['purc_rate']['last_cost_rate'];
    var ven_last_tp = response['purc_rate']['supplier_cost_rate'];
    tr.find('input.product_barcode_id').val(barcode['product_barcode_id']);
    tr.find('input.product_id').val(barcode['product_id']);
    tr.find('input.uom_id').val(selected_uom_id);
    tr.find('.product_name').val(barcode['product']['product_name']);
    tr.find('.pd_barcode').val(code);
    tr.find('.pd_uom').val(uom_name);
    tr.find('.tblGridCal_last_tp').val(last_tp);
    tr.find('.tblGridCal_vend_last_tp').val(ven_last_tp);
    tr.find('.pd_packing').val(barcode['purchase_order_dtlpacking']);
    tr.find('.tblGridCal_qty').val(barcode['purchase_order_dtlquantity']);
    tr.find('.tblGridSale_rate').val(notNullEmpty(current_sale_rate['product_barcode_sale_rate_rate'], 3));
    tr.find('.foc_qty').val(barcode['purchase_order_dtlfoc_quantity']);
    tr.find('.fc_rate').val(barcode['purchase_order_dtlfc_rate']);
    tr.find('.tblGridCal_rate').val(barcode['purchase_order_dtlrate']).addClass('grn_green');
    tr.find('input.grn_dtl_po_rate').val(barcode['purchase_order_dtlrate']);
    tr.find('.tblGridCal_amount').val(barcode['purchase_order_dtlamount']);
    tr.find('.tblGridCal_discount_perc').val(barcode['purchase_order_dtldisc_percent']);
    tr.find('.tblGridCal_discount_amount').val(barcode['purchase_order_dtldisc_amount']);
    tr.find('.tblGridCal_vat_perc').val(barcode['purchase_order_dtlvat_percent']);
    tr.find('.tblGridCal_vat_amount').val(barcode['purchase_order_dtlvat_amount']);
    tr.find('.tblGridCal_gross_amount').val(barcode['purchase_order_dtltotal_amount']);
    uomList(tr, uom_list, selected_uom_id);
}

function vegetableProduct(code, weight_apply) {
    var data = {};
    if(!valueEmpty(code)){
        var substr = code.substring(0, 2);
        if (weight_apply == 1) {
            var weight = code.substring(7, 12);
            var qty = parseFloat(weight / 1000);
            data.default_qty = notNullEmpty(qty, 3);
            data.vegetableProduct = true;
        }
    } else {
        data.default_qty = 1;
        data.vegetableProduct = false;
    }
    return data;
}

function uomList(tr, uom_list, selected_uom_id) {
    var options = '';
    for (var i = 0; uom_list.length > i; i++) {
        options += '<option value=' + uom_list[i]['uom_id'] + '>' + uom_list[i]['uom_name'] + '</option>';
    }
    tr.find('.pd_uom').html(options);
    tr.find('.pd_uom').val(selected_uom_id);
}

function barcode_already_exits_in_grid(rateFound) {
    var barcode_found_current_grid = false;
    var barcode_found_current_grid_rate = 0;
    $('.erp_form__grid_body>tr>td:first-child').each(function() {
        var bar_id = $(this).find('input[data-id="product_barcode_id"]').val();
        if (bar_id == rateFound.barcode_id) {
            barcode_found_current_grid = false;
            barcode_found_current_grid_rate = $(this).parents('tr').find('input[data-id="rate"]').val();
        }
    });
    if (barcode_found_current_grid) {
        rateFound.tr.find('.tblGridCal_rate').val(notNullEmpty(barcode_found_current_grid_rate, 3));
        $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
        var documentNo = $('#opening_stock_form').find('.erp-page--title').text();
        swal.fire({
            title: rateFound.barcode_barcode,
            html: 'Product already exists. <br>Document No: ' + documentNo,
            type: 'warning',
            showConfirmButton: true,
        });
    } else {
        if (rateFound.store_id != 0) {
            barcode_already_exits_in_OS_DBtable(rateFound);
        } else {
            $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
        }
    }
}

function barcode_already_exits_in_OS_DBtable(rateFound) {
    var formData = {
        barcode_id: rateFound.barcode_id,
        store_id: rateFound.store_id,
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/barcode/get-barcode-rate-os-table',
        dataType: 'json',
        data: formData,
        success: function(response) {
            if (response['status'] == 'success') {
                rateFound.tr.find('.tblGridCal_rate').val(notNullEmpty(response['os_barcode'][0].stock_dtl_rate, 3));
                $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
                swal.fire({
                    title: rateFound.barcode_barcode,
                    html: 'Product already exists. <br>Document No: ' + response['os_barcode'][0].stock_code,
                    type: 'warning',
                    showConfirmButton: true,
                });
            } else {
                if (rateFound.selected_barcode_rate == '0') {
                    rateFound.tr.find('.tblGridCal_rate').val(notNullEmpty(0, 3));
                    $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
                }
                if (rateFound.selected_barcode_rate == 'cost_rate') {
                    rateFound.tr.find('.tblGridCal_rate').val(notNullEmpty(rateFound.rate, 3));
                    $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
                }
                if (rateFound.selected_barcode_rate == 'last_purchase_rate') {
                    barcode_already_exits_in_GRN_DBtable(rateFound);
                }
                if (rateFound.selected_barcode_rate == "average_rate") {
                    $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
                }
                if (rateFound.selected_barcode_rate == "last_stock_rate") {
                    $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
                }
            }
        }
    });
}

function barcode_already_exits_in_GRN_DBtable(rateFound) {
    var formData = {
        barcode_id: rateFound.barcode_id,
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/barcode/get-barcode-rate-grn-table',
        dataType: 'json',
        data: formData,
        success: function(response) {
            if (response['status'] == 'success') {
                rateFound.tr.find('.tblGridCal_rate').val(notNullEmpty(response['grn_barcode'].tbl_purc_grn_dtl_rate, 3));
                $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
            } else {
                rateFound.tr.find('.tblGridCal_rate').val(notNullEmpty(0, 3));
                $('#opening_stock_form').find('.erp_form___block').removeClass('pointerEventsNone');
            }
        }
    });
}


$('#stock_from_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_stock_request_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_stock_request_detail(selected_row);
        }
    }
});

function get_stock_request_detail(selected_row) {
    var stock_from_code = selected_row.find('tr.data-dtl>td[data-field="demand_no"]').text();
    var stock_from_id = selected_row.find('tr.d-none>td[data-field="demand_id"]').text();
    selected_row.parents('.erp_form___block').find('#stock_from_code').val(stock_from_code);
    selected_row.parents('.erp_form___block').find('#stock_from_id').val(stock_from_id);
    selected_row.parents('.erp_form___block').find('#requestLink').attr('href', '/stock-request/print/' + stock_from_id).attr('target', '_blank');
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#stock_transfer_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_stock_transfer_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_stock_transfer_detail(selected_row);
        }
    }
});

function get_stock_transfer_detail(selected_row) {
    var stock_transfer_code = selected_row.find('tr.data-dtl>td[data-field="stock_code"]').text();
    var stock_from_id = selected_row.find('tr.d-none>td[data-field="stock_id"]').text();
    var stock_store_from_id = selected_row.find('tr.d-none>td[data-field="stock_store_from_id"]').text();
    var stock_branch_from_id = selected_row.find('tr.d-none>td[data-field="stock_branch_from_id"]').text();

    selected_row.parents('.erp_form___block').find('#stock_transfer_code').val(stock_transfer_code);
    selected_row.parents('.erp_form___block').find('#stock_from_id').val(stock_from_id);
    selected_row.parents('.erp_form___block').find('#store').val(stock_store_from_id);
    selected_row.parents('.erp_form___block').find('#branch_from_id').val(stock_branch_from_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#supplier_name').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_supplier_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_supplier_detail(selected_row);
        }
    }
});

function get_supplier_detail(selected_row) {
    var supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();
    var supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();
    // Check If the Row Identifier is Set
    if(selected_row.find('tr.d-none>td[data-field="row_identifier"]').length > 0){
        var target_row = selected_row.find('tr.d-none>td[data-field="row_identifier"]').text();
        if($('.row_' + target_row ).find('#supplier_name').length > 0){
            $('.row_' + target_row ).find('#supplier_name').val(supplier_name);
            $('.row_' + target_row ).find('#supplier_id').val(supplier_id);
        }else{
            $('.row_' + target_row ).find('.supplier_name').val(supplier_name);
            $('.row_' + target_row ).find('.supplier_id').val(supplier_id);
        }
    }else{
        selected_row.parents('.erp_form___block').find('#supplier_name').val(supplier_name);
        selected_row.parents('.erp_form___block').find('#supplier_id').val(supplier_id);
    }
    if(selected_row.find('tr.d-none>td[data-field="supplier_has_returnable"]').length > 0){
        swal.fire({
            title: "Supplier Have Some Products In Purchase Return (GRV)",
            text: 'Are you sure to add this?',
            type: 'warning',
            showConfirmButton: true
        });
        // alertShowed = true;
    }

    $('#inLineHelp').remove();
    selected_row.parents('tr').find('input').removeClass('open_inline__help__focus');
}

$('#employee_name').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_employee_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_employee_detail(selected_row);
        }
    }
});

function get_employee_detail(selected_row) {
    var employee_name = selected_row.find('tr.data-dtl>td[data-field="employee_name"]').text();
    var employee_id = selected_row.find('tr.d-none>td[data-field="employee_id"]').text();
    selected_row.parents('.erp_form___block').find('#employee_name').val(employee_name);
    selected_row.parents('.erp_form___block').find('#employee_id').val(employee_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#loan_confi_name').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_loan_confi_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_loan_confi_detail(selected_row);
        }
    }
});

function get_loan_confi_detail(selected_row) {
    var loan_type_id = selected_row.find('tr.d-none>td[data-field="loan_type"]').text();
    var configuration_id = selected_row.find('tr.d-none>td[data-field="loan_configuration_id"]').text();
    var loan_type_name = selected_row.find('tr.data-dtl>td[data-field="advance_type_name"]').text();
    var configuration_name = selected_row.find('tr.data-dtl>td[data-field="description"]').text();
    $('#loan_confi_name').val(configuration_name);
    selected_row.parents('.erp_form___block').find('#loan_confi_id').val(configuration_id);
    $('#loan_confi_type_id').val(loan_type_id);
    $('#loan_confi_type_name').val(loan_type_name);

    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#f_barcode').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_product_formulation_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_product_formulation_detail(selected_row);
        }
    }
});

function get_product_formulation_detail(selected_row) {
    var product_barcode = selected_row.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text();
    var product_name = selected_row.find('tr.data-dtl>td[data-field="product_name"]').text();
    var product_id = selected_row.find('tr.d-none>td[data-field="product_id"]').text();
    var uom_id = selected_row.find('tr.d-none>td[data-field="uom_id"]').text();
    var product_barcode_id = selected_row.find('tr.d-none>td[data-field="product_barcode_id"]').text();
    var product_barcode_packing = selected_row.find('tr.d-none>td[data-field="product_barcode_packing"]').text();
    $('#f_barcode').val(product_barcode);
    $('#f_product_name').val(product_name);
    $('#f_product_id').val(product_id);
    $('#f_product_uom_id').val(uom_id);
    $('#f_product_barcode_id').val(product_barcode_id);
    $('#f_product_barcode_packing').val(product_barcode_packing);

    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

function get_food_detail(selected_row) {
    console.log(selected_row);
    var product_name = selected_row.find('dtltr.data->td[data-field="food_name"]').text();
    var product_id = selected_row.find('tr.d-none>td[data-field="food_id"]').text();

    $('#food_name').val(product_name);
    $('#food_id').val(product_id);

    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

//formulationEntryHelp

$('#f_barcode').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_formula_entry(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_formula_entry(selected_row);
        }
    }
});

function get_formula_entry(selected_row) {

    var item_formulation_code = selected_row.find('tr.data-dtl>td[data-field="item_formulation_code"]').text();
    var item_formulation_id = selected_row.find('tr.d-none>td[data-field="item_formulation_id"]').text();
    var product_name = selected_row.find('tr.data-dtl>td[data-field="product_name"]').text();
    var product_id = selected_row.find('tr.d-none>td[data-field="product_id"]').text();

    $('#formulation_code').val(item_formulation_code);
    $('#formulation_id').val(item_formulation_id);

    if($('#m_product_barcode_id').length != 0){
        $('#m_product_barcode_id').val(product_id);
        $('#m_product_name').val(product_name);
    }
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}




$('#purchase_order').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_purchase_order_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_purchase_order_detail(selected_row);
        }
    }
});

function get_purchase_order_detail(selected_row) {
    // $('.erp_form__grid_body').html('');
    $('#pro_tot').val(notNullEmpty(0, 3));
    $('.t_gross_total').text(notNullEmpty(0, 3));
   // formClear();
    TotalExpenseAmount();
    var purchase_order = selected_row.find('tr.data-dtl>td[data-field="purchase_order_code"]').text();
    var purchase_order_id = selected_row.find('tr.d-none>td[data-field="purchase_order_id"]').text();
    var supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();
    var supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();
    selected_row.parents('.erp_form___block').find('#purchase_order').val(purchase_order);
    selected_row.parents('.erp_form___block').find('#purchase_order_id').val(purchase_order_id);
    $('#grn_form').find('.erp_form___block').find('#supplier_name').val(supplier_name);
    $('#grn_form').find('.erp_form___block').find('#supplier_name').parents('.open-modal-group').addClass('readonly');
    $('#grn_form').find('.erp_form___block').find('#supplier_name').addClass('readonly');
    $('#grn_form').find('.erp_form___block').find('#supplier_id').val(supplier_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#purchase_order').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_purchase_return_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_purchase_return_detail(selected_row);
        }
    }
});

function get_purchase_return_detail(selected_row) {
    var purc_return_code = selected_row.find('tr.data-dtl>td[data-field="grn_code"]').text();
    var purc_return_id = selected_row.find('tr.d-none>td[data-field="grn_id"]').text();
    var ref_supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();

    $('#retqty_code').val(purc_return_code);
    $('#retqty_id').val(purc_return_id);
    $('#ref_supplier_id').val(ref_supplier_id);

    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#sales_contract_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_sales_contract_code_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_sales_contract_code_detail(selected_row);
        }
    }
});

function get_sales_contract_code_detail(selected_row) {
    var sales_contract_code = selected_row.find('tr.data-dtl>td[data-field="sales_contract_code"]').text();
    var sales_contract_id = selected_row.find('tr.d-none>td[data-field="sales_contract_id"]').text();
    var sales_contract_rate_type = selected_row.find('tr.d-none>td[data-field="sales_contract_rate_type"]').text();
    var sales_contract_perc = selected_row.find('tr.d-none>td[data-field="sales_contract_perc"]').text();

    selected_row.parents('.erp_form___block').find('#sales_contract_code').val(sales_contract_code);
    selected_row.parents('.erp_form___block').find('#sales_contract_id').val(sales_contract_id);
    selected_row.parents('.row').find('#rate_type').val('item_contract_rate').change();
    selected_row.parents('.row').find('#rate_perc').val(0);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#sales_order_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_sales_order_code_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_sales_order_code_detail(selected_row);
        }
    }
});

function get_sales_order_code_detail(selected_row) {
    var sales_order_code = selected_row.find('tr.data-dtl>td[data-field="sales_order_code"]').text();
    var sales_order_booking_id = selected_row.find('tr.d-none>td[data-field="sales_order_id"]').text();

    selected_row.parents('.erp_form___block').find('#sales_order_code').val(sales_order_code);
    selected_row.parents('.erp_form___block').find('#sales_order_booking_id').val(sales_order_booking_id);

    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#customer_name').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_customer_name_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_customer_name_detail(selected_row);
        }
    }
});

function get_customer_name_detail(selected_row) {
    var customer_name = selected_row.find('tr.data-dtl>td[data-field="customer_name"]').text();
    customer_name = customer_name.trim();
    var customer_code = selected_row.find('tr.data-dtl>td[data-field="customer_code"]').text();
    customer_code = customer_code.trim();
    var customer_address = selected_row.find('tr.data-dtl>td[data-field="customer_address"]').text();
    customer_address = customer_address.trim();
    var customer_contact = selected_row.find('tr.data-dtl>td[data-field="customer_phone_1"]').text();
    customer_contact = customer_contact.trim();
    var customer_id = selected_row.find('tr.d-none>td[data-field="customer_id"]').text();
    customer_id = customer_id.trim();
    var city_id     = selected_row.find('tr.d-none>td[data-field="city_id"]').text();
    var region_id   = selected_row.find('tr.d-none>td[data-field="region_id"]').text();


    selected_row.parents('.erp_form___block').find('#customer_name').val(customer_name.trim());
    selected_row.parents('.erp_form___block').find('#customer_id').val(customer_id.trim());


    if(selected_row.parents('.erp_form___block').find('#customer_contact').length > 0){
        selected_row.parents('.erp_form___block').find('#customer_contact').val(customer_contact);
    }else{
        $('#sales_mobile_no').val(customer_contact);
    }
    if(selected_row.parents('.erp_form___block').find('#customer_address').length > 0){
        selected_row.parents('.erp_form___block').find('#customer_address').val(customer_address);
    }else{
        $('#customer_address').val(customer_address);
    }

    if($('#sales_order_city_id').length > 0){
        $('#sales_order_city_id').val(city_id);
        $('#sales_order_city_id').trigger('change');

        $('#sales_order_area').val(region_id);
    }

    if($('#customer-code-no').length > 0){
        $('#customer-code-no').html('').html(customer_code);
    }


    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#ist_code').keydown(function(e) {
    // ist == Internal Stock Transfer
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_ist_code_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_ist_code_detail(selected_row);
        }
    }
});

function get_ist_code_detail(selected_row) {
    // ist == Internal Stock Transfer
    var ist_code = selected_row.find('tr.data-dtl>td[data-field="stock_code"]').text();
    var ist_id = selected_row.find('tr.d-none>td[data-field="stock_id"]').text();
    selected_row.parents('.erp_form___block').find('#ist_code').val(ist_code);
    selected_row.parents('.erp_form___block').find('#ist_id').val(ist_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}


$('#tp_barcode').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_tp_barcode_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_tp_barcode_detail(selected_row);
        }
    }
});
function get_tp_barcode_detail(selected_row) {
    var product_barcode_barcode = selected_row.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text();
    var product_id = selected_row.find('tr.d-none>td[data-field="product_id"]').text();
    var product_barcode_id = selected_row.find('tr.d-none>td[data-field="product_barcode_id"]').text();
    selected_row.parents('.erp_form___block').find('#tp_barcode').val(product_barcode_barcode);
    selected_row.parents('.erp_form___block').find('#tp_product_id').val(product_id);
    selected_row.parents('.erp_form___block').find('#tp_product_barcode_id').val(product_barcode_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#f_barcode').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_merged_from_barcode_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_merged_from_barcode_detail(selected_row);
        }
    }
});
function get_merged_from_barcode_detail(selected_row) {
    var product_barcode_barcode = selected_row.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text();
    var product_name = selected_row.find('tr>td[data-field="product_name"]').text();
    var product_id = selected_row.find('tr.d-none>td[data-field="product_id"]').text();
    var product_barcode_id = selected_row.find('tr.d-none>td[data-field="product_barcode_id"]').text();
    selected_row.parents('.erp_form___block').find('#f_barcode').val(product_barcode_barcode);
    selected_row.parents('.erp_form___block').find('#f_product_barcode_id').val(product_barcode_id);
    selected_row.parents('.erp_form___block').find('#f_product_id').val(product_id);
    $('#f_product_name').val(product_name);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#m_barcode').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_merged_to_barcode_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_merged_to_barcode_detail(selected_row);
        }
    }
});
function get_merged_to_barcode_detail(selected_row) {
    var product_barcode_barcode = selected_row.find('tr.data-dtl>td[data-field="product_barcode_barcode"]').text();
    var product_name = selected_row.find('tr>td[data-field="product_name"]').text();
    var product_id = selected_row.find('tr.d-none>td[data-field="product_id"]').text();
    var product_barcode_id = selected_row.find('tr.d-none>td[data-field="product_barcode_id"]').text();
    selected_row.parents('.erp_form___block').find('#m_barcode').val(product_barcode_barcode);
    selected_row.parents('.erp_form___block').find('#m_product_barcode_id').val(product_barcode_id);
    selected_row.parents('.erp_form___block').find('#m_product_id').val(product_id);
    $('#m_product_name').val(product_name);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}


$('#account_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_account_code_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_account_code_detail(selected_row);
        }
    }
});
function get_account_code_detail(selected_row) {
    var account_code = selected_row.find('tr.data-dtl>td[data-field="chart_code"]').text();
    var account_name = selected_row.find('tr.data-dtl>td[data-field="chart_name"]').text();
    var account_id = selected_row.find('tr.d-none>td[data-field="chart_account_id"]').text();
    selected_row.parents('.erp_form___block').find('#account_code').val(account_code.trim());
    selected_row.parents('.erp_form___block').find('#account_name').val(account_name.trim());
    selected_row.parents('.erp_form___block').find('#account_id').val(account_id.trim());

    if($('#form_type').val() == 'pv' || 'rv'){
        var newAcc = {
            'id': account_id,
            'code': account_code,
            'name': account_name
        };
        localStorage.setItem('accDeductObj', JSON.stringify(newAcc));
    }
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}
$('#up_chart_account_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            func_up_get_account_code_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            func_up_get_account_code_detail(selected_row);
        }
    }
});
function func_up_get_account_code_detail(selected_row) {
    var account_code = selected_row.find('tr.data-dtl>td[data-field="chart_code"]').text();
    var account_name = selected_row.find('tr.data-dtl>td[data-field="chart_name"]').text();
    var account_id = selected_row.find('tr.d-none>td[data-field="chart_account_id"]').text();

    if($('#form_type').val() == 'pv' || 'rv'){
        selected_row.parents('.erp_form___block').find('#up_chart_account_code').val(account_code.trim());
        selected_row.parents('.erp_form___block').find('#up_chart_account_name').val(account_name.trim());
        selected_row.parents('.erp_form___block').find('#up_chart_account_id').val(account_id.trim());
        $('#ledger_bal').val(0);
    //    $('#bill_total_amount').val(0);
/*
        var account_id = account_id.trim();
        var arr = {
            account_id : account_id
        };
        getDatafromGrnBill(arr)*/
    }

    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}
$('#c_account_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_c_account_code_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_c_account_code_detail(selected_row);
        }
    }
});
function get_c_account_code_detail(selected_row) {
    var account_code = selected_row.find('tr.data-dtl>td[data-field="chart_code"]').text();
    var account_name = selected_row.find('tr.data-dtl>td[data-field="chart_name"]').text();
    var account_id = selected_row.find('tr.d-none>td[data-field="chart_account_id"]').text();
    selected_row.parents('.erp_form___block').find('#c_account_code').val(account_code.trim());
    selected_row.parents('.erp_form___block').find('#c_account_name').val(account_name.trim());
    selected_row.parents('.erp_form___block').find('#c_account_id').val(account_id.trim());
    if($('#form_type').val() == 'pv' || 'rv'){
        var newAcc = {
            'id': account_id,
            'code': account_code,
            'name': account_name
        };
        localStorage.setItem('accAccObj', JSON.stringify(newAcc));
    }
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#supplier_bank').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_supplier_bank_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_supplier_bank_detail(selected_row);
        }
    }
});
function get_supplier_bank_detail(selected_row) {
    var supplier_bank = selected_row.find('tr.data-dtl>td[data-field="bank_name"]').text();
    var branch_no = selected_row.find('tr.data-dtl>td[data-field="supplier_iban_no"]').text();
    var supplier_account_no = selected_row.find('tr.data-dtl>td[data-field="supplier_account_no"]').text();

    var supplier_account_id = selected_row.find('tr.d-none>td[data-field="supplier_account_id"]').text();
    var supplier_bank_id = selected_row.find('tr.d-none>td[data-field="supplier_bank_name"]').text();
    selected_row.parents('.erp_form___block').find('#supplier_account_id').val(supplier_account_id.trim());
    selected_row.parents('.erp_form___block').find('#supplier_bank_id').val(supplier_bank_id.trim());

    selected_row.parents('.erp_form___block').find('#supplier_bank').val(supplier_bank.trim());
    selected_row.parents('.erp_form___block').find('#bank_branch_code').val(branch_no.trim());
    selected_row.parents('.erp_form___block').find('#supplier_bank_ac_no').val(supplier_account_no.trim());
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}
// Sales Qutation Start
$('#sales_quotation_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_sales_quotation_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_sales_quotation_detail(selected_row);
        }
    }
});

function get_sales_quotation_detail(selected_row){
    var sales_quotation_code = selected_row.find('tr.data-dtl>td[data-field="sales_order_code"]').text();
    var sales_quotation_id = selected_row.find('tr.d-none>td[data-field="sales_order_id"]').text();

    $('#sales_quotation_code').val(sales_quotation_code);
    $('#sales_quotation_id').val(sales_quotation_id);
}
// Sales Qutation End

// Request Quotation Start
$('#sales_request_quotation_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_sales_request_quotation_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_sales_request_quotation_detail(selected_row);
        }
    }
});

function get_sales_request_quotation_detail(selected_row){
    var sales_quotation_code = selected_row.find('tr.data-dtl>td[data-field="sales_order_code"]').text();
    var sales_quotation_id = selected_row.find('tr.d-none>td[data-field="sales_order_id"]').text();

    $('#sales_request_quotation_code').val(sales_quotation_code);
    $('#sales_request_quotation_id').val(sales_quotation_id);
}
// Request Quotation End

// Services Order Help Start
// Request Quotation Start
$('#services_order_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_services_order_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_services_order_detail(selected_row);
        }
    }
});

function get_services_order_detail(selected_row){
    var sales_quotation_code = selected_row.find('tr.data-dtl>td[data-field="sales_order_code"]').text();
    var sales_quotation_id = selected_row.find('tr.d-none>td[data-field="sales_order_id"]').text();

    $('#services_order_code').val(sales_quotation_code);
    $('#services_order_id').val(sales_quotation_id);
}
// Services Order Help End

$('#lpo_generation_no').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_lpo_po_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_lpo_po_detail(selected_row);
        }
    }
});

function get_lpo_po_detail(selected_row){
    var lpo_generation_code = selected_row.find('tr.data-dtl>td[data-field="lpo_code"]').text();
    var lpo_generation_id = selected_row.find('tr.d-none>td[data-field="lpo_id"]').text();
    var lpo_supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();
    var lpo_supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();

    $('#lpo_generation_no').val(lpo_generation_code);
    $('#supplier_name').val(lpo_supplier_name);
    $('#lpo_generation_no_id').val(lpo_generation_id);
    $('#supplier_id').val(lpo_supplier_id);
}

$('#ref_grn_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_grn_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_grn_detail(selected_row);
        }
    }
});

function get_grn_detail(selected_row){
    var grn_code = selected_row.find('tr.data-dtl>td[data-field="grn_code"]').text();
    var grn_id = selected_row.find('tr.d-none>td[data-field="grn_id"]').text();
    if($('#form_type').val() == 'change_rate'){
        var supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();
        var supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();
        $('form#change_rate').find('.erp_form___block').find('#supplier_name').val(supplier_name);
        $('form#change_rate').find('.erp_form___block').find('#supplier_id').val(supplier_id);
        $('form#change_rate').find('.erp_form___block').find('#supplier_name').parents('.open-modal-group').addClass('readonly');
        $('form#change_rate').find('.erp_form___block').find('#supplier_name').addClass('readonly');

    }
    $('#ref_grn_code').val(grn_code);
    $('#ref_grn_id').val(grn_id);
}

$('#stock_receiving_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_stock_receiving_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_stock_receiving_detail(selected_row);
        }
    }
});

function get_stock_receiving_detail(selected_row){
    var stock_receiving_code = selected_row.find('tr.data-dtl>td[data-field="stock_code"]').text();
    var stock_receiving_id = selected_row.find('tr.d-none>td[data-field="stock_id"]').text();

    $('#stock_receiving_code').val(stock_receiving_code);
    $('#stock_receiving_id').val(stock_receiving_id);
}

$('#auto_demand_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_auto_demand_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_auto_demand_detail(selected_row);
        }
    }
});

function get_auto_demand_detail(selected_row){
    var auto_demand_code = selected_row.find('tr.data-dtl>td[data-field="ad_code"]').text();
    var auto_supplier_name = selected_row.find('tr.data-dtl>td[data-field="supplier_name"]').text();
    var auto_demand_id = selected_row.find('tr.d-none>td[data-field="ad_id"]').text();
    var auto_supplier_id = selected_row.find('tr.d-none>td[data-field="supplier_id"]').text();

    $('#auto_demand_code').val(auto_demand_code);
    $('#supplier_name').val(auto_supplier_name);
    $('#auto_demand_id').val(auto_demand_id);
    $('#supplier_id').val(auto_supplier_id);
}

$('#sales_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_sales_invoice_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_sales_invoice_detail(selected_row);
        }
    }
});

function get_sales_invoice_detail(selected_row) {
    var form = selected_row.parents('.erp_form___block').closest('form');
    form.find('.erp_form__grid_body>tr').each(function() {
        $(this).find('td:eq(0)>input[data-id="product_id"]').parents('tr').remove();
    })
    dataDeleteInit();
    var sales_code = selected_row.find('tr.data-dtl>td[data-field="sales_code"]').text();
    var sales_id = selected_row.find('tr.d-none>td[data-field="sales_id"]').text();
    var customer_id = selected_row.find('tr.d-none>td[data-field="customer_id"]').text();
    var customer_name = selected_row.find('tr.d-none>td[data-field="customer_name"]').text();

    form.find('#sales_code').val(sales_code);
    form.find('#sales_id').val(sales_id);
    form.find('#customer_name').val(customer_name);
    form.find('#customer_id').val(customer_id);
    $.ajax({
        type: 'GET',
        url: '/sales-delivery/sale-invoice/' + sales_id,
        data: {},
        success: function(response, status) {
            if (response.status == 'success') {
                var tr = '';
                var total_length = $('#repeated_data>tr').length;
                for (var p = 0; p < response.data['all'].length; p++) {
                    total_length++;
                    var row = response.data['all'][p];
                    tr += '<tr>' +
                        '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                        '<input type="text" name="pd[' + total_length + '][sr_no]" value="' + total_length + '" title="' + total_length + '" class="form-control sr_no erp-form-control-sm handle" readonly>' +
                        '<input type="hidden" name="pd[' + total_length + '][sales_dtl_id]" data-id="sales_dtl_id" value="' + notNull(row['sales_dtl_id']) + '" class="sales_dtl_id form-control erp-form-control-sm " readonly>' +
                        '<input type="hidden" name="pd[' + total_length + '][product_id]" data-id="product_id" value="' + notNull(row['product_id']) + '" class="product_id form-control erp-form-control-sm " readonly>' +
                        '<input type="hidden" name="pd[' + total_length + '][uom_id]" data-id="uom_id" value="' + notNull(row['uom_id']) + '"class="uom_id form-control erp-form-control-sm " readonly>' +
                        '<input type="hidden" name="pd[' + total_length + '][product_barcode_id]" data-id="product_barcode_id" value="' + notNull(row['product_barcode_id']) + '" class="product_barcode_id form-control erp-form-control-sm " readonly>' +
                        '</td>' +
                        '<td><input type="text" name="pd[' + total_length + '][pd_barcode]" data-id="pd_barcode" value="' + notNull(row['barcode']['product_barcode_barcode']) + '" title="' + notNull(row['barcode']['product_barcode_barcode']) + '" class="form-control erp-form-control-sm" readonly></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][product_name]" data-id="product_name" value="' + notNull(row['product']['product_name']) + '" title="' + notNull(row['product']['product_name']) + '" class="pd_product_name form-control erp-form-control-sm" readonly></td>' +
                        '<td>' +
                        '<select class="pd_uom field_readonly form-control erp-form-control-sm" name="pd[' + total_length + '][pd_uom]" data-id="pd_uom" title="' + notNull(row['uom']['uom_name']) + '">' +
                        '<option value="' + notNull(row['uom']['uom_id']) + '">' + notNull(row['uom']['uom_name']) + '</option>' +
                        '</select>' +
                        '</td>' +
                        '<td><input type="text" name="pd[' + total_length + '][pd_packing]" data-id="pd_packing" value="' + notNull(row['barcode']['product_barcode_packing']) + '" title="' + notNull(row['barcode']['product_barcode_packing']) + '" class="pd_packing form-control erp-form-control-sm" readonly></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][quantity]" data-id="quantity" value="' + notNull(row['sales_dtl_quantity']) + '" title="' + notNull(row['sales_dtl_quantity']) + '" class="tblGridCal_qty tb_moveIndex form-control erp-form-control-sm validNumber validOnlyNumber" ></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][foc_qty]" data-id="foc_qty" value="' + notNull(row['sales_dtl_foc_qty']) + '" title="' + notNull(row['sales_dtl_foc_qty']) + '" class="form-control tb_moveIndex erp-form-control-sm validNumber"></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][fc_rate]" data-id="fc_rate" value="' + notNull(row['sales_dtl_fc_rate']) + '" title="' + notNull(row['sales_dtl_fc_rate']) + '" class="fc_rate tb_moveIndex form-control erp-form-control-sm validNumber"></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][g_rate]" data-id="g_rate"  value="' + notNullEmpty(row['sales_dtl_gross_rate'], threeDecimal) + '" class="g_rate form-control erp-form-control-sm validNumber" ></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][rate]" data-id="rate" value="' + notNullEmpty(row['sales_dtl_rate'], twoDecimal) + '"  title="' + notNullEmpty(row['sales_dtl_rate'], threeDecimal) + '"  class="tblGridCal_rate tb_moveIndex form-control erp-form-control-sm validNumber" ></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][amount]" data-id="amount" value="' + notNullEmpty(row['sales_dtl_amount'], threeDecimal) + '" title="' + notNullEmpty(row['sales_dtl_amount'], threeDecimal) + '"  class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][dis_perc]" data-id="dis_perc" value="' + notNullEmpty(row['sales_dtl_disc_per'], twoDecimal) + '" title="' + notNullEmpty(row['sales_dtl_disc_per'], twoDecimal) + '"  class="tblGridCal_discount_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][dis_amount]" data-id="dis_amount" value="' + notNullEmpty(row['sales_dtl_disc_amount'], threeDecimal) + '"  title="' + notNullEmpty(row['sales_dtl_disc_amount'], threeDecimal) + '"    class="tblGridCal_discount_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][vat_perc]" data-id="vat_perc" value="' + notNullEmpty(row['sales_dtl_vat_per'], twoDecimal) + '" title="' + notNullEmpty(row['sales_dtl_vat_per'], twoDecimal) + '"   class="tblGridCal_vat_perc tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][vat_amount]" data-id="vat_amount" value="' + notNullEmpty(row['sales_dtl_vat_amount'], threeDecimal) + '" title="' + notNullEmpty(row['sales_dtl_vat_amount'], threeDecimal) + '" class="tblGridCal_vat_amount tb_moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber"></td>' +
                        '<td><input type="text" name="pd[' + total_length + '][gross_amount]" data-id="gross_amount" value="' + notNullEmpty(row['sales_dtl_total_amount'], threeDecimal) + '" title="' + notNullEmpty(row['sales_dtl_total_amount'], threeDecimal) + '" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' +
                        '<td class="text-center"></td>' +
                        '</tr>';
                }
                form.find('.erp_form__grid_body').append(tr);
                addDataInit();
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(response, status) {},
    });
    $('#inLineHelp').remove();
    $('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('.group').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_group_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_group_detail(selected_row);
        }
    }
});

function get_group_detail(selected_row) {
    var form_type = $('#form_type').val();
    if(form_type == 'sales_scheme'){
        var group_name = selected_row.find('tr.data-dtl>td[data-field="group_item_name_string"]').text();
        var group_id = selected_row.find('tr.d-none>td[data-field="group_item_id"]').text();
        selected_row.parents('.erp_form___block').find('#product_name').val(group_name);
        selected_row.parents('.erp_form___block').find('#pd_barcode').val(group_id);

        selected_row.parents('.erp_form___block').find('#product_id').val('0');
        selected_row.parents('.erp_form___block').find('#product_barcode_id').val('0');

        selected_row.parents('.erp_form___block').find('#dis_perc').focus();
    }else{
        var group_name = selected_row.find('tr.data-dtl>td[data-field="group_item_name_string"]').text();
        var group_id = selected_row.find('tr.d-none>td[data-field="group_item_id"]').text();
        selected_row.parents('.erp_form___block').find('.group').val(group_name);
        selected_row.parents('.erp_form___block').find('.group_id').val(group_id);
    }
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
    $('#inLineHelp').remove();
}

$('.brand').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_brand_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_brand_detail(selected_row);
        }
    }
});

function get_brand_detail(selected_row) {
    var brand_name = selected_row.find('tr.data-dtl>td[data-field="brand_name"]').text();
    var brand_id = selected_row.find('tr.d-none>td[data-field="brand_id"]').text();
    selected_row.parents('.erp_form___block').find('.brand').val(brand_name);
    selected_row.parents('.erp_form___block').find('.brand_id').val(brand_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#purchasing_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_stock_purchasing_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_stock_purchasing_detail(selected_row);
        }
    }
});

function get_stock_purchasing_detail(selected_row) {
    var purchasing_code = selected_row.find('tr.data-dtl>td[data-field="purchasing_code"]').text();
    var purchasing_id = selected_row.find('tr.d-none>td[data-field="purchasing_id"]').text();
    selected_row.parents('.erp_form___block').find('#purchasing_code').val(purchasing_code);
    selected_row.parents('.erp_form___block').find('#purchasing_id').val(purchasing_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$('#formulation_code').keydown(function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var selected_row = thix.parents('.erp_form___block').find('#inLineHelp .data_tbody_row.selected_row');
    if (code != "") {
        if (e.which === 13) {
            e.preventDefault();
            get_item_formulation_detail(selected_row);
        }
        if (e.which === 9 && thix.val() != '') {
            get_item_formulation_detail(selected_row);
        }
    }
});

function get_item_formulation_detail(selected_row) {
    var item_formulation_code = selected_row.find('tr.data-dtl>td[data-field="item_formulation_code"]').text();
    var item_formulation_id = selected_row.find('tr.d-none>td[data-field="item_formulation_id"]').text();
    selected_row.parents('.erp_form___block').find('#formulation_code').val(item_formulation_code);
    selected_row.parents('.erp_form___block').find('#formulation_id').val(item_formulation_id);
    $('#inLineHelp').remove();
    selected_row.parents('.erp_form___block').find('input').removeClass('open_inline__help__focus');
}

$(document).on('click' , '.toast-dismiss' , function(e){
    e.preventDefault();
});

//supplier barcode call supplier product registration
$(document).on('keydown', '.sup_barcode', function(e) {
    var thix = $(this);
    var code = thix.val().trim();
    var sup_id = $('#supplier_id').val();
    var form_type = $('#form_type').val();
    var tr = thix.parents('tr');
    var keycodeNo = e.which;
    var formData = {};
    formData = {
        val: code,
        sup_id: sup_id,
        form_type: form_type,
    }
    if ((keycodeNo === 13 || keycodeNo === 9) && code != "" && tr.find('.product_id').val() == '' && tr.find('.product_barcode_id').val() == '') {
        e.preventDefault()
        initBarcode(keycodeNo, tr, form_type, formData);
    }
});

/*
 *      Start Functionality modal
 * ******************************************/
$(document).on('dblclick', '#help_datatable_productHelp tr.kt-datatable__row--hover', function() {
    var that = $(this);
    var tr = $('.erp_form__grid_header>tr');
    var product_barcode_barcode = that.find('td[data-field="product_barcode_barcode"]>span').text();
    var keycodeNo = 13;
    var tr = $('.erp_form__grid_header>tr');
    var form_type = $('#form_type').val();
    var formData = {
        form_type: form_type,
        val: product_barcode_barcode,
    }
    initBarcode(keycodeNo, tr, form_type, formData);
    closeModalPop(false);
    tr.find('#quantity').focus();
})
$(document).on('dblclick', '#help_datatable_supplierHelp tr.kt-datatable__row--hover', function(e) {
    var thix = $(this);
    var id = thix.find('td[data-field="supplier_id"]>span').text();
    var name = thix.find('td[data-field="supplier_name"]>span').text();
    $(document).find('#supplier_id').val(id);
    $(document).find('#supplier_name').val(name);
    closeModalPop('supplier_name');
    $('#supplier_name').focus();
});
$(document).on('dblclick', '#help_datatable_poHelp tr.kt-datatable__row--hover', function(e) {
    var thix = $(this);
    var id = thix.find('td[data-field="purchase_order_id"]>span').text();
    var name = thix.find('td[data-field="purchase_order_code"]>span').text();
    $(document).find('#purchase_order_id').val(id);
    $(document).find('#purchase_order').val(name);
    closeModalPop('purchase_order');
    $('#purchase_order').focus();
});

$(document).on('dblclick', '#help_datatable_pendingPR tr.kt-datatable__row--hover', function(e) {
    var thix = $(this);
    var id = thix.find('td[data-field="grn_id"]>span').text();
    window.open('/purchase-return/form/'+id, '_blank');
});

function closeModalPop(tag) {
    $('.modal').find('.modal-content').empty();
    $('.modal').find('.modal-content').html('<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
    $('.modal').modal('hide');
    if (tag == 'pd_barcode') {
        $('#pd_barcode').focus();
    }
    if (tag == 'supplier_name') {
        $('#supplier_name').focus();
    }
}



/*
 *     End  Functionality modal
 *xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx*/

$(document).on('keyup' , function(e){
    if(e.which == 119){
        if(getCookie('showStockLog') == null){
            setCookie("showStockLog" , 1, 1);
            toastr.success('Toast(s) are actived.');
        }else{
            eraseCookie('showStockLog');
            toastr.error('Toast(s) are deactived.');
        }
    }
});

//========== Cookies Function
function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function eraseCookie(name) {
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

$(document).on('click' , '.toast-dismiss' , function(e){
    var thix = $(this);
    thix.parents('.modal ').remove();
    $('.modal-backdrop').remove(); // Optional
});

