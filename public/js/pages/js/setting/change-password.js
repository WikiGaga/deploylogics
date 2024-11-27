// Class definition

var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#password_form");
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function() {

        validator = formId.validate({
            // define validation rules

            rules: {
                new_password: {
                    required: function(){
                        if($('#new_password_pos').val() == "" && $('#new_password').val() == ""){
                            return true
                        }else{
                            $('#new_password_pos').removeClass('is-invalid');
                        }
                    },
                },
                conform_password: {
                    equalTo: "#new_password"
                },
                new_password_pos: {
                    required: function(){
                        if($('#new_password_pos').val() == "" && $('#new_password').val() == ""){
                            return true
                        }else{
                            $('#new_password').removeClass('is-invalid');
                        }
                    },
                },
                conform_password_pos: {
                    equalTo: "#new_password_pos"
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
                        console.log(response);
                        if(response.status == 'success'){
                            toastr.success(response.message);
                            $("form").find(":submit").prop('disabled', false);
                            window.location.href = response.data.redirect;
                        }else{
                            toastr.error(response.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                        }
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
