<style>
    div.inline_help {
        background: #ffffff;
        position: absolute;
        left: 0;
        z-index: 9999;
        width: 100%;
        box-shadow: 0px 3px 5px 0px #d4d4d4;
        max-height: 230px;
        overflow: auto;
    }
    #inLineHelp{
        position: absolute;
        width: 500px;
        height: 230px;
        z-index: 9999;
        box-shadow:0px 3px 12px 0px rgb(85 120 235 / 22%);
        background: #f5f7ff;
    }
    div.inline_help_table {
        background: #ffffff;
        position: sticky;
        width: 100% !important;
        max-height: 100% !important;
        overflow-y: scroll !important;
        position: -webkit-sticky
    }
    .data_tbody_row.selected_row,
    .data_tbody_row:hover{
        background-color: #e8e8e8;
    }
    .data_tbody_row>table {
        table-layout: fixed;
    }
    .inline_help_table>.data_thead_row>table>thead>tr>th,
    .inline_help>.data_thead_row>table>thead>tr>th {
        background: #5578eb;
        color: #fff !important;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
    }
    .inline_help>.data_thead_row>table>thead>tr>th,
    .inline_help>.data_tbody_row>table>tbody>tr>td,
    .inline_help_table>.data_thead_row>table>thead>tr>th,
    .inline_help_table>.data_tbody_row>table>tbody>tr>td{
        /*white-space: nowrap;*/
        text-overflow: ellipsis;
        overflow: hidden;
        border: 1px solid #e6e8f3;
        font-weight: 400;
        color: #212529;
        font-size: 12px;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
    }
    .inline_help_table>.data_thead_row>table>thead>tr>th,
    .inline_help_table>.data_tbody_row>table>tbody>tr>td {
        font-weight: 400 !important;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
    }
    .inline_help>.data_tbody_row>table>tbody>tr.data-dtl {
        background-color: #f7f8fa;
    }
    .data_tbody_row:hover>table>tbody>tr>td,
    .data_tbody_row.selected_row>table>tbody>tr>td,
    .data_tbody_row.selected_row>table>tbody>tr>td:hover {
        background: #dedede;
    }
    .data_tbody_row:hover {
        cursor: pointer;
    }
</style>
<div class="data_thead_row" id="{{$data['case']}}">
    <table border="1" class="" width="100%">
        <thead>
        <tr>
            @php
                $w = 100/count($data['head']);
            @endphp
            @foreach($data['head'] as $head)
                <th data-field="{{$head}}" width="{{$w}}%">{{$head}}</th>
            @endforeach
        </tr>
        </thead>
    </table>
</div>
@if(count($data['list']) == 0)
    <div class="data_tbody_row">
        <table border="1" class="" width="100%">
            <tbody>
                <tr class="data-dtl">
                    <td width="100%" class="text-center">No data found</td>
                </tr>
            </tbody>
        </table>
    </div>
@endif
@foreach($data['list'] as $list)
    <div class="data_tbody_row">
        <table border="1" class="" width="100%">
            <tbody>
            <tr class="data-dtl">
                @foreach($data['keys'] as $keys)
                    <td {{$data['show_name']==$keys?'data-view=show':""  }} data-field="{{$keys}}" width="{{$w}}%">@if($keys == 'product_barcode_cost_rate'){{number_format((float)$list->$keys , 3 ,'.' ,'')}}@else{{trim($list->$keys)}}@endif</td>
                @endforeach
                {{--<td data-view="show" data-field="Name" width="20%">John</td>--}}
            </tr>
            <tr class="d-none">
                @foreach($data['hideKeys'] as $hideKeys)
                    <td data-field="{{$hideKeys}}">{{$list->$hideKeys}}</td>
                @endforeach
                @if(isset($data['row_identifier']))
                    <td data-field="row_identifier">{{$data['row_identifier']}}</td>
                @endif
                @if(isset($list->supplier_has_returnable))
                    <td data-field="supplier_has_returnable">{{$list->supplier_has_returnable}}</td>
                @endif
            </tr>
            </tbody>
        </table>
    </div>
@endforeach

