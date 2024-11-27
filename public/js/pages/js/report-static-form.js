// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#report_static_form" );
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {

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
                console.log(formData);
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
                        $('#progressBar').html('<div class="progress"><div class="progress-bar progress-bar-success progress-bar-animated progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div>');
                        var min = Math.ceil(25);
                        var max = Math.floor(95);
                        var n = Math.floor(Math.random() * (max - min + 1)) + min;
                        $('.progress-bar').animate({width: n+"%"}, 100);
                    },
                    success: function(response,status) {
                        //console.log(response);
                        $('.progress-bar').animate({width: "100%"}, 100);
                        setTimeout(function(){
                            setTimeout(function(){
                                if(response.status == 'success'){
                                    $('#progressBar').html('');
                                    toastr.success(response.message);
                                    setTimeout(function () {
                                        $("form").find(":submit").prop('disabled', false);
                                    }, 2000);
                                    var win = window.open(response['data']['url'], "_blank");
                                    win.location.reload();
                                    //  window.location.href = response['data']['redirect'];
                                }else{
                                    toastr.error(response.message);
                                    setTimeout(function () {
                                        $("form").find(":submit").prop('disabled', false);
                                    }, 2000);
                                    $('#progressBar').html('');
                                }
                            }, 100);
                        }, 500);
                    },
                    error: function(response,status) {
                         console.log(response);
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
function formRepeaterValidation(){
    /*if($('select.report_filter_name option').length > 1){
        $('[name*="report_filter_name"]').each(function() {
            $(this).rules('add', {
                required: true,
                valueNotEquals: "0"
            });
        });
    }*/
}
jQuery(document).ready(function() {
    KTFormWidgets.init();
    formRepeaterValidation();
});
