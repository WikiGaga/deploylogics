@extends('layouts.pattern_listing')
@section('title', $data['title'].' listing')

@section('pageCSS')
    <style>
        .color-white{
            color: #fff !important;
        }
        .color-white:hover{
            border: 1px solid #fff;
            padding: 1px;
            color: #101740 !important;
        }
        th[data-field="stock"]>span,
        td[data-field="stock"]>span {
            text-align: right;
        }
        .backgroud_img{
            background: url(/assets/media/illustrations/2.png);
            background-repeat: no-repeat;
            background-position: 50% 100%;
            background-size: 40%;
        }
        #ajax_data>table{
            overflow: auto;
        }
        thead.kt-datatable__head>tr>th:last-child {
            background: #ffb822 !important;
            position: sticky;
            right: 0;
        }
        thead.kt-datatable__head>tr>th:last-child>span {
            text-align: center !important;
        }
        tbody.kt-datatable__body>tr>td:last-child {
            background: #838383 !important;
            position: sticky;
            right: 0;
        }
        tbody.kt-datatable__body>tr>td:last-child>span {
            text-align: center !important;
        }
        .mlr {
            margin: 0 5px;
        }
    </style>
@endsection
@section('content')
    @php
         $view = $data['menu_dtl_id'].'-view';
         $create = $data['menu_dtl_id'].'-create';
         $edit = $data['menu_dtl_id'].'-edit';
         $del = $data['menu_dtl_id'].'-delete';
         $print = $data['menu_dtl_id'].'-print';
         $changePass = $data['menu_dtl_id'].'-change_password';
       //  dd($data['table_columns']);
    @endphp
    <script>
        var dataFields = {
            @foreach($data['table_columns'] as $key=>$obj)
            "{{$key}}": {
                'title' : "{{$obj['title']}}",
                'type' : "{{$obj['type']}}",
            },
            @endforeach
        };
    </script>
    <script>
        var btnEditView = true;
        var btnDelView = false;
        var btnPrintView = true;
        var pathAction = '{{$data['form-action']}}'
        var table_id = '{{$data['table_id']}}'
    </script>
    @permission($edit)
    <script>
        var btnEditView = true;
    </script>
    @endpermission
    @permission($del)
    <script>
        var btnDelView = true;
    </script>
    @endpermission
    @permission($print)
    <script>
        var btnPrintView = true;
    </script>
    @endpermission
  {{--  @permission($view)--}}
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid backgroud_img">
        <div class="kt-portlet kt-portlet--mobile" style="margin-bottom: 5px;">
            <div class="kt-portlet__body">
                <!--begin: Search Form -->
                <div class="row">
                    <div class="col-md-3">
                        <h5 class="kt-portlet__head-title">
                            {{$data['title']}}
                        </h5>
                    </div>
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-5">
                        <button type="button" class="btn btn-sm btn-primary" id="fbrSaleTaxPost">FBR Sales Tax Invoice Post</button>
                    </div>
                    <div class="col-md-1 text-right">
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="flaticon-more"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                                @foreach($data['table_columns'] as $key=>$heading)
                                    <li >
                                        <label>
                                            <input value="{{$key}}" type="checkbox" checked> {{$heading['title']}}
                                        </label>
                                    </li>
                                @endforeach
                                <li >
                                    <label>
                                        <input value="actions" type="checkbox" checked> Actions
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <label class="mb-0">Branch</label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="branch_id" name="branch_id" >
                                        @foreach($data['branch'] as $branch)
                                            <option value="{{$branch->branch_id}}" {{$branch->branch_id==auth()->user()->branch_id?"selected":""}}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <label class="mb-0">Post Status</label>
                                <div>
                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                                        <input type="radio" name="radioPost" value="posted"> Posted
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--bold kt-radio--warning mb-0">
                                        <input type="radio" name="radioPost" value="unposted" checked> UnPosted
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <label class="mb-0">Invoice Amount Filter</label>
                                <div class="input-group">
                                    <div class="erp-select2" style="width: 66.66%;">
                                        <select class="form-control kt-select2 erp-form-control-sm" id="net_amount_filter">
                                            <option value="">Select</option>
                                            <option value=">">Greater Then</option>
                                            <option value="<">Less Than</option>
                                            <option value=">=">Greater than or equal</option>
                                            <option value="<=">Less than or equal</option>
                                            <option value="=">Equal To</option>
                                            <option value="!=">Not equal To</option>
                                        </select>
                                    </div>
                                    <div style="width: 33.33%;">
                                        <input type="text" id="net_amount_filter_val" class="form-control erp-form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <label class="mb-0">Payment Mode</label>
                                <div class="erp-select2">
                                    <select class="form-control kt-select2 erp-form-control-sm" id="payment_mode">
                                        <option value="">Select</option>
                                        @foreach($data['payment_type'] as $payment_type)
                                            <option value="{{$payment_type->payment_type_id}}">{{$payment_type->payment_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end: Search Form -->
            </div>
        </div>

        @include('partial_script.date_filter_listing')

        <div class="kt-portlet__body kt-portlet__body--fit">
            <!--begin: Datatable -->
            <style>
                .kt-datatable>.kt-datatable__table{
                    max-height: 450px !important;
                }
                .ps > .ps__rail-x {
                    height: 10px !important;
                }
                .ps > .ps__rail-x:hover, .ps > .ps__rail-x:focus {
                    height: 10px !important;
                }
                .kt-datatable .ps > .ps__rail-y > .ps__thumb-y:hover, .kt-datatable .ps > .ps__rail-y > .ps__thumb-y:focus, .kt-datatable .ps > .ps__rail-x > .ps__thumb-x:hover, .kt-datatable .ps > .ps__rail-x > .ps__thumb-x:focus{
                    background: #f44336 !important;;
                }
                .kt-datatable .ps > .ps__rail-y > .ps__thumb-y, .kt-datatable .ps > .ps__rail-x > .ps__thumb-x {
                    background: #f44336 !important;;
                }
                .ps > .ps__rail-x > .ps__thumb-x:hover, .ps > .ps__rail-x > .ps__thumb-x:focus {
                    background: #f44336 !important;;
                    height: 10px !important;
                }
                .ps > .ps__rail-x > .ps__thumb-x {
                    height: 10px !important;
                }

                .ps > .ps__rail-y {
                    width: 10px !important;
                }
                .ps > .ps__rail-y:hover, .ps > .ps__rail-y:focus {
                    width: 10px !important;
                }
                .ps > .ps__rail-y > .ps__thumb-y:hover, .ps > .ps__rail-y > .ps__thumb-y:focus {
                    width: 10px !important;
                }
                .ps > .ps__rail-y > .ps__thumb-y {
                    width: 10px !important;
                }
                .kt-datatable.kt-datatable--default > .kt-datatable__pager{
                    padding: 10px 25px !important;
                }
            </style>
            <div class="kt-datatable ajax_data_table listing_data_table" data-url="{{ $data['data_url'] }}" id="dynamic_ajax_data"></div>
            <!--end: Datatable -->
        </div>
    </div>
    <!-- end:: Content -->
    {{-- @endpermission end view permission--}}
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script>
        "use strict";
        var inline_filter_data = {};
        // Class definition

        var KTDatatableRemoteAjaxDemo = function() {
            // Private functions

            // basic demo
            var demo = function() {
                localStorage.removeItem('ajax_data-1-meta');
                var table = $('.kt-datatable');
                var tableUrl = table.attr('data-url');
                var dataColumns = [];
                var obj = {
                        field: table_id, title: "#", sortable: !1, width: 20, type: "number",selector: { class: "kt-checkbox--solid fbr_checkbox" },textAlign: "center",
                };
                dataColumns.push(obj);
                for (var key in dataFields) {
                    if(dataFields[key]['type'] == 'string'){
                        var obj = {
                            field: key,
                            title: dataFields[key]['title'],
                        };
                    }
                    if(dataFields[key]['type'] == 'date'){
                        var obj = {
                            field: key,
                            title: dataFields[key]['title'],
                            /*template: function(row) {
                                return funcDateFormat(row[key]);
                            },*/
                        };
                    }
                    if(dataFields[key]['type'] == 'datetime'){
                        var obj = {
                            field: key,
                            title: dataFields[key]['title'],
                        };
                    }
                    dataColumns.push(obj);
                }
                var lastColumn = {
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    width: 110,
                    overflow: 'visible',
                    autoHide: false,
                    template: function(row) {
                        var key_id = row[table_id];
                        var dropdownLink = false;
                        var btnDropdownLink = "";
                        var btnEdit = "";
                        var btnUpdatePaymentMethod = "";
                        var btnDel = "";
                        var btnPrint = "";
                        if(btnPrintView){
                            var btnPrint = '<a class="dropdown-item" href="'+pathAction+'/print/'+key_id+'"><i class="la la-edit"></i>Print</a>';
                            dropdownLink = true;
                        }
                        if(btnEditView){
                            var btnEdit = '<a href="'+pathAction+'/form/'+key_id+'" target="_blank" class="btn btn-sm btn-soft btn-icon btn-icon-sm" title="Edit">\
                                        <i class="la la-edit"></i>\
                                    </a>';
                        }
                        if(btnDelView){
                            var btnDel = '<button type="button" data-url="'+pathAction+'/delete/'+key_id+'" id="del"  class="btn btn-sm btn-soft btn-icon btn-icon-sm mlr" title="Delete">\
                                        <i class="la la-trash"></i>\
                                    </button>';
                        }
                        if(dropdownLink){
                            var btnDropdownLink = '<div class="dropdown">'+
                                '<a href="javascript:;" class="btn btn-sm btn-soft btn-icon btn-icon-sm" data-toggle="dropdown">'+
                                '<i class="la la-bars"></i>'+
                                '</a>'+
                                '<div class="dropdown-menu dropdown-menu-right">'+
                                btnPrint +
                                '</div>'+
                                '</div>';
                        }

                        return  btnEdit;
                    }
                };
                dataColumns.push(lastColumn);


                var datatable = $('.kt-datatable').KTDatatable({
                    // datasource definition
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                method: 'GET',
                                url: tableUrl,
                                // sample custom headers
                                // headers: {'x-my-custom-header': 'some value', 'x-test-header': 'the value'},
                                map: function(raw) {
                                    // sample data mapping
                                    var dataSet = raw;
                                    if (typeof raw.data !== 'undefined') {
                                        dataSet = raw.data;
                                    }
                                    return dataSet;
                                },
                            },
                        },
                        pageSize: 500,
                        serverPaging: true,
                        serverFiltering: true,
                        serverSorting: true,
                        deferLoading: false, // here
                    },
                    // layout definition
                    layout: {
                        scroll: true,
                        height: 400,
                        footer: false,
                    },

                    // column sorting
                    sortable: false,
                    filterable: true,
                    pagination: true,
                    toolbar: {
                        items: {
                            pagination: {
                                pageSizeSelect: [100, 200, 500, 1000, 2000],
                            },
                        },
                    },
                    search: {
                        // input: $('#generalSearch'),
                    },

                    rows: {
                        callback: function() {},
                        // auto hide columns, if rows overflow. work on non locked columns
                        autoHide: false,
                    },
                    // columns definition
                    columns: dataColumns,
                });

                $('body').on('submit', 'form[name="getRecordsByDateFilter"]', function(event) {
                    event.preventDefault();
                    var filterData = {};
                    var date_type = $(document).find('form input[name="radioDate"]:checked').val();
                    filterData.date = date_type;

                    filterData.time_from = $(document).find('form input[name="time_from"]').val();
                    filterData.time_to = $(document).find('form input[name="time_to"]').val();
                    if(date_type == 'custom_date'){
                        filterData.from = $(document).find('form input[name="from"]').val();
                        filterData.to = $(document).find('form input[name="to"]').val();
                    }
                    var global_search = $('#generalSearch').val();
                    if(!valueEmpty(global_search)){
                        filterData.global_search = global_search;
                    }

                    // custom filter of some forms
                    filterData.branch_id = $(document).find('#branch_id option:selected').val();
                    filterData.posted = $(document).find('input[name="radioPost"]:checked').val();
                    filterData.net_amount_filter = $(document).find('#net_amount_filter option:selected').val();
                    filterData.net_amount_filter_val = $(document).find('#net_amount_filter_val').val();
                    filterData.payment_mode = $(document).find('#payment_mode option:selected').val();


                    // inline column filter
                    filterData.inline = {};
                    var tr = $('.listing_data_table>table>thead>tr');

                    for (var key in dataFields) {
                        var val = tr.find('input[name='+key+']').val();
                        if(!valueEmpty(val)){
                            filterData.inline[key] = val;
                        }
                    }

                    $('.kt-container').css({'pointer-events':'none','opacity':'0.5'});

                    localStorage.removeItem('ajax_data-1-meta');

                    datatable.search(filterData, 'globalFilters');
                });

                $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
                    var val = $(this).val();
                    $('.listing_data_table').find('thead>tr>th').each(function(){
                        var th_val = $(this).attr('data-field');
                        if(val == th_val){
                            $(this).toggle();
                        }
                    });
                    $('.listing_data_table').find('tbody>tr>td').each(function(){
                        var td_val = $(this).attr('data-field');
                        if(val == td_val){
                            $(this).toggle();
                        }
                    });
                });
            };

            var eventsCapture = function() {
                $('.kt-datatable').on('kt-datatable--on-init', function() {
                    console.log("f");
                }).on('kt-datatable--on-layout-updated', function() {
                    console.log("f1");
                    $(document).find('.kt_datepicker_inline').datepicker('disable');
                    /* for 2nd tr th
                    $('.inline_filter').remove();
                    $('.kt-datatable thead').append('<tr class="kt-datatable__row inline_filter"></tr>')
                    var newTr = $('.inline_filter');*/

                    var date_fields = [];
                    for (var key in dataFields) {
                        if(['date','datetime'].includes(dataFields[key]['type'])){
                            date_fields.push(key);
                        }
                    }
                    $('.kt-datatable thead tr:first-child th').not('th:first-child').each(function() {
                        var thix = $(this);
                        var data_field = thix.attr('data-field');
                        var name_title = thix.find('span').text();
                        var width = thix.find('span').width();
                        width = parseFloat(width);
                        var widthPx = "width:"+width+"px";
                        var className = 'class="'+data_field+'"';
                        var val = "";
                        if(!valueEmpty(inline_filter_data[data_field])){
                            val = inline_filter_data[data_field];
                        }
                        if(date_fields.includes(data_field)){
                            className = 'class="'+data_field+' kt_datepicker_inline"';
                        }
                        // for 2nd tr th // newTr.append('<th class="kt-datatable__cell"><span style="'+widthPx+'"><input type="text" name="'+data_field+'" '+className+' value="'+val+'" style="width: 100%;"/></span></th>');
                        thix.html('<span style="'+widthPx+'">'+name_title+'<input type="text" name="'+data_field+'" '+className+' value="'+val+'" style="width: 100%;"/></span>');

                    });
                    // for 2nd tr th // $('.kt-datatable thead tr.inline_filter th:last-child').find('span').html("");
                    $('.kt-datatable thead tr:last-child th:last-child').find('span input').remove();

                    $(document).find('.kt_datepicker_inline').datepicker({
                        rtl: KTUtil.isRTL(),
                        todayHighlight: true,
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        }
                    });

                    var net_amt = 0;
                    $('table>tbody>tr').each(function(){
                        var amt = $(this).find("td[data-field='purchase_order_total_net_amount']>span").text();
                        net_amt += parseFloat(amt);
                    });
                    $('.grn_total_amount').html(net_amt.toLocaleString());

                }).on('kt-datatable--on-ajax-done', function() {
                    console.log("f2");
                    $('.kt-container').css({'pointer-events':'','opacity':''});
                });
            };


            var funcDateFormat =  function(date){
                var dd = new Date(date).toLocaleString();
                var d = new Date(dd);
                var returnDate = "";
                if(d){
                    var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
                    var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
                    var year = d.getFullYear();
                    if(!valueEmpty(day) && !valueEmpty(month) && !valueEmpty(year)){
                        returnDate =  day +'-'+ month +'-'+ year;
                    }
                }
                return returnDate;
            };

            var funcDateTimeFormat =  function(date){
                // console.log(date);
                var dd = new Date(date).toLocaleString();
                var d = new Date(dd);
                var returnDate = "";
                if(d){
                    console.log(d);
                    var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
                    var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
                    var year = d.getFullYear();
                    var time = d.toLocaleTimeString();
                    if(!valueEmpty(day) && !valueEmpty(month) && !valueEmpty(year) && !valueEmpty(time)){
                        returnDate =  day +'-'+ month +'-'+ year +' '+ time;
                    }
                }
                return returnDate;
            };

            return {
                // public functions
                init: function() {
                    demo();
                    eventsCapture();
                    funcDateFormat();
                    funcDateTimeFormat();
                },
            };
        }();

        jQuery(document).ready(function() {
            KTDatatableRemoteAjaxDemo.init();
            $(document).on('keyup change','.kt-datatable thead tr th input',function(){
                $('.kt-datatable thead tr th').each(function() {
                    var val = $(this).find('input').val();
                    var key = $(this).find('input').attr('name');
                    inline_filter_data[key] = val;
                });
            })

            var pageSpinner = '<div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span>';
            var xhrGetData = true;
            $(document).on('click','#fbrSaleTaxPost',function(){
                var fbrIds = [];
                $('.kt-datatable tbody tr').each(function() {
                    var thix = $(this);
                    var fbr_checkbox = thix.find('.fbr_checkbox>input[type="checkbox"]:checked').val();
                    if(!valueEmpty(fbr_checkbox)){
                        fbrIds.push(fbr_checkbox);
                    }
                });
                var thix = $(this);
                var val = thix.val();
                var container = $('.kt-container');

                var validate = true;
                if(valueEmpty(fbrIds.length)){
                    toastr.error("please checked any invoice no");
                    validate = false;
                    return true;
                }
                if(validate && xhrGetData){
                    var disabledElement = container;
                    xhrGetData = false;
                    var formData = {
                        'sale_ids' : fbrIds
                    };
                    var url = "{{action('Sales\PaymentModeController@fbrSaleInvoiceTaxPost')}}";
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: url,
                        dataType	: 'json',
                        data        : formData,
                        beforeSend: function( xhr ) {
                            disabledElement.addClass('pointerEventsNone');
                        },
                        success: function(response,data) {
                            console.log(response);
                            if(response.status == 'success'){
                                toastr.success(response.message);

                            }else{
                                toastr.error(response.message);
                            }
                            xhrGetData = true;
                            disabledElement.removeClass('pointerEventsNone');
                        },
                        error: function(response,status) {
                            toastr.error(response.responseJSON.message);
                            xhrGetData = true;
                            disabledElement.removeClass('pointerEventsNone');
                        }
                    });
                }
            })
        });
    </script>
@endsection

