@extends('layouts.pattern_listing')
@section('title', $data['title'].' listing')

@section('pageCSS')
    <style>
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
    @endphp
@permission($view)
    <script>
        var casetype = "{{$data['case']}}";
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
    @permission($changePass)
    <script>
        var btnChangePassView = true;
    </script>
    @endpermission
    <script>
        var dataFields = {
            @foreach($data['table_columns'] as $key=>$heading)
            "{{$key}}": "{{$heading}}",
            @endforeach
        };
        var path_url = {
            'path' : "{{$data['path']}}",
            'path_form' : "{{$data['path-form']}}"
        }
    </script>
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
                        <button type="button" data-url="{{ action('Common\ListingController@openListingUserFilterModal',$data['case']) }}" class="btn btn-bold btn-label-brand btn-sm" data-toggle="modal" id="listing_user_filter" data-target="#kt_modal_1">
                            <i class="fa fa-filter"></i>
                        </button>
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-sm btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-file"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul class="kt-nav">
                                    <li class="kt-nav__item">
                                        @if($create == '54-create' || $create == '6-create')
                                            <a href="/{{$data['path']}}/import" class="kt-nav__link">
                                                <i class="kt-nav__link-icon la la-upload"></i>
                                                <span class="kt-nav__link-text">Import</span>
                                            </a>
                                        @endif
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon la la-download"></i>
                                            <span class="kt-nav__link-text">Export</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @permission($create)
                        <a href="/{{$data['path-form']}}" id="btn-create" class="btn-create btn btn-success btn-elevate btn-sm btn-icon-sm">
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
        <div class="kt-portlet__body kt-portlet__body--fit">

            <!--begin: Datatable -->
            <div class="kt-datatable ajax_data_table listing_data_table" data-url="{{ action('Common\ListingController@index',$data['case']) }}"  id="ajax_data"></div>
            <!--end: Datatable -->
        </div>
        @if(in_array($data['case'],['purchase-order','grn','purchase-return']))
        <div style="background: #50cd89;padding: 10px;color: #fff;font-weight: 400;font-size: 18px;"> Total Amount: <span class="grn_total_amount">0</span></div>
        @endif
    </div>
    <!-- end:: Content -->
@endpermission {{--end view permission--}}
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script>
        var column_type_name = '';
        $('#listing_user_filter').on('click',function(e){
            var data_url = $(this).attr('data-url');
            $('#kt_modal_1').modal('show').find('.modal-content').load(data_url);
        });
    </script>
    <script src="{{ asset('js/pages/js/data-ajax.js?v=').time() }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/data-delete.js?v=').time() }}" type="text/javascript"></script>
    <div class="modal fade" id="kt_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="kt-spinner kt-spinner--lg kt-spinner--success kt-spinner-center"> <span>loading..</span></div>
            </div>
        </div>
    </div>

    <script>
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
        })

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

