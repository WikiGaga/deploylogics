@extends('layouts.new_datatable')
@section('title', 'Datatable')

@section('pageCSS')
@endsection
@section('content')
    @php
        $create = $data['menu_dtl_id'].'-create';
        $edit = $data['menu_dtl_id'].'-edit';
        $del = $data['menu_dtl_id'].'-delete';
        $print = $data['menu_dtl_id'].'-print';
    @endphp
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
    <script>
        var dataFields = {
            @foreach($data['table_colums'] as $key=>$heading)
                "{{$key}}": "{{$heading}}",
            @endforeach
        };
        var path_url = {
                'path' : "{{$data['path']}}",
                'path_form' : "{{$data['path-form']}}"
            }
    </script>
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
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
                            <input type="text" class="form-control form-control-sm" placeholder="Search..." id="generalSearch">
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-search"></i></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4 text-right">
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-sm btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-download"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul class="kt-nav">
                                    <li class="kt-nav__section kt-nav__section--first">
                                        <span class="kt-nav__section-text">Choose an option</span>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon la la-print"></i>
                                            <span class="kt-nav__link-text">Print</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                            <span class="kt-nav__link-text">Excel</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon la la-file-text-o"></i>
                                            <span class="kt-nav__link-text">CSV</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon la la-file-pdf-o"></i>
                                            <span class="kt-nav__link-text">PDF</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @permission($create)
                            <a href="/{{$data['path-form']}}" id="btn-create" class="btn-create btn btn-brand btn-elevate btn-sm btn-icon-sm">
                                <i class="la la-plus"></i> New
                            </a>
                        @endpermission
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="flaticon-more"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                                @foreach($data['table_colums'] as $key=>$heading)
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
        <div class="kt-portlet__body kt-portlet__body--fit">

            <!--begin: Datatable -->
            <div class="kt-datatable ajax_data_table" data-url="{{ action('Common\DataTableController@index',$data['case']) }}"  id="ajax_data"></div>
            <!--end: Datatable -->
        </div>
    </div>
    <!-- end:: Content -->
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script src="{{ asset('js/pages/js/data-ajax.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/js/data-delete.js') }}" type="text/javascript"></script>
@endsection
