var cd = console.log;
if (typeof var_form_name !== 'undefined'){
    var form_name = var_form_name;
}else{
    var form_name = '';
}

if (typeof arr_radio_field !== 'undefined'){
    var radio_Fields = arr_radio_field;
}else{
    var radio_Fields = [];
}

var text_Fields = [];
var hidden_fields = [];

// $(".addData").keypress(function(e1){
//     if(e1.keyCode==13){          // if user is hitting enter
//         return false;
//     }
//  });


$(document).on('click' , '#sldtlAddData,#pdAddData' , function(e){
    event.stopPropagation();
    event.stopImmediatePropagation();
    // Only Mouse Click and Enter Key is accepted
    if($(this).data('type') == "product"){
        if (typeof arr_text_FieldProduct !== 'undefined'){
            text_Fields = arr_text_FieldProduct;
        }else{
            text_Fields = [];
        }
        if (typeof arr_hidden_field_product !== 'undefined'){
            hidden_fields = arr_hidden_field_product;
        }else{
            hidden_fields = [];
        }
    }
    if($(this).data('type') == "slabInfo"){
        if (typeof arr_text_FieldSlab !== 'undefined'){
            text_Fields = arr_text_FieldSlab;
        }else{
            text_Fields = [];
        }
        if (typeof arr_hidden_field_slab !== 'undefined'){
            hidden_fields = arr_hidden_field_slab;
        }else{
            hidden_fields = [];
        }
    }
    if($(this).data('type') == "slabdtl"){
        if (typeof arr_text_FieldSlabDtl !== 'undefined'){
            text_Fields = arr_text_FieldSlabDtl;
        }else{
            text_Fields = [];
        }
        if (typeof arr_hidden_field_slab_dtl !== 'undefined'){
            hidden_fields = arr_hidden_field_slab_dtl;
        }else{
            hidden_fields = [];
        }
    }
    // Button Refrence
    var thix = $(this);
    var barcodeFound = 0;
    // TODO: Check If there are any duplicate Values in The Grid
    var tr = $(this).closest('.table.erp_form__grid').find('tbody.erp_form__grid_body>tr');
    var current_barcode_id = $(this).closest('.table.erp_form__grid').find('thead.erp_form__grid_header .product_barcode_id').val();
    tr.each(function( index ) {
        var product_barcode_id = $(this).find('td>.product_barcode_id').val();
        if(product_barcode_id == undefined || product_barcode_id == ""){
            product_barcode_id = $(this).find('td>.sldtl_product_barcode_id').val();
        }
        // console.log("Prev barcode : " + product_barcode_id);
        if(product_barcode_id == current_barcode_id){
            barcodeFound = 1;
            return false;
        }
    });

    if(barcodeFound == 1){
        thix.closest('#pd_barcode').focus();
        localStorage.setItem("addRow", 2);
        swal.fire({
            title: $('#pd_barcode').val(),
            text: 'Duplicate Products are not allowed.',
            type: 'warning',
            showConfirmButton:true,
            showCancelButton: true,
            confirmButtonText: 'Ok',
            cancelButtonText: 'Cancel',
            focusConfirm:true
        }).then(function(result){
            if(result.value){
                //addRowData(thix);
            }
        });
    }else{
        addRowData(thix);
    }
});
    

function addRowData(thix){
    var prefix = thix.data('prefix');
    if(prefix == "sldtl"){
        var repeaterIndex = thix.closest('.kt-margin-b-10.slab.p-3.border').attr('item-id');
        var repeaterIdentidier = thix.closest('.kt-margin-b-10.slab.p-3.border').parent().attr('data-repeater-list');
        prefix = repeaterIdentidier + '[' + repeaterIndex + ']['+ prefix + ']'; 
    }
    var validator = true;
    // Validation
    var required_fields = thix.closest('tr#erp_form_grid_header_row').find('.required_field');
    required_fields.each((element) => {
        if(required_fields[element].value == ""){
            validator = false;
        }    
    });
    
    if(validator){
        var total_length = thix.closest('.table.erp_form__grid').find('.erp_form__grid_body>tr').length + 1;
        var tds = '';
        var hidden_input = '';
        for(var i=0;i<hidden_fields.length;i++){
            var name = hidden_fields[i];
            var val = thix.closest('tr').find("#"+hidden_fields[i]).val();
            var classes = hidden_fields[i];
            hidden_input +='<input type="hidden" data-id="'+name+'" name="'+prefix+'['+total_length+']['+name+']" data-id="'+name+'" value="'+val+'" class="'+classes+' form-control erp-form-control-sm" readonly>';
        }
        tds += '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
            '<input type="text" data-id="sr_no" value="'+total_length+'" name="'+prefix+'['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly>'
            + hidden_input +
            '</td>';
        var rateClasses = thix.closest('tr').find('.tblGridCal_rate').attr('class');
        var arrSelect = [];
        let headerTr = thix.closest('.erp_form__grid_header>tr');
        for(var i=0;i<text_Fields.length;i++){
            if(text_Fields[i]['type'] == 'select'){
                var name = text_Fields[i]['id'];
                var index = headerTr.find('#'+name).parent().parent().index();
                var clone = headerTr.find('#'+name).clone();
                var classes = text_Fields[i]['fieldClass'] + ' form-control erp-form-control-sm';
                if(text_Fields[i]['convertType'] == 'input'){
                    if(text_Fields[i]['getVal'] == 'text'){
                        var selected_val = thix.parents('tr').find('select#'+name+' option:selected').text();
                    }else{
                        var selected_val = thix.parents('tr').find('select#'+name+' option:selected').val();
                    }
                    var readonly = text_Fields[i]['readonly']==true?'readonly':'';
                    tds += '<td><input type="text" name="'+prefix+'['+total_length+']['+name+']" data-id="'+name+'" value="'+selected_val+'" title="'+selected_val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
                }else{
                    var selected_val = thix.parents('tr').find('select#'+name).val();
                    tds += '<td><div class="erp-select2"></div></td>';
                    var arrOptions = {
                        "name": name,
                        "index": index,
                        "clone": clone,
                        "classes": classes,
                        "selected_val": selected_val,
                    };
                    arrSelect.push(arrOptions);
                }
            }else{
                var name = text_Fields[i]['id'];
                var readonly = text_Fields[i]['readonly']==true?'readonly':'';
                var val = headerTr.find("#"+name).val();
                val = val.replace('"',"'");
                if(name == 'rate'){
                    var classes = rateClasses;
                }else{
                    var classes = text_Fields[i]['fieldClass'];
                }
                var data_url = text_Fields[i]['data-url']!=undefined?text_Fields[i]['data-url']:"";
                
                tds += '<td><input type="text" name="'+prefix+'['+total_length+']['+name+']" data-id="'+name+'" data-url="'+data_url+'" value="'+val+'" title="'+val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
            }
        }
        var td_and_action_btn = tds + '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>';
        thix.closest('.table.erp_form__grid').find('.erp_form__grid_body').append('<tr class="new-row">'+ td_and_action_btn  +'</tr>');
        for(var i=0; arrSelect.length > i; i++){
            console.log();
            thix.closest('.table.erp_form__grid').find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2').html(arrSelect[i]['clone']);
            thix.closest('.table.erp_form__grid').find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').val(arrSelect[i]['selected_val']);
            thix.closest('.table.erp_form__grid').find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').attr('name', prefix+'['+total_length+']['+arrSelect[i]['name']+']');
            thix.closest('.table.erp_form__grid').find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').attr('class',arrSelect[i]["classes"]);
            // thix.closest('.table.erp_form__grid').find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').removeAttr('id');
            // thix.closest('.table.erp_form__grid').find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').removeAttr('data-id',arrSelect[i]["name"]);
        }
        for(var i=0;i<text_Fields.length;i++){
            if(text_Fields[i]['type'] == 'select' && text_Fields[i]['defaultValue'] != true){
            }
        }
        

        $(".table-scroll").scrollTop($('.erp_form__grid').height());
        addDataInit();
        thix.closest('tr').find('th:eq(1) input').focus();
        $('input').attr('autocomplete', 'off');
        if($(".date_inputmask").length >= 1 ){
            $(".date_inputmask").inputmask("99-99-9999", {
                "mask": "99-99-9999",
                "placeholder": "dd-mm-yyyy",
                autoUnmask: true
            });
        }
    }else{
        // toastr.error("Please Enter Product Details");
    }
    $(".erp_form__grid_header #pd_barcode").attr('data-url' , PRODUCT_HELP_URL);
    thix.closest('tr').find('.pd_barcode').focus();
    return true;
}
function formClear(){
    $('.erp_form__grid_header').find('input').val("");
    $('.erp_form__grid_header').find('input[type="radio"]').prop('checked', false);
    $('.erp_form__grid_header').find('select').prop('selectedIndex',0);
    $('.erp_form__grid_header').find('.tblGridCal_rate').removeClass('grn_green grn_red grn_yellow')
    $('.erp_form__grid_header').find('.pd_uom').html('<option>Select</option>');
}
function dataDelete() {
    $(document).on('click' , '.delData' , function(){
        $(this).closest("tr").remove();
        // dataDeleteInit();
    });
}
function updateKeys(){
    
    if(form_name == 'lpo_generation'){
        var total_length = $('tr.product_tr_no').length + 1;
        //console.log('total_length: ' + total_length);
    }else{
        var total_length = $('.erp_form__grid_body>tr').length + 1;
    }

    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            if(form_name == 'lpo_generation'){
                var td = '.erp_form__grid_body tr.product_tr_no:eq('+i+') td';
            }else{
                var td = '.erp_form__grid_body tr:eq('+i+') td';
            }
            var j = i+1;
            //console.log('j: ' + j);
            $($(td+':eq(0)').find('input[type="hidden"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name','pd['+j+']['+data_id+']');
            });
            $($(td).find('input[type="text"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','pd['+j+']['+data_id+']');
            });
            $($(td).find('input[type="radio"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','pd['+j+'][action]');
            });
            $($(td).find('select')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','pd['+j+']['+data_id+']');
            });
            $(td+':eq(0)').find('input[type="text"]').attr('name','pd['+j+'][sr_no]').attr('value',j).attr('title',j);
        }
    }
}
function table_td_sortable(){
    $( ".erp_form__grid_body" ).sortable({
        handle: ".handle",
        update: function (e,ui) {
            table_td_sortableInit();
        }
    });
    $( ".erp_form__grid_body>tr" ).disableSelection();
}
function datePicker(){
    var arrows;
    if (KTUtil.isRTL()) {
        arrows = {
            leftArrow: '<i class="la la-angle-right"></i>',
            rightArrow: '<i class="la la-angle-left"></i>'
        }
    } else {
        arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    }

    $('.kt_datepicker_3, .kt_datepicker_3_validate').datepicker({
        rtl: KTUtil.isRTL(),
        todayBtn: "linked",
        autoclose: true,
        format: "dd-mm-yyyy",
        todayHighlight: true,
        templates: arrows
    });

}
/***************************
 *  init functions
 */
function dataDeleteInit(){
    updateKeys();
    if (typeof allCalcFunc !== 'undefined'){
        allCalcFunc();
    }
    if (typeof calcDC !== 'undefined'){
        calcDC();
    }
}
function table_td_sortableInit(){
    updateKeys();
}
function addDataInit(){
    formClear();
    dataDelete();
    // table_td_sortable();
    datePicker();
    $('.validNumber').keypress(validateNumber);
    $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
    $('.OnlyEnterAllow').keypress(OnlyEnterAllow);
    if (typeof allCalcFunc !== 'undefined'){
        allCalcFunc();
    }
    if (typeof open_modal !== 'undefined'){
        open_modal();
    }
    if (typeof checkedAllInGrid !== 'undefined'){
        checkedAllInGrid();
    }
    if (typeof updateHiddenFields !== 'undefined'){
        updateHiddenFields();
    }
    if (typeof calcDC !== 'undefined'){
        calcDC();
    }
    if (typeof TotalAmt !== 'undefined'){
        TotalAmt();
    }
    if (typeof changeRate !== 'undefined'){
        changeRate();
    }



    $('.grid_select2').select2({
        placeholder: "Select",
    });
    var form_type = $('#form_type').val();

    if (typeof allGridTotal !== 'undefined'){
        allGridTotal();
    }
    /*if(form_type == 'brv' || form_type == 'bpv'){
        $('.erp_form__grid>thead>tr').find('input').attr('disabled',true);
        $('.erp_form__grid>thead>tr').find('select').addClass('pointerEventsNone');
        $('.erp_form__grid>thead>tr').find('#addData').attr('disabled',true);
    }*/
   // colWidthResize()
    if (typeof totalStockAmount !== 'undefined'){
        totalStockAmount();
    }
}
function erpInit(){
    // addData();
    // updateKeys();
    dataDelete();
    // table_td_sortable();
    if (typeof allGridTotal !== 'undefined'){
        allGridTotal();
    }
    if (typeof totalStockAmount !== 'undefined'){
        totalStockAmount();
    }
}
$(document).ready(function(){
    erpInit();
});
