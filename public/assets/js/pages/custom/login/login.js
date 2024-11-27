"use strict";

// Class Definition
var KTLoginV1 = function () {
    var login = $('#kt_login');

    var showErrorMsg = function(form, type, msg) {
        var alert = $('<div class="alert alert-bold alert-solid-' + type + ' alert-dismissible" role="alert">\
			<div class="alert-text">'+msg+'</div>\
			<div class="alert-close">\
                <i class="flaticon2-cross kt-icon-sm" data-dismiss="alert"></i>\
            </div>\
		</div>');

        form.find('.alert').remove();
        alert.prependTo(form);
        KTUtil.animateClass(alert[0], 'fadeIn animated');
    }

    // Private Functions
    var handleSignInFormSubmit = function () {
        $('#kt_login_signin_submit').click(function (e) {
            e.preventDefault();

            var btn = $(this);
            var form = $('#kt_login_form');

            form.validate({
                rules: {
                    email: {
                        required: true
                    },
                    password: {
                        required: true
                    }
                }
            });

            if (!form.valid()) {
                return;
            }

            KTApp.progress(btn[0]);

            setTimeout(function () {
                KTApp.unprogress(btn[0]);
            }, 2000);

            // ajax form submit:  http://jquery.malsup.com/form/
            form.ajaxSubmit({
                url: '',
                success: function (response, status, xhr, $form) {
                    // similate 2s delay
                    window.location.href = '/home';
                }
            });
        });
    }

    var validator;

    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var formId = $( "#kt_branch_form" );
    var handleSelectBranch = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                branches: {
                    required: true,
                    valueNotEquals: ''
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
                        console.log("L: "+response.status);
                        if(response.status == 'success'){
                            window.location.href = '/home';
                        }else{

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

    // Public Functions
    return {
        // public functions
        init: function () {
            handleSignInFormSubmit();
            handleSelectBranch();
        }
    };
}();

// Class Initialization
jQuery(document).ready(function () {
    KTLoginV1.init();
});
