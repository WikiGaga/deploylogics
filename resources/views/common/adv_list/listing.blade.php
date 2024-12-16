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
        $complete_module = $data['menu_dtl_id'].'-complete_module';
        $close_module = $data['menu_dtl_id'].'-close_module';
        $un_post_module = $data['menu_dtl_id'].'-un_post_module';
        $post_module = $data['menu_dtl_id'].'-post';
      //  dd($data['table_columns']);

     // dd($changePass);
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
        var btnEditView = false;
        var btnDelView = false;
        var btnPrintView = false;
        var btnCompleteAuditView = false;
        var btnCloseAuditView = false;
        var btnunpostAuditView = false;
        var btnpostView = false;
        var pathAction = '{{$data['form-action']}}'
        var table_id = '{{$data['table_id']}}'
        var casetype = '{{$data['case']}}'
        var user_id = '{{ auth()->user()->id }}'
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

    @permission($complete_module)
    <script>
        var btnCompleteAuditView = true;
    </script>
    @endpermission

    @permission($close_module)
    <script>
        var btnCloseAuditView = true;
    </script>
    @endpermission

    @permission($un_post_module)
    <script>
        var btnunpostAuditView = true;
    </script>
    @endpermission

    @permission($post_module)
    <script>
        var btnpostView = true;
    </script>
    @endpermission
    @permission($view)
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid backgroud_img">
        <div class="kt-portlet kt-portlet--mobile" style="margin-bottom: 0px;">
            <div class="kt-portlet__body">
                <!--begin: Search Form -->
                <div class="row">
                    <div class="col-md-3">
                        <h5 class="kt-portlet__head-title">
                            {{$data['title']}}
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <div class="kt-input-icon kt-input-icon--left">
                            <input type="text" class="form-control form-control-sm" placeholder="&nbsp; Search..." id="generalSearch" autofocus>
                            <span class="kt-input-icon__icon kt-input-icon__icon--left search-icon-bg">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        @permission($create)
                        <a href="{{$data['create-form']}}" id="btn-create" class="btn-create btn btn-success btn-elevate btn-sm btn-icon-sm mr-4">
                            <i class="la la-plus"></i>
                        </a>
                        @endpermission
                        <button type="button" data-url="{{ action('Common\ListingController@openListingUserFilterModal',$data['case']) }}" class="btn btn-bold btn-label-brand btn-sm" data-toggle="modal" id="listing_user_filter" data-target="#kt_modal_1">
                            <i class="fa fa-filter"></i>
                        </button>
                        <form method="get" name="getRecordsByDateFilter" class="form-group d-inline-block">
                            <div class="form-group d-inline-block">
                                <select class="form-select btn btn-md btn-default" name="radioDate" style="padding: 0.545rem 0.75rem;">
                                    <option value="all">All</option>
                                    <option value="today" selected>Today</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="last_7_days">Last 7 Days</option>
                                    <option value="last_30_days">Last 30 Days</option>
                                </select>
                            </div>
                            <div class="btn-group btn-group-md" role="group" aria-label="Button group with nested dropdown">
                                <button type="submit" class="btn btn-md btn-default" id="getRecordsByDateFilter">
                                <i class="la la-th-list"></i>Filter</button>
                                {{-- <button type="button" class="btn btn-sm btn-primary" onclick="window.location.href=window.location.href">Reset Filter</button> --}}
                            </div>
                        </form>
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-md " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-download"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                                <li>
                                    <a href="javascript:void(0);" id="export_csv" class="export-option">
                                        <img  src="{{asset('assets/images/excel.svg')}}" alt="CSV" style="width: 16px; height: 16px;" class="mr-2"> CSV
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" id="export_pdf" class="export-option">
                                        <img src="{{asset('assets/images/pdf.svg')}}" alt="CSV" style="width: 16px; height: 16px;" class="mr-2"> PDF
                                    </a>
                                </li>
                                <li>
                                    <button type="button" data-url="{{ action('Common\ListingController@openListingDownloads',$data['case']) }}" class="btn btn-default btn-sm" data-toggle="modal" id="listing_user_downloads" data-target="#kt_modal_1">
                                        View Downloads
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-table"></i> Columns
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                                @foreach($data['table_columns'] as $key=>$heading)
                                    <li >
                                        <label>
                                            <input value="{{$key}}" type="checkbox" checked> {{$heading['title']}}
                                        </label>
                                    </li>
                                @endforeach
                                {{-- <li >
                                    <label>
                                        <input value="actions" type="checkbox" checked> Actions
                                    </label>
                                </li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end: Search Form -->
            </div>
        </div>

        {{-- @include('partial_script.date_filter_listing') --}}

        @if($data['case'] == 'pv' ||
            $data['case'] == 'cpv' ||
            $data['case'] == 'crv'||
            $data['case'] == 'pve'||
            $data['case'] == 'lv'||
            $data['case'] == 'jv'||
            $data['case'] == 'brpv'||
            $data['case'] == 'brrv'||
            $data['case'] == 'ipv'||
            $data['case'] == 'irv'||
            $data['case'] == 'rv'||
            $data['case'] == 'obv' ||
            $data['case'] == 'product-discount-setup' )

        @include('partial_script.custom_filter_listing')

        @endif

        <div class="kt-portlet__body kt-portlet__body--fit">
            <!--begin: Datatable -->
            <style>
                .search-icon-bg {
                    background-color: #b3b8bf;
                    color: #fff !important; /* White icon color */
                    border-radius: 4px; /* Optional: Rounded corners */
                    padding: 5px; /* Optional: Add padding for better spacing */
                }

                .search-icon-bg i {
                    color: #fff !important;
                }
                .dropdown-menu > li:hover {
                    background-color: #EBEDF1;
                }
                .kt-datatable>.kt-datatable__table{
                    /* max-height: 500px !important; */
                }
                /* .portlet__body--fit {
                    height: 100vh;
                    display: flex;
                    flex-direction: column;
                }

                .kt-datatable {
                    flex-grow: 1;
                    overflow: auto;
                } */
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
                .kt-datatable__head body{
                    line-height: 1.2;
                }
            </style>
            <div class="kt-datatable ajax_data_table listing_data_table" data-url="{{ $data['data_url'] }}" id="dynamic_ajax_data"></div>
            <!--end: Datatable -->

            @if(in_array($data['case'],['purchase-order','grn','purchase-return','pv']))
                <div style="background: #50cd89;padding: 0 10px;color: #fff;font-weight: 400;font-size: 18px;"> Total Amount: <span class="grn_total_amount">0</span></div>
            @endif
        </div>

    </div>
    <!-- end:: Content -->
    @endpermission {{--end view permission--}}
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/listing/data-listing.js?v=').time() }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/data-delete.js?v=').time() }}" type="text/javascript"></script>
    <div class="modal fade" id="kt_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>
            </div>
        </div>
    </div>
    <script>
        var userId = @json(Auth::user()->id);

        $('#listing_user_filter').on('click',function(e){
            var data_url = $(this).attr('data-url');
            $('#kt_modal_1').modal('show').find('.modal-content').load(data_url);
        });
        $('#listing_user_downloads').on('click',function(e){
            var data_url = $(this).attr('data-url');
            $('#kt_modal_1').modal('show').find('.modal-content').load(data_url);
        });

        $(document).on('click','.generateTags', function(){

            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            console.log(formData);
            var url = '/grn/grn-price-tag';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        var url = '/barcode-labels/multi-barcode-labels/form';
                        var win = window.open(url, "generateBarcodeTags");
                    }
                },
                error: function(response, status) {}
            });
        });
        $(document).on('click','.Adjustment', function(){

            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            var url = '/stock-audit/adjustment/adjustment';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        var url = '/stock-audit/stock-audit-adjustment/form/'+data_id;
                        var win = window.open(url);
                    }
                },
                error: function(response, status) {}
            });
        });
        $(document).on('click','.AuditClose', function(){

            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            var url = '/stock-audit/AuditClose/AuditClose';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        alert("Audit Successfully Close");
                    }
                },
                error: function(response, status) {}
            });
        });

        $(document).on('click','.AuditSuspend', function(){

            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            var url = '/stock-audit/AuditSuspend/AuditSuspend';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        alert("Audit Successfully Suspended");
                    }
                },
                error: function(response, status) {}
            });
        });

        $(document).on('click','.AuditComplete', function(){

            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            var url = '/stock-audit/AuditComplete/AuditComplete';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        alert("Audit Successfully Completed");
                    }
                },
                error: function(response, status) {}
            });
        });

        $(document).on('click','.UnPost', function(){
            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            var url = '/stock-audit/UnPost/UnPost';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        alert("Audit Successfully Un-Posted.");
                    }
                },
                error: function(response, status) {}
            });
        });

        $(document).on('click','.UnPosted', function(){
            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            var url = '/accounts/pve/UnPosted';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        alert("Successfully Un-Posted.");
                    }
                },
                error: function(response, status) {}
            });
        });

        $(document).on('click','.Posted', function(){
            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            var url = '/accounts/pve/Posted';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        alert("Successfully Posted.");
                    }
                },
                error: function(response, status) {}
            });
        });

    </script>

    <script>
        $(document).on('click','.generatePrice', function(){
            var formData = {};
            formData.data = [];
            var data_id = $(this).attr('data-id');
            formData.data.push(data_id);
            // console.log(formData);
            var url = '/grn/update-product-price';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData,
                success: function(response, data) {
                    if (response) {
                        var url = '/change-rate/form';
                        var win = window.open(url, "updatePrice");
                    }
                },
                error: function(response, status) {}
            });
        })

    </script>
@endsection

