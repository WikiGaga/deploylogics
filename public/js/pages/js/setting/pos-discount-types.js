// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#discount_type_form" )
    var initDiscountScope = function () {
        var toggle = formId.find('#is_order_discount');
        var maxDiscount = formId.find('#max_discount');
        var updateDiscountScope = function () {
            var isOrderDiscount = toggle.prop('checked');
            formId.find('#max_discount_group').toggle(isOrderDiscount);
            maxDiscount.prop('disabled', !isOrderDiscount).prop('required', isOrderDiscount);
        };
        toggle.on('change', updateDiscountScope);
        formId.on('reset', function () {
            setTimeout(updateDiscountScope, 0);
        });
        updateDiscountScope();
    };
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                name: {
                    required: true
                },
                default_discount: {
                    required: true,
                },
                max_discount: {
                    required: function () {
                        return formId.find('#is_order_discount').prop('checked');
                    },
                    number: true,
                    min: 0,
                    max: 999999999999.999
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
                $("form").find(":submit").prop('disabled', true);
                //form[0].submit(); // submit the form
                var formData = new FormData(form);
                ajaxFunc(form,formData);
                
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
            success: function(response,status) {
                if(response.status == 'success'){
                    toastr.success(response.message);
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                    if(response.data.form == 'new'){
                        window.location.href = response.data.redirect;
                    }
                }else{
                    toastr.error(response.message);
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                }
            },
            error: function(response,status) {
                //   console.log(response);
                toastr.error(response.statusText);
                setTimeout(function () {
                    $("form").find(":submit").prop('disabled', false);
                }, 2000);
            },
        });
    }
    return {
        // public functions
        init: function() {
            initDiscountScope();
            initValidation();
        }
    };
}();

jQuery(document).ready(function() {
    KTFormWidgets.init();
});
