"use strict";

// Class Definition
var KTLogin = function() {

    var _handleBranchForm = function() {
        $('#kt_branch_form_submit').on('click', function (e) {
            e.preventDefault();
            $("form").find(":submit").prop('disabled', true);
            var formData = new FormData(document.getElementById("kt_branch_form"));
            var form = document.getElementById("kt_branch_form");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url         : form.action,
                type        : 'POST',
                data        : formData,
                cache       : false,
                contentType : false,
                processData : false,
                success: function (response, status, xhr, $form) {
                    $("form").find(":submit").prop('disabled', false);
                    // similate 2s delay
                    window.location.href = '/home';
                },
                error: function(response,status) {
                    // console.log(response.responseJSON);
                    toastr.error(response.responseJSON.message);
                    setTimeout(function () {
                        $("form").find(":submit").prop('disabled', false);
                    }, 2000);
                },
            });

        });
    }

    // Public Functions
    return {
        // public functions
        init: function() {
            _handleBranchForm();
        }
    };
}();

// Class Initialization
jQuery(document).ready(function() {
    KTLogin.init();
});
