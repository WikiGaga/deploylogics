// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#lpo_form" )
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "Select any country!");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                lpo_currency: {
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
                $("#lpo_form").find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response,status) {
                        if(response.status == 200){
                            formId[0].reset();
                            toastr.success(response.message);
                            setTimeout(function () {
                                $("#lpo_form").find(":submit").prop('disabled', false);
                            }, 2000);
                            location.reload();
                        }else{
                            toastr.error(response.message);
                            $("#lpo_form").find(":submit").prop('disabled', false);
                        }

                    }
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
