function addData(){
    $('#addData').click(function(){
        var table_id = $(this).parents("table").attr('id');
        var total_length = $(this).parents("table").find('tbody#repeated_data:last-child>tr').length + 1;
        var current_tr = $(this).parents("table").find('tbody>tr#dataEntryForm');
        var total_tds = $(this).parents("table").find('tbody>tr#dataEntryForm>td').length;
        var all_tds = "";
        for(var i=0; total_tds > i;i++){
            if(current_tr.find('td:first-child')) {
                var id = current_tr.find('td:eq('+i+')>input').attr('id');
                all_tds += '<td><input type="text" value="'+total_length+'" name="'+table_id+'['+total_length+']['+id+']"  class="form-control form-control-sm" readonly></td>'
            }else{
                if(current_tr.find('td:eq('+i+')>input["type=text"]')){
                    var val = current_tr.find('td:eq('+i+')>input').val();
                    var id = current_tr.find('td:eq('+i+')>input').attr('id');
                    all_tds += '<td><input type="text" value="'+val+'" name="'+table_id+'['+total_length+']['+id+']"  class="form-control form-control-sm" readonly></td>'
                }
                if(current_tr.find('td:eq('+i+')>input["type=radio"]')){
                    var id = current_tr.find('td:eq('+i+')>input').attr('id');
                    var checked = ($("input[id="+id+"]:checked").val()=='on')?'checked':"";
                    all_tds += '<td><label class="kt-radio kt-radio--brand"><input type="radio" name="'+table_id+'['+total_length+'][action]" '+checked+' onclick="this.checked = false;"><span></span></label></td>'
                }
                if(current_tr.find('td:last-child')){
                    all_tds += '<td><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-success btn-padding editData"><i class="la la-pencil"></i></button><button type="button" class="btn btn-danger btn-padding delData"><i class="la la-trash"></i></button></div></td>'
                }
            }
        }

        console.log("val: " );
        $('#repeated_data').append('<tr>'+all_tds+'</tr>');
        formClear();
        dataDelete();
        dataEdit();
    });
}
function formClear(){

}
function dataDelete() {
    $(document).on('click' , '.delData' , function(){
        $(this).parents("tr").remove();
        updateKeys();
    });
}
function updateKeys(){

}
function dataEdit(){
    $('.editData').click(function(){
        //disabled all button
        $('#repeated_data tr td:last-child').find('button').attr('disabled', true)

        dataUpdate();
    });
}
function dataUpdate(){
    $('.updateData').click(function(){

        formClear();
        $('#dataEntryForm td:last-child').html('<button type="button" id="addData" class="btn btn-primary btn-sm "> <i class="la la-plus"></i> Add </button>');
        $('#repeated_data tr td:last-child').find('button').attr('disabled', false)
        addData();
    });
}
$(document).ready(function(){
    addData();
});
