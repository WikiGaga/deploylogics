// Class definition

var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#FormDisplay_form")
    var initValidation = function() {
        validator = formId.validate({
            // define validation rules
            rules: {
            },

            //display error alert on form submit
            invalidHandler: function(event, validator) {
                var alert = $('#kt_form_1_msg');
                alert.removeClass('kt--hide').show();
                KTUtil.scrollTo('m_form_1_msg', -200);
            },
            beforeSend: function(form) {

            },
            submitHandler: function(form) {
                $("#FormDisplay_form").find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response, status) {
                        if (response.status == 200) {
                            formId[0].reset();
                            toastr.success(response.message);
                            setTimeout(function() {
                                $("#FormDisplay_form").find(":submit").prop('disabled', false);
                            }, 2000);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                            setTimeout(function() {
                                formId.find(":submit").prop('disabled', false);
                            }, 2000);
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
