$(document).ready(function() {
    cheqbookHelp();
    ChartCodeMasking();
  /*  AccountHelp();*/
    checkHasValueInput_AddForm();
});

function checkHasValueInput_AddForm(){
    // when click on back btn in create form and edit form
    // if in form any input has value then confirmation from user
    $('.check_value').click(function(e){
        $('.checkHasValue').each(function(){
            var checkHasValue = $(this).val();
            if(checkHasValue){
                e.preventDefault();
                swal.fire({
                    title: 'Alert?',
                    text: "Do You Want To Go Back?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ok'
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = $(".back").attr('href');
                    }else{
                        return false;
                    }
                });
            }
        });
    });
}

function cheqbookHelp(){
    $('.cheqbookhelp').keydown(function (e) {
        var tr = $(this).parents('tr');
        var payment_mode = tr.find('td> .payment_mode').val();
        if($(this).val() == "" && payment_mode == 'cheque'){
            if(e.which === 13){
                $('#kt_modal_KTDatatable_local').modal({
                    show: true
                });
                $.ajax({
                    type:'GET',
                    url:'/common/cheqbook/',
                    data:{},
                    success: function(response, status){
                        $('#kt_modal_KTDatatable_local').find('.modal-content').html(response);

                        $('#account_datatable').on('click', 'tbody>tr', function (e) {
                            tr.find('td> .cheqbookhelp').val($(this).find('td:eq(0)').text());
                            tr.find('td:eq(5)>input').focus();

                            $('#kt_modal_KTDatatable_local').find('.modal-content').html('');
                            $('#kt_modal_KTDatatable_local').modal('hide');
                        });
                    }
                });
                $(this).parents('tr').find('td:eq(1)>input').focus();

            }
        }
    });

}


function ChartCodeMasking(){
    //$(".masking").keyup(function(){
    //    var value = $(this).val();
    //    var formatted = value.replace(/^(\d{1})(\d{2})(\d{2})(\d{4}).*/,"$1-$2-$3-$4");
    //    $(this).val(formatted);
    //});*/
    $(".masking").prop('maxLength', 12);
    $(".masking").keyup(function(){
        var strText = $(this).val();
        var formatted = '';
        if(strText.length == 1)
        {
            formatted = strText + '-' ;
            $(this).val(formatted);
        }
        if(strText.length == 4)
        {
            formatted = strText + '-' ;
            $(this).val(formatted);
        }
        if(strText.length == 7)
        {
            formatted = strText + '-' ;
            $(this).val(formatted);
        }
    });
}

function notNull(val){
    if(val == null || val == '' || val == NaN || val == undefined){
        return "";
    }else{
        return val;
    }
}
function notEmptyZero(val){
    if(val == null || val == '' || val == NaN || val == undefined){
        return 0;
    }else{
        return val;
    }
}
function notNullEmpty(val,deci){
    if(val == null || val == '' || val == NaN || val == undefined){
        return Number(0).toFixed(deci);
    }else{
        return math.round(Number(val) , deci);
    }
}
function JS_number_format(val){
    if(val == null || val == '' || val == NaN || val == undefined){
        return 0;
    }else{
        return parseInt(val);
    }
}
function isInt(n){
    var num = Number(n);
    return (num % 1) === 0;
}

function isFloat(n){
    var num = Number(n);
    return (num % 1) !== 0;
}
$(document).on('keydown','.moveIndex',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        $('.moveIndex').css({'border':''});
        $('.select2-container>.selection>.select2-selection').css({'border':''});
        var index = $('.moveIndex').index(this) + 1;
        $('.moveIndex').eq(index).focus();

        if($('.moveIndex').eq(index).prop('nodeName') !== undefined){
           if( $('.moveIndex').eq(index).prop('nodeName').toLowerCase() == 'select' && $('.moveIndex').eq(index).prop('type').toLowerCase() == 'select-one'){
               $('.moveIndex').eq(index).parent('.erp-select2-sm').find('.select2-container>.selection>.select2-selection').css({'border':'1px solid #9aabff'});
               $('.moveIndex').eq(index).parent('.erp-select2').find('.select2-container>.selection>.select2-selection').css({'border':'1px solid #9aabff'});
           }
           if( $('.moveIndex').eq(index).prop('nodeName').toLowerCase() == 'input' && $('.moveIndex').eq(index).prop('type').toLowerCase() == 'text'){
               $('.moveIndex').eq(index).css({'border':'1px solid #9aabff'});
           }
        }
        if($('.moveIndex').eq(index).prop('nodeName') == undefined){
            $('.tb_moveIndex').eq(0).focus();
        }
    }
});
$(document).on('click','.btn-minus-selected-data',function (e) {
    e.preventDefault();
    thix = $(this);
    thix.parents('.open-modal-group').find('input').val('').attr('title','');
    var block_id = thix.parents('.erp_form___block').attr('id');
    if(block_id == 'select_po' || block_id == 'select_lpo'){
        $('.erp_form__grid_body').html('');
        $('#pro_tot').val(notNullEmpty(0,3));
        $('.t_gross_total').text(notNullEmpty(0,3));
        if (typeof formClear() !== 'undefined'){
            formClear();
        }
        if (typeof TotalExpenseAmount !== 'undefined'){
            TotalExpenseAmount();
        }
    }
})

function toFixedNumber(num,deci){
    num = num.toString(); //If it's not already a String
    num = num.slice(0, (num.indexOf("."))+(deci+1)); //set digits after decimals
    var res = Number(num); //If you need it back as a Number
    return res.toFixed(3);
}
