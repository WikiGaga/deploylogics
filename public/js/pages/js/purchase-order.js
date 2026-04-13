// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#purchase_order_form" )
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                supplier_name: {
                    required: true
                },
                supplier_id: {
                integer: true
                },
                lpo_generation_no_id: {
                    integer : true
                },
                payment_terms: {
                    integer : true
                },
                payment_mode: {
                    integer : true
                },
                exchange_rate: {
                    required: true
                },
                po_currency: {
                    required: true,
                    integer : true,
                    valueNotEquals: "0"
                },
                po_notes: {
                    maxlength:255
                }

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
                $("form").find(":submit").prop('disabled', true);
                $('body').addClass('pointerEventsNone');
                var ajaxValidate = 1;
                var title_msg = '';
                var title_text = '';
                if($('#product_barcode_id').val()){
                    ajaxValidate = 0;
                    title_msg = js_msg.entry_is_exits
                    title_text = js_msg.are_you_sure_to_save_without_it
                }
                $('.erp_form__grid_body>tr').each(function(){
                    if($(this).find('.tblGridCal_rate').val() == 0){
                        ajaxValidate = 0;
                        title_msg =  $(this).find('.pd_barcode').val();
                        title_text = js_msg.value_is_zero;
                        return false;
                    }
                });
                if(ajaxValidate == 0){
                    $("form").find(":submit").prop('disabled', false);
                    $('body').removeClass('pointerEventsNone');
                    swal.fire({
                        title: title_msg,
                        text: title_text,
                        type: 'warning',
                        showCancelButton: true,
                        showConfirmButton: true
                    }).then(function(result){
                        if(result.value){
                            ajaxFunc(form,formData);
                            formClear()
                        }else {
                            $('#pd_barcode').focus();
                        }
                    });
                }else{
                    ajaxFunc(form,formData);
                }
            }
        });
    }
    var ajaxFunc = function (form,formData){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url         : form.action,
            type        : form.method,
            dataType	: 'json',
            data        : formData,
            contentType : false,
            processData : false,
            beforeSend: function( xhr ) {
                 $('body').addClass('pointerEventsNone');
            },
            success: function(response,status) {
                if(response.status == 'success'){
                    toastr.success(response.message);
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                    if (response.data && response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else if (response.data && response.data.form == 'new') {
                        window.location.href = response.data.redirect || ('/purchase-order/form/' + (response.data.id || ''));
                    } else {
                        $('.new-row').removeClass('new-row');
                        $('body').removeClass('pointerEventsNone');
                    }
                }else{
                    toastr.error(response.message);
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                    $('body').removeClass('pointerEventsNone');
                }
                $('#pd_barcode').focus();
            },
            error: function(response,status) {
                //   console.log(response);
                toastr.error(response.statusText);
                $('body').removeClass('pointerEventsNone');
                setTimeout(function () {
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
