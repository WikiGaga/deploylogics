var id_01 = 'supplier_sr_number';
var id_02 = 'supplier_det_name';
var id_03 = 'supplier_det_contact_no';
var id_04 = 'supplier_det_address';
function addData(){
    $('#addData').click(function(){
        var total_length = $('#repeated_data>tr').length + 1;
        var val_01 = $("#"+id_01).val();
        var val_02 = $("#"+id_02).val();
        var val_03 = $("#"+id_03).val();
        var val_04 = $("#"+id_04).val();

        if (!val_02) {
            alert('some field are empty');
            return false;
        }

        $('#repeated_data').append('<tr>' +
            '<td><input type="text" value="'+total_length+'" name="sub_supplier['+total_length+']['+id_01+']"  class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="sub_supplier['+total_length+']['+id_02+']" value="'+val_02+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="sub_supplier['+total_length+']['+id_03+']" value="'+val_03+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="sub_supplier['+total_length+']['+id_04+']" value="'+val_04+'" class="form-control form-control-sm" readonly></td>' +
            '<td><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-success btn-padding editData"><i class="la la-pencil"></i></button><button type="button" class="btn btn-danger btn-padding delData"><i class="la la-trash"></i></button></div></td>' +
            '</tr>');
        formClear();
        dataDelete();
        dataEdit();
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
        updateKeys();
    });
}
function updateKeys(){
    var total_length = $('#repeated_data>tr').length + 1;
    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            var j = i+1;
            $('#repeated_data tr:eq('+i+') td:eq(0)').find('input').attr('name','sub_supplier['+j+']['+id_01+']').attr('value',j);
            $('#repeated_data tr:eq('+i+') td:eq(1)').find('input').attr('name','sub_supplier['+j+']['+id_02+']');
            $('#repeated_data tr:eq('+i+') td:eq(2)').find('input').attr('name','sub_supplier['+j+']['+id_03+']');
            $('#repeated_data tr:eq('+i+') td:eq(3)').find('input').attr('name','sub_supplier['+j+']['+id_04+']');
            }
    }
}
function dataEdit(){
    $('.editData').click(function(){
        //disabled all button
        $('#repeated_data tr td:last-child').find('button').attr('disabled', true)

        var tr = $(this).parents("tr");
        var srn =  tr.find('td:eq(0) input').val();
        var td_1 =  tr.find('td:eq(1) input').val();
        var td_2 =  tr.find('td:eq(2) input').val();
        var td_3 =  tr.find('td:eq(3) input').val();

        $('#dataEntryForm td:eq(0)').find('input').val(srn);
        $('#dataEntryForm td:eq(1)').find('input').val(td_1);
        $('#dataEntryForm td:eq(2)').find('input').val(td_2);
        $('#dataEntryForm td:eq(3)').find('input').val(td_3);
        $('#dataEntryForm td:eq(4)').html('<button type="button" class="btn btn-success btn-sm btn-padding updateData"><i class="la la-pencil"></i></button>');
        dataUpdate();
    });
}
function dataUpdate(){
    $('.updateData').click(function(){
        var srn =  $("#"+id_01).val();
        var td_1 = $("#"+id_02).val();
        var td_2 = $("#"+id_03).val();
        var td_3 = $("#"+id_04).val();

        var total_length = $('#repeated_data>tr').length + 1;
        var rowIndex = '';
        for (var i=0;total_length > i; i++){
            if($('#repeated_data tr:eq('+i+') td:first-child').find('input').val() == srn){
                rowIndex = i;
                var tr = $(this).parents("tr");
            }
        }
        $('#repeated_data tr:eq('+rowIndex+') td:eq(0)').find('input').val(srn);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(1)').find('input').val(td_1);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(2)').find('input').val(td_2);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(3)').find('input').val(td_3);
        formClear();
        $('#dataEntryForm td:last-child').html('<button type="button" id="addData" class="btn btn-primary btn-padding btn-sm "> <i class="la la-plus"></i> </button>');
        $('#repeated_data tr td:last-child').find('button').attr('disabled', false)
        addData();
    });
}
$(document).ready(function(){
    addData();
    dataEdit();
    updateKeys();
    dataDelete();
});

