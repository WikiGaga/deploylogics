// Class definition

var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $(".stock_form")
    $.validator.addMethod("valueNotEquals", function(value, element, arg) {
        return arg !== value;
    }, "This field is required");
    var initValidation = function() {
        validator = formId.validate({
            // define validation rules
            rules: {
                store_to: {
                    required: true,
                    valueNotEquals: "0"
                },
                /*f_product_barcode_id: {
                    required: true,
                },
                assamble_qty: {
                    required: true,
                },*/
                stock_remarks: {
                    maxlength: 100
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
                
                //form[0].submit(); // submit the form
                var formData = new FormData(form);
                var stagingActionId = form.querySelector('#staging_current_actions_id');
                var stagingActionCode = form.querySelector('#staging_action_code');

                if (stagingFlowId && stagingFlowId.value) {
                    formData.set('current_flow_id', stagingFlowId.value);
                    var flowRemarks = form.querySelector('textarea[name=flow_remarks]');
                    if (flowRemarks) {
                        formData.set('flow_remarks', flowRemarks.value || '');
                    }

                    // Prefer the button that actually submitted the form
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

                // Workflow-only staging actions should not be blocked by grid rate checks
                var stagingWorkflowOnly = warnCodes.indexOf(stagingCode) !== -1;
                var validate_form = ['str', 'st'];
                var form_type = $('#form_type').val();
                var ajaxValidate = 1;
                var title_msg = '';
                var title_text = '';
                if (!stagingWorkflowOnly && $('#product_barcode_id').val()) {
                    ajaxValidate = 0;
                    title_msg = js_msg.entry_is_exits
                    title_text = js_msg.are_you_sure_to_save_without_it
                }
                if (!stagingWorkflowOnly && validate_form.includes(form_type)) {
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
            success: function(response, status, xhr) {
                var ok = erpFormAjaxDone(response, xhr, {
                    reloadOnSuccess: !!(response.data && response.data.form !== 'new' && !response.data.redirect),
                    newFormUrl: function(res) {
                        return (res.data && res.data.redirect) || '';
                    }
                });
                setTimeout(function() {
                    $("form").find(":submit").prop('disabled', false);
                }, 2000);
                if (!ok) {
                    $('body').removeClass('pointerEventsNone');
                }
            },
            error: function(xhr) {
                erpDocumentAjaxDone(null, xhr, { errorMsg: 'Request failed', reload: false });
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
