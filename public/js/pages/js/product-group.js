// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#product_group_form" );
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                group_item_name: {
                    required: true,
                    maxlength:100
                },
                product_type_group_id: {
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
                var PG = $( "#parent_group_id" ).val();
                if(PG == 0){
                    swal.fire({
                        title: 'Alert?',
                        text: "Parent Group not select!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ok'
                    }).then(function(result) {
                        if (result.value) {
                            ajax_new()
                        }else{
                            $("form").find(":submit").prop('disabled', false);
                        }
                    });
                }
                if(PG != 0){
                    ajax_new()
                }
                function ajax_new(){
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
