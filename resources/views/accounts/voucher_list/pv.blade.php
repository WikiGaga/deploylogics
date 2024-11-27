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
        // dd($data);
        $view = $data['menu_dtl_id'].'-view';
        $create = $data['menu_dtl_id'].'-create';
        $edit = $data['menu_dtl_id'].'-edit';
        $del = $data['menu_dtl_id'].'-delete';
        $print = $data['menu_dtl_id'].'-print';
        $changePass = $data['menu_dtl_id'].'-change_password';
        $data['table_columns'] = [
              "voucher_no" => "Voucher NO",
              "voucher_date" => "Voucher Date",
              "amount" => "Amount",
              "debit_account_name" => "Debit Account",
              "credit_account_name" => "Credit Account",
              "voucher_notes" => "Remarks",
              "user_name" => "Entry User",
              "created_at" => "Entry Date",
        ];
        $data['case'] = 'pv';
    @endphp
    <script>
        var btnEditView = false;
        var btnDelView = false;
        var btnPrintView = false;
        var casetype = '{{$data['case']}}'
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
    @permission($view)
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid backgroud_img">
        <div class="kt-portlet kt-portlet--mobile" style="margin-bottom: 5px;">
            <div class="kt-portlet__body">
                <!--begin: Search Form -->
                <div class="row">
                    <div class="col-md-4">
                        <h5 class="kt-portlet__head-title">
                            {{$data['title']}}
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input type="text" class="form-control form-control-sm" placeholder="Search..." id="generalSearch" autofocus>
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        @permission($create)
                        <a href="{{$data['path-form']}}" id="btn-create" class="btn-create btn btn-success btn-elevate btn-sm btn-icon-sm">
                            <i class="la la-plus"></i>
                        </a>
                        @endpermission
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="flaticon-more"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                                @foreach($data['table_columns'] as $key=>$heading)
                                    <li >
                                        <label>
                                            <input value="{{$key}}" type="checkbox" checked> {{$heading}}
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
                <!--end: Search Form -->
            </div>
        </div>

        @include('partial_script.date_filter_listing')
        
        @include('partial_script.custom_filter_listing')

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
            <div class="kt-datatable ajax_data_table listing_data_table" data-url="{{ action('Accounts\VoucherListController@pvList') }}" id="ajax_data"></div>
            {{-- <div class="kt-datatable ajax_data_table listing_data_table" data-url="{{ action('Accounts\VoucherListController@pvList') }}" id="dynamic_ajax_data"></div> --}}
            <!--end: Datatable -->
        </div>
        <div style="background: #50cd89;padding: 10px;color: #fff;font-weight: 400;font-size: 18px;"> Total Amount: <span class="grn_total_amount">0</span></div>
    </div>
    <!-- end:: Content -->
    @endpermission {{--end view permission--}}
@endsection
@section('pageJS')

@endsection

@section('customJS')
    {{-- <script src="{{ asset('js/pages/listing/data-listing.js?v=').time() }}" type="text/javascript"></script> --}}
    {{-- <script src="{{ asset('js/pages/js/data-delete.js?v=').time() }}" type="text/javascript"></script> --}}
    {{-- <div class="modal fade" id="kt_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>
            </div>
        </div>
    </div> --}}
    {{-- <script src="{{ asset('js/pages/js/data-ajax.js?v=').time() }}" type="text/javascript"></script> --}}
    <script>
        "use strict";
        var inline_filter_data = {};
        // Class definition
       /* $(document).ready(function() {
            $('.kt-datatable thead th').each(function() {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');
            });
        });*/
        var KTDatatableRemoteAjaxDemo = function() {
            // Private functions

            // basic demo
            var demo = function() {
                localStorage.removeItem('ajax_data-1-meta');
                var table = $('.kt-datatable');
                var tableUrl = table.attr('data-url');
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
                        scroll: false,
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
                    columns: [
                        {
                            field: 'voucher_no',
                            title: 'Voucher NO',
                            // sortable: 'desc',
                        }, {
                            field: 'voucher_date',
                            title: 'Voucher Date',
                            template: function(row) {
                                return funcDateFormat(row.voucher_date);
                            },
                        }, {
                            field: 'amount',
                            title: 'Amount',
                            type: 'number',
                        }, {
                            field: 'debit_account_name',
                            title: 'Debit Account',
                        },{
                            field: 'credit_account_name',
                            title: 'Credit Account',
                        },{
                            field: 'voucher_notes',
                            title: 'Remarks',
                        },{
                            field: 'user_name',
                            title: 'Entry User',
                        }, {
                            field: 'created_at',
                            title: 'Entry Date',
                            template: function(row) {
                                return funcDateFormat(row.created_at);
                            },
                        }, {
                            field: 'Actions',
                            title: 'Actions',
                            sortable: false,
                            width: 110,
                            overflow: 'visible',
                            autoHide: false,
                            template: function(row) {
                                var key_id = row.voucher_id;
                                var dropdownLink = false;
                                var btnDropdownLink = "";
                                var btnEdit = "";
                                var btnDel = "";
                                var btnPrint = "";
                                if(btnPrintView){
                                    var btnPrint = '<a class="dropdown-item" href="/accounts/pv/print/'+key_id+'"><i class="la la-edit"></i>Print</a>';
                                    dropdownLink = true;
                                }
                                if(btnEditView){
                                    var btnEdit = '<a href="/accounts/pv/form/'+key_id+'" class="btn btn-sm btn-soft btn-icon btn-icon-sm" title="Edit">\
                                        <i class="la la-edit"></i>\
                                    </a>';
                                }
                                if(btnDelView){
                                    var btnDel = '<button type="button" data-url="/accounts/pv/delete/'+key_id+'" id="del"  class="btn btn-sm btn-soft btn-icon btn-icon-sm mlr" title="Delete">\
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

                                return  btnEdit + btnDel + btnDropdownLink;
                            },
                        }],
                });

                $('body').on('submit', 'form[name="getRecordsByDateFilter"]', function(event) {
                    event.preventDefault();
                    var filterData = {};
                    var date_type = $(document).find('form input[name="radioDate"]:checked').val()
                    filterData.date = date_type;
                    filterData.time_from = $(document).find('form input[name="time_from"]').val();;
                    filterData.time_to = $(document).find('form input[name="time_to"]').val();;
                    if(date_type == 'custom_date'){
                        filterData.from = $(document).find('form input[name="from"]').val();
                        filterData.to = $(document).find('form input[name="to"]').val();
                    }
                    var global_search = $('#generalSearch').val();
                    if(!valueEmpty(global_search)){
                        filterData.global_search = global_search;
                    }

                    // inline column filter
                    filterData.inline = {};
                    var tr = $('.listing_data_table>table>thead>tr');
                    var voucher_no = tr.find('input[name=voucher_no]').val();
                    if(!valueEmpty(voucher_no)){
                        filterData.inline.voucher_no = voucher_no;
                    }
                    var voucher_date = tr.find('input[name=voucher_date]').val();
                    if(!valueEmpty(voucher_date)){
                        filterData.inline.voucher_date = voucher_date;
                    }
                    var amount = tr.find('input[name=amount]').val();
                    if(!valueEmpty(amount)){
                        filterData.inline.amount = amount;
                    }
                    var debit_account_name = tr.find('input[name=debit_account_name]').val();
                    if(!valueEmpty(debit_account_name)){
                        filterData.inline.debit_account_name = debit_account_name;
                    }
                    var credit_account_name = tr.find('input[name=credit_account_name]').val();
                    if(!valueEmpty(credit_account_name)){
                        filterData.inline.credit_account_name = credit_account_name;
                    }
                    var voucher_notes = tr.find('input[name=voucher_notes]').val();
                    if(!valueEmpty(voucher_notes)){
                        filterData.inline.voucher_notes = voucher_notes;
                    }
                    var user_name = tr.find('input[name=user_name]').val();
                    if(!valueEmpty(user_name)){
                        filterData.inline.user_name = user_name;
                    }
                    var created_at = tr.find('input[name=created_at]').val();
                    if(!valueEmpty(created_at)){
                        filterData.inline.created_at = created_at;
                    }

                   // $('.kt-container').css({'pointer-events':'none','opacity':'0.5'});
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
                    $('.inline_filter').remove();
                    $('.kt-datatable thead').append('<tr class="kt-datatable__row inline_filter"></tr>')
                    var newTr = $('.inline_filter');
                    console.log(inline_filter_data);
                    $('.kt-datatable thead tr:first-child th').each(function() {
                        var data_field = $(this).attr('data-field');
                        var width = $(this).find('span').width();
                        width = parseFloat(width);
                        var widthPx = "width:"+width+"px";
                        var className = 'class="'+data_field+'"';
                        var val = "";
                        if(!valueEmpty(inline_filter_data[data_field])){
                            val = inline_filter_data[data_field];
                        }
                        var date_fields = ['voucher_date','created_at'];
                        if(date_fields.includes(data_field)){
                            className = 'class="'+data_field+' kt_datepicker_inline"';
                        }
                        newTr.append('<th class="kt-datatable__cell"><span style="'+widthPx+'"><input type="text" name="'+data_field+'" '+className+' value="'+val+'" style="width: 100%;"/></span></th>');
                    });
                    $('.kt-datatable thead tr.inline_filter th:last-child').find('span').html("");

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
                        var amt = $(this).find("td[data-field='amount']>span").text();
                        net_amt += parseFloat(amt);
                    });
                    $('.grn_total_amount').html(net_amt.toLocaleString());
                    
                }).on('kt-datatable--on-ajax-done', function() {
                    console.log("f2");
                    $('.kt-container').css({'pointer-events':'','opacity':''});
                });
            };
            return {
                // public functions
                init: function() {
                    demo();
                    eventsCapture();
                },
            };
        }();

        jQuery(document).ready(function() {
            KTDatatableRemoteAjaxDemo.init();
            $(document).on('keyup change','.kt-datatable thead tr.inline_filter th input',function(){
                $('.kt-datatable thead tr.inline_filter th').each(function() {
                    var val = $(this).find('input').val();
                    var key = $(this).find('input').attr('name');
                    inline_filter_data[key] = val;
                });
                console.log(inline_filter_data);
            })
        });
        function funcDateFormat(date){
            var dd = new Date(date).toLocaleString();
            var d = new Date(dd);
            var returnDate = "";
            if(d){
                var day =   (parseInt(d.getDate()) < 10) ? "0" + (d.getDate()).toString() : d.getDate();
                var month = (parseInt(d.getMonth()) < 10) ? "0" + (d.getMonth() + 1).toString() : (d.getMonth() + 1);
                var year = d.getFullYear();
                if(!valueEmpty(day) && !valueEmpty(month) && !valueEmpty(year)){
                    var returnDate =  day +'-'+ month +'-'+ year;
                }
            }
            return returnDate;
        }
    </script>
    <script src="{{ asset('js/pages/js/data-delete.js?v=').time() }}" type="text/javascript"></script>
    <div class="modal fade" id="kt_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>
            </div>
        </div>
    </div>
@endsection

