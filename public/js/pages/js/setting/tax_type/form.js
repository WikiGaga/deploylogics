var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#tax_type_form");
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
                short_name: {
                    required: true,
                    maxlength:100
                },
                tax_type_percent: {
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
                $("form").find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                var formData = new FormData(form);
                var el = this.submitButton;
                formData.append('action', el.getAttribute('data-id'));
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
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                            if(response.data.form == 'new'){
                                window.location.href = response.data.redirect;
                            }else{
                                $('.new-row').removeClass('new-row');
                            }
                            /*if(response.data.form == 'new'){
                                toastr.success(response.message);
                                location.reload();
                            }
                            if(response.data.form == 'import'){
                                swal.fire({
                                    title: response.message,
                                    type: 'success',
                                    confirmButtonText: 'Yes'
                                }).then((result) => {
                                    if(result){
                                        location.reload();
                                    }
                                })
                            }*/
                        }else{
                            /*if(response.data.form == 'import'){
                                swal.fire({
                                    title:  'Error! on',
                                    text:   response.message,
                                    type: 'error',
                                    confirmButtonText: 'Yes'
                                });
                            }else{
                                toastr.error(response.message);
                            }*/
                            toastr.error(response.message);
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