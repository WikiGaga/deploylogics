@extends('layouts.layout')
@section('title', 'Module List')
@section('pageCSS')
    <style>
        .staging_portlet>.kt-portlet__head{
            min-height: 44px !important;
            background: #5867dd !important;
        }
        .staging_portlet>.kt-portlet__head .kt-portlet__head-title{
            color: #fff !important;
        }
        .staging_portlet__body_title {
            background-color: #0abb87;
            font-weight: 600;
            color: #fff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 7px 10px 4px 10px;
            width: fit-content;
            position: relative;
            z-index: 9;
            min-width: 125px;
        }
        .staging_portlet__body_title_line {
            height: 2px;
            background: #0abb87;
            position: relative;
            top: 16px;
        }
        .staging_portlet__body_table>table>tbody>tr:first-child>td {
            border-top: 0;
            padding: 5px 10px !important;
        }
        .staging_portlet__body_table>table>tbody>tr>td {
            border-top: 1px dashed #016ce6;
            padding: 5px 10px !important;
        }

        .staging_portlet__body_table>table>tbody>tr>td>.kt-widget11__title {
            font-size: 1.1rem;
            font-weight: 400;
            display: block;
            color: #454a5f;
            transition: color .3s ease;
        }
        .staging_portlet__body_table>table>tbody>tr:hover>td{
            cursor: pointer;
            background: #f6faff;
        }
    </style>
@endsection

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile staging_portlet">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        City
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                {{-- 2-loop start--}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="staging_portlet__body_title_line"></div>
                        <div class="staging_portlet__body_title">Approval</div>
                        <div class="staging_portlet__body_table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                 <tbody>
                                    <tr onclick="window.location.href='{{action('StagingActivityController@create',['city',18920520050802])}}';">
                                        <td> <span class="kt-widget11__title">Lahore</span> </td>
                                        <td>  Pakistan </td>
                                    </tr>
                                    <tr onclick="window.location.href='{{action('StagingActivityController@create',['city',4678689])}}';">
                                        <td> <span class="kt-widget11__title">Karachi</span> </td>
                                        <td>  Pakistan </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- 2-loop end--}}
            </div>
        </div>
    </div>
@endsection

@section('pageJS')
@endsection
@section('customJS')

@endsection
