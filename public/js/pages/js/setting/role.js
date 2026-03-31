// Class definition
var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#role_form");
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function() {

        validator = formId.validate({
            // define validation rules

            rules: {
                name: {
                    required: true,
                    maxlength:100
                },
                d_name: {
                    required: true,
                    maxlength:100
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
                var $form = $(form);
                $form.find(":submit").prop('disabled', true);
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
                        if(response.status == 'success'){
                            toastr.success(response.message);
                            setTimeout(function () {
                                $form.find(":submit").prop('disabled', false);
                            }, 2000);
                            window.location.href = response.data.redirect;
                        }else{
                            toastr.error(response.message);
                            setTimeout(function () {
                                $form.find(":submit").prop('disabled', false);
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.statusText || 'Request failed');
                        toastr.error(msg);
                        setTimeout(function () {
                            $form.find(":submit").prop('disabled', false);
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
