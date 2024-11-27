$(document).on('change', '.report_fields_name', function(event) {
    $('.report_fields_name').select2({
        placeholder: "Select"
    });
    var that = $(this);
    var val = $(this).val();
    if(val == "" || val == 0 || val == null){
        that.parents('.filter_block').find('.report_condition').html('<option value="">Select</option>');;
        hideData(that)
        return false;
    }
    var formData =  {
        col_type : cloumnsList[val]
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type        : 'POST',
        url         : '/reports/get-column-conditions',
        dataType	: 'json',
        data        : formData,
        success: function(response, status){
            if(response.status == 'success') {
                var FiledConditionsList = '';
                var id = 'none';
                var data = response['data']['conditions'];
                column_type_name = response['data']['col_type'].toLowerCase();
                FiledConditionsList += '<option value="0">Select</option>';
                if(data.length != 0){
                    for(var i=0; data.length > i; i++){
                        FiledConditionsList += '<option value="'+data[i]['filter_type_value'].toLowerCase()+'">'+data[i]['filter_type_title'].toLowerCase()+'</option>';
                    }
                }

                that.parents('.filter_block').find('#fields_values .fields_values_append').html('<div class="erp-select2"></div>');
                that.parents('.filter_block').find('.report_condition').html(FiledConditionsList);
                that.parents('.filter_block').find('.report_value_column_type_name').val(column_type_name);

                columns(that,column_type_name,data[0]['filter_type_value'].toLowerCase())

                that.parents('.filter_block').find('.report_condition').prop("selectedIndex", 1);


                $('.report_condition').select2({
                    placeholder: "Select"
                });

                that.parents('.filter_block').find('#fields_values .erp-select2').attr('id',val+'_multi_select');
                that.parents('.filter_block').find('#fields_values .erp-select2>select').addClass('kt_select_report_'+val);
                var b = Math.floor(Math.random() * 10000000);
                var t_outer_block = that.parents('.outer-filter_block').attr('outer-id');
                var t_inner_block = that.parents('.filter_block').attr('inner-id');
                var nameKe = 'outer_filterList['+t_outer_block+'][inner_filterList]['+t_inner_block+'][val][]';
                var sel = '<select class="form-control erp-form-control-sm kt_select_report_multi'+b+'" multiple name="'+nameKe+'">' +
                    '<option></option>\n' +
                    '</select>';

                that.parents('.filter_block').find('#fields_values .erp-select2').html(sel);

                if(val == 'store_name'){
                    store_name_fun(b);
                }
                if(val == 'display_location_name_string'){
                    display_location_name_string_fun(b);
                }
                if(val == 'supplier_name'){
                    supplier_name_fun(b);
                }
                if(val == 'customer_name'){
                    customer_name_fun(b);
                }
                if(val == 'product_name' || val == 'product_barcode_barcode'){
                    that.parents('.filter_block').find('#fields_values .erp-select2>select');
                    product_name_fun(b,true);
                }
                var filter_list = ['store_name','display_location_name_string','supplier_name','customer_name',
                'product_name','product_barcode_barcode'];
                if(!filter_list.includes(val)){
                    var sel = '<select class="form-control erp-form-control-sm kt_select_none'+b+'" multiple name="'+nameKe+'">' +
                        '<option></option>\n' +
                        '</select>';

                    that.parents('.filter_block').find('#fields_values .erp-select2').html(sel);

                    $('.kt_select_none'+b).empty();
                    $('.kt_select_none'+b).select2({
                        placeholder: "Search.....",
                        allowClear: true,
                        tags:true
                    });
                    $('.kt_select_none'+b).trigger('change');
                }
                // hideData(that);
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
    var field_type = $(this).parents('.filter_block').find('.report_value_column_type_name').val();

    columns(that,field_type,val)
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
$(document).on('click', '.report-inner_clause-and-btn', function(event) {
    $(this).parents('.inner-repeater').find('.filter_block:nth-last-child(2) .inner_clause_item_input').val("AND");
    $(this).parents('.inner-repeater').find('.filter_block:nth-last-child(2) .inner_clause_item').html("AND");
    $(this).parents('.inner-repeater').find('.filter_block:nth-last-child(2) .inner_clause_item').css("padding", "6px 5px 5px 5px");
})
$(document).on('click', '.report-inner_clause-or-btn', function(event) {
    $(this).parents('.inner-repeater').find('.filter_block:nth-last-child(2)').find('.inner_clause_item_input').val("OR");
    $(this).parents('.inner-repeater').find('.filter_block:nth-last-child(2)').find('.inner_clause_item').html("OR");
})
$(document).on('click', '.outer_clause-and-btn', function(event) {
    $(this).parents('#kt_repeater_1').find('.outer-filter_block:last-child').find('.report-filter-and-del-btn_input').val("AND");
    $(this).parents('#kt_repeater_1').find('.outer-filter_block:last-child').find('.report-filter-and-del-btn').html('<i class="la la-trash-o"></i> AND');
})
$(document).on('click', '.outer_clause-or-btn', function(event) {
    $(this).parents('#kt_repeater_1').find('.outer-filter_block:last-child').find('.report-filter-and-del-btn_input').val("OR");
    $(this).parents('#kt_repeater_1').find('.outer-filter_block:last-child').find('.report-filter-and-del-btn').html('<i class="la la-trash-o"></i> OR');
})

function columns(that,column_type_name,val=""){
    var t_outer_block = that.parents('.outer-filter_block').attr('outer-id');
    var t_inner_block = that.parents('.filter_block').attr('inner-id');
    var nameKe = 'outer_filterList['+(parseInt(t_outer_block))+'][inner_filterList]['+(parseInt(t_inner_block))+'][val]';
    if(column_type_name == 'varchar2'){
        hideData(that);
        that.parents('.filter_block').find('#fields_values').find('select').attr('disabled',false).prop('disabled', false);
        that.parents('.filter_block').find('#fields_values').find('input').attr('disabled',true).show();
        that.parents('.filter_block').find('#fields_values').show();
    }
    if(column_type_name == 'number' && val == 'between'){
        hideData(that);
        that.parents('.filter_block').find('#number_between').find('input').attr('disabled',false);
        that.parents('.filter_block').find('#number_between').show();
    }
    if(column_type_name == 'number' && (val == '=' || val == '!=' || val == '=' || val == '<' || val == '>' || val == '>=' || val == '<=')){
        hideData(that);
        var inputHtml ='<input type="text" name="'+nameKe+'" class="form-control erp-form-control-sm text-right validNumber">';
        that.parents('.filter_block').find('#fields_values .fields_values_append').html(inputHtml);
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
}
function hideData(that){
    that.parents('.filter_block').find('#report_filter_block').find('select').attr('disabled',true);
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

function store_name_fun(b){
    $(".kt_select_report_multi"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:true,
        ajax: {
            url: "/reports/get-store-by-name",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.store_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.store_name).toLowerCase() + "</div>" +
                    "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.store_name || repo.text;
        } // omitted for brevity, see the source of this page
    });
}
function display_location_name_string_fun(b){
    $(".kt_select_report_multi"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:true,
        ajax: {
            url: "/reports/get-display-location-name-string-by-name",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.display_location_name_string == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.display_location_name_string).toLowerCase() + "</div>" +
                    "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.display_location_name_string || repo.text;
        } // omitted for brevity, see the source of this page
    });
}
function supplier_name_fun(b){
    $(".kt_select_report_multi"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:true,
        ajax: {
            url: "/reports/get-supplier-by-name",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.supplier_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.supplier_name).toLowerCase() + "</div>" +
                    "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.supplier_name || repo.text;
        } // omitted for brevity, see the source of this page
    });
}
function customer_name_fun(b){
    $(".kt_select_report_multi"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:true,
        ajax: {
            url: "/reports/get-customer-by-name",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.customer_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.customer_name).toLowerCase() + "</div>" +
                    "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.customer_name || repo.text;
        } // omitted for brevity, see the source of this page
    });
}
function product_name_fun(b,tagsVal){
    $(".kt_select_report_multi"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:tagsVal,
        ajax: {
            url: "/reports/get-product-by-name",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.product_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.product_name).toLowerCase() + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__desc'>" + repo.product_barcode_barcode + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__col_1'>" + repo.uom_name + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__col_2'>" + repo.packing + "</div>" +
                    "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.product_name || repo.text;
        } // omitted for brevity, see the source of this page
    });

    $(".kt_select_report_multi"+b).on('select2:open', function (e) {
        $('body').find('ul.select2-results__options').addClass('kt_select_pro');
    });
}

function product_id_fun(b,tagsVal){
    $(".re_select_product"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:tagsVal,
        ajax: {
            url: "/reports/get-product-by-id",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.product_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.product_name).toLowerCase() + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__desc'>" + repo.product_barcode_barcode + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__col_1'>" + repo.uom_name + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__col_2'>" + repo.packing + "</div>" +
                    "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.product_name || repo.text;
        } // omitted for brevity, see the source of this page
    });

    $(".re_select_product"+b).on('select2:open', function (e) {
        $('body').find('ul.select2-results__options').addClass('kt_select_pro');
    });
}
function marchant_id_fun(b,tagsVal){
    $(".re_select_marchant"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:tagsVal,
        ajax: {
            url: "/reports/get-marchant-by-id",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.chart_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.chart_name).toLowerCase() + "</div>" +
                   "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.chart_name || repo.text;
        } // omitted for brevity, see the source of this page
    });

    $(".re_select_marchant"+b).on('select2:open', function (e) {
        $('body').find('ul.select2-results__options').addClass('kt_select_pro');
    });
}
function customer_id_fun(b,tagsVal){
    $(".re_select_customer"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:tagsVal,
        ajax: {
            url: "/reports/get-customer-by-id",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.customer_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.customer_name).toLowerCase() + "</div>" +
                   "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.customer_name || repo.text;
        } // omitted for brevity, see the source of this page
    });

    $(".re_select_customer"+b).on('select2:open', function (e) {
        $('body').find('ul.select2-results__options').addClass('kt_select_pro');
    });
}

function supplier_id_fun(b,tagsVal){
    $(".re_select_supplier"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:tagsVal,
        ajax: {
            url: "/reports/get-supplier-by-id",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.supplier_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.supplier_name).toLowerCase() + "</div>" +
                   "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.supplier_name || repo.text;
        } // omitted for brevity, see the source of this page
    });

    $(".re_select_supplier"+b).on('select2:open', function (e) {
        $('body').find('ul.select2-results__options').addClass('kt_select_pro');
    });
}
function chart_account_fun(b,tagsVal){
    $(".kt_select_chart_account_multi"+b).select2({
        placeholder: "Search.....",
        allowClear: true,
        minimumInputLength: 3,
        tags:tagsVal,
        ajax: {
            url: "/reports/get-chart-account-by-name",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    report_case: $('#report_case').val(),
                    page: params.page
                };
            },
            processResults: function(data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        templateResult: function(repo){
            if (repo.loading) return repo.text;

            if(repo.chart_name == undefined){
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + repo.text + "</div>" +
                    "</div></div>";
            }else{
                var markup = "<div class='select2-result-resp select2-result-repository clearfix'>" +
                    "<div class='select2-resp_meta select2-result-repository__meta'>" +
                    "<div class='select2-resp_list select2-result-repository__title'>" + (repo.chart_name).toLowerCase() + "</div>" +
                    "<div class='select2-resp_list select2-result-repository__desc'>" + repo.chart_code + "</div>" +
                    "</div></div>";
            }
            return markup;
        }, // omitted for brevity, see the source of this page
        templateSelection: function(repo){
            return repo.chart_name || repo.text;
        } // omitted for brevity, see the source of this page
    });
    $(".kt_select_chart_account_multi"+b).on('select2:open', function (e) {
        $('body').find('ul.select2-results__options').addClass('kt_select_pro').css({width:'100%'});
    });
}


