// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    let grnXhr = true;
    var formId = $( "#grn_form" )
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
          //  debug: true,
            rules: {
                supplier_name: {
                    required: true
                },
                grn_currency: {
                    required: true,
                    valueNotEquals: "0",
                },
                exchange_rate: {
                    required: true
                },
                grn_store: {
                    required: true,
                    valueNotEquals: "0",
                },
                payment_type_id: {
                    required: true,
                    valueNotEquals: "0",
                },
                payment_acc_id: {
                    required: {
                        depends: function () {
                            var paymentType = parseInt($('#payment_type_id').val(), 10);
                            return paymentType === 1 || paymentType === 3;
                        }
                    },
                    valueNotEquals: {
                        param: "0",
                        depends: function () {
                            var paymentType = parseInt($('#payment_type_id').val(), 10);
                            return paymentType === 1 || paymentType === 3;
                        }
                    }
                },
                grn_bill_no: {
                    required: true
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
                }).then(function(result){
                    if(result.value){
                        formClear()
                    }
                });
            },
            submitHandler: function (form) {
                var stagingFlowId = form.querySelector('input[name=current_flow_id]');
                function stagingActionCodeForSubmit(frm) {
                    var el = document.activeElement;
                    if (el && el.classList && el.classList.contains('staging-action-btn')) {
                        return (el.getAttribute('data-staging-action-code') || '').toLowerCase();
                    }
                    return (frm.getAttribute('data-staging-last-action-code') || '').toLowerCase();
                }
                var stagingCode = stagingActionCodeForSubmit(form);
                var warnCodes = ['forward', 'post', 'back', 'cancel'];
                if (stagingFlowId && stagingFlowId.value && warnCodes.indexOf(stagingCode) !== -1
                    && form.getAttribute('data-staging-dirty') === '1') {
                    if (!window.confirm('Changes will not be saved. Do you want to continue?')) {
                        return;
                    }
                    form.removeAttribute('data-staging-dirty');
                }

                $("form").find(":submit").prop('disabled', true);
                $('body').addClass('pointerEventsNone');
                var formData = new FormData(form);
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
                    var stagingActionId = form.querySelector('#staging_current_actions_id');
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
                            $("form").find(":submit").prop('disabled', false);
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
        if(grnXhr) {
            grnXhr = false;
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
                beforeSend: function( xhr ) {
                  //  $('body').addClass('pointerEventsNone');
                },
                success: function(response, status, xhr) {
                    if (response.status == 'success' && window.GRNFormAutoSave) {
                        window.GRNFormAutoSave.clearSavedData();
                    }
                    var ok = erpFormAjaxDone(response, xhr, {
                        newFormUrl: function(res) {
                            return (res.data && res.data.redirect) || '';
                        }
                    });
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                    if (ok) {
                        if (!(response.data && (response.data.redirect || response.data.form === 'new'))) {
                            $('.new-row').removeClass('new-row');
                            $('body').removeClass('pointerEventsNone');
                        }
                        grnXhr = true;
                    } else {
                        $('body').removeClass('pointerEventsNone');
                        grnXhr = true;
                    }
                    $('#pd_barcode').focus();
                },
                error: function(xhr) {
                    erpDocumentAjaxDone(null, xhr, { errorMsg: 'Request failed', reload: false });
                    $('body').removeClass('pointerEventsNone');
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                    grnXhr = true;
                },
            });
        }
    }
    function formClear(){

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
