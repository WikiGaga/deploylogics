var KTFormWidgets = function() {
    var validator;
    var formId = $("#food_form");

    var initValidation = function() {
        validator = formId.validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 191
                },
                price: {
                    required: true,
                    min: 0.01
                }
            },
            submitHandler: function(form) {
                $("form").find(":submit").prop('disabled', true);
                var formData = new FormData(form);
                var el = this.submitButton;
                if (el) {
                    formData.append('action', el.getAttribute('data-id'));
                }
                if (!formData.has('status')) {
                    formData.append('status', '0');
                }
                if (!formData.has('is_halal')) {
                    formData.append('is_halal', '0');
                }
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
                            : 'Unable to save food item.';
                        toastr.error(message);
                        setTimeout(function() {
                            $("form").find(":submit").prop('disabled', false);
                        }, 1500);
                    }
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
    KTFormWidgets.init();
});
