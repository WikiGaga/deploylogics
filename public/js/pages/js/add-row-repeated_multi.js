//form_name = form_name variable
//text_fields =  keys = id, fieldClass,data-url, type, message, readonly(boolean), require(boolean), For Date add 'kt_datepicker_3' class
//radio_Fields =  keys = id, labelClass, inputClass, name, value, checked(boolean)
//hidden_field =  keys = [id_name,id_name,id_name,........]
if (typeof var_form_name !== 'undefined'){
    var form_name = var_form_name;
}else{
    var form_name = '';
}

if (typeof arr_hidden_field !== 'undefined'){
    var hidden_fields = arr_hidden_field;
}else{
    var hidden_fields = [];
}

if (typeof arr_text_Field !== 'undefined'){
    var text_Fields = arr_text_Field;
}else{
    var text_Fields = [];
}

if (typeof arr_radio_field !== 'undefined'){
    var radio_Fields = arr_radio_field;
}else{
    var radio_Fields = [];
}

function addData(){
    $(document).on('click','.addData',function(){
        var thix = $(this);
        var table = thix.parents('.erp_form__grid');
        var form_type = $('#form_type').val();
        /*if(form_type == 'brv' || form_type == 'bpv'){
            var len = $('.erp_form__grid>tbody.erp_form__grid_body>tr').length;
            if(len >= 1){
                $('.erp_form__grid>thead>tr').find('input').attr('disabled',true);
                $('.erp_form__grid>thead>tr').find('select').addClass('pointerEventsNone');
                $('.erp_form__grid>thead>tr').find('#addData').attr('disabled',true);
            }
        }*/
        var barcodeFound = 0;
        var form_type_arr = ['brv','bpv','crv','cpv','jv','obv','lv','lfv'];
        if(!form_type_arr.includes(form_type)){
            var product_arr = [];
            var tr = table.find('erp_form__grid_body>tr');
            tr.each(function( index ) {
                var product_id = $(this).find('td>.product_barcode_id').val();
                product_arr.push(product_id);
            });

            var new_product_id = thix.parents('tr').find("#product_barcode_id").val();
            if(product_arr.includes(new_product_id)){
                if(form_type == 'sa'){barcodeFound = 0;}else{barcodeFound = 1;}
            }
        }
        if(barcodeFound == 1){
            swal.fire({
                title: thix.parents('tr').find('#pd_barcode').val(),
                text: 'Barcode already exit in current table',
                type: 'warning',
                showCancelButton: true,
                showConfirmButton: true
            }).then(function(result){
                if(result.value){
                    addRowData(thix)
                }
            });
        }else{
            addRowData(thix)
        }
    });
}
function addRowData(thix){
    var table = thix.parents('.erp_form__grid');
    var trth = thix.parents('tr');
    var prefix = table.attr('prefix');
    for(var i=0;i<text_Fields.length;i++){
        var require = text_Fields[i]['require'];
        var message = text_Fields[i]['message'];
        var val = trth.find("#"+text_Fields[i]['id']).val();
        if (require == true && val == "") {
            alert(message);
            return false;
        }
    }
    if(form_name == 'lpo_generation'){
        var total_length = $('tr.product_tr_no').length + 1;
    }else{
        var total_length = table.find('.erp_form__grid_body>tr').length + 1;
    }

    var tds = '';
    var hidden_input = '';
    for(var i=0;i<hidden_fields.length;i++){
        var name = hidden_fields[i];
        var val = trth.find("#"+hidden_fields[i]).val();
        var classes = hidden_fields[i];
        hidden_input +='<input type="hidden" name="'+prefix+'['+total_length+']['+name+']" data-id="'+name+'" value="'+val+'" class="'+classes+' form-control erp-form-control-sm" readonly>';
    }
    if(form_name == 'lpo_generation'){
        var product_tr_length = $('tr.product_tr_no').length;
        tds += '<td>' +
            '<input type="text" value="'+total_length+'" name="'+prefix+'['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm" readonly>'
            + hidden_input +
            '</td>';
    }else{
        tds += '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
            '<input type="text" value="'+total_length+'" name="'+prefix+'['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly>'
            + hidden_input +
            '</td>';
    }
    var rateClasses = thix.parents('tr').find('.tblGridCal_rate').attr('class');
    var arrSelect = [];
    for(var i=0;i<text_Fields.length;i++){
        if(text_Fields[i]['type'] == 'select'){
            var name = text_Fields[i]['id'];
            if(name == 'stock_location'){
                var index = trth.find('#'+name).parent().parent().parent().index();
            }else{
                var index = trth.find('#'+name).parent().parent().index();
            }
            var clone = trth.find('#'+name).clone();
            var classes = text_Fields[i]['fieldClass'] + ' form-control erp-form-control-sm';
            if(text_Fields[i]['convertType'] == 'input'){
                if(text_Fields[i]['getVal'] == 'text'){
                    var selected_val = trth.find.find('select#'+name+' option:selected').text();
                }else{
                    var selected_val = trth.find.find('select#'+name+' option:selected').val();
                }
                var readonly = text_Fields[i]['readonly']==true?'readonly':'';
                tds += '<td><input type="text" name="'+prefix+'['+total_length+']['+name+']" data-id="'+name+'" value="'+selected_val+'" title="'+selected_val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
            }else{
                var selected_val = trth.find('select#'+name).val();
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
            var val = trth.find("#"+name).val();
            if(name == 'rate'){
                var classes = rateClasses;
            }else{
                var classes = text_Fields[i]['fieldClass'];
            }
            var data_url = text_Fields[i]['data-url']!=undefined?text_Fields[i]['data-url']:"";
            tds += '<td><input type="text" name="'+prefix+'['+total_length+']['+name+']" data-id="'+name+'" data-url="'+data_url+'" value="'+val+'" title="'+val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
        }
    }
    for(var i=0;i<radio_Fields.length;i++){
        var id = radio_Fields[i]['id'];
        if(radio_Fields[i]['checked']){
            var checked = radio_Fields[i]['checked']==true?'checked':'';
        }else{
            var checked = trth.find('#'+id).is(":checked")==true?'checked':'';
        }
        if(radio_Fields[i]['value']){
            var val = radio_Fields[i]['value'];
        }else{
            var val = trth.find("#"+id).val();
        }
        var labelClass = radio_Fields[i]['labelClass'];
        var inputClass = radio_Fields[i]['inputClass'];
        tds += '<td class="text-center"><label class="kt-radio '+labelClass+'"><input type="radio" class="'+inputClass+'" id="'+id+'" data-id="'+id+'" value="'+val+'" name="'+prefix+'['+total_length+'][action]" '+checked+'><span></span></label></td>';
    }
    var td_and_action_btn = tds + '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>';

    if(form_name == 'lpo_generation'){
        table.find('.erp_form__grid_body').append('<tr class="product_tr_no new-row">'+ td_and_action_btn  +'</tr>');
        for(var i=0; arrSelect.length > i; i++){
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')').html(arrSelect[i]['clone']);
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>select').val(arrSelect[i]['selected_val']);
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>select').attr('name',prefix+'['+total_length+']['+arrSelect[i]["name"]+']');
            thix.parents('tr').find('td>select').html('<option>Select</option>');
        }
    }else{
        table.find('.erp_form__grid_body').append('<tr class="new-row">'+ td_and_action_btn  +'</tr>');
        for(var i=0; arrSelect.length > i; i++){
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2').html(arrSelect[i]['clone']);
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').val(arrSelect[i]['selected_val']);
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').attr('name',''+prefix+'['+total_length+']['+arrSelect[i]["name"]+']');
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').attr('class',arrSelect[i]["classes"]);
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').removeAttr('id');
            table.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').removeAttr('data-id',arrSelect[i]["name"]);
        }
        if(form_name != 'bank_voucher' || form_name == ''){
            for(var i=0;i<text_Fields.length;i++){
                if(text_Fields[i]['type'] == 'select' && text_Fields[i]['defaultValue'] != true){
                    thix.parents('tr').find('select').html('<option>Select</option>');
                }
            }
        }
    }

    thix.parents('tr').find('.tblGridCal_rate').removeClass('grn_green grn_red grn_yellow')
    table.parents(".table-scroll").scrollTop(table.height());
    addDataInit();
    formClear(thix)
    var product_block = table.parents('.product_block');
    diffQty(product_block)
    thix.parents('tr').find('th:eq(1) input').focus();
    $('input').attr('autocomplete', 'off');
    if($(".date_inputmask").length >= 1 ){
        $(".date_inputmask").inputmask("99-99-9999", {
            "mask": "99-99-9999",
            "placeholder": "dd-mm-yyyy",
            autoUnmask: true
        });
    }
}
function formClear(thix){
    var table = thix.parents('.erp_form__grid');
    table.find('.erp_form__grid_header').find('input').val("");
    table.find('.erp_form__grid_header').find('input[type="radio"]').prop('checked', false);
    table.find('.erp_form__grid_header').find('select').prop('selectedIndex',0);
}
function dataDelete() {
    $(document).on('click' , '.delData' , function(){
        var thix = $(this);
        var tbody = thix.parents('.erp_form__grid_body');
        var product_block = thix.parents('.product_block');
        thix.parents("tr").remove();
        console.log(product_block);
        diffQty(product_block)
        updateKeysThix(tbody)
    });
}
function updateKeysThix(tbody){
    var table = tbody.parents('.erp_form__grid');
    var prefix = table.attr('prefix');

    if(form_name == 'lpo_generation'){
        var total_length = $('tr.product_tr_no').length + 1;
        //console.log('total_length: ' + total_length);
    }else{
        var total_length = tbody.find('tr').length + 1;
    }
    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            if(form_name == 'lpo_generation'){
                var td = 'tr.product_tr_no:eq('+i+') td';
            }else{
                var td = 'tr:eq('+i+') td';
            }
            var j = i+1;
            //console.log('j: ' + j);
            $(tbody.find(td+':eq(0)').find('input[type="hidden"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name',prefix+'['+j+']['+data_id+']');
            });
            $(tbody.find(td).find('input[type="text"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',prefix+'['+j+']['+data_id+']');
            });
            $(tbody.find(td).find('input[type="radio"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',prefix+'['+j+'][action]');
            });
            $(tbody.find(td).find('select')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',prefix+'['+j+']['+data_id+']');
            });
            tbody.find(td+':eq(0)').find('input[type="text"]').attr('name',prefix+'['+j+'][sr_no]').attr('value',j).attr('title',j);
        }
    }
}
function table_td_sortable(){
    $( ".erp_form__grid_body" ).sortable({
        handle: ".handle",
        update: function (e,ui) {
            var tbody = $(this);
            updateKeysThix(tbody);
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
function addDataInit(){
    dataDelete();
    table_td_sortable();
    datePicker();
    $('.validNumber').keypress(validateNumber);
    $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
    $('.OnlyEnterAllow').keypress(OnlyEnterAllow);
    $('.grid_select2').select2({
        placeholder: "Select",
    });
}
function erpInit(){
    addData();
    dataDelete();
    table_td_sortable();
}
$(document).ready(function(){
    erpInit();
});

$(document).on('keyup blur','.tblGridCal_qty',function(){
    var thix = $(this);
    var product_block = thix.parents('.product_block');
    diffQty(product_block)
});
function diffQty(product_block) {
    var vals = 0;
    product_block.find('.erp_form__grid_body>tr').each(function(){
        vals += parseFloat($(this).find('.tblGridCal_qty').val());
    })
    var total_qty = product_block.find('.total_qty').text();
    product_block.find('.purc_qty').html(vals);
    product_block.find('.input_purc_qty').val(parseFloat(vals).toFixed(3));
    var total_diff_qty = parseFloat(total_qty) - parseFloat(vals)
    product_block.find('.diff_qty').html(parseFloat(total_diff_qty).toFixed(3));
}
