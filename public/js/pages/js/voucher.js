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
                up_chart_account_code: {
                    required: true
                },
                up_chart_account_id: {
                    integer: true
                },
                currency_id: {
                    required: true,
                    valueNotEquals: "0"
                },
                exchange_rate: {
                    required: true
                },
                cash_type: {
                    required: true,
                    valueNotEquals: "0"
                },
                pos_branch_ids: {
                    required: true,
                    valueNotEquals: "0"
                },
                saleman_id: {
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
                        $("form").find(":submit").prop('disabled', false);
                        $('body').removeClass('pointerEventsNone');
                        return;
                    }
                    form.removeAttribute('data-staging-dirty');
                }


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
                    url         : form.action,
                    type        : form.method,
                    dataType	: 'json',
                    data        : formData,
                    cache       : false,
                    contentType : false,
                    processData : false,
                    beforeSend  : function(){
                        $('body').addClass('pointerEventsNone');
                        $('.erp_form__grid_body tr input.acc_code').removeAttr('style');
                        $('.erp_form__grid_body tr input.acc_name').removeAttr('style');
                    },
                    success: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
                        if(response.status == 'success'){
                            toastr.success(response.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                             if (response.data && response.data.redirect) {
                            window.location.href = response.data.redirect;
                            } else if(response.data.form == 'new'){
                                window.location.href = response.data.redirect || ('/accounts/{{ $type }}/form/' + (response.data.id || ''));
                            }else{
                                $('.new-row').removeClass('new-row');
                            }
                        }else{
                            toastr.error(response.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                            if(response.data.hasOwnProperty('budgetCharts')){
                                $('#kt_modal_lg .modal-content').html('').html(response.data.budgetCharts);
                                $('#kt_modal_lg').modal('show');
                            }
                            if(response.data.hasOwnProperty('budgets')){
                                var budgets = response.data.budgets;
                                var rows = document.querySelectorAll('.erp_form__grid_body tr input.account_id');
                                const entries = Object.values(budgets);
                                entries.forEach((key) => {
                                    rows.forEach(function(row){
                                        console.log(key);
                                        console.log($(row).val());
                                        if($(row).val() == key.accountId){
                                            $(row).parents('tr').find('.acc_code').attr('style', 'background: #dc3545 !important;color:#fff;');
                                            $(row).parents('tr').find('.acc_name').attr('style', 'background: #dc3545 !important;color:#fff;');
                                        }
                                    });
                                });
                            }
                        }
                    },
                    error: function(response,status) {
                        $('body').removeClass('pointerEventsNone');
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
