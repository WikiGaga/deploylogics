function addData(){
    $('#addData').click(function(){
        var total_length = $('#repeated_data>tr').length + 1;
        var product_life_country = $("#product_life_country :selected").val();
        var product_life_country_text = $("#product_life_country :selected").text();
        var product_life_period_type = $("#product_life_period_type :selected").val();
        var product_life_period = $("#product_life_period").val();
        if (!product_life_country && !product_life_period_type && !product_life_period ) {
            alert('Enter Product Detail');
            return false;
        }
        $('#repeated_data').append('<tr>' +
            '<td><input type="text" value="'+total_length+'" name="product_life['+total_length+'][serial_number]"  class="form-control form-control-sm" readonly></td>' +
            '<td><span class="product_life_country_text">'+product_life_country_text+'</span><input type="hidden" value="'+product_life_country+'" name="product_life['+total_length+'][country]" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" value="'+product_life_period_type+'" name="product_life['+total_length+'][period_type]" class="form-control form-control-sm" readonly></td>' +
            '<td><input type="text" value="'+product_life_period+'" name="product_life['+total_length+'][period]" class="form-control form-control-sm" readonly></td>' +
            '<td class="text-center"><div class="btn-group btn-group btn-group-sm" role="group" aria-label="..."><button type="button" class="btn btn-success editData gridBtn"><i class="la la-pencil"></i></button><button type="button" class="btn btn-danger delData gridBtn" data-id="'+total_length+'"><i class="la la-trash"></i></button></div></td>' +
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
            $('#repeated_data tr:eq('+i+') td:eq(0)').find('input').attr('name','product_life['+j+'][serial_number]').attr('value',j);
            $('#repeated_data tr:eq('+i+') td:eq(1)').find('input').attr('name','product_life['+j+'][country]');
            $('#repeated_data tr:eq('+i+') td:eq(2)').find('input').attr('name','product_life['+j+'][period_type]');
            $('#repeated_data tr:eq('+i+') td:eq(3)').find('input').attr('name','product_life['+j+'][period]');
        }
    }
}
function dataEdit(){
    $('.editData').click(function(){
        //disabled all button
        $('#repeated_data tr td:last-child').find('button').attr('disabled', true)

        var tr = $(this).parents("tr");
        var srn =  tr.find('td:eq(0) input').val();
        var product_life_country =  tr.find('td:eq(1) input').val();
        var product_life_period_type =  tr.find('td:eq(2) input').val();
        var product_life_period =  tr.find('td:eq(3) input').val();

        $('#dataEntryForm td:eq(0)').find('input').val(srn);
        $('#dataEntryForm td:eq(1)').find('select').val(product_life_country);
        $('#dataEntryForm td:eq(2)').find('select').val(product_life_period_type);
        $('#dataEntryForm td:eq(3)').find('input').val(product_life_period);
        $('#dataEntryForm td:eq(4)').html('<button type="button" class="btn btn-success btn-sm updateData gridBtn"><i class="la la-pencil-square-o"></i></button>');
        dataUpdate();
    });
}
function dataUpdate(){
    $('.updateData').click(function(){
        var srn =  $('#product_sr_number').val()
        var product_life_country = $("#product_life_country :selected").val();
        var product_life_country_text = $("#product_life_country :selected").text();
        var product_life_period_type = $("#product_life_period_type :selected").val();
        var product_life_period = $("#product_life_period").val();

        var total_length = $('#repeated_data>tr').length + 1;
        var rowIndex = '';
        for (var i=0;total_length > i; i++){
            if($('#repeated_data tr:eq('+i+') td:first-child').find('input').val() == srn){
                rowIndex = i;
                var tr = $(this).parents("tr");
            }
        }
        $('#repeated_data tr:eq('+rowIndex+') td:eq(0)').find('input').val(srn);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(1)').find('span').html(product_life_country_text);;
        $('#repeated_data tr:eq('+rowIndex+') td:eq(1)').find('input').val(product_life_country);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(2)').find('input').val(product_life_period_type);
        $('#repeated_data tr:eq('+rowIndex+') td:eq(3)').find('input').val(product_life_period);
        formClear();
        $('#dataEntryForm td:last-child').html('<button type="button" id="addData" class="btn btn-primary btn-sm gridBtn"> <i class="la la-plus"></i> </button>');
        $('#repeated_data tr td:last-child').find('button').attr('disabled', false)
        addData();
    });
}
$(document).ready(function(){
    addData();
});
