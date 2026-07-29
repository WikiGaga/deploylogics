@extends('layouts.report')
@section('title', 'Chart of Account')

@section('pageCSS')
    <style>
        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
               </h6>
            </div>
            @include('reports.template.branding')
        </div>
        @php
            $ResultList = DB::table('tbl_acco_chart_account')->orderby('chart_code')->get();
        @endphp
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered table-hover">
                        <tr class="sticky-header">
                            <th class="text-center">Group</th>
                            <th class="text-center">Level</th>
                            <th class="text-center">Account Code</th>
                            <th class="text-left">Account Name</th>
                        </tr>
                        @foreach($ResultList as $list)
                            @php
                                $bg = '';
                                if($list->chart_level == 1){
                                    $bg = 'style=background:#f0f8ff;';
                                }
                                $pd = '';
                                if($list->chart_level == 2){
                                    $pd = 'pl-3';
                                }
                                if($list->chart_level == 3){
                                    $pd = 'pl-4';
                                }
                                if($list->chart_level == 4){
                                    $pd = 'pl-5';
                                }
                            @endphp
                        <tr {{$bg}}>
                            <td class="text-center">{{ $list->chart_group }}</td>
                            <td class="text-center">{{ $list->chart_level }}</td>
                            <td class="text-center"><span class="">{{ $list->chart_code }}</span></td>
                            <td><span class="{{$pd}}">{{ $list->chart_name }}</span></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot sale_invoice_footer" style="background: #f7f8fa">
            <div class="row">
                <div class="col-lg-12 kt-align-right">
                    <div class="date"><span>Date: </span>{{ date('d-m-Y') }} - <span>User: </span>{{auth()->user()->name}}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')

@endsection

@section('customJS')

@endsection
@section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#rep_sale_invoice_datatable").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection



