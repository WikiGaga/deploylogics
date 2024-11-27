// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#purchase_demand_form" )
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            //  debug: true,
            rules: {
                salesman: {
                    required: true,
                    valueNotEquals: "0"
                }
            },

            //display error alert on form submit
            invalidHandler: function(event, validator) {
                var alert = $('#kt_form_1_msg');
                alert.removeClass('kt--hide').show();
                KTUtil.scrollTo('m_form_1_msg', -200);
            },
            beforeSend: function(form) {

            },
            submitHandler: function (form) {
                $("form").find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                var formData = new FormData(form);

                var ajaxValidate = 1;
                var title_msg = '';
                var title_text = '';
                if($('#product_barcode_id').val()){
                    ajaxValidate = 0;
                    title_msg = js_msg.entry_is_exits
                    title_text = js_msg.are_you_sure_to_save_without_it
                }
                $('.erp_form__grid_body>tr').each(function(){
                    if($(this).find('.tblGridCal_rate').val() == 0){
                        ajaxValidate = 0;
                        title_msg =  $(this).find('.pd_barcode').val();
                        title_text = js_msg.value_is_zero;
                        return false;
                    }
                });
                if(ajaxValidate == 0){
                    swal.fire({
                        title: title_msg,
                        text: title_text,
                        type: 'warning',
                        showCancelButton: true,
                        showConfirmButton: true
                    }).then(function(result){
                        if(result.value){
                            ajaxFunc(form,formData);
                            formClear()
                        }else {
                            $("form").find(":submit").prop('disabled', false);
                            $('#pd_barcode').focus();
                        }
                    });
                }else{
                    ajaxFunc(form,formData);
                }

            }
        });
    }
    var ajaxFunc = function (form,formData){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url         : form.action,
            type        : form.method,
            dataType	: 'json',
            data        : formData,
            cache       : false,
            contentType : false,
            processData : false,
            success: function(response,status) {
                if(response.status == 'success'){
                    toastr.success(response.message);
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                    if(response.data.form == 'new'){
                        window.location.href = response.data.redirect;
                    }else{
                        $('.new-row').removeClass('new-row');
                    }
                }else{
                    toastr.error(response.message);
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                }
                $('#pd_barcode').focus();
            },
            error: function(response,status) {
                // console.log(response.responseJSON);
                toastr.error(response.responseJSON.message);
                setTimeout(function () {
                    $("form").find(":submit").prop('disabled', false);
                }, 2000);
            },
        });
    }
    return {
        // public functions
        init: function() {
            initValidation();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormWidgets.init();
});
