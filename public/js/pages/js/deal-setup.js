// Class definition

var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#deal_steup_form")
    $.validator.addMethod("valueNotEquals", function(value, element, arg) {
        return arg !== value;
    }, "This field is required");
    var initValidation = function() {
        validator = formId.validate({
            // define validation rules
            rules: {
                store: {
                    required: true,
                    valueNotEquals: "0"
                },
                f_barcode: {
                    required: true,
                },
            },

            //display error alert on form submit
            invalidHandler: function(event, validator) {
                var alert = $('#kt_form_1_msg');
                alert.removeClass('kt--hide').show();
                KTUtil.scrollTo('m_form_1_msg', -200);
            },
            beforeSend: function(form) {
                swal.fire({
                    title: js_msg.entry_is_exits,
                    text: js_msg.are_you_sure_to_save_without_it,
                    type: 'warning',
                    showCancelButton: true,
                    showConfirmButton: true
                }).then(function(result) {
                    if (result.value) {
                        formClear()
                    }
                });
            },
            submitHandler: function(form) {
                $("form").find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                var formData = new FormData(form);
                var validate_form = ['str', 'st'];
                var form_type = $('#form_type').val();
                var ajaxValidate = 1;
                var title_msg = '';
                var title_text = '';
                if ($('#product_barcode_id').val()) {
                    ajaxValidate = 0;
                    title_msg = js_msg.entry_is_exits
                    title_text = js_msg.are_you_sure_to_save_without_it
                }
                if (validate_form.includes(form_type)) {
                    $('.erp_form__grid_body>tr').each(function() {
                        if ($(this).find('.tblGridCal_purc_rate').val() == 0 && form_type == 'st') {
                            ajaxValidate = 0;
                            title_msg = $(this).find('.pd_barcode').val();
                            title_text = js_msg.value_is_zero;
                            return false;
                        }
                        if ($(this).find('.tblGridCal_rate').val() == 0) {
                            ajaxValidate = 0;
                            title_msg = $(this).find('.pd_barcode').val();
                            title_text = js_msg.value_is_zero;
                            return false;
                        }
                    });
                }
                if (ajaxValidate == 0) {
                    swal.fire({
                        title: title_msg,
                        text: title_text,
                        type: 'warning',
                        showCancelButton: true,
                        showConfirmButton: true
                    }).then(function(result) {
                        if (result.value) {
                            ajaxFunc(form, formData);
                            formClear()
                        } else {
                            $("form").find(":submit").prop('disabled', false);
                            $('#pd_barcode').focus();
                        }
                    });
                } else {
                    ajaxFunc(form, formData);
                }
            }
        });
    }
    var ajaxFunc = function(form, formData) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: form.action,
            type: form.method,
            dataType: 'json',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response, status) {
                if (response.status == 'success') {
                    toastr.success(response.message);
                    setTimeout(function() {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                    if (response.data.form == 'new') {
                        window.location.href = response.data.redirect;
                    }else{
                        location.reload();
                    }
                } else {
                    toastr.error(response.message);
                    setTimeout(function() {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                }
            },
            error: function(response, status) {
                console.log(response.responseJSON);
                if(response.responseJSON !== null && response.responseJSON !== undefined){
                    toastr.error(response.responseJSON.message);
                }
                setTimeout(function() {
                    $("form").find(":submit").prop('disabled', false);
                }, 2000);
            },
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