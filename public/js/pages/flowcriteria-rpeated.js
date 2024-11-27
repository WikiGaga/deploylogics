function addData(){
    $('#addData').click(function(){
        var total_length = $('#repeated_data>tr').length + 1;
        var menu_flow_criteria_dtl_field = $("#menu_flow_criteria_dtl_field :selected").val();
        var menu_flow_criteria_dtl_field_text = $("#menu_flow_criteria_dtl_field :selected").text();
        var menu_flow_criteria_dtl_operator = $("#menu_flow_criteria_dtl_operator :selected").val();
        var menu_flow_criteria_dtl_operator_text = $("#menu_flow_criteria_dtl_operator :selected").text();
        var menu_flow_criteria_dtl_value = $("#menu_flow_criteria_dtl_value").val();
        var menu_flow_criteria_dtl_operation = $("#menu_flow_criteria_dtl_operation :selected").val();
        if (!menu_flow_criteria_dtl_field && !menu_flow_criteria_dtl_operator && !menu_flow_criteria_dtl_value && !menu_flow_criteria_dtl_operation) {
            alert('Enter Product Detail');
            return false;
        }
        $('#repeated_data').append('<tr>' +
            '<td><input type="text" value="'+total_length+'" name="flow_criteria['+total_length+'][serial_number]"  class="form-control form-control-sm" readonly></td>' +
            '<td><span class="menu_flow_criteria_dtl_field_text">'+menu_flow_criteria_dtl_field_text+'</span><input type="hidden" value="'+menu_flow_criteria_dtl_field+'" name="menu_flow_criteria['+total_length+'][dtl_field]" class="form-control form-control-sm" readonly></td>' +
            '<td><span class="menu_flow_criteria_dtl_operator_text">'+menu_flow_criteria_dtl_operator_text+'</span><input type="hidden" value="'+menu_flow_criteria_dtl_operator+'" name="menu_flow_criteria['+total_length+'][dtl_operator]" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" value="'+menu_flow_criteria_dtl_value+'" name="menu_flow_criteria['+total_length+'][dtl_value]" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" value="'+menu_flow_criteria_dtl_operation+'" name="menu_flow_criteria['+total_length+'][dtl_operation]" class="form-control form-control-sm" readonly></td>' +
            '<td><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-success editData"><i class="la la-pencil"></i></button><button type="button" class="btn btn-danger delData" data-id="'+total_length+'"><i class="la la-trash"></i></button></div></td>' +
            '</tr>');
        formClear();
        dataDelete();
        dataEdit();
    });
}
function formClear(){
    $("#flow_criteria_sr_number").val("");
    $("#menu_flow_criteria_dtl_field").prop('selectedIndex',0);
    $("#menu_flow_criteria_dtl_operator").prop('selectedIndex',0);
    $("#menu_flow_criteria_dtl_value").val("");
    $("#menu_flow_criteria_dtl_operation").prop('selectedIndex',0);
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
            $('#repeated_data tr:eq('+i+') td:eq(0)').find('input').attr('name','flow_criteria_serial_number['+j+']').attr('value',j);
            $('#repeated_data tr:eq('+i+') td:eq(1)').find('input').attr('name','menu_flow_criteria_dtl_field['+j+']');
            $('#repeated_data tr:eq('+i+') td:eq(2)').find('input').attr('name','menu_flow_criteria_dtl_operator['+j+']');
            $('#repeated_data tr:eq('+i+') td:eq(3)').find('input').attr('name','menu_flow_criteria_dtl_value['+j+']');
            $('#repeated_data tr:eq('+i+') td:eq(4)').find('input').attr('name','menu_flow_criteria_dtl_operation['+j+']');
        }
    }
}
function dataEdit(){
    $('.editData').click(function(){
        //disabled all button
        $('#repeated_data tr td:last-child').find('button').attr('disabled', true)

        var tr = $(this).parents("tr");
        var srn =  tr.find('td:eq(0) input').val();
        var menu_flow_criteria_dtl_field =  tr.find('td:eq(1) input').val();
        var menu_flow_criteria_dtl_operator =  tr.find('td:eq(2) input').val();
        var menu_flow_criteria_dtl_value =  tr.find('td:eq(3) input').val();
        var menu_flow_criteria_dtl_operation =  tr.find('td:eq(4) input').val();

        $('#dataEntryForm td:eq(0)').find('input').val(srn);
        $('#dataEntryForm td:eq(1)').find('select').val(menu_flow_criteria_dtl_field);
        $('#dataEntryForm td:eq(2)').find('select').val(menu_flow_criteria_dtl_operator);
        $('#dataEntryForm td:eq(3)').find('input').val(menu_flow_criteria_dtl_value);
        $('#dataEntryForm td:eq(4)').find('select').val(menu_flow_criteria_dtl_operation);
        $('#dataEntryForm td:eq(5)').html('<button type="button" class="btn btn-success btn-sm updateData"><i class="la la-pencil"></i> Update</button>');
        dataUpdate();
    });
}
function dataUpdate(){
    $('.updateData').click(function(){
        var srn =  $('#flow_criteria_sr_number').val()
        var menu_flow_criteria_dtl_field = $("#menu_flow_criteria_dtl_field :selected").val();
        var menu_flow_criteria_dtl_field_text = $("#menu_flow_criteria_dtl_field :selected").text();
        var menu_flow_criteria_dtl_operator = $("#menu_flow_criteria_dtl_operator :selected").val();
        var menu_flow_criteria_dtl_operator_text = $("#menu_flow_criteria_dtl_operator :selected").text();
        var menu_flow_criteria_dtl_value = $("#menu_flow_criteria_dtl_operator").val();
        var menu_flow_criteria_dtl_operation = $("#menu_flow_criteria_dtl_operation :selected").val();

        var total_length = $('#repeated_data>tr').length + 1;
        var rowIndex = '';
        for (var i=0;total_length > i; i++){
            if($('#repeated_data tr:eq('+i+') td:first-child').find('input').val() == srn){
                rowIndex = i;
                var tr = $(this).parents("tr");
            }
        }
        $('#repeated_data tr:eq('+rowIndex+') td:eq(0)').find('input').val(srn);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(1)').find('span').html(menu_flow_criteria_dtl_field_text);;
        $('#repeated_data tr:eq('+rowIndex+') td:eq(1)').find('input').val(menu_flow_criteria_dtl_field);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(2)').find('input').val(menu_flow_criteria_dtl_operator_text);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(2)').find('input').val(menu_flow_criteria_dtl_operator);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(3)').find('input').val(menu_flow_criteria_dtl_value);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(4)').find('input').val(menu_flow_criteria_dtl_operation);
        formClear();
        $('#dataEntryForm td:last-child').html('<button type="button" id="addData" class="btn btn-primary btn-sm "> <i class="la la-plus"></i> Add </button>');
        $('#repeated_data tr td:last-child').find('button').attr('disabled', false)
        addData();
    });
}
$(document).ready(function(){
    addData();
});
