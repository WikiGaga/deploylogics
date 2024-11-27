// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#rent_party_agreement_form" );
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                rent_agreement_amount:{
                    required: true,
                    maxlength:100,
                    valueNotEquals: "0"
                },
                rent_agreement_period:{
                    required: true,
                    maxlength:100,
                    valueNotEquals: "0"
                },
                rent_agreement_location: {
                    required: true,
                    valueNotEquals: "0"
                },
                rent_agreement_advance: {
                    required: true,
                    maxlength:100
                },
                first_party_id:{
                    required: true,
                    valueNotEquals: "0"
                },
                // first_party_cr:{
                //     required: true,
                // },
                // first_party_mobile: {
                //     required: true,
                // },
                second_party_id:{
                    required: true,
                    valueNotEquals: "0"
                },
                // second_party_cr:{
                //     required: true,
                // },
                // second_party_mobile: {
                //     required: true,
                // },
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

jQuery(document).ready(function() {
    KTFormWidgets.init();
});
