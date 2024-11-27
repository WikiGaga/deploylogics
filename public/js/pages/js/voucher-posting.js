// Class definition
/*
    jv
 */
var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#voucher_form");
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function() {

        validator = formId.validate({
            // define validation rules

            rules: {
                'pos_branch_ids[]': {
                    required: true,
                    valueNotEquals: "0"
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
                        if(response.status == 'success'){
                            swal.fire({
                                title: 'Success',
                                text: response.message,
                                type: 'success',
                                showCancelButton: false,
                                confirmButtonText: 'Ok'
                            }).then(function(result) {
                                if (result.value) {
                                    location.reload();
                                }
                            });
                            /*toastr.success(response.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                            if(response.data.form == 'edit'){
                                window.location.href = response.data.redirect;
                            }else{
                                location.reload();
                            }*/
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
