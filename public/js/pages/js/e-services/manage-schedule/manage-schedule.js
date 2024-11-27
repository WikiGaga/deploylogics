// Make Start Time to Make Time Picker
$('#start_time').timepicker();

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

$(document).on('click' , '#updateData' , function(){
    var required = ['salesman','start_time','interval'];
    var error = 0;
    required.forEach((el) => {
        if($('#' + el).val() == 0){
            error = 1;
        }
    });
    if(error == 0){
        var start_time  = $('#start_time').val();
        var interval    = parseInt($('#interval').val());
        var salesman    = $('#salesman').val();
        var salesmanName    = $('#salesman option:selected').text();
        var schedule_date   = $('#kt_datepicker_3').val();
        var delay = 0;
        var checked = $('.checkRow:checked').not(':disabled');
        clearRows();
        checked.each((element) => {
            var checkbox = checked[element];
            var tr = checkbox.closest('tr');
            var scheduled_time = Date.parse(start_time).addMinutes(delay + interval).toString('hh:mm tt');

            //Setting Values
            tr.querySelector('.salesManText').innerHTML = salesmanName;
            tr.querySelector('.scheduleDateText').innerHTML = Date.parse(start_time).addMinutes(delay + interval).toString('MM/dd/yyyy');
            tr.querySelector('.scheduleTimeText').innerHTML = scheduled_time;
            tr.querySelector('.sales_man_id').value = salesman;
            tr.querySelector('.scheduleDate').value = Date.parse(start_time).addMinutes(delay + interval).toString('dd-MM-yyyy');
            tr.querySelector('.scheduleTime').value = scheduled_time;

            delay = delay + interval;

        });


    }else{
        toastr.error('Please Fill All Required Fields');
    }
});

// Request To Get Data & Put it into table

$(document).on('click' , '#kt_quick_panel_close_btn',function(e){
    e.preventDefault();
    e.stopPropagation();
    var form = document.querySelector('#manage_schedule_form');
    var formData = new FormData(form);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url         : reqURL,
        method      : 'POST',
        data        : formData,
        cache       : false,
        processData: false,
        contentType: false,
        beforeSend  : function(){
            $('#kt_quick_panel_toggler_btn').prop('disabled' , true);
            $('#scheduleTable tbody').html('');
            $('#scheduleTable').addClass('d-none');
            $('body').addClass('pointerEventsNone');
        },  
        success: function(response,status) {
            $('#kt_quick_panel_toggler_btn').prop('disabled' , false);
            if(response.status == 'success'){
                $('body').removeClass('pointerEventsNone');
                toastr.success(response.message);
                var data = response.data;
                var string = '';var a_scheduled = [];
                // var temp = $('#a_scheduled').val().split(',');
                data.forEach((quotation,index) => {
                    console.log(quotation);
                    var iteration = index + 1;
                    // Variable List Will be here
                    var sales_order_id = quotation.sales_order_id;
                    var sales_quotation_id = quotation.sales_quotation_id;
                    var city_id = quotation.city_id;
                    var city_name = quotation.city;
                    var area_id = quotation.area_id;
                    var area = quotation.area;
                    var customer = quotation.customer_name;
                    var phone = quotation.phone_no;
                    var request_date = quotation.request_date;
                    var request_no = quotation.request_no;
                    var order_date = quotation.order_date;
                    var order_no = quotation.order_no;
                    var quoted_amount = quotation.quoted_amount;
                    var actual_amount = quotation.actual_amount;
                    var status = quotation.status;
                    var status_id = quotation.status_id;
                    var schedule_status = quotation.schedule_status;
                    // Schedule Data
                    var schedule_dtl_date = quotation.schedule_dt_date;
                    var schedule_dtl_time = quotation.schedule_dtl_time;
                    var schedule_salesman = quotation.schedule_salesman;
                    var schedule_salesman_id = quotation.schedule_salesman_id;
                    var for_update      = quotation.for_update;

                    string += '<tr>'+
                        '<td class="text-center">'+
                            '<label class="kt-checkbox kt-checkbox--bold'; if(schedule_status == 1) {string += ' kt-checkbox--success';}else{ string += ' kt-checkbox--primary'; } string += '" style="vertical-align: inherit;">';
                                if(schedule_status == 1 && for_update == 0){
                                    string += '<input type="checkbox" autocomplete="off"'; if(schedule_status == 1) {string += ' checked disabled value="on"';} string += ' class="checkRow" name="pd['+iteration+'][checkRow]">';
                                }else if(schedule_status == 1 && for_update == 1){
                                    string += '<input type="checkbox" autocomplete="off"'; if(schedule_status == 1) {string += ' checked value="on"';} string += ' class="checkRow" name="pd['+iteration+'][checkRow]">';
                                }else{
                                    string += '<input type="checkbox" autocomplete="off" class="checkRow" name="pd['+iteration+'][checkRow]">';
                                }
                                string += '<span></span>'+
                            '</label>'+
                            '<input type="hidden" name="pd['+iteration+'][city_id]" autocomplete="off" value="'+city_id+'">'+
                            '<input type="hidden" name="pd['+iteration+'][area_id]" autocomplete="off" value="'+area_id+'">'+
                            '<input type="hidden" name="pd['+iteration+'][sales_order_id]" autocomplete="off" value="'+sales_order_id+'">'+
                            '<input type="hidden" name="pd['+iteration+'][sales_quotation_id]" autocomplete="off" value="'+sales_quotation_id+'">'+
                            '<input type="hidden" name="pd['+iteration+'][schedule_time]" class="scheduleTime" value="'+schedule_dtl_time+'" />'+
                            '<input type="hidden" name="pd['+iteration+'][schedule_date]" class="scheduleDate" value="'+schedule_dtl_date+'" />'+
                            '<input type="hidden" name="pd['+iteration+'][sales_man_id]" class="sales_man_id" value="'+schedule_salesman_id+'" />'+
                            '<input type="hidden" name="pd['+iteration+'][status_id]" class="status_id" value="'+status_id+'" />'+
                        '</td>'+
                        '<td class="salesManText">'+schedule_salesman+'</td>'+
                        '<td>'+city_name+'</td>'+
                        '<td>'+area+'</td>'+
                        '<td>'+customer+'</td>'+
                        '<td>'+notNull(phone)+'</td>'+
                        '<td>'+request_date+'</td>'+
                        '<td>'+request_no+'</td>'+
                        '<td class="scheduleDateText">'+schedule_dtl_date+'</td>'+
                        '<td class="scheduleTimeText">'+schedule_dtl_time+'</td>'+
                        '<td>'+order_date+'</td>'+
                        '<td>'+order_no+'</td>'+
                        '<td>'+quoted_amount+'</td>'+
                        '<td>'+actual_amount+'</td>'+
                        '<td>'+
                            '<select class="form-control erp-form-control-sm" disabled>'+
                                '<option>'+status+'</option>'+
                            '</select>'+
                        '</td>'+
                    '</tr>';
                });
                $('#scheduleTable tbody').append(string);
                $('#scheduleTable').removeClass('d-none');
                // var values = handleDuplication(a_scheduled);
                // $('#a_scheduled').val(values);
            }else{
                $('body').removeClass('pointerEventsNone');
                toastr.error(response.message);
            }
        },
        error: function(response,status) {
            // console.log(response.responseJSON);
            $('body').removeClass('pointerEventsNone');
            toastr.error(response.responseJSON.message);
        },
    });
});


function clearRows(){
    var checkboxes = $('.checkRow').not(':disabled');
    checkboxes.each((element) => {
        var checkbox = checkboxes[element];
        var tr = checkbox.closest('tr');
        //Reset Values
        tr.querySelector('.salesManText').innerHTML = '';
        tr.querySelector('.scheduleDateText').innerHTML = '';
        tr.querySelector('.scheduleTimeText').innerHTML = '';
        tr.querySelector('.sales_man_id').value = '';
        tr.querySelector('.scheduleDate').value = '';
        tr.querySelector('.scheduleTime').value = '';
    });
}

function handleDuplication(arr){
    var old = $('#a_scheduled').val();
    var old = old.split(',');
    old.forEach((el)=>{
        arr.push(el);
    });
    return arr.join(',');
}