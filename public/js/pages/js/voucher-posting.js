// Class definition
/*
    jv
 */
var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#voucher_form");
    var ingredientForm = $("#ingredient_usage_form");
    var ingredientFeedback = $("#ingredient_usage_feedback");
    var hasValidator = typeof $.validator !== "undefined" && typeof $.validator.addMethod === "function";

    if (hasValidator) {
        $.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg !== value;
        }, "This field is required");
    } else {
        console.warn('[IngredientUsage] jQuery validation plugin not found; skipping voucher form validation setup.');
    }
    var initValidation = function() {
        if (!hasValidator || !formId.length) {
            return;
        }

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

    var formatDateToYMD = function (value) {
        if (!value) {
            return "";
        }
        var parts = value.split("-");
        if (parts.length !== 3) {
            return "";
        }
        return parts[2] + "-" + parts[1] + "-" + parts[0];
    };

    var bindIngredientSync = function () {
        var triggerButton = $("#ingredient_usage_sync_btn");
        var branchSelect = $("#ingredient_branch_id");

        if (!triggerButton.length || !ingredientForm.length) {
            console.warn('[IngredientUsage] Required elements not found; skipping sync binding.');
            return;
        }

        console.log('[IngredientUsage] bindIngredientSync initialized');

        if (branchSelect.length && $.fn.select2) {
            branchSelect.select2({
                placeholder: branchSelect.data('placeholder') || 'Select branch',
                width: '100%'
            });
        }

        triggerButton.off("click").on("click", function () {
            console.log('[IngredientUsage] Sync button clicked');
            var dateFromRaw = ingredientForm.find('[name="ingredient_date_from"]').val();
            var dateToRaw = ingredientForm.find('[name="ingredient_date_to"]').val();
            var branchId = ingredientForm.find('#ingredient_branch_id').val();

            if (!dateFromRaw || !dateToRaw) {
                toastr.error('Please choose both dates before syncing ingredient usage.');
                return;
            }

            if (!branchId) {
                toastr.error('Please choose a branch before syncing ingredient usage.');
                return;
            }

            var dateFrom = formatDateToYMD(dateFromRaw);
            var dateTo = formatDateToYMD(dateToRaw);

            if (!dateFrom || !dateTo) {
                toastr.error('Invalid date format. Please use the date picker to select valid dates.');
                return;
            }

            var token = window.apiAccessToken || $('meta[name="jwt-token"]').attr('content');

            if (!token) {
                toastr.error('Missing API token. Please refresh the page or contact the administrator.');
                return;
            }

            triggerButton.prop('disabled', true).addClass('kt-spinner kt-spinner--sm kt-spinner--light');
            ingredientFeedback
                .removeClass()
                .html('<div class="alert alert-info mb-0">Sync in progress...</div>');

            $.ajax({
                url: '/api/ingredient-usage',
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                processData: false,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    branch_id: branchId,
                    date_from: dateFrom,
                    date_to: dateTo
                }),
                success: function (response) {
                    triggerButton.prop('disabled', false).removeClass('kt-spinner kt-spinner--sm kt-spinner--light');

                    if (response.success) {
                        var alertClass = response.inserted_rows > 0 ? 'alert-success' : 'alert-warning';
                        var message = response.inserted_rows > 0
                            ? 'Ingredient usage synced successfully. Inserted rows: ' + response.inserted_rows + '.'
                            : (response.message || 'No ingredient usage records were generated.');
                        ingredientFeedback
                            .removeClass()
                            .addClass('mt-3')
                            .html('<div class="alert ' + alertClass + ' mb-0">' + message + '</div>');
                    } else {
                        ingredientFeedback
                            .removeClass()
                            .addClass('mt-3')
                            .html('<div class="alert alert-danger mb-0">' + (response.message || 'Failed to sync ingredient usage.') + '</div>');
                        toastr.error(response.message || 'Failed to sync ingredient usage.');
                    }
                },
                error: function (xhr) {
                    triggerButton.prop('disabled', false).removeClass('kt-spinner kt-spinner--sm kt-spinner--light');

                    var message = 'Failed to sync ingredient usage.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    ingredientFeedback
                        .removeClass()
                        .addClass('mt-3')
                        .html('<div class="alert alert-danger mb-0">' + message + '</div>');

                    toastr.error(message);
                }
            });
        });
    };

    return {
        // public functions
        init: function() {
            initValidation();
            bindIngredientSync();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormWidgets.init();
});
