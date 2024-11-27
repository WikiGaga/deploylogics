// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#sales_form" )
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                customer_name: {
                    required: true
                },
                sales_sales_man: {
                    required: true,
                    valueNotEquals: "0",
                },
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
                if($('#product_barcode_id').val()){
                    swal.fire({
                        title: js_msg.entry_is_exits,
                        text: js_msg.are_you_sure_to_save_without_it,
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
                    console.log(response);
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
