$(document).on('click' , '#kt_quick_panel_close_btn',function(e){
    e.preventDefault();
    var form = document.querySelector('#updateStatusForm');
    form.submit();
});

$('#filter_cities').on('select2:unselect' , function(e){
    var unselected = e.params.data.id;
    var options = $('#filter_areas').find("option[data-id='"+ unselected +"']").remove();
});

$('#filter_cities').on('select2:select',function(e){
    var city_id = e.params.data.id;
    if(city_id != "0" || city_id != ""){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : '/area/get-area-by-city',
            method : 'POST',
            data : {"city_id" : city_id},
            beforeSend : function() {
                $('body').addClass('pointerEventsNone');
            },
            success : function(response,status){
                $('body').removeClass('pointerEventsNone');
                $('#sales_order_area').html('');
                if(response.status == 'success'){
                    var areas = response.data;
                    var option = '';
                    areas.forEach((el) => {
                        option += '<option value="'+ el.area_id +'" data-id="'+el.city_id+'">'+el.area_name+'</option>';
                    });
                    $('#filter_areas').append(option);
                }
            },
            error: function(response,status) {
                toastr.error(response.responseJSON.message);
            },
        });
    }
});
