//$('#data_table #del').on('click',function (e) {
//$('#data_table #del').click(function (e) {
$('#data_table,#ajax_data,#dynamic_ajax_data').on('click', '#del', function (e) {
    var url = $(this).attr('data-url');
    swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.value) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url:url,
                data:{_token: CSRF_TOKEN},
                success: function(response, status){
                    if ( response.status === 'success' ) {
                        swal.fire({
                            title:  'Deleted!',
                            text:   response.message,
                            type:   'success',
                            showConfirmButton: false,
                        });
                        setTimeout(function () {
                            location.reload();
                        }, 2000);

                    }else{
                        swal.fire({
                            title:  'Not Deleted!',
                            text:   response.message,
                            type:   'error',
                        });
                    }
                }
            });
        }
    });
});
