"use strict";

// Class definition
var KTWizard1 = function () {
    // Base elements
    var wizardEl;
    var formEl;
    var validator;
    var wizard;

    // Private functions
    var initWizard = function () {
        // Initialize form wizard
        wizard = new KTWizard('kt_wizard_v1', {
            startStep: ACTIVE_STEP, // initial active step number
            clickableSteps: false  // allow step clicking
        });

        // Validation before going to next page
        wizard.on('beforeNext', function(wizardObj) {
            if (validator.form() !== true) {
                wizardObj.stop();  // don't go to the next step
            }
        });

        wizard.on('beforePrev', function(wizardObj) {
            if (validator.form() !== true) {
                wizardObj.stop();  // don't go to the next step
            }
        });

        // Change event
        wizard.on('change', function(wizard) {
            if(wizard.currentStep == 2){
                if($('.product_table .erp_form__grid_body>tr').length < 1){
                    $('#pd_barcode').focus();
                    toastr.error('Add Some Products');
                    wizard.goPrev();
                    return false;
                }
            }
            setTimeout(function() {
                KTUtil.scrollTop();
            }, 500);
        });
    }
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function() {
        validator = formEl.validate({
            // Validate only visible fields
            ignore: ":hidden",

            // Validation rules
            rules: {
                //= Step 1
                scheme_name: {
                    required: true,
                    maxlength:50
                },
            },

            // Display error
            invalidHandler: function(event, validator) {
                var alert = $('#kt_form_1_msg');
                alert.removeClass('kt--hide').show();
                KTUtil.scrollTo('m_form_1_msg', -200);
            },

            // Submit valid form
            submitHandler: function (form) {
            }
        });
    }

    var initSubmit = function() {
        var btn = formEl.find('[data-ktwizard-type="action-submit"]');

        btn.on('click', function(e) {
            e.preventDefault();

            var preRequired = $('.kt-margin-b-10.slab.p-3.border.repeater-container').find('input.noEmpty');
            var validatorSlabs = true;
            preRequired.each(element => {
                var ele = preRequired[element];
                if(ele.value == ""){
                    ele.focus();
                    validatorSlabs = false;
                    return false;
                }
            });

            if (validator.form() && validatorSlabs) {
                // See: src\js\framework\base\app.js
                KTApp.progress(btn);
                //KTApp.block(formEl);

                // See: http://malsup.com/jquery/form/#ajaxSubmit
                formEl.ajaxSubmit({
                    success: function(responseText, statusText, xhr) {
                        $("#kt_form").find(":submit").prop('disabled', true);
                        KTApp.unprogress(btn);
                        //KTApp.unblock(formEl);
                        if(responseText.status == 'success'){
                            toastr.success(responseText.message);
                            setTimeout(function () {
                                $("#kt_form").find(":submit").prop('disabled', false);
                            }, 2000);
                            if(responseText.data.form == 'new'){
                                window.location.href = responseText.data.redirect;
                            }else{
                                location.reload();
                            }
                        }else{
                            toastr.error(responseText.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                        }

                    },
                    error: function(response,status) {
                        toastr.error(response.responseJSON.message);
                        setTimeout(function () {
                            $("form").find(":submit").prop('disabled', false);
                        }, 2000);
                    },
                });
            }else{
                toastr.error('Fill All Required Fields');
            }
        });
    }

    return {
        // public functions
        init: function() {
            wizardEl = KTUtil.get('kt_wizard_v1');
            formEl = $('#kt_form');

            initWizard();
            initValidation();
            initSubmit();
        }
    };
}();

jQuery(document).ready(function() {
    KTWizard1.init();
});
