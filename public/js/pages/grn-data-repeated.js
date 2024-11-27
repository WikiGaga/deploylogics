var id_01 = 'grn_sr_no';
var id_02 = 'grn_supplier_barcode';
var id_03 = 'grn_barcode';
var id_04 = 'grn_product';
var id_05 = 'grn_uom';
var id_06 = 'grn_packing';
var id_07 = 'grn_quantity';
var id_08 = 'grn_foc_qty';
var id_09 = 'grn_foc_rate';
var id_10 = 'grn_rate';
var id_11 = 'grn_amount';
var id_12 = 'grn_discount';
var id_13 = 'grn_discount_val';
var id_14 = 'grn_gst';
var id_15 = 'grn_vat_val';
var id_16 = 'grn_net_vat';
var id_17 = 'grn_batch_no';
var id_18 = 'grn_production_date';
var id_19 = 'grn_expiry_date';
var id_20 = 'grn_gross_amount';
function addData(){
    $('#addData').click(function(){
        var total_length = $('#repeated_data>tr').length + 1;
        var val_01 = $("#"+id_01).val();
        var val_02 = $("#"+id_02).val();
        var val_03 = $("#"+id_03).val();
        var val_04 = $("#"+id_04).val();
        var val_05 = $("#"+id_05).val();
        var val_06 = $("#"+id_06).val();
        var val_07 = $("#"+id_07).val();
        var val_08 = $("#"+id_08).val();
        var val_09 = $("#"+id_09).val();
        var val_10 = $("#"+id_10).val();
        var val_11 = $("#"+id_11).val();
        var val_12 = $("#"+id_12).val();
        var val_13 = $("#"+id_13).val();
        var val_14 = $("#"+id_14).val();
        var val_15 = $("#"+id_15).val();
        var val_16 = $("#"+id_16).val();
        var val_17 = $("#"+id_17).val();
        var val_18 = $("#"+id_18).val();
        var val_19 = $("#"+id_19).val();
        var val_20 = $("#"+id_20).val();

        if (!val_02) {
            alert('Enter Product Detail');
            return false;
        }

        $('#repeated_data').append('<tr>' +
            '<td><input type="text" value="'+total_length+'" name="pd['+total_length+']['+id_01+']"  class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_02+']" value="'+val_02+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_03+']" value="'+val_03+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_04+']" value="'+val_04+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_05+']" value="'+val_05+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_06+']" value="'+val_06+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_07+']" value="'+val_07+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_08+']" value="'+val_08+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_09+']" value="'+val_09+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_10+']" value="'+val_10+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_11+']" value="'+val_11+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_12+']" value="'+val_12+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_13+']" value="'+val_13+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_14+']" value="'+val_14+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_15+']" value="'+val_15+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_16+']" value="'+val_16+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_17+']" value="'+val_17+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_18+']" value="'+val_18+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_19+']" value="'+val_19+'" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_20+']" value="'+val_20+'" class="form-control form-control-sm" readonly></td>' +
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
            $('#repeated_data tr:eq('+i+') td:eq(0)').find('input').attr('name','pd['+j+']['+id_01+']').attr('value',j);
            $('#repeated_data tr:eq('+i+') td:eq(1)').find('input').attr('name','pd['+j+']['+id_02+']');
            $('#repeated_data tr:eq('+i+') td:eq(2)').find('input').attr('name','pd['+j+']['+id_03+']');
            $('#repeated_data tr:eq('+i+') td:eq(3)').find('input').attr('name','pd['+j+']['+id_04+']');
            $('#repeated_data tr:eq('+i+') td:eq(4)').find('input').attr('name','pd['+j+']['+id_05+']');
            $('#repeated_data tr:eq('+i+') td:eq(5)').find('input').attr('name','pd['+j+']['+id_06+']');
            $('#repeated_data tr:eq('+i+') td:eq(6)').find('input').attr('name','pd['+j+']['+id_07+']');
            $('#repeated_data tr:eq('+i+') td:eq(7)').find('input').attr('name','pd['+j+']['+id_08+']');
            $('#repeated_data tr:eq('+i+') td:eq(8)').find('input').attr('name','pd['+j+']['+id_09+']');
            $('#repeated_data tr:eq('+i+') td:eq(9)').find('input').attr('name','pd['+j+']['+id_10+']');
            $('#repeated_data tr:eq('+i+') td:eq(10)').find('input').attr('name','pd['+j+']['+id_11+']');
            $('#repeated_data tr:eq('+i+') td:eq(11)').find('input').attr('name','pd['+j+']['+id_12+']');
            $('#repeated_data tr:eq('+i+') td:eq(12)').find('input').attr('name','pd['+j+']['+id_13+']');
            $('#repeated_data tr:eq('+i+') td:eq(13)').find('input').attr('name','pd['+j+']['+id_14+']');
            $('#repeated_data tr:eq('+i+') td:eq(14)').find('input').attr('name','pd['+j+']['+id_15+']');
            $('#repeated_data tr:eq('+i+') td:eq(15)').find('input').attr('name','pd['+j+']['+id_16+']');
            $('#repeated_data tr:eq('+i+') td:eq(16)').find('input').attr('name','pd['+j+']['+id_17+']');
            $('#repeated_data tr:eq('+i+') td:eq(17)').find('input').attr('name','pd['+j+']['+id_18+']');
            $('#repeated_data tr:eq('+i+') td:eq(18)').find('input').attr('name','pd['+j+']['+id_19+']');
            $('#repeated_data tr:eq('+i+') td:eq(19)').find('input').attr('name','pd['+j+']['+id_20+']');
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
        var td_4 =  tr.find('td:eq(4) input').val();
        var td_5 =  tr.find('td:eq(5) input').val();
        var td_6 =  tr.find('td:eq(6) input').val();
        var td_7 =  tr.find('td:eq(7) input').val();
        var td_8 =  tr.find('td:eq(8) input').val();
        var td_9 =  tr.find('td:eq(9) input').val();
        var td_10 =  tr.find('td:eq(10) input').val();
        var td_11 =  tr.find('td:eq(11) input').val();
        var td_12 =  tr.find('td:eq(12) input').val();
        var td_13 =  tr.find('td:eq(13) input').val();
        var td_14 =  tr.find('td:eq(14) input').val();
        var td_15 =  tr.find('td:eq(15) input').val();
        var td_16 =  tr.find('td:eq(16) input').val();
        var td_17 =  tr.find('td:eq(17) input').val();
        var td_18 =  tr.find('td:eq(18) input').val();
        var td_19 =  tr.find('td:eq(19) input').val();

        $('#dataEntryForm td:eq(0)').find('input').val(srn);
        $('#dataEntryForm td:eq(1)').find('input').val(td_1);
        $('#dataEntryForm td:eq(2)').find('input').val(td_2);
        $('#dataEntryForm td:eq(3)').find('input').val(td_3);
        $('#dataEntryForm td:eq(4)').find('input').val(td_4);
        $('#dataEntryForm td:eq(5)').find('input').val(td_5);
        $('#dataEntryForm td:eq(6)').find('input').val(td_6);
        $('#dataEntryForm td:eq(7)').find('input').val(td_7);
        $('#dataEntryForm td:eq(8)').find('input').val(td_8);
        $('#dataEntryForm td:eq(9)').find('input').val(td_9);
        $('#dataEntryForm td:eq(10)').find('input').val(td_10);
        $('#dataEntryForm td:eq(11)').find('input').val(td_11);
        $('#dataEntryForm td:eq(12)').find('input').val(td_12);
        $('#dataEntryForm td:eq(13)').find('input').val(td_13);
        $('#dataEntryForm td:eq(14)').find('input').val(td_14);
        $('#dataEntryForm td:eq(15)').find('input').val(td_15);
        $('#dataEntryForm td:eq(16)').find('input').val(td_16);
        $('#dataEntryForm td:eq(17)').find('input').val(td_17);
        $('#dataEntryForm td:eq(18)').find('input').val(td_18);
        $('#dataEntryForm td:eq(19)').find('input').val(td_19);
        $('#dataEntryForm td:eq(20)').html('<button type="button" class="btn btn-success btn-sm btn-padding updateData"><i class="la la-pencil"></i></button>');
        dataUpdate();
    });
}
function dataUpdate(){
    $('.updateData').click(function(){
        var srn =  $("#"+id_01).val();
        var td_1 = $("#"+id_02).val();
        var td_2 = $("#"+id_03).val();
        var td_3 = $("#"+id_04).val();
        var td_4 = $("#"+id_05).val();
        var td_5 = $("#"+id_06).val();
        var td_6 = $("#"+id_07).val();
        var td_7 = $("#"+id_08).val();
        var td_8 = $("#"+id_09).val();
        var td_9 = $("#"+id_10).val();
        var td_10 = $("#"+id_11).val();
        var td_11 = $("#"+id_12).val();
        var td_12 = $("#"+id_13).val();
        var td_13 = $("#"+id_14).val();
        var td_14 = $("#"+id_15).val();
        var td_15 = $("#"+id_16).val();
        var td_16 = $("#"+id_17).val();
        var td_17 = $("#"+id_18).val();
        var td_18 = $("#"+id_19).val();
        var td_19 = $("#"+id_20).val();

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
        $('#repeated_data tr:eq('+rowIndex+') td:eq(4)').find('input').val(td_4);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(5)').find('input').val(td_5);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(6)').find('input').val(td_6);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(7)').find('input').val(td_7);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(8)').find('input').val(td_8);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(9)').find('input').val(td_9);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(10)').find('input').val(td_10);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(11)').find('input').val(td_11);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(12)').find('input').val(td_12);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(13)').find('input').val(td_13);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(14)').find('input').val(td_14);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(15)').find('input').val(td_15);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(16)').find('input').val(td_16);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(17)').find('input').val(td_17);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(18)').find('input').val(td_18);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(19)').find('input').val(td_19);
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
