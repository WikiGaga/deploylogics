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
    $('#addData').click(function(){
        for(var i=0;i<text_Fields.length;i++){
            var require = text_Fields[i]['require'];
            var message = text_Fields[i]['message'];
            var val = $("#"+text_Fields[i]['id']).val();
            if (require == true && val == "") {
                alert(message);
                return false;
            }
        }
       
        var total_length = $('#repeated_data>tr').length + 1;
        var tds = '';
        var hidden_input = '';
        for(var i=0;i<hidden_fields.length;i++){
            var name = hidden_fields[i];
            var val = $("#"+hidden_fields[i]).val();
            var classes = hidden_fields[i];
            hidden_input +='<input type="hidden" name="pd['+total_length+']['+name+']" data-id="'+name+'" value="'+val+'" class="'+classes+' form-control erp-form-control-sm" readonly>';
        }
        tds += '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
            '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly>'
            + hidden_input +
            '</td>';
        var arrSelect = [];
        for(var i=0;i<text_Fields.length;i++){
            if(text_Fields[i]['type'] == 'select'){
                var name = text_Fields[i]['id'];
                var index = $('#'+name).parent().index();
                var clone = $('#'+name).clone();
                var selected_val = $(this).parents('tr').find('td>select#'+name).val();
                tds += '<td></td>';
                var arrOptions = {
                    "name": name,
                    "index": index,
                    "clone": clone,
                    "selected_val": selected_val,
                };
                arrSelect.push(arrOptions);

            }else{
                var name = text_Fields[i]['id'];
                var readonly = text_Fields[i]['readonly']==true?'readonly':'';
                var val = $("#"+name).val();
                var classes = text_Fields[i]['fieldClass'];
                var data_url = text_Fields[i]['data-url']!=undefined?text_Fields[i]['data-url']:"";
                tds += '<td><input type="text" name="pd['+total_length+']['+name+']" data-id="'+name+'" data-url="'+data_url+'" value="'+val+'" title="'+val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
            }
        }

        for(var i=0;i<radio_Fields.length;i++){
            var id = radio_Fields[i]['id'];
            if(radio_Fields[i]['checked']){
                var checked = radio_Fields[i]['checked']==true?'checked':'';
            }else{
                var checked = $('#'+id).is(":checked")==true?'checked':'';
            }
            if(radio_Fields[i]['value']){
                var val = radio_Fields[i]['value'];
            }else{
                var val = $("#"+id).val();
            }
            var labelClass = radio_Fields[i]['labelClass'];
            var inputClass = radio_Fields[i]['inputClass'];
            tds += '<td class="text-center"><label class="kt-radio '+labelClass+'"><input type="radio" class="'+inputClass+'" id="'+id+'" data-id="'+id+'" value="'+val+'" name="pd['+total_length+'][action]" '+checked+'><span></span></label></td>';
        }
        var td_and_action_btn = tds + '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>';
        $('#repeated_data').append('<tr>'+ td_and_action_btn  +'</tr>');
        for(var i=0; arrSelect.length > i; i++){
            $('#repeated_data>tr:last-child').find('td:eq('+arrSelect[i]['index']+')').html(arrSelect[i]['clone']);
            $('#repeated_data>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>select').val(arrSelect[i]['selected_val']);
            $('#repeated_data>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>select').attr('name','pd['+total_length+']['+arrSelect[i]["name"]+']');
            //$(this).parents('tr').find('td>select').html('<option>Select</option>');
        }
        addDataInit();
        $('input').attr('autocomplete', 'off');
    });
}
function formClear(){
    $('#dataEntryForm>td').find('input').val("");
    $('#dataEntryForm>td').find('input[type="radio"]').prop('checked', false);
    $('#dataEntryForm>td').find('select').prop('selectedIndex',0);
}
function dataDelete() {
    $(document).on('click' , '.delData' , function(){
        $(this).parents("tr").remove();
        dataDeleteInit();
    });
}
function updateKeys(){
    var total_length = $('#repeated_data>tr').length + 1;
    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            if(form_name == 'lpo_generation'){
                var td = '#repeated_data tr.product_tr_no:eq('+i+') td';
            }else{
                var td = '#repeated_data tr:eq('+i+') td';
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
            $(td+':eq(0)').find('input[type="text"]').attr('name','pd['+j+'][sr_no]').attr('value',j).attr('title',j);
        }
    }
}
function tdUpDown(){
    $( "#repeated_data" ).sortable({
        handle: ".handle",
        update: function (e,ui) {
            tdUpDownInit();
        }
    });
    $( "#repeated_data>tr" ).disableSelection();
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
}
function tdUpDownInit(){
    updateKeys();
}
function addDataInit(){
    formClear();
    dataDelete();
    moveIndex();
    tdUpDown();
    focusOnTableInput();
    datePicker();
    Masking();
    ChartCodeMasking();
    $('.validNumber').keypress(validateNumber);
    $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
    $('.OnlyEnterAllow').keypress(OnlyEnterAllow);
    if (typeof calcDC !== 'undefined'){
        calcDC();
    }
    if (typeof open_modal !== 'undefined'){
        open_modal();
    }
    if (typeof checkedAllInGrid !== 'undefined'){
        checkedAllInGrid();
    }
    if (typeof BudgetCalcFunc !== 'undefined'){
        BudgetCalcFunc();
    }
    $('input').attr('autocomplete', 'off');
}
function erpInit(){
    addData();
    updateKeys();
    dataDelete();
    tdUpDown();
    focusOnTableInput();
    Masking();
    ChartCodeMasking();
}
$(document).ready(function(){
    erpInit();
});
