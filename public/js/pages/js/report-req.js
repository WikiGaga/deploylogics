$('#reporting_table_name').on('change', function() {
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
                $('#reporting_dimension_column_name').html(cloumnsList);
                $('.reporting_user_filter_name').html(cloumnsList);
                $('#reporting_sort_colum_name_1').html(cloumnsList);
                $('#reporting_sort_colum_name_2').html(cloumnsList);
                $('.reporting_select_metric').html(cloumnsList);
                $('.report_fields_name').html(cloumnsList);
                $('.reporting_select_metric,.report_fields_name').select2({
                    placeholder: "Select"
                });
                addColumnAlign();
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

function getFiledConditions(){}
function addColumnAlign(){
    $('#reporting_dimension_column_name,.select2-selection__choice__remove').on('change', function() {
        var val = $(this).val();
        var len = $('#reporting_dimension_column_name option:selected').length;
        var AlignList = $('#AddColumnAlignList>.row').length;
        var column = '';
        if(len > AlignList){
            var i = parseInt(len) - 1;
            column += '<div class="row">' +
                '<div class="col-lg-8">' +
                '<label class="erp-col-form-label">Column '+len+':</label>' +
                '<div class="row text-center column-align">' +
                '<input type="hidden" class="column_align_val" value="left" name="column['+i+'][align]">'+
                '<div class="col-lg-4 sel-col-align">' +
                '<i class="fa fa-align-left fa-active" data-value="left"></i>' +
                '</div>' +
                '<div class="col-lg-4 sel-col-align">' +
                '<i class="fa fa-align-center" data-value="center"></i>' +
                '</div>' +
                '<div class="col-lg-4 sel-col-align">' +
                '<i class="fa fa-align-right" data-value="right"></i>' +
                '</div>' +
                '</div>' +
                '</div>' +
                /*'<div class="col-lg-4">' +
                '<label class="erp-col-form-label">Decimal:</label>' +
                '<div class="erp-select2">' +
                '<select class="form-control kt-select2 erp-form-control-sm" name="column['+i+'][decimal]">' +
                '<option value="0">auto</option>' +
                '<option value="1">1</option>' +
                '<option value="2">2</option>' +
                '<option value="3">3</option>' +
                '</select>' +
                '</div>' +
                '</div>' +*/
                '</div>';

            $('#AddColumnAlignList').append(column);
        }else{
            $('#AddColumnAlignList>.row:last-child').remove();
        }

        selectColumnAlign();
    });
}

function selectColumnAlign(){
    $('.sel-col-align').unbind();
    $('.sel-col-align').click(function(){
        var dataValue = $(this).find('i').attr("data-value");
        $(this).parents('.column-align').find('input.column_align_val').val(dataValue);
        $(this).parents('.column-align').find('.fa').removeClass('fa-active');
        $(this).find('.fa').addClass('fa-active');
    });
}

function allReportingFunc(){
    getFiledConditions();
}
$(document).ready(function(){
    allReportingFunc();
});
$(document).on('change', '.report_fields_name', function(event) {
    var that = $(this);
    var table_name = $('#reporting_table_name').val();
    var val = $(this).val();
    if(val == "" || val == 0){
        that.parents('.filter_block').find('.report_condition').html('<option value="">Select</option>');;
        hideData(that)
        return false;
    }
    url = '/report/get-filed-conditions/'+table_name+'/'+val;
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
function hideData(that){
    that.parents('.filter_block').find('#report_filter_block').find('input').attr('disabled',true);
    that.parents('.filter_block').find('#report_filter_block').find('.row').hide();
}
