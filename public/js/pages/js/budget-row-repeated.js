var id_01 = 'sr_no';
var id_02 = 'budget_branch_id';
var id_03 = 'budget_budgetart_position';
var id_04 = 'account_name';
var id_05 = 'budget_start_date';
var id_06 = 'budget_end_date';
var id_07 = 'budget_alert_type';
var id_08 = 'budget_planned_amount';
var id_09 = 'budget_practical_amount';
var id_10 = 'budget_achievement';
var account_id = 'account_id';

function addData(){
    $('#addData').unbind();
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
        var val_account_id= $("#"+account_id).val();

        var tr = '';
        var branchArray = [];
        $('#budget_branch_id>option').each(function(){
            branchArray.push({
                id: $( this ).val(),
                name: $( this ).text(),
            });
        });
        var alertArray = [];
        $('#budget_alert_type>option').each(function(){
            alertArray.push({
                id: $( this ).val(),
                name: $( this ).text(),
            });
        });

        if(val_04 == ""){
            alert("Add Account Name ");
            return false;
        }


        tr+='<tr>' +
            '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                '<input type="text" value="'+total_length+'" name="pd['+total_length+'][sr_no]" title="'+total_length+'" class="form-control erp-form-control-sm handle" readonly>' +
                '<input type="hidden" name="pd['+total_length+']['+account_id+']" data-id="'+account_id+'" value="'+val_account_id+'" title="'+val_account_id+'" class="account_id form-control erp-form-control-sm handle" readonly>' +
            '</td>' +
            '<td>'+
                '<select  name="pd['+total_length+'][budget_branch_id]" class="form-control erp-form-control-sm moveIndex">';
                for(var i=0;branchArray.length>i;i++){
                    tr+='<option value='+branchArray[i]['id']+' '+(val_02 == branchArray[i]['id']?"selected":"")+'>'+branchArray[i]['name']+'</option>';
                }
            tr+='</select>'+
            '</td>' +
            '<td><input type="text" name="pd['+total_length+'][budget_budgetart_position]" value="'+val_03+'" title="'+val_03+'" class=" form-control erp-form-control-sm moveIndex"></td>' +
            '<td><input type="text" name="pd['+total_length+'][account_name]" data-url="'+accountsHelpUrl+'" value="'+val_04+'" title="'+val_04+'" class="acc_name open_js_modal moveIndex OnlyEnterAllow form-control erp-form-control-sm"></td>' +
            '<td><input type="text" name="pd['+total_length+'][budget_start_date]" value="'+val_05+'" title="'+val_05+'" class="form-control form-control-sm kt_datepicker_3 moveIndex" readonly></td>' +
            '<td><input type="text" name="pd['+total_length+'][budget_end_date]" value="'+val_06+'" title="'+val_06+'" class="form-control form-control-sm kt_datepicker_3 moveIndex" readonly></td>' +
            '<td>'+
                '<select  name="pd['+total_length+'][budget_alert_type]" class="form-control erp-form-control-sm moveIndex">';
                for(var i=0;alertArray.length>i;i++){
                    tr+='<option value='+alertArray[i]['id']+' '+(val_07 == alertArray[i]['id']?"selected":"")+'>'+alertArray[i]['name']+'</option>';
                }
            tr+='</select>'+
            '</td>' +
            '<td><input type="text" name="pd['+total_length+'][budget_planned_amount]" value="'+val_08+'" title="'+val_08+'" class="planned_amount moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>' +
            '<td><input type="text" name="pd['+total_length+'][budget_practical_amount]" value="'+val_09+'" title="'+val_09+'" class="practical_amount moveIndex validNumber validOnlyFloatNumber form-control erp-form-control-sm"></td>' +
            '<td><input type="text" name="pd['+total_length+'][budget_achievement]" value="'+val_10+'" title="'+val_10+'" class="achievement_amount moveIndex form-control erp-form-control-sm text-right" readonly></td>' +
            '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delData"><i class="la la-trash"></i></button></div></td>' +
            '</tr>';
            $('#repeated_data').append(tr);
        formClear();
        dataDelete();
        moveIndex();
        allCalcFunc();
        $('.validNumber').keypress(validateNumber);
        $('.validOnlyFloatNumber').keypress(validateOnlyFloatNumber);
        open_modal();
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
        demandTotal();
    });
}
function updateKeys(){
    var total_length = $('#repeated_data>tr').length + 1;
    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            var j = i+1;
            $('#repeated_data tr:eq('+i+') td:eq(0)').find('input[type="text"]').attr('name','pd['+j+']['+id_01+']').attr('value',j);
            $($('#repeated_data tr:eq('+i+') td:eq(0)').find('input[type="hidden"]')).each(function(){
                //debugger
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name','pd['+j+']['+data_id+']');
            });
            $('#repeated_data tr:eq('+i+') td:eq(1)').find('input').attr('name','pd['+j+']['+id_02+']');
            $('#repeated_data tr:eq('+i+') td:eq(2)').find('input').attr('name','pd['+j+']['+id_03+']');
            $('#repeated_data tr:eq('+i+') td:eq(3)').find('input').attr('name','pd['+j+']['+id_04+']');
            $('#repeated_data tr:eq('+i+') td:eq(4)').find('input').attr('name','pd['+j+']['+id_05+']');
            $('#repeated_data tr:eq('+i+') td:eq(5)').find('input').attr('name','pd['+j+']['+id_06+']');
            $('#repeated_data tr:eq('+i+') td:eq(6)').find('input').attr('name','pd['+j+']['+id_07+']');
            $('#repeated_data tr:eq('+i+') td:eq(7)').find('input').attr('name','pd['+j+']['+id_08+']');
            $('#repeated_data tr:eq('+i+') td:eq(8)').find('input').attr('name','pd['+j+']['+id_09+']');
            $('#repeated_data tr:eq('+i+') td:eq(9)').find('input').attr('name','pd['+j+']['+id_10+']');
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
        $('#dataEntryForm td:eq(13)').html('<button type="button" class="btn btn-success btn-sm btn-padding updateData"><i class="la la-pencil"></i></button>');
        dataUpdate();
    });
}
function dataUpdate(){
    $('.updateData').click(function(){
        var srn =  id_01.val()
        var td_1 = id_02.val();
        var td_2 = id_03.val();
        var td_3 = id_04.val();
        var td_4 = id_05.val();
        var td_5 = id_06.val();
        var td_6 = id_07.val();
        var td_7 = id_08.val();
        var td_8 = id_09.val();
        var td_8 = id_10.val();

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
        formClear();
        $('#dataEntryForm td:last-child').html('<button type="button" id="addData" class="btn btn-primary btn-padding btn-sm "> <i class="la la-plus"></i> </button>');
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
function TotalAmt(){
    var t = 0;
    var v = 0;
    $( "#repeated_data>tr" ).each(function( index ) {
        v = $(this).find('td>.achievement_amount').val();
        t += parseFloat(v);
    });
    $('.t_gross_total').html(t);
}
function allCalcFunc(){
    $(".planned_amount").keyup(function(){
        var tr = $(this).parents('tr');
        AchvAmount(tr);
        TotalAmt();
    });
    $(".practical_amount").keyup(function(){
        var tr = $(this).parents('tr');
        AchvAmount(tr);
        TotalAmt();
    });
    tdUpDown();
    TotalAmt();
    addData();
    dataEdit();
    updateKeys();
    dataDelete();
    focusOnTableInput();
    datePicker();
}
$(document).ready(function(){
    allCalcFunc();
});
