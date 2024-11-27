
function addDatasm(){
    $('#addDatasm').click(function(){
        for(var i=0;i<text_Fields.length;i++){
            var require = text_Fields[i]['require'];
            var val = $("#"+text_Fields[i]['id']).val();
            if (require == true && val == "") {
                alert('Enter Account Detail');
                return false;
            }
        }
        var total_length = $('#repeated_datasm>tr').length + 1;
        var tds = '';
        var hidden_input = '';
        for(var i=0;i<hidden_fields.length;i++){
            var name = hidden_fields[i];
            var val = $("#"+hidden_fields[i]).val();
            var classes = hidden_fields[i];
            hidden_input +='<input type="hidden" name="pdsm['+total_length+']['+name+']" data-id="'+name+'" value="'+val+'" class="'+classes+' form-control erp-form-control-sm handle" readonly>';
        }
        tds += '<td><input width="10%" type="text" value="'+total_length+'" name="pdsm['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm" readonly></td>';

        for(var i=0;i<text_Fields.length;i++){
            var name = text_Fields[i]['id'];
            var readonly = text_Fields[i]['readonly']==true?'readonly':'';
            var val = $("#"+name).val();
            var classes = text_Fields[i]['fieldClass'];
            tds += '<td><input type="text" name="pdsm['+total_length+']['+name+']" data-id="'+name+'" value="'+val+'" title="'+val+'" class="form-control erp-form-control-sm '+classes+'" '+readonly+'></td>';
        }
        $('#repeated_datasm').append('<tr>' + tds +
            '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group"><button type="button" class="btn btn-danger gridBtn delDatasm"><i class="la la-trash"></i></button></div></td>' +
            '</tr>');
        formClearsm();
        dataDeletesm();
        moveIndexsm();
        tdUpDownsm();
        TotalAmount();
        $('.validNumber').keypress(validateNumber);
        Masking();
        ChartCodeMasking();
    });
}
function formClearsm(){
    $('#dataEntryFormsm>td').find('input').val("");
    $('#dataEntryFormsm>td').find('input[type="radio"]').prop('checked', false);
    $('#dataEntryFormsm>td').find('select').prop('selectedIndex',0);
}
function dataDeletesm() {
    $(document).on('click' , '.delDatasm' , function(){
        $(this).parents("tr").remove();
        updateKeyssm();
        TotalAmount();
    });
}
function updateKeyssm(){
    var total_length = $('#repeated_datasm>tr').length + 1;
    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            var j = i+1;
            $($('#repeated_datasm tr:eq('+i+') td:eq(0)').find('input[type="hidden"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name','pdsm['+j+']['+data_id+']');
            });
            $($('#repeated_datasm tr:eq('+i+') td').find('input[type="text"]')).each(function(){
                var data_id = $(this).attr('data-id');
                $(this).attr('name','pdsm['+j+']['+data_id+']');
            });
            $('#repeated_datasm tr:eq('+i+') td:eq(0)').find('input[type="text"]').attr('name','pdsm['+j+'][sr_no]').attr('value',j).attr('title',j);
         }
    }
}
function tdUpDownsm(){
    $( "#repeated_datasm" ).sortable({
        handle: ".handle",
        update: function (e,ui) {
            updateKeyssm();
        }
    });
    $( "#repeated_datasm>tr" ).disableSelection();
}
$(document).ready(function(){
    addDatasm();
    updateKeyssm();
    dataDeletesm();
    tdUpDownsm();
    TotalAmount();
});
