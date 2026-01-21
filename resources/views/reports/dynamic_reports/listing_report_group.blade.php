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


        if($report_tb_data['report_data_grouping_keys'] != "" && $report_tb_data['report_data_grouping_keys'] != null){
            $sortingKeys = explode(',',$report_tb_data['report_data_grouping_keys']);
            $key1 = strtolower(strtoupper($sortingKeys[0]));
            $key2 = strtolower(strtoupper($sortingKeys[1]));
            $key3 = strtolower(strtoupper($sortingKeys[2]));
            $key4 = strtolower(strtoupper($sortingKeys[3]));
        }

        $getdata = \Illuminate\Support\Facades\DB::select($data['qry']);

        $list = [];
        if(count($getdata) != 0){


            foreach ($getdata as $row)
            {
                if($key2 == 1){
                    $field_1 = date('Y-m-d', strtotime($row->$key1));
                }else{
                    $field_1 = $row->$key1;
                }
                if($key3 != ""){
                    if($key4 == 1){
                        $field_2 = date('Y-m-d', strtotime($row->$key3));
                    }else{
                        $field_2 = $row->$key3;
                    }
                }
                if($key3 != ""){
                    $list[$field_1][$field_2][] = $row;
                }else{
                    $list[$field_1][] = $row;
                }
            }
        }

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
        // variables default value foe calulations
        $arr = [];
        foreach ($calc as $var){
           $a_[$var] = 0;
        }
        $arr_grp = [];
        foreach ($calc as $var){
           $ag_[$var] = 0;
        }
        $arr_item = [];
        foreach ($calc as $var){
           $ai_[$var] = 0;
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
        table#dynamic_report_table tr th{
            border-top: 2px solid #777777 !important;
            border-bottom: 2px solid #777777 !important;
            background: #e8eaf6;
        }
        table#dynamic_report_table tr.item_row:nth-child(even){
            background-color: #f5f5f5;
        }
        table#dynamic_report_table tr.item_row:nth-child(odd){
            background-color: #ffffff;
        }
        table#dynamic_report_table tr.item_row.row-deleted{
            background-color: #ffebee !important;
            color: #c62828;
        }
        table#dynamic_report_table tr.item_row.row-deleted td{
            border-left: 3px solid #e57373;
        }
        table#dynamic_report_table tr.item_row.row-deleted td:first-child{
            border-left: 4px solid #c62828;
        }
        table#dynamic_report_table tr.item_row td.cell-zero-amount{
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
                table tr.item_row>td:first-child,
                table tr.sub_total>td:first-child,
                table tr.total>td:first-child,
                table tr.grand_total>td:first-child {
        @elseif($sr != true)
                table tr.item_row>td:nth-child({{$k+1}}),
                table tr.sub_total>td:nth-child({{$k+1}}),
                table tr.total>td:nth-child({{$k+1}}),
                table tr.grand_total>td:nth-child({{$k+1}}) {
        @else
                table tr.item_row>td:nth-child({{$k+2}}),
                table tr.sub_total>td:nth-child({{$k+2}}),
                table tr.total>td:nth-child({{$k+2}}),
                table tr.grand_total>td:nth-child({{$k+2}}) {
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

        table#dynamic_report_table tr.header {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        table#dynamic_report_table tr.header th {
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
            <div class="row d-none">
                <div class="col-lg-12 text-right">
                    <div class="data_entry_header">
                        <div class="hiddenFiledsCount" style="display: inline-block;"><span>0</span> fields hide</div>
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon btn-sm btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 15px; border: 0;">
                                <i class="flaticon-more" style="color: #666666;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right checkbox-menu allow-focus listing_dropdown" style="height: 200px;overflow: auto;" aria-labelledby="dropdownMenu1">
                                @foreach($headings as $key=>$heading)
                                    <li >
                                        <label>
                                            <input value="{{$key}}" type="checkbox" checked> {{$heading}}
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
                    <table width="100%" id="dynamic_report_table" class="table bt-datatable table-bordered table2ExcelExport">
                        <tr class="header">
                            @foreach($headings as $heading)
                                <th>{{$heading}}</th>
                            @endforeach
                        </tr>

                        @if(count($list) != 0 && count($headings) == count($fieldsKeys) )

                            @if($key3 == "")
                                @foreach($list as $kd=>$dt)
                                    <tr class="group_1">
                                        @if($key2 == 1)
                                            <td colspan="{{count($headings)}}"><b>{{date('d-m-Y', strtotime($kd))}}</b></td>
                                        @else
                                            <td colspan="{{count($headings)}}"><b>{{$kd}}</b></td>
                                        @endif
                                    </tr>
                                    @foreach($dt as $item_key=>$item)
                                        @php
                                            $rowClass = 'item_row';
                                            $rowStyle = '';
                                            $rowTextColor = '';

                                            if(property_exists($item, 'is_deleted') && (strtolower($item->is_deleted) == 'yes' || strtolower($item->is_deleted) == 'y' || $item->is_deleted == '1')){
                                                $rowClass .= ' row-deleted';
                                            }

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
                                                        if(!property_exists($item, $fieldName)){
                                                            $allRulesMatched = false;
                                                            $innerGroupMatches = false;
                                                            break 2;
                                                        }

                                                                $fieldValue = $item->$fieldName;

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
                                        <tr class="{{ $rowClass }}" @if(!empty($rowStyle)) style="{{ $rowStyle }}" @endif>
                                            @foreach($fieldsKeys as $key=>$fieldsKey)
                                                @php
                                                    $cellClass = '';
                                                @endphp
                                                @if($column_types[$key] == 'varchar2')
                                                    <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{$item->$fieldsKey}}</td>
                                                @elseif($column_types[$key] == 'number')
                                                    @php
                                                        $numVal = (int)$item->$fieldsKey;
                                                        if(in_array($key,$calc)){
                                                            $ai_[$key] += $numVal;
                                                            $arr_item[$key] = $ai_[$key];
                                                        }
                                                        if($numVal == 0 && (stripos($fieldsKey, 'amount') !== false || stripos($fieldsKey, 'qty') !== false || stripos($fieldsKey, 'quantity') !== false || stripos($fieldsKey, 'balance') !== false)){
                                                            $cellClass = 'cell-zero-amount';
                                                        }
                                                    @endphp
                                                    <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{$numVal}}</td>
                                                @elseif($column_types[$key] == 'float')
                                                    @php
                                                        $floatVal = (float)$item->$fieldsKey;
                                                        if(in_array($key,$calc)){
                                                            $ai_[$key] += $floatVal;
                                                            $arr_item[$key] = $ai_[$key];
                                                        }
                                                        if($floatVal == 0 && (stripos($fieldsKey, 'amount') !== false || stripos($fieldsKey, 'qty') !== false || stripos($fieldsKey, 'quantity') !== false || stripos($fieldsKey, 'balance') !== false)){
                                                            $cellClass = 'cell-zero-amount';
                                                        }
                                                    @endphp
                                                    <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{number_format($floatVal,!empty($decimal[$key])?$decimal[$key]:0)}}</td>
                                                @elseif($column_types[$key] == 'date')
                                                    <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{date('d-m-Y', strtotime($item->$fieldsKey))}}</td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    @if(count($calc) != 0)
                                        <tr class="sub_total">
                                            <td class="rep-font-bold">Sub Total: <span style="font-size: 12px;color: #fd397a !important;">({{$kd}})</span></td>
                                            @for($i=1; $i < count($headings); $i++)
                                                <td class="rep-font-bold">
                                                    @if(isset($arr_item[$i]))
                                                        {{number_format($arr_item[$i],!empty($decimal[$i])?$decimal[$i]:0)}}
                                                    @endif
                                                    @php
                                                        if(in_array($i,$calc)){
                                                            $a_[$i] += $arr_item[$i];
                                                            $arr[$i] = $a_[$i];
                                                        }
                                                    @endphp
                                                </td>
                                            @endfor
                                        </tr>
                                        @php
                                            $arr_item = [];
                                            foreach ($calc as $var){
                                               $ai_[$var] = 0;
                                            }
                                        @endphp
                                    @endif
                                @endforeach
                            @endif

                            @if($key3 != "")
                                @foreach($list as $kd=>$dt)
                                        <tr class="group_1">
                                            @if($key2 == 1)
                                                <td colspan="{{count($headings)}}"><b>{{date('d-m-Y', strtotime($kd))}}</b></td>
                                            @else
                                                <td colspan="{{count($headings)}}"><b>{{$kd}}</b></td>
                                            @endif
                                        </tr>
                                        @foreach($dt as $k=>$items)
                                            <tr class="group_2">
                                                @if($key4 == 1)
                                                    <td colspan="{{count($headings)}}"><b>{{date('d-m-Y', strtotime($k))}}</b></td>
                                                @else
                                                    <td colspan="{{count($headings)}}">{{$k}}</td>
                                                @endif
                                            </tr>
                                            @foreach($items as $item_key=>$item)
                                                @php
                                                    $rowClass = 'item_row';
                                                    $rowStyle = '';
                                                    $rowTextColor = '';

                                                    if(property_exists($item, 'is_deleted') && (strtolower($item->is_deleted) == 'yes' || strtolower($item->is_deleted) == 'y' || $item->is_deleted == '1')){
                                                        $rowClass .= ' row-deleted';
                                                    }

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
                                                                if(!property_exists($item, $fieldName)){
                                                                    $allRulesMatched = false;
                                                                    $innerGroupMatches = false;
                                                                    break 2;
                                                                }

                                                                $fieldValue = $item->$fieldName;

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
                                                <tr class="{{ $rowClass }}" @if(!empty($rowStyle)) style="{{ $rowStyle }}" @endif>
                                                    @foreach($fieldsKeys as $key=>$fieldsKey)
                                                        @php
                                                            $cellClass = '';
                                                        @endphp
                                                        @if($column_types[$key] == 'varchar2')
                                                            <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{$item->$fieldsKey}}</td>
                                                        @elseif($column_types[$key] == 'number')
                                                            @php
                                                                $numVal = (int)$item->$fieldsKey;
                                                                if(in_array($key,$calc)){
                                                                    $ai_[$key] += $numVal;
                                                                    $arr_item[$key] = $ai_[$key];
                                                                }
                                                                if($numVal == 0 && (stripos($fieldsKey, 'amount') !== false || stripos($fieldsKey, 'qty') !== false || stripos($fieldsKey, 'quantity') !== false || stripos($fieldsKey, 'balance') !== false)){
                                                                    $cellClass = 'cell-zero-amount';
                                                                }
                                                            @endphp
                                                            <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{$numVal}}</td>
                                                        @elseif($column_types[$key] == 'float')
                                                            @php
                                                                $floatVal = (float)$item->$fieldsKey;
                                                                if(in_array($key,$calc)){
                                                                    $ai_[$key] += $floatVal;
                                                                    $arr_item[$key] = $ai_[$key];
                                                                }
                                                                if($floatVal == 0 && (stripos($fieldsKey, 'amount') !== false || stripos($fieldsKey, 'qty') !== false || stripos($fieldsKey, 'quantity') !== false || stripos($fieldsKey, 'balance') !== false)){
                                                                    $cellClass = 'cell-zero-amount';
                                                                }
                                                            @endphp
                                                            <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{number_format($floatVal,!empty($decimal[$key])?$decimal[$key]:0)}}</td>
                                                        @elseif($column_types[$key] == 'date')
                                                            <td class="{{ $cellClass }}" @if(!empty($rowTextColor)) style="color: {{ $rowTextColor }} !important;" @endif>{{date('d-m-Y', strtotime($item->$fieldsKey))}}</td>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                            @if(count($calc) != 0)
                                                <tr class="total">
                                                    <td class="rep-font-bold">Total: <span style="font-size: 12px;color: #fd397a !important;">({{$k}})</span></td>
                                                    @for($i=1; $i < count($headings); $i++)
                                                        <td class="rep-font-bold">
                                                            @if(isset($arr_item[$i]))
                                                                {{number_format($arr_item[$i],!empty($decimal[$i])?$decimal[$i]:0)}}
                                                                @php
                                                                    if(in_array($i,$calc)){
                                                                        $ag_[$i] += $arr_item[$i];
                                                                        $arr_grp[$i] = $ag_[$i];
                                                                    }
                                                                @endphp
                                                            @endif
                                                        </td>
                                                    @endfor
                                                </tr>
                                                @php
                                                    $arr_item = [];
                                                    foreach ($calc as $var){
                                                       $ai_[$var] = 0;
                                                    }
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if(count($calc) != 0)
                                            <tr class="sub_total">
                                                <td class="rep-font-bold">Sub Total: <span style="font-size: 12px;color: #fd397a !important;">({{$kd}})</span></td>
                                                @for($i=1; $i < count($headings); $i++)
                                                    <td class="rep-font-bold">
                                                        @if(isset($arr_grp[$i]))
                                                            {{number_format($arr_grp[$i],!empty($decimal[$i])?$decimal[$i]:0)}}
                                                        @endif
                                                        @php
                                                            if(in_array($i,$calc)){
                                                                $a_[$i] += $arr_grp[$i];
                                                                $arr[$i] = $a_[$i];
                                                            }
                                                        @endphp
                                                    </td>
                                                @endfor
                                            </tr>
                                            @php
                                                $arr_grp = [];
                                                foreach ($calc as $var){
                                                   $ag_[$var] = 0;
                                                }
                                            @endphp
                                        @endif
                                    @endforeach
                            @endif

                        @else
                            <tr>
                                <td colspan="{{count($headings)}}">
                                    No Data Found......
                                    @if(count($list) != 0 && count($headings) != count($fieldsKeys))
                                        error...
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if(count($calc) != 0)
                            <tr class="grand_total">
                                <td class="rep-font-bold">Grand Total:</td>
                                @for($i=1; $i < count($headings); $i++)
                                    <td class="rep-font-bold">
                                        @if(isset($arr[$i]))
                                            {{number_format($arr[$i],!empty($decimal[$i])?$decimal[$i]:0)}}
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endif
                    </table>
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
        /*$('.listing_dropdown>li>label>input[type="checkbox"]').on('click', function(e) {
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
         */
        /* // Html Table Sorting ASC and DESC
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
         })));*/

        setTimeout(function() {
            $('.btnExcelExport').off('click');
            $(document).off('click', '.btnExcelExport');

            $(document).on('click', '.btnExcelExport', function(e) {
                var table = document.getElementById('dynamic_report_table');
                if (table) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();

                    var rowCount = $('#dynamic_report_table tbody tr.item_row').length;
                    if (rowCount === 0) {
                        alert('No data to export');
                        return false;
                    }

                    var hiddenColumns = [];
                    $('.listing_dropdown>li>label>input[type="checkbox"]').each(function() {
                        var val = $(this).val();
                        if (!$(this).is(':checked')) {
                            hiddenColumns.push(val);
                            $('#dynamic_report_table thead tr.header').find('th:eq('+val+')').show();
                            $('#dynamic_report_table tbody tr.item_row').find('td:eq('+val+')').show();
                            $('#dynamic_report_table tbody tr.group_1').find('td:eq('+val+')').show();
                            $('#dynamic_report_table tbody tr.group_2').find('td:eq('+val+')').show();
                            $('#dynamic_report_table tbody tr.sub_total').find('td:eq('+val+')').show();
                            $('#dynamic_report_table tbody tr.grand_total').find('td:eq('+val+')').show();
                        }
                    });

                    setTimeout(function() {
                        try {
                            $("#dynamic_report_table").table2excel({
                                exclude: ".noExport",
                                filename: "report.xls",
                            });
                        } catch(err) {
                            console.error('Excel export error:', err);
                            alert('Error exporting to Excel. Please try again.');
                        }

                        setTimeout(function() {
                            hiddenColumns.forEach(function(val) {
                                $('#dynamic_report_table thead tr.header').find('th:eq('+val+')').hide();
                                $('#dynamic_report_table tbody tr.item_row').find('td:eq('+val+')').hide();
                                $('#dynamic_report_table tbody tr.group_1').find('td:eq('+val+')').hide();
                                $('#dynamic_report_table tbody tr.group_2').find('td:eq('+val+')').hide();
                                $('#dynamic_report_table tbody tr.sub_total').find('td:eq('+val+')').hide();
                                $('#dynamic_report_table tbody tr.grand_total').find('td:eq('+val+')').hide();
                            });
                        }, 200);
                    }, 100);

                    return false;
                }
            });
        }, 100);
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
            <div class="kt-portlet__head-toolbar text-center">
                <div href="#" class="kt-invoice__logo">
                    <a href="#"><img src="/images/{{ auth()->user()->business->business_profile }}" width="60px"></a>
                    <div class="kt-invoice__desc">
                        <div>{{strtoupper(auth()->user()->branch->branch_name)}}</div>
                    </div>
                </div>
            </div>
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
