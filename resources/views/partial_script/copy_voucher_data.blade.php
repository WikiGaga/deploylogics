<script !src="">
    var xhrGetData = true;
    $(document).on('click','#getDataByVoucherNo',function(){
        var thix = $(this);
        var form = thix.parents('form');
        var voucher_no = form.find('#last_voucher_no').val();
        var form_type = form.find('#form_type').val();
        var validate = true;
        if(valueEmpty(voucher_no)){
            toastr.error("Add voucher no");
            validate = false;
            return true;
        }
        if(valueEmpty(form_type) && !form_type.includes(['jv','pve','lv'])){
            toastr.error("Type not found");
            validate = false;
            return true;
        }
        if(validate && xhrGetData){
            xhrGetData = false;
            var formData = {
                voucher_no: voucher_no,
                voucher_type: form_type,
            };
            var url = '/accounts/set-session-voucher';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType	: 'json',
                data        : formData,
                beforeSend: function( xhr ) {
                    $('body').addClass('pointerEventsNone');
                },
                success: function(response,data) {
                    console.log(response);
                    if(response.status == 'success'){
                        // toastr.success(response.message);
                        location.reload();
                    }else{
                        toastr.error(response.message);
                    }
                    xhrGetData = true;
                    $('body').removeClass('pointerEventsNone');
                },
                error: function(response,status) {
                    toastr.error(response.responseJSON.message);
                    xhrGetData = true;
                    $('body').removeClass('pointerEventsNone');
                }
            });
        }
    })
</script>
