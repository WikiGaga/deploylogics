// Class definition

var KTFormWidgets = function () {
    // Private functions
    var validator;
    var formId = $( "#form_import" );
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function () {
        validator = formId.validate({
            // define validation rules
            rules: {
                customer_name: {
                    required: true,
                    maxlength:200
                },
                customer_type:{
                    required: true,
                    valueNotEquals: "0",
                },
                'customer_branch_id[]':{
                    required: true,
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
                var t = 1;
                $(".kt-portlet__head-label").append('<div id="time" class="text-center" style="width: 100px"></div>');
                setInterval(function(){
                    $(".kt-portlet__head-label").find('#time').html(t+" second");
                    t = t+1;
                }, 1000)
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
                        $(".kt-portlet__head-label").find("#time").attr('id','time2');
                        var time = $(".kt-portlet__head-label").find("#time2").text();
                        if(response.status == 'success'){
                            if(response.data.form == 'import'){
                                swal.fire({
                                    title: response.message,
                                    html : time,
                                    type: 'success',
                                    confirmButtonText: 'Yes'
                                }).then((result) => {
                                    if(result){
                                        location.reload();
                                    }
                                })
                            }
                        }else{
                            if(response.data.form == 'import'){
                                swal.fire({
                                    title:  'Error! on',
                                    text:   response.message,
                                    type: 'error',
                                    confirmButtonText: 'Yes'
                                });
                            }else{
                                toastr.error(response.message);
                            }
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                        }

                    },
                    error: function(response,status) {
                        // console.log(response.responseJSON);
                        console.log(response);
                        console.log(response.responseJSON);
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
