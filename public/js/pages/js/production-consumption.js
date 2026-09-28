var KTFormWidgets = function() {
    var validator;
    var formId = $("#formulation_form");

    $.validator.addMethod("valueNotEquals", function(value, element, arg) {
        return arg !== value;
    }, "This field is required");

    var initValidation = function() {
        validator = formId.validate({
            rules: {
                record_date: {
                    required: true,
                },
                transfer_from: {
                    required: true,
                    valueNotEquals: '0',
                },
                transfer_to: {
                    required: true,
                    valueNotEquals: '0',
                },
                status: {
                    required: true,
                    valueNotEquals: '0',
                },
            },
            submitHandler: function(form) {
                if ($('.erp_form__grid_body tr').length === 0) {
                    toastr.error('Add at least one product line.');
                    return false;
                }

                $("form").find(":submit").prop('disabled', true);
                var formData = new FormData(form);
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
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            if (response.data.form === 'new') {
                                window.location.href = response.data.redirect;
                            } else {
                                $('.new-row').removeClass('new-row');
                            }
                        } else {
                            toastr.error(response.message);
                        }
                        setTimeout(function() {
                            $("form").find(":submit").prop('disabled', false);
                        }, 1500);
                    },
                    error: function(response) {
                        var message = response.responseJSON && response.responseJSON.message
                            ? response.responseJSON.message
                            : 'Unable to save record.';
                        toastr.error(message);
                        setTimeout(function() {
                            $("form").find(":submit").prop('disabled', false);
                        }, 1500);
                    },
                });
            }
        });
    };

    return {
        init: function() {
            initValidation();
        }
    };
}();

jQuery(document).ready(function() {
    if ($('#form_type').val() === 'production-consumption') {
        KTFormWidgets.init();
    }
});
