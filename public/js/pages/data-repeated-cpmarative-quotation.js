var id_01 = 'sr_no';
var id_02 = 'barcode';
var id_03 = 'product_name';
var id_04 = 'uom';
var id_05 = 'packing';
var id_06 = 'quantity';
var id_07 = 'foc_qty';
var id_08 = 'fc_rate';
var id_09 = 'rate';
var id_10 = 'amount';
var id_11 = 'discount';
var id_12 = 'discount_val';
var id_13 = 'vat_perc';
var id_14 = 'vat_val';
var id_15 = 'gross_amount';
var id_16 = 'supplier_name';
var id_17 = 'approve';
var product_id = 'product_id';
var uom_id = 'uom_id';
var packing_id = 'packing_id';
var supplier_id = 'supplier_id';
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
        var val_17 = ($("input[id="+id_17+"]:checked").val()=='on')?'checked':"";
        var val_product_id = $("#"+product_id).val();
        var val_uom_id = $("#"+uom_id).val();
        var val_packing_id = $("#"+packing_id).val();
        var val_supplier_id = $("#"+supplier_id).val();

        if (!val_02) {
            alert('Enter Product Detail');
            return false;
        }

        $('#repeated_data').append('<tr>' +
            '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                '<input type="text" value="'+total_length+'" name="pd['+total_length+']['+id_01+']" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly>' +
                '<input type="hidden" name="pd['+total_length+']['+product_id+']" data-id="'+product_id+'" value="'+val_product_id+'" title="'+val_product_id+'" class="product_id form-control erp-form-control-sm handle" readonly>' +
                '<input type="hidden" name="pd['+total_length+']['+uom_id+']" data-id="'+uom_id+'" value="'+val_uom_id+'" title="'+val_uom_id+'" class="uom_id form-control erp-form-control-sm handle" readonly>' +
                '<input type="hidden" name="pd['+total_length+']['+packing_id+']" data-id="'+packing_id+'" value="'+val_packing_id+'" title="'+val_packing_id+'" class="packing_id form-control erp-form-control-sm handle" readonly>' +
                '<input type="hidden" name="pd['+total_length+']['+supplier_id+']" data-id="'+supplier_id+'" value="'+val_supplier_id+'" title="'+val_supplier_id+'" class="supplier_id form-control erp-form-control-sm handle" readonly>' +
                '</td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_16+']" value="'+val_16+'" title="'+val_16+'" class="supplierHelp moveIndex form-control erp-form-control-sm" ></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_02+']" value="'+val_02+'" title="'+val_02+'" class="productHelp moveIndex form-control erp-form-control-sm" ></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_03+']" value="'+val_03+'" title="'+val_03+'" class="product_name form-control erp-form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_04+']" value="'+val_04+'" title="'+val_04+'" class="uom form-control erp-form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_05+']" value="'+val_05+'" title="'+val_05+'" class="packing form-control erp-form-control-sm" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_06+']" value="'+val_06+'" title="'+val_06+'" class="tblGridCal_qty moveIndex form-control erp-form-control-sm validNumber validOnlyFloatNumber" ></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_07+']" value="'+val_07+'" title="'+val_07+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_08+']" value="'+val_08+'" title="'+val_08+'" class="form-control erp-form-control-sm validNumber" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_09+']" value="'+val_09+'" title="'+val_09+'" class="tblGridCal_rate moveIndex form-control erp-form-control-sm validNumber" ></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_10+']" value="'+val_10+'" title="'+val_10+'" class="tblGridCal_amount form-control erp-form-control-sm validNumber" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_11+']" value="'+val_11+'" title="'+val_11+'" class="tblGridCal_discount moveIndex form-control erp-form-control-sm validNumber" ></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_12+']" value="'+val_12+'" title="'+val_12+'" class="tblGridCal_discount_amount form-control erp-form-control-sm validNumber" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_13+']" value="'+val_13+'" title="'+val_13+'" class="tblGridCal_vat_perc moveIndex form-control erp-form-control-sm validNumber" ></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_14+']" value="'+val_14+'" title="'+val_14+'" class="tblGridCal_vat_amount form-control erp-form-control-sm validNumber" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+']['+id_15+']" value="'+val_15+'" title="'+val_15+'" class="tblGridCal_gross_amount form-control erp-form-control-sm validNumber" readonly></td>' + 
            '<td class="text-center"><label class="kt-radio kt-radio--brand"><input type="checkbox" value="on" class="moveIndex" name="pd['+total_length+']['+id_17+']" '+val_17+'><span></span></label></td>' +
            '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>' +
            '</tr>');
        formClear();
        dataDelete();
        dataEdit();
        moveIndex();
        allCalcFunc();
        tdUpDown();
        $('.validNumber').keypress(validateNumber);
        $('.validOnlyNumber').keypress(validateOnlyNumber);
        productHelp();
        SupplierHelp();
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
            var count = $('#repeated_data tr:eq('+i+') td:eq(0)').find('input[type="text"]').val()
            if(count !='' || count != 'undefined')
            {
            $('#repeated_data tr:eq('+i+') td:eq(0)').find('input[type="text"]').attr('name','pd['+j+']['+id_01+']').attr('value',count);
            $($('#repeated_data tr:eq('+i+') td:eq(0)').find('input[type="hidden"]')).each(function(){
                //debugger
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name','pd['+j+']['+data_id+']');
            });
            $('#repeated_data tr:eq('+i+') td:eq(1)').find('input').attr('name','pd['+j+']['+id_16+']');
            $('#repeated_data tr:eq('+i+') td:eq(2)').find('input').attr('name','pd['+j+']['+id_02+']');
            $('#repeated_data tr:eq('+i+') td:eq(3)').find('input').attr('name','pd['+j+']['+id_03+']');
            $('#repeated_data tr:eq('+i+') td:eq(4)').find('input').attr('name','pd['+j+']['+id_04+']');
            $('#repeated_data tr:eq('+i+') td:eq(5)').find('input').attr('name','pd['+j+']['+id_05+']');
            $('#repeated_data tr:eq('+i+') td:eq(6)').find('input').attr('name','pd['+j+']['+id_06+']');
            $('#repeated_data tr:eq('+i+') td:eq(7)').find('input').attr('name','pd['+j+']['+id_07+']');
            $('#repeated_data tr:eq('+i+') td:eq(8)').find('input').attr('name','pd['+j+']['+id_08+']');
            $('#repeated_data tr:eq('+i+') td:eq(9)').find('input').attr('name','pd['+j+']['+id_09+']');
            $('#repeated_data tr:eq('+i+') td:eq(10)').find('input').attr('name','pd['+j+']['+id_10+']');
            $('#repeated_data tr:eq('+i+') td:eq(11)').find('input').attr('name','pd['+j+']['+id_11+']');
            $('#repeated_data tr:eq('+i+') td:eq(12)').find('input').attr('name','pd['+j+']['+id_12+']');
            $('#repeated_data tr:eq('+i+') td:eq(13)').find('input').attr('name','pd['+j+']['+id_13+']');
            $('#repeated_data tr:eq('+i+') td:eq(14)').find('input').attr('name','pd['+j+']['+id_14+']');
            $('#repeated_data tr:eq('+i+') td:eq(15)').find('input').attr('name','pd['+j+']['+id_15+']');
            $('#repeated_data tr:eq('+i+') td:eq(16)').find('input').attr('name','pd['+j+']['+id_17+']');
        }
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
        $('#dataEntryForm td:last-child').html('<button type="button" class="btn btn-success btn-sm gridBtn updateData"><i class="la la-pencil"></i></button>');
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
        formClear();
        $('#dataEntryForm td:last-child').html('<button type="button" id="addData" class="btn btn-primary gridBtn btn-sm "> <i class="la la-plus"></i> </button>');
        $('#repeated_data tr td:last-child').find('button').attr('disabled', false)
        addData();
    });
}
function tdUpDown(){
    $( "#repeated_data" ).sortable({
        handle: ".handle",
        update: function (e,ui) {
            updateKeys();
        }
    });
    $( "#repeated_data>tr" ).disableSelection();
}
$(document).ready(function(){
    addData();
    dataEdit();
    updateKeys();
    dataDelete();
    tdUpDown();
});
