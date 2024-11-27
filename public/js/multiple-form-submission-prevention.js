$(document).ready(function() {
    $('form').submit(function(event) {

        var $form = $(this);

        $form.find(':submit').prop('disabled', true);

        // Re-enable the submit button after a delay
        setTimeout(function () {
            $form.find(':submit').prop('disabled', false);
        }, 10000);
    });
});
