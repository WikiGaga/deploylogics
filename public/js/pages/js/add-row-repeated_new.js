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
    $('#addData').click(function(e){
        var thix = $(this);
        e.preventDefault();
        if (typeof data_po_selected !== 'undefined' && data_po_selected == 'multi'){
            var data_po_multi = true;
        }else{
            var data_po_multi = false;
        }
        var thix = $(this);
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
        var form_type_arr = ['pv','brpv','brv','bpv','crv','cpv','jv','obv','lv','lfv','display_rent_fee','rebate_invoice','wa_word','cheque_mangment','budget_form','product_item_tax'];
        var currentTable = thix.parents('table');
        if(!form_type_arr.includes(form_type)){
            var product_arr = [];
            var tr = currentTable.find('tbody.erp_form__grid_body>tr');
            tr.each(function( index ) {
                var product_id = $(this).find('td>.product_barcode_id').val();
                product_arr.push(product_id);
            });

            var new_product_id = $("#product_barcode_id").val();
            if(product_arr.includes(new_product_id)){
                if(form_type == 'sa'){barcodeFound = 0;}else{barcodeFound = 1;}
            }
        }
        if(data_po_multi){
            barcodeFound = 0;
        }
        
        if(barcodeFound == 1){
            $('#pd_barcode').focus();
            localStorage.setItem("addRow", 2);
            var auto_not_added = ['product_discount_setup','sale_report','change_rate'];
            if(!auto_not_added.includes(form_type)){
                swal.fire({
                    title: $('#pd_barcode').val(),
                    text: 'Barcode already exit in current table',
                    type: 'warning',
                    showConfirmButton:true,
                    showCancelButton: true,
                    confirmButtonText: 'Ok',
                    cancelButtonText: 'Cancel',
                    focusConfirm:true
                }).then(function(result){
                    if(result.value){
                        addRowData(thix)
                    }
                });
            }else{
                formClear();
            }

        }else{
            addRowData(thix)
        }
        data_po_multi = "";
        data_po_selected = "";
    });
}
function addRowData(thix){
    var currentTable = thix.parents('table');
    for(var i=0;i<text_Fields.length;i++){
        var require = text_Fields[i]['require'];
        var message = text_Fields[i]['message'];
        var val = currentTable.find("#"+text_Fields[i]['id']).val();
        if (require == true && val == "") {
            alert(message);
            return false;
        }

        if(text_Fields[i]['id'] == 'new_rate' && (val == 0 || !val.match(/^\d+/))){
            alert("New Rate not equal to zero");
            return false;
        }
    }
    if(form_name == 'lpo_generation'){
        var total_length = $('tr.product_tr_no').length + 1;
    }else{
        var total_length = currentTable.find('.erp_form__grid_body>tr').length + 1;
    }

    var tds = '';
    var hidden_input = '';
    for(var i=0;i<hidden_fields.length;i++){
        var name = hidden_fields[i];
        var val = $("#"+hidden_fields[i]).val();
        var classes = hidden_fields[i];
        hidden_input +='<input type="hidden" name="pd['+total_length+']['+name+']" data-id="'+name+'" value="'+val+'" class="'+classes+' form-control erp-form-control-sm" readonly>';
    }
    if(form_name == 'lpo_generation'){
        var product_tr_length = $('tr.product_tr_no').length;
        tds += '<td>' +
            '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm" readonly>'
            + hidden_input +
            '</td>';
    }else{
        tds += '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
            '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly>'
            + hidden_input +
            '</td>';
    }
    var rateClasses = thix.parents('tr').find('.tblGridCal_net_tp').attr('class');
    var arrSelect = [];
    let headerTr = $('.erp_form__grid_header>tr');
    for(var i=0;i<text_Fields.length;i++){
        if(text_Fields[i]['type'] == 'select'){
            var name = text_Fields[i]['id'];
            if(name == 'stock_location'){
                var index = $('#'+name).parent().parent().parent().index();
            }else{
                var index = headerTr.find('#'+name).parent().parent().index();
            }
            var clone = $('#'+name).clone();
            var classes = text_Fields[i]['fieldClass'] + ' form-control erp-form-control-sm';
            if(text_Fields[i]['convertType'] == 'input'){
                if(text_Fields[i]['getVal'] == 'text'){
                    var selected_val = thix.parents('tr').find('select#'+name+' option:selected').text();
                }else{
                    var selected_val = thix.parents('tr').find('select#'+name+' option:selected').val();
                }
                var readonly = text_Fields[i]['readonly']==true?'readonly':'';
                var hide = text_Fields[i]['skip']==true?'d-none':'';
                tds += '<td class="'+hide+'"><input type="text" name="pd['+total_length+']['+name+']" data-id="'+name+'" value="'+selected_val+'" title="'+selected_val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
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
            var hide = text_Fields[i]['skip']==true?'d-none':'';
            var val = $("#"+name).val();
            if(name == 'net_tp'){
                var classes = rateClasses;
            }else{
                var classes = text_Fields[i]['fieldClass'];
            }
            var data_url = text_Fields[i]['data-url']!=undefined?text_Fields[i]['data-url']:"";
            //val = val.replace('"', "'");
            tds += '<td class="'+hide+'"><input type="text" name="pd['+total_length+']['+name+']" data-id="'+name+'" data-url="'+data_url+'" value="'+val+'" title="'+val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
            //tds += "<td><input type='text' name='pd["+total_length+"]["+name+"]' data-id='"+name+"' data-url='"+data_url+"' value='"+val+"' title='"+val+"' class='form-control erp-form-control-sm "+classes+"' "+readonly+"></td>";
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

    if(form_name == 'lpo_generation'){
        currentTable.find('.erp_form__grid_body').append('<tr class="product_tr_no new-row">'+ td_and_action_btn  +'</tr>');
        for(var i=0; arrSelect.length > i; i++){
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')').html(arrSelect[i]['clone']);
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>select').val(arrSelect[i]['selected_val']);
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>select').attr('name','pd['+total_length+']['+arrSelect[i]["name"]+']');
            thix.parents('tr').find('td>select').html('<option>Select</option>');
        }
    }else{
        currentTable.find('.erp_form__grid_body').append('<tr class="new-row">'+ td_and_action_btn  +'</tr>');
        for(var i=0; arrSelect.length > i; i++){
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2').html(arrSelect[i]['clone']);
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').val(arrSelect[i]['selected_val']);
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').attr('name','pd['+total_length+']['+arrSelect[i]["name"]+']');
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').attr('class',arrSelect[i]["classes"]);
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').attr('data-id',arrSelect[i]["name"]);
            currentTable.find('.erp_form__grid_body>tr:last-child').find('td:eq('+arrSelect[i]['index']+')>.erp-select2>select').removeAttr('id');
        }
        for(var i=0;i<text_Fields.length;i++){
            if(text_Fields[i]['type'] == 'select' && text_Fields[i]['defaultValue'] != true){
              //  thix.parents('tr').find('.pd_uom').html('<option>Select</option>');
            }
        }
    }

    thix.parents('tr').find('.tblGridCal_net_tp').removeClass('tp_increase_color tp_decrease_color new_rate_color foc_item_color no_change_color')
    $(".table-scroll").scrollTop($('.erp_form__grid').height());
    addDataInit();
    thix.parents('tr').find('th:eq(1) input').focus();
    $('input').attr('autocomplete', 'off');
    if($(".date_inputmask").length >= 1 ){
        $(".date_inputmask").inputmask("99-99-9999", {
            "mask": "99-99-9999",
            "placeholder": "dd-mm-yyyy",
        });
    }
    // if(typeof funcAfterAddRow !== undefined){
    //     funcAfterAddRow();
    // }
}
function formClear(){
    $('.erp_form__grid_header').find('input').val("");
    $('.erp_form__grid_header').find('input[type="radio"]').prop('checked', false);
    $('.erp_form__grid_header').find('select').prop('selectedIndex',0);
    $('.erp_form__grid_header').find('.tblGridCal_net_tp').removeClass('tp_increase_color tp_decrease_color new_rate_color foc_item_color no_change_color')
    $('.erp_form__grid_header').find('.pd_uom').html('<option>Select</option>');
    $('#current_product_stock').val(0);
}
function dataDelete() {
    $(document).on('click' , '.delData' , function(){
        $(this).parents("tr").remove();
        dataDeleteInit();
    });
}
function updateKeys(){
    var body_length = $('.erp_form__grid_body').length;
    for(var z=0;z<body_length;z++){
        var current_body = $($('.erp_form__grid_body')[z]);
        var nameArr = current_body.parents('table').attr('data-prefix');
        if(valueEmpty(nameArr)){
            nameArr = 'pd';
        }
        if(form_name == 'lpo_generation'){
          //  var total_length = $('tr.product_tr_no').length + 1;
            var current_body_len = current_body.find('tr.product_tr_no').length;
        }else{
           // var total_length = $('.erp_form__grid_body>tr').length + 1;
            var current_body_len = current_body.find('tr').length;
        }

        for(var i=0;current_body_len > i; i++){
            if(form_name == 'lpo_generation'){
                // var td = '.erp_form__grid_body tr.product_tr_no:eq('+i+') td';
                var td = current_body.find('tr.product_tr_no:eq('+i+') td');
            }else{
                // var td = '.erp_form__grid_body tr:eq('+i+') td';
                var td = current_body.find('tr:eq('+i+') td');
            }
            var j = i+1;
            td.eq(0).find('input[type="hidden"]').each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name',nameArr+'['+j+']['+data_id+']');
            });
            td.find('input[type="text"]').each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',nameArr+'['+j+']['+data_id+']');
            });
            td.find('input[type="radio"]').each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',nameArr+'['+j+'][action]');
            });
            td.find('select').each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name',nameArr+'['+j+']['+data_id+']');
            });
            td.eq(0).find('input[type="text"]').attr('name',nameArr+'['+j+'][sr_no]').attr('value',j).attr('title',j);
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
    if (typeof allGridTotal !== 'undefined'){
        allGridTotal();
    }
    if (typeof funcSumGrnInvs !== 'undefined'){
        funcSumGrnInvs();
    }
}
function table_td_sortableInit(){
    updateKeys();
}
function addDataInit(){
    formClear();
    dataDelete();
    table_td_sortable();
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
    if (typeof funLocalStoragePV !== 'undefined'){
        funLocalStoragePV();
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
    if (typeof getValuesForDisc !== 'undefined'){
        getValuesForDisc(arr = {});
    }
}
function erpInit(){
    addData();
    updateKeys();
    dataDelete();
    table_td_sortable();
    if (typeof allGridTotal !== 'undefined'){
        allGridTotal();
    }
    if (typeof totalStockAmount !== 'undefined'){
        totalStockAmount();
    }
}
$(document).ready(function(){
    erpInit();
    $(document).on('blur','.date_inputmask',function(){
        var thix = $(this);
        var val = thix.val();
        if(!val){
            return;
        }
        if(val.length < 10){
            var newVal = "";
            var lastDigit = val.slice(-1)
            if(lastDigit == 2 || lastDigit == "2"){
                val = "0"+val;
            }
            newVal += val.substring(0,2)+"-";
            newVal += val.substring(2,4)+"-";
            newVal += val.substring(4,8);
            val = newVal;
        }
        var date = val.split("-");
        var d = parseInt(date[0]);
        var m = parseInt(date[1]);
        var y = parseInt(date[2]);

        y = (y < 1900 || y > 2100) ? new Date().getFullYear() : y;
        var leap_year = ((y%4) == 0)?true:false;

        var m_list = [1,2,3,4,5,6,7,8,9,10,11,12];
        m = (!m_list.includes(m)) ? 12 : m;
        m = (m < 10) ? ("0"+m) : m ;

        var days_30 = [4,6,9,11];
        var days_31 = [1,3,5,7,8,10,12];
        if(days_30.includes(m)){
            d = (d > 30) ? 30 : d;
        }
        if(days_31.includes(m)){
            d = (d > 31) ? 31 : d;
        }
        if(m == 2){
            if(leap_year){
                d = (d > 29) ? 29 : d;
            }else{
                d = (d > 28) ? 28 : d;
            }
        }
        d = (d < 10) ? ("0"+d) : d ;
        var final_date = d+"-"+m+"-"+y;
        thix.val(final_date)
    });

});
