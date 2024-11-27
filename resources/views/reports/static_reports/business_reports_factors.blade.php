@extends('layouts.report')
@section('title', 'Business Report Factors')

@section('pageCSS')
    <style>
        th, th>span,td {
            font-size: 11px !important;
        }
        tr>th, tr>td:first-child {
            background: #e8e8e8;
            border-right: 1px solid #ababab !important;
        }
    </style>
@endsection
@section('content')
    @php
        $data = Session::get('data');

        $from_date = $data['from_date'];
        $to_date = $data['to_date'];
        $branches = $data['branch_ids'];

        $list = \Illuminate\Support\Facades\DB::table('tmp_gaga')->get();
        $key_names = [];
        $table_name = "";
        $case_type = "";
        if(count($list) != 0 ){
            for ($i=0;$i<1;$i++){
                $allKeysOfEmployee = array_keys((array)$list[0]);
                foreach($allKeysOfEmployee as $k=>&$tempKey){
                    if($k != 0 && $k != 1 && $k != 3 && $k != 4){
                         array_push($key_names,$tempKey);
                    }
                    if($k == 3){ $table_name = $tempKey; }
                    if($k == 4){ $case_type = $tempKey; }
                }
            }
        }
    @endphp
    <div class="kt-portlet" id="kt_portlet_table">
        <div class="kt-portlet__head">
            <div class="kt-invoice__brand">
                <h1 class="kt-invoice__title">{{strtoupper($data['page_title'])}}</h1>
                <h6 class="kt-invoice__criteria">
                    <span style="color: #e27d00;">Date:</span>
                    <span style="color: #5578eb;">{{" ".date('d-m-Y', strtotime($data['from_date']))." to ". date('d-m-Y', strtotime($data['to_date']))." "}}</span>
                </h6>
                @if(count($data['branch_ids']) != 0)
                    @php $branch_lists = \Illuminate\Support\Facades\DB::table('tbl_soft_branch')->whereIn('branch_id',$data['branch_ids'])->get('branch_name'); @endphp
                    <h6 class="kt-invoice__criteria">
                        <span style="color: #e27d00;">Branch:</span>
                        @foreach($branch_lists as $branch_list)
                            <span style="color: #5578eb;">{{$branch_list->branch_name}}</span><span style="color: #fd397a;">, </span>
                        @endforeach
                    </h6>
                @endif
            </div>
            @include('reports.template.branding')
        </div>
        <div class="kt-portlet__body">
            <div class="row row-block">
                <div class="col-lg-12">
                    <table width="100%" id="rep_sale_invoice_datatable" class="table bt-datatable table-bordered">
                        <tr class="sticky-header">
                            @foreach($key_names as $key=>$key_name)
                                @if($key == 0)
                                <th width="80px">{{ucwords(str_replace("_"," ",$key_name))}}</th>
                                @else
                                <th width="80px" class="text-center">{{ucwords(substr(str_replace("''","'",$key_name), 1, -1))}}</th>
                                @endif
                            @endforeach
                        </tr>
                        @if(count($list) != 0)
                            @foreach($list as $k=>$item)
                            <tr>
                                @foreach($key_names as $key=>$key_name)
                                    @if($key == 0)
                                        <td class="text-left">{{$item->$key_name}}</td>
                                    @else
                                        <td class="text-right">{{$item->$key_name}}</td>
                                    @endif
                                @endforeach
                            </tr>
                            @endforeach
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



