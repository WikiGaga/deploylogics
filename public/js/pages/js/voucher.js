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
