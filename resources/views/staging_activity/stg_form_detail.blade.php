@extends('layouts.template')
@section('title', 'Staging List')

@section('pageCSS')
    <style>
        .view_form{
            cursor: pointer;
        }
        .stg_table thead th, .stg_table thead td{
            padding-top: 5px !important;
            padding-bottom: 5px !important;
        }
    </style>
@endsection

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Flow {{$data['menu_dtl']->menu_dtl_name}}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <a href="/staging-dashboard" class="btn kt-badge kt-badge--dark kt-badge--lg kt-badge--rounded">
                            Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="alert alert-info font-weight-bold" role="alert" style="margin-bottom: 1.25rem;">
                    Mush Login from relevant branch to view its document. If branch not listed, please contact administrator.
                </div>
                @foreach($data['flows'] as $flow)
                    <div class="form-group row" style="background: #eff0ff">
                        <div class="erp-col-form-label col-lg-12">
                            <div>{{$flow->stg_flows_name}}</div>
                        </div>
                    </div>

                    @if(isset($data['flows_menu_dtl'][$flow->stg_flows_id]) && count($data['flows_menu_dtl'][$flow->stg_flows_id]) > 0)
                        @php
                          $count_cols = count($data['flows_menu_dtl']['cols']);
                           $col_width = 87 / $count_cols;
                           $col_width = number_format($col_width,3);
                        @endphp
                        <table class="table stg_table">
                            <thead>
                                <tr style="background: #fff1f6;">
                                    <th></th>
                                    @foreach($data['flows_menu_dtl']['titles'] as $titles)
                                        <th style="width:{{$col_width}}%;">
                                            {{$titles}}
                                        </th>
                                    @endforeach
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data['flows_menu_dtl'][$flow->stg_flows_id] as $m_dtl)
                                <tr>
                                    <td width="3%">
                                        <i class="fa fa-caret-right"></i>
                                    </td>
                                    @foreach($data['flows_menu_dtl']['cols'] as $col)
                                        <td style="width:{{$col_width}}%;">
                                            <span style="display:inline-block;font-weight: 500;">{{$m_dtl[$col]}}</span>
                                        </td>
                                    @endforeach
                                    <td width="10%">
                                        <div data-link="{{$m_dtl['link']}}" class="view_form">
                                            <span class="kt-badge kt-badge--danger kt-badge--md kt-badge--rounded">
                                                <i class="fa fa-eye"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="table">
                            <tbody>
                            <tr>
                                <td colspan="3">
                                    No document available
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('pageJS')

@endsection

@section('customJS')
    <script>
        $(document).on('click','.view_form',function(){
            var thix = $(this);
            var url = window.location.href
            const state = { 'PrevLink': url}
            window.history.pushState(state, "Prev Title", thix.attr('data-link'))
            window.location = thix.attr('data-link');
        })
    </script>
@endsection
