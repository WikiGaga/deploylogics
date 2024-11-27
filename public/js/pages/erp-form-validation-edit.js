// Class definition
/*
    purchase -> brand
    purchase -> Manufacturer
    purchase -> Item Tags
    purchase -> Specification Tags
    Setting -> Country
    Setting -> City
    Setting -> Size
    Setting -> Origin
    Setting -> Color
    Setting -> UOM
    Development -> Action
    Development -> Flow
    Development -> Event
*/
var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $(".erp_form_validation");
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "Select any country!");
    var initValidation = function() {

        validator = formId.validate({
            // define validation rules
            rules: {
                name: {
                    required: true,
                    maxlength:200
                },
                city_country: {
                    required: true,
                    valueNotEquals: ""
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
            submitHandler: function(form) {
                formId.find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response, status) {
                        if (response.status == 200) {
                           // formId[0].reset();
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.history.back();
                                // javascript:history.go(-1);
                            }, 1000);
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
