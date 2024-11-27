$(document).on('change', '.report_filter_name', function(event) {
   // debugger
    var that = $(this);
    var table_id = $('#reporting_id').val();
    var val = $(this).val();
    var url = '/report/get-filed-type/'+table_id+'/'+val;
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    if(val != 0){
        $.ajax({
            type: 'GET',
            url: url,
            data:{_token: CSRF_TOKEN},
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            success: function(response, status){
                console.log(response);
                if(response.status == 'success') {
                    var field_type = '';
                    column_type_name = response['data']['column_type_name'];
                    that.parents('.report_filter_block').find('#report_value_column_type_name').val(column_type_name);
                    data_case = response['data']['case_data'];
                    var data_type = response['data']['type'];
                    field_type += '<option value="0">Select</option>';
                    if(data_type != ""){
                        for(var i=0; data_type.length > i; i++){
                            if(data_type[i]['filter_type_entry_status'] == 1){
                                field_type += '<option value="'+data_type[i]['filter_type_value'].toLowerCase()+'">'+data_type[i]['filter_type_title']+'</option>';
                            }
                        }
                    }
                    console.log(field_type);
                    that.parents('.report_filter_block').find('.report_filter_type').html(field_type);
                    $('.report_filter_type').select2({
                        placeholder: "Select"
                    });
                    hideData(that);
                    //that.parents('.report_filter_block').find('#report_filter_filed').html('');
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
    }else{
        that.parents('.report_filter_block').find('.report_filter_type').html('');
        hideData(that);
        /*that.parents('.report_filter_block').find('#report_filter_filed').html('');*/
    }

});
$(document).on('change', '.report_filter_type', function(event) {
    var that = $(this);
    var val = $(this).val();
    var field = '';
    var field_type = '';
    console.log(column_type_name);
    if(column_type_name == 'varchar2'){
        hideData(that);
        console.log("data_case: " + data_case);
        if(data_case != undefined){
            for(var i=0; data_case.length > i; i++){
                if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'manufacturer_id'){
                    field_type += '<option value="'+data_case[i]['manufacturer_id']+'">'+data_case[i]['manufacturer_name']+'</option>';
                }
                if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'product_item_tags'){
                    field_type += '<option value="'+data_case[i]['tags_id']+'">'+data_case[i]['tags_name']+'</option>';
                }
                if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'product_name'){
                    field_type += '<option value="'+data_case[i]['product_name']+'">'+data_case[i]['product_name']+'</option>';
                }
                if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'group_item_name'){
                    field_type += '<option value="'+data_case[i]['group_item_name']+'">'+data_case[i]['group_item_name']+'</option>';
                }
                if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'supplier_type_name'){
                    field_type += '<option value="'+data_case[i]['supplier_type_name']+'">'+data_case[i]['supplier_type_name']+'</option>';
                }
                if(that.parents('.report_filter_block').find('.report_filter_name ').val() == 'chart_code'){
                    field_type += '<option value="'+data_case[i]['chart_code']+'">'+data_case[i]['chart_code']+'</option>';
                }
            }
            field = '<option value="0">Select</option>' + field_type;
            that.parents('.report_filter_block').find('#fields_values').find('select').attr('disabled',false);
            that.parents('.report_filter_block').find('#fields_values').show();
            that.parents('.report_filter_block').find('#fields_values').find('select').html(field);
        }
    }
    if(column_type_name == 'number' && val == 'between'){
        hideData(that);
        that.parents('.report_filter_block').find('#number_between').find('input').attr('disabled',false);
        that.parents('.report_filter_block').find('#number_between').show();
    }
    if(column_type_name == 'number' && (val == '=' || val == '!=' || val == '=' || val == '<' || val == '>' || val == '>=' || val == '<=')){
        hideData(that);
        that.parents('.report_filter_block').find('#fields_values').find('select').attr('disabled',false);
        that.parents('.report_filter_block').find('#fields_values').show();
    }
    if(column_type_name == 'date' && val == 'between'){
        hideData(that);
        that.parents('.report_filter_block').find('#date_between').find('input').attr('disabled',false);
        that.parents('.report_filter_block').find('#date_between').show();
    }
    if(val == 'null' || val == 'not null' || val == 'yes' || val == 'no' || val == 0 || val == ''){
        hideData(that);
    }
    $('.validNumber').keypress(validateNumber);
    $('.report_value').select2({
        placeholder: "Select",
        tags: true
    });
    if(column_type_name == 'number' && (val == '=' || val == '!=' || val == '=' || val == '<' || val == '>' || val == '>=' || val == '<=')){
        $('.select2-search__field').keypress(validateNumber);
    }

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
function hideData(that){
    that.parents('.report_filter_block').find('#report_filter_filed').find('input').attr('disabled',true);
    that.parents('.report_filter_block').find('#report_filter_filed').find('select').attr('disabled',true);
    that.parents('.report_filter_block').find('#report_filter_filed').find('select').html('<option value="0">Select</option>');
    that.parents('.report_filter_block').find('#report_filter_filed').find('.row').hide();
}
function allUserReportingFunc(){

}
