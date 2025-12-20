$('#erp_dashboard').click(function(){
    $('.erp-widget').css('opacity','0.4');
    $(this).css('opacity','1.0');

    $('#dashboard_data').html('');
    $('#shimmer_loading').addClass('loading');

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
            $('#shimmer_loading').removeClass('loading');

            var data = response['data'];
            var view = data['view'];
            $('#dashboard_data').html(view);
            if(typeof month_sale_branch_ajax === 'function'){
                month_sale_branch_ajax();
            }
        },
        error: function() {
            $('#shimmer_loading').removeClass('loading');
            $('#dashboard_data').html('<div class="alert alert-danger">Error loading dashboard. Please try again.</div>');
        }
    });
});

$('#restaurants_dashboard').click(function(){
    $('.erp-widget').css('opacity','0.4');
    $(this).css('opacity','1.0');

    $('#dashboard_data').html('');
    $('#shimmer_loading').addClass('loading');

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
            $('#shimmer_loading').removeClass('loading');

            var data = response['data'];
            var view = data['view'];
            $('#dashboard_data').html(view);
            if(typeof loadRestaurantCharts === 'function'){
                loadRestaurantCharts();
            }
        },
        error: function() {
            $('#shimmer_loading').removeClass('loading');
            $('#dashboard_data').html('<div class="alert alert-danger">Error loading dashboard. Please try again.</div>');
        }
    });
});

