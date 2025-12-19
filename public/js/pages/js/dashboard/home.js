$('#erp_dashboard').click(function(){
    $('.erp-widget').css('opacity','0.4');
    $(this).css('opacity','1.0');
    var formData = {};
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type        : 'POST',
        url         : '/dashboard/get-sale-dashboard-detail',
        dataType	: 'json',
        data        : formData,
        success: function(response) {
            var data = response['data'];
            var view = data['view'];
            $('#dashboard_data').html(view);
            if(typeof month_sale_branch_ajax === 'function'){
                month_sale_branch_ajax();
            }
        }
    });
});

$('#restaurants_dashboard').click(function(){
    $('.erp-widget').css('opacity','0.4');
    $(this).css('opacity','1.0');
    var formData = {};
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type        : 'POST',
        url         : '/dashboard/get-restaurant-dashboard-detail',
        dataType	: 'json',
        data        : formData,
        success: function(response) {
            var data = response['data'];
            var view = data['view'];
            $('#dashboard_data').html(view);
            if(typeof loadRestaurantCharts === 'function'){
                loadRestaurantCharts();
            }
        }
    });
});

$(document).ready(function(){
    $('#erp_dashboard').trigger('click');
});


