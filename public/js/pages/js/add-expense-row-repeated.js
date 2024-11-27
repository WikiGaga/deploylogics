var id_01 = 'voucher_sr_number';
var id_02 = 'account_code';
var id_03 = 'account_name';
var id_04 = 'expense_amount';
var account_id = 'account_id';
function addDatasm(){
    $('#addDatasm').click(function(){
        var total_length = $('#repeated_datasm>tr').length + 1;
        var val_01 = $("#"+id_01).val();
        var val_02 = $("#"+id_02).val();
        var val_03 = $("#"+id_03).val();
        var val_04 = $("#"+id_04).val();
        var account_id_val = $("#"+account_id).val();

        if (!val_02 || !val_03) {
            alert('Enter Account Details');
            return false;
        }

        $('#repeated_datasm').append('<tr>' +
            '<td class="handle"><i class="fa fa-arrows-alt-v handle"></i>' +
                '<input type="text" value="'+total_length+'" name="pdsm['+total_length+']['+id_01+']"  class="form-control erp-form-control-sm handle" readonly>'+
                '<input type="hidden" name="pdsm['+total_length+']['+account_id+']" value="'+account_id_val+'" class="acc_id form-control erp-form-control-sm handle" readonly>' +
            '</td>' +
            '<td><input type="text" name="pdsm['+total_length+']['+id_02+']" value="'+val_02+'" title="'+val_02+'" data-url="'+accountsHelpUrl+'" class="acc_code open_js_modal masking moveIndexsm validNumber form-control erp-form-control-sm text-left" maxlength="12"></td>' +
            '<td><input type="text" name="pdsm['+total_length+']['+id_03+']" value="'+val_03+'" title="'+val_03+'" class="acc_name form-control erp-form-control-sm" readonly ></td>' +
            '<td><input type="text" name="pdsm['+total_length+']['+id_04+']" value="'+val_04+'" title="'+val_04+'" class="expense_amount moveIndexsm form-control erp-form-control-sm validNumber" ></td>' +
            '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-danger gridBtn delDatasm"><i class="la la-trash"></i></button></div></td>' +
            '</tr>');
            formClearsm();
            dataDeletesm();
            tdUpDownsm();
            TotalExpenseAmount();
            $('.validNumber').keypress(validateNumber);
            Masking();
            ChartCodeMasking();
            open_modal();
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
        TotalExpenseAmount();
    });
}
function updateKeyssm(){
    var total_length = $('#repeated_datasm>tr').length + 1;
    if(total_length != 0){
        for(var i=0;total_length > i; i++){
            var j = i+1;
            $('#repeated_datasm tr:eq('+i+') td:eq(0)').find('input[type="text"]').attr('name','pdsm['+j+']['+id_01+']').attr('value',j);
            $($('#repeated_datasm tr:eq('+i+') td:eq(0)').find('input[type="hidden"]')).each(function(){
                //debugger
                var data_id = $(this).attr('data-id');
                $(this).attr('name','');
                $(this).attr('name','pdsm['+j+']['+data_id+']');
            });
            $('#repeated_datasm tr:eq('+i+') td:eq(1)').find('input').attr('name','pdsm['+j+']['+id_02+']');
            $('#repeated_datasm tr:eq('+i+') td:eq(2)').find('input').attr('name','pdsm['+j+']['+id_03+']');
            $('#repeated_datasm tr:eq('+i+') td:eq(3)').find('input').attr('name','pdsm['+j+']['+id_04+']');
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

        $('#dataEntryForm td:eq(0)').find('input').val(srn);
        $('#dataEntryForm td:eq(1)').find('input').val(td_1);
        $('#dataEntryForm td:eq(2)').find('input').val(td_2);
        $('#dataEntryForm td:eq(3)').find('input').val(td_3);
        $('#dataEntryForm td:eq(4)').find('input').val(td_4);
        $('#dataEntryForm td:eq(5)').html('<button type="button" class="btn btn-success btn-sm btn-padding updateData"><i class="la la-pencil"></i></button>');
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
        formClear();
        $('#dataEntryForm td:last-child').html('<button type="button" id="addData" class="btn btn-primary btn-padding btn-sm "> <i class="la la-plus"></i> </button>');
        $('#repeated_data tr td:last-child').find('button').attr('disabled', false)
        addData();
        TotalExpenseAmount();
    });
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
    dataEdit();
    updateKeyssm();
    dataDeletesm();
    TotalExpenseAmount();
    tdUpDownsm();
});


function TotalExpenseAmount()
{
    var tot_amount = 0;
    var gtot_amount = 0;
    var pro_toal = 0;
    for(var i=0; $('#repeated_datasm>tr').length>i;i++){
        var amount = $('#repeated_datasm').find("tr:eq("+i+")").find("td:eq(3)>input").val();
            amount = (amount == '' || amount == undefined)? 0 : amount.replace( /,/g, '');
        tot_amount = (parseFloat(tot_amount)+parseFloat(amount));

    }
    if($('#form_type').val() == 'grn'){
        var overall_disc_amount = $('#overall_disc_amount').val();
        tot_amount = tot_amount-parseFloat(overall_disc_amount);
    }
    pro_toal = $("#pro_tot").val();
    gtot_amount = (parseFloat(tot_amount)+parseFloat(pro_toal));
    tot_amount= tot_amount.toFixed(3);
    gtot_amount= gtot_amount.toFixed(3);
    $("#total_amountsm").html(gtot_amount);
    $("#tot_expenses").html(tot_amount);
    $("#TotExpen").val(tot_amount);
    $("#TotalAmtSM").val(gtot_amount);
}

$(document).on('keydown','.expense_amount',function (e) {
    TotalExpenseAmount();
});
$(document).on('keydown','.moveIndexsm',function (e) {
    if(e.which === 13){
        if($(this).hasClass('moveIndexBtnsm')){
            $('.moveIndexBtnsm').click();
            $('tr#dataEntryFormsm').find('td:eq(1)>input').focus();
        }else{
            var index = $('.moveIndexsm').index(this) + 1;
            $('.moveIndexsm').eq(index).focus();
        }
    }
    if(e.which === 40){
        var currentTd = $(this).parents('td').index();
        var next = $(this).parents('tr').closest('tr').next('tr')
        next.find('td:eq('+currentTd+')>input').focus();
    }
    if(e.which === 38){
        var currentTd = $(this).parents('td').index();
        var prev = $(this).parents('tr').closest('tr').prev('tr')
        prev.find('td:eq('+currentTd+')>input').focus();
    }
});
