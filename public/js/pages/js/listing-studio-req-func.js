$('.listing_studio_table_name,.listing_studio_join_name').on('change', function() {
    var thix = $(this);
    var val = $(this).val();
    var url = '/report/get-columns/'+val;
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'GET',
        url: url,
        data:{_token: CSRF_TOKEN},
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        success: function(response, status){
            console.log(response);
            if(response.status == 'success') {
                cloumnsList = '';
                cloumnsData = response['data'];
                var data = response['data'];
                data.sort(function(a, b){
                    if(a.column_name < b.column_name) { return -1; }
                    if(a.column_name > b.column_name) { return 1; }
                    return 0;
                })

                cloumnsList += '<option value="0">Select</option>';
                for(var i in data){
                    cloumnsList += '<option value="'+data[i]['column_name'].toLowerCase()+'">'+data[i]['column_name'].toLowerCase()+'</option>';
                }
                if(thix.hasClass('listing_studio_join_name')){
                    $('.listing_studio_join_table_column_name').html(cloumnsList);
                }
                if(thix.hasClass('listing_studio_table_name')){
                    $(".report_fields_name ").val(-1).trigger('change');
                    $('.listing_studio_dimension_column_name').html(cloumnsList);
                    $('.listing_studio_user_filter_name').html(cloumnsList);
                    $('.listing_studio_metric_column_name').html(cloumnsList);
                    $('.listing_studio_sort_colum_name_1').html(cloumnsList);
                    $('.listing_studio_sort_colum_name_2').html(cloumnsList);
                    $('.report_fields_name').html(cloumnsList);
                    $('.report_fields_name').select2({
                        placeholder: "Select"
                    });
                }
                toastr.success(response.message);
            }
            else{
                toastr.error(response.message);
            }
        },
        error: function(response,status) {
            // console.log(response);
        },
    });
});
$(document).on('change', '.report_fields_name', function(event) {
    $('.report_fields_name').select2({
        placeholder: "Select"
    });
    var that = $(this);
    var table_name = $('.listing_studio_table_name').val();
    var val = $(this).val();
    console.log("val: " + val);
    if(val == "" || val == 0 || val == null){
        that.parents('.filter_block').find('.report_condition').html('<option value="">Select</option>');;
        hideData(that)
        return false;
    }
    var url = '/report/get-filed-conditions/'+table_name+'/'+val;
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'GET',
        url: url,
        data:{_token: CSRF_TOKEN},
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        success: function(response, status){
            console.log(response);
            if(response.status == 'success') {
                var FiledConditionsList = '';
                var data = response['data'];
                column_type_name = data[0]['filter_type_data_type_name'].toLowerCase();
                FiledConditionsList += '<option value="">Select</option>';
                if(data != ""){
                    for(var i=0; data.length > i; i++){
                        FiledConditionsList += '<option value="'+data[i]['filter_type_value'].toLowerCase()+'">'+data[i]['filter_type_title'].toLowerCase()+'</option>';
                    }
                }

                that.parents('.filter_block').find('.report_condition').html(FiledConditionsList);
                that.parents('.filter_block').find('#report_value_column_type_name').val(column_type_name);

                $('.report_condition').select2({
                    placeholder: "Select"
                });
                hideData(that);
                //  toastr.success(response.message);
            }
            else{
                toastr.error(response.message);
            }
        },
        error: function(response,status) {
            // console.log(response);
        },
    });
});
$(document).on('change', '.report_condition', function(event) {
    //debugger
    var that = $(this);
    var val = $(this).val();
    var field = '';
    var field_type = '';
    console.log(column_type_name);
    if(column_type_name == 'varchar2'){
        hideData(that);
        that.parents('.filter_block').find('#fields_values').find('input').attr('disabled',false);
        that.parents('.filter_block').find('#fields_values').show();
    }
    if(column_type_name == 'number' && val == 'between'){
        hideData(that);
        that.parents('.filter_block').find('#number_between').find('input').attr('disabled',false);
        that.parents('.filter_block').find('#number_between').show();
    }
    if(column_type_name == 'number' && (val == '=' || val == '!=' || val == '=' || val == '<' || val == '>' || val == '>=' || val == '<=')){
        hideData(that);
        that.parents('.filter_block').find('#fields_values').find('input').attr('disabled',false);
        that.parents('.filter_block').find('#fields_values').show();
    }
    if(column_type_name == 'date' && val == 'between'){
        hideData(that);
        that.parents('.filter_block').find('#date_between').find('input').attr('disabled',false);
        that.parents('.filter_block').find('#date_between').show();
    }
    if(val == 'null' || val == 'not null' || val == 'yes' || val == 'no' || val == 0 || val == ''){
        hideData(that);
    }
    $('.validNumber').keypress(validateNumber);

    // range picker
    var arrows = {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    }
    $('.kt_datepicker_5').datepicker({
        rtl: KTUtil.isRTL(),
        todayHighlight: true,
        format:'dd-mm-yyyy',
        templates: arrows
    });
});
$('.listing_studio_select_menu').on('change', function() {
    var val = $(this).val();
    var url = '/common/get-menu-dtl/';
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    if(val == 'main_listing'){
        $('#listing_studio_select_menu_dtl_id').show();
        $('#listing_studio_parent_menu').show();
        $.ajax({
            type: 'GET',
            url: url,
            data: {_token: CSRF_TOKEN},
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            success: function (response, status) {
                var data = response['data'];
                data.sort(function(a, b){
                    if(a.menu_dtl_name < b.menu_dtl_name) { return -1; }
                    if(a.menu_dtl_name > b.menu_dtl_name) { return 1; }
                    return 0;
                })
                var menuList = '';
                for(var i in data){
                    menuList += '<option value="'+data[i]['menu_dtl_id']+'">'+data[i]['menu_dtl_name']+'</option>';
                }
                $('.listing_studio_select_menu_dtl_id').html(menuList);
                var parentMenuList = '<option value="0">Select</option><option value="accounts">Accounts</option><option value="stock">Stock</option><option value="day">Day</option><option value="barcode-labels">Barcode Labels</option>';
                $('.listing_studio_parent_menu').html(parentMenuList);
                $('.listing_studio_select_menu_dtl_id').select2({
                    placeholder: "Select"
                });
                $('.listing_studio_parent_menu').select2({
                    placeholder: "Select"
                });
            }
        });
    }else {
        $('#listing_studio_parent_menu').hide();
        $('#listing_studio_select_menu_dtl_id').hide();
        $('.listing_studio_parent_menu').html('<option value="0">Select</option>');
        $('.listing_studio_select_menu_dtl_id').html('<option value="0">Select</option>');
    }
});
function hideData(that){
    that.parents('.filter_block').find('#report_filter_block').find('input').attr('disabled',true);
    that.parents('.filter_block').find('#report_filter_block').find('.row').hide();
}
function validateNumber(event) {
    event = (event) ? event : window.event;
    var charCode = (event.which) ? event.which : event.keyCode;
    var val = String.fromCharCode(charCode);
    var validateNum = ['1','2','3','4','5','6','7','8','9','0','.'];
    if(!validateNum.includes(val)) {
        return false;
    }
    return true;
}
