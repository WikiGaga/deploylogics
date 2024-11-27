@extends('layouts.layout')
@section('title', 'Sale Payment Mode')

@section('pageCSS')
@endsection

@section('content')
    @php
        $case = isset($data['page_data']['type']) ? $data['page_data']['type'] : "";
            if($case == 'new'){}
            if($case == 'edit'){

            }
    @endphp
    @permission($data['permission'])
    <!-- begin:: Content -->
        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    @include('elements.page_header',['page_data' => $data['page_data']])
                </div>
                <!--begin::Form-->
                <div class="kt-portlet__body">

                </div>
                <!--end::Form-->
            </div>
        </div>
    <!-- end:: Content -->
    @endpermission
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection
