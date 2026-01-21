@extends('layouts.report')
@php

// dd('$limit');
    // try{
    $grn_id_code=DB::table('TBL_PURC_GRN')->pluck('grn_id','grn_code')->all();
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

        // $selectedLimit = request('limit', 100);
        // $page = request('page', 1);
        // $limit = (isset($limit) && $limit == 'All') ? null : (int) request('limit', 100);
        // $offset = ($page - 1) * $limit;

        //check report status according to criteria

        if(isset($data['report_status'])){
            if($data['report_status'] == false){
                $report_status = false;
                $report_message = 'No Record Found' ;
            }
        }else{
            //dump($data['qry']);

           $baseQuery = $data['qry'];
            $page = request('page', 1);

            $requestedLimit = request('limit', 100); // "All" or number
            $isAll = ($requestedLimit === 'All');

            // get total
            $sqlCount = "SELECT COUNT(*) AS total FROM ({$baseQuery}) T";
            $total = DB::select($sqlCount)[0]->total;

            if ($isAll) {

                // Fetch ALL records
                $limit = $total;   // perPage must be > 0
                $offset = 0;
                $record = DB::select($baseQuery);

            } else {
                // convert to integer ONLY when not "All"
                $limit = (int)$requestedLimit;
                if ($limit < 1) { $limit = 1; }

                $offset = ($page - 1) * $limit;

                $sqlData = $baseQuery . " OFFSET {$offset} ROWS FETCH NEXT {$limit} ROWS ONLY";
                $record = DB::select($sqlData);
            }

            // paginator
            $list = new \Illuminate\Pagination\LengthAwarePaginator(
                $record,
                $total,
                $limit,
                $page,
                [
                    'path' => request()->url(),
                    'query' => request()->query()
                ]



            );

            // $list = \Illuminate\Support\Facades\DB::select($baseQuery);
        }
        $count_no=count($list);

        // styles
        $styles = isset($report_tb_data->report_styling)?$report_tb_data->report_styling:[];
        $ThStyles = [];
        $TdStyles = [];
        $conditionalLogics = [];
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
                if($style['report_styling_column_type'] == 'conditional_logic'){
                     $conditionalLogics[$style['report_styling_column_no']][$style['report_styling_key']] = $style['report_styling_value'];
                }
            }
        }

        $conditionalLogicGroups = [];
        if(count($conditionalLogics) > 0){
            $tempGroups = [];
            foreach($conditionalLogics as $columnNo => $logicData){
                $keyParts = explode('_', $columnNo);
                $outerKey = isset($keyParts[0]) ? $keyParts[0] : 0;
                $innerKey = isset($keyParts[1]) ? $keyParts[1] : $columnNo;

                $groupNo = isset($logicData['outer_group_no']) ? $logicData['outer_group_no'] : $outerKey;

                if(!isset($tempGroups[$groupNo])){
                    $tempGroups[$groupNo] = [];
                }
                if(!isset($tempGroups[$groupNo][$innerKey])){
                    $tempGroups[$groupNo][$innerKey] = [];
                }
                foreach($logicData as $keyName => $keyValue){
                    if($keyName != 'outer_group_no'){
                        $tempGroups[$groupNo][$innerKey][$keyName] = $keyValue;
                    }
                }
            }
            $conditionalLogicGroups = $tempGroups;
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
        $arr = [];
        $count = [];
        foreach ($calc as $var)
        {
           //$a_{$var} = 0;
           $a_[$var] = 0;
        }

        $report_status = true;
    // }catch (Exception $e){
    //     $report_status = false;
    //     $report_message = $e->getMessage();
    // }
@endphp
@section('title', $report_tb_data['report_title'])
@if($report_status == true)
@section('pageCSS')
    <style>
        .cursor-pointer{
            cursor: pointer;
        }
        table#dynamic_report_table .grand_total>td{
            border-bottom: 2px solid #969696 !important;
            border-top: 2px solid #cecece !important;
            background-color: #f7f8fa;
            font-size: 15px;
        }
        table#dynamic_report_table tr th{
            border-top: 2px solid #777777 !important;
            border-bottom: 2px solid #777777 !important;
            background: #e8eaf6;
        }
        table#dynamic_report_table tbody tr.item_row:nth-child(even){
            background-color: #f5f5f5;
        }
        table#dynamic_report_table tbody tr.item_row:nth-child(odd){
            background-color: #ffffff;
        }
        table#dynamic_report_table tbody tr.item_row.row-deleted{
            background-color: #ffebee !important;
            color: #c62828;
        }
        table#dynamic_report_table tbody tr.item_row.row-deleted td{
            border-left: 3px solid #e57373;
        }
        table#dynamic_report_table tbody tr.item_row.row-deleted td:first-child{
            border-left: 4px solid #c62828;
        }
        table#dynamic_report_table tbody tr.item_row td.cell-zero-amount{
            background-color: #fff9c4 !important;
            color: #f57c00;
            font-weight: 600;
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

        .table-responsive-scroll {
            overflow-x: auto;
            overflow-y: auto;
            max-width: 100%;
            width: 100%;
            max-height: calc(100vh - 300px);
        }

        table#dynamic_report_table thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        table#dynamic_report_table thead tr th {
            position: sticky;
            top: 0;
            background: #e8eaf6 !important;
            z-index: 11;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #777777 !important;
            border-top: 2px solid #777777 !important;
        }
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
                    <div class="table-responsive-scroll">
                          <select onchange="changeLimit(this.value)">
                                <option value="100"  {{ $limit == 100 ? 'selected' : '' }}>100</option>
                                <option value="1000" {{ $limit == 1000 ? 'selected' : '' }}>1000</option>
                                <option value="2000" {{ $limit == 2000 ? 'selected' : '' }}>2000</option>
                                <option value="All"  {{ $limit == $total ? 'selected' : '' }}>All</option>
                            </select>
                        <table width="100%" id="dynamic_report_table" class="table bt-datatable table-bordered table2ExcelExport">
                            <thead>
                                <tr class="header">
                                    @if($sr == 1)
                                        <th>Sr.</th>
                                    @endif
                                    @foreach($headings as $heading)
                                        <th>{{$heading}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($list) != 0 && count($headings) == count($fieldsKeys))
                                    @foreach($list as $kd=>$dt)
                                        @php
                                            $rowClass = 'item_row';
                                            $rowStyle = '';
                                            $rowTextColor = '';

                                            if(count($conditionalLogicGroups) > 0){
                                                foreach($conditionalLogicGroups as $outerGroup => $innerRules){
                                                    $outerGroupMatches = true;
                                                    $groupBgColor = '';
                                                    $groupTextColor = '';

                                                    $innerGroupMatches = true;
                                                    $hasRules = false;
                                                    $allRulesMatched = true;
                                                    foreach($innerRules as $ruleKey => $rule){
                                                        $fieldName = isset($rule['field_name']) ? $rule['field_name'] : '';
                                                        $condition = isset($rule['condition']) ? $rule['condition'] : '';
                                                        $value = isset($rule['value']) ? $rule['value'] : '';
                                                        $value2 = isset($rule['value_2']) ? $rule['value_2'] : '';
                                                        $fieldType = isset($rule['field_type']) ? $rule['field_type'] : '';

                                                        if(empty($fieldName) || empty($condition)){
                                                            continue;
                                                        }
                                                        $hasRules = true;

                                                        $ruleMatches = false;
                                                        if(!property_exists($dt, $fieldName)){
                                                            $allRulesMatched = false;
                                                            $innerGroupMatches = false;
                                                            break 2;
                                                        }

                                                        $fieldValue = $dt->$fieldName;

                                                        switch(strtolower($condition)){
                                                            case 'equals':
                                                            case '=':
                                                                $ruleMatches = (strtolower($fieldValue) == strtolower($value));
                                                                break;
                                                            case 'not equals':
                                                            case '!=':
                                                            case '<>':
                                                                $ruleMatches = (strtolower($fieldValue) != strtolower($value));
                                                                break;
                                                            case 'greater than':
                                                            case '>':
                                                                $ruleMatches = ((float)$fieldValue > (float)$value);
                                                                break;
                                                            case 'less than':
                                                            case '<':
                                                                $ruleMatches = ((float)$fieldValue < (float)$value);
                                                                break;
                                                            case 'greater than or equal':
                                                            case '>=':
                                                                $ruleMatches = ((float)$fieldValue >= (float)$value);
                                                                break;
                                                            case 'less than or equal':
                                                            case '<=':
                                                                $ruleMatches = ((float)$fieldValue <= (float)$value);
                                                                break;
                                                            case 'contains':
                                                                $ruleMatches = (stripos($fieldValue, $value) !== false);
                                                                break;
                                                            case 'not contains':
                                                                $ruleMatches = (stripos($fieldValue, $value) === false);
                                                                break;
                                                            case 'null':
                                                                $ruleMatches = ($fieldValue === null || $fieldValue === '' || $fieldValue === 'NULL');
                                                                break;
                                                            case 'not null':
                                                                $ruleMatches = ($fieldValue !== null && $fieldValue !== '' && $fieldValue !== 'NULL');
                                                                break;
                                                            case 'between':
                                                                $ruleMatches = ((float)$fieldValue >= (float)$value && (float)$fieldValue <= (float)$value2);
                                                                break;
                                                            case 'starts with':
                                                                $ruleMatches = (stripos($fieldValue, $value) === 0);
                                                                break;
                                                            case 'ends with':
                                                                $ruleMatches = (substr(strtolower($fieldValue), -strlen($value)) === strtolower($value));
                                                                break;
                                                        }

                                                        if(!$ruleMatches){
                                                            $allRulesMatched = false;
                                                            $innerGroupMatches = false;
                                                            break;
                                                        }
                                                    }

                                                    if(!$allRulesMatched){
                                                        $innerGroupMatches = false;
                                                    }

                                                    if($hasRules && $innerGroupMatches){
                                                        foreach($innerRules as $ruleKey => $rule){
                                                            $fieldName = isset($rule['field_name']) ? $rule['field_name'] : '';
                                                            if(empty($fieldName)){
                                                                continue;
                                                            }
                                                            if(empty($groupBgColor) && isset($rule['background_color']) && !empty($rule['background_color'])){
                                                                $groupBgColor = $rule['background_color'];
                                                            }
                                                            if(empty($groupTextColor) && isset($rule['text_color']) && !empty($rule['text_color'])){
                                                                $groupTextColor = $rule['text_color'];
                                                            }
                                                        }
                                                    }

                                                    if($hasRules && $innerGroupMatches && $allRulesMatched){
                                                        if(!empty($groupBgColor)){
                                                            $rowStyle .= 'background-color: ' . $groupBgColor . ' !important; ';
                                                        }
                                                        if(!empty($groupTextColor)){
                                                            $rowStyle .= 'color: ' . $groupTextColor . ' !important; ';
                                                            $rowTextColor = $groupTextColor;
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }}" @if(!empty($rowStyle)) style="{{ $rowStyle }}" @endif >
                                            @if($sr == 1)
                                                <td>{{$loop->iteration}}</td>
                                            @endif
                                            @foreach($fieldsKeys as $key=>$fieldsKey)

                                            @php
                                                if($fieldsKey == 'grn_code'){
                                                    $class = "open_model clickable-cell TEXT-INFO";

                                                    $grn_code=$dt->$fieldsKey;
                                                    $grn_id=$grn_id_code[$dt->$fieldsKey] ?? null;
                                                }else{
                                                    $class = "";
                                                    $grn_code="";
                                                    $grn_id="";
                                                }

                                            @endphp

                                                    @if($column_types[$key] == 'varchar2')
                                                        <td class="{{ $class }}" data-grn_id="{{ $grn_id }}" data-grn_code="{{ $grn_code }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{!! $dt->$fieldsKey !!}</td>
                                                    @elseif($column_types[$key] == 'number')
                                                        @php
                                                            $numVal = (int)$dt->$fieldsKey;

                                                            if(in_array($key,$calc)){
                                                                //$a_{$key} += $numVal;
                                                                //$arr[$key] = $a_{$key};
                                                                $a_[$key] += $numVal;
                                                                $arr[$key] = $a_[$key];
                                                            }
                                                            $cellClass = $class;
                                                        @endphp
                                                        <td class="{{ $cellClass }}" data-grn_id="{{ $grn_id }}" data-grn_code="{{ $grn_code }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{!! $numVal !!}</td>
                                                    @elseif($column_types[$key] == 'float')
                                                        @php
                                                            $floatVal = (float)$dt->$fieldsKey;
                                                            if(in_array($key,$calc)){
                                                                //$a_{$key} += $floatVal;
                                                                //$arr[$key] = $a_{$key};
                                                                $a_[$key]+= $floatVal;
                                                                $arr[$key] = $a_[$key];
                                                            }
                                                            $cellClass = $class;
                                                        @endphp
                                                        <td class="{{ $cellClass }}" data-grn_id="{{ $grn_id }}" data-grn_code="{{ $grn_code }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{!! number_format($floatVal,!empty($decimal[$key])?$decimal[$key]:0) !!}</td>
                                                    @elseif($column_types[$key] == 'date')
                                                        <td class="{{ $class }}" data-grn_id="{{ $grn_id }}" data-grn_code="{{ $grn_code }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{!! date('d-m-Y', strtotime($dt->$fieldsKey)) !!}</td>
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
                                     <tr class="grand_total">
                                        @if($sr == 1)
                                            <td class="rep-font-bold">Count:</td>
                                            <td class="rep-font-bold"></td>
                                        @else
                                            <td class="rep-font-bold">Count:</td>
                                        @endif

                                        <td colspan="{{ count($headings) }}" class="text-center rep-font-bold">
                                            {{$count_no}}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        @if (!$isAll)
                            {{ $list->appends(['limit' => request('limit')])->links() }}
                        @endif
                    </div>
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
         $(document).on('click','.open_model',function(){


        var grn_code = $(this).data('grn_code');
        var grn_id = $(this).data('grn_id');

         console.log('cccccc',grn_id, grn_code)

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var formData = {

                form_id :   grn_id,
                form_code :  grn_code,
                form_type :"grn",
                menu_id : 23,

                //    form_id :   66114225181204,
                // form_code :  "GRN-0000002",
                // form_type :"grn",
                // menu_id : 23,
            }
            console.log('ddddd', formData)
            var data_url = '/upload-document';
            $('#kt_modal_md').modal('show').find('.modal-content').load(data_url,formData);
        })

     function changeLimit(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            url.searchParams.set('page', 1);
            window.location.href = url;
        }

        $('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
            var table = document.getElementById('dynamic_report_table');
            if (table) {
                var val = $(this).val();
                $('#dynamic_report_table thead tr.header').find('th:eq('+val+')').toggle();
                $('#dynamic_report_table tbody tr.item_row').find('td:eq('+val+')').toggle();
                $('#dynamic_report_table tbody tr.grand_total').find('td:eq('+val+')').toggle();
                hiddenFiledsCount();
            }
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
