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
       //  dd($data);
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
        var btnEditView = false;
        var btnDelView = false;
        var btnPrintView = false;
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
                        <a href="{{$data['create-form']}}" id="btn-create" class="btn-create btn btn-success btn-elevate btn-sm btn-icon-sm">
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
            <div class="kt-datatable ajax_data_table listing_data_table" data-url="{{ $data['data_url'] }}" id="dynamic_ajax_data"></div>
            <!--end: Datatable -->

            @if(in_array($data['case'],['purchase-order','grn','purchase-return']))
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
    <script src="{{ asset('js/pages/listing/data-list.js?v=').time() }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/data-delete.js?v=').time() }}" type="text/javascript"></script>
    <div class="modal fade" id="kt_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>
            </div>
        </div>
    </div>
@endsection

