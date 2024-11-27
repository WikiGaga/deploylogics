// Class definition
var KTFormWidgets = function() {
    // Private functions
    var validator;
    var formId = $("#coa_form");
    $.validator.addMethod("valueNotEquals", function(value, element, arg){
        return arg !== value;
    }, "This field is required");
    var initValidation = function() {

        validator = formId.validate({
            // define validation rules

            rules: {
                name: {
                    required: true,
                    maxlength:100
                },
                parent_account_code:{
                    required: true,
                    valueNotEquals: "0"
                },
                branch_id: {
                    required: true,
                    valueNotEquals: "0"
                },
                'chart_branch_id[]':{
                    required: true,
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
                var el = this.submitButton;
                formData.append('action', el.getAttribute('data-id'));
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
                            console.log(response);
                            toastr.success(response.message);
                            $('#kt_modal_md').modal('hide');
                            $('#kt_modal_md').find('.modal-content').empty();
                            $('#kt_modal_md').find('.modal-content').html(' <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>');
                            var id = $(document).find('.jstree-clicked').attr('id');
                            var tree = jqTree("#kt_tree_4");
                            tree.jstree('set_text', id , response.data.name );
                            tree.jstree(true).get_node(id).original.level = parseInt(response.data.level);
                            tree.jstree(true).get_node(id).original.main_id = parseInt(response.data.main_id);
                            tree.jstree(true).get_node(id).original.parent_main_id = parseInt(response.data.main_id);
                            tree.jstree('set_id', id , response.data.code );
                        }else{
                            toastr.error(response.message);
                            setTimeout(function () {
                                $("form").find(":submit").prop('disabled', false);
                            }, 2000);
                        }
                    },
                    error: function(response,status) {
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
