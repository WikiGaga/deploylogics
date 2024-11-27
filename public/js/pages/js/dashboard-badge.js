// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#dashboard_badges_form" )
    $.validator.addMethod("noSpace", function(value, element) { 
        return value.indexOf(" ") < 0; 
      }, "No space please");
      $.validator.addMethod("notEqual", function(value, element, param) {
        return this.optional(element) || $(param).not(element).get().every(function(item) {
            return $(item).val() != value;
        });
    }, "Please specify a different value");
    $.validator.addClassRules("badge_case_name", {
        notEqual: ".badge_case_name",
        noSpace: ".badge_case_name"
    });
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                dash_widget_name: {
                    required: true,
                    maxlength:100
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
function formRepeaterValidation(){
    $('[name*="dash_widget_badge_name"]').each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "This field is required."
            }
        });
    });
    $('[name*="dash_widget_case_name"]').each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "This field is required."
            }
        });
    });
    $('[name*="dash_widget_badge_query"]').each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "This field is required."
            }
        });
    });
}
jQuery(document).ready(function() {
    KTFormWidgets.init();
    formRepeaterValidation();
});
