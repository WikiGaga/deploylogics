@extends('layouts.layout')
@section('title', 'Form')
@section('pageCSS')
@endsection

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile staging_portlet">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        City
                    </h3>
                    <div class="erp-page--actions">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            <button type="button" class="btn btn-outline-info">Approve</button>
                            <button type="button" class="btn btn-outline-info">Post</button>
                            <button type="button" class="btn btn-outline-info">Archive</button>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        @include('staging_activity.breadcrumb')
                        <a href="" id="btn-back" class="btn btn-clean btn-sm btn-icon-sm back ">
                            <i class="la la-long-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                    {{-- header
                        -- title
                        -- action
                        -- breadcrum
                    --}}

                    {{-- form --}}

                    {{-- form activity --}}
                @include('staging_activity.action_notes')
                @include('staging_activity.recent_activity')
            </div>
        </div>
    </div>
@endsection

@section('pageJS')
@endsection
@section('customJS')

@endsection
