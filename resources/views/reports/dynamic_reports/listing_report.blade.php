@extends('layouts.report')
@php

    try{
        $data = Session::get('data');
        $headings = [];
        $column_types = [];
        $decimal = [];
        $calc = [];
        $fieldsKeys = [];
        $column_toggle = [];
        $elements = [];
        $report_tb_data = \App\Models\TblSoftReports::with('report_styling')->where('report_id',$data['report_id'])->first();
        $sr = $report_tb_data['report_column_sr_no'];

        $list = [];
        //check report status according to criteria

        if(isset($data['report_status'])){
            if($data['report_status'] == false){
                $report_status = false;
                $report_message = 'No Record Found' ;
            }
        }else{
            //dump($data['qry']);
            $list = \Illuminate\Support\Facades\DB::select($data['qry']);
        }

        // styles
        $styles = isset($report_tb_data->report_styling)?$report_tb_data->report_styling:[];
        $ThStyles = [];
        $TdStyles = [];
        if(count($styles) != 0){
            foreach ($styles as $k=>$style){
                if($style['report_styling_column_type'] == 'th'){
                    $ThStyles[$style['report_styling_column_no']][$style['report_styling_key']] = $style['report_styling_value'];
                }
                if($style['report_styling_column_type'] == 'td'){
                     $TdStyles[$style['report_styling_column_no']][$style['report_styling_key']] = $style['report_styling_value'];
                }
                if($style['report_styling_column_type'] == 'element'){
                     $elements[$style['report_styling_column_no']][$style['report_styling_key']] = $style['report_styling_value'];
                }
            }
        }
        if(count($elements) != 0){
            foreach ($elements as $eKey=>$element){
                if($element['column_toggle'] == 1){
                    array_push($headings,$element['heading_name']);
                    array_push($column_types,$element['column_type']);
                    array_push($decimal,$element['decimal']);
                    if($element['calc'] == 1){ array_push($calc,$eKey); }
                    array_push($column_toggle,$element['column_toggle']);
                    array_push($fieldsKeys,$element['key_name']);
                }
            }
            $count_elements = count($column_toggle);
        }
        // variables default value foe calulations
        $arr = [];
        foreach ($calc as $var)
        {
           //$a_{$var} = 0;
           $a_[$var] = 0;
        }

        $report_status = true;
    }catch (Exception $e){
        $report_status = false;
        $report_message = $e->getMessage();
    }
@endphp
@section('title', $report_tb_data['report_title'])
@if($report_status == true)
@section('pageCSS')
    <style>
        table#dynamic_report_table .grand_total>td{
            border-bottom: 2px solid #969696 !important;
            border-top: 2px solid #cecece !important;
            background-color: #f7f8fa;
            font-size: 15px;
        }
        table#dynamic_report_table tr th{
            border-top: 2px solid #777777 !important;
            border-bottom: 2px solid #777777 !important;
            background: #f9f9f9;
        }
        /*==========================
        start hidden checkbox
     */
        .dropdown-menu {
            min-width:10px;
            padding: 5px;
        }
        .dropdown-item {
            padding: 5px;
            font-size: 12px;
            font-weight: normal;
        }
        .dropdown-menu>.dropdown-item>i{
            font-size: 14px;
        }
        .dropdown-menu > .dropdown-item [class*=" la-"] {
            font-size: 14px; }

        .checkbox-menu li label {
            display: block;
            padding: 3px 10px;
            clear: both;
            font-weight: normal;
            line-height: 1.42857143;
            color: #333;
            white-space: nowrap;
            margin:0;
            transition: background-color .4s ease;
        }
        .checkbox-menu li input {
            margin: 0 5px;
            top: 2px;
            position: relative;
        }

        .checkbox-menu li.active label {
            background-color: #f5f5f5;
        }

        .checkbox-menu li label:hover,
        .checkbox-menu li label:focus {
            background-color: #f5f5f5;
        }

        .checkbox-menu li.active label:hover,
        .checkbox-menu li.active label:focus {
            background-color: #f5f5f5;
        }
        /*
            end hidden checkbox
        ==================================== */

        /* Styles go here */
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group;}
            tfoot>tr>td {padding:0 !important;}
            body {margin: 0;}
        }
        @if($sr == true)
            table tr>th:first-child {
                text-align: left;
                width: 10%;
            }
            table tr>td:first-child {
                text-align: left;
            }
        @endif
        @foreach($ThStyles as $k=>$thstyle)
            @if($loop->first && $sr != true)
                table tr>th:first-child {
            @elseif($sr != true)
                table tr>th:nth-child({{$k+1}}) {
            @else
                table tr>th:nth-child({{$k+2}}) {
            @endif
                @foreach($thstyle as $pro=>$val)
                    {{$pro}} : {{$val.' !important'}};
                @endforeach
            }
        @endforeach
        @foreach($TdStyles as $k=>$tdstyle)
            @if($loop->first && $sr != true)
                table tr.item_row>td:first-child {
            @elseif($sr != true)
                table tr.item_row>td:nth-child({{$k+1}}) {
            @else
                table tr.item_row>td:nth-child({{$k+2}}) {
            @endif
                @foreach($tdstyle as $pro=>$val)
                    {{$pro}} : {{$val.' !important'}};
                @endforeach
            }
        @endforeach
    </style>
@endsection
@section('content')
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            @include('reports.dynamic_reports.criteria_list')
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <div class="data_entry_header">
                        <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                <i class="flaticon-more" style="color: #666666;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                @if($sr == 1)
                                    <li >
                                        <label>
                                            <input value="0" type="checkbox" checked> Sr No.
                                        </label>
                                    </li>
                                @endif
                                @foreach($headings as $key=>$heading)
                                    <li >
                                        <label>
                                            <input value="{{($sr == 1)?$loop->iteration:$key}}" type="checkbox" checked> {{$heading}}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="dynamic_report_table" class="table bt-datatable table-bordered">
                        <tr class="header">
                            @if($sr == 1)
                                <th>Sr.</th>
                            @endif
                            @foreach($headings as $heading)
                                <th>{{$heading}}</th>
                            @endforeach
                        </tr>
                        @if(count($list) != 0 && count($headings) == count($fieldsKeys))
                            @foreach($list as $kd=>$dt)
                                <tr class="item_row">
                                    @if($sr == 1)
                                        <td>{{$loop->iteration}}</td>
                                    @endif
                                    @foreach($fieldsKeys as $key=>$fieldsKey)
                                            @if($column_types[$key] == 'varchar2')
                                                <td>{!! $dt->$fieldsKey !!}</td>
                                            @elseif($column_types[$key] == 'number')
                                                @php
                                                    $numVal = (int)$dt->$fieldsKey;
                                                    if(in_array($key,$calc)){
                                                        //$a_{$key} += $numVal;
                                                        //$arr[$key] = $a_{$key};
                                                        $a_[$key] += $numVal;
                                                        $arr[$key] = $a_[$key];
                                                    }
                                                @endphp
                                                <td>{!! $numVal !!}</td>
                                            @elseif($column_types[$key] == 'float')
                                                @php
                                                    $floatVal = (float)$dt->$fieldsKey;
                                                    if(in_array($key,$calc)){
                                                        //$a_{$key} += $floatVal;
                                                        //$arr[$key] = $a_{$key};
                                                        $a_[$key]+= $floatVal;
                                                        $arr[$key] = $a_[$key];
                                                    }
                                                @endphp
                                                <td>{!! number_format($floatVal,!empty($decimal[$key])?$decimal[$key]:0) !!}</td>
                                            @elseif($column_types[$key] == 'date')
                                                <td>{!! date('d-m-Y', strtotime($dt->$fieldsKey)) !!}</td>
                                            @endif

                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{($sr == 1)?count($headings)+1:count($headings)}}">
                                    No Data Found......
                                    @if(count($list) != 0 && count($headings) != count($fieldsKeys))
                                        error...
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if(count($calc) != 0)
                            <tr class="grand_total">
                                @if($sr == 1)
                                    <td class="rep-font-bold">Grand Total:</td>
                                    <td class="rep-font-bold"></td>
                                @else
                                    <td class="rep-font-bold">Grand Total:</td>
                                @endif
                                @for($i=1; $i < count($headings); $i++)
                                    <td class="text-right rep-font-bold">
                                        @if(isset($arr[$i]))
                                            {{number_format($arr[$i],3)}}
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endif
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
    <script>
        $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
            var table = document.getElementById('dynamic_report_table');
            var tr = table.querySelectorAll('tr');
            var tbody = table.querySelectorAll('tbody');
            tr.forEach(function(tr1) {
                tbody[0].appendChild(tr1);
            });
            var val = $(this).val();
            $('.table tr.header').find('th:eq('+val+')').toggle();
            $('.table tr.item_row').find('td:eq('+val+')').toggle();
            $('.table tr.grand_total').find('td:eq('+val+')').toggle();
            hiddenFiledsCount();

        });
        function hiddenFiledsCount(){
            var count = 0;
            var hiddenFiled = [];
            $('.dropdown-menu>li').each(function(){
                if(!$(this).find('label>input').is(':checked')){
                    count += 1;
                    hiddenFiled.push($(this).find('label>input').val());
                }
            });
            $('.hiddenFiledsCount>span').html(count);
        }

        // Html Table Sorting ASC and DESC
        const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
        const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
                v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
        )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
        // do the work...
        document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
            const table = th.closest('table');
           // console.log(table.querySelectorAll('tr.grand_total'));
            Array.from(table.querySelectorAll('tr.item_row'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                .forEach(tr => table.appendChild(tr) );

            var grand_total = th.closest('table>tbody').querySelectorAll('tr.grand_total');
            if(grand_total[0] == undefined){
                var grand_total = th.closest('table').querySelectorAll('tr.grand_total');
            }
            table.appendChild(grand_total[0])
        })));
    </script>
@endsection

@section('exportXls')
    @if($data['form_file_type'] == 'xls')
        <script>
            $(document).ready(function() {
                $("#dynamic_report_table").table2excel({
                    // exclude: ".noExport",
                    filename: "report.xls",
                });
            });
        </script>
    @endif
@endsection
@else
@section('content')
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head" style="padding: 36px">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">No Report Found...</h1>
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            {{$report_message}}
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
@endif
