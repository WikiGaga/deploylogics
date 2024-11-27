// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#product_form" );
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    $.validator.addMethod("notEqual", function(value, element, param) {
        return this.optional(element) || $(param).not(element).get().every(function(item) {
            return $(item).val() != value;
        });
    }, "Please specify a different value");
    $.validator.addClassRules("barcode_repeat_b", {
        notEqual: ".barcode_repeat_b"
    });
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                product_name: {
                    required: true
                },
                product_control_group: {
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
                formId.find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response,status) {
                        console.log(response);
                        if(response.status == 'success'){
                            toastr.success(response.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                            if(response.data.form == 'edit'){
                                window.location.href = response.data.redirect;
                            }else{
                                location.reload();
                            }
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
    };

    return {
        // public functions
        init: function() {
            initValidation();
        }
    };
}();
function formRepeaterValidation(){
    $('[name*="v_product_barcode"]').each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Please enter a barcode"
            }
        });
    });
    $('[name*="uom_packing_uom"]').each(function() {
        $(this).rules('add', {
            required: true,
            valueNotEquals: "0",
            messages: {
                required: "Required - select an option."
            }
        });
    });
    $('[name*="product_barcode_packing"]').each(function() {
        $(this).rules('add', {
            required: true,
            valueNotEquals: "0",
            messages: {
                required: "This field is required."
            }
        });
    });
}
/*jQuery.validator.addMethod(
    "uom_packing_uom",
    function (value, element)
    {
        if (element.value === "0") {return false;}
        else {return true;}
    },
    "Required - select an option."
);*/
jQuery(document).ready(function() {
    KTFormWidgets.init();
    formRepeaterValidation();
});
