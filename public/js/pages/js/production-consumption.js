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
            },
            submitHandler: function(form) {
                if ($('.erp_form__grid_body tr').length === 0) {
                    toastr.error('Add at least one product line.');
                    return false;
                }

                var stagingFlowId = form.querySelector('input[name=current_flow_id]');
                function stagingUserCanSave(frm) {
                    var buttons = frm.querySelectorAll('.staging-action-btn[data-staging-action-code]');
                    for (var i = 0; i < buttons.length; i++) {
                        var c = (buttons[i].getAttribute('data-staging-action-code') || '').toLowerCase();
                        if (c === 'save' || c === 'create' || c === 'edit') return true;
                    }
                    return false;
                }
                function stagingActionCodeForSubmit(frm) {
                    var el = document.activeElement;
                    if (el && el.classList && el.classList.contains('staging-action-btn')) {
                        return (el.getAttribute('data-staging-action-code') || '').toLowerCase();
                    }
                    return (frm.getAttribute('data-staging-last-action-code') || '').toLowerCase();
                }
                var stagingCode = stagingActionCodeForSubmit(form);
                var warnCodes = ['forward', 'post', 'back', 'cancel'];
                var needStagingDiscardWarn = stagingFlowId && stagingFlowId.value && warnCodes.indexOf(stagingCode) !== -1
                    && !stagingUserCanSave(form) && form.getAttribute('data-staging-dirty') === '1';
                if (needStagingDiscardWarn) {
                    if (!window.confirm('Changes will not be saved. Do you want to continue?')) {
                        return;
                    }
                    form.removeAttribute('data-staging-dirty');
                }

                $("form").find(":submit").prop('disabled', true);
                $('body').addClass('pointerEventsNone');
                var formData = new FormData(form);
                var stagingActionId = form.querySelector('#staging_current_actions_id');
                var stagingActionCode = form.querySelector('#staging_action_code');

                if (stagingFlowId && stagingFlowId.value) {
                    formData.set('current_flow_id', stagingFlowId.value);
                    var flowRemarks = form.querySelector('textarea[name=flow_remarks]');
                    if (flowRemarks) {
                        formData.set('flow_remarks', flowRemarks.value || '');
                    }

                    var submitter = document.activeElement;
                    var actionId = null;
                    var actionCode = null;

                    if (submitter && submitter.classList && submitter.classList.contains('staging-action-btn')) {
                        actionId = submitter.value || submitter.getAttribute('data-staging-action-id');
                        actionCode = submitter.getAttribute('data-staging-action-code') || '';
                    }

                    if (!actionId && stagingActionId && stagingActionId.value) {
                        actionId = stagingActionId.value;
                    }

                    if (actionId) {
                        formData.set('current_actions_id', actionId);
                    }
                    if (actionCode) {
                        formData.set('staging_action_code', actionCode);
                    }

                    var nextFlow = form.querySelector('input[name=next_flow_id]');
                    var prevFlow = form.querySelector('input[name=prev_flow_id]');
                    if (nextFlow && nextFlow.value) formData.set('next_flow_id', nextFlow.value);
                    if (prevFlow && prevFlow.value) formData.set('prev_flow_id', prevFlow.value);
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
                    success: function(response, status, xhr) {
                        var ok = typeof erpFormAjaxDone === 'function'
                            ? erpFormAjaxDone(response, xhr, {
                                newFormUrl: function(res) {
                                    return '/production-consumption/form/' + ((res.data && res.data.id) || '');
                                }
                            })
                            : false;

                        if (!ok && response && response.status === 'success') {
                            toastr.success(response.message);
                            if (response.data && response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else if (response.data && response.data.form === 'new') {
                                window.location.href = response.data.redirect;
                            } else {
                                $('.new-row').removeClass('new-row');
                            }
                        }

                        setTimeout(function() {
                            $("form").find(":submit").prop('disabled', false);
                        }, 1500);

                        if (ok) {
                            if (!(response.data && (response.data.redirect || response.data.form === 'new'))) {
                                $('.new-row').removeClass('new-row');
                                $('body').removeClass('pointerEventsNone');
                            }
                        } else if (!(response && response.status === 'success' && response.data && response.data.redirect)) {
                            $('body').removeClass('pointerEventsNone');
                        }
                    },
                    error: function(response) {
                        if (typeof erpDocumentAjaxDone === 'function') {
                            erpDocumentAjaxDone(null, response, { errorMsg: 'Unable to save record.', reload: false });
                        } else {
                            var message = response.responseJSON && response.responseJSON.message
                                ? response.responseJSON.message
                                : 'Unable to save record.';
                            toastr.error(message);
                        }
                        $('body').removeClass('pointerEventsNone');
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
