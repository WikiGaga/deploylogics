// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#dashboard_graph_bar_form" );
    $.validator.addMethod("noSpace", function(value, element) { 
        return value.indexOf(" ") < 0; 
      }, "No space please");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                widget_case_name: {
                    required: true,
                    noSpace: true,
                    maxlength:100
                },
                widget_name: {
                    required: true,
                    maxlength:100
                },
                y_axis: {
                    required: true,
                    maxlength:100
                },
                x_axis_titles_qry: {
                    required: true
                },
                x_axis_values_qry: {
                    required: true
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
