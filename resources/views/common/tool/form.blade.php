@extends('layouts.layout')
@section('title', 'Importer Tool')

@section('pageCSS')
    <style>

    </style>
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
        if($case == 'new'){

        }
        if($case == 'edit'){

        }

    @endphp
    <form class="import_tool_form kt-form" method="post" action="{{ action('Common\DataImportController@store') }}">
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg erp-header-sticky">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <!--begin::Form-->
                <div class="kt-portlet__body">
                    <div class="form-group-block row">
                        <div class="col-lg-3">

                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
        </div>
    </form>
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection
