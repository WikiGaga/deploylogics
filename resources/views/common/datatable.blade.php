@extends('layouts.datatable')
@section('title', 'Datatable')

@section('pageCSS')
@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            {{--<div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {{$data['title']}}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                            &nbsp;
                            <a href="/{{$data['path']}}/new" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                New
                            </a>
                        </div>
                    </div>
                </div>
            </div>--}}
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
                        <a href="/{{$data['path-form']}}" class="btn btn-brand btn-elevate btn-sm btn-icon-sm">
                            <i class="la la-plus"></i> New
                        </a>
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="flaticon-more"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" aria-labelledby="dropdownMenu1">
                                @foreach($data['headings'] as $heading)
                                    <li >
                                        <label>
                                            <input value="{{$heading}}" type="checkbox" checked> {{$heading}}
                                        </label>
                                    </li>
                                @endforeach
                                    <li >
                                        <label>
                                            <input value="Action" type="checkbox" checked> Action
                                        </label>
                                    </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end: Search Form -->
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <!--begin: Datatable -->

                <table class="kt-datatable data_table index_listing_datatable" id="data_table" width="100%">
                    <thead>
                    <tr>
                        {{--<th class="kt-datatable__cell--check">
                            <label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid">
                                <input type="checkbox">&nbsp;
                                <span></span>
                            </label>
                        </th>--}}
                        @foreach($data['headings'] as $heading)
                            <th>{{$heading}}</th>
                        @endforeach
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($data['table'] as $tr)
                        @php
                            $pKey = $data['primaryKeyName'];
                        @endphp
                        <tr>
                            {{--<td>
                                <label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid">
                                    <input type="checkbox">&nbsp;
                                    <span></span>
                                </label>
                            </td>--}}
                            @foreach($data['columnName'] as $key)
                                <td>{{$tr->$key}}</td>
                            @endforeach
                            <td>
                                <a href="/{{$data['path-form']}}/{{$tr->$pKey}}" class="btn btn-sm btn-primary btn-icon btn-icon-sm" title="Edit">
                                    <i class="la la-pencil"></i>
                                </a>
                                <button data-url="/{{$data['path']}}/delete/{{$tr->$pKey}}" id="del" class="btn btn-danger btn-sm btn-icon btn-icon-sm" title="Delete">
                                    <i class="la la-trash"></i>
                                </button>
                                <a href="/{{$data['path']}}/print/{{$tr->$pKey}}" target="_blank" class="btn btn-sm btn-success btn-icon btn-icon-sm" title="Print">
                                    <i class="la la-print"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <!--end: Datatable -->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
@endsection
@section('pageJS')

@endsection

@section('customJS')
<script>
    $('#searchAll').keyup(function(e) {
        var searchStr = $(this).val();
        var caseType = $(this).attr('data-type');
        $.ajax({
            type:'GET',
            url:'/get-data/'+ caseType +'/'+ searchStr,
            data:{},

            success: function(response, status){
               // console.log(response);
            }
        });
    })

</script>

<script src="{{ asset('js/pages/js/data-delete.js') }}" type="text/javascript"></script>

@endsection
