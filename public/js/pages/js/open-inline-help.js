var currentRequest = null;

$(document).on('focusin', '.open_inline__help', function(e) {
    $(document).find('.open_inline__help').removeClass('open_inline__help__focus');
    $(this).addClass('open_inline__help__focus');
});

function display_help(that, table_block, table_block__table) {
    table_block__table.append('<div id="inLineHelp"><div class="inLineHelp"></div></div>');
    var inLineHelp = table_block.find('.inLineHelp');
    var help_width = 511;
    var body_width = $('body').width()
    if (that.parents('.open-modal-group').length != 0) {
        console.log('hello baby');
        var help_left = table_block.find('#inLineHelp').offset().left;
        if ((body_width - help_left) > help_width) {
            var cssLeft = 0;
        } else {
            var cssLeft = body_width - (help_left + help_width);
        }
        $('#inLineHelp').css({ left: cssLeft + 'px' });
        inLineHelp.addClass("inline_help");
    } else {
        if (that.parents('thead').hasClass('erp_form__grid_header')) {
            var offsetTop = that.parents('tr').height();
            if ((that.offset().left + help_width) > body_width) {
                var minus = (that.offset().left + help_width) - body_width;
                var offsetLeft = (that.offset().left - that.parents('.erp_form___block').offset().left) - minus;
            } else {
                var offsetLeft = that.offset().left - that.parents('.erp_form___block').offset().left;
            }
        }
        if (that.parents('tbody').hasClass('erp_form__grid_body')) {
            var offsetTop = $('.open_inline__help__focus').offset().top - $('.erp_form___block').offset().top + that.parents('tr').height();
        }
        var cssTop = offsetTop + 'px';
        var cssLeft = offsetLeft + 'px';
        $('#inLineHelp').css({ top: cssTop });
        $('#inLineHelp').css({ left: cssLeft });
        inLineHelp.addClass("inline_help_table");
    }
}
$(document).on('keyup', '.open_inline__help', function(e) {
    var that = $(this);
    var table_block = that.closest('.erp_form___block');
    var table_block__table = that.closest('.erp_form___block');
    var form_type = $('#form_type').val();
    if (e.which === 113 || ($(this).is('#OpenInlineSupplierHelp') && e.type === 'click') ) { //F2
        e.preventDefault();
        $('#inLineHelp').remove();
        // Purchase Return (GRV) Validation If he try to enter reffrence number
        if(form_type == 'purc_return' && that.attr('id') == "retqty_code" && $('#supplier_id').val() == ""){
            toastr.error("Please Select Supplier");$('#supplier_name').focus();
            $('#retqty_code,#retqty_id,#ref_supplier_id').val("");
            return false;
        }
        if (table_block.find('#inLineHelp').length == 0) {
            display_help(that, table_block, table_block__table);
            var inLineHelp = table_block.find('.inLineHelp');
            console.log(inLineHelp);

            var data_url = that.attr('data-url');
            if(that.attr('id') == 'formulation_code'){
                var product_id = $('#f_barcode').val();
                var url = data_url + '/' + product_id  + '/' + encodeURIComponent($(this).val());
            }else{
                var data_url = that.attr('data-url');
            }

            var formData = {};
            formData.form_type = form_type;
            // Assign an Unique ID to Parent TR
            var unique_id = new Date().getTime();
            formData.unique_id = unique_id;
            that.parents('tr').addClass('row_' + unique_id);

            if(form_type == 'pv'
                && !valueEmpty($('form').find('#up_chart_account_id').val())
            ){
                formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
            }

            if(form_type == 'rv'
                && !valueEmpty($('form').find('#up_chart_account_id').val())
            ){
                formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
            }

            var acc_form = ['cpv', 'bpv']
            if (acc_form.includes(form_type)) {
                formData.account_id = that.parents('tr').find('.account_id').val();
            }
            var form_type_list = ['sale_invoice', 'sales_fee', 'sales_quotation'];
            if ($('form').find('#sales_contract_code').length != 0 && form_type_list.includes(form_type)) {
                formData.customer_id = $('#customer_id').val();
            }
            if ($('form').find('#supplier_name').length != 0 && form_type == 'purc_return') {
                formData.supplier_id = $('#supplier_id').val();
            }
            if(form_type == 'purc_order' && $('form').find('#supplier_id').val() != ""){
                formData.supplier_id = $('#supplier_id').val();
            }
            formData.val = that.val();
            currentRequest = $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend : function(){
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                type: 'POST',
                url: data_url,
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response['body'] != null) {
                        inLineHelp.html(response['body']);
                    }
                }
            });
        }
    }
    if(e.which === 115){
        $('#inLineHelp').remove();
        var caseHelp = "";
        if (that.attr('id') == 'purchase_order') {
            funcPOModalHelp(e);
        }
        if (that.attr('id') == 'pd_barcode') {
            funcProductModalHelp(e);
        }
    }
    var helpNotOpen = false;
    if (e.which === 115 && helpNotOpen) { // F4
        $('#inLineHelp').remove();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var caseHelp = "";
        if (that.attr('id') == 'supplier_name') {
            caseHelp = 'supplierHelp';
        }
        if (that.attr('id') == 'pd_barcode') {
            caseHelp = 'productHelp';
        }
        if (that.attr('id') == 'purchase_order') {
            caseHelp = 'poHelp';
        }
        if (that.attr('id') == 'f_barcode') {
            caseHelp = 'productHelp';
        }

        if(that.attr('id') == 'multi_product'){
            var formData = {
                supplier_id: $('#supplier_id').val(),
            }
            var data_url = '/common/select-multiple-products';
        }

        $('#kt_modal_xl').modal('show').find('.modal-content').load('/common/help-open/' + caseHelp);
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });
        return false;
    }
    if (e.which === 40 && table_block.find('.inLineHelp').length != 0) {
        var inLineHelp = table_block.find('.inLineHelp');
        if (inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false) {
            inLineHelp.find('.data_tbody_row:eq(0)').addClass('selected_row');
        } else {
            var index = inLineHelp.find('.data_tbody_row.selected_row').index();
            var ww_index = index - 2;
            index = index - 1;
            inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
            inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
        }
        var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
        that.val(val);
    }
    if (e.which === 38 && table_block.find('.inLineHelp').length != 0) {
        var inLineHelp = table_block.find('.inLineHelp');
        if (inLineHelp.find('.data_tbody_row').hasClass('selected_row') == true) {
            var index = inLineHelp.find('.data_tbody_row.selected_row').index();
            var ww_index = index - 2;
            index = index - 3;
            inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
            inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
        }
        var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
        that.val(val);
    }

    var mobileRequest = true;

    // if type barcode search open help
    if (table_block.find('#inLineHelp').length != 0 && that.val().length >= 3) {
        var notAllowKeyCode = [113, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46];
        if (that.val() != '' && !notAllowKeyCode.includes(e.keyCode)) {
            //  display_help(that,table_block,table_block__table);
            var inLineHelp = table_block.find('.inLineHelp');
            inLineHelp.find('.data_tbody_row').removeClass('selected_row');
            if (inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false) {
                var data_url = that.attr('data-url');
                if( inLineHelp.find('.data_thead_row').attr('id') == 'productHelp'){
                    var url = data_url;
                }else{
                    var url = data_url + '/' + encodeURIComponent($(this).val());
                }
                var formData = {};
                formData.form_type = form_type;
                formData.val = $(this).val();
                var acc_form = ['cpv', '']
                if (acc_form.includes(form_type)) {
                    formData.account_id = that.parents('tr').find('.account_id').val();
                }
                var form_type_list = ['sale_invoice', 'sales_fee', 'sales_quotation'];
                if ($('form').find('#sales_contract_code').length != 0 && form_type_list.includes(form_type)) {
                    formData.customer_id = $('#customer_id').val();
                }
                if ($('form').find('#supplier_name').length != 0 && form_type == 'purc_return') {
                    formData.supplier_id = $('#supplier_id').val();
                }
                if(form_type == 'purc_order' && $('form').find('#supplier_id').val() != ""){
                    formData.supplier_id = $('#supplier_id').val();
                }
                if(form_type == 'pv'
                    && !valueEmpty($('form').find('#up_chart_account_id').val())
                ){
                    formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
                }
                if(form_type == 'rv'
                    && !valueEmpty($('form').find('#up_chart_account_id').val())
                ){
                    formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
                }
                currentRequest = $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        if(currentRequest != null) {
                            currentRequest.abort();
                        }
                        if (table_block.find('#inLineHelp').find('.kt-inline-spinner').length == 0) {
                            table_block.find('#inLineHelp').prepend('<div class="kt-spinner kt-inline-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style="position: absolute;top: 0;z-index: 999;width: 100%;background: #f3f3f3;height: 100%;opacity: 0.5;"></div>');
                        }
                    },
                    success: function(response) {
                        if (response['body'] != null) {
                            inLineHelp.html(response['body']);
                        }
                    },
                    error: function() {
                        table_block.find('#inLineHelp').find('.kt-inline-spinner').remove();
                    }
                });
                $(document).ajaxStop(function(e, d) {
                    table_block.find('#inLineHelp').find('.kt-inline-spinner').remove();
                    $(document).unbind("ajaxStop");
                });
                //if(inLineHelp.length != 0){
                //inLineHelp.load(url);
                //}
                mobileRequest = false;
            }
        }
        if ($(window).width() <= 1024 && mobileRequest == true) {
            // display_help(that,table_block,table_block__table);
            var inLineHelp = table_block.find('.inLineHelp');
            var data_url = $(this).attr('data-url');
            var url = data_url + '/' + encodeURIComponent($(this).val());
            if (inLineHelp.length != 0) {
                inLineHelp.load(url);
            }
        }
    }
    if ($('.inLineHelp .data_tbody_row').hasClass('selected_row')) {
        $("#inLineHelp .inline_help_table").scrollTop(0); //set to top
        $("#inLineHelp .inline_help").scrollTop(0); //set to top
        var selected_row = $("#inLineHelp").find('.selected_row:first');
        if (selected_row.length != 0) {
            $("#inLineHelp .inline_help_table").scrollTop(selected_row.position().top - 150);
            $("#inLineHelp .inline_help").scrollTop(selected_row.position().top - 150);
        }
    }
});

$(document).on('click', '#OpenInlineSupplierHelp', function(e) {
    var that = $(this);
    var table_block = that.closest('.erp_form___block');
    var table_block__table = that.closest('.erp_form___block');
    var form_type = $('#form_type').val();
    // if (e.which === 113) { //F2
        e.preventDefault();
        $('#inLineHelp').remove();
        // Purchase Return (GRV) Validation If he try to enter reffrence number
        if(form_type == 'purc_return' && that.attr('id') == "retqty_code" && $('#supplier_id').val() == ""){
            toastr.error("Please Select Supplier");$('#supplier_name').focus();
            $('#retqty_code,#retqty_id,#ref_supplier_id').val("");
            return false;
        }
        if (table_block.find('#inLineHelp').length == 0) {
            display_help(that, table_block, table_block__table);
            var inLineHelp = table_block.find('.inLineHelp');
            var data_url = that.attr('data-url');
            if(that.attr('id') == 'formulation_code'){
                var product_id = $('#f_barcode').val();
                var url = data_url + '/' + product_id  + '/' + encodeURIComponent($(this).val());
            }else{
                var data_url = that.attr('data-url');
            }

            var formData = {};
            formData.form_type = form_type;
            // Assign an Unique ID to Parent TR
            var unique_id = new Date().getTime();
            formData.unique_id = unique_id;
            that.parents('tr').addClass('row_' + unique_id);

            if(form_type == 'pv'
                && !valueEmpty($('form').find('#up_chart_account_id').val())
            ){
                formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
            }

            if(form_type == 'rv'
                && !valueEmpty($('form').find('#up_chart_account_id').val())
            ){
                formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
            }

            var acc_form = ['cpv', 'bpv']
            if (acc_form.includes(form_type)) {
                formData.account_id = that.parents('tr').find('.account_id').val();
            }
            var form_type_list = ['sale_invoice', 'sales_fee', 'sales_quotation'];
            if ($('form').find('#sales_contract_code').length != 0 && form_type_list.includes(form_type)) {
                formData.customer_id = $('#customer_id').val();
            }
            if ($('form').find('#supplier_name').length != 0 && form_type == 'purc_return') {
                formData.supplier_id = $('#supplier_id').val();
            }
            if(form_type == 'purc_order' && $('form').find('#supplier_id').val() != ""){
                formData.supplier_id = $('#supplier_id').val();
            }
            formData.val = that.val();
            currentRequest = $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend : function(){
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                type: 'POST',
                url: data_url,
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response['body'] != null) {
                        inLineHelp.html(response['body']);
                    }
                }
            });
        }
    // }
    if(e.which === 115){
        $('#inLineHelp').remove();
        var caseHelp = "";
        if (that.attr('id') == 'purchase_order') {
            funcPOModalHelp(e);
        }
        if (that.attr('id') == 'pd_barcode') {
            funcProductModalHelp(e);
        }
    }
    var helpNotOpen = false;
    if (e.which === 115 && helpNotOpen) { // F4
        $('#inLineHelp').remove();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var caseHelp = "";
        if (that.attr('id') == 'supplier_name') {
            caseHelp = 'supplierHelp';
        }
        if (that.attr('id') == 'pd_barcode') {
            caseHelp = 'productHelp';
        }
        if (that.attr('id') == 'purchase_order') {
            caseHelp = 'poHelp';
        }
        if (that.attr('id') == 'f_barcode') {
            caseHelp = 'productHelp';
        }

        if(that.attr('id') == 'multi_product'){
            var formData = {
                supplier_id: $('#supplier_id').val(),
            }
            var data_url = '/common/select-multiple-products';
        }

        $('#kt_modal_xl').modal('show').find('.modal-content').load('/common/help-open/' + caseHelp);
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });
        return false;
    }
    if (e.which === 40 && table_block.find('.inLineHelp').length != 0) {
        var inLineHelp = table_block.find('.inLineHelp');
        if (inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false) {
            inLineHelp.find('.data_tbody_row:eq(0)').addClass('selected_row');
        } else {
            var index = inLineHelp.find('.data_tbody_row.selected_row').index();
            var ww_index = index - 2;
            index = index - 1;
            inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
            inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
        }
        var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
        that.val(val);
    }
    if (e.which === 38 && table_block.find('.inLineHelp').length != 0) {
        var inLineHelp = table_block.find('.inLineHelp');
        if (inLineHelp.find('.data_tbody_row').hasClass('selected_row') == true) {
            var index = inLineHelp.find('.data_tbody_row.selected_row').index();
            var ww_index = index - 2;
            index = index - 3;
            inLineHelp.find('.data_tbody_row:eq(' + ww_index + ')').removeClass('selected_row');
            inLineHelp.find('.data_tbody_row:eq(' + index + ')').addClass('selected_row');
        }
        var val = inLineHelp.find('.data_tbody_row.selected_row>table>tbody>tr.data-dtl>td[data-view="show"]').text();
        that.val(val);
    }

    var mobileRequest = true;

    // if type barcode search open help
    if (table_block.find('#inLineHelp').length != 0 && that.val().length >= 3) {
        var notAllowKeyCode = [113, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46];
        if (that.val() != '' && !notAllowKeyCode.includes(e.keyCode)) {
            //  display_help(that,table_block,table_block__table);
            var inLineHelp = table_block.find('.inLineHelp');
            inLineHelp.find('.data_tbody_row').removeClass('selected_row');
            if (inLineHelp.find('.data_tbody_row').hasClass('selected_row') == false) {
                var data_url = that.attr('data-url');
                if( inLineHelp.find('.data_thead_row').attr('id') == 'productHelp'){
                    var url = data_url;
                }else{
                    var url = data_url + '/' + encodeURIComponent($(this).val());
                }
                var formData = {};
                formData.form_type = form_type;
                formData.val = $(this).val();
                var acc_form = ['cpv', '']
                if (acc_form.includes(form_type)) {
                    formData.account_id = that.parents('tr').find('.account_id').val();
                }
                var form_type_list = ['sale_invoice', 'sales_fee', 'sales_quotation'];
                if ($('form').find('#sales_contract_code').length != 0 && form_type_list.includes(form_type)) {
                    formData.customer_id = $('#customer_id').val();
                }
                if ($('form').find('#supplier_name').length != 0 && form_type == 'purc_return') {
                    formData.supplier_id = $('#supplier_id').val();
                }
                if(form_type == 'purc_order' && $('form').find('#supplier_id').val() != ""){
                    formData.supplier_id = $('#supplier_id').val();
                }
                if(form_type == 'pv'
                    && !valueEmpty($('form').find('#up_chart_account_id').val())
                ){
                    formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
                }
                if(form_type == 'rv'
                    && !valueEmpty($('form').find('#up_chart_account_id').val())
                ){
                    formData.supplier_chart_id = $('form').find('#up_chart_account_id').val();
                }
                currentRequest = $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: url,
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        if(currentRequest != null) {
                            currentRequest.abort();
                        }
                        if (table_block.find('#inLineHelp').find('.kt-inline-spinner').length == 0) {
                            table_block.find('#inLineHelp').prepend('<div class="kt-spinner kt-inline-spinner kt-spinner--lg kt-spinner--success kt-spinner-center" style="position: absolute;top: 0;z-index: 999;width: 100%;background: #f3f3f3;height: 100%;opacity: 0.5;"></div>');
                        }
                    },
                    success: function(response) {
                        if (response['body'] != null) {
                            inLineHelp.html(response['body']);
                        }
                    },
                    error: function() {
                        table_block.find('#inLineHelp').find('.kt-inline-spinner').remove();
                    }
                });
                $(document).ajaxStop(function(e, d) {
                    table_block.find('#inLineHelp').find('.kt-inline-spinner').remove();
                    $(document).unbind("ajaxStop");
                });
                //if(inLineHelp.length != 0){
                //inLineHelp.load(url);
                //}
                mobileRequest = false;
            }
        }
        if ($(window).width() <= 1024 && mobileRequest == true) {
            // display_help(that,table_block,table_block__table);
            var inLineHelp = table_block.find('.inLineHelp');
            var data_url = $(this).attr('data-url');
            var url = data_url + '/' + encodeURIComponent($(this).val());
            if (inLineHelp.length != 0) {
                inLineHelp.load(url);
            }
        }
    }
    if ($('.inLineHelp .data_tbody_row').hasClass('selected_row')) {
        $("#inLineHelp .inline_help_table").scrollTop(0); //set to top
        $("#inLineHelp .inline_help").scrollTop(0); //set to top
        var selected_row = $("#inLineHelp").find('.selected_row:first');
        if (selected_row.length != 0) {
            $("#inLineHelp .inline_help_table").scrollTop(selected_row.position().top - 150);
            $("#inLineHelp .inline_help").scrollTop(selected_row.position().top - 150);
        }
    }
});

if ($(window).width() <= 1024) {
    $(document).on('click', '#mobOpenInlineHelp', function(e) {
        $(document).find('#inLineHelp').remove();
        var thix = $(this);
        var that = thix.parents('th').find('.open_inline__help');
        var table_block = that.parents('.erp_form___block');
        var table_block__table = that.parents('.erp_form___block>.form_input__block');
        display_help(that, table_block, table_block__table);
        // load data
        var inLineHelp = table_block.find('.inLineHelp');
        var data_url = that.attr('data-url');
        if (inLineHelp.length != 0) {
            inLineHelp.load(data_url);
        }
    });
}
$(document).on('click', function(e) {
    if (!$(e.target).hasClass('open-inline-help')) {
        if ($(window).width() <= 1024) {
            $('#inLineHelp').hide();
        } else {
            $('#inLineHelp').remove();
        }
    }
    if ($(e.target).is('#mobOpenInlineHelp') || $(e.target).is('#mobOpenInlineHelp>i') ||
        $(e.target).is('#mobOpenInlineSupplierHelp') || $(e.target).is('#mobOpenInlineSupplierHelp>i')) {
        $('#inLineHelp').show();
    }
});
$(document).on('keydown', '.erp_form__grid .tb_moveIndex', function(e) {
    var bodyGrid = false;
    var body = $(this).parents('.erp_form__grid_body');
    var body_len = body.length;
    if(body_len == 1){
        bodyGrid = true
    }
    if (e.which === 13 && bodyGrid == false) {
        e.preventDefault();
        if($(this).hasClass('validNumber') && $(this).val() == ""){
            $(this).val("0");
        }
        if ($(this).hasClass('tb_moveIndexBtn')) {
            localStorage.setItem("addRow", 1);
            $(this).click();
            var addRow = localStorage.getItem("addRow");
            if ($(this).parents('thead').hasClass('erp_form__grid_header') && addRow != 2) {
                $(this).parents('.erp_form__grid_header').find('th:eq(1) input').focus();
            }
        } else {
            var index = $('.tb_moveIndex').index(this) + 1;
            $('.tb_moveIndex').eq(index).focus();
        }
    }

    // 40 arrow down
    if( (e.which === 40 && $('.erp_form___block').find('.inLineHelp').length == 0)
        || (e.which === 13 && bodyGrid)
    ){
        $(".kt_datepicker_3").datepicker("hide");
        var currentTd = $(this).parents('td').index();
        var next = $(this).parents('tr').closest('tr').next('tr')
        next.find('td:eq(' + currentTd + ')>input').focus();
    }
    // 38 arrow up
    if (e.which === 38 && $('.erp_form___block').find('.inLineHelp').length == 0) {
        $(".kt_datepicker_3").datepicker("hide");
        var currentTd = $(this).parents('td').index();
        var prev = $(this).parents('tr').closest('tr').prev('tr')
        prev.find('td:eq(' + currentTd + ')>input').focus();
    }
    // 37 arrow left
    if (e.which === 37 && $('.erp_form___block').find('.inLineHelp').length == 0 && bodyGrid) {
        $(".kt_datepicker_3").datepicker("hide");
        var index = $('.tb_moveIndex').index(this) - 1;
        $('.tb_moveIndex').eq(index).focus();
    }
    // 39 arrow right
    if (e.which === 39 && $('.erp_form___block').find('.inLineHelp').length == 0 && bodyGrid) {
        $(".kt_datepicker_3").datepicker("hide");
        var index = $('.tb_moveIndex').index(this) + 1;
        $('.tb_moveIndex').eq(index).focus();
    }
    /*if($('.erp_form___block').find('.inLineHelp').length == 0){
        $(".table-scroll").scrollTop(0); //set to top
        var focus_input = $(".tb_moveIndex__focus").parents('tr').height() * $(".tb_moveIndex__focus").parents('tr').index() + $('.erp_form__grid_header>tr').height();
        $(".table-scroll").scrollTop(focus_input - 84);

        $('.table-scroll>.JCLRgrips').css('top','0px');
        var JCLRgripsTop = $('.table-scroll').offset().top - $('.JCLRgrips').offset().top;
        JCLRgripsTop = JCLRgripsTop+'px';
        $('.table-scroll>.JCLRgrips').css('top', JCLRgripsTop);
    }*/
});
$(document).on('focusin', '.tb_moveIndex', function(e) {
    $(this).addClass('tb_moveIndex__focus');
    if($(this).parents('tr').length == 1){
        $(this).parents('tr').addClass('focus_selected_tr');
    }
    $(this).select();
}).on('focusout', '.tb_moveIndex', function(e) {
    $(this).removeClass('tb_moveIndex__focus');
    $('.erp_form__grid_body>tr').removeClass('focus_selected_tr');
});

$(document).on('focusin', 'input,select', function(e) {
    if($(this).parents('tr').length == 1){
        $(this).parents('tr').addClass('focus_selected_tr');
    }
}).on('focusout', 'input,select', function(e) {
    $('.erp_form__grid_body>tr').removeClass('focus_selected_tr');
});
$(document).on('mousewheel', '.table-scroll', function() {
    if($('.JCLRgrips').offset() !== undefined){
        $('.table-scroll>.JCLRgrips').css('top', '0px');
        var JCLRgripsTop = $('.table-scroll').offset().top - $('.JCLRgrips').offset().top;
        JCLRgripsTop = JCLRgripsTop + 'px';
        $('.table-scroll>.JCLRgrips').css('top', JCLRgripsTop);
    }
});

////////////////////////////////////////////////////////
