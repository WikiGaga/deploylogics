window.setTimeout( function() {
    accounts_dashboard();
}, 60000);


$('#accounts_dashboard').click(function(){
    $('.erp-widget').css('opacity','');
    $(this).css('opacity','1.0');
    
    var formData = {

    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type        : 'POST',
        url         : '/dashboard/get-account-dashboard-detail',
        dataType	: 'json',
        data        : formData,
        success: function(response) {
            var data = response['data'];
            var view = data['view'];
            $('#dashboard_data').html(view);
        }
    });
});

function accounts_dashboard()
{
    $('.erp-widget').css('opacity','');
    $(this).css('opacity','1.0');
    
    var formData = {

    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type        : 'POST',
        url         : '/dashboard/get-account-dashboard-detail',
        dataType	: 'json',
        data        : formData,
        success: function(response) {
            var data = response['data'];
            var view = data['view'];
            $('#dashboard_data').html(view);
        }
    });
}
